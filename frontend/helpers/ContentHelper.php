<?php

namespace biowareru\frontend\helpers;


use bioengine\common\modules\articles\models\Article;
use bioengine\common\modules\files\models\File;
use bioengine\common\modules\gallery\models\GalleryPic;
use bioengine\common\modules\main\models\Developer;
use bioengine\common\modules\main\models\Game;
use bioengine\common\modules\news\models\News;
use yii\helpers\Html;
use yii\web\View;

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
            'placeholder' => '\[gallery:([0-9]+):([0-9]+):([0-9]+)\]',
            'method'      => 'replaceGallery',
            'onlyUrl'     => false
        ],
        [
            'placeholder' => '\[galleryUrl:([0-9]+)\]',
            'method'      => 'replaceGallery',
            'onlyUrl'     => true
        ],
        [
            'placeholder' => 'src=\"http:',
            'method'      => 'replaceHttp',
            'onlyUrl'     => true
        ],
        [
            'placeholder' => '\[video id\=([0-9]+?) uri\=(.*?)\](.*?)\[\/video\]',
            'method'      => 'replaceVideo',
        ],
        [
            'placeholder' => '\[twitter:([0-9]+)\]',
            'method'      => 'replaceTW',
            'onlyUrl'     => false
        ],
    ];

    public static function replacePlaceholders($text)
    {
        $hash = md5($text);
        $cached = \Yii::$app->cache->get('parsed_text_' . $hash);
        if ($cached) {
            return $cached;
        }
        foreach (self::$placeholders as $placeholder) {
            $matches = [];
            preg_match_all('/' . $placeholder['placeholder'] . '/', $text, $matches);
            if ($matches[0]) {
                $method = $placeholder['method'];

                foreach ($matches[0] as $key => $match) {
                    $attrs = [];
                    foreach ($matches as $index => $group) {
                        if ($index > 0) {
                            $attrs[] = $group[$key];
                            if ($index === 1) {
                                $attrs[] = $placeholder['onlyUrl']??null;
                            }
                        }
                    }
                    $replacement = call_user_func_array([self::class, $method], $attrs);

                    if ($replacement === false) {
                        $replacement = 'n/a';
                    }
                    $text = str_ireplace($match, $replacement, $text);
                }
            }
        }
        \Yii::$app->cache->set('parsed_text_' . $hash, $text);
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

    private static function replaceHttp()
    {
        return 'src="';
    }

    private static function replaceVideo($id, $onlyUrl = false)
    {
        /**
         * @var File $file
         */
        $file = File::findOne($id);
        if ($file && $file->yt_id) {
            return '<iframe width="560" height="315" src="//www.youtube.com/embed/' . $file->yt_id . '"
                            frameborder="0"
                            allowfullscreen></iframe>';
        }
        return null;
    }


    private static function replaceTW($id, $onlyUrl = false)
    {
        $html = <<<EOF
        <div class="embed-twit" id="twitter{$id}"></div>
        <script type="text/javascript">
        twttr.ready(function(){
twttr.widgets.createTweet(
  "{$id}",
  document.getElementById("twitter{$id}"),
  {
    linkColor: "#55acee",
    conversation: "none"
  }
);
});
</script>
EOF;
        return $html;
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

    private static function replaceGallery($id, $onlyUrl = false, $width = 300, $height = 300)
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

            $html = Html::img($picture->getThumbUrl($width, $height), ['alt' => $picture->desc]);

            return Html::a($html, $url, ['title' => $picture->desc]);
        }
        return false;
    }

    public static function getImage($html)
    {
        preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $html, $image);
        return isset($image['src']) ? $image['src'] : null;
    }

    public static function getDescription($html)
    {
        $html = str_ireplace(PHP_EOL, ' ', self::replacePlaceholders($html));
        $content = trim(preg_replace('#<[^>]+>#', '  ', $html));

        $content = str_ireplace('&nbsp;', ' ', $content);
        $content = str_ireplace('  ', '', $content);

        $words = explode(' ', $content);

        $count = count($words);


        $content = trim(implode(' ', array_slice($words, 0, 20)));
        if ($count > 20) {
            $content .= '...';
        }

        return htmlspecialchars_decode($content);
    }

    public static function getRemoteFileSizeAndMime($url)
    {
        $hash = md5($url);
        $cached = \Yii::$app->cache->get('parsed_img_' . $hash);
        if ($cached) {
            return $cached;
        }
        try {
            $headers = get_headers($url, 1);
            if ($headers) {
                $size = $headers['Content-Length'];
                $mime = $headers['Content-Type'];
                $result = ['size' => $size, 'mime' => $mime];
                \Yii::$app->cache->set('parsed_img_' . $hash, $result);

                return $result;
            }
        } catch (\Exception $ex) {

        }
        return null;
    }

    public static function getRemoteFileSize($url)
    {
        static $regex = '/^Content-Length: *+\K\d++$/im';
        if (!$fp = @fopen($url, 'rb')) {
            return 0;
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