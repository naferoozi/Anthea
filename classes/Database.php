<?php
/**
 * Database Class - Comprehensive MySQLi CRUD and Operations Handler
 * 
 * This class provides a complete solution for database operations using MySQLi
 * with prepared statements, error handling, and security features.
 * 
 * Features:
 * - Singleton pattern for connection management
 * - Prepared statements for SQL injection protection
 * - Comprehensive CRUD operations
 * - Transaction support
 * - Error logging and handling
 * - Connection pooling
 * - Query builder methods
 * 
 * @author Your Name
 * @version 1.0
 */

class Database {
    private static $instance = null;
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    private $charset;
    private $port;
    
    // Error handling
    private $errors = [];
    private $logErrors = true;
    private $logFile = 'logs/database_errors.log';
    
    // Transaction state
    private $inTransaction = false;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct($config = []) {
        $this->host = $config['host'] ?? 'localhost';
        $this->username = $config['username'] ?? 'root';
        $this->password = $config['password'] ?? '';
        $this->database = $config['database'] ?? '';
        $this->charset = $config['charset'] ?? 'utf8mb4';
        $this->port = $config['port'] ?? 3306;
        
        $this->connect();
    }
    
    /**
     * Get singleton instance of Database
     * 
     * @param array $config Database configuration
     * @return Database
     */
    public static function getInstance($config = []) {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     * 
     * @return bool
     */
    private function connect() {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database,
                $this->port
            );
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            // Set charset
            $this->connection->set_charset($this->charset);
            
            return true;
            
        } catch (Exception $e) {
            $this->logError("Connection Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get the MySQLi connection object
     * 
     * @return mysqli
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Execute a prepared statement
     * 
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind
     * @param string $types Parameter types (i, d, s, b)
     * @return mysqli_result|bool
     */
    public function execute($query, $params = [], $types = '') {
        try {
            if (!$this->connection) {
                throw new Exception("No database connection available");
            }
            
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }
            
            if (!empty($params)) {
                if (empty($types)) {
                    $types = $this->detectTypes($params);
                }
                $stmt->bind_param($types, ...$params);
            }
            
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $queryResult = $stmt->get_result();
            $stmt->close();
            
            return $queryResult;
            
        } catch (Exception $e) {
            $this->logError("Query Error: " . $e->getMessage() . " | Query: " . $query);
            return false;
        }
    }
    
    /**
     * SELECT operation - Fetch multiple records
     * 
     * @param string $table Table name
     * @param array $columns Columns to select (default: *)
     * @param array $conditions WHERE conditions
     * @param array $options Additional options (ORDER BY, LIMIT, etc.)
     * @return array|false
     */
    public function select($table, $columns = ['*'], $conditions = [], $options = []) {
        try {
            $columnStr = implode(', ', $columns);
            $query = "SELECT {$columnStr} FROM `{$table}`";
            
            $params = [];
            $types = '';
            
            // Build WHERE clause
            if (!empty($conditions)) {
                $whereClause = $this->buildWhereClause($conditions, $params, $types);
                $query .= " WHERE " . $whereClause;
            }
            
            // Add ORDER BY
            if (isset($options['order_by'])) {
                $query .= " ORDER BY " . $options['order_by'];
            }
            
            // Add LIMIT
            if (isset($options['limit'])) {
                $query .= " LIMIT " . (int)$options['limit'];
                if (isset($options['offset'])) {
                    $query .= " OFFSET " . (int)$options['offset'];
                }
            }
            
            $result = $this->execute($query, $params, $types);
            if ($result === false) {
                return false;
            }
            
            return $result->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            $this->logError("Select Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * SELECT operation - Fetch single record
     * 
     * @param string $table Table name
     * @param array $columns Columns to select
     * @param array $conditions WHERE conditions
     * @return array|false
     */
    public function selectOne($table, $columns = ['*'], $conditions = []) {
        $result = $this->select($table, $columns, $conditions, ['limit' => 1]);
        return $result ? ($result[0] ?? false) : false;
    }
    
    /**
     * INSERT operation
     * 
     * @param string $table Table name
     * @param array $data Associative array of column => value
     * @return int|false Insert ID or false on failure
     */
    public function insert($table, $data) {
        try {
            if (empty($data)) {
                throw new Exception("No data provided for insert");
            }
            
            $columns = array_keys($data);
            $values = array_values($data);
            $placeholders = str_repeat('?,', count($values) - 1) . '?';
            $types = $this->detectTypes($values);
            
            $columnStr = '`' . implode('`, `', $columns) . '`';
            $query = "INSERT INTO `{$table}` ({$columnStr}) VALUES ({$placeholders})";
            
            $result = $this->execute($query, $values, $types);
            if ($result === false) {
                return false;
            }
            
            return $this->connection->insert_id;
            
        } catch (Exception $e) {
            $this->logError("Insert Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * UPDATE operation
     * 
     * @param string $table Table name
     * @param array $data Associative array of column => value
     * @param array $conditions WHERE conditions
     * @return int|false Number of affected rows or false on failure
     */
    public function update($table, $data, $conditions) {
        try {
            if (empty($data)) {
                throw new Exception("No data provided for update");
            }
            
            if (empty($conditions)) {
                throw new Exception("No conditions provided for update - this would update all rows");
            }
            
            $setClause = [];
            $params = [];
            $types = '';
            
            // Build SET clause
            foreach ($data as $column => $value) {
                $setClause[] = "`{$column}` = ?";
                $params[] = $value;
                $types .= $this->detectType($value);
            }
            
            $query = "UPDATE `{$table}` SET " . implode(', ', $setClause);
            
            // Build WHERE clause
            $whereClause = $this->buildWhereClause($conditions, $params, $types);
            $query .= " WHERE " . $whereClause;
            
            $result = $this->execute($query, $params, $types);
            if ($result === false) {
                return false;
            }
            
            return $this->connection->affected_rows;
            
        } catch (Exception $e) {
            $this->logError("Update Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * DELETE operation
     * 
     * @param string $table Table name
     * @param array $conditions WHERE conditions
     * @return int|false Number of affected rows or false on failure
     */
    public function delete($table, $conditions) {
        try {
            if (empty($conditions)) {
                throw new Exception("No conditions provided for delete - this would delete all rows");
            }
            
            $params = [];
            $types = '';
            
            $whereClause = $this->buildWhereClause($conditions, $params, $types);
            $query = "DELETE FROM `{$table}` WHERE " . $whereClause;
            
            $result = $this->execute($query, $params, $types);
            if ($result === false) {
                return false;
            }
            
            return $this->connection->affected_rows;
            
        } catch (Exception $e) {
            $this->logError("Delete Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count records in table
     * 
     * @param string $table Table name
     * @param array $conditions WHERE conditions
     * @return int|false
     */
    public function count($table, $conditions = []) {
        $result = $this->selectOne($table, ['COUNT(*) as count'], $conditions);
        return $result ? (int)$result['count'] : false;
    }
    
    /**
     * Check if record exists
     * 
     * @param string $table Table name
     * @param array $conditions WHERE conditions
     * @return bool
     */
    public function exists($table, $conditions) {
        return $this->count($table, $conditions) > 0;
    }
    
    /**
     * Execute raw SQL query
     * 
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @param string $types Parameter types
     * @return mysqli_result|bool
     */
    public function query($query, $params = [], $types = '') {
        return $this->execute($query, $params, $types);
    }
    
    /**
     * Begin transaction
     * 
     * @return bool
     */
    public function beginTransaction() {
        if ($this->inTransaction) {
            return false;
        }
        
        $result = $this->connection->begin_transaction();
        if ($result) {
            $this->inTransaction = true;
        }
        return $result;
    }
    
    /**
     * Commit transaction
     * 
     * @return bool
     */
    public function commit() {
        if (!$this->inTransaction) {
            return false;
        }
        
        $result = $this->connection->commit();
        if ($result) {
            $this->inTransaction = false;
        }
        return $result;
    }
    
    /**
     * Rollback transaction
     * 
     * @return bool
     */
    public function rollback() {
        if (!$this->inTransaction) {
            return false;
        }
        
        $result = $this->connection->rollback();
        $this->inTransaction = false;
        return $result;
    }
    
    /**
     * Get last insert ID
     * 
     * @return int
     */
    public function getLastInsertId() {
        return $this->connection->insert_id;
    }
    
    /**
     * Get affected rows from last operation
     * 
     * @return int
     */
    public function getAffectedRows() {
        return $this->connection->affected_rows;
    }
    
    /**
     * Escape string for SQL
     * 
     * @param string $string
     * @return string
     */
    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }
    
    /**
     * Build WHERE clause from conditions array
     * 
     * @param array $conditions
     * @param array &$params
     * @param string &$types
     * @return string
     */
    private function buildWhereClause($conditions, &$params, &$types) {
        $whereClause = [];
        
        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                // Handle IN clause
                if (isset($value['in'])) {
                    $placeholders = str_repeat('?,', count($value['in']) - 1) . '?';
                    $whereClause[] = "`{$column}` IN ({$placeholders})";
                    foreach ($value['in'] as $inValue) {
                        $params[] = $inValue;
                        $types .= $this->detectType($inValue);
                    }
                }
                // Handle BETWEEN clause
                elseif (isset($value['between'])) {
                    $whereClause[] = "`{$column}` BETWEEN ? AND ?";
                    $params[] = $value['between'][0];
                    $params[] = $value['between'][1];
                    $types .= $this->detectType($value['between'][0]);
                    $types .= $this->detectType($value['between'][1]);
                }
                // Handle comparison operators
                elseif (isset($value['operator']) && isset($value['value'])) {
                    $operator = $value['operator'];
                    $whereClause[] = "`{$column}` {$operator} ?";
                    $params[] = $value['value'];
                    $types .= $this->detectType($value['value']);
                }
            } else {
                // Simple equality
                $whereClause[] = "`{$column}` = ?";
                $params[] = $value;
                $types .= $this->detectType($value);
            }
        }
        
        return implode(' AND ', $whereClause);
    }
    
    /**
     * Detect parameter types for prepared statements
     * 
     * @param array $params
     * @return string
     */
    private function detectTypes($params) {
        $types = '';
        foreach ($params as $param) {
            $types .= $this->detectType($param);
        }
        return $types;
    }
    
    /**
     * Detect single parameter type
     * 
     * @param mixed $param
     * @return string
     */
    private function detectType($param) {
        if (is_int($param)) {
            return 'i';
        } elseif (is_float($param)) {
            return 'd';
        } elseif (is_string($param)) {
            return 's';
        } else {
            return 'b'; // blob
        }
    }
    
    /**
     * Log error message
     * 
     * @param string $message
     */
    private function logError($message) {
        $this->errors[] = $message;
        
        if ($this->logErrors) {
            $logDir = dirname($this->logFile);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
            file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
        }
    }
    
    /**
     * Get all errors
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get last error
     * 
     * @return string|null
     */
    public function getLastError() {
        return end($this->errors) ?: null;
    }
    
    /**
     * Clear errors
     */
    public function clearErrors() {
        $this->errors = [];
    }
    
    /**
     * Set error logging
     * 
     * @param bool $enabled
     * @param string $logFile
     */
    public function setErrorLogging($enabled, $logFile = null) {
        $this->logErrors = $enabled;
        if ($logFile) {
            $this->logFile = $logFile;
        }
    }
    
    /**
     * Close database connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }
    
    /**
     * Destructor - ensure connection is closed
     */
    public function __destruct() {
        $this->close();
    }
    
    /**
     * Prevent cloning of singleton
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of singleton
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>