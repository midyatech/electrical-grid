<?php
//include_once 'ExceptionHandling.php';
include_once 'Log.php';

/**
 * @author Miqdad
 *
 */
class MysqliDB{

	//state constants
	const ERROR = 0;
	const WARNING = 1;
	const SUCCESS = 2;

	const MIXED_RESULT = 2;
	const ASSOC_RESULT = 1;
	const ARRAY_RESULT = 0;

	public $sql;
	public $row;

	public $result;
	public $message;//Message returned from last operation
	public $state;//State of last operation
	public $result_count;
	protected $conn;// mysql_connect connection object
	protected $user;
	protected $host;
	protected $database;
	protected $password;
	Protected $persistant;
	protected $Log;
    protected $dbObj;

	protected $connection_type;

    public function __construct()
    {

        $this->Log = new Log();
        $result = array();

        $config = parse_ini_file('/etc/webconfig/config.ini');
        //print_r($config);

        $this->host = $config['host'];
        $this->database = $config['dbname'];
        $this->user = $config['username'];
        $this->password = $config['password'];
        $this->persistant = false;

        $this->conn = "";
        $this->message = "";
        $this->OpenConnection();
    }

    public function __destruct() {
	    $this->CloseConnection();
	}

    /**
     *Opens the connection string
     */
    function OpenConnection()
    {
        try{
            if($this->persistant==true){
                $this->conn = mysqli_connect("p:".$this->host, $this->user, $this->password, $this->database);
            }else{
                $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
            }

            if (!$this->conn) {
                $this->state = self::ERROR;
                $this->message = "db_connection_failed";
                $this->Log->Add($this->state, __METHOD__.", ".__LINE__, NULL, mysqli_connect_error());
            }else{
                $this->conn->set_charset('utf8');
                $this->state = self::SUCCESS;
                return true;
            }
        }catch(Exception $e){
            //echo $e->getMessage();
            $this->state = self::ERROR;
            $this->message = "db_connection_failed";
            $this->Log->Add($this->state, __METHOD__.", line: ".__LINE__, $e->getMessage(), $this->message);
            return false;
        }
    }

    public function CloseConnection()
    {
	    mysqli_close($this->conn);
		$this->conn = null;
        return true;
    }

    public function SqlVal($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        try
        {
            $theValue = $this->EscapeString($theValue);
            switch ($theType) {
                case "text":
                    $theValue = ($theValue != "" && $theValue != NULL) ? "'" . $theValue . "'" : NULL;
                    break;
                case "mytext":
                    $theValue = ($theValue != "" && $theValue != NULL) ? "" . $theValue . "" : NULL;
                    break;
                case "long":
                case "int":
                    $theValue = ($theValue != "") ? intval($theValue) : NULL;
                    break;
                case "double":
                    $theValue = ($theValue != "") ? doubleval($theValue) : NULL;
                    break;
                case "date":
                    $theValue = ($theValue != "") ? "DATE_FORMAT('" . $theValue . "', '%Y-%m-%d')" : NULL;
                    break;
                case "datetime":
                    $theValue = ($theValue != "") ? "DATE_FORMAT('" . $theValue . "', '%Y-%m-%d %k:%i:%s')" : NULL;
                    break;
                case "defined":
                    $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                    break;
                case "bind_param": //thisi s for prepared statements
                    $theValue = '?';
                    break;
            }
            return $theValue;
        }catch(Exception $e){
            $this->state = self::ERROR;
            $this->message = "db_sqlval_failed";
            $this->Log->Add($this->state, __METHOD__.", line: ".__LINE__, $e->getMessage().', SQL: ['.$theValue.']', NULL);
            return false;
        }
    }

    private function MapType($userType)
    {
        $mysqliType = "";
        switch ($userType) {
            case "text":
            case "mytext":
            case "date":
            case "datetime":
                $mysqliType = "s";
                break;
            case "long":
            case "int":
                $mysqliType = "i";
                break;
            case "double":
                $mysqliType = "b";
                break;
        }
        return $mysqliType;
    }

    Private function Detect($type)
    {
        //print $Item."***********".gettype($Item);
        switch ($type) {
            case 'NULL':
            case 'string':
                return 's';
                break;

            case 'integer':
                return 'i';
                break;

            case 'blob':
                return 'b';
                break;

            case 'double':
                return 'd';
                break;
        }
        return '';
    }

