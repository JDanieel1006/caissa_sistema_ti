<?php
// config/database.php

// define('DB_HOST',    '65.99.252.172');
// define('DB_NAME',    'caissamx_ti_helpdesk');
// define('DB_USER',    'caissamx_ti');
// define('DB_PASS',    'oRp+JoLV$By6PVdk');
// define('DB_CHARSET', 'utf8');

define('DB_HOST',    'mysql');
define('DB_NAME',    'caissa_ti');
define('DB_USER',    'root');
define('DB_PASS',    'root');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die(json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]));
            }
        }
        return self::$instance;
    }
}
