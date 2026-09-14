CREATE TABLE IF NOT EXISTS `comments` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `entity_type` VARCHAR(32) NOT NULL COMMENT 'тип объекта: post, page',
    `entity_id` INT NOT NULL,
    `user_id` INT DEFAULT NULL,
    `parent_id` INT DEFAULT NULL,
    `body` TEXT NOT NULL,
    `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_comments_entity` (`entity_type`, `entity_id`, `is_approved`),
    KEY `idx_comments_user` (`user_id`),
    CONSTRAINT `fk_comments_user`
        FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_comments_parent`
        FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;