<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    public function uploadPhoto(UploadedFile $file, string $folder = 'photos'): string
    {
        $filename = uniqid() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs($folder, $filename, 'public');
        return $path;
    }

    public function deletePhoto(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    public function getBase64Photo(string $path): ?string
    {
        if (Storage::disk('public')->exists($path)) {
            $data = Storage::disk('public')->get($path);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }
}

