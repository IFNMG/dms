<?php


namespace app\models;

use Yii;
use yii\base\Model;

/**
 * CityForm is the model behind the city form.
 */
class CityForm extends Model
{    
    public $id;
    public $value;
    public $zip_code;
    public $state_id;
    public $country_id;
    public $status;
    


    public function rules()
    {
        return [
            [['country_id', 'state_id', 'zip_code', 'status', 'is_delete', 'created_on', 'created_by', 'modified_on', 'modified_by'], 'required'],
            [['state_id', 'zip_code', 'status', 'is_delete', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            ['value', 'string', 'max' => 150,'tooLong'=>'Name should contain at most 150 characters.'],
            ['value','required','message'=>'Name cannot be blank.'],
            ['zip_code', 'match', 'pattern' => '/^[0-9]*$/','message'=>'Zip Code should contain numeric digits.'],
            ['zip_code', 'string', 'max' => 11,'tooLong'=>'Zip Code should contain at most 11 digits.'],           
            ['value','validateCity'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'state_id' => 'State ID',
            'zip_code' => 'Zip Code',
            'status' => 'Status',
            'is_delete' => 'Is Delete',
            'created_on' => 'Created On',
            'created_by' => 'Created By',
            'modified_on' => 'Modified On',
            'modified_by' => 'Modified By',
        ];
    }
    
     //validate
    
     /**
     * Validates the city.
     * This method serves as the inline validation for city.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateCity($attribute, $params)
    {
     
        if (!$this->hasErrors()) {
            
          $sql="SELECT COUNT(id) as count FROM cc_cities WHERE LOWER(value)='".  strtolower($this->value)."' AND is_delete='1' AND state_id='".$this->state_id."'";
            if($this->id!=""){    
                $sql.=" AND id!='".$this->id."'";
                }
                
                
         $connection = Yii::$app->getDb();           
         $command = $connection->createCommand($sql);
         $city = $command->queryAll();   
         $city=$city[0]['count'];
            if($city>0){
                $this->messages=  \app\facades\common\CommonFacade::getMessages();
                $msg=$this->messages->M128;
                $msg=  str_replace("{1}",  $this->value, $msg);
                $msg=  str_replace("{2}",  $this->state->value, $msg);
                $this->addError($attribute, $msg);
            }
        }     
    }
      
}