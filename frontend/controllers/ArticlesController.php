<?php

namespace biowareru\frontend\controllers;


use bioengine\common\BioEngine;
use bioengine\common\modules\articles\controllers\frontend\IndexController;
use bioengine\common\modules\articles\models\Article;
use bioengine\common\modules\articles\models\ArticleCat;
use yii\web\NotFoundHttpException;

class ArticlesController extends IndexController
{
    public $breadCrumbs = [];

    public function actionShow($parentUrl, $catUrl, $articleUrl)
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

        $article = Article::find()->where(['cat_id' => $cat->id, 'url' => $articleUrl, 'pub' => 1])->one();
        if (!$article) {
            throw new NotFoundHttpException;
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
}