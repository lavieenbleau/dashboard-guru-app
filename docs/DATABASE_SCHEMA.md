# Database Physical Design Document

## Overview

This document provides a comprehensive overview of the database schema for the DashboardGuru application, including all tables, columns, data types, relationships, and indexes.

---

## Table of Contents

1. [Core Tables](#core-tables)
2. [Product & Serial Management](#product--serial-management)
3. [Classroom & Student Management](#classroom--student-management)
4. [Curriculum Management](#curriculum-management)
5. [Exercise & Assessment](#exercise--assessment)
6. [Content Management](#content-management)
7. [Pivot/Junction Tables](#pivotjunction-tables)
8. [System Tables](#system-tables)

---

## Core Tables

### 1. users

**Description:** Stores user information (teachers/administrators)

| Column            | Type            | Constraints                 | Description                  |
| ----------------- | --------------- | --------------------------- | ---------------------------- |
| id                | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | User ID                      |
| name              | VARCHAR(100)    | NOT NULL                    | Full name                    |
| username          | VARCHAR(100)    | NOT NULL                    | Login username               |
| password          | VARCHAR(100)    | NOT NULL                    | Hashed password              |
| password_text     | VARCHAR(100)    | NULLABLE                    | Plain text password (backup) |
| email             | VARCHAR(100)    | NULLABLE                    | Email address                |
| email_verified_at | TIMESTAMP       | NULLABLE                    | Email verification timestamp |
| role              | TINYINT         | NOT NULL                    | User role (admin/teacher)    |
| address           | TEXT            | NULLABLE                    | Physical address             |
| phone             | VARCHAR(20)     | NULLABLE                    | Phone number                 |
| img               | VARCHAR(100)    | NULLABLE                    | Profile image filename       |
| login_at          | TIMESTAMP       | NULLABLE                    | Last login timestamp         |
| remember_token    | VARCHAR(100)    | NULLABLE                    | Remember me token            |
| created_at        | TIMESTAMP       | NOT NULL                    | Creation timestamp           |
| updated_at        | TIMESTAMP       | NOT NULL                    | Last update timestamp        |

**Indexes:**

- PRIMARY KEY: id
- No explicit foreign keys defined

**Relationships:**

- Has many: serials, students, posts, exercises, online_meetings
- Polymorphic: Can be associated with various content types

---

### 2. password_reset_tokens

**Description:** Stores password reset tokens

| Column     | Type         | Constraints | Description         |
| ---------- | ------------ | ----------- | ------------------- |
| email      | VARCHAR(255) | PRIMARY KEY | User email          |
| token      | VARCHAR(255) | NOT NULL    | Reset token         |
| created_at | TIMESTAMP    | NULLABLE    | Token creation time |

**Indexes:**

- PRIMARY KEY: email

---

### 3. sessions

**Description:** Stores user session data

| Column        | Type            | Constraints     | Description             |
| ------------- | --------------- | --------------- | ----------------------- |
| id            | VARCHAR(255)    | PRIMARY KEY     | Session ID              |
| user_id       | BIGINT UNSIGNED | NULLABLE, INDEX | Foreign key to users    |
| ip_address    | VARCHAR(45)     | NULLABLE        | Client IP address       |
| user_agent    | TEXT            | NULLABLE        | Browser user agent      |
| payload       | LONGTEXT        | NOT NULL        | Session data            |
| last_activity | INTEGER         | NOT NULL, INDEX | Last activity timestamp |

**Indexes:**

- PRIMARY KEY: id
- INDEX: user_id
- INDEX: last_activity

---

## Product & Serial Management

### 4. products

**Description:** Educational products/packages available in the system

| Column         | Type            | Constraints                 | Description                  |
| -------------- | --------------- | --------------------------- | ---------------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Product ID                   |
| lesson_id      | VARCHAR(100)    | NULLABLE                    | Associated lesson identifier |
| name           | VARCHAR(50)     | NOT NULL                    | Product name                 |
| grade          | VARCHAR(50)     | NULLABLE                    | Grade level                  |
| grade_category | VARCHAR(100)    | NOT NULL                    | Grade category               |
| semester       | VARCHAR(50)     | NULLABLE                    | Semester                     |
| created_at     | TIMESTAMP       | NOT NULL                    | Creation timestamp           |
| updated_at     | TIMESTAMP       | NOT NULL                    | Last update timestamp        |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Has many: serials

---

### 5. serials

**Description:** Serial numbers/licenses for products

| Column     | Type            | Constraints                 | Description             |
| ---------- | --------------- | --------------------------- | ----------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Serial ID               |
| user_id    | BIGINT UNSIGNED | NULLABLE                    | Foreign key to users    |
| product_id | BIGINT UNSIGNED | NOT NULL                    | Foreign key to products |
| serial     | VARCHAR(50)     | NOT NULL                    | Serial number/code      |
| paket      | VARCHAR(1)      | NOT NULL                    | Package type            |
| active     | VARCHAR(3)      | NOT NULL                    | Activation status       |
| expired_at | TIMESTAMP       | NULLABLE                    | Expiration date         |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp      |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp   |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: users, products
- Has many: classrooms, students, posts, tasks, reports, exercise_points, online_meetings

---

## Classroom & Student Management

### 6. classrooms

**Description:** Virtual classrooms in the system

| Column     | Type            | Constraints                 | Description            |
| ---------- | --------------- | --------------------------- | ---------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Classroom ID           |
| serial_id  | BIGINT UNSIGNED | NOT NULL                    | Foreign key to serials |
| name       | VARCHAR(100)    | NOT NULL                    | Classroom name         |
| grade      | VARCHAR(10)     | NOT NULL                    | Grade level            |
| code       | VARCHAR(24)     | NOT NULL                    | Unique classroom code  |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp     |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp  |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: serials
- Has many: students, online_meetings
- Many to many: lessons (via lesson_classroom), exercises (via exercise_classroom)

---

### 7. students

**Description:** Student accounts in the system

| Column        | Type            | Constraints                 | Description                    |
| ------------- | --------------- | --------------------------- | ------------------------------ |
| id            | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Student ID                     |
| serial_id     | BIGINT UNSIGNED | NOT NULL                    | Foreign key to serials         |
| user_id       | BIGINT UNSIGNED | NOT NULL                    | Foreign key to users (teacher) |
| classroom_id  | BIGINT UNSIGNED | NOT NULL                    | Foreign key to classrooms      |
| name          | VARCHAR(200)    | NOT NULL                    | Student name                   |
| username      | VARCHAR(100)    | NOT NULL                    | Login username                 |
| password      | VARCHAR(150)    | NOT NULL                    | Hashed password                |
| password_text | VARCHAR(100)    | NOT NULL                    | Plain text password            |
| nis           | VARCHAR(20)     | NULLABLE                    | Student ID number              |
| email         | VARCHAR(100)    | NULLABLE                    | Email address                  |
| phone         | VARCHAR(20)     | NULLABLE                    | Phone number                   |
| created_at    | TIMESTAMP       | NOT NULL                    | Creation timestamp             |
| updated_at    | TIMESTAMP       | NOT NULL                    | Last update timestamp          |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: serials, users, classrooms
- Has many: tasks, reports, exercise_points, post_comments, post_child_comments

---

## Curriculum Management

### 8. mapels

**Description:** Subject/course master data (Mata Pelajaran)

| Column     | Type            | Constraints                 | Description           |
| ---------- | --------------- | --------------------------- | --------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Subject ID            |
| name       | VARCHAR(255)    | NOT NULL                    | Subject name          |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp    |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Has many: lessons, competences, posts, online_meetings

---

### 9. lessons

**Description:** Lessons/chapters in each subject

| Column            | Type            | Constraints                 | Description                      |
| ----------------- | --------------- | --------------------------- | -------------------------------- |
| id                | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Lesson ID                        |
| mapel_id          | BIGINT UNSIGNED | NOT NULL                    | Foreign key to mapels            |
| name              | VARCHAR(50)     | NOT NULL                    | Lesson name                      |
| grade             | VARCHAR(10)     | NOT NULL                    | Grade level                      |
| semester          | INTEGER         | NOT NULL                    | Semester number                  |
| category          | INTEGER         | NOT NULL, DEFAULT 1         | Category type                    |
| description       | TEXT            | NULLABLE                    | Lesson description               |
| questions         | TEXT            | NULLABLE                    | Related questions                |
| file              | VARCHAR(255)    | NULLABLE                    | Attached file                    |
| deadline          | DATETIME        | NULLABLE                    | Assignment deadline              |
| shared_to_classes | JSON            | NULLABLE                    | Classes this lesson is shared to |
| created_at        | TIMESTAMP       | NOT NULL                    | Creation timestamp               |
| updated_at        | TIMESTAMP       | NOT NULL                    | Last update timestamp            |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: mapels
- Has many: themes, subthemes, competences, exercises, lesson_items
- Many to many: classrooms (via lesson_classroom)

---

### 10. themes

**Description:** Themes/topics within lessons

| Column     | Type            | Constraints                 | Description            |
| ---------- | --------------- | --------------------------- | ---------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Theme ID               |
| lesson_id  | BIGINT UNSIGNED | NULLABLE                    | Foreign key to lessons |
| theme      | INTEGER         | NOT NULL                    | Theme number           |
| name       | VARCHAR(255)    | NOT NULL                    | Theme name             |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp     |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp  |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: lessons
- Has many: subthemes, lesson_items

---

### 11. subthemes

**Description:** Subtopics within themes

| Column     | Type            | Constraints                 | Description            |
| ---------- | --------------- | --------------------------- | ---------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Subtheme ID            |
| lesson_id  | BIGINT UNSIGNED | NULLABLE                    | Foreign key to lessons |
| theme_id   | BIGINT UNSIGNED | NOT NULL                    | Foreign key to themes  |
| subtheme   | INTEGER         | NOT NULL                    | Subtheme number        |
| name       | VARCHAR(255)    | NOT NULL                    | Subtheme name          |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp     |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp  |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: lessons, themes
- Has many: lesson_items

---

### 12. lesson_items

**Description:** Individual learning materials/content items

| Column            | Type            | Constraints                 | Description              |
| ----------------- | --------------- | --------------------------- | ------------------------ |
| id                | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Item ID                  |
| lesson_id         | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL       | Foreign key to lessons   |
| theme_id          | BIGINT UNSIGNED | FOREIGN KEY, NULLABLE       | Foreign key to themes    |
| subtheme_id       | BIGINT UNSIGNED | FOREIGN KEY, NULLABLE       | Foreign key to subthemes |
| number            | INTEGER         | NOT NULL, DEFAULT 1         | Order number             |
| title             | VARCHAR(255)    | NOT NULL                    | Item title               |
| description       | TEXT            | NULLABLE                    | Content description      |
| link              | VARCHAR(255)    | NULLABLE                    | External link            |
| embed             | VARCHAR(255)    | NULLABLE                    | Video embed code         |
| attachment        | VARCHAR(255)    | NULLABLE                    | File attachment          |
| is_admin          | BOOLEAN         | NOT NULL, DEFAULT false     | Created by admin         |
| shared_to_classes | JSON            | NULLABLE                    | Shared classrooms        |
| created_at        | TIMESTAMP       | NOT NULL                    | Creation timestamp       |
| updated_at        | TIMESTAMP       | NOT NULL                    | Last update timestamp    |

**Indexes:**

- PRIMARY KEY: id
- FOREIGN KEY: lesson_id → lessons(id) ON DELETE CASCADE
- FOREIGN KEY: theme_id → themes(id) ON DELETE SET NULL
- FOREIGN KEY: subtheme_id → subthemes(id) ON DELETE SET NULL

**Relationships:**

- Belongs to: lessons, themes, subthemes

---

### 13. competences

**Description:** Learning competencies/objectives

| Column      | Type            | Constraints                 | Description            |
| ----------- | --------------- | --------------------------- | ---------------------- |
| id          | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Competence ID          |
| lesson_id   | BIGINT UNSIGNED | NOT NULL                    | Foreign key to lessons |
| mapel_id    | BIGINT UNSIGNED | NOT NULL                    | Foreign key to mapels  |
| point       | VARCHAR(10)     | NOT NULL                    | Competence code        |
| description | TEXT            | NULLABLE                    | Competence description |
| created_at  | TIMESTAMP       | NOT NULL                    | Creation timestamp     |
| updated_at  | TIMESTAMP       | NOT NULL                    | Last update timestamp  |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: lessons, mapels
- Has many: exercise_items

---

## Exercise & Assessment

### 14. exercise_types

**Description:** Types of exercises (quiz, assignment, etc.)

| Column     | Type            | Constraints                 | Description           |
| ---------- | --------------- | --------------------------- | --------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Type ID               |
| kode       | VARCHAR(10)     | NOT NULL                    | Type code             |
| name       | VARCHAR(50)     | NOT NULL                    | Type name             |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp    |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Has many: exercises, exercise_items

---

### 15. exercise_models

**Description:** Exercise format models (multiple choice, essay, etc.)

| Column     | Type            | Constraints                 | Description           |
| ---------- | --------------- | --------------------------- | --------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Model ID              |
| name       | VARCHAR(20)     | NOT NULL                    | Model name            |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp    |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Has many: exercise_items

---

### 16. exercises

**Description:** Exercise/assessment containers

| Column            | Type            | Constraints                 | Description                   |
| ----------------- | --------------- | --------------------------- | ----------------------------- |
| id                | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Exercise ID                   |
| lesson_id         | BIGINT UNSIGNED | NOT NULL                    | Foreign key to lessons        |
| serial_id         | BIGINT UNSIGNED | NULLABLE                    | Foreign key to serials        |
| exercise_type_id  | BIGINT UNSIGNED | NOT NULL                    | Foreign key to exercise_types |
| title             | VARCHAR(200)    | NULLABLE                    | Exercise title                |
| description       | TEXT            | NULLABLE                    | Exercise description          |
| is_admin          | TINYINT         | NOT NULL, DEFAULT 1         | Created by admin              |
| shared_to_classes | JSON            | NULLABLE                    | Shared classrooms             |
| created_at        | TIMESTAMP       | NOT NULL                    | Creation timestamp            |
| updated_at        | TIMESTAMP       | NOT NULL                    | Last update timestamp         |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: lessons, serials, exercise_types
- Has many: exercise_items, exercise_points
- Many to many: classrooms (via exercise_classroom)

---

### 17. exercise_items

**Description:** Individual exercise questions

| Column            | Type            | Constraints                 | Description                    |
| ----------------- | --------------- | --------------------------- | ------------------------------ |
| id                | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Item ID                        |
| admin_id          | BIGINT UNSIGNED | NULLABLE                    | Admin creator ID               |
| user_id           | BIGINT UNSIGNED | NULLABLE                    | User creator ID                |
| competence_id     | BIGINT UNSIGNED | NULLABLE                    | Foreign key to competences     |
| exercise_id       | BIGINT UNSIGNED | NOT NULL                    | Foreign key to exercises       |
| exercise_type_id  | BIGINT UNSIGNED | NOT NULL                    | Foreign key to exercise_types  |
| exercise_model_id | BIGINT UNSIGNED | NOT NULL                    | Foreign key to exercise_models |
| exercise_choice   | TINYINT         | NOT NULL                    | Choice count                   |
| exercise_number   | INTEGER         | NOT NULL                    | Question number                |
| question          | TEXT            | NOT NULL                    | Question text                  |
| selection         | TEXT            | NULLABLE                    | Answer choices (JSON)          |
| answer            | TEXT            | NULLABLE                    | Correct answer                 |
| is_user           | TINYINT         | NOT NULL                    | User-created flag              |
| created_at        | TIMESTAMP       | NOT NULL                    | Creation timestamp             |
| updated_at        | TIMESTAMP       | NOT NULL                    | Last update timestamp          |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: competences, exercises, exercise_types, exercise_models

---

### 18. exercise_points

**Description:** Student exercise scores/submissions

| Column           | Type            | Constraints                 | Description              |
| ---------------- | --------------- | --------------------------- | ------------------------ |
| id               | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Point ID                 |
| serial_id        | BIGINT UNSIGNED | NOT NULL                    | Foreign key to serials   |
| exercise_id      | BIGINT UNSIGNED | NOT NULL                    | Foreign key to exercises |
| student_id       | BIGINT UNSIGNED | NOT NULL                    | Foreign key to students  |
| answer           | TEXT            | NOT NULL                    | Student answer           |
| competence_point | TEXT            | NULLABLE                    | Competence-based points  |
| exercise_point   | VARCHAR(3)      | NULLABLE                    | Total exercise points    |
| created_at       | TIMESTAMP       | NOT NULL                    | Creation timestamp       |
| updated_at       | TIMESTAMP       | NOT NULL                    | Last update timestamp    |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: serials, exercises, students

---

## Content Management

### 19. posts

**Description:** Content posts (materials, tasks, quizzes)

| Column            | Type            | Constraints                 | Description                             |
| ----------------- | --------------- | --------------------------- | --------------------------------------- |
| id                | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Post ID                                 |
| serial_id         | BIGINT UNSIGNED | NOT NULL                    | Foreign key to serials                  |
| user_id           | BIGINT UNSIGNED | NOT NULL                    | Foreign key to users                    |
| mapel_id          | BIGINT UNSIGNED | NOT NULL                    | Foreign key to mapels                   |
| title             | VARCHAR(150)    | NOT NULL                    | Post title                              |
| description       | TEXT            | NULLABLE                    | Post content                            |
| slug              | VARCHAR(200)    | NOT NULL                    | URL slug                                |
| link              | TEXT            | NULLABLE                    | External link                           |
| attachment        | TEXT            | NULLABLE                    | File attachment                         |
| embed             | TEXT            | NULLABLE                    | Video embed                             |
| category          | TEXT            | NULLABLE                    | Category                                |
| shared_to_classes | JSON            | NULLABLE                    | Shared classrooms                       |
| deadline          | DATETIME        | NULLABLE                    | Task deadline                           |
| is_task           | TINYINT         | NOT NULL, DEFAULT 0         | Is task flag                            |
| quiz_data         | TEXT            | NULLABLE                    | Quiz questions (JSON)                   |
| time_limit        | INTEGER         | NULLABLE                    | Quiz time limit (minutes)               |
| is_quiz           | TINYINT         | NOT NULL, DEFAULT 0         | Content type (0=materi, 1=task, 2=quiz) |
| created_at        | TIMESTAMP       | NOT NULL                    | Creation timestamp                      |
| updated_at        | TIMESTAMP       | NOT NULL                    | Last update timestamp                   |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: serials, users, mapels
- Has many: tasks, post_comments

---

### 20. tasks

**Description:** Student task submissions

| Column      | Type            | Constraints                 | Description             |
| ----------- | --------------- | --------------------------- | ----------------------- |
| id          | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Task ID                 |
| serial_id   | BIGINT UNSIGNED | NOT NULL                    | Foreign key to serials  |
| post_id     | BIGINT UNSIGNED | NOT NULL                    | Foreign key to posts    |
| student_id  | BIGINT UNSIGNED | NOT NULL                    | Foreign key to students |
| description | TEXT            | NOT NULL                    | Submission description  |
| attachment  | TEXT            | NULLABLE                    | Submitted file          |
| point       | VARCHAR(3)      | NULLABLE                    | Score/grade             |
| created_at  | TIMESTAMP       | NOT NULL                    | Creation timestamp      |
| updated_at  | TIMESTAMP       | NOT NULL                    | Last update timestamp   |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: serials, posts, students

---

### 21. post_comments

**Description:** Comments on posts

| Column     | Type            | Constraints                 | Description             |
| ---------- | --------------- | --------------------------- | ----------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Comment ID              |
| post_id    | BIGINT UNSIGNED | NOT NULL                    | Foreign key to posts    |
| user_id    | BIGINT UNSIGNED | NULLABLE                    | Foreign key to users    |
| student_id | BIGINT UNSIGNED | NULLABLE                    | Foreign key to students |
| message    | TEXT            | NOT NULL                    | Comment message         |
| code       | VARCHAR(50)     | NOT NULL                    | Unique code             |
| is_user    | TINYINT         | NOT NULL, DEFAULT 0         | User/student flag       |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp      |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp   |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: posts, users, students
- Has many: post_child_comments

---

### 22. post_child_comments

**Description:** Replies to comments

| Column          | Type            | Constraints                 | Description                  |
| --------------- | --------------- | --------------------------- | ---------------------------- |
| id              | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Child comment ID             |
| post_comment_id | BIGINT UNSIGNED | NOT NULL                    | Foreign key to post_comments |
| user_id         | BIGINT UNSIGNED | NULLABLE                    | Foreign key to users         |
| student_id      | BIGINT UNSIGNED | NULLABLE                    | Foreign key to students      |
| message         | TEXT            | NOT NULL                    | Reply message                |
| is_user         | TINYINT         | NOT NULL                    | User/student flag            |
| created_at      | TIMESTAMP       | NOT NULL                    | Creation timestamp           |
| updated_at      | TIMESTAMP       | NOT NULL                    | Last update timestamp        |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: post_comments, users, students

---

### 23. reports

**Description:** Student reports or feedback

| Column     | Type            | Constraints                 | Description             |
| ---------- | --------------- | --------------------------- | ----------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Report ID               |
| serial_id  | BIGINT UNSIGNED | NOT NULL                    | Foreign key to serials  |
| student_id | BIGINT UNSIGNED | NOT NULL                    | Foreign key to students |
| report     | TEXT            | NOT NULL                    | Report content          |
| img        | VARCHAR(50)     | NULLABLE                    | Image attachment        |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp      |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp   |

**Indexes:**

- PRIMARY KEY: id

**Relationships:**

- Belongs to: serials, students

---

### 24. helps

**Description:** Help/support table (structure only)

| Column     | Type            | Constraints                 | Description           |
| ---------- | --------------- | --------------------------- | --------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Help ID               |
| created_at | TIMESTAMP       | NOT NULL                    | Creation timestamp    |
| updated_at | TIMESTAMP       | NOT NULL                    | Last update timestamp |

**Indexes:**

- PRIMARY KEY: id

**Note:** This table has minimal structure. Additional columns may be needed based on requirements.

---

### 25. online_meetings

**Description:** Virtual classroom meetings (Jitsi, Zoom, etc.)

| Column           | Type            | Constraints                   | Description                                   |
| ---------------- | --------------- | ----------------------------- | --------------------------------------------- |
| id               | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT   | Meeting ID                                    |
| serial_id        | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL         | Foreign key to serials                        |
| classroom_id     | BIGINT UNSIGNED | FOREIGN KEY, NULLABLE         | Foreign key to classrooms                     |
| user_id          | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL         | Foreign key to users (creator)                |
| mapel_id         | BIGINT UNSIGNED | NULLABLE                      | Foreign key to mapels                         |
| title            | VARCHAR(255)    | NOT NULL                      | Meeting title                                 |
| description      | TEXT            | NULLABLE                      | Meeting description                           |
| meeting_code     | VARCHAR(255)    | NULLABLE, UNIQUE              | Join code                                     |
| meeting_link     | VARCHAR(255)    | NULLABLE                      | External meeting link                         |
| platform         | VARCHAR(255)    | NOT NULL, DEFAULT 'jitsi'     | Platform (jitsi, zoom, gmeet)                 |
| start_time       | DATETIME        | NOT NULL                      | Start time                                    |
| end_time         | DATETIME        | NOT NULL                      | End time                                      |
| status           | ENUM            | NOT NULL, DEFAULT 'scheduled' | Status (scheduled, ongoing, ended, cancelled) |
| room_id          | VARCHAR(255)    | NULLABLE                      | Room identifier                               |
| is_internal      | BOOLEAN         | NOT NULL, DEFAULT true        | Internal/external flag                        |
| max_participants | INTEGER         | NULLABLE                      | Max participant count                         |
| participants     | JSON            | NULLABLE                      | Participant list                              |
| created_at       | TIMESTAMP       | NOT NULL                      | Creation timestamp                            |
| updated_at       | TIMESTAMP       | NOT NULL                      | Last update timestamp                         |

**Indexes:**

- PRIMARY KEY: id
- UNIQUE: meeting_code
- FOREIGN KEY: serial_id → serials(id) ON DELETE CASCADE
- FOREIGN KEY: classroom_id → classrooms(id) ON DELETE CASCADE
- FOREIGN KEY: user_id → users(id) ON DELETE CASCADE

**Relationships:**

- Belongs to: serials, classrooms, users, mapels

---

## Pivot/Junction Tables

### 26. lesson_classroom

**Description:** Many-to-many relationship between lessons and classrooms

| Column       | Type            | Constraints                 | Description               |
| ------------ | --------------- | --------------------------- | ------------------------- |
| id           | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Pivot ID                  |
| lesson_id    | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL       | Foreign key to lessons    |
| classroom_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL       | Foreign key to classrooms |
| created_at   | TIMESTAMP       | NOT NULL                    | Creation timestamp        |
| updated_at   | TIMESTAMP       | NOT NULL                    | Last update timestamp     |

**Indexes:**

- PRIMARY KEY: id
- FOREIGN KEY: lesson_id → lessons(id) ON DELETE CASCADE
- FOREIGN KEY: classroom_id → classrooms(id) ON DELETE CASCADE

**Relationships:**

- Belongs to: lessons, classrooms

---

### 27. exercise_classroom

**Description:** Many-to-many relationship between exercises and classrooms

| Column       | Type            | Constraints                 | Description               |
| ------------ | --------------- | --------------------------- | ------------------------- |
| id           | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Pivot ID                  |
| exercise_id  | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL       | Foreign key to exercises  |
| classroom_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL       | Foreign key to classrooms |
| created_at   | TIMESTAMP       | NOT NULL                    | Creation timestamp        |
| updated_at   | TIMESTAMP       | NOT NULL                    | Last update timestamp     |

**Indexes:**

- PRIMARY KEY: id
- FOREIGN KEY: exercise_id → exercises(id) ON DELETE CASCADE
- FOREIGN KEY: classroom_id → classrooms(id) ON DELETE CASCADE

**Relationships:**

- Belongs to: exercises, classrooms

---

## System Tables

### 28. cache

**Description:** Application cache storage

| Column     | Type         | Constraints | Description          |
| ---------- | ------------ | ----------- | -------------------- |
| key        | VARCHAR(255) | PRIMARY KEY | Cache key            |
| value      | MEDIUMTEXT   | NOT NULL    | Cached value         |
| expiration | INTEGER      | NOT NULL    | Expiration timestamp |

**Indexes:**

- PRIMARY KEY: key

---

### 29. cache_locks

**Description:** Cache lock mechanism

| Column     | Type         | Constraints | Description          |
| ---------- | ------------ | ----------- | -------------------- |
| key        | VARCHAR(255) | PRIMARY KEY | Lock key             |
| owner      | VARCHAR(255) | NOT NULL    | Lock owner           |
| expiration | INTEGER      | NOT NULL    | Expiration timestamp |

**Indexes:**

- PRIMARY KEY: key

---

### 30. jobs

**Description:** Queue jobs

| Column       | Type             | Constraints                 | Description         |
| ------------ | ---------------- | --------------------------- | ------------------- |
| id           | BIGINT UNSIGNED  | PRIMARY KEY, AUTO_INCREMENT | Job ID              |
| queue        | VARCHAR(255)     | NOT NULL, INDEX             | Queue name          |
| payload      | LONGTEXT         | NOT NULL                    | Job payload         |
| attempts     | TINYINT UNSIGNED | NOT NULL                    | Attempt count       |
| reserved_at  | INTEGER UNSIGNED | NULLABLE                    | Reserved timestamp  |
| available_at | INTEGER UNSIGNED | NOT NULL                    | Available timestamp |
| created_at   | INTEGER UNSIGNED | NOT NULL                    | Creation timestamp  |

**Indexes:**

- PRIMARY KEY: id
- INDEX: queue

---

### 31. job_batches

**Description:** Batch job tracking

| Column         | Type         | Constraints | Description            |
| -------------- | ------------ | ----------- | ---------------------- |
| id             | VARCHAR(255) | PRIMARY KEY | Batch ID               |
| name           | VARCHAR(255) | NOT NULL    | Batch name             |
| total_jobs     | INTEGER      | NOT NULL    | Total job count        |
| pending_jobs   | INTEGER      | NOT NULL    | Pending job count      |
| failed_jobs    | INTEGER      | NOT NULL    | Failed job count       |
| failed_job_ids | LONGTEXT     | NOT NULL    | Failed job IDs         |
| options        | MEDIUMTEXT   | NULLABLE    | Batch options          |
| cancelled_at   | INTEGER      | NULLABLE    | Cancellation timestamp |
| created_at     | INTEGER      | NOT NULL    | Creation timestamp     |
| finished_at    | INTEGER      | NULLABLE    | Completion timestamp   |

**Indexes:**

- PRIMARY KEY: id

---

### 32. failed_jobs

**Description:** Failed queue jobs

| Column     | Type            | Constraints                         | Description       |
| ---------- | --------------- | ----------------------------------- | ----------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT         | Failed job ID     |
| uuid       | VARCHAR(255)    | NOT NULL, UNIQUE                    | Unique identifier |
| connection | TEXT            | NOT NULL                            | Connection name   |
| queue      | TEXT            | NOT NULL                            | Queue name        |
| payload    | LONGTEXT        | NOT NULL                            | Job payload       |
| exception  | LONGTEXT        | NOT NULL                            | Exception details |
| failed_at  | TIMESTAMP       | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Failure timestamp |

**Indexes:**

- PRIMARY KEY: id
- UNIQUE: uuid

---

## Entity Relationship Summary

### Core Relationships

**Users (Teachers/Admins)**

- → serials (1:many)
- → students (1:many) - as teacher
- → posts (1:many)
- → exercises (1:many)
- → online_meetings (1:many)
- → post_comments (1:many)
- → post_child_comments (1:many)

**Products**

- → serials (1:many)

**Serials**

- ← users (many:1)
- ← products (many:1)
- → classrooms (1:many)
- → students (1:many)
- → posts (1:many)
- → tasks (1:many)
- → reports (1:many)
- → exercises (1:many - optional)
- → exercise_points (1:many)
- → online_meetings (1:many)

**Classrooms**

- ← serials (many:1)
- → students (1:many)
- → online_meetings (1:many)
- ↔ lessons (many:many via lesson_classroom)
- ↔ exercises (many:many via exercise_classroom)

**Students**

- ← serials (many:1)
- ← users (many:1) - teacher
- ← classrooms (many:1)
- → tasks (1:many)
- → reports (1:many)
- → exercise_points (1:many)
- → post_comments (1:many)
- → post_child_comments (1:many)

**Mapels (Subjects)**

- → lessons (1:many)
- → competences (1:many)
- → posts (1:many)
- → online_meetings (1:many - optional)

**Lessons**

- ← mapels (many:1)
- → themes (1:many)
- → subthemes (1:many)
- → lesson_items (1:many)
- → competences (1:many)
- → exercises (1:many)
- ↔ classrooms (many:many via lesson_classroom)

**Themes**

- ← lessons (many:1)
- → subthemes (1:many)
- → lesson_items (1:many)

**Subthemes**

- ← lessons (many:1)
- ← themes (many:1)
- → lesson_items (1:many)

**Exercises**

- ← lessons (many:1)
- ← serials (many:1 - optional)
- ← exercise_types (many:1)
- → exercise_items (1:many)
- → exercise_points (1:many)
- ↔ classrooms (many:many via exercise_classroom)

**Posts**

- ← serials (many:1)
- ← users (many:1)
- ← mapels (many:1)
- → tasks (1:many)
- → post_comments (1:many)

**Post Comments**

- ← posts (many:1)
- ← users (many:1 - nullable)
- ← students (many:1 - nullable)
- → post_child_comments (1:many)

---

## Database Design Notes

### Data Integrity

1. Foreign key constraints are explicitly defined only in some tables (lesson_items, online_meetings, pivot tables)
2. Most relationships use implicit foreign keys without CASCADE actions
3. Consider adding explicit foreign key constraints for better data integrity

### JSON Columns

The following tables use JSON columns for flexible data storage:

- `lesson_items.shared_to_classes`
- `lessons.shared_to_classes`
- `exercises.shared_to_classes`
- `posts.shared_to_classes`
- `posts.quiz_data`
- `online_meetings.participants`

### Indexing Recommendations

Current indexes are primarily on primary keys. Consider adding:

- Foreign key indexes for better join performance
- Composite indexes on frequently queried combinations
- Indexes on commonly filtered columns (status, grade, semester, etc.)

### Security Considerations

1. Both `users` and `students` tables store plain text passwords (`password_text`) - security risk
2. Consider removing plain text password storage
3. Ensure password hashing uses secure algorithms (bcrypt/argon2)

### Timestamp Conventions

All tables follow Laravel's standard timestamp convention:

- `created_at`: Record creation timestamp
- `updated_at`: Last modification timestamp

---

## Version History

- **Initial Version:** Based on migration files as of 2025-12-24
- **Last Updated:** January 4, 2026

---

## Notes

This document was automatically generated from Laravel migration files. For the most up-to-date schema information, please refer to the migration files in `database/migrations/`.
