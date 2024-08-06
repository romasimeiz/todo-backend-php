<?php
    require_once __DIR__ . '/../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    $host = $_ENV['DB_HOST'];
    $db =   $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    
    $dsn = "mysql:host=$host;charset=utf8";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    error_reporting(E_ALL);
    ini_set('display_errors',1);

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $db");
        $pdo->exec("USE $db");
        $createTableQuery = "
            CREATE TABLE IF NOT EXISTS todos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description VARCHAR(255) NOT NULL
            )";
        $pdo->exec($createTableQuery);
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
?>