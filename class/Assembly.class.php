<?php
require_once (realpath(dirname(__FILE__)) . '/MysqliDB.php');

/**
 * @access public
 */
class Assembly
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

    public function GetAssemblyOrder($assembly_order_id)
    {
        $sql = "SELECT
                    assembly_order.assembly_order_id,
                    assembly_order_code,
                    create_date,
                    start_date,
                    assembly_order.user_id,
                    status_id,
                    assembly_order.area_id,
                    notes,
                    USER.NAME as user_name,
                    station.station_id, station, feeder.feeder_id, feeder,
                    transformer_number,
                    is_extra_stock
                FROM assembly_order
                INNER JOIN USER on USER.USER_ID = assembly_order.user_id
                LEFT JOIN assembly_order_transformers t on t.assembly_order_id = assembly_order.assembly_order_id
                LEFT JOIN service_point on service_point.point_id = t.transformer_id
                LEFT JOIN station on station.station_id = service_point.station_id
                LEFT JOIN feeder on feeder.feeder_id = service_point.feeder_id
                WHERE assembly_order.assembly_order_id = ?";
        $params = $this->db->ConvertToParamsArray([$assembly_order_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetAssemblyOrderList($condition= null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        $params =null;
        $condition_start = null;
        if(isset($condition["completeness"])) {
            if(isset($condition["completeness"]) && $condition["completeness"] == 2){
                $completeness = ' WHERE B.manufactured_count < A.enclosures_count';
            } else if(isset($condition["completeness"]) && $condition["completeness"] == 3){
                $completeness = ' WHERE B.manufactured_count >= A.enclosures_count';
            }
            unset($condition["completeness"]);
            $condition_start = "AND";
        }

        $sql = "SELECT assembly_order.assembly_order_id, assembly_order_code, create_date, start_date, assembly_order.user_id, status_id, area_id, station_id, feeder_id, notes,
                        `NAME`, IFNULL(enclosures_count, 0) enclosures_count, IFNULL(manufactured_count, 0) manufactured_count, simcard_status
                FROM assembly_order
                INNER JOIN `USER` on USER.USER_ID = assembly_order.user_id
                LEFT JOIN (
                    SELECT SUM(enclosure_count) AS enclosures_count, assembly_order_id
                    FROM assembly_order_configuration
                    GROUP BY assembly_order_id
                ) A ON A.assembly_order_id = assembly_order.assembly_order_id

                LEFT JOIN (
                    SELECT count(*) AS manufactured_count, assembly_order_id
                    FROM enclosure
                    INNER JOIN enclosure_config on enclosure_config.enclosure_config_id = enclosure.enclosure_configuration_id
                    GROUP BY assembly_order_id
                ) B ON B.assembly_order_id = assembly_order.assembly_order_id
                LEFT JOIN simcard_status ON simcard_status.simcard_status_id = assembly_order.simcard_status_id
                $completeness";
        if($order == null){
            $order = array("create_date" => "DESC");
        }
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray(null, $condition);
        }
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount, null, $condition_start);
        return $result;
    }

    public function getAssemblyOrders()
    {
        $sql = "SELECT assembly_order.*, USER.NAME as user_name FROM assembly_order
            INNER JOIN USER on USER.USER_ID = assembly_order.user_id
            WHERE assembly_order.status_id = 1
            ORDER By start_date, create_date, assembly_order_id DESC";
        $result = $this->db->SelectData($sql);
        return $result;
    }

    public function getActiveAssemblyOrders($assembly_order_id=null)
    {
        $conditionStr = "";
        if ($assembly_order_id != null) {
            $filter = ["1"=>$assembly_order_id, "2"=>$assembly_order_id, "3"=>$assembly_order_id];
            $conditionStr = " AND assembly_order.assembly_order_id=? " ;
        }
        $sql = "SELECT assembly_order.*, USER.NAME as user_name,
                    IFNULL(required_count, 0) as required_count, IFNULL(manufactured_count, 0) as manufactured_count
            FROM assembly_order
            INNER JOIN USER on USER.USER_ID = assembly_order.user_id
            LEFT JOIN (
                SELECt SUM(enclosure_count) AS required_count, assembly_order_items.assembly_order_id
                FROM assembly_order_items
                INNER JOIN assembly_order on assembly_order.assembly_order_id = assembly_order_items.assembly_order_id
                WHERE assembly_order.status_id =1
                $conditionStr
                GROUP BY assembly_order_items.assembly_order_id
            ) req ON req.assembly_order_id = assembly_order.assembly_order_id
            LEFT JOIN (
                SELECt COUNT(*) AS manufactured_count, enclosure.assembly_order_id
                FROM enclosure
                INNER JOIN assembly_order on assembly_order.assembly_order_id = enclosure.assembly_order_id
                WHERE assembly_order.status_id =1
                $conditionStr
                GROUP BY enclosure.assembly_order_id
            ) e ON e.assembly_order_id = assembly_order.assembly_order_id
            WHERE assembly_order.status_id =1
            $conditionStr
            ORDER By start_date, create_date, assembly_order_id DESC";
        if ($assembly_order_id != null) {
            $params = $this->db->ConvertToParamsArray($filter);
            $result = $this->db->SelectData($sql, $params);
        } else {
            $result = $this->db->SelectData($sql);
        }

        return $result;
    }

    public function getActiveAssemblyOrdersArr()
    {
        $sql = "SELECT assembly_order.*, USER.NAME as user_name,
                    IFNULL(required_count, 0) as required_count, IFNULL(manufactured_count, 0) as manufactured_count
            FROM assembly_order
            INNER JOIN USER on USER.USER_ID = assembly_order.user_id
            LEFT JOIN (
                SELECt SUM(enclosure_count) AS required_count, assembly_order_items.assembly_order_id
                FROM assembly_order_items
                INNER JOIN assembly_order on assembly_order.assembly_order_id = assembly_order_items.assembly_order_id
                WHERE assembly_order.status_id =1
                GROUP BY assembly_order_items.assembly_order_id
            ) req ON req.assembly_order_id = assembly_order.assembly_order_id
            LEFT JOIN (
                SELECt COUNT(*) AS manufactured_count, enclosure.assembly_order_id -- , enclosure_type_id
                FROM enclosure
                INNER JOIN assembly_order on assembly_order.assembly_order_id = enclosure.assembly_order_id
                WHERE assembly_order.status_id =1
                GROUP BY enclosure.assembly_order_id
            ) e ON e.assembly_order_id = assembly_order.assembly_order_id
            WHERE assembly_order.status_id =1
            ORDER By start_date, create_date, assembly_order_id DESC";
            $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);

            return $result;
    }

    public function getAssemblyOrdersTransformerArr($assembly_order_id)
    {
        $sql = "SELECT transformer_id, transformer_number
                FROM assembly_order_transformers
                INNER JOIN service_point ON assembly_order_transformers.transformer_id = service_point.point_id
                WHERE assembly_order_id = ?
                ORDER By transformer_number";
        $params = $this->db->ConvertToParamsArray([$assembly_order_id]);
        $result = $this->db->SelectData($sql, $params, NULL, NULL, NULL, $recordsCount, 1);
        return $result;
    }

    public function AddAssemlyOrder($assemly_order_data)
    {
        $error = false;
        $this->db->BeginTransaction();

        $data = array();
        $data['area_id']= $this->db->SqlVal($assemly_order_data["area_id"], "int");
        $data['station_id']= $this->db->SqlVal($assemly_order_data["station_id"], "int");
        $data['feeder_id']= $this->db->SqlVal($assemly_order_data["feeder_id"], "int");
        $data['start_date']=$this->db->SqlVal($assemly_order_data["start_date"], "text");
        $data['notes']= $this->db->SqlVal($assemly_order_data["notes"], "mytext");
        $data['create_date']= $this->db->SqlVal($assemly_order_data["create_date"], "mytext");
        $data['user_id']= $this->db->SqlVal($assemly_order_data["user_id"], "int");
        //insert order
        $assemly_order_id = $this->db->Insert("assembly_order", $data, true);


        if ($assemly_order_id) {
            $TrnasformersArr = $assemly_order_data["trnasformers"];
            if ($TrnasformersArr) {
                // insert into assembly_order_transformers
                $values = array();
                for ($i=0; $i<count($TrnasformersArr); $i++) {
                    $tdata = array();
                    $tdata['assembly_order_id']=$assemly_order_id;
                    $tdata['transformer_id']=$this->db->SqlVal($TrnasformersArr[$i], "int");
                    $result = $this->db->Insert("assembly_order_transformers", $tdata);
                    if (!$result) {
                        $error = true;
                        break;
                    }
                }

                // insert into assembly_order_items
                if (!$error) {
                    $transformerIds = implode(",", $TrnasformersArr);
                    // $Insert2 = "INSERT INTO assembly_order_items (assembly_order_id, enclosure_type_id, enclosure_count, is_extra)
                    //         SELECT $assemly_order_id, enclosure_type_id, enclosures_count, 0
                    //         FROM (
                    //             SELECT $assemly_order_id, enclosure_type.enclosure_type_id, COUNT(*) as enclosures_count
                    //             FROM service_point_enclosure_type
                    //             INNER JOIN service_point on service_point.point_id = service_point_enclosure_type.point_id
                    //             INNER JOIN enclosure_type on enclosure_type.enclosure_type_id = service_point_enclosure_type.enclosure_type_id
                    //             INNER JOIN line_points ON line_points.point_id = service_point.point_id
                    //             INNER JOIN point_line ON point_line.line_id = line_points.line_id
                    //             WHERE point_line.transformer_id in  ($transformerIds)
                    //             GROUP BY enclosure_type.enclosure_type_id
                    //         ) t";
                    $Insert2 = "INSERT INTO assembly_order_items (assembly_order_id, enclosure_type_id, enclosure_count, is_extra, transformer_id)
                                SELECT $assemly_order_id, enclosure_type_id, enclosures_count, 0, transformer_id
                                FROM (
                                    SELECT enclosure_type.enclosure_type_id, SUM(enclosure_count) as enclosures_count, l.transformer_id transformer_id
                                    FROM service_point_enclosure_type
                                    INNER JOIN (
                                        SELECT DISTINCT point_id, transformer_id
                                        FROM line_points
                                        INNER JOIN point_line ON  point_line.line_id = line_points.line_id
                                        WHERE line_points.is_service_point = 1
                                    ) l ON service_point_enclosure_type.point_id = l.point_id
                                    INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = service_point_enclosure_type.enclosure_type_id
                                    WHERE l.transformer_id IN ($transformerIds)
                                    GROUP BY enclosure_type.enclosure_type_id, l.transformer_id
                                ) t";
                    $assembly_order_items = $this->db->Execute($Insert2, null);
                    if (!$assembly_order_items) {
                        $error = true;
                    }
                }

                //insert order configuarations
                if (!$error) {
                    $Insert3 = "INSERT INTO assembly_order_configuration (assembly_order_id, enclosure_config_id, transformer_id, enclosure_count)
                                SELECT * FROM (
                                    SELECT i.assembly_order_id, enclosure_config_id, transformer_id,
                                        FLOOR(enclosure_count/configs)+
                                        CASE
                                            WHEN mod(enclosure_count, configs) = 0 THEN 0
                                            ELSE if( mod(enclosure_count, configs) % 2 = 0, even_remainder_factor, odd_remainder_factor) + mod(enclosure_count, configs)
                                        END AS enclosure_config_count
                                    FROM assembly_order_items i
                                    INNER JOIN enclosure_type e on e.enclosure_type_id = i.enclosure_type_id
                                    INNER JOIN enclosure_config c on c.enclosure_type_id = i.enclosure_type_id
                                    INNER JOIN (
                                        SELECT count(*) configs, enclosure_type_id FROM enclosure_config GROUP BY enclosure_type_id
                                    ) cc on cc.enclosure_type_id = i.enclosure_type_id
                                    WHERE i.assembly_order_id = $assemly_order_id
                                ) t
                                WHERE enclosure_config_count > 0";
                    $result = $this->db->Execute($Insert3, null);
                    if (!$result) {
                        $error = true;
                    }
                }

            } else {
                $error = true;
            }
        } else {
            $error = true;
        }

        if ($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "assemly_order_insert_failed";
            //return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "assemly_order_insert_success";
        }
        return $assemly_order_id;
    }

    public function UpdateAssemlyOrder($assemly_order_data)
    {
        $data = array();
        //if(isset($assemly_order_data["area_id"])) $data['area_id']= $this->db->SqlVal($assemly_order_data["area_id"], "int");
        //if(isset($assemly_order_data["station_id"])) $data['station_id']= $this->db->SqlVal($assemly_order_data["station_id"], "int");
        //if(isset($assemly_order_data["feeder_id"])) $data['feeder_id']= $this->db->SqlVal($assemly_order_data["feeder_id"], "int");
        if(isset($assemly_order_data["start_date"])) $data['start_date']=$this->db->SqlVal($assemly_order_data["start_date"], "mytext");
        if(isset($assemly_order_data["notes"])) $data['notes']= $this->db->SqlVal($assemly_order_data["notes"], "mytext");
        if(isset($assemly_order_data["assembly_order_code"])) $data['assembly_order_code']= $this->db->SqlVal($assemly_order_data["assembly_order_code"], "mytext");
        if(isset($assemly_order_data["status_id"])) $data['status_id']= $this->db->SqlVal($assemly_order_data["status_id"], "int");

        $condition = array();
        $condition['assembly_order_id']= $this->db->SqlVal($assemly_order_data["assembly_order_id"], "int");
        $result = $this->db->Update("assembly_order", $data, $condition);
        return $result;
    }

    public function AddVanStock($assemly_order_data)
    {
        $error = false;
        $this->db->BeginTransaction();

        $data = array();
        $data['assembly_order_code']= $this->db->SqlVal($assemly_order_data["assembly_order_code"], "mytext");
        $data['start_date']=$this->db->SqlVal($assemly_order_data["start_date"], "mytext");
        $data['notes']= $this->db->SqlVal($assemly_order_data["notes"], "mytext");
        $data['create_date']= $this->db->SqlVal($assemly_order_data["create_date"], "mytext");
        $data['user_id']= $this->db->SqlVal($assemly_order_data["user_id"], "int");
        $data['is_extra_stock']= 1;

        $assemly_order_id = $this->db->Insert("assembly_order", $data, true);
        if (!$assemly_order_id) {
            $error = true;
        }

        if (!$error) {
            $transformer_id = $this->GetVanstockTransformerID($assemly_order_id);
            $tdata = array();
            $tdata['assembly_order_id']=$assemly_order_id;
            $tdata['transformer_id']=$this->db->SqlVal($transformer_id, "int");
            $result = $this->db->Insert("assembly_order_transformers", $tdata);
            if (!$result) {
                $error = true;
            }
        }

        if ($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "assemly_order_insert_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "assemly_order_insert_success";
            return $assemly_order_id;
        }
    }



    public function GetEnclosureTypeByConfig($enclosure_config_id)
    {
        $sql = "SELECT enclosure_type_id FROM enclosure_config WHERE enclosure_config_id = ?";
        $params = $this->db->ConvertToParamsArray([$enclosure_config_id]);
        return $this->db->SelectValue($sql, $params);
    }

    public function GetAssemblyOrdersTransformer($assembly_order_id)
    {
        $sql = "SELECT MIN(transformer_id) FROM assembly_order_transformers WHERE assembly_order_id = ?";
        $params = $this->db->ConvertToParamsArray([$assembly_order_id]);
        return $this->db->SelectValue($sql, $params);
    }


    public function AddAssemblyExtraItems($enclosure_data)
    {
        $error = false;
        $this->db->BeginTransaction();

        $enclosure_type_id = $this->GetEnclosureTypeByConfig($enclosure_data["enclosure_config_id"]);

        $assembly_order_id = $enclosure_data["assembly_order_id"];
        $transformer_id = $this->GetAssemblyOrdersTransformer($assembly_order_id);

        if (!$error) {
            // $condition = array();
            // $condition['assembly_order_id']= $this->db->SqlVal($assembly_order_id, "int");
            // $condition['enclosure_config_id']= $this->db->SqlVal($enclosure_data["enclosure_config_id"], "int");
            // $result = $this->db->Delete("assembly_order_configuration", $condition);

            $data = array();
            $data['assembly_order_id']= $this->db->SqlVal($assembly_order_id, "int");
            $data['enclosure_config_id']= $this->db->SqlVal($enclosure_data["enclosure_config_id"], "int");
            $data['enclosure_count']= $this->db->SqlVal($enclosure_data["count"], "int");
            $data['transformer_id']=$this->db->SqlVal($transformer_id, "int");
            $data['enclosure_count']= $this->db->SqlVal($enclosure_data["count"], "int");
            $result = $this->db->Insert("assembly_order_configuration", $data);
            if (!$result) {
                $error = true;
                echo "1";
            }
        }


        if (!$error) {
            /*
            $sql = "SELECT SUM(assembly_order_configuration.enclosure_count)
                    FROM assembly_order_configuration
                    inner join enclosure_config on enclosure_config.enclosure_config_id = assembly_order_configuration.enclosure_config_id
                    inner join enclosure_type on enclosure_type.enclosure_type_id = enclosure_config.enclosure_type_id
                    where assembly_order_configuration.assembly_order_id = ?
                    and  assembly_order_configuration.enclosure_config_id = ?
                    GROUP BY enclosure_type.enclosure_type_id";
            $params = $this->db->ConvertToParamsArray([$assembly_order_id, $enclosure_data["enclosure_config_id"]]);
            $new_type_count = $this->db->SelectValue($sql, $params);

            $sql = "SELECT SUM(assembly_order_items.enclosure_count)
                    FROM assembly_order_items
                    inner join enclosure_type on enclosure_type.enclosure_type_id = assembly_order_items.enclosure_type_id
                    inner join enclosure_config on enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id
                    where assembly_order_items.assembly_order_id = ?
                    and  enclosure_config.enclosure_config_id = ?
                    GROUP BY enclosure_type.enclosure_type_id";
            $params = $this->db->ConvertToParamsArray([$assembly_order_id, $enclosure_data["enclosure_config_id"]]);
            $old_type_count = $this->db->SelectValue($sql, $params);

            if (!$old_type_count) {

            }
            */

            $data = array();
            $data['assembly_order_id']= $this->db->SqlVal($enclosure_data["assembly_order_id"], "int");
            $data['enclosure_type_id']= $this->db->SqlVal($enclosure_type_id, "int");
            $data['enclosure_count']= $this->db->SqlVal($enclosure_data["count"], "int");
            $data['transformer_id']=$this->db->SqlVal($transformer_id, "int");
            $data['is_extra']= 1;
            $result = $this->db->Insert("assembly_order_items", $data);
            if (!$result) {
                $error = true;
                echo "2";
            }
        }


        if ($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "assemly_order_items_insert_failed";
            return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "assemly_order_items_insert_success";
            return true;
        }

    }

    private function GetVanstockTransformerID($assembly_order_id)
    {
        $sql = "SELECT MAX(transformer_id) FROM assembly_order_transformers WHERE assembly_order_id = $assembly_order_id";
        $transformer_id = $this->db->SelectValue($sql);
        if (!$transformer_id) {
            $sql = "SELECT MIN(transformer_id) FROM assembly_order_transformers";
            $transformer_id = $this->db->SelectValue($sql);
            $transformer_id--;
        }
        return $transformer_id;
    }

    public function getEnclosureTypes()
    {
        $sql = "SELECT * FROM enclosure_type ORDER BY enclosure_type_id";
        $result = $this->db->SelectData($sql, null, null, null, null, $recordsCount, 1);
        return $result;
    }

    public function GetEnclosureConfigurations($box_type_id=null)
    {
        $params = null;
        $sql = "SELECT enclosure_config_id, CONCAT(enclosure_type, ' [', configuration_name, ']') as enclosure_config
                FROM enclosure_type
                INNER JOIN enclosure_config ON enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id";
        if ($box_type_id != null) {
            $sql .= " WHERE box_type_id = ? ";
            $params = $this->db->ConvertToParamsArray([$box_type_id]);
        }
        $sql .= " ORDER BY enclosure_type.enclosure_type_id, enclosure_config.enclosure_config_id";

        $result = $this->db->SelectData($sql, $params, null, null, null, $recordsCount, 1);
        return $result;
    }

    public function getAssemblyTrnaformers($assembly_order_id)
    {
        $sql = "SELECT service_point.point_id as transformer_id, service_point.station_id, service_point.feeder_id, capacity_id, transformer_number, latitude, longitude, user_id, service_point.area_id, timestamp,
                CONCAT(IFNULL(service_point.station_id, 0), '/', IFNULL(service_point.feeder_id, 0), '/', IFNULL(capacity_id, 0), '/', IFNULL(transformer_number, 0)) as transformer_name,
                station, feeder, enclosures_count, manufactured_count,
                CONCAT(service_point.transformer_code, service_point.transformer_gov_number) AS transformer_generated_number
                FROM assembly_order_transformers
                INNER JOIN service_point ON assembly_order_transformers.transformer_id = service_point.point_id
                LEFT JOIN station on station.station_id = service_point.station_id
                LEFT JOIN feeder on feeder.feeder_id = service_point.feeder_id

                INNER JOIN (
                    SELECT DISTINCT SUM(enclosure_count)  AS enclosures_count, IFNULL(manufactured_count, 0) AS manufactured_count, assembly_order_configuration.transformer_id, assembly_order_configuration.assembly_order_id
                    FROM assembly_order_configuration
                    LEFT JOIN (
                            SELECT count(*) AS manufactured_count, assembly_order_id, transformer_id
                            FROM enclosure
                            GROUP BY assembly_order_id, transformer_id
                    ) e
                    ON e.transformer_id = assembly_order_configuration.transformer_id
                    AND e.assembly_order_id = assembly_order_configuration.assembly_order_id
                    GROUP BY manufactured_count, assembly_order_configuration.transformer_id, assembly_order_configuration.assembly_order_id
                ) enclosures
                ON assembly_order_transformers.transformer_id = enclosures.transformer_id
                AND assembly_order_transformers.assembly_order_id = enclosures.assembly_order_id

                WHERE assembly_order_transformers.assembly_order_id = ?
                ORDER BY service_point.point_id";
        $params = $this->db->ConvertToParamsArray([$assembly_order_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function getAssemblyOrderItems($assembly_order_id = null, $feeder_id=null, $transformer_id = NULL, $all_orders = false)
    {
        $filter = null;
        if ($assembly_order_id != null) {
            $select_assembly_order_id = ", assembly_order_id";
            $condition_assembly_order_id = "  AND e.assembly_order_id = assembly_order_items.assembly_order_id ";
        } else {
            $select_assembly_order_id = "";
            $condition_assembly_order_id = "";
        }

        $sql = "SELECT DISTINCT enclosure_type.enclosure_type_id, enclosure_type, SUM(enclosure_count) as enclosures_count, manufactured_count
                FROM assembly_order_items
                INNER JOIN assembly_order ON assembly_order.assembly_order_id = assembly_order_items.assembly_order_id
                INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = assembly_order_items.enclosure_type_id
                LEFT JOIN service_point on service_point.point_id = assembly_order_items.transformer_id
                LEFT JOIN (
                    SELECt count(*) AS manufactured_count, enclosure_config.enclosure_type_id $select_assembly_order_id
                    FROM enclosure
                    INNER JOIN enclosure_config on enclosure_config.enclosure_config_id = enclosure.enclosure_configuration_id";

        if ($transformer_id != null) {
            $sql .= " WHERE enclosure.transformer_id = ? ";
            $filter[] = $transformer_id;
        }

        $sql .= " GROUP BY enclosure_type_id $select_assembly_order_id
            ) e ON e.enclosure_type_id = assembly_order_items.enclosure_type_id $condition_assembly_order_id
            WHERE ";//IN ( 1, 0)";

        if ($all_orders) {
            $sql .= " assembly_order.status_id in (0, 1) ";
        } else {
            $sql .= " assembly_order.status_id = 1 ";
        }

        if ($feeder_id != null) {
            $sql .= " AND service_point.feeder_id = ? ";
            $filter[] = $feeder_id;
        }

        if ($assembly_order_id != null) {
            $sql .= " AND assembly_order_items.assembly_order_id = ? ";
            $filter[] = $assembly_order_id;
        }

        if ($transformer_id != null) {
            $sql .= " AND assembly_order_items.transformer_id = ? ";
            $filter[] = $transformer_id;
        }


        $sql .= " GROUP BY enclosure_type.enclosure_type_id, enclosure_type, manufactured_count
                ORDER BY enclosure_type.enclosure_type_id";
        //print $sql;
        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function getAssemblyOrderConfigByTransfomer($assembly_order_id = null, $feeder_id=null, $transformer_id=null)
    {
        $filter = array();
        $sql = "SELECT DISTINCT enclosure_type.enclosure_type_id, enclosure_type,
        SUM(enclosure_count)  AS enclosures_count,
        IFNULL(manufactured_count, 0) AS manufactured_count,
        SUM(enclosure_count) - IFNULL(manufactured_count, 0) AS remaining_enclosures,
        transformer_number, configuration_name, feeder, enclosure_config.enclosure_config_id,
        CONCAT(service_point.transformer_code, service_point.transformer_gov_number) AS transformer_generated_number
        FROM assembly_order_configuration
        INNER JOIN enclosure_config ON enclosure_config.enclosure_config_id = assembly_order_configuration.enclosure_config_id
        INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = enclosure_config.enclosure_type_id
        LEFT JOIN service_point on service_point.point_id = assembly_order_configuration.transformer_id
        LEFT JOIN feeder ON service_point.feeder_id = feeder.feeder_id
        LEFT JOIN (
                SELECT count(*) AS manufactured_count, assembly_order_id, enclosure_config.enclosure_type_id, enclosure_configuration_id, transformer_id
                FROM enclosure
                INNER JOIN enclosure_config on enclosure_config.enclosure_config_id = enclosure.enclosure_configuration_id
                GROUP BY assembly_order_id, enclosure_type_id, enclosure_configuration_id, transformer_id
        ) e
        ON e.enclosure_configuration_id = assembly_order_configuration.enclosure_config_id
        AND e.transformer_id = assembly_order_configuration.transformer_id
        AND e.assembly_order_id = assembly_order_configuration.assembly_order_id";

        if ($assembly_order_id != null) {
            $sql .= " WHERE assembly_order_configuration.assembly_order_id=? ";
            $filter[] = $assembly_order_id;
        }
        if ($feeder_id != null) {
            $sql .= " AND service_point.feeder_id = ? ";
            $filter[] = $feeder_id;
        }
        if ($transformer_id != null) {
            $sql .= " AND assembly_order_configuration.transformer_id=? ";
            $filter[] = $transformer_id;
        }
/*
        $sql .= " GROUP BY enclosure_type.enclosure_type_id, enclosure_type, transformer_number, configuration_name, feeder, manufactured_count
                    ORDER BY enclosure_type.enclosure_type_id";
*/
        $sql .= " GROUP BY enclosure_type.enclosure_type_id, enclosure_type, manufactured_count, transformer_number, configuration_name, feeder, enclosure_config.enclosure_config_id,
                            service_point.transformer_code, service_point.transformer_gov_number
                ORDER BY enclosure_type.enclosure_type_id";
        //print $sql;
        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    // public function getAssemblyOrderByTransfomer($assembly_order_id = null, $feeder_id=null, $transformer_id=null)
    // {
    //     $filter = array();
    //     $sql = "SELECT DISTINCT enclosure_config.enclosure_config_id, enclosure_type, SUM(enclosure_count)  AS enclosures_count, IFNULL(manufactured_count, 0) AS manufactured_count,
    //             configuration_name, feeder
    //             FROM assembly_order_configuration
    //             INNER JOIN enclosure_config ON enclosure_config.enclosure_config_id = assembly_order_configuration.enclosure_config_id
    //             INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = enclosure_config.enclosure_type_id
    //             LEFT JOIN service_point on service_point.point_id = assembly_order_configuration.transformer_id
    //             LEFT JOIN feeder ON service_point.feeder_id = feeder.feeder_id
    //             LEFT JOIN (
    //                     SELECT count(*) AS manufactured_count, assembly_order_id, enclosure_config.enclosure_type_id, enclosure_configuration_id
    //                     FROM enclosure
    //                     INNER JOIN enclosure_config on enclosure_config.enclosure_config_id = enclosure.enclosure_configuration_id
    //                     GROUP BY assembly_order_id, enclosure_configuration_id
    //             ) e
    //             ON e.enclosure_configuration_id = assembly_order_configuration.enclosure_config_id
    //             AND e.assembly_order_id = assembly_order_configuration.assembly_order_id";

    //             if ($assembly_order_id != null) {
    //                 $sql .= " WHERE assembly_order_configuration.assembly_order_id=? ";
    //                 $filter[] = $assembly_order_id;
    //             }
    //             if ($feeder_id != null) {
    //                 $sql .= " AND service_point.feeder_id = ? ";
    //                 $filter[] = $feeder_id;
    //             }
    //             if ($transformer_id != null) {
    //                 $sql .= " AND assembly_order_configuration.transformer_id=? ";
    //                 $filter[] = $transformer_id;
    //             }

    //     $sql .= " GROUP BY enclosure_config.enclosure_config_id, enclosure_type, configuration_name, feeder, manufactured_count
    //                 ORDER BY enclosure_config.enclosure_config_id";
    //     //print $sql;
    //     $params = $this->db->ConvertToParamsArray($filter);
    //     $result = $this->db->SelectData($sql, $params);
    //     return $result;
    // }

    public function getAssemblyOrderByTransfomer($assembly_order_id = null, $feeder_id=null, $transformer_id=null)
    {
        $filter = array();
        $sql = "SELECT enclosure_config.enclosure_config_id, enclosure_type, enclosures_count, IFNULL(manufactured_count, 0) AS manufactured_count, configuration_name, is_extra_stock
                FROM assembly_order
                LEFT JOIN (
                    SELECT SUM(enclosure_count)  AS enclosures_count, enclosure_config_id, assembly_order_configuration.assembly_order_id-- , transformer_id
                    FROM assembly_order_configuration
                    WHERE assembly_order_configuration.assembly_order_id = ?
                    GROUP BY assembly_order_configuration.enclosure_config_id, assembly_order_configuration.assembly_order_id-- , transformer_id
                ) c on c.assembly_order_id = assembly_order.assembly_order_id
                LEFT JOIN (
                        SELECT count(*) AS manufactured_count, assembly_order_id, enclosure_config.enclosure_type_id, enclosure_configuration_id-- , transformer_id
                        FROM enclosure
                        INNER JOIN enclosure_config on enclosure_config.enclosure_config_id = enclosure.enclosure_configuration_id
                        WHERE assembly_order_id = ?
                        GROUP BY assembly_order_id, enclosure_configuration_id -- , transformer_id
                ) e ON e.enclosure_configuration_id = c.enclosure_config_id
                    AND e.assembly_order_id = c.assembly_order_id
                    -- AND e.transformer_id = c.transformer_id
                LEFT JOIN enclosure_config ON enclosure_config.enclosure_config_id = c.enclosure_config_id
                LEFT JOIN enclosure_type ON enclosure_type.enclosure_type_id = enclosure_config.enclosure_type_id
                WHERE assembly_order.assembly_order_id = ?
                ORDER BY enclosure_config.enclosure_config_id";
        $filter[] = $assembly_order_id;
        $filter[] = $assembly_order_id;
        $filter[] = $assembly_order_id;
        //print $sql;
        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }


    public function getEnclosuresByTransformer($assembly_order_id, $transformer_id)
    {
        $sql = "SELECT enclosure.enclosure_id, enclosure_type, configuration_name, enclosure_sn, gateway_sn, Meter1, Meter2, Meter3, Meter4, Meter5, Meter6
                FROM enclosure
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                INNER JOIN enclosure_config ON enclosure.enclosure_configuration_id = enclosure_config.enclosure_config_id
                INNER JOIN enclosure_type ON enclosure_config.enclosure_type_id = enclosure_type.enclosure_type_id
                INNER JOIN
                (
                        SELECT enclosure_id,
                        MAX(CASE WHEN meter_sequence = 1 THEN meter_sn ELSE '' END) Meter1,
                        MAX(CASE WHEN meter_sequence = 2 THEN meter_sn ELSE '' END) Meter2,
                        MAX(CASE WHEN meter_sequence = 3 THEN meter_sn ELSE '' END) Meter3,
                        MAX(CASE WHEN meter_sequence = 4 THEN meter_sn ELSE '' END) Meter4,
                        MAX(CASE WHEN meter_sequence = 5 THEN meter_sn ELSE '' END) Meter5,
                        MAX(CASE WHEN meter_sequence = 6 THEN meter_sn ELSE '' END) Meter6
                        FROM enclosure_meters
                        INNER JOIN meter m ON m.meter_id = enclosure_meters.meter_id
                GROUP BY enclosure_id
                ) a ON enclosure.enclosure_id = a.enclosure_id
                WHERE assembly_order_id = ? AND transformer_id = ?";

        $filter[] = $assembly_order_id;
        $filter[] = $transformer_id;
        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function getTransformersEnclosures($trnaformers)
    {
        // $transformerIds = "null";
        // if (count($trnaformers)>0) {
        //     $transformerIds = implode(",", $trnaformers);
            $sql = "SELECT SUM(enclosure_count) as enclosures_count, enclosure_type.enclosure_type
                    FROM service_point_enclosure_type
                    INNER JOIN (
                        SELECT DISTINCT point_id, transformer_id
                        FROM line_points
                        INNER JOIN point_line ON  point_line.line_id = line_points.line_id
                        WHERE line_points.is_service_point = 1
                    ) l ON service_point_enclosure_type.point_id = l.point_id
                    INNER JOIN enclosure_type ON enclosure_type.enclosure_type_id = service_point_enclosure_type.enclosure_type_id
                    WHERE l.transformer_id IN ($trnaformers)
                    GROUP BY enclosure_type.enclosure_type";

            //$params = $this->db->ConvertToParamsArray($filter);
            $result = $this->db->SelectData($sql);
            return $result;
        // } else {
        //     return false;
        // }
    }

    public function getICCID($condition= null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        // print_r($condition);
        $filter = array();
        if(isset($condition["status"]) && $condition["status"] == "installed") {
            $installed = " INNER JOIN installed_point_enclosure ON installed_point_enclosure.enclosure_id = enclosure.enclosure_id ";
        } else {
            $installed = "";
        }
        unset($condition['status']);

        if(isset($condition["ICCID_pattern"]) && $condition["ICCID_pattern"] !== "") {
            $ICCID_pattern = $condition["ICCID_pattern"];
            // print 'befor'.$ICCID_pattern;
            $ICCID_pattern = $this->db->SqlVal($ICCID_pattern, "mytext");
            // print 'after'.$ICCID_pattern;
            $ICCID_pattern = " AND iccid_pattern in ($ICCID_pattern)";
        } else {
            $ICCID_pattern = "";
        }

        $sql = "SELECT Model, Serial_No, STS_No, IMEI, ICCID, ip_address, simcard_status, simcard_status_id, activation_date FROM
                (
                    SELECT Model, Serial_No, STS_No, IMEI, ICCID, ip_address, simcard_status, meter.simcard_status_id, meter.activation_date
                    FROM enclosure
                    $installed
                    INNER JOIN enclosure_meters ON enclosure.enclosure_id = enclosure_meters.enclosure_id
                    INNER JOIN meter ON enclosure_meters.meter_id = meter.meter_id
                    LEFT JOIN simcard_status ON simcard_status.simcard_status_id = meter.simcard_status_id
                    WHERE ICCID IS NOT NULL AND DATE(enclosure.timestamp) >= ? AND DATE(enclosure.timestamp) <= ?
                    $ICCID_pattern
                    UNION
                    SELECT Model, Serial_No, '' AS STS_No, IMEI, ICCID, ip_address, simcard_status, gateway_sn.simcard_status_id, gateway_sn.activation_date
                    FROM enclosure
                    $installed
                    INNER JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                    LEFT JOIN simcard_status ON simcard_status.simcard_status_id = gateway_sn.simcard_status_id
                    WHERE DATE(enclosure.timestamp) >= ? AND DATE(enclosure.timestamp) <= ?
                    $ICCID_pattern
                ) A ";

        $filter["from_date#1"] = $condition["from_date"];
        $filter["to_date#1"] = $condition["to_date"];
        // if(isset($condition["ICCID_pattern"]) && $condition["ICCID_pattern"] !== "") {
        //     $filter["ICCID_pattern#1"] = $condition["ICCID_pattern"];
        // }
        $filter["from_date#2"] = $condition["from_date"];
        $filter["to_date#2"] = $condition["to_date"];
        // if(isset($condition["ICCID_pattern"]) && $condition["ICCID_pattern"] !== "") {
        //     $filter["ICCID_pattern#2"] = $condition["ICCID_pattern"];
        // }
        unset($condition['from_date']);
        unset($condition['to_date']);
        if(isset($condition["ICCID_pattern"]) && $condition["ICCID_pattern"] !== "") {
            unset($condition['ICCID_pattern']);
        }

        $params = $this->db->ConvertToParamsArray($filter, $condition);
        // print_r($params);
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        return $result;
    }


    public function getAssemblyOrderDetails($assembly_order_id)
    {
        //$transformerIds = implode(",", $trnaformers);
        /*$sql = "SELECT 'assembly' as text,
                    SUM(single_phase_consumers) as single_phase,
                    SUM(three_phase_consumers) as three_phase,
                    COUNT(needs_gateway) as gateways,
                    SUM(enclosures) as enclosures
                FROM service_point p
                INNER JOIN assembly_order_transformers on assembly_order_transformers.transformer_id = p.transformer_id
                LEFT JOIN (
                    SELECT COUNT(*) as enclosures, point_id FROM service_point_enclosure_type
                    GROUP BY point_id
                ) e ON e.point_id = p.point_id
                WHERE assembly_order_id = ?";*/
        $sql = "SELECT
                    SUM(single*enclosure_count) as single_phase,
                    SUM(CASE meter_type_id WHEN 2 THEN three*enclosure_count ELSE 0 END) as three_phase,
                    SUM(CASE meter_type_id WHEN 3 THEN three*enclosure_count ELSE 0 END) as ct,
                    SUM(gateway*enclosure_count) as gateway,
                    SUM(CASE WHEN enclosure_shape_id=1 THEN enclosure_count ELSE 0 END) as small_enclosure,
                    SUM(CASE WHEN enclosure_shape_id=2 THEN enclosure_count ELSE 0 END) as big_enclosure, transformers as ct_meters
                FROM assembly_order_items
                INNER JOIN enclosure_type on enclosure_type.enclosure_type_id = assembly_order_items.enclosure_type_id
                INNER JOIN (
                    SELECT count(*) as transformers, assembly_order_id
                    FROM assembly_order_transformers
                    GROUP BY assembly_order_id
                ) t ON t.assembly_order_id = assembly_order_items.assembly_order_id
                WHERE assembly_order_items.assembly_order_id = " . $this->db->SqlVal($assembly_order_id, "mytext");
        $result = $this->db->SelectData($sql);
        return $result;

    }

    public function getOrderIccids($assembly_order_id, $condition= null, $order = null, $start = 0, $size = 0, &$recordsCount = null)
    {
        $id = $this->db->SqlVal($assembly_order_id, "mytext");
        $sql = "SELECT meter.iccid iccids, iccid_pattern, model, meter_sn serial_number, activation_date, simcard_status
                FROM enclosure
                INNER JOIN enclosure_meters ON enclosure.enclosure_id = enclosure_meters.enclosure_id
                INNER JOIN meter ON enclosure_meters.meter_id = meter.meter_id
                LEFT JOIN simcard_status ON simcard_status.simcard_status_id = meter.simcard_status_id
                WHERE enclosure.assembly_order_id = $id
                AND ICCID IS NOT NULL
                UNION
                SELECT gateway_sn.ICCID iccids, iccid_pattern, model, gateway_sn serial_number, activation_date, simcard_status
                FROM enclosure
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                INNER JOIN simcard_status ON simcard_status.simcard_status_id = gateway_sn.simcard_status_id
                WHERE enclosure.assembly_order_id = $id
                AND ICCID IS NOT NULL";

        $result = $this->db->SelectData($sql);
        return $result;
    }

    public function UpdateIccidStatus($id, $activation_date, $order_id)
    {
        $id = $this->db->SqlVal($id, "mytext");
        $activation_date = $this->db->SqlVal($activation_date, "text");
        if($id == 1){
            $meter_simcard_status = ' AND (meter.simcard_status_id != 1 OR meter.simcard_status_id IS NULL)';
            $gatewaya_simcard_status = ' AND (gateway_sn.simcard_status_id != 1 OR gateway_sn.simcard_status_id IS NULL)';
            $assembly_order_simcard_status = ' AND (simcard_status_id !=1 OR simcard_status_id IS NULL)';
        } else if($id == 0){
            $meter_simcard_status = ' AND (meter.simcard_status_id != 0 OR meter.simcard_status_id IS NULL)';
            $gatewaya_simcard_status = ' AND (gateway_sn.simcard_status_id != 0 OR gateway_sn.simcard_status_id IS NULL)';
            $assembly_order_simcard_status = ' AND (simcard_status_id !=0 OR simcard_status_id IS NULL)';
        }

        $sql="UPDATE enclosure
                INNER JOIN enclosure_meters ON enclosure.enclosure_id = enclosure_meters.enclosure_id
                INNER JOIN meter ON enclosure_meters.meter_id = meter.meter_id
                LEFT JOIN simcard_status ON simcard_status.simcard_status_id = meter.simcard_status_id
                SET meter.simcard_status_id = $id, activation_date = $activation_date
                WHERE enclosure.assembly_order_id = $order_id
                AND ICCID IS NOT NULL
                $meter_simcard_status";

        $sql_1="UPDATE enclosure
                LEFT JOIN gateway_sn ON enclosure.gateway_id = gateway_sn.gateway_id
                INNER JOIN simcard_status ON simcard_status.simcard_status_id = gateway_sn.simcard_status_id
                SET gateway_sn.simcard_status_id = $id, activation_date = $activation_date
                WHERE enclosure.assembly_order_id = $order_id
                AND ICCID IS NOT NULL
                $gatewaya_simcard_status";

        $sql_2="UPDATE assembly_order SET simcard_status_id = $id
                WHERE assembly_order_id = $order_id
                $assembly_order_simcard_status";

        if (( ! $this->db->Execute($sql, null)) || ( ! $this->db->Execute($sql_1, null)) || ( ! $this->db->Execute($sql_2, null))) {
            $this->Message = 'simcard_status_update_failed';
            $this->State = self::ERROR;
            return false;
        } else {
            $this->Message = 'simcard_status_update_success';
            $this->State = self::SUCCESS;
            return true;
        }
    }

    public function getAssemblyOrderSummaryDashboard($condition = null)
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
            FROM enclosure
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
            $sql .= " AND DATE(enclosure.timestamp) >= ? AND DATE(enclosure.timestamp) <= ?";
            $filter["from_date"] = $condition["from_date"];
            $filter["to_date"] = $condition["to_date"];
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

    public function GetStationByArea($AreaID = NULL)
    {
        $sql = "SELECT DISTINCT station.station_id station_id, station
                FROM
                station";
        if( $AreaID ){
            $sql .= " INNER JOIN transformer ON station.station_id = transformer.station_id
                        WHERE area_id = ".$this->db->SqlVal($AreaID, "int");
        }
        $sql .= " ORDER BY station";

        $result = $this->db->SelectData($sql, NULL, NULL, NULL, NULL, $recordsCount, 1);
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

    public function AddCalculatedEnclosureTypes($transformerIds)
    {
        $error = false;
        $this->db->BeginTransaction();
        $transformerIdsStr = $transformerIds;//implode(",", $transformerIds);

        // Delete from service_point_enclosure_type
        $sql="DELETE service_point_enclosure_type
                FROM service_point_enclosure_type
                INNER JOIN line_points ON service_point_enclosure_type.point_id = line_points.point_id
                INNER JOIN point_line ON point_line.line_id = line_points.line_id
                WHERE point_line.transformer_id in ($transformerIdsStr)";
        $delete_result = $this->db->Execute($sql, null);
        if ( ! $delete_result ) {
            $error=true;
        }

        // Insert to service_point_enclosure_type
        $query = "INSERT INTO service_point_enclosure_type (point_id, enclosure_type_id, enclosure_count)
                SELECT sp.point_id, e.enclosure_type_id, SUM(enclosure_count) as enclosures_count
                FROM
                (
                    SELECT sum(enclosure_count) as enclosure_count,
                    point_id, point_type_id, meter_type_id, enclosure_size, meter_count, meters, ifnull(needs_gateway, 0) as  needs_gateway
                    FROM (
                            -- 1 enclosure single phase
                            SELECT point_id, point_type_id, single_phase_consumers as meters,
                                1 as meter_type_id, 1 enclosure_size, single_phase_consumers as meter_count, 1 as enclosure_count,
                                needs_gateway
                            FROM service_point
                            WHERE single_phase_consumers > 0 and single_phase_consumers <= 3

                            UNION ALL
                            SELECT point_id, point_type_id, single_phase_consumers as meters,
                                1 as meter_type_id, 2 enclosure_size, single_phase_consumers as meter_count, 1 as enclosure_count,
                                needs_gateway
                            FROM service_point
                            WHERE single_phase_consumers > 3 and single_phase_consumers <= 6

                            -- 12, 18, 24...
                            -- more than one enclosure no remainder
                            -- three cases:
                            -- 		no gateway
                            -- 		needs gateway: all-1 no gateway
                            -- 		1 with gateway
                            UNION ALL
                            SELECT point_id, point_type_id, single_phase_consumers as meters,
                                1 as meter_type_id, 2 enclosure_size, 6 as meter_count, FLOOR(single_phase_consumers/6) as enclosure_count,
                                0 needs_gateway
                            FROM service_point
                            WHERE single_phase_consumers > 6 and (single_phase_consumers MOD 6) = 0
                                and needs_gateway = 0 -- no gateway in point
                            UNION ALL
                            SELECT point_id, point_type_id, single_phase_consumers as meters,
                                1 as meter_type_id, 2 enclosure_size, 6 as meter_count, FLOOR(single_phase_consumers/6)-1 as enclosure_count,
                                0 needs_gateway
                            FROM service_point
                            WHERE single_phase_consumers > 6 and (single_phase_consumers MOD 6) = 0
                                and needs_gateway = 1 -- all - one: no gateway
                            UNION ALL
                            SELECT point_id, point_type_id, single_phase_consumers as meters,
                                1 as meter_type_id, 2 enclosure_size, 6 as meter_count, 1 as enclosure_count,
                                1 needs_gateway
                            FROM service_point
                            WHERE single_phase_consumers > 6 and (single_phase_consumers MOD 6) = 0
                                and needs_gateway = 1 -- only one gateway


                            -- more than one enclosure with remainder
                            -- three cases:
                            -- 		full enclosures always no gateway
                            -- 		small remaining enclosure DEFAULT gateway
                            -- 		large remaining enclosure DEFAULT gateway
                            -- first the wholes, always no gateway
                            UNION ALL
                            SELECT point_id, point_type_id, single_phase_consumers as meters,
                                1 as meter_type_id, 2 enclosure_size, 6 as meter_count, FLOOR(single_phase_consumers/6) as enclosure_count,
                                0 needs_gateway
                            FROM service_point
                            WHERE single_phase_consumers > 6 and (single_phase_consumers MOD 6) > 0
                            -- use gateway if needed
                            --  remainder 1-3
                            UNION ALL
                            SELECT point_id, point_type_id, single_phase_consumers as meters,
                                1 as meter_type_id, 1 enclosure_size, (single_phase_consumers MOD 6) as meter_count, 1 as enclosure_count,
                                needs_gateway
                            FROM service_point
                            WHERE single_phase_consumers > 6 and (single_phase_consumers MOD 6) > 0 and (single_phase_consumers MOD 6) <= 3
                            -- remainder 4-5
                            UNION ALL
                            SELECT point_id, point_type_id, single_phase_consumers as meters,
                                1 as meter_type_id, 2 enclosure_size, (single_phase_consumers MOD 6) as meter_count, 1 as enclosure_count,
                                needs_gateway
                            FROM service_point
                            WHERE single_phase_consumers > 6 and (single_phase_consumers MOD 6) > 3


                            -- ==================================================
                            -- ==================================================

                            -- 3 phase
                            -- only one
                            UNION ALL
                            SELECT point_id, point_type_id, three_phase_consumers as meters,
                                2 as meter_type_id, 2 enclosure_size, three_phase_consumers as meter_count, 1 as enclosure_count,
                                CASE WHEN needs_gateway = 1 and single_phase_consumers = 0 then 1 else 0 end as needs_gateway
                            FROM service_point
                            WHERE three_phase_consumers > 0 and three_phase_consumers <= 2

                            -- 2, 4, 6
                            -- more than one enclosure no remainder
                            -- three cases:
                            -- 		no gateway
                            -- 		needs gateway: all-1 no gateway
                            -- 		1 with gateway
                            -- no need for gateway
                            UNION ALL
                            SELECT point_id, point_type_id, three_phase_consumers as meters,
                                2 as meter_type_id, 2 enclosure_size, 2 as meter_count, FLOOR(three_phase_consumers/2) as enclosure_count,
                                CASE WHEN needs_gateway = 1 and single_phase_consumers = 0 then 1 else 0 end as needs_gateway
                            FROM service_point
                            WHERE three_phase_consumers > 2 and three_phase_consumers MOD 2 = 0
                            and (single_phase_consumers > 0 or needs_gateway = 0) -- one whole

                            -- needs gateway
                            -- all whole minus one
                            UNION ALL
                            SELECT point_id, point_type_id, three_phase_consumers as meters,
                                2 as meter_type_id, 2 enclosure_size, 2 as meter_count, FLOOR(three_phase_consumers/2)-1 as enclosure_count,
                                0 as needs_gateway
                            FROM service_point
                            WHERE three_phase_consumers > 2 and three_phase_consumers MOD 2 = 0
                                    and (single_phase_consumers = 0 and needs_gateway = 1) -- whol- one
                            -- extra 1 for gateway
                            UNION ALL
                            SELECT point_id, point_type_id, three_phase_consumers as meters,
                                2 as meter_type_id, 2 enclosure_size, 2 as meter_count, 1 as enclosure_count,
                                1 as needs_gateway
                            FROM service_point
                            WHERE three_phase_consumers > 2 and three_phase_consumers MOD 2 = 0
                                    and (single_phase_consumers = 0 and needs_gateway = 1) -- whol- one


                            -- 3, 5, 7
                            -- more than 1 enclosure with remainder
                            -- more than one enclosure with remainder
                            -- three cases:
                            -- 		full enclosures always no gateway
                            -- 		small remaining enclosure DEFAULT gateway, if no single phase
                            -- first the wholes, always no gateway
                            UNION ALL
                            SELECT point_id, point_type_id, three_phase_consumers as meters,
                                2 as meter_type_id, 2 enclosure_size, 2 as meter_count, FLOOR(three_phase_consumers/2) as enclosure_count,
                                0 as needs_gateway
                            FROM service_point
                            WHERE three_phase_consumers > 2 and (three_phase_consumers MOD 2) > 0

                            -- remaining one
                            UNION ALL
                            SELECT point_id, point_type_id, three_phase_consumers as meters,
                                2 as meter_type_id, 2 enclosure_size, 1 as meter_count, 1 as enclosure_count,
                                CASE WHEN needs_gateway = 1 and single_phase_consumers = 0 then 1 else 0 end as needs_gateway
                            FROM service_point
                            WHERE three_phase_consumers > 2 and (three_phase_consumers MOD 2) > 0

                            -- ==================================================
                            -- ==================================================

                            -- CT
                            UNION ALL
                            SELECT point_id, point_type_id, 1 as meters,
                                3 as meter_type_id, 2 enclosure_size, 1 as meter_count, 1 as enclosure_count,
                                0 as needs_gateway
                            FROM service_point
                            WHERE point_type_id = 4

                    ) m
                    -- WHERE point_type_id in (1,2,3)
                    GROUP BY -- point_id, meter_type_id, enclosure_size, meter_count, meters, ifnull(needs_gateway, 0),needs_gateway
                    point_id, point_type_id, meter_type_id, enclosure_size, meter_count, meters, needs_gateway
                ) sp
                INNER JOIN (
                    SELECT DISTINCT service_point.point_id, transformer_id
                    FROM service_point
                    INNER JOIN line_points ON service_point.point_id = line_points.point_id
                    INNER JOIN point_line ON point_line.line_id = line_points.line_id
                    WHERE point_line.transformer_id in ($transformerIdsStr)
                    AND is_service_point = 1
                ) l ON l.point_id = sp.point_id
                LEFT JOIN enclosure_type e
                    ON
                    (
                        e.enclosure_shape_id = sp.enclosure_size
                        AND e.gateway = sp.needs_gateway
                        AND e.Meter = sp.meter_count
                        AND e.meter_type_id = sp.meter_type_id
                        AND sp.point_type_id != 4
                    )
                    OR
                    (
                        e.meter_type_id = sp.meter_type_id
                        AND sp.point_type_id = 4
                    )
                GROUP BY sp.point_id, e.enclosure_type_id";
        $insert_result = $this->db->Execute($query, null);
        if( ! $insert_result ){
            $error = true;
        }

        if ($error) {
            $this->db->RollbackTransaction();
            $this->State = self::ERROR;
            $this->Message = "service_point_enclosure_type_insert_failed";
            //return false;
        } else {
            $this->db->CommitTransaction();
            $this->State = self::SUCCESS;
            $this->Message = "service_point_enclosure_type_insert_success";
        }
    }

    public function GetEnclosureAttributesBySN($enclosure_sn)
    {
        $sql = "SELECT enclosure_id, enclosure_configuration_id AS enclosure_config_id,  ec.enclosure_type_id,
                    et.enclosure_type, et.meter, et.single, et.three, et.gateway, et.enclosure_shape_id, et.phase, et.meter_type_id,
                    meter_type, ec.enclosure_config_id, configuration_name, mt.phase, e.assembly_order_id, assembly_order_code, create_date,
                    station.station_id, station, feeder.feeder_id, feeder, transformer_number, box_type_id

                FROM enclosure e
                INNER  JOIN enclosure_config ec ON ec.enclosure_config_id = e.enclosure_configuration_id
                INNER JOIN enclosure_type et ON et.enclosure_type_id = ec.enclosure_type_id
                INNER JOIN meter_type mt ON mt.meter_type_id = et.meter_type_id
                INNER JOIN assembly_order ON e.assembly_order_id = assembly_order.assembly_order_id

                -- INNER JOIN assembly_order_transformers t on t.assembly_order_id = assembly_order.assembly_order_id
                LEFT JOIN service_point on service_point.point_id = e.transformer_id
                LEFT JOIN station on station.station_id = service_point.station_id
                LEFT JOIN feeder on feeder.feeder_id = service_point.feeder_id

                WHERE TRIM(e.enclosure_sn) =?";
        $params = $this->db->ConvertToParamsArray(array(trim($enclosure_sn)));
        $result = $this->db->SelectData($sql, $params);
        if ($result == null) {
            return false;
        } else {
            return $result;
        }
    }

    public function GetCurrentTeamTask($filter)
    {
        $sql = "SELECT enclosure_id, enclosure_configuration_id as enclosure_config_id,  ec.enclosure_type_id,
                    et.enclosure_type, et.meter, et.single, et.three, et.gateway, et.enclosure_shape_id, et.phase, et.meter_type_id,
                    meter_type, ec.enclosure_config_id, configuration_name, mt.phase, e.assembly_order_id,
                    team_name, u.NAME, e.transformer_id, sp.transformer_number
                FROM enclosure e
                INNER JOIN service_point sp on sp.point_id = e.transformer_id
                INNER  JOIN enclosure_config ec on ec.enclosure_config_id = e.enclosure_configuration_id
                INNER JOIN enclosure_type et on et.enclosure_type_id = ec.enclosure_type_id
                INNER JOIN meter_type mt on mt.meter_type_id = et.meter_type_id
                INNER JOIN USER u ON u.USER_ID = e.user_id
                INNER JOIN assembly_team t ON t.user_id = u.user_id
                WHERE e.status_id = 0
                AND e.user_id =?";
        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params);
        if ($result == null) {
            return false;
        } else {
            return $result;
        }
    }

    public function GetEnclosureTaskDetails($filter)
    {
        $sql = "SELECT ec.enclosure_type_id, o.enclosure_config_id, enclosure_count,
                    assembled_enclosures, (enclosure_count-assembled_enclosures) AS remaining_enclosures,
                    et.enclosure_type, et.meter, et.single, et.three, et.gateway, et.enclosure_shape_id, et.phase, et.meter_type_id,
                    meter_type, ec.enclosure_config_id, configuration_name, mt.phase, o.assembly_order_id,
                    o.transformer_id, sp.transformer_number
                FROM assembly_order_configuration o
                LEFT JOIN service_point sp ON sp.point_id = o.transformer_id
                INNER JOIN assembly_order ao ON ao.assembly_order_id = o.assembly_order_id
                INNER JOIN enclosure_config ec ON ec.enclosure_config_id = ?
                INNER JOIN enclosure_type et ON et.enclosure_type_id = ec.enclosure_type_id
                INNER JOIN meter_type mt ON mt.meter_type_id = et.meter_type_id
                LEFT JOIN assembly_order_transformers aot
                    ON aot.transformer_id = o.transformer_id
                    AND aot.assembly_order_id = o.assembly_order_id
                LEFT JOIN (
                    SELECT COUNT(*) as assembled_enclosures, enclosure_configuration_id, assembly_order_id, transformer_id
                    FROM enclosure GROUP BY enclosure_configuration_id, assembly_order_id, transformer_id
                ) e ON e.enclosure_configuration_id = ec.enclosure_config_id
                    AND e.assembly_order_id = o.assembly_order_id
                    AND e.transformer_id = o.transformer_id
                WHERE ao.assembly_order_id = ?
                AND ifnull(o.transformer_id, '') = ifnull(?, '')";
        $params = $this->db->ConvertToParamsArray($filter);
        $result = $this->db->SelectData($sql, $params);
        if ($result == null) {
            return false;
        } else {
            return $result;
        }
    }

    public function GetTeamStack($filter, $limit=null, $ignore_count=false)
    {
        $condition = array();
        $sql = "SELECT ec.enclosure_type_id, o.enclosure_config_id, enclosure_count, assembled_enclosures, (enclosure_count-assembled_enclosures) AS remaining_enclosures,
                    et.enclosure_type, et.meter, et.single, et.three, et.gateway, et.enclosure_shape_id, et.phase, et.meter_type_id,
                    meter_type, ec.enclosure_config_id, configuration_name, mt.phase, o.assembly_order_id,
                    t.team_name, u.NAME, o.transformer_id, sp.transformer_number
                FROM assembly_order_configuration o
                INNER JOIN service_point sp ON sp.point_id = o.transformer_id
                INNER JOIN assembly_order ao ON ao.assembly_order_id = o.assembly_order_id
                INNER JOIN team_default_config tc ON o.enclosure_config_id = tc.enclosure_config_id
                INNER JOIN enclosure_config ec ON ec.enclosure_config_id = tc.enclosure_config_id
                INNER JOIN assembly_team t ON t.team_id = tc.team_id
                INNER JOIN USER u ON u.USER_ID = t.user_id
                INNER JOIN enclosure_type et ON et.enclosure_type_id = ec.enclosure_type_id
                INNER JOIN meter_type mt ON mt.meter_type_id = et.meter_type_id
                INNER JOIN assembly_order_transformers aot
                        ON aot.transformer_id = o.transformer_id
                        AND aot.assembly_order_id = o.assembly_order_id
                LEFT JOIN (
                    SELECT COUNT(*) as assembled_enclosures, enclosure_configuration_id, assembly_order_id, transformer_id
                    FROM enclosure GROUP BY enclosure_configuration_id, assembly_order_id, transformer_id
                ) e ON e.enclosure_configuration_id = tc.enclosure_config_id
                    AND e.assembly_order_id = o.assembly_order_id
                    AND e.transformer_id = o.transformer_id";
        // if (isset($filter["assembly_order_id"])) {
        //     $sql .= " WHERE assembly_order_id = ? ";
        //     $condition[] = $filter["assembly_order_id"];
        // }
        // GROUP BY enclosure_configuration_id, assembly_order_id
        // ) e on e.enclosure_configuration_id = tc.enclosure_config_id and e.assembly_order_id = o.assembly_order_id
        $sql .= "
                WHERE t.user_id = ?
                AND ao.status_id = 1";
        if (!$ignore_count) {
            $sql .= " AND o.enclosure_count - IFNULL(e.assembled_enclosures, 0) >0 ";
        }

        $condition[] = $filter["user_id"];

        if (isset($filter["assembly_order_id"])) {
            $sql .= " AND o.assembly_order_id=? ";
            $condition[] = $filter["assembly_order_id"];
        }
        $sql .= " ORDER BY aot.is_priority DESC, aot.assembly_order_id, transformer_id, tc.priority";
        if ($limit!=null) {
            $sql .=" LIMIT 1";
        }
//print $sql;
        $params = $this->db->ConvertToParamsArray($condition);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function AvailalbeOrderConfiguration($order_id, $configurtaion_id)
    {
        $sql = "SELECT (enclosure_count-assembled_enclosures) AS remaining_enclosures
                FROM assembly_order_configuration o
                LEFT JOIN (
                    SELECT COUNT(*) as assembled_enclosures, enclosure_configuration_id, assembly_order_id
                    FROM enclosure
                    WHERE assembly_order_id = ? AND enclosure_configuration_id = ?
                ) e ON e.enclosure_configuration_id = o.enclosure_config_id and e.assembly_order_id = o.assembly_order_id
                WHERE o.assembly_order_id = ?
                AND o.enclosure_config_id = ?";
        $condition = array($order_id, $configurtaion_id, $order_id, $configurtaion_id);
        $params = $this->db->ConvertToParamsArray($condition);
        $result = $this->db->SelectValue($sql, $params);
        return $result;
    }

    public function InsertEnclosure($enclosure_data)
    {
        $availalbe = $this->AvailalbeOrderConfiguration($enclosure_data['assembly_order_id'], $enclosure_data['enclosure_configuration_id']);
        if ($availalbe > 0) {
            $data['assembly_order_id']= $this->db->SqlVal($enclosure_data["assembly_order_id"], "int");
            $data['user_id']= $this->db->SqlVal($enclosure_data["user_id"], "int");
            $data['phase']= $this->db->SqlVal($enclosure_data["phase"], "int");
            $data['enclosure_type_id']= $this->db->SqlVal($enclosure_data["enclosure_type_id"], "int");
            $data['enclosure_configuration_id']= $this->db->SqlVal($enclosure_data["enclosure_configuration_id"], "int");
            $data['timestamp']= date("Y-m-d H:i:s");
            $enclosure_id = $this->db->Insert("enclosure", $data, true);
            return $enclosure_id;
        } else {
            return false;
        }
    }

    public function GetTeamProgress($assembly_order_id=null)
    {
        $conditionStr = "";
        if ($assembly_order_id != null) {
            $filter = ["1"=>$assembly_order_id, "2"=>$assembly_order_id];
            $conditionStr = " AND assembly_order.assembly_order_id=? " ;
        }
        $sql = "SELECT assembly_team.team_id, team_name, manufactured_count, enclosures_count
                FROM assembly_team
                LEFT JOIN (
                        SELECt COUNT(*) AS manufactured_count, enclosure.assembly_order_id, team_id
                        FROM enclosure
                        INNER JOIN assembly_order on assembly_order.assembly_order_id = enclosure.assembly_order_id
                        INNER JOIN `USER` u on u.USER_ID = enclosure.user_id
                        INNER JOIN assembly_team on assembly_team.user_id = u.USER_ID
                        WHERE assembly_order.status_id =1
                        $conditionStr
                        GROUP BY team_id, enclosure.assembly_order_id
                ) e ON e.team_id = assembly_team.team_id
                LEFT JOIN (
                    SELECt SUM(oc.enclosure_count) as enclosures_count, team_id
                    FROM assembly_order_configuration oc
                    INNER JOIN assembly_order on assembly_order.assembly_order_id = oc.assembly_order_id
                    INNER JOIN enclosure_config on enclosure_config.enclosure_config_id = oc.enclosure_config_id
                    INNER JOIN team_default_config tc on tc.enclosure_config_id = enclosure_config.enclosure_type_id
                    WHERE assembly_order.status_id =1
                    $conditionStr
                    GROUP BY team_id
                ) req ON req.team_id = e.team_id";
        if ($assembly_order_id != null) {
            $params = $this->db->ConvertToParamsArray($filter);
            $result = $this->db->SelectData($sql, $params);
        } else {
            $result = $this->db->SelectData($sql);
        }
        return $result;
    }

    // public function UpdateIccidStatus($id, $iccids, $activation_date)
    // {
    //     $id = $this->db->SqlVal($id, "mytext");
    //     $activation_date = $this->db->SqlVal($activation_date, "text");
    //     if($id == 1){
    //         $simcard_status = 'AND (simcard_status_id != 1 OR simcard_status_id IS NULL)';
    //     } else if($id == 0){
    //         $simcard_status = 'AND (simcard_status_id != 0 OR simcard_status_id IS NULL)';
    //         //$activation_date = 'NULL';
    //     }

    //     $sql="UPDATE meter
    //             SET simcard_status_id = $id, activation_date = $activation_date
    //             WHERE ICCID IN ($iccids)
    //             $simcard_status";

    //     $sql_1="UPDATE gateway_sn
    //             SET simcard_status_id = $id, activation_date = $activation_date
    //             WHERE ICCID IN ($iccids)
    //             $simcard_status";

    //     if ( ( ! $this->db->Execute($sql, null) ) || ( ! $this->db->Execute($sql_1, null) ) ) {
    //         $this->Message = 'simcard_status_update_failed';
    //         $this->State = self::ERROR;
    //         return false;
    //     } else {
    //         $this->Message = 'simcard_status_update_success';
    //         $this->State = self::SUCCESS;
    //         return true;
    //     }
    // }


}
?>