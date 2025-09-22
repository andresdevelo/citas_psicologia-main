<?php
class DB {
  public static function pdo(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $cfg = require __DIR__ . '/config.php';
    $dsn = "mysql:host={$cfg['db']['host']};dbname={$cfg['db']['name']};charset={$cfg['db']['charset']}";
    $pdo = new PDO($dsn, $cfg['db']['user'], $cfg['db']['pass'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    if (!empty($cfg['app']['timezone'])) {
      DB::pdo()->exec("SET time_zone='" . addslashes($cfg['app']['timezone']) . "'");
    }
    return $pdo;
  }
}