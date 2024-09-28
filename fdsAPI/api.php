<?php
header("Content-Type: application/json");

require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

$id = isset($request[1]) ? intval($request[1]) : null;

switch ($method) {
    // 1. GET ALL USERS
    case 'GET':
        if ($id) {
            // 2. GET SINGLE USER BY ID
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'User not found']);
            }
        } else {
            // Get all users
            $stmt = $pdo->query("SELECT * FROM Users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        }
        break;

    // 3. INSERT A SINGLE USER
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($input['firstname'], $input['lastname'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid input']);
            break;
        }

        $stmt = $pdo->prepare("INSERT INTO Users (firstname, lastname) VALUES (?, ?)");
        $result = $stmt->execute([$input['firstname'], $input['lastname']]);

        if ($result) {
            echo json_encode(['message' => 'User created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create user']);
        }
        break;

    // 4. UPDATE A SINGLE USER
    case 'PUT':
        if ($id) {
            $input = json_decode(file_get_contents("php://input"), true);

            if (!isset($input['firstname'], $input['lastname'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid input']);
                break;
            }

            $stmt = $pdo->prepare("UPDATE Users SET firstname = ?, lastname = ? WHERE id = ?");
            $result = $stmt->execute([$input['firstname'], $input['lastname'], $id]);

            if ($result) {
                echo json_encode(['message' => 'User updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to update user']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'User ID required']);
        }
        break;

    // 5. DELETE A SINGLE USER
    case 'DELETE':
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM Users WHERE id = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                echo json_encode(['message' => 'User deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to delete user']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'User ID required']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>
