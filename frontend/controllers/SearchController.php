<?php

namespace biowareru\frontend\controllers;


use bioengine\common\modules\articles\models\search\ArticleCatSearch;
use bioengine\common\modules\articles\models\search\ArticleSearch;
use bioengine\common\modules\files\models\search\FileCatSearch;
use bioengine\common\modules\files\models\search\FileSearch;
use bioengine\common\modules\gallery\models\search\GalleryCatSearch;
use bioengine\common\modules\main\models\search\GameSearch;
use bioengine\common\modules\news\models\search\NewsSearch;
use bioengine\frontend\components\Controller;
use yii\helpers\Url;
use yii\web\Response;

class SearchController extends Controller
{
    public function actionIndex($q, $block = null)
    {
        $results = [
            'query'  => $q,
            'groups' => []
        ];

        if (!$block || $block === 'games') {
            $gameSearch = new GameSearch();
            $gameResults = $gameSearch->search(['GameSearch' => ['title' => $q]]);

            $this->fillSearchResults($results, 'games', 'Игры', $gameResults, 'title', 'publicUrl', 'news_desc',
                $block ? 0 : 5);
        }

        if (!$block || $block === 'news') {
            $newsSearch = new NewsSearch();
            $newsResults = $newsSearch->search(['NewsSearch' => ['title' => $q, 'short_text' => $q, 'add_text' => $q]]);

            $this->fillSearchResults($results, 'news', 'Новости', $newsResults, 'title', 'publicUrl', 'short_text',
                $block ? 0 : 5);
        }
        if (!$block || $block === 'articles') {
            $articlesSearch = new ArticleSearch();
            $articlesResults = $articlesSearch->search(['ArticleSearch' => ['title' => $q, 'text' => $q]]);

            $this->fillSearchResults($results, 'articles', 'Статьи', $articlesResults, 'title', 'publicUrl',
                'announce', $block ? 0 : 5);
        }

        if (!$block || $block === 'articlesCats') {
            $articlesCatSearch = new ArticleCatSearch();
            $articlesCatResults = $articlesCatSearch->search(['ArticleCatSearch' => ['title' => $q, 'content' => $q]]);

        }

        if (!$block || $block === 'files') {
            $filesSearch = new FileSearch();
            $filesResults = $filesSearch->search(['FileSearch' => ['title' => $q, 'desc' => $q, 'announce' => $q]]);

            $this->fillSearchResults($results, 'files', 'Файлы', $filesResults, 'title', 'publicUrl', 'announce',
                $block ? 0 : 5);
        }

        if (!$block || $block === 'filesCat') {
            $filesCatSearch = new FileCatSearch();
            $filesCatResults = $filesCatSearch->search(['FileCatSearch' => ['title' => $q, 'descr' => $q]]);
        }

        if (!$block || $block === 'galleryCats') {
            $galleryCatSearch = new GalleryCatSearch();
            $galleryCatResults = $galleryCatSearch->search(['GalleryCatSearch' => ['title' => $q, 'content' => $q]]);
        }

        /*\Yii::$app->response->format = Response::FORMAT_JSON;
        return $results;*/

        return $this->render('@app/static/tmpl/p-search.twig', ['searchResults' => $results]);
    }

    private function fillSearchResults(
        &$results,
        $key,
        $title,
        $provider,
        $titleField,
        $urlField,
        $textField,
        $limit = 5
    ) {
        if ($provider->count > 0) {
            $results['groups'][$key] = [
                'title' => $title,
                'items' => [],
                'url'   => Url::toRoute(['search/index', 'q' => $results['query'], 'block' => $key])
            ];
            $itemsCount = 0;
            foreach ($provider->models as $game) {
                $item = [
                    'title' => $game->$titleField,
                    'url'   => $game->$urlField,
                    'text'  => $game->$textField
                ];
                $results['groups'][$key]['items'][] = $item;
                $itemsCount++;
                if ($limit > 0 && $itemsCount === $limit) {
                    break;
                }
            }
        }
    }

}