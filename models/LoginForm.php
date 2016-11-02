<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    
    private $_user = false;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['password'], 'required'],
            [['password'], 'string', 'max' =>30],
            [['password'], 'string', 'min' =>6],
            [['username'], 'required', 'message'=>'Email address cannot be blank.'],
            
            [['username'], 'email', 'message'=>'Email address is invalid.'],
            
            //['username', 'email'],
            
            // password is validated by validatePassword()
            //['password', 'validatePassword'],
        ];
    }
    
    
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            
            if($user){
                if ($user->is_delete == 0) {
                    $this->addError($attribute, $this->messages->M111);
                } else {
                    $User = Users::find()->select(['password'])->where(['id'=>$user->user_id])->one();
                    if(md5(md5($this->password)) != $User->password){
                        $this->addError($attribute, $this->messages->M104);
                    }
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
    public function getUser(){
        if ($this->_user === false) {
            $this->_user = UserPersonal::find()->where(['email'=>$this->username])->orWhere(['phone' =>$this->username])->one();
        }
        return $this->_user;
    }
    
}
