<?php
require_once (realpath(dirname(__FILE__)) . '/MysqliDB.php');
//require_once (realpath(dirname(__FILE__)) . '/Tree.php');

class User_Log {
		public $Message;
		public $State;
		protected $db;
		protected $tree;

	/**
	 * @access public
	 */
	public function __construct() {
		$this ->db = new MysqliDB();
		//$this -> tree = new TREE("AGENT_TREE");
	}

	//fill not provided log data
	public function PrepareLogData($data){
		$data["TIMESTAMP"] = date('Y-m-d H:i:s');
		$data["USER_ID"] = $_SESSION["user_id"];
		return $data;
	}

	public function AddRecord($log_data){
		$data = array();
		$data['USER_ID'] = $this ->db -> SqlVal($log_data["USER_ID"], "int");
		$data['TIMESTAMP'] = $this ->db -> SqlVal($log_data["TIMESTAMP"], "mytext");
		$data['MODULE_ID'] = $this ->db -> SqlVal($log_data["MODULE_ID"], "int");
		$data['OPERATION_ID'] = $this ->db -> SqlVal($log_data["OPERATION_ID"], "int");
		// $data['NOTES'] = "'".$notes."'";
		$data['KEY_DATA'] = $this ->db -> SqlVal($log_data["KEY_DATA"], "mytext");
		$data['NEW_DATA'] = $this ->db -> SqlVal($log_data["NEW_DATA"], "mytext");
		$data['OLD_DATA'] = $this ->db -> SqlVal($log_data["OLD_DATA"], "mytext");
		$data['CRUD_OPERATION_ID'] = $this ->db -> SqlVal($log_data["CRUD_OPERATION_ID"], "int");
		$data['TABLE_NAME'] = $this ->db -> SqlVal($log_data["TABLE_NAME"], "mytext");
		$data['RECORD_ID'] = $this ->db -> SqlVal($log_data["RECORD_ID"], "int");
		// $data['RESULT'] = $this ->db -> SqlVal($log_data["RESULT"], "mytext");

		//Insert
		$log_id = $this ->db-> Insert("USER_LOG", $data, true);
		//Check result
		if (!$log_id) {
			//insert failed
			$this->Message = $this ->db-> message;
			return false;
		} else {
			$this->Message = $this ->db-> message;
			return $log_id;
		}
	}

	public function SearchUserLog($condition=NULL, $order=NULL, $start=0, $size=0, &$recordsCount=NULL){
		$params = array();
		$filter = array();
		$select = "SELECT USER.USER_ID ,RECORD_ID,TABLE_NAME,RESULT,NOTES,
							USER_LOG.TIMESTAMP, MODULE_NAME, MODULE_OPERATION, USER_LOG.KEY_DATA,
							USER_LOG.NEW_DATA, USER_LOG.OLD_DATA,CRUD_OPERATION, NAME
					FROM USER_LOG
					INNER JOIN USER ON USER.USER_ID = USER_LOG.USER_ID
					INNER JOIN MODULE ON MODULE.MODULE_ID = USER_LOG.MODULE_ID
					INNER JOIN CRUD_OPERATION ON CRUD_OPERATION.CRUD_OPERATION_ID = USER_LOG.CRUD_OPERATION_ID
					INNER JOIN MODULE_OPERATION ON MODULE_OPERATION.MODULE_OPERATION_ID = USER_LOG.OPERATION_ID
					WHERE DATE(USER_LOG.TIMESTAMP) >= ? AND DATE(USER_LOG.TIMESTAMP) <= ?";
		// print $filter;
		// if($filter!= NULL){
		// 	$condition = '';
		// 	$select .='WHERE ';
		// 	// if(isset($filter["lastoperations"])){
		// 	// 	$condition = " OPERATION_TYPE IN ('userlog_admin_print_cards','userlog_admin_charged_cards') ";
		// 	// 	unset($filter["lastoperations"]);
		// 	// }
		// 	if($filter!= NULL){
		// 		if($condition!='') $condition .= ' AND ';
		// 		$condition .= $this->db->SetConditionString($filter);
		// 	}

		// 	$select .= $condition;
		// }
		// // $select .=' ORDER BY `USER_LOG`.`DATE` DESC';

		$filter["from_date"] = $condition["from_date"];
		$filter["to_date"] = $condition["to_date"];
		unset($condition['from_date']);
		unset($condition['to_date']);

		// if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray($filter, $condition);
		// }
		$log = $this->db->SelectData($select, $params, $order, $start, $size, $recordsCount, 0, 'AND');
		$this->Message = $this->db->message;
		$this->State = $this->db->state;
		if($log === NULL){
			return false;
		}else if(!$log){
			return false;
		}else{
			return $log;
		}
	}

	public function GetModules()
	{
		//Original query
		$select = 'SELECT * FROM `MODULE` ';

		//Set default sorting
		// $order = array("MODULE_CODE"=>"ASC");

		//Select data
		$allModules = $this->db->SelectData($select, NULL, NULL, NULL, NULL, $recordsCount, 1);
		//Check resutl
		$this->State = $this->db->state;
		if($allModules === NULL){
			//empty result
			$this->Message = "user_not_exists";
			return false;
		}else if($allModules === false){
			//something wrong
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->Message = "success";
			return $allModules;
		}
	}

	public function GetOperations($module=NULL)
	{
		$params = array();
		$select = "SELECT *
					FROM MODULE_OPERATION";
		if($module != null){
			$select .= " WHERE MODULE_ID = ?";
            $params = $this->db->ConvertToParamsArray([$module]);
		}
			$select .= " ORDER BY MODULE_OPERATION";

		$allOperation = $this->db->SelectData($select, $params, NULL, NULL, NULL, $recordsCount, 1);
		//Check resutl
		$this->State = $this->db->state;
		if($allOperation === NULL){
			//empty result
			$this->Message = "operation_not_exists";
			return false;
		}else if($allOperation === false){
			//something wrong
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->Message = "success";
			return $allOperation;
		}
	}



	public function ConvertArrayToString($data)
	{
		$string = NULL;
		for($i=0;$i<count($data);$i++)
		{
			foreach($data[$i] as $key=>$value)
			{
				$string .= $key." = ".$value." , ";
			}
		$string .="\n\r";
		}
	return $string;

	}

	public function GetJsonData($table_name, $condition=NULL)
	{
		$table_name = $this->db->SqlVal( $table_name, "mytext");
		$select = "SELECT * FROM $table_name";

		$params = array();
		if ($condition!=null) {
			$params = $this->db->ConvertToParamsArray(null, $condition);
		}

		$arrayResult = $this->db->SelectData($select, $params);

		return json_encode($arrayResult);
	}

}
?>