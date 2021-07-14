<?php
require_once(realpath(dirname(__FILE__)) . '/MysqliDB.php');

/**
 * @access public
 */
class Tree {

	//state constants
	const ERROR = 0;
	const WARNING = 1;
	const SUCCESS = 2;
	//tree node status constants
	const STATUS_MERGED = 5;
	const STATUS_MOVED = 4;
	const STATUS_DELETED = 3;
	const STATUS_INACTIVE = 2;
	const STATUS_ACTIVE = 1;


	/**
	 * @AttributeType string
	 */
	public $TreeTableName;
	public $Message;
	public $State;
	/**
	 * @AttributeType MySQLDB
	 */
	protected $db;
	protected $referenceTableName;
	protected $parentHistroyTableName;

	/**
	 * @access public
	 * @param string treeTableName
	 * @ParamType treeTableName string
	 */
	public function __construct($treeTableName) {
		$this->db = new MysqliDB();
		$this->TreeTableName = $treeTableName;
		$this->referenceTableName = $this->TreeTableName. "_NODE_REFERENCE";
		$this->parentHistroyTableName = $this->TreeTableName. "_PARENT_HISTORY";
	}

	public function __destruct() {
		$this->db = null;
	}
	/**
	 * @access public
	 * @param int nodeId
	 * @param string nodeName
	 * @param int nodeParent
	 * @param boolean selectable
	 * @param boolean private
	 * @param int status
	 * @param parameter
	 * @return boolean
	 * @ParamType nodeId int
	 * @ParamType nodeName string
	 * @ParamType nodeParent int
	 * @ParamType selectable boolean
	 * @ParamType private boolean
	 * @ParamType status int
	 * @ParamType parameter
	 * @ReturnType boolean
	 */
	public function AddNode($nodeName, $nodeParentId, $selectable, $status=1, $coordinates=NULL, $color=NULL) {

		//Validate node name
		if(!$this->ValidateNodeName($nodeName))
		{
			return false;
		}else{
			//Check if parent exists
			$parentNode = $this->GetNodeInfo($nodeParentId);
			if(!$parentNode){
				return false;
			}else{
				//Check the parent status
				if(!$this->CheckNodeStatus($parentNode[0]['STATUS_ID'])){
					$this->State = self::WARNING;
					return false;
				}else{
					//Validate that node name does not already exist under the parent
					if($this->CheckNameWithSiblings($nodeName, $nodeParentId)){
						//All checks passed, we can add now
						$data = array();
						$id = $this->db->GetNewID($this->TreeTableName, "NODE_ID");
						$data['NODE_ID'] = $id;
						$data['NODE_NAME']= $this->db->SqlVal($nodeName,"mytext");
						$data['PARENT_ID']= $this->db->SqlVal($nodeParentId,"int");
						$data['SELECTABLE'] = $this->db->SqlVal($selectable, "int");
						$data['STATUS_ID'] = $this->db->SqlVal(1, "int");
						$data['NODE_PATH']= $this->db->SqlVal($this->GetNodePath($nodeParentId).".".$id, "mytext");
						$data['COORDINATES'] = $this->db->SqlVal($coordinates, "mytext");
						$data['COLOR'] = $this->db->SqlVal($color, "mytext");

						if( $coordinates != "" ){
							$CenterPoints = $this->GetCenterArea( $coordinates );
							$data['LAT_CENERT'] = $CenterPoints["lat"];
							$data['LONG_CENERT'] = $CenterPoints["long"];
						}

						$res = $this->db->Insert($this->TreeTableName, $data);

						$this->State = $this->db->state;
						if(!$res)
						{
							$this->Message = "tree_node_insert_failed";
							return false;
						}else{
							$this->Message = "tree_node_insert_success";
							return $id;
						}
					}else{
						$this->Message = "tree_node_name_sibling_taken";
						return false;
					}
				}
			}
		}
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @param string nodeName
	 * @param int nodeParent
	 * @param boolean selectable
	 * @param boolean private
	 * @param int status
	 * @param parameter
	 * @return boolean
	 * @ParamType nodeId int
	 * @ParamType nodeName string
	 * @ParamType nodeParent int
	 * @ParamType selectable boolean
	 * @ParamType private boolean
	 * @ParamType status int
	 * @ParamType parameter
	 * @ReturnType boolean
	 */
	public function EditNode($nodeId, $nodeName=NULL, $nodeParentId=NULL, $selectable=NULL, $status=null, $coordinates=null, $color=null) {
		//Check if node exists
		$nodeInfo = $this->GetNodeInfo($nodeId);
		if(!$nodeInfo){
			//echo "EditNode: step-1";
			return false;
		}else{
			$data = array();
		//echo "step 1";
			//In case we want to update either the parent, or the node name
			if($nodeName!==NULL || $nodeParentId!==NULL)
			{
				//echo "EditNode: step-2";
				//Validate node name
				if($nodeName!==NULL){
					if(!$this->ValidateNodeName($nodeName)){
				//	echo "EditNode: step-3";
						return false;
					}else{
						$data['NODE_NAME']= $this->db->SqlVal($nodeName,"mytext");
					}
					//parameter used to check node name with siblings
					//if passed then use the new node name, otherwise use the old name
				//echo "EditNode: step-4";
					$name = $nodeName;
				}else{
					$name = $nodeInfo[0]['NODE_NAME'];
				}
				//Validate Parent
				if($nodeParentId!==NULL){
					//Several checks on node-parent restrictions
					$parentNode = $this->ValidateParent($nodeId, $name, $nodeParentId);
					if(!$parentNode){
						//echo "EditNode: step-5";
						return false;
					}else{
						$data['PARENT_ID']= $this->db->SqlVal($nodeParentId,"int");
						$data['NODE_PATH']= $this->db->SqlVal($parentNode[0]["NODE_PATH"].".".$nodeId, "mytext");
					}

					//parameter used to check node name with siblings
					//if passed then use the new parent, otherwise use the old parent
					//echo "EditNode: step-6";
					$parentId = $nodeParentId;
				}else{
					$parentId = $nodeInfo[0]['PARENT_ID'];
				}

				//Validate Name under Parent
				//Validate that name does not exist under the same parent
				if(!$this->CheckNameWithSiblings($name, $parentId, $nodeId)){
					//echo "EditNode: step-7";
					$this->Message = "tree_node_name_sibling_taken";
					return false;
				}
			}

			if($selectable!==NULL)  $data['SELECTABLE'] = $this->db->SqlVal($selectable, "int");
			if($status!==NULL) 	  	$data['STATUS_ID'] = $this->db->SqlVal($status, "int");
			if($color!==NULL)		$data['COLOR'] = $this->db->SqlVal($color, "mytext");
			if($coordinates!==NULL)	{
				$data['COORDINATES'] = $this->db->SqlVal($coordinates, "mytext");

				$CenterPoints = $this->GetCenterArea( $coordinates );
				$data['LAT_CENERT'] = $CenterPoints["lat"];
				$data['LONG_CENERT'] = $CenterPoints["long"];
			}

			$condition = array();
			$condition['NODE_ID'] = $this->db->SqlVal($nodeId,"int");
			//echo "EditNode: step-8 ::: ".$status."::::";
			$res = $this->db->Update($this->TreeTableName, $data, $condition);
			if(!$res)
			{
				$this->State = $this->db->state;
				$this->Message = "tree_node_update_failed";
				//echo "EditNode: step-9";
				return false;
			}else{

				// //save history of old parent
				// if($nodeParentId!==NULL){
				// 	//save history for old parent id
				// 	//echo "EditNode: step-10";
				// 	if(!$this->SaveParentHistory($nodeId, $nodeInfo[0]["PARENT_ID"]))
				// 	{
				// 		//echo "EditNode: step-11";
				// 		return false;
				// 	}
				// }
				//echo "EditNode: step-12";
				$this->State = self::SUCCESS;
				$this->Message = "tree_node_update_success";
				return true;
			}
		}
	}

	public function DeleteNode($nodeId)
	{
		//not implemented
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @return boolean
	 * @ParamType nodeId int
	 * @ReturnType boolean
	 */
	public function SetNodeDeleted($nodeId)
	{
		$res = $this->EditNode($nodeId, NULL, NULL, NULL, self::STATUS_DELETED);
		if(!$res)
		{
			$this->State = $this->db->state;
			$this->Message = "tree_node_deleted_failed";
			return false;
		}else{

			$this->State = self::SUCCESS;
			$this->Message = "tree_node_deleted_success";
			return true;
		}
	}

	public function SetNodeUnlocked($nodeId)
	{
		$res = $this->EditNode($nodeId, NULL, NULL, NULL, self::STATUS_ACTIVE);
		if(!$res)
		{
			$this->State = $this->db->state;
			$this->Message = "tree_node_unlocked_failed";
			return false;
		}else{

			$this->State = self::SUCCESS;
			$this->Message = "tree_node_unlocked_success";
			return true;
		}
	}
	public function MoveNode($nodeId, $newNodeParentId)
	{
		//Update node children
		if(!$this->SetNodeParent($nodeId, $newNodeParentId)){
			//echo "AFTER SET NODE PARENT";
			return false;
		}

		//Update status
		if(!$this->SetNodeStatus($nodeId, self::STATUS_MOVED)){
			return false;
		}
		return true;
	}


	/**
	 * @access public
	 * @param int nodeId1
	 * @param int nodeId2
	 * @return int
	 * @ParamType nodeId1 int
	 * @ParamType nodeId2 int
	 * @ReturnType int
	 */
	public function MergeNodes($nodeIds, $newNodeName, $parentId, $selectable) {
		$return = false;
		if(count($nodeIds)>1){
			//check if merged nodes exists
			for($i=0; $i < count($nodeIds); $i++){
				if(!$this->NodeExists($nodeIds[$i])){
					$this->State = self::WARNING;
					$this->Message ="tree_merged_nodes_not_valid";
					return false;
				}
			}

			//check if new parent exists
			if(!$this->NodeExists($parentId)){
				$this->State = self::WARNING;
				$this->Message ="tree_parent_for_merged_nodes_not_valid";
				return false;
			}


			$parent = $this->GetNodeParent($nodeIds[0]);

			//check if merged under their parent
			if($parent[0]["NODE_ID"] != $parentId)
			{
				$this->Message ="tree_merged_node_under_another_parent";
				return false;
			}

			//Check if all merged nodes has the same parent
			for($i=1; $i < count($nodeIds); $i++){
				$node_parent_id = $this->GetNodeParent($nodeIds[$i]);
				if($node_parent_id[0]["NODE_ID"] != $parent[0]["NODE_ID"])
				{
					$this->Message ="tree_merged_nodes_have_different_parent";
					return false;
				}
			}
			//Check if same level
			$lvl = $this->GetNodeLevel($nodeIds[0]);
			for($i=1; $i < count($nodeIds); $i++){
				if($this->GetNodeLevel($nodeIds[$i]) != $lvl){
					$this->Message ="tree_node_different_level";
					return false;
				}
			}

			//if the same level proceed

			//Create new node
			$newNodeId = $this->AddNode($newNodeName, $parentId, $selectable);
			if(!$newNodeId){
				$this->Message ="tree_create_new_node_faild";
				return false;
			}



			/*//Update parent of tree of old nodes
			for($i=0; $i < count($nodeIds); $i++){
				if(!$this->SetNodeParent($nodeIds[$i], $newNodeId)){
					$this->Message ="tree_node_merge_failed";
					return false;
				}
			}*/
			// Get children of merged nodes
			for($i=0; $i < count($nodeIds); $i++){
				$children = $this->GetNodeChildren($nodeIds[$i]);
				if(is_array($children)){
					for ($j=0; $j<count($children); $j++){
							if(!$this->CheckNameWithSiblings($children[$j]["NODE_NAME"], $newNodeId)){
								$this->Message ="tree_dublicate_node_name";
								return false;
							}
							else
							{
								//Update parent ID and Node Path for children
								$res = $this->SetNodeParent($children[$j]["NODE_ID"], $newNodeId);
								if(!$res)
								{
									$this->Message ="tree_move_children_to_new_node_faild";
									return false;
								}

							}
						}
				}

			}

			//Add reference in the new node to the old ones
			for($i=0; $i < count($nodeIds); $i++){
				if(!$this->AddReferenceNode($nodeIds[$i], $newNodeId)){
					$this->Message ="tree_node_merge_add_reference_failed";
					return false;
				}
			}

			//mark old parent nodes	as merged
			for($i=0; $i < count($nodeIds); $i++){
				if(!$this->SetNodeStatus($nodeIds[$i], self::STATUS_MERGED)){
					$this->Message ="tree_set_nodes_status_failed";
					return false;
				}
			}

			//if all goes well return true
			$this->State = self::SUCCESS;
			$this->Message = "success";
			return true;
		}else{
			$this->Message = "tree_node_merge_only_one_node";
			return false;
		}
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @ParamType nodeId int
	 */
	public function GetNodeInfo($nodeId)
	{
		$cat_field = $Other_field = "";
		if($this->TreeTableName =="DIR_TREE"){
			$cat_field .= ' org_dir_node_id, ';
		}

		if($this->TreeTableName =="AREA_TREE"){
			$Other_field .= ' , `'.$this->TreeTableName.'`.`LAT_CENERT`, `'.$this->TreeTableName.'`.`LONG_CENERT`';
		}

		$sql = 'SELECT `'.$this->TreeTableName.'`.`NODE_ID`, `'.$this->TreeTableName.'`.`NODE_PATH`, `'.$this->TreeTableName.'`.`NODE_NAME`, `'.$this->TreeTableName.'`.`PARENT_ID`, `'.$this->TreeTableName.'`.`STATUS_ID`, `'.$this->TreeTableName.'`.`SELECTABLE`,'. $cat_field.'
				(CHAR_LENGTH(`'.$this->TreeTableName.'`.`NODE_PATH`) - CHAR_LENGTH(replace(`'.$this->TreeTableName.'`.`NODE_PATH`, \'.\',\'\')))+1 AS `LVL`, `'.$this->TreeTableName.'`.`COORDINATES`, `'.$this->TreeTableName.'`.`COLOR` '.$Other_field.'
				FROM `'.$this->TreeTableName.'` ';
		if($this->TreeTableName =="DIR_TREE"){
			$sql .=	'INNER JOIN `settings` ON settings.id = 1 ';
		}
		$sql .=	'WHERE `'.$this->TreeTableName.'`.`NODE_ID`=?';

		$params = $this->db->ConvertToParamsArray(array($nodeId));
		$nodeInfo = $this->db->SelectData($sql, $params);
		$this->State = $this->db->state;
		if($nodeInfo === NULL){
			$this->Message = "tree_node_not_exists";
			return false;
		}else if($nodeInfo === FALSE){
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->Message = "success";
			return $nodeInfo;
		}
	}


	public function GetReferenceNodes($nodeId)
	{
		$nodesInfo = $this->db->SelectData('SELECT `NODE_ID`, `NODE_PATH`, `NODE_NAME`, `PARENT_ID`, `STATUS_ID`, `SELECTABLE`, `PRIVATE`, `STATUS_ID`, `REFERENCE_DATE`
												FROM `'.$this->TreeTableName.'`
												INNER JOIN `'.$this->referenceTableName.'` ON `'.$this->referenceTableName.'`.`OLD_NODE_ID` = `'.$this->TreeTableName.'`.`NODE_ID`
												WHERE `NEW_NODE_ID`='.$this->db->SqlVal($nodeId, "int"));
		$this->State = $this->db->state;
		if($nodesInfo === NULL){
			//empty data
			$this->Message = "tree_node_not_exists";
			return false;
		}else if($nodesInfo === FALSE){
			//error
			$this->Message = "tree_node_select_reference_failed";
			return false;
		}else{
			$this->Message = "success";
			return $nodesInfo;
		}
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @return obj[]
	 * @ParamType nodeId int
	 * @ReturnType obj[]
	 */
	public function GetNodePath($nodeId)
	{
		$nodeInfo = $this->GetNodeInfo($nodeId);
		if(!$nodeInfo){
			return false;
		}else{
			return $nodeInfo[0]["NODE_PATH"];
		}
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @param string spearator
	 * @return string
	 * @ParamType nodeId int
	 * @ParamType spearator string
	 * @ReturnType string
	 */
	public function GetPathString($nodeId, $spearator=" / ")
	{
		$str = "";
		if($nodeId != null){
			$nodeInfo = $this->GetNodeInfo($nodeId);
			$nodeInfo[0]['NODE_PATH'];
			$node_path = explode(".",$nodeInfo[0]['NODE_PATH']);
			for($i=1;$i<count($node_path)-1;$i++)
			{
				$parent_info = $this->GetNodeInfo($node_path[$i]);
				$str .=$parent_info[0]['NODE_NAME'].$spearator;
			}
			$str .=$nodeInfo[0]['NODE_NAME'];
		}
		return $str;
	}

	public function GetOrgPathString($nodeId, $spearator=" / ")
	{
		if($nodeId != null){
			$str = "";
			$nodeInfo = $this->GetNodeInfo($nodeId);
			$nodeInfo[0]['NODE_PATH'];
			$node_path = explode(".",$nodeInfo[0]['NODE_PATH']);
			for($i=1;$i<count($node_path)-1;$i++)
			{
				$parent_info = $this->GetNodeInfo($node_path[$i]);
				/*if($parent_info[0]["NODE_ID"] == $parent_info[0]["org_dir_node_id"]){
					break;
				}*/
				$str .=$parent_info[0]['NODE_NAME'].$spearator;
			}
			$str .=$nodeInfo[0]['NODE_NAME'];
		}else{
			$str = "";
		}
		return $str;
	}

	/*public function GetAncestors($nodeId)
	{
		$ancestors = $this->db->SelectData("SELECT `NODE_ID`, `NODE_NAME`, (CHAR_LENGTH(`NODE_PATH`) - CHAR_LENGTH(replace(`NODE_PATH`, '.', '')))+1 AS `LVL`
											FROM `".$this->TreeTableName."`
											START WITH `NODE_ID`=".$this->db->SqlVal($nodeId, "int")."
											CONNECT BY PRIOR `PARENT_ID` =`NODE_ID`
											ORDER BY `NODE_PATH`");

		$this->State = $this->db->state;
		if($ancestors === NULL){
			//empty data
			$this->Message = "tree_node_not_exists";
			return false;
		}else if($ancestors === FALSE){
			//error
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->Message = "success";
			return $ancestors;
		}
	}*/

	/**
	 * @access public
	 * @param int nodeId
	 * @ParamType nodeId int
	 */
	public function SetNodeParent($nodeId, $parentId, $onlySubordinates=false) {
		$nodeInfo = $this->GetNodeInfo($nodeId);
		if(!$nodeInfo){
			//echo "NODE INFO";
			return false;
		}else{
			if($nodeInfo[0]["PARENT_ID"] == NULL)
			{
				$this->Message = "The_First_Node_could_not_be_moved";
				return false;
			}
			//Validate parent
			if(!$this->ValidateParent($nodeId, $nodeInfo[0]["NODE_NAME"], $parentId)){
				//parent is invalid
				$this->Message = "The_Parent_Node_is_not_valid";
				return false;
			}else{

				$data = array();
				$data["PARENT_ID"]= $this->db->SqlVal($parentId, "int");
				$condition = array("NODE_ID"=> $this->db->SqlVal($nodeId, "int"));
				$oldNodePath = $nodeInfo[0]["NODE_PATH"];
				$newParentPath = $this->GetNodePath($parentId);

				if(!$onlySubordinates)
				{

					//check name with new siblings first
					if(!$this->CheckNameWithSiblings($nodeId, $parentId)){
						$this->Message = "The_Parent_has_the_same_name";
						return false;
					}

					//Update parent_id
					$res = $this->EditNode($nodeId, NULL, $parentId, NULL, NULL, NULL, NULL);
					if(!$res){

						$this->Message = "tree_node_update_parent_failed";
						return false;
					}else{
						$newNodePath = $newParentPath.".".$nodeId;
					}

				}else {

					//check children names with new siblings first
					$children = $this->GetNodeChildren($nodeId);
					for ($i=0; $i<=count($children); $i++){
						if(!$this->CheckNameWithSiblings($children[$i]["NODE_NAME"], $parentId)){
							return false;
						}
					}

					//update parent id for children
					for ($i=0; $i<=count($children); $i++){
						if(!$this->Edit($children[$i]["NODE_ID"], NULL, $parentId)){
							return false;
						}
					}

					$newNodePath = $newParentPath;
				}

				//Update node_path
				$data2 = array();
				$data2['NODE_PATH']= 'REPLACE("NODE_PATH", \''.$oldNodePath.'\', \''.$newNodePath.'\')';
				$condition2 = array();
				$condition2['NODE_PATH']=array("Operator"=>"LIKE","Value"=> $oldNodePath."%", "Type"=>"mytext");

				$res2 = $this->db->Update($this->TreeTableName, $data2, $condition2);

				//$return2 = false;
				if(!$res2){

					////TBD undo update parent id
					$this->State = $this->db->state;
					if($this->db->message == "db_update_empty"){

						$return2 = true;
					}else{
						$this->Message = "tree_node_update_subordinates_path_failed";

						return false;
					}
				}


				//if($res && $return2){
				if($res){
					$this->Message = "tree_node_update_parent_succeed";
					$this->State = self::SUCCESS;
					return true;

				}
				else{
					$this->Message = $this->db->message;
					$this->Message = "tree_node_update_parent_failed";
					return false;

				}
			}
		}
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @return obj[]
	 * @ParamType nodeId int
	 * @ReturnType obj[]
	 */
	public function GetNodeParent($nodeId) {
		$parentNode = $this->db->SelectData('SELECT `T2`.`NODE_ID`, `T2`.`NODE_PATH`, `T2`.`NODE_NAME`, `T2`.`PARENT_ID`, `T2`.`SELECTABLE`, `T2`.`PRIVATE`, `T2`.`STATUS_ID`
												FROM `'.$this->TreeTableName.'` `T1`
												INNER JOIN `'.$this->TreeTableName.'` `T2`
												ON `T1`.`PARENT_ID` = `T2`.`NODE_ID`
												WHERE `T1`.`NODE_ID`='.$this->db->SqlVal($nodeId, "int"));
		$this->State = $this->db->state;
		if($parentNode === NULL){
			//empty data
			$this->Message = "tree_node_not_exists";
			return false;
		}else if($parentNode === FALSE){
			//error
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->Message = "success";
			return $parentNode;
		}
	}

	public function GetNextNodeByLevel($node_id)
	{
		if($this->TreeTableName == "POSITION_TREE"){

			//get parent id
			$old_position_details = $this->GetNodeInfo($node_id);
			$old_position_parent_id = $old_position_details[0]["PARENT_ID"];

			//get level rank from parent level node
			$old_position_parent = $this->GetNodeInfo($old_position_parent_id);
			$old_position_parent_level_rank = $old_position_parent[0]["LEVEL_RANK"];

			//get node of next level rank
			$sql = 'SELECT `NODE_ID`, `NODE_PATH`, `NODE_NAME`, `PARENT_ID`, `SELECTABLE`, `PRIVATE`, `STATUS_ID`,
					(CHAR_LENGTH(`NODE_PATH`) - CHAR_LENGTH(replace(`NODE_PATH`, \'.\',\'\')))+1 AS `LVL`, LEVEL_RANK
					FROM `'.$this->TreeTableName.'`
					WHERE `LEVEL_RANK`='.$this->db->SqlVal($old_position_parent_level_rank-1, "int");

			$nodeInfo = $this->db->SelectData($sql);

			$this->State = $this->db->state;
			if($nodeInfo === NULL){
				$this->Message = "tree_node_not_exists";
				return false;
			}else if($nodeInfo === FALSE){
				$this->Message = $this->db->message;
				return false;
			}else{
				$this->Message = "success";
				return $nodeInfo;
			}
		}else{
			return false;
		}
	}

	public function GetNextNodeByDegree($node_id)
	{
		if($this->TreeTableName == "POSITION_TREE"){

			//get parent id
			$old_position_details = $this->GetNodeInfo($node_id);
			$old_position_parent_id = $old_position_details[0]["PARENT_ID"];

			//get parent level node
			$old_position_parent = $this->GetNodeInfo($old_position_parent_id);
			$old_position_grand_parent_id = $old_position_parent[0]["PARENT_ID"];

			//get degree rank from grand parent level node
			$old_position_grand_parent = $this->GetNodeInfo($old_position_grand_parent_id);
			$old_position_parent_degree_rank = $old_position_grand_parent[0]["DEGREE_RANK"];

			//get node for next degree rank
			$sql = 'SELECT `NODE_ID`, `NODE_PATH`, `NODE_NAME`, `PARENT_ID`, `SELECTABLE`, `PRIVATE`, `STATUS_ID`,
					(CHAR_LENGTH(`NODE_PATH`) - CHAR_LENGTH(replace(`NODE_PATH`, \'.\',\'\')))+1 AS `LVL`, LEVEL_RANK
					FROM `'.$this->TreeTableName.'`
					WHERE `DEGREE_RANK`='.$this->db->SqlVal($old_position_parent_degree_rank-1, "int");
			$new_position_grand_parent = $this->db->SelectData($sql);
			$new_position_grand_parent_id = $new_position_grand_parent[0]["NODE_ID"];

			//get max level rank for parent id
			$sql = 'SELECT `NODE_ID`, `NODE_PATH`, `NODE_NAME`, `PARENT_ID`, `SELECTABLE`, `PRIVATE`, `STATUS_ID`,
					(CHAR_LENGTH(`NODE_PATH`) - CHAR_LENGTH(replace(`NODE_PATH`, \'.\',\'\')))+1 AS `LVL`, LEVEL_RANK
					FROM `'.$this->TreeTableName.'`
					WHERE LEVEL_RANK = (
						SELECT MAX(LEVEL_RANK) FROM POSITION_TREE WHERE `PARENT_ID`='.$this->db->SqlVal($new_position_grand_parent_id, "int").'
					)';
			$nodeInfo = $this->db->SelectData($sql);

			$this->State = $this->db->state;
			if($nodeInfo === NULL){
				$this->Message = "tree_node_not_exists";
				return false;
			}else if($nodeInfo === FALSE){
				$this->Message = $this->db->message;
				return false;
			}else{
				$this->Message = "success";
				return $nodeInfo;
			}
		}else{
			return false;
		}
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @return boolean
	 * @ParamType nodeId int
	 * @ReturnType boolean
	 */
	public function SetNodeStatus($nodeId, $statusId) {
		$res = $this->EditNode($nodeId, NULL, NULL, NULL, NULL, $statusId, NULL);
		if(!$res){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @return int
	 * @ParamType nodeId int
	 * @ReturnType int
	 */
	public function GetNodeStatus($nodeId) {
		$nodeInfo = $this->GetNodeInfo($nodeId);
		if(!$nodeInfo){
			return false;
		}else{
			return $nodeInfo[0]["STATUS_ID"];
		}
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @ParamType nodeId int
	 */
	public function GetNodeChildren($nodeId, $showDeleted = NULL) {
		$condition = "";
		$nodeInfo = $this->GetNodeInfo($nodeId);
		if(!$nodeInfo){
			return false;
		}else{

			if($showDeleted == true)
			{
				$condition = 'OR `STATUS_ID` = '. self::STATUS_DELETED .'';
			}
			//selects only active children
			$sql = "SELECT `NODE_ID`, `NODE_NAME`, `NODE_PATH`, (CHAR_LENGTH(`NODE_PATH`) - CHAR_LENGTH(replace(`NODE_PATH`, '.', '')))+1 AS `LVL`,
												`SELECTABLE`, `STATUS_ID`, `COORDINATES`, `COLOR`
												FROM `".$this->TreeTableName."`
												WHERE `PARENT_ID`= ?
												AND (`STATUS_ID` = ". self::STATUS_ACTIVE ."
														OR `STATUS_ID` = ". self::STATUS_MOVED ." ".$condition.")
												ORDER BY `NODE_NAME`";
			$filter = array();
			$filter[] = $this->db->SqlVal($nodeId,"int");
			$params = $this->db->ConvertToParamsArray($filter);
			$childNodes = $this->db->SelectData($sql, $params);

			$this->State = $this->db->state;
			if($childNodes === NULL){
				//empty data
				$this->Message = "tree_node_no_children";
				return false;
			}else if($childNodes === FALSE){
				//error
				$this->Message = $this->db->message;
				return false;
			}else{
				$this->State = self::SUCCESS;
				$this->Message = "success";
				return $childNodes;
			}
		}
	}

	public function SelectNodeChildren($nodeId, $showDeleted = NULL) {
		$condition = "";
		$nodeInfo = $this->GetNodeInfo($nodeId);
		if(!$nodeInfo){
			return false;
		}else{

			if($showDeleted == true)
			{
				$condition = 'OR `STATUS_ID` = '. self::STATUS_DELETED .'';
			}
			//selects only active children
			$sql = "SELECT `NODE_ID`, `NODE_NAME`, `NODE_PATH`, (CHAR_LENGTH(`NODE_PATH`) - CHAR_LENGTH(replace(`NODE_PATH`, '.', '')))+1 AS `LVL`,
												`SELECTABLE`, `STATUS_ID`
												FROM `".$this->TreeTableName."`
												WHERE `PARENT_ID`= ?
												AND (`STATUS_ID` = ". self::STATUS_ACTIVE ."
														OR `STATUS_ID` = ". self::STATUS_MOVED ." ".$condition.")
												ORDER BY `NODE_NAME`";
			$filter = array();
			$filter[] = $this->db->SqlVal($nodeId,"int");
			$params = $this->db->ConvertToParamsArray($filter);
			$recordsCount = null;
			$childNodes = $this->db->SelectData($sql, $params, NULL, 0, 0, $recordsCount, 1);

			$this->State = $this->db->state;
			if($childNodes === NULL){
				//empty data
				$this->Message = "tree_node_no_children";
				return false;
			}else if($childNodes === FALSE){
				//error
				$this->Message = $this->db->message;
				return false;
			}else{
				$this->State = self::SUCCESS;
				$this->Message = "success";
				return $childNodes;
			}
		}
	}

	/**
	 * @access public
	 * @param int nodeId
	 * @ParamType nodeId int
	 */
	/*public function GetTree($nodeId) {

		$nodeInfo = $this->GetNodeInfo($nodeId);
		if(!$nodeInfo){
			return false;
		}else{
			//select everything under the node
			$subTree = $this->db->SelectData("SELECT `NODE_ID`, `NODE_PATH`, `NODE_NAME`, (CHAR_LENGTH(`NODE_PATH`) - CHAR_LENGTH(replace(`NODE_PATH`, '.', '')))+1 AS `LVL`, `STATUS_ID`
												FROM `".$this->TreeTableName."`
												START WITH `NODE_ID`=".$this->db->SqlVal($nodeId, "int")."
												CONNECT BY PRIOR `NODE_ID` = `PARENT_ID`
												ORDER BY `NODE_PATH`");
			$this->State = $this->db->state;
			if($subTree === NULL){
				//empty data
				$this->Message = "tree_node_no_children";
				return false;
			}else if($subTree === FALSE){
				//error
				$this->Message = $this->db->message;
				return false;
			}else{
				$this->State = self::SUCCESS;
				$this->Message = "success";
				return $subTree;
			}
		}


	}*/

	/**
	 * @access public
	 * @param int nodeId
	 * @return int
	 * @ParamType nodeId int
	 * @ReturnType int
	 */
	public function GetNodeLevel($nodeId) {
		$nodeInfo = $this->GetNodeInfo($nodeId);
		if(!$nodeInfo){
			return false;
		}else{
			return $nodeInfo[0]["LVL"];
		}
	}

	/**
	 * @access public
	 * @param string nodeName
	 * @return obj[]
	 * @ParamType nodeName string
	 * @ReturnType obj[]
	 */
	public function FindNode($nodeName) {
		$nodes = $this->db->SelectData("SELECT `NODE_ID`, `NODE_PATH`, `NODE_NAME`, (CHAR_LENGTH(`NODE_PATH`) - CHAR_LENGTH(replace(`NODE_PATH`, '.', '')))+1 AS `LVL`, `STATUS_ID`
												FROM `".$this->TreeTableName."`
												WHERE `NODE_NAME` LIKE ".$this->db->SqlVal('%'.$nodeName.'%', "mytext")."
												ORDER BY `NODE_PATH`");
		$this->State = $this->db->state;
		if($nodes === NULL){
			//empty data
			$this->Message = "tree_node_search_empty";
			return false;
		}else if($nodes === FALSE){
			//error
			$this->Message = $this->db->message;
			return false;
		}else{
			$this->State = self::SUCCESS;
			$this->Message = "success";
			return $nodes;
		}
	}

	protected function AddReferenceNode($nodeId, $newNodeId)
	{
		$data = array();
		$data['OLD_NODE_ID'] = $this->db->SqlVal($nodeId, "int");
		$data['NEW_NODE_ID'] = $this->db->SqlVal($newNodeId, "int");
		$data['REFERENCE_DATE'] = "SYSDATE";

		$exist = array();
		$exist['OLD_NODE_ID'] = $this->db->SqlVal($nodeId, "int");
		$exist['NEW_NODE_ID'] = $this->db->SqlVal($newNodeId, "int");

		$res = $this->db->InsertIfNotExists($this->referenceTableName, $data, $exist);
		$this->State = $this->db->state;
		$this->Message = $this->db->message;
		if(!$res){
			return false;
		}
		else
		{
			$this->Message = 'success';
			$this->State = self::SUCCESS;
			return true;
		}
	}


	protected function SaveParentHistory($nodeId, $oldParentId)
	{
		$data = array();
		$data["ID"] = "@@new_id";
		$data["NODE_ID"] = $this->db->SqlVal($nodeId, "int");
		$data["PARENT_ID"] = $this->db->SqlVal($oldParentId, "int");
		$data["CHANGE_DATE"] = "SYSDATE";

		$res = $this->db->Insert($this->parentHistroyTableName, $data);
		$this->State = $this->db->state;
		$this->Message = $this->db->message;
		if(!$res){
			return false;
		}else{
			return true;
		}
	}

	public function IsAncestor($nodeId, $subOrdinateId)
	{
		$return = false;
		$path = $this->db->SelectValue('SELECT `NODE_PATH` FROM `'.$this->TreeTableName.'`
										WHERE `NODE_ID`='.$this->db->SqlVal($subOrdinateId, "int"));

		$this->State = $this->db->state;
		if($path === NULL){
			//empty data
			$this->Message = "tree_node_not_exists";
		}else if($path === FALSE){
			//error
			$this->Message = $this->db->message;
		}else{
			if(strpos($path, $nodeId.".") === 0 || strpos($path, ".".$nodeId.".")> 0){
				$this->State = self::SUCCESS;
				$this->Message = "tree_node_is_ancestor";
				return true;
			}
		}
		//default return false
		return false;
	}


	protected function GetParentPath($nodeId)
	{
		$parentNode = $this->GetNodeParent($nodeId);
		if(!$parentNode){
			return false;
		}
		else{
			return $parentNode[0]["NODE_PATH"];
		}
	}

	public function NodeExists($id)
	{
		$res = $this->db->ValueExists($this->TreeTableName, "NODE_ID", $id);
		$this->State = $this->db->state;
		if($res === NULL){
			//something wrong
			$this->Message = $this->db->message;
			return false;
		}else if($res === FALSE){
			$this->State = self::WARNING;
			$this->Message = "tree_node_not_exists";
			return false;
		}else{
			return true;
		}

		/* 	$nodeInfo = $this->GetNodeInfo($id);
		if(!$nodeInfo){
			if($this->db->message == "db_select_empty")
				return true;
			else
				return false;
		}else
			return false; */
	}

	protected function CheckNodeStatus($statusId)
	{
		switch ($statusId)
		{
			case self::STATUS_ACTIVE:
			case self::STATUS_MERGED:
			case self::STATUS_MOVED:
				return true;
			case self::STATUS_INACTIVE:
				$this->Message = "tree_node_status_inactive";
				return false;
			case self::STATUS_DELETED:
				$this->Message = "tree_node_status_deleted";
				return false;
			default:
				$this->Message = "status_invalid";
				return false;
		}
	}

	public function CheckNameWithSiblings($nodeName, $parentId, $nodeId=NULL)
	{
		$filter = array();
		$filter[] = $this->db->SqlVal($nodeName,"mytext");
		$filter[] = $this->db->SqlVal($parentId,"int");

		$sql = 'SELECT COUNT(`NODE_ID`) FROM `'.$this->TreeTableName.'`
								WHERE `NODE_NAME`=?
								AND `PARENT_ID`=?';
		//Execlude the current node name from check if specified
		if($nodeId != NULL){
			$sql .= ' AND `NODE_ID`!=?';
			$filter[] = $this->db->SqlVal($nodeId,"int");
		}
		$params = $this->db->ConvertToParamsArray($filter);
		$nodes = $this->db->SelectValue($sql, $params);
		if($nodes===NULL){
			$this->State = $this->db->state;
			$this->Message = $this->db->message;
			return false;
		}else{
			if($nodes == 0){
				$this->State = self::SUCCESS;
				$this->Message = "success";
				return true;
			}else {
				$this->State = self::WARNING;
				$this->Message = "tree_node_name_sibling_taken";
				return false;
			}
		}
	}

	public function ValidateParent($id, $nodeName, $nodeParentId)
	{
		$parentNode = $this->GetNodeInfo($nodeParentId);
		$this->State = $this->db->state;
		$this->Message = $this->db->message;
		//Check if parent exists
		if($parentNode == NULL){
			$this->Message = "tree_node_parent_not_exists";
			return false;
		}else if($parentNode == FALSE){

			return false;
		}else{
			//Check for parent status
			if(!$this->CheckNodeStatus($parentNode[0]['STATUS_ID'])){
				//echo "CheckNodeStatus";
				return false;
			}else{
				//Check for status
				if($this->IsAncestor($id, $nodeParentId)){
					$this->State = self::WARNING;
					return false;
				}else{
					//Check for name validation with siblings
					if(!$this->CheckNameWithSiblings($nodeName, $nodeParentId, $id)){
						return false;
					}else{
						//return parent node for further operations
						return $parentNode;
					}
				}
			}
		}
	}


	/* protected function ChangeParent($nodeId, $parentId)
	{
		$children = $this->GetNodeChildren($nodeId);
		//first check names with children of new parent
		for ($i=0; $i<count($children); $i++){
			if(!$this->CheckNameWithSiblings($children[$i]["NODE_NAME"], $parentId)){
				return false;
			}
		}

		//then set parent for each child
		for ($i=0; $i<count($children); $i++){
			if(!$this->SetNodeParent($children[$i]["NODE_ID"], $parentId)){
				return false;
			}
		}
	} */

	protected function ValidateNodeName($nodeName)
	{
		////TBD
		if(false){
			$this->State = self::WARNING;
			$this->Message = "tree_node_invalid_name";
		}
		return true;
	}



	public function SelectAllNodes()
	{
		$sql = 'SELECT * FROM `'.$this->TreeTableName.'`
		WHERE `NODE_ID`>1
		ORDER BY `NODE_ID`';
		$nodes = $this->db->SelectData($sql);
		return $nodes;
	}

	public function GetFullTree()
	{
		$sql = "SELECT NODE_ID, COORDINATES, NODE_NAME, (CHAR_LENGTH(`NODE_PATH`) - CHAR_LENGTH(replace(`NODE_PATH`, '.', '')))+1 AS `LVL`, `COLOR`
				FROM `".$this->TreeTableName."`
				WHERE `NODE_ID`>1
				ORDER BY `NODE_PATH`";
		$nodes = $this->db->SelectData($sql);
		return $nodes;
	}

	public function TestUpdate($id, $parent_path)
	{
		$path = $parent_path.'.'.$id;
		$sql = 'UPDATE `'.$this->TreeTableName.'` SET `NODE_PATH` = \''.$path.'\' WHERE `NODE_ID`='.$id;
		//print $sql ."<br>";
		$nodes = $this->db->SelectData($sql);
		return true;
	}

	public function GetAreaPolygon($node_id){
		$sql = "SELECT COORDINATES FROM AREA_TREE WHERE NODE_ID = ?";
		$coordinates = $this->db->SelectValue($sql, $this->db->GenerateParam($node_id));

		if(!$coordinates){
			return false;
		}else{
			return $coordinates;
		}
	}
	public function GetNodeName($condition= null, $order = null, $start = 0, $size = 0, &$recordsCount = null){
        $params =null;
        $sql = "SELECT NODE_ID, NODE_NAME, NODE_PATH
                FROM AREA_TREE
                LEFT JOIN USER ON USER.LOGIN=AREA_TREE.NODE_ID 
                WHERE USER_ID is null and COLOR=?
                ";
        if ($condition!=null) {
            $params = $this->db->ConvertToParamsArray($condition);
        }
        $result = $this->db->SelectData($sql, $params, $order, $start, $size, $recordsCount);
        $this->Message = $this->db->message;
        $this->State = $this->db->state;
        return $result;
	}

	public function GetCenterArea($condition){
		//$condition = '[[36.153903,44.027045],[36.155505,44.031562],[36.158463,44.029947],[36.15836,44.029292],[36.158108,44.025854],[36.153989,44.027077],[36.153903,44.027045]]';
		$CenterPoint = array();
		$points = json_decode($condition);
		if(count($points) > 0){
			$lat = $lng = 0;
			for($i=0; $i<count($points); $i++){
				$lat += $points[$i][0];
				$lng += $points[$i][1];
			}
			$centerLat =  $lat/count($points);
			$centerLng = $lng/count($points);
			//update node
			$CenterPoint["lat"] = $centerLat;
			$CenterPoint["long"] = $centerLng;
		}
		return $CenterPoint;
	}

}
?>
