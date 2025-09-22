# Sistema de Gestión de Citas de Psicología

Este proyecto es una aplicación web para la gestión de citas en un consultorio de psicología. Permite registrar estudiantes, psicólogos, servicios y gestionar horarios y citas.

## Estructura del proyecto

- `index.html`: Página principal de la aplicación.
- `script.js`: Lógica de frontend.
- `src/`: Código fuente principal.
  - `config.php`: Configuración de la base de datos.
  - `db.php`: Conexión a la base de datos.
  - `guardar_estudiante.php`: Registro de estudiantes.
  - `PHPMailer_autoload.php`: Envío de correos electrónicos.
  - `registro.log`: Archivo de logs.
  - `router.php`: Enrutador de peticiones.
  - `controllers/`: Controladores de la lógica de negocio.
    - `EstudiantesController.php`
    - `HorariosController.php`
    - `PsicologosController.php`
    - `ServiciosController.php`
  - `services/`: Servicios de la aplicación.
    - `CitaService.php`
  - `utils/`: Utilidades y helpers.
    - `Response.php`
    - `Validator.php`

## Requisitos

- PHP >= 7.4
- MySQL
- Servidor web (XAMPP recomendado)

## Instalación

1. Clona el repositorio en tu servidor local:
   ```
   git clone <URL-del-repositorio>
   ```
2. Configura la base de datos en `src/config.php`.
3. Inicia el servidor web y accede a `index.html` desde tu navegador.

## Uso

- Registra estudiantes y psicólogos.
- Agenda y gestiona citas.
- Consulta servicios y horarios disponibles.

## Estructura de la base de datos

A continuación se muestra un ejemplo de código SQL para crear las tablas principales:

```sql
CREATE DATABASE citas_psicologia;
USE citas_psicologia;

CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE psicologos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    psicologo_id INT,
    fecha DATE,
    hora_inicio TIME,
    hora_fin TIME,
    disponible BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (psicologo_id) REFERENCES psicologos(id)
);

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT,
    psicologo_id INT,
    servicio_id INT,
    horario_id INT,
    fecha DATETIME,
    estado VARCHAR(50) DEFAULT 'pendiente',
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id),
    FOREIGN KEY (psicologo_id) REFERENCES psicologos(id),
    FOREIGN KEY (servicio_id) REFERENCES servicios(id),
    FOREIGN KEY (horario_id) REFERENCES horarios(id)
);
```

Puedes modificar los campos según las necesidades de tu proyecto.

## Créditos

Desarrollado por el equipo de citas_psicologia-main.

## Licencia

Este proyecto está bajo la licencia MIT.
