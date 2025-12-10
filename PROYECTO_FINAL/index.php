<?php
include 'db.php';

// --- LÓGICA PHP ---
$mensaje = "";
$datos_factura = null; // Variable para activar el PDF

// 1. Registrar Mascota (Con Cédula y Correo)
if (isset($_POST['crear_mascota'])) {
    $sql = "INSERT INTO mascotas (nombre_mascota, especie, nombre_propietario, cedula, correo, telefono) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$_POST['mascota'], $_POST['especie'], $_POST['propietario'], $_POST['cedula'], $_POST['correo'], $_POST['tel']])){
        $mensaje = "Paciente registrado correctamente.";
    }
}

// 2. Agendar Cita
if (isset($_POST['crear_cita'])) {
    $pdo->prepare("INSERT INTO citas (mascota_id, fecha, motivo) VALUES (?, ?, ?)")
        ->execute([$_POST['mascota_id'], $_POST['fecha'], $_POST['motivo']]);
    $mensaje = "Cita agendada.";
}

// 3. Historial
if (isset($_POST['crear_historial'])) {
    $pdo->prepare("INSERT INTO historial (mascota_id, tipo, descripcion) VALUES (?, ?, ?)")
        ->execute([$_POST['mascota_id'], $_POST['tipo'], $_POST['descripcion']]);
    $mensaje = "Historial actualizado.";
}

// 4. Facturar y Preparar PDF
if (isset($_POST['crear_factura'])) {
    $monto = $_POST['monto'];
    $servicio = $_POST['servicio'];
    $mascota_id = $_POST['mascota_id'];

    // Guardar en BD
    $pdo->prepare("INSERT INTO facturas (mascota_id, servicio, monto) VALUES (?, ?, ?)")
        ->execute([$mascota_id, $servicio, $monto]);
    
    // Obtener datos del cliente para el PDF
    $stmt = $pdo->prepare("SELECT * FROM mascotas WHERE id = ?");
    $stmt->execute([$mascota_id]);
    $cliente = $stmt->fetch();

    $itbms = $monto * 0.07;
    $total = $monto + $itbms;

    // Preparamos los datos para enviarlos a JavaScript
    $datos_factura = [
        'folio' => rand(1000, 9999),
        'fecha' => date('d/m/Y'),
        'cliente' => $cliente['nombre_propietario'],
        'cedula' => $cliente['cedula'],
        'correo' => $cliente['correo'],
        'mascota' => $cliente['nombre_mascota'],
        'servicio' => $servicio,
        'subtotal' => number_format($monto, 2),
        'itbms' => number_format($itbms, 2),
        'total' => number_format($total, 2)
    ];
    $mensaje = "Factura generada y descargada.";
}

