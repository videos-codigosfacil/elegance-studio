
<?php
session_start();

require_once __DIR__ . "/../config/conexion.php";

// Verificar sesión
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "Cliente") {
    header("Location: ../login.php");
    exit();
}

$id_cliente = $_SESSION["id_usuario"];
$nombre = htmlspecialchars($_SESSION["nombre"] ?? "Cliente");

// Consultar estadísticas del cliente
$consulta = $conexion->prepare(
    "SELECT
        COUNT(*) AS total,
        COALESCE(SUM(estado = 'Pendiente'), 0) AS pendientes,
        COALESCE(SUM(estado = 'Confirmada'), 0) AS confirmadas,
        COALESCE(SUM(estado = 'Rechazada'), 0) AS rechazadas
     FROM citas
     WHERE id_cliente = ?"
);

$consulta->bind_param("i", $id_cliente);
$consulta->execute();

$estadisticas = $consulta->get_result()->fetch_assoc();

$consulta->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mi espacio | Élégance Studio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/panel.css">
    <link rel="icon" type="image/png" href="/peluqueria/img/icon.png">

    <style>
        .stats-section {
            margin: 40px 0;
        }

        .stats-title {
            font-family: 'Playfair Display', serif;
            color: #35291f;
            font-size: 27px;
            margin-bottom: 22px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border: 1px solid #eee7de;
            transition: .3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,.06);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            margin-bottom: 15px;
        }

        .stat-card h6 {
            color: #81766c;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            color: #35291f;
        }

        .stat-total {
            background: #e9dfd2;
            color: #956d40;
        }

        .stat-pending {
            background: #f5ebd9;
            color: #a67f50;
        }

        .stat-confirmed {
            background: #e8efe5;
            color: #527b60;
        }

        .stat-rejected {
            background: #f4e4e1;
            color: #a64e4e;
        }

        @media(max-width: 850px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 500px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="dashboard">

    <!-- MENÚ LATERAL -->
    <aside class="sidebar">

        <a href="../index.php" class="brand">
            ÉLÉGANCE<span>.</span>
            <small>HAIR STUDIO</small>
        </a>

        <div class="sidebar-label">MENÚ PRINCIPAL</div>

        <a href="inicio.php" class="side-link active">
            <i class="bi bi-grid"></i>
            <span>Inicio</span>
        </a>

        <a href="reservar.php" class="side-link">
            <i class="bi bi-calendar-plus"></i>
            <span>Reservar una cita</span>
        </a>

        <a href="mis_citas.php" class="side-link">
            <i class="bi bi-calendar-check"></i>
            <span>Mis citas</span>
        </a>

        <div class="sidebar-bottom">

            <div class="help-card">
                <i class="bi bi-stars"></i>
                <h6>¿Necesitas ayuda?</h6>
                <p>Estamos aquí para ayudarte a disfrutar tu experiencia.</p>
            </div>

            <a href="../cerrar_sesion.php" class="side-link logout">
                <i class="bi bi-box-arrow-left"></i>
                <span>Cerrar sesión</span>
            </a>

        </div>

    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">

        <!-- BARRA SUPERIOR -->
        <header class="topbar">

            <div>
                <span class="topbar-subtitle">TU ESPACIO PERSONAL</span>
                <h6>Panel del cliente</h6>
            </div>

            <div class="user-info">

                <div class="user-avatar">
                    <i class="bi bi-person"></i>
                </div>

                <div>
                    <strong><?php echo $nombre; ?></strong>
                    <small>Cliente</small>
                </div>

            </div>

        </header>

        <!-- BIENVENIDA -->
        <section class="welcome-banner">

            <div class="welcome-content">

                <span class="welcome-tag">
                    <i class="bi bi-stars"></i> BIENVENIDO A ÉLÉGANCE
                </span>

                <h1>Hola, <?php echo $nombre; ?>.</h1>

                <p>
                    Nos alegra tenerte aquí. Este es tu espacio para
                    organizar tus visitas y disfrutar de nuestros
                    servicios de belleza.
                </p>

                <a href="reservar.php" class="welcome-button">
                    Reservar una cita
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

            <div class="welcome-decoration">
                <i class="bi bi-flower1"></i>
            </div>

        </section>

        <!-- ESTADÍSTICAS -->
        <section class="stats-section">

            <h2 class="stats-title">Resumen de mis citas</h2>

            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon stat-total">
                        <i class="bi bi-calendar2-week"></i>
                    </div>
                    <h6>Total de citas</h6>
                    <div class="stat-number">
                        <?php echo $estadisticas["total"]; ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-pending">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h6>Pendientes</h6>
                    <div class="stat-number">
                        <?php echo $estadisticas["pendientes"]; ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-confirmed">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h6>Confirmadas</h6>
                    <div class="stat-number">
                        <?php echo $estadisticas["confirmadas"]; ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-rejected">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <h6>Rechazadas</h6>
                    <div class="stat-number">
                        <?php echo $estadisticas["rechazadas"]; ?>
                    </div>
                </div>

            </div>

        </section>

        <!-- ACCESOS RÁPIDOS -->
        <section class="dashboard-section">

            <div class="section-header">
                <div>
                    <span class="section-label">GESTIONA TU EXPERIENCIA</span>
                    <h2>¿Qué deseas hacer hoy?</h2>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-md-6">

                    <div class="action-card">

                        <div class="action-icon">
                            <i class="bi bi-calendar-plus"></i>
                        </div>

                        <span class="card-number">01 / RESERVACIONES</span>

                        <h3>Reserva tu próxima cita</h3>

                        <p>
                            Elige el servicio que deseas y solicita
                            una fecha para tu próxima visita.
                        </p>

                        <a href="reservar.php" class="card-link">
                            Crear una reserva
                            <i class="bi bi-arrow-up-right"></i>
                        </a>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="action-card">

                        <div class="action-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>

                        <span class="card-number">02 / MIS VISITAS</span>

                        <h3>Consulta tus citas</h3>

                        <p>
                            Revisa las solicitudes que has realizado
                            y consulta el estado de tus reservas.
                        </p>

                        <a href="mis_citas.php" class="card-link">
                            Ver mis citas
                            <i class="bi bi-arrow-up-right"></i>
                        </a>

                    </div>

                </div>

            </div>

        </section>

        <!-- INFORMACIÓN -->
        <section class="experience-section">

            <div class="experience-icon">
                <i class="bi bi-gem"></i>
            </div>

            <div>
                <span class="section-label">ÉLÉGANCE STUDIO</span>

                <h3>Un momento especial comienza contigo.</h3>

                <p>
                    Queremos que cada visita sea una experiencia
                    agradable. Organiza tus citas desde tu espacio
                    personal de manera sencilla.
                </p>
            </div>

        </section>

        <!-- PIE DE PÁGINA -->
        <footer class="dashboard-footer">

            <span>© 2026 ÉLÉGANCE STUDIO</span>

            <span>Tu belleza, nuestra inspiración.</span>

        </footer>

    </main>

</div>

</body>
</html>
