ALTER TABLE `sale_orders` COLLATE = utf8_unicode_ci;
ALTER TABLE `sale_orders` CHANGE COLUMN `notes` `notes` VARCHAR(1000) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT '';