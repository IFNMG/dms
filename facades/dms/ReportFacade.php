<?php

namespace app\facades\dms;


use Yii;
use app\facades\common\CommonFacade;
use app\web\util\Codes\Codes;
use \app\models\Document;
use \app\models\AdminPersonal;

class ReportFacade {

    public $messages; //stores an instance of the messages XML file.
 
    public function __construct() {
        $this->messages = CommonFacade::getMessages();
    }
    
    public function addDocument($data){
        $text = '';
            
            if($_FILES["Document"]["tmp_name"]["document_path"] != ''){
                $tmpName = $_FILES["Document"]["tmp_name"]["document_path"];
                $name = $_FILES["Document"]["name"]["document_path"];
                $fileSize = $_FILES["Document"]['size']["document_path"];
                $fileType = $_FILES["Document"]['type']["document_path"];

                
                
                ////////////////////////////////////////////////////
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';
                for ($i = 0; $i < 10; $i++) {
                    $randomString .= $characters[rand(0, strlen($characters) - 1)];
                }
                
                $randomString .= time();
                
                $dir = Yii::getAlias('@app') . '/web/uploads/';
                if(!is_dir($dir)){
                   mkdir("$dir", 0777);
                }
                ///////////////////////////////////////////////////

                $fp = fopen($tmpName, 'r');
                $content1 = fread($fp, filesize($tmpName));
                $content1 = addslashes($content1);
                fclose($fp);
                if(!get_magic_quotes_gpc()){
                    $name1 = addslashes($name);
                }


                move_uploaded_file($tmpName, $dir.$randomString.$name);

                $filename = $dir.$randomString.$name;

                if($fileType == 'application/msword' || $fileType == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
                    $striped_content = '';
                    $content = '';

                    if($filename && file_exists($filename)){ 
                        $zip = zip_open($filename);
                        if ($zip && (!is_numeric($zip))) {
                            while ($zip_entry = zip_read($zip)) {
                                if (zip_entry_open($zip, $zip_entry) == FALSE){ 
                                    continue;
                                }
                                if (zip_entry_name($zip_entry) != "word/document.xml"){ 
                                    continue;
                                }
                                $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                                zip_entry_close($zip_entry);
                            }
                            zip_close($zip);      
                            $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
                            $content = str_replace('</w:r></w:p>', "\r\n", $content);
                            $text = strip_tags($content);
                        } else {
                            $this->redirect(Yii::$app->urlManager->createUrl("site/list"));
                        }
                    } else {
                        $this->redirect(Yii::$app->urlManager->createUrl("site/list"));
                    }
                }


                if($fileType == 'application/pdf'){
                    $vendorDir =   \Yii::getAlias('@webroot').'/PDF2TEXT';
                    require_once($vendorDir.'/PDF2Text.php');
                    $a = new \PDF2Text();
                    $a->setFilename($filename);
                    $a->decodePDF();
                    $text = $a->output();
                }


                if($fileType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $fileType == 'application/vnd.ms-excel'  || $fileType == 'application/vnd.oasis.opendocument.spreadsheet'){

                    $vendorDir =   \Yii::getAlias('@webroot').'/spreadsheet-reader-master';
                    require($vendorDir.'/php-excel-reader/excel_reader2.php');
                    require($vendorDir.'/SpreadsheetReader.php');
                    $Reader = new \SpreadsheetReader($dir.$randomString.$name);
                    foreach ($Reader as $key=>$row){
                        //echo "<pre>";print_r($row);
                        $text .= implode(', ', $row);

                    }
                }


                $model->name = $randomString.$name;
                $model->content = $text;
                $model->type = $fileType;
                $model->created_on = date('Y-m-d H:i:s');
                $model->created_on = date('Y-m-d H:i:s');

                if($model->save()){
                    $fileObj = new \app\models\Files();
                    $fileObj->name = $name1;
                    $fileObj->document_id = $model->id;
                    $fileObj->type = $fileType;
                    $fileObj->size = $fileSize;
                    $fileObj->content = $content1;
                    $fileObj->save();
                }
            } 
            
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
        $vendorF = $data['vendor'];
        
        $validFromF =  $data['valid_from'];
        $validTillF = $data['valid_till'];
        $policyHeaderF = $data['policy_header'];
        $processNameF = $data['process_name'];
        
        
        $result = Document::find();
        $result->where(['document.is_delete'=>1, 'document.status'=>2600002]);
        
        
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
            $result->andWhere(['department_id'=>$userObj->department_id]);
        } else {
            if($departmentF != ''){
                $result->andWhere(['department_id'=>$departmentF]);
            }
        }
         * 
         */
        
        
        if($documentTypeF != ''){
            if($documentTypeF == 2500001){
                $result->andWhere(['document.document_type_id'=>array($documentTypeF, 2500004)]);
            } else {
                $result->andWhere(['document.document_type_id'=>$documentTypeF]);
            }
            
        }
        
