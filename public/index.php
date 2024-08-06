<?php
header('Content-Type: application/json');
require __DIR__ .'/../app/bootstrap.php';
require __DIR__ .'/../app/TodosController.php';

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$uriSegments = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
$todoController = new TodoController($pdo);

if (count($uriSegments) < 2 || $uriSegments[0] !== 'api' || $uriSegments[1] !== 'todos') {
    http_response_code(404);
    echo json_encode(['status' => 'failed', 'message' => 'Not Found']);
    exit;
}

    switch ($method) {
        case 'GET':
            $todoController->getTodos();
            break;
        case 'POST':
            $todoController->addTodo();
            break;
        case 'DELETE':
            if (isset($uriSegments[2])) {
                $id = $uriSegments[2];
                $todoController->deleteTodo($id);
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'failed', 'message' => 'ID is required']);
            }
            break;    
        default:
            echo json_encode(['status' => 'failed', 'message' => 'URL is not exist']);
            break;
    }
?>