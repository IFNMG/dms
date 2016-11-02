<?php

namespace app\facades\common;

use Yii;
use \app\models\EmailHistory;
use app\web\util\Codes\LookupCodes;
use yii\web\UploadedFile;


/**
 * This is a facades class for mail module.
 */
class MailFacade {
    
    /*
     * function for sending generated mail to queue
     * @author: Waseem
     */

    public function sendEmail($emailObj, $recepient, $eventId, $langId, $sender=NULL, $immiate) {
        $att = '';
        $attachment = '';
        $lookupObj = \app\models\Lookups::find()
                    ->select(['parent_id', 'id'])
                    ->where(['id'=>$eventId])->one();
        
        if($lookupObj){
            if($lookupObj->parent_id != ''){
                $sender = $lookupObj->parent->value;
            }
        }
        
        
        if($sender == ""){
            $sender = Yii::$app->params['configurations']['ADMIN_EMAIL'];
        }
        
        $template = CommonFacade::getEmailTemplate($eventId, $langId);
        
        if($template){
            $subject = $template['subject'];
            $content = $template['content'];
            if($template['attachment']){
                $attachment = $template['attachment'];
            }
        } else {
            $xml = CommonFacade::getEmailXml($eventId);
            $subject = $template['subject'];
            $content = $template['content'];
        }
        
        if($attachment != ''){
            $att =  \Yii::getAlias('@webroot').'/uploads/'.$attachment;
        }  
        
        foreach ($emailObj as $key => $val) { //replacing placeholders with param values
            $content = str_replace('[['.$key.']]', $val, $content);
        }
        
        $mailMethod = \app\models\Configurations::find()->select(['value'])->where(['short_code'=>'MAIL_METHOD'])->one();
        
        $mailHistory = new EmailHistory();
        $mailHistory->email_id = $recepient;
        $mailHistory->event_id = $eventId;
        $mailHistory->status = LookupCodes::L_EMAIL_STATUS_PENDING; 
        $mailHistory->subject = $subject;
        $mailHistory->mail_method = $mailMethod->value;
        $mailHistory->body = $content;
        if($attachment != ''){
            $mailHistory->attachment = $attachment;
        }    
        $mailHistory->attempts = 0;
        $mailHistory->sender_email = $sender;
        $mailHistory->sent_on = gmdate('Y-m-d h:i:s', time());
        
        if($mailHistory->save()){  
            
            /*
            $mailHistory->attempts = $mailHistory->attempts+1;
            
            if($mailMethod->value == LookupCodes::L_MAIL_METHOD_SMTP){
                
                $host = Yii::$app->params['configurations']['SMTP_HOST'];
                $port = Yii::$app->params['configurations']['SMTP_PORT'];
                $uname = Yii::$app->params['configurations']['SMTP_USER'];
                $pswd = Yii::$app->params['configurations']['SMTP_PASSWORD'];
                $encrp = Yii::$app->params['configurations']['SMTP_ENCRYPTION_TYPE'];
                
                Yii::$app->smtp->setMailType('SMTP');
                Yii::$app->smtp->setHost($host);
                Yii::$app->smtp->setUname($uname);
                Yii::$app->smtp->setPassd($pswd);
                Yii::$app->smtp->setEncType($encrp);
                Yii::$app->smtp->setServerPort($port); 
                Yii::$app->smtp->configSet();
                Yii::$app->smtp->setFrom(array($sender));
                
                
                if($att != ''){
                    try{
                        Yii::$app->smtp->SendMail($sender, $recepient, $subject, $content, '', '', array($att));
                        $mailHistory->status = LookupCodes::L_EMAIL_STATUS_SENT;
                    } catch (\Swift_SwiftException $e) {
                        Yii::error($e);
                        $mailHistory->status = LookupCodes::L_EMAIL_STATUS_FAILED;
                        return true;
                    } 
                } else {
                    try{
                        Yii::$app->smtp->SendMail($sender, $recepient, $subject, $content);
                        $mailHistory->status = LookupCodes::L_EMAIL_STATUS_SENT;
                    } catch (\Swift_SwiftException $e) {
                        Yii::error($e);
                        $mailHistory->status = LookupCodes::L_EMAIL_STATUS_FAILED;
                        return true;
                    }      
                }
                
            } else if($mailMethod->value == LookupCodes::L_MAIL_METHOD_SES){
                
                
                Yii::$app->ses->access_key = Yii::$app->params['configurations']['AWS_ACCESS_KEY'];
                Yii::$app->ses->secret_key = Yii::$app->params['configurations']['AWS_SECRET_KEY'];
                Yii::$app->ses->host = Yii::$app->params['configurations']['AWS_HOST_NAME'];
                
                
                if($att != ''){
                    try{
                        Yii::$app->ses->compose()->setFrom($sender)->setTo($recepient)->setSubject($subject)->setHtmlBody($content)->attach($att)->send();
                        $mailHistory->status = LookupCodes::L_EMAIL_STATUS_SENT;
                    } catch (\yii\base\Exception $e) {
                        Yii::error($e);
                        $mailHistory->status = LookupCodes::L_EMAIL_STATUS_FAILED;
                        return true;
                    }  
                } else {
                    try{
                        Yii::$app->ses->compose()->setFrom($sender)->setTo($recepient)->setSubject($subject)->setHtmlBody($content)->send();
                        $mailHistory->status = LookupCodes::L_EMAIL_STATUS_SENT;
                    } catch (\yii\base\Exception $e) {
                        Yii::error($e);
                        $mailHistory->status = LookupCodes::L_EMAIL_STATUS_FAILED;
                        return true;
                    }
                }
               
            }    
            $mailHistory->save();
             * 
             */
            $status = true;
        } else {
            
            $status = false;
        }
        return $status;
    }

    
}


//Yii::$app->ses->compose('contact/html', ['contactForm' => 'Test'])


/*
            try {
                Yii::$app->mailer->compose()
                ->setFrom($mailHistory->sender_email)
                ->setTo($mailHistory->email_id)
                ->setSubject($mailHistory->subject)
                ->setHtmlBody($mailHistory->body)
                ->send();
                
                $mailHistory->attempts = $mailHistory->attempts+1;
                $mailHistory->status = 400002;
            } catch (\Swift_SwiftException $e) {
                $mailHistory->status = 400003;
            }    
            */
