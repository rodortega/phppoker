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

    public function fields()
    {
        return [
            'id', 
            'username', 
            'first_name',
            'last_name', 
            'status',
            'hand'
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['hand']);
    }
}