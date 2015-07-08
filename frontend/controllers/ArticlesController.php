<?php

namespace biowareru\frontend\controllers;


use bioengine\common\BioEngine;
use bioengine\common\modules\articles\controllers\frontend\IndexController;
use bioengine\common\modules\articles\models\Article;
use bioengine\common\modules\articles\models\ArticleCat;
use yii\data\Pagination;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class ArticlesController extends IndexController
{
    public $breadCrumbs = [];

    public function actionCat($parentUrl, $catUrl)
    {
        return $this->showCat($parentUrl, $catUrl);
    }

    private function showCat($parentUrl, $catUrl)
    {
        $parent = BioEngine::getParentByUrl($parentUrl);
        if (!$parent) {
            throw new NotFoundHttpException;
        }

        $catUrlParts = explode('/', $catUrl);
        $url = end($catUrlParts);
        /**
         * @var ArticleCat $cat
         */
        $cat = ArticleCat::find()->where(['url' => $url, $parent->parentKey => $parent->id])->one();
        if (!$cat) {
            throw new NotFoundHttpException;
        }
        $articlesQuery = Article::find()->orderBy([
            'id' => SORT_DESC
        ])->where(['cat_id' => $cat->id]);
        $pagination = new Pagination(['totalCount' => $articlesQuery->count(), 'pageSize' => 24]);

        $articles = $articlesQuery->offset($pagination->offset)
            ->limit($pagination->limit)->all();

        $children = $cat->getChildren()->all();
        $parentCat = $cat->parent;
        while ($parentCat) {
            $this->breadCrumbs[] = [
                'title' => $parentCat->title,
                'url'   => $parentCat->getPublicUrl()
            ];
            $parentCat = $parentCat->parent;
        }
        $this->breadCrumbs[] = [
            'title' => $parent->title,
            'url'   => $parent->getPublicUrl()
        ];
        $this->breadCrumbs[] = [
            'title' => 'Статьи',
            'url'   => $parent->getArticlesUrl()
        ];

        array_reverse($this->breadCrumbs);

        return $this->render('@app/static/tmpl/p-articles-cat.twig',
            [
                'parent'     => $parent,
                'cat'        => $cat,
                'children'   => $children,
                'articles'   => $articles,
                'pagination' => $pagination
            ]);
    }

    public function actionShow($parentUrl, $catUrl, $articleUrl)
    {
        list($article, $parent, $cat) = Article::getByParent($parentUrl, $catUrl, $articleUrl);
        if (!$article) {
            throw new HttpException(404, 'Страница не найдена');
        }

        $this->pageTitle = $article->title;
        if ($cat) {
            $this->pageTitle .= ' - ' . $cat->title;
        }
        if ($parent) {
            $this->pageTitle .= ' - ' . $parent->title;
        }

        $parentCat = $cat->parent;
        while ($parentCat) {
            $this->breadCrumbs[] = [
                'title' => $parentCat->title,
                'url'   => $parentCat->getPublicUrl()
            ];
            $parentCat = $parentCat->parent;
        }
        $this->breadCrumbs[] = [
            'title' => $cat->title,
            'url'   => $cat->getPublicUrl()
        ];

        $this->breadCrumbs[] = [
            'title' => 'Статьи',
            'url'   => $parent->getArticlesUrl()
        ];
        $this->breadCrumbs[] = [
            'title' => $parent->title,
            'url'   => $parent->getPublicUrl()
        ];
        $this->breadCrumbs = array_reverse($this->breadCrumbs);

        return $this->render('@app/static/tmpl/p-article.twig',
            ['parent' => $parent, 'cat' => $cat, 'article' => $article]);
    }

    public function actionRoot($parentUrl)
    {
        $parent = BioEngine::getParentByUrl($parentUrl);
        if (!$parent) {
            throw new NotFoundHttpException;
        }

        $cats = ArticleCat::find()->where([$parent->parentKey => $parent->id, 'pid' => 0])->all();
        $this->breadCrumbs[] = [
            'title' => $parent->title,
            'url'   => $parent->getPublicUrl()
        ];

        $this->pageTitle = $parent->title . ' - Статьи';

        return $this->render('@app/static/tmpl/p-articles-game.twig',
            ['parent' => $parent, 'cats' => $cats]);
    }
}