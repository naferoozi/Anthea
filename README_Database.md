# MySQLi Database Class Documentation

This comprehensive MySQLi database class provides a complete solution for database operations with security, error handling, and ease of use.

## Features

- **Singleton Pattern**: Ensures single database connection throughout the application
- **Prepared Statements**: Automatic SQL injection protection
- **CRUD Operations**: Complete Create, Read, Update, Delete functionality
- **Transaction Support**: Begin, commit, and rollback transactions
- **Error Handling**: Comprehensive error logging and reporting
- **Query Builder**: Flexible condition building for complex queries
- **Model Base Class**: Foundation for creating model classes

## Installation

1. Copy the `classes/Database.php` file to your project
2. Configure your database settings in `config/database.php`
3. Include the class in your PHP files

## Basic Usage

### 1. Setup and Configuration

```php
// Include the Database class
require_once 'classes/Database.php';

// Load configuration
$config = require 'config/database.php';

// Get database instance (Singleton)
$db = Database::getInstance($config);
```

### 2. INSERT Operations

```php
// Simple insert
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 30
];

$insertId = $db->insert('users', $data);
if ($insertId) {
    echo "User created with ID: {$insertId}";
} else {
    echo "Error: " . $db->getLastError();
}
```

### 3. SELECT Operations

```php
// Select all records
$users = $db->select('users');

// Select specific columns
$users = $db->select('users', ['id', 'name', 'email']);

// Select with conditions
$conditions = ['age' => ['operator' => '>', 'value' => 18]];
$adults = $db->select('users', ['*'], $conditions);

// Select with ORDER BY and LIMIT
$options = [
    'order_by' => 'created_at DESC',
    'limit' => 10,
    'offset' => 0
];
$recent = $db->select('users', ['*'], [], $options);

// Select single record
$user = $db->selectOne('users', ['*'], ['email' => 'john@example.com']);
```

### 4. UPDATE Operations

```php
$updateData = [
    'age' => 31,
    'updated_at' => date('Y-m-d H:i:s')
];
$conditions = ['id' => 1];

$affectedRows = $db->update('users', $updateData, $conditions);
```

### 5. DELETE Operations

```php
$conditions = ['id' => 1];
$affectedRows = $db->delete('users', $conditions);
```

## Advanced Features

### Complex WHERE Conditions

```php
// IN clause
$conditions = ['id' => ['in' => [1, 2, 3, 4, 5]]];

// BETWEEN clause
$conditions = ['age' => ['between' => [18, 65]]];

// Multiple conditions with operators
$conditions = [
    'age' => ['operator' => '>=', 'value' => 18],
    'status' => 'active',
    'name' => ['operator' => 'LIKE', 'value' => 'John%']
];
```

### Transactions

```php
$db->beginTransaction();

try {
    $userId = $db->insert('users', $userData);
    $profileId = $db->insert('profiles', $profileData);
    
    $db->commit();
    echo "Transaction successful";
} catch (Exception $e) {
    $db->rollback();
    echo "Transaction failed: " . $e->getMessage();
}
```

### Count and Exists

```php
// Count records
$totalUsers = $db->count('users');
$activeUsers = $db->count('users', ['status' => 'active']);

// Check if record exists
$exists = $db->exists('users', ['email' => 'john@example.com']);
```

### Raw Queries

```php
$query = "SELECT u.name, p.bio FROM users u LEFT JOIN profiles p ON u.id = p.user_id WHERE u.age > ?";
$result = $db->query($query, [18], 'i');

if ($result) {
    $data = $result->fetch_all(MYSQLI_ASSOC);
}
```

## Using Models

### Base Model

The `BaseModel` class provides a foundation for creating model classes:

```php
require_once 'models/BaseModel.php';

class UserModel extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'email', 'age', 'status'];
    protected $timestamps = true;
    
    public function findByEmail($email) {
        return $this->findFirst(['email' => $email]);
    }
}
```

### Using Models

```php
$userModel = new UserModel();

// Find by ID
$user = $userModel->find(1);

// Create new user
$id = $userModel->create([
    'name' => 'Jane Doe',
    'email' => 'jane@example.com',
    'age' => 25
]);

// Update user
$userModel->update(1, ['age' => 26]);

// Delete user
$userModel->delete(1);

// Pagination
$result = $userModel->paginate(1, 10);
echo "Page 1 of " . $result['pagination']['total_pages'];
```

## Error Handling

```php
// Check for errors
if (!$result) {
    echo "Last error: " . $db->getLastError();
    
    // Get all errors
    $errors = $db->getErrors();
    foreach ($errors as $error) {
        echo "Error: {$error}\n";
    }
    
    // Clear errors
    $db->clearErrors();
}

// Configure error logging
$db->setErrorLogging(true, 'logs/custom_errors.log');
```

## Security Features

- **Prepared Statements**: All queries use prepared statements to prevent SQL injection
- **Parameter Type Detection**: Automatic detection of parameter types (integer, string, float, blob)
- **Input Validation**: Built-in validation in model classes
- **Error Logging**: Secure error logging without exposing sensitive information

## Configuration Options

```php
$config = [
    'host' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'your_database',
    'charset' => 'utf8mb4',
    'port' => 3306
];
```

## Best Practices

1. **Always use the singleton pattern** to get the database instance
2. **Use models** for business logic and data validation
3. **Handle errors appropriately** and log them for debugging
4. **Use transactions** for operations that involve multiple tables
5. **Validate input data** before database operations
6. **Use prepared statements** (automatically handled by the class)
7. **Close connections** when done (handled automatically by destructor)

## File Structure

```
project/
├── classes/
│   └── Database.php          # Main database class
├── config/
│   └── database.php          # Database configuration
├── models/
│   ├── BaseModel.php         # Base model class
│   └── UserModel.php         # Example user model
├── examples/
│   └── database_usage.php    # Usage examples
└── logs/
    └── database_errors.log   # Error log file
```

This database class provides everything you need for robust database operations in your PHP application. It's secure, flexible, and easy to use while maintaining best practices for database interactions.