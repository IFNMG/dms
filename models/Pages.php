<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cc_pages".
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property string $image
 * @property integer $category
 * @property string $content
 * @property integer $layout
 * @property string $short_description
 * @property string $meta_title
 * @property string $meta_description
 * @property string $keywords
 * @property integer $sort_order
 * @property integer $showTitle
 * @property integer $is_delete
 * @property integer $status
 * @property integer $created_by
 * @property string $created_on
 * @property integer $modified_by
 * @property string $modified_on
 *
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property Lookups $category0
 * @property Lookups $layout0
 */
class Pages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cc_pages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'url', 'content', 'layout', 'is_delete', 'status', 'created_by', 'created_on'], 'required'],
            [['category', 'layout', 'sort_order', 'showTitle', 'is_delete', 'status', 'created_by', 'modified_by'], 'integer'],
            [['content', 'keywords'], 'string'],
            [['created_on', 'modified_on'], 'safe'],
            [['title', 'url', 'image', 'meta_title'], 'string', 'max' => 255],
            [['short_description', 'meta_description'], 'string', 'max' => 1000],
            [['image'], 'file', 'skipOnEmpty' => true, 
                'extensions' => 'png, jpg, jpeg', 
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
            'title' => 'Title',
            'url' => 'Url',
            'image' => 'Image',
            'category' => 'Category',
            'content' => 'Content',
            'layout' => 'Layout',
            'short_description' => 'Short Description',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'keywords' => 'Keywords',
            'sort_order' => 'Sort Order',
            'showTitle' => 'Show Title',
            'is_delete' => 'Is Delete',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_on' => 'Created On',
            'modified_by' => 'Modified By',
            'modified_on' => 'Modified On',
        ];
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
    public function getCategory0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'category']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLayout0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'layout']);
    }
}
