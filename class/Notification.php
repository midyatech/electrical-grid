<?php
require_once(realpath(dirname(__FILE__)) . '/MysqliDB.php');//MysqliDB
require_once(realpath(dirname(__FILE__)) . '/Tree.php');
require_once(realpath(dirname(__FILE__)) . '/Document.class.php');

/**
 * @access public
 */
class Notification {

	protected $db, $document;

	/**
	 * @access public
	 */
	public function __construct() {
		$this->db = new MysqliDB();
		$this->document = new Document();
	}

	public function GetApplicationNotificationsCount($dir_id){
		$sql = "SELECT COUNT(*) FROM client_application_trace WHERE is_read=0 AND is_current=1 AND assigned_to_department_node_id =?";
		$filter = array();
        $filter[] = $this->db->SqlVal($dir_id, 'int');
        $params = $this->db->ConvertToParamsArray($filter);
        $res = $this->db->SelectValue($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
		if($res == 0){
			$res = "";
		}
		return $res;
	}

	public function GetDocinNotificationsCount($dir_id)
	{
		$document = $this->document;

		$sql = "SELECT COUNT(*) FROM doc_trace_view
		WHERE assigned_to_department_node_id=?
		AND is_current=?
		AND is_read=?
		AND in_out_trace_status_id =?";

		$condition = array();
		$condition["assigned_to_department_node_id"] = $dir_id;
		$condition["is_current"]=1;
		$condition["is_read"]=0;
		$condition["in_out_trace_status_id#3"] = $document::INOUT_SENT;

        $params = $this->db->ConvertToParamsArray($condition);
        $res = $this->db->SelectValue($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
		if($res == 0){
			$res = "";
		}
		return $res;
	}
}
?>
