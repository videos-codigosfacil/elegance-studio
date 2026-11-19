<?php
require_once __DIR__ . "/../config/conexion.php";

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];
    $confirmar = $_POST["confirmar"];

    if (empty($nombre) || empty($correo) || empty($password)) {

        $mensaje = "Completa todos los campos.";
        $tipo_mensaje = "danger";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Ingresa un correo electrónico válido.";
        $tipo_mensaje = "danger";

    } elseif (strlen($password) < 6) {

        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
        $tipo_mensaje = "danger";

    } elseif ($password !== $confirmar) {

        $mensaje = "Las contraseñas no coinciden.";
        $tipo_mensaje = "danger";

    } else {

        // Comprobar si el correo ya está registrado
        $consulta = $conexion->prepare(
            "SELECT id FROM clientes WHERE correo = ?"
        );

        $consulta->bind_param("s", $correo);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje = "Este correo ya se encuentra registrado.";
            $tipo_mensaje = "danger";

        } else {

            // Encriptar la contraseña antes de guardarla
            $password_segura = password_hash($password, PASSWORD_DEFAULT);

            $insertar = $conexion->prepare(
                "INSERT INTO clientes (nombre, correo, `contraseña`)
                 VALUES (?, ?, ?)"
            );

            $insertar->bind_param(
                "sss",
                $nombre,
                $correo,
                $password_segura
            );

            if ($insertar->execute()) {

                $mensaje = "¡Cuenta creada correctamente! Ya puedes iniciar sesión.";
                $tipo_mensaje = "success";

            } else {

                $mensaje = "No se pudo crear la cuenta. Inténtalo nuevamente.";
                $tipo_mensaje = "danger";

            }

            $insertar->close();
        }

        $consulta->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear cuenta | Élégance Studio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" type="image/png" href="/peluqueria/img/icon.png">

    <style>
        body {
            background: #f8f5f0;
            min-height: 100vh;
        }

        .registro-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 110px 20px 50px;
        }

        .registro-card {
            background: white;
            max-width: 950px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            box-shadow: 0 15px 50px rgba(0,0,0,0.08);
        }

        .registro-imagen {
            background:
                linear-gradient(rgba(40,30,23,.35), rgba(40,30,23,.35)),
                url("https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=900")
                center/cover;
            min-height: 600px;
            display: flex;
            align-items: flex-end;
            padding: 40px;
            color: white;
        }

        .registro-imagen h2 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
        }

        .registro-formulario {
            padding: 55px 45px;
        }

        .registro-formulario h1 {
            font-family: 'Playfair Display', serif;
            font-size: 35px;
            margin: 15px 0;
        }

        .registro-formulario p {
            color: #746a61;
            font-size: 13px;
        }

        .registro-formulario label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .registro-formulario .form-control {
            border-radius: 2px;
            padding: 12px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .registro-formulario .btn {
            width: 100%;
            padding: 13px;
            border-radius: 2px;
            background: #332820;
            color: white;
            margin-top: 10px;
        }

        .registro-formulario .btn:hover {
            background: #bd9b70;
        }

        .registro-login {
            text-align: center;
            margin-top: 25px;
        }

        .registro-login a {
            color: #a98459;
            font-weight: 600;
            text-decoration: none;
        }

        @media(max-width: 768px) {
            .registro-card {
                grid-template-columns: 1fr;
            }

            .registro-imagen {
                min-height: 230px;
            }

            .registro-formulario {
                padding: 35px 25px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            ÉLÉGANCE<span>.</span>
            <small>HAIR STUDIO</small>
        </a>

        <a href="index.php" class="text-decoration-none text-dark">
            <i class="bi bi-arrow-left"></i> Volver al inicio
        </a>
    </div>
</nav>

<div class="registro-container">

    <div class="registro-card">

        <div class="registro-imagen">
            <div>
                <span class="section-subtitle">ÉLÉGANCE STUDIO</span>
                <h2>Tu belleza comienza con una experiencia.</h2>
                <p>Estamos felices de recibirte.</p>
            </div>
        </div>

        <div class="registro-formulario">

            <span class="section-subtitle">BIENVENIDO</span>

            <h1>Crear cuenta</h1>

            <p>Regístrate para reservar tus próximas citas.</p>

            <?php if ($mensaje != ""): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="registro.php">

                <label>Nombre completo</label>
                <input
                    type="text"
                    name="nombre"
                    class="form-control"
                    placeholder="Escribe tu nombre"
                    value="<?php echo htmlspecialchars($_POST["nombre"] ?? ""); ?>"
                    required>

                <label>Correo electrónico</label>
                <input
                    type="email"
                    name="correo"
                    class="form-control"
                    placeholder="ejemplo@correo.com"
                    value="<?php echo htmlspecialchars($_POST["correo"] ?? ""); ?>"
                    required>

                <label>Contraseña</label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Mínimo 6 caracteres"
                    minlength="6"
                    required>

                <label>Confirmar contraseña</label>
                <input
                    type="password"
                    name="confirmar"
                    class="form-control"
                    placeholder="Repite tu contraseña"
                    minlength="6"
                    required>

                <button type="submit" class="btn">
                    Crear mi cuenta <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <div class="registro-login">
                <p>¿Ya tienes una cuenta?</p>
                <a href="login.php">Iniciar sesión</a>
            </div>

        </div>

    </div>

</div>

</body>
</html>
