<?php
// Include the main TCPDF library (search for installation path).
require_once realpath(dirname(__FILE__) . '/..') . '/tcpdf/tcpdf_include.php';
//require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once(realpath(dirname(__FILE__)) . '/Dictionary.php');

class PDF
{
	protected $dictionary, $pdf;

	public function __construct($language)
	{
		$this->dictionary = new Dictionary($language);
	}

	public function OutputPDF($title, $subject, $name, $orintation='P')
	{
		$this->pdf = new TCPDF($orintation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$this->pdf->SetCreator(PDF_CREATOR);
		$this->pdf->SetTitle($this->dictionary -> GetValue($title));
		$this->pdf->SetSubject($subject);


		// set default header data
		//$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $this->dictionary -> GetValue("list_all_employees"), PDF_HEADER_STRING);

		// set header and footer fonts
		//$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		//$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language dependent data:
		$lg = Array();
		$lg['a_meta_charset'] = 'UTF-8';
		$lg['a_meta_dir'] = 'ltr';
		$lg['a_meta_language'] = 'fa';
		$lg['w_page'] = 'page';

		// set some language-dependent strings (optional)
		$this->pdf->setLanguageArray($lg);

		//$this->pdf->SetFont('dejavusanscondensed', '', 12, '', false);
		$this->pdf->SetFont('', '', 10, '', false);

		// add a page
		$this->pdf->AddPage();


		//ob_start();
		//include '../../include/printheader.php';
		$out = ob_get_contents();
		//$out = '<style>'.file_get_contents('css/print.css').'</style>'.$out;
		ob_end_clean();
		//echo $out;
		$this->pdf->WriteHTML($out, true, 0, true, 0);
		$this->pdf->setRTL(false);
		$this->pdf->Ln();
		// ---------------------------------------------------------
		ob_clean();
		//Close and output PDF document
		$this->pdf->Output($name, 'I');
		//============================================================+
		// END OF FILE
		//============================================================+
	}

}
?>
