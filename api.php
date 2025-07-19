<?php
// api.php - API endpoint for Ira Beauty application
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

header('Content-Type: application/json');

// Get the requested action
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'getProducts':
            // Get search term if provided
            $search = $_GET['search'] ?? '';
            
            // Build the query
            $query = "SELECT * FROM products";
            if (!empty($search)) {
                $search = $conn->real_escape_string($search);
                $query .= " WHERE title LIKE '%$search%' OR brand LIKE '%$search%' OR category LIKE '%$search%'";
            }
            $query .= " ORDER BY id DESC";
            
            $result = $conn->query($query);
            $products = [];
            
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $products
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>