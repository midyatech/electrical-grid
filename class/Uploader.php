<?php
require_once realpath(__DIR__ . '/..') . '/include/config.php';

class Uploader
{
	//state constants
	const ERROR = 0;
	const WARNING = 1;
	const SUCCESS = 2;

	//Maximum allower file size in MB
	CONST MAX_SIZE = 5;

	CONST BASE_PATH = "attachment/"; //"../../attachment/";
	CONST CLIENT_APPLICATION_FORM_PATH = "client_application_form/";
	CONST DOC_IN_PATH = "doc_in/";
	CONST DOC_OUT_PATH = "doc_out/";
	CONST DOC_IN_TRACE_FORM_PATH = "doc_in_trace_form/";
	CONST DOC_OUT_TRACE_FORM_PATH = "doc_out_trace_form/";
	CONST UNIT_ATTACHMENT_PATH="unit_attachment/";
	CONST USER_PIC_PATH = "user_pic/";

	protected $text_ext = array("txt", "csv", "tsv");
	protected $pdf_ext = array("pdf");
	protected $img_ext = array("gif", "jpeg", "jpg", "png", "JPG");
	protected $img_pdf = array("gif", "jpeg", "jpg", "png", "JPG", "pdf");

	protected $text_type = array("text/plain", "text/tab-separated-values");
	protected $pdf_type = array("application/pdf");
	protected $img_type = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
	protected $img_pdf_type = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png","application/pdf","image/JPG");

	public $Message;
	public $State;

	public function UploadFile($file, $generate_new_name, $key=NULL, $attach_type, $overright = false)
	{
		$allowUpload = true;
		$return_value = false;

		switch($attach_type)
		{
			case "doc_in":
				$path = self::DOC_IN_PATH;
				$allowed_type = "pdf";
				break;

			case "doc_in_trace_form":
				$path = self::DOC_IN_TRACE_FORM_PATH;
				$allowed_type = "pdf";
				break;

			case "doc_out_trace_form":
				$path = self::DOC_OUT_TRACE_FORM_PATH;
				$allowed_type = "pdf";
				break;

			case "client_application_form":
				$path = self::CLIENT_APPLICATION_FORM_PATH;
				$allowed_type = "pdf";
				break;

			case "user_pic":
				$path = self::USER_PIC_PATH;
				$allowed_type = "image";
				break;

			case "doc_out":
				$path = self::DOC_OUT_PATH;
				$allowed_type = "pdf";
				break;
			case "unit_attachment":
				$path = self::UNIT_ATTACHMENT_PATH;
				$allowed_type = "img_pdf";
				break;
			default:
				$allowUpload = false;
				$this->Message = "wrong_attachment_type";
				break;
		}

		if($allowUpload)
		{
			//get extension and type
			$type = $file["type"];
			$exploded = explode(".", $file["name"]);
			$extension = end($exploded);
			if($generate_new_name) {
				$name  = $this-> GenerateFileName($key, $attach_type);
				$name .= '.'.$extension;
			} else {
				$name = $file["name"];
			}

			if($this->FileTypeAllowed($allowed_type, $extension, $type)) {
				//Check for upload error
				if ($file["error"] > 0) {
					$allowUpload = false;
					//$this->Message = $file["error"];
					$this->Message = "Server Error, could not upload file";
				} else {
					//Check file size
					$file_size = $file["size"] / 1024 / 1024; //MB
					if($file_size <= self::MAX_SIZE) {
						//echo $path . $name."<br>";
						if (file_exists($path . $name) && !$overright) {
							$allowUpload = false;
							$this->Message ="file_name_already_exists";
						} else {
							//Save file
							move_uploaded_file($file["tmp_name"], ROOT_PATH."/".self::BASE_PATH.$path. $name);
							//echo ROOT_PATH."/".self::BASE_PATH.$path. $name;
							//self::BASE_PATH.$path. $name;
							$return_value = $name;
						}
					} else {
						$allowUpload = false;
						$this->Message ="File size cannot be larger than ". self::MAX_SIZE ." MB";
					}
				}
			} else {
				//Extention is not allowed
				$allowUpload = false;
				$this->Message = "file_type_not_allowd";
			}
		}

		return $return_value;
	}

	public function GenerateFileName($key, $attach_type)
	{
		switch($attach_type)
		{
			/*case "admin_doc":
				return "adm_doc_".date("YmdHis")."_".rand(100,999);
				break;
			case "employee_doc":
				return $key."_".date("YmdHis")."_".rand(100,999);
				break;
			case "employee_pic":
				return "hr_pic_".$key."_".date("YmdHis")."_".rand(100,999);
				break;
			case "student_doc":
				return "st_doc_".date("YmdHis")."_".rand(100,999);
				break;*/
			case "pic":
				return "pic_".$key."_".date("YmdHis")."_".rand(100,999);
				break;
			default:
				return $key."_".date("YmdHis")."_".rand(100,999);
				break;
		}
	}


	public function FileTypeAllowed($allowed_type, $extension, $type)
	{

		switch ($allowed_type)
		{
			case "pdf":
				return $this->CheckExtensionAndType($extension, $this->pdf_ext, $type, $this->pdf_type);
				break;
			case "image":
				return $this->CheckExtensionAndType($extension, $this->img_ext, $type, $this->img_type);
				break;

			case "img_pdf":
				return $this->CheckExtensionAndType($extension, $this->img_pdf, $type, $this->img_pdf_type);
				break;
			case "text":
				return $this->CheckExtensionAndType($extension, $this->text_ext, $type, $this->text_type);
				break;
		}
	}

	public function CheckExtensionAndType($extension, $extensionsArray, $type, $typeArray)
	{
		//	if(in_array($extension, $extensionsArray) && in_array($type, $typeArray)) update by chia
		if(in_array($extension, $extensionsArray))
			return true;
		else
			return false;

	}


}
?>
