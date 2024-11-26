-- va_unit_feature table
CREATE TABLE PREFIX_unit_feature (
                                   id_unit_feature INT AUTO_INCREMENT NOT NULL,
                                   unit_feature_name VARCHAR(64) NOT NULL,
                                   unit_feature_shortcut VARCHAR(64) NOT NULL,
                                   unit_feature_base_value INT(128) NOT NULL,
                                   PRIMARY KEY(id_unit_feature)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- va_unit_feature_value
CREATE TABLE PREFIX_unit_feature_value (
                                         id_unit_feature_value INT AUTO_INCREMENT NOT NULL,
                                         id_unit_feature INT NOT NULL,
                                         value VARCHAR(64) NOT NULL,
                                         PRIMARY KEY(id_unit_feature_value),
                                         CONSTRAINT FK_PREFIX_unit_feature_value_unit_feature FOREIGN KEY (id_unit_feature)
                                           REFERENCES PREFIX_unit_feature(id_unit_feature) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- va_unit_feature_product table
CREATE TABLE PREFIX_unit_feature_product (
                                           id int(11) NOT NULL AUTO_INCREMENT,
                                           id_unit_feature INT NOT NULL,
                                           id_unit_feature_value INT NOT NULL,
                                           id_product INT NOT NULL,
                                           id_product_attribute INT DEFAULT NULL,
                                           id_attribute_group INT DEFAULT NULL,
                                           PRIMARY KEY (id),
                                           CONSTRAINT FK_PREFIX_unit_feature_product_unit_feature FOREIGN KEY (id_unit_feature)
                                             REFERENCES PREFIX_unit_feature(id_unit_feature) ON DELETE CASCADE,
                                           CONSTRAINT FK_PREFIX_unit_feature_product_unit_feature_value FOREIGN KEY (id_unit_feature_value)
                                             REFERENCES PREFIX_unit_feature_value(id_unit_feature_value) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Wstawianie danych do PREFIX_unit_feature z nową kolumną unit_feature_base_value
INSERT INTO PREFIX_unit_feature (id_unit_feature, unit_feature_name, unit_feature_shortcut, unit_feature_base_value) VALUES
                                                                                                                       (1, 'Waga', 'kg', 1),
                                                                                                                       (2, 'Pojemność', 'l', 1000),
                                                                                                                       (3, 'Ilość', 'szt', 1);

-- Wartości dla Waga (id_unit_feature = 1)
INSERT INTO PREFIX_unit_feature_value (id_unit_feature, value) VALUES
                                                                 (1, '300'), (1, '200'), (1, '100'), (1, '1000'), (1, '500'),
                                                                 (1, '400'), (1, '600'), (1, '700'), (1, '800'), (1, '900');

-- Wartości dla Pojemność (id_unit_feature = 2)
INSERT INTO PREFIX_unit_feature_value (id_unit_feature, value) VALUES
                                                                 (2, '300'), (2, '200'), (2, '100'), (2, '1000'), (2, '500'),
                                                                 (2, '400'), (2, '600'), (2, '700'), (2, '800'), (2, '900');

-- Wartości dla Ilość (id_unit_feature = 3)
INSERT INTO PREFIX_unit_feature_value (id_unit_feature, value) VALUES
                                                                 (3, '1'), (3, '2'), (3, '3'), (3, '4'), (3, '5'),
                                                                 (3, '6'), (3, '7'), (3, '8'), (3, '9'), (3, '10'),
                                                                 (3, '11'), (3, '12'), (3, '13'), (3, '14'), (3, '15'),
                                                                 (3, '16'), (3, '17'), (3, '18'), (3, '19'), (3, '20'),
                                                                 (3, '21'), (3, '22'), (3, '23'), (3, '24');

-- Losowe przypisania cech do produktów (id_product od 1 do 19)
INSERT INTO PREFIX_unit_feature_product (id_unit_feature, id_unit_feature_value, id_product) VALUES
                                                                                                                     (1, 1, 1),
                                                                                                                     (2, 2, 2),
                                                                                                                     (2, 3, 2),
                                                                                                                     (3, 5, 3),
                                                                                                                     (1, 7, 4);
