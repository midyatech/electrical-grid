<?php
require_once (realpath(dirname(__FILE__)) . '/MysqliDB.php');

/**
 * @access public
 * @package EMPLOYEE
 */
class Enclosure
{
    //state constants
    const ERROR = 0;
    const WARNING = 1;
    const SUCCESS = 2;


    public $Message;
    public $State;
    public $db;
    protected $dir_path;

    public function __construct($access_dir_path = null)
    {
        $this -> db = new MysqliDB();
        $this->dir_path = $access_dir_path;
    }

    public function __destruct()
    {
        $this -> db = null;
    }

    public function ImportData($table, $number, $data)
    {
        if ($table == "meter") {
            $table_name = "meter";
            $serial_column = "meter_sn";
        } else if ($table == "gateway") {
            $table_name = "gateway_sn";
            $serial_column = "gateway_sn";
        } else {
            $table_name = "enclosure_sn";
            $serial_column = "enclosure_sn";
        }
        $timestamp = date('Y-m-d H:i:s');

        for ($i=0; $i<count($data); $i++) {
            if ($table == "meter") {
                if ($data[$i]["Model"] == "Mk32H") {
                    //single phase
                    $insert_data["meter_type_id"] = 1;
                } else if ($data[$i]["Model"] == "Mk10M") {
                    //three phase
                    $insert_data["meter_type_id"] = 2;
                } else if ($data[$i]["Model"] == "Mk10E") {
                    //ct
                    $insert_data["meter_type_id"] = 3;
                }
            }
            $insert_data[$serial_column] = $this->db->SqlVal($data[$i]["Serial No."], "mytext");
            $insert_data["imoprt_number"] = $this->db->SqlVal($number, "int");
            $insert_data["timestamp"] = $timestamp;
            $result = $this->db->Insert($table_name, $insert_data);
        }

    }

    public function GetTransformerRemainingEnclosureCount($condition)
    {
        $sql = "SELECT o.enclosure_count - IFNULL(e.assembled_enclosures, 0) AS difference
            FROM assembly_order_configuration o
            LEFT JOIN (
                SELECT COUNT(*) as assembled_enclosures, enclosure_configuration_id, assembly_order_id, transformer_id
                FROM enclosure GROUP BY enclosure_configuration_id, assembly_order_id, transformer_id
            ) e ON e.enclosure_configuration_id = enclosure_config_id
                AND e.assembly_order_id = o.assembly_order_id
                AND e.transformer_id = o.transformer_id
            WHERE o.assembly_order_id = ?
            AND ifnull(o.transformer_id, 0) = ifnull(?, 0)
            AND o.enclosure_config_id = ?";

        $params = $this->db->ConvertToParamsArray($condition);
        $result = $this->db->SelectValue($sql, $params);
        return $result;
    }

    public function GetTransformerReservedEnclosureCount($condition)
    {
        $sql = "SELECT enclosure_id, enclosure_configuration_id, assembly_order_id, transformer_id
                FROM enclosure
                WHERE assembly_order_id = ?
                AND transformer_id = ?
                AND enclosure_configuration_id = ?
                AND status_id = 0";
        $params = $this->db->ConvertToParamsArray($condition);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }


