<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\AdminPersonal;
use \app\modules\user\models\UserPersonal;

/**
 * LoginForm is the model behind the login form.
 */
class ForgotpasswordForm extends Model
{
    public $email;
    public $messages;
    public $id;
    private $_user = false;
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
            
            //['email', 'validateEmail'],
        ];
    }
    
    
    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            
            if($user){
                if ($user->is_delete == 0) {
                    $this->addError($attribute, $this->messages->M111);
                } else {
                    $this->id = $user->user_id;
                }
            } else {
                $this->addError($attribute, $this->messages->M110);
            }
            
        }
    }
    
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = UserPersonal::find()->where(['email'=>$this->email])->one();
        }

        return $this->_user;
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
