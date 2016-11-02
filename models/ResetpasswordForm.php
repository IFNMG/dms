<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\AdminPersonal;
use \app\modules\user\models\UserPersonal;

/**
 * LoginForm is the model behind the login form.
 */
class ResetpasswordForm extends Model
{
    public $newPassword;
    public $repeatNewPassword;
    public $secretHash;
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['newPassword','repeatNewPassword','secretHash'], 'required'],
            [['newPassword','repeatNewPassword'], 'string', 'max' =>30],
            [['newPassword','repeatNewPassword'], 'string', 'min' =>8],
            [['secretHash'], 'string', 'max' =>150],
            [['secretHash'], 'string', 'min' =>8],
            ['repeatNewPassword', 'compare','compareAttribute'=>'newPassword','operator'=>'==='],
        ];
    }
    
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'newPassword' => 'New password',
            'repeatNewPassword' => 'Confirm password',
        ];
    }
    
   }
