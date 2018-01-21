<?php

namespace frontend\controllers;

use yii\base\BootstrapInterface;
use common\models\User;

class AppBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $user = $app->user->identity;
        if ($user) {
            $app->onlineManager->setOnline($user);
        }

        $app->user->on(\yii\web\User::EVENT_BEFORE_LOGOUT, [$app->onlineManager, 'onLogout']);
    }
}