    protected function SortArrayOfArrays(&$array, $subfield)
    {
        $sortarray = array();
        foreach ($array as $key => $row)
        {
            $sortarray[$key] = isset($row[$subfield]) ? $row[$subfield] : "";
        }

        array_multisort($sortarray, SORT_ASC, $array);
    }

    private function ExtractConditionParams($conditionValue){
        $value = $conditionValue;

        $conj = $operator = $val = "";

        if(is_array($value)) //If value is passed as an assmysqlative array ("Conj", "Operator", "Value")
        {
            //Condition Conjuctor ("AND", "OR"), default is AND
            $conj = strtoupper((isset($value["Conj"]))? $value["Conj"]." " : "AND ");

            //Condition Operator, default is "="
            $operator = (isset($value["Operator"]))? " ".$value["Operator"]." " : " = ";

            //Condition Value, default is the string value of condition
            $val = (isset($value["Value"]))? $value["Value"] : $value;

            //Value type, default is "text"
            $type = (isset($value["Type"]))? $value["Type"] : "mytext";

            //escape value and prepare it to be used
            $val = $this->SqlVal($val, $type);

        } else { //if value is passed as string (simple condition with default: "Conj" is 'AND', and "Operator" is '=' and type is 'text')
            $conj = "AND ";
            $operator = " = ";
            $type ="mytext";
            $val = $value;
        }

        $conditionParams = array();
        $conditionParams["Conj"] = $conj;
        $conditionParams["Operator"] = $operator;
        $conditionParams["Type"] = $type;
        $conditionParams["Value"] = $val;

        return $conditionParams;
    }

    public function ValueExists($table, $field, $value, $excludedField=NULL, $excludedValue=NULL)
    {
        $sql = 'SELECT COUNT(`'.$field.'`) FROM `'.$table.'` WHERE `'.$field.'`='. $this->SqlVal($value, "text");
        if($excludedValue !== NULL){
            $sql .= ' AND `'.$excludedField.'` != '. $this->SqlVal($excludedValue, "text");
        }
        $res = $this->SelectValue($sql);
        if($res == "0"){
            $this->state = self::WARNING;
            $this->message = "db_value_not_exists";
            return false;
        }else if($res === FALSE){
            //error
            $this->state = self::ERROR;
            return NULL;
        }else{
            $this->state = self::SUCCESS;
            $this->message = "db_value_already_exists";
            return true;
        }
    }

    private function EscapeString($string)
    {
        $string = str_ireplace('"','', $string);
        $string = str_ireplace("'","", $string);
        $string = str_ireplace("=","", $string);
        $string = str_ireplace(' and', '', $string);
        $string = str_ireplace(' or', '', $string);
        $string = str_ireplace(' union', '', $string);
        $string = str_ireplace(' BENCHMARK', '', $string);
        $string = str_ireplace(' waitfor delay', '', $string);
        return $string;
    }

    protected function CheckConditions($params, &$condition, $include_all_params=false)
    {
		// echo "##<br>";
		// print_r($params);
		// echo "<br>##";
        if($params != null && count($params) > 0){
            foreach($params as $name=>$param){
                //if associative array
                if(is_array($param)){
                    //check if we need to include it in conditionStr
                    //if parameter is specified as condition, or all paramters are treated as condition
                    if(isset($param["in_where"]) || $include_all_params){
						// if (strpos($name, '#') !== false) {
						// 	$name_arr = explode("#", $name);
						// 	$name = $name_arr[0];
						// }
                        $conj = isset($param["conj"]) ? $param["conj"] : NULL;
                        $op = isset($param["op"]) ? $param["op"] : NULL;
                        $condition[$name] = array("Type"=>"bind_param", "Conj"=>$conj, "Operator"=>$op);
                    }
                }
            }
        }
    }

    public function ConvertToParamsArray($data=null, $condition=null)
    {
        $params = array();
        if($data !=null){
            foreach ($data as $key => $value) {
                $conditionParams = $this->ExtractConditionParams($value);
                $params[$key]= array("value"=> $conditionParams["Value"], "type"=>$conditionParams["Type"]);
            }
        }
        if($condition !=null){
            foreach ($condition as $key => $value) {
                $conditionParams = $this->ExtractConditionParams($value);
                $params[$key]= array("value"=> $conditionParams["Value"], "in_where"=>true, "op"=>$conditionParams["Operator"], "type"=>$conditionParams["Type"]);
            }
        }
        return $params;
    }

