<?php
class Validator {
  public static function required(array $payload, array $fields) {
    $missing = [];
    foreach ($fields as $f) {
      if (!isset($payload[$f]) || $payload[$f] === '') { $missing[] = $f; }
    }
    if ($missing) {
      Response::json(['error' => 'Campos requeridos faltantes', 'campos' => $missing], 422);
    }
  }

  public static function date($s) {
    return (bool) DateTime::createFromFormat('Y-m-d', $s);
  }

  public static function time($s) {
    return (bool) DateTime::createFromFormat('H:i', $s);
  }
}