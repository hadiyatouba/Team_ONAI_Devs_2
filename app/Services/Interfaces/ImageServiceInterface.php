<?php

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;

interface ImageServiceInterface
{
    public function uploadToCloudinary(UploadedFile $file): string;
    public function uploadToLocal(UploadedFile $file): string;
}
