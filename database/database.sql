CREATE DATABASE IF NOT EXISTS biblioteca_coses_db;
USE biblioteca_coses_db;

-- 1. TAULA DE CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    descripcio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. TAULA D'USUARIS
CREATE TABLE usuaris (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    contra VARCHAR(255) NOT NULL, -- Aquí es guardarà el hash de la contrasenya (per a JWT)
    rol ENUM('user', 'admin') DEFAULT 'user',
    mail VARCHAR(100) NOT NULL UNIQUE,
    barri VARCHAR(50) NOT NULL,    -- Filtre geogràfic de Barcelona (Ex: 'Poblenou', 'Sants')
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. TAULA D'OBJECTES (Tots gratuïts)
CREATE TABLE objectes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    imatge VARCHAR(255),          -- Ruta del fitxer o URL de la imatge (Ex: 'trepant.jpg')
    descripcio TEXT NOT NULL,
    estat ENUM('disponible', 'prestat', 'manteniment') DEFAULT 'disponible',
    categoria_id INT NOT NULL,
    propietari_id INT NOT NULL,   -- L'usuari que cedeix l'objecte gratuïtament
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Relacions (Claus Estrangeres)
    CONSTRAINT fk_objecte_categoria FOREIGN KEY (categoria_id) 
        REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_objecte_propietari FOREIGN KEY (propietari_id) 
        REFERENCES usuaris(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. TAULA DE PRÉSTECS
CREATE TABLE prestecs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    objecte_id INT NOT NULL,
    usuari_id INT NOT NULL,       -- L'usuari que demana l'objecte prestat
    fecha_inici DATE NOT NULL,
    fecha_fi DATE NOT NULL,
    estat_prestec ENUM('actiu', 'retornat', 'retrassat') DEFAULT 'actiu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Relacions (Claus Estrangeres)
    CONSTRAINT fk_prestec_objecte FOREIGN KEY (objecte_id) 
        REFERENCES objectes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_prestec_usuari FOREIGN KEY (usuari_id) 
        REFERENCES usuaris(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;