<?php
    $db = new SQLite3('tools.db');

    // 1. Crear taula de categories
    $db->exec("CREATE TABLE IF NOT EXISTS 'categories' (
        'cat_id'          INTEGER,
        'cat_nom'         TEXT NOT NULL UNIQUE,
        'cat_descripcio'  TEXT,
        PRIMARY KEY('cat_id' AUTOINCREMENT)
    );");

    // Inserir Totes les 12 Categories
    $db->exec("INSERT OR IGNORE INTO 'categories' ('cat_id', 'cat_nom', 'cat_descripcio') VALUES 
    (1, 'Bricolatge', 'Eines per a reparacions de la llar i construcció (trepants, serres...)'),
    (2, 'Neteja', 'Electrodomèstics d`ús esporàdic com netejadores de vapor o aspiradors industrials'),
    (3, 'Càmping i Aventura', 'Tendes de campanya, sacs de dormir, motxilles i material de muntanya'),
    (4, 'Cuina i Rebosteria', 'Petits electrodomèstics poc habituals (fondues, panificadores, liquadores)'),
    (5, 'Esdeveniments i Festes', 'Projectors, altaveus, taules plegables i llums per a celebracions'),
    (6, 'Jardineria i Hort urbà', 'Eines per tenir cura de plantes, terrasses i horts comunitaris (tisores de podar, aixades)'),
    (7, 'Mobilitat Sostenible', 'Accessoris per a bicicletes i patinets, infladors, portabebès o portabicicletes'),
    (8, 'Tecnologia i Oficina', 'Escàners de documents, destructores de paper, micròfons o trípodes'),
    (9, 'Jocs i Oci en família', 'Jocs de taula familiars, material per a gimcanes o activitats a l`aire lliure'),
    (10, 'Criança i Nadons', 'Cadires de viatge, bressols plegables, motxilles de porteig o joguines grans temporals'),
    (11, 'Salut i Benestar', 'Crosses, cadires de rodes temporals, humidificadors o mantes elèctriques'),
    (12, 'Artesania i Costura', 'Màquines de cosir, eines de marroquineria, gúbies o cavallets de pintura');");


    // 2. Crear taula d'usuaris
    $db->exec("CREATE TABLE IF NOT EXISTS 'usuaris' (
        'usu_id'      INTEGER,
        'usu_nom'     TEXT NOT NULL,
        'usu_contra'  TEXT NOT NULL,
        'usu_rol'     TEXT NOT NULL DEFAULT 'user',
        'usu_mail'    TEXT NOT NULL UNIQUE,
        'usu_barri'   TEXT NOT NULL,
        PRIMARY KEY('usu_id' AUTOINCREMENT)
    );");

    // Inserir Usuaris de prova
    $db->exec("INSERT OR IGNORE INTO 'usuaris' ('usu_id', 'usu_nom', 'usu_contra', 'usu_rol', 'usu_mail', 'usu_barri') VALUES 
    (1, 'Adria Sbert', '" . md5('1234') . "', 'user', 'adria@gmail.com', 'Sant Andreu'),
    (2, 'Marc Lopez', '" . md5('5678') . "', 'user', 'marc@gmail.com', 'Gràcia'),
    (3, 'admin', '" . md5('admin1234') . "', 'admin', 'admin@gmail.com', 'Eixample');");


    // 3. Crear taula d'objectes
    $db->exec("CREATE TABLE IF NOT EXISTS 'objectes' (
        'obj_id'          INTEGER,
        'obj_nom'         TEXT NOT NULL,
        'obj_imatge'      TEXT,
        'obj_descripcio'  TEXT NOT NULL,
        'obj_estat'       TEXT NOT NULL DEFAULT 'disponible',
        'cat_id'          INTEGER NOT NULL,
        'usu_propietari_id' INTEGER NOT NULL,
        PRIMARY KEY('obj_id' AUTOINCREMENT),
        FOREIGN KEY('cat_id') REFERENCES categories('cat_id'),
        FOREIGN KEY('usu_propietari_id') REFERENCES usuaris('usu_id') ON DELETE CASCADE
    );");

    // Inserir objectes
    $db->exec("INSERT OR IGNORE INTO 'objectes' ('obj_id', 'obj_nom', 'obj_imatge', 'obj_descripcio', 'obj_estat', 'cat_id', 'usu_propietari_id') VALUES 
    (1, 'Trepant percutor Bosch', 'https://images.unsplash.com/photo-1504148455328-c376907d081c?q=80', 'Trepant de 800W amb cable. Inclou estoig amb broques.', 'disponible', 1, 1),
    (2, 'Netejadora de vapor Kärcher', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?q=80', 'Ideal per a netejar sofàs, catifes i matalassos en profunditat.', 'disponible', 2, 2),
    (3, 'Tenda de campanya (4 pers.)', 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?q=80', 'Tenda tipus iglú de fàcil muntatge. Impermeable.', 'prestat', 3, 1),
    (4, 'Panificadora Moulinex', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?q=80', 'Màquina per fer pa a casa. Té 12 programes automàtics diferents.', 'disponible', 4, 2),
    (5, 'Projector HD i Pantalla', 'https://images.unsplash.com/photo-1535016120720-40c646be5580?q=80', 'Projector amb entrada HDMI i pantalla plegable d`1.5 metres per a cine a la fresca.', 'disponible', 5, 2),
    (6, 'Tisores de podar telescòpiques', 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?q=80', 'Tisores per arribar a branques altes. Ideals per a horts urbans i terrasses.', 'disponible', 6, 1),
    (7, 'Inflador de peu amb manòmetre', 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?q=80', 'Inflador d`alta pressió per a rodes de bicicleta, patinets elèctrics o pilotes.', 'disponible', 7, 2),
    (8, 'Destructora de paper d`oficina', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?q=80', 'Talla el paper en tires fines de forma segura. Capacitat de 10 fulls alhora.', 'disponible', 8, 3),
    (9, 'Joc de taula Catan', 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?q=80', 'El famós joc d`estratègia i gestió de recursos. Edició en català.', 'disponible', 9, 1),
    (10, 'Bressol de viatge plegable', 'https://images.unsplash.com/photo-1544816155-12df9643f363?q=80', 'Bressol fàcil de muntar i transportar. Inclou matalàs prim i bossa de transport.', 'disponible', 10, 2),
    (11, 'Set de crosses regulables', 'https://images.unsplash.com/photo-1584515933487-75982139b2a1?q=80', 'Crosses d`alumini lleugeres i regulables en alçada per a adults.', 'disponible', 11, 3),
    (12, 'Màquina de cosir Singer', 'https://images.unsplash.com/photo-1528570188006-2d8262a675af?q=80', 'Màquina de cosir mecànica amb 23 puntades diferents. Perfecta per reparar roba.', 'disponible', 12, 1),
    (13, 'Serra de calar elèctrica', 'https://images.unsplash.com/photo-1534224039826-c7a0dea0e66a?q=80', 'Eina per a talls de precisió en fusta o plàstic. Inclou un joc de fulles.', 'disponible', 1, 3),
    (14, 'Màquina de Fondue de formatge', 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?q=80', 'Set elèctric de fondue amb capacitat per a 6 persones. Inclou les forquilles.', 'disponible', 4, 1),
    (15, 'Altaveu portàtil potent', 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?q=80', 'Altaveu amb connexió Bluetooth i micròfon inclòs per a esdeveniments de barri.', 'disponible', 5, 2);");


    // 4. Crear taula de préstecs
    $db->exec("CREATE TABLE IF NOT EXISTS 'prestecs' (
        'pre_id'           INTEGER,
        'obj_id'           INTEGER NOT NULL,
        'usu_id'           INTEGER NOT NULL,
        'pre_fecha_inici'  TEXT NOT NULL,
        'pre_fecha_fi'     TEXT NOT NULL,
        'pre_estat'        TEXT NOT NULL DEFAULT 'actiu',
        PRIMARY KEY('pre_id' AUTOINCREMENT),
        FOREIGN KEY('obj_id') REFERENCES objectes('obj_id') ON DELETE CASCADE,
        FOREIGN KEY('usu_id') REFERENCES usuaris('usu_id') ON DELETE CASCADE
    );");

    // Inserir un Préstec de prova
    $db->exec("INSERT OR IGNORE INTO 'prestecs' ('obj_id', 'usu_id', 'pre_fecha_inici', 'pre_fecha_fi', 'pre_estat') VALUES 
    (3, 2, '2026-05-20', '2026-05-27', 'actiu');");

    $db->close();
?>