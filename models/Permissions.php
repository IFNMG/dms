<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cc_permissions".
 *
 * @property integer $id
 * @property integer $permission_type
 * @property string $url
 * @property string $value
 * @property string $description
 * @property double $sort_order
 * @property string $image
 * @property integer $display_option
 * @property integer $parent_id
 * @property integer $status
 * @property integer $is_delete
 * @property integer $is_new_window
 * @property integer $developer_admin_only
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property Lookups $permissionType
 * @property Permissions $parent
 * @property Permissions[] $permissions
 * @property Lookups $displayOption
 * @property RolePermissions[] $rolePermissions
 */
class Permissions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cc_permissions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['permission_type', 'value', 'status', 'is_delete', 'created_on', 'created_by'], 'required'],
            //[['permission_type', 'display_option', 'parent_id', 'status', 'is_delete', 'is_new_window', 'created_by', 'modified_by'], 'integer'],
            //[['sort_order'], 'number'],
            //[['created_on', 'modified_on'], 'safe'],
            //[['url', 'description', 'image'], 'string', 'max' => 255],
            //[['value'], 'string', 'max' => 150],
            
            [['status', 'is_delete', 'created_on', 'created_by'], 'required'],
            [['permission_type', 'display_option', 'parent_id', 'status', 'is_delete', 'developer_admin_only', 'is_new_window', 'created_by', 'modified_by'], 'integer'],
            
            [['created_on', 'modified_on'], 'safe'],
            [['url', 'image'], 'string', 'max' => 255],
            [['value'], 'string', 'max' => 150, 'tooLong'=>\Yii::t('app', 'Permission name should contain at most 150 characters.') ],
            [[ 'description', ], 'string', 'max' => 255, 'tooLong'=>\Yii::t('app', 'Description should contain at most 255 characters.') ],
            
            ['permission_type', 'required', 'message' =>\Yii::t('app', 'Permission type cannot be blank.') ],
            ['value', 'required', 'message' =>\Yii::t('app', 'Permission name cannot be blank.') ],
            
            [['value'], 'match', 'pattern'=>'/^([0-9a-zA-Z  ]+)$/', 'message' => \Yii::t('app', 'Permission name is invalid.')],
            [['description'], 'match', 'pattern'=>'/^([0-9a-zA-Z.  ]+)$/', 'message' => \Yii::t('app', 'Description cannot contain special characters.')],
            
            
            [['sort_order'], 'number', 'message'=> \Yii::t('app', 'Sort order must be a number.')],
            
            [['url'], 'unique', 'message' => 'Url already taken.'],
            
            [['image'], 'file', 'skipOnEmpty' => true, 
                'extensions' => 'png, jpg, jpeg', 
                'maxSize' => 2e+6, 
                'tooBig' => 'Limit is 2MB'
            ],
            //[['value'], 'unique', 'message' => 'Permission name already taken.'],
            //['url', 'url', 'defaultScheme' => 'http'],
            //['url', 'url'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'permission_type' => 'Permission Type',
            'url' => 'Url',
            'value' => 'Value',
            'description' => 'Description',
            'sort_order' => 'Sort Order',
            'image' => 'Image',
            'display_option' => 'Display Option',
            'parent_id' => 'Parent ID',
            'status' => 'Status',
            'is_delete' => 'Is Delete',
            'is_new_window' => 'Is New Window',
            'developer_admin_only'=>'Developer Admin Only',
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
    public function getPermissionType()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'permission_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Permissions::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permissions::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDisplayOption()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'display_option']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolePermissions()
    {
        return $this->hasMany(RolePermissions::className(), ['permission_id' => 'id']);
    }
}