    public function Add($enclosure_data)
    {


        $assembly_order_id = $this->db->SqlVal($enclosure_data["assembly_order_id"], "int");
        $transformer_id = $this->db->SqlVal($enclosure_data["transformer_id"], "int");
        $enclosure_configuration_id = $this->db->SqlVal($enclosure_data["enclosure_configuration_id"], "int");

        //get order
        $sql1 = "SELECT is_extra_stock FROM assembly_order WHERE assembly_order_id = ?";
        $params = $this->db->ConvertToParamsArray([$assembly_order_id]);
        $is_extra_stock = $this->db->SelectValue($sql1, $params);

        $count = $this->GetTransformerRemainingEnclosureCount([$assembly_order_id, $transformer_id, $enclosure_configuration_id]);
        if ($count > 0 || $is_extra_stock ==1) {
            $data = array();
            //$data['enclosure_id']= $this->db->SqlVal($enclosure_data["enclosure_id"], "int");
            //$data['gateway_id']= $this->db->SqlVal($enclosure_data["gateway_id"], "int");
            //$data['enclosure_type_id']= $this->db->SqlVal($enclosure_data["enclosure_type_id"], "int");
            //$data['phase']= $this->db->SqlVal($enclosure_data["phase"], "int");
            $data['assembly_order_id']= $assembly_order_id;
            $data['transformer_id']= $transformer_id;
            $data['status_id']= $this->db->SqlVal($enclosure_data["status_id"], "int");
            $data['user_id']= $this->db->SqlVal($enclosure_data["user_id"], "int");
            $data['timestamp']= date("Y-m-d H:i:s");
            $data['enclosure_configuration_id']= $enclosure_configuration_id;

            $enclosure_id = $this->db->Insert("enclosure", $data, true);
            if ($enclosure_id) {
                $this->State = self::ERROR;
                $this->Message = "enclosure_insert_success";
                return $enclosure_id;
            } else {
                $this->State = self::ERROR;
                $this->Message = "enclosure_insert_failed";
                return false;
            }
        } else {
            $this->State = self::ERROR;
            $this->Message = "configuration_assembly_completed ". $sql1. " ".$assembly_order_id;
            return false;
        }
    }

    public function AddEnclosureSN($enclosure_data)
    {

        $error = false;
        $this->Message = "";
        $this->db->BeginTransaction();

        $data['enclosure_sn']= $this->db->SqlVal($enclosure_data["enclosure_sn"], "mytext");
        $data['status_id']= $this->db->SqlVal($enclosure_data["status_id"], "int");
        $data['timestamp']= date("Y-m-d H:i:s");

        $condition = array();
        $condition['enclosure_id'] = $this -> db -> SqlVal($enclosure_data['enclosure_id'], "int");
        $result = $this->db->Update("enclosure", $data, $condition);

        if (!$result) {
            $error = true;
        } else {

            //add trace
            // $data = array();
            // $data["user_id"] = $enclosure_data['user_id'];
            // $data["timestamp"] = date("Y-m-d H:i:s");
            // $data["enclosure_id"] = $enclosure_data['enclosure_id'];
            // $data["enclosure_trace_status_id"] = 1; // assemble enclosure
            //$data["description"] = order_id;
            //$this->db->Insert('enclosure_trace', $data);

            $r = $this->AddEnclosureTrace($enclosure_data['user_id'], $enclosure_data['enclosure_id'], 1);

            if (!$r) {
                $error = true;
            }
        }

        if ($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "enclosure_update_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "enclosure_update_success";
            return true;
        }
    }

    public function GetEnclosureComponents($enclosure_id)
    {
        $sql = "SELECT meter_id, 'm' as type FROM enclosure_meters WHERE enclosure_id = ?
                UNION ALL
                SELECT concat('gateway_',gateway_id), 'g' as type FROM enclosure WHERE enclosure_id = ?";
        $params = $this->db->ConvertToParamsArray([$enclosure_id, $enclosure_id]);
        return $this->db->SelectData($sql, $params);
    }






    private function GetRemovedMetersFromEnclosure($components, $meters)
    {
        // echo "<BR><BR>";
        // print_r($components);
        // print_r($meters);
        $deleted_meters = array();
        for ($i=0; $i<count($components); $i++) {
            if ($components[$i]["meter_id"] != null) {
                $component_exists_in_meters = 0;
                foreach ($meters as $meter) {
                    if ($meter["meter_id"] != "") {
                        if ($meter["meter_id"] == $components[$i]["meter_id"]) {
                            $component_exists_in_meters++;
                        }
                    }
                }
                if ($component_exists_in_meters == 0) {
                    $deleted_meters[] = $components[$i]["meter_id"];
                }
            }
        }
        return $deleted_meters;
    }

