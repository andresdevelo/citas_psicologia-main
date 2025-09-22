<?php
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../utils/Response.php';
require_once __DIR__.'/../utils/Validator.php';

class EstudiantesController {
  public static function list() {
    $rows = DB::pdo()->query('SELECT * FROM estudiantes ORDER BY creado_en DESC')->fetchAll();
    Response::json($rows);
  }
  public static function create($payload) {
    Validator::required($payload, ['identificacion','nombre','programa','email']);
    $sql = 'INSERT INTO estudiantes (identificacion,nombre,programa,email,telefono) VALUES (?,?,?,?,?)';
    $st = DB::pdo()->prepare($sql);
    $st->execute([$payload['identificacion'],$payload['nombre'],$payload['programa'],$payload['email'],$payload['telefono']??null]);
    Response::json(['id'=>DB::pdo()->lastInsertId()], 201);
  }
}