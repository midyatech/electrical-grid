<?php
require_once(realpath(dirname(__FILE__)) . '/Dictionary.php');
require_once(realpath(dirname(__FILE__)) . '/PHPExcel.php');

class Excel
{
	//public $type, $name, $value, $list, $options;
	protected $dictionary, $objPHPExcel, $row;

	public function __construct($language)
	{
		$this->dictionary = new Dictionary($language);
		$this->dictionary->GetAllDictionary();
		$c = new PHPExcel();
		$this->objPHPExcel = $c;
		$this->row = 1;
	}


	protected function SetProperties($title="exported"){
		$this->objPHPExcel->getProperties() ->setTitle($title);
											 /*->setCreator("Maarten Balliauw")
											 ->setLastModifiedBy("Maarten Balliauw")
											 ->setSubject("PHPExcel Test Document")
											 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
											 ->setKeywords("office PHPExcel php")
											 ->setCategory("Test result file");*/
	}

	public function DrawHeader($title, $filter_array, $page_index=0){
		$this->WriteCell('A'.$this->row, $this->dictionary->GetValue("title").":", $page_index);
		$this->WriteCell('B'.$this->row, $title, $page_index);
		$filter_text = $this->GetFilterValues($filter_array);
		if($filter_text!=""){
			$this->row++;
			$this->WriteCell('A'.$this->row, $this->dictionary->GetValue("filter").":", $page_index);
			$this->WriteCell('B'.$this->row, $filter_text, $page_index);
		}
		$this->row++;
		$this->row++;
	}

	public function DrawExcelTable($data, $meta, $starting_row=NULL, $page_index=0)
	{
		if($starting_row!=NULL)
			$this->row = $starting_row;
        //Draw header line
        $col_counter = 1;
        foreach ($meta as $fieldInfo){
			$cell_index = $this->ExcelColumnFromNumber($col_counter).$this->row;
			if (strpos($fieldInfo["column"], "ACTION_COL") !== 0 )
            {
	        	if(isset($fieldInfo["title"]))
					$content = $this->dictionary->GetValue($fieldInfo["title"]);
				else
					$content = $this->dictionary->GetValue($fieldInfo["column"]);

	            $this->WriteCell($cell_index, $content, $page_index);

	            $col_counter++;
	        }
        }
		$this->row++;

        //Draw content
        if($data!=null){
	        for ($i=0; $i<count($data); $i++){
	        	$col_counter = 1;
	            foreach ($meta as $fieldInfo)
	            {
	            	$cell_index = $this->ExcelColumnFromNumber($col_counter).$this->row;

	            	//if column is not action (it should not happen anyway)
	            	if (strpos($fieldInfo["column"], "ACTION_COL") !== 0 )
	            	{
						//if this column needs translation, pass the text to translate function
						if(isset($fieldInfo["dictionary"]) && $fieldInfo["dictionary"]=="true"){
							$content = $this->dictionary->GetValue($data[$i][$fieldInfo["column"]]);
						}else{
							$content = $data[$i][$fieldInfo["column"]];
							//echo $data[$i][$fieldInfo["column"]]."/";
						}
						$this->WriteCell($cell_index, $content, $page_index);
	            	    $col_counter++;
					}
	            }
	            $this->row++;
	        }
	    }
    }

    public function SaveExcelFile($filename){
    	$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		//$objWriter->save(realpath("./").$filename.'.xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
    }

    protected function WriteCell($cell_index, $content, $page_index=0){
    	$this->objPHPExcel->setActiveSheetIndex($page_index)->setCellValue($cell_index, $content);
    }

    protected function ExcelColumnFromNumber($column_index)
    {
        $columnString = "";
        while ($column_index > 0)
        {
            $currentLetterNumber = ($column_index - 1) % 26;
            $currentLetter = chr($currentLetterNumber + 65);
            $columnString = $currentLetter . $columnString;
            $column_index = ($column_index - ($currentLetterNumber + 1)) / 26;
        }
        return $columnString;
    }

    public function GetFilterValues($data){
    	$filter_text = "";
		if($data != NULL){
			foreach ($data as $column=>$options)
			{
				if(isset($options["Name"]))
					$name = $options["Name"];
				else
					$name = $column;

				if(isset($options["Operator"]))
					$op = $options["Operator"];
				else
					$op = "=";

				if(isset($options["Translate"]) && $options["Translate"]=="true")
					$value = $this->dictionary->GetValue($options["Value"]);
				else
					$value = $options["Value"];

				$value = trim($value, "%");

				$filter_text .= $this->dictionary->GetValue($name) ." ". $this->dictionary->GetValue($op) ." ". $value .", \r\n";
			}

		}
		return $filter_text;
	}
}
?>
