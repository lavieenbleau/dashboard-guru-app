
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `serial_id` int unsigned DEFAULT NULL,
  `exercise_type_id` int unsigned NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercises_serial_id_foreign` (`serial_id`),
  KEY `exercises_exercise_type_id_foreign` (`exercise_type_id`),
  KEY `exercises_lesson_id_serial_id_index` (`lesson_id`,`serial_id`),
  CONSTRAINT `exercises_exercise_type_id_foreign` FOREIGN KEY (`exercise_type_id`) REFERENCES `exercise_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exercises_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exercises_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE SET NULL