    private function GetAddedMetrsFromEnclosure($components, $meters)
    {
        // echo "<BR><BR>";
        // print_r($components);
        // print_r($meters);
        $added_meters = array();
        for ($i=0; $i<count($meters); $i++) {
            if ($meters[$i]["meter_id"] != "") {
                $meter_exists_in_components = 0;
                foreach ($components as $comp) {
                    if ($comp["meter_id"] != null) {
                        if ($comp["meter_id"] == $meters[$i]["meter_id"]) {
                            $meter_exists_in_components++;
                        }
                    }
                }
                if ($meter_exists_in_components == 0) {
                    $added_meters[] = $meters[$i]["meter_id"];
                }
            }
        }
        return $added_meters;
    }


    private function UpdateEnclosureConfig($enclosure_id, $enclosure_config_id)
    {
        $data = array();
        $data['enclosure_configuration_id'] = $this -> db -> SqlVal($enclosure_config_id, "int");
        $data['gateway_id'] = null;
        $condition = array();
        $condition['enclosure_id'] = $this->db->SqlVal($enclosure_id, "int");
        return $this->db->Update('enclosure', $data, $condition);
    }

    private function AddEnclosureTrace($user_id, $enclosure_id, $enclosure_trace_status_id, $change_reason=null, $description=null)
    {
        $data = array();
        $data["user_id"] = $user_id;
        $data["timestamp"] = date("Y-m-d H:i:s");
        $data["enclosure_id"] = $enclosure_id;
        $data["trace_change_reason_id"] = $change_reason;
        $data["enclosure_trace_status_id"] = $enclosure_trace_status_id;
        $data["description"] = $description;

        return $this->db->Insert('enclosure_trace', $data);
    }

    private function AddMeterTrace($meter_id, $data)
    {
        if (strpos($meter_id, 'gateway_') === 0) {
            $table = "gateway_trace";
            $data["gateway_id"] = str_replace("gateway_", "", $meter_id);
            $data["gateway_trace_status_id"] = $data["meter_trace_status_id"];
            unset($data["meter_trace_status_id"]);
        } else {
            $table = "meter_trace";
            $data["meter_id"] = $meter_id;
        }
        return $this->db->Insert($table, $data);
    }



