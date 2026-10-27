-- =====================================================
-- JhoasBarber Pro — Script SQL
-- Importar en phpMyAdmin de InfinityFree
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------
-- Tabla: usuarios
-- ---------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20) DEFAULT NULL,
  `rol` ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Tabla: barberos
-- ---------------------------------
CREATE TABLE IF NOT EXISTS `barberos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `especialidad` VARCHAR(150) DEFAULT 'Corte clásico',
  `descripcion` TEXT DEFAULT NULL,
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Tabla: servicios
-- ---------------------------------
CREATE TABLE IF NOT EXISTS `servicios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `precio` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  `duracion_min` INT NOT NULL DEFAULT 30,
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Tabla: citas
-- ---------------------------------
CREATE TABLE IF NOT EXISTS `citas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `barbero_id` INT NOT NULL,
  `servicio_id` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `hora` TIME NOT NULL,
  `estado` ENUM('pendiente','confirmada','completada','cancelada') DEFAULT 'pendiente',
  `notas` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`barbero_id`) REFERENCES `barberos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`servicio_id`) REFERENCES `servicios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Datos de ejemplo
-- ---------------------------------

-- Admin: admin@jhoasbarber.com / admin123
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `telefono`, `rol`) VALUES
('Administrador', 'admin@jhoasbarber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0000', 'admin');

-- Clientes de ejemplo (password: cliente123)
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `telefono`, `rol`) VALUES
('Carlos Martínez', 'carlos@email.com', '$2y$10$TKh8H1.PtIll3Bz3GjHvuOzGFf0GYbGUjvGh.4r.YIjn.lIriRG.', '555-1111', 'cliente'),
('Pedro Rodríguez', 'pedro@email.com',  '$2y$10$TKh8H1.PtIll3Bz3GjHvuOzGFf0GYbGUjvGh.4r.YIjn.lIriRG.', '555-2222', 'cliente');

-- Barberos
INSERT INTO `barberos` (`nombre`, `especialidad`, `descripcion`) VALUES
('Jhoas', 'Cortes modernos y fade', 'Fundador y maestro barbero. Especialista en estilos contemporáneos y diseños creativos.'),
('Miguel Herrera', 'Barba y afeitado clásico', 'Experto en barba y técnicas de afeitado tradicional con navaja.'),
('Luis Torres', 'Cortes clásicos', 'Especialista en cortes clásicos y peinados formales.');

-- Servicios
INSERT INTO `servicios` (`nombre`, `descripcion`, `precio`, `duracion_min`) VALUES
('Corte Clásico',      'Corte tradicional con tijera y máquina',                   15.00, 30),
('Corte + Barba',      'Servicio completo: corte de cabello y arreglo de barba',   25.00, 50),
('Fade / Degradado',   'Degradado moderno con acabado perfecto',                   18.00, 40),
('Afeitado con navaja','Afeitado clásico con navaja y toalla caliente',            12.00, 25),
('Diseño de barba',    'Modelado y diseño personalizado de barba',                 10.00, 20),
('Corte infantil',     'Corte especial para niños menores de 12 años',             10.00, 25);

-- Citas de ejemplo
INSERT INTO `citas` (`usuario_id`, `barbero_id`, `servicio_id`, `fecha`, `hora`, `estado`) VALUES
(2, 1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', 'confirmada'),
(3, 2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:30:00', 'pendiente');

SET FOREIGN_KEY_CHECKS = 1;
