<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "config.php";
require_once "model_user.php";

$database = new Database();
$db = $database->connect();

$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Get JSON input
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->name) && !empty($data->email)) {
        $user->name = $data->name;
        $user->email = $data->email;

        if ($user->create()) {
            echo json_encode(["message" => "User created successfully (stored procedure)."]);
        } else {
            echo json_encode(["message" => "Failed to create user."]);
        }
    } else {
        echo json_encode(["message" => "Incomplete data."]);
    }
} elseif ($method === 'GET') {
    $stmt = $user->read();
    $users = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $users[] = [
            "id" => $id,
            "name" => $name,
            "email" => $email
        ];
    }

    echo json_encode($users);
} else {
    echo json_encode(["message" => "Unsupported request method."]);
}
?>
