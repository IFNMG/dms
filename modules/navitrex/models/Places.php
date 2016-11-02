<?php

namespace app\modules\navitrex\models;

use Yii;

/**
 * This is the model class for table "places".
 *
 * @property integer $id
 * @property integer $poi_type
 * @property double $latitude
 * @property double $longitude
 * @property string $name
 * @property string $phone_number
 * @property string $opening_hour
 * @property string $closing_hour
 * @property integer $possibility_to_stay
 * @property integer $loading_dock
 * @property string $name_logistic_agent
 * @property double $rating
 * @property string $brand_icon
 * @property string $comments
 * @property integer $status
 * @property integer $is_delete
 * @property integer $created_by
 * @property string $created_on
 * @property integer $modified_by
 * @property string $modified_on
 *
 * @property FacilitiesAmenities[] $facilitiesAmenities
 * @property Lookups $poiType
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property Lookups $status0
 */
class Places extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'places';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['poi_type', 'latitude', 'longitude', 'name', 'status', 'is_delete', 'created_by', 'created_on'], 'required'],
            [['poi_type', 'possibility_to_stay', 'loading_dock', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['latitude', 'longitude', 'rating'], 'number'],
            [['created_on', 'modified_on'], 'safe'],
            [['name'], 'string', 'max' => 40],
            [['phone_number'], 'string', 'max' => 15, 'min'=>10],
            [['opening_hour', 'closing_hour', 'name_logistic_agent'], 'string', 'max' => 100],
            [['brand_icon'], 'string', 'max' => 255],
            [['comments'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'poi_type' => 'Poi Type',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'name' => 'Name',
            'phone_number' => 'Phone Number',
            'opening_hour' => 'Opening Hour',
            'closing_hour' => 'Closing Hour',
            'possibility_to_stay' => 'Possibility To Stay',
            'loading_dock' => 'Loading Dock',
            'name_logistic_agent' => 'Name Logistic Agent',
            'rating' => 'Rating',
            'brand_icon' => 'Brand Icon',
            'comments' => 'Comments',
            'status' => 'Status',
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
    public function getFacilitiesAmenities()
    {
        return $this->hasMany(FacilitiesAmenities::className(), ['place_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPoiType()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'poi_type']);
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
    public function getStatus0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'status']);
    }
}
