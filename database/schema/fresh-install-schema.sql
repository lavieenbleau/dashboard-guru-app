CREATE TABLE `admin_activity_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_activity_logs_admin_id_foreign` (`admin_id`),
  CONSTRAINT `admin_activity_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` tinyint NOT NULL,
  `date_in` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `classrooms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `classrooms_serial_id_foreign` (`serial_id`),
  CONSTRAINT `classrooms_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `competences` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `mapel_id` int unsigned NOT NULL,
  `point` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `competences_lesson_id_foreign` (`lesson_id`),
  KEY `competences_mapel_id_foreign` (`mapel_id`),
  CONSTRAINT `competences_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `competences_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mapels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cs_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int unsigned NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cs_files_room_id_foreign` (`room_id`),
  CONSTRAINT `cs_files_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `cs_rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cs_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `room_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_categories_id` int unsigned DEFAULT NULL,
  `admin_id` int unsigned DEFAULT NULL,
  `completion_time` datetime NOT NULL,
  `resolution_by` enum('QnA','ChatBot','Admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL,
  `review` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cs_logs_room_code_unique` (`room_code`),
  KEY `cs_logs_admin_id_foreign` (`admin_id`),
  KEY `cs_logs_question_categories_id_foreign` (`question_categories_id`),
  CONSTRAINT `cs_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cs_logs_question_categories_id_foreign` FOREIGN KEY (`question_categories_id`) REFERENCES `question_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cs_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cs_rooms_id` int unsigned NOT NULL,
  `message_sender` enum('Pelapor','Admin','Sistem','Chatbot') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_content` text COLLATE utf8mb4_unicode_ci,
  `sent_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cs_messages_cs_rooms_id_foreign` (`cs_rooms_id`),
  CONSTRAINT `cs_messages_cs_rooms_id_foreign` FOREIGN KEY (`cs_rooms_id`) REFERENCES `cs_rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cs_rooms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `room_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_categories_id` int unsigned DEFAULT NULL,
  `student_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `admin_id` int unsigned DEFAULT NULL,
  `chat_status` enum('QnA','ChatBot','Admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'QnA',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cs_rooms_room_code_unique` (`room_code`),
  KEY `cs_rooms_question_categories_id_foreign` (`question_categories_id`),
  KEY `cs_rooms_student_id_foreign` (`student_id`),
  KEY `cs_rooms_user_id_foreign` (`user_id`),
  KEY `cs_rooms_admin_id_foreign` (`admin_id`),
  CONSTRAINT `cs_rooms_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cs_rooms_question_categories_id_foreign` FOREIGN KEY (`question_categories_id`) REFERENCES `question_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cs_rooms_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cs_rooms_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `email_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int unsigned NOT NULL,
  `email_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_type` enum('Serial','Peringatan','Kedaluwarsa') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Berhasil','Gagal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` enum('Otomatis','Login_Admin','Manual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email_logs_serial_id_foreign` (`serial_id`),
  CONSTRAINT `email_logs_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exercise_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `competence_id` int unsigned DEFAULT NULL,
  `exercise_id` int unsigned NOT NULL,
  `exercise_type_id` int unsigned NOT NULL,
  `exercise_model_id` int unsigned NOT NULL,
  `exercise_choice` tinyint NOT NULL,
  `exercise_number` int NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `selection` text COLLATE utf8mb4_unicode_ci,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `is_user` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercise_items_admin_id_foreign` (`admin_id`),
  KEY `exercise_items_user_id_foreign` (`user_id`),
  KEY `exercise_items_competence_id_foreign` (`competence_id`),
  KEY `exercise_items_exercise_id_foreign` (`exercise_id`),
  KEY `exercise_items_exercise_type_id_foreign` (`exercise_type_id`),
  KEY `exercise_items_exercise_model_id_foreign` (`exercise_model_id`),
  CONSTRAINT `exercise_items_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `exercise_items_competence_id_foreign` FOREIGN KEY (`competence_id`) REFERENCES `competences` (`id`) ON DELETE SET NULL,
  CONSTRAINT `exercise_items_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exercise_items_exercise_model_id_foreign` FOREIGN KEY (`exercise_model_id`) REFERENCES `exercise_models` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exercise_items_exercise_type_id_foreign` FOREIGN KEY (`exercise_type_id`) REFERENCES `exercise_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exercise_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exercise_models` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exercise_points` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int unsigned NOT NULL,
  `exercise_id` int unsigned NOT NULL,
  `student_id` int unsigned NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `competence_point` text COLLATE utf8mb4_unicode_ci,
  `exercise_point` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercise_points_serial_id_foreign` (`serial_id`),
  KEY `exercise_points_exercise_id_foreign` (`exercise_id`),
  KEY `exercise_points_student_id_exercise_id_index` (`student_id`,`exercise_id`),
  CONSTRAINT `exercise_points_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exercise_points_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exercise_points_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exercise_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exercises` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `helps` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL DEFAULT '3',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lesson_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `theme_id` int unsigned NOT NULL,
  `subtheme_id` int unsigned NOT NULL,
  `admin_id` int unsigned NOT NULL,
  `number` int NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `embed` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lesson_items_lesson_id_foreign` (`lesson_id`),
  KEY `lesson_items_theme_id_foreign` (`theme_id`),
  KEY `lesson_items_subtheme_id_foreign` (`subtheme_id`),
  KEY `lesson_items_admin_id_foreign` (`admin_id`),
  CONSTRAINT `lesson_items_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_items_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_items_subtheme_id_foreign` FOREIGN KEY (`subtheme_id`) REFERENCES `subthemes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_items_theme_id_foreign` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lessons` (
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mapels` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `online_meeting_participants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `online_meeting_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `role` enum('teacher','student') COLLATE utf8mb4_unicode_ci NOT NULL,
  `joined_at` datetime NOT NULL,
  `left_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_meeting_user` (`online_meeting_id`,`user_id`),
  KEY `idx_meeting` (`online_meeting_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_role` (`role`),
  CONSTRAINT `online_meeting_participants_online_meeting_id_foreign` FOREIGN KEY (`online_meeting_id`) REFERENCES `online_meetings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_meeting_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `online_meetings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int unsigned NOT NULL,
  `classroom_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `meeting_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meeting_link` text COLLATE utf8mb4_unicode_ci,
  `platform` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('upcoming','live','ended','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `online_meetings_meeting_code_unique` (`meeting_code`),
  KEY `online_meetings_serial_id_foreign` (`serial_id`),
  KEY `online_meetings_classroom_id_index` (`classroom_id`),
  KEY `online_meetings_user_id_index` (`user_id`),
  KEY `online_meetings_start_time_index` (`start_time`),
  CONSTRAINT `online_meetings_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_meetings_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `online_meetings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `post_child_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_comment_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `student_id` int unsigned DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_user` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_child_comments_post_comment_id_index` (`post_comment_id`),
  KEY `post_child_comments_user_id_index` (`user_id`),
  KEY `post_child_comments_student_id_index` (`student_id`),
  CONSTRAINT `post_child_comments_post_comment_id_foreign` FOREIGN KEY (`post_comment_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_child_comments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
  CONSTRAINT `post_child_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `post_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `student_id` int unsigned DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_user` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_comments_code_unique` (`code`),
  KEY `post_comments_post_id_index` (`post_id`),
  KEY `post_comments_user_id_index` (`user_id`),
  KEY `post_comments_student_id_index` (`student_id`),
  CONSTRAINT `post_comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_comments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
  CONSTRAINT `post_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `mapel_id` int unsigned NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci,
  `attachment` text COLLATE utf8mb4_unicode_ci,
  `embed` text COLLATE utf8mb4_unicode_ci,
  `due_date` timestamp NULL DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_task` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_unique` (`slug`),
  KEY `posts_user_id_foreign` (`user_id`),
  KEY `posts_mapel_id_foreign` (`mapel_id`),
  KEY `posts_serial_id_is_task_index` (`serial_id`,`is_task`),
  CONSTRAINT `posts_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mapels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `posts_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `question_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` enum('Umum','Siswa','Guru') COLLATE utf8mb4_unicode_ci NOT NULL,
  `solution_text` text COLLATE utf8mb4_unicode_ci,
  `guide_file` text COLLATE utf8mb4_unicode_ci,
  `guide_video` text COLLATE utf8mb4_unicode_ci,
  `category_status` enum('Aktif','Tidak Aktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=382 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `quiz_activity_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int unsigned NOT NULL,
  `exercise_id` int unsigned NOT NULL,
  `event_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_seconds` int DEFAULT NULL,
  `suspicious_flag` tinyint(1) NOT NULL DEFAULT '0',
  `device_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_activity_logs_student_id_index` (`student_id`),
  KEY `quiz_activity_logs_exercise_id_index` (`exercise_id`),
  KEY `quiz_activity_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int unsigned NOT NULL,
  `student_id` int unsigned NOT NULL,
  `report` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_serial_id_foreign` (`serial_id`),
  KEY `reports_student_id_foreign` (`student_id`),
  CONSTRAINT `reports_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reports_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `serial_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int unsigned NOT NULL,
  `active` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Baru','Perpanjang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Baru',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `serial_logs_serial_id_foreign` (`serial_id`),
  CONSTRAINT `serial_logs_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `serials` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `product_id` int unsigned NOT NULL,
  `serial` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paket` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `notif` enum('Tidak_ada','Peringatan','Kedaluwarsa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tidak_ada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `serials_serial_unique` (`serial`),
  KEY `serials_user_id_foreign` (`user_id`),
  KEY `serials_product_id_foreign` (`product_id`),
  CONSTRAINT `serials_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `serials_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `share_exercises` (
  `serial_id` int unsigned NOT NULL,
  `exercise_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`serial_id`,`exercise_id`),
  KEY `share_exercises_exercise_id_foreign` (`exercise_id`),
  CONSTRAINT `share_exercises_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  CONSTRAINT `share_exercises_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `students` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `classroom_id` int unsigned NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nis` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `absen_number` int DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_username_unique` (`username`),
  KEY `students_user_id_foreign` (`user_id`),
  KEY `students_serial_id_index` (`serial_id`),
  KEY `students_classroom_id_index` (`classroom_id`),
  CONSTRAINT `students_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `subthemes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `theme_id` int unsigned NOT NULL,
  `subtheme` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subthemes_lesson_id_foreign` (`lesson_id`),
  KEY `subthemes_theme_id_foreign` (`theme_id`),
  CONSTRAINT `subthemes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subthemes_theme_id_foreign` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `serial_id` int unsigned NOT NULL,
  `post_id` int unsigned NOT NULL,
  `student_id` int unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` text COLLATE utf8mb4_unicode_ci,
  `point` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_serial_id_foreign` (`serial_id`),
  KEY `tasks_post_id_foreign` (`post_id`),
  KEY `tasks_student_id_post_id_index` (`student_id`,`post_id`),
  CONSTRAINT `tasks_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `themes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int unsigned NOT NULL,
  `theme` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `themes_lesson_id_foreign` (`lesson_id`),
  CONSTRAINT `themes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `unanswered_questions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `solution_text` text COLLATE utf8mb4_unicode_ci,
  `count` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unanswered_questions_keyword_unique` (`keyword`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` tinyint NOT NULL DEFAULT '0',
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


