<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $tablesToDrop = [
            'admin_activity_logs',
            'attendance_reports',
            'exercise_classroom',
            'grade_reports',
            'lesson_classroom',
            'meeting_participants',
            'report_types',
            'student_reports',
            'task_submissions',
        ];

        $tablesToDrop = array_merge($tablesToDrop, [
            'admins',
            'classrooms',
            'competences',
            'cs_files',
            'cs_logs',
            'cs_messages',
            'cs_rooms',
            'email_logs',
            'exercise_items',
            'exercise_models',
            'exercise_points',
            'exercise_types',
            'exercises',
            'helps',
            'lesson_items',
            'lessons',
            'mapels',
            'online_meeting_participants',
            'online_meetings',
            'post_child_comments',
            'post_comments',
            'posts',
            'products',
            'question_categories',
            'quiz_activity_logs',
            'reports',
            'serial_logs',
            'serials',
            'share_exercises',
            'students',
            'subthemes',
            'tasks',
            'themes',
            'unanswered_questions',
            'users',
        ]);

        foreach (array_unique($tablesToDrop) as $table) {
            Schema::dropIfExists($table);
        }

        $schemaPath = database_path('schema/fresh-install-schema.sql');
        DB::unprepared(file_get_contents($schemaPath));

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach (array_reverse([
            'admin_activity_logs',
            'admins',
            'classrooms',
            'competences',
            'cs_files',
            'cs_logs',
            'cs_messages',
            'cs_rooms',
            'email_logs',
            'exercise_items',
            'exercise_models',
            'exercise_points',
            'exercise_types',
            'exercises',
            'helps',
            'lesson_items',
            'lessons',
            'mapels',
            'online_meeting_participants',
            'online_meetings',
            'post_child_comments',
            'post_comments',
            'posts',
            'products',
            'question_categories',
            'quiz_activity_logs',
            'reports',
            'serial_logs',
            'serials',
            'share_exercises',
            'students',
            'subthemes',
            'tasks',
            'themes',
            'unanswered_questions',
            'users',
        ]) as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }
};
