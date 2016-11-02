<?php

namespace app\modules\navitrex\models;

use Yii;

/**
 * This is the model class for table "facilities_amenities".
 *
 * @property integer $id
 * @property integer $place_id
 * @property integer $facility_id
 * @property integer $is_delete
 * @property integer $created_by
 * @property string $created_on
 * @property integer $modified_by
 * @property string $modified_on
 *
 * @property Users $modifiedBy
 * @property Places $place
 * @property Lookups $facility
 * @property Users $createdBy
 */
class FacilitiesAmenities extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'facilities_amenities';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['place_id', 'facility_id', 'is_delete', 'created_by', 'created_on'], 'required'],
            [['place_id', 'facility_id', 'is_delete', 'created_by', 'modified_by'], 'integer'],
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
            'place_id' => 'Place ID',
            'facility_id' => 'Facility ID',
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
    public function getModifiedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'modified_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlace()
    {
        return $this->hasOne(Places::className(), ['id' => 'place_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacility()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'facility_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }
}
