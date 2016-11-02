<?php

namespace app\facades\dms;


use Yii;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use \app\models\Group;

class GroupFacade {

    public $messages; //stores an instance of the messages XML file.
 
    public function __construct() {
        $this->messages = CommonFacade::getMessages();
    }
    
    
    public function addMember($data){
        $CODES = new Codes;
        
        
        $model = New \app\models\Group();
        
        
        $model->attributes = $data['Group'];
        $model->is_delete = 1;
        $model->status = 550001;
        $model->created_by = Yii::$app->admin->adminId;
        $model->created_on = date('Y-m-d H:i:s');
        
        
        if ($model->save()) {
            $MSG = $this->messages->M135;
            $CODE = $CODES::SUCCESS;
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
        } else {
            $CODE = $CODES::ERROR;
            $errre = array_merge(array_values($model->firstErrors),array_values($model->firstErrors));
            $MSG = $errre[0];
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
        }
    }
    
    
    public function getGroupList(){
        $CODES = new Codes();   
        $list = \app\models\Lookups::find()->where(['is_delete'=>1, 'type'=>48])->all();
        if (!empty($list)) {
            $MSG ="";
            $CODE = $CODES::SUCCESS;
            $data = array('STATUS' => 'success', 'SUBDATA' => $list);
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);                
        } else {                
            $MSG = $this->messages->M121;
            $CODE = $CODES::VALIDATION_ERROR;
            $data = array('STATUS' => 'error', 'SUBDATA' => array());
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
        }
    }
    
    
    public function viewGroup($id){
        $CODES = new Codes();   
        $list = Group::find()->where(['is_delete'=>1, 'group_id'=>$id])->all();
        if (!empty($list)) {
            $MSG ="";
            $CODE = $CODES::SUCCESS;
            $data = array('STATUS' => 'success', 'SUBDATA' => $list);
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $list);                
        } else {                
            $MSG = $this->messages->M121;
            $CODE = $CODES::VALIDATION_ERROR;
            $data = array('STATUS' => 'error', 'SUBDATA' => array());
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
        }
    }
    
    
    public function deleteMember($id){
        $CODES = new Codes;
        if($id != ''){
            $model = Group::find()->where(['id'=>$id])->one();
            if($model){

                if($model->delete()){
                    $MSG = $this->messages->M120;
                    $CODE = $CODES::SUCCESS;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
                } else {
                    $MSG = $this->messages->M103;
                    $CODE = $CODES::ERROR;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
                }
            }
            
        }
    }
    
    
    public function viewRequest($id){
        $CODES = new Codes;
        if($id != ''){
            $model = AssessmentRegistration::find()->where(['id'=>$id])->one();
            if($model){
                $reasonList = \app\models\WhyTaxReturn::find()->where(['assessment_id'=>$id, 'is_delete'=>1])->all();
                $MSG = $this->messages->M117;
                $CODE = $CODES::SUCCESS;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>array('model'=>$model, 'reasonList'=>$reasonList));
            } else {
                $MSG = $this->messages->M103;
                $CODE = $CODES::ERROR;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>'');
            }
        }
    }
    
    public function viewList($data){        
        $CODES = new Codes();
        $draw = $data['start'];
        $length = $data['length'];
        $orderArr = $data['order'];
        
        $sortBy = $orderArr[0]['column'];
        if($orderArr[0]['dir'] == 'desc'){
            $order = SORT_DESC;
        } else if($orderArr[0]['dir'] == 'asc'){
            $order = SORT_ASC;
        }
        
        $term = $data['search']['value'];
        
        $name = $data['name'];
        $code = $data['code'];
        $status = $data['status'];
        
        
        $result1 = Vendor::find();
        $result1->where(['is_delete'=>1]);
        if($term != ''){
            $result1->andFilterWhere(['or', 
                ['like','assessment_registration.id', $term]]);
        }
        
        if($code != ''){
            $result1->andWhere(['code'=>$code]);
        }
        
        if($name != ''){
            $result1->andWhere(['name'=>$name]);
        }
        
        
        if($status != ''){
            $result1->andWhere(['status'=>$status]);
        }
        
        $total = $result1->count();
        
        
        
        $result = Vendor::find();
        $result->where(['is_delete'=>1]);
        if($code != ''){
            $result->andWhere(['code'=>$code]);
        }
        
        if($name != ''){
            $result->andWhere(['name'=>$name]);
        }
        
        
        if($status != ''){
            $result->andWhere(['status'=>$status]);
        }
        
        if($sortBy == 0){
            $result->orderBy(['code' =>$order]);
        } else if($sortBy == 1){
            $result->orderBy(['name' =>$order]);
        } else if($sortBy == 2){
            $result->orderBy(['status' =>$order]);
        }
        
        if($term != ''){
            $result->andFilterWhere(['or', 
            ['like','assessment_registration.id', $term]]);
        }
        $output = $result->offset($draw)->limit($length)->all();
        
        
        
        $finalArr = array();
        if($output){
            foreach($output as $model){
                $view = Yii::$app->getUrlManager()->createUrl(['index.php/dms/vendor/view', 'Id'=>$model->id]);
                $edit = Yii::$app->getUrlManager()->createUrl(['index.php/dms/vendor/edit', 'Id'=>$model->id]);
                $delete = Yii::getAlias('@web'). '/index.php/dms/vendor/delete';
                
                $action = '<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<a href="'.$edit.'">Edit</a>&nbsp;|&nbsp;<a style="cursor: pointer;" onclick=permanentDelete('.$model->id.','."'".$delete."'".','.$model->id.')>Delete</a>';
                
                array_push($finalArr, array(
                                        $model->code, 
                                        $model->name, 
                                        date("j M Y, h:i:s", strtotime($model->created_on)), 
                                        date("j M Y, h:i:s", strtotime($model->modified_on)), 
                                        $model->status0->value, 
                                        $action
                                    )
                        );
            }
        }
        $finalArray = array('recordsTotal'=>$total, 'recordsFiltered'=>$total, 'data'=>$finalArr);
        return ($finalArray);
    }
    
    
    
    public function addVendor($data){
        $CODES = new Codes;
        
        
        if($data['Vendor']['id'] != ''){
            $id = $data['Vendor']['id'];
            $model = Vendor::find()->where(['id'=>$id])->one();
            $model->modified_by = Yii::$app->admin->adminId;
            $model->modified_on = date('Y-m-d H:i:s');
        } else {
            $model = new Vendor();
            $model->created_by = Yii::$app->admin->adminId;
            $model->created_on = date('Y-m-d H:i:s');
        }
        $model->status = \app\web\util\Codes\LookupCodes::L_COMMON_STATUS_ENABLED;
        $model->is_delete = 1;
        $model->attributes = $data['Vendor'];
        
        if ($model->save()) {
            $MSG = $this->messages->M115;
            $CODE = $CODES::SUCCESS;
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
        } else {
            $MSG = $this->messages->M116;
            $CODE = $CODES::ERROR;
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
        }
    }
    
    
    public function editVendor($id){
        $CODES = new Codes;
        if($id != ''){
            $model = Vendor::find()->where(['id'=>$id])->one();
            
            
            if($model){
                $MSG = $this->messages->M155;
                $CODE = $CODES::SUCCESS;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
            } else {
                $MSG = $this->messages->M121;
                $CODE = $CODES::ERROR;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>'');
            }
        }
    }
    
    public function deleteVendor($id){
        $CODES = new Codes;
        if($id != ''){
            $model = Vendor::find()->where(['id'=>$id])->one();
            if($model){
                $model->is_delete = 0;
                $model->modified_by = Yii::$app->admin->adminId;
                $model->modified_on = date('Y-m-d H:i:s');

                if($model->save()){
                    $MSG = $this->messages->M120;
                    $CODE = $CODES::SUCCESS;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
                } else {
                    $MSG = $this->messages->M103;
                    $CODE = $CODES::ERROR;
                    return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
                }
            }
            
        }
    }
}