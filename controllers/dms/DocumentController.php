<?php

namespace app\controllers\dms;

use Yii;
use \app\facades\common\CommonFacade;
use \app\facades\dms\DocumentFacade;
use \app\models\Document;
use \app\models\DocumentDepartments;

class DocumentController extends \yii\web\Controller {

    public $enableCsrfValidation = false;

   
    
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate', 'viewlist'
                            
                        ],
                'rules' => [
                    [
                        'actions' => [
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate', 'viewlist'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            
        ];
    }
    
    
    public function beforeAction($e){
        
        $status = CommonFacade::authorize(Yii::$app->request);
        if(!$status){
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/home/index"));
        } else {
            return parent::beforeAction($e);
        }
    }  
    
     /*
     * function for getting list of all assessment requests submitted by users
     * @author: Waseem
     */ 
    public function actionList(){        
        $source = 'list';
        $scenario = '';
        $term = '';
        
        $lang = CommonFacade::getLanguage();
        $request = Yii::$app->request->get();
        
        if(isset($request['type']) && $request['type'] == 'dashboard'){
            $source = 'dashboard';
        }
        
        if(isset($request['scenario']) && $request['scenario'] != ''){
            $scenario = $request['scenario'];
        }
        
        
           
        $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
        return $this->render('list', ['permission'=>$permission, 'lang'=>$lang, 'source'=>$source, 'scenario'=>$scenario, 'term'=>$term]);
    }
    
    
     /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionSearch(){
        $term = $_POST['term'];
        if($term != ''){
            $source = 'dashboard';
            $scenario = 'term';
            $lang = CommonFacade::getLanguage();
            $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
            return $this->render('list', ['permission'=>$permission, 'lang'=>$lang, 'source'=>$source, 'scenario'=>$scenario, 'term'=>$term]);
            
            //$model = Document::findBySql('SELECT * FROM document WHERE MATCH (document_text) AGAINST ('."'".$term."'".' IN BOOLEAN MODE)')->all();
            //echo "<pre>";print_r($model);die;
            
            /*
            $result = Document::find();
            $result->where(['is_delete'=>1]);
            if($userObj->department_id != 2300001){
                $result->andWhere(['department_id'=>$userObj->department_id]);
            }
            $total = $result->count();
            $output = $result->offset($draw)->limit($length)->all();
            */
            
            
            
            
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/admin/landing"));
        }
        
        
        
    }
    
    public function actionViewlist(){
        
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            $facade = new DocumentFacade();
            $response = $facade->viewList($request);
            return json_encode($response);
        } else {
            
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
        }
    } 
    
    public function actionAlertme(){
        
        if(Yii::$app->request->post()){
            $request = Yii::$app->request->post();
            $facade = new DocumentFacade();
            $response = $facade->alertMe($request);
            return json_encode($response);
        } else {
            
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
    } 
    
    public function actionDashboardlist(){
        
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            $facade = new DocumentFacade();
            $response = $facade->dashboardList($request);
            return json_encode($response);
        } else {
            
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
    } 
    
    public function actionRollback(){
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                $facade = new DocumentFacade();
                $response = $facade->rollBack($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
        }
    } 
    
    /*
     * function for viewing assessment requests complete data
     * @author: Waseem
     */
    public function actionView(){
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                $facade = new DocumentFacade();
                $response = $facade->viewDocument($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA']['DOCUMENT'];
                    $versionList = $response['DATA']['VERSION_LIST'];
                    $selectedDepartmentList = $response['DATA']['SELECTED_DEPARTMENT'];
                    $addendumList = $response['DATA']['ADDENDUM_LIST'];
                    return $this->render('view', array('model'=>$model, 'versionList'=>$versionList, 'permission'=>$permission, 'selectedDepartmentList'=>$selectedDepartmentList, 'addendumList'=>$addendumList));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
    }
    
    
    public function actionAddendum(){
        if(isset($_REQUEST['id'])){
            $id =  $_REQUEST['id'];
            if($id != ''){
                $parent = Document::find()->select(['vendor_id'])->where(['id'=>$id, 'is_delete'=>1])->one();
                
                if($parent){
                    $model = new Document();
                    $departmentObj = new DocumentDepartments();
                    $model->document_type_id = 2500004;
                    $model->old_id = $id;
                    $model->vendor_id = $parent->vendor_id;
                    
                    return $this->render('add', array('model'=>$model, 'departmentObj'=>$departmentObj));
                } else {
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
        }
        
    } 
    
    
    public function actionAdd() {
        $model = new Document();
        $departmentObj = new DocumentDepartments();
        $text = '';
        $oldId  = '';
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $createdByName = '';
        //$version = 1;
        $mailMode = 0;
        $parentObj = '';
        
        if(Yii::$app->request->post()) {
            
            $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id', 'first_name', 'last_name', 'email'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
            $request = Yii::$app->request->post();
            
            //$department_id = $request['Document']['department_id'];
            $department_id = $userObj->department_id;
            
            $departmentArr = $request['DocumentDepartments']['department_id'];
            
            
            if(isset($request['Document']['id']) && $request['Document']['id'] != ''){
                $mailMode = 1;
                $model = Document::find()->where(['id'=>$request['Document']['id'], 'is_delete'=>1])->one();
                if($model){
                    $parentObj = $model;
                    $docPath = $model->document_path;
                    $docText = $model->document_text;
                    $docSize = $model->document_size;
                    $docType = $model->document_type;
                    $docDocument = $model->document;
                    
                    $scannedDocPath = $model->scanned_document_path;
                    $scannedDocSize = $model->scanned_document_size;
                    $scannedDocType = $model->scanned_document_type;
                    $scannedDocument = $model->scanned_document;
                    
                    if($model->old_id != ''){
                        $oldId = $model->old_id;
                    } else {
                        $oldId = $model->id;
                    }
                    
                    if($model->document_type_id == 2500004){
                        $oldId = $model->id;
                    }
                    
                    $model->modified_on = date('Y-m-d H:i:s');
                    $model->modified_by = Yii::$app->admin->adminId;
                    $model->attributes = $request['Document'];
                    
                    $model->document_path = $docPath;
                    $model->document_text = $docText;
                    $model->document_size = $docSize;
                    $model->document_type = $docType;
                    $model->document = $docDocument;
                    
                    $model->scanned_document_path = $scannedDocPath;
                    $model->scanned_document_size = $scannedDocSize;
                    $model->scanned_document_type = $scannedDocType;
                    $model->scanned_document = $scannedDocument;
                    
                    if($model->created_by != Yii::$app->admin->adminId){
                        $mailMode = 0;
                        $model = new Document();
                        $model->created_on = date('Y-m-d H:i:s');
                        $model->created_by = Yii::$app->admin->adminId;
                        $model->attributes = $request['Document'];
                        $model->document_path = $docPath;
                        $model->document_text = $docText;
                        $model->document_size = $docSize;
                        $model->document_type = $docType;
                        $model->document = $docDocument;
                        $model->scanned_document_path = $scannedDocPath;
                        $model->scanned_document_size = $scannedDocSize;
                        $model->scanned_document_type = $scannedDocType;
                        $model->scanned_document = $scannedDocument;
                    
                        $model->version = 'draft';
                        $model->old_id = $oldId;
                    } else if($model->created_by == Yii::$app->admin->adminId){
                        if($model->status == 2600002 || $model->status == 2600006){
                            $mailMode = 0;
                            $model = new Document();
                            $model->created_on = date('Y-m-d H:i:s');
                            $model->created_by = Yii::$app->admin->adminId;
                            $model->attributes = $request['Document'];
                            $model->document_path = $docPath;
                            $model->document_text = $docText;
                            $model->document_size = $docSize;
                            $model->document_type = $docType;
                            $model->document = $docDocument;
                            $model->scanned_document_path = $scannedDocPath;
                            $model->scanned_document_size = $scannedDocSize;
                            $model->scanned_document_type = $scannedDocType;
                            $model->scanned_document = $scannedDocument;
                            $model->version = 'draft';
                            $model->old_id = $oldId;
                        }
                    }
                }
            } else {
                $model = new Document();
                $model->attributes = $request['Document'];
                $model->created_on = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->admin->adminId;
            }
            
            $model->status = 2600001;
            $model->is_delete = 1;
            $model->scope_of_work = $request['Document']['scope_of_work'];
            
            
            $departmentObj = \app\models\Lookups::find()->select(['value'])->where(['id'=>$department_id, 'is_delete'=>1])->one();
            $dir = Yii::getAlias('@app') . '/web/uploads/times/'.$departmentObj->value.'/';
            if(!is_dir($dir)){
               mkdir("$dir", 0777);
            }
            
            if($_FILES["Document"]["tmp_name"]["document_path"] != ''){
                $tmpName = $_FILES["Document"]["tmp_name"]["document_path"];
                $name = $_FILES["Document"]["name"]["document_path"];
                $fileSize = $_FILES["Document"]['size']["document_path"];
                $fileType = $_FILES["Document"]['type']["document_path"];

                ////////////////////////////////////////// RANDOM NUMBER  ///////////////////////////////////////////
                $randomString = '';
                for ($i = 0; $i < 10; $i++) {
                    $randomString .= $characters[rand(0, strlen($characters) - 1)];
                }
                $randomString .= time();
                ////////////////////////////////////////// RANDOM NUMBER  ///////////////////////////////////////////

                $fp = fopen($tmpName, 'r');
                $content1 = fread($fp, filesize($tmpName));
                $content1 = addslashes($content1);
                fclose($fp);
                if(!get_magic_quotes_gpc()){
                    $name1 = addslashes($name);
                }
                $filename = $dir.$randomString.$name;
                move_uploaded_file($tmpName, $filename);
                chmod($filename, 0777);
                
                
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
                        }
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
                        $text .= implode(', ', $row);
                    }
                }
                $model->document_path = $randomString.$name;
                $model->document_text = $text;
                $model->document_size = $fileSize;
                $model->document_type = $fileType;
                $model->document = $content1;
            }
            
            if($_FILES["Document"]["tmp_name"]["scanned_document_path"] != ''){
                $tmpNameS = $_FILES["Document"]["tmp_name"]["scanned_document_path"];
                $nameS = $_FILES["Document"]["name"]["scanned_document_path"];
                $fileSizeS = $_FILES["Document"]['size']["scanned_document_path"];
                $fileTypeS = $_FILES["Document"]['type']["scanned_document_path"];

                ////////////////////////////////////////// RANDOM NUMBER  ///////////////////////////////////////////
                $randomStringS = '';
                for ($i = 0; $i < 10; $i++) {
                    $randomStringS .= $characters[rand(0, strlen($characters) - 1)];
                }
                $randomStringS .= time();
                ////////////////////////////////////////// RANDOM NUMBER  ///////////////////////////////////////////

                $fpS = fopen($tmpNameS, 'r');
                $contentS = fread($fpS, filesize($tmpNameS));
                $contentS = addslashes($contentS);
                fclose($fp1S);
                if(!get_magic_quotes_gpc()){
                    $nameS1 = addslashes($nameS);
                }
                $filenameS = $dir.$randomStringS.$nameS;
                move_uploaded_file($tmpNameS, $filenameS);
                chmod($filenameS, 0777);
                $model->scanned_document_path = $randomStringS.$nameS;
                $model->scanned_document_size = $fileSizeS;
                $model->scanned_document_type = $fileTypeS;
                $model->scanned_document = $contentS;
            }
            
            
            if($userObj){
                $createdByName = $userObj->first_name.' '.$userObj->last_name;
                $model->created_by_name = $createdByName;
            }
           
            
            $model->department_id = $department_id;
            
            if($model->save()){
                
                if($departmentArr && $departmentArr[0] != ''){
                    DocumentDepartments::updateAll(['is_delete' => 0, 'modified_by'=>Yii::$app->admin->adminId, 'modified_on'=>date('Y-m-d H:i:s')], "document_id = $model->id");
                    foreach($departmentArr as $depart){
                        $departmentModel = DocumentDepartments::find()->where(['document_id'=>$model->id, 'department_id'=>$depart])->one();
                        if(!empty($departmentModel)){
                            $dep = $departmentModel;
                        } else {
                            $dep = new DocumentDepartments();
                        }
                        
                        $dep->document_id = $model->id;
                        $dep->department_id = $depart;
                        $dep->status = 550001;
                        $dep->is_delete = 1;
                        $dep->created_on = date('Y-m-d H:i:s');
                        $dep->created_by = Yii::$app->admin->adminId;
                        $dep->save();
                    }
                }
                
                if($userObj->user->role == 100001){
                    $docFac = new DocumentFacade();
                    $docFac->updateDocumentStatus($model->id, 2600002, $parentObj);
                }
                
                if($mailMode == 0){
                    $list = \app\models\AdminPersonal::find()->select(['email', 'first_name', 'last_name', 'department_id'])
                            ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
                            ->where([
                                    'cc_users.role'=>array(100001), 
                                    'cc_users.status'=>550001, 'cc_users.is_delete'=>1])
                            
                            ->andFilterWhere(['or', 
                                ['=','department_id', $userObj->department_id],
                                ['=','department_id', 2300001]
                                ])
                            ->all();
                   
                    if($model->document_type_id == 2500001 || $model->document_type_id == 2500004 || $model->document_type_id == 2500005){
                        $docVenName = $model->vendor->name;
                    } else {
                        $docVenName = $model->name;
                    }
                    
                    $adminEmailSelf = '';
                    
                    
                        
                    if($list){
                        foreach($list as $admin){
                            if($admin->department_id == 2300001){
                                $adminEmailSelf  = $admin;
                            }
                            $selected = ''; 
                            $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                            if($selectedDepartmentList){

                                foreach($selectedDepartmentList as $dep){
                                    $selected .= $dep->department->value.' ';
                                }
                            }

                            $emailObjAdmin = array(
                                            'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name, 
                                            'TYPE'=>$model->documentType->value, 
                                            'NAME'=>$docVenName, 
                                            'DEPARTMENT'=>$selected, 
                                            'USER.FIRSTNAME'=>$admin->first_name, 
                                            'USER.LASTNAME'=>$admin->last_name, 
                                            'DATE'=>date("Y-m-d", strtotime($model->created_on))
                                        );
                            
                            
                            $mailfacade = new \app\facades\common\MailFacade();
                            $mailfacade->sendEmail($emailObjAdmin, $admin->email, 950013, 1050001);
                        }
                    }
                    
                    if($adminEmailSelf != ''){
                        $emailObjSelf = array(
                                        'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name, 
                                        'TYPE'=>$model->documentType->value, 
                                        'NAME'=>$docVenName, 
                                        'USER.FIRSTNAME'=>$adminEmailSelf->first_name, 
                                        'USER.LASTNAME'=>$adminEmailSelf->last_name, 
                                    );
                        
                        
                        
                        $mailfacade = new \app\facades\common\MailFacade();
                        $mailfacade->sendEmail($emailObjSelf, $model->createdBy->adminPersonals->email, 950006, 1050001);
                    }
                } else if($mailMode == 1){
                    $documentObj = new DocumentFacade();
                    $receiverList = $documentObj->getReceiverList($userObj, $model);
                    
                    if($receiverList){
                        $emailObj = array('OTHER2'=>$model->department->value, 'OTHER1'=>$model->name, 'OTHER3'=>$model->created_by_name);
                        foreach($receiverList as $receiver){
                            $mailfacade = new \app\facades\common\MailFacade();
                            $mailfacade->sendEmail($emailObj, $receiver, 950007, 1050001);
                        }
                    }
                }
                
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
                
            } else {
                return $this->render('add', array('model'=>$model, 'departmentObj'=>$departmentObj));
            }
            
            
        } else {
            return $this->render('add', array('model'=>$model, 'departmentObj'=>$departmentObj));
        }
    }
    
    /*
     public function actionAdd() {
        $model = new Document();
        $text = '';
        $oldId  = '';
        $createdByName = '';
        //$version = 1;
        $mailMode = 0;
        
        if(Yii::$app->request->post()) {
            $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id', 'first_name', 'last_name', 'email'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
            $request = Yii::$app->request->post();
            $department_id = $request['Document']['department_id'];
            $model->attributes = $request['Document'];
            
            if(isset($request['Document']['id']) && $request['Document']['id'] != ''){
                $mailMode = 1;
                $oldDocument = Document::find()->where(['id'=>$request['Document']['id'], 'is_delete'=>1])->one();
                if($oldDocument){
                    if($oldDocument->old_id != ''){
                        $oldId = $oldDocument->old_id;
                    } else {
                        $oldId = $oldDocument->id;
                    }
                    
                    //if($model->version == 1){
                    //    $version = $model->version+1;
                    //} else {
                    //    $versionModel = Document::find()->select(['version'])->orderBy('version DESC')->where(['old_id'=>$oldId])->one();
                    //    if($versionModel){
                    //        $version = $versionModel->version+1;
                    //    }
                    //}
                    $model->document_path = $oldDocument->document_path;
                    $model->document_text = $oldDocument->document_text;
                    $model->document_size = $oldDocument->document_size;
                    $model->document_type = $oldDocument->document_type;
                    $model->document = $oldDocument->document;
                }
            }
            
            if($_FILES["Document"]["tmp_name"]["document_path"] != ''){
                $tmpName = $_FILES["Document"]["tmp_name"]["document_path"];
                $name = $_FILES["Document"]["name"]["document_path"];
                $fileSize = $_FILES["Document"]['size']["document_path"];
                $fileType = $_FILES["Document"]['type']["document_path"];

                ////////////////////////////////////////// DIRECTORY CREATION ///////////////////////////////////////////
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';
                for ($i = 0; $i < 10; $i++) {
                    $randomString .= $characters[rand(0, strlen($characters) - 1)];
                }
                $randomString .= time();
                $departmentObj = \app\models\Lookups::find()->select(['value'])->where(['id'=>$department_id, 'is_delete'=>1])->one();
                $dir = Yii::getAlias('@app') . '/web/uploads/times/'.$departmentObj->value.'/';
                if(!is_dir($dir)){
                   mkdir("$dir", 0777);
                }
                ////////////////////////////////////////// DIRECTORY CREATION ////////////////////////////////////////////

                $fp = fopen($tmpName, 'r');
                $content1 = fread($fp, filesize($tmpName));
                $content1 = addslashes($content1);
                fclose($fp);
                if(!get_magic_quotes_gpc()){
                    $name1 = addslashes($name);
                }
                $filename = $dir.$randomString.$name;
                move_uploaded_file($tmpName, $filename);
                chmod($filename, 0777);
                
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
                        }
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
                        $text .= implode(', ', $row);
                    }
                }
                $model->document_path = $randomString.$name;
                $model->document_text = $text;
                $model->document_size = $fileSize;
                $model->document_type = $fileType;
                $model->document = $content1;
            }
            
            if($userObj){
                $createdByName = $userObj->first_name.' '.$userObj->last_name;
            }
            
            //$model->version = (string)$version;
            if($oldId != ''){
                $model->old_id = $oldId;
            }
            if($createdByName != ''){
                $model->created_by_name = $createdByName;
            }
            $model->created_on = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->admin->adminId;
            $model->status = 2600001;
            $model->is_delete = 1;

            if($model->save()){
                
                if($mailMode == 0){
                    //if($userObj->department_id == 2300001){ // For Finance department send email to finance HOD only
                    //    $list = \app\models\AdminPersonal::find()->select(['email', 'first_name', 'last_name'])->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')->where(['department_id'=>$userObj->department_id, 'cc_users.role'=>array(100001, 100005), 'cc_users.status'=>550001])->all();
                    //} else { // for other departments send email to finance department HOD also
                    //    $list = \app\models\AdminPersonal::find()->select(['email', 'first_name', 'last_name'])->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')->where(['department_id'=>array($userObj->department_id, 2300001), 'cc_users.role'=>array(100001, 100005), 'cc_users.status'=>550001])->all();
                    //}
                    
                    
                    $list = \app\models\AdminPersonal::find()->select(['email', 'first_name', 'last_name'])->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')->where(['department_id'=>$userObj->department_id, 'cc_users.role'=>array(100001), 'cc_users.status'=>550001])->all();
                    
                    if($list){
                        foreach($list as $admin){
                            $emailObj = array('USER.FIRSTNAME'=>$admin->first_name, 'USER.LASTNAME'=>$admin->last_name, 'OTHER1'=>$model->department->value, 'OTHER2'=>$model->name, 'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name, 'DATE'=>$model->created_on);
                            $mailfacade = new \app\facades\common\MailFacade();
                            $mailfacade->sendEmail($emailObj, $admin->email, 950006, 1050001);
                        }
                    }
                    
                    $emailObj = array('USER.FIRSTNAME'=>$model->created_by_name, 'USER.LASTNAME'=>'', 'OTHER1'=>$model->department->value, 'OTHER2'=>$model->name, 'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name, 'DATE'=>$model->created_on);
                    $mailfacade = new \app\facades\common\MailFacade();
                    $mailfacade->sendEmail($emailObj, $model->createdBy->adminPersonals->email, 950006, 1050001);
                    
                } else if($mailMode == 1){
                    $documentObj = new DocumentFacade();
                    $receiverList = $documentObj->getReceiverList($userObj, $model);
                    
                    if($receiverList){
                        $emailObj = array('OTHER2'=>$model->department->value, 'OTHER1'=>$model->name, 'OTHER3'=>$model->created_by_name);
                        foreach($receiverList as $receiver){
                            $mailfacade = new \app\facades\common\MailFacade();
                            $mailfacade->sendEmail($emailObj, $receiver, 950007, 1050001);
                        }
                    }
                }
                
                Yii::$app->getSession()->setFlash('success', "Uploaded Successfully");
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
            } else {
                return $this->render('add', array('model'=>$model));
            }
            
            
        }
     	return $this->render('add', array('model'=>$model));
    }
    * 
     */
    
     /*
     * function for opening a vendor's data in edit mode
     * @author: Waseem
     */
    public function actionEdit() {
        
        if(isset($_REQUEST['Id'])){
        $id =  $_REQUEST['Id'];
            if($id){
                $facade = new DocumentFacade();
                $response = $facade->editDocument($id);
                
                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA']['model'];
                    
                    $selectedDepartmentArr = $response['DATA']['selectedDepartmentArr'];
                    
                    $departmentObj = new DocumentDepartments();
                    return $this->render('add', array('model'=>$model, 'departmentObj'=>$departmentObj, 'selectedDepartmentArr'=>$selectedDepartmentArr));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
        }
        
    }
    
    
      /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new DocumentFacade();
            $response = $facade->deleteDocument($id);
            return json_encode($response);
        }
    }
    
     /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionArchive(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new DocumentFacade();
            $response = $facade->archiveDocument($id);
            return json_encode($response);
        }
    }
    
      /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionUpdate(){
        
        if(isset($_REQUEST['id'])){
            $id =  $_REQUEST['id'];
            
            $request = $_REQUEST;
            
            if($id){
                $facade = new DocumentFacade();
                $response = $facade->updateDocumentStatus($request);
                return json_encode($response);
            }
            /*
            if($id){
                
                $facade = new DocumentFacade();
                $response = $facade->updateDocumentStatus($request);
                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];
                if ($code == 200){
                    $model = $response['DATA'];
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
            }
             * 
             */
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
        }
        
    }
    
    public function actionDownload(){
        $id =  $_REQUEST['id'];
        $type =  $_REQUEST['type'];
        
        $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id', 'first_name', 'last_name', 'email'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
        
        
        if($id != ''){
            $model = \app\models\Document::find()->where(['id'=>$id])->one();
            if($model){
                if($type == 'doc'){
                    $model->document = stripslashes($model->document);
                    if($userObj->user->role == 100008){
                        if($userObj->department_id == 2300001){
                            if($model->status == 2600002){
                                header("Content-length: $model->document_size");
                                header("Content-type: $model->document_type");
                                header("Content-Disposition: attachment; filename=$model->name");
                                echo $model->document;
                            }
                        } else {
                            if($model->status == 2600002 && $model->department_id == $userObj->department_id){
                                header("Content-length: $model->document_size");
                                header("Content-type: $model->document_type");
                                header("Content-Disposition: attachment; filename=$model->name");
                                echo $model->document;
                            }
                        } 
                    } else if($userObj->user->role == 100004){
                        header("Content-length: $model->document_size");
                        header("Content-type: $model->document_type");
                        header("Content-Disposition: attachment; filename=$model->name");
                        echo $model->document;
                    } else {
                        header("Content-length: $model->document_size");
                        header("Content-type: $model->document_type");
                        header("Content-Disposition: attachment; filename=$model->name");
                        echo $model->document;
                    }
                } else if($type == 'scanned'){
                    $model->scanned_document = stripslashes($model->scanned_document);
                    if($userObj->user->role == 100008){
                        if($userObj->department_id == 2300001){
                            if($model->status == 2600002){
                                header("Content-length: $model->scanned_document_size");
                                header("Content-type: $model->scanned_document_type");
                                header("Content-Disposition: attachment; filename=$model->name");
                                echo $model->scanned_document;
                            }
                        } else {
                            if($model->status == 2600002 && $model->department_id == $userObj->department_id){
                                header("Content-length: $model->scanned_document_size");
                                header("Content-type: $model->scanned_document_type");
                                header("Content-Disposition: attachment; filename=$model->name");
                                echo $model->scanned_document;
                            }
                        } 
                    } else if($userObj->user->role == 100004){
                        header("Content-length: $model->scanned_document_size");
                        header("Content-type: $model->scanned_document_type");
                        header("Content-Disposition: attachment; filename=$model->name");
                        echo $model->scanned_document;
                    } else {
                        header("Content-length: $model->scanned_document_size");
                        header("Content-type: $model->scanned_document_type");
                        header("Content-Disposition: attachment; filename=$model->name");
                        echo $model->scanned_document;
                    }
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
        }
    }
    
      /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionGetvendor(){
        $term = $_REQUEST['term'];
        $array = array();
        $vendorList = \app\models\Vendor::find()
                    ->orderBy(['name' => SORT_ASC])
                    ->andFilterWhere([
                        'or',
                        ['like', 'name', $term],
                        ['like', 'code', $term],
                    ])
                    ->andWhere(['status'=>550001, 'is_delete'=>1])
                    ->all();
        foreach($vendorList as $vendor){
            $array[] = array (
                'label' => $vendor->name,
                'value' => $vendor->id,
            );
            //array_push($data, array('ID'=>$vendor->id, 'NAME'=>$vendor->name));
        }
        echo json_encode($array);
    
        
    }
}
