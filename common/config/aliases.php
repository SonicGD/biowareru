<?php
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('admin', dirname(dirname(__DIR__)) . '/admin');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
//Yii::setAlias('bioengine', dirname(dirname(__DIR__)) . '/vendor/sonicgd/bioengine');
Yii::setAlias('bioengine', BIOENGINE_PATH);
Yii::setAlias('biowareru', dirname(dirname(__DIR__)));
Yii::setAlias('appVendor', dirname(dirname(__DIR__)) . '/vendor');
Yii::setAlias('appBower', dirname(dirname(__DIR__)) . '/vendor/bower');
Yii::setAlias('bower', dirname(dirname(__DIR__)) . '/vendor/bower');
