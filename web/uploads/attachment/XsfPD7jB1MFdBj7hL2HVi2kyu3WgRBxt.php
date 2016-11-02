<?php
namespace app\facades\common;

use Yii;
use yii\base\ErrorException;
use app\models\Lookups;
use app\models\Lookuptypes;
use app\facades\request\Status;
use app\facades\security\Securityfacade;
use app\facades\cache\Cachefacade;
use app\models\Rolemenus;
use app\models\Menus;
use app\models\Listbox;
use app\models\Userpreferences;
use app\models\Users;
use app\facades\common\Commonfacade;
use app\models\SubscriberInviteEmailDetails;
use app\models\Emailtemplates;
use app\models\Registration;
use app\models\PointConfigurations;
use app\models\SubscriberInvites;
use app\models\Plvisibility;
use app\models\Isstickerlikesdislikes;
use app\models\Isstickersprimary;
use app\models\Isproductinfo;
use app\models\Isdesigninfo;
use app\models\Isstickerimagevideos;
use app\models\Isstickerscertificates;
use app\models\Isknowledgeinfo;
use app\models\Isstickerbrandproductline;
use app\models\Plproductline;
use app\models\Plbrandsingleline;
use app\models\Isproductprice;
use app\models\Stickerpriceterritory;



/**
 * This is a facades class for ommon functions.
 */
class Commonfacade {
    /*
     * If role menu relation is not present in cache then create one here.
     * @author: Anjan
     * @date: 28-01-15
     */

    public function createRolemenusTableCache() {

        $rolemenus = new Rolemenus();
        $rolemenusInstance = $rolemenus::find()->joinWith('menu')->all();

        if ($rolemenusInstance) {
            foreach ($rolemenusInstance AS $instance) {

                $cacheObject = new Cachefacade();
                $cacheObject->createCacheRow('ROLEMENUS' . $instance->MenuID . $instance->SubscriberCategoryId . $instance->RoleId . $instance['menu']->IsDashboard, $instance);
            }
            return true;
        } else {
            return false;
        }
    }

    public function createRolemenusTableFullRecursive() {

        $rolemenus = new Rolemenus();
        $rolemenusInstance = $rolemenus::find()->joinWith('menu')->all();

        if ($rolemenusInstance) {
            foreach ($rolemenusInstance AS $instance) {

                $cacheObject = new Cachefacade();
                $cacheObject->createCacheRow('ROLEMENUSRECURSIVE' . $instance->MenuID . $instance->SubscriberCategoryId . $instance->RoleId, $instance);
            }
            return true;
        } else {
            return false;
        }
    }

    /*
     * If menu table is not present in cache then create one here.
     * @author: Anjan
     * @date: 28-01-15
     */

    public function createMenusTableCache($select = null) {

        $menus = new Menus();
        if ($select) {
            $menusInstance = $menus::find()->select([$select])
                    ->all();
        } else {
            $menusInstance = $menus::find()
                    ->all();
        }
        if ($menusInstance) {
            $cacheObject = new Cachefacade();
            if ($select) {
                $cacheObject->createCacheRow('MENUS' . $select, $menusInstance);
            } else {
                $cacheObject->createCacheRow('MENUS', $menusInstance);
            }

            return true;
        } else {
            return false;
        }
    }

    
    
    /*
     * for getting lookup data by lookup type id
     * @author: Waseem
     */ 
    public function getLookupDataById($id){
        $finalListArray = array();
        //$lookupType = Lookuptypes::find()->select(['ID'])->where(['ID'=>$id, 'IsActive'=>1])->one();
        if($id == 41 || $id == 36 || $id == 39 || $id == 40){
            $lookup = Lookups::find()->where(['LookupTypeId'=>$id, 'IsActive'=>1])->orderBy(['ID' => SORT_ASC])->all();
        } else if($id == 38) {
            $lookup = Lookups::find()->where(['LookupTypeId'=>$id, 'IsActive'=>1])->orderBy(['Value' => SORT_DESC])->all();
        } else {
            $lookup = Lookups::find()->where(['LookupTypeId'=>$id, 'IsActive'=>1])->orderBy(['Value' => SORT_ASC])->all();
        }    
        foreach ($lookup as $lookup1) {
            if($lookup1->IsActive == 1){
                $finalListArray[] = new Listbox($lookup1->attributes['ID'], $lookup1->attributes['Value']);
            }
        }
        
        //array_multisort($finalListArray, SORT_DESC, $finalListArray);
        return $finalListArray;
    }
     
    /*
     * function for uploading image to folder
     * @author: Waseem
     */
    
    public function uploadImage($image, $imagetype, $userId, $imagecat=null){
        
        $user = Users::find()->select(['ID','SubscribersId'])->where(['ID'=>$userId])->one();
        if($imagetype == 'primary_logo'){
            $path = Yii::getAlias('@app') . '/tempImages/logo' . $userId;
            if(file_exists($path)) {
                foreach (scandir($path) as $item) {
                    if ($item == '.' || $item == '..') continue;
                    unlink($path.DIRECTORY_SEPARATOR.$item);
                }
                rmdir($path);
            }
        }
        
        
        $visibility = Commonfacade::getVisibility($user->SubscribersId);
        
        $extension = $this->getB64Type($image);
        $uniqueIdnew = $this->generateRandomString();
        $name = $this->getImageName($imagetype, $userId, $extension, $imagename=null, $uniqueIdnew);
        
        if($extension == 'image/jpeg'){
            $image = str_replace("data:image/jpeg;base64,", "", $image);
        } else if ($extension == 'image/png') {
            $image = str_replace("data:image/png;base64,", "", $image);
        } else if ($extension == 'application/pdf') {
            $image = str_replace("data:application/pdf;base64,", "", $image);
        } else if ($extension == 'video/mp4') {
            $image = str_replace("data:video/mp4;base64,", "", $image);
        }
        
        if($extension == 'image/jpeg' || $extension == 'image/png'){
            $data = base64_decode($image);
            $im = imagecreatefromstring($data);
            if ($im !== false) {
                header("Content-Type: $extension");
                if($extension == 'image/jpeg'){
                    imagejpeg($im, $name);
                } else if($extension == 'image/png'){
                    imagepng($im, $name);
                }
                
                // for creating blurred images if image type is sticker and sticker type is Product and visibility set to restricted
                //if($visibility == 0 && $imagecat == 'imagesandvideos' && $imagetype == 'primary_pisimage'){
                if($imagecat == 'imagesandvideos' && $imagetype == 'primary_pisimage'){
                    $this->createBlurImage($im, $name, $imagetype, $userId, $extension, $imagecat, $uniqueIdnew);
                }
                
                $imagetype = str_replace("primary_", "", $imagetype);
                $this->createMultipleImages($imagetype, $userId, $extension, $name, $imagecat, $uniqueIdnew);
            } else {
                return new Status('failed',array('message'=>'Please upload valid file')); 
            }
        } else if($extension == 'application/pdf'){
            $data = base64_decode($image);
            file_put_contents($name, $data);
        } else if($extension == 'video/mp4') {
            $data = base64_decode($image);
            file_put_contents($name, $data);
        }
        $finalListArray[] = new Listbox('success', $name);
        return $finalListArray;
    }
    
    
    /*
     * for creating blurred sticker images 
     * @author: Waseem
     */ 
    public function createBlurImage($im, $name, $imagetype, $subscribersId, $extension, $imagecat, $uniqueIdnew){
        $name = str_replace("primary_", "blur_", $name);
        $imagetype = str_replace("primary_", "blur_", $imagetype);
        for($i=0; $i<60; $i++) {
            imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
        }
        header("Content-Type: $extension");
        if($extension == 'image/jpeg'){
            imagejpeg($im, $name);
        } else if($extension == 'image/png'){
            imagepng($im, $name);
        }
        //$this->createMultipleImages($imagetype, $subscribersId, $extension, $name, $imagecat, $uniqueIdnew);
    }
  
    public function getvariableimages($imagecat) {
        
        switch ($imagecat) {
               case 'imagesandvideos': //CIS
                    $sizeArr = array(
                            //work bench
                            '269X190'=>0.078298541,
                            '360X255'=>0.204786152,
                            '425X300'=>0.323705873,
                           //project image
                             '53X40'=>0.01512685, 
                            '166X83'=>0.047378436,
                            '245X100'=>0.069926005,
                            '366X150'=>0.204460889,
                            //normal 
                            //'53X40'=>0.01512685, 
                            '95X67'=>0.027114165,
                            //'58X41' =>0.016553911,
                            '113X80'=>0.032251586, 
                            '132X94'=>0.037674419, 
                            '141X100'=>0.040243129,
                            '120X90'=>0.034249471,
                            '184X130'=>0.052515856
                    );
                break;
               case 'users': // Profile prefrerence and 
                    $sizeArr = array(
                        //my preference
                        '120X90'=> 0.19047619, 
                        '135X191'=>0.214285714,
                        '200X283'=>0.317460317,
                        '300X424'=>0.476190475,
                        
                        '360X255'=>0.476190475,
                        '366X150'=>0.476190475,
                        '425X300'=>0.476190475,
                        
                        //profile
                        //'120X90'=> 0.19047619, 
                        //'135X191'=>0.214285714,
                        //'200X283'=>0.317460317,
                        //'300X424'=>0.476190475,
                         //normal
                        '53X40'=>0.063095238, 
                        '95X67'=>0.113095238, 
                        '141X100'=>0.167857143, 
                        '184X130'=>0.219047619, 
                        '113X80'=>0.134523809
                    );
                break;
            case 'finishes':
                    $sizeArr = array(
                        '120X90'=> 0.19047619, 
                        '135X191'=>0.214285714,
                        '200X283'=>0.317460317,
                        '300X424'=>0.476190475
                    );
                break;
                case 'associate':
                    $sizeArr = array(
                        '53X40'=>0.01512685, 
                        '95X67'=>0.027114165, 
                        '141X100'=>0.040243129, 
                        '120X90'=> 0.034249472, 
                        '184X130'=>0.052515856, 
                        '132X94'=>0.037674418, 
                        '113X80'=>0.032251585
                    );
                break;
                
                default:
                    $sizeArr = array(0.7, 0.5, 0.3, 0.1, 0.09, 0.07, 0.05);
                break;
            }
        return $sizeArr;
    }
        
