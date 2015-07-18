<?php

namespace biowareru\console\controllers;


use bioengine\common\modules\ipb\models\IpbPost;
use bioengine\common\modules\news\models\News;
use yii\console\Controller;

class NewsController extends Controller
{

    public function actionRefresh($newsId)
    {
        /**
         * @var News $news
         */
        $news = News::findOne($newsId);
        if ($news) {
            $news->comments = IpbPost::find()->where(['topic_id' => $news->tid])->count();
            if ($news->validate()) {
                $news->save(false);
            }
        }
    }
}