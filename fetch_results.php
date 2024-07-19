<?php
include 'conexion.php';

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

if ($result->num_rows > 0): ?>
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