    public function createMultipleImages($imagetype, $subscriberId, $extension, $name,$imagecat,$uniqueIdnew){
        str_replace('/', "'\'", $name);
        str_replace("'", "", $name);
        //$imagecat='workbench';
        $sizeArr = $this->getvariableimages($imagecat);
        
       
        header("Content-Type: $extension");
        list($width, $height) = getimagesize($name);
        
        foreach($sizeArr as $sizename=>$size){
            $output = $this->getImageName($imagetype, $subscriberId, $extension,$sizename,$uniqueIdnew);
            
            $new_width = $width * $size;
            $new_height = $height * $size;
            
            $image_p = imagecreatetruecolor($new_width, $new_height);
            
            if($extension == 'image/jpeg'){
                $image = imagecreatefromjpeg($name);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_p, $output, 100);
            } else if($extension == 'image/png'){
                $image = imagecreatefrompng($name);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagepng($image_p, $output, 9);
            } 
            imagedestroy($image_p);
        }
        return $image_p;
    }
    /*
     * function for uploading pdf document to folder
     * @author: Anjan
     * @date: 13-02-2015
     */

    public function uploadDocument2($image, $imagetype, $userId, $fileName) {
        //print_r($image);die;

        /* $extension = $this->getB64Type($image);

          if ($extension == 'application/pdf') {
          $image = str_replace("data:application/pdf;base64,", "", $image);
          } else {
          $message = array('message' => 'Only PDF documents are accepted');
          return new Status('failed', $message);
          }
          $data = base64_decode($image);
          $string_array = str_split($data);

          $byteArr = array();
          foreach ($string_array as $key => $val) {

          $byteArr[$key] = ord($val);
          } */

        $path = Yii::getAlias('@app') . '/tempData/' . 'subscriber' . $userId . '/';

        if (file_exists($path)) {

            $this->deleteDirectoryRecursively($path);
            mkdir($path, 0777);
        } else {
            mkdir($path, 0777);
        }
        /*$fp = fopen($path . $fileName, 'wb+');
          while (!empty($byteArr)) {
          $byte = array_shift($byteArr);
          fwrite($fp, pack('c', $byte));
          } 
         */
        $fp = fopen($path . 'somefile.pdf', 'w');
        fputs($fp, $image);
        fclose($fp);

        if (move_uploaded_file($image, $path)) {
            $message = array('message' => 'file saved');
            return new Status('success', $message);
        } else {
            $message = array('message' => 'file save failed');
            return new Status('failed', $message);
        }
    }
    
    /*
     * Image upload function
     * @author: Anjan Dutta
     * @date: 12-06-15
     */
    
    public function uploadWbImage($data, $userId, $lookupTypeValue) {

        $src = Yii::getAlias('@app') . '/tempData/' . $userId . '/' . $data['sourceName'] . '/' . $lookupTypeValue . '/';
        
        $path = Commonfacade::moveImagesToS3($src);
        if($path){
            return $path[0];
        }else
            return false; 
    }
    

    /*
     * Document upload function
     * @author: Anjan Dutta
     * @date: 17-02-15
     */
    public function uploadDocument($path, $token, $file, $separator,$type = null) {
        
        $tempUrl = Yii::getAlias('@app') . '/tempData/' . $path . '/' . $separator . '/';
        if (file_exists($tempUrl)) {
            $this->deleteDirectoryRecursively($tempUrl);
        }
        mkdir($tempUrl, 0755, true);
        
        
        if ($file->saveAs($tempUrl . '/' . str_replace('-', '', $file->name))) {
            if($type=='WBD'){
                
                $fixedHeight = 586;
                $extension = $file->getExtension();
                
                $imgDtls = getimagesize($tempUrl . '/' . str_replace('-', '', $file->name));
                $imgName = $file->getBaseName();
                
                $output = $tempUrl.'/'.str_replace('-', '', $imgName).'_wbd.'.$extension;
                if($imgDtls){
                    $width = $imgDtls[0];
                    $height = $imgDtls[1];
                    $resizePercent = floatval($fixedHeight/$height);
                    
                    $new_width = $width * $resizePercent;
                    $new_height = $height * $resizePercent;
                
                
                    $image_p = imagecreatetruecolor($new_width, $new_height);

                    if($extension=='jpg'||$extension=='jpeg'){
                        $image = imagecreatefromjpeg($tempUrl . '/' . str_replace('-', '', $file->name));
                        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagejpeg($image_p, $output, 80);
                    }else if($extension=='png'){
                        $image = imagecreatefrompng($tempUrl . '/' . str_replace('-', '', $file->name));
                        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                        imagepng($image_p, $output, 8);
                    }
                    imagedestroy($image_p);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /*
     * Delete a directory recursively
     * @author: anjan
     * @date: 13-02-2015
     */

    public function deleteDirectoryRecursively($path) {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                $this->deleteDirectoryRecursively(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        } else if (is_file($path) === true) {
            return unlink($path);
        }

        return false;
    }

    /*
     * function for generating image name
     * @author: Waseem
     */

    public function getImageName($imagetype, $subId, $extension,$sizename=null,$uniqueIdnew=null) {
        if(!$uniqueIdnew){
          $uniqueId = $this->generateRandomString();
        }else{
          $uniqueId =$uniqueIdnew;  
        }
        $name = '';
        
        if($imagetype == 'primary_logo' || $imagetype == 'logo'){
            $path = Yii::getAlias('@app') . '/tempImages/logo' . $subId;
        } else {
            $path = Yii::getAlias('@app') . '/tempImages/subscriber' . $subId;
        }    

        if (file_exists($path)) {
            $dirname = $path;
        } else {
            $dirname = $path;
            mkdir($dirname);
        }
        if($sizename!=''){
          $sizename='-'.$sizename;  
        }        
        if ($extension == 'image/jpeg') {
            $name = $dirname . '/' . $imagetype . '-' . $subId . '-' . $uniqueId . ''.$sizename.'.jpg';
        } else if ($extension == 'image/png') {
            $name = $dirname . '/' . $imagetype . '-' . $subId . '-' . $uniqueId . ''.$sizename.'.png';
        } else if($extension == 'application/pdf') {
            $name = $dirname . '/' . $imagetype . '-' . $subId . '-' . $uniqueId . '.pdf';
        } else if($extension == 'video/mp4') {
            $name = $dirname . '/' . $imagetype . '-' . $subId . '-' . $uniqueId . '.mp4';
        } 
        return $name;
    }
    
    
    
    
    
    
      /*
     * function for generating image name
     * @author: Waseem
     */

       public function getSizedImageName($imagetype, $userId, $extension,$sizename=null,$uniqueIdnew=null,$getname) {
        $temp = explode('.', $getname);
        $temp_name=$temp['0'];
        if(!$uniqueIdnew){
          $uniqueId = $this->generateRandomString();
        }else{
          $uniqueId =$uniqueIdnew;  
        }
        $name = '';
        
        if($imagetype == 'primary_logo' || $imagetype == 'logo'){
            $path = Yii::getAlias('@app') . '/tempImages/images/' . $userId;
        } else {
            $path = Yii::getAlias('@app') . '/tempImages/images/' . $userId;
        }    

        if (file_exists($path)) {
            $dirname = $path;
        } else {
            $dirname = $path;
            mkdir($dirname);
        }
        if($sizename!=''){
          $sizename='-'.$sizename;  
        }        
        if ($extension == 'image/jpeg') {
            $name = $dirname . '/' . $temp_name . ''.$sizename.'.jpg';
        } else if ($extension == 'image/png') {
            $name = $dirname . '/' . $temp_name . ''.$sizename.'.png';
        } else if($extension == 'application/pdf') {
            $name = $dirname . '/' . $temp_name . '.pdf';
        } else if($extension == 'video/mp4') {
            $name = $dirname . '/' . $temp_name . '.mp4';
        } 
        return $name;
    }
    
    
    
    
    
       /*
     * function for generating image name
     * @author: Waseem
     */

    public function getImageNameUpdated($imagetype, $subId, $extension,$path) {
        $uniqueId = $this->generateRandomString();
        $name = '';
        if (file_exists($path)) {
            $dirname = $path;
        } else {
           $newpath =  mkdir($path,0777 , true);
            $dirname = $newpath;
            
        }

        if ($extension == 'image/jpeg') {
            $name = $imagetype . '-' . $subId . '-' . $uniqueId . '.jpg';
        } else if ($extension == 'image/png') {
            $name = $imagetype . '-' . $subId . '-' . $uniqueId . '.png';
        }
        return $name;
    }
    
    
    
    

    /*
     * function for removing directory with images
     * @author: Waseem
     */
    
     public static function removeDirectrory($id){
        $s1 = Yii::getAlias('@app').'/tempImages/subscriber'.$id;
        $s2 = Yii::getAlias('@app').'/tempImages/logo'.$id;
        $s3 = Yii::getAlias('@app').'/tempImages/images/'.$id;
        $sourceArr = array($s1, $s2, $s3);
        foreach($sourceArr as $source){
            if(is_dir($source)){
                    foreach (scandir($source) as $item){
                       if ($item == '.' || $item == '..') continue;
                       unlink($source.DIRECTORY_SEPARATOR.$item);
                    }
                    rmdir($source);
            }
        }
        return true;
    }
    
    /*
     * function for getting images from temp folder
     * @author: Waseem
     */
     public static function moveImagesToS3($source){
         $obj = '';
         $files = '';
        if(is_dir($source)){
            $dh  = opendir($source);
            while (false !== ($filename = readdir($dh))) {
                if($filename != '.' && $filename != '..'){
                    $obj = Yii::$app->s3->upload($source.$filename, $filename, Yii::$app->params['bucketName']);
                    if($obj == 0){
                        $files[] = $filename;
                    }
                }
            } 
            if($obj == 0){
                foreach (scandir($source) as $item) {
                    if ($item == '.' || $item == '..') continue;
                    unlink($source.DIRECTORY_SEPARATOR.$item);
                 }
                 rmdir($source);
            } else {
                $files = '';
            }
            
            return $files;
        }
    }
    
        
    /*
     * function for getting images from temp folder
     * @author: Abhay
     */
     public static function moveImagesToS3Custom($source){
        $files = '';
        if(is_dir($source)){
            //echo $source;die;
            $dh  = opendir($source);
            
            while (false !== ($filename = readdir($dh))) {
                if($filename != '.' && $filename != '..'){
                    $files[] = $filename;
                    Yii::$app->s3->upload($source.$filename, $filename, Yii::$app->params['bucketName']);
                }
            }
            foreach (scandir($source) as $item) {
               if ($item == '.' || $item == '..') continue;
               unlink($source.DIRECTORY_SEPARATOR.$item);
            }
            //rmdir($source);
            return $files;
        }
    }
    
    
    
    /*
     * function for generating random string
     * @author: Waseem
     */

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        $randomString .= time();
        return $randomString;
    }
    
    /*
     * function for generating random string
     * @author: Waseem
     */

    public static function generateInvoiceNumber($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    function getB64Type($str) {
        return substr($str, 5, strpos($str, ';') - 5);
    }

    /*
     * function for getting address list of subscriber .saved from profile.
     * @author: Waseem
     */
    public function getStudio($id){
        $user = Users::find()->select(['SubscribersId'])->where(['ID'=>$id, 'IsActive'=>1])->one();
        $finalListArray = '';
        
        if(!empty($user->subscribers->subscriberaddresses)){
            foreach($user->subscribers->subscriberaddresses as $address){
                if(!empty($address->city->Name)){
                    $city = $address->city->Name;
                } else if(!empty($address->city->OtherCity)){
                    $city = $address->city->OtherCity;
                } else {
                    $city = '';
                }
                
                
                if(!empty($address->country->Name)){
                    $country = $address->country->Name;
                } else {
                    $country = '';
                }
                $studio = $country.' '.$city.' '.$address->attributes['Street1'].' '.$address->attributes['Street2'];
                $finalListArray[] = new Listbox($address->attributes['ID'], $studio);
            }
        }
        return $finalListArray;
    }
    
    
    /*
     * function for getting the list of employees by address id
     * @author: Waseem
     */
    public function getEmployeesListByAddress($userId, $addressId){
        $finalListArray = '';
        $user = Users::find()->select(['users.SubscribersId', 'users.ID'])->rightJoin('passwords','users.ID=passwords.UserId')->where(['users.ID'=>$userId, 'users.IsActive'=>1])->one();
        if(!empty($user)){
            $userList = Users::find()->select(['users.ID', 'users.FirstName', 'users.LastName'])->rightJoin('passwords','users.ID=passwords.UserId')->where(['users.SubscriberAddresseId'=>$addressId, 'users.SubscribersId'=>$user->SubscribersId,  'users.IsActive'=>1])->all();
            if(!empty($userList)){
                foreach($userList as $user){
                    $name = \app\facades\masters\MasterFacade::remove_safe($user->attributes['FirstName']).' '.\app\facades\masters\MasterFacade::remove_safe($user->attributes['LastName']);
                    $finalListArray[] = new Listbox($user->attributes['ID'], $name);
                }
            }
        }
        return $finalListArray;
    }
    
    
    
    public function getInvitationEmailTemplates($eventId, $langId){
        $finalListArray = '';
        $templatesArr = \app\models\Lookups::find()->select(['ID'])
                    ->where(['ParentLookupId'=>$eventId, 'IsActive'=>1])
                    ->all();       
        
        foreach($templatesArr as $temp){
            $template = \app\models\Emailtemplatestranslation::find()->select(['ID', 'Name'])->where(['IsActive'=>1, 'LanguageId'=>$langId, 'EventId'=>$temp->ID])->one();       
            if($template){
                $finalListArray[] = new Listbox($template->ID, $template->Name);
            }
        }
        
                    
        /*
        $templates = \app\models\Emailtemplatestranslation::find()
                        ->select(['ID', 'Name'])
                        ->where(['EventId'=>$eventId, 'IsActive'=>1, 'LanguageId'=>$langId])
                        ->all();
        
        $finalListArray = '';
        foreach($templates as $temp){
            $finalListArray[] = new Listbox($temp->attributes['ID'], $temp->attributes['Name']);
        }
        */
        return $finalListArray;
    }
    
    /*
     * function for getting points for events by id and event id 
     * @author: Waseem
     */
    public static function getSubscriberPoints($sId, $sCat, $eventId) {
        $today = gmdate('Y-m-d', time());
        $points = false;   
        $pointById = PointConfigurations::find()->select(['ID', 'PointValue'])->where(['VenirePointEventId' => $eventId, 'SubscriberId'=>$sId, 'IsActive'=>1])
                        ->andWhere(['<=', 'ActivationDate', $today])
                        ->orderBy('ID Desc')->limit(1)->one();

        if(!empty($pointById)){
            $points =  array('id'=>$pointById->ID, 'value'=>$pointById->PointValue);
        } else {
            $pointByCat = PointConfigurations::find()->select(['ID', 'PointValue'])->where(['VenirePointEventId' => $eventId, 'SubscriberCategoryId'=>$sCat,'IsActive'=>1])
                            ->andWhere(['<=', 'ActivationDate', $today])
                            ->orderBy('ID Desc')->limit(1)->one();
            if(!empty($pointByCat)){
                $points =  array('id'=>$pointByCat->ID, 'value'=>$pointByCat->PointValue);
            } 
        }
        return $points;
    }
    
    public static function connectToCloud(){
        $domain = Yii::$app->params['DOMAIN'];
        $serverLocation = Yii::$app->params['SERVER_LOCATION'];
        
        $cloudSearch = new \AwsCloudSearch\AwsCloudSearch($domain, $serverLocation);
        return $cloudSearch;
    }
    
    /*
    * function for posting data to amazon cloud
    * @author: Waseem
    */
    /*
    public function uploadDoc($userId) {
        
        $stickerList = \app\models\Isstickersprimary::find()->where(['StickerCategoryID'=>15001])->limit(20)->all();
        
        foreach($stickerList as $sticker){
            $designerName = '';
            $brandName = '';
            $authorName = '';
            $shipmentTime = '';
            $materialCost = 0;
            $madeIn = 0;
            $priceVal = 0;
            $visibility = 1;
            $country = 0;
            $likes =0;
            $imgArr = array();
            $certArr = array();
            $dealerArray = array();
            
            if($sticker->StickerDescription != ''){
                $description = $sticker->StickerDescription;
            } else {
                $description = '';
            }
            
            if($sticker->StickerCode != ''){
                $code = $sticker->StickerCode;
            } else {
                $code = '';
            }
            
            if($sticker->StickerCategoryID == 15001){
                $designer = \app\models\Isproductinfo::find()->select(['DesignerName'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                if($designer){
                    $designerName = $designer->DesignerName;
                }
            } else if($sticker->StickerCategoryID == 15003){
                $designer = \app\models\Isdesigninfo::find()->select(['ExternalDesigner'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                if($designer){
                    $designerName = $designer->ExternalDesigner;
                }
            }
            
            if($sticker->DesignStyle){
                $designStyle = $sticker->designStyle->Value;
            } else {
                $designStyle = '';
            }
            
            $image = \app\models\Isstickerimagevideos::find()->select(['Url'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID, 'Media'=>1])->all();
            if($image){
                foreach($image as $img){
                    array_push($imgArr, $img->Url);
                }
            }
            
            if($sticker->StickerCategoryID == 15001 || $sticker->StickerCategoryID == 15004){
                $certificate = \app\models\Isstickerscertificates::find()->select(['CertificateType'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->all();
                if($certificate){
                    foreach($certificate as $cert){
                        array_push($certArr, $cert->certificateType->Value);
                    }
                }
            }
            
            if($sticker->StickerCategoryID == 15002){
                $author = \app\models\Isknowledgeinfo::find()->select(['AuthorID', 'IsInternalAuthor', 'ExternalAuthor'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                if($author){
                    if($author->IsInternalAuthor == 1){
                        $authorName = $author->author->FirstName.' '.$author->author->LastName;
                    } else {
                        $authorName = $author->ExternalAuthor;
                    }
                }
            }
            
            if($sticker->StickerCategoryID == 15001){
                $brand = \app\models\Isstickerbrandproductline::find()->select(['BrandID', 'ProductLineID', 'DShipment'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                if($brand){
                    $brandName = $brand->brand->BrandName;
                    $shipmentTime = $brand->DShipment;
                    if($brand->ProductLineID != ''){
                        $brandObj = \app\models\Plproductline::find()->select(['MadeinID'])->where(['IsActive' =>1, 'ID'=>$brand->ProductLineID])->one();
                        if($brandObj){
                            if($brandObj->MadeinID){
                                $madeIn = $brandObj->MadeinID;
                            }
                        }
                    } else {
                        $brandObj = \app\models\Plbrandsingleline::find()->select(['MadeInID'])->where(['IsActive' =>1, 'brandID'=>$brand->BrandID])->one();
                        if($brandObj){
                            if($brandObj->MadeInID){
                                $madeIn = $brandObj->MadeInID;
                            }
                        }
                    }
                }
            }
            
            $likes = Commonfacade::getstickerLikes($sticker->ID);
            
            $finalArray = array(
                    'id'=>$sticker->ID,
                    'sticker_category_id'=>$sticker->StickerCategoryID,
                    'sticker_name'=>$sticker->StickerName,
                    'sticker_description'=>$description,
                    'subscriber_id'=>$sticker->SubscriberID,
                    'sticker_code'=>$code,
                    'design_style'=>$designStyle,
                    'product_category_id'=>$sticker->productCategory->Value,
                    'is_active'=>$sticker->IsActive,
                    'is_published'=>$sticker->IsPublished,
                    'brand'=>$brandName,
                    'shipment_time'=>"$shipmentTime",
                    'designer_name'=>$designerName,
                    'image_url'=>$imgArr,
                    'certificate_list'=>$certArr,
                    'author_name'=>$authorName,
                    'material_cost'=>$materialCost,
                    'made_in'=>$madeIn,
                    'country'=>$country,
                    'price'=>$priceVal,
                    'local_dealers'=>$dealerArray,
                    'visibility'=>$visibility,
                    'likes'=>$likes
                );
                
            if($sticker->StickerCategoryID == 15001){
                $this->pisData($sticker, $finalArray);
            } else if($sticker->StickerCategoryID == 15004){
                $this->sisData($sticker, $finalArray);
            } else {
                $territoryList = \app\models\Plsalesterritoriesviewership::find()->select(['CountryID', 'ID'])->where(['IsActive' =>1, 'SubscriberID'=>$sticker->SubscriberID])->all();
                if($territoryList){
                    foreach($territoryList as $territory){
                        $finalArray['country'] = $territory->CountryID;
                        $id = $sticker->ID.$territory->ID;
                        $this->postData($id, $finalArray, $sticker);
                    }
                } else {
                    $id = $sticker->ID;
                    $this->postData($id, $finalArray, $sticker);
                }
            } 
        }
    }
    
    public function pisData($sticker, $finalArray){
    $category = $sticker->subscriber->subscriberCategory->ID;
        $visibility = Commonfacade::getVisibility($sticker->SubscriberID);
        if($category == 4){
            $priceList = \app\models\Isproductprice::find()->select(['ID', 'PriceListCode', 'POR', 'StickerID'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->all();
            if($priceList){
                foreach($priceList as $price){
                    $territoryList = \app\models\Stickerpriceterritory::find()->select(['ID', 'TerritoryID', 'Price'])->where(['IsActive' =>1, 'StickerPriceId'=>$price->ID])->all();
                    if($territoryList){
                        foreach($territoryList as $territory){
                            $dealerArray1 = $this->getDealerLocations($territory);
                            $finalArray['local_dealers'] = $dealerArray1;
                            $finalArray['country'] = $territory->territory->CountryID;
                            if($price->POR == 0){
                                $finalArray['price'] = $territory->Price;
                            } else {
                                $finalArray['price'] = 0;
                            }
                            $finalArray['visibility'] = $visibility;
                            $id = $sticker->ID.$price->ID.$territory->TerritoryID;
                            $this->postData($id, $finalArray, $sticker);
                        }
                    } else {
                        $id = $sticker->ID;
                        $this->postData($id, $finalArray, $sticker);
                    }
                }
            } else {
                $id = $sticker->ID;
                $this->postData($id, $finalArray, $sticker);
            }
        } else {
            $price = \app\models\Isproductpriceothers::find()->select(['Price'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
            if($price){
                $territoryList = \app\models\Plsalesterritoriesviewership::find()->select(['CountryID', 'ID'])->where(['IsActive' =>1, 'SubscriberID'=>$sticker->SubscriberID])->all();
                if($territoryList){
                    foreach($territoryList as $territory){
                        $dealerArray1 = $this->getDealerLocations($territory);
                        $finalArray['local_dealers'] = $dealerArray1;
                        $finalArray['country'] = $territory->CountryID;
                        $finalArray['price'] = $price->Price;
                        $finalArray['visibility'] = $visibility;
                        
                        $id = $sticker->ID.$territory->ID;
                        $this->postData($id, $finalArray, $sticker);
                    }
                } else {
                    $id = $sticker->ID;
                    $this->postData($id, $finalArray, $sticker);
                }
            } else {
                $id = $sticker->ID;
                $this->postData($id, $finalArray, $sticker);
            }
        }
        return true;
    }
    
    public function sisData($sticker, $finalArray){
    $servicePrice = \app\models\Isserviceprice::find()->select(['IsMaterial', 'ID'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
        if($servicePrice){
            if($servicePrice->IsMaterial == 1){
                $finalArray['material_cost']=$servicePrice->IsMaterial;
            }
            $territoryList = \app\models\Servicestickerpriceterritory::find()->select(['TerritoryID', 'Price'])->where(['IsActive' =>1, 'StickerPriceID'=>$servicePrice->ID])->all();
            if($territoryList){
                foreach($territoryList as $territory){
                    $dealerArray1 = $this->getDealerLocations($territory);
                    $finalArray['local_dealers'] = $dealerArray1;
                    $finalArray['country'] = $territory->territory->CountryID;
                    $finalArray['price'] = $territory->Price;
                    $id = $sticker->ID.$territory->TerritoryID;
                    $this->postData($id, $finalArray, $sticker);
                }
            } else {
                $id = $sticker->ID;
                $this->postData($id, $finalArray, $sticker);
            }
        } else {
            $id = $sticker->ID;
            $this->postData($id, $finalArray, $sticker);
        }
    }
    
    public function getDealerLocations($territory){
        $dealerArray1 = array();
        $dealerList = \app\models\Plsalespartnerterritories::find()->select(['ID', 'All_Cities_ST', 'SalesTerritoryID'])->where(['IsActive' =>1, 'SalesTerritoryID'=>$territory->ID, 'POS'=>1])->all();
        if($dealerList){
            foreach($dealerList as $dealer){
                if($dealer->All_Cities_ST == 1){
                    if($dealer->salesTerritory->All_Cities == 1){
                        foreach($dealer->salesTerritory->country->cities as $city){
                            if(!in_array($city->Name, $dealerArray1)){
                                array_push($dealerArray1, $city->Name);
                            }
                        }
                    } else {
                       foreach($dealer->salesTerritory->plsalesterritorycities as $city){
                           if(!in_array($city->city->Name, $dealerArray1)){
                                array_push($dealerArray1, $city->city->Name);
                           }
                       } 
                    }
                } else {
                    foreach($dealer->plsalespartnerterritorycities as $city){
                        if(!in_array($city->city->Name, $dealerArray1)){
                            array_push($dealerArray1, $city->city->Name);
                        }
                    }
                }
            }
        }
        return $dealerArray1;
    }
    
    public function postData($id, $finalArray, $sticker){
        $cloudSearch = new \AwsCloudSearch\AwsCloudSearch(Yii::$app->params['DOMAIN'], Yii::$app->params['SERVER_LOCATION']);
        $documents = array();
        
        $document = new \AwsCloudSearch\Document\AddDocument($id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
        $documents[] = $document;
        $response = $cloudSearch->processDocuments($documents);
        if($response->success()) {
            $sticker->IsUploaded = 0;
            $sticker->save();
            //return $response;
        } else {
            print_r($response->getErrors());
        }
    }
     
     */
    
    public function getConversionRates() {
        $status = 2;
        
        $currencyList = \app\models\Currencies::find()->select(['ID', 'Name'])->where(['IsActive'=>1])->all();
        
        $obj = file_get_contents('https://openexchangerates.org/api/latest.json?app_id=5d6637de5b2b4b299c74a68aab901aef');
        $model1 = (json_decode($obj));
        $ratesArr = $model1->rates;
        
        foreach($currencyList as $currency){
            $var =  $currency->Name;
            if($ratesArr->$var){
                $value = $ratesArr->$var;
            }
            if($value){
                $model = \app\models\Isstickercurrency::find()->where(['CurrencyTypeID'=>$currency->ID])->one();
                if($model){
                    $model->Value = $value;
                    $model->Notes = $var;
                    $model->IsActive = 1;
                    $model->CreatedOn = gmdate('Y-m-d h:i:s', time());
                    $model->save();
                } else {
                    $model = new \app\models\Isstickercurrency();
                    $model->CurrencyTypeID = $currency->ID;
                    $model->Value = $value;
                    $model->Notes = $var;
                    $model->IsActive = 1;
                    $model->CreatedOn = gmdate('Y-m-d h:i:s', time());
                    $model->save();
                }
                
                
            }    
        }
        echo "Updated successfuly";
        die;
        
        
    }
    
    
    public function uploadDoc($stickerId) {
        $requestType = 'add';
        
        
        if($stickerId != ''){
            $stickerList = Isstickersprimary::find()->where(['ID'=>$stickerId])->all();
        } else {
            $stickerList = Isstickersprimary::find()->where(['IsUploaded'=>1])->all();
        }
        
        $visibilityArr = array();
        //$subscriberTypes = \app\models\Subscribertype::find()->select(['ID'])->where(['IsActive'=>1])->all();
        //if($subscriberTypes){
        //    foreach($subscriberTypes as $type){
        //        array_push($visibilityArr, $type->ID);
        //    }
        //}
        $is_all_visibility = 1;
        $documents = array();
        if($stickerList){
            
            foreach($stickerList as $sticker){
                $subscriberObj = \app\models\Subscribers::find()->select(['IsActive', 'ID', 'SubscriberTypeId'])->where(['ID'=>$sticker->SubscriberID])->one();
                $plVisibility = Plvisibility::find()->select(['ID', 'Visibility_Level', 'IsAll', 'StikcerType', 'SubscriberID'])->where(['SubscriberID'=>$subscriberObj->ID, 'StikcerType'=>$sticker->StickerCategoryID, 'IsActive'=>1])->one();
                
                if($plVisibility){
                    if($plVisibility->Visibility_Level == 13004 && $plVisibility->IsAll == 0){
                        $visibileTypesArr = \app\models\Plvisibilityb2bprojects::find()->select(['ID', 'Subscriber_type_id'])->where(['VisibilityID'=>$plVisibility->ID, 'IsActive'=>1])->all();
                        if($visibileTypesArr){
                            $visibilityArr = [];
                            $is_all_visibility = 0;
                            $self = $subscriberObj->SubscriberTypeId;
                            
                            array_push($visibilityArr, $self);
                            foreach($visibileTypesArr as $vTypes){
                                array_push($visibilityArr, $vTypes->Subscriber_type_id);
                            }
                        }
                    }
                }
                
                $designerName = '';
                $brandName = '';
                $authorName = '';
                $shipmentTime = '';
                $materialCost = 0;
                $isImage = 0;
                $madeIn = 0;
                $priceVal = 0;
                $visibility = 1;
                $projectTypeArr = array();
                $country = 0;
                $likes =0;
                $madeInCountry = 0;
                $imgArr = array();
                $certArr = array();
                $dealerArray = array();
                

                if($sticker->StickerDescription != ''){
                    $description = $sticker->StickerDescription;
                } else {
                    $description = '';
                }

                if($sticker->StickerCode != ''){
                    $code = $sticker->StickerCode;
                } else {
                    $code = '';
                }

                if($sticker->StickerCategoryID == 15001){
                    $designer = Isproductinfo::find()->select(['DesignerName'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                    if($designer){
                        $designerName = $designer->DesignerName;
                    }
                } else if($sticker->StickerCategoryID == 15003){
                    
                    $isknowledgeinfo = Isknowledgeinfo::find()->where(['StickerID' => $sticker->ID, 'IsActive' => 1])->one();
                    if(isset($isknowledgeinfo)) {
                        $IsInternalAuthor = $isknowledgeinfo->attributes['IsInternalAuthor'];
                        $knowledgeinfoID = $isknowledgeinfo->attributes['ID'];
                        $full_name = "";
                        if ($IsInternalAuthor == 2) {
                            
                            $externalAuthor = $isknowledgeinfo->attributes['ExternalAuthor'];
                            $Isexternalauthor = \app\models\Isexternalauthor::find()->where(['IsKnowledgeId' => $knowledgeinfoID, 'IsActive' => 1])->one();
                            if($Isexternalauthor){
                                $AuthorName = $Isexternalauthor->attributes['AuthorName'];
                                $designerName = $AuthorName;
                            }
                        } else {
                            $AuthorID = $isknowledgeinfo->attributes['AuthorID'];
                            if ($AuthorID != '') {
                                $users = Users::find()->select(['FirstName', 'LastName'])->where(['id' => $AuthorID, 'IsActive' => 1])->one();
                                if (isset($users)) {
                                    //$full_name = \app\facades\masters\MasterFacade::remove_safe($users->attributes['FirstName']) . " " . \app\facades\masters\MasterFacade::remove_safe($users->attributes['LastName']);
                                    $full_name = $users->attributes['FirstName']." ". $users->attributes['LastName'];
                                }
                            }
                            $designerName = $full_name;
                        }
                    }
                    
                    //$designer = Isdesigninfo::find()->select(['ExternalDesigner'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                    //if($designer){
                    //    $designerName = $designer->ExternalDesigner;
                    //}
                }

                if($sticker->DesignStyle){
                    $designStyle = $sticker->designStyle->Value;
                } else {
                    $designStyle = '';
                }

                $image = Isstickerimagevideos::find()->select(['Url'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID, 'Media'=>1, 'IsDisplay'=>1])->all();
                if($image){
                    foreach($image as $img){
                        $isImage = 1;
                        array_push($imgArr, $img->Url);
                    }
                }

                if($sticker->StickerCategoryID == 15001 || $sticker->StickerCategoryID == 15004){
                    $certificate = Isstickerscertificates::find()->select(['CertificateType'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->all();
                    if($certificate){
                        foreach($certificate as $cert){
                            if(isset($cert->certificateType->Value))
                                array_push($certArr, $cert->certificateType->Value);
                        }
                    }
                }

                if($sticker->StickerCategoryID == 15002){
                    $author = Isknowledgeinfo::find()->select(['AuthorID', 'IsInternalAuthor', 'ExternalAuthor'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                    if($author){
                        if($author->IsInternalAuthor == 1){
                            //$authorName = $author->author->FirstName.' '.$author->author->LastName;
                        } else {
                            //$authorName = $author->ExternalAuthor;
                        }
                    }
                }

                if($sticker->StickerCategoryID == 15001){
                    $brand = Isstickerbrandproductline::find()->select(['BrandID', 'ProductLineID', 'DShipment'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                    if($brand){
                        $brandName = $brand->brand->BrandName;
                        $shipmentTime = $brand->DShipment;
                        if($brand->ProductLineID != ''){
                            $brandObj = Plproductline::find()->select(['ID', 'MadeinID'])->where(['IsActive' =>1, 'ID'=>$brand->ProductLineID])->one();
                            if($brandObj){
                                if($brandObj->MadeinID){
                                    $madeIn = $brandObj->MadeinID;
                                    $madeInCountry = $brandObj->madein->Name;
                                }
                                $projectTypeList = \app\models\Plproductlineprojecttypes::find()->select(['ProjectType'])->where(['IsActive' =>1, 'ProductLineID'=>$brandObj->ID])->all();
                                
                                if($projectTypeList){
                                    foreach($projectTypeList as $type){
                                        array_push($projectTypeArr, $type->projectType->Value);
                                    }
                                }
                            }
                        } else {
                            $brandObj = Plbrandsingleline::find()->select(['MadeInID', 'brandID'])->where(['IsActive' =>1, 'brandID'=>$brand->BrandID])->one();
                            if($brandObj){
                                if($brandObj->MadeInID){
                                    $madeIn = $brandObj->MadeInID;
                                    $madeInCountry = $brandObj->madeIn->Name;
                                }
                                $projectTypeList = \app\models\Plbrandprojecttypes::find()->select(['ProjectType'])->where(['IsActive' =>1, 'BrandID'=>$brandObj->brandID])->all();
                                if($projectTypeList){
                                    foreach($projectTypeList as $type){
                                        array_push($projectTypeArr, $type->projectType->Value);
                                    }
                                }
                            }
                        }
                    }
                }
                
                $likes = Commonfacade::getstickerLikes($sticker->ID);

                $finalArray = array(
                        'id'=>$sticker->ID,
                        'sticker_category_id'=>$sticker->StickerCategoryID,
                        'sticker_name'=>$sticker->StickerName,
                        'sticker_description'=>$description,
                        'subscriber_id'=>$sticker->SubscriberID,
                        'sticker_code'=>$code,
                        'design_style'=>$designStyle,
                        'product_category_id'=>$sticker->productCategory->Value,
                        'is_active'=>$sticker->IsActive,
                        'is_published'=>$sticker->IsPublished,
                        'brand'=>$brandName,
                        'shipment_time'=>"$shipmentTime",
                        'designer_name'=>$designerName,
                        'image_url'=>$imgArr,
                        'certificate_list'=>$certArr,
                        'author_name'=>$authorName,
                        'material_cost'=>$materialCost,
                        'made_in'=>$madeIn,
                        'country'=>$country,
                        'price'=>$priceVal,
                        'local_dealers'=>$dealerArray,
                        'visibility'=>$visibility,
                        'is_image'=>$isImage,
                        'likes'=>$likes,
                        'made_in_country'=>$madeInCountry,
                        'project_type'=>$projectTypeArr,
                        'is_search'=>1,
                        'is_first'=>1,
                        'subscriber_types'=>$visibilityArr,
                        'is_all_visibility'=>$is_all_visibility
                    );

                if($sticker->StickerCategoryID == 15001){ ////////////// FOR PIS ////////////////////////
                    
                    $category = $sticker->subscriber->subscriberCategory->ID;
                    $visibility = Commonfacade::getVisibility($sticker->SubscriberID);
                    
                    if($category == 4){
                        $h = 0;
                        $tempVar = array();
                        $priceList = Isproductprice::find()->select(['ID', 'PriceListCode', 'POR', 'StickerID', 'IsActive'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->all();
                        if($priceList){
                            foreach($priceList as $price){
                                $territoryList = Stickerpriceterritory::find()->select(['ID', 'TerritoryID', 'Price', 'IsActive'])->where(['StickerPriceId'=>$price->ID])->all();
                                if($territoryList){
                                    foreach($territoryList as $territory){
                                        
                                        $document1 = new \AwsCloudSearch\Document\AddDocument("delete", $sticker->ID, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                                        $documents[] = $document1;
                    
                                        $model = \app\models\Plsalesterritoriesviewership::find()->select(['ID', 'CountryID', 'IsActive'])->where(['ID'=>$territory->TerritoryID])->one();
                                        if($model){
                                            if (in_array($model->CountryID, $tempVar)) {
                                                $finalArray['is_search'] = 0;
                                            } else {
                                                $finalArray['is_search'] = 1;
                                                array_push($tempVar, $model->CountryID);
                                            }
                                            $finalArray['country'] = $model->CountryID;
                                        }
                                        $dealerArray1 = $this->getDealerLocations($territory->TerritoryID, $sticker->SubscriberID);
                                        $finalArray['local_dealers'] = $dealerArray1;
                                        
                                        if($price->POR == 0){
                                            $finalArray['price'] = $territory->Price;
                                        } else {
                                            $finalArray['price'] = 0;
                                        }
                                        
                                        if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0 || $subscriberObj->IsActive == 2 || $territory->IsActive == 0){
                                            $requestType = "delete";
                                        } else {
                                            $requestType = "add";
                                            if($h == 0){
                                            $finalArray['is_first'] = 1;
                                            } else {
                                                $finalArray['is_first'] = 0;
                                            }
                                            $h = $h+1;
                                        }
                                        
                                        $finalArray['visibility'] = $visibility;
                                        
                                        $id = $sticker->ID.$price->ID.$territory->TerritoryID;
                                        $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                                        $documents[] = $document;
                                    }
                                } else {
                                    if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0){
                                        $requestType = "delete";
                                    } else {
                                        $requestType = "add";
                                    }
                                    $finalArray['visibility'] = $visibility;
                                    $id = $sticker->ID;
                                    $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                                    $documents[] = $document;
                                }
                            }
                        } else {
                            if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0){
                                $requestType = "delete";
                            } else {
                                $requestType = "add";
                            }
                            $finalArray['visibility'] = $visibility;         
                            $id = $sticker->ID;
                            $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                            $documents[] = $document;
                        }
                        
                    } else {
                        $r = 0;
                        $price = \app\models\Isproductpriceothers::find()->select(['Price', 'IsActive'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                        if($price){
                            $territoryList = \app\models\Plsalesterritoriesviewership::find()->select(['CountryID', 'ID', 'IsActive'])->where(['SubscriberID'=>$sticker->SubscriberID])->all();
                            
                            if($territoryList){
                                foreach($territoryList as $territory){
                                    
                                    $document1 = new \AwsCloudSearch\Document\AddDocument("delete", $sticker->ID, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                                    $documents[] = $document1;
                                        
                                    $dealerArray1 = $this->getDealerLocations($territory->ID, $sticker->SubscriberID);
                                    $finalArray['local_dealers'] = $dealerArray1;
                                    $finalArray['country'] = $territory->CountryID;
                                    $finalArray['price'] = $price->Price;
                                    
                                    if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0 || $territory->IsActive == 0){
                                        $requestType = "delete";
                                    } else {
                                        if($r == 0){
                                        $finalArray['is_first'] = 1;
                                        } else {
                                            $finalArray['is_first'] = 0;
                                        }
                                        $r = $r+1;
                                        $requestType = "add";
                                    }
                                    
                                    $id = $sticker->ID.$territory->ID;
                                    $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                                    $documents[] = $document;
                                }
                            } else {
                                if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0){
                                    $requestType = "delete";
                                } else {
                                    $requestType = "add";
                                }
                                $finalArray['price'] = $price->Price;
                                $id = $sticker->ID;
                                $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                                $documents[] = $document;
                            } 
                        } else {
                            if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0){
                                $requestType = "delete";
                            } else {
                                $requestType = "add";
                            }
                            $id = $sticker->ID;
                            $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                            $documents[] = $document;
                        }
                    }
                } else if($sticker->StickerCategoryID == 15004){  ////////////// FOR SIS ////////////////////////
                    $servicePrice = \app\models\Isserviceprice::find()->select(['IsMaterial', 'ID', 'Price', 'IsActive'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                    if($servicePrice){
                        if($servicePrice->IsMaterial == 1){
                            $finalArray['material_cost']=$servicePrice->IsMaterial;
                        }
                        
                        $territoryList = \app\models\Servicestickerpriceterritory::find()->select(['TerritoryID', 'Price', 'IsActive'])->where(['StickerPriceID'=>$servicePrice->ID])->all();
                        
                        if($territoryList){
                            $s = 0;
                            foreach($territoryList as $territory){
                                $document1 = new \AwsCloudSearch\Document\AddDocument("delete", $sticker->ID, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                                $documents[] = $document1;
                                
                                //$dealerArray1 = $this->getDealerLocations($territory->ID, $sticker->SubscriberID);
                                //$finalArray['local_dealers'] = $dealerArray1;
                                if($territory->TerritoryID){
                                    if($territory->territory){
                                        $finalArray['country'] = $territory->territory->CountryID;
                                    }
                                }
                                if($territory->Price != ''){
                                    $finalArray['price'] = $territory->Price;
                                } else {
                                    $finalArray['price'] = 0;
                                }
                                
                                if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0 || $territory->IsActive == 0){
                                    $requestType = "delete";
                                } else {
                                    if($s == 0){
                                        $finalArray['is_first'] = 1;
                                    } else {
                                        $finalArray['is_first'] = 0;
                                    }
                                    $s = $s+1;
                                    $requestType = "add";
                                }
                                $id = $sticker->ID.$territory->TerritoryID;
                                $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                                $documents[] = $document;
                            }
                        } else {
                            if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0){
                                $requestType = "delete";
                            } else {
                                $requestType = "add";
                            }
                            $id = $sticker->ID;
                            $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                            $documents[] = $document;
                        }
                    } else {
                        if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0){
                            $requestType = "delete";
                        } else {
                            $requestType = "add";
                        }
                        $id = $sticker->ID;
                        $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                        $documents[] = $document;
                    }
                } else { ////////////// FOR KIS & DIS ////////////////////////
                    $territoryList = \app\models\Plsalesterritoriesviewership::find()->select(['CountryID', 'ID', 'IsActive'])->where(['SubscriberID'=>$sticker->SubscriberID])->all();
                    if($territoryList){
                        $k = 0;
                        foreach($territoryList as $territory){
                            
                            $document1 = new \AwsCloudSearch\Document\AddDocument("delete", $sticker->ID, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                            $documents[] = $document1;
                                
                            if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $territory->IsActive == 0 || $subscriberObj->IsActive == 0){
                                $requestType = "delete";
                            } else {
                                $requestType = "add";
                                $k++;
                                if($k > 1){
                                    $finalArray['is_first'] = 0;
                                } else {
                                    $finalArray['is_first'] = 1;
                                }
                            }
                            
                            $finalArray['country'] = $territory->CountryID;
                            $id = $sticker->ID.$territory->ID;
                            $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                            $documents[] = $document;
                        }
                    } else {
                        if($sticker->IsActive == 0 || $sticker->IsActive == 2 || $subscriberObj->IsActive == 0){
                            $requestType = "delete";
                        } else {
                            $requestType = "add";
                        }
                        $id = $sticker->ID;
                        $document = new \AwsCloudSearch\Document\AddDocument($requestType, $id, Yii::$app->params['VERSION'], Yii::$app->params['LANG'], $finalArray);
                        $documents[] = $document;
                    }
                } 
            }
            //print_r($documents);die;
            $this->postData($documents, $stickerList, $stickerId);
        } else {
            print_r('No Sticker available');die;
            $finalArray = new Status("No stickers available.");
            return $finalArray;
        }
    }
    
    public function postData($documents, $stickerList, $id){
        
        
        $cloudSearch = new \AwsCloudSearch\AwsCloudSearch(Yii::$app->params['DOMAIN'], Yii::$app->params['SERVER_LOCATION']);
        $response = $cloudSearch->processDocuments($documents);
        if($response->success()) {
            foreach($stickerList as $sticker){
                $sticker->IsUploaded = 0;
                $sticker->save();
            }
            if($id == ''){
                print_r('Stickers moved to cloud successfully');die;
            } else {
                return true;
                //print_r('Stickers moved to cloud successfully');die;
            }
        } else {
            print_r($response->getErrors());
        }
    }
    
    public function getDealerLocations($territory, $subId){
        
        $dealerArray1 = array();
        
        
        $territoryObj = \app\models\Plsalesterritoriesviewership::find()->select(['ID', 'SubscriberID', 'All_Cities', 'CountryID'])->where(['ID'=>$territory, 'IsActive'=>1, 'POS'=>1])->one();
        if($territoryObj){
            if($territoryObj->All_Cities == 1){
                $cityList = \app\models\Cities::find()->select(['ID', 'Name'])->where(['IsActive' =>1, 'CountryId'=>$territoryObj->CountryID])->all();
                if($cityList){ 
                    foreach($cityList as $city){
                        array_push($dealerArray1, $city->Name);
                    }
                }
            } else {
                $cityList = \app\models\Plsalesterritorycities::find()->select(['ID', 'CityID', 'SalesTerritoryID'])->where(['IsActive' =>1, 'SalesTerritoryID'=>$territoryObj->ID])->all();
                if($cityList){ 
                    foreach($cityList as $city){
                        array_push($dealerArray1, $city->city->Name);
                    }
                }
            }
        }
        
        $dealerList = \app\models\Plsalespartnerterritories::find()->select(['ID', 'All_Cities_ST', 'SalesTerritoryID'])
                        ->where(['IsActive' =>1, 'SalesTerritoryID'=>$territory, 'POS'=>1, 'SubscriberID'=>$subId])->all();
        if($dealerList){
            foreach($dealerList as $dealer){
                if($dealer->All_Cities_ST == 1){
                    if($dealer->salesTerritory->All_Cities == 1){
                        foreach($dealer->salesTerritory->country->cities as $city){
                            if(!in_array($city->Name, $dealerArray1)){
                                array_push($dealerArray1, $city->Name);
                            }
                        }
                    } else {
                        $cityList = \app\models\Plsalesterritorycities::find()->select(['ID', 'CityID', 'SalesTerritoryID'])->where(['IsActive' =>1, 'SalesTerritoryID'=>$dealer->SalesTerritoryID])->all();
                        if($cityList){
                            foreach($cityList as $city){
                                if(!in_array($city->city->Name, $dealerArray1)){
                                    array_push($dealerArray1, $city->city->Name);
                                }
                            }
                        }
                        //foreach($dealer->salesTerritory->plsalesterritorycities as $city){
                        //   if(!in_array($city->city->Name, $dealerArray1)){
                        //        array_push($dealerArray1, $city->city->Name);
                        //   }
                        //} 
                    }
                } else {
                    $cityList = \app\models\Plsalespartnerterritorycities::find()->select(['ID', 'CityID', 'SalesPartnerTerritoryID'])->where(['IsActive' =>1, 'SalesPartnerTerritoryID'=>$dealer->ID])->all();
                    if($cityList){
                        foreach($cityList as $city){
                            if(!in_array($city->city->Name, $dealerArray1)){
                                array_push($dealerArray1, $city->city->Name);
                            }
                        }
                    }
                    
                    //foreach($dealer->plsalespartnerterritorycities as $city){
                    //    if(!in_array($city->city->Name, $dealerArray1)){
                    //        array_push($dealerArray1, $city->city->Name);
                    //    }
                    //}
                }
            }
        }
        return $dealerArray1;
    }
    
    
    
    
    /////////////////////////////////////////////////CLOUD SEARCH START//////////////////////////////////////////////
    /*
    * function for searching data from amazon cloud
    * @author: Waseem
    */
    public static function stickerCloudSearch($catId, $request, $userId) {
        $role = 1;
        $user = Users::find()->select(['SubscribersId'])->where(['ID' => $userId, 'IsActive' => 1])->one();
        $role = $user->subscribers->SubscriberCategoryId;
        $subsTypeId = $user->subscribers->SubscriberTypeId;
        
        
        
        $status = 2;
        $cloudSearch = new \AwsCloudSearch\AwsCloudSearch(Yii::$app->params['DOMAIN'], Yii::$app->params['SERVER_LOCATION']);
        
        if($user){
            
            $searchFields = array('id', 'sticker_category_id', 'image_url', 'visibility', 'subscriber_id');
            
            ////////////////////// COMMON////////////////////////////
            $term = $request->post('term');
            $start = $request->post('start');
            if($start < 0){
                $start = 0;
            }
            
            $projectCity = '';
            $projectCountrySearch = '';
            $projectId = $request->post('projectId');
            if($projectId){
                $wbProject = \app\models\Wbproject::find()->select(['Country', 'City'])->where(['IsActive' =>1, 'ID'=>$projectId])->one();
                if($wbProject){
                    $projectCountry = $wbProject->Country;
                    if($request->post('localDealer') == 1){
                        $projectCity = $wbProject->city->Name;
                    }
                    $projectCountrySearch = "country:$projectCountry";
                }
                $groupBy = "is_search:1";
            } else {
                $groupBy = "is_first:1";
            }
            
            
            $src = intval($request->post('fromSource'));
            if($src == 0){
                $limit = 10;
            } else {
                $limit = 5;
            }
            
            
            if($request->post('subscriberId') != ''){
                $id = $request->post('subscriberId');
                $subId = "subscriber_id: $id";
            } else {
                $subId = '';
            }
            
            
            //////////////////////// PIS ///////////////////////
            $productCategory = $request->post('productCategory');
            $brand = $request->post('brand');
            $designerName = $request->post('designerName');
            $madeIn = $request->post('madeIn');
            $isPublished = $request->post('isPublished');
            $min = $request->post('min');
            $max = $request->post('max');
            
            $certificate = $request->post('certificate');
            if($certificate == 1){
                $type = 'Green Certificate';
            } else {
                $type = '';
            }
            
            $isCountry = $request->post('myCountry');
            if($isCountry == 1){
                $country = $user->subscribers->CountryId;
            } else {
                $country = '';
            }
            
            
            
            //////////////////////// KIS ///////////////////////
            
            $topic = $request->post('topic');
            $authorName = $request->post('author');
            
            //////////////////////// DIS ///////////////////////
            $designStyle = $request->post('designStyle');
            
            //////////////////////// SIS ///////////////////////
            $isMaterial = $request->post('isMaterial');
            
            
            if($catId == 15001){
                // MAnufactuter
                if($role == 4 && $catId == 15001){
                    $isPublished = 0;
                } else {
                    $isPublished = $isPublished;
                }
                // MP
                if($role == 5 && $catId == 15001){
                    $isAllowed = \app\models\Plsalespartnerassociations::find()->where(['PartnerID' =>$user->SubscribersId, 'IsActive' => 1, 'Association_Status' => 10000036])->exists();
                    if($isAllowed){
                        $isPublished = $isPublished;
                    } else {
                        $isPublished = 0;
                    }
                }
                
                
                $criteria = "$catId $term $productCategory $brand $designerName $type $projectCity";
                
                if($isPublished == 2){
                    if($madeIn != '' && $min != '' && $country != ''){
                        $advanced = "(and $groupBy is_active:1 made_in:$madeIn price:['$min', '$max'] (or $projectCountrySearch country:$country) (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($madeIn != '' && $min != ''){
                        $advanced = "(and $groupBy is_active:1 $projectCountrySearch made_in:$madeIn price:['$min', '$max'] (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($madeIn != '' && $country != ''){
                        $advanced = "(and $groupBy is_active:1 made_in:$madeIn (or $projectCountrySearch country:$country) (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($min != '' && $country != ''){
                        $advanced = "(and $groupBy is_active:1 price:['$min', '$max'] (or $projectCountrySearch country:$country) (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($madeIn != ''){
                        $advanced = "(and $groupBy is_active:1 $projectCountrySearch made_in:$madeIn (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($min != ''){
                        $advanced = "(and $groupBy is_active:1 $projectCountrySearch price:['$min', '$max'] (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($country != ''){
                        $advanced = "(and $groupBy is_active:1 (or $projectCountrySearch country:$country) (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else {
                        $advanced = "(and $groupBy is_active:1 $projectCountrySearch (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    }
                } else if($isPublished == 1){
                    if($madeIn != '' && $min != '' && $country != ''){
                        $advanced = "(and $groupBy is_published:1 is_active:1 $subId made_in:$madeIn price:['$min', '$max'] (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($madeIn != '' && $min != ''){
                        $advanced = "(and $groupBy is_published:1 is_active:1 $subId $projectCountrySearch made_in:$madeIn price:['$min', '$max'] (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($madeIn != '' && $country != ''){
                        $advanced = "(and $groupBy is_published:1 is_active:1 $subId made_in:$madeIn (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($min != '' && $country != ''){
                        $advanced = "(and $groupBy is_published:1 is_active:1 $subId price:['$min', '$max'] (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($madeIn != ''){
                        $advanced = "(and $groupBy is_published:1 is_active:1 $subId $projectCountrySearch made_in:$madeIn (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($min != ''){
                        $advanced = "(and $groupBy is_published:1 is_active:1 $subId $projectCountrySearch price:['$min', '$max'] (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($country != ''){
                        $advanced = "(and $groupBy is_published:1 is_active:1 $subId (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else {
                        $advanced = "(and $groupBy is_published:1 is_active:1 $subId $projectCountrySearch (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    }
                } else {
                    $groupBy = "is_first:1";
                    if($madeIn != '' && $min != '' && $country != ''){
                        //$advanced = "(and $groupBy is_active:1 made_in:$madeIn price:['$min', '$max'] subscriber_id: $user->SubscribersId (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 made_in:$madeIn price:['$min', '$max'] subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($madeIn != '' && $min != ''){
                        //$advanced = "(and $groupBy is_active:1 $projectCountrySearch made_in:$madeIn price:['$min', '$max'] subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 made_in:$madeIn price:['$min', '$max'] subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($madeIn != '' && $country != ''){
                        //$advanced = "(and $groupBy is_active:1 made_in:$madeIn subscriber_id: $user->SubscribersId (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 made_in:$madeIn subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($min != '' && $country != ''){
                        //$advanced = "(and $groupBy is_active:1 price:['$min', '$max'] subscriber_id: $user->SubscribersId (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 price:['$min', '$max'] subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($madeIn != ''){
                        //$advanced = "(and $groupBy is_active:1 $projectCountrySearch made_in:$madeIn subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 made_in:$madeIn subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($min != ''){
                        //$advanced = "(and $groupBy is_active:1 $projectCountrySearch price:['$min', '$max'] subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 price:['$min', '$max'] subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else if($country != ''){
                        //$advanced = "(and $groupBy is_active:1 (or $projectCountrySearch country:$country) subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else {
                        //$advanced = "(and $groupBy is_active:1 $projectCountrySearch subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    }
                }
            } else if ($catId == 15002){
                $criteria = "$catId $term $productCategory $topic $authorName";
                if($isPublished == 2){
                    $advanced = "(and $groupBy is_active:1 $projectCountrySearch (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else if($isPublished == 1){
                    $advanced = "(and $groupBy is_published:1 is_active:1 $projectCountrySearch (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else {
                    $groupBy = "is_first:1";
                    //$advanced = "(and $groupBy is_active:1 $projectCountrySearch subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    $advanced = "(and $groupBy is_active:1 subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                }
            } else if ($catId == 15003){
                $criteria = "$catId $term $productCategory $designStyle $designerName";
                if($isPublished == 2){
                    $advanced = "(and $groupBy is_active:1 $projectCountrySearch (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else if($isPublished == 1){
                    $advanced = "(and $groupBy is_published:1 is_active:1 $projectCountrySearch (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else {
                    $groupBy = "is_first:1";
                    //$advanced = "(and $groupBy is_active:1 $projectCountrySearch subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    $advanced = "(and $groupBy is_active:1 subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                }
            } else if ($catId == 15004){
                $criteria = "$catId $term $productCategory $type $projectCity";
                if($isPublished == 2){
                    if($isMaterial == 1){
                        $advanced = "(and $groupBy is_active:1 $projectCountrySearch material_cost: 1 (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else {
                        $advanced = "(and $groupBy is_active:1 $projectCountrySearch (or subscriber_id: $user->SubscribersId is_published: 1) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    }
                } else if($isPublished == 1){
                    if($isMaterial == 1){
                        $advanced = "(and $groupBy is_published:1 is_active:1 $projectCountrySearch material_cost: 1 (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else {
                        $advanced = "(and $groupBy is_published:1 is_active:1 $projectCountrySearch (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    }
                } else {
                    $groupBy = "is_first:1";
                    if($isMaterial == 1){
                        //$advanced = "(and $groupBy is_active:1 $projectCountrySearch material_cost: 1 subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 material_cost: 1 subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    } else {
                        //$advanced = "(and $groupBy is_active:1 $projectCountrySearch subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                        $advanced = "(and $groupBy is_active:1 subscriber_id: $user->SubscribersId (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                    }
                }
            }
            
            if($request->post('sortBy') == 1){
                $sortBy = 'price';
                $order  = 'desc';
            } else if ($request->post('sortBy') == 2){
                $sortBy = 'price';
                $order  = 'asc';
            } else if ($request->post('sortBy') == 3){
                $sortBy = 'shipment_time';
                $order  = 'asc';
            } else if($request->post('sortBy') == 4){
                $sortBy = 'id';
                $order  = 'desc';
            } else if($request->post('sortBy') == 5){
                $sortBy = 'likes';
                $order  = 'desc';
            } else {
                $sortBy = 'self';
                $order = '';
            }
            
            
            $cloudSearch->setReturnFields($searchFields);
            $response = $cloudSearch->search($criteria, $sortBy, $order, $start, $advanced, $limit);
            if($response->wasSuccessful()) {
                $status = 0;
                //json_encode($response->getHitDocuments());
                return $response->getHitDocuments();
            } else {
                print_r($response);die;
                print_r($response->getErrors());
            }
        }    
    }
    
    
    /*
    * function for getting all stickers of a subscriber by ID
    * @author: Waseem
    */
    public static function getAllStickerBySubscriberId($catId, $request, $userId) {
        $role = 1;
        $status = 2;
        
        $user = Users::find()->select(['SubscribersId'])->where(['ID' => $userId, 'IsActive' => 1])->one();
        $role = $user->subscribers->SubscriberCategoryId;
        $subsTypeId = $user->subscribers->SubscriberTypeId;
        
        $cloudSearch = new \AwsCloudSearch\AwsCloudSearch(Yii::$app->params['DOMAIN'], Yii::$app->params['SERVER_LOCATION']);
        
        if($user){
            $searchFields = array('id', 'sticker_category_id', 'image_url', 'visibility', 'subscriber_id');
            
            ////////////////////// COMMON////////////////////////////
            $term = $request->post('term');
            $start = $request->post('start');
            if($start < 0){
                $start = 0;
            }
            
            $projectCity = '';
            $projectCountrySearch = '';
            $projectId = $request->post('projectId');
            if($projectId){
                $wbProject = \app\models\Wbproject::find()->select(['Country', 'City'])->where(['IsActive' =>1, 'ID'=>$projectId])->one();
                if($wbProject){
                    $projectCountry = $wbProject->Country;
                    if($request->post('localDealer') == 1){
                        $projectCity = $wbProject->city->Name;
                    }
                    $projectCountrySearch = "country:$projectCountry";
                }
                $groupBy = "is_search:1";
            } else {
                $groupBy = "is_first:1";
            }
            
            
            $src = intval($request->post('fromSource'));
            if($src == 0){
                $limit = 10;
            } else {
                $limit = 5;
            }
            
            
            if($request->post('subscriberId') != ''){
                $id = $request->post('subscriberId');
                $subId = "subscriber_id: $id";
            } else {
                $subId = '';
            }
            
            if($user->SubscribersId != $subId){
                $publishedFlag = "is_published: 1";
            } else {
                $publishedFlag = '';
            }
            
            //////////////////////// PIS ///////////////////////
            $productCategory = $request->post('productCategory');
            $brand = $request->post('brand');
            $designerName = $request->post('designerName');
            $madeIn = $request->post('madeIn');
            $isPublished = $request->post('isPublished');
            $min = $request->post('min');
            $max = $request->post('max');
            
            $certificate = $request->post('certificate');
            if($certificate == 1){
                $type = 'Green Certificate';
            } else {
                $type = '';
            }
            
            $isCountry = $request->post('myCountry');
            if($isCountry == 1){
                $country = $user->subscribers->CountryId;
            } else {
                $country = '';
            }
            
            //////////////////////// KIS ///////////////////////
            $topic = $request->post('topic');
            $authorName = $request->post('author');
            
            //////////////////////// DIS ///////////////////////
            $designStyle = $request->post('designStyle');
            
            //////////////////////// SIS ///////////////////////
            $isMaterial = $request->post('isMaterial');
            
            
            if($catId == 15001){
                $criteria = "$catId $term $productCategory $brand $designerName $type $projectCity";
                if($madeIn != '' && $min != '' && $country != ''){
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 made_in:$madeIn price:['$min', '$max'] (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else if($madeIn != '' && $min != ''){
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 $projectCountrySearch made_in:$madeIn price:['$min', '$max'] (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else if($madeIn != '' && $country != ''){
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 made_in:$madeIn (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else if($min != '' && $country != ''){
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 price:['$min', '$max'] (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else if($madeIn != ''){
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 $projectCountrySearch made_in:$madeIn (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else if($min != ''){
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 $projectCountrySearch price:['$min', '$max'] (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else if($country != ''){
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 (or $projectCountrySearch country:$country) (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else {
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 $projectCountrySearch (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                }
            } else if ($catId == 15002){
                $criteria = "$catId $term $productCategory $topic $authorName";
                $advanced = "(and $groupBy is_active:1 $publishedFlag $subId $projectCountrySearch (or subscriber_types: $subsTypeId is_all_visibility: 1))";
            } else if ($catId == 15003){
                $criteria = "$catId $term $productCategory $designStyle $designerName";
                $advanced = "(and $groupBy $publishedFlag $subId is_active:1 $projectCountrySearch (or subscriber_types: $subsTypeId is_all_visibility: 1))";
            } else if ($catId == 15004){
                $criteria = "$catId $term $productCategory $type $projectCity";
                if($isMaterial == 1){
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 $projectCountrySearch material_cost: 1 (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                } else {
                    $advanced = "(and $groupBy $publishedFlag $subId is_active:1 $projectCountrySearch (or subscriber_types: $subsTypeId is_all_visibility: 1))";
                }
            }
            
            if($request->post('sortBy') == 1){
                $sortBy = 'price';
                $order  = 'desc';
            } else if ($request->post('sortBy') == 2){
                $sortBy = 'price';
                $order  = 'asc';
            } else if ($request->post('sortBy') == 3){
                $sortBy = 'shipment_time';
                $order  = 'asc';
            } else if($request->post('sortBy') == 4){
                $sortBy = 'id';
                $order  = 'desc';
            } else if($request->post('sortBy') == 5){
                $sortBy = 'likes';
                $order  = 'desc';
            } else {
                $sortBy = 'self';
                $order = '';
            }
            
            
            $cloudSearch->setReturnFields($searchFields);
            $response = $cloudSearch->search($criteria, $sortBy, $order, $start, $advanced, $limit);
            if($response->wasSuccessful()) {
                $status = 0;
                //json_encode($response->getHitDocuments());
                return $response->getHitDocuments();
            } else {
                print_r($response);die;
                print_r($response->getErrors());
            }
        }   
        
        /*
        $status = 2;
        $cloudSearch = new \AwsCloudSearch\AwsCloudSearch(Yii::$app->params['DOMAIN'], Yii::$app->params['SERVER_LOCATION']);
        if($subId){
            $searchFields = array('id', 'sticker_category_id', 'image_url', 'visibility');
            $term = $request->post('term');
            
            $projectCountrySearch = '';
            $projectId = $request->post('projectId');
            if($projectId){
                $wbProject = \app\models\Wbproject::find()->select(['Country', 'City'])->where(['IsActive' =>1, 'ID'=>$projectId])->one();
                if($wbProject){
                    $projectCountry = $wbProject->Country;
                    $projectCountrySearch = "country:$projectCountry";
                }
                $groupBy = "is_search:1";
            } else {
                $groupBy = "is_first:1";
            }
            
            $criteria = "$term $catId";
            
            $advanced = "(and is_published:1 $projectCountrySearch $groupBy is_active:1 subscriber_id: $subId)";
            
            $start = 0;
            $sortBy = 'id';
            $order  = 'desc';
            $limit = 100;
            
            $cloudSearch->setReturnFields($searchFields);
            $response = $cloudSearch->search($criteria, $sortBy, $order, $start, $advanced, $limit);
            if($response->wasSuccessful()) {
                $status = 0;
                //json_encode($response->getHitDocuments());
                return $response->getHitDocuments();
            } else {
                print_r($response);die;
                print_r($response->getErrors());
            }
        }    
         * 
         * 
         */
    }
    
    
    /*
    * function for searching data from amazon cloud
    * @author: Waseem
    */
    public static function favouriteStickerSearch($catId, $request, $userId) {
        $user = Users::find()->select(['SubscribersId'])->where(['ID' => $userId, 'IsActive' => 1])->one();
        $status = 2;
        
        $role = Commonfacade::getRole($user);
        
        if($user){
            
            $finalArray = array();
            
            ////////////////////// COMMON////////////////////////////
            $sort = $request->post('sortBy');
            $term = $request->post('term');
            $start = $request->post('start');
            
            if($start < 0){
                $start = 0;
            }
            
            $projectCity = '';
            $projectId = $request->post('projectId');
            if($projectId){
                $wbProject = \app\models\Wbproject::find()->select(['Country', 'City'])->where(['IsActive' =>1, 'ID'=>$projectId])->one();
                if($wbProject){
                    $projectCountry = $wbProject->Country;
                    $projectCity = $wbProject->City;
                }
            }
            
            
            //////////////////////// PIS ///////////////////////
            $productCategory = $request->post('productCategory');
            $brand = $request->post('brand');
            $designerName = $request->post('designerName');
            $madeIn = $request->post('madeIn');
            $isPublished = $request->post('isPublished');
            $min = $request->post('min');
            $max = $request->post('max');
            $certificate = $request->post('certificate');
            $isCountry = $request->post('myCountry');
            $localDealer = $request->post('localDealer');
            
            //////////////////////// KIS ///////////////////////
            $topic = $request->post('topic');
            $authorName = $request->post('author');
            
            //////////////////////// DIS ///////////////////////
            $designStyle = $request->post('designStyle');
            
            //////////////////////// SIS ///////////////////////
            $isMaterial = $request->post('isMaterial');
            
            if($catId == 15001){
                $result = \app\models\Isstickerfavorites::find();
                $result->select('isstickerfavorites.StickerID, isstickerfavorites.ID, isstickersprimary.StickerCategoryID, isstickersprimary.SubscriberID');
                $result->innerJoin('isstickersprimary', 'isstickersprimary.ID = isstickerfavorites.StickerID');
                $result->where(['isstickerfavorites.IsActive' =>1, 'isstickerfavorites.StickerFavorites'=>1, 'isstickerfavorites.StickerCategoryID' =>$catId, 'isstickerfavorites.SubscriberID'=>$user->SubscribersId]);

                if($productCategory != ''){
                    $result->andWhere(['=', 'isstickersprimary.ProductCategoryID', $productCategory]);
                }
                
                if($brand != ''){
                    $result->innerJoin('isstickerbrandproductline', 'isstickerbrandproductline.StickerID = isstickerfavorites.StickerID');
                    $result->innerJoin('plbrands', 'plbrands.ID = isstickerbrandproductline.BrandID');
                    $result->andWhere(['LIKE', 'plbrands.BrandName', $brand]);
                    if($madeIn != ''){
                        $result->andWhere(['=', 'isstickerbrandproductline.MadeInCountry', $madeIn]);
                    }
                } else if($sort == 3){
                    $result->innerJoin('isstickerbrandproductline', 'isstickerbrandproductline.StickerID = isstickerfavorites.StickerID');
                    if($madeIn != ''){
                        $result->andWhere(['=', 'isstickerbrandproductline.MadeInCountry', $madeIn]);
                    }
                } else {
                    if($madeIn != ''){
                        $result->innerJoin('isstickerbrandproductline', 'isstickerbrandproductline.StickerID = isstickerfavorites.StickerID');
                        $result->andWhere(['=', 'isstickerbrandproductline.MadeInCountry', $madeIn]);
                    }
                } 

                if($designerName != ''){
                    $result->innerJoin('isproductinfo', 'isproductinfo.StickerID = isstickerfavorites.StickerID');
                    $result->andWhere(['LIKE', 'isproductinfo.DesignerName', $designerName]);
                }
                
                
                if($certificate == 1){
                    $result->innerJoin('isstickerscertificates', 'isstickerscertificates.StickerID = isstickerfavorites.StickerID');
                    $result->andWhere(['=', 'isstickerscertificates.CertificateType', 32001]);
                }
                
                //if(($isPublished == 2 && $sort == 1) || ($isPublished == 2 && $sort == 2) || ($isPublished == 2 && $max > 0)){
                if($isPublished == 2){
                    if(($projectId != '' && $sort == 1) || ($projectId != '' && $sort == 2)){
                        $result->leftJoin('isproductprice', 'isproductprice.StickerID = isstickerfavorites.StickerID');
                        $result->leftJoin('stickerpriceterritory', 'stickerpriceterritory.StickerPriceId = isproductprice.ID');
                        $result->andWhere(['=', 'stickerpriceterritory.IsActive', 1]);
                    
                        $result->leftJoin('plsalesterritoriesviewership', 'plsalesterritoriesviewership.ID = stickerpriceterritory.TerritoryID');
                        $result->andWhere(['=', 'plsalesterritoriesviewership.IsActive', 1]);
                        $result->leftJoin('countries', 'countries.ID = plsalesterritoriesviewership.CountryID');
                        if($isCountry == 1){
                            $result->where(['or',['=', 'countries.ID', $user->subscribers->CountryId], ['like', 'countries.ID', $projectCountry]]);
                        } else {
                            $result->andWhere(['=', 'countries.ID', $projectCountry]);
                        }
                        $result->andWhere(['between', 'stickerpriceterritory.Price', $min, $max]);
                    } else if(($projectId == '' && $sort == 1) || ($projectId == '' && $sort == 2)){
                        $result->leftJoin('isproductprice', 'isproductprice.StickerID = isstickerfavorites.StickerID');
                        $result->leftJoin('stickerpriceterritory', 'stickerpriceterritory.StickerPriceId = isproductprice.ID');
                        //$result->andWhere(['=', 'stickerpriceterritory.IsActive', 1]);
                        //$result->andWhere(['between', 'stickerpriceterritory.Price', $min, $max]);
                    }
                    
                } else if($isPublished == 1){
                    $result->andWhere(['=', 'isstickersprimary.IsPublished', 1]);
                    $result->innerJoin('isproductprice', 'isproductprice.StickerID = isstickerfavorites.StickerID');
                    $result->innerJoin('stickerpriceterritory', 'stickerpriceterritory.StickerPriceId = isproductprice.ID');
                    $result->andWhere(['=', 'stickerpriceterritory.IsActive', 1]);
                    if($projectId != ''){
                        $result->leftJoin('plsalesterritoriesviewership', 'plsalesterritoriesviewership.ID = stickerpriceterritory.TerritoryID');
                        $result->andWhere(['=', 'plsalesterritoriesviewership.IsActive', 1]);
                        $result->leftJoin('countries', 'countries.ID = plsalesterritoriesviewership.CountryID');
                        if($isCountry == 1){
                            $result->where(['or',['=', 'countries.ID', $user->subscribers->CountryId], ['like', 'countries.ID', $projectCountry]]);
                            //$result->andWhere(['=', 'countries.ID', $user->subscribers->CountryId]);
                        } else {
                            $result->andWhere(['=', 'countries.ID', $projectCountry]);
                        }
                    }
                    $result->andWhere(['between', 'stickerpriceterritory.Price', $min, $max]);
                } else if($isPublished == 0){
                    $result->andWhere(['=', 'isstickersprimary.SubscriberID', $user->SubscribersId]);
                    if($role == 'ManufacturerIndividual' ||  $role == 'Manufacturer' || $role == 'ManufacturerNonAdmin'){
                        $result->leftJoin('isproductprice', 'isproductprice.StickerID = isstickerfavorites.StickerID');
                        $result->leftJoin('stickerpriceterritory', 'stickerpriceterritory.StickerPriceId = isproductprice.ID');
                        $result->andWhere(['=', 'stickerpriceterritory.IsActive', 1]);
                        if($projectId != ''){
                            $result->leftJoin('plsalesterritoriesviewership', 'plsalesterritoriesviewership.ID = stickerpriceterritory.TerritoryID');
                            $result->andWhere(['=', 'plsalesterritoriesviewership.IsActive', 1]);
                            $result->leftJoin('countries', 'countries.ID = plsalesterritoriesviewership.CountryID');
                            if($isCountry == 1){
                                $result->where(['or',['=', 'countries.ID', $user->subscribers->CountryId], ['like', 'countries.ID', $projectCountry]]);
                            } else {
                                $result->andWhere(['=', 'countries.ID', $projectCountry]);
                            }
                        }
                        $result->andWhere(['between', 'stickerpriceterritory.Price', $min, $max]);
                    } else {
                        $result->leftJoin('isproductpriceothers', 'isproductpriceothers.StickerID = isstickerfavorites.StickerID');
                        $result->andWhere(['=', 'isproductpriceothers.IsActive', 1]);
                        $result->andWhere(['between', 'isproductpriceothers.Price', $min, $max]);
                    }
                }
                
                if($projectId != ''){
                    if($localDealer == 1){
                        $result->leftJoin('plsalespartnerterritories', 'plsalespartnerterritories.SalesTerritoryID = plsalesterritoriesviewership.ID');
                        $result->andWhere(['=', 'plsalespartnerterritories.IsActive', 1]);
                        $result->andWhere(['=', 'plsalespartnerterritories.POS', 1]);
                        $result->leftJoin('plsalespartnerterritorycities', 'plsalespartnerterritorycities.SalesPartnerTerritoryID = plsalespartnerterritories.ID');
                        $result->andWhere(['=', 'plsalespartnerterritorycities.IsActive', 1]);
                        $result->leftJoin('plsalesterritorycities', 'plsalesterritorycities.SalesTerritoryID = plsalesterritoriesviewership.ID');
                        $result->andWhere(['=', 'plsalesterritorycities.IsActive', 1]);
                        $result->where(['or',['=', 'plsalespartnerterritorycities.CityID', $projectCity], ['=', 'plsalesterritorycities.ID', $projectCity]]);
                    }
                }
                
                $result->limit(2022);
                $result->offset($start);
                
                
                if($sort == 1){
                    if($isPublished == 1 || $isPublished == 2){
                        $result->orderBy(['stickerpriceterritory.Price' => SORT_DESC]);    
                    } else {
                        $result->orderBy(['isproductpriceothers.Price' => SORT_DESC]);    
                    }
                } else if ($sort == 2){
                    if($isPublished == 1 || $isPublished == 2){
                        $result->orderBy(['stickerpriceterritory.Price' => SORT_ASC]);    
                    } else {
                        $result->orderBy(['isproductpriceothers.Price' => SORT_ASC]);    
                    }
                } else if ($sort == 3){
                    $result->orderBy(['isstickerbrandproductline.DShipment' => SORT_ASC]);
                } else if($sort == 4){
                    $result->orderBy(['isstickersprimary.ID' => SORT_DESC]);
                } else if($sort == 5){
                    $sortBy = 'likes';
                    $order  = 'desc';
                } else {
                    $result->orderBy(['isstickersprimary.ID' => SORT_DESC]);
                }
                $model = $result->all();
                                
            } else if ($catId == 15002){
                $result = \app\models\Isstickerfavorites::find();
                $result->select('isstickerfavorites.StickerID, isstickerfavorites.ID, isstickersprimary.StickerCategoryID, isstickersprimary.SubscriberID');
                $result->innerJoin('isstickersprimary', 'isstickersprimary.ID = isstickerfavorites.StickerID');
                $result->where(['isstickerfavorites.IsActive' =>1, 'isstickerfavorites.StickerFavorites'=>1, 'isstickerfavorites.StickerCategoryID' =>$catId, 'isstickerfavorites.SubscriberID'=>$user->SubscribersId]);

                if($productCategory != ''){
                    $result->andWhere(['=', 'isstickersprimary.ProductCategoryID', $productCategory]);
                }
                
                if($topic != ''){
                    $result->andWhere(['LIKE', 'isstickersprimary.StickerName', $topic]);
                }
                
                if($authorName != ''){
                    $result->innerJoin('isknowledgeinfo', 'isknowledgeinfo.StickerID = isstickerfavorites.StickerID');
                    $result->andWhere(['LIKE', 'isknowledgeinfo.AuthorName', $authorName]);
                }
                
                if($isPublished == 1){
                    $result->andWhere(['=', 'isstickersprimary.IsPublished', 1]);
                } else if($isPublished == 0){
                    $result->andWhere(['=', 'isstickersprimary.SubscriberID', $user->SubscribersId]);
                }
                
                if($sort == 5){
                    ///// LIKE SORT 
                    $result->orderBy(['isstickersprimary.ID' => SORT_DESC]);
                } else {
                    $result->orderBy(['isstickersprimary.ID' => SORT_DESC]);
                }
                $result->limit(12);
                $result->offset($start);
                $model = $result->all();
                
                
            } else if ($catId == 15003){
                $result = \app\models\Isstickerfavorites::find();
                $result->select('isstickerfavorites.StickerID, isstickerfavorites.ID, isstickersprimary.StickerCategoryID, isstickersprimary.SubscriberID');
                $result->innerJoin('isstickersprimary', 'isstickersprimary.ID = isstickerfavorites.StickerID');
                $result->where(['isstickerfavorites.IsActive' =>1, 'isstickerfavorites.StickerFavorites'=>1, 'isstickerfavorites.StickerCategoryID' =>$catId, 'isstickerfavorites.SubscriberID'=>$user->SubscribersId]);

                if($productCategory != ''){
                    $result->andWhere(['=', 'isstickersprimary.ProductCategoryID', $productCategory]);
                }
                
                if($designStyle != ''){
                    $result->andWhere(['=', 'isstickersprimary.DesignStyle', $designStyle]);
                }
                
                if($designerName != ''){
                    $result->innerJoin('isknowledgeinfo', 'isknowledgeinfo.StickerID = isstickerfavorites.StickerID');
                    $result->andWhere(['LIKE', 'isknowledgeinfo.AuthorName', $authorName]);
                }
                
                if($isPublished == 1){
                    $result->andWhere(['=', 'isstickersprimary.IsPublished', 1]);
                } else if($isPublished == 0){
                    $result->andWhere(['=', 'isstickersprimary.SubscriberID', $user->SubscribersId]);
                }
                if($sort == 5){
                    $result->orderBy(['isstickersprimary.ID' => SORT_DESC]);
                } else {
                    $result->orderBy(['isstickersprimary.ID' => SORT_DESC]);
                }
                $result->limit(12);
                $result->offset($start);
                $model = $result->all();
                
            } else if ($catId == 15004){
                $result = \app\models\Isstickerfavorites::find();
                $result->select('isstickerfavorites.StickerID, isstickerfavorites.ID, isstickersprimary.StickerCategoryID, isstickersprimary.SubscriberID');
                $result->innerJoin('isstickersprimary', 'isstickersprimary.ID = isstickerfavorites.StickerID');
                $result->where(['isstickerfavorites.IsActive' =>1, 'isstickerfavorites.StickerFavorites'=>1, 'isstickerfavorites.StickerCategoryID' =>$catId, 'isstickerfavorites.SubscriberID'=>$user->SubscribersId]);

                if($productCategory != ''){
                    $result->andWhere(['=', 'isstickersprimary.ProductCategoryID', $productCategory]);
                }
                
                if($certificate == 1){
                    $result->innerJoin('isstickerscertificates', 'isstickerscertificates.StickerID = isstickerfavorites.StickerID');
                    $result->andWhere(['=', 'isstickerscertificates.CertificateType', 32001]);
                }
                
                if($isPublished == 1){
                    $result->andWhere(['=', 'isstickersprimary.IsPublished', 1]);
                } else if($isPublished == 0){
                    $result->andWhere(['=', 'isstickersprimary.SubscriberID', $user->SubscribersId]);
                }
                
                $result->innerJoin('isserviceprice', 'isserviceprice.StickerID = isstickerfavorites.StickerID');
                $result->innerJoin('servicestickerpriceterritory', 'servicestickerpriceterritory.StickerPriceID = isserviceprice.ID');
                $result->andWhere(['=', 'servicestickerpriceterritory.IsActive', 1]);
                if($projectId != ''){
                    $result->leftJoin('plsalesterritoriesviewership', 'plsalesterritoriesviewership.ID = servicestickerpriceterritory.TerritoryID');
                    $result->andWhere(['=', 'plsalesterritoriesviewership.IsActive', 1]);
                    $result->leftJoin('countries', 'countries.ID = plsalesterritoriesviewership.CountryID');
                    $result->andWhere(['=', 'countries.ID', $projectCountry]);
                }
                
                if($isMaterial == 1){
                    $result->andWhere(['=', 'isserviceprice.IsMaterial', 1]);
                }
                
                $result->limit(30);
                $result->offset($start);
                if($sort == 1){
                    $result->orderBy(['servicestickerpriceterritory.Price' => SORT_DESC]);    
                } else if ($sort == 2){
                    $result->orderBy(['servicestickerpriceterritory.Price' => SORT_ASC]);  
                } else if($sort == 4){
                    $result->orderBy(['isstickersprimary.ID' => SORT_DESC]);
                } else if($sort == 5){
                    $sortBy = 'likes';
                    $order  = 'desc';
                } else {
                    $result->orderBy(['isstickersprimary.ID' => SORT_DESC]);
                }
                
                $model = $result->all();
            }
            
            
            foreach($model as $obj){
                $images = Isstickerimagevideos::find()->select(['Url'])->where(['IsActive' =>1, 'StickerID'=>$obj->StickerID, 'Media'=>1])->all();
                $imgArr = array();
                foreach($images as $img){
                    array_push($imgArr, $img->Url);
                }
                $tempArray = array('id'=>$obj->StickerID, 'sticker_category_id'=>$obj->StickerCategoryID, 'image_url'=>$imgArr, 'visibility'=>1, 'subscriber_id'=>$obj->SubscriberID);
                $fieldsArray = array('fields'=>$tempArray);
                array_push($finalArray, $fieldsArray);
            }
            
            return $finalArray;
            
            
        }    
    }
    
    
    
    
    
    /*
     * function for getting subscriber type
     * @author: Waseem
     */
    public static function getType($user){
        $data = array();
        $data['type'] = $user->subscribers->SubscriberTypeId;
        $data['isCorp'] = $user->subscribers->IsCorporate;
        return $data;
    }
     /*
     * function for getting subscriber role
     * @author: Waseem
     */
    public static function getRole($user){
        $type = $user->subscribers->subscriberCategory->ID;
        $isCorporate = $user->subscribers->IsCorporate;
        
        $roleId = Users::find()->select(['RoleId'])->where(['ID' => $user->ID])->one();
        
        
        if($roleId){
            $isAdmin =$roleId->RoleId;
        } else {
            $isAdmin = '';
        }
        
       // return array('type'=>$type, 'isCorporate' =>$isCorporate, 'roleId' =>$roleId, 'isadmin'=>$isAdmin );
        $role = '';
        
        if($type == '1' && $isCorporate == '1' &&  $isAdmin == '1'){
            $role = 'IDAFirm';
        }
        
        if($type == '1' && $isCorporate == '1' &&  $isAdmin != '1'){
            $role = 'IDAFirmNonAdmin';
        }
        
        if($type == '1' && $isCorporate == '0'){
            $role = 'IDAIndividual';
        }
        
        
        if($type == '2' && $isCorporate == '1' && $isAdmin == '1'){
            $role = 'Consultant';
        }
        
        if($type == '2' && $isCorporate == '1' && $isAdmin != '1'){
            $role = 'ConsultantNonAdmin';
        }
        
        if($type == '2' && $isCorporate == '0'){
            $role = 'ConsultantIndividual';
        }
        
        if($type == '3'){
            $role = 'Student';
        }
                
        if($type == '4' && $isCorporate == '1' && $isAdmin == '1'){
            $role = 'Manufacturer';
        }
        
        if($type == '4' && $isCorporate == '1' && $isAdmin != '1'){
            $role = 'ManufacturerNonAdmin';
        }
        
        if($type == '4' && $isCorporate == '0'){
            $role = 'ManufacturerIndividual';
        }
        
        if($type == '5' && $isCorporate == '1' && $isAdmin == '1'){
            $role = 'ManufacturerPartner';
        }
        
        if($type == '5' && $isCorporate == '1' && $isAdmin != '1'){
            $role = 'ManufacturerPartnerNonAdmin';
        }
        
        if($type == '5' && $isCorporate == '0'){
            $role = 'ManufacturerPartnerIndividual';
        }
        
        if($type == '6'){
            $role = 'Manufacturer';
        }
        
        return $role;
        
    }
    
    
    /*
     * function for getting plansubscriber role
     * @author: Megha
     */
    
     public static function getPlanSubscriberRole($user){
        $type = $user->subscribers->subscriberCategory->ID;
        $isCorporate = $user->subscribers->IsCorporate;
        
        $roleId = Users::find()->select(['RoleId'])->where(['ID' => $user->ID])->one();
        
        
        if($roleId){
            $isAdmin =$roleId->RoleId;
        } else {
            $isAdmin = '';
        }
        
       // return array('type'=>$type, 'isCorporate' =>$isCorporate, 'roleId' =>$roleId, 'isadmin'=>$isAdmin );
        $role = '';
        
        if($type == '1' && $isCorporate == '1' &&  $isAdmin == '1'){
            $role = 'IDAFirm';
        }
        
        if($type == '1' && $isCorporate == '1' &&  $isAdmin != '1'){
            $role = 'IDAFirmNonAdmin';
        }
        
        if($type == '1' && $isCorporate == '0'){
            $role = 'IDAIndividual';
        }
        
        
        if($type == '2' && $isCorporate == '1' && $isAdmin == '1'){
            $role = 'Consultant';
        }
        
        if($type == '2' && $isCorporate == '1' && $isAdmin != '1'){
            $role = 'ConsultantNonAdmin';
        }
        
        if($type == '2' && $isCorporate == '0'){
            $role = 'ConsultantIndividual';
        }
        
        if($type == '3'){
            
            $role = 'Student';
        }
                
        if($type == '4' && $isCorporate == '1' && $isAdmin == '1'){
            $role = 'Manufacturer';
        }
        
        if($type == '4' && $isCorporate == '1' && $isAdmin != '1'){
            $role = 'ManufacturerNonAdmin';
        }
        
        if($type == '4' && $isCorporate == '0'){
            $role = 'ManufacturerIndividual';
        }
        
        if($type == '5' && $isCorporate == '1' && $isAdmin == '1'){
            $role = 'ManufacturerPartner';
        }
        
        if($type == '5' && $isCorporate == '1' && $isAdmin != '1'){
            $role = 'ManufacturerPartnerNonAdmin';
        }
        
        if($type == '5' && $isCorporate == '0'){
            $role = 'ManufacturerPartnerIndividual';
        }
        
        if($type == '6'){
            $role = 'Manufacturer';
        }
        
        if($type == '1' && $isCorporate == '0' && $isAdmin == '7'){
            $role = 'IDAIndividual';
        }
        
         if($type == '3' && $isCorporate ==  '0' && $isAdmin == '7'){
            $role = 'Student';
        }
        
        if($type == '6' && $isCorporate ==  '1' && $isAdmin == '1'){
            $role = 'Contractor';
        }
        return $role; 
    }

    
     /*
     * function for getting late payment percent value 
     * @author: Waseem
     */
    public static function getLatePaymentCharge(){
        $today = gmdate('Y-m-d h:i:s', time()); 
        $latePayment = \app\models\HotLeadLatePaymentCharges::find()->select(['PrcntHotLeadFees'])
                        ->where(['IsActive' => 1])->one();
                        //->andWhere(['<', 'EffectiveFrom', $today])->one();
        
        if(!empty($latePayment)){
            $percent = $latePayment->PrcntHotLeadFees;
        } else {
            $percent = 0;
        }
        return $percent;
    }
    
     /*
     * function for getting no.of days after which late payment will be applicable
     * @author: Waseem
     */
    
    /*public static function getDaysCount(){
        $today = gmdate('Y-m-d h:i:s', time()); 
        $daysRule = \app\models\HotLeadGenerationRules::find()->select(['DaysAfterPinning'])
                        ->where(['IsActive' => 1])
                        ->andWhere(['<', 'EffectiveFrom', $today])->one();
        
        if(!empty($daysRule)){
            $days = $daysRule->DaysAfterPinning;
        } else {
            $days = 0;
        }
        return $days;
    }*/
    
    /*
     * function for getting IS Point Value
     * @author: Waseem
     */
    public static function getConfigurationValue($id){
        $config = \app\models\Configurations::find()->where(['ID' =>$id])->one();
        if(!empty($config)){
            $val = $config->Value;
        } else {
            $val = '';
        }
        return $val;
    }
    
    /*
     * function for getting Subscriber Points By User ID
     * @author: Waseem
     */
    
    public static function getPoints($userId){
        $status = 2;
        $earnedPoints = 0;
        $redeemedPoints = 0;
        $finalTotal = 0;
        
        $user = Users::find()->select(['SubscribersId'])->where(['ID'=>$userId])->one();
        if(!empty($user)){
            $points = \app\models\SubscriberPoints::find()->select(['Points'])->where(['SubscriberId'=>$user->SubscribersId])->all();
            foreach($points as $point){
                $earnedPoints = $earnedPoints+$point->Points; 
            }
            
            $pointsRedeemed = \app\models\Subscriberpointsredeemed::find()->select(['PointsRedeemed'])->where(['SubscriberId'=>$user->SubscribersId])->all();
            foreach($pointsRedeemed as $point){
                $redeemedPoints = $redeemedPoints+$point->PointsRedeemed; 
            }
            
            $finalTotal = $earnedPoints - $redeemedPoints;
            if($finalTotal <0 )
            {
               $finalTotal = 0; 
            }
            $status = 0;
        } 
        return $finalTotal;
    }
    
    /*
     * function for sending data to sendbox
     * @author: Waseem
     */
    
    public static function payment($data){
        if($data){
            $PGTransactionId = '123456';
            $PGAcknowledgement = 'success';
            $data = array('transactionId'=>$PGTransactionId, 'transactionStatus'=>$PGAcknowledgement);    
        } else {
            $PGAcknowledgement = 'failed';
            $data = array('transactionStatus'=>$PGAcknowledgement);    
        }
        
        return $data;
        
    }
    
    /*
     * for creating invoice name dynamically
     * @author: Waseem Khan
    */
    public static function generateInvoiceName($length = 3) {
        $today = gmdate('m-d-Y');
        $characters = '123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        $randomString = $randomString.'-'.$today;
        return $randomString;
    }
    
    /*
     * for getting user's selected date format
     * @author: Waseem Khan
    */
    public static function getUserDateFormat($userId){
        $dateFormat = '';
        $userPref = \app\models\Userpreferences::find()->select(['DateFormatId'])->where(['UserId'=>$userId])->one();
        if(!empty($userPref->DateFormatId)){
            $dateFormat = $userPref->dateFormat->Value;
        }
        return $dateFormat;
    }
    
    /*
     * for getting user's preferred language
     * @author: Waseem Khan
    */
    
    
    
      public static function getUnitConversion($userId , $dimensionData , $measurementUnit){
       // ALL LENGTH IN METRIC SYSTEM WILL BE SAVED INTO INCHES AND ALL WEIGHTS INTO POUNDS
            $finalArray = array();
                switch ($measurementUnit)
                {
                    case 10:
                    $converted = ($dimensionData * 0.393701);
                    $uom = $measurementUnit;   
                    $rate = 0.393701;
                    break;
                
                    case 11:
                    $converted = ($dimensionData * 39.3701 );
                    $uom = $measurementUnit;    
                    $rate = 39.3701;
                    break;
                
                    case 12:
                    $converted = ($dimensionData * 39370.079 );
                    $uom = $measurementUnit;   
                    $rate = 39370.079;
                    break;
                
                    case 9:
                    $converted = ($dimensionData *  2.20462 );
                    $uom = $measurementUnit;   
                    $rate = 2.20462;
                    break;
                
                    case 8:
                    $converted = ($dimensionData *  2204.62 );
                    $uom = $measurementUnit;   
                    $rate = 2204.62 ;
                    break;
                
                    default:
                    $converted = $dimensionData;
                    $uom = $measurementUnit;    
                    $rate = 1 ;
                    break;
                }
            $data = array($converted, $uom, $rate);
            return($data);

    }
    
    
    
    
    
    
    
    
    
    //COnverting to English System before Saving into Database
    // Code : Shashank Saxena
    
    
    
      public static function getUserUnitPreference($userId , $dimensionData , $measurementUnit , $measurementType){
        
        $finalArray = array();
        $userpreferences = Userpreferences::find()->where(['UserId' => $userId, 'IsActive' => 1])->one();
         
         if(!empty($userpreferences->attributes['UOMId'])){
          $UOMId = $userpreferences->attributes['UOMId'];
          if($UOMId == '1')
          {
              $converted = $dimensionData;
              $uom = $measurementUnit;  
              $data = array($converted, $uom);
          }
          else
          {

       if($measurementType = '15000091')
       {
           print_r($measurementUnit);
           die;
                    switch ($measurementUnit)
                    {
                        case 4:
                        $converted = ($dimensionData * 0.0254 );
                        $uom = 4;    
                        break;

                        case 2:
                        $converted = ($dimensionData * 0.453592 );
                        $uom = 4;    
                        break;

                        default:
                        $converted = $dimensionData;
                        $uom = $measurementUnit;    
                        break;
                    }
            }
            
            else if($measurementType = '15000092')
            {
           print_r($measurementUnit);
           die; 
                      switch ($measurementUnit)
                    {
                        case 4:
                        $converted = ($dimensionData * 0.0254 );
                        $uom = 4;    
                        break;

                        case 2:
                        $converted = ($dimensionData * 0.453592 );
                        $uom = 4;    
                        break;

                        default:
                        $converted = $dimensionData;
                        $uom = $measurementUnit;    
                        break;
                    }
                
            }
        
                $data = array($converted, $uom);

              
          }
        //  print_r($data);
          return($data);
          
         }
    }
    
    /*
     * for getting user's preferred date format
     * @author: Waseem Khan
    */
    public static function getUserLanguage($userId){
        $language = 14002;
        $userPref = \app\models\Userpreferences::find()->select(['LanguageId'])->where(['UserId'=>$userId])->one();
        if(!empty($userPref->LanguageId)){
            $language = $userPref->LanguageId;
        }
        return $language;
    }
    
    /*
     * for converting dates to the format selected by user in preference
     * @author: Waseem Khan
    */
    public static function convertDate($dateFormat, $date){
        if($dateFormat == 'mm-dd-yyyy'){
            $convertedDate = date('m-d-Y', strtotime($date));
        } else if($dateFormat == 'dd-mm-yyyy'){
            $convertedDate = date('d-m-Y', strtotime($date));
        } else if($dateFormat == 'yyyy-mm-dd'){
            $convertedDate = date('Y-m-d', strtotime($date));
        } else {
            $convertedDate = date('d-m-Y', strtotime($date));
        }
        return $convertedDate;
    }
    
    public function getLookupValue($id){
        $lookup = Lookups::find()->select(['Value'])->where(['ID'=>$id, 'IsActive'=>1])->one();
        if($lookup){
            return $lookup->Value;
        }else
            return false;
    }
    
    /*
     * for getting device type on the basis of screen size
     * @author: Waseem Khan
    */
    public static function getDeviceType($width, $height){
        
        //if(($width > 300 && $width < 700) && ($height > 400 && $height < 1000)){
        //    $media = 1;
        //} else {
        //    $media = 2;
        //}
        if($width >= 1024 && $width <= 1365){
            $media = 1;
        } else if($width >= 1366 && $width <= 1999) {
            $media = 2;
        } else {
            $media = '';
        }
        return $media;
        
    }
    
    /*
     * for getting no. of likes of a sticker
     * @author: Waseem Khan
    */
    public static function getstickerLikes($Id) {
       $stickercount=Isstickerlikesdislikes::find()
               ->where(['StickerID'=>$Id,'StickerLikes'=>1])
               ->groupBy(['CreatedBy'])
               ->count();
       return $stickercount;
    }
   
    /*
     * for getting visibility status set by the subscriber
     * @author: Waseem Khan
    */
    public static function getVisibility($subscriberId){
        $visibility = 1;
        $stickerVisibility = Plvisibility::find()->select(['Visibility_Level'])->where(['SubscriberID' =>$subscriberId, 'StikcerType' =>15001, 'IsActive'=>1])->one();
        if($stickerVisibility){
            if($stickerVisibility->Visibility_Level == 13003){
                $visibility = 0;
            }
        }    
        return $visibility;
    }
    
    /*
     * for getting currency conversion rate by fetching planned currency from plan section
     * @author: Waseem
     */ 
    public static function getConversionRate($subscriberId){
        $conversion = 1;
        $currency = 1;
        $symbol = '$';
        
        $symbolArray = array(
            'AUD'=>'$', 'BRL'=>'R$', 
            'CAD'=>'$', 'CHF'=>'Fr', 'CNY'=>'¥', 'EUR'=>'€', 'GBP'=>'£', 'HKD'=>'$', 'INR'=>'₹',
            'JPY'=>'¥', 'MXN'=>'$', 'NOK'=>'kr', 'NZD'=>'$', 'USD'=>'$', 'ZAR'=> 'R', 'TRY'=>'₤', 'SGD'=>'$',
            'KRW'=>'₩', 'JOD'=>'د.ا', 'KWD'=>'د.ك', 'SAR'=>'ر.س', 'AED'=>'د.إ'
        );
        
        $plannedCurrency = \app\models\Plstickersettings::find()->select(['PricingCurrency'])->where(['SubscriberID' =>$subscriberId, 'IsActive' => 1])->one();
        
        if($plannedCurrency){
            if($plannedCurrency->PricingCurrency){
                $currency = $plannedCurrency->PricingCurrency;
                $symbol = $plannedCurrency->pricingCurrency->Name;
            }
        } else {
            $user = \app\models\Users::find()->select(['SubscribersId', 'ID'])->where(['SubscribersId' =>$subscriberId, 'IsActive' => 1])->one();
            if($user){
                $preffredCurrency = \app\models\Userpreferences::find()->select(['CurrencyId'])->where(['UserId' =>$user->ID, 'IsActive' => 1])->one();
                if($preffredCurrency){
                    if($preffredCurrency->CurrencyId){
                        $currency = $preffredCurrency->CurrencyId;
                        $symbol = $preffredCurrency->currency->Name;
                    }
                }
            }
        }
        
        
        
        $model = \app\models\Isstickercurrency::find()->select(['Value'])->where(['CurrencyTypeID' =>$currency])->one();
        if($model){
            $conversion = $model->Value;
        }    
        
        if(array_key_exists($symbol, $symbolArray)){
            $symbol = $symbolArray[$symbol];
        }

        $data = array($conversion, $symbol);
        return $data;
        //return $conversion;
    }
    
    /*
     * for getting currency conversion rate by fetching preferred currency selected by user in preference
     * @author: Waseem
     */ 
    public static function getPrefferedConversionRate($userId){
        $conversion = 1;
        $currency = 1;
        $symbol = '$';
        
        $symbolArray = array(
            'AUD'=>'$', 'BRL'=>'R$', 
            'CAD'=>'$', 'CHF'=>'Fr', 'CNY'=>'¥', 'EUR'=>'€', 'GBP'=>'£', 'HKD'=>'$', 'INR'=>'₹',
            'JPY'=>'¥', 'MXN'=>'$', 'NOK'=>'kr', 'NZD'=>'$', 'USD'=>'$', 'ZAR'=> 'R', 'TRY'=>'₤', 'SGD'=>'$',
            'KRW'=>'₩', 'JOD'=>'د.ا', 'KWD'=>'د.ك', 'SAR'=>'ر.س', 'AED'=>'د.إ'
        );
        
        $preffredCurrency = \app\models\Userpreferences::find()->select(['CurrencyId'])->where(['UserId' =>$userId, 'IsActive' => 1])->one();
        if($preffredCurrency){
            if($preffredCurrency->CurrencyId){
                $currency = $preffredCurrency->CurrencyId;
                $symbol = $preffredCurrency->currency->Name;
            }
        }
        
        
        $model = \app\models\Isstickercurrency::find()->select(['Value'])->where(['CurrencyTypeID' =>$currency])->one();
        if($model){
            $conversion = $model->Value;
        }
        
        
        if(array_key_exists($symbol, $symbolArray)){
            $symbol = $symbolArray[$symbol];
        }

        
        $data = array($conversion, $symbol);
        return $data;
    }
    
    
    /*
     * function for updating product categories
     * @author: Waseem
     */

    public function updateProductCategories() {
        $status = 2;
        $message = 'Failed';
        
        $categoryList = \app\models\Isstickersprimary::find()->select(['ProductCategoryID'])->where(['IsActive'=>1])
                        ->groupBy('ProductCategoryID')->all();
        
        if($categoryList){
            
            foreach($categoryList as $cat){
                $lookup = \app\models\Lookups::find()->select(['ParentLookupId', 'LookupTypeId', 'Value'])->where(['ID'=>$cat->ProductCategoryID, 'IsActive'=>1])->one();
                if($lookup){
                    $filtered = \app\models\Filteredproductcategories::find()->where(['CategoryId'=>$lookup->ParentLookupId])->one();
                    if($filtered){
                        $filtered->CategoryId = $lookup->ParentLookupId;
                        $filtered->Value = $lookup->parentLookup->Value;
                        $filtered->LookupTypeId = $lookup->LookupTypeId;
                        $filtered->IsActive = 1;
                        $filtered->CreatedOn = date("Y-m-d H:i:s");
                    } else {
                        $filtered = new \app\models\Filteredproductcategories();
                        $filtered->CategoryId = $lookup->ParentLookupId;
                        $filtered->Value = $lookup->Value;
                        $filtered->LookupTypeId = $lookup->LookupTypeId;
                        $filtered->IsActive = 1;
                        $filtered->CreatedOn = date("Y-m-d H:i:s");
                    }
                    if($filtered->save()){
                        $message = 'Success';
                    }
                }
            }
            
            foreach($categoryList as $cat){
                $lookup = \app\models\Lookups::find()->select(['LookupTypeId', 'ParentLookupId', 'Value', 'ID'])->where(['ID'=>$cat->ProductCategoryID, 'IsActive'=>1])->one();
                if($lookup){
                    $filtered = \app\models\Filteredproductcategories::find()->where(['CategoryId'=>$lookup->ID])->one();
                    if($filtered){
                        $filtered->CategoryId = $lookup->ID;
                        $filtered->Value = $lookup->Value;
                        $filtered->LookupTypeId = $lookup->LookupTypeId;
                        $filtered->ParentLookupId = $lookup->ParentLookupId;
                        $filtered->IsActive = 1;
                        $filtered->CreatedOn = date("Y-m-d H:i:s");
                    } else {
                        $filtered = new \app\models\Filteredproductcategories();
                        $filtered->CategoryId = $lookup->ID;
                        $filtered->Value = $lookup->Value;
                        $filtered->LookupTypeId = $lookup->LookupTypeId;
                        $filtered->ParentLookupId = $lookup->ParentLookupId;
                        $filtered->IsActive = 1;
                        $filtered->CreatedOn = date("Y-m-d H:i:s");
                    }
                    if($filtered->save()){
                        $message = 'Success';
                    }
                }
            }
        }
        print_r($message);die;
    }
    
    
    /*
     * function for getting the product category list
     * @author: Abhay
     * @date:01/04/2015
     */

    public function getFilteredCategories($id=97) {
        $lookuparray = array();

        $lookups = \app\models\Filteredproductcategories::find()->where(['LookupTypeId' => $id, 'IsActive' => 1])->all();
        
        foreach ($lookups as $lookups1) {
            $chield_exist = \app\models\Filteredproductcategories::find()->where(['ParentLookupId' => $lookups1['CategoryId'], 'IsActive'=>1])->exists();
            if ($chield_exist) {
                $inode = true;
            } else {
                $inode = false;
            }
            $open = false;

            $lookup['open'] = $open;
            $lookup['inode'] = $inode;
            $lookup['id'] = $lookups1['CategoryId'];
            $lookup['checkbox'] = false;
            $lookup['radio'] = true;

            if ($lookups1['ParentLookupId'] != '') {
                $lookup['parent'] = $lookups1['ParentLookupId'];
                //$lookup['checkbox'] = false;
                $lookup['radio'] = true;
            } else {
                $lookup['parent'] = 0;
                //$lookup['checkbox'] = false;
                $lookup['radio'] = false;
            }
            $lookup['label'] = $lookups1['Value'];
            array_push($lookuparray, $lookup);
        }

        $new = array();
        foreach ($lookuparray as $a) {


            $new[$a['parent']][] = $a;
        }

        return $tree = $this->createTree($new, $new[0]);
    }
    
    public function createTree(&$list, $parent) {
        $tree = array();
        foreach ($parent as $k => $l) {
            if (isset($list[$l['id']])) {
                $l['branch'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }
    
    /*
     * Function for getting the list of product category in edit mode.
     * @author: Abhay
     * Date:27/04/2015
     */

    public function getFilteredCategoriesEdit($cat_id, $id) {

        $lookuparray = array();
        $lookups = \app\models\Filteredproductcategories::find()->where(['LookupTypeId' => $id, 'IsActive' => 1])->all();

        foreach ($lookups as $lookups1) {
            $chield_exist = \app\models\Filteredproductcategories::find()->where(['ParentLookupId' => $lookups1['CategoryId'], 'IsActive'=>1])->exists();
            if ($chield_exist) {
                $inode = true;
            } else {
                $inode = false;
            }
            $open = false;

            $lookup['open'] = $open;
            $lookup['inode'] = $inode;
            $lookup['id'] = $lookups1['CategoryId'];
            $lookup['checkbox'] = false;
            $lookup['radio'] = true;

            if ($lookups1['CategoryId'] == $cat_id) {

                $lookup['checked'] = true;
            } else {
                $lookup['checked'] = false;
            }

            if ($lookups1['ParentLookupId'] != '') {
                $lookup['parent'] = $lookups1['ParentLookupId'];

                $lookup['radio'] = true;
            } else {
                $lookup['parent'] = 0;

                $lookup['radio'] = false;
            }
            $lookup['label'] = $lookups1['Value'];
            array_push($lookuparray, $lookup);
        }

        $new = array();
        foreach ($lookuparray as $a) {


            $new[$a['parent']][] = $a;
        }

        return $tree = $this->createTree($new, $new[0]);
    }
    
    
    public function unpublishStickers() {
        $status = 'Success';
        
        $now = time(); 
        
        $leadList = \app\models\SubscriberHotLeads::find()->where(['Paid'=>0])->all();
        if($leadList){
            foreach($leadList as $list){
                $datediff = $now - strtotime($list->GeneratedOn);
                $diff = floor($datediff/(60*60*24));
                if($diff > 30){
                    Isstickersprimary::updateAll(['IsUploaded' => 1, 'IsPublished' =>0, 'ModifiedOn' => gmdate('Y-m-d h:i:s', time())], "(StickerCategoryID = 15001 OR StickerCategoryID = 15004) and SubscriberID = $list->SubscriberId");
                    $status = 'Success';
                }
            }
        } else {
            $status = 'No late leads';
        }
        print_r($status);die;
    }
    
    
    
}




        /*
        $query = new \yii\db\Query();
        $query->select([
                        "isstickersprimary.ID",
                        "isstickersprimary.StickerCategoryID",
                        "isstickersprimary.StickerName",
                        "isstickersprimary.StickerDescription",
                        "isstickersprimary.SubscriberID",
                        "isstickersprimary.StickerCode",
                        "isstickersprimary.StickerCode",
                        "isstickersprimary.DesignStyle",
                        "isstickersprimary.ProductCategoryID", 
                        "isstickersprimary.IsActive", 
                        "isstickersprimary.IsPublished", 
                        "isstickerbrandproductline.BrandID",
                        "isproductinfo.DesignerName",
                        //"isstickerimagevideos.Url", 
                        //"isstickerfinishes.FinishID"
                        ])
                    ->from('isstickersprimary')
                    //->join('LEFT OUTER JOIN', 'isstickerimagevideos', 'isstickerimagevideos.StickerID = isstickersprimary.ID')
                    //->join('LEFT OUTER JOIN', 'isstickerfinishes', 'isstickerfinishes.StickerID = isstickersprimary.ID')
                    ->join('LEFT OUTER JOIN', 'isstickerbrandproductline', 'isstickerbrandproductline.StickerID = isstickersprimary.ID')
                    ->join('LEFT OUTER JOIN', 'isproductinfo', 'isproductinfo.StickerID = isstickersprimary.ID')
                    ->where(['isstickersprimary.IsActive'=>1])
                    ->andWhere(['isstickersprimary.IsPublished'=>1]);
            
        $command = $query->createCommand();
        $data = $command->queryAll();
          print_r($data);die;  
        //return $data;
        */


/*
                $brand = \app\models\Isstickerbrandproductline::find()->select(['BrandID'])->where(['IsActive' =>1, 'StickerID'=>$sticker->ID])->one();
                if($brand){
                    $brandName = $brand->brand->BrandName;
                    if($brand->brand->IsProductLine == 1){
                        $madein = \app\models\Plproductline::find()->select(['MadeInID'])->where(['IsActive' =>1, 'BrandID'=>$brand->BrandID])->all();
                        if($madein){
                            foreach($madein as $made){
                                if($made->MadeinID){
                                    array_push($madeInArray, $made->madein->Name);
                                }
                            }
                        }
                    } else {
                        $madein = \app\models\Plbrandsingleline::find()->select(['MadeInID'])->where(['IsActive' =>1, 'brandID'=>$brand->BrandID])->one();
                        if($madein){
                            if($madein->MadeInID){
                                array_push($madeInArray, $madein->madeIn->Name);
                            }
                        }
                    }
                }
                 * */
                

