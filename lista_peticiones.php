<?php
require 'db_config.php';

// Obtener las peticiones desde la base de datos
$sql = "SELECT * FROM peticiones";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Peticiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Función para manejar las acciones de pausa y reanudar
        function manejarAccion(accion, id) {
            let url = accion === 'pausar' ? 'pausar_peticion.php' : 'reanudar_peticion.php';
            let mensaje = accion === 'pausar' ? '¿Estás seguro de que deseas pausar esta petición?' : '¿Estás seguro de que deseas reanudar esta petición?';

            if (confirm(mensaje)) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { id: id },
                    success: function(response) {
                        let result = JSON.parse(response);
                        if (result.exito) {
                            alert('Acción realizada con éxito');
                            location.reload(); // Recargar la página para actualizar los estados
                        } else {
                            alert('Error: ' + result.mensaje);
                        }
                    },
                    error: function() {
                        alert('Ocurrió un error al procesar la solicitud.');
                    }
                });
            }
        }

        // Función para actualizar el tiempo restante dinámicamente
        function actualizarTiempoRestante() {
            const elementos = document.querySelectorAll(".tiempo-restante");
            elementos.forEach((elemento) => {
                const estado = elemento.getAttribute("data-estado");
                if (estado === "pausada") {
                    elemento.textContent = "Pausado";
                    return;
                }

                const fechaInicio = new Date(elemento.getAttribute("data-fecha-inicio"));
                const tiempoPausadoSegundos = parseInt(elemento.getAttribute("data-tiempo-pausado"), 10);
                const tiempoRestanteSegundos = parseInt(elemento.getAttribute("data-tiempo-restante"), 10);

                const ahora = new Date();
                const tiempoRestante = (fechaInicio.getTime() + ((tiempoRestanteSegundos - tiempoPausadoSegundos) * 1000)) - ahora.getTime();

                if (tiempoRestante > 0) {
                    const dias = Math.floor(tiempoRestante / (1000 * 60 * 60 * 24));
                    const horas = Math.floor((tiempoRestante % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutos = Math.floor((tiempoRestante % (1000 * 60 * 60)) / (1000 * 60));
                    const segundos = Math.floor((tiempoRestante % (1000 * 60)) / 1000);

                    elemento.textContent = `${dias} días, ${horas} horas, ${minutos} minutos, ${segundos} segundos`;
                } else {
                    elemento.textContent = "Tiempo expirado";
                }
            });
        }

        // Actualizar el tiempo restante cada segundo
        setInterval(actualizarTiempoRestante, 1000);
    </script>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Lista de Peticiones</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo de Petición</th>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Correo</th>
                    <th>Fecha de Inicio</th>
                    <th>Tiempo Restante</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['tipo'] ?></td>
                        <td><?= $row['nombre'] ?></td>
                        <td><?= $row['documento'] ?></td>
                        <td><?= $row['correo'] ?></td>
                        <td><?= $row['fecha_inicio'] ?></td>
                        <td class="tiempo-restante" 
                            data-fecha-inicio="<?= $row['fecha_inicio'] ?>"
                            data-tiempo-restante="<?= $row['tiempo_restante_segundos'] ?>"
                            data-tiempo-pausado="<?= $row['tiempo_pausado_segundos'] ?>"
                            data-estado="<?= $row['estado'] ?>">
                            <!-- El tiempo restante se actualizará dinámicamente -->
                            Cargando...
                        </td>
                        <td>
                            <?php if ($row['estado'] === 'activa'): ?>
                                <button onclick="manejarAccion('pausar', <?= $row['id'] ?>)" 
                                        class="btn btn-warning btn-sm">Pausar</button>
                            <?php else: ?>
                                <button onclick="manejarAccion('reanudar', <?= $row['id'] ?>)" 
                                        class="btn btn-success btn-sm">Reanudar</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
