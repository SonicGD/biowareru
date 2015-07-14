<?php
namespace biowareru\common\components;


class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * Converts an exception into an array.
     *
     * @param \Exception $exception the exception being converted
     *
     * @return array the array representation of the exception.
     */
    protected function convertExceptionToArray($exception)
    {
        return parent::convertExceptionToArray($exception);
    }
}