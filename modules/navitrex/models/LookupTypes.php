<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%lookup_types}}".
 *
 * @property integer $id
 * @property string $value
 * @property string $short_code
 * @property integer $type_of_lookup_type
 * @property integer $parent_id
 * @property integer $sync_to_mobile
 * @property integer $status
 * @property integer $is_delete
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property Configurations[] $configurations
 * @property LookupTypes $parent
 * @property LookupTypes[] $lookupTypes
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property Lookups[] $lookups
 */
class LookupTypes extends \yii\db\ActiveRecord
{
    public $messages="";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lookup_types}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value', 'is_delete', 'created_on', 'modified_on'], 'required'],
            [['type_of_lookup_type', 'parent_id', 'sync_to_mobile', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            ['value', 'string', 'max' => 150,'tooLong'=>'Name should contain at most 150 characters.'],
            ['short_code', 'string', 'max' => 100],
            ['value','required','message'=>'Name cannot be blank.'],
            ['value','validateLookupType'],
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
            'short_code' => 'short code to read value',
            'type_of_lookup_type' => '1=system defined 0=user defined',
            'parent_id' => 'Parent ID',
            'sync_to_mobile' => 'Sync To Mobile',
            'status' => '1 for enable,0 for disable',
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
    public function getConfigurations()
    {
        return $this->hasMany(Configurations::className(), ['source_value' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(LookupTypes::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLookupTypes()
    {
        return $this->hasMany(LookupTypes::className(), ['parent_id' => 'id']);
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
    public function getLookups()
    {
        return $this->hasMany(Lookups::className(), ['type' => 'id']);
    }
    
    //validate
    
     /**
     * Validates the LookupType.
     * This method serves as the inline validation for LookupType.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateLookupType($attribute, $params)
    {
     
        if (!$this->hasErrors()) {
            
          $sql="SELECT COUNT(id) as count FROM cc_lookup_types WHERE LOWER(value)='".  strtolower($this->value)."' AND is_delete='1'";
            if($this->id!=""){    
                $sql.=" AND id!='".$this->id."'";
}
                
         $connection = Yii::$app->getDb();           
         $command = $connection->createCommand($sql);
         $lookupType = $command->queryAll();   
         $lookupType=$lookupType[0]['count'];           
            if($lookupType>0){
                $this->messages=  \app\facades\common\CommonFacade::getMessages();
                $msg=$this->messages->M129;
                $msg=  str_replace("{1}",  $this->value, $msg);                
                $this->addError($attribute, $msg);
            }
        }     
    }
}
