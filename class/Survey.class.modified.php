<?php
require_once (realpath(dirname(__FILE__)) . '/MysqliDB.php');

/**
 * @access public
 * @package EMPLOYEE
 */
class Survey
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
        $this->db = new MysqliDB();
        $this->dir_path = $access_dir_path;
    }

    public function __destruct()
    {
        $this->db = null;
    }

    public function GetTransformerCoding($area_id)
    {
        $sql = "SELECT NODE_PATH FROM AREA_TREE WHERE NODE_ID=?";
        $filter = array($area_id);
        $params = $this->db->ConvertToParamsArray($filter);
        $area_path = $this->db->SelectValue($sql, $params);
        if (!$area_path) {
            print 'invalid area';
            return false;
        } else {
            print $directorate = substr($area_path, 0, 3);
            $code = null;
            switch($directorate) {
                case '1.2':
                    $code = 'E';
                    break;
                case '1.3':
                    $code = 'S';
                    break;
                case '1.4':
                    $code = 'D';
                    break;
                case '1.5':
                    $code = 'G';
                    break;
                default:
                    $code = null;
            }

            if ($code == null) {
                print 'invalid gov';
                return false;
            } else {
                $sql = 'SELECT MAX(transformer_gov_number) FROM service_point WHERE transformer_code=?';
                $filter = array($code);
                $params = $this->db->ConvertToParamsArray($filter);
                $number = $this->db->SelectValue($sql, $params);
                if (!$number) {
                    print 'invalid number';
                    return false;
                } else {
                    return ["code"=>$code, "number"=>$number+1];
                }
            }
        }
    }

    public function AddServicePoint($service_point_data){
        $error = false;
        $this->db->BeginTransaction();
        if ($service_point_data!=null) {

            $data = array();
            // if isset point_id delete old point and insert new point
            if($service_point_data['point_id'] > 0 ){
                $condition['point_id'] = $this->db->SqlVal($service_point_data['point_id'], "int");
                if (!$this->db->Delete("service_point", $condition)) {
                    $error=true;
                }
                $data['point_id']= $this->db->SqlVal($service_point_data["point_id"], "int");
            }

            // check already we have same GPS point
            $filter = array();
            $filter["area_id"] = $this->db->SqlVal($service_point_data["area_id"], "int");
            $filter["latitude"] = $this->db->SqlVal($service_point_data["latitude"], "mytext");
            $filter["longitude"] = $this->db->SqlVal($service_point_data["longitude"], "mytext");
            $same_gps_point = $this->CheckSameGpsPoint($filter);
            if ( $same_gps_point > 0 ) {
                $error=true;
            }


            $data['point_type_id']= $this->db->SqlVal($service_point_data["type_id"], "int");
            $data['single_phase_consumers']= $this->db->SqlVal($service_point_data["single_phase"], "int");
            $data['three_phase_consumers']= $this->db->SqlVal($service_point_data["three_phase"], "int");
            $data['accuracy_id']=$this->db->SqlVal($service_point_data["accuracy"],"int");
            $data['longitude']= $this->db->SqlVal($service_point_data["longitude"], "mytext");
            $data['latitude']=$this->db->SqlVal($service_point_data["latitude"],"mytext");
            $data['x']= $this->db->SqlVal($service_point_data["x"], "mytext");
            $data['y']=$this->db->SqlVal($service_point_data["y"],"mytext");
            $data['area_id']= $this->db->SqlVal($service_point_data["area_id"], "int");
            $data['user_id']=$this->db->SqlVal($service_point_data["user_id"],"int");
            $data['timestamp']= date("Y-m-d H:i:s");

            //$data['station_id']=$this->db->SqlVal($service_point_data["station_id"],"int");
            $data['feeder_id']=$this->db->SqlVal($service_point_data["feeder_id"],"int");
            $data['capacity_id']=$this->db->SqlVal($service_point_data["capacity_id"],"int");
            $data['transformer_number']=$this->db->SqlVal($service_point_data["transformer_number"],"mytext");

            $data['transformer_type_id']=$this->db->SqlVal($service_point_data["transformer_type_id"],"int");
            $data['transformer_privacy_id']=$this->db->SqlVal($service_point_data["transformer_privacy_id"],"int");

            if (isset($service_point_data["not_from_survey"]) && $service_point_data["not_from_survey"] != null) {
                $data['not_from_survey']=$this->db->SqlVal($service_point_data["not_from_survey"],"int");
            }

            //this section is for assigning code to trnasformer
            $transformer_code = null;
            $transformer_gov_number = null;
            if ($service_point_data["type_id"] == 4) {
                $transformer_coding = $this->GetTransformerCoding($service_point_data["area_id"]);
                if (!$transformer_coding) {
                    $error = true;
                } else {
                    $transformer_code = $transformer_coding["code"];
                    $transformer_gov_number = $transformer_coding["number"];
                }
            }
            $data['transformer_code']=$this->db->SqlVal($transformer_code,"mytext");
            $data['transformer_gov_number']=$this->db->SqlVal($transformer_gov_number,"int");
            ////

            $result = $this->db->Insert("service_point", $data, true);
            if (!$result) {
                $error = true;
            }
        }

        if ($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "service_point_insert_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "service_point_insert_success";
            return true;
        }
    }

    public function EditServicePoint($service_point_data){



        $data = array();
        if(isset($service_point_data["point_type_id"]))              $data['point_type_id']= $this->db->SqlVal($service_point_data["type_id"], "int");
        if(isset($service_point_data["single_phase_consumers"]))     $data['single_phase_consumers']= $this->db->SqlVal($service_point_data["single_phase"], "int");
        if(isset($service_point_data["three_phase_consumers"]))      $data['three_phase_consumers']= $this->db->SqlVal($service_point_data["three_phase"], "int");
        if(isset($service_point_data["accuracy_id"]))                $data['accuracy_id']=$this->db->SqlVal($service_point_data["accuracy"],"int");
        if(isset($service_point_data["longitude"]))                  $data['longitude']= $this->db->SqlVal($service_point_data["longitude"], "mytext");
        if(isset($service_point_data["latitude"]))                   $data['latitude']=$this->db->SqlVal($service_point_data["latitude"],"mytext");
        if(isset($service_point_data["area_id"]))                    $data['area_id']= $this->db->SqlVal($service_point_data["area_id"], "int");
        if(isset($service_point_data["user_id"]))                    $data['user_id']=$this->db->SqlVal($service_point_data["user_id"],"int");
        if(isset($service_point_data["timestamp"]))                  $data['timestamp']= date("Y-m-d H:i:s");

        //if(isset($service_point_data["station_id"]))                 $data['station_id']=$this->db->SqlVal($service_point_data["station_id"],"int");
        if(isset($service_point_data["feeder_id"]))                  $data['feeder_id']=$this->db->SqlVal($service_point_data["feeder_id"],"int");
        if(isset($service_point_data["capacity_id"]))                $data['capacity_id']=$this->db->SqlVal($service_point_data["capacity_id"],"int");
        if(isset($service_point_data["transformer_number"]))         $data['transformer_number']=$this->db->SqlVal($service_point_data["transformer_number"],"mytext");
        if(isset($service_point_data["transformer_type_id"]))         $data['transformer_type_id']=$this->db->SqlVal($service_point_data["transformer_type_id"],"mytext");
        if(isset($service_point_data["transformer_privacy_id"]))      $data['transformer_privacy_id']=$this->db->SqlVal($service_point_data["transformer_privacy_id"],"mytext");

        if(isset($service_point_data["needs_gateway"]))              $data['needs_gateway']=$this->db->SqlVal($service_point_data["needs_gateway"],"mytext");


        //get previus state
        $sql = "SELECT * FROM service_point WHERE point_id=?";
        $filter = array($service_point_data['point_id']);
        $params = $this->db->ConvertToParamsArray($filter);
        $point = $this->db->SelectData($sql, $params);

        if (isset($service_point_data["type_id"])) {
            if ($service_point_data["type_id"] != 4) {
                //if current type is not transformer but previous was transformer clear transformer coding, otherwise ignore
                if ($point[0]["point_type_id"] != $service_point_data["type_id"]) {
                    $data['transformer_code']=null;
                    $data['transformer_gov_number']=null;
                }
            } else {
                //if previous type was not transformer and current is transformer set new transformer coding
                if ($point[0]["point_type_id"] != $service_point_data["type_id"]) {
                    $transformer_coding = $this->GetTransformerCoding($service_point_data["area_id"]);
                    if (!$transformer_coding) {
                        $error = true;
                    } else {
                        $transformer_code = $transformer_coding["code"];
                        $transformer_gov_number = $transformer_coding["number"];
                    }
                }
                $data['transformer_code']=$this->db->SqlVal($transformer_code,"mytext");
                $data['transformer_gov_number']=$this->db->SqlVal($transformer_gov_number,"int");
            }
        }



        $condition = array();
        $condition['point_id'] = $this->db->SqlVal($service_point_data['point_id'], "int");
        $result = $this->db->Update("service_point", $data, $condition);

        if (!$result) {//if error
            $this->State = self::ERROR;
            $this->Message = "service_point_update_failed";
            return false;
        } else {
            $this->State = self::SUCCESS;
            $this->Message = "service_point_update_success";
            return true;
        }

    }
    public function EditServicePointLocation($service_point_data){
        $data = array();
        if(isset($service_point_data["longitude"]))
            $data['longitude']= $this->db->SqlVal($service_point_data["longitude"], "mytext");
            if(isset($service_point_data["latitude"]))
                $data['latitude']=$this->db->SqlVal($service_point_data["latitude"],"mytext");
            if(isset($service_point_data["x"]))
                $data['x']=$this->db->SqlVal($service_point_data["x"],"mytext");
            if(isset($service_point_data["y"]))
                $data['y']=$this->db->SqlVal($service_point_data["y"],"mytext");
        $condition = array();
        $condition['point_id'] = $this->db->SqlVal($service_point_data['point_id'], "int");
        $result = $this->db->Update("service_point", $data, $condition);
        if (!$result) {//if error
            $this->State = self::ERROR;
            $this->Message = "service_point_update_failed";
            return false;
        } else {
            $this->State = self::SUCCESS;
            $this->Message = "service_point_update_success";
            return true;
        }
    }

    public function DeleteServicePoint($point_id)
    {
        $condition = array();
        $condition['point_id'] = $this->db->SqlVal($point_id, "int");

        if (!$this->db->Delete("service_point", $condition)) {
            $this->State = $this->db->state;
            $this->Message = "service_point_delete_failed";
            return false;
        } else {
            $this->State = self::SUCCESS;
            $this->Message = "service_point_delete_success";
            return true;
        }
    }

    public function GetServicePoint($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $params=null;
        $sql="SELECT DISTINCT service_point.point_id, service_point.point_type_id, service_point.single_phase_consumers, service_point.three_phase_consumers, service_point.accuracy_id, feeder.station_id,
                    service_point.feeder_id, service_point.capacity_id, service_point.transformer_number, service_point.transformer_type_id, service_point.transformer_privacy_id, service_point.latitude,
                    service_point.longitude, service_point.user_id, service_point.area_id, service_point.timestamp, service_point.needs_gateway,
                    point_type, NODE_NAME, COLOR, LAT_CENERT, LONG_CENERT, transformer_id,
                    f2.station_id station_id1, sp2.feeder_id feeder_id1, sp2.capacity_id capacity_id1, sp2.transformer_number transformer_number1,
                    CONCAT(service_point.transformer_code, service_point.transformer_gov_number) AS transformer_generated_number
                FROM service_point
                LEFT JOIN feeder ON feeder.feeder_id = service_point.feeder_id
                INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
                INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
                LEFT JOIN line_points p ON service_point.point_id = p.point_id AND is_service_point = 1
                LEFT JOIN point_line l ON l.line_id = p.line_id
                LEFT JOIN service_point sp2 ON sp2.point_id = l.transformer_id
                LEFT JOIN feeder f2 ON f2.feeder_id = sp2.feeder_id ";

        if(isset($condition["station_id"])){
            $condition["f2.station_id"] = $condition["station_id"];
            unset($condition["station_id"]);
        }
        if(isset($condition["feeder_id"])){
            $condition["sp2.feeder_id"] = $condition["feeder_id"];
            unset($condition["feeder_id"]);
        }

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

    public function GetGatewayServicePoint($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $Where = "";
        if(isset($condition["station_id"]) && $condition["station_id"] != "" ){
            $Where = "AND f2.station_id = ". $this->db->SqlVal($condition["station_id"], "int");
        }
        if(isset($condition["feeder_id"]) && $condition["feeder_id"] != "" ){
            $Where = "AND f2.feeder_id = ". $this->db->SqlVal($condition["feeder_id"], "int");
        }
        if(isset($condition["transformer_id"]) && $condition["transformer_id"] != "" ){
            $Where = "AND l.transformer_id = ". $this->db->SqlVal($condition["transformer_id"], "int");
        }
        $params=null;
        $sql="SELECT DISTINCT service_point.point_id, service_point.point_type_id, service_point.single_phase_consumers, service_point.three_phase_consumers, service_point.accuracy_id, feeder.station_id,
                        service_point.feeder_id, service_point.capacity_id, service_point.transformer_number, service_point.latitude, service_point.longitude, service_point.user_id, service_point.area_id, service_point.timestamp, service_point.needs_gateway,
                        point_type, NODE_NAME, COLOR, LAT_CENERT, LONG_CENERT, transformer_id,
                        f2.station_id station_id1, sp2.feeder_id feeder_id1, sp2.capacity_id capacity_id1, sp2.transformer_number transformer_number1,
                        CONCAT(service_point.transformer_code, service_point.transformer_gov_number) AS transformer_generated_number
                FROM service_point
                LEFT JOIN feeder ON feeder.feeder_id = service_point.feeder_id
                INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
                INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
                INNER JOIN line_points p ON service_point.point_id = p.point_id
                INNER JOIN point_line l ON l.line_id = p.line_id
                INNER JOIN service_point sp2 ON sp2.point_id = l.transformer_id AND is_service_point = 1
                LEFT JOIN feeder f2 ON f2.feeder_id = sp2.feeder_id
                WHERE ( service_point.single_phase_consumers > 0 OR service_point.point_type_id = 4 )
                $Where";

        /*
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }
        */

        if ($order == null) {
            $order=array();
            $order["timestamp"]="DESC";
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetServicePointGatewaySummary($condition = null)
    {
        $Where = "";
        if(isset($condition["station_id"]) && $condition["station_id"] != "" ){
            $Where = "AND f2.station_id = ". $this->db->SqlVal($condition["station_id"], "int");
        }
        if(isset($condition["feeder_id"]) && $condition["feeder_id"] != "" ){
            $Where = "AND sp2.feeder_id = ". $this->db->SqlVal($condition["feeder_id"], "int");
        }
        if(isset($condition["transformer_id"]) && $condition["transformer_id"] != "" ){
            $Where = "AND pl.transformer_id = ". $this->db->SqlVal($condition["transformer_id"], "int");
        }
        $params=null;
        $sql="SELECT IFNULL(count(DISTINCT service_point.point_id), 0) AS point_count, IFNULL(needs_gateway, 0) AS needs_gateway
                FROM service_point
                INNER JOIN line_points lp ON lp.point_id = service_point.point_id
                INNER JOIN point_line pl ON lp.line_id = pl.line_id
                LEFT JOIN (
                    SELECT point_id, service_point.feeder_id, feeder.station_id
                    FROM service_point
                    LEFT JOIN feeder ON feeder.feeder_id = service_point.feeder_id
                )	sp2 ON sp2.point_id = pl.transformer_id
                WHERE ( service_point.single_phase_consumers> 0 OR service_point.point_type_id = 4 )
                $Where
                GROUP BY needs_gateway";

        /*
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray($condition);
        }
        */

        $order=array();
        $order["needs_gateway"]="ASC";

        $result = $this->db->SelectData($sql, $params, $order);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }


    public function GetServicePointWithLines($filter = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $params=null;
        $condition = $this->db->SqlVal($filter["area_id"], "mytext"); //optional, used for subquery
        $sql="SELECT DISTINCT service_point.point_id, service_point.point_type_id, single_phase_consumers, three_phase_consumers, accuracy_id, feeder.station_id, service_point.feeder_id,
                        capacity_id, transformer_number, latitude, longitude, user_id, area_id, `timestamp`, point_type, NODE_NAME, COLOR, needs_gateway,
                        l.transformer_id, LAT_CENERT, LONG_CENERT,
                        CASE WHEN lp.in_line IS NULL THEN 0 ELSE 1 END AS in_line,
                        CASE WHEN point_used > 0 THEN 1 ELSE 0 END as point_used,
                        CONCAT(service_point.transformer_code, service_point.transformer_gov_number) AS transformer_generated_number
                FROM service_point
                LEFT JOIN feeder on feeder.feeder_id = service_point.feeder_id
                INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
                INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
                LEFT JOIN (
                    SELECT COUNT(*) as point_used, point_id FROM line_points
                    WHERE line_point_position_id = 2
                    GROUP By point_id
                ) lps ON lps.point_id = service_point.point_id
                LEFT JOIN (
                    SELECT COUNT(*) AS in_line, line_points.point_id
                    FROM line_points
                    INNER JOIN service_point p on p.point_id = line_points.point_id and p.area_id IN ( $condition )
                GROUP BY line_points.point_id
                ) lp  on lp.point_id = service_point.point_id
                LEFT JOIN line_points p ON service_point.point_id = p.point_id
                LEFT JOIN point_line l ON l.line_id = p.line_id
                WHERE area_id IN ( $condition )";
        // if ($condition) {
        //     $params = $this->db->ConvertToParamsArray($condition);
        // }
        // print_r($params);
        if ($order == null) {
            $order=array();
            $order["timestamp"]="DESC";
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    // public function GetTransformers($station_id = null, $feeder_id=null)
    // {
    //     $condition = array();
    //     $params=null;
    //     $sql = "SELECT service_point.point_id as transformer_id, CONCAT(transformer_number, ' [' ,service_point.transformer_code, service_point.transformer_gov_number, ']') AS transformer_number, station_id, feeder_id, capacity_id, latitude, longitude, user_id, area_id, timestamp
    //             FROM service_point
    //             WHERE point_type_id = 4";

    //     if ($station_id!=null) {
    //         $sql .= " AND station_id = ?";
    //         $condition[] = $station_id;
    //     }
    //     if ($feeder_id!=null) {
    //         $sql .= " AND feeder_id = ?";
    //         $condition[] = $feeder_id;
    //     }

    //     if (count($condition) > 0) {
    //         $params = $this->db->ConvertToParamsArray($condition);
    //     }

    //     $result = $this->db->SelectData($sql, $params, null, null, null, $recordsCount, 1);
    //     $this->Message = $this->db->message;
    //     $this->State = $this->db->state;
    //     return $result;
    // }

    // public function GetTransformerPoints($transformer_id)
    // {
    //     $sql = "SELECT DISTINCT service_point.point_id, service_point.point_type_id, single_phase_consumers, three_phase_consumers, accuracy_id, feeder.station_id, service_point.feeder_id,
    //                     capacity_id, transformer_number, latitude, longitude, user_id, area_id, `timestamp`, point_type, NODE_NAME, COLOR, needs_gateway,
    //                     l.transformer_id
    //             FROM point_line l
    //             INNER JOIN line_points p on l.line_id = p.line_id
    //             INNER JOIN service_point on service_point.point_id = p.point_id
    //             LEFT JOIN feeder ON feeder.feeder_id = service_point.feeder_id
    //             INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
    //             INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
    //             WHERE l.transformer_id = ?

    //             UNION
    //             SELECT service_point.point_id, service_point.point_type_id, single_phase_consumers, three_phase_consumers, accuracy_id, feeder.station_id, service_point.feeder_id,
    //                     capacity_id, transformer_number, latitude, longitude, user_id, area_id, `timestamp`, point_type, NODE_NAME, COLOR, needs_gateway, null
    //             FROM service_point
    //             LEFT JOIN feeder ON feeder.feeder_id = service_point.feeder_id
    //             INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
    //             INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
    //             WHERE not_from_survey = 1
    //             AND area_id = (SELECT area_id FROM service_point WHERE point_id = ?)
    //             AND service_point.point_id NOT IN (SELECT point_id FROM line_points)";
    //     $params = $this->db->ConvertToParamsArray([$transformer_id, $transformer_id]);
    //     $result = $this->db->SelectData($sql, $params);
    //     $this->Message = $this->db->message;
    //     $this->State = $this->db->state;
    //     return $result;
    // }

    public function GetGridPoints($feeder_id, $transformer_id=null)
    {
        $sql = "SELECT DISTINCT service_point.point_id, service_point.point_type_id, single_phase_consumers, three_phase_consumers, accuracy_id, station_id, feeder_id,
                        capacity_id, transformer_number, latitude, longitude, user_id, area_id, `timestamp`, point_type, NODE_NAME, COLOR, needs_gateway,
                        l.transformer_id
                FROM point_line l
                INNER JOIN line_points p on l.line_id = p.line_id
                INNER JOIN service_point on service_point.point_id = p.point_id
                INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
                INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
                WHERE ";
        if ($feeder_id !=null){
            $sql .= "service_point.feeder_id = ?";
            $params = $this->db->ConvertToParamsArray([$feeder_id, $feeder_id]);
        } else if ($transformer_id != null){
            $sql .= "l.transformer_id = ?";
            $params = $this->db->ConvertToParamsArray([$transformer_id, $transformer_id]);
        }

        $sql .= "UNION
                SELECT service_point.point_id, service_point.point_type_id, single_phase_consumers, three_phase_consumers, accuracy_id, station_id, feeder_id,
                        capacity_id, transformer_number, latitude, longitude, user_id, area_id, `timestamp`, point_type, NODE_NAME, COLOR, needs_gateway, null
                FROM service_point
                INNER JOIN point_type ON service_point.point_type_id = point_type.point_type_id
                INNER JOIN AREA_TREE ON AREA_TREE.NODE_ID=service_point.area_id
                WHERE not_from_survey = 1";
        if ($feeder_id !=null){
            $sql .= " AND area_id in (SELECT distinct area_id FROM service_point WHERE feeder_id = ?)";
        } else if ($transformer_id != null){
            $sql .= " AND area_id = (SELECT area_id FROM service_point WHERE point_id = ?)";
        }

        $sql .= " AND service_point.point_id NOT IN (SELECT point_id FROM line_points)";


        $result = $this->db->SelectData($sql, $params);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }


    public function GetTransformer($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $assembly_order_transformers = $this->db->SqlVal($condition["point_id"], "mytext");

        $params=null;
        $sql="SELECT point_id, point_type_id, single_phase_consumers, three_phase_consumers, accuracy_id, feeder.station_id,
                        service_point.feeder_id, capacity_id, transformer_number, latitude, longitude, user_id, area_id, timestamp, needs_gateway,
                        CONCAT(service_point.transformer_code, service_point.transformer_gov_number) AS transformer_generated_number
                FROM service_point
                LEFT JOIN feeder ON feeder.feeder_id = service_point.feeder_id
                WHERE point_id NOT IN ($assembly_order_transformers)";

        unset($condition["point_id"]);
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }

        if ($order == null) {
            $order=array();
            $order["timestamp"]="DESC";
        }

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount, 0, "AND");
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetAssemblyOrderTransformer($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $params=null;
        $sql="SELECT assembly_order_id, transformer_id FROM assembly_order_transformers";
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }


    public function GetTransformerPrivacy()
    {
        $sql = "SELECT * FROM transformer_privacy ORDER By transformer_privacy";
        $result = $this->db->SelectData($sql, null, null, null, null, $recordsCount, 1);
        return $result;
    }

    public function GetTransformerTypes()
    {
        $sql = "SELECT * FROM transformer_type ORDER By transformer_type";
        $result = $this->db->SelectData($sql, null, null, null, null, $recordsCount, 1);
        return $result;
    }


    public function GetServicePointsReport()
    {
        $sql = "SELECT single_phase_consumers+three_phase_consumers, single_phase_consumers, three_phase_consumers, count(*)
                FROM
                service_point
                GROUP BY single_phase_consumers, three_phase_consumers
                ORDER BY single_phase_consumers+three_phase_consumers";

        $result = $this->db->SelectData($sql);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetMonthSalaries($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        $point_factor = self::POINTS_FACTOR;
        $month_start = $condition["month_id"]."-01";
        $month_end = date("Y-m-t", strtotime($month_start))." 23:59:59";
        $filter = array();
        $filter["timestamp#1"] = array("Operator"=>">=","Value"=>$month_start, "Type"=>"mytext");
        $filter["timestamp#2"] = array("Operator"=>"<", "Value"=>$month_end, "Type"=>"mytext");

        $sql = "SELECT USER.NAME, points, '".$condition["month_id"]."' AS month,
                (points * $point_factor) AS salary
                FROM USER
                LEFT JOIN(
                    select user_id, count(service_point.point_id) as points
                    from service_point
                    where service_point.timestamp >= ? and service_point.timestamp <= ?
                    group by user_id
                ) t ON t.user_id = USER.USER_ID
                ";
        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetServicePointCount($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        // $condition = array();
        $params=null;
        $sql = "SELECT ( ( count(*) * single_phase_consumers ) + ( count(*) * three_phase_consumers ) ) as number_of_consumers,single_phase_consumers, three_phase_consumers, count(*) as service_point_count
                FROM service_point
                INNER JOIN AREA_TREE on AREA_TREE.NODE_ID = service_point.area_id
                ";
            $sql .= " AND (AREA_TREE.`NODE_PATH` LIKE ?
                        OR AREA_TREE.`NODE_PATH` LIKE ?
                        OR `AREA_TREE`.`NODE_ID`=?
                    )";
            $sql.="WHERE (service_point.timestamp BETWEEN  ? AND ?) group by single_phase_consumers, three_phase_consumers";

            $filter = array('%.'.$condition["area_id"].'.%','%'.$condition["area_id"].'.%',$condition["area_id"],$condition["from_date"],$condition["to_date"]);
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray($filter);
        }
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetServicePolygonsByArea($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        // $condition = array();
        $params=null;
        $sql = "SELECT *
                FROM AREA_TREE
                WHERE
                    ( NODE_PATH LIKE ? OR NODE_PATH LIKE ? )
                order by NODE_ID";

        $filter = array($condition["area_path"],$condition["area_path"].'.%');
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray($filter);
        }
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetServicePointByArea($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        // $condition = array();
        $params=null;
        $sql = "SELECT  DISTINCT service_point.point_id, service_point.point_type_id, single_phase_consumers, three_phase_consumers,
                    accuracy_id, feeder.station_id, service_point.feeder_id, capacity_id, transformer_number, latitude, longitude, user_id, area_id,
                    timestamp, needs_gateway, point_path_sequence, installation_status_id, point_type,
                    CONCAT(service_point.transformer_code, service_point.transformer_gov_number) AS transformer_generated_number
                FROM service_point
                LEFT JOIN feeder on feeder.feeder_id = service_point.feeder_id";


        // if set filter feeder_id or transformer_id
        if( isset($condition["feeder_id"]) || isset($condition["transformer_number"]) ){

            $Where = " WHERE ";

            if( isset($condition["feeder_id"]) ){
                $Where .= " t.feeder_id = ".$this->db->SqlVal($condition["feeder_id"], "int");
            }

            if( isset($condition["feeder_id"]) && isset($condition["transformer_number"]) ){
                $Where .= " AND ";
            }

            if( isset($condition["transformer_number"]) ){
                $Where .= " service_point.transformer_number = ".$this->db->SqlVal($condition["transformer_number"], "text");
            }

            $sql .= " INNER JOIN (
                        SELECT DISTINCT service_point.point_id FROM service_point
                        INNER JOIN line_points ON service_point.point_id = line_points.point_id
                        INNER JOIN point_line ON line_points.line_id = point_line.line_id
                        INNER JOIN service_point t ON point_line.transformer_id = t.point_id
                        $Where
                    ) sp2 ON service_point.point_id = sp2.point_id";
        }



        $sql .= " INNER JOIN AREA_TREE on AREA_TREE.NODE_ID = service_point.area_id
                LEFT JOIN point_type ON service_point.point_type_id = point_type.point_type_id
                WHERE
                    ( NODE_PATH LIKE ? OR NODE_PATH LIKE ? )";

                if( isset($condition["from_date"]) && isset($condition["to_date"]) ){
                    $sql .= " AND ( service_point.timestamp BETWEEN  ? AND ? )";
                }

            $sql .= "order by service_point.point_id";

        $filter = array($condition["area_path"],$condition["area_path"].'.%');

        if( isset($condition["from_date"]) && $condition["from_date"] != "" && isset($condition["to_date"]) && $condition["to_date"] != "" ){
            array_push( $filter, $condition["from_date"], $condition["to_date"] );
        }

        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray($filter);
        }
        // print $sql;
        // print_r($params);
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetServicePointSummaryByArea($condition = null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        // $condition = array();
        $params=null;
        $sql = "SELECT
                    NODE_ID, NODE_NAME, NODE_PATH, LAT_CENERT, LONG_CENERT,
                    sum(single_phase_consumers) as single_phase_consumers,
                    sum(three_phase_consumers)as three_phase_consumers,
                    count(*) as service_point_count,
                    sum(case when service_point.point_type_id = 4 then 1 else 0 end ) as transformer_number_count
                FROM service_point
                INNER JOIN AREA_TREE on AREA_TREE.NODE_ID = service_point.area_id
                LEFT JOIN point_type ON service_point.point_type_id = point_type.point_type_id ";

        // if set filter station_id or feeder_id or transformer_id
        if( isset($condition["station_id"]) || isset($condition["feeder_id"]) || isset($condition["transformer_number"]) ){

            $Where = " WHERE ";

            if( isset($condition["station_id"]) ){
                $Where .= " t.station_id = ".$this->db->SqlVal($condition["station_id"], "int");
            }

            if( isset($condition["station_id"]) && isset($condition["feeder_id"]) ){
                $Where .= " AND ";
            }

            if( isset($condition["feeder_id"]) ){
                $Where .= " t.feeder_id = ".$this->db->SqlVal($condition["feeder_id"], "int");
            }

            if( ( isset($condition["feeder_id"]) || isset($condition["station_id"]) ) && isset($condition["transformer_number"]) ){
                $Where .= " AND ";
            }

            if( isset($condition["transformer_number"]) ){
                $Where .= " service_point.transformer_number = ".$this->db->SqlVal($condition["transformer_number"], "text");
            }

            $sql .= " INNER JOIN (
                        SELECT DISTINCT service_point.point_id FROM service_point
                        INNER JOIN line_points ON service_point.point_id = line_points.point_id
                        INNER JOIN point_line ON line_points.line_id = point_line.line_id
                        INNER JOIN service_point t ON point_line.transformer_id = t.point_id
                        $Where
                    ) sp2 ON service_point.point_id = sp2.point_id";
        }

        $sql .=" WHERE
                    ( NODE_PATH LIKE ? OR NODE_PATH LIKE ? )";

        if( isset($condition["from_date"]) && isset($condition["to_date"]) ){
            $sql .= " AND ( service_point.timestamp BETWEEN  ? AND ? )";
        }

        $sql .= " GROUP BY NODE_ID, NODE_NAME, NODE_PATH, LAT_CENERT, LONG_CENERT";

        $filter = array($condition["area_path"],$condition["area_path"].'.%');

        if( isset($condition["from_date"]) && $condition["from_date"] != "" && isset($condition["to_date"]) && $condition["to_date"] != "" ){
            array_push( $filter, $condition["from_date"], $condition["to_date"] );
        }

        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray($filter);
        }

        if( $order == NULL ){
            $order = array();
            $order["NODE_PATH"] = "ASC";
        }
        // print $sql;
        // print_r($params);
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetServicePointCountByArea($area_data, $point_type = NULL, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        $params=null;
        $Node_ids = "";
        if(is_array($area_data)){
            $ids = array_map(function ($a) {
                return "?";
            }, $area_data);
            $Node_ids = implode(",", $ids);
            $params = $this->db->ConvertToParamsArray($area_data);
        }else{
            $condition = array();

            $condition['path'] = '%.'.$area_data.'.%';
            $condition['area_id'] = $this->db->SqlVal($area_data, "int");

            if( $point_type ) {
                $condition['point_type'] = $this->db->SqlVal($point_type, "int");
            }

            $params = $this->db->ConvertToParamsArray($condition);
        }

        $sql = "SELECT single_phase_consumers, three_phase_consumers, point_type, service_point.point_type_id,
                count(*) as service_point_count,
                ( ( count(*) * single_phase_consumers ) + ( count(*) * three_phase_consumers ) ) as number_of_consumers
                FROM service_point
                INNER JOIN AREA_TREE on AREA_TREE.NODE_ID = service_point.area_id
                INNER JOIN point_type on point_type.point_type_id = service_point.point_type_id";
                if(is_array($area_data)){
                    $sql.=" WHERE NODE_ID IN ($Node_ids)";
                }else{
                    $sql .= " AND (CONCAT('.', AREA_TREE.`NODE_PATH`) LIKE ?
                        OR `AREA_TREE`.`NODE_ID`= ?
                    )";
                }

        // if( $point_type ) {
        //     $sql.=" AND point_type_id = ? ";
        // } else {
        //     $sql.=" AND point_type_id != 4 ";
        // }
        $sql.= " group by single_phase_consumers, three_phase_consumers, point_type, service_point.point_type_id";
        $sql.= " order by service_point.point_type_id, number_of_consumers";

        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetStation()
    {
        $sql = "SELECT station_id, station
                FROM
                station
                ORDER BY station";

        $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetFeeder($station_id = null)
    {
        $params = array();

        $sql = "SELECT feeder_id, feeder
                FROM
                feeder";
        if($station_id > 0){
            $sql .= " WHERE station_id = ?";
            $params = $this->db->ConvertToParamsArray([$station_id]);
        }
        $sql .= " ORDER BY feeder";

        $result = $this->db->SelectData($sql, $params, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetTransformerCapacity()
    {
        $sql = "SELECT transformer_capacity_id, transformer_capacity
                FROM
                transformer_capacity
                ORDER BY transformer_capacity";

        $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetPointTransformer($point_id)
    {
        $transformer_id = null;
        $filter = array("point_id"=>$point_id);
        $params = $this->db->ConvertToParamsArray($filter);

        //check if point is transformer
        $sql = "SELECT point_id FROM service_point WHERE point_id=? AND point_type_id=4";
        $transformer_id = $this->db->SelectValue($sql, $params);

        if (!$transformer_id) {
            //get parent line transformer id
            $sql = "SELECT distinct transformer_id
                    FROM point_line l
                    INNER JOIN line_points p on l.line_id = p.line_id
                    WHERE point_id = ?
                    LIMIT 1";
            $transformer_id = $this->db->SelectValue($sql, $params);
        }
        return $transformer_id;
    }

    // public function GetTransformerLines($transformer_id)
    // {
    //     $sql = "SELECT l.line_id, p.point_id, latitude, longitude
    //             FROM point_line l
    //             left join line_points lp on lp.line_id = l.line_id
    //             left join service_point p on p.point_id = lp.point_id
    //             WHERE l.transformer_id=?
    //             order by parent_line_id, line_id, point_sequence";
    //     $params = $this->db->ConvertToParamsArray(array("transformer_id"=>$transofrmer_id));
    //     $result = $this->db->SelectData($sql, $params);
    //     return $result;
    // }

    public function GetGridLines($feeder_id, $transformer_id=null)
    {
        $sql = "SELECT l.line_id, p.point_id, latitude, longitude
                FROM point_line l
                left join line_points lp on lp.line_id = l.line_id
                left join service_point p on p.point_id = lp.point_id
                WHERE ";
        if ($feeder_id != null) {
            $sql .= "l.transformer_id in (SELECT point_id FROM service_point WHERE point_type_id=4 AND feeder_id = ?)";
            $filter = array("feeder_id"=>$feeder_id);
        } else if ($transformer_id != null) {
            $sql .= "l.transformer_id=?";
            $filter = array("transformer_id"=>$transformer_id);
        }
        $sql .= " ORDER BY parent_line_id, line_id, point_sequence";
        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetLines($area_id)
    {
        $area_id = $this->db->SqlVal($area_id, "mytext");
        $sql = "SELECT l.line_id, p.point_id, latitude, longitude
                FROM point_line l
                left join line_points lp on lp.line_id = l.line_id
                left join service_point p on p.point_id = lp.point_id
                WHERE p.area_id IN ( $area_id )
                order by parent_line_id, line_id, point_sequence";
        $params = NULL;//$this->db->ConvertToParamsArray(array("area_id"=>$area_id));
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetParentLine($transformer_id, $lastPoint)
    {
        $sql = "SELECT l.line_id
                FROM point_line l
                INNER JOIN line_points p ON p.line_id = l.line_id AND line_point_position_id = 3
                WHERE l.transformer_id = $transformer_id
                AND p.point_id = $lastPoint";
        $line_id = $this->db->SelectValue($sql);
        return $line_id;
    }

    public function ValidateLineStart($firstPoint)
    {
        $sql = "SELECT IFNULL(count(*), 0)
                FROM line_points
                WHERE line_point_position_id = 2
                AND point_id = $firstPoint";
        $result = $this->db->SelectValue($sql);
        return $result;
    }

    public function ValidateLinePoint($point_id, $transformer_id)
    {
        $sql = "SELECT IFNULL(count(*), 0)
                FROM line_points
                INNER JOIN point_line on line_points.line_id = point_line.line_id
                WHERE point_id = $point_id
                AND point_line.transformer_id = $transformer_id";
        $result = $this->db->SelectValue($sql);
        return $result;

    }

    public function ValidateLine($point_id, $position, $transformer_id)
    {
        if ($position == 1) {
            $used_points = $this->ValidateLineStart($point_id);
            if ($used_points > 0) {
                return false;
            }
        }

        if ($position != 1) {
            $used_points = $this->ValidateLinePoint($point_id, $transformer_id);
            if ($used_points > 0) {
                return false;
            }
        }

        return true;
    }

    public function CheckUsedPoint($point_id, $transformer_id)
    {
        $sql = "SELECT IFNULL(count(*), 0)
                FROM line_points
                INNER JOIN point_line on line_points.line_id = point_line.line_id
                WHERE point_id = $point_id
                AND is_service_point = 1
                AND point_line.transformer_id != $transformer_id";
        $result = $this->db->SelectValue($sql);
        if ($result == 0) {
            return false; // point not used before as service point
        } else {
            return true; // point used before as service point
        }
    }

    public function InsertLinePoint($line_id, $sequence, $point_id, $line_point_position_id, $is_service_point)
    {
        $data = array();
        $data['line_id']=$this->db->SqlVal($line_id, "int");
        $data['point_id']=$this->db->SqlVal($point_id, "int");
        $data['point_sequence']=$this->db->SqlVal($sequence, "int");
        $data['is_service_point']=$this->db->SqlVal($is_service_point, "int");
        $data['line_point_position_id']=$this->db->SqlVal($line_point_position_id, "int");
        $result = $this->db->Insert("line_points", $data);
        return $result;
    }

    public function InsertLine($transformer_id, $points)
    {
        $error = false;
        $this->db->BeginTransaction();

        //get first point of the line
        $firstPoint = $points[1][0];

        // //validate line start not used in other line
        // $used_point = $this->ValidateLineStart($firstPoint);
        // if ($used_point > 0) {
        //     $message = "line_start_found_in_other_line";
        //     $error = true;
        // }

        if (!$error) {
            //get parent line
            $parent_line_id = $this->GetParentLine($transformer_id, $firstPoint);
            if (!$parent_line_id) {
                $parent_line_id = null;
            }

            //first save the line
            $data = array();
            $data['parent_line_id']=$this->db->SqlVal($parent_line_id, "int");
            $data['starting_point_id']=$this->db->SqlVal($firstPoint, "int");
            $data['transformer_id']=$this->db->SqlVal($transformer_id, "int");
            $line_id = $this->db->Insert("point_line", $data, true);
            if (!$line_id) {
                $message = "error_saving_line";
                $error = true;
            }

            if (!$error) {
                foreach ($points as $sequence=>$point) {
                    $free_point = $this->ValidateLine($point[0], $point[1], $transformer_id);
                    if (!$free_point) {
                        $message = "line_point_found_in_other_line";
                        $error = true;
                    } else {

                        //the point if used in a different transformer
                        $point_used = $this->CheckUsedPoint($point[0], $transformer_id);
                        if ($point_used) {
                            $is_service_point = 0;
                        } else {
                            $is_service_point = 1;
                        }
                        $result2 = $this->InsertLinePoint($line_id, $sequence, $point[0], $point[1], $is_service_point);
                        if (!$result2) {
                            $error = true;
                            $message = "error_saving_points";
                            break;
                        }
                    }
                }
            }

            if ($error) {
                $this->db->RollbackTransaction();
                $this->State = self::ERROR;
                $this->Message = $message;
                return false;
            } else {
                $this->db->CommitTransaction();
                $this->State = self::SUCCESS;
                $this->Message = "line_insert_success";
                return true;
            }
        }

    }

    public function GetLineChildren($line_id)
    {
        $sql = "SELECT line_id FROM point_line WHERE parent_line_id = $line_id";
        $result = $this->db->SelectData($sql);
        return $result;
    }

    private function recursiveDelete($line_id)
    {
        $childLines = $this->GetLineChildren($line_id);
        if ($childLines && count($childLines) > 0) {
            for ($i=0; $i<count($childLines); $i++) {
                $result = $this->recursiveDelete($childLines[$i]["line_id"]);
                if (!$result) {
                    return false;
                    break;
                }
            }
        }
        $result = $this->DeleteLine($line_id);
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }

    public function DeleteLine($line_id)
    {
        $error = false;

        //delete line points
        $condition['line_id'] = $this->db->SqlVal($line_id, "int");
        $result = $this->db->Delete("line_points", $condition);
        if (!$result) {
            $error = true;
            return false;
        }

        //delete line
        if (!$error) {
            $condition['line_id'] = $this->db->SqlVal($line_id, "int");
            $result = $this->db->Delete("point_line", $condition);
            if (!$result) {
                $error=true;
            }
        }

        return !$error;
    }

    public function RemoveLine($line_id)
    {
        $error = false;
        $this->db->BeginTransaction();

        $result = $this->recursiveDelete($line_id);

        if (!$result) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "line_delete_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "line_delete_success";
            return true;
        }
    }

    public function GetTransformerArr($condition = null)
    {
        $params=null;
        $sql="SELECT service_point.point_id transformer_id, IFNULL(transformer_number, 0), ponit_count
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

    public function GetFeederByStation($StationID = NULL)
    {
        $sql = "SELECT DISTINCT feeder_id, CONCAT(feeder_id, ' - ', feeder) AS feeder
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


    public function GetStationByArea($AreaID = NULL)
    {
        $sql = "SELECT DISTINCT station.station_id station_id, CONCAT(station, ' [', station_id, ']') AS station
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

    public function GetFeeders($area_id=null, $StationID = NULL)
    {
        $sql = "SELECT DISTINCT feeder_id, CONCAT(feeder, ' [', feeder_id, ']') AS feeder
                FROM feeder
                LEFT JOIN station ON station.station_id = feeder.station_id
                WHERE 1=1";
        if( $area_id != "" ){
            $sql .= " AND station.area_id = ".$this->db->SqlVal($area_id, "int");
        }

        if( $StationID != "") {
            $sql .= " AND feeder.station_id = ".$this->db->SqlVal($StationID, "int");
        }
        $sql .= " ORDER BY feeder";

        $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetTransformers($area_id=null, $station_id = NULL, $feeder_id)
    {
        $sql = "SELECT service_point.point_id as transformer_id, CONCAT(transformer_number, ' [' ,service_point.transformer_code, service_point.transformer_gov_number, ']') AS transformer_number,
                    feeder.station_id, service_point.feeder_id, capacity_id, latitude, longitude, user_id, service_point.area_id, timestamp
                FROM service_point
                LEFT JOIN feeder on feeder.feeder_id = service_point.feeder_id
                LEFT JOIN station on station.station_id = feeder.station_id
                WHERE point_type_id = 4";

        if( $area_id != "" ){
            $sql .= " AND station.area_id = ".$this->db->SqlVal($area_id, "int");
        }

        if( $station_id != "") {
            $sql .= " AND feeder.station_id = ".$this->db->SqlVal($station_id, "int");
        }

        if( $feeder_id != "") {
            $sql .= " AND service_point.feeder_id = ".$this->db->SqlVal($feeder_id, "int");
        }
        $sql .= " ORDER BY transformer_number";

        //print $sql;
        $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
    }

    public function GetStationDetails($station_id)
    {
        $sql = "SELECT station.*, NODE_NAME as area FROM station
                LEFT JOIN DIR_TREE t ON t.NODE_ID = station.area_id
                WHERE station_id=?";
        $params = $this->db->ConvertToParamsArray(array("station_id"=>$station_id));
        return $this->db->SelectData($sql, $params);
    }

    public function GetFeederDetails($feeder_id)
    {
        $sql = "SELECT feeder.*, station, NODE_NAME as area  FROM feeder
                LEFT JOIN station on station.station_id = feeder.station_id
                LEFT JOIN DIR_TREE t ON t.NODE_ID = station.area_id
                WHERE feeder_id=?";
        $params = $this->db->ConvertToParamsArray(array("feeder_id"=>$feeder_id));
        return $this->db->SelectData($sql, $params);
    }

    public function GetTransformerDetails($transformer_id)
    {
        $sql = "SELECT service_point.point_id as transformer_id, CONCAT(transformer_number, ' [' ,service_point.transformer_code, service_point.transformer_gov_number, ']') AS transformer_number,
                    feeder.station_id, service_point.feeder_id, capacity_id, latitude, longitude, user_id, service_point.area_id,
                    station.station, feeder.feeder, NODE_NAME as area, transformer_type_id, transformer_privacy_id, transformer_number AS original_transformer_number
                FROM service_point
                LEFT JOIN feeder on feeder.feeder_id = service_point.feeder_id
                LEFT JOIN station on station.station_id = feeder.station_id
                LEFT JOIN DIR_TREE t ON t.NODE_ID = station.area_id
                WHERE point_id=?";
        $params = $this->db->ConvertToParamsArray(array("point_id"=>$transformer_id));
        return $this->db->SelectData($sql, $params);
    }


    public function CheckSameGpsPoint($filter)
    {
        $params = $this->db->ConvertToParamsArray($filter);

        $sql = "SELECT point_id FROM service_point WHERE area_id = ? AND latitude=? AND longitude = ?";
        $result = $this->db->SelectValue($sql, $params);
        return $result;
    }

    public function AddStation($area_id, $station)
    {
        $data = array();
        $data['area_id']= $this->db->SqlVal($area_id, "int");
        $data['station']= $this->db->SqlVal($station, "mytext");

        $result = $this->db->Insert("station", $data);
        return $result;

    }

    public function UpdateStation($station_id, $area_id, $station)
    {
        $data = array();
        $data['area_id']= $this->db->SqlVal($area_id, "int");
        $data['station']= $this->db->SqlVal($station, "mytext");

        $condition = array();
        $condition['station_id']= $this->db->SqlVal($station_id, "int");

        $result = $this->db->Update("station", $data, $condition);
        return $result;
    }

    public function AddFeeder($station_id, $feeder)
    {
        $data = array();
        $data['station_id']= $this->db->SqlVal($station_id, "int");
        $data['feeder']= $this->db->SqlVal($feeder, "mytext");

        $result = $this->db->Insert("feeder", $data);
        return $result;

    }

    public function UpdateFeeder($feeder_id, $station_id, $feeder)
    {
        $data = array();
        $data['station_id']= $this->db->SqlVal($station_id, "int");
        $data['feeder']= $this->db->SqlVal($feeder, "mytext");

        $condition = array();
        $condition['feeder_id']= $this->db->SqlVal($feeder_id, "int");

        $result = $this->db->Update("feeder", $data, $condition);
        return $result;

        // $data = array();
        // $data['station_id']= $this->db->SqlVal($station_id, "int");
        // $condition = array();
        // $condition['feeder_id']= $this->db->SqlVal($feeder_id, "int");
        // $condition['piont_type_id']= 4;
        // $result = $this->db->Update("feeder", $data, $condition);
    }

    // public function UpdateFeederStation($feeder_id, $station_id)
    // {
    //     $data = array();
    //     $data['station_id']= $this->db->SqlVal($station_id, "int");

    //     $condition = array();
    //     $condition['feeder_id']= $this->db->SqlVal($feeder_id, "int");

    //     $result = $this->db->Update("feeder", $data, $condition);
    //     return $result;
    // }

    public function UpdateTransformerFeeder($transformer_id, $transformer)
    {
        $data = array();
        if (isset($transformer['station_id']) && $transformer['station_id'] != null) {
            $data['station_id']= $this->db->SqlVal($transformer['station_id'], "int");
        }
        $data['feeder_id']= $this->db->SqlVal($transformer['feeder_id'], "int");
        $data['capacity_id']= $this->db->SqlVal($transformer['capacity_id'], "int");
        $data['transformer_number']= $this->db->SqlVal($transformer['transformer_number'], "mytext");
        $data['transformer_type_id']= $this->db->SqlVal($transformer['transformer_type_id'], "int");
        $data['transformer_privacy_id']= $this->db->SqlVal($transformer['transformer_privacy_id'], "int");

        $condition = array();
        $condition['point_id']= $this->db->SqlVal($transformer_id, "int");

        $result = $this->db->Update("service_point", $data, $condition);
        return $result;
    }

}
