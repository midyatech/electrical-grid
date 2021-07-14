<?php
require_once (realpath(dirname(__FILE__)) . '/MysqliDB.php');

/**
 * @access public
 */
class AssemblyTeam
{
    //state constants
    const ERROR = 0;
    const WARNING = 1;
    const SUCCESS = 2;

    public $Message;
    public $State;
    public $db;

    public function __construct($access_dir_path = null)
    {
        $this->db = new MysqliDB();
    }

    public function __destruct()
    {
        $this->db = null;
    }

    public function AddAssemblyTeam($team_data)
    {
        $data = array();
        $data["team_name"]= $this->db->SqlVal($team_data["team_name"], "mytext");
        $data["user_id"]= $this->db->SqlVal($team_data["user_id"], "int");
        $data["table_number"]= $this->db->SqlVal($team_data["table_number"], "int");
        $data["position_number"]= $this->db->SqlVal($team_data["position_number"], "int");
        $data["status_id"] = 1;

        $team_id = $this->db->Insert("assembly_team", $data, true);

        if ($team_id) {
            $this->State = self::SUCCESS;
            $this->Message = "assembly_team_insert_success";
            //return false;
            return $team_id;
        } else {
            $this->State = self::ERROR;
            $this->Message = "assembly_team_insert_failed";
            return false;
        }
    }

    public function AddTeamConfig($team_config_data)
    {
        $data = array();
        $data["team_id"]= $this->db->SqlVal($team_config_data["team_id"], "mytext");
        $data["enclosure_config_id"]= $this->db->SqlVal($team_config_data["enclosure_config_id"], "int");
        $data["priority"] = 1;

        $team_id = $this->db->Insert("team_default_config", $data, true);

        if ($team_id) {
            $this->State = self::SUCCESS;
            $this->Message = "team_default_config_insert_success";
            return $team_id;
        } else {
            $this->State = self::ERROR;
            $this->Message = "team_default_config_insert_failed";
            return false;
        }
    }

    public function EditTeamConfig($team_config_data)
    {
        $data = array();
        $data['priority']=$this->db->SqlVal($team_config_data["priority"], "mytext");
        $condition = array();
        $condition['team_default_config_id']= $this->db->SqlVal($team_config_data["team_default_config_id"], "int");
        $result = $this->db->Update("team_default_config", $data, $condition);

        if ($result) {
            $this->State = self::SUCCESS;
            $this->Message = "team_default_config_update_success";
            return true;
        } else {
            $this->State = self::ERROR;
            $this->Message = "team_default_config_update_failed";
            return false;
        }
    }

    public function EditAssemblyOrderTransformerpriority($transformer_id)
    {
        $error = false;
        $this->db->BeginTransaction();

        $data = array();
        $data['is_priority'] = 0 ;
        $condition = array();
        $result1 = $this->db->Update("assembly_order_transformers", $data, $condition);
        if(! $result1){
            $error = true;
        }

        $data = array();
        $data['is_priority'] = 1 ;
        $condition = array();
        $condition['transformer_id']= $this->db->SqlVal($transformer_id, "int");
        $result2 = $this->db->Update("assembly_order_transformers", $data, $condition);
        if(! $result2){
            $error = true;
        }

        if ($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "assembly_order_transformers_update_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "assembly_order_transformers_update_success";
            return true;
        }
    }

    public function DeleteTeamConfig($team_config_data)
    {
        $condition = array();
        $condition['team_default_config_id']= $this->db->SqlVal($team_config_data["team_default_config_id"], "int");
        $result = $this->db->Delete("team_default_config", $condition);

        if ($result) {
            $this->State = self::SUCCESS;
            $this->Message = "team_default_config_delete_success";
            return true;
        } else {
            $this->State = self::ERROR;
            $this->Message = "team_default_config_delete_failed";
            return false;
        }
    }

