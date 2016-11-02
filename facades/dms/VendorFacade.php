<?php

namespace app\facades\dms;


use Yii;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use \app\models\Vendor;


class VendorFacade {

    public $messages; //stores an instance of the messages XML file.
 
    public function __construct() {
        $this->messages = CommonFacade::getMessages();
    }
    
    public function getVendorList(){
        $CODES = new Codes();   
        $list = Vendor::find()->where(['is_delete'=>1])->all();
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
        
        /*
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
        */
        
        
        
        $result = Vendor::find();
        $result->where(['is_delete'=>1]);
        if($code != ''){
            $result->andFilterWhere(['like', 'code', $code]);
            //$result->andWhere(['code'=>$code]);
        }
        
        if($name != ''){
            $result->andFilterWhere(['like', 'name', $name]);
            //$result->andWhere(['name'=>$name]);
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
        
        $total = $result->count();
        $output = $result->offset($draw)->limit($length)->all();
        
        
        
        $finalArr = array();
        if($output){
            foreach($output as $model){
                $view = Yii::$app->getUrlManager()->createUrl(['index.php/dms/vendor/view', 'Id'=>$model->id]);
                $edit = Yii::$app->getUrlManager()->createUrl(['index.php/dms/vendor/edit', 'Id'=>$model->id]);
                $delete = Yii::getAlias('@web'). '/index.php/dms/vendor/delete';
                
                $action = '<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<a href="'.$edit.'">Edit</a>&nbsp;|&nbsp;<a style="cursor: pointer;" onclick=permanentDelete('.$model->id.','."'".$delete."'".','.$model->id.')>Delete</a>';
                
                if($model->status  == 550002){
                    $change = '<select onchange="changeVendorStatus('.$model->id.');" id="vendor_status_change_'.$model->id.'" class="form-control"><option value="">--Select Status--</option><option value="550001">Enabled</option><option value="550002">Disabled</option></select>';
                } else {
                    $change = '<select onchange="changeVendorStatus('.$model->id.');" id="vendor_status_change_'.$model->id.'" class="form-control"><option value="">--Select Status--</option><option value="550001">Enabled</option><option  value="550002">Disabled</option></select>';
                }
                
                
                /*
                $changeTo = \app\web\util\Codes\LookupCodes::L_COMMON_STATUS_DISABLED;
                if($model->status ==  \app\web\util\Codes\LookupCodes::L_COMMON_STATUS_DISABLED){
                    $changeTo=  \app\web\util\Codes\LookupCodes::L_COMMON_STATUS_ENABLED;
                }
                if($model->status == \app\web\util\Codes\LookupCodes::L_COMMON_STATUS_ENABLED){
                    $temp = 'checked';
                } else {
                    $temp = '';
                }
                $path = Yii::$app->getUrlManager()->createUrl(["index.php/adminuser/subscriber/changestatus"]); ;
                $onclick = 'onchange=activateDeactivate('.$model->id.', '.$path.', '.$changeTo.', "")';
                $changeStatus = '<input id=toggle-event_'.$model->id.' type=checkbox '.$temp.' id=toggle-event data-toggle=toggle data-on="Enabled" data-off="Disabled" data-style="ios" '.$onclick.'>';
                */
                
                
                array_push($finalArr, array(
                                        $model->code, 
                                        $model->name, 
                                        '<label id=vendor_'.$model->id.'>'.$model->status0->value.'</label>', 
                                        $change,
                                        date("Y-m-d", strtotime($model->created_on)), 
                                        date("Y-m-d", strtotime($model->modified_on)), 
                                        $model->modifiedBy->adminPersonals->first_name.' '.$model->modifiedBy->adminPersonals->last_name,
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
            $model->status = \app\web\util\Codes\LookupCodes::L_COMMON_STATUS_ENABLED;
        }
        
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