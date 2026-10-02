<?php

namespace App\Http\Controllers\Backend;

use App\Console\Commands\BackupSiteData;
use App\Console\Commands\ExportStaticSite;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Article;
use App\Models\Farm;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function index()
    {
        $admin = Admin::find(Auth::guard('admins')->id());

        return view('backend.dashboard', compact('admin'));
    }

    // サイトのしくみ(Mac・Cloudflare・ドメインの関係と、何をどこに保存しているか)
    public function system(ExportStaticSite $export)
    {
        $uploads = public_path('uploads');
        $exported = base_path('dist/index.html');
        $backups = File::glob(BackupSiteData::defaultDestination().'/db/*.sql');

        $stats = [
            'pages' => count($export->pagePaths()),
            'farms' => Farm::published()->count(),
            'articles' => Article::where('is_published', true)->count(),
            'images' => File::isDirectory($uploads) ? count(File::allFiles($uploads)) : 0,
        ];

        $lastExport = File::exists($exported) ? Carbon::createFromTimestamp(File::lastModified($exported)) : null;
        $lastBackup = $backups !== [] ? Carbon::createFromTimestamp(max(array_map(fn ($f) => File::lastModified($f), $backups))) : null;

        return view('backend.system', compact('stats', 'lastExport', 'lastBackup'));
    }
}
