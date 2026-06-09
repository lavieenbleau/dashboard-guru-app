<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Serial;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    // 3. Pilih kelas
    public function pilihKelas($serial)
    {
        $serial = Serial::findOrFail($serial);
        $classrooms = Classroom::where('serial_id', $serial->id)
            ->withCount('students')
            ->get();

        return view('guru.kelas.pilih', compact('serial', 'classrooms'));
    }

    // store a new classroom for the serial
    public function store(Request $request, $serial)
    {
        $serial = Serial::findOrFail($serial);

        // Check classroom limit
        $currentClassroomsCount = Classroom::where('serial_id', $serial->id)->count();
        $maxClassrooms = $serial->getMaxClassrooms();
        
        if ($currentClassroomsCount >= $maxClassrooms) {
            return redirect()->route('guru.kelas.pilih', ['serial' => $serial->id])
                ->with('error', 'Gagal membuat kelas. Anda telah mencapai batas maksimal pembuatan kelas untuk paket ini (' . $maxClassrooms . ' Kelas).');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50',
        ]);

        // Generate unique classroom code
        $code = $this->generateClassroomCode();

        $classroom = new Classroom();
        $classroom->serial_id = $serial->id;
        $classroom->name = $data['name'];
        $classroom->grade = $data['grade'] ?? null;
        $classroom->code = $code;
        $classroom->save();

        return redirect()->route('guru.kelas.pilih', ['serial' => $serial->id])->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Generate unique classroom code
     */
    private function generateClassroomCode()
    {
        do {
            $code = strtoupper(
                \Illuminate\Support\Str::random(4) . '-' .
                \Illuminate\Support\Str::random(4) . '-' .
                \Illuminate\Support\Str::random(4) . '-' .
                \Illuminate\Support\Str::random(4)
            );
        } while (Classroom::where('code', $code)->exists());

        return $code;
    }

    // remove a classroom
    public function destroy($serial, $classroom)
    {
        $serial = Serial::findOrFail($serial);
        $c = Classroom::where('serial_id', $serial->id)->where('id', $classroom)->firstOrFail();
        $c->delete();

        return redirect()->route('guru.kelas.pilih', ['serial' => $serial->id])->with('success', 'Kelas berhasil dihapus.');
    }

    // 4. Dashboard kelas
    public function dashboard($serial, $classroom)
    {
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroom);
        
        // Get students in this classroom
        $students = \App\Models\Student::where('classroom_id', $classroom->id)
            ->orderBy('name', 'asc')
            ->get();
        
        $studentCount = $students->count();
        $maxStudents = $classroom->getMaxStudents();
        $isFull = $studentCount >= $maxStudents;
        $isOverCapacity = $studentCount > $maxStudents;

        return view('guru.kelas.dashboard', compact('serial', 'classroom', 'students', 'studentCount', 'maxStudents', 'isFull', 'isOverCapacity'));
    }
    
    // Store new student
    public function storeStudent(Request $request, $serial, $classroom)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'nis' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
        ]);
        
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroom);
        
        // Check capacity
        if ($classroom->isFull()) {
            return redirect()->route('guru.kelas.dashboard', [$serial->id, $classroom->id])
                ->with('error', 'Kelas sudah mencapai kapasitas maksimum ' . $classroom->getMaxStudents() . ' siswa.');
        }
        
        // Generate username from NIS or name
        $username = $request->nis ?? strtolower(str_replace(' ', '', $request->name));
        
        // Default password
        $defaultPassword = '12345678';
        
        \App\Models\Student::create([
            'serial_id' => $serial->id,
            'user_id' => auth()->id(),
            'classroom_id' => $classroom->id,
            'name' => $request->name,
            'username' => $username,
            'password' => bcrypt($defaultPassword),
            'nis' => $request->nis,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        
        return redirect()->route('guru.kelas.dashboard', [$serial->id, $classroom->id])
            ->with('success', 'Siswa berhasil ditambahkan!');
    }
    
    // Update student
    public function updateStudent(Request $request, $serial, $classroom, $student)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'nis' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
        ]);
        
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroom);
        $student = \App\Models\Student::findOrFail($student);
        
        $student->update([
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        
        return redirect()->route('guru.kelas.dashboard', [$serial->id, $classroom->id])
            ->with('success', 'Data siswa berhasil diperbarui!');
    }
    
    // Update student password
    public function updateStudentPassword(Request $request, $serial, $classroom, $student)
    {
        $request->validate([
            'password' => 'required|string|min:6|max:50',
        ]);
        
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroom);
        $student = \App\Models\Student::findOrFail($student);
        
        $student->update([
            'password' => bcrypt($request->password),
        ]);
        
        return redirect()->route('guru.kelas.dashboard', [$serial->id, $classroom->id])
            ->with('success', 'Password siswa berhasil diubah!');
    }
    
    // Delete student
    public function destroyStudent($serial, $classroom, $student)
    {
        $student = \App\Models\Student::findOrFail($student);
        $student->delete();
        
        return redirect()->route('guru.kelas.dashboard', [$serial, $classroom])
            ->with('success', 'Siswa berhasil dihapus!');
    }
    
    // Import students from CSV
    public function importStudents(Request $request, $serial, $classroom)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);
        
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroom);
        
        // Check if classroom is already full before import
        $currentCount = $classroom->students()->count();
        $maxStudents = $classroom->getMaxStudents();
        $remaining = $maxStudents - $currentCount;
        
        if ($remaining <= 0) {
            return redirect()->route('guru.kelas.dashboard', [$serial->id, $classroom->id])
                ->with('error', 'Kelas sudah mencapai kapasitas maksimum ' . $maxStudents . ' siswa. Tidak dapat mengimpor siswa baru.');
        }
        
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        // Read CSV file
        $csv = array_map('str_getcsv', file($path));
        
        // Get header row
        $header = array_shift($csv);
        
        // Normalize header (trim and lowercase)
        $header = array_map(function($h) {
            return strtolower(trim($h));
        }, $header);
        
        $imported = 0;
        $skippedCapacity = 0;
        $errors = [];
        $defaultPassword = '12345678';
        
        foreach ($csv as $index => $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Check capacity before each insert
            if ($imported >= $remaining) {
                $skippedCapacity = count($csv) - $index;
                break;
            }
            
            // Combine header with row values
            $data = array_combine($header, $row);
            
            // Validate required fields
            if (empty($data['nama'] ?? $data['name'])) {
                $errors[] = "Baris " . ($index + 2) . ": Nama siswa wajib diisi";
                continue;
            }
            
            $name = $data['nama'] ?? $data['name'];
            $nis = $data['nis'] ?? null;
            $email = $data['email'] ?? null;
            $phone = $data['telepon'] ?? $data['phone'] ?? $data['hp'] ?? null;
            
            // Generate username from NIS or name
            $username = $nis ?? strtolower(str_replace(' ', '', $name));
            
            // Check if student already exists
            $existingStudent = \App\Models\Student::where('classroom_id', $classroom->id)
                ->where(function($q) use ($username, $nis) {
                    $q->where('username', $username);
                    if ($nis) {
                        $q->orWhere('nis', $nis);
                    }
                })
                ->first();
            
            if ($existingStudent) {
                $errors[] = "Baris " . ($index + 2) . ": Siswa dengan username/NIS '{$username}' sudah ada";
                continue;
            }
            
            try {
                \App\Models\Student::create([
                    'serial_id' => $serial->id,
                    'user_id' => auth()->id(),
                    'classroom_id' => $classroom->id,
                    'name' => $name,
                    'username' => $username,
                    'password' => bcrypt($defaultPassword),
                    'nis' => $nis,
                    'email' => $email,
                    'phone' => $phone,
                ]);
                
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        $message = "{$imported} siswa berhasil diimpor.";
        if ($skippedCapacity > 0) {
            $message .= " {$skippedCapacity} siswa tidak diimpor karena kelas sudah mencapai kapasitas maksimum " . $maxStudents . " siswa.";
        }
        if (!empty($errors)) {
            $message .= " Namun ada " . count($errors) . " baris yang gagal diimpor.";
        }
        
        return redirect()->route('guru.kelas.dashboard', [$serial->id, $classroom->id])
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
    
    // Download CSV template
    public function downloadTemplate()
    {
        $csv = "nama,nis,email,telepon\n";
        $csv .= "Contoh Siswa 1,12345,siswa1@email.com,081234567890\n";
        $csv .= "Contoh Siswa 2,12346,siswa2@email.com,081234567891\n";
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template-import-siswa.csv"');
    }
}