    public function AddEnclosureMeters($enclosure_meters, $meters, $user_id, $after_intallation = false, $change_reason=null, $enclosure_config_id = null)
    {

        $error = false;
        $this->Message = "";
        $this->db->BeginTransaction();


        //0- set variables
        $enclosure_id = $this->db->SqlVal($enclosure_meters["enclosure_id"], "int");
        if(isset($enclosure_meters["gateway_id"]) && $enclosure_meters["gateway_id"] != ""){
            $gateway_id = $this->db->SqlVal($enclosure_meters["gateway_id"], "int");
        } else {
            $gateway_id = null;
        }
        $enclosure_status = $this->db->SqlVal($enclosure_meters["enclosure_status"], "int");
        $params = $this->db->ConvertToParamsArray([$enclosure_id]);
        $enclosure_sn = $this->db->SelectValue("SELECT enclosure_sn FROM enclosure WHERE enclosure_id = ?", $params);

        //get previous meters
        $components = $this->GetEnclosureComponents($enclosure_id);


        //1- Update Enclosure Status
        $enclosure_data = array();
        $enclosure_data['status_id']= $enclosure_status;
        $enclosure_data['gateway_id']= $gateway_id;
        $condition = array();
        $condition['enclosure_id'] = $enclosure_id;
        if (!$this->db->Update("enclosure", $enclosure_data, $condition)) {
            $error=true;
        }


        //default add meter traced is assemble
        $addMeterTraceStatusId = 1;

        //if coming from installation, add enclosure trace
        if ($after_intallation) {
            //if coming from installation then  meter trace is add
            $addMeterTraceStatusId = 3;

            //configuration changed, clear meters
            if ($enclosure_config_id != null) {

                $result = $this->UpdateEnclosureConfig($enclosure_id, $enclosure_config_id);
                if (!$result) {
                    $error = true;
                }

                $enclosure_trace_status_id = 4; //components changed
            } else {
                $enclosure_trace_status_id = 3; //components changed
            }

            //3- add enclosure trace
            $r = $this->AddEnclosureTrace($user_id, $enclosure_id, $enclosure_trace_status_id, $change_reason, null);
            if (!$r) {
                $error = true;
            }
        }


        //meter trace data
        $data = array();
        $data["user_id"] = $user_id;
        $data["timestamp"] = date("Y-m-d H:i:s");
        $data["enclosure_id"] = $enclosure_id;
        $data["description"] = $enclosure_sn; // enclosure sn


        //4- trace for removed meters
        $deleted_meters = $this->GetRemovedMetersFromEnclosure($components, array_merge($meters, array(["meter_id"=>"gateway_".$gateway_id])));
        if (count($deleted_meters) > 0) {
            $data["meter_trace_status_id"] = 2; // remove metere
            foreach($deleted_meters as $meter_id) {
                $r = $this->AddMeterTrace($meter_id, $data);
                if (!$r) {
                    $error = true;
                    break;
                }
            }
        }


        //5- trace for added meters
        $added_meters = $this->GetAddedMetrsFromEnclosure($components, array_merge($meters, array(["meter_id"=>"gateway_".$gateway_id])));
        if (count($added_meters) > 0) {
            $data["meter_trace_status_id"] = $addMeterTraceStatusId; // add metere
            foreach($added_meters as $meter_id) {
                $r = $this->AddMeterTrace($meter_id, $data);
                if (!$r) {
                    $error = true;
                    break;
                }
            }
        }

        //whenever we update an enclosure meters, we delete old meters and add new ones
        $delete_condition = array();
        $delete_condition['enclosure_id'] = $this -> db -> SqlVal($enclosure_id, "int");
        if (!$this -> db -> Delete("enclosure_meters", $delete_condition)) {
            $error=true;
        }


        // Insert Enclosure Meters
        if($meters != null && count($meters) > 0){
            for($i=0; $i<count($meters); $i++){
                if ($meters[$i] != null) {
                    $meter_id = $this->db->SqlVal($meters[$i]["meter_id"], "mytext");
                    if(!$error){
                        $enclosure_meters_data = array();
                        $enclosure_meters_data["enclosure_id"] = $enclosure_id;
                        $enclosure_meters_data["meter_id"] = $meter_id;
                        $enclosure_meters_data["meter_sequence"] = $this->db->SqlVal($meters[$i]["sequence"], "int");
                        $result = $this->db->Insert("enclosure_meters", $enclosure_meters_data);
                        if (!$result) {
                            $error = true;
                            $this->Message = "enclosure_meters_insert_failed";
                        }
                    }
                }
            }
        }


        if ($error) {
            //print "A";
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "enclosure_insert_failed";
            return false;
        } else {
            //print "B";
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "enclosure_insert_success";
            return $enclosure_id;
        }
    }


    public function GetLastEnclosureByUser($user_id)
    {
        $sql = "SELECT enclosure_id FROM enclosure WHERE user_id=? order by timestamp DESC LIMIT 1";
        $condition = array($user_id);
        $params = $this->db->ConvertToParamsArray($condition);
        $result = $this->db->SelectValue($sql, $params);
        return $result;
    }


