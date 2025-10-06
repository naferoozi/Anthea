<?php
/**
 * Database Class Usage Examples
 * 
 * This file demonstrates how to use the Database class for various operations
 */

// Include the Database class
require_once '../classes/Database.php';

// Load database configuration
$config = require '../config/database.php';

// Get database instance (Singleton pattern)
$db = Database::getInstance($config);

// Example 1: INSERT operation
echo "=== INSERT Example ===\n";
$userData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 30,
    'created_at' => date('Y-m-d H:i:s')
];

$insertId = $db->insert('users', $userData);
if ($insertId) {
    echo "User inserted successfully with ID: {$insertId}\n";
} else {
    echo "Insert failed: " . $db->getLastError() . "\n";
}

// Example 2: SELECT operations
echo "\n=== SELECT Examples ===\n";

// Select all users
$allUsers = $db->select('users');
echo "All users: " . json_encode($allUsers, JSON_PRETTY_PRINT) . "\n";

// Select specific columns
$userNames = $db->select('users', ['id', 'name', 'email']);
echo "User names and emails: " . json_encode($userNames, JSON_PRETTY_PRINT) . "\n";

// Select with conditions
$conditions = ['age' => ['operator' => '>', 'value' => 25]];
$adultUsers = $db->select('users', ['*'], $conditions);
echo "Users over 25: " . json_encode($adultUsers, JSON_PRETTY_PRINT) . "\n";

// Select with ORDER BY and LIMIT
$options = [
    'order_by' => 'created_at DESC',
    'limit' => 5
];
$recentUsers = $db->select('users', ['*'], [], $options);
echo "Recent 5 users: " . json_encode($recentUsers, JSON_PRETTY_PRINT) . "\n";

// Select single record
$user = $db->selectOne('users', ['*'], ['email' => 'john@example.com']);
echo "Single user: " . json_encode($user, JSON_PRETTY_PRINT) . "\n";

// Example 3: UPDATE operation
echo "\n=== UPDATE Example ===\n";
$updateData = [
    'age' => 31,
    'updated_at' => date('Y-m-d H:i:s')
];
$conditions = ['email' => 'john@example.com'];

$affectedRows = $db->update('users', $updateData, $conditions);
if ($affectedRows !== false) {
    echo "Updated {$affectedRows} rows\n";
} else {
    echo "Update failed: " . $db->getLastError() . "\n";
}

// Example 4: Advanced WHERE conditions
echo "\n=== Advanced WHERE Examples ===\n";

// IN clause
$conditions = ['id' => ['in' => [1, 2, 3, 4, 5]]];
$users = $db->select('users', ['*'], $conditions);
echo "Users with IDs 1-5: " . json_encode($users, JSON_PRETTY_PRINT) . "\n";

// BETWEEN clause
$conditions = ['age' => ['between' => [25, 35]]];
$users = $db->select('users', ['*'], $conditions);
echo "Users between 25-35 years: " . json_encode($users, JSON_PRETTY_PRINT) . "\n";

// Multiple conditions
$conditions = [
    'age' => ['operator' => '>=', 'value' => 18],
    'name' => ['operator' => 'LIKE', 'value' => 'John%']
];
$users = $db->select('users', ['*'], $conditions);
echo "Adult users named John: " . json_encode($users, JSON_PRETTY_PRINT) . "\n";

// Example 5: COUNT and EXISTS
echo "\n=== COUNT and EXISTS Examples ===\n";

$totalUsers = $db->count('users');
echo "Total users: {$totalUsers}\n";

$adultCount = $db->count('users', ['age' => ['operator' => '>=', 'value' => 18]]);
echo "Adult users: {$adultCount}\n";

$userExists = $db->exists('users', ['email' => 'john@example.com']);
echo "User john@example.com exists: " . ($userExists ? 'Yes' : 'No') . "\n";

// Example 6: Transaction example
echo "\n=== Transaction Example ===\n";

$db->beginTransaction();

try {
    // Insert multiple related records
    $userId = $db->insert('users', [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'age' => 28
    ]);
    
    if (!$userId) {
        throw new Exception('Failed to insert user');
    }
    
    $profileId = $db->insert('user_profiles', [
        'user_id' => $userId,
        'bio' => 'Software developer',
        'location' => 'New York'
    ]);
    
    if (!$profileId) {
        throw new Exception('Failed to insert profile');
    }
    
    $db->commit();
    echo "Transaction completed successfully\n";
    
} catch (Exception $e) {
    $db->rollback();
    echo "Transaction rolled back: " . $e->getMessage() . "\n";
}

// Example 7: Raw query
echo "\n=== Raw Query Example ===\n";

$query = "SELECT u.name, p.bio FROM users u LEFT JOIN user_profiles p ON u.id = p.user_id WHERE u.age > ?";
$result = $db->query($query, [25], 'i');

if ($result) {
    $data = $result->fetch_all(MYSQLI_ASSOC);
    echo "Users with profiles: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
}

// Example 8: DELETE operation
echo "\n=== DELETE Example ===\n";

// Delete with conditions (be careful!)
$conditions = ['age' => ['operator' => '<', 'value' => 18]];
$deletedRows = $db->delete('users', $conditions);

if ($deletedRows !== false) {
    echo "Deleted {$deletedRows} underage users\n";
} else {
    echo "Delete failed: " . $db->getLastError() . "\n";
}

// Example 9: Error handling
echo "\n=== Error Handling Example ===\n";

// This will fail because we're trying to insert duplicate email (assuming email is unique)
$duplicateUser = $db->insert('users', [
    'name' => 'Duplicate User',
    'email' => 'john@example.com', // This email already exists
    'age' => 25
]);

if (!$duplicateUser) {
    echo "Insert failed as expected: " . $db->getLastError() . "\n";
    
    // Get all errors
    $errors = $db->getErrors();
    echo "All errors: " . json_encode($errors, JSON_PRETTY_PRINT) . "\n";
    
    // Clear errors
    $db->clearErrors();
}

echo "\n=== Examples completed ===\n";
?>