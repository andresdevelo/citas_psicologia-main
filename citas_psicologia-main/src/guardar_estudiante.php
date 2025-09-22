<?php
// Configuración de conexión
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "citas_psicologia";

// Función para registrar errores en un log
function log_error($mensaje) {
    $logFile = __DIR__ . '/registro.log';
    $fecha = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$fecha] $mensaje\n", FILE_APPEND);
}

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    log_error("Conexión fallida: " . $conn->connect_error);
    http_response_code(500);
    exit("Error de conexión a la base de datos.");
}

// Sanitizar y validar datos recibidos
function limpiar($valor) {
    return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
}

$campos = [
    'nombre', 'programa', 'semestre', 'horario', 'rol',
    'nombreRemitido', 'rolRemitido', 'motivo', 'telefonoRemitido', 'telefonoRemite', 'correo_remitido'
];
$datos = [];
foreach ($campos as $campo) {
    $datos[$campo] = isset($_POST[$campo]) ? limpiar($_POST[$campo]) : '';
}

// Validación básica de campos obligatorios
$faltantes = [];
foreach (['nombre','programa','semestre','horario','rol','nombreRemitido','rolRemitido','telefonoRemitido','telefonoRemite','correo_remitido'] as $obligatorio) {
    if (empty($datos[$obligatorio])) {
        $faltantes[] = $obligatorio;
    }
}
if (count($faltantes) > 0) {
    http_response_code(400);
    exit("Faltan campos obligatorios: " . implode(', ', $faltantes));
}

// Validación de teléfono (solo números y longitud razonable)
if (!preg_match('/^\d{7,15}$/', $datos['telefonoRemitido']) || !preg_match('/^\d{7,15}$/', $datos['telefonoRemite'])) {
    http_response_code(400);
    exit("Teléfonos inválidos.");
}

// Normalizar teléfono del remitido con +57
$telefonoWhatsApp = '+57' . $datos['telefonoRemitido'];

// Insertar datos en la tabla
$sql = "INSERT INTO estudiantes 
        (nombre, programa, semestre, horario, fecha_registro, rol, nombre_remitido, rol_remitido, motivo, telefono_remitido, telefono_remite, correo_remitido) 
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    log_error("Error en prepare: " . $conn->error);
    http_response_code(500);
    exit("Error interno al preparar la consulta.");
}

$stmt->bind_param(
    "sssssssssss",
    $datos['nombre'],
    $datos['programa'],
    $datos['semestre'],
    $datos['horario'],
    $datos['rol'],
    $datos['nombreRemitido'],
    $datos['rolRemitido'],
    $datos['motivo'],
    $datos['telefonoRemitido'],
    $datos['telefonoRemite'],
    $datos['correo_remitido']
);

// Función para enviar correo vía API Brevo
function enviarCorreo($datos, $telefonoWhatsApp) {
    $to_email = 'desarrollohumano@elyonyireh.edu.co';
    $to_name  = 'psicosocial';
    $subject  = 'Nueva remisión psicosocial';

   $content = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 2px 8px #e2e8f0; padding:32px; background:#f9fafb;">
        <h2 style="color:#4A90E2; text-align:center; margin-bottom:24px;">Nueva Remisión Psicosocial</h2>
        <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
            <tr><td><b>Nombre de quien remite:</b></td><td>' . $datos['nombre'] . '</td></tr>
            <tr><td><b>Rol de quien remite:</b></td><td>' . $datos['rol'] . '</td></tr>
            <tr><td><b>Nombre del remitido:</b></td><td>' . $datos['nombreRemitido'] . '</td></tr>
            <tr><td><b>Rol del remitido:</b></td><td>' . $datos['rolRemitido'] . '</td></tr>
            <tr><td><b>Programa:</b></td><td>' . $datos['programa'] . '</td></tr>
            <tr><td><b>Semestre:</b></td><td>' . $datos['semestre'] . '</td></tr>
            <tr><td><b>Horario:</b></td><td>' . $datos['horario'] . '</td></tr>
            <tr><td><b>Motivo de remisión:</b></td><td>' . nl2br($datos['motivo']) . '</td></tr>
            <tr><td><b>Teléfono de quien remite:</b></td><td>' . $datos['telefonoRemite'] . '</td></tr>
            <tr><td><b>Teléfono del remitido:</b></td><td>' . $datos['telefonoRemitido'] . '</td></tr>
            <tr><td><b>Correo del remitido:</b></td><td>' . $datos['correo_remitido'] . '</td></tr>
        </table>

        <!-- Botones Responsivos con colores de psicología -->
        <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:15px; margin-top:32px;">
            <a href="mailto:' . $datos['correo_remitido'] . '?subject=Respuesta a remisión psicosocial" 
               style="flex:1 1 240px; max-width:100%; background:#4A90E2; color:#fff; padding:14px 20px; border-radius:6px; text-decoration:none; font-weight:bold; text-align:center; box-sizing:border-box;">
               📧 Responder por Correo
            </a>
            <br>
            <a href="https://wa.me/' . ltrim($telefonoWhatsApp, '+') . '" 
               style="flex:1 1 240px; max-width:100%; background:#50C878; color:#fff; padding:14px 20px; border-radius:6px; text-decoration:none; font-weight:bold; text-align:center; box-sizing:border-box;">
               💬 Responder por WhatsApp
            </a>
           
        </div>

        <p style="color:#64748b; font-size:13px; text-align:center; margin-top:32px;">
            Este mensaje fue generado automáticamente por el sistema de remisiones psicosociales.
        </p>
    </div>';


    $apiKey = '';

    $data = [
        'sender' => [
            'name'  => 'Remisión Psicosocial',
            'email' => 'automaticoscorreos21@gmail.com'
        ],
        'to' => [
            ['email' => $to_email, 'name' => $to_name]
        ],
        'subject' => $subject,
        'htmlContent' => '<html><body>' . $content . '</body></html>'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'api-key: ' . $apiKey,
        'content-type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode < 200 || $httpcode >= 300) {
        log_error("Error al enviar correo: HTTP $httpcode - Respuesta: $response");
    }
    curl_close($ch);
}

// Ejecutar y responder
if ($stmt->execute()) {
    enviarCorreo($datos, $telefonoWhatsApp);
    echo "OK";
} else {
    log_error("Error al insertar: " . $stmt->error);
    http_response_code(500);
    echo "Error al guardar el registro.";
}

$stmt->close();
$conn->close();
?>
