<?php

namespace biowareru\frontend\controllers;

use bioengine\common\modules\main\models\Game;
use yii\web\NotFoundHttpException;

class GamesController extends \bioengine\common\modules\main\controllers\frontend\GamesController
{
    public function actionShow($gameUrl)
    {
        $game = Game::find()->where(['url' => $gameUrl])->one();
        if (!$game) {
            throw new NotFoundHttpException();
        }

        return $this->render('@app/static/tmpl/p-game-page.twig', ['game' => $game]);
    }
}