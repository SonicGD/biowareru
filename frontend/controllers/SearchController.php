<?php

namespace biowareru\frontend\controllers;


use bioengine\common\modules\articles\models\Article;
use bioengine\common\modules\articles\models\ArticleCat;
use bioengine\common\modules\files\models\File;
use bioengine\common\modules\files\models\FileCat;
use bioengine\common\modules\gallery\models\GalleryCat;
use bioengine\common\modules\main\models\Game;
use bioengine\common\modules\news\models\News;
use bioengine\frontend\components\Controller;
use biowareru\frontend\search\models\ArticlesCatSearch;
use biowareru\frontend\search\models\ArticlesSearch;
use biowareru\frontend\search\models\FilesCatSearch;
use biowareru\frontend\search\models\FilesSearch;
use biowareru\frontend\search\models\GalleryCatSearch;
use biowareru\frontend\search\models\GamesSearch;
use biowareru\frontend\search\models\NewsSearch;
use yii\data\Pagination;
use yii\db\Expression;
use yii\helpers\Url;
use yii\sphinx\ActiveRecord;

class SearchController extends Controller
{
    public $pagination;

    /**
     * @param ActiveRecord         $searchModel
     * @param \yii\db\ActiveRecord $arModel
     * @param string               $q
     * @return mixed
     */
    private function getModels($searchModel, $arModel, $q, &$count, $limit)
    {
        $arIds = array_keys(
            $searchModel::find()
                ->select('id')
                ->match($q)
                ->indexBy('id')
                ->limit($arModel::find()->count())
                ->asArray()
                ->orderBy(new Expression('WEIGHT() DESC,ID DESC'))
                ->all()
        );
        $count = count($arIds);
        if ($limit > 0) {
            $arIds = array_slice($arIds, 0, $limit);
        } else {
            $arIds = array_slice($arIds, 0, 20);
            $this->pagination = new Pagination(['totalCount' => $count, 'pageSize' => 20]);
        }
        $query = $arModel::find()
            ->where(['id' => $arIds])
            /*->orderBy(['id' => SORT_DESC])*/
            ->indexBy('id');
        $sortedModels = [];
        $models = $query->all();
        foreach ($arIds as $id) {
            $sortedModels[] = $models[$id];
        }
        return $sortedModels;
    }

    public function actionIndex($q, $block = null)
    {
        $results = [
            'query'  => $q,
            'groups' => []
        ];

        if (!$block || $block === 'games') {
            $count = 0;
            $limit = $block ? 0 : 5;
            $games = $this->getModels(GamesSearch::class, Game::class, $q, $count, $limit);
            $this->fillSearchResults($results, 'games', 'Игры', $games, 'title', 'publicUrl', 'news_desc',
                $count);
        }

        if (!$block || $block === 'news') {
            $count = 0;
            $limit = $block ? 0 : 5;
            $news = $this->getModels(NewsSearch::class, News::class, $q, $count, $limit);
            $this->fillSearchResults($results, 'news', 'Новости', $news, 'title', 'publicUrl', 'short_text',
                $count);
        }
        if (!$block || $block === 'articles') {
            $count = 0;
            $limit = $block ? 0 : 5;
            $articles = $this->getModels(ArticlesSearch::class, Article::class, $q, $count, $limit);
            $this->fillSearchResults($results, 'articles', 'Статьи', $articles, 'title', 'publicUrl',
                'announce', $count);
        }

        if (!$block || $block === 'articlesCats') {
            $count = 0;
            $limit = $block ? 0 : 5;
            $articlesCats = $this->getModels(ArticlesCatSearch::class, ArticleCat::class, $q, $count, $limit);
            $this->fillSearchResults($results, 'articlesCats', 'Категории статей', $articlesCats, 'title', 'publicUrl',
                'descr', $count);
        }

        if (!$block || $block === 'files') {
            $count = 0;
            $limit = $block ? 0 : 5;
            $files = $this->getModels(FilesSearch::class, File::class, $q, $count, $limit);
            $this->fillSearchResults($results, 'files', 'Файлы', $files, 'title', 'publicUrl',
                'announce',
                $count);
        }

        if (!$block || $block === 'filesCat') {
            $count = 0;
            $limit = $block ? 0 : 5;
            $filesCats = $this->getModels(FilesCatSearch::class, FileCat::class, $q, $count, $limit);
            $this->fillSearchResults($results, 'articlesCats', 'Категории файлов', $filesCats, 'title', 'publicUrl',
                'announce', $count);
        }

        if (!$block || $block === 'galleryCats') {
            $count = 0;
            $limit = $block ? 0 : 5;
            $galleryCats = $this->getModels(GalleryCatSearch::class,
                GalleryCat::class, $q, $count, $limit);
            $this->fillSearchResults($results, 'articlesCats', 'Категории картинок', $galleryCats, 'title', 'publicUrl',
                'desc', $count);
        }

//        var_dump(count($results['groups']['news']));
        /* \Yii::$app->response->format = Response::FORMAT_JSON;
         return $results;*/
        if ($block) {
            $results['url'] = Url::toRoute(['search/index', 'q' => $results['query']]);
            return $this->render('@app/static/tmpl/p-search-cat.twig',
                ['searchResultsCat' => $results, 'pagination' => $this->pagination]);
        } else {
            return $this->render('@app/static/tmpl/p-search.twig', ['searchResults' => $results]);
        }
    }

    private function fillSearchResults(
        &$results,
        $key,
        $title,
        $models,
        $titleField,
        $urlField,
        $textField,
        $count
    ) {
        $result = [
            'title' => $title,
            'items' => [],
            'count' => $count,
            'url'   => Url::toRoute(['search/index', 'q' => $results['query'], 'block' => $key])
        ];
        foreach ($models as $game) {
            $item = [
                'title' => $game->$titleField,
                'url'   => $game->$urlField,
                'text'  => $game->$textField
            ];
            $result['items'][] = $item;
        }
        $results['groups'][] = $result;
    }

}