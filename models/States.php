<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%states}}".
 *
 * @property integer $id
 * @property string $value
 * @property string $short_name
 * @property integer $country_id
 * @property integer $status
 * @property integer $is_delete
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property Cities[] $cities
 * @property Countries $country
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 */
class States extends \yii\db\ActiveRecord
{
    public $messages="";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%states}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'status', 'is_delete', 'created_on', 'created_by', 'modified_on', 'modified_by'], 'required'],
            [['country_id', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            ['value', 'string', 'max' => 150,'tooLong'=>'Name should contain at most 150 characters.'],
            ['value','required','message'=>'Name cannot be blank.'],
            [['short_name'], 'string', 'max' => 5],
            ['value','validateState'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Name',
            'short_name' => 'Short Name',
            'country_id' => 'Country ID',
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
    public function getCities()
    {
        return $this->hasMany(Cities::className(), ['state_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
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
     * Validates the state.
     * This method serves as the inline validation for state.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateState($attribute, $params)
    {
     
        if (!$this->hasErrors()) {
            
          $sql="SELECT COUNT(id) as count FROM cc_states WHERE LOWER(value)='".  strtolower($this->value)."' AND is_delete='1' AND country_id='".$this->country_id."'";
            if($this->id!=""){    
                $sql.=" AND id!='".$this->id."'";
                }
                
         $connection = Yii::$app->getDb();           
         $command = $connection->createCommand($sql);
         $state = $command->queryAll();   
         $state=$state[0]['count'];           
            if($state>0){
                $this->messages=  \app\facades\common\CommonFacade::getMessages();
                $msg=$this->messages->M128;
                $msg=  str_replace("{1}",  $this->value, $msg);
                $msg=  str_replace("{2}",  $this->country->value, $msg);
                $this->addError($attribute, $msg);
            }
        }     
    }
}