    //quick generate params array from single variable
    public function GenerateParam($val)
    {
        $condition = array();
		$condition[$this->GenerateRandomString()]= $val;
		$params = $this->ConvertToParamsArray($condition);
        return $params;
    }


    public function SetConditionString($condition)
    {
        $conditionStr = "";
        foreach ($condition as $key => $value) {

			if (strpos($key, '#') !== false) {
				$name_arr = explode("#", $key);
				$key = $name_arr[0];
			}

            //in case column name is passed with table name for ambiguity, don't enclose with double quotation
            if (strpos($key,'.') === false && 	strpos($key,'(') === false) {
                $key = '`'. $key .'`';
            }


            $value = $this->ExtractConditionParams($value);//get all paramerters of condition
            $val = $value["Value"];
            $operator = $value["Operator"];
            $type = $value["Type"];
            $conj = $value["Conj"];

            //compose condition string
            if($type == "date" ){
                $conditionStr.= $conj . "  DATE_FORMAT(".$key." , '%Y-%m-%d')" . $operator. $val."  ";
            }elseif($type == "timestamp" ){
                $conditionStr.= $conj . "  DATE_FORMAT(".$key." , '%Y-%m-%d %k:%i:%s')" . $operator. $val."  ";
            }else{
                $conditionStr.= $conj . $key . $operator .$val."  ";
            }
        }
        return ltrim(ltrim($conditionStr, "AND"), "OR");
    }

	public function SelectColumn($table, $column){
		$sql = "SELECT $column FROM $table ORDER BY $column";
		return self::SelectData($sql);
	}

    public function SelectData($sql, $params=NULL, /*$condition=NULL,*/ $order=NULL, $start=0, $size=0, &$recordsCount=NULL, $assoc = 0, $condition_start = '')
    {
        //make sure condition in not null
        // if($condition == NULL){
        //     $condition = array();
        // }

        //check if parameters has conditions
        if($params != NULL){
            $this->CheckConditions($params, $condition);
        }

        //convert condition array to where statement
        if(isset($condition) && count($condition) > 0){
            //print_r($condition);
            $conditionStr = $this->SetConditionString($condition);
            //$conditionStr = " WHERE $conditionStr";

			if($condition_start == ''){
				if(strtolower(substr(trim($sql), -5)) != "where"){
					$sql .= " WHERE ";
				}
			}else{
				$sql .= " $condition_start ";
			}
			$sql .= $conditionStr;
        }

        //Optional passing order fields as an array
        if(isset($order) && $order != NULL)
        {
            $orderStr = "";
            foreach ($order as $key => $value) {
                $orderStr.= $key.' '.$value.',';
            }
            $orderStr = ' ORDER BY '.rtrim($orderStr, ",");
            $sql .= $orderStr;
        }

        //If we are using paging, we need to find the total number of records
        if($start>0 or $size>0){
			$count_sql = 'SELECT COUNT(*) FROM('.$sql.') as `temp`';
			$count_result = $this->Execute($count_sql, $params, true);
			$count_result_array = $this->Fetch($this->stmt, 1);
			if(isset($count_result_array[0][0])){
				$recordsCount = $count_result_array[0][0];
			}
        }

        //Manage paging different cases
        if($start>0 and $size>0)
        {
            $sql .= " LIMIT ".$start." , ".$size;
        }
        else if($start>0 and $size==0)
        {
            $sql .= " LIMIT ".$start." , ".$size;
        }
        else if($start==0 and $size>0)
        {
            $sql .= " LIMIT ".$start." , ".$size;
        }

		//print "<br><br>".$sql."<br><br>";
		$result = $this->Execute($sql, $params, true);
        // if($result != null){
		// 	$rows = array();
		// 	$rows = $this->Fetch($this->stmt, $assoc);
	    //     return $rows;
        // }else{
        //     return null;
        // }
		$rows = array();
		$rows = $this->Fetch($this->stmt, $assoc);
		if(count($rows)>0){
	        return $rows;
        }else{
            return null;
        }
    }


