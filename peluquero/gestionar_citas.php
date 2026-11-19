
<?php
session_start();

require_once __DIR__ . "/../config/conexion.php";

// Verificar que sea un peluquero
if (
    !isset($_SESSION["id_usuario"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "Peluquero"
) {
    header("Location: ../login.php");
    exit();
}

$id_peluquero = $_SESSION["id_usuario"];
$mensaje = "";
$tipoMensaje = "success";

// Actualizar estado de una cita
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cita = intval($_POST["id_cita"] ?? 0);
    $estado = $_POST["estado"] ?? "";

    if ($id_cita > 0 && in_array($estado, ["Confirmada", "Rechazada"])) {

        $actualizar = $conexion->prepare(
            "UPDATE citas
             SET estado = ?
             WHERE id = ? AND id_peluquero = ? AND estado = 'Pendiente'"
        );

        $actualizar->bind_param(
            "sii",
            $estado,
            $id_cita,
            $id_peluquero
        );

        if ($actualizar->execute() && $actualizar->affected_rows > 0) {
            $mensaje = "La cita fue actualizada correctamente.";
        } else {
            $mensaje = "No se pudo actualizar la cita. Comprueba que siga pendiente.";
            $tipoMensaje = "warning";
        }

        $actualizar->close();

    } else {
        $mensaje = "La solicitud no es válida.";
        $tipoMensaje = "danger";
    }
}

// Consultar citas
$consulta = $conexion->prepare(
    "SELECT citas.id, citas.fecha_hora, citas.servicio,
            citas.estado, clientes.nombre AS cliente
     FROM citas
     INNER JOIN clientes
     ON citas.id_cliente = clientes.id
     WHERE citas.id_peluquero = ?
     ORDER BY citas.fecha_hora DESC"
);

$consulta->bind_param("i", $id_peluquero);
$consulta->execute();

$resultado = $consulta->get_result();

// Preparar encabezado reutilizable
$tituloPagina = "Gestionar citas | Élégance Studio";
require_once __DIR__ . "/../includes/header.php";
?>

<style>

    body {
        background: #f7f4ef;
        font-family: 'DM Sans', sans-serif;
        color: #35291f;
    }

    .barra-superior {
        background: #30251e;
        padding: 18px 7%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .marca {
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        letter-spacing: 2px;
        color: white;
        text-decoration: none;
    }

    .marca span {
        color: #c8a477;
    }

    .barra-superior a.volver-inicio {
        color: #e8ddd1;
        text-decoration: none;
        font-size: 13px;
    }

    .contenido {
        max-width: 1150px;
        margin: 60px auto;
        padding: 0 22px;
    }

    .subtitulo {
        color: #b18a5c;
        letter-spacing: 3px;
        font-size: 11px;
        font-weight: bold;
    }

    h1 {
        font-family: 'Playfair Display', serif;
        font-size: 42px;
        margin: 12px 0;
    }

    .descripcion {
        color: #81766c;
        font-size: 14px;
        margin-bottom: 35px;
    }

    .tabla-card {
        background: white;
        padding: 25px;
        box-shadow: 0 10px 35px rgba(0,0,0,.04);
        border: 1px solid #eee7de;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 750px;
    }

    th {
        background: #f7f4ef;
        padding: 17px;
        text-align: left;
        font-size: 12px;
        color: #74685c;
        font-weight: 700;
    }

    td {
        padding: 18px 16px;
        border-bottom: 1px solid #eee8df;
        font-size: 13px;
    }

    .estado {
        display: inline-block;
        padding: 7px 13px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
    }

    .Pendiente {
        background: #fff0d7;
        color: #a16b1e;
    }

    .Confirmada {
        background: #e0f3e7;
        color: #287746;
    }

    .Rechazada {
        background: #fbe3e3;
        color: #b43b3b;
    }

    .boton {
        border: none;
        padding: 9px 13px;
        font-size: 11px;
        color: white;
        margin: 3px;
        cursor: pointer;
        transition: .3s;
    }

    .confirmar {
        background: #527b60;
    }

    .confirmar:hover {
        background: #365b43;
    }

    .rechazar {
        background: #a64e4e;
    }

    .rechazar:hover {
        background: #843535;
    }

    .sin-acciones {
        color: #81766c;
        font-size: 12px;
    }

    .volver {
        display: inline-block;
        margin-top: 25px;
        color: #a7845d;
        text-decoration: none;
        font-size: 13px;
    }

    .volver:hover {
        color: #35291f;
    }

    @media(max-width: 600px) {
        h1 {
            font-size: 33px;
        }

        .tabla-card {
            padding: 12px;
        }

        .barra-superior {
            padding: 18px 5%;
        }
    }

</style>

<!-- BARRA SUPERIOR -->
<nav class="barra-superior">

    <a href="../index.php" class="marca">
        ÉLÉGANCE<span>.</span>
    </a>

    <a href="inicio.php" class="volver-inicio">
        <i class="bi bi-arrow-left"></i> Volver al panel
    </a>

</nav>

<!-- CONTENIDO -->
<main class="contenido">

    <span class="subtitulo">PANEL DEL PERSONAL</span>

    <h1>Gestionar citas</h1>

    <p class="descripcion">
        Consulta y administra las reservas de tus clientes.
    </p>

    <?php if ($mensaje != ""): ?>

        <div class="alert alert-<?php echo $tipoMensaje; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>

    <?php endif; ?>

    <div class="tabla-card">

        <table>

            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Servicio</th>
                    <th>Fecha y hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($resultado->num_rows > 0): ?>

                    <?php while ($cita = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($cita["cliente"]); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($cita["servicio"]); ?>
                            </td>

                            <td>
                                <?php echo date("d/m/Y H:i", strtotime($cita["fecha_hora"])); ?>
                            </td>

                            <td>

                                <span class="estado <?php echo htmlspecialchars($cita["estado"]); ?>">
                                    <?php echo htmlspecialchars($cita["estado"]); ?>
                                </span>

                            </td>

                            <td>

                                <?php if ($cita["estado"] == "Pendiente"): ?>

                                    <form method="POST" style="display:inline;">

                                        <input type="hidden" name="id_cita"
                                               value="<?php echo $cita["id"]; ?>">

                                        <input type="hidden" name="estado"
                                               value="Confirmada">

                                        <button type="submit" class="boton confirmar">
                                            <i class="bi bi-check-lg"></i>
                                            Confirmar
                                        </button>

                                    </form>

                                    <form method="POST" style="display:inline;">

                                        <input type="hidden" name="id_cita"
                                               value="<?php echo $cita["id"]; ?>">

                                        <input type="hidden" name="estado"
                                               value="Rechazada">

                                        <button type="submit" class="boton rechazar">
                                            <i class="bi bi-x-lg"></i>
                                            Rechazar
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="sin-acciones">
                                        Sin acciones disponibles
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-calendar-x fs-2 d-block mb-3"></i>
                            Todavía no tienes citas reservadas.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

    <a href="inicio.php" class="volver">
        <i class="bi bi-arrow-left"></i>
        Volver al panel
    </a>

</main>

<?php
$consulta->close();

require_once __DIR__ . "/../includes/footer.php";
?>
