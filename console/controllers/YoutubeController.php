<?php
/**
 * Created by PhpStorm.
 * User: sonic
 * Date: 14-Jun-15
 * Time: 14:00
 */

namespace biowareru\console\controllers;


use bioengine\common\modules\files\models\File;
use biowareru\console\helpers\YoutubeHelper;
use yii\console\Controller;

class YoutubeController extends Controller
{
    public function actionUpload()
    {
        /**
         * @var File[] $files
         */
        $files = File::find()->all();
        $count = 0;
        $size = 0;
        $extensions = [
            'flv',
            'mp4',
            'mkv',
            'mov',
            'wmv',
            'avi',
            'mpeg'
        ];
        $allExt = [];
        foreach ($files as $file) {
            if (strpos($file->link, 'files.bioware.ru') !== false) {
                if ($file->yt_status === 1) {
                    continue;
                }
                if ($file->size > 0) {
                    $info = pathinfo(parse_url($file->link, PHP_URL_PATH), PATHINFO_EXTENSION);
                    if (!in_array($info, $extensions, true)) {
                        continue;
                    }
                    YoutubeHelper::upload($file);
                    die();
                }
            }
        }
    }
}