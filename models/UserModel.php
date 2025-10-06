<?php
/**
 * User Model Example
 * 
 * This is an example of how to create a specific model that extends BaseModel
 */

require_once 'BaseModel.php';

class UserModel extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'email', 'age', 'status'];
    protected $timestamps = true;
    
    /**
     * Find user by email
     * 
     * @param string $email
     * @return array|false
     */
    public function findByEmail($email) {
        return $this->findFirst(['email' => $email]);
    }
    
    /**
     * Find active users
     * 
     * @return array|false
     */
    public function findActive() {
        return $this->findAll(['status' => 'active']);
    }
    
    /**
     * Find users by age range
     * 
     * @param int $minAge
     * @param int $maxAge
     * @return array|false
     */
    public function findByAgeRange($minAge, $maxAge) {
        $conditions = ['age' => ['between' => [$minAge, $maxAge]]];
        return $this->findAll($conditions);
    }
    
    /**
     * Search users by name
     * 
     * @param string $name
     * @return array|false
     */
    public function searchByName($name) {
        $conditions = ['name' => ['operator' => 'LIKE', 'value' => "%{$name}%"]];
        return $this->findAll($conditions);
    }
    
    /**
     * Get user statistics
     * 
     * @return array
     */
    public function getStatistics() {
        $total = $this->count();
        $active = $this->count(['status' => 'active']);
        $inactive = $this->count(['status' => 'inactive']);
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0
        ];
    }
    
    /**
     * Activate user
     * 
     * @param int $id
     * @return int|false
     */
    public function activate($id) {
        return $this->update($id, ['status' => 'active']);
    }
    
    /**
     * Deactivate user
     * 
     * @param int $id
     * @return int|false
     */
    public function deactivate($id) {
        return $this->update($id, ['status' => 'inactive']);
    }
    
    /**
     * Validate user data
     * 
     * @param array $data
     * @return array Array of validation errors (empty if valid)
     */
    public function validate($data) {
        $errors = [];
        
        // Required fields
        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (isset($data['age']) && (!is_numeric($data['age']) || $data['age'] < 0 || $data['age'] > 150)) {
            $errors[] = 'Age must be a number between 0 and 150';
        }
        
        // Check for duplicate email (if creating new user)
        if (!empty($data['email']) && !isset($data['id'])) {
            if ($this->findByEmail($data['email'])) {
                $errors[] = 'Email already exists';
            }
        }
        
        return $errors;
    }
    
    /**
     * Create user with validation
     * 
     * @param array $data
     * @return array Result with success status and data/errors
     */
    public function createWithValidation($data) {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
        
        $id = $this->create($data);
        
        if ($id) {
            return [
                'success' => true,
                'id' => $id,
                'data' => $this->find($id)
            ];
        } else {
            return [
                'success' => false,
                'errors' => ['Failed to create user: ' . $this->getLastError()]
            ];
        }
    }
    
    /**
     * Update user with validation
     * 
     * @param int $id
     * @param array $data
     * @return array Result with success status and data/errors
     */
    public function updateWithValidation($id, $data) {
        $data['id'] = $id; // Add ID for validation
        $errors = $this->validate($data);
        unset($data['id']); // Remove ID from data
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
        
        $affected = $this->update($id, $data);
        
        if ($affected !== false) {
            return [
                'success' => true,
                'affected_rows' => $affected,
                'data' => $this->find($id)
            ];
        } else {
            return [
                'success' => false,
                'errors' => ['Failed to update user: ' . $this->getLastError()]
            ];
        }
    }
}
?>