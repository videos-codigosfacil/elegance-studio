<?php
session_start();

require_once __DIR__ . "/../config/conexion.php";

// Comprobar que el cliente haya iniciado sesión
if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['rol']) ||
    $_SESSION['rol'] != "Cliente"
) {
    header("Location: ../login.php");
    exit();
}

$id_cliente = $_SESSION['id_usuario'];
$mensaje = "";
$tipo = "";

// Obtener los peluqueros registrados
$sql = $conexion->query(
    "SELECT id, nombre FROM peluqueros ORDER BY nombre"
);

$peluqueros = [];

if ($sql) {
    while ($fila = $sql->fetch_assoc()) {
        $peluqueros[] = $fila;
    }
}

// Procesar la reserva
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_peluquero = intval($_POST["id_peluquero"] ?? 0);
    $servicio = trim($_POST["servicio"] ?? "");
    $fecha_hora_original = trim($_POST["fecha_hora"] ?? "");

    // Convertir fecha al formato de MySQL
    $fecha_hora = str_replace("T", " ", $fecha_hora_original);

    // Validar que los campos estén completos
    if (
        $id_peluquero <= 0 ||
        $servicio == "" ||
        $fecha_hora_original == ""
    ) {

        $mensaje = "Completa todos los campos.";
        $tipo = "danger";

    } else {

        // Validar que el servicio sea uno de los permitidos
        $serviciosPermitidos = [
            "Corte de cabello",
            "Peinado",
            "Tinte",
            "Tratamiento capilar",
            "Alisado",
            "Manicure"
        ];

        if (!in_array($servicio, $serviciosPermitidos, true)) {

            $mensaje = "Selecciona un servicio válido.";
            $tipo = "danger";

        } else {

            // Validar fecha y hora
            $timestamp = strtotime($fecha_hora);

            if ($timestamp === false || $timestamp <= time()) {

                $mensaje = "Selecciona una fecha y hora futuras.";
                $tipo = "danger";

            } else {

                // Verificar que el peluquero exista
                $verificar = $conexion->prepare(
                    "SELECT id FROM peluqueros WHERE id = ?"
                );

                $verificar->bind_param("i", $id_peluquero);
                $verificar->execute();

                $resultado = $verificar->get_result();

                if ($resultado->num_rows == 0) {

                    $mensaje = "El peluquero seleccionado no existe.";
                    $tipo = "danger";

                } else {

                    // Comprobar si el peluquero ya tiene una cita
                    // pendiente o confirmada en ese horario
                    $disponibilidad = $conexion->prepare(
                        "SELECT id FROM citas
                         WHERE id_peluquero = ?
                         AND fecha_hora = ?
                         AND estado IN ('Pendiente', 'Confirmada')
                         LIMIT 1"
                    );

                    $disponibilidad->bind_param(
                        "is",
                        $id_peluquero,
                        $fecha_hora
                    );

                    $disponibilidad->execute();

                    $resultadoDisponibilidad =
                        $disponibilidad->get_result();

                    if ($resultadoDisponibilidad->num_rows > 0) {

                        $mensaje = "El peluquero ya tiene una cita en ese horario. Selecciona otro.";
                        $tipo = "danger";

                    } else {

                        // Guardar la cita en la base de datos
                        $insertar = $conexion->prepare(
                            "INSERT INTO citas
                            (id_cliente, id_peluquero, fecha_hora, servicio)
                            VALUES (?, ?, ?, ?)"
                        );

                        $insertar->bind_param(
                            "iiss",
                            $id_cliente,
                            $id_peluquero,
                            $fecha_hora,
                            $servicio
                        );

                        if ($insertar->execute()) {

                            $mensaje = "¡Tu cita fue reservada correctamente!";
                            $tipo = "success";

                        } else {

                            $mensaje = "No se pudo guardar la cita.";
                            $tipo = "danger";
                        }

                        $insertar->close();
                    }

                    $disponibilidad->close();
                }

                $verificar->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reservar cita | Élégance Studio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

        .contenedor {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .tarjeta {
            width: 100%;
            max-width: 950px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            box-shadow: 0 15px 45px rgba(0,0,0,.08);
        }

        .imagen {
            background:
                linear-gradient(rgba(40,29,20,.35), rgba(40,29,20,.7)),
                url("https://images.unsplash.com/photo-1560066984-138dadb4c035?w=900")
                center/cover;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 40px;
            color: white;
        }

        .imagen small {
            color: #e2c39d;
            letter-spacing: 3px;
        }

        .imagen h2 {
            font-family: 'Playfair Display', serif;
            font-size: 40px;
            margin-top: 15px;
        }

        .imagen p {
            font-size: 13px;
            line-height: 1.8;
        }

        .formulario {
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .marca {
            font-family: 'Playfair Display', serif;
            font-size: 23px;
            letter-spacing: 2px;
            margin-bottom: 35px;
        }

        .marca span {
            color: #bd9b70;
        }

        .subtitulo {
            color: #bd9b70;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 35px;
            margin: 12px 0;
        }

        .descripcion {
            font-size: 12px;
            color: #81766c;
            margin-bottom: 25px;
        }

        label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input, select {
            width: 100%;
            padding: 13px;
            border: 1px solid #e5ded5;
            background: white;
            margin-bottom: 20px;
            font-size: 12px;
            color: #35291f;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #bd9b70;
        }

        .boton {
            width: 100%;
            padding: 15px;
            border: none;
            background: #35291f;
            color: white;
            font-size: 12px;
            transition: .3s;
        }

        .boton:hover {
            background: #bd9b70;
        }

        .volver {
            text-align: center;
            margin-top: 22px;
        }

        .volver a {
            color: #a7845d;
            font-size: 12px;
            text-decoration: none;
        }

        @media(max-width: 750px) {
            .tarjeta {
                grid-template-columns: 1fr;
            }

            .imagen {
                min-height: 230px;
            }

            .formulario {
                padding: 35px 25px;
            }
        }
    </style>
</head>

<body>

<div class="contenedor">

    <div class="tarjeta">

        <div class="imagen">
            <small>ÉLÉGANCE STUDIO</small>

            <h2>Tu momento de belleza.</h2>

            <p>
                Elige tu servicio, selecciona a tu peluquero
                y reserva el momento perfecto para ti.
            </p>
        </div>

        <div class="formulario">

            <div class="marca">
                ÉLÉGANCE<span>.</span>
            </div>

            <span class="subtitulo">RESERVACIONES</span>

            <h1>Reserva tu cita</h1>

            <p class="descripcion">
                Completa los siguientes datos para solicitar tu cita.
            </p>

            <?php if ($mensaje != ""): ?>
                <div class="alert alert-<?php echo $tipo; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <label>Selecciona tu peluquero</label>

                <select name="id_peluquero" required>
                    <option value="">Seleccionar peluquero</option>

                    <?php foreach ($peluqueros as $p): ?>
                        <option value="<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['nombre']); ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <label>Servicio</label>

                <select name="servicio" required>
                    <option value="">Seleccionar servicio</option>
                    <option value="Corte de cabello">Corte de cabello</option>
                    <option value="Peinado">Peinado</option>
                    <option value="Tinte">Tinte</option>
                    <option value="Tratamiento capilar">Tratamiento capilar</option>
                    <option value="Alisado">Alisado</option>
                    <option value="Manicure">Manicure</option>
                </select>

                <label>Fecha y hora</label>

                <input
                    type="datetime-local"
                    name="fecha_hora"
                    min="<?php echo date('Y-m-d\TH:i'); ?>"
                    required
                >

                <button type="submit" class="boton">
                    Confirmar reserva →
                </button>

            </form>

            <div class="volver">
                <a href="inicio.php">← Volver al panel del cliente</a>
            </div>

        </div>

    </div>

</div>

</body>
</html>
