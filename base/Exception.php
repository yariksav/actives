<?php

namespace yariksav\actives\base;

class Exception extends \Exception {

    protected static function createAndThrow($message = null, $code = 0){
        $class = get_class();
        throw new $class($message, $code);
    }

    public static function fmt(){
        $args = func_get_args();
        if (is_array($args[0])){
            $args = $args[0];
        }
        $message = call_user_func_array('sprintf', $args);
        $class = get_class();
        return new $class($message);
    }

    public static function ifTrue($bool, $message = null, $code = 0){
        if ($bool) {
            $class = get_class();
            eval($class."::createAndThrow('$message', $code);");
        }
        return true;
    }

    public static function ifTrueFmt($bool, $message = null){
        if ($bool){
            $args = func_get_args();
            unset($args[0]);
            $class = get_class();
            $obj = eval($class."::fmt($args);");
            throw $obj;
        }
        return true;
    }

    public static function ifFalseFmt($bool, $message = null){
        if (!$bool){
            $args = func_get_args();
            unset($args[0]);
            $class = get_class();
            $obj = eval($class."::createFmt($args);");
            throw $obj;
        }
        return true;
    }

    public static function ifFalse($bool, $message = null, $code = 0){
        if (!$bool) {
            $class = get_class();
            eval($class."::createAndThrow('$message', $code);");
        }
    }
}
?>