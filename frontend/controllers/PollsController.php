<?php

namespace biowareru\frontend\controllers;


use bioengine\common\helpers\UserHelper;
use bioengine\common\modules\polls\controllers\frontend\IndexController;
use bioengine\common\modules\polls\models\Poll;
use bioengine\common\modules\polls\models\PollWho;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class PollsController extends IndexController
{
    public function actionVote($pollId)
    {
        /**
         * @var Poll $poll
         */
        $poll = Poll::findOne($pollId);
        if (!$poll || $poll->onoff !== 1) {
            throw new NotFoundHttpException();
        }

        if ($poll->isVoted()) {
            $this->redirect(Url::toRoute('polls/alreadyVoted'));
        }

        if ($poll->multiple) {
            $votes = \Yii::$app->request->getBodyParam('vote', []);
        } else {
            $votes = (array)\Yii::$app->request->getBodyParam('vote', []);
        }
        if ($votes) {
            foreach ($votes as $k => $voteValue) {
                $user = null;
                if (!\Yii::$app->user->isGuest) {
                    $user = UserHelper::getUser();
                }
                $vote = new PollWho;
                $vote->user_id = $user ? $user->member_id : 0;
                $vote->poll_id = $poll->poll_id;
                $vote->login = $user ? $user->name : 'guest';
                $vote->vote_date = time();
                $vote->voteoption = $voteValue;
                $vote->ip = $_SERVER['REMOTE_ADDR'];
                $vote->session_id = (string)UserHelper::getSessionId();
                if ($vote->validate()) {
                    $vote->save();
                } else {
                    var_dump($vote->errors);
                    die();
                }
            }
            $poll->recount();
            $this->redirect(\Yii::$app->request->referrer);
        }
    }
}