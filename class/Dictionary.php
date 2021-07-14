<?php
require_once(realpath(dirname(__FILE__)) . '/MysqliDB.php');

class Dictionary {

	protected $language;
	protected $allKeywords=array();

	public function __construct($language) {
		$this->db = new MysqliDB();
		$this->language = $language;
	}

	public function GetAllDictionary()
	{
		$query = 'SELECT LOWER(`KEYWORD`) AS `KEYWORD`, IFNULL(`'.$this->language.'`, LOWER(`KEYWORD`)) AS `VALUE` FROM `DICTIONARY`';
		$row_Dictionary =$this->db->SelectData($query);

		$Dictionary=array();
		for ( $counter = 0; $counter < count($row_Dictionary); $counter += 1) {
			$Dictionary[$row_Dictionary[$counter]['KEYWORD']]= $row_Dictionary[$counter]["VALUE"];
		}
		$this->allKeywords = $Dictionary;
		return $this->allKeywords;
	}

	public function GetValue($keyword = NULL)
	{
		if((array_key_exists(strtolower($keyword), $this->allKeywords)) && $keyword != NULL){
			return $this->allKeywords[strtolower($keyword)];
		} else {
			return $keyword;
		}
	}

	public function GetDictionaryWord($keyword)
	{
		$query = 'SELECT LOWER(`KEYWORD`) AS `KEYWORD`, IFNULL(`'.$this->language.'`, (`KEYWORD`)) AS `VALUE`
					FROM `DICTIONARY` WHERE LOWER(`KEYWORD`)
					LIKE ?';
		$filter = array();
		$filter[] = $this->db->SqlVal(strtolower($keyword),"mytext");
		$params = $this->db->ConvertToParamsArray($filter);
		$row_Dictionary =$this->db->SelectData($query, $params);
		if($row_Dictionary[0]["VALUE"]!=""){
			return $row_Dictionary[0]["VALUE"];
		}else{
			return $keyword;
		}
	}
}
?>
