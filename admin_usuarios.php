<?php
include 'conexion.php';
include 'header.php';

// Manejo de eliminación de usuario
if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Eliminar registros relacionados en la tabla mensajes
        $sql_delete_mensajes = "DELETE FROM mensajes WHERE id_usuario = $userId";
        if (!$conn->query($sql_delete_mensajes)) {
            throw new Exception("Error al eliminar mensajes: " . $conn->error);
        }
        
        // Eliminar registros relacionados en la tabla operarios
        $sql_delete_operario = "DELETE FROM operarios WHERE id_usuario = $userId";
        if (!$conn->query($sql_delete_operario)) {
            throw new Exception("Error al eliminar operario: " . $conn->error);
        }
        
        // Eliminar el usuario de la base de datos
        $sql_delete_user = "DELETE FROM usuarios WHERE id = $userId";
        if (!$conn->query($sql_delete_user)) {
            throw new Exception("Error al eliminar usuario: " . $conn->error);
        }
        
        // Confirmar transacción
        $conn->commit();
        
        echo "<script>alert('Usuario eliminado correctamente.'); window.location.href='admin_usuarios.php';</script>";
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        echo "<script>alert('" . $e->getMessage() . "');</script>";
    }
}

$searchTerm = '';
$filterBy = 'nombre';
$page = 1;
$resultsPerPage = 10;

if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

if (isset($_GET['filter'])) {
    $filterBy = $_GET['filter'];
    if (!in_array($filterBy, ['id', 'nombre', 'correo'])) {
        $filterBy = 'nombre'; // Valor predeterminado
    }
}

if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}

$offset = ($page - 1) * $resultsPerPage;

$sql = "SELECT id, nombre, correo, rol FROM usuarios WHERE $filterBy LIKE '%$searchTerm%' LIMIT $resultsPerPage OFFSET $offset";
$result = $conn->query($sql);

// Verificar errores en la consulta
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Obtener el total de resultados para la paginación
$sql_total = "SELECT COUNT(*) as total FROM usuarios WHERE $filterBy LIKE '%$searchTerm%'";
$totalResult = $conn->query($sql_total);
if (!$totalResult) {
    die("Error en la consulta de conteo total: " . $conn->error);
}
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $resultsPerPage);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #0056b3;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .edit-btn, .delete-btn, .view-btn {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            margin-right: 5px;
            transition: background-color 0.3s;
            display: inline-block;
            text-align: center;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .edit-btn:hover {
            background-color: #45a049;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        .delete-btn:hover {
            background-color: #da190b;
        }

        .view-btn {
            background-color: #2196F3;
            color: white;
        }

        .view-btn:hover {
            background-color: #0b7dda;
        }

        .no-data {
            text-align: center;
            color: #777;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .form-select {
            margin-right: 10px;
        }

        .icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Administración de Usuarios</h2>
        
        <form method="GET" action="admin_usuarios.php">
            <div class="input-group">
                <select name="filter" class="form-select" aria-label="Filtro">
                    <option value="id" <?php if ($filterBy == 'id') echo 'selected'; ?>>ID</option>
                    <option value="nombre" <?php if ($filterBy == 'nombre') echo 'selected'; ?>>Nombre</option>
                    <option value="correo" <?php if ($filterBy == 'correo') echo 'selected'; ?>>Correo</option>
                </select>
                <input type="text" name="search" class="form-control" placeholder="Buscar usuarios" value="<?php echo $searchTerm; ?>">
                <button class="btn btn-outline-secondary" type="submit">Buscar</button>
            </div>
        </form>
        
        <div id="results">
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nombre']; ?></td>
                                <td><?php echo isset($row['correo']) ? $row['correo'] : 'No definido'; ?></td>
                                <td><?php echo $row['rol']; ?></td>
                                <td>
                                    <a href="#" class="view-btn" data-bs-toggle="modal" data-bs-target="#viewUserModal" data-id="<?php echo $row['id']; ?>" data-nombre="<?php echo $row['nombre']; ?>" data-correo="<?php echo $row['correo']; ?>" data-rol="<?php echo $row['rol']; ?>"><i class="icon bi bi-eye"></i>Ver</a>
                                    <a href="edit_usuario.php?id=<?php echo $row['id']; ?>" class="edit-btn"><i class="icon bi bi-pencil-square"></i>Editar</a>
                                    <a href="admin_usuarios.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');"><i class="icon bi bi-trash"></i>Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                <a class="page-link" href="admin_usuarios.php?page=<?php echo $i; ?>&search=<?php echo $searchTerm; ?>&filter=<?php echo $filterBy; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php else: ?>
                <p class="no-data">No se encontraron usuarios.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para ver usuario -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">Detalles del Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID:</strong> <span id="userId"></span></p>
                    <p><strong>Nombre:</strong> <span id="userName"></span></p>
                    <p><strong>Correo:</strong> <span id="userEmail"></span></p>
                    <p><strong>Rol:</strong> <span id="userRole"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
    <script>
        var viewUserModal = document.getElementById('viewUserModal');
        viewUserModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; 
            var userId = button.getAttribute('data-id');
            var userName = button.getAttribute('data-nombre');
            var userEmail = button.getAttribute('data-correo');
            var userRole = button.getAttribute('data-rol');

            var modalTitle = viewUserModal.querySelector('.modal-title');
            var modalBodyId = viewUserModal.querySelector('#userId');
            var modalBodyName = viewUserModal.querySelector('#userName');
            var modalBodyEmail = viewUserModal.querySelector('#userEmail');
            var modalBodyRole = viewUserModal.querySelector('#userRole');

            modalTitle.textContent = 'Detalles del Usuario: ' + userName;
            
            // Fetch additional data for the modal
            fetch('get_user_details.php?id=' + userId)
                .then(response => response.json())
                .then(data => {
                    modalBodyId.textContent = data.id;
                    modalBodyName.textContent = data.nombre;
                    modalBodyEmail.textContent = data.correo;
                    modalBodyRole.textContent = data.rol;
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