// Consultas Generales
$mascotas = $pdo->query("SELECT * FROM mascotas ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clínica Veterinaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style> 
        body { background-color: #f8f9fa; } 
        .navbar { background-color: #0d6efd; }
        .card-header { font-weight: bold; background-color: #e9ecef; }
        .btn-custom { width: 100%; margin-top: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fs-3">Clínica Veterinaria</span>
    </div>
</nav>

<div class="container">
    
    <?php if($mensaje): ?>
        <div class="alert alert-info text-center shadow-sm"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="row g-4">
        
        <div class="col-lg-5">
            
            <div class="accordion shadow-sm" id="accordionPanel">
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelRegistro">
                            Registro de Pacientes
                        </button>
                    </h2>
                    <div id="panelRegistro" class="accordion-collapse collapse show" data-bs-parent="#accordionPanel">
                        <div class="accordion-body">
                            <form method="POST">
                                <div class="mb-2">
                                    <label class="form-label">Datos de la Mascota</label>
                                    <div class="row g-2">
                                        <div class="col-6"><input type="text" name="mascota" class="form-control" placeholder="Nombre" required></div>
                                        <div class="col-6">
                                            <select name="especie" class="form-control">
                                                <option value="Perro">Perro</option>
                                                <option value="Gato">Gato</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Datos del Propietario</label>
                                    <input type="text" name="propietario" class="form-control mb-2" placeholder="Nombre Completo" required>
                                    <div class="row g-2">
                                        <div class="col-6"><input type="text" name="cedula" class="form-control" placeholder="Cédula" required></div>
                                        <div class="col-6"><input type="text" name="tel" class="form-control" placeholder="Teléfono"></div>
                                    </div>
                                    <input type="email" name="correo" class="form-control mt-2" placeholder="Correo Electrónico">
                                </div>
                                <button type="submit" name="crear_mascota" class="btn btn-primary btn-custom">Guardar Paciente</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelCitas">
                            Agendar Cita
                        </button>
                    </h2>
                    <div id="panelCitas" class="accordion-collapse collapse" data-bs-parent="#accordionPanel">
                        <div class="accordion-body">
                            <form method="POST">
                                <select name="mascota_id" class="form-control mb-2" required>
                                    <option value="">Seleccionar Paciente...</option>
                                    <?php foreach($mascotas as $m): ?>
                                        <option value="<?= $m['id'] ?>"><?= $m['nombre_mascota'] ?> - <?= $m['nombre_propietario'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="datetime-local" name="fecha" class="form-control mb-2" required>
                                <input type="text" name="motivo" class="form-control mb-2" placeholder="Motivo" required>
                                <button type="submit" name="crear_cita" class="btn btn-secondary btn-custom">Agendar</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelHistorial">
                            Historial Médico / Vacunas
                        </button>
                    </h2>
                    <div id="panelHistorial" class="accordion-collapse collapse" data-bs-parent="#accordionPanel">
                        <div class="accordion-body">
                            <form method="POST">
                                <select name="mascota_id" class="form-control mb-2" required>
                                    <option value="">Seleccionar Paciente...</option>
                                    <?php foreach($mascotas as $m): ?>
                                        <option value="<?= $m['id'] ?>"><?= $m['nombre_mascota'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="tipo" class="form-control mb-2">
                                    <option value="Consulta">Consulta</option>
                                    <option value="Vacuna">Vacuna</option>
                                </select>
                                <textarea name="descripcion" class="form-control mb-2" placeholder="Detalles del procedimiento..." required></textarea>
                                <button type="submit" name="crear_historial" class="btn btn-secondary btn-custom">Guardar Historial</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#panelFactura">
                            Facturación
                        </button>
                    </h2>
                    <div id="panelFactura" class="accordion-collapse collapse" data-bs-parent="#accordionPanel">
                        <div class="accordion-body">
                            <form method="POST">
                                <select name="mascota_id" class="form-control mb-2" required>
                                    <option value="">Seleccionar Paciente...</option>
                                    <?php foreach($mascotas as $m): ?>
                                        <option value="<?= $m['id'] ?>"><?= $m['nombre_mascota'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="servicio" class="form-control mb-2" placeholder="Descripción del Servicio" required>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="monto" class="form-control" placeholder="Subtotal" required>
                                </div>
                                <button type="submit" name="crear_factura" class="btn btn-success btn-custom">Facturar y Descargar PDF</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">Pacientes Registrados</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 text-center" style="font-size: 0.9rem;">
                            <thead class="table-dark"><tr><th>Mascota</th><th>Dueño</th><th>Cédula</th><th>Contacto</th></tr></thead>
                            <tbody>
                                <?php foreach($mascotas as $m): ?>
                                <tr>
                                    <td><?= $m['nombre_mascota'] ?> (<?= $m['especie'] ?>)</td>
                                    <td><?= $m['nombre_propietario'] ?></td>
                                    <td><?= $m['cedula'] ?></td>
                                    <td><?= $m['telefono'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">Últimos Movimientos</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 text-center" style="font-size: 0.9rem;">
                        <thead class="table-secondary"><tr><th>Fecha</th><th>Tipo</th><th>Detalle</th></tr></thead>
                        <tbody>
                            <?php 
                            $logs = $pdo->query("
                                SELECT 'Cita' as tipo, fecha, motivo as detalle FROM citas
                                UNION ALL
                                SELECT tipo, fecha, descripcion as detalle FROM historial
                                ORDER BY fecha DESC LIMIT 5
                            ");
                            while($row = $logs->fetch()): ?>
                            <tr>
                                <td><?= substr($row['fecha'], 0, 10) ?></td>
                                <td><?= $row['tipo'] ?></td>
                                <td><?= $row['detalle'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Función invocada desde PHP si hay datos de factura
    function generarPDF(datos) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Encabezado
        doc.setFontSize(22);
        doc.setTextColor(40);
        doc.text("Clínica Veterinaria", 105, 20, null, null, "center");
        
        doc.setFontSize(12);
        doc.text("RUC: 8-000-0000 DV 00", 105, 30, null, null, "center");
        doc.text("Panamá", 105, 36, null, null, "center");

        // Línea divisoria
        doc.setLineWidth(0.5);
        doc.line(20, 45, 190, 45);

        // Datos del Cliente
        doc.setFontSize(10);
        doc.text(`Fecha: ${datos.fecha}`, 20, 55);
        doc.text(`Factura #: ${datos.folio}`, 150, 55);
        
        doc.text(`Cliente: ${datos.cliente}`, 20, 65);
        doc.text(`Cédula: ${datos.cedula}`, 20, 71);
        doc.text(`Correo: ${datos.correo}`, 20, 77);
        doc.text(`Mascota: ${datos.mascota}`, 120, 65);

        // Tabla Simple de Detalles
        doc.setFillColor(240, 240, 240);
        doc.rect(20, 90, 170, 10, 'F');
        doc.setFont(undefined, 'bold');
        doc.text("Descripción del Servicio", 25, 96);
        doc.text("Total", 170, 96);

        doc.setFont(undefined, 'normal');
        doc.text(datos.servicio, 25, 110);
        doc.text("$" + datos.subtotal, 170, 110);

        // Totales (Cálculos ITBMS)
        let y = 140;
        doc.line(120, y, 190, y);
        
        doc.text("Subtotal:", 130, y + 10);
        doc.text("$" + datos.subtotal, 170, y + 10);
        
        doc.text("ITBMS (7%):", 130, y + 18);
        doc.text("$" + datos.itbms, 170, y + 18);
        
        doc.setFont(undefined, 'bold');
        doc.setFontSize(14);
        doc.text("TOTAL:", 130, y + 30);
        doc.text("$" + datos.total, 170, y + 30);

        // Pie de página
        doc.setFontSize(8);
        doc.text("Gracias por su preferencia.", 105, 280, null, null, "center");

        // Descargar
        doc.save(`Factura_${datos.folio}.pdf`);
    }
</script>

<?php if($datos_factura): ?>
<script>
    generarPDF(<?= json_encode($datos_factura) ?>);
</script>
<?php endif; ?>

</body>
</html>