<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PengaturanController extends Controller
{
    public function index($serial)
    {
        $serial = \App\Models\Serial::findOrFail($serial);
        $user = Auth::user();
        
        return view('guru.pengaturan.index', compact('serial', 'user'));
    }
    
    public function updateProfile(Request $request, $serial)
    {
        \Illuminate\Support\Facades\Log::info('Update Profile Request', $request->all());
        \Illuminate\Support\Facades\Log::info('Has File?', ['avatar' => $request->hasFile('avatar')]);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . Auth::id(),
            'email' => 'nullable|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $user = Auth::user();
        
        if ($request->input('delete_avatar') === '1') {
            if ($user->img && Storage::disk('public')->exists('avatars/' . $user->img)) {
                Storage::disk('public')->delete('avatars/' . $user->img);
            }
            $user->img = null;
            $user->save();
            
            return redirect()->route('guru.pengaturan', $serial)
                ->with('success', 'Foto profil berhasil dihapus!');
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('username')) {
            $user->username = $request->username;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->img && Storage::disk('public')->exists('avatars/' . $user->img)) {
                Storage::disk('public')->delete('avatars/' . $user->img);
            }
            
            $avatarName = time() . '_' . $user->id . '.' . $request->avatar->extension();
            $request->avatar->storeAs('avatars', $avatarName, 'public');
            $user->img = $avatarName;
        }
        
        $user->save();
        
        return redirect()->route('guru.pengaturan', $serial)
            ->with('success', 'Profile berhasil diperbarui!');
    }
    
    public function updatePassword(Request $request, $serial)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();
        
        return redirect()->route('guru.pengaturan', $serial)
            ->with('success', 'Password berhasil diperbarui!');
    }
    
    public function updateField(Request $request, $serial)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        
        // Validation rules based on field
        $rules = [];
        switch ($field) {
            case 'name':
                $rules = ['value' => 'required|string|max:255'];
                break;
            case 'username':
                $rules = ['value' => 'required|string|max:255|unique:users,username,' . Auth::id()];
                break;
            case 'email':
                $rules = ['value' => 'nullable|email|max:255|unique:users,email,' . Auth::id()];
                break;
            case 'phone':
                $rules = ['value' => 'nullable|string|max:20'];
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Field tidak valid'
                ], 400);
        }
        
        try {
            $request->validate($rules);
            
            $user = Auth::user();
            $user->$field = $value;
            $user->save();
            
            $fieldLabels = [
                'name' => 'Nama Lengkap',
                'username' => 'Username',
                'email' => 'Email',
                'phone' => 'Nomor Telepon'
            ];
            
            return response()->json([
                'success' => true,
                'message' => $fieldLabels[$field] . ' berhasil diperbarui!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['value'][0] ?? 'Validasi gagal'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
