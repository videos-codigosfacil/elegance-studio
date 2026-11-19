
<?php
session_start();

require_once __DIR__ . "/../config/conexion.php";

// Verificar sesión del cliente
if (
    !isset($_SESSION["id_usuario"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "Cliente"
) {
    header("Location: ../login.php");
    exit();
}

$id_cliente = $_SESSION["id_usuario"];
$nombre = htmlspecialchars($_SESSION["nombre"] ?? "Cliente");

$mensaje = "";
$tipo_mensaje = "";

// Cancelar una cita
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cancelar_cita"])) {

    $id_cita = intval($_POST["id_cita"]);

    if ($id_cita > 0) {

        $cancelar = $conexion->prepare(
            "UPDATE citas
             SET estado = 'Cancelada'
             WHERE id = ?
             AND id_cliente = ?
             AND estado IN ('Pendiente', 'Confirmada')"
        );

        $cancelar->bind_param("ii", $id_cita, $id_cliente);
        $cancelar->execute();

        if ($cancelar->affected_rows > 0) {
            $mensaje = "Tu cita ha sido cancelada correctamente.";
            $tipo_mensaje = "exito";
        } else {
            $mensaje = "No se pudo cancelar la cita. Puede que ya haya sido modificada.";
            $tipo_mensaje = "error";
        }

        $cancelar->close();

    } else {
        $mensaje = "La cita seleccionada no es válida.";
        $tipo_mensaje = "error";
    }
}

// Consultar citas del cliente
$consulta = $conexion->prepare(
    "SELECT citas.id, citas.fecha_hora, citas.servicio,
            citas.estado, peluqueros.nombre AS peluquero
     FROM citas
     INNER JOIN peluqueros
     ON citas.id_peluquero = peluqueros.id
     WHERE citas.id_cliente = ?
     ORDER BY citas.fecha_hora DESC"
);

$consulta->bind_param("i", $id_cliente);
$consulta->execute();

$resultado = $consulta->get_result();

$tituloPagina = "Mis citas | Élégance Studio";

require_once __DIR__ . "/../includes/header.php";
?>

<style>
body {
    background: #f7f4ef;
    font-family: 'DM Sans', sans-serif;
    color: #35291f;
}

.contenido {
    max-width: 1150px;
    margin: 55px auto;
    padding: 0 25px;
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
    margin-bottom: 35px;
}

.tabla-card {
    background: white;
    padding: 25px;
    border: 1px solid #eee7de;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 850px;
    border-collapse: collapse;
}

th {
    background: #f7f4ef;
    padding: 16px;
    text-align: left;
    font-size: 12px;
    color: #74685c;
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

.Cancelada {
    background: #eeeeee;
    color: #666666;
}

.boton-cancelar {
    background: #fbe3e3;
    color: #b43b3b;
    border: none;
    padding: 9px 13px;
    font-size: 12px;
    cursor: pointer;
    border-radius: 4px;
}

.boton-cancelar:hover {
    background: #b43b3b;
    color: white;
}

.boton-reservar {
    display: inline-block;
    margin-top: 25px;
    background: #b89468;
    color: white;
    padding: 13px 22px;
    text-decoration: none;
    font-size: 13px;
}

.boton-reservar:hover {
    background: #35291f;
    color: white;
}

.volver {
    display: block;
    margin-top: 20px;
    color: #a7845d;
    text-decoration: none;
    font-size: 13px;
}

.alerta {
    padding: 14px 18px;
    margin-bottom: 20px;
    font-size: 13px;
}

.alerta.exito {
    background: #e0f3e7;
    color: #287746;
}

.alerta.error {
    background: #fbe3e3;
    color: #b43b3b;
}

@media(max-width: 600px) {
    h1 {
        font-size: 33px;
    }

    .tabla-card {
        padding: 12px;
    }
}
</style>

<main class="contenido">

    <span class="subtitulo">TU ESPACIO PERSONAL</span>

    <h1>Mis citas</h1>

    <p class="descripcion">
        Hola, <?php echo $nombre; ?>.
        Aquí puedes consultar tus reservas y su estado.
    </p>

    <?php if ($mensaje !== ""): ?>
        <div class="alerta <?php echo $tipo_mensaje; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="tabla-card">

        <table>

            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Peluquero</th>
                    <th>Fecha y hora</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($resultado->num_rows > 0): ?>

                    <?php while ($cita = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($cita["servicio"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($cita["peluquero"]); ?>
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

                                <?php if (
                                    $cita["estado"] === "Pendiente" ||
                                    $cita["estado"] === "Confirmada"
                                ): ?>

                                    <form method="POST"
                                          onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta cita?');">

                                        <input type="hidden"
                                               name="id_cita"
                                               value="<?php echo intval($cita["id"]); ?>">

                                        <button type="submit"
                                                name="cancelar_cita"
                                                class="boton-cancelar">
                                            <i class="bi bi-x-circle"></i>
                                            Cancelar
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span>Sin acciones</span>

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

    <a href="reservar.php" class="boton-reservar">
        <i class="bi bi-calendar-plus"></i>
        Reservar una cita
    </a>

    <a href="inicio.php" class="volver">
        ← Volver a mi panel
    </a>

</main>

<?php
$consulta->close();

require_once __DIR__ . "/../includes/footer.php";
?>
