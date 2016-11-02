<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cc_email_templates".
 *
 * @property integer $id
 * @property integer $event_id
 * @property string $name
 * @property integer $language
 * @property string $subject
 * @property string $content
 * @property string $attachment
 * @property integer $status
 * @property integer $is_delete
 * @property integer $created_by
 * @property string $created_on
 * @property integer $modified_by
 * @property string $modified_on
 *
 * @property Lookups $event
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property Lookups $language0
 */
class EmailTemplates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cc_email_templates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['event_id', 'language', 'subject', 'content', 'status', 'is_delete', 'created_by', 'created_on'], 'required'],
            //[['event_id', 'language', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            //[['content'], 'string'],
            //[['created_on', 'modified_on'], 'safe'],
            //[['name'], 'string', 'max' => 255],
            //[['subject'], 'string', 'max' => 500],
            
            
            [['subject', 'content', 'status', 'is_delete', 'created_by', 'created_on'], 'required'],
            [['event_id', 'language', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['content'], 'string'],
            [['created_on', 'modified_on', 'attachment'], 'safe'],
            
            [['subject'], 'string', 'max' => 500],
            
            ['event_id', 'required', 'message' => 'Event name cannot be blank.'],
            ['language', 'required', 'message' => 'Language cannot be blank.'],
            
            [['subject'], 'match', 'pattern'=>'/^([0-9a-zA-Z  ]+)$/','message' => 'Subject is invalid.'],
            
            [['attachment'], 'file', 'skipOnEmpty' => true, 
                'maxSize' => 2e+6, 
                'tooBig' => 'Limit is 2MB'
            ],
            //[['name'], 'string', 'max' => 255, 'tooLong'=>'Template name should contain at most 255 characters.'],
            //['name', 'required', 'message' => 'Template name cannot be blank.'],
            //[['name'], 'match', 'pattern'=>'/^([0-9a-zA-Z  ]+)$/','message' => 'Template name is invalid.'],
            //[['name'], 'unique', 'message' => 'Template name already taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'name' => 'Name',
            'language' => 'Language',
            'subject' => 'Subject',
            'content' => 'Content',
            'attachment'=>'Attachment',
            'status' => 'Status',
            'is_delete' => 'Is Deleted',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'event_id']);
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
    public function getLanguage0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'language']);
    }
}
