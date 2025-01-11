<?php

// Function to load .env variables
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        return; // Exit if .env file doesn't exist
    }

    // Read .env file line by line
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Split key and value
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Set the environment variable if not already set
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load .env file (Make sure it's in the same directory as this script)
loadEnv(__DIR__ . '/.env');

// Set headers to allow CORS and specify JSON response
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Use environment variables
$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

// Get the HTTP method and input data
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

// Handle CRUD operations based on the HTTP method
switch ($method) {
    case 'GET':
        // Fetch all products
        $result = $conn->query("SELECT * FROM products");
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        echo json_encode(["success" => true, "data" => $products]);
        break;

    case 'POST':
        // Create a new product
        if (isset($data['action']) && $data['action'] === 'create') {
            if (isset($data['name'], $data['image'], $data['quantity'])) {
                $name = $conn->real_escape_string($data['name']);
                $image = $conn->real_escape_string($data['image']);
                $quantity = intval($data['quantity']);

                $stmt = $conn->prepare("INSERT INTO products (name, image, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $name, $image, $quantity);

                if ($stmt->execute()) {
                    echo json_encode([
                        "success" => true,
                        "data" => [
                            "id" => $conn->insert_id,
                            "name" => $name,
                            "image" => $image,
                            "quantity" => $quantity,
                        ],
                    ]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to create product"]);
                }

                $stmt->close();
            } else {
                echo json_encode(["success" => false, "message" => "Invalid input"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
        break;

    case 'PUT':
        // Update an existing product
        if (isset($data['id'], $data['name'], $data['image'], $data['quantity'])) {
            $id = intval($data['id']);
            $name = $conn->real_escape_string($data['name']);
            $image = $conn->real_escape_string($data['image']);
            $quantity = intval($data['quantity']);

            $stmt = $conn->prepare("UPDATE products SET name = ?, image = ?, quantity = ? WHERE id = ?");
            $stmt->bind_param("ssii", $name, $image, $quantity, $id);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "data" => [
                        "id" => $id,
                        "name" => $name,
                        "image" => $image,
                        "quantity" => $quantity,
                    ],
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update product"]);
            }

            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Invalid input"]);
        }
        break;

    case 'DELETE':
        // Parse the input JSON body
        $input = json_decode(file_get_contents("php://input"), true);
    
        // Check if 'id' is provided
        if (isset($input['id'])) {
            $id = intval($input['id']);
    
            // Prepare the DELETE query
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $id);
    
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Product deleted successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete product"]);
            }
    
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Invalid input"]);
        }
        break;
        
        
    default:
        // Handle unsupported HTTP methods
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
        break;
}

// Close the database connection
$conn->close();
?>
