<?php
require_once __DIR__ . '/../db.php';

class CitaService {
  /** Verifica si el psicólogo está disponible para la fecha/hora y duración dadas */
  public static function psicologoDisponible(int $psicologo_id, string $fecha, string $hora, int $duracion): bool {
    $pdo = DB::pdo();
    // 1) Existe horario disponible que cubra el intervalo?
    $stmt = $pdo->prepare(
      "SELECT * FROM horarios
       WHERE psicologo_id = ? AND fecha = ? AND disponible = 1
         AND hora_inicio <= ? AND hora_fin >= ADDTIME(?, SEC_TO_TIME(?*60))");
    $stmt->execute([$psicologo_id, $fecha, $hora, $hora, $duracion]);
    $hayHorario = (bool)$stmt->fetch();
    if (!$hayHorario) return false;

    // 2) No choca con otra cita?
    $stmt = $pdo->prepare(
      "SELECT 1 FROM citas
       WHERE psicologo_id = ? AND fecha = ? AND estado IN ('pendiente','confirmada','atendida')
         AND (
           (hora <= ? AND ADDTIME(hora, SEC_TO_TIME(duracion_minutos*60)) > ?) OR
           (? < ADDTIME(hora, SEC_TO_TIME(duracion_minutos*60)) AND ADDTIME(?, SEC_TO_TIME(?*60)) > hora)
         )");
    $stmt->execute([$psicologo_id, $fecha, $hora, $hora, $hora, $hora, $duracion]);
    return !$stmt->fetch();
  }
}