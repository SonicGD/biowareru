<?php

namespace biowareru\frontend\controllers;


use bioengine\common\BioEngine;
use bioengine\common\modules\main\models\Menu;
use bioengine\common\modules\news\models\News;
use biowareru\frontend\helpers\SliderHelper;
use yii\data\Pagination;
use yii\validators\UrlValidator;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SiteController extends \bioengine\frontend\controllers\SiteController
{
    public $breadCrumbs = [];

    public function actionIndex()
    {
        $newsQuery = News::find()->orderBy([
            'sticky' => SORT_DESC,
            'id'     => SORT_DESC
        ])->where(['pub' => 1]);

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

    public function actionLogin($login, $password)
    {
        $this->enableCsrfValidation = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['result' => false, 'error' => 'Неверное имя пользователя или пароль'];
    }
} 