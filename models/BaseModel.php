<?php
/**
 * Base Model Class
 * 
 * This class provides a foundation for creating model classes that interact with the database.
 * It extends the Database functionality with model-specific methods.
 */

require_once __DIR__ . '/../classes/Database.php';

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;
    
    public function __construct() {
        // Load database configuration
        $config = require __DIR__ . '/../config/database.php';
        $this->db = Database::getInstance($config);
    }
    
    /**
     * Find record by ID
     * 
     * @param int $id
     * @return array|false
     */
    public function find($id) {
        return $this->db->selectOne($this->table, ['*'], [$this->primaryKey => $id]);
    }
    
    /**
     * Find all records
     * 
     * @param array $conditions
     * @param array $options
     * @return array|false
     */
    public function findAll($conditions = [], $options = []) {
        return $this->db->select($this->table, ['*'], $conditions, $options);
    }
    
    /**
     * Find first record matching conditions
     * 
     * @param array $conditions
     * @return array|false
     */
    public function findFirst($conditions = []) {
        return $this->db->selectOne($this->table, ['*'], $conditions);
    }
    
    /**
     * Create new record
     * 
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Update record
     * 
     * @param int $id
     * @param array $data
     * @return int|false
     */
    public function update($id, $data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->update($this->table, $data, [$this->primaryKey => $id]);
    }
    
    /**
     * Delete record
     * 
     * @param int $id
     * @return int|false
     */
    public function delete($id) {
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }
    
    /**
     * Count records
     * 
     * @param array $conditions
     * @return int|false
     */
    public function count($conditions = []) {
        return $this->db->count($this->table, $conditions);
    }
    
    /**
     * Check if record exists
     * 
     * @param array $conditions
     * @return bool
     */
    public function exists($conditions) {
        return $this->db->exists($this->table, $conditions);
    }
    
    /**
     * Get paginated results
     * 
     * @param int $page
     * @param int $perPage
     * @param array $conditions
     * @param array $options
     * @return array
     */
    public function paginate($page = 1, $perPage = 10, $conditions = [], $options = []) {
        $offset = ($page - 1) * $perPage;
        $options['limit'] = $perPage;
        $options['offset'] = $offset;
        
        $data = $this->findAll($conditions, $options);
        $total = $this->count($conditions);
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
                'has_next' => $page < ceil($total / $perPage),
                'has_prev' => $page > 1
            ]
        ];
    }
    
    /**
     * Filter data to only include fillable fields
     * 
     * @param array $data
     * @return array
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Get database instance
     * 
     * @return Database
     */
    protected function getDb() {
        return $this->db;
    }
    
    /**
     * Begin transaction
     * 
     * @return bool
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     * 
     * @return bool
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     * 
     * @return bool
     */
    public function rollback() {
        return $this->db->rollback();
    }
    
    /**
     * Get last error
     * 
     * @return string|null
     */
    public function getLastError() {
        return $this->db->getLastError();
    }
    
    /**
     * Get all errors
     * 
     * @return array
     */
    public function getErrors() {
        return $this->db->getErrors();
    }
}
?>