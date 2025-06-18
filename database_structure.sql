-- Base de datos: emanuel_torres_db2
-- Estructura de tablas para el portafolio

-- Tabla de usuarios para autenticación
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de categorías de proyectos
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla principal de proyectos
CREATE TABLE proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    descripcion_corta VARCHAR(300),
    tecnologias JSON,
    url_demo VARCHAR(255),
    url_repositorio VARCHAR(255),
    imagen_principal VARCHAR(255),
    imagenes_adicionales JSON,
    fecha_inicio DATE,
    fecha_fin DATE,
    estado ENUM('en_desarrollo', 'completado', 'pausado', 'cancelado') DEFAULT 'en_desarrollo',
    categoria_id INT,
    usuario_id INT NOT NULL,
    destacado BOOLEAN DEFAULT FALSE,
    visible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_estado (estado),
    INDEX idx_destacado (destacado),
    INDEX idx_visible (visible),
    INDEX idx_categoria (categoria_id)
);

-- Tabla de sesiones para manejo de autenticación
CREATE TABLE sesiones (
    id VARCHAR(128) PRIMARY KEY,
    usuario_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_expires (expires_at)
);

-- Insertar categorías por defecto
INSERT INTO categorias (nombre, descripcion) VALUES
('Web Development', 'Proyectos de desarrollo web frontend y backend'),
('Mobile Apps', 'Aplicaciones móviles para iOS y Android'),
('Desktop Apps', 'Aplicaciones de escritorio'),
('Data Science', 'Proyectos de análisis de datos y machine learning'),
('APIs', 'Desarrollo de APIs y servicios web'),
('Other', 'Otros proyectos diversos');

-- Insertar usuario administrador por defecto (password: admin123)
INSERT INTO usuarios (username, email, password_hash) VALUES
('admin', 'admin@portfolio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertar algunos proyectos de ejemplo
INSERT INTO proyectos (titulo, descripcion, descripcion_corta, tecnologias, url_demo, url_repositorio, fecha_inicio, fecha_fin, estado, categoria_id, usuario_id, destacado) VALUES
('Portfolio Personal', 'Sitio web personal desarrollado con HTML, CSS y JavaScript vanilla. Incluye secciones de about, proyectos, skills y contacto con diseño responsive.', 'Portfolio personal con diseño responsive', '["HTML", "CSS", "JavaScript", "PHP"]', 'https://ejemplo.com/portfolio', 'https://github.com/usuario/portfolio', '2024-01-15', '2024-02-28', 'completado', 1, 1, true),
('E-commerce API', 'API REST completa para un sistema de e-commerce con autenticación JWT, gestión de productos, carrito de compras y procesamiento de pedidos.', 'API REST para e-commerce con JWT', '["PHP", "MySQL", "JWT", "REST API"]', NULL, 'https://github.com/usuario/ecommerce-api', '2024-03-01', NULL, 'en_desarrollo', 5, 1, false),
('Task Manager App', 'Aplicación web para gestión de tareas con funcionalidades CRUD, categorización y sistema de notificaciones.', 'Gestor de tareas con notificaciones', '["React", "Node.js", "MongoDB"]', 'https://taskmanager.ejemplo.com', 'https://github.com/usuario/task-manager', '2023-11-10', '2024-01-20', 'completado', 1, 1, true);