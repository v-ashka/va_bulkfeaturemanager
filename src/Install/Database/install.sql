-- va_unit_feature table
CREATE TABLE PREFIX_unit_feature (
                                   id_unit_feature INT AUTO_INCREMENT NOT NULL,
                                   unit_feature_name VARCHAR(64) NOT NULL,
                                   unit_feature_shortcut VARCHAR(64) NOT NULL,
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
                                    id_unit_feature INT NOT NULL,
                                    id_unit_feature_value INT NOT NULL,
                                    id_product INT NOT NULL,
                                    id_product_attribute INT NOT NULL,
                                    id_attribute_group INT DEFAULT NULL,
                                    CONSTRAINT FK_PREFIX_unit_feature_product_unit_feature FOREIGN KEY (id_unit_feature)
                                      REFERENCES PREFIX_unit_feature(id_unit_feature) ON DELETE CASCADE,
                                    CONSTRAINT FK_PREFIX_unit_feature_product_unit_feature_value FOREIGN KEY (id_unit_feature_value)
                                      REFERENCES PREFIX_unit_feature_value(id_unit_feature_value) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


