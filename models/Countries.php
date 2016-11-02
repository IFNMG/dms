<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%countries}}".
 *
 * @property integer $id
 * @property string $value 
 * @property string $iso_code
 * @property string $isd_code
 * @property string $flag_url
 * @property string $exit_code
 * @property integer $status
 * @property integer $is_delete
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property States[] $states
 */
class Countries extends \yii\db\ActiveRecord
{
    public  $messages="";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%countries}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'is_delete', 'created_on', 'created_by', 'modified_on', 'modified_by'], 'required'],
            [['status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            ['value','required','message'=>'Name cannot be blank.'],
            ['value', 'string', 'max' => 150,'tooLong'=>'Name should contain at most 150 characters.'],            
            ['iso_code','required','message'=>'ISO Code cannot be blank.'],
            ['iso_code', 'match', 'pattern' => '/^[A-Z]*$/','message'=>'ISO Code should contain capital characters.'],
            ['iso_code', 'string', 'max' => 5,'tooLong'=>'ISO Code should contain at most 5 characters.'],
            ['isd_code', 'match', 'pattern' => '/^[0-9]*$/','message'=>'ISD Code should contain numeric digits.'],
            ['isd_code', 'string', 'max' => 5,'tooLong'=>'ISD Code should contain at most 5 digits.'],
            [['flag_url'], 'string', 'max' => 255],
            [['exit_code'], 'string', 'max' => 10],  
            [['flag_url'], 'file',
              'skipOnEmpty' => true, 
              'extensions' => 'png, jpg, jpeg', 
              'maxFiles' => 1,
              'maxSize' => 1024*1024, 
              'tooBig' => 'Limit is 1MB'
            ],
            ['value','validateCountry'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',            
            'iso_code' => 'Iso Code',
            'isd_code' => 'Isd Code',
            'flag_url' => 'Flag Url',
            'exit_code' => 'Exit Code',
            'status' => 'Status',
            'is_delete' => 'Is Delete',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'modified_on' => 'Modified On',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'status']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'modified_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStates()
    {
        return $this->hasMany(States::className(), ['country_id' => 'id']);
    }
    
    //validate
    
     /**
     * Validates the Country.
     * This method serves as the inline validation for country.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateCountry($attribute, $params)
    {
     
        if (!$this->hasErrors()) {
            
          $sql="SELECT COUNT(id) as count FROM cc_countries WHERE LOWER(value)='".  strtolower($this->value)."' AND is_delete='1'";
            if($this->id!=""){    
                $sql.=" AND id!='".$this->id."'";
                }
                
         $connection = Yii::$app->getDb();           
         $command = $connection->createCommand($sql);
         $country = $command->queryAll();   
         $country=$country[0]['count'];           
            if($country>0){
                $this->messages=  \app\facades\common\CommonFacade::getMessages();
                $msg=$this->messages->M129;
                $msg=  str_replace("{1}",  $this->value, $msg);                
                $this->addError($attribute, $msg);
            }
        }     
    }
}
