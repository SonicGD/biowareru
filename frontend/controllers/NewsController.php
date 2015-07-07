<?php

namespace biowareru\frontend\controllers;


use bioengine\common\modules\news\controllers\frontend\IndexController;
use bioengine\common\modules\news\models\News;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class NewsController extends IndexController
{
    public $breadCrumbs = [];
    public $test = 'asd';

    public function actionShow($year, $month, $day, $newsUrl)
    {
        $dateStart = mktime(0, 0, 0, $month, $day, $year);
        $dateEnd = mktime(23, 59, 59, $month, $day, $year);
        $news = News::find()
            ->where(['url' => $newsUrl])
            ->andWhere(['>=', 'date', $dateStart])
            ->andWhere(['<=', 'date', $dateEnd])
            ->one();

        if (!$news) {
            throw new NotFoundHttpException();
        }
        $this->breadCrumbs[] = [
            'title' => 'Новости',
            'url'   => '/'
        ];
        $this->breadCrumbs[] = [
            'title' => $news->parent->title,
            'url'   => $news->parent->getNewsUrl()
        ];

        return $this->render('@app/static/tmpl/p-news-page.twig', ['singleNews' => $news]);
    }

    public function actionJson()
    {
        /**
         * @var News $item
         */
        $item = News::find()->one();
        $data['breadCrumbs'][] = [
            'title' => 'Новости',
            'url'   => '/'
        ];
        $data['breadCrumbs'][] = [
            'title' => $item->parent->title,
            'url'   => $item->parent->getNewsUrl()
        ];
        $data['singleNews'] = [
            'publicUrl'        => $item->getPublicUrl(),
            'sticky'           => $item->sticky,
            'title'            => $item->title,
            'last_change_date' => $item->last_change_date,
            'short_text'       => $item->short_text,
            'add_text'         => $item->add_text,
            'hasMore'          => $item->getHasMore(),
            'comments'         => $item->comments,
            'forumUrl'         => $item->getForumUrl(),
            'parent'           => [
                'title'   => $item->parent->title,
                'icon'    => $item->parent->getIcon(),
                'newsUrl' => $item->parent->getNewsUrl()
            ]
        ];
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return $data;
    }

    public function actionRss()
    {
        /**
         * @var \Zend\Feed\Writer\Feed $feed
         */
        $feed = \Yii::$app->feed->writer();

        $feed->setTitle('www.BioWare.ru');
        $feed->setLink('https://bioware.ru');
        $feed->setFeedLink('https://bioware.ru/news/rss.xml', 'rss');
        $feed->setDescription(\Yii::t('app', 'Последние новости'));
        $feed->setGenerator('https://bioware.ru/news/rss.xml');
        $feed->setDateModified(time());

        /**
         * @var News[] $latestNews
         */
        $latestNews = News::find()->orderBy([
            'sticky' => SORT_DESC,
            'id'     => SORT_DESC
        ])->where(['pub' => 1])->limit(20)->all();
        foreach ($latestNews as $news) {
            $entry = $feed->createEntry();
            $entry->setTitle($news->title);
            $entry->setLink($news->getPublicUrl(true));
            $entry->setDateModified((int)$news->last_change_date);
            $entry->setDateCreated((int)$news->date);
            $entry->setContent(
                $news->short_text
            );
            $entry->setEnclosure(
                [
                    'uri'    => $news->getParent()->getIcon(),
                    'type'   => 'image/png',
                    'length' => $news->getParent()->getIconSize()
                ]
            );
            $feed->addEntry($entry);
        }
        header('Content-type: text/xml');
        \Yii::$app->response->format = Response::FORMAT_RAW;
        $out = $feed->export('rss');

        return $out;
    }

    private function remoteFilesize($url)
    {
        static $regex = '/^Content-Length: *+\K\d++$/im';
        if (!$fp = @fopen($url, 'rb')) {
            return false;
        }
        if (
            isset($http_response_header) &&
            preg_match($regex, implode("\n", $http_response_header), $matches)
        ) {
            return (int)$matches[0];
        }
        return strlen(stream_get_contents($fp));
    }
}