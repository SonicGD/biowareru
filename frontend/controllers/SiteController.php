<?php

namespace biowareru\frontend\controllers;


use bioengine\common\BioEngine;
use bioengine\common\modules\main\models\Menu;
use bioengine\common\modules\news\models\News;
use biowareru\frontend\helpers\SliderHelper;
use Google_Client;
use Google_Service_YouTube;
use IPBWI\ipbwi_member;
use yii\data\Pagination;
use yii\validators\UrlValidator;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SiteController extends \bioengine\frontend\controllers\SiteController
{
    public $breadCrumbs = [];

    public function actionError()
    {
        return $this->render('@app/static/tmpl/p-404.twig');
    }

    public function actionIndex()
    {
        $newsQuery = News::find()->orderBy([
            'sticky' => SORT_DESC,
            'id'     => SORT_DESC
        ])->where(['pub' => 1]);

        $this->pageTitle = 'www.BioWare.ru - Новости';
        return $this->renderNews($newsQuery);
    }

    public function actionRoot($parentUrl)
    {
        $parent = BioEngine::getParentByUrl($parentUrl);
        if (!$parent) {
            throw new NotFoundHttpException;
        }

        $this->breadCrumbs[] = [
            'title' => $parent->title,
            'url'   => $parent->getPublicUrl()
        ];

        $this->breadCrumbs[] = [
            'title' => 'Новости',
            'url'   => $parent->getNewsUrl()
        ];

        $newsQuery = News::find()->orderBy([
            'sticky' => SORT_DESC,
            'id'     => SORT_DESC
        ])->where(['pub' => 1, $parent->parentKey => $parent->id]);

        return $this->renderNews($newsQuery);
    }

    public function actionRemenu()
    {
        /**
         * @var Menu $menu
         */
        $menu = Menu::find()->one();
        $items = $menu->getItems();
        $this->processMenu($items);
        $menu->code = json_encode($items);
        $menu->save();
    }

    private function processMenu(&$menu)
    {
        $urlValidator = new UrlValidator();
        foreach ($menu as &$item) {
            if ($item['url'] === '' || $item['url'] === '#') {
                $item['url'] = '#';
            } elseif ($urlValidator->validate($item['url'])) {
                $item['url'] = str_ireplace('http://www.bioware.ru/', '/', $item['url']);
                if (stripos($item['url'], '.html') === false) {
                    if (strripos($item['url'], '/') === strlen($item['url']) - 1) {
                        $item['url'] = substr($item['url'], 0, strlen($item['url']) - 1);
                    }
                    $item['url'] .= '.html';
                }
            } else {
                if (stripos($item['url'], '.html') === false) {
                    if (strripos($item['url'], '/') === strlen($item['url']) - 1) {
                        $item['url'] = substr($item['url'], 0, strlen($item['url']) - 1);
                    }
                    $item['url'] .= '.html';
                }
            }
            if (count($item['items'])) {
                $this->processMenu($item['items']);
            }
        }
        unset($item);
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

    public function actionJsonGames()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return SliderHelper::getGames();
    }

    /**
     * @param $newsQuery
     * @return string
     */
    private function renderNews($newsQuery)
    {
        $pagination = new Pagination(['totalCount' => $newsQuery->count(), 'pageSize' => 20]);
        //$pagination->route = 'site/index';
        $news = $newsQuery->offset($pagination->offset)
            ->limit($pagination->limit)->all();

        return $this->render('@app/static/tmpl/p-index.twig', ['news' => $news, 'pagination' => $pagination]);
    }

    public function actionLogin()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;
        if (\Yii::$app->user->isGuest) {
            $login = \Yii::$app->request->post('login');
            $password = \Yii::$app->request->post('password');

            /**
             * @var ipbwi_member $member
             */
            $member = $this->ipbwi->member;

            $result = $member->login($login, $password);
            if ($result) {
                return ['result' => true];
            } else {
                return ['result' => false, 'error' => 'Неверное имя пользователя или пароль'];
            }
        } else {
            return ['result' => true];
        }
    }

    public function actionLogout()
    {
        if (!\Yii::$app->user->isGuest) {
            /**
             * @var ipbwi_member $member
             */
            $member = $this->ipbwi->member;
            if ($member->logout()) {
                \Yii::$app->user->logout();
            }
        }
        return $this->redirect('/');
    }

    public function actionOauth()
    {
        $OAUTH2_CLIENT_ID = '580173741933-a4qbnr6c6ic7k05050vqreobh0fp52t3.apps.googleusercontent.com';
        $OAUTH2_CLIENT_SECRET = 'ROnux265Uk7vBIidKxQMI43e';
        //$refreshToken = '1\/V-9z8MapSHmYsl2kNBn_1uGlbrYtGeNlpsV_YNDbHyJIgOrJDtdun6zK6XiATCKT';
        //$accessToken = 'ya29.kgFVYW70_3a4UU1Vh-BLQPKxlyktKUy4Q21XxzCPZgwjEqgxEWcB0Ey-OXB1itfCfcy9Gz_znhwRzw';

        $token = '{"access_token":"ya29.kgFVYW70_3a4UU1Vh-BLQPKxlyktKUy4Q21XxzCPZgwjEqgxEWcB0Ey-OXB1itfCfcy9Gz_znhwRzw","token_type":"Bearer","expires_in":3600,"refresh_token":"1\/V-9z8MapSHmYsl2kNBn_1uGlbrYtGeNlpsV_YNDbHyJIgOrJDtdun6zK6XiATCKT","created":1434275779}';

        $client = new Google_Client();
        $client->setClientId($OAUTH2_CLIENT_ID);
        $client->setClientSecret($OAUTH2_CLIENT_SECRET);
        $client->setScopes('https://www.googleapis.com/auth/youtube');
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $redirect = 'http://bw.localhost.ru/site/oauth.html';
        $client->setRedirectUri($redirect);
        $client->setAccessToken($token);
        // Define an object that will be used to make all API requests.
        $youtube = new Google_Service_YouTube($client);

        /*if (isset($_GET['code'])) {
            if (strval($_SESSION['state']) !== strval($_GET['state'])) {
                die('The session state did not match.');
            }

            $client->authenticate($_GET['code']);
            $_SESSION['token'] = $client->getAccessToken();
            header('Location: ' . $redirect);
        }

        if (isset($_SESSION['token'])) {
            $client->setAccessToken($_SESSION['token']);
        } else {
            $authUrl = $client->createAuthUrl();
            $this->redirect($authUrl);
        }*/

// Check to ensure that the access token was successfully acquired.
        if ($client->getAccessToken()) {
            // var_dump($client->getAccessToken());
            $channelsResponse = $youtube->channels->listChannels('contentDetails', [
                'mine' => 'true',
            ]);


            foreach ($channelsResponse['items'] as $channel) {
                // Extract the unique playlist ID that identifies the list of videos
                // uploaded to the channel, and then call the playlistItems.list method
                // to retrieve that list.
                $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

                $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', [
                    'playlistId' => $uploadsListId,
                    'maxResults' => 50
                ]);

                foreach ($playlistItemsResponse['items'] as $playlistItem) {
                    var_dump($playlistItem['snippet']['title'], $playlistItem['snippet']['resourceId']['videoId']);
                }
            }
        } else {
            var_dump('No');
        }
    }
}