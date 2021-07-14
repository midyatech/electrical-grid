<?php
require_once(realpath(dirname(__FILE__)) . '/MysqliDB.php');//MysqliDB
require_once(realpath(dirname(__FILE__)) . '/Tree.php');
require_once(realpath(dirname(__FILE__)) . '/Crypt.php');

/**
 * @access public
 */
class User {

	//state constants
	const ERROR = 0;
	const WARNING = 1;
	const SUCCESS = 2;

	//status constants
	//const STATUS_WAITING_CONFIRMATION = 7;
	//const STATUS_WAITING_MAC = 6;
	//const STATUS_CHANGE_PASSWORD = 5;
	const STATUS_LOCKED = 4;
	const STATUS_DELETED = 3;
	const STATUS_INACTIVE = 2;
	const STATUS_ACTIVE = 1;

	//Allowed failed login attempts
	const ALLOWED_ATTEMPTS = 10;


	//Permission constatns
	const READ = 1;
	const WRITE = 2;

	public $Message;

    public $State;//State of last operation

	/**
	 * @AttributeType OraDB
	 */
	protected $db;
	protected $tree;
    protected $treeName;
	protected $cryptData;

	/**
	 * @access public
	 */
	public function __construct() {
		$this->db = new MysqliDB();
        $this->treeName = "AREA_TREE";
		$this->tree = new Tree($this->treeName);
		$this->cryptData = new Crypt();
		//$this->msg = new SystemMessage();
	}

