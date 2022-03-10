CREATE TABLE `front_model_page` (`title` VARCHAR(512),`description` TEXT,`image` TEXT,`content` TEXT,`keywords` TEXT,`slug` VARCHAR(512) UNIQUE,`parent` INTEGER(11),`auth` INTEGER(11),`id` INTEGER PRIMARY KEY AUTO_INCREMENT,`sorting` INTEGER(11) DEFAULT 0 NOT NULL,`createdat` INTEGER(11) DEFAULT 1646922413 NOT NULL,`updatedat` INTEGER(11) DEFAULT 1646922413 NOT NULL, FOREIGN KEY (auth) REFERENCES front_model_user(id));
