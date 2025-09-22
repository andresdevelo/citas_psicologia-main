<?php
class Response {
  public static function json($data, int $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
  }
}