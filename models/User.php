<?php 

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class User extends ActiveRecord
{
    public static function tableName()
    {
        return 'users';
    }
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['CREATE_USER'] = [];
        $scenarios['UPDATE_USER'] = [];
        return $scenarios;
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['hand']);
    }
}