        if($policyHeaderF != ''){
            $result->andFilterWhere(['like', 'document.policy_header', $policyHeaderF]);
        }
        
        
        if($processNameF != ''){
            $result->andFilterWhere(['like', 'document.process_name', $processNameF]);
        }
        
        if($vendorF != ''){
            $result->andWhere(['document.vendor_id'=>$vendorF]);
        }
        
        
        
        if($validFromF != ''){
            $validTillF = date("$validTillF 23:59:59");
            $result->andWhere(['>=','document.valid_from', $validFromF]);
        }
        
        if($validTillF != ''){
            $result->andWhere(['<=','document.valid_till', $validTillF]);
        }
        
        if($documentTypeF == 2500001 || $documentTypeF == 2500005){
            if($sortBy == 0){
                $result->orderBy(['document.name' =>$order]);
            } else if($sortBy == 1){
                $result->orderBy(['document.department_id' =>$order]);
            } else if($sortBy == 2){
                $result->orderBy(['document.vendor_id' =>$order]);
            } else if($sortBy == 3){
                $result->orderBy(['document.vendor_id' =>$order]);
            } else if($sortBy == 4){
                $result->orderBy(['document.scope_of_work' =>$order]);
            } else if($sortBy == 5){
                $result->orderBy(['document.valid_from' =>$order]);
            } else if($sortBy == 6){
                $result->orderBy(['document.valid_till' =>$order]);
            } else if($sortBy == 7){
                $result->orderBy(['document.status' =>$order]);
            } else if($sortBy == 8){
                $result->orderBy(['document.payment_terms' =>$order]);
            } else if($sortBy == 9){
                $result->orderBy(['document.fee' =>$order]);
            } else if($sortBy == 10){
                $result->orderBy(['document.created_by_name' =>$order]);
            } else if($sortBy == 11){
                $result->orderBy(['document.document_type' =>$order]);
            } 
        } else if($documentTypeF == 2500002){
            if($sortBy == 0){
                $result->orderBy(['document.name' =>$order]);
            } else if($sortBy == 1){
                $result->orderBy(['document.department_id' =>$order]);
            } else if($sortBy == 2){
                $result->orderBy(['document.policy_header' =>$order]);
            } else if($sortBy == 3){
                $result->orderBy(['document.valid_from' =>$order]);
            } else if($sortBy == 4){
                $result->orderBy(['document.valid_till' =>$order]);
            } else if($sortBy == 5){
                $result->orderBy(['document.approved_by' =>$order]);
            } else if($sortBy == 6){
                $result->orderBy(['document.document_type' =>$order]);
            } 
        } else if($documentTypeF == 2500003){
            if($sortBy == 0){
                $result->orderBy(['document.name' =>$order]);
            } else if($sortBy == 1){
                $result->orderBy(['document.department_id' =>$order]);
            } else if($sortBy == 2){
                $result->orderBy(['document.process_name' =>$order]);
            } else if($sortBy == 3){
                $result->orderBy(['document.created_by_name' =>$order]);
            } else if($sortBy == 4){
                $result->orderBy(['document.document_type' =>$order]);
            } 
        }
        
        $total = $result->count();
        
        $output = $result->offset($draw)->limit($length)->all();
        
        $finalArr = array();
        
