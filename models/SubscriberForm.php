<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Users;

/**
 * SubscriberForm is the model behind the Subscriber form.
 */
class SubscriberForm extends Model
{
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $role;    
    public $gender;
    public $marital_status;
    public $address;
    public $country;
    public $state;
    public $city;
    public $status;    
    public $image_path;
   
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email','role'], 'required'],
            [['first_name','last_name'], 'string', 'max' =>30],           
            ['email','email'],
            [['phone'], 'match', 'pattern'=>'/^([0-9  ]+)$/','message' => 'Phone number is invalid.'],
            [['phone'], 'string', 'max' =>15],
            [['phone'], 'string', 'min' =>10],
            [['email', 'address'], 'string', 'max' => 255],
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
            'first_name' => 'First Name',
            'last_name' => 'Last Name',            
            'email' => 'Email',
            'phone' => 'Phone',
            'image_path' => 'Image',
        ];
    }
    
   }