    public function GetEnclosure($enclosure_id){
        $sql = "SELECT enclosure.enclosure_id, enclosure.enclosure_sn, gateway_id, single_phase, three_phase
                FROM enclosure
                LEFT JOIN (
                    SELECT enclosure_meters.enclosure_id,
                    SUM(CASE WHEN phase = 1 THEN 1 ELSE 0 END) AS single_phase,
                    SUM(CASE WHEN phase = 3 THEN 1 ELSE 0 END) AS three_phase
                    FROM enclosure_meters
                    INNER JOIN meter ON meter.meter_id = enclosure_meters.meter_id
                    GROUP BY enclosure_meters.enclosure_id
                ) meters
                ON meters.enclosure_id = enclosure.enclosure_id
                WHERE enclosure.enclosure_id = ?";
        $result = $this->db->SelectData($sql, $this->db->GenerateParam($enclosure_id));
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetEnclosureDetails($enclosure_id, $enclosure_sn=null)
    {
        $params = null;
        $sql = "SELECT enclosure.enclosure_id, enclosure.enclosure_sn, enclosure.gateway_id, enclosure_meters.meter_id, meter_sequence,
                enclosure_type.meter_type_id, meter_type, enclosure_type, enclosure.assembly_order_id, feeder, meter,
                transformer_number, IFNULL(assembly_order_code, assembly_order.assembly_order_id) as assembly_order, meter_count,
                CASE WHEN installed_point_enclosure.enclosure_id IS NULL THEN 0 ELSE 1 END AS enclosure_installed, gateway_sn, meter_sn, meter.Serial_No AS 'meter_serial_number'
                FROM enclosure
                INNER JOIN assembly_order ON assembly_order.assembly_order_id = enclosure.assembly_order_id
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                LEFT JOIN enclosure_meters on enclosure_meters.enclosure_id=enclosure.enclosure_id
                LEFT JOIN meter ON meter.meter_id = enclosure_meters.meter_id
                LEFT JOIN meter_type ON meter.meter_type_id = meter_type.meter_type_id
                LEFT JOIN installed_point_enclosure ON installed_point_enclosure.enclosure_id = enclosure.enclosure_id
                INNER JOIN enclosure_config ON enclosure.enclosure_configuration_id = enclosure_config.enclosure_config_id
                INNER JOIN enclosure_type ON enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id
                LEFT JOIN service_point ON service_point.point_id = enclosure.transformer_id
                LEFT JOIN feeder ON service_point.feeder_id = feeder.feeder_id
                LEFT JOIN (
                    SELECT enclosure_id, COUNT(enclosure_meters.meter_id) meter_count
                    FROM enclosure_meters
                    LEFT JOIN meter ON meter.meter_id = enclosure_meters.meter_id
                    GROUP BY enclosure_id
                ) enclosure_meters_count on enclosure_meters_count.enclosure_id=enclosure.enclosure_id";
        if ($enclosure_sn != null) {
            $sql .= " WHERE TRIM(enclosure.enclosure_sn) = ?";
            $param = trim($enclosure_sn);
            $params = $this->db->GenerateParam($param);
        } else if ($enclosure_id != null) {
            $sql .= " WHERE enclosure.enclosure_id = ?";
            $param = $enclosure_id;
            $params = $this->db->GenerateParam($param);
        }
        $sql .= " ORDER BY meter_sequence";
        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetEnclosures($condition= null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        if( isset($condition["simcard_status_id"]) && $condition["simcard_status_id"] != "" ){
            $simcard_status_id = $this->db->SqlVal($condition["simcard_status_id"], "text");
        }
        $params =null;
        $sql = "SELECT distinct enclosure.enclosure_id, enclosure.enclosure_sn, enclosure.gateway_id, enclosure_type, enclosure.assembly_order_id,
                configuration_name, gateway_sn, transformer_number, meter_count, `NAME`,
                CASE WHEN installed_point_enclosure.enclosure_id IS NULL THEN 0 ELSE 1 END AS enclosure_installed, enclosure.timestamp, assembly_order_code,
                CONCAT(service_point.transformer_code, service_point.transformer_gov_number) AS transformer_generated_number
                FROM enclosure
                LEFT JOIN installed_point_enclosure ON installed_point_enclosure.enclosure_id = enclosure.enclosure_id
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                INNER JOIN enclosure_config ON enclosure.enclosure_configuration_id = enclosure_config.enclosure_config_id
                LEFT JOIN enclosure_type ON enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id
                LEFT JOIN service_point ON enclosure.transformer_id = service_point.point_id
                INNER JOIN assembly_order ON enclosure.assembly_order_id = assembly_order.assembly_order_id
                INNER JOIN `USER` ON `USER`.USER_ID = enclosure.user_id
                LEFT JOIN
                (
                    SELECT enclosure_id, COUNT(*) meter_count
                    FROM enclosure_meters
                    GROUP BY enclosure_id
                ) a ON enclosure.enclosure_id = a.enclosure_id
                LEFT JOIN enclosure_meters ON enclosure.enclosure_id = enclosure_meters.enclosure_id
                LEFT JOIN meter ON enclosure_meters.meter_id = meter.meter_id
                ";
        /*
        $sql = "SELECT DISTINCT enclosure.enclosure_id, enclosure.enclosure_sn, gateway_id, single_phase, three_phase, enclosure_type
                FROM enclosure ";

        if(isset($condition["meter_id"])) {
            $sql .= "INNER";
        } else {
            $sql .= "LEFT OUTER";
        }


        $sql .= " JOIN (
                    SELECT enclosure_meters.enclosure_id,
                    SUM(CASE WHEN meter_type.phase = 1 THEN 1 ELSE 0 END) AS single_phase,
                    SUM(CASE WHEN meter_type.phase = 3 THEN 1 ELSE 0 END) AS three_phase
                    FROM enclosure_meters
                    INNER JOIN meter ON meter.meter_id = enclosure_meters.meter_id
                    INNER JOIN meter_type on meter.meter_type_id = meter_type.meter_type_id
                    GROUP BY enclosure_meters.enclosure_id
            ) meters ON meters.enclosure_id = enclosure.enclosure_id";

        if(isset($condition["meter_id"])) {
            $sql .= " INNER JOIN (
                        SELECT enclosure.enclosure_id
                        FROM enclosure
                        INNER JOIN enclosure_meters on enclosure.enclosure_id = enclosure_meters.enclosure_id
                        WHERE meter_id = ".$this->db->SqlVal($condition["meter_id"], "mytext")."
                ) em on em.enclosure_id = meters.enclosure_id";

            unset($condition["meter_id"]);
        }

        print $sql .= " LEFT JOIN enclosure_type
                ON
                    (
                        (gateway_id IS NOT NULL AND enclosure_type.gateway = 1) OR
                        (IFNULL(gateway_id, 0)=enclosure_type.gateway)
                    )

                AND enclosure.phase = enclosure_type.phase
                AND single_phase + three_phase = enclosure_type.Meter
                AND
                (
                    ( single_phase + three_phase > 3 AND enclosure_type.enclosure_shape_id = 2 ) OR
                    ( single_phase + three_phase <= 3 AND enclosure_type.enclosure_shape_id = 1 )
                )";
*/
        if($simcard_status_id){
            $sql .= " WHERE ( meter.simcard_status_id = $simcard_status_id OR gateway_sn.simcard_status_id = $simcard_status_id )";
            unset($condition["simcard_status_id"]);
        } else {
            $sql .= " WHERE 1 = 1 ";
        }
        if($order == null){
            $order = array("enclosure_id" => "ASC");
        }
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }
        // print $sql;
        // print_r($params);
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount, 0, "AND");
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetEnclosureCount($condition = null,$order = null, $start = 0, $size = 0, &$recordsCount = null){
        // $condition = array();
        $params=null;
        $sql = "SELECT count(*) as enclosure_count, enclosure_type
                FROM enclosure
                INNER JOIN (
                        SELECT enclosure_meters.enclosure_id,
                        SUM(CASE WHEN meter_type.phase = 1 THEN 1 ELSE 0 END) AS single_phase,
                        SUM(CASE WHEN meter_type.phase = 3 THEN 1 ELSE 0 END) AS three_phase
                        FROM enclosure_meters
                        INNER JOIN meter ON meter.meter_id = enclosure_meters.meter_id
                        INNER JOIN meter_type on meter.meter_type_id = meter_type.meter_type_id
                        GROUP BY enclosure_meters.enclosure_id
                ) meters ON meters.enclosure_id = enclosure.enclosure_id
                LEFT JOIN enclosure_type
                ON
                    (
                        (gateway_id IS NOT NULL AND enclosure_type.gateway = 1) OR
                        (IFNULL(gateway_id, 0)=enclosure_type.gateway)
                    )

                AND enclosure.phase = enclosure_type.phase
                AND single_phase + three_phase = enclosure_type.Meter
                AND
                (
                    ( single_phase + three_phase > 3 AND enclosure_type.enclosure_shape_id = 2 ) OR
                    ( single_phase + three_phase <= 3 AND enclosure_type.enclosure_shape_id = 1 )
                )

                WHERE (enclosure.timestamp BETWEEN  ? AND ?)";
                if(isset($condition["user_id"])&&$condition["user_id"]!=null){
                    $sql.="AND user_id=? ";
                    $filter = array($condition["from_date"],$condition["to_date"],$condition["user_id"]);
                }
                else{
                    $filter = array($condition["from_date"],$condition["to_date"]);

                }
            //$sql.=   "group by single_phase, three_phase";
        $sql .= " GROUP BY enclosure_type";

        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray($filter);
        }
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetMeters($condition= null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        $params =null;
        $sql = "SELECT DISTINCT meter.meter_id, meter_sn, meter.timestamp, enclosure_sn, gateway_sn
                FROM meter
                LEFT JOIN enclosure_meters ON meter.meter_id = enclosure_meters.meter_id
                LEFT JOIN enclosure on enclosure.enclosure_id = enclosure_meters.enclosure_id
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id";

        if($order == null){
            $order = array("meter.timestamp" => "DESC");
        }
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetGateway($condition= null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        $params =null;
        $sql = "SELECT DISTINCT gateway_sn.gateway_id, gateway_sn, enclosure_sn, CAST(ICCID AS CHAR(20)) ICCID, simcard_status, activation_date, ip_address
                FROM gateway_sn
                LEFT JOIN enclosure on enclosure.gateway_id = gateway_sn.gateway_id
                INNER JOIN simcard_status on simcard_status.simcard_status_id = gateway_sn.simcard_status_id";

        if($order == null){
            $order = array("gateway_sn.activation_date" => "DESC");
        }
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }



    public function DeleteEnclosure($enclosure_id)
    {
        $error = false;
        $this->db->BeginTransaction();

        /*
        $sql="DELETE meter
                FROM meter
                INNER JOIN enclosure_meters ON enclosure_meters.meter_id = meter.meter_id
                WHERE enclosure_meters.enclosure_id = ?";

        $params = $this->db->ConvertToParamsArray(array($enclosure_id));
        if (! $this->db->Execute($sql, $params) ) {
            $error=true;
        }
        */

        $condition = array();
        $condition['enclosure_id'] = $this -> db -> SqlVal($enclosure_id, "int");
        if (!$this -> db -> Delete("enclosure_meters", $condition)) {
            $error=true;
        }
        if (!$this -> db -> Delete("enclosure", $condition)) {
            $error=true;
        }
        if ($error) {
            $this->db->RollbackTransaction();
            $this -> State = $this -> db -> state;
            $this -> Message = "enclosure_meters_Delete_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this -> State = self::SUCCESS;
            $this -> Message = "enclosure_meters_Delete_success";
            return true;
        }
    }

    public function CancelEnclosure($enclosure_id)
    {
        $condition = array();
        $condition['enclosure_id'] = $this -> db -> SqlVal($enclosure_id, "int");
        $condition['status_id'] = 0;
        $result = $this -> db -> Delete("enclosure", $condition);

        if ($result) {
            $this -> State = self::SUCCESS;
            $this -> Message = "enclosure_meters_Cancel_success";
            return true;
        } else {
            $this -> State = $this -> db -> state;
            $this -> Message = "enclosure_meters_Cancel_failed";
            return false;
        }
    }

    public function MeterExist($id){
        $sql = "SELECT meter_id
                FROM meter
                WHERE meter_id = ?";
        $result = $this->db->SelectData($sql, $this->db->GenerateParam($id));
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetEnclosureType($condition = null){
        $sql = "SELECT enclosure_type_id, enclosure_type, Meter, single, three, gateway, enclosure_shape_id, phase, meter_type_id FROM enclosure_type";

        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }
        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetMeterId($meter_sn)
    {
        $meter_sn = $this->db->SqlVal($meter_sn, "mytext");
        $sql = "SELECT meter_id FROM meter WHERE meter_sn = '$meter_sn'";
        $meter_id = $this->db->SelectValue($sql);
        if($meter_id > 0){
            return $meter_id;
        } else {
            return false;
        }
    }

    public function MeterIsAssembledInOtherEnclosure($condition)
    {
        $sql = "SELECT *
                from enclosure_meters
                INNER JOIN enclosure ON enclosure_meters.enclosure_id = enclosure.enclosure_id
                INNER JOIN meter ON enclosure_meters.meter_id = meter.meter_id
                WHERE enclosure.enclosure_id != ?
                AND meter.meter_id = ?";

        $params = $this->db->ConvertToParamsArray($condition);

        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetEnclosureId($enclosure_sn)
    {
        $enclosure_sn = $this->db->SqlVal($enclosure_sn, "mytext");
        $sql = "SELECT enclosure_id FROM enclosure WHERE enclosure_sn = '$enclosure_sn'";
        $enclosure_id = $this->db->SelectValue($sql);
        if($enclosure_id > 0){
            return $enclosure_id;
        } else {
            return false;
        }
    }

    public function GetGatewayId($gateway_sn)
    {
        $gateway_sn = $this->db->SqlVal($gateway_sn, "mytext");
        $sql = "SELECT gateway_id FROM gateway_sn WHERE gateway_sn = '$gateway_sn'";
        $gateway_id = $this->db->SelectValue($sql);
        if($gateway_id > 0){
            return $gateway_id;
        } else {
            return false;
        }
    }

    public function GatewayIsAssembledInOtherEnclosure($condition)
    {
        $sql = "SELECT *
                FROM gateway_sn
                INNER JOIN enclosure ON gateway_sn.gateway_id = enclosure.gateway_id
                WHERE enclosure_id != ?
                AND gateway_sn.gateway_id = ?";

        $params = $this->db->ConvertToParamsArray($condition);

        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function getActiveAssemblyOrdersArr()
    {
        $sql = "SELECT *
                FROM assembly_order
                WHERE assembly_order.status_id =1
                ORDER By start_date, create_date, assembly_order_id DESC";
            $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);

            return $result;
    }

    public function GetEnclosureMeters($condition= null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $filter = array();
        $sql = "SELECT enclosure.enclosure_id, enclosure_sn, gateway_sn, meter_sn, enclosure.`timestamp` AS `timestamp`
                FROM enclosure
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                LEFT JOIN enclosure_meters ON enclosure.enclosure_id = enclosure_meters.enclosure_id
                LEFT JOIN meter ON enclosure_meters.meter_id = meter.meter_id
                WHERE (enclosure.timestamp BETWEEN  ? AND ?)";

        $filter[] = $condition["from_date"];
        $filter[] = $condition["to_date"];
        unset($condition["from_date"]);
        unset($condition["to_date"]);

        $params = $this->db->ConvertToParamsArray($filter, $condition);

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount, 0, "AND");
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function UpdateIccidStatus($id, $iccids)
    {
        $sql="UPDATE meter SET gateway_status_id = ?
        WHERE ICCID IN (?)";

        $filter = array();
        $filter[] = $id;
        $filter[] = $iccids;
        $params = $this->db->ConvertToParamsArray($filter);
        if (! $this->db->Execute($sql, $params) ) {
            // $error=true;
            return false;
        } else {
            return true;
        }
    }


    public function UpdateEcnclosureConfig($enclosure_id, $configuration)
    {
        $error = false;
        $this->Message = "";
        $this->db->BeginTransaction();

        $condition = array();
        $condition['enclosure_id'] = $this -> db -> SqlVal($enclosure_id, "int");
        $result = $this->db->Delete('enclosure_meters', $condition);

        if (!$result) {
            $error = true;
        } else {
            $data = array();
            $data['enclosure_configuration_id'] = $this -> db -> SqlVal($configuration, "int");
            $data['gateway_id'] = null;
            $result = $this->db->Update('enclosure', $data, $condition);
            if (!$result) {
                $error = true;
            }
        }

        if ($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "enclosure_update_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "enclosure_update_success";
            return $enclosure_id;
        }

    }

    public function GetTraceReasons()
    {
        $sql = "SELECT * FROM trace_change_reason";
        $result = $this->db->SelectData($sql, null, null, null, null, $recordsCount, 1);
        return $result;
    }

    public function GetConfiguration($enclosure_config_id)
    {
        $sql = "SELECT c.enclosure_type_id, meter_type_id, configuration_name, meter, gateway
                FROM enclosure_config c
                INNER JOIN enclosure_type t on t.enclosure_type_id = c.enclosure_type_id
                WHERE enclosure_config_id = ?";
        $params = $this->db->ConvertToParamsArray([$enclosure_config_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }
}
