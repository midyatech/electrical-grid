<?php
class Helper
{
    //private function__construct() {}

    public static function Post($field, $empty_is_null=false)
    {
    	if(isset($_POST[$field]) ) { // && $_POST[$field]!=""
    		if($_POST[$field]=="" && $empty_is_null){
                return null;
            }
            else
                return $_POST[$field];
    	}else{
    		return null;
    	}
    }

    public static function Request($field, $empty_is_null=false)
    {
        if(isset($_REQUEST[$field])) { //&& $_REQUEST[$field]!=""
            if($_REQUEST[$field]=="" && $empty_is_null)
                return null;
            else
                return $_REQUEST[$field];
        }else{
            return null;
        }
    }

    //sets new condition in filter array
    public static function FilterArray(&$array, $key, $value, $op="=", $type="text", $conj="AND")
    {
    	if($value != NULL){
	    	$array[$key]=array(
				"Conj"=>$conj,
				"Operator"=>$op,
				"Value"=>$value,
				"Type"=>$type);
    	}
    }

    //retrieve only one column from an array
    function array_col(array $a, $x)
    {
        return array_map(function($a) use ($x) { return $a[$x]; }, $a);
    }

    public static function DateOrTime($s)
    {
        $dt = new DateTime($s);
        $date = $dt->format('Y-m-d');
        $time = $dt->format('H:i:s');

        $today = date("Y-m-d");
        if($date == $today){
            return $time;
        }else{
            return $date;
        }
    }
}



?>
