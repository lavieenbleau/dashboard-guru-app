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

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50',
        ]);

        $classroom = new Classroom();
        $classroom->serial_id = $serial->id;
        $classroom->name = $data['name'];
        $classroom->grade = $data['grade'] ?? null;
        $classroom->save();

        return redirect()->route('guru.kelas.pilih', ['serial' => $serial->id])->with('success', 'Kelas berhasil ditambahkan.');
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
            ->orderBy('absen', 'asc')
            ->get();

        return view('guru.kelas.dashboard', compact('serial', 'classroom', 'students'));
    }
    
    // Store new student
    public function storeStudent(Request $request, $serial, $classroom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50',
            'absen' => 'nullable|string|max:10',
        ]);
        
        $serial = Serial::findOrFail($serial);
        $classroom = Classroom::findOrFail($classroom);
        
        \App\Models\Student::create([
            'serial_id' => $serial->id,
            'user_id' => auth()->id(),
            'classroom_id' => $classroom->id,
            'name' => $request->name,
            'nis' => $request->nis,
            'absen' => $request->absen,
            'username' => $request->nis ?? strtolower(str_replace(' ', '', $request->name)),
            'password' => bcrypt('12345678'),
            'password_text' => '12345678',
            'role' => 0,
        ]);
        
        return redirect()->route('guru.kelas.dashboard', [$serial->id, $classroom->id])
            ->with('success', 'Siswa berhasil ditambahkan!');
    }
    
    // Delete student
    public function destroyStudent($serial, $classroom, $student)
    {
        $student = \App\Models\Student::findOrFail($student);
        $student->delete();
        
        return redirect()->route('guru.kelas.dashboard', [$serial, $classroom])
            ->with('success', 'Siswa berhasil dihapus!');
    }
}