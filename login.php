
<?php
session_start();
require_once "config/conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    // Buscar primero en los clientes
    $consulta = $conexion->prepare(
        "SELECT id, nombre, correo, `contraseña`
         FROM clientes WHERE correo = ?"
    );

    $consulta->bind_param("s", $correo);
    $consulta->execute();
    $resultado = $consulta->get_result();

    $usuario = $resultado->fetch_assoc();
    $consulta->close();

    if ($usuario && password_verify($password, $usuario["contraseña"])) {

        $_SESSION["id_usuario"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["correo"] = $usuario["correo"];
        $_SESSION["rol"] = "Cliente";

        header("Location: cliente/inicio.php");
        exit();

    }

    // Si no es cliente, buscar en peluqueros
    $consulta = $conexion->prepare(
        "SELECT id, nombre, correo, `contraseña`
         FROM peluqueros WHERE correo = ?"
    );

    $consulta->bind_param("s", $correo);
    $consulta->execute();
    $resultado = $consulta->get_result();

    $usuario = $resultado->fetch_assoc();
    $consulta->close();

    if ($usuario && password_verify($password, $usuario["contraseña"])) {

        $_SESSION["id_usuario"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["correo"] = $usuario["correo"];
        $_SESSION["rol"] = "Peluquero";

        header("Location: peluquero/inicio.php");
        exit();

    }

    $mensaje = "Correo o contraseña incorrectos.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | Élégance Studio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" type="image/png" href="/peluqueria/img/icon.png">

    <style>
        body {
            background: #f8f5f0;
            min-height: 100vh;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 110px 20px 50px;
        }

        .login-card {
            background: white;
            max-width: 950px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            box-shadow: 0 15px 50px rgba(0,0,0,.08);
        }

        .login-image {
            background:
                linear-gradient(rgba(40,30,23,.4), rgba(40,30,23,.4)),
                url("https://images.unsplash.com/photo-1560066984-138dadb4c035?w=900")
                center/cover;
            min-height: 550px;
            display: flex;
            align-items: flex-end;
            padding: 40px;
            color: white;
        }

        .login-image h2 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
        }

        .login-form {
            padding: 65px 45px;
        }

        .login-form h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            margin: 18px 0;
        }

        .login-form p {
            color: #746a61;
            font-size: 13px;
        }

        .login-form label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .login-form .form-control {
            padding: 13px;
            border-radius: 2px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .login-form button {
            width: 100%;
            padding: 13px;
            border-radius: 2px;
            background: #332820;
            color: white;
            margin-top: 10px;
        }

        .login-form button:hover {
            background: #bd9b70;
        }

        .login-register {
            text-align: center;
            margin-top: 30px;
        }

        .login-register a {
            color: #a98459;
            text-decoration: none;
            font-weight: 600;
        }

        @media(max-width: 768px) {
            .login-card {
                grid-template-columns: 1fr;
            }

            .login-image {
                min-height: 220px;
            }

            .login-form {
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

<div class="login-container">

    <div class="login-card">

        <div class="login-image">
            <div>
                <span class="section-subtitle">ÉLÉGANCE STUDIO</span>
                <h2>Nos alegra tenerte de vuelta.</h2>
                <p>Tu próximo momento especial te espera.</p>
            </div>
        </div>

        <div class="login-form">

            <span class="section-subtitle">BIENVENIDO DE NUEVO</span>

            <h1>Iniciar sesión</h1>

            <p>Ingresa tus datos para acceder a tu cuenta.</p>

            <?php if ($mensaje != ""): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">

                <label>Correo electrónico</label>

                <input
                    type="email"
                    name="correo"
                    class="form-control"
                    placeholder="ejemplo@correo.com"
                    required>

                <label>Contraseña</label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Ingresa tu contraseña"
                    required>

                <button type="submit" class="btn">
                    Ingresar <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <div class="login-register">
                <p>¿Todavía no tienes una cuenta?</p>
                <a href="registro.php">Crear una cuenta</a>
            </div>

        </div>

    </div>

</div>

</body>
</html>