        if($output){
            foreach($output as $model){
        
                $path = '';
                $facade = new DocumentFacade();
                $filesize = CommonFacade::formatBytes($model->document_size);
                $icon = $facade->getIcon($model->document_type);
                
                $path = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
                //$path = \Yii::$app->getUrlManager()->createUrl(["index.php/dms/document/download", 'id'=>$model->id, 'type'=>'doc']);
                
                $download = '<a download="'.$model->name.'" target="_blank" href="'.$path.'" style="cursor: pointer;" title="Click to download"><img width="35" height="40" alt="" src='.$icon.' style="margin-left: 18px;" /></a><br><label>'.$filesize.'</label>';
                
                if($model->valid_till < date('Y-m-d H:i:s')){
                    $expired = 'Expired';
                } else {
                    $expired = 'Active';
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
                $viewPath = Yii::$app->getUrlManager()->createUrl(['index.php/dms/document/view', 'Id'=>$model->id]);
                $view = '<a target="_blank" id="tr_'.$model->id.'" href='.$viewPath.'>View</a>';
                
                if($documentTypeF == 2500001 || $documentTypeF == 2500004 || $documentTypeF == 2500005){
                        array_push($finalArr, array(
                                                $model->name,
                                                $selected, 
                                                $model->vendor->name, 
                                                $model->vendor->code, 
                                                $model->scope_of_work, 
                                                date("Y-m-d", strtotime($model->valid_from)), 
                                                date("Y-m-d", strtotime($model->valid_till)), 
                                                $expired, 
                                                $model->paymentTerms->value, 
                                                $model->fee, 
                                                $model->created_by_name,
                                                $download,
                                                $view
                                            )
                                );
                } else if($documentTypeF == 2500002){
                     array_push($finalArr, array(
                                                $model->name, 
                                                $selected, 
                                                $model->policy_header,
                                                date("Y-m-d", strtotime($model->valid_from)), 
                                                date("Y-m-d", strtotime($model->valid_till)), 
                                                $model->approvedBy->adminPersonals->first_name.' '.$model->approvedBy->adminPersonals->last_name,
                                                $download,
                                                $view
                                            )
                        );
                } else if($documentTypeF == 2500003){
                     array_push($finalArr, array(
                                                $model->name, 
                                                $selected, 
                                                $model->process_name, 
                                                $model->created_by_name,
                                                $download,
                                                $view
                                            )
                        );
                }           
            }
        }
        $finalArray = array('recordsTotal'=>$total, 'recordsFiltered'=>$total, 'data'=>$finalArr);
        return ($finalArray);
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
        
        if($scenario){
            if($scenario == 'agreement'){
                $result = Document::find();
                $result->where(['is_delete'=>1, 'document_type_id'=>2500001, 'status'=>2600002]);
                $result->andFilterWhere(['or', ['=', 'is_locked', 1], ['=','department_id', $userObj->department_id]]);
                $total = $result->count();
                $output = $result->offset($draw)->limit($length)->all();
            } else if($scenario == 'po'){
                $result = Document::find();
                $result->where(['is_delete'=>1, 'document_type_id'=>2500005, 'status'=>2600002]);
                $result->andFilterWhere(['or', ['=', 'is_locked', 1], ['=','department_id', $userObj->department_id]]);
                $total = $result->count();
                $output = $result->offset($draw)->limit($length)->all();
            }else if($scenario == 'policy'){
                $result = Document::find();
                $result->where(['is_delete'=>1, 'document_type_id'=>2500002, 'status'=>2600002]);
                $result->andFilterWhere(['or', ['=', 'is_locked', 1], ['=','department_id', $userObj->department_id]]);
                $total = $result->count();
                $output = $result->offset($draw)->limit($length)->all();
            } else if($scenario == 'sop'){
                $result = Document::find();
                $result->where(['is_delete'=>1, 'document_type_id'=>2500003, 'status'=>2600002]);
                $result->andFilterWhere(['or', ['=', 'is_locked', 1], ['=','department_id', $userObj->department_id]]);
                $total = $result->count();
                $output = $result->offset($draw)->limit($length)->all();
            } else if($scenario == 'expiring'){
                $result = Document::find();
                $result->where(['is_delete'=>1, 'status'=>2600002]);
                $result->andFilterWhere(
                    ['or', 
                    ['=', 'is_locked', 1], 
                    ['=','department_id', $userObj->department_id]])
                    ->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')]);
                $total = $result->count();
                $output = $result->offset($draw)->limit($length)->all();
            } else {
                $result = Document::find();
                $result->where(['is_delete'=>1, 'status'=>2600002]);
                $result->andFilterWhere(['or', ['=', 'is_locked', 1], ['=','department_id', $userObj->department_id]]);
                $total = $result->count();
                $output = $result->offset($draw)->limit($length)->all();
            }
        }
        
        
        
        $finalArr = array();
        
        if($output){
            foreach($output as $model){
                $path = '';
                $icon = '';
        
                $view = Yii::$app->getUrlManager()->createUrl(['index.php/dms/document/view', 'Id'=>$model->id]);
                $edit = Yii::$app->getUrlManager()->createUrl(['index.php/dms/document/edit', 'Id'=>$model->id]);
                $alert = Yii::$app->getUrlManager()->createUrl(['index.php/dms/document/alert', 'Id'=>$model->id]);
                
                $action = '<a id="tr_'.$model->id.'" href='.$view.'>View</a>&nbsp;|&nbsp;<a href="'.$edit.'">Edit</a>&nbsp;|&nbsp;<a href="'.$alert.'">Alert Me</a>';
                
                $userName = '';
                if($model->createdBy->adminPersonals){
                    $userName = $model->createdBy->adminPersonals->first_name.' '.$model->createdBy->adminPersonals->last_name;
                }
                
                
                
                if($model->document_type == 'application/vnd.oasis.opendocument.text' || $model->document_type == 'application/msword' || $model->document_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
                    $icon = '/at/web/images/word.png';
                } else if($model->document_type == 'application/pdf'){
                    $icon = '/at/web/images/pdf.png';
                } else if($model->document_type == 'image/png'){
                    $icon = '/at/web/images/png.png';
                } else if($model->document_type == 'image/jpeg' || $model->document_type == 'image/jpg'){
                    $icon = '/at/web/images/jpeg.png';
                } else if($model->document_type == 'application/vnd.ms-excel' || $model->document_type == 'application/vnd.oasis.opendocument.spreadsheet' || $model->document_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
                    $icon = '/at/web/images/excel.png';
                }
                
                $path = \Yii::getAlias('@web') . '/uploads/times/'.$model->department->value.'/'.$model->document_path;
                
                $download = '<a target="_blank" href="'.$path.'" style="cursor: pointer;" title="Click to download"><img width="35" height="40" alt="" src='.$icon.' style="margin-left: 18px;" /></a>';
                
                
                
                array_push($finalArr, array(
                                        $model->name, 
                                        $model->department->value, 
                                        $model->version.'.0', 
                                        $model->documentType->value, 
                                        $download, 
                                        $userName, 
                                        date("j M Y, h:i:s", strtotime($model->created_on)), 
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
    
    
    public function editDocument($id){
        $CODES = new Codes;
        if($id != ''){
            $model = Document::find()->where(['id'=>$id])->one();
            
            
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
    
    
    public function viewDocument($id){
        $CODES = new Codes;
        $versionArray = array();
        
        if($id != ''){
            $model = Document::find()->where(['id'=>$id, 'is_delete'=>1])->one();
            
            
            
            $versionObj = Document::find()
                            ->select(['id', 'old_id', 'document_type', 'name', 'version', 'document_path'])
                            ->andFilterWhere(['or', 
                                        ['=', 'id', $model->id],
                                        ['=', 'old_id', $model->id],
                            ])->all();
            
            if($model->old_id != ''){
                $versionObj1 = Document::find()
                            ->select(['id', 'old_id', 'document_type', 'name', 'version', 'document_path'])
                            ->andFilterWhere(['or', 
                                        ['=', 'id', $model->old_id],
                                        ['=', 'old_id', $model->old_id],
                            ])->all();
            }
            
            if($versionObj){
                foreach($versionObj as $obj){
                    if($obj->id != $id){
                        array_push($versionArray, $obj);
                    }
                }
            }
            
            
            if($versionObj1){
                foreach($versionObj1 as $obj){
                    if($obj->id != $id){
                        array_push($versionArray, $obj);
                    }
                }
            }
            
           //echo "<pre>"; print_r($versionArray);die;
            
            /*
            
            $totalVer = array();
            $versionArray = array();
            
            array_push($totalVer, $model->id);
            if($model->old_id != ''){
                array_push($totalVer, $model->old_id);
            }
            
            
            foreach($totalVer as $ver){
                $versionObj = Document::find()
                                ->select(['id', 'old_id', 'document_type', 'name', 'version', 'document_path'])
                                ->andFilterWhere(['or', 
                                            ['=', 'id', $ver],
                                            ['=', 'old_id', $ver],
                                ])->one();
                array_push($versionArray, $versionObj);
            }
             * 
             */
            if($model){
                $MSG = $this->messages->M155;
                $CODE = $CODES::SUCCESS;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>array('DOCUMENT'=>$model, 'VERSION_LIST'=>$versionArray));
            } else {
                $MSG = $this->messages->M121;
                $CODE = $CODES::ERROR;
                return array('CODE' => $CODE, 'MESSAGE' => $MSG, 'DATA' =>'');
            }
        }
    }
    
    /*
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
    
     * 
     */
    
    public function updateDocumentStatus($id, $status){
        $CODES = new Codes;
        if($id != ''){
            $model = Document::find()->where(['id'=>$id])->one();
            if($model){
                if($status == 2600002){
                    $model->status = 2600002;
                    $model->approved_by = Yii::$app->admin->adminId;
                    $model->approved_on = date('Y-m-d H:i:s');
                    if($model->old_id == ''){
                        Document::updateAll(['status' =>2600001, 'modified_by'=>Yii::$app->admin->adminId, 'modified_on'=>date('Y-m-d H:i:s')], "id = $model->id");
                        Document::updateAll(['status' =>2600001, 'modified_by'=>Yii::$app->admin->adminId, 'modified_on'=>date('Y-m-d H:i:s')], "old_id = $model->id");
                    } else {
                        Document::updateAll(['status' =>2600001, 'modified_by'=>Yii::$app->admin->adminId, 'modified_on'=>date('Y-m-d H:i:s')], "id = $model->old_id");
                        Document::updateAll(['status' =>2600001, 'modified_by'=>Yii::$app->admin->adminId, 'modified_on'=>date('Y-m-d H:i:s')], "old_id = $model->old_id");
                    }
                } else if($status == 2600003){
                    $model->status = 2600003;
                    $model->modified_by = Yii::$app->admin->adminId;
                    $model->modified_on = date('Y-m-d H:i:s');
                }
                

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