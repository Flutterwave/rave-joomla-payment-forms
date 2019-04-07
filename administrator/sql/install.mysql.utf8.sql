CREATE TABLE IF NOT EXISTS `#__ravepayments_records` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`tx_ref` VARCHAR(255)  NOT NULL ,
`customer_email` VARCHAR(255)  NOT NULL ,
`amount` VARCHAR(255)  NOT NULL ,
`status` VARCHAR(255)  NOT NULL ,
`module` VARCHAR(255)  NOT NULL ,
`date` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Records','com_ravepayments.records','{"special":{"dbtable":"#__ravepayments_records","key":"id","type":"Records","prefix":"RavepaymentsTable"}}', '{"formFile":"administrator\/components\/com_ravepayments\/models\/forms\/records.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_ravepayments.records')
) LIMIT 1;
