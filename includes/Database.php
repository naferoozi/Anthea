<?php

/**
 * Database: Lightweight MySQLi helper with CRUD, transactions, and utilities.
 *
 * Usage:
 * $db = new Database(host: '127.0.0.1', user: 'root', pass: 'secret', db: 'app');
 * $user = $db->fetchOne('SELECT * FROM users WHERE id = ?', [123]);
 * $db->insert('users', [ 'name' => 'Alice', 'email' => 'a@example.com' ]);
 */
class Database
{
    private mysqli $conn;

    /**
     * Construct a new Database connection.
     *
     * @param string $host Database host
     * @param string $user Database username
     * @param string $pass Database password
     * @param string $db   Database name
     * @param int    $port Database port
     * @param string $charset Connection charset
     * @param bool   $strict Throw exceptions on errors
     */
    public function __construct(
        string $host,
        string $user,
        string $pass,
        string $db,
        int $port = 3306,
        string $charset = 'utf8mb4',
        bool $strict = true
    ) {
        mysqli_report($strict ? MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT : MYSQLI_REPORT_OFF);

        $this->conn = new mysqli($host, $user, $pass, $db, $port);
        $this->conn->set_charset($charset);

        // Use exceptions consistently if strict
        if ($strict) {
            set_error_handler(function (int $errno, string $errstr) {
                throw new Exception($errstr, $errno);
            });
        }
    }

    /** Close connection on destruction. */
    public function __destruct()
    {
        if (isset($this->conn)) {
            $this->conn->close();
        }
        // Restore previous error handler if modified
        restore_error_handler();
    }

    /**
     * Prepare and execute a statement with parameters.
     *
     * @param string $sql SQL with ? placeholders
     * @param array<int, mixed> $params
     * @return mysqli_stmt
     */
    public function prepareAndExecute(string $sql, array $params = []): mysqli_stmt
    {
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            [$types, $values] = $this->inferParamTypesAndValues($params);
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        return $stmt;
    }

    /** Fetch all rows as associative arrays. */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->prepareAndExecute($sql, $params);
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    /** Fetch a single row or null. */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->prepareAndExecute($sql, $params);
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    /** Fetch a single scalar value or null. */
    public function fetchValue(string $sql, array $params = []): mixed
    {
        $row = $this->fetchOne($sql, $params);
        if ($row === null) {
            return null;
        }
        return array_values($row)[0] ?? null;
    }

    /** Execute a non-select statement and return affected rows. */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->prepareAndExecute($sql, $params);
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    /** Insert a row and return insert id. */
    public function insert(string $table, array $data): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Insert data cannot be empty');
        }
        $columns = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $this->escapeIdentifier($table),
            implode(', ', array_map(fn($c) => sprintf('`%s`', $this->escapeIdentifier($c)), $columns)),
            $placeholders
        );
        $this->execute($sql, array_values($data));
        return $this->conn->insert_id;
    }

    /** Update rows with a where clause. Returns affected rows. */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Update data cannot be empty');
        }
        $setClause = implode(', ', array_map(fn($c) => sprintf('`%s` = ?', $this->escapeIdentifier($c)), array_keys($data)));
        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $this->escapeIdentifier($table),
            $setClause,
            $where
        );
        return $this->execute($sql, array_merge(array_values($data), $whereParams));
    }

    /** Delete rows with a where clause. Returns affected rows. */
    public function delete(string $table, string $where, array $whereParams = []): int
    {
        $sql = sprintf('DELETE FROM `%s` WHERE %s', $this->escapeIdentifier($table), $where);
        return $this->execute($sql, $whereParams);
    }

    /**
     * Upsert helper using INSERT ... ON DUPLICATE KEY UPDATE.
     */
    public function upsert(string $table, array $data, array $updateColumns = []): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Upsert data cannot be empty');
        }
        $columns = array_keys($data);
        if (empty($updateColumns)) {
            $updateColumns = $columns; // default: update all
        }
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $updateClause = implode(', ', array_map(fn($c) => sprintf('`%s` = VALUES(`%s`)', $this->escapeIdentifier($c), $this->escapeIdentifier($c)), $updateColumns));
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
            $this->escapeIdentifier($table),
            implode(', ', array_map(fn($c) => sprintf('`%s`', $this->escapeIdentifier($c)), $columns)),
            $placeholders,
            $updateClause
        );
        $this->execute($sql, array_values($data));
        return $this->conn->insert_id;
    }

    /** Transaction helpers */
    public function begin(): void { $this->conn->begin_transaction(); }
    public function commit(): void { $this->conn->commit(); }
    public function rollback(): void { $this->conn->rollback(); }

    /** Get last insert id */
    public function lastInsertId(): int { return $this->conn->insert_id; }

    /** Get affected rows from last operation */
    public function affectedRows(): int { return $this->conn->affected_rows; }

    /** Escape identifier like table/column names - conservative */
    private function escapeIdentifier(string $identifier): string
    {
        return str_replace('`', '``', $identifier);
    }

    /** Infer parameter types for bind_param */
    private function inferParamTypesAndValues(array $params): array
    {
        $types = '';
        $values = [];
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_null($param)) {
                $types .= 's'; // send NULL as string; MySQL will cast
                $param = null;
            } elseif (is_bool($param)) {
                $types .= 'i';
                $param = $param ? 1 : 0;
            } else {
                $types .= 's';
            }
            $values[] = $param;
        }
        return [$types, $values];
    }

    /** Expose raw connection if needed (read-only) */
    public function getConnection(): mysqli
    {
        return $this->conn;
    }
}

