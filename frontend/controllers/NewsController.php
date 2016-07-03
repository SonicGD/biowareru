<?php

namespace biowareru\frontend\controllers;


use bioengine\common\helpers\UserHelper;
use bioengine\common\modules\ipb\models\IpbPost;
use bioengine\common\modules\news\controllers\frontend\IndexController;
use bioengine\common\modules\news\models\News;
use biowareru\frontend\helpers\ContentHelper;
use yii\httpclient\Client;
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
        $newsQuery = News::find();

        $user = UserHelper::getUser();
        if ($user) {
            if ($user->isSiteTeam()) {
                if (!$user->hasRights('pubNews')) {
                    $newsQuery->where(['pub' => 1]);
                    $newsQuery->orWhere(['author_id' => $user->member_id]);
                }
            } elseif (!$user->isAdmin()) {
                $newsQuery->where(['pub' => 1]);
            }
        } else {
            $newsQuery->where(['pub' => 1]);
        }

        $newsQuery->andWhere(['url' => $newsUrl])
            ->andWhere(['>=', 'date', $dateStart])
            ->andWhere(['<=', 'date', $dateEnd]);

        $news = $newsQuery->one();

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
        if (!isset($this->settings['ipbApiKey'])) {
            return false;
        }
        /**
         * @var News $news
         */
        $news = News::findOne($newsId);
        if ($news) {

            if (!$news->tid) {
                //create new topic

                $topic = [
                    'forum'  => 4,
                    'author' => $news->author_id,
                    'title'  => $news->title,
                    'post'   => $this->getPostContent($news),
                    'hidden' => $news->pub ? 0 : 1,
                    'pinned' => $news->sticky
                ];

                $response = $this->doApiRequest("/forums/topics", $topic);
                if ($response->isOk) {
                    $news->tid = $response->data['id'];
                    $news->pid = $response->data['firstPost']['id'];
                }
            } else {
                //upd topic

                $topic = [
                    'title'  => $news->title,
                    'hidden' => $news->pub ? 0 : 1,
                    'pinned' => $news->sticky
                ];

                $response = $this->doApiRequest("/forums/topics/" . $news->tid, $topic);
                if ($response->isOk) {
                    $post = [
                        'post' => $this->getPostContent($news)
                    ];
                    $postResponse = $this->doApiRequest("/forums/posts/" . $news->pid, $post);
                    if (!$postResponse->isOk) {
                        //error
                    }
                } else {
                    //error
                }
            }

            if ($news->validate()) {
                $news->save(false);
            }
        }
    }

    private $client;

    private function getClient():Client
    {
        if (!$this->client) {
            $this->client = new Client();
        }
        return $this->client;
    }

    private function doApiRequest($path, $data):\yii\httpclient\Response
    {
        $url = \Yii::$app->params['ipb_url'] . 'api' . $path;
        var_dump($url);
        $client = $this->getClient();
        $request = $client->createRequest()
            ->setMethod('post')
            ->setHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->settings['ipbApiKey'] . ':')
            ])
            ->setUrl($url)
            ->setData($data);
        $response = $request->send();
        var_dump($response);
        return $response;
    }


    private function getPostContent(News $news):string
    {
        $postcontent = $news->short_text;

        if (mb_strlen(trim($news->add_text)) > 0) {

            $addText = <<<EOF
                    <br />
<div class="ipsSpoiler" data-ipsspoiler="">
	<div class="ipsSpoiler_header">
		<span>Скрытый текст</span>
	</div>

	<div class="ipsSpoiler_contents">
		{$news->add_text}
	</div>
</div>
EOF;
            $postcontent .= $addText;
        }


        $postcontent = str_ireplace("<ul>", "<ul class=\"bbc\">", $postcontent);
        $postcontent = str_ireplace("<ol>", "<ul class=\"bbcol decimal\">", $postcontent);
        $postcontent = str_ireplace("</ol>", "</ul>", $postcontent);
        $postcontent = preg_replace(
            '#\[video id\=(([0-9]+)?) uri\=(.*?)\](.*?)\[/video\]#i',
            "<div style=\"text-align:center\"><a href=\"" . $news->getPublicUrl() . "\">Посмотреть видео</a></div>",
            $postcontent
        );

        $postcontent = ContentHelper::replacePlaceholders($postcontent);

        return $postcontent;
    }

}