	function Fetch($result, $result_type)
	{
	    $array = array();
	    if($result instanceof mysqli_stmt)
	    {
	        $result->store_result();
	        $variables = array();
	        $data = array();
	        $meta = $result->result_metadata();
	        while($field = $meta->fetch_field()){
	            $variables[] = &$data[$field->name]; // pass by reference
	        }
	        call_user_func_array(array($result, 'bind_result'), $variables);

	        $i=0;
	        while($result->fetch())
	        {
	            $array[] = array();
				$j=0;
	            foreach($data as $k=>$v){
					switch($result_type){
						case 0:
						{
							$array[$i][$k] = $v;
							break;
						}
						case 1:
						{
							$array[$i][$j] = $v;
							break;
						}
						// case 2:
						// {
						// 	break;
						// }
					}
					$j++;
				}
	            $i++;
	            // don't know why, but when I tried $array[] = $data, I got the same one result in all rows
	        }
			//print_r($array);
	    }
	    // elseif($result instanceof mysqli_result)
	    // {
	    //     while($row = $result->fetch_assoc())
	    //         $array[] = $row;
	    // }
	    return $array;
	}


    public function SelectValue($sql, $params=null)
    {
		$count = null;//to pass recrods count as reference
		$result = $this->SelectData($sql, $params, null, null, null, $count, 1);
		if(count($result) > 0){
			return $result[0][0];
		} else {
			return false;
		}
    }


    public function GenerateUpdateQuery($table, $params)
    {
        //1//generate update values string
        $updateStr = "";
        foreach($params as $name=>$param){
            //if associative array
            if( !is_array($param) ||
                (is_array($param) && !isset($param["in_where"]))
            ){
				if (strpos($name, '#') !== false) {
					$name_arr = explode("#", $name);
					$name = $name_arr[0];
				}
                $value = is_array($param)? $param["value"] : $param;
                $updateStr.=' `'.$name.'`= ?,';
            }
        }

        //2//Clean up extra comma
        $updateStr = rtrim($updateStr, ",");

        //3//check conditions in parameters
        $condition = array();
        $this->CheckConditions($params, $condition);

        //4//convert condition array to where statement
        $conditionStr = "";
        if(isset($condition) && count($condition) > 0){
            $conditionStr = $this->SetConditionString($condition);
            $conditionStr = " WHERE $conditionStr";
        }

        //5//generate query
        $query = 'UPDATE `'.$table.'` SET '.$updateStr. $conditionStr;
        return $query;
    }

    public function Update($table, $data, $condition)
    {
		$error = false;
        $params = $this->ConvertToParamsArray($data, $condition);
        $this->SortArrayOfArrays($params, "in_where");
		$sql = $this->GenerateUpdateQuery($table, $params);
		try{
            $b_params = array();
            $result = $this->Execute($sql, $params/*, $b_params*/);
            if(!$result){
				$error = true;
			}else{
				if($this->stmt->affected_rows>0){
					$this->state = self::SUCCESS;
					$this->message = "db_update_success";
				}else{
					$this->state = self::WARNING;
					$this->message = "db_update_empty";
				}
			}
		}catch(Exception $e){
			$error = true;
			$this->Log->Add($this->state, __METHOD__.", line: ".__LINE__, $e->getMessage().', SQL: ['.$query.']', $this->message);
		}

		if($error){
			$this->message = 'db_update_failed';
			$this->state = self::ERROR;
			return false;
		}else{
			return true;
		}
    }


    public function GenerateInsertQuery($table, $params)
    {
        //1//Get fields and values strings
        $fieldsStr = $valuesStr = "";
        foreach ($params as $key => $value) {
            $fieldsStr.= '`'.$key.'`,';
            $valuesStr.= "?,";
        }

        //2//Clean up extra comma
        $fieldsStr = rtrim($fieldsStr, ",");
        $valuesStr = rtrim($valuesStr, ",");

        //3//Generate query
        $query = 'INSERT INTO `'.$table.'` ('.$fieldsStr.') VALUES ('.$valuesStr.')';
        return $query;
    }

    public function Insert($table, $data, $return_new_id=false)
    {
		$error = false;
        //1//Generate query
        $new_id=null;
        $params = $this->ConvertToParamsArray($data);
		$sql = $this->GenerateInsertQuery($table, $params, $new_id);

        //2//Execute
		try{
            //$b_params = array();
            $result = $this->Execute($sql, $params/*, $b_params*/);
			if(!$result){
				$error = true;
			}else{
				if($this->stmt->affected_rows>0){
					//success
					$this->state = self::SUCCESS;
					$this->message = "db_insert_success";
					if($return_new_id){
						//return new id
						return $this->stmt->insert_id;
					}else{
						//we don't want new id, return success
						return true;
					}
				}else{
					//failed
					$this->state = self::WARNING;
					$this->message = "db_insert_empty";
					return false;
				}
			}
		}
        //3//Exception handling
        catch(Exception $e)
        {
			$error = true;
			$this->Log->Add($this->state, __METHOD__.", line: ".__LINE__, $e->getMessage().', SQL: ['.$query.']', $this->message);
		}

		if($error){
			$this->message = 'db_insert_failed';
			$this->state = self::ERROR;
			return false;
		}
    }

