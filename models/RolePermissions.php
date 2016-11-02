<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cc_role_permissions".
 *
 * @property integer $id
 * @property integer $role_id
 * @property integer $permission_id
 * @property integer $default
 * @property integer $add
 * @property integer $edit
 * @property integer $delete
 * @property integer $view
 * @property integer $list
 * @property integer $change_status
 * @property integer $status
 * @property integer $is_delete
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property Lookups $role
 * @property Permissions $permission
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 */
class RolePermissions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cc_role_permissions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'permission_id', 'default', 'add', 'edit', 'delete', 'view', 'list', 'change_status', 'status', 'is_delete', 'created_on', 'created_by'], 'required'],
            [['role_id', 'permission_id', 'default', 'add', 'edit', 'delete', 'view', 'list', 'change_status', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'permission_id' => 'Permission ID',
            'default' => 'Default',
            'add' => 'Add',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'view' => 'View',
            'list' => 'List',
            'change_status' => 'Change Status',
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
    public function getRole()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermission()
    {
        return $this->hasOne(Permissions::className(), ['id' => 'permission_id']);
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
}
