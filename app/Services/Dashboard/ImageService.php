<?php

namespace App\Services\Dashboard;

use Intervention\Image\Facades\Image;

final class ImageService
{
    protected ?\Intervention\Image\Image $image;

    /**
     * @param mixed $contents
     * @param int $width
     * @return ImageService
     */
    public function resize(mixed $contents, int $width): ImageService
    {
        $image = Image::make($contents);
        $image->widen($width, function ($constraint) {
            $constraint->aspectRatio();
        });

        $this->image = $image;

        return $this;
    }

    public function save(string $relativePath, string $name): string
    {
        if (is_null($this->image)) {
            return false;
        }

        collect(explode('/', $relativePath))->reduce(function ($fullPath, $directory) {
            $fullPath .= $directory . '/';

            if (!is_dir($fullPath)) {
                mkdir($fullPath);
            }

            return $fullPath;
        }, public_path('storage/'));

        $path = 'storage/' . $relativePath . $name;

        $this->image->save(public_path($path));

        return $path;
    }
}