    public function GenerateDeleteQuery($table, $params)
    {
        //1//check conditions in parameters
        $condition = array();
        $this->CheckConditions($params, $condition, true);

        //2//convert condition array to where statement
        $conditionStr = "";
        if(isset($condition) && count($condition) > 0){
            $conditionStr = $this->SetConditionString($condition);
            $conditionStr = " WHERE $conditionStr";
        }

        //3//generate query
        $query = 'DELETE FROM `'. $table.'` ' .$conditionStr;
        return $query;
    }

    public function Delete($table, $condition)
    {
        //1//Generate query
        $params = $this->ConvertToParamsArray(null, $condition);
        $sql = $this->GenerateDeleteQuery($table, $params);

        //2//Execute it
        try{
            $result = $this->Execute($sql, $params);
            if(!$result){
                return false;
            }else{
                if($this->stmt->affected_rows>0){
                    $this->state = self::SUCCESS;
                    $this->message = "db_delete_success";
                    return true;
                }else{
                    $this->state = self::WARNING;
                    $this->message = "db_delete_empty";
                    //$this->Log->Add($this->state, __METHOD__.", line: ".__LINE__, NULL, $this->message);
                    return true;
                }
            }
        }
        //3//Exception handling
        catch(Exception $e)
        {
            $this->message = 'delete_failed';
            $this->state = self::ERROR;
            $this->Log->Add($this->state, __METHOD__.", line: ".__LINE__, $e->getMessage().', SQL: ['.$query.']', $this->message);
            return false;
        }
    }

	public function GetNewID($table, $field)
	{
		$sql = 'SELECT MAX(`'.$field.'`) FROM `'.$table.'`';

		try{
			$val = $this->SelectValue($sql);
			if(!$val){
				return 1;
			}else{
				$val +=1;
				return $val;
			}
		} catch(Exception $e){
			$this->message = 'db_new_id_failed';
			$this->state = self::ERROR;
			$this->Log->Add($this->state, __METHOD__.", line: ".__LINE__, $e->getMessage().', SQL: ['.$sql.']', $this->message);
			return false;
		}
	}

    public function GetInsertedId(){
        return $this->stmt->insert_id;
    }

    public function GetAffectedRows(){
        return $this->stmt->affected_rows;
    }

    function GenerateRandomString($length = 3) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

	public function BeginTransaction(){
		$this->conn->autocommit(false);
		return $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
	}

	public function RollbackTransaction(){
		return $this->conn->rollback();
	}

	public function CommitTransaction(){
		return $this->conn->commit();
	}


    public function Execute($sql, $params, $is_select = false)
    {
        //turn on this option to see preparing errors and warnings
        if (0 === strpos($sql, 'INSERT')) {
            print "<br>{<br>".$sql."<br>}<br>";
            print_r($params);
            mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT);
            echo "<br>";
        }

        //1//Prepare statement
		//$this->stmt = $this->conn->prepare($sql);
		if ($this->stmt = $this->conn->prepare($sql)){
            //loop over parameters
            if($params != null && count($params) > 0)
            {
                $params_type = "";
                $params_value = array();
                foreach($params as $name=>$param){
                    $params_value[] = $param["value"];
                    $params_type .= $this->MapType($param["type"]);
                }

                //2//Include parameters types to first item of binding parameters array
                $a_params[] =& $params_type;

                //3//Include parameters values to binding parameters array
                for($i=0; $i<count($params_value); $i++){
                    $a_params[] =& $params_value[$i];
                }
				//echo "##";
                // print_r($a_params);
				//echo "<br>";
                //4// Bind Parameters
                call_user_func_array(array($this->stmt, 'bind_param'), $a_params);
            }

            //5//Execute
            if($is_select){
				return $this->stmt->execute();
			}else{
				return $this->stmt->execute();
			}
			mysqli_report(MYSQLI_REPORT_OFF);

        }else{
			//mysqli_report(MYSQLI_REPORT_OFF);
			// print $this->conn->error;
		    print $sql."<br>";
			print_r($params);
			//mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT);
            echo "Preparing Failed!";
			return false;
        }
    }


}
