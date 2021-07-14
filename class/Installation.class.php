<?php
require_once (realpath(dirname(__FILE__)) . '/MysqliDB.php');

/**
 * @access public
 * @package EMPLOYEE
 */
class Installation
{
    //state constants
    const ERROR = 0;
    const WARNING = 1;
    const SUCCESS = 2;
    const POINTS_FACTOR = 125;

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
        if ($meter_id == null) {
            return true;
        }
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

    public function GetEnclosureComponents($enclosure_id)
    {
        $sql = "SELECT meter_id, 'm' as type FROM enclosure_meters WHERE enclosure_id = ?
                UNION ALL
                SELECT concat('gateway_',gateway_id), 'g' as type FROM enclosure WHERE enclosure_id = ?";
        $params = $this->db->ConvertToParamsArray([$enclosure_id, $enclosure_id]);
        return $this->db->SelectData($sql, $params);
    }


    public function AddInstalledPointEnclosure($point_enclosure)
    {
        $error = false;
        $this->Message = "";
        $this->db->BeginTransaction();

        $data = array();
        $data['point_id']= $this->db->SqlVal($point_enclosure["point_id"], "int");
        $data['enclosure_id']= $this->db->SqlVal($point_enclosure["enclosure_id"], "int");
        $data['transformer_id']= $this->db->SqlVal($point_enclosure["transformer_id"], "int");
        $data['user_id']= $this->db->SqlVal($point_enclosure["user_id"], "int");
        $data['timestamp']= date('Y-m-d H:i:s');
        $installed_point_enclosure = $this->db->Insert("installed_point_enclosure", $data, true);
        if (!$installed_point_enclosure) {
            $error= true;
        }

        $r = $this->AddEnclosureTrace($point_enclosure["user_id"], $point_enclosure["enclosure_id"], 2, null, $point_enclosure["point_id"]);
        if (!$r) {
            $error = true;
        }

        $components = $this->GetEnclosureComponents($point_enclosure["enclosure_id"]);

        $data = array();
        $data["user_id"] = $point_enclosure["user_id"];
        $data["timestamp"] = date("Y-m-d H:i:s");
        $data["enclosure_id"] = $point_enclosure["enclosure_id"];
        $data["description"] = $point_enclosure["point_id"];
        $data["meter_trace_status_id"] = 4; //installed

        for ($i=0; $i<count($components); $i++) {
            $r = $this->AddMeterTrace($components[$i]["meter_id"], $data);
            if (!$r) {
                $error = true;
                break;
            }
        }


        if (!$error) {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "installed_point_enclosure_insert_success";
            return $installed_point_enclosure;
        } else {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "installed_point_enclosure_insert_failed";
            return false;
        }
    }






    public function DeleteInstalledPointEnclosure($installed_point_enclosure, $user_id)
    {
        $error = false;
        $this->Message = "";
        $this->db->BeginTransaction();

        $sql = "SELECT * FROM installed_point_enclosure WHERE installed_point_enclosure_id = ?";
        $params = $this->db->ConvertToParamsArray([$installed_point_enclosure["installed_point_enclosure_id"]]);
        $point_enclosure =  $this->db->SelectData($sql, $params);
        if ($point_enclosure) {
            $point_enclosure = $point_enclosure[0];
        } else {
            $error = true;
        }

        $condition = array();
        $condition['installed_point_enclosure_id'] = $this -> db -> SqlVal($installed_point_enclosure["installed_point_enclosure_id"], "int");
        if ($this -> db -> Delete("installed_point_enclosure", $condition)) {
            //
        } else {
            $error = true;
        }

        $r = $this->AddEnclosureTrace($user_id, $point_enclosure["enclosure_id"], 5, null, $point_enclosure["point_id"]);
        if (!$r) {
            $error = true;
        }

        $components = $this->GetEnclosureComponents($point_enclosure["enclosure_id"]);

        $data = array();
        $data["user_id"] = $user_id;
        $data["timestamp"] = date("Y-m-d H:i:s");
        $data["enclosure_id"] = $point_enclosure["enclosure_id"];
        $data["description"] = $point_enclosure["point_id"];
        $data["meter_trace_status_id"] = 5; //uninstalled

        for ($i=0; $i<count($components); $i++) {
            $r = $this->AddMeterTrace($components[$i]["meter_id"], $data);
            if (!$r) {
                $error = true;
                break;
            }
        }


        if (!$error) {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "installed_point_enclosure_Delete_success";
            return $installed_point_enclosure;
        } else {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "installed_point_enclosure_Delete_failed";
            return false;
        }
    }

    public function EditPointInstallationStatus($point_data)
    {
        if(isset($point_data["status_type_id"]))    $data['installation_status_id'] = $this->db->SqlVal($point_data["status_type_id"], "int");
        if(isset($point_data["installation_user_id"]))    $data['installation_user_id'] = $this->db->SqlVal($point_data["installation_user_id"], "int");
        $data['installation_timestamp'] = date('Y-m-d H:i:s');

        $condition = array();
        $condition['point_id'] = $this -> db -> SqlVal($point_data["point_id"], "int");

        $result = $this->db->Update("service_point", $data, $condition);
        if ($result) {
            $this -> State = self::SUCCESS;
            $this -> Message = "point_installation_status_update_success";
            return true;
        } else {
            $this -> State = $this -> db -> state;
            $this -> Message = "point_installation_status_update_failed";
            return false;
        }
    }

    public function AddInstallationProblem($problem_data){
        $data = array();
        $data['point_id']= $this->db->SqlVal($problem_data["point_id"], "int");
        $data['installation_problem_id']= $this->db->SqlVal($problem_data["installation_problem_id"], "int");
        $data['state']= 1;
        $data['create_notes']= $this->db->SqlVal($problem_data["create_notes"], "mytext");
        $data['create_user_id']= $this->db->SqlVal($problem_data["create_user_id"], "int");
        $data['create_time_stamp']= date("Y-m-d H:i:s");

        $problem_report_id = $this->db->Insert("installation_problem_report", $data, true);

        if ($problem_report_id) {
            $this->State = self::SUCCESS;
            $this->Message = "installation_problem_report_insert_success";
            return $problem_report_id;
        } else {
            $this->State = self::ERROR;
            $this->Message = "installation_problem_report_insert_failed";
            return false;
        }
    }

    public function AddInstallationComment($comment_data){
        $data = array();
        $data['point_id']= $this->db->SqlVal($comment_data["point_id"], "int");
        $data['user_id']= $this->db->SqlVal($comment_data["user_id"], "int");
        $data['comment_time']= date("Y-m-d H:i:s");
        $data['comment']= $this->db->SqlVal($comment_data["comment"], "mytext");

        $comment_id = $this->db->Insert("installation_comment", $data, true);

        if ($comment_id) {
            $this->State = self::SUCCESS;
            $this->Message = "installation_problem_report_insert_success";
            return $comment_id;
        } else {
            $this->State = self::ERROR;
            $this->Message = "installation_problem_report_insert_failed";
            return false;
        }
    }

    public function EditInstallationProblem($problem_data)
    {
        if(isset($problem_data["point_id"]))                    $data['point_id']= $this->db->SqlVal($problem_data["point_id"], "int");
        if(isset($problem_data["installation_problem_id"]))     $data['installation_problem_id']= $this->db->SqlVal($problem_data["installation_problem_id"], "int");
        if(isset($problem_data["state"]))                       $data['state']= $this->db->SqlVal($problem_data["state"], "int");
        if(isset($problem_data["create_notes"]))                $data['create_notes']= $this->db->SqlVal($problem_data["create_notes"], "mytext");
        if(isset($problem_data["create_user_id"]))              $data['create_user_id']= $this->db->SqlVal($problem_data["create_user_id"], "int");
        if(isset($problem_data["create_time_stamp"]))           $data['create_time_stamp']= $this->db->SqlVal($problem_data["create_time_stamp"], "date");
        if(isset($problem_data["update_notes"]))                $data['update_notes']= $this->db->SqlVal($problem_data["update_notes"], "mytext");
        if(isset($problem_data["update_user_id"]))              $data['update_user_id']= $this->db->SqlVal($problem_data["update_user_id"], "int");
        $data['update_time_stamp']= date("Y-m-d H:i:s");

        $condition = array();
        $condition['problem_report_id'] = $this -> db -> SqlVal($problem_data["problem_report_id"], "int");

        $result = $this->db->Update("installation_problem_report", $data, $condition);
        if ($result) {
            $this -> State = self::SUCCESS;
            $this -> Message = "installation_problem_report_update_success";
            return true;
        } else {
            $this -> State = $this -> db -> state;
            $this -> Message = "installation_problem_report_update_failed";
            return false;
        }
    }

