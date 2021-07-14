<?php
require_once(realpath(dirname(__FILE__)) . '/MysqliDB.php');
require_once(realpath(dirname(__FILE__)) . '/Dictionary.php');
require_once(realpath(dirname(__FILE__)) . '/HtmlHelper.php');

class SysetemMessage
{
	protected $db;
	protected $dictionary;
	protected $messages;
	protected $html;
	public function __construct($language) {
		$this->db = new MysqliDB();
		$this->db->OpenConnection();
		$this->dictionary = new Dictionary($language);
		$this->messages = array();
		$this->html = new HTML($language);
	}

	public function AddMessage($status,$message)
	{
		$this->messages[] = array("status"=>$status, "message"=>$this->dictionary->GetValue($message));
		//return message index
		return count($this->messages)-1;
	}

	public function RemoveMessage($message_index)
	{
		if(isset($this->messages[$message_index]))
			unset($this->messages[$message_index]);
	}

	public function RemoveAllMessages()
	{
		for($i=0; $i<count($this->messages); $i++){
			unset($this->messages[$i]);
		}
	}

	public function PrintMessage($message_index=NULL)
	{
		for($i=0; $i<count($this->messages); $i++){
			if($message_index!=NULL){
				if($i == $message_index){
					$this->html->PrintMessage($this->messages[$message_index]);
					$this->RemoveMessage($message_index);
				}
			}
			else{
				$this->html->PrintMessage($this->messages[$i]);
				$this->RemoveMessage($i);
			}
		}
	}

	public function PrintMessages($action, $button_text=NULL)
	{
		//$this->html->PrintMessages($this->messages, $action, $button_text);
		$this->html->PrintAlerts($this->messages, $action, $button_text);
		$this->RemoveAllMessages();
	}

	public function GetMessagesCount()
	{
		return count($this->messages);
	}

	public function GetMessages()
	{
		return $this->messages;
	}

	public function PermissionMessage($is_valid, $kill = true){
		if(!$is_valid){
			$this -> AddMessage(0, "permission_denied");
			$this -> PrintMessages("back");
			if($kill)
				die();
		}
		return $is_valid;
	}

	public function PrintJsonMessage(){
		ob_clean();
		header('Content-type: application/json');
		echo json_encode($this->GetMessages());
	}

}

?>
