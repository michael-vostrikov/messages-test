<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'frontend\controllers\AppBootstrap'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'formatter' => [
            'dateFormat' => 'php:d-m-Y',
            'datetimeFormat' => 'php:d-m-Y H:i:s',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views' => '@frontend/modules/user/views',
                ],
            ],
        ],
        'onlineManager' => [
            'class' => 'common\components\OnlineManager',
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableFlashMessages' => false,
            'enableUnconfirmedLogin' => false,
            'enableRegistration' => true,
            'enableConfirmation' => true,
            'enablePasswordRecovery' => true,
            'enableGeneratingPassword' => false,
            'confirmWithin' => 6 * 3600,
            'admins' => ['admin'],
            'modelMap' => [
                'RegistrationForm' => 'frontend\modules\user\models\RegistrationForm',
                'Profile' => 'common\models\Profile',
                'User' => 'common\models\User',
            ],
            'on afterLogin' => function($event) {
                echo 1; die;
            }
        ],
    ],
    'params' => $params,
];
