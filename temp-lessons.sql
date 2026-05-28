
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mapel_id` int unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` tinyint NOT NULL,
  `category` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lessons_mapel_id_grade_index` (`mapel_id`,`grade`),
  CONSTRAINT `lessons_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mapels` (`id`) ON DELETE CASCADE

