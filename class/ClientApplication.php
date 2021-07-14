<?php

require_once realpath(dirname(__FILE__)).'/MysqliDB.php';

/**
 */
class ClientApplication
{
    //state constants
    const ERROR = 0;
    const WARNING = 1;
    const SUCCESS = 2;
    //tree node status constants
    const STATUS_DELETED = 3;
    const STATUS_INACTIVE = 2;
    const STATUS_ACTIVE = 1;

    const CLIENT_APPLICATION_CATEGORY = 1;
    const CLIENT_APPLICATION_STATUS_REQUEST = 2;
    const CLIENT_APPLICATION_STATUS_ARCHIVE = 6;
    const CLIENT_APPLICATION_STATUS_RESPONSE = 3;
    const CLIENT_APPLICATION_STATUS_APPROVE = 4;
    const CLIENT_APPLICATION_STATUS_DOCIN = 5;


    /**
     * @AttributeType string
     */
    public $Message;
    public $State;
    /**
     * @AttributeType MySQLDB
     */
    public $db;
    protected $dir_path;
    /**
     */
    public function __construct($access_dir_path = null)
    {
        $this->db = new MysqliDB();
        $this->dir_path = $access_dir_path;
    }

    public function GetLang()
    {
        $sql = 'SELECT `lang_id`, `lang_keyword` FROM `lang` ORDER BY `lang_id`';
        $res = $this->db->SelectData($sql, null, null, null, null, $count, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;

        return $res;
    }

    public function GetClientApplications($filter, $order, $start = 0, $size = 0, &$recordsCount = null)
    {
        $sql = 'SELECT client_application.client_application_id, application_name, application_date, document_issue_date, document_subject,
                document_content, document_lang_id, document_attachment, document_note, client_id, client_application_trace.client_application_trace_status_id,
                trace_request_type_id, client_application_trace_status_keyword,client_application_trace.is_read, client_application_trace.note, trace_date
                FROM application
                INNER JOIN client_application ON application.application_id = client_application.application_id
				LEFT JOIN client_application_trace ON client_application.client_application_id = client_application_trace.client_application_id and is_current=1
				LEFT JOIN client_application_trace_status ON client_application_trace.client_application_trace_status_id = client_application_trace_status.client_application_trace_status_id';

        $params = $this->db->ConvertToParamsArray(null,$filter);
        $res = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $res;
    }
    //
    // public function GetPendingClientApplications($client_id)
    // {
    //     $sql = 'SELECT client_application.client_application_id, application_name, client_application_trace.client_application_trace_status_id,
    //             trace_request_type_id, client_application_trace_status_keyword, client_application_trace.note, trace_date
    //             FROM application
    //             INNER JOIN client_application ON application.application_id = client_application.application_id
	// 			LEFT JOIN client_application_trace ON client_application.client_application_id = client_application_trace.client_application_id and is_current=1
	// 			LEFT JOIN client_application_trace_status ON client_application_trace.client_application_trace_status_id = client_application_trace_status.client_application_trace_status_id
    //             WHERE client_id = ?
    //             AND is_read = 0
    //             AND (client_application_trace.client_application_trace_status_id = 1
    //                 OR client_application_trace.client_application_trace_status_id = 2
    //                 OR client_application_trace.client_application_trace_status_id = 3)
    //             ORDER BY trace_date DESC';
    //
    //     $filter = array();
    //     $filter[] = $client_id;
    //     $params = $this->db->ConvertToParamsArray($filter);
    //
    //     $res = $this->db->SelectData($sql, $params);
    //     $this->Message = $this->db->message;
    //     $this->State = $this->db->state;
    //     return $res;
    // }

    public function GetApplications()
    {
        $sql = 'SELECT application_id, application_name
				FROM application
				WHERE application_category_id='. self::CLIENT_APPLICATION_CATEGORY;
        $res = $this->db->SelectData($sql, null, null, null, null, $count, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $res;
    }

    public function GetClientApplicationsTrace($filter, $status=null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $sql = 'SELECT client_application.client_application_id, application_name, application_date, document_issue_date, document_subject,
                document_content, document_lang_id, document_attachment, document_note,client_application.client_id, client_application_trace.client_application_trace_status_id,is_read,assign_to_user.NAME AS assign_to_user_name,from_user.NAME AS from_user_name,client_application_trace_status_keyword,
                trace_date, assign_to_user.user_picture,from_user.user_picture as from_user_picture, client_application_trace_status.icon, client_application_trace_status.color, client.client_name, note
                FROM application
                INNER JOIN client_application ON application.application_id = client_application.application_id
				INNER JOIN client_application_trace ON client_application.client_application_id = client_application_trace.client_application_id
				INNER JOIN client_application_trace_status ON client_application_trace.client_application_trace_status_id = client_application_trace_status.client_application_trace_status_id
                INNER JOIN client ON client.client_id = client_application.client_id
                LEFT JOIN USER assign_to_user ON (assign_to_user.USER_ID = client_application_trace.assigned_to_user_id)
                INNER JOIN USER from_user ON (from_user.USER_ID = client_application_trace.from_user_id)';
        $where = "";
        if($status != null){
            $where .= "AND";
        }
        if(isset($status["client_request"])){
            $where .= " (client_application_trace.client_application_trace_status_id = ".$status["client_request"];
        }
        if(isset($status["employee_response"])){
            if ($where != "AND"){
                $where .= " OR";
            }else{
                $where .= " (";
            }
            $where .= " client_application_trace.client_application_trace_status_id = ".$status["employee_response"];
        }
        if(isset($status["approve"])){
            if ($where != "AND"){
                $where .= " OR";
            }else{
                $where .= " (";
            }
            $where .= " client_application_trace.client_application_trace_status_id = ".$status["approve"];
        }
        if(isset($status["submit"])){
            if ($where != "AND"){
                $where .= " OR";
            }else{
                $where .= " (";
            }
            $where .= " client_application_trace.client_application_trace_status_id = ".$status["submit"];
        }
        if(isset($status["generated_docin"])){
            if ($where != "AND"){
                $where .= " OR";
            }else{
                $where .= " (";
            }
            $where .= " client_application_trace.client_application_trace_status_id = ".$status["generated_docin"];
        }
        if($status != null){
            $where .= " ) ";
        }
        $sql .= $where;
        //print  $sql."<br><br>";

        $params = $this->db->ConvertToParamsArray(null, $filter);
        $order = array("trace_date"=>"DESC");

        $res = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);

        $this->Message = $this->db->message;
        $this->State = $this->db->state;

        return $res;
    }

    public function GetClientApplication($client_application_id)
    {
        $sql = 'SELECT client_application.client_application_id, application.application_id, application_name, application_date, document_number, document_issue_date, document_subject,
                document_content, document_lang_id, document_attachment, document_note, client_name, client.client_id,
				submit_trace.trace_date AS submit_date, approve_trace.trace_date AS approve_date, client_application_trace_status.client_application_trace_status_keyword
                FROM application
                INNER JOIN client_application ON application.application_id = client_application.application_id
				INNER JOIN client ON client.client_id = client_application.client_id
				LEFT JOIN client_application_trace AS last_trace
					ON client_application.client_application_id = last_trace.client_application_id
					AND last_trace.is_current = 1
				LEFT JOIN client_application_trace_status
					ON last_trace.client_application_trace_status_id = client_application_trace_status.client_application_trace_status_id
				LEFT JOIN client_application_trace AS submit_trace
					ON client_application.client_application_id = submit_trace.client_application_id
					AND submit_trace.client_application_trace_status_id = 1
				LEFT JOIN client_application_trace AS approve_trace
					ON client_application.client_application_id = approve_trace.client_application_id
					AND approve_trace.client_application_trace_status_id = 4
                WHERE client_application.client_application_id=?';
        $filter = array();//
        $filter[] = $this->db->SqlVal($client_application_id, 'int');
        $params = $this->db->ConvertToParamsArray($filter);
        $res = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $res;
    }


    public function GetApplication($application_id)
    {
        $sql = 'SELECT application_id, application_name, notes
				FROM application
				WHERE application_id=?';
        $filter = array();
        $filter[] = $this->db->SqlVal($application_id, 'int');
        $params = $this->db->ConvertToParamsArray($filter);

        $res = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;

        return $res;
    }

    public function GetApplicationForms($application_id)
    {
        $sql = 'SELECT
				application.application_id,
				application.application_name,
				application.notes,
				application_application_form.sequence,
				application_form.application_form_id,
				application_form.application_form_name
				FROM
				application
				INNER JOIN application_application_form ON application_application_form.application_id = application.application_id
				INNER JOIN application_form ON application_form.application_form_id = application_application_form.application_form_id
				WHERE application.application_id = ?
				ORDER BY application_application_form.sequence';

        $filter = array($this->db->SqlVal($application_id, 'int'));
        $params = $this->db->ConvertToParamsArray($filter);

        $res = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;

        return $res;
    }

    public function GetFormSections($fomr_id)
    {
        $sql = 'SELECT
				application_form_section.application_form_section_id,
				application_form_section.application_form_section_name,
				application_form_section.sequence
				FROM
				application_form
				INNER JOIN application_form_section ON application_form.application_form_id = application_form_section.application_form_id
				WHERE application_form.application_form_id = ?
				ORDER BY application_form_section.sequence';

        $filter = array($this->db->SqlVal($fomr_id, 'int'));
        $params = $this->db->ConvertToParamsArray($filter);

        $res = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;

        return $res;
    }

    public function GetFormSectionFields($client_application_id, $section_id)
    {
        $sql = 'SELECT
				application_form_section_field.application_form_section_field_id,
				application_form_section_field.application_form_section_field_name,
				application_form_section_field.sequence,
				application_form_section_field_type.application_form_section_field_type,
				application_form_section_field_type.application_form_section_field_type_id,
				client_application_value.`value`, client_application.client_id
				FROM application_application_form
                INNER JOIN application_form ON application_application_form.application_form_id = application_form.application_form_id
				INNER JOIN application_form_section ON application_application_form.application_form_id = application_form_section.application_form_id
				INNER JOIN application_form_section_field ON application_form_section.application_form_section_id = application_form_section_field.application_form_section_id
				INNER JOIN application_form_section_field_type ON application_form_section_field.application_form_section_field_type_id = application_form_section_field_type.application_form_section_field_type_id
				LEFT JOIN client_application ON client_application.application_id = application_application_form.application_id
				LEFT JOIN client_application_value ON client_application_value.client_application_id = client_application.client_application_id
					AND application_form_section_field.application_form_section_field_id = client_application_value.application_form_section_field_id
				WHERE client_application.client_application_id = ?
                AND application_form_section.application_form_section_id = ?
				ORDER BY application_form_section_field.sequence';

        $filter = array();
        $filter[] = $this->db->SqlVal($client_application_id, 'int');
        $filter[] = $this->db->SqlVal($section_id, 'int');
        $params = $this->db->ConvertToParamsArray($filter);

        $res = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;

        return $res;
    }

    public function GetFormSectionFieldOptions($field_id)
    {
        $sql = 'SELECT
				application_form_section_field_option.application_form_section_field_option_id AS option_id,
				application_form_section_field_option.application_form_section_field_option_value,
				application_form_section_field_option.application_form_section_field_id
				FROM
				application_form_section_field
				INNER JOIN application_form_section_field_option ON application_form_section_field.application_form_section_field_id = application_form_section_field_option.application_form_section_field_id
				WHERE application_form_section_field.application_form_section_field_id = ?
				ORDER BY application_form_section_field.sequence';

        $filter = array();
        $filter[] = $this->db->SqlVal($field_id, 'int');
        $params = $this->db->ConvertToParamsArray($filter);
        $res = $this->db->SelectData($sql, $params, null, null, null, $count, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;

        return $res;
    }

    public function SaveClientApplicationForm($client_application_id, $data_array)
    {
        $error = false;
        $this->db->BeginTransaction();

        $delete_condition = array();
        $delete_condition['client_application_id'] = array('Value' => $client_application_id, 'Type' => 'int');
        $result1 = $this->db->Delete('client_application_value', $delete_condition);

        if (!$result1) {
            $error = true;
        } else {
            foreach ($data_array as $key => $value) {
                $data = array();
                $data['client_application_id'] = $client_application_id;
                $data['application_form_section_field_id'] = $key;
                $data['value'] = $value;
                $result2 = $this->db->Insert('client_application_value', $data);
                if (!$result2) {
                    $error = true;
                }
            }
        }
        if ($error) {
            $this->db->RollbackTransaction();
            $this->Message = 'application_form_insert_failed';
            $this->State = self::ERROR;
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->Message = 'application_form_insert_success';
            $this->State = self::SUCCESS;
            return true;
        }
    }

    public function UpdateClientApplicaiton($condition, $data)
    {
        $res = $this->db->Update('client_application', $data, $condition);
        $this->State = $this->db->state;
        $db = $this->db;
        if ($db->state == $db::ERROR) {
            $this->Message = 'client_application_update_failed';
            return false;
        } else {
            $this->Message = 'client_application_update_success';
            return true;
        }
    }

    public function InsertClientApplicaiton($data)
    {
        $res = $this->db->Insert('client_application', $data, true);
        $this->State = $this->db->state;
        $db = $this->db;
        if ($db->state == $db::ERROR) {
            $this->Message = 'client_application_insert_failed';

            return false;
        } else {
            $this->Message = 'client_application_insert_success';

            return $res;
        }
    }

    public function InsertClientApplicaitonTrace($data)
    {
        $error = false;
      //  $this->db->BeginTransaction();

        $condition = array();
        $condition['is_current#0'] = 1;
        $condition['client_application_id'] = $this->db->SqlVal($data['client_application_id'], 'int');
        $update_data = array();
        $update_data['is_current#1'] = 0;
        $update_data['close_date'] = date('Y-m-d H:i:s');

        $res = $this->db->Update('client_application_trace', $update_data, $condition);
        if (!$res) {
            $error = true;
        } else {
            $res2 = $this->db->Insert('client_application_trace', $data, true);
            if (!$res2) {
                $error = true;
            }
        }

        $db = $this->db;
        if ($error) {
            $this->State = self::ERROR;
            $this->Message = 'client_application_trace_insert_failed';
            $result = $this->db->RollbackTransaction();
            return false;
        } else {
            $this->State = self::SUCCESS;
        //    $this->db->CommitTransaction();
            $this->Message = 'client_application_trace_insert_success';
            return $res;
        }
    }

    public function GetClientApplicationTraceCount($client_application_id)
    {
        $sql = 'SELECT COUNT(*) FROM client_application_trace WHERE client_application_id=?';

        $filter = array();
        $filter[] = $this->db->SqlVal($client_application_id, 'int');
        $params = $this->db->ConvertToParamsArray($filter);

        $res = $this->db->SelectValue($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;

        return $res;
    }
    //SetAsUnreadTrace
    public function SetTraceReadState($client_application_id, $is_read,$user_dir)
    {

        $data = array();
        $data['is_read'] = $this -> db -> SqlVal($is_read, "int");
        $condition = array();
        $condition['client_application_id'] = $this -> db -> SqlVal($client_application_id, "int");
        $condition['from_department_node_id'] = array("Operator"=>"!=","Value"=>$user_dir, "Type"=>"int");
        $condition['is_current'] = 1;


        if (!$this -> db -> Update("client_application_trace",$data,$condition))
        {
            $this -> State = $this -> db -> state;
            $this -> Message = "client_application_trace_read_failed";
            return false;

        }
        else
        {
            $this -> State = self::SUCCESS;
            $this -> Message = "client_application_trace_read_success";
            return true;
        }
    }

    public function GetClientApplicationTrace($client_application_id, $only_current=null)
    {
        $sql = 'SELECT client_application_trace_id, client_application.client_application_id, from_department_node_id, from_user_id, trace_date, assigned_to_department_node_id, DIR_TREE.NODE_NAME,
				assigned_to_user_id, close_date, attachment,client_application_trace.client_application_trace_status_id, is_read, is_current, note,
                client_application_trace_status.client_application_trace_status_keyword, client_application_trace_status.trace_request_type_id,
                client_application_trace_status.color AS response_color, client_application_trace_status.icon AS response_icon,
				ASSIGN_TO_USER.NAME AS assign_to_user_name, ASSIGN_TO_USER.user_picture AS assign_to_user_picture,
                FROM_USER.NAME AS from_user_name, FROM_USER.user_picture AS from_user_picture,
                CT.NODE_ID AS client_dir
 				FROM client_application_trace
                INNER JOIN client_application_trace_status on client_application_trace_status.client_application_trace_status_id=client_application_trace.client_application_trace_status_id
		 		LEFT JOIN USER AS ASSIGN_TO_USER on ASSIGN_TO_USER.user_id = client_application_trace.assigned_to_user_id
                LEFT JOIN USER AS FROM_USER on FROM_USER.user_id = client_application_trace.from_user_id
                LEFT JOIN DIR_TREE ON DIR_TREE.NODE_ID= client_application_trace.assigned_to_department_node_id
                INNER JOIN client_application on client_application_trace.client_application_id = client_application.client_application_id
                INNER JOIN client ON client_application.client_id = client.client_id
                INNER JOIN USER AS CLIENT_USER ON CLIENT_USER.user_id = client.user_id
                INNER JOIN DIR_TREE CT on CT.NODE_ID = CLIENT_USER.user_department_node_id
				WHERE client_application.client_application_id=?';
		if($only_current===1){
			$sql .= ' AND is_current=1';
		}
        else if($only_current===0){
            $sql .= ' AND is_current=0';
        }
        $sql .=' ORDER BY  trace_date DESC';
        $filter = array();
        $filter[] = $this->db->SqlVal($client_application_id, 'int');
        $params = $this->db->ConvertToParamsArray($filter);

        $res = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;

        return $res;
    }

    public function GetDocInSummary($client_application_id)
    {
        $sql = 'SELECT doc_in.doc_in_id, doc_in_number, memo_timestamp,
				doc_in_trace_status.doc_in_trace_status_keyword, doc_in_trace.trace_date, doc_in_trace.from_department_node_id, doc_in_trace.from_user_id,  doc_in_trace_status_keyword,
                assigned_to_department_node_id, USER.name AS assigned_to_user_name,
				doc_in_trace_request_type_keyword, issued_date
				FROM client_application
				INNER JOIN doc_in on client_application.doc_in_id = doc_in.doc_in_id
				INNER JOIN memo on doc_in.doc_in_id = memo.doc_in_id
                INNER JOIN doc_in_trace ON doc_in_trace.memo_id = memo.memo_id AND is_current = 1
				INNER JOIN doc_in_trace_request_type_status ON doc_in_trace.doc_in_trace_request_type_status_id = doc_in_trace_request_type_status.doc_in_trace_request_type_status_id
				INNER JOIN doc_in_trace_status on doc_in_trace_request_type_status.doc_in_trace_status_id = doc_in_trace_status.doc_in_trace_status_id
				INNER JOIN doc_in_trace_request_type on doc_in_trace_request_type_status.doc_in_trace_request_type_id = doc_in_trace_request_type.doc_in_trace_request_type_id
                LEFT JOIN USER ON USER.user_id = doc_in_trace.assigned_to_user_id
				WHERE client_application_id = ? ';
        $filter = array();
        $filter[] = $this->db->SqlVal($client_application_id, 'int');
        $params = $this->db->ConvertToParamsArray($filter);
        $res = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $res;
    }
    public function GetTraceAction($approved,$client)
    {
        $sql = 'SELECT `client_application_trace_status_id`, `client_application_trace_status_keyword`,`icon` FROM `client_application_trace_status`';
        if($approved){
            $sql .= " WHERE client_application_trace_status_id = ".self::CLIENT_APPLICATION_STATUS_DOCIN;
        }else {

            if($client==true && $approved==null)
            {

            $sql .= " WHERE client_application_trace_status_id = ".self::CLIENT_APPLICATION_STATUS_REQUEST ." OR client_application_trace_status_id =".self::CLIENT_APPLICATION_STATUS_ARCHIVE;

            }
            else
            {
                $sql .= " WHERE client_application_trace_status_id <= ".self::CLIENT_APPLICATION_STATUS_DOCIN ." AND client_application_trace_status_id >=".self::CLIENT_APPLICATION_STATUS_RESPONSE;
            }
        }


        $sql .=' ORDER BY client_application_trace_status_id';
         $sql;
        $res = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $res;
    }

    public function ClientApplicationsCount($client_id)
    {
        $sql = "SELECT
                SUM( CASE WHEN client_application_trace_status_id = 4 OR client_application_trace_status_id = 3 OR client_application_trace_status_id = 5 THEN 1 ELSE 0 END) AS employee_response_count,
                SUM( CASE WHEN (client_application_trace_status_id = 1 OR client_application_trace_status_id = 2 ) THEN 1 ELSE 0 END) AS pending_count,
                SUM( CASE WHEN client_application_trace_status_id = 6 THEN 1 ELSE 0 END) AS archived_count,
                SUM( CASE WHEN client_application_trace_status_id = 2 THEN 1 ELSE 0 END) AS client_request,
                SUM( CASE WHEN client_application_trace_status_id = 2 AND is_read = 0 THEN 1 ELSE 0 END) AS unread_client_request,
                SUM( CASE WHEN client_application_trace_status_id = 4 OR client_application_trace_status_id = 3 OR client_application_trace_status_id = 5 AND is_read = 0 THEN 1 ELSE 0 END) AS unread_employee_response_count,
                SUM( CASE WHEN (client_application_trace_status_id = 1 OR client_application_trace_status_id = 2 ) AND is_read = 0 THEN 1 ELSE 0 END) AS unread_pending_count,
                SUM( CASE WHEN client_application_trace_status_id = 6 AND is_read = 0 THEN 1 ELSE 0 END) AS unread_archived_count,
                SUM( CASE WHEN client_application_trace_status_id is null  THEN 1 ELSE 0 END) AS new

                FROM client_application
                LEFT JOIN client_application_trace ON client_application.client_application_id = client_application_trace.client_application_id and is_current=1
                WHERE client_id = ?";

        $filter = array();
        $filter[] = $this->db->SqlVal($client_id, 'int');
        $params = $this->db->ConvertToParamsArray($filter);
        $res = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $res;
    }

    public function ClientApplicationsTraceStatus(){
        $sql = "SELECT client_application_trace_status_id, client_application_trace_status_keyword
                FROM client_application_trace_status
                WHERE client_application_trace_status_id != 5
                ORDER BY client_application_trace_status_id";
        $res = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $res;
    }

    public function SearchClientApplication($condition=null,$start=0, $size=0, &$recordsCount=NULL)
    {

        $params=null;
        $sql = "SELECT distinct client_application.client_application_id,client_application.document_number, client_application.document_issue_date, client_application.document_content,client_application.document_subject AS details,client_application_trace_status.icon,T1.NODE_NAME,
                '' AS doc_details
                FROM `client_application`
                LEFT JOIN `client_application_trace` ON `client_application_trace`.`client_application_id`=`client_application`.`client_application_id` AND `client_application_trace`.`is_current`=1
                LEFT JOIN `client_application_trace_status` ON `client_application_trace_status`.`client_application_trace_status_id`=`client_application_trace`.`client_application_trace_status_id`

                LEFT JOIN `doc_in` ON `doc_in`.`doc_in_id`=`client_application`.`doc_in_id`
                LEFT JOIN `memo` ON `memo`.`doc_in_id`=`doc_in`.`doc_in_id`
                LEFT JOIN `doc_in_trace` ON `memo`.`memo_id`=`doc_in_trace`.`memo_id`
                AND doc_in_trace.is_current=1
                LEFT JOIN `doc_in_trace_request_type_status` ON `doc_in_trace_request_type_status`.`doc_in_trace_request_type_status_id`=`doc_in_trace`.`doc_in_trace_request_type_status_id`
                LEFT JOIN `contact` ON `doc_in`.`contact_id`=`contact`.`contact_id`
                LEFT JOIN `DIR_TREE` T1 ON (T1.`NODE_ID`=`client_application_trace`.`from_department_node_id`)
                LEFT JOIN `DIR_TREE` T2 ON (T2.`NODE_ID`=`client_application_trace`.`assigned_to_department_node_id`)
                INNER JOIN `application` ON `application`.`application_id`=`client_application`.`application_id`
                INNER JOIN `client` ON (`client`.`client_id`=`client_application`.`client_id`)


                " ;
            if(isset($condition["doc_in_trace.doc_in_trace_request_type_status_id"])){
            $statusArr = $condition["doc_in_trace.doc_in_trace_request_type_status_id"];
            $sql .= " AND (";
            $statusCondition="";
            for($i=0; $i<count($statusArr);$i++){
                $statusCondition .=" OR doc_in_trace.doc_in_trace_request_type_status_id = ".$statusArr[$i];
            }
            $statusCondition = substr($statusCondition, 4);
            $sql.=$statusCondition;
            $sql.=") ";
            unset($condition["doc_in_trace.doc_in_trace_request_type_status_id"]);
        }
        if(isset($condition["client_application_trace.client_application_trace_status_id"])){
            $AppstatusArr = $condition["client_application_trace.client_application_trace_status_id"];
            $sql .= " AND (";
            $AppstatusCondition="";
            for($i=0; $i<count($AppstatusArr);$i++){
                $AppstatusCondition .=" OR client_application_trace.client_application_trace_status_id = ".$AppstatusArr[$i];
            }
            $AppstatusCondition = substr($AppstatusCondition, 4);
            $sql.=$AppstatusCondition;
            $sql.=") ";
            unset($condition["client_application_trace.client_application_trace_status_id"]);
        }
            if(isset($condition["doc_out.doc_out_number"]) ||isset($condition["doc_out.copy_list"])||
            isset($condition["doc_out.content"])|| isset($condition["doc_out.subject"])
            ||isset($condition["doc_out.signed_by"])||isset($condition["doc_out_issue_to_date"])
            ||isset($condition["doc_out_issue_from_date"])||isset($condition["doc_out_entry_to_date"])
            ||isset($condition["doc_out_publisher_office_node_id"])||isset($condition["doc_out_destination_office_node_id"])
            ||isset($condition["doc_out_temp.doc_cat_id"])||isset($condition["doc_out_temp.folder_number"])
            ){
            $sql .= "LEFT JOIN `doc_out_reference` ON ((`doc_out_reference`.`memo_id`=`memo`.`memo_id`)
            OR (`doc_out_reference`.`document_number`=`doc_in`.`document_number`))
            LEFT JOIN `doc_out` ON `doc_out`.`out_temp_id`=`doc_out_reference`.`doc_out_temp_id`
            LEFT JOIN `doc_out_temp` ON `doc_out_temp`.`doc_out_temp_id`=`doc_out`.`out_temp_id`
            ";

        }

            if(isset($condition["userid"])){
            $sql .= "AND (`client_application`.`client_id`=".$condition["userid"]."
            )";
            unset($condition["userid"]);
        }
                if(isset($condition["application_id"])){
            $sql .= " INNER JOIN `doc_in_application` ON `doc_in_application`.`doc_in_id`=`doc_in`.`doc_in_id`AND (`doc_in_application`.`application_id`=".$condition["application_id"].")";
            unset($condition["application_id"]);
        }

        if(isset($condition["copy_list"])){
            $sql .= "AND (`doc_in`.`copy_list` LIKE '%".$condition["copy_list"]."%')";
            unset($condition["copy_list"]);
        }
        if(isset($condition["doc_out_copy_list"])){
            $sql .= "AND (`doc_out`.`copy_list` LIKE '%".$condition["doc_out_copy_list"]."%')";
            unset($condition["doc_out_copy_list"]);
        }
        if(isset($condition["content"])){
            $sql .= "AND (`memo`.`content` LIKE '%".$condition["content"]."%')";
            unset($condition["content"]);
        }
        if(isset($condition["doc_out_content"])){
            $sql .= "AND (`doc_out_temp`.`content` LIKE '%".$condition["doc_out_content"]."%')";
            unset($condition["doc_out_content"]);
        }
        if(isset($condition["client_application.document_content"])){
            $sql .= "AND (`client_application`.`document_content` LIKE '%".$condition["client_application.document_content"]."%')";
            unset($condition["client_application.document_content"]);
        }
        if(isset($condition["issued_from_date"])||isset($condition["issued_to_date"])){
            if($condition["issued_from_date"]!=null &&$condition["issued_to_date"]==null){
                $sql .= "AND (`memo`.`issued_date` BETWEEN '".$condition["issued_from_date"]."' "."AND"." '".date('Y-m-d H:i:s')."')";
            }
            else if ($condition["issued_from_date"]==null&&$condition["issued_to_date"]!=null){
                $sql .= "AND (`memo`.`issued_date` BETWEEN '1-1-1990' "."AND"." '".$condition["issued_to_date"]."')";

            }
            else{
                $sql .= "AND (`memo`.`issued_date` BETWEEN '".$condition["issued_from_date"]."' "."AND"." '".$condition["issued_to_date"]."')";

            }
            unset($condition["issued_from_date"]);
            unset($condition["issued_to_date"]);
        }
        if(isset($condition["doc_out_issue_from_date"])||isset($condition["doc_out_issue_to_date"])){
                if($condition["doc_out_issue_from_date"]!=null&&$condition["doc_out_issue_to_date"]==null){
                    $sql .= "AND (`doc_out`.`issue_date` BETWEEN '".$condition["doc_out_issue_from_date"]."' "."AND"." '".date('Y-m-d H:i:s')."')";
                }
                else if($condition["doc_out_issue_from_date"]==null&&$condition["doc_out_issue_to_date"]!=null){
                    $sql .= "AND (`doc_out`.`issue_date` BETWEEN '1-1-1990' "."AND"." '".$condition["doc_out_issue_to_date"]."')";
                }
                else{
                    $sql .= "AND (`doc_out`.`issue_date` BETWEEN '".$condition["doc_out_issue_from_date"]."' "."AND"." '".$condition["doc_out_issue_to_date"]."')";
                }
            unset($condition["doc_out_issue_from_date"]);
            unset($condition["doc_out_issue_to_date"]);
        }

        if(isset($condition["stamp_from_date"])||isset($condition["stamp_to_date"])){
            if($condition["stamp_from_date"]!=null&&$condition["stamp_to_date"]==null){
                $sql .= "AND (`memo`.`memo_timestamp` BETWEEN '".$condition["stamp_from_date"]."' "."AND"." '".date('Y-m-d H:i:s')."')";

            }
            else if($condition["stamp_from_date"]==null&&$condition["stamp_to_date"]!=null){
                $sql .= "AND (`memo`.`memo_timestamp` BETWEEN '1-1-1990' "."AND"." '".$condition["stamp_to_date"]."')";

            }
            else{
                $sql .= "AND (`memo`.`memo_timestamp` BETWEEN '".$condition["stamp_from_date"]."' "."AND"." '".$condition["stamp_to_date"]."')";
            }
            unset($condition["stamp_from_date"]);
            unset($condition["stamp_to_date"]);

        }
        if(isset($condition["recieve_from_date"])||isset($condition["recieve_to_date"])){
            if($condition["recieve_from_date"]!=null&&$condition["recieve_to_date"]==null){

                $sql .= "AND (`memo`.`recieve_date` BETWEEN '".$condition["recieve_from_date"]."' "."AND"." '".date('Y-m-d H:i:s')."')";
            }
            else if($condition["recieve_from_date"]==null&&$condition["recieve_to_date"]!=null){

                $sql .= "AND (`memo`.`recieve_date` BETWEEN '1-1-1990' "."AND"." '".$condition["recieve_to_date"]."')";
            }
            else{
                $sql .= "AND (`memo`.`recieve_date` BETWEEN '".$condition["recieve_from_date"]."' "."AND"." '".$condition["recieve_to_date"]."')";
            }

            unset($condition["recieve_from_date"]);
            unset($condition["recieve_to_date"]);
        }
        /* if(isset($condition["doc_out_entry_from_date"])||isset($condition["doc_out_entry_to_date"])){
            if($condition["doc_out_entry_from_date"]!=null&&$condition["doc_out_entry_to_date"]==null){
                $sql .= "AND (`doc_out_temp`.`entry_date` BETWEEN '".$condition["doc_out_entry_from_date"]."' "."AND"." '".date('Y-m-d H:i:s')."')";
            }
            else if($condition["doc_out_entry_from_date"]==null&&$condition["doc_out_entry_to_date"]!=null){
                $sql .= "AND (`doc_out_temp`.`entry_date` BETWEEN '1-1-1990' "."AND"." '".$condition["doc_out_entry_to_date"]."')";
            }
            else{
                $sql .= "AND (`doc_out_temp`.`entry_date` BETWEEN '".$condition["doc_out_entry_from_date"]."' "."AND"." '".$condition["doc_out_entry_to_date"]."')";
            }

            unset($condition["doc_out_entry_from_date"]);
            unset($condition["doc_out_entry_to_date"]);
        }*/
        if(isset($condition["publisher_office_node_id"])){
            $sql .= "
            AND (T1.`NODE_PATH` LIKE '%".$condition["publisher_office_node_id"].".%' OR T1.`NODE_ID` = ".$condition["publisher_office_node_id"].")";
            unset($condition["publisher_office_node_id"]);

        }
        if(isset($condition["destination_department_node_id"])){
            $sql .= "
            AND (T1.`NODE_PATH` LIKE '%".$condition["destination_department_node_id"].".%' OR T1.`NODE_ID` = ".$condition["destination_department_node_id"].")";
            unset($condition["destination_department_node_id"]);

        }
        if(isset($condition["doc_out_publisher_office_node_id"])){
            $sql .= "LEFT JOIN `DIR_TREE` T2 ON T2.`NODE_ID`= doc_out_temp.publisher_office_node_id
            AND (T2.`NODE_PATH` LIKE '%".$condition["doc_out_publisher_office_node_id"].".%' OR T2.`NODE_ID` = ".$condition["doc_out_publisher_office_node_id"].")";
            unset($condition["doc_out_publisher_office_node_id"]);

        }
        if(isset($condition["doc_out_destination_office_node_id"])){
            $sql .= "LEFT JOIN `DIR_TREE` T2 ON T2.`NODE_ID`= doc_out_temp.destination_office_node_id
            AND (T2.`NODE_PATH` LIKE '%".$condition["doc_out_destination_office_node_id"].".%' OR T2.`NODE_ID` = ".$condition["doc_out_destination_office_node_id"].")";
            unset($condition["doc_out_destination_office_node_id"]);

        }
        if(isset($condition["contact_tel"])){
            $sql .= "
            AND (`contact`.`contact_tel` =".$condition["contact_tel"]." OR `contact`.`contact_tel2` = ".$condition["contact_tel"].")";
            unset($condition["contact_tel"]);

        }
        if(isset($condition["document_issue_from_date"])||isset($condition["document_issue_to_date"])){
            if($condition["document_issue_from_date"]!=null&&$condition["document_issue_to_date"]==null){
                $sql .= "AND (`client_application`.`document_issue_date` BETWEEN '".$condition["document_issue_from_date"]."' "."AND"." '".date('Y-m-d H:i:s')."')";
            }
            else if($condition["document_issue_from_date"]==null&&$condition["document_issue_to_date"]!=null){
                $sql .= "AND (`client_application`.`document_issue_date` BETWEEN '1-1-1990' "."AND"." '".$condition["document_issue_to_date"]."')";
            }
            else{
                $sql .= "AND (`client_application`.`document_issue_date` BETWEEN '".$condition["document_issue_from_date"]."' "."AND"." '".$condition["document_issue_to_date"]."')";
            }

            unset($condition["document_issue_from_date"]);
            unset($condition["document_issue_to_date"]);
        }
        if(isset($condition["application_from_date"])||isset($condition["application_to_date"])){
            if($condition["application_from_date"]!=null&&$condition["application_to_date"]==null){
                $sql .= "AND (`client_application`.`application_date` BETWEEN '".$condition["application_from_date"]."' "."AND"." '".date('Y-m-d H:i:s')."')";

            }
            else if($condition["application_from_date"]==null&&$condition["application_to_date"]!=null){
                $sql .= "AND (`client_application`.`application_date` BETWEEN '1-1-1990' "."AND"." '".$condition["application_to_date"]."')";

            }
            else{
                $sql .= "AND (`client_application`.`application_date` BETWEEN '".$condition["application_from_date"]."' "."AND"." '".$condition["application_to_date"]."')";
            }
            unset($condition["application_from_date"]);
            unset($condition["application_to_date"]);

        }

 //print $sql;
        if($condition!=null){
            $params = $this->db->ConvertToParamsArray(NULL,$condition);
        }

        $allClientApplication = $this->db->SelectData($sql,$params,null,$start, $size,$recordsCount,0);

        if($allClientApplication)
        {
            return $allClientApplication;
        }
        else
        {
          return false;
        }
    }

    public function UpdateAssignTrace($trace_id, $user_id, $dir_id)
    {
        $data = array();
        $data['assigned_to_user_id'] = $this -> db -> SqlVal($user_id, "int");
        $data['assigned_to_department_node_id'] = $this -> db -> SqlVal($dir_id, "int");

        $condition = array();
        $condition['client_application_trace_id'] = $this -> db -> SqlVal($trace_id, "int");
        $condition['is_current'] = 1;

        if (!$this -> db -> Update("client_application_trace", $data, $condition))
        {
            $this -> State = $this -> db -> state;
            $this -> Message = "trace_update_assign_failed";
            return false;
        }
        else
        {
            $this -> State = self::SUCCESS;
            $this -> Message = "trace_update_assign_success";
            return true;
        }
    }

    public function GetApplicationTraceIcon($client_application_trace_status_id=null, $start=0, $size=0, &$recordsCount=NULL)
    {
        $sql = 'SELECT `client_application_trace_status_id`,`icon` ,`client_application_trace_status_keyword` FROM `client_application_trace_status` ';
        $res = $this->db->SelectData($sql, NULL, NULL, $start, $size, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $res;
    }

    public function SearchClientApplicationSubjects($document_subject)
    {
        $sql = "SELECT DISTINCT document_subject FROM `client_application` WHERE document_subject LIKE '%".$this -> db -> SqlVal($document_subject, "mytext")."%' ORDER BY document_subject";
        $res = $this->db->SelectData($sql);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $res;
    }
}
