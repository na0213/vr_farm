<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * php artisan serve(手元の開発サーバー)を、アップロードの上限を上げて起動する。
 * 撮影一覧の動画は iPhone の動画をそのまま受け取り、最初の15秒だけ使うので、ふつうの上限(20MB)では足りない。
 * 上限はフレームワークの serve と同じ動きのまま、PHP の -d で渡す(ProductController の 200MB と合わせる)。
 * ターミナルから起動しても、アプリのプレビューから起動しても効く。
 */
#[AsCommand(name: 'serve')]
class ServeCommand extends BaseServeCommand
{
    public const UPLOAD_LIMITS = [
        'upload_max_filesize' => '200M',
        'post_max_size' => '210M',
    ];

    protected function serverCommand()
    {
        $command = parent::serverCommand();
        $options = [];
        foreach (self::UPLOAD_LIMITS as $key => $value) {
            array_push($options, '-d', "{$key}={$value}");
        }

        // [php, -S, host:port, server.php] の php のすぐ後ろに入れる
        array_splice($command, 1, 0, $options);

        return $command;
    }
}
