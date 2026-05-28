<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Theme;
use App\Models\Subtheme;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\ExerciseType;
use App\Models\ExerciseModel;
use App\Models\Classroom;
use App\Models\Student;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // User
        User::create([
            'name' => 'Guru Demo',
            'username' => 'guru',
            'email' => 'guru@demo.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'password_text' => 'password',
            'role' => 1,
        ]);

        // Products
        $product1 = Product::create(['name' => 'Kelas 4 K13', 'grade_category' => 'SD', 'grade' => '4']);
        $product2 = Product::create(['name' => 'Kelas 4 Merdeka', 'grade_category' => 'SD', 'grade' => '4']);

        // Serials
        Serial::create(['user_id' => 1, 'product_id' => $product1->id, 'serial' => 'K4-K13-2024', 'paket' => '1', 'active' => 'yes', 'expired_at' => now()->addYear()]);
        Serial::create(['user_id' => 1, 'product_id' => $product2->id, 'serial' => 'K4-MRD-2024', 'paket' => '1', 'active' => 'yes', 'expired_at' => now()->addYear()]);

        // Classrooms
        Classroom::create(['serial_id' => 1, 'name' => 'Kelas 4A', 'grade' => '4', 'code' => 'CLS-000001']);
        Classroom::create(['serial_id' => 1, 'name' => 'Kelas 4B', 'grade' => '4', 'code' => 'CLS-000002']);

        // Students
        $students = [
            ['serial_id' => 1, 'user_id' => 1, 'classroom_id' => 1, 'name' => 'Ahmad Rizki', 'nis' => '2024001', 'username' => '2024001', 'password' => bcrypt('password'), 'email' => 'ahmad@student.com', 'phone' => '081234567801'],
            ['serial_id' => 1, 'user_id' => 1, 'classroom_id' => 1, 'name' => 'Siti Nur Azizah', 'nis' => '2024002', 'username' => '2024002', 'password' => bcrypt('password'), 'email' => 'siti@student.com', 'phone' => '081234567802'],
            ['serial_id' => 1, 'user_id' => 1, 'classroom_id' => 1, 'name' => 'Budi Santoso', 'nis' => '2024003', 'username' => '2024003', 'password' => bcrypt('password'), 'email' => 'budi@student.com', 'phone' => '081234567803'],
            ['serial_id' => 1, 'user_id' => 1, 'classroom_id' => 2, 'name' => 'Dewi Lestari', 'nis' => '2024004', 'username' => '2024004', 'password' => bcrypt('password'), 'email' => 'dewi@student.com', 'phone' => '081234567804'],
            ['serial_id' => 1, 'user_id' => 1, 'classroom_id' => 2, 'name' => 'Eko Prasetyo', 'nis' => '2024005', 'username' => '2024005', 'password' => bcrypt('password'), 'email' => 'eko@student.com', 'phone' => '081234567805'],
            ['serial_id' => 1, 'user_id' => 1, 'classroom_id' => 2, 'name' => 'Fitri Handayani', 'nis' => '2024006', 'username' => '2024006', 'password' => bcrypt('password'), 'email' => 'fitri@student.com', 'phone' => '081234567806'],
        ];
        foreach ($students as $s) {
            Student::create($s);
        }

        // Mapels
        $mapels = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'PJOK', 'SBdP', 'PAI', 'PPKn'];
        foreach ($mapels as $m) {
            Mapel::create(['name' => $m]);
        }

        // Lessons (Admin Materials)
        $lesson1 = Lesson::create(['name' => 'Pecahan Senilai', 'mapel_id' => 1, 'category' => 1, 'grade' => 1, 'semester' => 1]);
        $lesson2 = Lesson::create(['name' => 'Penjumlahan Pecahan', 'mapel_id' => 1, 'category' => 1, 'grade' => 1, 'semester' => 1]);
        $lesson3 = Lesson::create(['name' => 'Pengurangan Pecahan', 'mapel_id' => 1, 'category' => 1, 'grade' => 1, 'semester' => 1]);
        $lesson4 = Lesson::create(['name' => 'Siklus Air', 'mapel_id' => 2, 'category' => 1, 'grade' => 1, 'semester' => 1]);
        $lesson5 = Lesson::create(['name' => 'Bagian-bagian Tumbuhan', 'mapel_id' => 2, 'category' => 1, 'grade' => 1, 'semester' => 1]);
        $lesson6 = Lesson::create(['name' => 'Keragaman Budaya', 'mapel_id' => 3, 'category' => 1, 'grade' => 1, 'semester' => 1]);

        // Themes - link to lessons
        $theme1 = Theme::create(['lesson_id' => $lesson1->id, 'theme' => 1, 'name' => 'Tema 1: Indahnya Kebersamaan']);
        $theme2 = Theme::create(['lesson_id' => $lesson2->id, 'theme' => 1, 'name' => 'Tema 1: Indahnya Kebersamaan']);
        $theme3 = Theme::create(['lesson_id' => $lesson3->id, 'theme' => 2, 'name' => 'Tema 2: Selalu Berhemat Energi']);
        $theme4 = Theme::create(['lesson_id' => $lesson4->id, 'theme' => 2, 'name' => 'Tema 2: Selalu Berhemat Energi']);
        $theme5 = Theme::create(['lesson_id' => $lesson5->id, 'theme' => 3, 'name' => 'Tema 3: Peduli Terhadap Makhluk Hidup']);
        $theme6 = Theme::create(['lesson_id' => $lesson6->id, 'theme' => 1, 'name' => 'Tema 1: Indahnya Kebersamaan']);

        // Subthemes
        $sub1 = Subtheme::create(['theme_id' => $theme1->id, 'subtheme' => 1, 'name' => 'Subtema 1: Keberagaman Budaya Bangsaku']);
        $sub2 = Subtheme::create(['theme_id' => $theme1->id, 'subtheme' => 2, 'name' => 'Subtema 2: Kebersamaan dalam Keberagaman']);
        $sub3 = Subtheme::create(['theme_id' => $theme2->id, 'subtheme' => 2, 'name' => 'Subtema 2: Kebersamaan dalam Keberagaman']);
        $sub4 = Subtheme::create(['theme_id' => $theme3->id, 'subtheme' => 1, 'name' => 'Subtema 1: Sumber Energi']);
        $sub5 = Subtheme::create(['theme_id' => $theme4->id, 'subtheme' => 1, 'name' => 'Subtema 1: Sumber Energi']);
        $sub6 = Subtheme::create(['theme_id' => $theme5->id, 'subtheme' => 1, 'name' => 'Subtema 1: Hewan dan Tumbuhan']);
        $sub7 = Subtheme::create(['theme_id' => $theme6->id, 'subtheme' => 1, 'name' => 'Subtema 1: Keberagaman Budaya Bangsaku']);

        // Lesson Items (Admin Materials)
        LessonItem::create(['lesson_id' => $lesson1->id, 'theme_id' => $theme1->id, 'subtheme_id' => $sub1->id, 'title' => 'Pengenalan Pecahan', 'description' => 'Memahami konsep pecahan', 'number' => 1, 'is_admin' => 1]);
        LessonItem::create(['lesson_id' => $lesson1->id, 'theme_id' => $theme1->id, 'subtheme_id' => $sub1->id, 'title' => 'Pecahan Senilai', 'description' => 'Mencari pecahan yang senilai', 'number' => 2, 'is_admin' => 1]);
        LessonItem::create(['lesson_id' => $lesson1->id, 'theme_id' => $theme1->id, 'subtheme_id' => $sub2->id, 'title' => 'Membandingkan Pecahan', 'description' => 'Cara membandingkan pecahan senilai', 'number' => 3, 'is_admin' => 1]);
        LessonItem::create(['lesson_id' => $lesson2->id, 'theme_id' => $theme2->id, 'subtheme_id' => $sub3->id, 'title' => 'Penjumlahan Pecahan Berpenyebut Sama', 'description' => 'Cara menjumlahkan pecahan', 'number' => 1, 'is_admin' => 1]);
        LessonItem::create(['lesson_id' => $lesson2->id, 'theme_id' => $theme2->id, 'subtheme_id' => $sub3->id, 'title' => 'Penjumlahan Pecahan Berpenyebut Beda', 'description' => 'Menjumlahkan dengan penyebut berbeda', 'number' => 2, 'is_admin' => 1]);
        LessonItem::create(['lesson_id' => $lesson3->id, 'theme_id' => $theme3->id, 'subtheme_id' => $sub4->id, 'title' => 'Pengurangan Pecahan Berpenyebut Sama', 'description' => 'Cara mengurangkan pecahan', 'number' => 1, 'is_admin' => 1]);
        LessonItem::create(['lesson_id' => $lesson4->id, 'theme_id' => $theme4->id, 'subtheme_id' => $sub5->id, 'title' => 'Proses Siklus Air', 'description' => 'Memahami siklus air', 'number' => 1, 'is_admin' => 1]);
        LessonItem::create(['lesson_id' => $lesson5->id, 'theme_id' => $theme5->id, 'subtheme_id' => $sub6->id, 'title' => 'Akar, Batang, Daun', 'description' => 'Bagian-bagian tumbuhan', 'number' => 1, 'is_admin' => 1]);
        LessonItem::create(['lesson_id' => $lesson6->id, 'theme_id' => $theme6->id, 'subtheme_id' => $sub7->id, 'title' => 'Budaya Nusantara', 'description' => 'Keberagaman budaya Indonesia', 'number' => 1, 'is_admin' => 1]);

        // Exercise Types
        $exercise_types = [
            ['kode' => 'UH', 'name' => 'Ulangan Harian'],
            ['kode' => 'SL', 'name' => 'Soal Latihan'],
            ['kode' => 'PTS', 'name' => 'PTS (Penilaian Tengah Semester)'],
            ['kode' => 'PAS', 'name' => 'PAS (Penilaian Akhir Semester)'],
            ['kode' => 'AKM', 'name' => 'AKM (Asesmen Kompetensi Minimum)'],
            ['kode' => 'SPD', 'name' => 'Sumatif/SPD'],
        ];
        foreach ($exercise_types as $et) {
            ExerciseType::create($et);
        }

        // Exercise Models
        $exercise_models = [
            ['name' => 'Pilihan Ganda'],
            ['name' => 'Essai'],
            ['name' => 'Jawaban Singkat'],
        ];
        foreach ($exercise_models as $em) {
            ExerciseModel::create($em);
        }

        // Exercises (Soal dari Admin)
        // Soal untuk Matematika - Pecahan
        $exercise1 = \App\Models\Exercise::create([
            'lesson_id' => $lesson1->id,
            'exercise_type_id' => 1, // Ulangan Harian
            'title' => 'Ulangan Harian - Pecahan Senilai',
            'description' => 'Soal latihan tentang pecahan senilai',
            'is_admin' => 1,
        ]);

        $exercise2 = \App\Models\Exercise::create([
            'lesson_id' => $lesson2->id,
            'exercise_type_id' => 1, // Ulangan Harian
            'title' => 'Ulangan Harian - Penjumlahan Pecahan',
            'description' => 'Soal latihan penjumlahan pecahan',
            'is_admin' => 1,
        ]);

        // Soal untuk IPA - Siklus Air
        $exercise3 = \App\Models\Exercise::create([
            'lesson_id' => $lesson4->id,
            'exercise_type_id' => 2, // PTS
            'title' => 'PTS - Siklus Air',
            'description' => 'Soal penilaian tengah semester tentang siklus air',
            'is_admin' => 1,
        ]);

        // Exercise Items (Soal-soal)
        // Soal Pilihan Ganda untuk Exercise 1
        \App\Models\ExerciseItem::create([
            'exercise_id' => $exercise1->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 1, // Pilihan Ganda
            'exercise_choice' => 4, // 4 pilihan
            'exercise_number' => 1,
            'question' => 'Pecahan yang senilai dengan 1/2 adalah...',
            'selection' => json_encode(['a' => '1/4', 'b' => '2/4', 'c' => '3/4', 'd' => '2/3']),
            'answer' => 'b',
            'is_user' => 0,
        ]);

        \App\Models\ExerciseItem::create([
            'exercise_id' => $exercise1->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 2,
            'question' => 'Pecahan 2/6 jika disederhanakan menjadi...',
            'selection' => json_encode(['a' => '1/2', 'b' => '1/3', 'c' => '2/3', 'd' => '1/4']),
            'answer' => 'b',
            'is_user' => 0,
        ]);

        \App\Models\ExerciseItem::create([
            'exercise_id' => $exercise1->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 2, // Essai
            'exercise_choice' => 0,
            'exercise_number' => 3,
            'question' => 'Jelaskan cara mencari pecahan yang senilai dengan 3/5!',
            'selection' => null,
            'answer' => 'Mengalikan pembilang dan penyebut dengan bilangan yang sama',
            'is_user' => 0,
        ]);

        // Soal untuk Exercise 2
        \App\Models\ExerciseItem::create([
            'exercise_id' => $exercise2->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 1,
            'question' => '1/4 + 2/4 = ...',
            'selection' => json_encode(['a' => '2/4', 'b' => '3/4', 'c' => '3/8', 'd' => '1/2']),
            'answer' => 'b',
            'is_user' => 0,
        ]);

        \App\Models\ExerciseItem::create([
            'exercise_id' => $exercise2->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 2,
            'question' => '3/5 + 1/5 = ...',
            'selection' => json_encode(['a' => '3/5', 'b' => '4/5', 'c' => '4/10', 'd' => '1']),
            'answer' => 'b',
            'is_user' => 0,
        ]);

        // Soal untuk Exercise 3 (IPA)
        \App\Models\ExerciseItem::create([
            'exercise_id' => $exercise3->id,
            'exercise_type_id' => 2,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 1,
            'question' => 'Proses penguapan air dari permukaan bumi disebut...',
            'selection' => json_encode(['a' => 'Kondensasi', 'b' => 'Evaporasi', 'c' => 'Presipitasi', 'd' => 'Infiltrasi']),
            'answer' => 'b',
            'is_user' => 0,
        ]);

        \App\Models\ExerciseItem::create([
            'exercise_id' => $exercise3->id,
            'exercise_type_id' => 2,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 2,
            'question' => 'Uap air yang berubah menjadi titik-titik air disebut...',
            'selection' => json_encode(['a' => 'Evaporasi', 'b' => 'Kondensasi', 'c' => 'Presipitasi', 'd' => 'Transpirasi']),
            'answer' => 'b',
            'is_user' => 0,
        ]);

        \App\Models\ExerciseItem::create([
            'exercise_id' => $exercise3->id,
            'exercise_type_id' => 2,
            'exercise_model_id' => 2, // Essai
            'exercise_choice' => 0,
            'exercise_number' => 3,
            'question' => 'Jelaskan tahapan-tahapan dalam siklus air!',
            'selection' => null,
            'answer' => 'Evaporasi -> Kondensasi -> Presipitasi -> Infiltrasi -> kembali ke evaporasi',
            'is_user' => 0,
        ]);
    }
}
