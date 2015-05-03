<?php

namespace biowareru\frontend\controllers;


use bioengine\common\BioEngine;
use bioengine\common\modules\files\controllers\frontend\IndexController;
use bioengine\common\modules\files\models\File;
use bioengine\common\modules\files\models\FileCat;
use bioengine\common\modules\main\models\Developer;
use bioengine\common\modules\main\models\Game;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class FilesController extends IndexController
{
    public $breadCrumbs = [];

    public function actionRoot($parentUrl)
    {
        $parent = BioEngine::getParentByUrl($parentUrl);
        if (!$parent) {
            throw new NotFoundHttpException;
        }

        $cats = FileCat::find()->where([$parent->parentKey => $parent->id, 'pid' => 0])->all();
        $this->breadCrumbs[] = [
            'title' => $parent->title,
            'url'   => $parent->getPublicUrl()
        ];
        return $this->render('@app/static/tmpl/p-files-game.twig',
            ['parent' => $parent, 'cats' => $cats]);
    }

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
         * @var FileCat $cat
         */
        $cat = FileCat::find()->where(['url' => $url, $parent->parentKey => $parent->id])->one();
        if (!$cat) {
            throw new NotFoundHttpException;
        }
        $filesQuery = File::find()->orderBy([
            'id' => SORT_DESC
        ])->where(['cat_id' => $cat->id]);
        $pagination = new Pagination(['totalCount' => $filesQuery->count(), 'pageSize' => 24]);

        $files = $filesQuery->offset($pagination->offset)
            ->limit($pagination->limit)->all();


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
            'title' => 'Файлы',
            'url'   => $parent->getFilesUrl()
        ];

        array_reverse($this->breadCrumbs);

        return $this->render('@app/static/tmpl/p-files-cat.twig',
            ['parent' => $parent, 'cat' => $cat, 'files' => $files, 'pagination' => $pagination]);
    }

    public function actionShow($parentUrl, $catUrl, $fileUrl)
    {
        list($parent, $cat, $file) = $this->getFile($parentUrl, $catUrl, $fileUrl);
        if (!$file) {
            return $this->showCat($parentUrl, $catUrl . '/' . $fileUrl);
        }

        $this->fillFileBreadcrumbs($cat, $parent);

        return $this->render('@app/static/tmpl/p-files-file.twig',
            ['parent' => $parent, 'cat' => $cat, 'file' => $file]);
    }

    public function actionDownload($parentUrl, $catUrl, $fileUrl)
    {
        /**
         * @var File $file
         */
        list($parent, $cat, $file) = $this->getFile($parentUrl, $catUrl, $fileUrl);
        if (!$file) {
            throw new NotFoundHttpException;
        }
        $this->fillFileBreadcrumbs($cat, $parent);
        $file->count++;
        $file->save(false);

        \Yii::$app->response->headers['Refresh'] = '5; url=' . $file->link;

        return $this->render('@app/static/tmpl/p-files-file-download.twig',
            ['parent' => $parent, 'cat' => $cat, 'file' => $file]);
    }

    /**
     * @param $parentUrl
     * @param $catUrl
     * @param $fileUrl
     * @return array
     * @throws NotFoundHttpException
     */
    private function getFile($parentUrl, $catUrl, $fileUrl)
    {
        $parent = BioEngine::getParentByUrl($parentUrl);
        if (!$parent) {

            throw new NotFoundHttpException;
        }

        $catUrlParts = explode('/', $catUrl);
        $url = end($catUrlParts);
        /**
         * @var FileCat $cat
         */
        $cat = FileCat::find()->where(['url' => $url, $parent->parentKey => $parent->id])->one();

        if (!$cat) {

            throw new NotFoundHttpException;
        }

        $file = File::find()->where(['cat_id' => $cat->id, 'url' => $fileUrl])->one();

        return [$parent, $cat, $file];
    }

    /**
     * @param FileCat        $cat
     * @param Game|Developer $parent
     */
    private function fillFileBreadcrumbs($cat, $parent)
    {
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
            'title' => 'Файлы',
            'url'   => $parent->getFilesUrl()
        ];
        $this->breadCrumbs[] = [
            'title' => $parent->title,
            'url'   => $parent->getPublicUrl()
        ];
        $this->breadCrumbs = array_reverse($this->breadCrumbs);
    }
}