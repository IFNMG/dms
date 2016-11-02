<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%system_tokens}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $value
 * @property string $creation_date_time
 * @property string $expiration_date_time
 * @property integer $user_id
 * @property integer $status
 * @property integer $is_delete
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property Lookups $type0
 * @property Users $user
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 */
class SystemTokens extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_tokens}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'value', 'creation_date_time', 'expiration_date_time', 'user_id', 'status', 'is_delete', 'created_on', 'created_by', 'modified_on', 'modified_by'], 'required'],
            [['type', 'user_id', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['creation_date_time', 'expiration_date_time', 'created_on', 'modified_on'], 'safe'],
            [['value'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'value' => 'Value',
            'creation_date_time' => 'Creation Date Time',
            'expiration_date_time' => 'Expiration Date Time',
            'user_id' => 'User ID',
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
    public function getType0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'type']);
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
