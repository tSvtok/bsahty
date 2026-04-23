<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Handle file upload.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480', // 5MB max
            'type' => 'required|string|in:avatar,post,event',
        ]);

        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');
        $type = $request->type;
        
        $path = $file->store('uploads/' . $type, 'public');
        $url = Storage::disk('public')->url($path);

        return response()->json([
            'path' => $path,
            'url' => $url,
            'message' => 'File uploaded successfully'
        ]);
    }

    /**
     * Delete a file.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        if (Storage::disk('public')->exists($request->path)) {
            Storage::disk('public')->delete($request->path);
            return response()->json(['message' => 'File deleted']);
        }

        return response()->json(['message' => 'File not found'], 404);
    }
}
