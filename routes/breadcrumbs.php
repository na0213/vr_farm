<?php
use Diglactic\Breadcrumbs\Breadcrumbs;
// Home
Breadcrumbs::for('home', function ($trail) {
    $trail->push('Home', route('index'));
});

// Farms index
Breadcrumbs::for('farm.index', function ($trail) {
    $trail->parent('home');
    $trail->push('牧場検索', route('farm.index'));
});

// Farm details
Breadcrumbs::for('farm.show', function ($trail, $farm) {
    $trail->parent('farm.index');
    $trail->push($farm->farm_name, route('farm.show', $farm->id));
});

// Article details
Breadcrumbs::for('article.show', function ($trail, $article) {
    if ($article->farm) {
        $trail->parent('farm.show', $article->farm); // 牧場に紐づく記事は牧場ページを親にする
    } else {
        $trail->parent('home'); // コラム記事(牧場に紐づかない)はホームを親にする
    }
    $trail->push($article->title, route('article.show', $article->id));
});
