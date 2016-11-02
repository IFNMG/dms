<?php

namespace app\base;

use Yii;
//use yii\base\Module;
use yii\base\BootstrapInterface;

/*
/* The base class that you use to retrieve the settings from the database
*/

Class Configurations implements BootstrapInterface {

    private $db;

    public function __construct() {
        $this->db = Yii::$app->db;
    }

    /**
    * Bootstrap method to be called during application bootstrap stage.
    * Loads all the settings into the Yii::$app->params array
    * @param Application $app the application currently running
    */

    public function bootstrap($app) {

        // Get settings from database
        $sql = $this->db->createCommand("SELECT short_code,value FROM cc_configurations");
        $configurations = $sql->queryAll();

        // Now let's load the settings into the global params array

        foreach ($configurations as $key => $val) {
            Yii::$app->params['configurations'][$val['short_code']] = $val['value'];
        }

        
       // Yii::$app->params['configurations']['prachi']="hi prachi";
        //echo Yii::$app->params['settings']['prachi'];
    }

}?>