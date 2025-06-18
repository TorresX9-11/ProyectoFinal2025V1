<?php
require_once 'config.php';

// Manejar las diferentes solicitudes HTTP
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$id = isset($request[0]) ? intval($request[0]) : null;

switch ($method) {
    case 'GET':
        // GET es público, no requiere autenticación
        if ($id) {
            // Obtener un proyecto específico
            $stmt = $conn->prepare("SELECT * FROM proyectos WHERE id = ?");
            $stmt->execute([$id]);
            $proyecto = $stmt->fetch();
            
            if ($proyecto) {
                responderJSON($proyecto);
            } else {
                responderJSON(['error' => 'Proyecto no encontrado'], 404);
            }
        } else {
            // Listar todos los proyectos
            $stmt = $conn->query("SELECT * FROM proyectos ORDER BY fecha_creacion DESC");
            responderJSON($stmt->fetchAll());
        }
        break;

    case 'POST':
        // Crear nuevo proyecto (requiere autenticación)
        verificarAutenticacion();
        $data = obtenerDatosJSON();
        
        if (!isset($data['titulo']) || !isset($data['descripcion'])) {
            responderJSON(['error' => 'Datos incompletos'], 400);
        }
        
        $stmt = $conn->prepare("
            INSERT INTO proyectos (titulo, descripcion, descripcion_corta, imagen_principal, categoria_id, estado)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        try {
            $stmt->execute([
                $data['titulo'],
                $data['descripcion'],
                $data['descripcion_corta'] ?? null,
                $data['imagen_principal'] ?? null,
                $data['categoria_id'] ?? null,
                $data['estado'] ?? 'activo'
            ]);
            
            responderJSON([
                'message' => 'Proyecto creado',
                'id' => $conn->lastInsertId()
            ], 201);
        } catch (PDOException $e) {
            responderJSON(['error' => 'Error al crear el proyecto'], 500);
        }
        break;

    case 'PUT':
        // Actualizar proyecto existente (requiere autenticación)
        verificarAutenticacion();
        if (!$id) {
            responderJSON(['error' => 'ID no proporcionado'], 400);
        }
        
        $data = obtenerDatosJSON();
        $campos = [];
        $valores = [];
        
        // Construir la consulta dinámicamente basada en los campos proporcionados
        if (isset($data['titulo'])) {
            $campos[] = 'titulo = ?';
            $valores[] = $data['titulo'];
        }
        if (isset($data['descripcion'])) {
            $campos[] = 'descripcion = ?';
            $valores[] = $data['descripcion'];
        }
        if (isset($data['descripcion_corta'])) {
            $campos[] = 'descripcion_corta = ?';
            $valores[] = $data['descripcion_corta'];
        }
        if (isset($data['imagen_principal'])) {
            $campos[] = 'imagen_principal = ?';
            $valores[] = $data['imagen_principal'];
        }
        if (isset($data['categoria_id'])) {
            $campos[] = 'categoria_id = ?';
            $valores[] = $data['categoria_id'];
        }
        if (isset($data['estado'])) {
            $campos[] = 'estado = ?';
            $valores[] = $data['estado'];
        }
        
        if (empty($campos)) {
            responderJSON(['error' => 'No hay datos para actualizar'], 400);
        }
        
        $valores[] = $id; // Añadir el ID para la cláusula WHERE
        $sql = "UPDATE proyectos SET " . implode(', ', $campos) . " WHERE id = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($valores);
            
            if ($stmt->rowCount() > 0) {
                responderJSON(['message' => 'Proyecto actualizado']);
            } else {
                responderJSON(['error' => 'Proyecto no encontrado'], 404);
            }
        } catch (PDOException $e) {
            responderJSON(['error' => 'Error al actualizar el proyecto'], 500);
        }
        break;

    case 'DELETE':
        // Eliminar proyecto (requiere autenticación)
        verificarAutenticacion();
        if (!$id) {
            responderJSON(['error' => 'ID no proporcionado'], 400);
        }
        
        try {
            $stmt = $conn->prepare("DELETE FROM proyectos WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                responderJSON(['message' => 'Proyecto eliminado']);
            } else {
                responderJSON(['error' => 'Proyecto no encontrado'], 404);
            }
        } catch (PDOException $e) {
            responderJSON(['error' => 'Error al eliminar el proyecto'], 500);
        }
        break;

    default:
        responderJSON(['error' => 'Método no permitido'], 405);
        break;
}
