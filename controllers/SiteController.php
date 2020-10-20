<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [[
            'succes'=> true
        ]];
    }

    public function actionError()
    {
        Yii::$app->monolog->getLogger()->log('error', Yii::$app->errorHandler->exception);
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [[
        	'succes'=> false
        ]];
    }
}
