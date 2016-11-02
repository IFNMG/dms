<?php

namespace app\models;

use Yii;

/**
  * This is the model class for table "{{%admin_personal}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $department_id
 * @property integer $sub_department_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $image_path
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property Lookups $subDepartment
 * @property Users $user
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property Lookups $department
 */
class AdminPersonal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
                return '{{%admin_personal}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'first_name', 'last_name', 'email', 'created_on', 'modified_on'], 'required'],
            [['user_id',  'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 30],
            [['email'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 15],
            [['phone'], 'string', 'min' => 10],
            [['image_path'], 'string', 'max' => 250],
            [['email'], 'unique'],
            ['department_id', 'required', 'message' => 'Please select department.'],
            ['sub_department_id', 'required', 'message' => 'Please select sub department.'],
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
            'department_id' => 'Department ID',
            'sub_department_id' => 'Sub Department ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'image_path' => 'Image Path',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'modified_on' => 'Modified On',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubDepartment()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'sub_department_id']);
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
    public function getDepartment()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'department_id']);
    }
}