	/**
	 * @access public
	 * @param int id
	 * @param string name
	 * @param string loginName
	 * @param string password
	 * @param string department
	 * @param string accessDepartment
	 * @param int status
	 * @return boolean
	 * @ParamType id int
	 * @ParamType name string
	 * @ParamType loginName string
	 * @ParamType password string
	 * @ParamType department string
	 * @ParamType accessDepartment string
	 * @ParamType status int
	 * @ReturnType boolean
	 */
	public function Add($name, $loginName, $password, $status, $uiLanguage = "KURDISH" , $change_password =0, $Access, $user_department_node_id, $web_browse=1)
	{
		$data = array();
		if($this->ValidateLoginName($loginName))
		{
			if(!$this->LoginExists($loginName))
			{
				if($this->ValidatePassword($password))
				{

					//$data["USER_ID"] = "@@new_id";
					$data['NAME'] = $this->db->SqlVal($name,"mytext");
					$data['LOGIN']=$this->db->SqlVal(strtolower($loginName),"mytext");
					$data['PASSWORD']=$this->cryptData->MediaEncrypt($password);
					$data['user_department_node_id']=$this->db->SqlVal($user_department_node_id,"mytext");
					$data['USER_STATUS_ID']=$this->db->SqlVal($status,"int");
					$data['UI_LANGUAGE']=$this->db->SqlVal($uiLanguage,"mytext");
					$data['CHANGE_PASSWORD']=$this->db->SqlVal($change_password,"int");
					$data['user_access_node_id']=$this->db->SqlVal($Access,"int");
					$data['WEB_BROWSE']=$this->db->SqlVal($web_browse,"int");
					//FAILED_ATTEMPT_NUM

					$userId = $this->db->Insert("USER", $data, true);
					if(!$userId){
						$this->State = $this->db->state;
						$this->Message = "user_insert_failed";
						return false;
					}else{
						$this->State = self::SUCCESS;
						$this->Message = "user_insert_success";
						return $userId;
					}

				}else {
					$this->State = self::WARNING;
					$this->Message = "user_invalid_password";
					return false;
				}
			}else{
				$this->State = self::WARNING;
				$this->Message = "user_login_name_taken";
				return false;
			}
		}else{
			$this->State = self::WARNING;
			$this->Message = "user_invalid_login_name";
			return false;
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @param string name
	 * @param string loginName
	 * @param string password
	 * @param string department
	 * @param string accessDepartment
	 * @param int status
	 * @return boolean
	 * @ParamType id int
	 * @ParamType name string
	 * @ParamType loginName string
	 * @ParamType password string
	 * @ParamType department string
	 * @ParamType accessDepartment string
	 * @ParamType status int
	 * @ReturnType boolean
	 */
	public function Edit($id, $name=NULL, $loginName=NULL, $oldPassword=NULL, $password=NULL, $status=NULL, $attemptNum=NULL, $uiLanguage = NULL, $map_coordinates=null, $change_password = NULL, $user_picture=NULL, $user_department_node_id=null, $user_access_node_id=null)
	{
		$userInfo = $this->GetInfo($id);
		if(!$userInfo)
		{
			return false;
		}else{
			$data = array();
			//Cehck loginName
			if($loginName!==NULL)
			{
				if($this->ValidateLoginName($loginName))
				{
					//Check login availability excluding current login of this user
					if(!$this->LoginExists($loginName, $userInfo[0]["USER_ID"]))
					{
						$data['LOGIN'] = $this->db->SqlVal(strtolower($loginName),"mytext");
					}else{
						$this->State = self::WARNING;
						$this->Message = "user_login_name_taken";
						return false;
					}
				}else{
					$this->State = self::WARNING;
					$this->Message = "user_invalid_login_name";
					return false;
				}
			}

			//Cehck password
			if($password!==NULL)
			{
				//Old password not match
                // if($oldPassword !== $this->cryptData->MediaDecrypt($userInfo[0]["PASSWORD"])){
				// 	//if($oldPassword !== $userInfo[0]["PASSWORD"]){
                //     $this->State = self::WARNING;
                //     $this->Message = "user_old_pass_wrong";
                //     return false;
                // }else {
                    //Validate new password
                    if(!$this->ValidatePassword($password))
                    {
                        $this->State = self::WARNING;
                        $this->Message = "user_invalid_password";
                        return false;
                    }else {
                        $data['PASSWORD']= $this->cryptData->MediaEncrypt($password);
                    }
                // }
			}

			if($name!==NULL)			$data['NAME'] = $this->db->SqlVal($name,"mytext");
			if($user_department_node_id!==NULL)		$data['user_department_node_id']=$this->db->SqlVal($user_department_node_id,"int");
			if($status!==NULL)			$data['USER_STATUS_ID']=$this->db->SqlVal($status,"int");
			if($attemptNum!==NULL)		$data['FAILED_ATTEMPT_NUM']=$this->db->SqlVal($attemptNum,"int");
			if($uiLanguage!==NULL)		$data['UI_LANGUAGE']=$this->db->SqlVal($uiLanguage,"mytext");
			if($change_password!==Null)	$data['CHANGE_PASSWORD']=$this->db->SqlVal($change_password,"int");
			if($user_picture!==Null)	$data['user_picture']=$this->db->SqlVal($user_picture,"mytext");
			if($map_coordinates!==Null)	$data['map_coordinates']=$this->db->SqlVal($map_coordinates,"mytext");

			$condition = array();
			$condition["USER_ID"]=$this->db->SqlVal($id,"int");

			$res = $this->db->Update("USER", $data, $condition);
			$this->State = $this->db->state;
			$db = $this->db;
			if($db->state == $db::ERROR){
				$this->Message = "user_update_failed";
				return false;
			}
			else{
				$this->Message = "user_update_success";
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
		// Not yet implemented
	}

	public function SetAsDeleted($id) {
		if(!$this->SetStatus($id, self::STATUS_DELETED)){
			return false;
		}else{
			$this->State = self::SUCCESS;
			$this->Message = "user_delete_success";
			return true;
		}
	}

	/**
	 * @access public
	 * @param string loginName
	 * @param string password
	 * @return boolean
	 * @ParamType loginName string
	 * @ParamType password string
	 * @ReturnType boolean
	 */
	public function Login($loginName, $password)
	{
		$sql = 'SELECT `USER_ID`, `PASSWORD`, `NAME`, `USER_STATUS_ID`, `FAILED_ATTEMPT_NUM`, `UI_LANGUAGE` , `CHANGE_PASSWORD`, WEB_BROWSE, UNIQUE_ID ,
				`user_department_node_id`, user_access_node_id, user_picture, map_coordinates, warehouse_id
				FROM `USER`
				WHERE `LOGIN`=?';
		$filter = array();
		$filter[] = $this->db->SqlVal(strtolower($loginName), "mytext");
		$params = $this->db->ConvertToParamsArray($filter);
		$userInfo = $this->db->SelectData($sql,$params);
		$user_id=$this->db->SqlVal($userInfo[0]["USER_ID"], "int");
		$check_client=$this->CheckClient($user_id);
		//Check lgoin name
		if(!$userInfo || $check_client!=NULL){
			$this->State = self::WARNING;
			$this->Message = "user_not_exists";
			return false;
		}else {
				$userId = $userInfo[0]['USER_ID'];
				//Check Dealer Status
				$dir_status = $this->tree->GetNodeStatus($userInfo[0]['user_department_node_id']);
				if($dir_status == self::STATUS_DELETED)
				{
					$this->State = self::WARNING;
					$this->Message = "user_dealer_is_locked";
					return false;
				}
				else
				{
					$this->cryptData->MediaDecrypt($userInfo[0]['PASSWORD']);
					//Check password
					if($password == $this->cryptData->MediaDecrypt($userInfo[0]['PASSWORD']))
					{

						// if($userInfo[0]['WEB_BROWSE'] == 0 )
						// {
						// 	if($userInfo[0]['USER_STATUS_ID'] == 6 )
						// 	{
						// 		header('Location: ../user_identify.php?user_id='.$userId.'&unique_id='.$unique_id);
						// 		die;
						// 	}
						// 	/*elseif($userInfo[0]['USER_STATUS_ID'] == 7 )// if waiting for confirmation
						// 	{
						// 		$this->State = self::WARNING;
						// 		$this->Message = "user_wait_to_confirm";
						// 		return false;
						// 	}*/
						//
						// 	//if windows user is using the browser
						// 	if($unique_id == NULL)
						// 	{
						// 		$this->State = self::WARNING;
						// 		$this->Message = "web_browse_not_allowed_for_this_user";
						// 		return false;
						// 	}
						//
						// 	//Check UniqeID
						// 	if(trim($unique_id) !== trim($userInfo[0]['UNIQUE_ID']) ){
						// 		$this->State = self::WARNING;
						// 		$this->Message = "user_not_recognized";
						// 		return false;
						// 	}
						// }

						//Check status
						if(!$this->CheckUserStatus( $userInfo[0]['USER_STATUS_ID'] )){
							$this->State = self::WARNING;
							return false;
						}else{
							// is active status
							//If log in succeeded, and status is active, reset failed attempts number
							$this->ResetFailedLogin($userId);
							$this->State = self::SUCCESS;
							$this->Message = "user_login_sucess";
							return $userInfo;
						}

					}else{
						// wrong password
						$this->AddFailedLogin($userId, $userInfo[0]['FAILED_ATTEMPT_NUM']);
						$this->State = self::WARNING;
						$this->Message = "user_wrong_pass";
						//If number of failed login attempts is greater than allowed attempts (ALLOWED_ATTEMPTS constant) then lock the account
						if($userInfo[0]['FAILED_ATTEMPT_NUM']>= self::ALLOWED_ATTEMPTS){
							$this->LockUser($userId);
							$this->Message = "user_status_locked";
						}
						return false;
					}
				}
			}

	}

	/**
	 * @param statusId
	 */


	 protected function CheckUserStatus($statusId) {
		switch ($statusId)
		{
			case self::STATUS_ACTIVE:
				return true;
			case self::STATUS_INACTIVE:
				$this->Message = "status_inactive";
				return false;
			case self::STATUS_DELETED:
				$this->Message = "status_deleted";
				return false;
			case self::STATUS_LOCKED:
				$this->Message = "user_status_locked";
				return false;
			//case self::STATUS_CHANGE_PASSWORD:
			//	$this->Message = "user_status_pass_change";
			//	return false;
			//case self::STATUS_WAITING_CONFIRMATION:
			//	$this->Message = "user_status_waiting_confirmation";
			//	return false;
			default:
				$this->Message = "status_invalid";
				return false;
		}
	}

	public function CheckClient($id)
	{
		$sql="SELECT * FROM `client` WHERE user_id=?";
		$filter = array();
			$filter[] = $this->db->SqlVal($id,"int");
			$params = $this->db->ConvertToParamsArray($filter);
			$client = $this->db->SelectValue($sql,$params);
		if($client==NULL)
		{
			return null;
		}
		else
		{
			return $client;
		}


	}
	public function CheckPermission($id, $form)
	{
		if(!$userInfo = $this->GetInfo($id)){
			return 0;
		}else{
			$sql = 'SELECT MAX(`PERMISSION_LEVEL`) as `LVL` FROM `GROUP_PERMISSION`
									INNER JOIN `USER_GROUP` ON `GROUP_PERMISSION`.`GROUP_ID` = `USER_GROUP`.`GROUP_ID`
									INNER JOIN `GROUP` ON `GROUP`.`GROUP_ID` = `USER_GROUP`.`GROUP_ID`
									WHERE `USER_ID`=?
									AND `GROUP`.`STATUS_ID` = 1
									AND `OPERATION_NAME`=?';
			$filter = array();
			$filter[] = $this->db->SqlVal($id,"int");
			$filter[] = $this->db->SqlVal($form,"mytext");
			$params = $this->db->ConvertToParamsArray($filter);
			$lvl = $this->db->SelectValue($sql,$params);

			if(!$lvl){
				$this->State = $this->db->state;
				$this->Message = "user_no_access";
				return 0;
			}else{
				$this->State = self::SUCCESS;
				switch ($lvl)
				{
					case self::READ :
						$this->Message = "user_permission_read";
						break;
					case self::WRITE :
						$this->Message = "user_permission_write";
						break;
					default:
						$this->State = self::WARNING;
						$this->Message = "user_no_access";
						$lvl = 0;
						break;
				}
				return $lvl;
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
		$sql = 'SELECT `USER_ID`, `LOGIN`, `PASSWORD`,`NAME`, USER_STATUS.`USER_STATUS_ID`, `FAILED_ATTEMPT_NUM`, `UI_LANGUAGE` , `CHANGE_PASSWORD`, WEB_BROWSE, UNIQUE_ID ,
			`user_department_node_id`, `user_access_node_id`, user_picture, USER_STATUS.USER_STATUS, map_coordinates
			FROM `USER`
			INNER JOIN USER_STATUS ON `USER`.USER_STATUS_ID = USER_STATUS.USER_STATUS_ID
			WHERE `USER_ID`=?';
		$filter = array();
		$filter[] = $this->db->SqlVal($id,"int");
		$params = $this->db->ConvertToParamsArray($filter);
		$userInfo = $this->db->SelectData($sql, $params);
		$this->State = $this->db->state;
		$this->Message = $this->db->message;
		if($userInfo === NULL){
			$this->Message = "user_not_exists";
			return false;
		}else if($userInfo === false){
			//something wrong
			return false;
		}else{
			return $userInfo;
		}
	}


	public function GetDirNodeId($id)
	{
		$sql = 'SELECT `user_department_node_id` FROM `USER` WHERE `USER_ID`=?';
		$filter = array();
		$filter[] = $this->db->SqlVal($id,"int");
		$params = $this->db->ConvertToParamsArray($filter);
		$dir_id  = $this->db->SelectData($sql, $params);
		$this->State = $this->db->state;
		$this->Message = $this->db->message;
		if($dir_id === NULL){
			$this->Message = "user_not_exists";
			return false;
		}else if($dir_id === false){
			//something wrong
			return false;
		}else{
			return $dir_id;
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @return string
	 * @ParamType id int
	 * @ReturnType string
	 */
	public function GetDepartment($id) {
		$userDepartment = $this->GetInfo($id);
		if(!$userDepartment){
			return false;
		}else{
			return $userDepartment[0]["user_department_node_id"];
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @param string departmentId
	 * @return boolean
	 * @ParamType id int
	 * @ParamType departmentId string
	 * @ReturnType boolean
	 */
	/*public function SetDepartment($id, $agent_id) {
		$res = $this->Edit($id, NULL, NULL, NULL, NULL, $agent_id, NULL, NULL, NULL);
		if(!$res){
			return false;
		}else{
			return true;
		}
	}*/

	/**
	 * @access public
	 * @param int id
	 * @return int
	 * @ParamType id int
	 * @ReturnType int
	 */
	public function GetStatus($id) {
		$userStatus = $this->GetInfo($id);
		if(!$userStatus){
			return false;
		}else{
			return $userStatus[0]["USER_STATUS_ID"];
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @param int status
	 * @return boolean
	 * @ParamType id int
	 * @ParamType status int
	 * @ReturnType boolean
	 */
	public function SetStatus($id, $status) {
		$res = $this->Edit($id, NULL, NULL,NULL, NULL, $status);
		if(!$res){
			return false;
		}else{
			return true;
		}
	}

	public function LockUser($id)
	{
		if(!$this->SetStatus($id, self::STATUS_LOCKED)){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @param string oldPassword
	 * @param string password
	 * @return boolean
	 * @ParamType id int
	 * @ParamType oldPassword string
	 * @ParamType password string
	 * @ReturnType boolean
	 */
	public function ChangePassword($id, $oldPassword, $password, $reset=false) {
		$userInfo = $this->GetInfo($id);
		//If allow change without old password, get password from db
		if(!$userInfo){
			return false;
		}else{
			if($reset){
				$oldPassword = $this->cryptData->MediaDecrypt($userInfo[0]["PASSWORD"]);
				//$oldPassword = $userInfo[0]["PASSWORD"];
			}else{
				if(!$this->CheckUserStatus($userInfo[0]["USER_STATUS_ID"])){
					return false;
				}
			}
			if(!$this->Edit($id, NULL, NULL, $oldPassword, $password)){
				return false;
			}else {
				return true;
			}
		}
	}

	/**
	 * @access public
	 * @return obj[]
	 * @ReturnType obj[]
	 */
	public function GetUsers($filter=NULL, $order=NULL, $start=0, $size=0, &$recordsCount=NULL, $ListOrSelect = 0)
	{
		$allUsers =array();
		$allStatusCondition =NULL;
		// if(! isset($order)){
		// 	$order = array("LOGIN"=>"ASC");
		// }
		//Original query
		$select = 'SELECT `USER_ID`, `LOGIN`, `PASSWORD`, `NAME`, `USER`.`USER_STATUS_ID`, `FAILED_ATTEMPT_NUM` , `USER_STATUS` ,
					`NODE_NAME` AS DIR_NAME, `USER`.`user_department_node_id`
					FROM `USER`
					INNER JOIN `USER_STATUS` ON `USER_STATUS`.`USER_STATUS_ID` = `USER`.`USER_STATUS_ID`
					INNER JOIN '.$this->treeName.' ON `USER`.`user_department_node_id` = `NODE_ID` ';

		$params = $this->db->ConvertToParamsArray(null, $filter);
		$allUsers = $this->db->SelectData($select, $params, $order, $start, $size, $recordsCount, $ListOrSelect);

		//Check resutl
		$this->State = $this->db->state;
		if($allUsers === NULL){
			//empty result
			$this->Message = "user_not_exists";
			return false;
		}else if($allUsers === false){
			//something wrong
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->Message = "success";
			return $allUsers;
		}
	}
	public function GetUser($order=NULL, $start=0, $size=0, &$recordsCount=NULL)
	{
		$params=null;
		$sql = "SELECT *
                FROM `USER`
                ";
		$condition = 'WHERE `USER_STATUS_ID`=1 ';
        $sql .= $condition." ORDER BY `user_department_node_id` ASC";
        $result = $this->db->SelectData($sql, $params, NULL, $start, $size, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
	}
	public function GetCollectUser()
	{
		$params=null;
		$sql = "SELECT `USER_ID`, `NAME`
                FROM `USER`
				WHERE `USER_STATUS_ID`=1 and WEB_BROWSE = 0
				ORDER BY `user_department_node_id`, NAME ASC";
        $result = $this->db->SelectData($sql, $params, NULL, $start, $size, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
	}
	public function GetAccountUser()
	{
		$params=null;
		$sql = "SELECT *
                FROM `USER`
                ";
		$condition = 'WHERE `USER_STATUS_ID`=1 AND user_id NOT IN (SELECT user_id from account)';
        $sql .= $condition." ORDER BY `user_department_node_id` ASC";
        $result = $this->db->SelectData($sql, $params, NULL, $start, $size, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
	}

	public function GetUsersByDir($node_id)
	{
		$select = "SELECT `USER_ID`, `NAME`
					FROM USER
					INNER JOIN ".$this->treeName." ON NODE_ID=USER.user_department_node_id AND NODE_ID=?";

		$filter = array();
		$filter[] = $this->db->SqlVal($node_id,"int");
		$params = $this->db->ConvertToParamsArray($filter);
		$allUsers = $this->db->SelectData($select, $params, null, null, null, $count,1);
		//Check resutl
		$this->State = $this->db->state;
		if($allUsers === NULL){
			//empty result
			$this->Message = "user_not_exists";
			return false;
		}else if($allUsers === false){
			//something wrong
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->Message = "success";
			return $allUsers;
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @ParamType id int
	 */
	public function GetUserGroups($id) {
		$userInfo = $this->GetInfo($id);
		if(!$userInfo){
			return false;
		}else {
			$sql = 'SELECT `GROUP`.`GROUP_ID`, `GROUP_NAME` FROM `USER_GROUP`
												JOIN `GROUP` ON `GROUP`.`GROUP_ID` = `USER_GROUP`.`GROUP_ID`
												WHERE `USER_ID`=?
												ORDER BY `GROUP_NAME`';
			$filter = array();
			$filter[] = $this->db->SqlVal($id,"int");
			$params = $this->db->ConvertToParamsArray($filter);
			$userGroups = $this->db->SelectData($sql, $params,null,null,0,$recordsCount,1);
			$this->State = $this->db->state;
			$this->Message = $this->db->message;
			if($userGroups === NULL){
				//empty result
				return false;
			}else if($userGroups === false){
				//something wrong
				return false;
			}else{
				return $userGroups;
			}
		}
	}
	/**
	 * @access public
	 * @param int id
	 * @ParamType id int
	 */
	public function UserNonAssosiatedGroups($id) {
		$userInfo = $this->GetInfo($id);
		if(!$userInfo){
			return false;
		}else {
			$sql = 'SELECT `GROUP`.`GROUP_ID`, `GROUP_NAME` , `USER_ID`
											FROM `GROUP`
											LEFT JOIN `USER_GROUP` ON `GROUP`.`GROUP_ID` = `USER_GROUP`.`GROUP_ID`
											AND `USER_ID`=?
											WHERE `USER_GROUP`.`USER_ID` IS NULL AND `GROUP`.`STATUS_ID` = '.self::STATUS_ACTIVE.'
											ORDER BY `GROUP_NAME`';
			$filter = array();
			$filter[] = $this->db->SqlVal($id,"int");
			$params = $this->db->ConvertToParamsArray($filter);
			$groups = $this->db->SelectData($sql, $params,null,null,0,$recordsCount,1);

			$this->State = $this->db->state;
			$this->Message = $this->db->message;
			if($groups === NULL){
				return false;
			}else if($groups === false){
				//something wrong
				return false;
			}else{
				return $groups;
			}
		}
	}

	/**
	 * @access public
	 * @param int id
	 * @param int group
	 * @return boolean
	 * @ParamType id int
	 * @ParamType group int
	 * @ReturnType boolean
	 */
	public function AddToGroup($id, $group) {
		$userInfo = $this->GetInfo($id);
		if(!$userInfo){
			return false;
		}else {
			if($this->UserInGroup($id, $group)){
				return false;
			}else{
				$data = array("USER_ID"=>$this->db->SqlVal($id, "int"),
								"GROUP_ID"=>$this->db->SqlVal($group, "int"));

				$res = $this->db->Insert("USER_GROUP", $data);
				$this->State = $this->db->state;
				if(!$res)
				{
					$this->Message = "user_add_group_failed";
					return false;
				}else{
					$this->Message = "user_add_group_success";
				return true;
				}
			}
		}
	}

	protected function UserInGroup($id, $group)
	{
		 $sql ='SELECT `GROUP`.`GROUP_ID`, `GROUP_NAME` FROM `USER_GROUP`
												JOIN `GROUP` ON `GROUP`.`GROUP_ID` = `USER_GROUP`.`GROUP_ID`
												WHERE `USER_ID`=?
												AND `GROUP`.`GROUP_ID`=?';
		$filter = array();
		$filter[] = $this->db->SqlVal($id,"int");
		$filter[] = $this->db->SqlVal($group,"int");
		$params = $this->db->ConvertToParamsArray($filter);
		$userGroup = $this->db->SelectData($sql, $params);
		$this->State = $this->db->state;
		if($userGroup === NULL){
			//empty result
			$this->Message = "user_not_in_group";
			return false;
		}else if($userGroup === false){
			//something wrong
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->Message = "user_in_group";
			return $userGroup;
		}
	}
	/**
	 * @access public
	 * @param int id
	 * @return boolean
	 * @ParamType id int
	 */
	public function IsAdmin($id) {
		$userInfo = $this->GetInfo($id);
		if(!$userInfo){
			return false;
		}else { // admin group
			if($this->UserInGroup($id, 5)){
				return true;
			}else{
				return false;
			}
		}
	}
	/**
	 * @access public
	 * @param int id
	 * @param int group
	 * @return boolean
	 * @ParamType id int
	 * @ParamType group int
	 * @ReturnType boolean
	 */
	public function RemoveFromGroup($id, $group) {
		$userInfo = $this->GetInfo($id);
		if(!$userInfo){
			return false;
		}else {

			if(!$this->UserInGroup($id, $group)){
				return false;
			}else{
				$condition = array();
				$condition["USER_ID"] = array("Value"=> $id, "Type"=>"int");
				$condition["GROUP_ID"] = array("Value"=> $group, "Type"=>"int");

				$res = $this->db->Delete("USER_GROUP", $condition);
				$this->State = $this->db->state;
				if(!$res){
					$this->Message = "user_remove_group_failed";
					return false;
				}else{
					$this->Message = "user_remove_group_success";
					return true;
				}
			}
		}
	}



	protected function UserExists($id)
	{
		$userInfo = $this->GetInfo($id);
		if(!$userInfo)
			return false;
		else{
			$this->Message = "user_already_exists";
			return true;
		}
	}

	public function LoginExists($login, $userId=NULL)
	{
		$res = $this->db->ValueExists("USER", "LOGIN", $login, "USER_ID", $userId);
		$this->State = $this->db->state;
		if($res === FALSE){
			//something wrong
			$this->Message = "user_login_name_available";
			return false;
		}else{
			$this->Message = "user_login_name_taken";
			return true;
		}
	}

	protected function ValidateLoginName($login)
	{
		////TBD
		return true;
	}

	protected function ValidatePassword($password)
	{
		////TBD
		return true;
	}

	protected function AddFailedLogin($id, $attemptNum)
	{
		if(!$this->Edit($id, NULL, NULL, NULL, NULL, NULL, $attemptNum+1)){
			return false;
		}else{
			return $attemptNum;
		}
	}

	protected function ResetFailedLogin($id)
	{
		if(!$this->Edit($id, NULL, NULL, NULL, NULL, NULL, 0)){
			return false;
		}else{
			return true;
		}
	}
	public function GetOperationName($form)
	{
		$sql = 'SELECT `OPERATION_NAME`
				FROM `FORM`
				WHERE `FORM_CODE` = ?';
		$filter = array();
		$filter[] = $this->db->SqlVal($form,"mytext");
		$params = $this->db->ConvertToParamsArray($filter);

		//Select data
		$operation_name = $this->db->SelectData($sql, $params);
		//Check resutl
		$this->State = $this->db->state;
		if($operation_name === NULL){
			//empty result
			$this->Message = "user_not_exists";
			return false;
		}else if($operation_name === false){
			//something wrong
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->Message = "success";
			return $operation_name[0]["OPERATION_NAME"];
		}
	}

	public function AddUserToGroup($operation, $id, $group)
	{
		$return = true;
		if ($operation == 'Insert') {
			if(is_array($group))
			{
				for($i=0; $i< count($group) ;$i++)
				{
					$result = $this -> AddToGroup($id, $group[$i]);
					if($result!= true)
						$return = false;
				}
			}
		} else if ($operation == 'Delete'){
			for($i=0; $i< count($group) ;$i++)
			{
				$result = $this -> RemoveFromGroup($id, $group[$i]);
				if($result!= true)
					$return = false;
			}
		}
		return $return;
	}

	public function ClientLogin($loginName, $password)
	{
		$sql = 'SELECT `USER`.`USER_ID`, `PASSWORD`, `NAME`, `USER_STATUS_ID`, `FAILED_ATTEMPT_NUM`, `UI_LANGUAGE` , `CHANGE_PASSWORD`, WEB_BROWSE, `user_department_node_id`,
				client_id, client_name, user_picture
				FROM `USER`
				INNER JOIN `client` ON `client`.`user_id`=`USER`.`USER_ID`
				WHERE `LOGIN`=?';
		$filter = array();
		$filter[] = $this->db->SqlVal(strtolower($loginName), "mytext");
		$params = $this->db->ConvertToParamsArray($filter);
		$userInfo = $this->db->SelectData($sql,$params);
		//Check lgoin name
		if(!$userInfo){
			$this->State = self::WARNING;
			$this->Message = "client_not_exists";
			return false;
		}else {
			$userId = $userInfo[0]['USER_ID'];
			//Check Dealer Status
			//$dealer_status = $this->tree->GetNodeStatus($userInfo[0]['AGENT_ID']);



				$this->cryptData->MediaDecrypt($userInfo[0]['PASSWORD']);
				//Check password
				if($password == $this->cryptData->MediaDecrypt($userInfo[0]['PASSWORD']))
				{



					//Check status
					if(!$this->CheckUserStatus( $userInfo[0]['USER_STATUS_ID'] )){
						$this->State = self::WARNING;
						return false;
					}else{
						// is active status
						//If log in succeeded, and status is active, reset failed attempts number
						$this->ResetFailedLogin($userId);
						$this->State = self::SUCCESS;
						$this->Message = "client_login_sucess";
						return $userInfo;
					}

				}else{
					// wrong password
					$this->AddFailedLogin($userId, $userInfo[0]['FAILED_ATTEMPT_NUM']);
					$this->State = self::WARNING;
					$this->Message = "client_wrong_pass";
					//If number of failed login attempts is greater than allowed attempts (ALLOWED_ATTEMPTS constant) then lock the account
					if($userInfo[0]['FAILED_ATTEMPT_NUM']>= self::ALLOWED_ATTEMPTS){
						$this->LockUser($userId);
						$this->Message = "client_status_locked";
					}
					return false;

			}
		}

	}


	public function GetUsersStatus(){
		$select = "SELECT USER_STATUS_ID, USER_STATUS
					FROM USER_STATUS ORDER BY USER_STATUS_ID";

		$allstatus = $this->db->SelectData($select, null, null, null, null, $count, 1);
		return $allstatus;
	}
	/*public function ValidateUser($user_id, $agent_id, $from_code, $allow_children = true){
		$sql = "SELECT VALIDATE_USER(". $this->db->SqlVal($user_id,"int").",".
										$this->db->SqlVal($agent_id,"int").",".
										$this->db->SqlVal($from_code,"text").",".
										$this->db->SqlVal($allow_children,"int").
									") FROM DUAL";
		$result = $this->db->SelectValue($sql);
		return (bool)$result;
	}*/
}
?>
