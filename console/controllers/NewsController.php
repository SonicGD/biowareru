<?php

namespace biowareru\console\controllers;


use bioengine\common\modules\ipb\models\IpbPost;
use bioengine\common\modules\news\models\News;
use yii\console\Controller;
use yii\httpclient\Client;
use yii\httpclient\Response;

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

    public function actionSyncForum($newsId)
    {
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
                    'hidden' => $news->pub ? 0 : 1
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
                    'hidden' => $news->pub ? 0 : 1
                ];

                $response = $this->doApiRequest("/forum/topics/" . $news->tid, $topic);
                if ($response->isOk) {
                    $post = [
                        'post' => $this->getPostContent($news)
                    ];
                    $postResponse = $this->doApiRequest("/forum/posts/" . $news->pid, $post);
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

    private function doApiRequest($path, $data):Response
    {
        $client = $this->getClient();
        $request = $client->createRequest()
            ->setMethod('post')
            ->setHeaders([
                'Authorization' => 'Basic MTM2ODg3NjAxODU3ZTYyZTAzZGM4NDQ4NTg4MzdiNmE6'
            ])
            ->setUrl('http://ipb4.bioware.ru/api' . $path)
            ->setData($data);
        $response = $request->send();
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

        return $postcontent;
    }
}