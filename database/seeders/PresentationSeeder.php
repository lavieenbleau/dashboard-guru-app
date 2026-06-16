<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Serial;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Mapel;
use App\Models\Lesson;
use App\Models\Post;
use App\Models\Task;
use App\Models\Exercise;
use App\Models\ExercisePoint;

class PresentationSeeder extends Seeder
{
    public function run()
    {
        // 1. Create or Find User
        $user = User::firstOrCreate(
            ['id' => 16],
            [
                'name' => 'Guru IPA',
                'email' => 'gurusci@sci.com',
                'password' => Hash::make('password'),
                'role' => 'guru'
            ]
        );

        // Update email if user already existed but had different email
        if ($user->email !== 'gurusci@sci.com') {
            $user->update(['email' => 'gurusci@sci.com', 'password' => Hash::make('password')]);
        }

        // 2. Create Serial (Application Access)
        $serial = Serial::firstOrCreate(
            ['user_id' => $user->id],
            [
                'product_id' => 1,
                'serial' => 'PRES-12345-67890',
                'paket' => 'Paket Premium',
                'max_students_per_class' => 45,
                'active' => 1,
                'expired_at' => now()->addYear()
            ]
        );

        // 3. Create Classroom
        $classroom = Classroom::firstOrCreate(
            ['serial_id' => $serial->id, 'name' => 'Kelas X IPA 1'],
            ['grade' => 'X', 'code' => 'X-IPA-1-PRES']
        );

        // 4. Create Students
        $students = [];
        $studentNames = ['Budi Santoso', 'Siti Aminah', 'Andi Darmawan', 'Rina Kartika', 'Joko Susilo'];
        foreach ($studentNames as $idx => $name) {
            $students[] = Student::firstOrCreate(
                ['serial_id' => $serial->id, 'nis' => '100' . ($idx + 1)],
                [
                    'classroom_id' => $classroom->id,
                    'user_id' => $user->id,
                    'name' => $name,
                    'username' => strtolower(str_replace(' ', '', $name)),
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'absen' => $idx + 1,
                ]
            );
        }

        // 5. Create Mapel & Lesson
        $mapel = Mapel::firstOrCreate(
            ['serial_id' => $serial->id, 'name' => 'Ilmu Pengetahuan Alam (IPA)']
        );

        // Lesson Materi (Semester 1)
        $lessonMateri = Lesson::firstOrCreate(
            [
                'serial_id' => $serial->id,
                'mapel_id' => $mapel->id,
                'name' => 'Bab 1: Ekosistem',
                'semester' => 1,
                'category' => Lesson::CATEGORY_MATERI ?? 1
            ]
        );

        // Lesson Soal (Semester 1)
        $lessonSoal = Lesson::firstOrCreate(
            [
                'serial_id' => $serial->id,
                'mapel_id' => $mapel->id,
                'name' => 'Penilaian Ekosistem',
                'semester' => 1,
                'category' => Lesson::CATEGORY_SOAL ?? 3
            ]
        );

        // 6. Create Posts (1 Materi, 1 Tugas)
        $postMateri = Post::firstOrCreate(
            ['serial_id' => $serial->id, 'title' => 'Pengenalan Ekosistem dan Rantai Makanan'],
            [
                'user_id' => $user->id,
                'mapel_id' => $mapel->id,
                'classroom_id' => $classroom->id,
                'description' => 'Silakan pelajari materi tentang Ekosistem Darat dan Ekosistem Air.',
                'category' => json_encode(['lesson_id' => $lessonMateri->id]),
                'is_task' => 0
            ]
        );

        $postTugas = Post::firstOrCreate(
            ['serial_id' => $serial->id, 'title' => 'Tugas Rantai Makanan'],
            [
                'user_id' => $user->id,
                'mapel_id' => $mapel->id,
                'classroom_id' => $classroom->id,
                'description' => 'Gambarkan rantai makanan yang ada di sawah beserta penjelasannya.',
                'category' => json_encode(['lesson_id' => $lessonMateri->id]),
                'is_task' => 1
            ]
        );

        // 7. Seed Tugas points (Laporan Harian)
        foreach ($students as $student) {
            Task::firstOrCreate(
                ['serial_id' => $serial->id, 'post_id' => $postTugas->id, 'student_id' => $student->id],
                [
                    'description' => 'Jawaban terlampir, Pak/Bu.',
                    'point' => rand(75, 100),
                    'created_at' => now()->subDays(rand(1, 3))
                ]
            );
        }

        // 8. Create Exercises (UH, PTS, PAS, AKM)
        $exercisesData = [
            ['title' => 'Ulangan Harian 1: Ekosistem', 'type' => 1],
            ['title' => 'PTS Ganjil IPA', 'type' => 2],
            ['title' => 'PAS Ganjil IPA', 'type' => 3],
            ['title' => 'Simulasi AKM Numerasi & Literasi', 'type' => 4],
        ];

        foreach ($exercisesData as $data) {
            $ex = Exercise::firstOrCreate(
                [
                    'serial_id' => $serial->id,
                    'lesson_id' => $lessonSoal->id,
                    'title' => $data['title'],
                    'exercise_type_id' => $data['type']
                ],
                [
                    'user_id' => $user->id,
                    'is_admin' => 0, // Guru soal
                    'time_limit' => 60,
                    'created_at' => now()->subDays(rand(1, 10))
                ]
            );

            // 9. Seed Exercise points (Laporan Harian)
            foreach ($students as $student) {
                ExercisePoint::firstOrCreate(
                    [
                        'serial_id' => $serial->id,
                        'student_id' => $student->id,
                        'exercise_id' => $ex->id
                    ],
                    [
                        'exercise_point' => rand(70, 100),
                        'answer' => json_encode(['Q1' => 'A', 'Q2' => 'B']), // Dummy answer indicating submission
                        'updated_at' => now()->subDays(rand(0, 5))
                    ]
                );
            }
        }
    }
}
