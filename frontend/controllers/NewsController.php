<?php

namespace biowareru\frontend\controllers;


use bioengine\common\modules\ipb\models\IpbPost;
use bioengine\common\modules\news\controllers\frontend\IndexController;
use bioengine\common\modules\news\models\News;
use biowareru\frontend\helpers\ContentHelper;
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
        /**
         * @var News $news
         */
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

        $this->pageTitle = $news->title;

        $this->image = ContentHelper::getImage($news->short_text);
        $desc = ContentHelper::getDescription($news->short_text);
        if ($desc !== null) {
            $this->description = $desc;
        }

        return $this->render('@app/static/tmpl/p-news-page.twig', ['singleNews' => $news]);
    }

    public function actionRss()
    {
        /**
         * @var \Zend\Feed\Writer\Feed $feed
         */
        $feed = \Yii::$app->feed->writer();

        $feed->setTitle('www.BioWare.ru');
        $feed->setLink('https://www.bioware.ru');
        $feed->setFeedLink('https://www.bioware.ru/news/rss.xml', 'rss');
        $feed->setDescription(\Yii::t('app', 'Последние новости'));
        $feed->setGenerator('https://www.bioware.ru/news/rss.xml');
        $feed->setDateModified(time());
        $feed->setLastBuildDate(time());

        /**
         * @var News[] $latestNews
         */
        $latestNews = News::find()->orderBy([
            'sticky' => SORT_DESC,
            'id'     => SORT_DESC
        ])->where(['pub' => 1])->limit(20)->all();
        foreach ($latestNews as $news) {
            $entry = $feed->createEntry();
            $entry->setTitle(htmlspecialchars_decode($news->title));
            $entry->setLink($news->getPublicUrl(true));
            $entry->setDateModified((int)$news->last_change_date);
            $entry->setDateCreated((int)$news->date);
            $entry->setDescription(ContentHelper::getDescription($news->short_text));
            $entry->setContent(
                $news->short_text
            );
            $entry->setCommentCount($news->comments);
            $img = ContentHelper::getImage($news->short_text);
            $imgData = ContentHelper::getRemoteFileSizeAndMime($img);
            if ($imgData) {
                $entry->setEnclosure(
                    [
                        'uri'    => $img,
                        'type'   => $imgData['mime'],
                        'length' => $imgData['size']
                    ]
                );
            }
            $feed->addEntry($entry);
        }
        header('Content-type: text/xml');
        \Yii::$app->response->format = Response::FORMAT_RAW;
        $out = $feed->export('rss');

        return $out;
    }

    public function actionUpdateForumPost($newsId)
    {
        /**
         * @var News $news
         */
        $news = News::findOne($newsId);
        if (!$news) {
            return;
        }
        /**
         * @var IpbPost $post
         */
        $post = IpbPost::findOne($news->pid);
        if (!$post) {
            return;
        }

        $post->post = ContentHelper::replacePlaceholders($post->post);
        $post->save();

        \Yii::$app->db->createCommand()->delete('be_content_cache_posts',
            ['cache_content_id' => $post->pid])->execute();

        var_dump($post->errors);

        return $post->pid;
    }
}