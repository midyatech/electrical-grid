<?php
require_once (realpath(dirname(__FILE__)) . '/MysqliDB.php');

/**
 * @access public
 * @package EMPLOYEE
 */
class Message {

    //state constants
    const ERROR = 0;
    const WARNING = 1;
    const SUCCESS = 2;

    public $Message;
    public $State;
    public $db;
    protected $dir_path;

    public function __construct($access_dir_path=NULL) {
        $this -> db = new MysqliDB();
        $this->dir_path = $access_dir_path;
    }

    public function __destruct() {
        $this -> db = null;
    }
    
    public function AddMessage($message_data)
    {
        $this->db->BeginTransaction();

        //insert Message
        $data = array();
        $data['from_user_id'] = $message_data["from_user_id"];
        $data['to_user_id'] = $message_data["to_user_id"];
        $data['message_text'] = $message_data["message_text"];
        $data['message_date'] = date('Y-m-d');
        $data['latitude'] = $message_data["latitude"];
        $data['longitude'] = $message_data["longitude"];
        $data['message_status_id'] = 1;

        $unit_id = $this->db->Insert("message", $data, true);
        
        if($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "message_insert_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "message_insert_success";
            return $unit_id;
        }
    }


    public function EditMessage($message_data)
    {
        $error = false;
        $this->db->BeginTransaction();

        $message_id = $message_data["message_id"];

        //Update Message
        $data = array();
        $data['from_user_id'] = $message_data["from_user_id"];
        $data['to_user_id'] = $message_data["to_user_id"];
        $data['message_text'] = $message_data["message_text"];
        $data['latitude'] = $message_data["latitude"];
        $data['longitude'] = $message_data["longitude"];
        $data['message_status_id'] = $message_data["message_status_id"];

        $condition = array();
        $condition['message_id'] = $message_id;
        $result1 = $this->db->Update("message", $data, $condition);

        if(!$result1){
            $error = true;
        }

        if($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "message_update_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "message_update_success";
            return $unit_id;
        }
    }
    
    
    public function GetMessageList( $condition=NULL , $order=NULL, $start=0, $size=0, &$recordsCount=NULL )
    {
        $sql = "SELECT  message_id, from_user_id, to_user_id, message_text, message_date, latitude, longitude, message.message_status_id, message_status_name, NAME
                FROM message
                INNER JOIN message_status on message.message_status_id = message_status.message_status_id
                INNER JOIN USER on message.to_user_id = USER.USER_ID
                INNER JOIN AREA_TREE on USER.user_department_node_id = AREA_TREE.NODE_ID" ;
        
        if ( $order == NULL ){
            $order=array();
            $order["message_id"]="DESC";
        }
    
        if($condition!=null){
            $params = $this->db->ConvertToParamsArray(NULL,$condition);
        }
        
        $allMessage = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount, 0, $condition_start);
        
        if($allMessage){
            return $allMessage;
        }else{
          return false;
        }
    }

    public function GetUsersByDepID( $DipID = NULL, $User_ID = NULL )
    {
        $params=null;
        $sql = "SELECT * FROM `USER` ";
        $condition = 'WHERE `USER_STATUS_ID`=1 ';
        if ( $DipID != NULL ) {
            $condition .= 'AND user_department_node_id = '.$this -> db -> SqlVal($DipID, "int");    
        }
        if ( $User_ID != NULL ) {
            $condition .= 'AND USER_ID = '.$this -> db -> SqlVal($User_ID, "int");    
        }
        
        $sql .= $condition." ORDER BY `user_department_node_id` ASC";
        $result = $this->db->SelectData($sql, $params, NULL, 0, 0, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetMessageStatus()
    {
        $params=null;
        $sql = "SELECT * FROM message_status";
        $result = $this->db->SelectData($sql, $params, NULL, 0, 0, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

}
?>
