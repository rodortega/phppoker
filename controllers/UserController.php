<?php 

namespace app\controllers;

use Yii;

use app\Models\User;
use app\libraries\Poker;

class UserController extends BaseController
{
    public $modelClass = 'app\models\User';

    public function actionLogin()
    {
        $users = User::find()->all();
        return $users;
    }
}