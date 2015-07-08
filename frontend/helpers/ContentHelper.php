<?php

namespace biowareru\frontend\helpers;


use bioengine\common\modules\articles\models\Article;
use bioengine\common\modules\files\models\File;
use bioengine\common\modules\gallery\models\GalleryPic;
use bioengine\common\modules\main\models\Developer;
use bioengine\common\modules\main\models\Game;
use bioengine\common\modules\news\models\News;
use yii\helpers\Html;

class ContentHelper
{
    public static $placeholders = [
        [
            'placeholder' => '\[game:([a-zA-Z0-9_]+)\]',
            'method'      => 'replaceGame',
            'onlyUrl'     => false
        ],
        [
            'placeholder' => '\[gameUrl:([a-zA-Z0-9_]+)\]',
            'method'      => 'replaceGame',
            'onlyUrl'     => true
        ],
        [
            'placeholder' => '\[developer:([a-zA-Z0-9_]+)\]',
            'method'      => 'replaceDeveloper',
            'onlyUrl'     => false
        ],
        [
            'placeholder' => '\[developerUrl:([a-zA-Z0-9_]+)\]',
            'method'      => 'replaceDeveloper',
            'onlyUrl'     => true
        ],
        [
            'placeholder' => '\[news:([0-9]+)\]',
            'method'      => 'replaceNews',
            'onlyUrl'     => false
        ],
        [
            'placeholder' => '\[newsUrl:([0-9]+)\]',
            'method'      => 'replaceNews',
            'onlyUrl'     => true
        ],
        [
            'placeholder' => '\[file:([0-9]+)\]',
            'method'      => 'replaceFile',
            'onlyUrl'     => false
        ],
        [
            'placeholder' => '\[fileUrl:([0-9]+)\]',
            'method'      => 'replaceFile',
            'onlyUrl'     => true
        ],
        [
            'placeholder' => '\[article:([0-9]+)\]',
            'method'      => 'replaceArticle',
            'onlyUrl'     => false
        ],
        [
            'placeholder' => '\[articleUrl:([0-9]+)\]',
            'method'      => 'replaceArticle',
            'onlyUrl'     => true
        ],
        [
            'placeholder' => '\[gallery:([0-9]+)\]',
            'method'      => 'replaceGallery',
            'onlyUrl'     => false
        ],
        [
            'placeholder' => '\[galleryUrl:([0-9]+)\]',
            'method'      => 'replaceGallery',
            'onlyUrl'     => true
        ],
        [
            'placeholder' => 'http:',
            'method'      => 'replaceHttp',
            'onlyUrl'     => true
        ]
    ];

    public static function replacePlaceholders($text)
    {
        foreach (self::$placeholders as $placeholder) {
            $matches = [];
            preg_match_all('/' . $placeholder['placeholder'] . '/', $text, $matches);
            if ($matches[0]) {
                $method = $placeholder['method'];
                foreach ($matches[0] as $key => $match) {
                    $value = $matches[1][$key];
                    $replacement = self::$method($value, $placeholder['onlyUrl']);
                    if ($replacement === false) {
                        $replacement = 'n/a';
                    }
                    $text = str_ireplace($match, $replacement, $text);
                }
            }
        }
        return $text;
    }

    private static function replaceGame($id, $onlyUrl = false)
    {
        /**
         * @var Game $game
         */
        $game = Game::findOne(['url' => $id]);
        if ($game) {
            $url = $game->getPublicUrl();
            if ($onlyUrl) {
                return $url;
            }
            return Html::a($game->title, $url, ['title' => $game->title]);
        }
        return false;
    }

    private static function replaceHttp($id, $onlyUrl = false)
    {
        return null;
    }

    private static function replaceDeveloper($id, $onlyUrl = false)
    {
        /**
         * @var Developer $developer
         */
        $developer = Developer::findOne(['url' => $id]);
        if ($developer) {
            $url = $developer->getNewsUrl();
            if ($onlyUrl) {
                return $url;
            }
            return Html::a($developer->name, $url, ['title' => $developer->name]);
        }
        return false;
    }

    private static function replaceNews($id, $onlyUrl = false)
    {
        if (!is_numeric($id)) {
            return false;
        }
        /**
         * @var News $news
         */
        $news = News::findOne($id);
        if ($news) {
            $url = $news->getPublicUrl();
            if ($onlyUrl) {
                return $url;
            }
            return Html::a($news->title, $url, ['title' => $news->title]);
        }
        return false;
    }

    private static function replaceFile($id, $onlyUrl = false)
    {
        if (!is_numeric($id)) {
            return false;
        }
        /**
         * @var File $file
         */
        $file = File::findOne($id);
        if ($file) {
            $url = $file->getPublicUrl();
            if ($onlyUrl) {
                return $url;
            }
            return Html::a($file->title, $url, ['title' => $file->title]);
        }
        return false;
    }

    private static function replaceArticle($id, $onlyUrl = false)
    {
        if (!is_numeric($id)) {
            return false;
        }
        /**
         * @var Article $article
         */
        $article = Article::findOne($id);
        if ($article) {
            $url = $article->getPublicUrl();
            if ($onlyUrl) {
                return $url;
            }
            return Html::a($article->title, $url, ['title' => $article->title]);
        }
        return false;
    }

    private static function replaceGallery($id, $onlyUrl = false)
    {
        if (!is_numeric($id)) {
            return false;
        }
        /**
         * @var GalleryPic $picture
         */
        $picture = GalleryPic::findOne($id);
        if ($picture) {
            $url = $picture->getPublicUrl();
            $url .= '#nanogallery/nanoGallery/0/' . $picture->id;
            if ($onlyUrl) {
                return $url;
            }

            $html = Html::img($picture->getThumbUrl(300, 500), ['alt' => $picture->desc]);

            return Html::a($html, $url, ['title' => $picture->desc]);
        }
        return false;
    }
}