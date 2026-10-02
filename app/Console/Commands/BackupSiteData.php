<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

/**
 * サイトのデータ(手元の MySQL と public/uploads の画像)を iCloud Drive にバックアップする。
 * 静的サイトに移ってからは、記事や牧場の情報はこの Mac にしかないので、公開のたびに実行する。
 */
class BackupSiteData extends Command
{
    protected $signature = 'site:backup
        {--to= : バックアップ先(省略時は iCloud Drive の FARM360-backup)}
        {--keep=30 : 残すデータベースのバックアップの数}';

    protected $description = 'データベースとアップロード画像を iCloud Drive にバックアップする';

    /** 既定のバックアップ先(iCloud Drive の FARM360-backup) */
    public static function defaultDestination(): string
    {
        return getenv('HOME').'/Library/Mobile Documents/com~apple~CloudDocs/FARM360-backup';
    }

    public function handle(): int
    {
        $to = rtrim((string) ($this->option('to') ?: self::defaultDestination()), '/');
        $db = config('database.connections.'.config('database.default'));

        if (($db['driver'] ?? null) !== 'mysql') {
            $this->error('MySQL 以外のデータベースには対応していません。');

            return self::FAILURE;
        }

        File::ensureDirectoryExists("{$to}/db");

        // 1. データベース(パスワードは画面やコマンドラインに出さないよう、環境変数で渡す)
        $file = "{$to}/db/{$db['database']}-".now()->format('Ymd-His').'.sql';
        $dump = new Process(
            [$this->mysqldump(), '--single-transaction', '--no-tablespaces', '--routines', '--default-character-set=utf8mb4',
                '-h', (string) $db['host'], '-P', (string) $db['port'], '-u', (string) $db['username'], '--result-file='.$file, $db['database']],
            null,
            ['MYSQL_PWD' => (string) $db['password']],
        );
        $dump->setTimeout(300)->run();

        if (! $dump->isSuccessful() || ! File::exists($file) || File::size($file) === 0) {
            File::delete($file);
            $this->error('データベースのバックアップに失敗しました: '.trim($dump->getErrorOutput()));

            return self::FAILURE;
        }

        // 古いものを消す(新しい順に --keep 件だけ残す)
        $dumps = collect(File::glob("{$to}/db/{$db['database']}-*.sql"))->sort()->values();
        $dumps->slice(0, max(0, $dumps->count() - (int) $this->option('keep')))->each(fn ($old) => File::delete($old));

        // 2. 画像(消した画像もバックアップには残す)
        if (File::isDirectory(public_path('uploads'))) {
            $sync = new Process(['rsync', '-a', public_path('uploads').'/', "{$to}/uploads/"]);
            $sync->setTimeout(600)->run();

            if (! $sync->isSuccessful()) {
                $this->error('画像のバックアップに失敗しました: '.trim($sync->getErrorOutput()));

                return self::FAILURE;
            }
        }

        $this->info("バックアップしました: {$to}");

        return self::SUCCESS;
    }

    private function mysqldump(): string
    {
        // Homebrew の mysql@8.0 は PATH に入っていないことがある
        foreach (['/opt/homebrew/opt/mysql@8.0/bin/mysqldump', '/opt/homebrew/bin/mysqldump', '/usr/local/bin/mysqldump'] as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        return 'mysqldump';
    }
}
