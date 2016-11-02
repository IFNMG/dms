<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Users;

/**
 * LoginForm is the model behind the login form.
 */
class ChangepasswordForm extends Model
{
    public $oldPassword;
    public $newPassword;
    public $repeatNewPassword;
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['oldPassword', 'newPassword'], 'required'],
            [['oldPassword','newPassword','repeatNewPassword'], 'string', 'max' =>30],
            [['oldPassword','newPassword','repeatNewPassword'], 'string', 'min' =>6],
            ['repeatNewPassword', 'compare','compareAttribute'=>'newPassword','operator'=>'===', 'message'=>'Confirm password must be equal to "New password".'],
            
            [['repeatNewPassword'], 'required', 'message'=>'Confirm new password cannot be blank.'],
        ];
    }
    
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'oldPassword' => 'Old password',
            'newPassword' => 'New password',
            'repeatNewPassword' => 'Repeat password',
        ];
    }
    
   }
