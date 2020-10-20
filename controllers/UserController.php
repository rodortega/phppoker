<?php 

namespace app\controllers;

use Yii;

use app\Models\User;
use app\libraries\Poker;

class UserController extends BaseController
{
    public $modelClass = 'app\models\User';

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action))
        {
            return false;
        }

        return true;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['update']['scenario'] = 'UPDATE_USER';
        $actions['create']['scenario'] = 'CREATE_USER';
        return $actions;
    }

    public function actionLogin()
    {
        $users = User::find()->all();

        $Poker = new Poker();
        $Poker->shuffle();

        for ($i = 0; $i < count($users); $i++)
        {
            $users[$i]->hand = $Poker->draw(2);
        }

        return $users;
    }
}