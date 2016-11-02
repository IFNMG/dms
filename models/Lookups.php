<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%lookups}}".
 *
 * @property integer $id
 * @property string $value
 * @property string $short_code
 * @property integer $type
 * @property integer $parent_id
 * @property string $description
 * @property string $info1
 * @property string $info2
 * @property string $image_path
 * @property integer $is_seed_data
 * @property integer $status
 * @property integer $is_delete
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property AdminPersonal[] $adminPersonals
 * @property Cities[] $cities
 * @property Cities[] $cities0
 * @property Configurations[] $configurations
 * @property Configurations[] $configurations0
 * @property Configurations[] $configurations1
 * @property Configurations[] $configurations2
 * @property Countries[] $countries
 * @property Devices[] $devices
 * @property Devices[] $devices0
 * @property EmailHistory[] $emailHistories
 * * @property LookupTypes[] $lookupTypes
 * @property EmailTemplates[] $emailTemplates
 * @property EmailTemplates[] $emailTemplates0
 * @property EmailTemplates[] $emailTemplates1
 * @property LookupTypes $type0
 * @property Lookups $parent
 * @property Lookups[] $lookups
 * @property Lookups $status0
 * @property Lookups[] $lookups0
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property Notifications[] $notifications
 * @property Pages[] $pages
 * @property Pages[] $pages0
 * @property PasswordHistory[] $passwordHistories
 * @property PasswordHistory[] $passwordHistories0
 * @property Permissions[] $permissions
 * @property Permissions[] $permissions0
 * @property Permissions[] $permissions1
 * @property RolePermissions[] $rolePermissions
 * @property RolePermissions[] $rolePermissions0
 * @property States[] $states
 * @property States[] $states0
 * @property SystemTokens[] $systemTokens
 * @property SystemTokens[] $systemTokens0
 * @property UserPersonalDetails[] $userPersonalDetails
 * @property UserPersonalDetails[] $userPersonalDetails0
 * @property UserPersonalDetails[] $userPersonalDetails1
 * @property UserPersonalDetails[] $userPersonalDetails2
 * @property Users[] $users
 * @property Users[] $users0
 * @property Users[] $users1
 */
class Lookups extends \yii\db\ActiveRecord
{
    public $messages="";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lookups}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'is_seed_data', 'is_delete', 'created_on', 'modified_on'], 'required'],
            [['type', 'parent_id', 'is_seed_data', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            ['value', 'string', 'max' => 150,'tooLong'=>'Name should contain at most 150 characters.'],
            ['value','required','message'=>'Name cannot be blank.'],
            [['description', 'info1', 'info2', 'image_path'], 'string', 'max' => 255],
            ['short_code', 'string', 'max' => 100],
            [['image_path'], 'safe'],            
            [['image_path'], 'file',
                'skipOnEmpty' => true, 
                'extensions' => 'png, jpg, jpeg', 
                'maxFiles' => 1,
                'maxSize' => 1024*1024, 
                'tooBig' => 'Limit is 1MB'
            ],
            ['value','validateLookup'],
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
            'short_code' => 'short code to read values',
            'type' => 'Type',
            'parent_id' => 'Parent ID',
            'description' => 'Description',
            'info1' => 'type-1: order-of-access-level',
            'info2' => 'Info2',
            'image_path' => 'Image Path',
            'is_seed_data' => 'Is Seed Data',
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
    public function getAdminPersonals()
    {
        return $this->hasMany(AdminPersonal::className(), ['status' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(Cities::className(), ['status' => 'id']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities0()
    {
        return $this->hasMany(Cities::className(), ['modified_by' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigurations()
    {
        return $this->hasMany(Configurations::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigurations0()
    {
        return $this->hasMany(Configurations::className(), ['auto_generate_type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigurations1()
    {
        return $this->hasMany(Configurations::className(), ['section' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigurations2()
    {
        return $this->hasMany(Configurations::className(), ['menu_section' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Countries::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Devices::className(), ['device_type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices0()
    {
        return $this->hasMany(Devices::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmailHistories()
    {
        return $this->hasMany(EmailHistory::className(), ['status' => 'id']);
    }
  /**
     * @return \yii\db\ActiveQuery
     */
    public function getLookupTypes()
    {
        return $this->hasMany(LookupTypes::className(), ['status' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmailTemplates()
    {
        return $this->hasMany(EmailTemplates::className(), ['event_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmailTemplates0()
    {
        return $this->hasMany(EmailTemplates::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmailTemplates1()
    {
        return $this->hasMany(EmailTemplates::className(), ['language' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(LookupTypes::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLookups()
    {
        return $this->hasMany(Lookups::className(), ['parent_id' => 'id']);
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
    public function getLookups0()
    {
        return $this->hasMany(Lookups::className(), ['status' => 'id']);
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
    public function getNotifications()
    {
        return $this->hasMany(Notifications::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPages()
    {
        return $this->hasMany(Pages::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPages0()
    {
        return $this->hasMany(Pages::className(), ['category' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasswordHistories()
    {
        return $this->hasMany(PasswordHistory::className(), ['type_of_operation' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasswordHistories0()
    {
        return $this->hasMany(PasswordHistory::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permissions::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions0()
    {
        return $this->hasMany(Permissions::className(), ['permission_type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions1()
    {
        return $this->hasMany(Permissions::className(), ['display_option' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolePermissions()
    {
        return $this->hasMany(RolePermissions::className(), ['role_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolePermissions0()
    {
        return $this->hasMany(RolePermissions::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStates()
    {
        return $this->hasMany(States::className(), ['status' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStates0()
    {
        return $this->hasMany(States::className(), ['modified_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSystemTokens()
    {
        return $this->hasMany(SystemTokens::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSystemTokens0()
    {
        return $this->hasMany(SystemTokens::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPersonalDetails()
    {
        return $this->hasMany(UserPersonalDetails::className(), ['gender' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPersonalDetails0()
    {
        return $this->hasMany(UserPersonalDetails::className(), ['marital_status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPersonalDetails1()
    {
        return $this->hasMany(UserPersonalDetails::className(), ['social_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPersonalDetails2()
    {
        return $this->hasMany(UserPersonalDetails::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['role' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany(Users::className(), ['status' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers1()
    {
        return $this->hasMany(Users::className(), ['user_type' => 'id']);
    }
    
    
    /**
     * Validates the lookup.
     * This method serves as the inline validation for lookup.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateLookup($attribute, $params)
    {
     
        if (!$this->hasErrors()) {
            
          $sql="SELECT COUNT(id) as count FROM cc_lookups WHERE LOWER(value)='".  strtolower($this->value)."' AND is_delete='1' AND type='".$this->type."'";
            if($this->id!=""){    
                $sql.=" AND id!='".$this->id."'";
}
                
                
         $connection = Yii::$app->getDb();           
         $command = $connection->createCommand($sql);
         $lookup = $command->queryAll();   
         $lookup=$lookup[0]['count'];
            if($lookup>0){
                $this->messages=  \app\facades\common\CommonFacade::getMessages();
                $msg=$this->messages->M128;
                $msg=  str_replace("{1}",  $this->value, $msg);
                $msg=  str_replace("{2}",  $this->type0->value, $msg);
                $this->addError($attribute, $msg);
            }
        }     
    }
}
