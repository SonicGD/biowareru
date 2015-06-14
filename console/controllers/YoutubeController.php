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

        $extensions = [
            'flv',
            'mp4',
            'mkv',
            'mov',
            'wmv',
            'avi',
            'mpeg'
        ];

        foreach ($files as $file) {
            if (strpos($file->link, 'files.bioware.ru') !== false) {
                if ($file->yt_status === 1) {
                    continue;
                }
                if ($file->size > 0 && $file->yt_id === '') {
                    $info = pathinfo(parse_url($file->link, PHP_URL_PATH), PATHINFO_EXTENSION);
                    if (!in_array($info, $extensions, true)) {
                        continue;
                    }
                    echo 'File ' . $file->title . ' starts' . PHP_EOL;
                    if (YoutubeHelper::upload($file)) {
                        echo 'File ' . $file->title . ' done' . PHP_EOL;
                    } else {
                        echo 'Error' . PHP_EOL;
                    }

                }
            }
        }
    }
}