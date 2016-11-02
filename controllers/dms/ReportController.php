<?php

namespace app\controllers\dms;

use Yii;
use \app\facades\common\CommonFacade;
use \app\facades\dms\ReportFacade;
use \app\models\Document;

class ReportController extends \yii\web\Controller {

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
        
        
        $lang = CommonFacade::getLanguage();
        $request = Yii::$app->request->get();
        $type = $request['type'];
        
           
        $permission = \app\facades\common\CommonFacade::getPermissions(Yii::$app->request);
        return $this->render('list', ['permission'=>$permission, 'lang'=>$lang, 'type'=>$type]);
    }
    
    
    public function actionViewlist(){
        
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            $facade = new ReportFacade();
            $response = $facade->viewList($request);
            return json_encode($response);
        } else {
            
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
    } 
    
    public function actionDashboardlist(){
        
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            $facade = new ReportFacade();
            $response = $facade->dashboardList($request);
            return json_encode($response);
        } else {
            
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
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
                $facade = new ReportFacade();
                $response = $facade->viewDocument($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA']['DOCUMENT'];
                    $versionList = $response['DATA']['VERSION_LIST'];
                    return $this->render('view', array('model'=>$model, 'versionList'=>$versionList, 'permission'=>$permission));
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
    
     public function actionAdd() {
         
        $model = new Document();
        $text = '';
        $oldId  = '';
        $version = 1;
        
        if(Yii::$app->request->post()) {
            $request = Yii::$app->request->post();
            
            
            $department_id = $request['Document']['department_id'];
            $model->attributes = $request['Document'];
            
            if(isset($request['Document']['id']) && $request['Document']['id'] != ''){
                
                $oldDocument = Document::find()->where(['id'=>$request['Document']['id'], 'is_delete'=>1])->one();
                if($oldDocument){
                    if($oldDocument->old_id != ''){
                        $oldId = $oldDocument->old_id;
                    } else {
                        $oldId = $oldDocument->id;
                    }
                    
                    if($model->version == 1){
                        $version = $model->version+1;
                    } else {
                        $versionModel = Document::find()->select(['version'])->orderBy('version DESC')->where(['old_id'=>$oldId])->one();
                        if($versionModel){
                            $version = $versionModel->version+1;
                        }
                    }
                    
                    
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

                
                
                ////////////////////////////////////////////////////
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
                
                ///////////////////////////////////////////////////

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
            
            
            $createdByName = '';
            $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id', 'first_name', 'last_name'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
            if($userObj){
                $createdByName = $userObj->first_name.' '.$userObj->last_name;
            }
            
            $model->version = (string)$version;
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

            //echo "<pre>";print_r($model);die;
            
            if($model->save()){
                Yii::$app->getSession()->setFlash('success', "Uploaded Successfully");
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
            } else {
                return $this->render('add', array('model'=>$model));
            }
            
            
        }
     	return $this->render('add', array('model'=>$model));
    }
    
     /*
     * function for opening a vendor's data in edit mode
     * @author: Waseem
     */
    public function actionEdit() {
        
        if(isset($_REQUEST['Id'])){
        $id =  $_REQUEST['Id'];
            if($id){
                $facade = new ReportFacade();
                $response = $facade->editDocument($id);
                
                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('add', array('model'=>$model));
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
    
    
      /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new ReportFacade();
            $response = $facade->deleteVendor($id);
            return json_encode($response);
        }
    }
    
    
      /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionUpdate(){
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            $status =  $_REQUEST['Status'];
            if($id){
                $facade = new ReportFacade();
                $response = $facade->updateDocumentStatus($id, $status);
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
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/document/list"));
        }
        
    }
    
    
    
    public function actionExport() {
        
        $data = Yii::$app->request->post();
        
        $vendorDir =   \Yii::getAlias('@app').'/vendor/yiisoft/PHPExcel_1.8.0_doc/Classes';
        require_once $vendorDir.'/PHPExcel.php';
        $objPHPExcel = new \PHPExcel();
        
        $userObj = \app\models\AdminPersonal::find()->select(['department_id', 'id', 'user_id'])->where(['user_id'=>Yii::$app->admin->adminId])->one();
        $departmentF = $data['department_id'];
        $documentTypeF = $data['document_type'];
        $vendorF = $data['vendor_id_hidden'];
        
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
        
        $output = $result->all();
        
        
        if($output){
            if($documentTypeF == 2500001 || $documentTypeF == 2500004 || $documentTypeF == 2500005){
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A1', 'Document Name')
                            ->setCellValue('B1', 'Department')
                            ->setCellValue('C1', 'Vendor Name')
                            ->setCellValue('D1', 'Vendor Code')
                            ->setCellValue('E1', 'Scope of Work')
                            ->setCellValue('F1', 'Valid From')
                            ->setCellValue('G1', 'Valid Till')
                            ->setCellValue('H1', 'Expiry Status')
                            ->setCellValue('I1', 'Payment Term')    
                            ->setCellValue('J1', 'Fee')
                            ->setCellValue('K1', 'Uploaded By')
                        ;   

                $celArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K');
                foreach($celArr as $key=>$cell){
                    $objPHPExcel->getActiveSheet()->getStyle($cell.'1')->getFill()->applyFromArray(array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array(
                             'rgb' => 'D5D8DC  
        '               )
                    ));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
                }

                $i = 1;
                foreach($output as $model){
                    $i++;
                    if($model->valid_till < date('Y-m-d H:i:s')){
                        $expired = 'Expired';
                    } else {
                        $expired = 'Active';
                    }
                    
                    $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                    if($selectedDepartmentList){
                        $selected = ''; 
                        foreach($selectedDepartmentList as $dep){
                            $selected .= $dep->department->value.', ';
                        }
                    }
                    
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue("A$i", $model->name)
                            ->setCellValue("B$i", $selected)
                            ->setCellValue("C$i", $model->vendor->name)
                            ->setCellValue("D$i", $model->vendor->code)
                            ->setCellValue("E$i", $model->scope_of_work)
                            ->setCellValue("F$i", date("Y-m-d", strtotime($model->valid_from)))
                            ->setCellValue("G$i", date("Y-m-d", strtotime($model->valid_till)))
                            ->setCellValue("H$i", $expired)
                            ->setCellValue("I$i", $model->paymentTerms->value)
                            ->setCellValue("J$i", $model->fee)
                            ->setCellValue("K$i", $model->created_by_name)
                        ;
                }
            } else if($documentTypeF == 2500002){
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A1', 'Document Name')
                            ->setCellValue('B1', 'Department')
                            ->setCellValue('C1', 'Policy Header')
                            ->setCellValue('D1', 'Valid From')
                            ->setCellValue('E1', 'Valid Till')
                            ->setCellValue('F1', 'Approved By')
                        ;   

                $celArr = array('A', 'B', 'C', 'D', 'E', 'F');
                foreach($celArr as $key=>$cell){
                    $objPHPExcel->getActiveSheet()->getStyle($cell.'1')->getFill()->applyFromArray(array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array(
                             'rgb' => 'D5D8DC  
        '               )
                    ));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
                }

                $i = 1;
                foreach($output as $model){
                    $i++;
                    if($model->valid_till < date('Y-m-d H:i:s')){
                        $expired = 'Expired';
                    } else {
                        $expired = 'Active';
                    }
                    
                    $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                    if($selectedDepartmentList){
                        $selected = ''; 
                        foreach($selectedDepartmentList as $dep){
                            $selected .= $dep->department->value.', ';
                        }
                    }
                    
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue("A$i", $model->name)
                            ->setCellValue("B$i", $selected)
                            ->setCellValue("C$i", $model->policy_header)
                            ->setCellValue("D$i", $model->valid_from)
                            ->setCellValue("E$i", $model->valid_till)
                            ->setCellValue("F$i", $model->approvedBy->adminPersonals->first_name.' '.$model->approvedBy->adminPersonals->last_name)
                        ;
                }
            } else if($documentTypeF == 2500003){
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A1', 'Document Name')
                            ->setCellValue('B1', 'Department')
                            ->setCellValue('C1', 'Process Name')
                            ->setCellValue('D1', 'Prepared By')
                        ;   

                $celArr = array('A', 'B', 'C', 'D');
                foreach($celArr as $key=>$cell){
                    $objPHPExcel->getActiveSheet()->getStyle($cell.'1')->getFill()->applyFromArray(array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array(
                             'rgb' => 'D5D8DC  
        '               )
                    ));
                    $objPHPExcel->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
                }

                $i = 1;
                foreach($output as $model){
                     $i++;
                    $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                    if($selectedDepartmentList){
                        $selected = ''; 
                        foreach($selectedDepartmentList as $dep){
                            $selected .= $dep->department->value.', ';
                        }
                    }
                    
                    
                   
                    if($model->valid_till < date('Y-m-d H:i:s')){
                        $expired = 'Expired';
                    } else {
                        $expired = 'Active';
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue("A$i", $model->name)
                            ->setCellValue("B$i", $selected)
                            ->setCellValue("C$i", $model->process_name)
                            ->setCellValue("D$i", $model->created_by_name)
                        ;
                }
            }           
        }
        
        
        
        
        
        
        

        $objPHPExcel->getActiveSheet()->setTitle('Report');

        $objPHPExcel->setActiveSheetIndex(0);


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Subscribers.xls"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');

        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
        exit;
    }
    
}
