<?php
require_once (realpath(dirname(__FILE__)) . '/MysqliDB.php');

/**
 * @access public
 * @package EMPLOYEE
 */
class SupplyChain
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

    public function GetMeterTrace($meter_id)
    {
        $sql = "SELECT meter_trace.*, NAME, enclosure_sn, meter_trace_status, meter_trace_status.icon, meter_trace_status.color
                FROM meter_trace
                LEFT JOIN USER ON USER.USER_ID = meter_trace.user_id
                LEFT JOIN enclosure on enclosure.enclosure_id = meter_trace.enclosure_id
                LEFT JOIN meter_trace_status ON meter_trace_status.meter_trace_status_id = meter_trace.meter_trace_status_id
                WHERE meter_id = ?
                ORDER BY `timestamp` DESC";
        $params = $this->db->ConvertToParamsArray([$meter_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetMeterDetails($meter_id)
    {
        $sql = "SELECT meter.*, enclosure_id FROM meter
                LEFT JOIN enclosure_meters ON enclosure_meters.meter_id = meter.meter_id
                WHERE meter.meter_id=?";
        $params = $this->db->ConvertToParamsArray([$meter_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetMeterLinking($meter_id)
    {
        $sql = "SELECT meter.meter_id, enclosure_sn, sp.point_id, concat(t.transformer_number, ' [', t.transformer_code, t.transformer_gov_number,']') as transformer_number, feeder, station
                FROM meter
                LEFT JOIN enclosure_meters em ON em.meter_id = meter.meter_id
                LEFT JOIN enclosure ON enclosure.enclosure_id = em.enclosure_id
                LEFT JOIN installed_point_enclosure ep ON ep.enclosure_id = em.enclosure_id
                LEFT JOIN service_point sp ON sp.point_id = ep.point_id
                LEFT JOIN line_points lp ON lp.point_id = sp.point_id
                LEFT JOIN point_line pl ON pl.line_id = lp.line_id
                LEFT JOIN service_point t ON t.point_id = lp.point_id
                LEFT JOIN feeder ON t.feeder_id = feeder.feeder_id
                LEFT JOIN station ON station.station_id = feeder.station_id
                WHERE meter.meter_id=?";
        $params = $this->db->ConvertToParamsArray([$meter_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetGatewayTrace($gateway_id)
    {
        $sql = "SELECT gateway_trace.*, NAME, enclosure_sn, gateway_trace_status, gateway_trace_status.icon, gateway_trace_status.color
                FROM gateway_trace
                LEFT JOIN USER ON USER.USER_ID = gateway_trace.user_id
                LEFT JOIN enclosure on enclosure.enclosure_id = gateway_trace.enclosure_id
                LEFT JOIN gateway_trace_status ON gateway_trace_status.gateway_trace_status_id = gateway_trace.gateway_trace_status_id
                WHERE gateway_trace.gateway_id = ?
                ORDER BY `timestamp` DESC";
        $params = $this->db->ConvertToParamsArray([$gateway_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetGatewayDetails($gateway_id)
    {
        $sql = "SELECT gateway_sn.*, enclosure_id FROM gateway_sn
                LEFT JOIN enclosure ON enclosure.gateway_id = gateway_sn.gateway_id
                WHERE gateway_sn.gateway_id=?";
        $params = $this->db->ConvertToParamsArray([$gateway_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetGatewayLinking($gateway_id)
    {
        $sql = "SELECT gateway_sn.gateway_id, enclosure_sn, sp.point_id, concat(t.transformer_number, ' [', t.transformer_code, t.transformer_gov_number,']') as transformer_number, feeder, station
                FROM gateway_sn
                LEFT JOIN enclosure ON enclosure.gateway_id = gateway_sn.gateway_id
                LEFT JOIN installed_point_enclosure ep ON ep.enclosure_id = enclosure.enclosure_id
                LEFT JOIN service_point sp ON sp.point_id = ep.point_id
                LEFT JOIN line_points lp ON lp.point_id = sp.point_id
                LEFT JOIN point_line pl ON pl.line_id = lp.line_id
                LEFT JOIN service_point t ON t.point_id = lp.point_id
                LEFT JOIN feeder ON t.feeder_id = feeder.feeder_id
                LEFT JOIN station ON station.station_id = feeder.station_id
                WHERE gateway_sn.gateway_id=?";
        $params = $this->db->ConvertToParamsArray([$gateway_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }



    public function GetEnclosureDetails($enclosure_id)
    {
        $sql = "SELECT enclosure.*, enclosure_type, configuration_name
                FROM enclosure
                INNER JOIN enclosure_config c on c.enclosure_config_id = enclosure.enclosure_configuration_id
                INNER JOIN enclosure_type t on t.enclosure_type_id = c.enclosure_type_id
                WHERE enclosure.enclosure_id= ?";
        $params = $this->db->ConvertToParamsArray([$enclosure_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetEnclosureTrace($enclosure_id)
    {
        $sql = "SELECT enclosure_trace.*, NAME, enclosure_sn, enclosure_trace_status, enclosure_trace_status.icon, enclosure_trace_status.color
                FROM enclosure_trace
                LEFT JOIN USER ON USER.USER_ID = enclosure_trace.user_id
                INNER JOIN enclosure on enclosure.enclosure_id = enclosure_trace.enclosure_id
                LEFT JOIN enclosure_trace_status ON enclosure_trace_status.enclosure_trace_status_id = enclosure_trace.enclosure_trace_status_id
                WHERE enclosure.enclosure_id = ?
                ORDER BY `timestamp` DESC";
        $params = $this->db->ConvertToParamsArray([$enclosure_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function GetEnclosureLinking($enclosure_id)
    {
        $sql = "SELECT enclosure.enclosure_id, enclosure_sn, sp.point_id, concat(t.transformer_number, ' [', t.transformer_code, t.transformer_gov_number,']') as transformer_number, feeder, station
                FROM enclosure
                LEFT JOIN installed_point_enclosure ep ON ep.enclosure_id = enclosure.enclosure_id
                LEFT JOIN service_point sp ON sp.point_id = ep.point_id
                LEFT JOIN line_points lp ON lp.point_id = sp.point_id
                LEFT JOIN point_line pl ON pl.line_id = lp.line_id
                LEFT JOIN service_point t ON t.point_id = lp.point_id
                LEFT JOIN feeder ON t.feeder_id = feeder.feeder_id
                LEFT JOIN station ON station.station_id = feeder.station_id
                WHERE enclosure.enclosure_id=?";
        $params = $this->db->ConvertToParamsArray([$enclosure_id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }








    public function AddRequest($r_data)
    {
        $data = array();

        $data["source_warehouse_id"] = $this->db->SqlVal($r_data["source_warehouse_id"], "int");
        $data["destination_warehouse_id"] = $this->db->SqlVal($r_data["destination_warehouse_id"], "int");
        $data["estimated_receive_time"] = $this->db->SqlVal($r_data["estimated_receive_time"], "mytext");
        $data["request_type_id"] = $this->db->SqlVal($r_data["request_type_id"], "int");
        $data["request_reason_id"] = $this->db->SqlVal($r_data["request_reason_id"], "int");
        $data["request_time"] = $this->db->SqlVal($r_data["request_time"], "mytext");
        $data["is_confirmed"] = $this->db->SqlVal($r_data["is_confirmed"], "int");

		$result = $this->db->Insert("transfer_order_request", $data);
        if(!$result)
		{
			$this->State = self::ERROR;
			$this->Message = "request_insert_failed";
			return false;
		}else{
			$this->State = self::SUCCESS;
			$this->Message = "request_insert_success";
			return true;
		}
    }

    public function UpdateRequest($r_data, $r_contition)
    {
        $data = array();
        $data["source_warehouse_id"] = $this->db->SqlVal($r_data["source_warehouse_id"], "int");
        $data["destination_warehouse_id"] = $this->db->SqlVal($r_data["destination_warehouse_id"], "int");
        $data["estimated_receive_time"] = $this->db->SqlVal($r_data["estimated_receive_time"], "mytext");
        $data["request_type_id"] = $this->db->SqlVal($r_data["request_type_id"], "int");
        $data["request_reason_id"] = $this->db->SqlVal($r_data["request_reason_id"], "int");
        $data["request_time"] = $this->db->SqlVal($r_data["request_time"], "mytext");
        $data["is_confirmed"] = $this->db->SqlVal($r_data["is_confirmed"], "int");

        $condition = array();
        $condition["request_id"] = $this->db->SqlVal($r_contition["request_id"], "int");

		$result = $this->db->Update("transfer_order_request", $data, $condition);
        if(!$result)
		{
			$this->State = self::ERROR;
			$this->Message = "request_update_failed";
			return false;
		}else{
			$this->State = self::SUCCESS;
			$this->Message = "request_update_success";
			return true;
		}
    }

    public function get_request_type($id)
    {
        $sql = "SELECT * FROM transfer_order_request_type WHERE request_type_id = ?";
        $params = $this->db->ConvertToParamsArray([$id]);
        $result = $this->db->SelectData($sql, $params);
        return $result;
    }

    public function get_request_reasons()
    {
        $sql = "SELECT * FROM transfer_order_request_reason";
        $result = $this->db->SelectData($sql, null, null, null, null, $x, 1);
        return $result;
    }

    public function get_warehouses()
    {
        $sql = "SELECT * FROM warehouse WHERE is_selectable = 1";
        $result = $this->db->SelectData($sql, null, null, null, null, $x, 1);
        return $result;
    }

}
