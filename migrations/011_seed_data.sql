-- Seed data for initial setup

-- Insert brands
INSERT IGNORE INTO brands (name, slug, description, is_active) VALUES
('Nike', 'nike', 'Marca líder en calzado deportivo.', 1),
('Adidas', 'adidas', 'Innovación y estilo desde Alemania.', 1),
('New Balance', 'new-balance', 'Comodidad y calidad estadounidense.', 1),
('Puma', 'puma', 'Deportivo y casual.', 1),
('Reebok', 'reebok', 'Clásico y funcional.', 1),
('Vans', 'vans', 'Estilo urbano y skate.', 1),
('Converse', 'converse', 'Icono del calzado casual.', 1),
('Asics', 'asics', 'Tecnología en running.', 1);

-- Insert categories
INSERT IGNORE INTO categories (name, slug, description, sort_order, is_active) VALUES
('Running', 'running', 'Zapatillas para correr.', 1, 1),
('Casual', 'casual', 'Zapatillas para uso diario.', 2, 1),
('Deportivo', 'deportivo', 'Calzado para entrenamiento.', 3, 1),
('Skate', 'skate', 'Diseñadas para skateboarding.', 4, 1),
('Botas', 'botas', 'Botas y calzado de altura.', 5, 1),
('Sandalias', 'sandalias', 'Calzado abierto y fresco.', 6, 1);

-- Insert admin user (password: admin123)
INSERT IGNORE INTO users (name, email, password, role, is_active) VALUES
('Super Admin', 'admin@catalogozapatillas.com', '$2y$10$ZLBhazxz0YsdQLPZni.YKuMYCUibdawTSI.eqY3sRs96cqQuOvbFC', 'super_admin', 1);