    public function GetInstallationProblem($condition)
    {
        $sql = "SELECT problem_report_id, point_id, installation_problem_report.installation_problem_id installation_problem_id, `state`, create_notes, create_user_id,
                create_time_stamp, update_notes, update_user_id, update_time_stamp, installation_problem, user1.NAME create_user, user2.NAME update_user
                FROM installation_problem_report
                INNER JOIN installation_problem on installation_problem_report.installation_problem_id = installation_problem.installation_problem_id
                INNER JOIN `USER` user1 ON installation_problem_report.create_user_id = user1.USER_ID
                LEFT JOIN `USER` user2 ON installation_problem_report.update_user_id = user2.USER_ID";
        $params = $this->db->ConvertToParamsArray(NULL, $condition);
        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetInstallationComments($point_id = null)
    {
        $filter = array();
        $sql = "SELECT NAME, comment_time, comment comments FROM installation_comment
                INNER JOIN USER ON USER.USER_ID = installation_comment.user_id
                WHERE point_id = ?
                ORDER BY comment_time DESC";

        $params = $this->db->ConvertToParamsArray($point_id);
        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetDifferenceCalculatedAndInstalled($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        if(isset($condition["count"]) AND $condition["count"] == true){
            $Select = "COUNT(DISTINCT point_id) AS count_point_id";
            $CountDistinctInstalledPointID = " UNION SELECT COUNT(DISTINCT point_id) FROM installed_point_enclosure";
            unset($condition["count"]);
        } else {
            $Select = "*";
            $CountDistinctInstalledPointID = "";
        }

        $sql = "SELECT $Select FROM (
                    SELECT c.point_id,
                        c.enclosure_type AS calculated_type,
                        c.enclosures AS calculated_enclosures,
                        i.enclosure_type AS installed_type,
                        i.enclosures AS installed_enclosures
                    FROM
                    (
                            SELECT COUNT(*) AS enclosures, a.point_id, enclosure_type
                            FROM service_point_enclosure_type a
                            INNER JOIN enclosure_type t ON t.enclosure_type_id = a.enclosure_type_id
                                            INNER JOIN service_point ON service_point.point_id = a.point_id AND installation_status_id = 2
                            GROUP BY a.point_id, enclosure_type
                    ) c
                    LEFT JOIN
                    (
                            SELECT COUNT(*) AS enclosures, point_id, enclosure_type
                            FROM installed_point_enclosure a
                            INNER JOIN enclosure ON a.enclosure_id = enclosure.enclosure_id
                            INNER JOIN enclosure_config ec ON ec.enclosure_config_id = enclosure.enclosure_configuration_id
                            INNER JOIN enclosure_type t ON t.enclosure_type_id = ec.enclosure_type_id
                            GROUP BY point_id, enclosure_type
                    ) i ON c.point_id = i.point_id AND i.enclosure_type = c.enclosure_type
                    WHERE (i.enclosures != c.enclosures OR i.enclosure_type IS NULL)

                    UNION ALL

                    SELECT i.point_id,
                                c.enclosure_type AS calculated_type,
                                c.enclosures AS calculated_enclosures,
                                i.enclosure_type AS installed_type,
                                i.enclosures AS installed_enclosures
                    FROM
                    (
                            SELECT COUNT(*) AS enclosures, a.point_id, enclosure_type
                            FROM installed_point_enclosure a
                            INNER JOIN enclosure ON a.enclosure_id = enclosure.enclosure_id
                            INNER JOIN enclosure_config ec ON ec.enclosure_config_id = enclosure.enclosure_configuration_id
                            INNER JOIN enclosure_type t ON t.enclosure_type_id = ec.enclosure_type_id
                                            INNER JOIN service_point ON service_point.point_id = a.point_id AND installation_status_id = 2
                            GROUP BY point_id, enclosure_type
                    ) i
                    LEFT JOIN (
                            SELECT COUNT(*) as enclosures, point_id, enclosure_type
                            FROM service_point_enclosure_type a
                            INNER JOIN enclosure_type t ON t.enclosure_type_id = a.enclosure_type_id
                            GROUP BY point_id, enclosure_type
                    ) c ON c.point_id = i.point_id AND i.enclosure_type = c.enclosure_type
                    WHERE (i.enclosures != c.enclosures OR c.enclosure_type IS NULL)
                ) bb

                $CountDistinctInstalledPointID
            ";

        $params = "";
        if($condition) {
            $params = $this->db->ConvertToParamsArray($condition);
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetInstallationProblemReportCount($condition=null)
    {
        $sql = "SELECT installation_problem, COUNT(installation_problem) installation_problem_count
                FROM installation_problem_report
                INNER JOIN installation_problem ON installation_problem_report.installation_problem_id = installation_problem.installation_problem_id
                GROUP BY installation_problem
                ORDER BY installation_problem";
        $params =array();
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(NULL, $condition);
        }
        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetTransformer($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null, $has_point = false)
    {
        $params=null;
        $sql = "SELECT transformer.transformer_id transformer_id, station_id, feeder_id, capacity_id, transformer_number, latitude, longitude,
                user_id, area_id, timestamp, ponit_count
                FROM transformer
                LEFT JOIN (
                        SELECT transformer_id, COUNT(transformer_id) ponit_count FROM service_point  GROUP BY transformer_id
                    ) t ON transformer.transformer_id = t.transformer_id";
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }

        if ($order == null) {
            $order=array();
            $order["timestamp"]="DESC";
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetTransformerArr($condition)
    {
        $params=null;
        $sql="SELECT service_point.point_id transformer_id,
                    CONCAT(transformer_number, ' [' ,service_point.transformer_code, service_point.transformer_gov_number, ']') AS transformer_number
                    , ponit_count
                FROM service_point
                LEFT JOIN (
                    SELECT pl.transformer_id, COUNT(pl.transformer_id) ponit_count
                    FROM service_point s
                    INNER JOIN line_points lp ON lp.point_id = s.point_id
                    INNER JOIN point_line pl ON lp.line_id = pl.line_id
                    GROUP BY pl.transformer_id
                ) sp2 ON service_point.point_id = sp2.transformer_id";

        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }

        $order=array();
        $order["transformer_number"]="ASC";

        $result = $this->db->SelectData($sql, $params, $order, 0, 0, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetInstalledEnclosures($condition)
    {
        $sql = "SELECT enclosure.enclosure_id, enclosure_sn, installed_point_enclosure_id, point_id , enclosure_type.enclosure_type
                FROM enclosure
                INNER JOIN enclosure_config ON enclosure.enclosure_configuration_id = enclosure_config.enclosure_config_id
                INNER JOIN enclosure_type on enclosure_type.enclosure_type_id = enclosure_config.enclosure_type_id
                INNER JOIN installed_point_enclosure on installed_point_enclosure.enclosure_id = enclosure.enclosure_id
                WHERE point_id = ?
                ORDER BY installed_point_enclosure_id";
                /*
        $sql = "SELECT enclosure.enclosure_id, enclosure_sn, enclosure_type.enclosure_type, installed_point_enclosure_id, point_id
                FROM enclosure
                INNER JOIN enclosure_type on enclosure_type.enclosure_type_id = enclosure.enclosure_type_id
                INNER JOIN installed_point_enclosure on installed_point_enclosure.enclosure_id = enclosure.enclosure_id
                WHERE point_id = ?
                ORDER BY installed_point_enclosure_id";
                */
        $params = $this->db->ConvertToParamsArray($condition);
        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetEnclosureInstallationSummary($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $Where = "";
        if(isset($condition["station_id"])){
            $Where = " WHERE service_point.station_id = ".$this->db->SqlVal($condition["station_id"], "int");
        }
        if(isset($condition["feeder_id"])){
            $Where = " WHERE service_point.feeder_id = ".$this->db->SqlVal($condition["feeder_id"], "int");
        }
        if(isset($condition["transformer_id"])){
            $Where = " WHERE transformer_id IN ( ".$this->db->SqlVal($condition["transformer_id"], "mytext")." ) ";
        }

        /*$sql = "SELECT L.transformer_id, T.transformer_number,
                SUM(A.enclosure_count) Total_Enclosure, SUM(A.Meter) Total_Meter,
                COUNT(B.enclosure_id) Installed_Enclosure, IFNULL(SUM(B.Meter), 0) Installed_Meter

                FROM service_point SP
                INNER JOIN line_points P ON SP.point_id = P.point_id
                INNER JOIN point_line L ON L.line_id = P.line_id
                INNER JOIN service_point T ON L.transformer_id = T.point_id

                LEFT JOIN (
                                SELECT DISTINCT s.point_id, enclosure_count, Meter
                                FROM service_point s
                                INNER JOIN service_point_enclosure_type SPET ON s.point_id = SPET.point_id
                                INNER JOIN enclosure_type ET on ET.enclosure_type_id = SPET.enclosure_type_id
                                WHERE s.point_type_id < 4
                ) A ON A.point_id = SP.point_id
                INNER JOIN line_points PA ON A.point_id = PA.point_id
                INNER JOIN point_line LA ON LA.line_id = PA.line_id

                LEFT JOIN (
                        SELECT DISTINCT s.point_id, IPE.enclosure_id, COUNT(meter_id) Meter
                        FROM service_point s
                        INNER JOIN  installed_point_enclosure IPE ON s.point_id = IPE.point_id
                        LEFT JOIN enclosure_meters EM ON IPE.enclosure_id = EM.enclosure_id
                        WHERE s.point_type_id < 4
                        GROUP BY s.point_id, IPE.enclosure_id
                ) B ON B.point_id = SP.point_id
                LEFT JOIN line_points PB ON B.point_id = PB.point_id
                LEFT JOIN point_line LB ON LB.line_id = PB.line_id
                $Where
                GROUP BY L.transformer_id, T.transformer_number";*/
        $sql = "SELECT SP.transformer_id, SP.transformer_number,
                        SUM(Total_Enclosure) as Total_Enclosure, SUM(Total_Meter) as Total_Meter,
                        COUNT(Installed_Enclosure) as Installed_Enclosure, IFNULL(SUM(Installed_Meter), 0) as Installed_Meter
                FROM (
                    SELECT DISTINCT service_point.point_id, L.transformer_id, T.transformer_number, T.feeder_id, T.station_id
                    FROM service_point
                    INNER JOIN line_points P ON service_point.point_id = P.point_id
                    INNER JOIN point_line L ON L.line_id = P.line_id
                    INNER JOIN service_point T ON L.transformer_id = T.point_id
                    $Where
                ) SP
                LEFT JOIN (
                    SELECT DISTINCT s.point_id, SUM(enclosure_count) Total_Enclosure, SUM(enclosure_count*Meter) Total_Meter
                    FROM service_point s
                    INNER JOIN service_point_enclosure_type SPET ON s.point_id = SPET.point_id
                    INNER JOIN enclosure_type ET on ET.enclosure_type_id = SPET.enclosure_type_id
                    GROUP BY s.point_id
                ) c ON c.point_id = SP.point_id
                LEFT JOIN (
                    SELECT s.point_id, COUNT(DISTINCT IPE.enclosure_id) Installed_Enclosure, COUNT(meter_id) Installed_Meter
                    FROM service_point s
                    INNER JOIN  installed_point_enclosure IPE ON s.point_id = IPE.point_id
                    LEFT JOIN enclosure_meters EM ON IPE.enclosure_id = EM.enclosure_id
                    GROUP BY s.point_id
                ) i ON i.point_id = SP.point_id
                GROUP BY SP.transformer_id, SP.transformer_number";

        $params = "";
        if($condition) {
            //$params = $this->db->ConvertToParamsArray($condition);
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetEnclosureInstallation($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $filter = array();
        $filter_by_meter_sn = $ICCID = $ip_address = "";

        // if isset meter_sn check to filter
        if( isset($condition["meter_sn"]) && $condition["meter_sn"] != "" ){
            $meter_sn = $this->db->SqlVal($condition["meter_sn"], "text");
            $filter_by_meter_sn = true;
        }

        // if isset meter_sn check to filter
        if( isset($condition["ICCID"]) && $condition["ICCID"] != "" ){
            $ICCID = $this->db->SqlVal($condition["ICCID"], "text");
        }

        // if isset ip_address check to filter
        if( isset($condition["ip_address"]) && $condition["ip_address"] != "" ){
            $ip_address = $this->db->SqlVal($condition["ip_address"], "text");
        }

        /*
        if( (isset($condition["simcard_status_id"]) && $condition["simcard_status_id"] != "") ||
            (isset($condition["activation_date"]) && $condition["activation_date"] != "") ) {

            $card_status_where = "WHERE";

            if( isset($condition["simcard_status_id"]) && $condition["simcard_status_id"] != "" ){
                $simcard_status_id = $this->db->SqlVal($condition["simcard_status_id"], "text");
                $card_status_where .= " ( meters.simcard_status_id = $simcard_status_id OR gateway_sn.simcard_status_id = $simcard_status_id )";
                unset($condition["simcard_status_id"]);
            }

            if( isset($condition["activation_date"]) && $condition["activation_date"] != "" ){
                $activation_date = $this->db->SqlVal($condition["activation_date"], "text");

                if ($card_status_where !== "WHERE") {
                    $card_status_where .=" AND ";
                }
                $card_status_where .= " ( meters.activation_date = $activation_date OR gateway_sn.activation_date = $activation_date ) ";
                unset($condition["activation_date"]);
            }
        }
        */


        $sql = "SELECT DISTINCT installed_point_enclosure.`timestamp` installed_time, station, feeder, NODE_NAME, sp.point_id, sp.latitude, sp.longitude, t.transformer_number,
                enclosure.enclosure_id , enclosure_type , configuration_name , enclosure_sn , gateway_sn,
                Meter1, Meter2, Meter3, Meter4, Meter5, Meter6,
                CONCAT( IFNULL(ICCID, ''), ' ', IFNULL(ICCID1, ''), ' ', IFNULL(ICCID2, '') ) iccides,
                CONCAT( IFNULL(ip_address, ''), ' ', IFNULL(ip_address1, ''), ' ', IFNULL(ip_address2, '') ) ip_addresses

                FROM enclosure
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                INNER JOIN enclosure_config ON enclosure.enclosure_configuration_id = enclosure_config.enclosure_config_id
                INNER JOIN enclosure_type ON enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id
                INNER JOIN installed_point_enclosure ON enclosure.enclosure_id = installed_point_enclosure.enclosure_id
                INNER JOIN service_point sp ON sp.point_id = installed_point_enclosure.point_id
                INNER JOIN	(
                    SELECT lp.point_id, transformer_id
                    FROM line_points p
                    INNER JOIN point_line l ON l.line_id = p.line_id
                    INNER JOIN (
                        SELECT MIN(line_id) as line_id, point_id FROM line_points WHERE is_service_point = 1 group by point_id
                    ) lp on lp.point_id = p.point_id and lp.line_id = p.line_id
                    WHERE is_service_point = 1
                ) line ON line.point_id = installed_point_enclosure.point_id
                INNER JOIN service_point t ON t.point_id = line.transformer_id
                INNER JOIN station ON t.station_id = station.station_id
                INNER JOIN feeder ON t.feeder_id = feeder.feeder_id
                INNER JOIN AREA_TREE ON t.area_id = AREA_TREE.NODE_ID
                LEFT JOIN
                (
                    SELECT enclosure_id,
                    MAX(CASE WHEN meter_sequence = 1 THEN meter_sn ELSE null END) Meter1,
                    MAX(CASE WHEN meter_sequence = 2 THEN meter_sn ELSE null END) Meter2,
                    MAX(CASE WHEN meter_sequence = 3 THEN meter_sn ELSE null END) Meter3,
                    MAX(CASE WHEN meter_sequence = 4 THEN meter_sn ELSE null END) Meter4,
                    MAX(CASE WHEN meter_sequence = 5 THEN meter_sn ELSE null END) Meter5,
                    MAX(CASE WHEN meter_sequence = 6 THEN meter_sn ELSE null END) Meter6,
                    MAX(CASE WHEN meter_sequence = 1 THEN ICCID ELSE null END) ICCID1,
                    MAX(CASE WHEN meter_sequence = 2 THEN ICCID ELSE null END) ICCID2,
                    MAX(CASE WHEN meter_sequence = 1 THEN ip_address ELSE null END) ip_address1,
                    MAX(CASE WHEN meter_sequence = 2 THEN ip_address ELSE null END) ip_address2
                    FROM enclosure_meters
                    INNER JOIN meter m ON m.meter_id = enclosure_meters.meter_id
                    GROUP BY enclosure_id
                    ORDER BY null
                ) meters ON enclosure.enclosure_id = meters.enclosure_id
                WHERE
                    (NODE_PATH LIKE ?)";

        $filter = array($condition["area_path"].'.%');

        if( isset($condition["from_date"]) && isset($condition["to_date"]) ){
            $sql .= " AND installed_point_enclosure.`timestamp` >= ? AND installed_point_enclosure.`timestamp` <= ?";
            $filter["from_date"] = $condition["from_date"]. " 00:00:00";
            $filter["to_date"] = $condition["to_date"]." 23:59:59";
        }

        if( $filter_by_meter_sn ){
            $sql .= " AND (
                            Meter1 = $meter_sn OR
                            Meter2 = $meter_sn OR
                            Meter3 = $meter_sn OR
                            Meter4 = $meter_sn OR
                            Meter5 = $meter_sn OR
                            Meter6 = $meter_sn
                        )";
        }

        if( $ICCID ){
            $sql .= " AND ( meters.ICCID1 = $ICCID OR
                            meters.ICCID2 = $ICCID OR
                            gateway_sn.ICCID = $ICCID ) ";
        }

        if( $ip_address ){
            $sql .= " AND ( meters.ip_address1 = $ip_address OR
                            meters.ip_address2 = $ip_address OR
                            gateway_sn.ip_address = $ip_address ) ";
        }


        unset($condition["area_path"]);
        unset($condition["from_date"]);
        unset($condition["to_date"]);
        unset($condition["meter_sn"]);
        unset($condition["ICCID"]);
        unset($condition["ip_address"]);

        $params = "";
        if($condition || $filter) {
            $params = $this->db->ConvertToParamsArray($filter, $condition);
        }

        // print $sql;
        // print_r($params);
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount, 0, "AND");
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetMeterInstallation($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $filter = array();

        $sql = "SELECT DISTINCT installed_point_enclosure.`timestamp` installed_time, station, feeder, NODE_ID, NODE_NAME, sp.point_id, sp.latitude, sp.longitude, t.transformer_number,
                        enclosure.enclosure_id , enclosure_type , configuration_name , enclosure_sn , gateway_sn,
                        meter.Model, meter.Plant_No, meter.Serial_No, meter.STS_No, meter.IMEI, meter.ICCID, meter.meter_id

                FROM enclosure
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                INNER JOIN enclosure_config ON enclosure.enclosure_configuration_id = enclosure_config.enclosure_config_id
                INNER JOIN enclosure_type ON enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id
                INNER JOIN installed_point_enclosure ON enclosure.enclosure_id = installed_point_enclosure.enclosure_id
                INNER JOIN service_point sp ON sp.point_id = installed_point_enclosure.point_id
                INNER JOIN	(
                        SELECT point_id, transformer_id
                        FROM line_points p
                        INNER JOIN point_line l ON l.line_id = p.line_id
                        WHERE is_service_point = 1
                ) line ON line.point_id = installed_point_enclosure.point_id
                INNER JOIN service_point t ON t.point_id = line.transformer_id
                INNER JOIN station ON t.station_id = station.station_id
                INNER JOIN feeder ON t.feeder_id = feeder.feeder_id
                INNER JOIN AREA_TREE ON t.area_id = AREA_TREE.NODE_ID
                LEFT JOIN enclosure_meters ON enclosure.enclosure_id = enclosure_meters.enclosure_id
                LEFT JOIN meter ON enclosure_meters.meter_id = meter.meter_id
                WHERE
                    ( NODE_PATH LIKE ? OR NODE_PATH LIKE ? )
                ";

                $filter = array($condition["area_path"],$condition["area_path"].'.%');

                if( isset($condition["from_date"]) && isset($condition["to_date"]) ){
                    $sql .= " AND DATE(installed_point_enclosure.`timestamp`) >= ? AND DATE(installed_point_enclosure.`timestamp`) <= ?";

                    $filter["from_date"] = $condition["from_date"];
                    $filter["to_date"] = $condition["to_date"];
                }

                unset($condition["area_path"]);
                unset($condition["from_date"]);
                unset($condition["to_date"]);

        $params = "";
        if($condition || $filter) {
            $params = $this->db->ConvertToParamsArray($filter, $condition);
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount, 0, "AND");
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetInstalledItems($condition = null)
    {
        $filter = array();

        $sql = "SELECT enclosure.enclosure_sn, gateway_sn AS SN, 'Gateway' as Type, Model, Plant_No, Serial_No, IMEI, ICCID, null AS STS_No,simcard_status,activation_date
                FROM gateway_sn
                INNER JOIN enclosure ON enclosure.gateway_id = gateway_sn.gateway_id
                INNER JOIN installed_point_enclosure ON enclosure.enclosure_id = installed_point_enclosure.enclosure_id
                LEFT JOIN simcard_status ON simcard_status.simcard_status_id = gateway_sn.simcard_status_id
                WHERE installed_point_enclosure.point_id = ?
                UNION ALL
                SELECT  enclosure.enclosure_sn, meter_sn AS SN, meter_type as Type,Model,Plant_No,Serial_No, IMEI, ICCID, STS_No,simcard_status,activation_date
                FROM meter
                INNER JOIN enclosure_meters ON enclosure_meters.meter_id = meter.meter_id
                INNER JOIN enclosure ON enclosure.enclosure_id = enclosure_meters.enclosure_id
                INNER JOIN installed_point_enclosure ON enclosure.enclosure_id = installed_point_enclosure.enclosure_id
                INNER JOIN meter_type ON meter_type.meter_type_id = meter.meter_type_id
                LEFT JOIN simcard_status ON simcard_status.simcard_status_id = meter.simcard_status_id
                WHERE installed_point_enclosure.point_id = ?";

        $filter = array($condition["point_id"], $condition["point_id"]);
        $params = $this->db->ConvertToParamsArray($filter);

        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetInstallationSummary($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $sql = "SELECT T.transformer_id, T.transformer_number,
                SUM(A.enclosure_count) Total_Enclosure, SUM(A.Meter) Total_Meter,
                COUNT(B.enclosure_id) Installed_Enclosure, SUM(B.Meter) Installed_Meter

                FROM service_point SP
                INNER JOIN transformer T ON SP.transformer_id = T.transformer_id

                LEFT JOIN (
                        SELECT point_id, enclosure_count, Meter FROM service_point_enclosure_type SPET
                        LEFT JOIN enclosure_type ET on ET.enclosure_type_id = SPET.enclosure_type_id
                ) A ON A.point_id = SP.point_id

                LEFT JOIN (
                    SELECT point_id, IPE.enclosure_id, COUNT(meter_id) Meter FROM installed_point_enclosure IPE
                    LEFT JOIN enclosure_meters EM ON IPE.enclosure_id = EM.enclosure_id
                    GROUP BY point_id, IPE.enclosure_id
                ) B ON B.point_id = SP.point_id";
        if(isset($condition["transformer_id"])){
            $sql .= " WHERE T.transformer_id IN ( ? ) ";
        }
        $sql .= " GROUP BY T.transformer_id, T.transformer_number
                ORDER BY T.transformer_number";
        $params = "";
        if($condition) {
            $params = $this->db->ConvertToParamsArray($condition);
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetCalculatedEnclosures($condition)
    {
        $sql = "SELECT enclosure_count, enclosure_type
                FROM service_point_enclosure_type et
                INNER JOIN enclosure_type on enclosure_type.enclosure_type_id = et.enclosure_type_id
                WHERE point_id = ?
                ORDER BY enclosure_count DESC, et.enclosure_type_id";
        $params = $this->db->ConvertToParamsArray($condition);
        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetCalculatedAndInstalledEnclosures($point_id)
    {
        $sql = "SELECT * FROM (
                    SELECT IFNULL(t1.enclosure_type_id, t2.enclosure_type_id) AS enclosure_type_id, IFNULL(t1.enclosure_type, t2.enclosure_type) AS enclosure_type,
                    calculated_enclosure_count, inatalle_enclosure_count
                    FROM
                    (
                        SELECT COUNT(*) AS inatalle_enclosure_count, enclosure_type.enclosure_type_id, enclosure_type.enclosure_type
                        FROM enclosure
                        INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = enclosure.enclosure_type_id
                        INNER JOIN installed_point_enclosure ON installed_point_enclosure.enclosure_id = enclosure.enclosure_id
                        WHERE point_id = ?
                        GROUP BY enclosure_type.enclosure_type_id, enclosure_type.enclosure_type
                    ) t1
                    LEFT JOIN
                    (
                        SELECT SUM(enclosure_count) AS calculated_enclosure_count, enclosure_type.enclosure_type_id, enclosure_type
                        FROM service_point_enclosure_type et
                        INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = et.enclosure_type_id
                        WHERE point_id = ?
                        GROUP BY enclosure_type.enclosure_type_id, enclosure_type
                    )t2 ON t1.point_id = t2.point_id and t1.enclosure_type_id = t2.enclosure_type_id

                    UNION

                    SELECT IFNULL(t1.enclosure_type_id, t2.enclosure_type_id) AS enclosure_type_id, IFNULL(t1.enclosure_type, t2.enclosure_type) AS enclosure_type,
                    calculated_enclosure_count, inatalle_enclosure_count
                    FROM
                    (
                        SELECT COUNT(*) AS inatalle_enclosure_count, enclosure_type.enclosure_type_id, enclosure_type.enclosure_type
                        FROM enclosure
                        INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = enclosure.enclosure_type_id
                        INNER JOIN installed_point_enclosure ON installed_point_enclosure.enclosure_id = enclosure.enclosure_id
                        WHERE point_id = ?
                        GROUP BY enclosure_type.enclosure_type_id, enclosure_type.enclosure_type
                    ) t1
                    RIGHT JOIN
                    (
                        SELECT SUM(enclosure_count) AS calculated_enclosure_count, enclosure_type.enclosure_type_id, enclosure_type, point_id
                        FROM service_point_enclosure_type et
                        INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = et.enclosure_type_id
                        WHERE point_id = ?
                        GROUP BY enclosure_type.enclosure_type_id, enclosure_type, point_id
                    )t2 ON t1.point_id = t2.point_id and t1.enclosure_type_id = t2.enclosure_type_id
                ) t
                ORDER BY enclosure_type_id";
        $condition = array("1"=>$point_id, "2"=>$point_id, "3"=>$point_id, "4"=>$point_id);
        $params = $this->db->ConvertToParamsArray($condition);
        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetServicePoint($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $params=null;
        $sql='SELECT DISTINCT service_point.point_id, service_point.point_type_id, single_phase_consumers, three_phase_consumers, accuracy_id, service_point.station_id, service_point.feeder_id,
                        capacity_id, transformer_number, latitude, longitude, user_id, area_id, `timestamp`, point_type, NODE_NAME, needs_gateway,
                        installed_enclosures, l.transformer_id,
                        CASE WHEN point_used > 0 THEN 1 ELSE 0 END as point_used,
                        CONCAT(service_point.transformer_code, service_point.transformer_gov_number) AS transformer_generated_number,
                        CASE
                            WHEN IFNULL(installed_enclosures,0) > 0 AND installation_status_id = 1 AND IFNULL(pr.problem_count, 0) = 0 THEN -1
                            WHEN IFNULL(pr.problem_count, 0) > 0 THEN -2
                            ELSE installation_status_id
                        END AS installation_status_id, LAT_CENERT, LONG_CENERT
                        -- , codes
                FROM service_point
                INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
                INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
                LEFT JOIN (
                    SELECT COUNT(*) AS installed_enclosures, point_id
                    FROM installed_point_enclosure
                    GROUP BY point_id
                ) i ON i.point_id = service_point.point_id
                LEFT JOIN (
                    SELECT COUNT(*) as point_used, point_id FROM line_points
                    WHERE line_point_position_id = 2
                    GROUP By point_id
                ) lp ON lp.point_id = service_point.point_id
                LEFT JOIN (
                    SELECT COUNT(*) as problem_count, point_id
                    FROM installation_problem_report
                    WHERE state = 1
                    GROUP BY point_id
                ) pr on pr.point_id = service_point.point_id
                LEFT JOIN line_points p ON service_point.point_id = p.point_id
                LEFT JOIN point_line l ON l.line_id = p.line_id
                LEFT JOIN (
                    SELECT point_id, station_id, feeder_id FROM service_point
                ) t ON l.transformer_id = t.point_id
';
if ($condition!=null) {
    if(isset($condition["transformer_id"])){
        unset($condition["station_id"]);
        unset($condition["feeder_id"]);
    }
    $params = $this->db->ConvertToParamsArray(null, $condition);
}

if(!$order){
    $order["timestamp"]="DESC";
}

$result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
$this->Message = $this->db->message;
$this->State = $this->db->state;
return $result;
}

public function GetServicePointInstallation($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
{
$params=null;
$sql="SELECT DISTINCT
        service_point.point_id, service_point.point_type_id, transformer_number, timestamp, latitude, longitude, x, y, needs_gateway, -- codes,
        CASE
                WHEN IFNULL(installed_enclosures,0) > 0 AND installation_status_id = 1 AND IFNULL(pr.problem_count, 0) = 0 THEN -1
                WHEN IFNULL(pr.problem_count, 0) > 0 THEN -2
                ELSE installation_status_id
        END AS installation_status_id

        FROM service_point
        LEFT JOIN (
                SELECT COUNT(*) AS installed_enclosures, point_id
                FROM installed_point_enclosure
                GROUP BY point_id
        ) i ON i.point_id = service_point.point_id
        LEFT JOIN (
                SELECT COUNT(*) as problem_count, point_id
                FROM installation_problem_report
                WHERE state = 1
                GROUP BY point_id
        ) pr on pr.point_id = service_point.point_id

        INNER JOIN (
                SELECT distinct feeder_id, station_id, transformer_id, p.point_id
                FROM line_points p
                LEFT JOIN point_line l ON l.line_id = p.line_id
                LEFT JOIN service_point on service_point.point_id = l.transformer_id
                WHERE is_service_point = 1
        ) t ON service_point.point_id = t.point_id
/*
                LEFT JOIN (
                        SELECT point_id,
                                CONCAT(
                                        IFNULL(MAX(A), ''),
                                        IFNULL(MAX(B), ''),
                                        IFNULL(MAX(C), ''),
                                        IFNULL(MAX(D), ''),
                                        IFNULL(MAX(E), ''),
                                        IFNULL(MAX(F), ''),
                                        IFNULL(MAX(G), ''),
                                        IFNULL(MAX(H), ''),
                                        IFNULL(MAX(I), ''),
                                        IFNULL(MAX(J), ''),
                                        IFNULL(MAX(K), ''),
                                        IFNULL(MAX(L), ''),
                                        IFNULL(MAX(M), ''),
                                        IFNULL(MAX(N), ''),
                                        IFNULL(MAX(O), ''),
                                        IFNULL(MAX(P), ''),
                                        IFNULL(MAX(Q), ''),
                                        IFNULL(MAX(R), '')
                                ) AS codes
                        FROM
                                (
                                        SELECT point_id,
                                        CASE WHEN `code` = 'A' THEN CONCAT(SUM(enclosure_count), 'A,') END A,
                                        CASE WHEN `code` = 'B' THEN CONCAT(SUM(enclosure_count), 'B,') END B,
                                        CASE WHEN `code` = 'C' THEN CONCAT(SUM(enclosure_count), 'C,') END C,
                                        CASE WHEN `code` = 'D' THEN CONCAT(SUM(enclosure_count), 'D,') END D,
                                        CASE WHEN `code` = 'E' THEN CONCAT(SUM(enclosure_count), 'E,') END E,
                                        CASE WHEN `code` = 'F' THEN CONCAT(SUM(enclosure_count), 'F,') END F,
                                        CASE WHEN `code` = 'G' THEN CONCAT(SUM(enclosure_count), 'G,') END G,
                                        CASE WHEN `code` = 'H' THEN CONCAT(SUM(enclosure_count), 'H,') END H,
                                        CASE WHEN `code` = 'I' THEN CONCAT(SUM(enclosure_count), 'I,') END I,
                                        CASE WHEN `code` = 'J' THEN CONCAT(SUM(enclosure_count), 'J,') END J,
                                        CASE WHEN `code` = 'K' THEN CONCAT(SUM(enclosure_count), 'K,') END K,
                                        CASE WHEN `code` = 'L' THEN CONCAT(SUM(enclosure_count), 'L,') END L,
                                        CASE WHEN `code` = 'M' THEN CONCAT(SUM(enclosure_count), 'M,') END M,
                                        CASE WHEN `code` = 'N' THEN CONCAT(SUM(enclosure_count), 'N,') END N,
                                        CASE WHEN `code` = 'O' THEN CONCAT(SUM(enclosure_count), 'O,') END O,
                                        CASE WHEN `code` = 'P' THEN CONCAT(SUM(enclosure_count), 'P,') END P,
                                        CASE WHEN `code` = 'Q' THEN CONCAT(SUM(enclosure_count), 'Q,') END Q,
                                        CASE WHEN `code` = 'R' THEN CONCAT(SUM(enclosure_count), 'R,') END R
                                        FROM service_point_enclosure_type
                                        INNER JOIN enclosure_type ON service_point_enclosure_type.enclosure_type_id = enclosure_type.enclosure_type_id
                                        GROUP BY point_id, `code`
                                ) point_code
                        GROUP BY point_id
                ) calculate_codes ON service_point.point_id = calculate_codes.point_id
                */
                ";

        if ($condition!=null) {
            if(isset($condition["transformer_id"])){
                unset($condition["station_id"]);
                unset($condition["feeder_id"]);
            }
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }

        if(!$order){
            $order["timestamp"]="DESC";
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }
/*
    public function GetServicePointInstallationSummary($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $params=null;
        $sql='SELECT DISTINCT
                service_point.point_id, service_point.point_type_id, transformer_number, latitude, longitude, x, y, needs_gateway, codes,
                CASE
                        WHEN IFNULL(installed_enclosures,0) > 0 AND installation_status_id = 1 AND IFNULL(pr.problem_count, 0) = 0 THEN -1
                        WHEN IFNULL(pr.problem_count, 0) > 0 THEN -2
                        ELSE installation_status_id
                END AS installation_status_id

                FROM service_point
                LEFT JOIN (
                        SELECT COUNT(*) AS installed_enclosures, point_id
                        FROM installed_point_enclosure
                        GROUP BY point_id
                ) i ON i.point_id = service_point.point_id
                LEFT JOIN (
                        SELECT COUNT(*) as problem_count, point_id
                        FROM installation_problem_report
                        WHERE state = 1
                        GROUP BY point_id
                ) pr on pr.point_id = service_point.point_id

                INNER JOIN (
                        SELECT distinct feeder_id, station_id, transformer_id, p.point_id
                        FROM line_points p
                        LEFT JOIN point_line l ON l.line_id = p.line_id
                        LEFT JOIN service_point on service_point.point_id = l.transformer_id
                        WHERE is_service_point = 1
                ) t ON service_point.point_id = t.point_id

                LEFT JOIN (
                        SELECT point_id,
                                CONCAT(
                                        IFNULL(MAX(A), ""),
                                        IFNULL(MAX(B), ""),
                                        IFNULL(MAX(C), ""),
                                        IFNULL(MAX(D), ""),
                                        IFNULL(MAX(E), ""),
                                        IFNULL(MAX(F), ""),
                                        IFNULL(MAX(G), ""),
                                        IFNULL(MAX(H), ""),
                                        IFNULL(MAX(I), ""),
                                        IFNULL(MAX(J), ""),
                                        IFNULL(MAX(K), ""),
                                        IFNULL(MAX(L), ""),
                                        IFNULL(MAX(M), ""),
                                        IFNULL(MAX(N), ""),
                                        IFNULL(MAX(O), ""),
                                        IFNULL(MAX(P), ""),
                                        IFNULL(MAX(Q), ""),
                                IFNULL(MAX(R), "")
                                ) AS codes
                        FROM
                                (
                                        SELECT point_id,
                                        CASE WHEN `code` = "A" THEN CONCAT(SUM(enclosure_count), "A,") END A,
                                        CASE WHEN `code` = "B" THEN CONCAT(SUM(enclosure_count), "B,") END B,
                                        CASE WHEN `code` = "C" THEN CONCAT(SUM(enclosure_count), "C,") END C,
                                        CASE WHEN `code` = "D" THEN CONCAT(SUM(enclosure_count), "D,") END D,
                                        CASE WHEN `code` = "E" THEN CONCAT(SUM(enclosure_count), "E,") END E,
                                        CASE WHEN `code` = "F" THEN CONCAT(SUM(enclosure_count), "F,") END F,
                                        CASE WHEN `code` = "G" THEN CONCAT(SUM(enclosure_count), "G,") END G,
                                        CASE WHEN `code` = "H" THEN CONCAT(SUM(enclosure_count), "H,") END H,
                                        CASE WHEN `code` = "I" THEN CONCAT(SUM(enclosure_count), "I,") END I,
                                        CASE WHEN `code` = "J" THEN CONCAT(SUM(enclosure_count), "J,") END J,
                                        CASE WHEN `code` = "K" THEN CONCAT(SUM(enclosure_count), "K,") END K,
                                        CASE WHEN `code` = "L" THEN CONCAT(SUM(enclosure_count), "L,") END L,
                                        CASE WHEN `code` = "M" THEN CONCAT(SUM(enclosure_count), "M,") END M,
                                        CASE WHEN `code` = "N" THEN CONCAT(SUM(enclosure_count), "N,") END N,
                                        CASE WHEN `code` = "O" THEN CONCAT(SUM(enclosure_count), "O,") END O,
                                        CASE WHEN `code` = "P" THEN CONCAT(SUM(enclosure_count), "P,") END P,
                                        CASE WHEN `code` = "Q" THEN CONCAT(SUM(enclosure_count), "Q,") END Q,
                                        CASE WHEN `code` = "R" THEN CONCAT(SUM(enclosure_count), "R,") END R
                                        FROM service_point_enclosure_type
                                        INNER JOIN enclosure_type ON service_point_enclosure_type.enclosure_type_id = enclosure_type.enclosure_type_id
                                        GROUP BY point_id, `code`
                                ) point_code
                        GROUP BY point_id
                ) calculate_codes ON service_point.point_id = calculate_codes.point_id
            ';
        if ($condition!=null) {
            if(isset($condition["transformer_id"])){
                unset($condition["station_id"]);
                unset($condition["feeder_id"]);
            }
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }

        if(!$order){
            $order["timestamp"]="DESC";
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }
*/
    public function GetServicePointWithLines($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $params=null;
        $condition2 = null; //optional, used for subquery
        $sql="SELECT DISTINCT service_point.point_id, service_point.point_type_id, single_phase_consumers, three_phase_consumers, accuracy_id, station_id, feeder_id,
                        capacity_id, transformer_number, latitude, longitude, user_id, area_id, `timestamp`, point_type, NODE_NAME, COLOR, needs_gateway,
                        l.transformer_id, LAT_CENERT, LONG_CENERT,
                        CASE WHEN lp.in_line IS NULL THEN 0 ELSE 1 END AS in_line,
                        CASE WHEN point_used > 0 THEN 1 ELSE 0 END as point_used
                FROM service_point
                INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
                INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
                LEFT JOIN (
                    SELECT COUNT(*) as point_used, point_id FROM line_points
                    WHERE line_point_position_id = 2
                    GROUP By point_id
                ) lps ON lps.point_id = service_point.point_id
                LEFT JOIN (
                    SELECT COUNT(*) AS in_line, line_points.point_id
                    FROM line_points ";
        //in case we have area id, we can increase subquery performance by filtering area id
        if (isset($condition["area_id"])) {
            $condition2 = array("area_id#2"=>$condition["area_id"]);
            $sql .= " INNER JOIN service_point p on p.point_id = line_points.point_id and p.area_id = ? ";
        }
        $sql .= " GROUP BY line_points.point_id
                ) lp  on lp.point_id = service_point.point_id
                LEFT JOIN line_points p ON service_point.point_id = p.point_id
                LEFT JOIN point_line l ON l.line_id = p.line_id";

        if ($condition!=null || $condition2 != null) {
            $params = $this->db->ConvertToParamsArray($condition2, $condition);
        }

        if ($order == null) {
            $order=array();
            $order["timestamp"]="DESC";
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetLines($condition = NULL)
    {
        $params = NULL;
        $sql = "SELECT l.line_id, p.point_id, latitude, longitude
                FROM point_line l
                left join line_points lp on lp.line_id = l.line_id
                left join service_point p on p.point_id = lp.point_id";
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }
        $order["parent_line_id, line_id, point_sequence"]="DESC";
        $result = $this->db->SelectData($sql, $params, $order);
        return $result;
    }

    public function GetServicePointSummary($condition = null)
    {
        $Where = "";
        if(isset($condition["t.station_id"])){
            $Where = " WHERE t.station_id = ".$this->db->SqlVal($condition["t.station_id"], "int");
        }
        if(isset($condition["t.feeder_id"])){
            $Where = " WHERE t.feeder_id = ".$this->db->SqlVal($condition["t.feeder_id"], "int");
        }
        if(isset($condition["transformer_id"])){
            $Where = " WHERE transformer_id IN ( ".$this->db->SqlVal($condition["transformer_id"], "mytext")." ) ";
        }

        $sql="SELECT COUNT(service_point.point_id) point_count,
                        CASE
                            WHEN IFNULL(installed_enclosures,0) > 0 AND installation_status_id = 1 AND IFNULL(pr.problem_count, 0) = 0 THEN -1
                            WHEN IFNULL(pr.problem_count, 0) > 0 THEN -2
                            ELSE installation_status_id
                        END AS installation_status
                FROM service_point
                INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
                INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
                LEFT JOIN (
                    SELECT COUNT(*) AS installed_enclosures, point_id
                    FROM installed_point_enclosure
                    GROUP BY point_id
                ) i ON i.point_id = service_point.point_id
                LEFT JOIN (
                    SELECT COUNT(*) as point_used, point_id FROM line_points
                    WHERE line_point_position_id = 2
                    GROUP By point_id
                ) lp ON lp.point_id = service_point.point_id
                LEFT JOIN (
                    SELECT COUNT(*) as problem_count, point_id
                    FROM installation_problem_report
                    WHERE state = 1
                    GROUP BY point_id
                ) pr on pr.point_id = service_point.point_id
                LEFT JOIN (
                    SELECT distinct feeder_id, station_id, transformer_id, p.point_id
                    FROM line_points p
                    LEFT JOIN point_line l ON l.line_id = p.line_id
                    LEFT JOIN service_point on service_point.point_id = l.transformer_id
                    WHERE is_service_point = 1
                ) t ON t.point_id = service_point.point_id
                $Where
                GROUP BY installation_status
                ";
        $result = $this->db->SelectData($sql);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetInstallationMeterSummary($condition = null)
    {
        $where = "";
        if(isset($condition["t.station_id"])){
            $where = " AND service_point.station_id = ".$this->db->SqlVal($condition["t.station_id"], "int");
        }
        if(isset($condition["t.feeder_id"])){
            $where = " AND service_point.feeder_id = ".$this->db->SqlVal($condition["t.feeder_id"], "int");
        }
        if(isset($condition["transformer_id"])){
            $where = " AND transformer_id IN ( ".$this->db->SqlVal($condition["transformer_id"], "mytext")." ) ";
        }

        $sql = "SELECT meter_type, COUNT(EM.meter_id) Meter
                FROM installed_point_enclosure IPE
                INNER JOIN enclosure_meters EM ON IPE.enclosure_id = EM.enclosure_id
                INNER JOIN meter M ON EM.meter_id = M.meter_id
                INNER JOIN meter_type MT ON M.meter_type_id = MT.meter_type_id
                INNER JOIN (
                    SELECT distinct feeder_id, station_id, transformer_id, p.point_id
                    FROM line_points p
                    LEFT JOIN point_line l ON l.line_id = p.line_id
                    LEFT JOIN service_point on service_point.point_id = l.transformer_id
                    WHERE is_service_point = 1
                    $where
                ) t ON t.point_id = IPE.point_id
                GROUP BY meter_type

                UNION ALL

                SELECT 'Gateway' type, count(E.gateway_id) Installed_Meter
                FROM installed_point_enclosure IPE
                INNER JOIN enclosure E ON IPE.enclosure_id = E.enclosure_id
                INNER JOIN service_point SP ON IPE.point_id = SP.point_id
                INNER JOIN (
                    SELECT distinct feeder_id, station_id, transformer_id, p.point_id
                    FROM line_points p
                    LEFT JOIN point_line l ON l.line_id = p.line_id
                    LEFT JOIN service_point on service_point.point_id = l.transformer_id
                    WHERE is_service_point = 1
                    $where
                ) t ON t.point_id = SP.point_id
                HAVING  count(E.gateway_id) > 0";

        $result = $this->db->SelectData($sql);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetInstallationEnclosureSummary($condition = null)
    {
        $Where = "";
        if(isset($condition["t.station_id"])){
            $Where = " WHERE t.station_id = ".$this->db->SqlVal($condition["t.station_id"], "int");
        }
        if(isset($condition["t.feeder_id"])){
            $Where = " WHERE t.feeder_id = ".$this->db->SqlVal($condition["t.feeder_id"], "int");
        }
        if(isset($condition["transformer_id"])){
            $Where = " WHERE t.transformer_id IN ( ".$this->db->SqlVal($condition["transformer_id"], "mytext")." ) ";
        }

        $sql = "SELECT enclosure_type, COUNT(installed_point_enclosure.enclosure_id) enclosure_count
                FROM installed_point_enclosure
                INNER JOIN enclosure ON enclosure.enclosure_id = installed_point_enclosure.enclosure_id
                INNER JOIN enclosure_config on enclosure_config.enclosure_config_id = enclosure.enclosure_configuration_id
                INNER JOIN enclosure_type ON enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id
                INNER JOIN service_point ON installed_point_enclosure.point_id = service_point.point_id
                -- LEFT JOIN line_points p ON installed_point_enclosure.point_id = p.point_id
                -- LEFT JOIN point_line l ON l.line_id = p.line_id
                LEFT JOIN (
                    SELECT distinct feeder_id, station_id, transformer_id, p.point_id
                    FROM line_points p
                    LEFT JOIN point_line l ON l.line_id = p.line_id
                    LEFT JOIN service_point on service_point.point_id = l.transformer_id
                    WHERE is_service_point = 1
                ) t ON t.point_id = service_point.point_id
                $Where
                GROUP BY enclosure_type
                ";
        $result = $this->db->SelectData($sql);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetServicePointGatewaySummary($condition = null)
    {
        $params=null;
        $sql="SELECT needs_gateway, count(service_point.point_id) point_coint
                FROM service_point
                INNER JOIN line_points lp ON lp.point_id = service_point.point_id
                INNER JOIN point_line pl ON lp.line_id = pl.line_id
                WHERE pl.transformer_id = ?
                GROUP BY needs_gateway";
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray($condition);
        }

        $order=array();
        $order["needs_gateway"]="ASC";

        $result = $this->db->SelectData($sql, $params, $order);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetInstallationArea()
    {
        $sql = "SELECT NODE_ID, NODE_NAME
                FROM DIR_TREE
                WHERE PARENT_ID = 1
                AND STATUS_ID = 1";

        $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetStationByArea($AreaID = NULL)
    {
        $sql = "SELECT DISTINCT station.station_id station_id, station
                FROM
                station";
        if( $AreaID ){
            $sql .= " WHERE area_id = ".$this->db->SqlVal($AreaID, "int");
        }
        $sql .= " ORDER BY station";

        $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetInstallationProblemArr($installation_problem_id = NULL)
    {
        $params = array();
        $sql = "SELECT installation_problem_id, installation_problem
                FROM
                installation_problem";
        if($installation_problem_id > 0){
            $sql .= " WHERE installation_problem_id = ?";
            $params = $this->db->ConvertToParamsArray(array("installation_problem_id"=>$installation_problem_id));
        }

        $result = $this->db->SelectData($sql, $params, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetFeederByStation($StationID = NULL)
    {
        $sql = "SELECT DISTINCT feeder_id, feeder
                FROM
                feeder";
        if( $StationID ){
            $sql .= " WHERE station_id = ".$this->db->SqlVal($StationID, "int");
        }
        $sql .= " ORDER BY feeder";

        $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function getInstallationSummaryDashboard($condition = null)
    {
        $filter = array();
        $sql = "SELECT
            COUNT(enclosure.enclosure_id) enclosure_count,
            SUM(CASE WHEN enclosure_shape.enclosure_shape_id=1 THEN 1 ELSE 0 END) as small_enclosure,
            SUM(CASE WHEN enclosure_shape.enclosure_shape_id=2 THEN 1 ELSE 0 END) as big_enclosure,
            SUM(enclosure_meter_count) as meter_count,
            SUM(CASE WHEN enclosure_meter.meter_type_id = 1 THEN enclosure_meter_count ELSE 0 END) as single_phase,
            SUM(CASE WHEN enclosure_meter.meter_type_id = 2 THEN enclosure_meter_count ELSE 0 END) as three_phase,
            SUM(CASE WHEN enclosure_meter.meter_type_id = 3 THEN enclosure_meter_count ELSE 0 END) as ct,
            COUNT(gateway_id) as gateways
            FROM installed_point_enclosure
            INNER JOIN enclosure ON enclosure.enclosure_id = installed_point_enclosure.enclosure_id
            INNER JOIN service_point ON enclosure.transformer_id = service_point.point_id
            INNER JOIN AREA_TREE ON service_point.area_id = AREA_TREE.NODE_ID
            INNER JOIN enclosure_config ON enclosure.enclosure_configuration_id = enclosure_config.enclosure_config_id
            INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = enclosure_config.enclosure_type_id
            INNER JOIN enclosure_shape ON enclosure_shape.enclosure_shape_id = enclosure_type.enclosure_shape_id
            LEFT JOIN
            (
                    SELECT enclosure_id, meter.meter_type_id, COUNT(enclosure_meters.meter_id) enclosure_meter_count
                    FROM enclosure_meters
                    INNER JOIN meter ON enclosure_meters.meter_id = meter.meter_id
                    INNER JOIN meter_type ON meter_type.meter_type_id = meter.meter_type_id
                    GROUP BY enclosure_id, meter.meter_type_id
            ) enclosure_meter ON enclosure.enclosure_id = enclosure_meter.enclosure_id
            WHERE ( NODE_PATH LIKE ? OR NODE_PATH LIKE ? )";

        $filter = array($condition["area_path"],$condition["area_path"].'.%');

        if( isset($condition["from_date"]) && isset($condition["to_date"]) ) {
            $sql .= " AND DATE(installed_point_enclosure.timestamp) >= ? AND DATE(installed_point_enclosure.timestamp) <= ?";
            $filter["from_date"] = $condition["from_date"];
            $filter["to_date"] = $condition["to_date"];
            unset($condition["from_date"]);
            unset($condition["to_date"]);
        }

        if( isset($condition["station_id"]) ) {
            $sql .= " AND station_id = ?";
            $filter["station_id"] = $condition["station_id"];
        }

        if( isset($condition["feeder_id"]) ) {
            $sql .= " AND feeder_id = ?";
            $filter["feeder_id"] = $condition["feeder_id"];
        }

        // print $sql;
        // print_r($filter);

        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetPointInclosures($condition){
        $filter = array();
        $sql = "SELECT DISTINCT installed_point_enclosure.`timestamp` installed_time, station, feeder, NODE_NAME, sp.point_id, sp.latitude, sp.longitude, t.transformer_number,
                    enclosure.enclosure_id , enclosure_type, enclosure_sn , gateway_sn,
                    Meter1, Meter2, Meter3, Meter4, Meter5, Meter6,
                    CONCAT(t.transformer_code, t.transformer_gov_number) AS transformer_generated_number

                FROM service_point sp
                LEFT JOIN installed_point_enclosure ON sp.point_id = installed_point_enclosure.point_id
                LEFT JOIN enclosure ON enclosure.enclosure_id = installed_point_enclosure.enclosure_id
                LEFT JOIN enclosure_config ON enclosure.enclosure_configuration_id = enclosure_config.enclosure_config_id
                LEFT JOIN enclosure_type ON enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                LEFT JOIN	(
                    SELECT lp.point_id, transformer_id
                    FROM line_points p
                    INNER JOIN point_line l ON l.line_id = p.line_id
                    INNER JOIN (
                        SELECT MIN(line_id) as line_id, point_id FROM line_points WHERE is_service_point = 1 group by point_id
                    ) lp on lp.point_id = p.point_id and lp.line_id = p.line_id
                    WHERE is_service_point = 1
                ) line ON line.point_id = sp.point_id
                LEFT JOIN service_point t ON t.point_id = line.transformer_id
                LEFT JOIN station ON t.station_id = station.station_id
                LEFT JOIN feeder ON t.feeder_id = feeder.feeder_id
                LEFT JOIN AREA_TREE ON t.area_id = AREA_TREE.NODE_ID
                LEFT JOIN
                (
                    SELECT enclosure_id, ICCID,
                    MAX(CASE WHEN meter_sequence = 1 THEN meter_sn ELSE '' END) Meter1,
                    MAX(CASE WHEN meter_sequence = 2 THEN meter_sn ELSE '' END) Meter2,
                    MAX(CASE WHEN meter_sequence = 3 THEN meter_sn ELSE '' END) Meter3,
                    MAX(CASE WHEN meter_sequence = 4 THEN meter_sn ELSE '' END) Meter4,
                    MAX(CASE WHEN meter_sequence = 5 THEN meter_sn ELSE '' END) Meter5,
                    MAX(CASE WHEN meter_sequence = 6 THEN meter_sn ELSE '' END) Meter6
                    FROM enclosure_meters
                    INNER JOIN meter m ON m.meter_id = enclosure_meters.meter_id
                    GROUP BY enclosure_id, ICCID
                ) meters ON enclosure.enclosure_id = meters.enclosure_id
                WHERE sp.point_id = ?";

        $filter["point_id"] = $condition["point_id"];
        // print $sql;
        // print_r($filter);
        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }
}
