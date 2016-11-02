<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%email_history}}".
 *
 * @property integer $id
 * @property string $email_id
 * @property integer $event_id
 * @property integer $status
 * @property integer $mail_method
 * @property string $subject
 * @property string $body
 * @property string $attachment
 * @property integer $attempts
 * @property string $sender_email
 * @property string $sent_on
 *
 * @property Lookups $status0
 */
class EmailHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%email_history}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email_id', 'status', 'mail_method', 'subject', 'body', 'attempts', 'sender_email', 'sent_on'], 'required'],
            [['event_id', 'status', 'mail_method', 'attempts'], 'integer'],
            [['body'], 'string'],
            [['sent_on'], 'safe'],
            [['email_id', 'attachment', 'sender_email'], 'string', 'max' => 255],
            [['subject'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email_id' => 'Email ID',
            'event_id' => 'Event ID',
            'status' => 'Status',
            'mail_method' => 'Mail Method',
            'subject' => 'Subject',
            'body' => 'Body',
            'attachment' => 'Attachment',
            'attempts' => 'Attempts',
            'sender_email' => 'Sender Email',
            'sent_on' => 'Sent On',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'status']);
    }
}
