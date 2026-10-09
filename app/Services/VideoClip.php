<?php

namespace App\Services;

use App\Exceptions\VideoClipException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\ExecutableFinder;

/**
 * 撮影一覧の短い動画を、公開用に変換して保存する(ffmpeg を使う。管理画面は Mac だけで動かす前提)。
 * - 最初の MAX_SECONDS 秒だけ使い、音は消し、長い辺は MAX_EDGE px まで縮める(拡大はしない)
 * - H.264 の MP4(faststart)にする。撮影場所などのメタデータは残さない
 * - 最初の場面を JPEG で書き出し、一覧の写真(product_image)に使う
 * - iPhone の HDR 動画(HLG / PQ)は、そのままだと色が薄くなるので、Mac 標準の avconvert で先に通常の色(SDR)にする
 * 本番(Cloudflare)は1ファイル25MiBまでなので、変換後が MAX_BYTES を超えたら保存しない。
 */
class VideoClip
{
    public const MAX_SECONDS = 15;

    public const MAX_EDGE = 1280;

    public const MAX_BYTES = 20 * 1024 * 1024;

    private const HDR_TRANSFERS = ['arib-std-b67', 'smpte2084'];

    /**
     * @return array{video: string, poster: string} 動画と最初の場面の公開URL
     */
    public static function store(UploadedFile $file, string $baseName): array
    {
        $work = sys_get_temp_dir().'/farm360-video-'.uniqid();
        File::ensureDirectoryExists($work);

        try {
            // avconvert は拡張子で形式を判断するので、元の拡張子を付けた名前で渡す
            $source = "{$work}/source.".(strtolower($file->getClientOriginalExtension()) ?: 'mp4');
            symlink($file->getRealPath(), $source);

            if (in_array(self::colorTransfer($source), self::HDR_TRANSFERS, true)) {
                $source = self::toSdr($source, $work);
            }

            $video = "{$work}/clip.mp4";
            $edge = self::MAX_EDGE;
            self::run([
                self::binary('ffmpeg'), '-y', '-i', $source,
                '-map', '0:v:0', '-map_metadata', '-1', '-map_chapters', '-1',
                '-t', (string) self::MAX_SECONDS, '-an', '-fpsmax', '30',
                '-vf', "scale='min({$edge},iw)':'min({$edge},ih)':force_original_aspect_ratio=decrease:force_divisible_by=2,format=yuv420p",
                '-c:v', 'libx264', '-preset', 'medium', '-crf', '26', '-movflags', '+faststart',
                $video,
            ], '動画を変換できませんでした。iPhone などで撮った MP4 / MOV の動画を選んでください。');

            $bytes = filesize($video);
            if ($bytes > self::MAX_BYTES) {
                throw new VideoClipException(sprintf('変換後の動画が大きすぎます(%.1fMB)。動きの少ない、短い動画にしてください。', $bytes / 1024 / 1024));
            }

            $poster = "{$work}/poster.jpg";
            self::run([self::binary('ffmpeg'), '-y', '-i', $video, '-frames:v', '1', '-q:v', '3', $poster], '動画の最初の場面を取り出せませんでした。');

            $disk = Storage::disk(ImageStorage::DISK);
            $videoKey = "product_videos/{$baseName}.mp4";
            $posterKey = "product_images/{$baseName}.jpg";
            $disk->put($videoKey, File::get($video));
            $disk->put($posterKey, File::get($poster));

            return ['video' => ImageStorage::url($videoKey), 'poster' => ImageStorage::url($posterKey)];
        } finally {
            File::deleteDirectory($work);
        }
    }

    /**
     * 動画の色の伝達特性(HDR の判定に使う)。読めなければ空文字。
     */
    private static function colorTransfer(string $path): string
    {
        $result = Process::timeout(60)->run([
            self::binary('ffprobe'), '-v', 'error', '-select_streams', 'v:0',
            '-show_entries', 'stream=color_transfer', '-of', 'default=noprint_wrappers=1:nokey=1', $path,
        ]);

        return trim($result->output());
    }

    /**
     * HDR の動画を、Mac 標準の avconvert で通常の色(SDR)の H.264 にする。HEVC 以外のプリセットは SDR で書き出される。
     */
    private static function toSdr(string $source, string $work): string
    {
        $output = "{$work}/sdr.mov";
        self::run([
            self::binary('avconvert'), '--preset', 'Preset1920x1080',
            '--source', $source, '--output', $output,
            '--duration', (string) (self::MAX_SECONDS + 1), '--replace',
        ], 'HDR の動画を変換できませんでした。');

        return $output;
    }

    private static function run(array $command, string $message): void
    {
        $result = Process::timeout(300)->run($command);

        if ($result->failed()) {
            Log::error('動画の変換に失敗: '.basename($command[0]), ['error' => mb_substr($result->errorOutput(), -2000)]);
            throw new VideoClipException($message);
        }
    }

    /**
     * 開発サーバーの PATH に Homebrew が入っていないこともあるので、よくある場所も探す。
     */
    private static function binary(string $name): string
    {
        $path = (new ExecutableFinder)->find($name, null, ['/opt/homebrew/bin', '/usr/local/bin', '/usr/bin']);

        if ($path === null) {
            throw new VideoClipException("{$name} が見つかりません。Mac に ffmpeg を入れてください(brew install ffmpeg)。");
        }

        return $path;
    }
}
