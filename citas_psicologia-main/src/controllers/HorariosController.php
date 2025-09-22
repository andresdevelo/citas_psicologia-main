<?php
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../utils/Response.php';
require_once __DIR__.'/../utils/Validator.php';

class HorariosController {
  public static function list() {
    $pdo = DB::pdo();
    $sql = 'SELECT h.*, p.nombre AS psicologo FROM horarios h JOIN psicologos p ON p.id = h.psicologo_id ORDER BY h.fecha, h.hora_inicio';
    $rows = $pdo->query($sql)->fetchAll();
    Response::json($rows);
  }
  public static function create($payload) {
    Validator::required($payload, ['psicologo_id','fecha','hora_inicio','hora_fin']);
    if (!Validator::date($payload['fecha']) || !Validator::time($payload['hora_inicio']) || !Validator::time($payload['hora_fin'])) {
      Response::json(['error'=>'Formato inválido (fecha: Y-m-d, hora: H:i)'], 422);
    }
}
}