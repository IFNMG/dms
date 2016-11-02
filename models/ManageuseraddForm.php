<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Users;

/**
 * LoginForm is the model behind the login form.
 */
class ManageuseraddForm extends Model
{
    public $firstName;
    public $lastName;
    public $email;
    public $department;
    public $sub_department;
    public $phone;
    public $userType;
    public $onetimePassword;
    public $role;
    public $image_path;

   
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['firstName','lastName', 'email', 'department', 'sub_department','role'], 'required'],
            [['firstName','lastName','onetimePassword'], 'string', 'max' =>30],
            [['onetimePassword'], 'string', 'min' =>8],
            ['email','email'],
            [['phone'], 'match', 'pattern'=>'/^([0-9  ]+)$/','message' => 'Phone number is invalid.'],
            [['phone'], 'string', 'max' =>15],
            [['phone'], 'string', 'min' =>10],
            [['image_path'], 'safe'],            
            [['image_path'], 'file',
                'skipOnEmpty' => true, 
                'extensions' => 'png, jpg, jpeg', 
                'maxFiles' => 1,
                'maxSize' => 1024*1024, 
                'tooBig' => 'Limit is 1MB'
            ],
            
        ];
    }
    
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'onetimePassword' => 'OTP',
            'email' => 'Email',
            'phone' => 'Phone',
            'role' => 'Role',
            'image_path' =>'Image Path'
        ];
    }
    
   }
