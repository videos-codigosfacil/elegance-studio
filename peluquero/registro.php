
<?php
require_once __DIR__ . "/../config/conexion.php";

$mensaje = "";
$tipo = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Ingresa un correo electrónico válido.";
        $tipo = "danger";

    } elseif (strlen($password) < 6) {

        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
        $tipo = "danger";

    } else {

        // Comprobar si el correo ya existe
        $consulta = $conexion->prepare(
            "SELECT id FROM peluqueros WHERE correo = ?"
        );

        $consulta->bind_param("s", $correo);
        $consulta->execute();

        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje = "Este correo ya está registrado.";
            $tipo = "danger";

        } else {

            // Proteger la contraseña
            $password_segura = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Guardar peluquero
            $insertar = $conexion->prepare(
                "INSERT INTO peluqueros (nombre, correo, `contraseña`)
                 VALUES (?, ?, ?)"
            );

            $insertar->bind_param(
                "sss",
                $nombre,
                $correo,
                $password_segura
            );

            if ($insertar->execute()) {

                $mensaje = "¡Peluquero registrado correctamente!";
                $tipo = "success";

            } else {

                $mensaje = "Error al registrar. Inténtalo nuevamente.";
                $tipo = "danger";
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

    <title>Registro de peluquero | Élégance Studio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/peluqueria/img/icon.png">
    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f7f4ef;
            font-family: 'DM Sans', sans-serif;
            color: #35291f;
        }

        .container-register {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 45px 20px;
        }

        .register-card {
            width: 100%;
            max-width: 950px;
            min-height: 570px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            box-shadow: 0 15px 50px rgba(0,0,0,.08);
        }

        .register-image {
            background:
                linear-gradient(rgba(45,32,23,.45), rgba(45,32,23,.65)),
                url("https://images.unsplash.com/photo-1560066984-138dadb4c035?w=900")
                center/cover;

            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 40px;
            color: white;
        }

        .register-image span {
            font-size: 11px;
            letter-spacing: 3px;
            color: #e2c39d;
        }

        .register-image h2 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            margin-top: 15px;
        }

        .register-image p {
            font-size: 13px;
            color: #eee5dc;
            line-height: 1.8;
        }

        .register-form {
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 21px;
            letter-spacing: 2px;
            margin-bottom: 35px;
        }

        .brand span {
            color: #bd9b70;
        }

        .subtitle {
            color: #bd9b70;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .register-form h1 {
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            margin: 12px 0;
        }

        .description {
            font-size: 12px;
            color: #81766c;
            margin-bottom: 25px;
        }

        .register-form label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .register-form input {
            width: 100%;
            padding: 13px;
            border: 1px solid #e5ded5;
            margin-bottom: 19px;
            font-size: 12px;
            outline: none;
        }

        .register-form input:focus {
            border-color: #bd9b70;
        }

        .register-button {
            width: 100%;
            padding: 14px;
            border: none;
            background: #35291f;
            color: white;
            font-size: 12px;
            transition: .3s;
        }

        .register-button:hover {
            background: #bd9b70;
        }

        .back-link {
            text-align: center;
            margin-top: 22px;
        }

        .back-link a {
            color: #a7845d;
            font-size: 12px;
            text-decoration: none;
        }

        @media(max-width: 750px) {

            .register-card {
                grid-template-columns: 1fr;
            }

            .register-image {
                min-height: 220px;
            }

            .register-form {
                padding: 35px 25px;
            }
        }

    </style>

</head>

<body>

<div class="container-register">

    <div class="register-card">

        <!-- IMAGEN -->
        <div class="register-image">

            <span>ÉLÉGANCE STUDIO</span>

            <h2>El talento crea belleza.</h2>

            <p>
                Forma parte de nuestro equipo y ayuda a crear
                experiencias especiales para nuestros clientes.
            </p>

        </div>


        <!-- FORMULARIO -->
        <div class="register-form">

            <div class="brand">
                ÉLÉGANCE<span>.</span>
                <small style="display:block;font-size:9px;letter-spacing:4px;">
                    HAIR STUDIO
                </small>
            </div>

            <span class="subtitle">REGISTRO DE PERSONAL</span>

            <h1>Crear cuenta</h1>

            <p class="description">
                Ingresa tus datos para registrar al peluquero.
            </p>

            <?php if ($mensaje != ""): ?>

                <div class="alert alert-<?php echo $tipo; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>

            <?php endif; ?>


            <form method="POST" action="">

                <label>Nombre completo</label>

                <input
                    type="text"
                    name="nombre"
                    placeholder="Nombre del peluquero"
                    required>

                <label>Correo electrónico</label>

                <input
                    type="email"
                    name="correo"
                    placeholder="ejemplo@correo.com"
                    required>

                <label>Contraseña</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Mínimo 6 caracteres"
                    minlength="6"
                    required>

                <button type="submit" class="register-button">
                    Registrar peluquero
                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <div class="back-link">
                <a href="../index.php">
                    <i class="bi bi-arrow-left"></i>
                    Volver al inicio
                </a>
            </div>

        </div>

    </div>

</div>

</body>
</html>
