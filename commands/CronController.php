<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use Yii;
/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CronController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex(){
        /*
        $list = \app\models\EmailHistory::find()->where(['status'=>400001])->orderBy(['id' => SORT_DESC])->all();
        
        $sender = 'waseem.khan@claritusconsulting.com';
        

        foreach($list as $mailHistory){
            try {
                Yii::$app->mailer->compose()
                ->setFrom($sender)
                ->setTo($mailHistory->email_id)
                ->setSubject($mailHistory->subject)
                ->setHtmlBody($mailHistory->body)
                ->send();
                $mailHistory->attempts = $mailHistory->attempts+1;
                $mailHistory->status = 400002;
                $mailHistory->save();
            } catch (Exception $e) {
                $mailHistory->status = 400003;
                $mailHistory->save();
            }
        }
        */

    }
    
    
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionExpire(){
        $result = \app\models\Document::find();
        //$result->select(['status']);
        $result->where(['is_delete'=>1, 'status'=>2600002]);
        $result->andWhere(['<', 'valid_till', date('Y-m-d H:i:s')]);
        $output = $result->all();
        
        if($output){
            foreach($output as $document){
                //if($document->document_type_id == 2500001 || $document->document_type_id == 2500002){
                    $document->status = 2600005;
                    $document->save();
                //}
            }
        }
    }
    
    
    public function actionSixty(){
        $sender = 'waseem.khan@claritusconsulting.com';
        
        $date = date('Y-m-d', strtotime("+60 days"));
        
        $result = \app\models\Document::find();
        $result->select(['id']);
        $result->where(['is_delete'=>1]);
        $result->andWhere(['=', 'valid_till', $date]);
        $result->andWhere(['=', 'status', 2600002]);
        $output = $result->all();
        
        
        foreach($output as $model){
            
            $receiverArr = array();
            
            
            $financeCommercial = \app\models\AdminPersonal::find()
            ->select(['email'])
            ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
            ->where(['department_id'=>2300001, 'cc_users.role'=>array(100001, 100005), 'cc_users.status'=>550001])
            ->all();
            if(isset($financeCommercial) && $financeCommercial != ''){
                foreach($financeCommercial as $fc){
                    if(!in_array($fc, $receiverArr)) {
                        array_push($receiverArr, $fc->email);
                    }
                }
            }
            
            
            $list = \app\models\AdminPersonal::find()
                    ->select(['email'])
                    ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
                    ->where(['department_id'=>$model->department_id, 'cc_users.role'=>array(100001), 'cc_users.status'=>550001])
                    ->all();
            if(isset($list) && $list != ''){
                foreach($list as $ls){
                    if(!in_array($ls, $receiverArr)) {
                        array_push($receiverArr, $ls->email);
                    }
                }
            }

            $alertList = \app\models\Alerts::find()->select(['email'])->where(['document_id'=>array($model->old_id, $model->id), 'status'=>550001])->all();
            if($alertList){
                foreach($alertList as $alert){
                    if(!in_array($alert, $receiverArr)) {
                        array_push($receiverArr, $alert->email);
                    }
                }
            }

            $createrEmail = $model->createdBy->adminPersonals->email;

            if(!in_array($createrEmail, $receiverArr)) {
                array_push($receiverArr, $createrEmail);
            }

            $oldCreater = \app\models\Document::find()->select(['created_by'])->where(['id'=>$model->old_id])->one();
            if($oldCreater){
                $createrEmailOld = $oldCreater->createdBy->adminPersonals->email;
                if(!in_array($createrEmailOld, $receiverArr)) {
                    array_push($receiverArr, $createrEmailOld);
                }
            }
            
            
            
            if(!empty($receiverArr)){
                $selected = ''; 
                        $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                        if($selectedDepartmentList){
                            
                            foreach($selectedDepartmentList as $dep){
                                $selected .= $dep->department->value.' ';
                            }
                        }
                        
                $emailObj = array(
                    'NAME'=>$model->vendor->name, 
                    'OTHER1'=>$model->version.'0', 
                    'STATUS'=>$model->status0->value,
                    'DEPARTMENT'=>$selected,
                    'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name,
                    'DATE'=>$model->valid_till
                );
                
                $receiverArr = array_unique($receiverArr);
                
                foreach($receiverArr as $receiver){
                    $mailfacade = new \app\facades\common\MailFacade();
                    $mailfacade->sendEmail($emailObj, $receiver, 950010, 1050001, $sender);
                }
            }
        }
    }
    
    
    public function actionThirty(){
        $sender = 'waseem.khan@claritusconsulting.com';
        
        $date = date('Y-m-d', strtotime("+30 days"));
        
        $result = \app\models\Document::find();
        $result->where(['is_delete'=>1]);
        $result->andWhere(['=', 'valid_till', $date]);
        $result->andWhere(['=', 'status', 2600002]);
        $output = $result->all();
        
        
        foreach($output as $model){
            
            $receiverArr = array();
            
            
            $financeCommercial = \app\models\AdminPersonal::find()
            ->select(['email'])
            ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
            ->where(['department_id'=>2300001, 'cc_users.role'=>array(100001, 100005), 'cc_users.status'=>550001])
            ->all();
            if(isset($financeCommercial) && $financeCommercial != ''){
                foreach($financeCommercial as $fc){
                    if(!in_array($fc, $receiverArr)) {
                        array_push($receiverArr, $fc->email);
                    }
                }
            }
            
            
            $list = \app\models\AdminPersonal::find()
                    ->select(['email'])
                    ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
                    ->where(['department_id'=>$model->department_id, 'cc_users.role'=>array(100001), 'cc_users.status'=>550001])
                    ->all();
            if(isset($list) && $list != ''){
                foreach($list as $ls){
                    if(!in_array($ls, $receiverArr)) {
                        array_push($receiverArr, $ls->email);
                    }
                }
            }

            $alertList = \app\models\Alerts::find()->select(['email'])->where(['document_id'=>array($model->old_id, $model->id), 'status'=>550001])->all();
            if($alertList){
                foreach($alertList as $alert){
                    if(!in_array($alert, $receiverArr)) {
                        array_push($receiverArr, $alert->email);
                    }
                }
            }

            $createrEmail = $model->createdBy->adminPersonals->email;

            if(!in_array($createrEmail, $receiverArr)) {
                array_push($receiverArr, $createrEmail);
            }

            $oldCreater = \app\models\Document::find()->select(['created_by'])->where(['id'=>$model->old_id])->one();
            if($oldCreater){
                $createrEmailOld = $oldCreater->createdBy->adminPersonals->email;
                if(!in_array($createrEmailOld, $receiverArr)) {
                    array_push($receiverArr, $createrEmailOld);
                }
            }
            
            
            
            if(!empty($receiverArr)){
                $selected = ''; 
                $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                if($selectedDepartmentList){

                    foreach($selectedDepartmentList as $dep){
                        $selected .= $dep->department->value.' ';
                    }
                }


                $emailObj = array(
                    'NAME'=>$model->vendor->name, 
                    'OTHER1'=>$model->version.'0', 
                    'STATUS'=>$model->status0->value,
                    'DEPARTMENT'=>$selected,
                    'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name,
                    'DATE'=>$model->valid_till
                );
                
                $receiverArr = array_unique($receiverArr);
                
                foreach($receiverArr as $receiver){
                    $mailfacade = new \app\facades\common\MailFacade();
                    $mailfacade->sendEmail($emailObj, $receiver, 950010, 1050001, $sender);
                }
            }
        }
    }
    
    public function actionFifteen(){
        $sender = 'waseem.khan@claritusconsulting.com';
        
        $date = date('Y-m-d', strtotime("+15 days"));
        
        $result = \app\models\Document::find();
        $result->where(['is_delete'=>1]);
        $result->andWhere(['=', 'valid_till', $date]);
        $result->andWhere(['=', 'status', 2600002]);
        $output = $result->all();
        
        
        foreach($output as $model){
            
            $receiverArr = array();
            
            
            $financeCommercial = \app\models\AdminPersonal::find()
            ->select(['email'])
            ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
            ->where(['department_id'=>2300001, 'cc_users.role'=>array(100001, 100005), 'cc_users.status'=>550001])
            ->all();
            if(isset($financeCommercial) && $financeCommercial != ''){
                foreach($financeCommercial as $fc){
                    if(!in_array($fc, $receiverArr)) {
                        array_push($receiverArr, $fc->email);
                    }
                }
            }
            
            
            $list = \app\models\AdminPersonal::find()
                    ->select(['email'])
                    ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
                    ->where(['department_id'=>$model->department_id, 'cc_users.role'=>array(100001), 'cc_users.status'=>550001])
                    ->all();
            if(isset($list) && $list != ''){
                foreach($list as $ls){
                    if(!in_array($ls, $receiverArr)) {
                        array_push($receiverArr, $ls->email);
                    }
                }
            }

            $alertList = \app\models\Alerts::find()->select(['email'])->where(['document_id'=>array($model->old_id, $model->id), 'status'=>550001])->all();
            if($alertList){
                foreach($alertList as $alert){
                    if(!in_array($alert, $receiverArr)) {
                        array_push($receiverArr, $alert->email);
                    }
                }
            }

            $createrEmail = $model->createdBy->adminPersonals->email;

            if(!in_array($createrEmail, $receiverArr)) {
                array_push($receiverArr, $createrEmail);
            }

            $oldCreater = \app\models\Document::find()->select(['created_by'])->where(['id'=>$model->old_id])->one();
            if($oldCreater){
                $createrEmailOld = $oldCreater->createdBy->adminPersonals->email;
                if(!in_array($createrEmailOld, $receiverArr)) {
                    array_push($receiverArr, $createrEmailOld);
                }
            }
            
            
            
            if(!empty($receiverArr)){
                $selected = ''; 
                $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                if($selectedDepartmentList){

                    foreach($selectedDepartmentList as $dep){
                        $selected .= $dep->department->value.' ';
                    }
                }

                
                $emailObj = array(
                    'NAME'=>$model->vendor->name, 
                    'OTHER1'=>$model->version.'0', 
                    'STATUS'=>$model->status0->value,
                    'DEPARTMENT'=>$selected,
                    'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name,
                    'DATE'=>$model->valid_till
                );
                
                $receiverArr = array_unique($receiverArr);
                
                foreach($receiverArr as $receiver){
                    $mailfacade = new \app\facades\common\MailFacade();
                    $mailfacade->sendEmail($emailObj, $receiver, 950010, 1050001, $sender);
                }
            }
        }
    }
    
    public function actionSeven(){
        $sender = 'waseem.khan@claritusconsulting.com';
        
        $date = date('Y-m-d', strtotime("+7 days"));
        
        $result = \app\models\Document::find();
        $result->where(['is_delete'=>1]);
        $result->andWhere(['=', 'valid_till', $date]);
        $result->andWhere(['=', 'status', 2600002]);
        $output = $result->all();
        
        
        foreach($output as $model){
            
            $receiverArr = array();
            
            
            $financeCommercial = \app\models\AdminPersonal::find()
            ->select(['email'])
            ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
            ->where(['department_id'=>2300001, 'cc_users.role'=>array(100001, 100005), 'cc_users.status'=>550001])
            ->all();
            if(isset($financeCommercial) && $financeCommercial != ''){
                foreach($financeCommercial as $fc){
                    if(!in_array($fc, $receiverArr)) {
                        array_push($receiverArr, $fc->email);
                    }
                }
            }
            
            
            $list = \app\models\AdminPersonal::find()
                    ->select(['email'])
                    ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
                    ->where(['department_id'=>$model->department_id, 'cc_users.role'=>array(100001), 'cc_users.status'=>550001])
                    ->all();
            if(isset($list) && $list != ''){
                foreach($list as $ls){
                    if(!in_array($ls, $receiverArr)) {
                        array_push($receiverArr, $ls->email);
                    }
                }
            }

            $alertList = \app\models\Alerts::find()->select(['email'])->where(['document_id'=>array($model->old_id, $model->id), 'status'=>550001])->all();
            if($alertList){
                foreach($alertList as $alert){
                    if(!in_array($alert, $receiverArr)) {
                        array_push($receiverArr, $alert->email);
                    }
                }
            }

            $createrEmail = $model->createdBy->adminPersonals->email;

            if(!in_array($createrEmail, $receiverArr)) {
                array_push($receiverArr, $createrEmail);
            }

            $oldCreater = \app\models\Document::find()->select(['created_by'])->where(['id'=>$model->old_id])->one();
            if($oldCreater){
                $createrEmailOld = $oldCreater->createdBy->adminPersonals->email;
                if(!in_array($createrEmailOld, $receiverArr)) {
                    array_push($receiverArr, $createrEmailOld);
                }
            }
            
            
            
            if(!empty($receiverArr)){
                $selected = ''; 
                $selectedDepartmentList = \app\models\DocumentDepartments::find()->select(['department_id'])->where(['document_id'=>$model->id, 'is_delete'=>1])->all();
                if($selectedDepartmentList){

                    foreach($selectedDepartmentList as $dep){
                        $selected .= $dep->department->value.' ';
                    }
                }
                
                $emailObj = array(
                    'NAME'=>$model->vendor->name, 
                    'OTHER1'=>$model->version.'0', 
                    'STATUS'=>$model->status0->value,
                    'DEPARTMENT'=>$selected,
                    'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name,
                    'DATE'=>$model->valid_till
                );
                
                $receiverArr = array_unique($receiverArr);
                
                foreach($receiverArr as $receiver){
                    $mailfacade = new \app\facades\common\MailFacade();
                    $mailfacade->sendEmail($emailObj, $receiver, 950010, 1050001, $sender);
                }
            }
        }
    }
    
    
    public function actionWeekly(){
        
        $days = \app\models\Configurations::find()->select(['value'])->where(['is_delete'=>1, 'short_code'=>'MAIL_DAYS'])->one();
        $frequency = \app\models\Configurations::find()->select(['value'])->where(['is_delete'=>1, 'short_code'=>'MAIL_FREQUENCY'])->one();
        $count = \app\models\Configurations::find()->select(['value'])->where(['is_delete'=>1, 'short_code'=>'MAIL_COUNT'])->one();
        
        
        
        $sender = 'waseem.khan@claritusconsulting.com';
        $result = \app\models\Document::find();
        $result->where(['is_delete'=>1]);
        $result->andWhere(['between', 'valid_till', date('Y-m-01 H:i:s'), date('Y-m-t 23:59:59')]);
        
        $output = $result->all();
        
        foreach($output as $model){
            $receiverArr = array();
            
            $list = \app\models\AdminPersonal::find()
                    ->select(['email'])
                    ->innerJoin('cc_users', 'cc_users.id = cc_admin_personal.user_id')
                    ->where(['department_id'=>$model->department_id, 'cc_users.role'=>array(100001), 'cc_users.status'=>550001])
                    ->all();
            if(isset($list) && $list != ''){
                foreach($list as $ls){
                    if(!in_array($ls, $receiverArr)) {
                        array_push($receiverArr, $ls->email);
                    }
                }
            }

            $alertList = \app\models\Alerts::find()->select(['email'])->where(['document_id'=>array($model->old_id, $model->id), 'status'=>550001])->all();
            if($alertList){
                foreach($alertList as $alert){
                    if(!in_array($alert, $receiverArr)) {
                        array_push($receiverArr, $alert->email);
                    }
                }
            }

            $createrEmail = $model->createdBy->adminPersonals->email;

            if(!in_array($createrEmail, $receiverArr)) {
                array_push($receiverArr, $createrEmail);
            }

            $oldCreater = \app\models\Document::find()->select(['created_by'])->where(['id'=>$model->old_id])->one();
            if($oldCreater){
                $createrEmailOld = $oldCreater->createdBy->adminPersonals->email;
                if(!in_array($createrEmailOld, $receiverArr)) {
                    array_push($receiverArr, $createrEmailOld);
                }
            }
            
            
            
            if(!empty($receiverArr)){
                $emailObj = array(
                    'NAME'=>$model->name, 
                    'OTHER1'=>$model->version.'0', 
                    'STATUS'=>$model->status0->value,
                    'DEPARTMENT'=>$model->department->value,
                    'SUBSCRIBER.FIRSTNAME'=>$model->created_by_name,
                    'DATE'=>$model->valid_till
                );
                
                foreach($receiverArr as $receiver){
                    $mailfacade = new \app\facades\common\MailFacade();
                    $mailfacade->sendEmail($emailObj, $receiver, 950010, 1050001, $sender);
                }
            }
        }
    }
    
    
}












 
            
            
            /*
            $mailHistory->attempts = $mailHistory->attempts+1;
            Yii::$app->smtp->setMailType('SMTP');
            Yii::$app->smtp->setHost('mail.claritusconsulting.com');
            Yii::$app->smtp->setUname('waseem.khan@claritusconsulting.com');
            Yii::$app->smtp->setPassd('Mithi@123');
            Yii::$app->smtp->setEncType('tls');
            Yii::$app->smtp->setServerPort('25'); 
            Yii::$app->smtp->configSet();
            Yii::$app->smtp->setFrom(array($sender));

            try{
                Yii::$app->smtp->SendMail($sender, $mailHistory->email_id, $mailHistory->subject, $mailHistory->body);
                $mailHistory->status = 400002;
            } catch (\Swift_SwiftException $e) {
                Yii::error($e);
                $mailHistory->status = 400003;
                return true;
            } 
            $mailHistory->save();
            
             * 
             */