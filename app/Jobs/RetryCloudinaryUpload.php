<?php

namespace App\Jobs;

use App\Services\Interfaces\ImageServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\UploadedFile;

class RetryCloudinaryUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file;
    protected $clientId;

    public function __construct(UploadedFile $file, $clientId)
    {
        $this->file = $file;
        $this->clientId = $clientId;
    }

    public function handle(ImageServiceInterface $imageService)
    {
        try {
            $photoUrl = $imageService->uploadToCloudinary($this->file);

            $client = \App\Models\Client::find($this->clientId);
            if ($client) {
                $client->photo_url = $photoUrl;
                $client->save();
            }
        } catch (\Exception $e) {
            // Gérer l'échec de l'upload ici si nécessaire
        }
    }
}
