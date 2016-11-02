<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%configurations}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $short_code
 * @property string $hint
 * @property string $entity_type
 * @property integer $parent_id
 * @property integer $source_value
 * @property string $value
 * @property integer $auto_generate
 * @property integer $auto_generate_type
 * @property integer $section
 * @property integer $menu_section
 * @property integer $developer_only
 * @property integer $sort_order
 * @property integer $status
 * @property integer $is_delete
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 *
 * @property Lookups $status0
 * @property Users $createdBy
 * @property Users $modifiedBy
 * @property LookupTypes $sourceValue
 * @property Configurations $parent
 * @property Configurations[] $configurations
 * @property Lookups $autoGenerateType
 * @property Lookups $section0
 * @property Lookups $menuSection
 */
class Configurations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%configurations}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'short_code', 'value', 'menu_section', 'is_delete', 'created_on', 'modified_on'], 'required'],
            [['entity_type'], 'string'],
            [['parent_id', 'source_value', 'auto_generate', 'auto_generate_type', 'section', 'menu_section', 'developer_only','sort_order', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            [['name', 'short_code'], 'string', 'max' => 100],
            [['hint'], 'string', 'max' => 50],
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
            'name' => 'Name of config key',
            'short_code' => 'short code to read value',
            'hint' => 'Hint',
            'entity_type' => 'Entity Type',
            'parent_id' => 'Parent ID',
            'source_value' => 'in case of ddl',
            'value' => 'Value',
            'auto_generate' => '0 for false 1 for true',
            'auto_generate_type' => 'value from lookup',
            'section' => 'Section',
            'menu_section' => 'left menu relation',
            'developer_only' => '1 means Allow to developer user type only',
            'sort_order'=>'Sort Order',
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
    public function getSourceValue()
    {
        return $this->hasOne(LookupTypes::className(), ['id' => 'source_value']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Configurations::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigurations()
    {
        return $this->hasMany(Configurations::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutoGenerateType()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'auto_generate_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSection0()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'section']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuSection()
    {
        return $this->hasOne(Lookups::className(), ['id' => 'menu_section']);
    }
}
