<?php

namespace App\Actions;

use App\Models\FileSource;
use Faker\Core\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class StoreFileAction
{
    private const BASE_DIRECTORY = 'file_sources';

    public function __invoke($file, string $type)
    {
        $path = self::BASE_DIRECTORY . '/' . now()->year . '/' . now()->month . '/' . now()->day . '/' . $type;
        $pathToFile = 'storage/' . Storage::disk('public')->putFile($path, $file);

        $fileSource = FileSource::create([
            'id' => Str::uuid(),
            'path' => $pathToFile,
            'type' => $type
        ]);

        return $fileSource->id;
    }
}
