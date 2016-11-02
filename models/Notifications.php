<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cc_notifications".
 *
 * @property integer $id
 * @property integer $device_id
 * @property integer $screen_id
 * @property string $image
 * @property string $message
 * @property integer $status
 * @property string $created_on
 * @property integer $created_by
 *
 * @property Devices $device
 * @property Users $createdBy
 * @property Lookups $status0
 * @property Lookups $screen
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cc_notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_id', 'message', 'status', 'created_on', 'created_by'], 'required'],
            [['device_id', 'screen_id', 'status', 'created_by'], 'integer'],
            [['created_on'], 'safe'],
            [['image'], 'string', 'max' => 255],
            [['message'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device_id' => 'Device ID',
            'screen_id' => 'Screen ID',
            'image' => 'Image',
            'message' => 'Message',
            'status' => 'Status',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Devices::className(), ['id' => 'device_id']);
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
    public function getStatus0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'status']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScreen()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'screen_id']);
    }
}
