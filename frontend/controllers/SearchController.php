<?php

namespace biowareru\frontend\controllers;


use bioengine\common\modules\articles\models\search\ArticleCatSearch;
use bioengine\common\modules\articles\models\search\ArticleSearch;
use bioengine\common\modules\files\models\search\FileSearch;
use bioengine\common\modules\main\models\Game;
use bioengine\common\modules\main\models\search\GameSearch;
use bioengine\common\modules\news\models\search\NewsSearch;
use bioengine\frontend\components\Controller;

class SearchController extends Controller
{
    public function actionIndex($q)
    {
        $gameSearch = new GameSearch();
        $gameResults = $gameSearch->search(['GameSearch' => ['title' => $q]]);

        $newsSearch = new NewsSearch();
        $newsResults = $newsSearch->search(['NewsSearch' => ['title' => $q, 'short_text' => $q, 'add_text' => $q]]);

        $articlesSearch = new ArticleSearch();
        $articlesResults = $articlesSearch->search(['ArticleSearch' => ['title' => $q, 'text' => $q]]);

        $articlesCatSearch = new ArticleCatSearch();
        $articlesCatResults = $articlesCatSearch->search(['ArticleCatSearch' => ['title' => $q, 'content' => $q]]);

        $filesSearch = new FileSearch();
        $filesResults = $filesSearch->search(['FileSearch' => ['title' => $q, 'desc' => $q, 'announce' => $q]]);

        $filesCatSearch = new FileCatSearch();
        $filesCatResults = $filesCatSearch->search(['FileCatSearch' => ['title' => $q, 'descr' => $q]]);

        $galleryCatSearch = new GalleryCatSearch();
        $galleryCatResults = $galleryCatSearch->search(['GalleryCatSearch' => ['title' => $q, 'content' => $q]]);
    }
}