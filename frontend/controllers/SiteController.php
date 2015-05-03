<?php

namespace biowareru\frontend\controllers;


use bioengine\common\modules\news\models\News;
use yii\data\Pagination;
use yii\web\Response;

class SiteController extends \bioengine\frontend\controllers\SiteController
{
    public function actionIndex()
    {
        $newsQuery = News::find()->orderBy([
            'sticky' => SORT_DESC,
            'id'     => SORT_DESC
        ])->where(['pub' => 1]);
        $pagination = new Pagination(['totalCount' => $newsQuery->count(), 'pageSize' => 20]);

        $news = $newsQuery->offset($pagination->offset)
            ->limit($pagination->limit)->all();

        return $this->render('@app/static/tmpl/p-index.twig', ['news' => $news, 'pagination' => $pagination]);
    }

    public function actionJson()
    {
        /**
         * @var News[] $news
         */
        $news = News::find()->orderBy([
            'sticky' => SORT_DESC,
            'id'     => SORT_DESC
        ])->where(['pub' => 1])->limit(20)->all();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = [
            'news' => [

            ]
        ];
        foreach ($news as $item) {
            $data['news'][] = [
                'publicUrl'        => $item->getPublicUrl(),
                'sticky'           => $item->sticky,
                'title'            => $item->title,
                'last_change_date' => $item->last_change_date,
                'short_text'       => $item->short_text,
                'hasMore'          => $item->getHasMore(),
                'comments'         => $item->comments,
                'forumUrl'         => $item->getForumUrl(),
                'parent'           => [
                    'title'   => $item->parent->title,
                    'icon'    => $item->parent->getIcon(),
                    'newsUrl' => $item->parent->getNewsUrl()
                ]
            ];
        }

        return $data;
    }
} 