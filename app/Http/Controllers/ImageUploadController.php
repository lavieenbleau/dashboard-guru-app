<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $hasUpload = $request->hasFile('upload');
        $fileKey = $hasUpload ? 'upload' : 'image';

        $validator = Validator::make($request->all(), [
            $fileKey => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'folder' => 'required|in:soal,materi,tugas'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if ($request->hasFile($fileKey)) {
            $file = $request->file($fileKey);
            $folder = $request->folder;
            
            // Unique filename
            $filename = time() . '_' . Str::random(20) . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $path = Storage::disk('public')->putFileAs($folder, $file, $filename);
            
            // For CKEditor it expects slightly different JSON format if it's the default uploader
            // Let's provide a format that satisfies both our custom AJAX and CKEditor's default upload adapter
            return response()->json([
                'success' => true,
                'url' => url(Storage::url($path)),
                'urls' => [
                    'default' => url(Storage::url($path)) // CKEditor 5 uses 'urls.default' or 'url'
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File tidak ditemukan'
        ], 400);
    }
}
