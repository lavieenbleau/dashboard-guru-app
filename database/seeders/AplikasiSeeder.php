<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mapel;
use App\Models\Product;
use App\Models\Serial;
use App\Models\Classroom;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AplikasiSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n🚀 Setup Aplikasi Berdasarkan Grade & Kurikulum\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        // 1. Buat atau ambil user guru
        $guru = User::firstOrCreate(
            ['email' => 'guru@sekolah.com'],
            [
                'name' => 'Guru Utama',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        echo "✅ User Guru: {$guru->email}\n\n";

        // 2. Buat Mata Pelajaran SMP
        echo "📚 Membuat Mata Pelajaran...\n";
        $mapelData = [
            'Matematika',
            'IPA (Ilmu Pengetahuan Alam)',
            'IPS (Ilmu Pengetahuan Sosial)',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'PPKn (Pendidikan Pancasila)',
            'PJOK (Pendidikan Jasmani)',
            'Seni Budaya',
            'Prakarya',
            'Informatika',
            'Pendidikan Agama',
        ];

        // Hapus mapel yang ada dengan aman
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('mapels')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        foreach ($mapelData as $mapelName) {
            Mapel::create(['name' => $mapelName]);
        }
        echo "   ✓ " . count($mapelData) . " mata pelajaran dibuat\n\n";

        // 3. Buat Products berdasarkan Grade & Kurikulum
        echo "📦 Membuat Produk Pembelajaran...\n";
        
        $grades = [
            ['grade' => '7', 'name' => 'Kelas 7 (VII)'],
            ['grade' => '8', 'name' => 'Kelas 8 (VIII)'],
            ['grade' => '9', 'name' => 'Kelas 9 (IX)'],
        ];

        $kurikulum = [
            ['code' => 'K13', 'name' => 'Kurikulum 2013'],
            ['code' => 'KM', 'name' => 'Kurikulum Merdeka'],
        ];

        $products = [];
        
        foreach ($grades as $grade) {
            foreach ($kurikulum as $k) {
                // Semester 1
                $product1 = Product::create([]);
                $products[] = [
                    'product' => $product1,
                    'grade' => $grade['grade'],
                    'semester' => '1',
                    'kurikulum' => $k['code'],
                    'description' => "{$grade['name']} - {$k['name']} - Semester 1"
                ];
                
                // Semester 2
                $product2 = Product::create([]);
                $products[] = [
                    'product' => $product2,
                    'grade' => $grade['grade'],
                    'semester' => '2',
                    'kurikulum' => $k['code'],
                    'description' => "{$grade['name']} - {$k['name']} - Semester 2"
                ];
            }
        }
        echo "   ✓ " . count($products) . " produk dibuat (3 Grade × 2 Kurikulum × 2 Semester)\n\n";

        // 4. Buat Serials untuk setiap produk
        echo "🔑 Membuat Serial Number & Aplikasi...\n";
        
        $serials = [];
        foreach ($products as $index => $productData) {
            $serialCode = sprintf(
                "%s-%s-S%s-%04d",
                $productData['kurikulum'],
                $productData['grade'],
                $productData['semester'],
                $index + 1
            );
            
            $serial = Serial::create([
                'user_id' => $guru->id,
                'product_id' => $productData['product']->id,
                'code' => $serialCode,
                'is_active' => true,
                'activated_at' => now(),
                'expired_at' => now()->addYear(),
            ]);
            
            $serials[] = [
                'serial' => $serial,
                'description' => $productData['description']
            ];
            
            echo "   ✓ {$serialCode} - {$productData['description']}\n";
        }
        echo "\n";

        // 5. Buat Classrooms untuk beberapa serial
        echo "🏫 Membuat Kelas...\n";
        
        $classroomCount = 0;
        foreach ($serials as $serialData) {
            $serial = $serialData['serial'];
            
            // Buat 2 kelas per aplikasi (A & B)
            foreach (['A', 'B'] as $section) {
                // Extract grade from code (K13-7-S1-0001 -> 7)
                preg_match('/-(\d+)-/', $serial->code, $matches);
                $grade = $matches[1] ?? '7';
                
                Classroom::create([
                    'serial_id' => $serial->id,
                    'name' => "Kelas {$grade}{$section}",
                ]);
                $classroomCount++;
            }
        }
        echo "   ✓ {$classroomCount} kelas dibuat\n\n";

        // Summary
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✨ SETUP SELESAI!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "📊 Ringkasan:\n";
        echo "   • Mata Pelajaran: " . count($mapelData) . "\n";
        echo "   • Produk: " . count($products) . "\n";
        echo "   • Serial/Aplikasi: " . count($serials) . "\n";
        echo "   • Kelas: {$classroomCount}\n\n";
        echo "🔐 Login:\n";
        echo "   Email    : guru@sekolah.com\n";
        echo "   Password : password\n\n";
        echo "📱 Struktur Aplikasi:\n";
        echo "   • Kelas 7, 8, 9\n";
        echo "   • Kurikulum 2013 & Kurikulum Merdeka\n";
        echo "   • Semester 1 & 2 untuk setiap grade\n\n";
        echo "💡 Akses: Login → Menu Aplikasi → Pilih Serial\n\n";
    }
}
