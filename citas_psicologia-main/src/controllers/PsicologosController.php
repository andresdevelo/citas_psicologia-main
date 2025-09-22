<?php
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../utils/Response.php';
require_once __DIR__.'/../utils/Validator.php';

class PsicologosController {
  public static function list() {
    $rows = DB::pdo()->query('SELECT * FROM psicologos ORDER BY creado_en DESC')->fetchAll();
    Response::json($rows);
  }
  public static function create($payload) {
    Validator::required($payload, ['identificacion','nombre','especialidad','email']);
    $sql = 'INSERT INTO psicologos (identificacion,nombre,especialidad,email,telefono) VALUES (?,?,?,?,?)';
    $st = DB::pdo()->prepare($sql);
    $st->execute([$payload['identificacion'],$payload['nombre'],$payload['especialidad'],$payload['email'],$payload['telefono']??null]);
    Response::json(['id'=>DB::pdo()->lastInsertId()], 201);
  }
}