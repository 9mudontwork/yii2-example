<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$string = '([-\w]+|\d+)';
$number = '\d+';

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
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
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            // 'suffix' => '/',
            // 'normalizer' => [
            //     // 'class' => 'yii\web\UrlNormalizer',
            //     // 'action' => \yii\web\UrlNormalizer::ACTION_REDIRECT_TEMPORARY,
            // ],
            'rules' => [
                // normal route
                "<controller:{$string}>" => "<controller>/index",
                "<controller:{$string}>/<id:{$number}>" => "<controller>/view",
                "<controller:{$string}>/<action:{$string}>/<id:{$string}>" => "<controller>/<action>",
                "<controller:{$string}>/<action:{$string}>" => "<controller>/<action>",

                // file route
                "<controller:{$string}>/<action:{$string}>/<id:{$string}>/<width:{$string}>" => "<controller>/<action>",
                "<controller:{$string}>/<action:{$string}>/<id:{$string}>/<width:{$string}>/<height:{$string}>" => "<controller>/<action>",
            ],
        ],
    ],
    'params' => $params,
];
