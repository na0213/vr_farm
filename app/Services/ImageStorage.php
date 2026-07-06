<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageStorage
{
    /**
     * 画像を横幅上限までリサイズ・JPEG圧縮してS3へ保存し、公開URLを返す。
     * 上限より小さい画像は拡大しない。
     * VRパノラマ画像は画質が落ちるため、このメソッドを使わずそのまま保存すること。
     */
    public static function storeResized(UploadedFile $file, string $fileName, int $maxWidth = 1600, int $quality = 80): string
    {
        $manager = new ImageManager(new Driver());

        $image = $manager->read($file->getPathname());
        $image->scaleDown(width: $maxWidth);

        Storage::disk('s3')->put($fileName, (string) $image->toJpeg($quality), 'public');

        return Storage::disk('s3')->url($fileName);
    }
}
