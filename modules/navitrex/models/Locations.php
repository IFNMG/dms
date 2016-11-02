<?php

namespace app\modules\navitrex\models;

use Yii;

/**
 * This is the model class for table "locations".
 *
 * @property integer $id
 * @property integer $type
 * @property string $image
 * @property integer $is_delete
 * @property integer $created_by
 * @property string $created_on
 * @property integer $modified_by
 * @property string $modified_on
 *
 * @property Lookups $type0
 */
class Locations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'locations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'is_delete', 'created_by', 'created_on'], 'required'],
            [['type', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            [['image'], 'string', 'max' => 255],
            ['image', 'required', 'message' => 'Please select the file.'],
            [['image'], 'file', 'skipOnEmpty' => true, 
                'extensions' => 'gpx', 
                'maxSize' => 2e+6, 
                'tooBig' => 'Limit is 2MB'
            ],
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
            'image' => 'Image',
            'is_delete' => 'Is Delete',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'type']);
    }
}
