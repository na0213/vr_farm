<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Farm;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * 公開ページを静的な HTML に書き出す(Cloudflare で配信する)。
 * 管理画面は書き出さない(Mac 上の php artisan serve だけで使う)。
 *
 * URL と書き出すファイルの対応: / → index.html、/farm/1 → farm/1.html。
 * Cloudflare は /farm/1 へのアクセスに farm/1.html を返す。
 */
class ExportStaticSite extends Command
{
    public const PRODUCTION_URL = 'https://www.farm360.jp';

    protected $signature = 'site:export
        {--url='.self::PRODUCTION_URL.' : 公開先のURL(canonical・OGP・サイトマップに使う)}
        {--out=dist : 書き出し先のフォルダ}';

    protected $description = '公開ページを静的な HTML に書き出す';

    /** 書き出しに含めない public/ のファイル(PHP の入口など) */
    private const PUBLIC_EXCLUDES = ['index.php', '.htaccess', 'hot', 'web.config'];

    public function handle(HttpKernel $kernel): int
    {
        if (File::exists(public_path('hot'))) {
            $this->error('npm run dev が動いています(public/hot がある)。止めてから npm run build してください。');

            return self::FAILURE;
        }

        if (rtrim((string) $this->option('url'), '/') === self::PRODUCTION_URL && ! config('services.turnstile.site_key')) {
            $this->error('Turnstile のサイトキーが未設定です(config/services.php)。このままではお問い合わせが送れません。');

            return self::FAILURE;
        }

        if (! File::exists(public_path('build/manifest.json'))) {
            $this->error('public/build がありません。先に npm run build してください。');

            return self::FAILURE;
        }

        $base = rtrim((string) $this->option('url'), '/');
        $out = $this->outputPath();

        // 書き出し先は丸ごと消して作り直すので、大事なフォルダを指していないか確かめる
        if (in_array(rtrim($out, '/'), ['', base_path(), public_path(), rtrim((string) getenv('HOME'), '/')], true)) {
            $this->error("書き出し先に {$out} は使えません。");

            return self::FAILURE;
        }

        URL::forceRootUrl($base);
        URL::forceScheme(parse_url($base, PHP_URL_SCHEME) ?: 'https');

        File::deleteDirectory($out);
        File::copyDirectory(public_path(), $out);
        foreach (self::PUBLIC_EXCLUDES as $file) {
            File::delete($out.'/'.$file);
        }

        $failed = [];

        foreach ($this->pagePaths() as $path) {
            $response = $this->render($kernel, $base.$path);

            if ($response->getStatusCode() !== 200) {
                $failed[] = "{$path} (HTTP {$response->getStatusCode()})";

                continue;
            }

            $this->write($out, $this->fileFor($path), $response->getContent());
        }

        // 存在しないURLで返すページ
        $notFound = $this->render($kernel, $base.'/__not-found__');
        $this->write($out, '404.html', $notFound->getContent());

        $this->write($out, '_redirects', implode("\n", [
            '/animal-welfare /kodawari 301', // 旧URL(被リンク・検索評価の引き継ぎ)
        ])."\n");

        $headers = [
            '/build/*',
            '  Cache-Control: public, max-age=31536000, immutable',
        ];
        if ($base !== self::PRODUCTION_URL) {
            // 確認用のURLが検索結果に出ないように
            array_push($headers, '/*', '  X-Robots-Tag: noindex');
        }
        $this->write($out, '_headers', implode("\n", $headers)."\n");

        if ($failed !== []) {
            foreach ($failed as $path) {
                $this->error("書き出せなかったページ: {$path}");
            }

            return self::FAILURE;
        }

        $this->info('書き出し完了: '.count($this->pagePaths()).' ページ → '.$out."({$base} 向け)");

        return self::SUCCESS;
    }

    /**
     * 書き出すページの URL(パス)。公開中の牧場・記事だけを含める。
     *
     * @return list<string>
     */
    public function pagePaths(): array
    {
        $paths = ['/', '/farm/map', '/products', '/kodawari', '/about', '/contact', '/sitemap.xml'];

        foreach (Farm::published()->orderBy('id')->pluck('id') as $id) {
            $paths[] = '/farm/'.$id;
        }

        foreach (Article::where('is_published', true)->orderBy('created_at')->pluck('id') as $id) {
            $paths[] = '/article/'.$id;
        }

        return $paths;
    }

    /**
     * URL のパスを、書き出すファイル名に変える。
     */
    public function fileFor(string $path): string
    {
        if ($path === '/') {
            return 'index.html';
        }

        $file = ltrim($path, '/');

        return pathinfo($file, PATHINFO_EXTENSION) === '' ? $file.'.html' : $file;
    }

    private function render(HttpKernel $kernel, string $url): Response
    {
        $request = Request::create($url, 'GET');
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        return $response;
    }

    private function write(string $out, string $file, string $content): void
    {
        File::ensureDirectoryExists(dirname($out.'/'.$file));
        File::put($out.'/'.$file, $content);
    }

    private function outputPath(): string
    {
        $out = (string) $this->option('out');

        return str_starts_with($out, '/') ? $out : base_path($out);
    }
}
