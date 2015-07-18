<?php

namespace biowareru\console\controllers;


use bioengine\common\modules\ipb\models\IpbPost;
use bioengine\common\modules\news\models\News;
use yii\console\Controller;

class NewsController extends Controller
{

    public function actionRefresh($newsId, $new = true)
    {
        /**
         * @var News $news
         */
        $news = News::findOne($newsId);
        if ($news) {
            $news->comments = IpbPost::find()->where(['topic_id' => $news->tid, 'queued' => 0])->andWhere([
                'not',
                ['pid' => $news->pid]
            ])->count();
            if ($new) {
                $news->comments++;
            }
            if ($news->validate()) {
                $news->save(false);
            } else {
                var_dump($news->errors);
            }
        }
    }
}