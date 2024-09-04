<?php
namespace App\Services;

use Exception;
use Cloudinary\CloudinaryBuilder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\Interfaces\ImageServiceInterface;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageService implements ImageServiceInterface
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = CloudinaryBuilder::build([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ]
        ]);
    }

    public function uploadImage($file)
    {
        try {
            // Tente de télécharger l'image sur Cloudinary
            $result = Cloudinary::upload($file->getRealPath())->getSecurePath();
            return $result;
        } catch (Exception $e) {
            // En cas d'échec, stocke l'image localement
            $path = $file->store('photos', 'public');
            return Storage::url($path);
        }
    }

    public function retryUploadToCloudinary($filePath)
    {
        try {
            // Tente de télécharger l'image depuis le stockage local vers Cloudinary
            $result = Cloudinary::upload(Storage::path($filePath))->getSecurePath();
            return $result;
        } catch (Exception $e) {
            // Vous pouvez enregistrer cette erreur dans les logs pour une tentative ultérieure
            throw $e;
        }
    }
}























