<?php

namespace app\facades\dms;


use Yii;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use \app\models\Document;
use \app\models\AdminPersonal;

class DocumentFacade {

    public $messages; //stores an instance of the messages XML file.
 
    public function __construct() {
        $this->messages = CommonFacade::getMessages();
    }
    
    
     public function getReceiverList($userObj=NULL, $documentObj, $type=NULL){
        $receiverArr = array();
        
        //if($userObj->department_id == 2300001){ 
        //    $list = \app\models\AdminPersonal::find()->select(['email'])->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')->where(['department_id'=>$userObj->department_id, 'cc_users.role'=>array(100001, 100005), 'cc_users.status'=>550001])->all();
        //} else { 
        //    $list = \app\models\AdminPersonal::find()->select(['email'])->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')->where(['department_id'=>array($userObj->department_id, 2300001), 'cc_users.role'=>array(100001, 100005), 'cc_users.status'=>550001])->all();
        //}

        
        if($type == NULL){
        
            $list = \app\models\AdminPersonal::find()
                    ->select(['email', 'department_id'])
                    ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
                    ->where([
                            'cc_users.role'=>array(100001), 
                            'cc_users.status'=>550001, 'cc_users.is_delete'=>1])

                    ->andFilterWhere(['or', 
                        ['=','department_id', $documentObj->department_id],
                        ['=','department_id', 2300001]
                        ])

                    ->all();
            if(isset($list) && $list != ''){
                foreach($list as $ls){
                    if(!in_array($ls, $receiverArr)) {
                        array_push($receiverArr, $ls->email);
                    }
                }
            }
        }
        $alertList = \app\models\Alerts::find()->select(['email'])->where(['document_id'=>array($documentObj->old_id, $documentObj->id), 'status'=>550001])->all();
        if($alertList){
            foreach($alertList as $alert){
                if(!in_array($alert, $receiverArr)) {
                    array_push($receiverArr, $alert->email);
                }
            }
        }
        
        $createrEmail = $documentObj->createdBy->adminPersonals->email;
        
        if(!in_array($createrEmail, $receiverArr)) {
            array_push($receiverArr, $createrEmail);
        }
        
        $oldCreater = Document::find()->select(['created_by'])->where(['id'=>$documentObj->old_id, 'is_delete'=>1])->one();
        if($oldCreater){
            $createrEmailOld = $oldCreater->createdBy->adminPersonals->email;

            if(!in_array($createrEmailOld, $receiverArr)) {
                array_push($receiverArr, $createrEmailOld);
            }
        }
        return $receiverArr;
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
       
        $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
        
        $draw = $data['start'];
        $length = $data['length'];
        $orderArr = $data['order'];
        
        $sortBy = $orderArr[0]['column'];
        if($orderArr[0]['dir'] == 'desc'){
            $order = SORT_DESC;
        } else if($orderArr[0]['dir'] == 'asc'){
            $order = SORT_ASC;
        }
        
        
        $departmentF = $data['department'];
        $documentTypeF = $data['document_type'];
        $statusF = $data['document_status'];
        
        $validFromF =  $data['valid_from'];
        $validTillF = $data['valid_till'];
        
        $uploadedByF = $data['uploaded_by'];
        $vendorF = $data['vendor_id'];
        
        $result = Document::find();
        $result->where(['document.is_delete'=>1]);
        
        
        if($userObj->department_id != 2300001){
            if($departmentF != ''){
                $result->leftJoin('document_departments as dd', 'dd.document_id = document.id');
                $result->andWhere(['dd.department_id'=>$departmentF, 'dd.is_delete'=>1]);
            } else {
                $result->leftJoin('document_departments as dd', 'dd.document_id = document.id');
                $result->andFilterWhere(['or', 
                        ['=', 'document.created_by', $userObj->user_id], 
                        ['=','dd.department_id', $userObj->department_id]
                    ]
                );
                $result->andWhere(['dd.is_delete'=>1]);
            }
        } else {
            if($departmentF != ''){
                $result->leftJoin('document_departments as dd', 'dd.document_id = document.id');
                $result->andWhere(['dd.department_id'=>$departmentF, 'dd.is_delete'=>1]);
            }
        }
        
        /*
        if($userObj->department_id != 2300001){
            if($applicableToAll == 1){
                //$result->andWhere(['department_id'=>$userObj->department_id]);
                $result->andFilterWhere(['or', ['=', 'is_locked', 1], ['=','department_id', $userObj->department_id]]);
            } else {
                $result->andWhere(['department_id'=>$userObj->department_id]);
            }
        } else {
            if($departmentF != ''){
                $result->andWhere(['department_id'=>$departmentF]);
            }
        }
        */
        
        if($documentTypeF != ''){
            $result->andWhere(['document.document_type_id'=>$documentTypeF]);
        }
        
        
        if($vendorF != ''){
            $result->andWhere(['document.vendor_id'=>$vendorF]);
        }
        
        
        
        if($uploadedByF != ''){
            $result->andWhere(['LIKE', 'document.created_by_name', $uploadedByF]);
        }
        
        
        if($validFromF != ''){
            $validTillF = date("$validTillF 23:59:59");
            $result->andWhere(['>=','document.valid_from', $validFromF]);
        }
        
        if($validTillF != ''){
            $result->andWhere(['<=','document.valid_till', $validTillF]);
        }
        
        if($userObj->user->role != 100001 && $userObj->user->role != 100005){
            if($statusF != ''){
                if($statusF == 2600002){
                    $result->andWhere(['document.status'=>$statusF]);
                } else {
                    $result->andWhere(['document.status'=>$statusF, 'document.created_by'=>$userObj->user_id]);
                }
            } else {
                $result->andFilterWhere(['or', ['=', 'document.status', 2600002], ['=','document.created_by', $userObj->user_id]]);
            }
        } else {
            if($statusF != ''){
                $result->andWhere(['document.status'=>$statusF]);
            }
        }
        
        
        
        if($sortBy == 5){
            $result->orderBy(['document.status' =>$order]);
        } else if($sortBy == 0){
            $result->orderBy(['status' =>$order]);
            //$result->orderBy(['document.department_id' =>$order]);
        } else if($sortBy == 1){
            $result->orderBy(['document_type_id' =>$order]);
        } else if($sortBy == 2){
            $result->leftJoin('vendor as vr', 'vr.id = document.vendor_id');
            $result->orderBy(['vr.name' =>$order]);
        } else if($sortBy == 6){
            $result->orderBy(['document_type' =>$order]);
        } else if($sortBy == 7){
            $result->orderBy(['name' =>$order]);
        } else if($sortBy == 3){
            $result->orderBy(['created_by_name' =>$order]);
        } else if($sortBy == 4){
            $result->orderBy(['created_on' =>$order]);
        } else if($sortBy == 8){
            $result->orderBy(['version' =>$order]);
        } else {
            /*
            $sort = new \yii\data\Sort([
                'attributes' => [
                    'name' => [
                        'desc' => ['status' => SORT_DESC, 'document_type_id' => SORT_DESC],
                        'label' => 'Name',
                    ],
                ],
            ]);
            $result->orderBy($sort->orders);
            */
            
            $result->leftJoin('vendor as vr', 'vr.id = document.vendor_id');
            $result->orderBy(['status' =>$order]);
            $result->orderBy(['document_type_id' =>$order]);
            $result->orderBy(['vr.name' =>$order]);
            $result->orderBy(['created_on' =>$order]);
        }
        
        $total = $result->count();
        $output = $result->offset($draw)->limit($length)->all();
        
        $finalArr = array();
        
        if($output){
            foreach($output as $model){
                //if($model->status != 2600006){
                    $path = '';
                    $icon = '';
                    $isDelete = 0;
                    
                    $userName = '';
                    if($model->createdBy->adminPersonals){
                        $userName = $model->createdBy->adminPersonals->first_name.' '.$model->createdBy->adminPersonals->last_name;
                    }
                    
                    $alertText = 'Alert Me';
                    $alertObj = \app\models\Alerts::find()->where(['user_id'=>Yii::$app->admin->adminId, 'document_id'=>$model->id])->one();
                    if($alertObj){
                        if($alertObj->status == 550001){
                            $alertText = 'Stop Alerts';
                        }
                    }

                    if($userObj->user->role == 100004){
                        if($model->created_by == Yii::$app->admin->adminId && $model->status == 2600001){
                            $isDelete = 1;
                        }
                    } else if($userObj->user->role == 100001){
                        $isDelete = 1;
                    }

                    $view = Yii::$app->getUrlManager()->createUrl(['index.php/dms/document/view', 'Id'=>$model->id]);
                    $edit = Yii::$app->getUrlManager()->createUrl(['index.php/dms/document/edit', 'Id'=>$model->id]);
                    $delete = Yii::getAlias('@web'). '/index.php/dms/document/delete';
                    $archive = Yii::getAlias('@web'). '/index.php/dms/document/archive';
                    
                    if($model->status == 2600002){
                        $archive1 = '<a style="cursor: pointer;" onclick=permanentArchive('.$model->id.','."'".$archive."'".','.$model->id.')>Archive</a>&nbsp;|&nbsp;';
                    } else {
                        $archive1 = '<label>Archive</label>&nbsp;|&nbsp;';
                    }
                    
                    if($isDelete == 1){
                        $action = ''.$archive1.'<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<a href="'.$edit.'">Edit</a>&nbsp;|&nbsp;<a style="cursor: pointer;" onclick=permanentDelete('.$model->id.','."'".$delete."'".','.$model->id.')>Delete</a>&nbsp;|&nbsp;<a id=alert_'.$model->id.' style=cursor:pointer; onclick="alertMe('.$model->id.')">'.$alertText.'</a>';
                    } else if($isDelete == 0){
                        $action = ''.$archive1.'<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<a href="'.$edit.'">Edit</a>&nbsp;|&nbsp;<label>Delete</label>&nbsp;|&nbsp;<a id=alert_'.$model->id.' style=cursor:pointer; onclick="alertMe('.$model->id.')">'.$alertText.'</a>';
                    }

                    
                    if($userObj->department_id != 2300001){
                        if($model->department_id != $userObj->department_id){
                            $action = ''.$archive1.'<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<label>Edit</label>&nbsp;|&nbsp;<label>Delete</label>&nbsp;|&nbsp;<a id=alert_'.$model->id.' style=cursor:pointer; onclick="alertMe('.$model->id.')">'.$alertText.'</a>';
                        }
                    }

                    if($userObj->user->role == 100008){
                        $action = ''.$archive1.'<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<a id=alert_'.$model->id.' style=cursor:pointer; onclick="alertMe('.$model->id.')">'.$alertText.'</a>';
                    } 

                    
                    $filesize = CommonFacade::formatBytes($model->document_size);
                    $icon = $this->getIcon($model->document_type);
                    $path = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
                    //$path = \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/download", 'id'=>$model->id, 'type'=>'doc']);
                    $download = '<a target="_blank" download="'.$model->name.'" href="'.$path.'" style="cursor: pointer;" title="Click to download"><img width="35" height="40" alt="" src='.$icon.' style="margin-left: 18px;" /></a><br><label>'.$filesize.'</label>';

                    if($model->version != ''  && $model->version != 'draft'){
                        $version = $model->version.'.0';
                    } else {
                        $version = '';
                    }

                    $depFlag = 0;
                    $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                    if($selectedDepartmentList){
                        $selected = ''; 
                        foreach($selectedDepartmentList as $dep){
                            if($dep->department_id == $userObj->department_id){
                                $depFlag = 1;
                            }
                            $selected .= '<label>'.$dep->department->value.'</label><br>';
                        }
                    }
                    
                    $label = '';
                    if($model->document_type_id == 2500001 || $model->document_type_id == 2500004 || $model->document_type_id == 2500005){
                        $label = $model->vendor->name;
                    } else {
                        $label = $model->process_name;
                    }
                    
                    if($depFlag == 1 || $userObj->department_id == 2300001 || $model->created_by == $userObj->user_id){
                        array_push($finalArr, array(
                                $selected,
                                $model->documentType->value, 
                                $label, 
                                $userName, 
                                date("Y-m-d", strtotime($model->created_on)), 
                                $model->status0->value, 
                                $download, 
                                $model->name, 
                                '<label style="margin-left: 35%;">'.$version.'</label>', 
                                $action
                            )
                        );
                    }
                //}
            }
        }
        
        $finalArray = array('recordsTotal'=>$total, 'recordsFiltered'=>$total, 'data'=>$finalArr);
        return ($finalArray);
    }
    
