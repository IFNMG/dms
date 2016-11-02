<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%devices}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $device_id
 * @property integer $device_type
 * @property string $device_token
 * @property string $current_token
 * @property string $previous_token
 * @property integer $status
 * @property integer $is_delete
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property Users $user
 * @property Lookups $deviceType
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 */
class Devices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%devices}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'device_type', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['device_id', 'device_type', 'device_token', 'is_delete', 'created_on', 'modified_on'], 'required'],
            [['created_on', 'modified_on'], 'safe'],
            [['device_id', 'device_token'], 'string', 'max' => 150],
            [['current_token', 'previous_token'], 'string', 'max' => 1000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'device_id' => 'Device ID',
            'device_type' => 'Device Type',
            'device_token' => 'Device Token',
            'current_token' => 'Current Token',
            'previous_token' => 'Previous Token',
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
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceType()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'device_type']);
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
