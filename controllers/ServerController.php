<?php 

namespace app\controllers;

use Yii;

use app\Models\User;
use app\libraries\Poker;

class ServerController extends BaseController
{
    public $modelClass = 'app\models\User';

    public function actionStart()
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