    public function getIcon($type){
        $icon = '';
        if($type == 'application/vnd.oasis.opendocument.text' || $type == 'application/msword' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
            $icon = Yii::$app->request->baseUrl.'/images/word.png';
        } else if($type == 'application/pdf'){
            $icon = Yii::$app->request->baseUrl.'/images/pdf.png';
        } else if($type == 'image/png'){
            $icon = Yii::$app->request->baseUrl.'/images/png.png';
        } else if($type == 'image/jpeg' || $type == 'image/jpg'){
            $icon = Yii::$app->request->baseUrl.'/images/jpeg.png';
        } else if($type == 'application/vnd.ms-excel' || $type == 'application/vnd.oasis.opendocument.spreadsheet' || $type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
            $icon = Yii::$app->request->baseUrl.'/images/excel.png';
        } else if($type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.oasis.opendocument.presentation'){
            $icon = Yii::$app->request->baseUrl.'/images/ppt.png';
        } else {
            $icon = Yii::$app->request->baseUrl.'/images/default.png';
        }
        return $icon;
    }
    
    public function dashboardList($data){        
        $CODES = new Codes();
        $total = 0;
        $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
        
        $draw = $data['start'];
        $length = $data['length'];
        $orderArr = $data['order'];
        
        $sortBy = $orderArr[0]['column'];
        if($orderArr[0]['dir'] == 'desc'){
            $order = SORT_DESC;
        } else if($orderArr[0]['dir'] == 'asc'){
            $order = SORT_ASC;
        }
        
        
        $scenario = $data['scenario'];
        $term = $data['term'];
        
        if($scenario){
            if($scenario == 'term'){
                /*
                if($userObj->department_id != 2300001){
                    $department = "dd.department_id  = $userObj->department_id AND";
                } else {
                    $department = "";
                }
                
                if($userObj->user->role == 100004){
                    $statusRole = "(document.status =  2600002 OR document.created_by = $userObj->user_id) AND";
                    
                } else if($userObj->user->role == 100008){
                    $statusRole = "(document.status =  2600002) AND";
                } else if($userObj->user->role == 100001 || $userObj->user->role == 100005){
                    $statusRole = "";
                }
                 * 
                 */
                
                
                $result = Document::find();
                $result->where(['document.is_delete'=>1]);
        
                if($userObj->department_id != 2300001){
                    $result->leftJoin('document_departments as dd', 'dd.document_id = document.id');
                    $result->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1]);
                } else {
                    $result->leftJoin('document_departments as dd', 'dd.document_id = document.id');
                }
      
                
                if($userObj->user->role != 100001 && $userObj->user->role != 100005){
                    $result->andFilterWhere(['or', ['=', 'document.status', 2600002], ['=','document.created_by', $userObj->user_id]]);
                }
        
                $result->leftJoin('cc_lookups as lk', 'lk.id = dd.department_id');
                $result->leftJoin('cc_lookups as lk1', 'lk1.id = document.status');
                $result->leftJoin('cc_lookups as lk2', 'lk2.id = document.document_type_id');
                $result->leftJoin('vendor as vn', 'vn.id = document.vendor_id');
                
                $result->andFilterWhere(['or', 
                    ['LIKE', 'document.document_text', $term], 
                    ['LIKE', 'document.comments', $term], 
                    ['LIKE', 'document.policy_header', $term], 
                    ['LIKE', 'document.scope_of_work', $term], 
                    ['LIKE', 'document.name', $term], 
                    ['LIKE','document.created_by_name', $term],
                    ['LIKE','lk.value', $term],
                    ['LIKE','lk1.value', $term],
                    ['LIKE','lk2.value', $term],
                    ['LIKE','vn.code', $term],
                    ['LIKE','vn.name', $term],
                ]);
                
                $output = $result->limit($length)->all();
                $total = $result->count();
                /*
                
                $query = 'SELECT document.id, document.created_by, '
                        . 'document.created_by_name, document.created_on, document.status, document.name, '
                        . 'document.version, document.department_id, document.document_size, '
                        . 'document.document_type_id, document.document_path, '
                        . 'document.document_type '
                        . 'FROM document '
                        . 'LEFT JOIN vendor as vn ON vn.id = document.vendor_id '
                        . 'LEFT JOIN document_departments as dd ON dd.document_id = document.id '
                        . 'LEFT JOIN cc_lookups as lk ON lk.id = dd.department_id '
                        . 'LEFT JOIN cc_lookups as lk1 ON lk1.id = document.status '
                        . 'LEFT JOIN cc_lookups as lk2 ON lk2.id = document.document_type_id '
                        . 'WHERE '.$statusRole.' '.$department.' '
                        . 'document.is_delete = 1 '
                        . 'AND MATCH(document.document_text, document.name, document.comments, document.policy_header, '
                        . 'document.created_by_name, document.process_name, document.scope_of_work, document.policy_header, '
                        . 'document.document_type, document.version, lk.value, lk1.value, lk2.value, vn.name, vn.code) '
                        . 'AGAINST('."'".$term."'".' IN BOOLEAN MODE)'
                        . 'LIMIT '.$length;
                        
                $query1 = 'SELECT document.id, document.created_by, '
                        . 'document.created_by_name, document.created_on, document.status, document.name, '
                        . 'document.version, document.department_id, '
                        . 'document.document_type_id, document.document_path, '
                        . 'document.document_type '
                        . 'FROM document '
                        . 'LEFT JOIN vendor as vn ON vn.id = document.vendor_id '
                        . 'LEFT JOIN document_departments as dd ON dd.document_id = document.id '
                        . 'LEFT JOIN cc_lookups as lk ON lk.id = dd.department_id '
                        . 'LEFT JOIN cc_lookups as lk1 ON lk1.id = document.status '
                        . 'LEFT JOIN cc_lookups as lk2 ON lk2.id = document.document_type_id '
                        . 'WHERE '.$statusRole.' '.$department.' '
                        . 'document.is_delete = 1 '
                        . 'AND MATCH(document_text, document.name, comments, policy_header, '
                        . 'created_by_name, process_name, scope_of_work, policy_header, '
                        . 'document_type, version, lk.value, lk1.value, lk2.value, vn.name, vn.code) '
                        . 'AGAINST('."'".$term."'".' IN BOOLEAN MODE)';
                
                $output = Document::findBySql($query)->all();
                $total = Document::findBySql($query1)->count();
                */
                
            } else if($scenario == 'agreement'){
                if($userObj->user->role == 100008){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>array(2500001, 2500004), 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>array(2500001, 2500004), 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100004){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>array(2500001, 2500004), 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>array(2500001, 2500004), 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100001){
                    if($userObj->department_id == 2300001){
                        $output = Document::find()->where(['is_delete'=>1, 'document_type_id'=>array(2500001, 2500004), 'status'=>2600001 ])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['is_delete'=>1, 'document_type_id'=>array(2500001, 2500004), 'status'=>2600001 ])->count();
                    } else {
                        $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>array(2500001, 2500004), 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>array(2500001, 2500004), 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                    }
                }
            } else if($scenario == 'po'){
                if($userObj->user->role == 100008){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500005, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500005, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100004){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500005, 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500005, 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100001){
                    if($userObj->department_id == 2300001){
                        $output = Document::find()->where(['is_delete'=>1, 'document_type_id'=>2500005, 'status'=>2600001 ])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['is_delete'=>1, 'document_type_id'=>2500005, 'status'=>2600001 ])->count();
                    } else {
                        $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500005, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500005, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                    }
                }
            } else if($scenario == 'policy'){
                if($userObj->user->role == 100008){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500002, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500002, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100004){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500002, 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500002, 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100001){
                    if($userObj->department_id == 2300001){
                        $output = Document::find()->where(['is_delete'=>1, 'document_type_id'=>2500002, 'status'=>2600001 ])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['is_delete'=>1, 'document_type_id'=>2500002, 'status'=>2600001 ])->count();
                    } else {
                        $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500002, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500002, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                    }
                }
            } else if($scenario == 'sop'){
                if($userObj->user->role == 100008){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500003, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500003, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100004){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500003, 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500003, 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100001){
                    if($userObj->department_id == 2300001){
                        $output = Document::find()->where(['is_delete'=>1, 'document_type_id'=>2500003, 'status'=>2600001 ])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['is_delete'=>1, 'document_type_id'=>2500003, 'status'=>2600001 ])->count();
                        
                    } else {
                        $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500003, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500003, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                    }
                }
            } else if($scenario == 'expiring'){
                if($userObj->user->role == 100008){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')])->count();
                } else if($userObj->user->role == 100004){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document.status'=>2600002, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document.status'=>2600002, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')])->count();
                } else {
                    if($userObj->department_id == 2300001){
                        $output = Document::find()->where(['is_delete'=>1, 'status'=>2600001])->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['is_delete'=>1, 'status'=>2600001])->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')])->count();
                    } else {
                        $output = Document::find()->where(['document.is_delete'=>1, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['document.is_delete'=>1, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')])->count();
                    }
                }
            } else {
                if($userObj->user->role == 100008){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500001, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500001, 'document.status'=>2600002])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100004){
                    $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500001, 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                    $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500001, 'document.status'=>2600001, 'document.created_by'=>$userObj->user_id])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                } else if($userObj->user->role == 100001){
                    if($userObj->department_id == 2300001){
                        $output = Document::find()->where(['is_delete'=>1, 'document_type_id'=>2500001, 'status'=>2600001 ])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['is_delete'=>1, 'document_type_id'=>2500001, 'status'=>2600001 ])->count();
                    } else {
                        $output = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500001, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->offset($draw)->limit($length)->all();
                        $total = Document::find()->where(['document.is_delete'=>1, 'document_type_id'=>2500001, 'document.status'=>2600001])->leftJoin('document_departments as dd', 'dd.document_id = document.id')->andWhere(['dd.department_id'=>$userObj->department_id, 'dd.is_delete'=>1])->count();
                    }
                }
            }
        }
        
        
        
        $finalArr = array();
        
        if($output){
            
            foreach($output as $model){
                //if($model->status != 2600006){
                    
                    $path = '';
                    $icon = '';
                    $isDelete = 0;

                    if($userObj->user->role == 100004){
                        if($model->created_by == Yii::$app->admin->adminId && $model->status == 2600001){
                            $isDelete = 1;
                        }
                    } else if($userObj->user->role == 100001){
                        $isDelete = 1;
                    }

                    $view = Yii::$app->getUrlManager()->createUrl(['index.php/dms/document/view', 'Id'=>$model->id]);
                    $edit = Yii::$app->getUrlManager()->createUrl(['index.php/dms/document/edit', 'Id'=>$model->id]);
                    $delete = Yii::getAlias('@web'). '/index.php/dms/document/delete';


                    $alertText = 'Alert Me';
                    $alertObj = \app\models\Alerts::find()->where(['user_id'=>Yii::$app->admin->adminId, 'document_id'=>$model->id])->one();
                    if($alertObj){
                        if($alertObj->status == 550001){
                            $alertText = 'Stop Alerts';
                        }
                    }


                    if($isDelete == 1){
                        $action = '<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<a href="'.$edit.'">Edit</a>&nbsp;|&nbsp;<a style="cursor: pointer;" onclick=permanentDelete('.$model->id.','."'".$delete."'".','.$model->id.')>Delete</a>&nbsp;|&nbsp;<a id=alert_'.$model->id.' style=cursor:pointer; onclick="alertMe('.$model->id.')">'.$alertText.'</a>';
                    } else if($isDelete == 0){
                        $action = '<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<a href="'.$edit.'">Edit</a>&nbsp;|&nbsp;<label>Delete</label>&nbsp;|&nbsp;<a id=alert_'.$model->id.' style=cursor:pointer; onclick="alertMe('.$model->id.')">'.$alertText.'</a>';
                    }

                    if($userObj->user->role == 100008){ // for view rights only
                        $action = '<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<a id=alert_'.$model->id.' style=cursor:pointer; onclick="alertMe('.$model->id.')">'.$alertText.'</a>';
                    }

                    if($userObj->department_id != 2300001){
                        if($model->department_id != $userObj->department_id){
                            $action = '<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<label>Edit</label>&nbsp;|&nbsp;<label>Delete</label>&nbsp;|&nbsp;<a id=alert_'.$model->id.' style=cursor:pointer; onclick="alertMe('.$model->id.')">'.$alertText.'</a>';
                        }
                    }

                    $userName = '';
                    if($model->createdBy->adminPersonals){
                        $userName = $model->createdBy->adminPersonals->first_name.' '.$model->createdBy->adminPersonals->last_name;
                    }

                    $filesize = CommonFacade::formatBytes($model->document_size);
                    $icon = $this->getIcon($model->document_type);

                    $path = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
                    //$path = \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/download", 'id'=>$model->id, 'type'=>'doc']);

                    $download = '<a target="_blank" download="'.$model->name.'" href="'.$path.'" style="cursor: pointer;" title="Click to download"><img width="35" height="40" alt="" src='.$icon.' style="margin-left: 18px;" /></a><label>'.$filesize.'</label>';

                    $statusVal = \app\models\Lookups::find()->select('value')->where(['id'=>$model->status])->one();


                    if($model->version != ''  && $model->version != 'draft'){
                        $version = $model->version.'.0';
                    } else {
                        $version = '';
                    }
                    
                    $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                    if($selectedDepartmentList){
                        $selected = ''; 
                        foreach($selectedDepartmentList as $dep){
                            $selected .= '<label>'.$dep->department->value.'</label><br>';
                        }
                    }

                    $label = '';
                    if($model->document_type_id == 2500001 || $model->document_type_id == 2500004 || $model->document_type_id == 2500005){
                        $label = $model->vendor->name;
                    } else {
                        $label = $model->process_name;
                    }
                    
                    array_push($finalArr, array(
                                            $selected,
                                            $model->documentType->value, 
                                            $label, 
                                            $userName,
                                            date("Y-m-d", strtotime($model->created_on)), 
                                            $statusVal->value, 
                                            $download, 
                                            $model->name, 
                                            $version, 
                                            $action
                                        )
                            );
                //}
            }
        }
        $finalArray = array('recordsTotal'=>  $total, 'recordsFiltered'=>$total, 'data'=>$finalArr);
        return ($finalArray);
    }
    
    
    
    public function rollBack($id){
        $CODES = new Codes;
        $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id', 'first_name', 'last_name', 'email'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
        $model = Document::find()->where(['id'=>$id, 'is_delete'=>1])->one();
        
        if($model){
            
            if($model->old_id != ''){
                $oldId = $model->old_id;
            } else {
                $oldId = $model->id;
            }
                    
            $new = new Document();
            $new->old_id = $oldId;
            
            $new->name = $model->name;
            $new->department_id = $model->department_id;
            $new->document_type_id = $model->document_type_id;
            $new->comments = $model->comments;
            $new->vendor_id = $model->vendor_id;
            $new->process_name = $model->process_name;
            $new->valid_from = $model->valid_from;
            $new->valid_till = $model->valid_till;
            $new->scope_of_work = $model->scope_of_work;
            $new->payment_terms = $model->payment_terms;
            $new->fee = $model->fee;
            $new->policy_header = $model->policy_header;
            $new->document_path = $model->document_path;
            $new->document_text = $model->document_text;
            $new->document_size = $model->document_size;
            $new->document_type = $model->document_type;
            $new->document = $model->document;
            $new->version = 'draft';
            $new->status = 2600001;
            $new->is_delete = 1;
            $new->created_by = Yii::$app->admin->adminId;
            if($userObj){
                $createdByName = $userObj->first_name.' '.$userObj->last_name;
                $new->created_by_name = $createdByName;
            }
            $new->created_on = date('Y-m-d H:i:s');
            
            
            if ($new->save()) {
                $MSG = $this->messages->M159;
                $CODE = $CODES::SUCCESS;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
            } else {
                $MSG = $this->messages->M160;
                $CODE = $CODES::ERROR;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
            }
            
        } else {
            $MSG = $this->messages->M160;
            $CODE = $CODES::ERROR;
            return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
        }
        
    }
    
    
    public function editDocument($id){
        $CODES = new Codes;
        $selectedDepartmentArr = array();
        if($id != ''){
            $model = Document::find()->where(['id'=>$id])->one();
            $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
            
            foreach($selectedDepartmentList as $obj){
                array_push($selectedDepartmentArr, $obj->department_id);
            }
            
            if($model){
                $MSG = $this->messages->M155;
                $CODE = $CODES::SUCCESS;
                $data = array('model'=>$model, 'selectedDepartmentArr'=>$selectedDepartmentArr);
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $data);
            } else {
                $MSG = $this->messages->M121;
                $CODE = $CODES::ERROR;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>'');
            }
        }
    }
    
    
    public function viewDocument($id){
        $CODES = new Codes;
        $versionArray = array();
        $addendumArray = array();
        $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
        
        if($id != ''){
            $model = Document::find()->where(['id'=>$id, 'is_delete'=>1])->one();
            
            if($model->old_id != ''){
                $parent = Document::find()->select(['name'])->where(['id'=>$model->old_id, 'is_delete'=>1])->one();
                if($parent){
                    $model->is_locked = $parent->name;
                }
            }
            $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
            
            
            if($model->document_type_id == 2500001){
                $addendumList = Document::find()->select(['id', 'old_id', 'document_type', 'name', 'version', 'document_path', 'status', 'created_by', 'created_on'])->where(['is_delete'=>1, 'old_id'=>$model->id, 'document_type_id'=>2500004])->all();
                if($addendumList){
                    $addendumArray = $addendumList;
                }
            }
            
            if($model->document_type_id == 250001){
                if($userObj->user->role == 100001 || $userObj->user->role == 100004 || $userObj->user->role == 100005){
                    //if($userObj->user_id == $model->created_by){
                        $versionObj = Document::find()->select(['id', 'old_id', 'document_type', 'name', 'version', 'document_path', 'status', 'created_by', 'created_on'])->where(['document_type_id'=>$model->document_type_id])->andFilterWhere(['or', ['=', 'id', $model->id],['=', 'old_id', $model->id],])->all();
                        if($model->old_id != ''){
                            $versionObj1 = Document::find()->select(['id', 'old_id', 'document_type', 'name', 'version', 'document_path', 'status', 'created_by', 'created_on'])->where(['document_type_id'=>$model->document_type_id])->andFilterWhere(['or', ['=', 'id', $model->old_id],['=', 'old_id', $model->old_id],])->all();
                        }
                    //}
                }
                if($versionObj){
                    foreach($versionObj as $obj){
                        if($obj->id != $id){
                            if($obj->version != 'draft'){
                                array_push($versionArray, $obj);
                            }
                        }
                    }
                }
                if($versionObj1){
                    foreach($versionObj1 as $obj){
                        if($obj->id != $id){
                            if($obj->version != 'draft'){
                                array_push($versionArray, $obj);
                            }
                        }
                    }
                }
            }
            
            if($model){
                $MSG = $this->messages->M155;
                $CODE = $CODES::SUCCESS;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>array('DOCUMENT'=>$model, 'VERSION_LIST'=>$versionArray, 'SELECTED_DEPARTMENT'=>$selectedDepartmentList, 'ADDENDUM_LIST'=>$addendumArray));
            } else {
                $MSG = $this->messages->M121;
                $CODE = $CODES::ERROR;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>'');
            }
        }
    }
    
   
    
    public function updateDocumentStatus($request, $parentObj=NULL){
        $CODES = new Codes;
        
        $id = $request['id'];
        $status = $request['status'];
        $reason = $request['reason'];
        
        $version = 0;
        if($id != ''){
            $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id', 'first_name', 'last_name', 'email'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
            
            $model = Document::find()->where(['id'=>$id])->one();
            
            if($model){
                
                if($status == 2600002){
                    $model->status = 2600002;
                    $model->approved_by = Yii::$app->admin->adminId;
                    $model->approved_on = date('Y-m-d H:i:s');
                    
                    
                        if($model->old_id != ''){
                            $oldVersionList = Document::find()
                                                ->andWhere(['!=','version', 'draft'])
                                                ->andFilterWhere(['or', 
                                                    ['=', 'id', $model->old_id],
                                                    ['=', 'old_id', $model->old_id]])
                                                ->all();

                            $max = Document::find()
                                                ->andWhere(['!=','version', 'draft'])
                                                ->andFilterWhere(['or', 
                                                    ['=', 'id', $model->old_id],
                                                    ['=', 'old_id', $model->old_id]])
                                                ->max('version');


                        } else {
                            $oldVersionList = Document::find()
                                                ->andWhere(['!=','version', 'draft'])
                                                ->andFilterWhere(['or', 
                                                    ['=', 'id', $model->id],
                                                    ['=', 'old_id', $model->id]])
                                                ->all();

                            $max = Document::find()
                                                ->andWhere(['!=','version', 'draft'])
                                                ->andFilterWhere(['or', 
                                                    ['=', 'id', $model->id],
                                                    ['=', 'old_id', $model->id]])
                                                ->max('version');

                        }
                        
                        if($model->document_type_id == 2500004){
                            foreach($oldVersionList as $old){
                                if($old->document_type_id == 2500004){
                                    if($version < $old->version){
                                        $version = $old->version;
                                    }
                                    if($old->version != '' && $old->version != 'draft'){
                                        $old->status = 2600006;
                                        $old->save();
                                    }
                                }
                            }
                            $version = (int)$version+1;
                            $model->version = (string)$version;
                        } else {
                            foreach($oldVersionList as $old){
                                $old->status = 2600006;
                                $old->save();
                            }
                            $version = (int)$max+1;
                            $model->version = (string)$version;
                            
                        }
                        $model->status = 2600002;
                        
                        $model->save();
                    
                } else if($status == 2600003){
                    $model->status = 2600003;
                    $model->reason = $reason;
                    $model->modified_by = Yii::$app->admin->adminId;
                    $model->modified_on = date('Y-m-d H:i:s');
                }
                

                if($model->save()){
                    $event = 950008;
                    if($model->status == 2600002){
                        $event = 950008;
                    } else if($model->status == 2600003){
                        $event = 950009;
                    }
                    $type = 'Approved';
                    $receiverList = $this->getReceiverList($userObj, $model, $type);
                    
                    if($receiverList){
                        if($model->document_type_id == 2500001 || $model->document_type_id == 2500004 || $model->document_type_id == 2500005){
                            $docVenName = 'Vendor Name: '.$model->vendor->name;
                        } else {
                            $docVenName = 'Document Name: '.$model->name;
                        }
                        $emailObj = array('OTHER1'=>$docVenName, 'OTHER2'=>$userObj->first_name.' '.$userObj->last_name, 'OTHER3'=>$model->reason);
                        foreach($receiverList as $receiver){
                            $mailfacade = new \app\facades\common\MailFacade();
                            $mailfacade->sendEmail($emailObj, $receiver, $event, 1050001);
                        }
                    }
                    
                    /*
                    if($parentObj != ''){
                        $receiverList1 = $this->getReceiverList($userObj, $parentObj);
                        if($receiverList1){
                            $emailObj1 = array('OTHER1'=>$parentObj->name, 'OTHER2'=>$parentObj->approvedBy->adminPersonals->email);
                            foreach($receiverList1 as $receiver){
                                $mailfacade1 = new \app\facades\common\MailFacade();
                                $mailfacade1->sendEmail($emailObj1, $receiver, 950008, 1050001);
                            }
                        }
                    }
                     * 
                     */
                    
                    
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
    
    
    
    public function alertMe($data){
        $CODES = new Codes;
        
        $id = $data['id'];
        $text = 'Alert Me';
        
        if($id != ''){
            $userObj = \app\models\AdminPersonal::find()->select(['email'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
            $obj = \app\models\Alerts::find()->where(['user_id'=>Yii::$app->admin->adminId, 'document_id'=>$id])->one();
            if($obj){
                $model = $obj;
                $model->modified_by = Yii::$app->admin->adminId;
                $model->modified_on = date('Y-m-d H:i:s');
                if($model->status == 550001){
                    $model->status = 550002;
                    $text = 'Alert Me';
                } else {
                    $model->status = 550001;
                    $text = 'Stop Alerts';
                }
            } else {
                $model = new \app\models\Alerts();
                $model->created_by = Yii::$app->admin->adminId;
                $model->created_on = date('Y-m-d H:i:s');
                $model->status = 550001;
                $text = 'Stop Alerts';
            }
            
            $model->email = $userObj->email;
            $model->document_id = $id;
            $model->user_id = Yii::$app->admin->adminId;
            
            if($model->save()){
                if($model->status == 550001){
                    $MSG = $this->messages->M156;
                } else {
                    $MSG = $this->messages->M157;
                }
                
                $CODE = $CODES::SUCCESS;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $text);
            } else {
                $MSG = $this->messages->M158;
                $CODE = $CODES::ERROR;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' => $model);
            }
            
            
        }
    }
    
    
    public function archiveDocument($id){
        $CODES = new Codes;
        
        if($id != ''){
            $userObj = \app\models\AdminPersonal::find()->select(['email', 'first_name', 'last_name'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
            $model = Document::find()->where(['id'=>$id])->one();
            if($model){
                $model->status = 2600006;
                $model->modified_by = Yii::$app->admin->adminId;
                $model->modified_on = date('Y-m-d H:i:s');

                if($model->save()){
                    $receiverList = $this->getReceiverList($userObj, $model);
                    
                    if($receiverList){
                        
                        if($model->document_type_id == 2500001 || $model->document_type_id == 2500004 || $model->document_type_id == 2500005){
                            $docVenName = 'Vendor Name: '.$model->vendor->name;
                        } else {
                            $docVenName = 'Document Name: '.$model->name;
                        }
                    
                        $selected = ''; 
                        $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                        if($selectedDepartmentList){
                            
                            foreach($selectedDepartmentList as $dep){
                                $selected .= $dep->department->value.', ';
                            }
                        }
                        
                        $emailObj = array(
                            'NAME'=>$docVenName, 
                            'DEPARTMENT'=>$selected,
                            'TYPE'=>$model->documentType->value,
                            'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name,
                            'USER.FIRSTNAME'=>$userObj->first_name,
                            'USER.LASTNAME'=>$userObj->last_name,
                        );
                        
                        
                        foreach($receiverList as $receiver){
                            $mailfacade = new \app\facades\common\MailFacade();
                            $mailfacade->sendEmail($emailObj, $receiver, 950012, 1050001);
                        }
                    }
                    
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
    
    public function deleteDocument($id){
        $CODES = new Codes;
        
        if($id != ''){
            $userObj = \app\models\AdminPersonal::find()->select(['email', 'first_name', 'last_name'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
            $model = Document::find()->where(['id'=>$id])->one();
            if($model){
                $model->is_delete = 0;
                $model->modified_by = Yii::$app->admin->adminId;
                $model->modified_on = date('Y-m-d H:i:s');

                if($model->save()){
                    $receiverList = $this->getReceiverList($userObj, $model);
                    
                    if($receiverList){
                        if($model->document_type_id == 2500001 || $model->document_type_id == 2500004 || $model->document_type_id == 2500005){
                            $docVenName = 'Vendor Name: '.$model->vendor->name;
                        } else {
                            $docVenName = 'Document Name: '.$model->name;
                        }
                        
                        $selected = ''; 
                        $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                        if($selectedDepartmentList){
                            
                            foreach($selectedDepartmentList as $dep){
                                $selected .= $dep->department->value.' ';
                            }
                        }
                    
                        $emailObj = array(
                            'NAME'=>$docVenName, 
                            'DEPARTMENT'=>$selected,
                            'TYPE'=>$model->documentType->value,
                            'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name,
                            'USER.FIRSTNAME'=>$userObj->first_name,
                            'USER.LASTNAME'=>$userObj->last_name,
                        );
                        
                        
                        foreach($receiverList as $receiver){
                            $mailfacade = new \app\facades\common\MailFacade();
                            $mailfacade->sendEmail($emailObj, $receiver, 950011, 1050001);
                        }
                    }
                    
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