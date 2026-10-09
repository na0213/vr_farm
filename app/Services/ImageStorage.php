<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * 管理画面からアップロードした画像の保存・削除。
 * 保存先は public/uploads(ディスク uploads)。静的サイトの書き出し(site:export)にそのまま含まれる。
 * DB には「/uploads/...」というサイト内のURLを保存する。
 */
class ImageStorage
{
    public const DISK = 'uploads';

    private const URL_PREFIX = '/uploads/';

    /**
     * 画像を横幅上限までリサイズ・JPEG圧縮して保存し、公開URLを返す。
     * 上限より小さい画像は拡大しない。
     * VRパノラマ画像は画質が落ちるため、このメソッドではなく storeOriginal() を使うこと。
     */
    public static function storeResized(UploadedFile $file, string $fileName, int $maxWidth = 1600, int $quality = 80): string
    {
        $manager = new ImageManager(new Driver);

        $image = $manager->read($file->getPathname());
        $image->scaleDown(width: $maxWidth);

        Storage::disk(self::DISK)->put($fileName, (string) $image->toJpeg($quality));

        return self::URL_PREFIX.$fileName;
    }

    /**
     * 画像をそのまま(リサイズせずに)保存し、公開URLを返す。VRパノラマ用。
     */
    public static function storeOriginal(UploadedFile $file, string $fileName): string
    {
        Storage::disk(self::DISK)->put($fileName, $file->getContent());

        return self::URL_PREFIX.$fileName;
    }

    /**
     * 公開URLから画像を削除する。このサイトに保存した画像でなければ何もしない。
     */
    public static function delete(?string $url): void
    {
        $key = self::keyFromUrl($url);

        if ($key !== null) {
            Storage::disk(self::DISK)->delete($key);
        }
    }

    /**
     * 公開URL(/uploads/...)を、ディスク上のパスに変換する。対象外なら null。
     */
    public static function keyFromUrl(?string $url): ?string
    {
        $path = parse_url((string) $url, PHP_URL_PATH);

        if (! is_string($path) || ! str_starts_with($path, self::URL_PREFIX)) {
            return null;
        }

        $key = substr($path, strlen(self::URL_PREFIX));

        return ($key === '' || str_contains($key, '..')) ? null : $key;
    }
}
