<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Lesson;
use App\Models\ExerciseType;
use App\Models\ExerciseModel;
use App\Models\Exercise;
use App\Models\ExerciseItem;
use App\Models\Classroom;
use App\Models\Student;

class OriginalStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Users (Guru)
        User::create([
            'name' => 'Guru Demo',
            'username' => 'guru',
            'password' => bcrypt('password'),
            'password_text' => 'password',
            'email' => 'guru@demo.com',
            'role' => 1,
        ]);

        // Products
        $product1 = Product::create(['name' => 'Kelas 4 K13', 'grade' => '4', 'grade_category' => 'SD', 'semester' => '1']);
        $product2 = Product::create(['name' => 'Kelas 4 Merdeka', 'grade' => '4', 'grade_category' => 'SD', 'semester' => '1']);

        // Serials
        Serial::create(['user_id' => 1, 'product_id' => $product1->id, 'serial' => 'K4-K13-2024', 'paket' => '1', 'active' => 'yes']);
        Serial::create(['user_id' => 1, 'product_id' => $product2->id, 'serial' => 'K4-MRD-2024', 'paket' => '1', 'active' => 'yes']);

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

        // Lessons (Materi dari Admin)
        Lesson::create([
            'mapel_id' => 1,
            'name' => 'Pecahan Senilai',
            'description' => 'Materi tentang pecahan yang senilai - memahami konsep dan cara mencari pecahan senilai',
            'grade' => '4',
            'semester' => 1,
            'category' => 1,
        ]);

        Lesson::create([
            'mapel_id' => 1,
            'name' => 'Penjumlahan Pecahan',
            'description' => 'Materi tentang cara menjumlahkan pecahan dengan penyebut sama dan berbeda',
            'grade' => '4',
            'semester' => 1,
            'category' => 1,
        ]);

        Lesson::create([
            'mapel_id' => 1,
            'name' => 'Pengurangan Pecahan',
            'description' => 'Materi tentang cara mengurangkan pecahan',
            'grade' => '4',
            'semester' => 1,
            'category' => 1,
        ]);

        Lesson::create([
            'mapel_id' => 2,
            'name' => 'Siklus Air',
            'description' => 'Materi tentang proses siklus air dan perannya dalam kehidupan',
            'grade' => '4',
            'semester' => 1,
            'category' => 1,
        ]);

        Lesson::create([
            'mapel_id' => 2,
            'name' => 'Bagian-bagian Tumbuhan',
            'description' => 'Materi tentang bagian-bagian tumbuhan dan fungsinya',
            'grade' => '4',
            'semester' => 1,
            'category' => 1,
        ]);

        Lesson::create([
            'mapel_id' => 3,
            'name' => 'Keragaman Budaya Indonesia',
            'description' => 'Materi tentang keberagaman budaya di Indonesia',
            'grade' => '4',
            'semester' => 1,
            'category' => 1,
        ]);

        // Exercise Types
        ExerciseType::create(['kode' => 'UH', 'name' => 'Ulangan Harian']);
        ExerciseType::create(['kode' => 'PTS', 'name' => 'PTS (Penilaian Tengah Semester)']);
        ExerciseType::create(['kode' => 'PAS', 'name' => 'PAS (Penilaian Akhir Semester)']);
        ExerciseType::create(['kode' => 'AKM', 'name' => 'AKM (Asesmen Kompetensi Minimum)']);
        ExerciseType::create(['kode' => 'SPD', 'name' => 'Sumatif/SPD']);

        // Exercise Models
        ExerciseModel::create(['name' => 'Pilihan Ganda']);
        ExerciseModel::create(['name' => 'Essai']);
        ExerciseModel::create(['name' => 'Jawaban Singkat']);

        // Exercises (Soal dari Admin)
        $exercise1 = Exercise::create([
            'lesson_id' => 1,
            'exercise_type_id' => 1,
            'title' => 'Ulangan Harian - Pecahan Senilai',
            'description' => 'Soal latihan tentang pecahan senilai',
            'is_admin' => 1,
        ]);

        $exercise2 = Exercise::create([
            'lesson_id' => 2,
            'exercise_type_id' => 1,
            'title' => 'Ulangan Harian - Penjumlahan Pecahan',
            'description' => 'Soal latihan penjumlahan pecahan',
            'is_admin' => 1,
        ]);

        $exercise3 = Exercise::create([
            'lesson_id' => 4,
            'exercise_type_id' => 2,
            'title' => 'PTS - Siklus Air',
            'description' => 'Soal penilaian tengah semester tentang siklus air',
            'is_admin' => 1,
        ]);

        $exercise4 = Exercise::create([
            'lesson_id' => 5,
            'exercise_type_id' => 1,
            'title' => 'Ulangan Harian - Bagian Tumbuhan',
            'description' => 'Soal latihan tentang bagian-bagian tumbuhan',
            'is_admin' => 1,
        ]);

        $exercise5 = Exercise::create([
            'lesson_id' => 6,
            'exercise_type_id' => 3,
            'title' => 'PAS - Keragaman Budaya',
            'description' => 'Soal penilaian akhir semester tentang budaya Indonesia',
            'is_admin' => 1,
        ]);

        $exercise6 = Exercise::create([
            'lesson_id' => 3,
            'exercise_type_id' => 1,
            'title' => 'Ulangan Harian - Pengurangan Pecahan',
            'description' => 'Soal latihan pengurangan pecahan',
            'is_admin' => 1,
        ]);

        // Exercise Items
        // Soal untuk Exercise 1
        ExerciseItem::create([
            'exercise_id' => $exercise1->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 1,
            'question' => 'Pecahan yang senilai dengan 1/2 adalah...',
            'selection' => json_encode(['a' => '1/4', 'b' => '2/4', 'c' => '3/4', 'd' => '2/3']),
            'answer' => 'b',
            'is_user' => 0,
        ]);

        ExerciseItem::create([
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

        ExerciseItem::create([
            'exercise_id' => $exercise1->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 2,
            'exercise_choice' => 0,
            'exercise_number' => 3,
            'question' => 'Jelaskan cara mencari pecahan yang senilai dengan 3/5!',
            'selection' => null,
            'answer' => 'Mengalikan pembilang dan penyebut dengan bilangan yang sama',
            'is_user' => 0,
        ]);

        // Soal untuk Exercise 2
        ExerciseItem::create([
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

        ExerciseItem::create([
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

        // Soal untuk Exercise 3
        ExerciseItem::create([
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

        ExerciseItem::create([
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

        ExerciseItem::create([
            'exercise_id' => $exercise3->id,
            'exercise_type_id' => 2,
            'exercise_model_id' => 2,
            'exercise_choice' => 0,
            'exercise_number' => 3,
            'question' => 'Jelaskan tahapan-tahapan dalam siklus air!',
            'selection' => null,
            'answer' => 'Evaporasi -> Kondensasi -> Presipitasi -> Infiltrasi -> kembali ke evaporasi',
            'is_user' => 0,
        ]);

        // Soal untuk Exercise 4 (Bagian Tumbuhan)
        ExerciseItem::create([
            'exercise_id' => $exercise4->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 1,
            'question' => 'Bagian tumbuhan yang berfungsi menyerap air dan mineral adalah...',
            'selection' => json_encode(['a' => 'Daun', 'b' => 'Batang', 'c' => 'Akar', 'd' => 'Bunga']),
            'answer' => 'c',
            'is_user' => 0,
        ]);

        ExerciseItem::create([
            'exercise_id' => $exercise4->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 2,
            'question' => 'Proses pembuatan makanan pada tumbuhan terjadi di bagian...',
            'selection' => json_encode(['a' => 'Akar', 'b' => 'Batang', 'c' => 'Daun', 'd' => 'Buah']),
            'answer' => 'c',
            'is_user' => 0,
        ]);

        ExerciseItem::create([
            'exercise_id' => $exercise4->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 2,
            'exercise_choice' => 0,
            'exercise_number' => 3,
            'question' => 'Sebutkan 3 fungsi utama batang pada tumbuhan!',
            'selection' => null,
            'answer' => 'Menyangga tubuh tumbuhan, mengangkut air dan mineral, menyimpan cadangan makanan',
            'is_user' => 0,
        ]);

        // Soal untuk Exercise 5 (Keragaman Budaya)
        ExerciseItem::create([
            'exercise_id' => $exercise5->id,
            'exercise_type_id' => 3,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 1,
            'question' => 'Tari Saman berasal dari daerah...',
            'selection' => json_encode(['a' => 'Jawa Barat', 'b' => 'Aceh', 'c' => 'Bali', 'd' => 'Papua']),
            'answer' => 'b',
            'is_user' => 0,
        ]);

        ExerciseItem::create([
            'exercise_id' => $exercise5->id,
            'exercise_type_id' => 3,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 2,
            'question' => 'Rumah adat Joglo berasal dari...',
            'selection' => json_encode(['a' => 'Jawa Tengah', 'b' => 'Sumatera', 'c' => 'Kalimantan', 'd' => 'Sulawesi']),
            'answer' => 'a',
            'is_user' => 0,
        ]);

        ExerciseItem::create([
            'exercise_id' => $exercise5->id,
            'exercise_type_id' => 3,
            'exercise_model_id' => 2,
            'exercise_choice' => 0,
            'exercise_number' => 3,
            'question' => 'Mengapa Indonesia memiliki keragaman budaya yang tinggi?',
            'selection' => null,
            'answer' => 'Karena Indonesia terdiri dari berbagai suku bangsa, bahasa, adat istiadat, dan letak geografis yang berbeda',
            'is_user' => 0,
        ]);

        // Soal untuk Exercise 6 (Pengurangan Pecahan)
        ExerciseItem::create([
            'exercise_id' => $exercise6->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 1,
            'question' => '3/4 - 1/4 = ...',
            'selection' => json_encode(['a' => '1/4', 'b' => '2/4', 'c' => '3/8', 'd' => '1/2']),
            'answer' => 'b',
            'is_user' => 0,
        ]);

        ExerciseItem::create([
            'exercise_id' => $exercise6->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 1,
            'exercise_choice' => 4,
            'exercise_number' => 2,
            'question' => '5/6 - 2/6 = ...',
            'selection' => json_encode(['a' => '2/6', 'b' => '3/6', 'c' => '1/2', 'd' => '1/3']),
            'answer' => 'c',
            'is_user' => 0,
        ]);

        ExerciseItem::create([
            'exercise_id' => $exercise6->id,
            'exercise_type_id' => 1,
            'exercise_model_id' => 3,
            'exercise_choice' => 0,
            'exercise_number' => 3,
            'question' => '7/8 - 3/8 = ...',
            'selection' => null,
            'answer' => '4/8 atau 1/2',
            'is_user' => 0,
        ]);
    }
}
