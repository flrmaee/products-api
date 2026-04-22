<?php
header("Content-Type: application/json");
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$id = isset($request[1]) ? intval($request[1]) : null;

$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        if ($id) {
            $result = $conn->query("SELECT * FROM products WHERE id = $id");
            $product = $result->fetch_assoc();

            echo json_encode($product);
        } else {
            $result = $conn->query("SELECT * FROM products");
            $products = [];

            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }

            echo json_encode($products);
        }
        break;
    case 'POST':
        if (!empty($data['product']) && !empty($data['price'])) {

            $product = $conn->real_escape_string($data['product']);
            $price = $conn->real_escape_string($data['price']);

            $sql = "INSERT INTO products (product, price) VALUES ('$product', '$price')";

            if ($conn->query($sql)) {
                echo json_encode(["message" => "Product created successfully"]);
            } else {
                echo json_encode(["error" => "Failed to create product"]);
            }

        } else {
            echo json_encode(["error" => "Invalid input"]);
        }
        break;

    // ✏️ PUT: Update product
    case 'PUT':
        if ($id && !empty($data['product']) && !empty($data['price'])) {

            $product = $conn->real_escape_string($data['product']);
            $price = $conn->real_escape_string($data['price']);

            $sql = "UPDATE products SET product='$product', price='$price' WHERE id=$id";

            if ($conn->query($sql)) {
                echo json_encode(["message" => "Product updated successfully"]);
            } else {
                echo json_encode(["error" => "Failed to update product"]);
            }

        } else {
            echo json_encode(["error" => "Invalid input or missing ID"]);
        }
        break;
    case 'DELETE':
        if ($id) {

            $sql = "DELETE FROM products WHERE id=$id";

            if ($conn->query($sql)) {
                echo json_encode(["message" => "Product deleted successfully"]);
            } else {
                echo json_encode(["error" => "Failed to delete product"]);
            }

        } else {
            echo json_encode(["error" => "ID is required"]);
        }
        break;
    default:
        echo json_encode(["error" => "Invalid request method"]);
        break;
}
?>
