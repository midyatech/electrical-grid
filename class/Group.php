<?php
require_once(realpath(dirname(__FILE__)) . '/MysqliDB.php');

/**
 * @access public
 */
class Group {
	/**
	 * @AttributeType string
	 */
	public $messageCode;
	/**
	 * @AttributeType OraDB
	 */
	//state constants
	const ERROR = 0;
	const WARNING = 1;
	const SUCCESS = 2;
	//status constants
	const STATUS_DELETED = 3;
	const STATUS_INACTIVE = 2;
	const STATUS_ACTIVE = 1;

	public $Message;
    public $State;//State of last operation

	protected  $db;
	private $msg;
	/**
	 * @access public
	 */
	public function __construct() {
		$this->db = new MysqliDB();
		//$this->msg = new SystemMessage();
	}

	/**
	 * @access public
	 * @param int id
	 * @param string name
	 * @param int status
	 * @return boolean
	 * @ParamType id int
	 * @ParamType name string
	 * @ParamType status int
	 * @ReturnType boolean
	 */
	public function Add($name, $status) {
		if($this->GroupNameExists($name)){
			return false;
		}else{
			$data = array();
			//$data['GROUP_ID'] = "@@new_id";
			$data['GROUP_NAME'] = $this->db->SqlVal($name, "mytext");
			$data['STATUS_ID'] = array("Value"=>$status, "Type"=>"int");

			$id = $this->db->Insert("GROUP", $data, true);
			$this->State = $this->db->state;
			if(!$id){
				$this->Message = "group_insert_failed";
				return false;
			}else{
				$this->Message = "group_insert_success";
				return $id;
			}
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @param string name
	 * @param int status
	 * @return boolean
	 * @ParamType id int
	 * @ParamType name string
	 * @ParamType status int
	 * @ReturnType boolean
	 */
	public function Edit($id, $name=NULL, $status=NULL) {
		if(! $this->GroupExists($id)){
			return false;
		}else{

			$data = array();

			if($name!==NULL)
			{
				if($this->GroupNameExists($name, $id)){
					return false;
				}else{
					$data['GROUP_NAME'] = $this->db->SqlVal($name, "mytext");
				}
			}

			if($status!==NULL)
				$data['STATUS_ID'] = $this->db->SqlVal($status, "int");

			$condition = array();
			$condition['GROUP_ID']=array("Value"=>$id, "Type"=>"int");
			$res = $this->db->Update("GROUP", $data, $condition);
			$this->State = $this->db->state;
			$db = $this->db;
			if($db->state == $db::ERROR){
				$this->Message = "group_update_failed";
				return false;
			}else{
				$this->Message = "group_update_success";
				return true;
			}
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @return boolean
	 * @ParamType id int
	 * @ReturnType boolean
	 */
	public function Delete($id) {
		$condition = array();
		$condition['GROUP_ID'] = array("Value"=>$id, "Type"=>"int");

		$res = $this->db->Delete("GROUP", $condition);
		$this->State = $this->db->state;
		if(!$res){
			$this->Message = "group_delete_failed";
			return false;
		}else{
			$this->Message = "group_delete_success";
			return true;
		}
	}


	public function SetAsDeleted($id) {
		if(!$this->GroupExists($id)){
			return false;
		}else{
			$res = $this->Edit($id, NULL, self::STATUS_DELETED);

			if(!$res){
				return false;
			}else{
				return true;
			}
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @return obj[]
	 * @ParamType id int
	 * @ReturnType obj[]
	 */
	public function GetInfo($id) {
		$groupInfo = $this->db->SelectData('SELECT `GROUP`.`GROUP_ID`, `GROUP_NAME`, `GROUP`.`STATUS_ID`, `STATUS`.`STATUS`
											FROM `GROUP`
											LEFT OUTER JOIN `STATUS` ON `GROUP`.`STATUS_ID` = `STATUS`.`STATUS_ID`
											WHERE `GROUP`.`GROUP_ID`='.$this->db->SqlVal($id,"int"));
		$this->State = $this->db->state;
		$this->Message = $this->db->message;
		if($groupInfo === NULL){
			$this->Message = "group_not_exists";
			return false;
		}else if($groupInfo === FALSE){
			return false;
		}else{
			return $groupInfo;
		}
	}


	public function GetStatus($id) {
		$groupStatus = $this->GetInfo($id);
		if(!$groupStatus){
			return false;
		}else{
			return $groupStatus[0]["STATUS_ID"];
		}
	}

	public function SetStatus($id, $status) {
		if(!$this->GroupExists($id)){
			return false;
		}else{
			$res = $this->Edit($id, NULL, $status);
			if(!$res){
				return false;
			}else{
				return true;
			}
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @param int form
	 * @return boolean
	 * @ParamType id int
	 * @ParamType form int
	 * @ReturnType boolean
	 */
	public function AddPermission($id, $form, $permissionLevel) {
		if(!$this->GroupExists($id)){
			return false;
		}else {
			$data = array("GROUP_ID"=>$this->db->SqlVal($id, "int"),
							"OPERATION_NAME"=>$this->db->SqlVal($form, "mytext"),
							"PERMISSION_LEVEL"=>$this->db->SqlVal($permissionLevel, "int"));

			$res = $this->db->Insert("GROUP_PERMISSION", $data);
			$this->State = $this->db->state;
			if(!$res){
				echo "FAILED";
				$this->Message = "group_insert_permission_failed";
				return false;
			}else{
				echo "SUCCESS";
				$this->Message = "group_insert_permission_success";
				return true;
			}
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @param int form
	 * @return boolean
	 * @ParamType id int
	 * @ParamType form int
	 * @ReturnType boolean
	 */
	public function RemovePermission($id, $form) {
		if(!$this->GroupExists($id)){
			return false;
		}else {
			$condition = array();
			$condition['GROUP_ID'] = array("Value"=>$id, "Type"=>"int");
			$condition['OPERATION_NAME'] = array("Value"=>$form, "Type"=>"mytext");

			$res = $this->db->Delete("GROUP_PERMISSION", $condition);
			$this->State = $this->db->state;
			if(!$res){
				$this->Message = "group_delete_permission_failed";
				return false;
			}else{
				$this->Message = "group_delete_permission_success";
				return true;
			}
		}
	}

	public function UpdatePermission($id, $form, $permissionLevel) {
		if(!$this->GroupExists($id)){
			return false;
		}else {
			$data = array("PERMISSION_LEVEL"=>$this->db->SqlVal($permissionLevel, "int"));

			$condition = array();
			$condition['GROUP_ID'] = array("Value"=>$id, "Type"=>"int");
			$condition['FORM_CODE'] = array("Value"=>$form, "Type"=>"mytext");

			$res = $this->db->Update("GROUP_PERMISSION", $data, $condition);
			$this->State = $this->db->state;
			if(!$res){
				$this->Message = "group_update_permission_failed";
				return false;
			}else{
				$this->Message = "group_update_permission_success";
				return true;
			}
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @param int form
	 * @return int
	 * @ParamType id int
	 * @ParamType form int
	 * @ReturnType int
	 */
	public function CheckPermission($id, $form) {
		if(!$this->GroupExists($id)){
			return false;
		}else {
			$groupPermission = $this->db->SelectValue('SELECT `PERMISSION_LEVEL` FROM `GROUP_PERMISSION` WHERE
														`GROUP_ID`='. $this->db->SqlVal($id, "int") .' AND
														`FORM_CODE`='. $this->db->SqlVal($form, "mytext"));
			$this->State = $this->db->state;
			$this->Message = $this->db->message;
			if(!$groupPermission){
				return 0;
			}else{
				return $groupPermission;
			}
		}
	}

	/**
	 * @access public
	 * @return obj[]
	 * @ReturnType obj[]
	 */
	public function GetGroups($filter=NULL, $order=NULL, $start=0, $size=0, &$recordsCount=NULL)
	{
		//Original query
		$select = 'SELECT `GROUP_ID`, `GROUP_NAME`, `GROUP`.`STATUS_ID`, `STATUS`
					FROM `GROUP`
					LEFT OUTER JOIN `STATUS` ON `GROUP`.`STATUS_ID` = `STATUS`.`STATUS_ID` ';

		//Set default sorting
		if(! isset($order)){
			$order = array("GROUP_NAME"=>"ASC");
		}

		$params = $this->db->ConvertToParamsArray(null, $filter);
		$allGroups = $this->db->SelectData($select, $params, $order, $start, $size, $recordsCount);
		//print_r($allGroups)."hhh";
		$this->State = $this->db->state;
		$this->Message = $this->db->message;
		if(!$allGroups){
			return false;
		}else{
			return $allGroups;
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @return obj[]
	 * @ParamType id int
	 * @ReturnType obj[]
	 */
	public function GetGroupUsers($id) {
		$groupsUsers =  $this->db->SelectData('SELECT `USER`.`USER_ID`, `LOGIN`, `PASSWORD`, `NAME`, `USER_STATUS_ID`, `AGENT_ID`,
											`FAILED_ATTEMPT_NUM`
											FROM `USER`
											JOIN `USER_GROUP` ON `USER_GROUP`.`USER_ID` = `USER`.`USER_ID`
											WHERE `GROUP_ID`='.$this->db->SqlVal($id,"int").'
											ORDER BY `LOGIN`');
		$this->State = $this->db->state;
		$this->Message = $this->db->message;
		if(!$groupsUsers){
			return false;
		}else{
			return $groupsUsers;
		}
	}


	/**
	 * @access public
	 * @param int id
	 * @param int user
	 * @return boolean
	 * @ParamType id int
	 * @ParamType user int
	 * @ReturnType boolean
	 */
	/* public function AddUser($id, $user) {
		$data = array("USER_ID"=>$this->db->SqlVal($user, "int"),
						"GROUP_ID"=>$this->db->SqlVal($id, "int"));
		$res = $this->db->Insert("USER_GROUP", $data);
		if(!$res)
			$this->Message = $this->db->message;
		return $res;
	} */

	/**
	 * @access public
	 * @param int id
	 * @param int user
	 * @return boolean
	 * @ParamType id int
	 * @ParamType user int
	 * @ReturnType boolean
	 */
	/* public function RemoveUser($id, $user) {
		$condition = array("GROUP_ID"=>$this->db->SqlVal($id, "int"),
						   "USER_ID"=>$this->db->SqlVal($user, "int"));
		$res = $this->db->Delete("USER_GROUP", $condition);
		if(!$res)
			$this->Message = $this->db->message;
		return $res;
	} */

	/**
	 * @access public
	 * @param string language
	 * @return string
	 * @ParamType language string
	 * @ReturnType string
	 */
	public function GetMessage($language) {
		// Not yet implemented
	}


	protected function GroupExists($id)
	{
		$res = $this->db->ValueExists("GROUP",  "GROUP_ID", $id);
		if($res === NULL){
			$this->State = $this->db->state;
			$this->Message = $this->db->message;
		}else if($res === FALSE){
			//no record
			$this->State = self::WARNING;
			$this->Message = "group_not_exists";
			return false;
		}else if($res === TRUE){
			$this->State = self::SUCCESS;
			return true;
		}
	}

	public function GroupNameExists($group, $id=NULL)
	{
		$res = $this->db->ValueExists("GROUP",  "GROUP_NAME", $group, "GROUP_ID", $id);
		$this->State = $this->db->state;
		if($res === NULL){
			$this->State = $this->db->state;
			$this->Message = $this->db->message;
		}else if($res === FALSE){
			//no record
			$this->State = self::SUCCESS;
			$this->Message = "group_name_not_exists";
			return false;
		}else if($res === TRUE){
			$this->State = self::WARNING;
			$this->Message = "group_name_exists";
			return true;
		}
	}

	public function AddPermissionToGroup($operation, $id, $form, $permissionLevel = 1)
	{
		$return = true;
		if ($operation == 'Insert') {
			if(is_array($form))
			{
				for($i=0; $i< count($form) ;$i++)
				{
					$result = $this -> AddPermission($id, $form[$i], $permissionLevel);
					if($result!= true)
						$return = false;
				}
			}
		} else if ($operation == 'Delete'){
			if(is_array($form))
			{
				for($i=0; $i< count($form) ;$i++)
				{
					$result = $this -> RemovePermission($id, $form[$i]);
					if($result!= true)
						$return = false;
				}
			}
		}
		return $return;
	}


	/**
	 * @access public
	 * @param int id
	 * @ParamType id int
	 */
	public function GetGroupPermissions($id) {
		$groupInfo = $this->GetInfo($id);
		if(!$groupInfo){
			return false;
		}else {
			$params = $this->db->ConvertToParamsArray(array($this->db->SqlVal($id,"int")));
			$sql = 'SELECT DISTINCT `GROUP_PERMISSION`.`OPERATION_NAME` as `ID` , `GROUP_PERMISSION`.`OPERATION_NAME`
					 FROM `GROUP_PERMISSION`
					 JOIN FORM ON `GROUP_PERMISSION`.OPERATION_NAME = FORM.OPERATION_NAME
					 WHERE `GROUP_ID`=?
					 ORDER BY `OPERATION_NAME`';
			$groupPermissions = $this->db->SelectData($sql, $params, null, null, null, $count, 1);
			$this->State = $this->db->state;
			$this->Message = $this->db->message;
			if($groupPermissions === NULL){
				//empty result
				return false;
			}else if($groupPermissions === false){
				//something wrong
				return false;
			}else{
				return $groupPermissions;
			}
		}
	}
	/**
	 * @access public
	 * @param int id
	 * @ParamType id int
	 */
	public function GroupNonAssosiatedPermissions($id, $module_code ) {
		$groupInfo = $this->GetInfo($id);
		if(!$groupInfo){
			return false;
		}else {
			$condition = NULL;
			// IF FILTERED by MODULE
			// -1 value for all modules
			if( $module_code >= 1){
				$condition = 'AND `FORM`.`MODULE_CODE` ='.$module_code ;
			}
			$sql = 'SELECT `FORM`.`OPERATION_NAME` AS ID, `FORM`.`OPERATION_NAME`
							FROM `FORM`
							LEFT JOIN `GROUP_PERMISSION`
							ON `FORM`.`OPERATION_NAME` = `GROUP_PERMISSION`.`OPERATION_NAME`
							AND  `GROUP_PERMISSION`.`GROUP_ID`=?
							WHERE `GROUP_PERMISSION`.`OPERATION_NAME` IS NULL
							AND `FORM`.`OPERATION_NAME` IS NOT NULL '.$condition.'
							GROUP BY `FORM`.`OPERATION_NAME`, `FORM`.`OPERATION_NAME`';

			$params = $this->db->ConvertToParamsArray(array($this->db->SqlVal($id,"int")));
			$permissions = $this->db->SelectData($sql, $params, null, null, null, $count, 1);

			$this->State = $this->db->state;
			$this->Message = $this->db->message;
			if($permissions === NULL){
				//empty
				return false;
			}else if($permissions === false){
				//something wrong
				return false;
			}else{
				return $permissions;
			}
		}
	}
	/*
	 * @access public
	 * @return obj[]
	 * @ReturnType obj[]
	 */
	public function GetModules($filter=NULL, $order=NULL, $start=0, $size=0, &$recordsCount=NULL)
	{
		//Original query
		$select = 'SELECT `MODULE_CODE`, `MODULE_NAME` FROM `MODULE`';

		//Set default sorting
		if(! isset($order)){
			$order = array("MODULE_NAME"=>"ASC");
		}
		$allModules = $this->db->SelectData($select, $filter, $order, $start, $size, $recordsCount);
		$this->State = $this->db->state;
		$this->Message = $this->db->message;
		if(!$allModules){
			return false;
		}else{
			return $allModules;
		}
	}

	public function GetGroupsStatus(){
		$select = "SELECT STATUS_ID, STATUS
					FROM STATUS ORDER BY STATUS_ID";

		$allstatus = $this->db->SelectData($select, null, null, null, null, $count, 1);
		return $allstatus;
	}

}
?>
