<?php
// helpers.php
function config() {
    static $c = null;
    if ($c === null) $c = require __DIR__ . '/config.php';
    return $c;
}

function db(){
    static $pdo = null;
    if($pdo) return $pdo;
    $conf = config();
    $dsn = "mysql:host={$conf['db_host']};dbname={$conf['db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $conf['db_user'], $conf['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    return $pdo;
}

function genToken($len = 32){ return bin2hex(random_bytes((int)($len/2))); }

function base_url($path = '') {
    $conf = config();
    $url = rtrim($conf['base_url'], '/');
    if ($path === '') return $url;
    return $url . '/' . ltrim($path, '/');
}

function h($s){ return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
