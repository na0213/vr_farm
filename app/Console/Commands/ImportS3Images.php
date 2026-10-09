<?php

namespace App\Console\Commands;

use App\Services\ImageStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * DB に残っている S3 の画像URLを、手元(public/uploads)の画像に置き換える。
 * 画像は公開URLからダウンロードするので、AWS のキーは要らない。
 * 本文(HTML)や JSON(\/ でエスケープされたURL)の中のURLも置き換える。
 */
class ImportS3Images extends Command
{
    protected $signature = 'images:import-from-s3 {--dry-run : ダウンロードもDB更新もせず、件数だけ表示する}';

    protected $description = 'S3 の画像を public/uploads に取り込み、DB のURLを /uploads/... に置き換える';

    // 例: https://bucket.s3.ap-northeast-1.amazonaws.com/farms/a.jpg(JSON内では / が \/ になる)
    private const S3_URL = '~https?:(?:\\\\?/){2}[a-z0-9.-]+\.amazonaws\.com((?:\\\\?/[A-Za-z0-9._%-]+)+)~i';

    private const TEXT_TYPES = ['char', 'varchar', 'text', 'tinytext', 'mediumtext', 'longtext', 'json'];

    /** @var array<string, bool> ダウンロード済み(または既存)のキー */
    private array $available = [];

    /** @var array<string, string> 失敗したURL => 理由 */
    private array $failed = [];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $changedRows = 0;

        // 接続中のデータベースのテーブルだけ(同じ MySQL の別データベースは触らない)
        foreach (Schema::getTables(Schema::getCurrentSchemaName()) as $table) {
            $tableName = $table['name'];
            $columns = collect(Schema::getColumns($tableName));

            if (! $columns->contains('name', 'id')) {
                continue; // 中間テーブルなど(画像URLは入っていない)
            }

            $textColumns = $columns
                ->filter(fn ($c) => in_array(strtolower($c['type_name']), self::TEXT_TYPES, true))
                ->pluck('name')
                ->all();

            if ($textColumns === []) {
                continue;
            }

            foreach (DB::table($tableName)->select(array_merge(['id'], $textColumns))->get() as $row) {
                $updates = [];

                foreach ($textColumns as $column) {
                    $value = $row->{$column};

                    if (! is_string($value) || ! str_contains($value, 'amazonaws.com')) {
                        continue;
                    }

                    $replaced = preg_replace_callback(self::S3_URL, fn ($m) => $this->localize($m[0], $m[1], $dryRun), $value);

                    if ($replaced !== $value) {
                        $updates[$column] = $replaced;
                    }
                }

                if ($updates !== []) {
                    $changedRows++;
                    $this->line(sprintf('%s #%s: %s', $tableName, $row->id, implode(', ', array_keys($updates))));

                    if (! $dryRun) {
                        DB::table($tableName)->where('id', $row->id)->update($updates);
                    }
                }
            }
        }

        $this->newLine();
        $this->info(($dryRun ? '[dry-run] ' : '').'画像 '.count($this->available)." 件 / 更新する行 {$changedRows} 件");

        foreach ($this->failed as $url => $reason) {
            $this->error("取り込めなかった画像(URLはそのまま): {$url} ({$reason})");
        }

        return $this->failed === [] ? self::SUCCESS : self::FAILURE;
    }

    /**
     * S3 のURLを /uploads/... に変える。ダウンロードに失敗したら元のURLのまま返す。
     */
    private function localize(string $original, string $escapedPath, bool $dryRun): string
    {
        $escaped = str_contains($escapedPath, '\\/');
        $key = ltrim(str_replace('\\/', '/', $escapedPath), '/');
        $url = str_replace('\\/', '/', $original);

        if (str_contains($key, '..')) {
            $this->failed[$url] = '不正なパス';

            return $original;
        }

        if (! isset($this->available[$key]) && ! $dryRun) {
            $disk = Storage::disk(ImageStorage::DISK);

            if (! $disk->exists($key)) {
                $response = Http::timeout(60)->get($url);

                if (! $response->successful()) {
                    $this->failed[$url] = 'HTTP '.$response->status();

                    return $original;
                }

                $disk->put($key, $response->body());
            }
        }

        $this->available[$key] = true;

        $local = '/uploads/'.$key;

        return $escaped ? str_replace('/', '\\/', $local) : $local;
    }
}
