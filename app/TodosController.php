<?php
class TodoController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getTodos() {
        try {
            $query = $this->pdo->query('SELECT * FROM todos ORDER BY created_at');
            $todos = $query->fetchAll();
            http_response_code(200);
            echo json_encode(['status' => 'success', 'data' => ['todos' => $todos] ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function addTodo() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $title = $data['title'];
            $description = $data['description'];
            $query = $this->pdo->prepare('INSERT INTO todos (title, description) VALUES (?, ?)');
            $isSucces = $query->execute([$title, $description]);
            if ($isSucces) {
                $id = $this->pdo->lastInsertId();
                http_response_code(201);
                echo json_encode(['status' => 'success', 'data' => ['todo' => [
                    'id' => $id,
                    'title' => $title,
                    'description' => $description
                ]] ]);
                return;
            }
            http_response_code(400);
            echo json_encode(['status' => 'fail', 'message' => 'Todo wasnt added']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }

    public function deleteTodo($id) {
        try {
            $isExistQuery = $this->pdo->prepare('SELECT * FROM todos WHERE id = ?');
            $isExistQuery->execute([$id]);
            if (!$isExistQuery->fetch()) {
                http_response_code(404);
                echo json_encode(['status' => 'fail', 'message' => 'Todo with that id is not exist']);
                return;
            }
            $query = $this->pdo->prepare('DELETE FROM todos WHERE id = ?');
            if ($query->execute([$id])) {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Todo was deleted successfully']);
                return;
            }
            throw new Exception('Failed to execute statement');
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }
}
?>
