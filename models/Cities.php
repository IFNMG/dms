<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%cities}}".
 *
 * @property integer $id
 * @property string $value
 * @property integer $state_id
 * @property integer $zip_code
 * @property integer $status
 * @property integer $is_delete
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property States $state
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 */
class Cities extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
        
    public $messages;    
    
    public static function tableName()
    {
        return '{{%cities}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value', 'state_id', 'zip_code', 'status', 'is_delete', 'created_on', 'created_by', 'modified_on', 'modified_by'], 'required'],
            [['state_id', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            [['value'], 'string', 'max' => 150],//zip_code            
            ['zip_code', 'match', 'pattern' => '/^[0-9]*$/','message'=>'Zip Code should contain numeric digits.'],
            ['zip_code', 'string', 'max' => 11,'tooLong'=>'Zip Code should contain at most 11 digits.'],           
            ['value','validateCity'],
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
            'state_id' => 'State ID',
            'zip_code' => 'Zip Code',
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
    public function getState()
    {
        return $this->hasOne(States::className(), ['id' => 'state_id']);
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
    
      //validate
    
     /**
     * Validates the city.
     * This method serves as the inline validation for city.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateCity($attribute, $params)
    {
     
        if (!$this->hasErrors()) {
            
          $sql="SELECT COUNT(id) as count FROM cc_cities WHERE LOWER(value)='".  strtolower($this->value)."' AND is_delete='1' AND state_id='".$this->state_id."'";
            if($this->id!=""){    
                $sql.=" AND id!='".$this->id."'";
                }
                
                
         $connection = Yii::$app->getDb();           
         $command = $connection->createCommand($sql);
         $city = $command->queryAll();   
         $city=$city[0]['count'];           
            if($city>0){
                $this->messages=  \app\facades\common\CommonFacade::getMessages();
                $msg=$this->messages->M128;
                $msg=  str_replace("{1}",  $this->value, $msg);
                $msg=  str_replace("{2}",  $this->state->value, $msg);
                $this->addError($attribute, $msg);
            }
        }     
    }
    
}