    public function GetUsersArr($filter=NULL, $order=NULL)
	{
		$filter["USER_STATUS_ID"] = 1;
		$select = 'SELECT USER_ID, NAME FROM USER';

        $params = $this->db->ConvertToParamsArray(null, $filter);
        $recordsCount = 0;
		$allUsers = $this->db->SelectData($select, $params, $order, 0, 0, $recordsCount, 1);

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

    public function GetTableTeams($table_number)
    {
        $sql = "SELECT t.team_id, t.team_name, t.user_id, t.status_id, t.table_number, t.position_number, `NAME`
                FROM assembly_team t
                INNER JOIN `USER` u on u.USER_ID = t.user_id
                WHERE table_number = ?";
        $params = $this->db->ConvertToParamsArray([$table_number]);
        $result = $this->db->SelectData($sql, $params);
        if ($result == null) {
            return false;
        } else {
            return $result;
        }
    }

    public function GetTeamEnclosureConfig($team_id)
    {
        $sql = "SELECT team_default_config_id, c.enclosure_config_id, c.team_id, enclosure_type, configuration_name, priority
                FROM team_default_config c
                INNER JOIN enclosure_config ec on ec.enclosure_config_id = c.enclosure_config_id
                INNER JOIN enclosure_type et on et.enclosure_type_id = ec.enclosure_type_id
                WHERE c.team_id = ?
                ORDER BY priority";
        $params = $this->db->ConvertToParamsArray([$team_id]);
        $result = $this->db->SelectData($sql, $params);
        if ($result == null) {
            return false;
        } else {
            return $result;
        }
    }

    public function GetTeamEnclosureConfigByID($team_default_config_id)
    {
        $sql = "SELECT team_default_config_id, c.enclosure_config_id, c.team_id, enclosure_type, configuration_name, priority
                FROM team_default_config c
                INNER JOIN enclosure_config ec on ec.enclosure_config_id = c.enclosure_config_id
                INNER JOIN enclosure_type et on et.enclosure_type_id = ec.enclosure_type_id
                WHERE team_default_config_id = ?
                ORDER BY priority";
        $params = $this->db->ConvertToParamsArray([$team_default_config_id]);
        $result = $this->db->SelectData($sql, $params);
        if ($result == null) {
            return false;
        } else {
            return $result;
        }
    }

    public function getTeamsArr()
    {
        $sql = "SELECT team_id, team_name, user_id, status_id, table_number, position_number
                FROM assembly_team
                ORDER BY team_name";
        $recordsCount = 0;
        $result = $this->db->SelectData($sql, NULL, NULL, 0, 0, $recordsCount, 1);
        //$result = $this->db->SelectData($sql, NULL, NULL, $start, $size, $recordsCount, 1);
        if ($result == null) {
            return false;
        } else {
            return $result;
        }
    }

    public function getEnclosureConfig($WithoutInTeamID = NULL)
    {
        $sql = "SELECT DISTINCT enclosure_config_id, CONCAT(enclosure_type,' [',configuration_name,']'), configuration_name, enclosure_config.enclosure_type_id,
                        meter_1, meter_2, meter_3, meter_4, meter_5, meter_6, even_remainder_factor, odd_remainder_factor
                        FROM enclosure_config
                        INNER JOIN enclosure_type ON enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id";
        if( $WithoutInTeamID > 0) {
            $WithoutInTeamID = $this->db->SqlVal($WithoutInTeamID, "int");
            $sql .= " WHERE enclosure_config_id NOT IN ( SELECT enclosure_config_id FROM team_default_config WHERE team_id = $WithoutInTeamID )";
        }
        $sql .= " ORDER BY enclosure_config_id";
        $recordsCount = 0;
        $result = $this->db->SelectData($sql, NULL, NULL, 0, 0, $recordsCount, 1);
        if ($result == null) {
            return false;
        } else {
            return $result;
        }
    }

    public function GetActiveAssemblyOrderTransformerArr($is_priority = false)
    {
        $sql = "SELECT transformer_id, transformer_number
                FROM assembly_order_transformers
                INNER JOIN service_point ON assembly_order_transformers.transformer_id = service_point.point_id
                INNER JOIN assembly_order ON assembly_order_transformers.assembly_order_id = assembly_order.assembly_order_id
                WHERE status_id = 1";
        if($is_priority){
            $sql .= " AND is_priority = 1";
        }
        $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        return $result;
    }
}
?>