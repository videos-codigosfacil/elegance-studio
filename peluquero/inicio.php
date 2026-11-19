
<?php
session_start();

require_once __DIR__ . "/../config/conexion.php";

// Verificar sesión del peluquero
if (
    !isset($_SESSION["id_usuario"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "Peluquero"
) {
    header("Location: ../login.php");
    exit();
}

$id_peluquero = $_SESSION["id_usuario"];
$nombre = $_SESSION["nombre"] ?? "Peluquero";

// Obtener estadísticas reales de las citas
$sql = $conexion->prepare(
    "SELECT
        COUNT(*) AS total,
        COALESCE(SUM(estado = 'Pendiente'), 0) AS pendientes,
        COALESCE(SUM(estado = 'Confirmada'), 0) AS confirmadas,
        COALESCE(SUM(estado = 'Rechazada'), 0) AS rechazadas,
        COALESCE(SUM(estado = 'Cancelada'), 0) AS canceladas
     FROM citas
     WHERE id_peluquero = ?"
);

if (!$sql) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$sql->bind_param("i", $id_peluquero);
$sql->execute();

$estadisticas = $sql->get_result()->fetch_assoc();

$sql->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel del peluquero | Élégance Studio</title>

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

        .navbar {
            background: #30251e;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            letter-spacing: 2px;
        }

        .logo span {
            color: #c8a477;
        }

        .logo small {
            display: block;
            font-family: 'DM Sans', sans-serif;
            font-size: 9px;
            letter-spacing: 5px;
            color: #d2c2b0;
        }

        .saludo-nav {
            color: #e8ddd1;
            font-size: 12px;
        }

        .contenido {
            max-width: 1150px;
            margin: 65px auto;
            padding: 0 25px;
        }

        .etiqueta {
            color: #b18a5c;
            font-size: 11px;
            letter-spacing: 4px;
            font-weight: 700;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 46px;
            margin-top: 15px;
        }

        .bienvenida {
            font-size: 16px;
            color: #75685d;
            margin-top: 12px;
        }

        .hero {
            margin-top: 40px;
            padding: 45px;
            background: #e9dfd2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
        }

        .hero h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            max-width: 500px;
        }

        .hero p {
            color: #75685d;
            font-size: 13px;
            max-width: 430px;
            line-height: 1.8;
        }

        .hero-icon {
            font-size: 100px;
            color: #b18a5c;
            padding-right: 30px;
        }

        .seccion-titulo {
            font-family: 'Playfair Display', serif;
            font-size: 27px;
            margin-top: 55px;
            margin-bottom: 25px;
        }

        .estadisticas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 18px;
        }

        .estadistica {
            background: white;
            padding: 25px;
            border: 1px solid #eee7de;
            transition: .3s;
        }

        .estadistica:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,.06);
        }

        .estadistica-icono {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            margin-bottom: 18px;
        }

        .icon-total {
            background: #e9dfd2;
            color: #956d40;
        }

        .icon-pendiente {
            background: #f5ebd9;
            color: #a67f50;
        }

        .icon-confirmada {
            background: #e8efe5;
            color: #527b60;
        }

        .icon-rechazada {
            background: #f4e4e1;
            color: #a64e4e;
        }

        .icon-cancelada {
            background: #eeeeee;
            color: #666666;
        }

        .estadistica h3 {
            font-size: 12px;
            color: #81766c;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .numero {
            font-family: 'Playfair Display', serif;
            font-size: 35px;
            color: #35291f;
        }

        .tarjetas {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px;
        }

        .tarjeta {
            background: white;
            padding: 32px;
            border: 1px solid #eee7de;
            transition: .3s;
            text-decoration: none;
            color: #35291f;
            display: block;
        }

        .tarjeta:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,.07);
            color: #35291f;
        }

        .icono {
            width: 55px;
            height: 55px;
            background: #f1e8dc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #a67f50;
            font-size: 24px;
            margin-bottom: 22px;
        }

        .tarjeta h3 {
            font-family: 'Playfair Display', serif;
            font-size: 23px;
        }

        .tarjeta p {
            color: #81766c;
            font-size: 13px;
            line-height: 1.8;
        }

        .enlace {
            color: #a67f50;
            font-size: 12px;
            font-weight: 700;
        }

        footer {
            margin-top: 80px;
            padding: 30px;
            text-align: center;
            background: #30251e;
            color: #cfc1b3;
            font-size: 11px;
            letter-spacing: 1px;
        }

        @media(max-width: 850px) {
            .estadisticas {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 600px) {
            .contenido {
                margin-top: 40px;
            }

            h1 {
                font-size: 35px;
            }

            .hero {
                padding: 30px;
            }

            .hero-icon {
                display: none;
            }

            .tarjetas {
                grid-template-columns: 1fr;
            }

            .saludo-nav {
                display: none;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="logo">
        ÉLÉGANCE<span>.</span>
        <small>HAIR STUDIO</small>
    </div>

    <div class="saludo-nav">
        <i class="bi bi-person-circle"></i>
        Panel profesional
    </div>
</nav>

<main class="contenido">

    <span class="etiqueta">ESPACIO PROFESIONAL</span>

    <h1>Panel del peluquero</h1>

    <p class="bienvenida">
        Bienvenido,
        <strong><?php echo htmlspecialchars($nombre); ?></strong>.
        Nos alegra tenerte en nuestro equipo.
    </p>

    <section class="hero">
        <div>
            <span class="etiqueta">ÉLÉGANCE STUDIO</span>

            <h2>La belleza está en cada detalle.</h2>

            <p>
                Administra tus reservas, organiza tus horarios
                y ofrece una experiencia especial a cada cliente.
            </p>
        </div>

        <div class="hero-icon">
            <i class="bi bi-scissors"></i>
        </div>
    </section>

    <h2 class="seccion-titulo">Resumen de citas</h2>

    <section class="estadisticas">

        <div class="estadistica">
            <div class="estadistica-icono icon-total">
                <i class="bi bi-calendar2-week"></i>
            </div>
            <h3>Total de citas</h3>
            <div class="numero">
                <?php echo $estadisticas["total"]; ?>
            </div>
        </div>

        <div class="estadistica">
            <div class="estadistica-icono icon-pendiente">
                <i class="bi bi-clock-history"></i>
            </div>
            <h3>Pendientes</h3>
            <div class="numero">
                <?php echo $estadisticas["pendientes"]; ?>
            </div>
        </div>

        <div class="estadistica">
            <div class="estadistica-icono icon-confirmada">
                <i class="bi bi-calendar-check"></i>
            </div>
            <h3>Confirmadas</h3>
            <div class="numero">
                <?php echo $estadisticas["confirmadas"]; ?>
            </div>
        </div>

        <div class="estadistica">
            <div class="estadistica-icono icon-rechazada">
                <i class="bi bi-calendar-x"></i>
            </div>
            <h3>Rechazadas</h3>
            <div class="numero">
                <?php echo $estadisticas["rechazadas"]; ?>
            </div>
        </div>

        <div class="estadistica">
            <div class="estadistica-icono icon-cancelada">
                <i class="bi bi-calendar-x"></i>
            </div>
            <h3>Canceladas</h3>
            <div class="numero">
                <?php echo $estadisticas["canceladas"]; ?>
            </div>
        </div>

    </section>

    <h2 class="seccion-titulo">Administración</h2>

    <div class="tarjetas">

        <a href="gestionar_citas.php" class="tarjeta">
            <div class="icono">
                <i class="bi bi-calendar2-check"></i>
            </div>

            <h3>Gestionar citas</h3>

            <p>
                Consulta las reservas de tus clientes,
                confirma solicitudes y administra las citas.
            </p>

            <span class="enlace">
                ADMINISTRAR CITAS →
            </span>
        </a>

        <a href="../cerrar_sesion.php" class="tarjeta">
            <div class="icono">
                <i class="bi bi-box-arrow-right"></i>
            </div>

            <h3>Cerrar sesión</h3>

            <p>
                Finaliza tu sesión de forma segura
                cuando termines de trabajar.
            </p>

            <span class="enlace">
                SALIR DEL SISTEMA →
            </span>
        </a>

    </div>

</main>

<footer>
    ÉLÉGANCE STUDIO · CUIDAMOS CADA DETALLE
</footer>

</body>
</html>
