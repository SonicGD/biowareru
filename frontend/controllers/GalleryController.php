<?php

namespace biowareru\frontend\controllers;


use bioengine\common\BioEngine;
use bioengine\common\modules\gallery\models\GalleryCat;
use bioengine\common\modules\gallery\models\GalleryPic;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class GalleryController extends \bioengine\common\modules\gallery\controllers\frontend\GalleryController
{
    public $breadCrumbs = [];

    public function actionCat($parentUrl, $catUrl)
    {
        $parent = BioEngine::getParentByUrl($parentUrl);
        if (!$parent) {
            throw new NotFoundHttpException;
        }

        /**
         * @var GalleryCat $cat
         */
        $cat = GalleryCat::find()->where(['url' => $catUrl, $parent->parentKey => $parent->id])->one();
        if (!$cat) {
            throw new NotFoundHttpException;
        }
        $picsQuery = GalleryPic::find()->orderBy([
            'id' => SORT_DESC
        ])->where(['pub' => 1, 'cat_id' => $cat->id]);
        $pagination = new Pagination(['totalCount' => $picsQuery->count(), 'pageSize' => GalleryCat::PICS_ON_PAGE]);

        $pics = $picsQuery->offset($pagination->offset)
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
            'title' => 'Галерея',
            'url'   => $parent->getGalleryUrl()
        ];

        array_reverse($this->breadCrumbs);

        $this->pageTitle = $cat->title . ' - ' . $parent->title;

        return $this->render('@app/static/tmpl/p-gallery.twig',
            ['parent' => $parent, 'cat' => $cat, 'pics' => $pics, 'pagination' => $pagination]);
    }

    /**
     * @param $picId
     * @param $width
     * @param $height
     * @return $this
     * @throws NotFoundHttpException
     */
    public function actionThumb($picId, $width = 100, $height = 0)
    {
        /**
         * @var GalleryPic $pic
         */
        $pic = GalleryPic::findOne($picId);
        if ($pic && $pic->pub) {
            $thumbPath = $pic->getThumbPath($width, $height);
            if ($thumbPath) {
                return \Yii::$app->response->sendFile($thumbPath, $pic->getFileName(), ['inline' => true]);
            }
        }

        throw new NotFoundHttpException();
    }

    public function actionShow($picId)
    {
        /**
         * @var GalleryPic $pic
         */
        $pic = GalleryPic::findOne($picId);
        if ($pic && $pic->pub) {
            return $this->redirect($pic->getPublicUrl(true) . '#nanogallery/nanoGallery/0/' . $picId);
        }

        throw new NotFoundHttpException();
    }

    public function actionRoot($parentUrl)
    {
        $parent = BioEngine::getParentByUrl($parentUrl);
        if (!$parent) {
            throw new NotFoundHttpException;
        }

        $cats = GalleryCat::find()->where([$parent->parentKey => $parent->id, 'pid' => 0])->all();
        $this->breadCrumbs[] = [
            'title' => $parent->title,
            'url'   => $parent->getPublicUrl()
        ];

        $this->pageTitle = $parent->title . ' - Галерея';

        return $this->render('@app/static/tmpl/p-gallery-game.twig',
            ['parent' => $parent, 'cats' => $cats]);
    }

}