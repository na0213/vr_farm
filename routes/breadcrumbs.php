<?php
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
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
    $trail->parent('farm.show', $article->farm); // farm.showのルートに関連するfarmデータを渡します
    $trail->push($article->title, route('article.show', $article->id));
});
