<?php
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../utils/Response.php';
require_once __DIR__.'/../utils/Validator.php';

class ServiciosController {
  public static function list() {
    $rows = DB::pdo()->query('SELECT * FROM servicios WHERE activo=1 ORDER BY nombre')->fetchAll();
    Response::json($rows);
  }
  public static function create($payload) {
    Validator::required($payload, ['nombre','duracion_minutos']);
    $sql = 'INSERT INTO servicios (nombre,duracion_minutos,costo,activo) VALUES (?,?,?,1)';
    $st = DB::pdo()->prepare($sql);
    $st->execute([$payload['nombre'], (int)$payload['duracion_minutos'], $payload['costo']??0]);
    Response::json(['id'=>DB::pdo()->lastInsertId()], 201);
  }
}