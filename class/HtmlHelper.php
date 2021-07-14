<?php
require_once(realpath(dirname(__FILE__)) . '/Dictionary.php');
require_once (realpath(dirname(__FILE__)) . '/MysqliDB.php');

class HTML
{
	public $type, $name, $value, $list, $options, $root_directory;
	protected $dictionary;
	protected $language;
	protected $db;

	//public function __construct($type, $name, $value, $list, $options)
	public function __construct($language)
	{
		$this -> db = new MysqliDB();
		$this->dictionary = new Dictionary($language);
		$this->dictionary->GetAllDictionary();
		$this->root_directory = "Admin";
		$this->language = $language;
	}

	public function __destruct() {
		$this -> db = null;
	}

	public function OpenTag($tagname, $name=NULL, $options=NULL)
	{
		echo '<'.$tagname.' ';
		if($options != NULL)
			foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
		echo '>';
		if($tagname != "input" && $tagname != "img")
			return $tagname;
	}

	public function CloseTag($tagname)
	{
		if($tagname != NULL)
			echo '</'.$tagname.'>';
	}

	public function OpenWidget($title=NULL, $actions=null, $options=array('collapse' => true, 'fullscreen' => true, "content"=>"form", "caption-class"=>"caption"), $color='green', $type='box' )
	{
		$caption_class = "caption";
		if(isset($options["caption-class"])){
			$caption_class = $options["caption-class"];
		}
		$portlet_content = $collapsed = "";
		$collapse_icon = "collapse";
		if(isset($options["id"]) && $options["id"] != null){
			$id = $options["id"];
		}else{
			$id = "portlet_".$title;
		}
		?>
        <div class="clearfix"></div>
		<div class="portlet <?php echo $type." ".$color;?>" id="<?php echo $id; ?>">
			<div class="portlet-title">
				<div class="<?php echo $caption_class;?>">
					<?php echo $this->dictionary->GetValue($title); ?>
				</div>
				<?php
				if($options != null){
					if(isset($options["portlet-collapsed"]) && $options["portlet-collapsed"]==true){
						$collapsed = "portlet-collapsed";
						$collapse_icon = "expand";
					}
					if(isset($options["content"]) && $options["content"]==true){
						$portlet_content =  "form";
					}

					echo '<div class="tools">';
					if(isset($options["fullscreen"]) && $options["fullscreen"]==true){
						echo '<a href="" class="fullscreen" data-original-title="" title=""> </a>';
					}

					if(isset($options["collapse"]) && $options["collapse"]==true){
						echo '<a href="javascript:;" class="'.$collapse_icon.'" data-original-title="" title=""> </a>';
					}

					if(isset($options["remove"]) && $options["remove"]==true){
						echo '<a href="javascript:;" class="remove" data-original-title="" title=""> </a>';
					}
					//	<a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
					//	<a href="javascript:;" class="reload" data-original-title="" title=""> </a>
					echo '</div>';
				}



				if($actions != null || (isset($options["table-actions"]) && $options["table-actions"]==true)){
					?>
					<div class="actions">
	                    <?php
						if($actions != null){
							foreach ($actions as $control) {
								self::DrawFormInput($control["type"], $control["name"], $control["value"], $control["list"], $control["options"]);
							}
						}

						if(isset($options["table-actions"]) && $options["table-actions"]!=null){
							?>
							<div class="btn-group">
							    <a class="btn <?php echo $color;?> btn-sm" href="javascript:;" data-toggle="dropdown" aria-expanded="false">
							        <i class="fa fa-table"></i>
							        <span class="hidden-xs"><?php echo $this->dictionary->GetValue("table_tools");?></span>
							    </a>
							    <ul class="dropdown-menu pull-right btn-outline" id="sample_3_tools">
							        <li>
							            <a href="javascript:;" data-action="0" class="tool-action" aria-controls="<?php echo $options["table-actions"];?>">
							                <i class="icon-printer"></i> Print</a>
							        </li>
							        <li>
							            <a href="javascript:;" data-action="1" class="tool-action" aria-controls="<?php echo $options["table-actions"];?>">
							                <i class="icon-check"></i> Copy</a>
							        </li>
							        <!-- <li>
							            <a href="javascript:;" data-action="2" class="tool-action" aria-controls="<?php //echo $options["table-actions"];?>">
							                <i class="icon-doc"></i> PDF</a>
							        </li> -->
							        <li>
							            <a href="javascript:;" data-action="3" class="tool-action" aria-controls="<?php echo $options["table-actions"];?>">
							                <i class="icon-paper-clip"></i> Excel</a>
							        </li>
							        <li>
							            <a href="javascript:;" data-action="4" class="tool-action" aria-controls="<?php echo $options["table-actions"];?>">
							                <i class="icon-cloud-upload"></i> CSV</a>
							        </li>
							    </ul>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
			<?php

			echo '<div class="portlet-body '.$portlet_content .' '.$collapsed.'">';
	}

	public function CloseWidget()
	{
		?>
			</div><!-- /widget-content -->
		</div>
		<?php
	}

	public function OpenMenuWidget($title=NULL, $icon=NULL, $type=NULL)
	{
		if($title != NULL){
		?>
		<div class="widget  <?php if($type!=NULL) echo $type;?>">
			<div class="widget-header">
				<?php if($icon != NULL){?>
				<i class="icon-<?php echo $icon?>"></i>
				<?php }?>
				<h3><?php echo $this->dictionary->GetValue($title); ?></h3>
			</div><!-- /widget-header -->
		<?php }
	}


	public function OpenMenuWidgetContent()
	{
		?>
			<div class="widget-content">
		<?php
	}

	public function OpenCollapsible()
	{
	?>
		<div class="span12 widget">
			<div id="myCollapsible" class="accordion">
	<?php
	}

	public function CloseCollapsible()
	{
		?>
			</div>
		</div>
		<?php
	}

	public function OpenCollapseDiv($number=NULL, $title=NULL)
	{
	?>
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php print $number; ?>"><?php print $this->dictionary->GetValue($title); ?></a>
			</div>
			<div id="collapse<?php print $number; ?>" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="widget no-bottom-margin">
	<?php
	}

	public function CloseCollapseDiv()
	{
	?>
					</div>
				</div>
			</div>
		</div>
	<?php
	}


	public function OpenSpan($spanwidth, $optionalClass=NULL)
	{
		?>
		<div class="col-lg-<?php echo $spanwidth?> <?php if($optionalClass != NULL) echo $optionalClass;?>">
		<?php
	}

	public function CloseSpan()
	{
		?>
		</div>
		<?php
	}


	//** FORM SECTION **//
	public function OpenForm($action=NULL, $id="form1", $form_flow="vertical", $method="post", $class=NULL)
	{
		//form-horizontal => fully horizental
		//horizontal-form => vertical label
		if($form_flow == "horizental"){
			$flow_class = "form-horizontal";
		}else {
			$flow_class = "horizontal-form"; // vertical layout
		}
		$class .= " ".$flow_class;
		?>
		<form id="<?php echo $id;?>" class="<?php echo $class; ?>" action="<?php echo $action?>" method="<?php echo $method;?>" enctype="multipart/form-data">
		<?php
			self::OpenDiv("form-body");
	}

	public function CloseForm($actions=null)
	{
			self::CloseDiv();
			if($actions != null){
				self::DrawFormActions($actions);
			}
		?>
		</form>
		<?php
	}

	public function OpenDiv($className, $id=null)
	{
		echo '<div class="'.$className.'" ';
		if($id!=null) echo 'id="'.$id.'" ';
		echo '>';
	}

	public function CloseDiv()
	{
		?>
		</div>
		<?php
	}

	public function DrawPageTitle($title, $title_class)
	{
	?>
         <div class="row">
            <div class="col-lg-12">
                <div class="page-title">
                    <ol class="breadcrumb">
                        <li><i class="fa <?php echo $title_class;?>"></i>  <a href="javascript:;"><?php echo $this->dictionary->GetValue($title);?></a>
                        </li>
                    </ol>
                </div>
            </div>
         </div>
	<?php
	}

	public function DrawFormField($type, $name, $value, $list, $options)
	{
		$flow = "vertical";
		if(isset($options["flow"])){
			if($options["flow"]=="horizental"){
				$flow = "horizental";
			}
			unset($options["flow"]);
		}

		if(isset($options["label"])){
			$label = $options["label"];
		}else{
			$label = $name;
		}

		if(isset($options["label-align"]) && $options["label-align"] == "opposite"){
			$label_align = "";
		}else{
			$label_align = "default_align";
		}

		if(isset($options["suffix"])){
			$suffix = $options["suffix"];
		}else{
			$suffix = ": ";
		}

		if(isset($options["size"])){
			$size_class = "form-group-".$options["size"];
		}else{
			$size_class = "";
		}

		?>
    	<div class="form-group <?php echo $size_class;?>">
        	<label for="<?php echo $name?>" class="<?php
				echo $label_align;
				if($flow=="horizental") {
					echo " col-sm-4";
				}
				?> control-label">
				<?php
				if($label !=""){
					if($options != null && isset($options["view"]) && $type != "label"){
						echo $this->dictionary->GetValue($label).$suffix;
					}else{
						echo "<b>".$this->dictionary->GetValue($label).$suffix."</b>";
					}
				}
				?>
			</label>
			<?php
			$col_class="col-sm-8";
			if($options != null && isset($options["col-class"])){
				$col_class = $options["col-class"];
				unset($options["col-class"]);
			}

			if($flow == "horizental"){
				echo '<div class="'.$col_class.'">';
			}
			if($options != null && isset($options["view"]) && $type != "table" && $type != "file"){
				echo '<div class="form-control-static" ';
				if($options != NULL){
					foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
				}
				echo '>';
				echo $value;
				echo '</div>';
			}else{
	        	self::DrawFormInput($type, $name, $value, $list, $options);
			}
			if($flow == "horizental"){
				echo '</div>';
        	}
			?>
    	</div>
		<?php
	}

	/*public function DrawFormMultiField($type, $name, $values, $list, $options)
	{
		for($i = 0; $i < count ( $list ); $i ++) {
			$id = $list[$i][0];
			$text = $list[$i][1];
			?>
            <div class="form-group">
                <label for="<?php echo $name.$id;?>" class="col-sm-3 control-label"><?php echo $this->dictionary->GetValue($text); ?></label>
                <div class="col-sm-9">
                    <?php self::DrawFormInput($type, $name.$id, $values[$list [$i] [0]], NULL, $options);?>
                </div>
  		    </div>
			<?php
		}
	}*/

	public function DrawGenericFormField($name, $fileds, $flow="vertical", $options=null)
	{
		if(isset($options["suffix"])){
			$suffix = $options["suffix"];
		}else{
			$suffix = ": ";
		}

		if(isset($options["size"])){
			$size_class = "form-group-".$options["size"];
		}else{
			$size_class = "";
		}
		?>
	    <div class="form-group <?php echo $size_class;?>">
        	<label for="<?php echo $name?>" class="<?php if($flow=="horizental") { echo "col-sm-4"; }?> control-label"
        	style="<?php if($flow=="vertical") { echo "display:block"; }?>">
			<?php
			if($this->dictionary->GetValue($name) != ""){
				echo $this->dictionary->GetValue($name).$suffix;
			}else{
				echo "&nbsp;";
			}
			?>
			</label>
			<?php
			$col_class="col-sm-8";
			//if($options != null && isset($options["col-class"])){
			//	$col_class = $options["col-class"];
			//	unset($options["col-class"]);
			//}
			if($flow == "horizental"){
				echo '<div class="'.$col_class.'">';
			}
			foreach ($fileds as $filed) {
				self::DrawFormInput($filed["type"], $filed["name"], $filed["value"], $filed["list"], $filed["options"]);
			}
			if($flow == "horizental"){
				echo '</div>';
        	}
			?>
	    </div>
		<?php
	}


	public function DrawFormActions($controls)
	{
		?>
		<div class="form-actions fluid right">
			<div class="row">
				<div class="col-md-offset-3 col-md-9">
					<?php
					foreach ($controls as $filed) {
						self::DrawFormInput($filed["type"], $filed["name"], $filed["value"], isset($filed["list"])?$filed["list"]:null, isset($filed["options"])?$filed["options"]:null);
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/*public function DrawFormActions($name, $fileds)
	{
		?>
		<div class="form-actions">
        <?php
		foreach ($fileds as $filed) {
			self::DrawFormInput($filed["type"], $filed["name"], $filed["value"], $filed["list"], $filed["options"]);
		}
		?>
		</div>
		<?php
	}*/


	public function DrawToolbarField($type, $name, $value, $list, $options)
	{
	?>
    <label for="<?php echo $name?>">
    	<?php
    	if(!(isset($options["nolabel"]) && $options["nolabel"]=="true")){
	    	echo $this->dictionary->GetValue($name);
    	}
    	self::DrawFormInput($type, $name, $value, $list, $options);
    	?>
    </label>
	<?php
	}

	public function DrawFormInput($type, $name, $value, $list, $options)
	{
		switch ($type)
		{
			case "hidden":
				self::HiddenField($name, $value);
				break;
			case "text":
			case "number":
				self::TextBox($name, $value, $options, $type);
				break;
			case "password":
				self::Password($name, $value, $options);
				break;
			case "textarea":
				self::TextArea($name, $value, $options);
				break;
			case "select":
				self::Select($name, $value, $list, $options);
				break;
			case "radio":
				self::RadioButton($name, $value, $list, $options);
				break;
			case "radioIcon":
				self::RadioButtonIcon($name, $value, $list, $options);
				break;
			case "checkbox":
				self::CheckBox($name, $value, $list, $options);
				break;
			case "checkboxIcon":
				self::checkboxIcon($name, $value, $list, $options);
				break;
			case "file":
				self::FileUpload($name, $options, $value);
				break;
			case "label":
				self::Label($name, $value, $options);
				break;
			case "literal":
				self::Literal($name, $value, $list, $options);
				break;
			case "link":
				self::Link($name, $value, $list, $options);
				break;
			case "table":
				self::FormTable($name, $value, $list, $options);
				break;
			case "button":
			case "submit":
			case "reset":
				self::Button($type, $name, $value, $options);
				break;
		}
	}

	public function FormTable($name, $value=NULL, $list=NULL, $options=NULL)
	{
		$tableArray = json_decode($value, true);

		if($options!=null && isset($options["display-add"])){
			if($options["display-add"]){
				$editable = true;
			}else{
				$editable = false;
			}
		}else{
			$editable = false;
		}

		self::OpenDiv("table-responsive");
		{
			echo "<input type='hidden' id='".$name."' name='".$name."' value='".str_replace("'","`", $value)."'>";
			echo '<table id="tft_'.$name.'" class="table"><tr>';
			for ($i=0; $i<count($list); $i++) {
				$field_name = $list[$i][1];
				if(substr($field_name, 0, 1) === "{"){
					$array = json_decode($field_name, True);
					$field_name = $array["field_name"];
				}
				echo "<th>".$this->dictionary->GetValue($field_name)."</th>";
			}
			if($editable){
				echo "<th></th>";
			}
			echo "</tr>";

			if($editable){
				echo "<tr class='tf_edit_row'>";
				for ($i=0; $i<count($list); $i++) {
					$field_name = $list[$i][1];
					echo "<td>";
					if(substr($field_name, 0, 1) === "{"){

						//select
						$array = json_decode($field_name, True);
						$field_name = $array["field_name"];

						//get list items array
						$list_items = $this->db->SelectColumn($array["table"], $array["column"]);
						for($j=0; $j<count($list_items); $j++){
							$list_items[$j][0] =  $list_items[$j][$array["column"]];
							$list_items[$j][1] =  $list_items[$j][$array["column"]];
						}

						self::Select($field_name, null, $list_items, null);
					}else{
						//text
						self::TextBox($field_name, null, null);
					}
					echo "</td>";
				}
				//add button
				if($editable){
					echo '<td><a href="javascript:;" class="btn dark btn-sm btn-outline sbold tf_add"><i class="fa fa-plus"></i></a></td>';
				}
				echo "</tr>";
			}

			for ($i=0; $i<count($tableArray); $i++) {
				$recordArray = $tableArray[$i];
				echo "<tr>";
				for ($j=0; $j<count($list); $j++) {
					$name = $list[$j][1];
					if(substr($name, 0, 1) === "{"){
						$array = json_decode($name, True);
						$name = $array["field_name"];
					}
					echo "<td>";
					if(isset($recordArray["$name"])){
						echo $recordArray["$name"];
					}
					echo "</td>";
				}
				//remove button
				if($editable){
					echo '<td><a href="javascript:;" class="btn dark btn-sm btn-outline sbold tf_remove"><i class="fa fa-minus"></i></a></td>';

				}
				echo "</tr>";
			}
			echo "</table>";
		}
		self::CloseDiv();
	}

	public function Literal($name, $value=NULL, $list=NULL, $options=NULL)
	{
		if($value != "")
			echo $value;
		else
			echo $name;
	}
	public function Link($name, $value=NULL, $list=NULL, $options=NULL)
	{
		echo '<a href="'.$list.'" ';
		if($options != NULL){
			foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
		}
		echo '>';
		if(isset($options["icon"])){
			echo '<i class="'.$options["icon"].'"></i> ';
		}
		echo $this->dictionary->GetValue($value).'</a> ';
	}
	public function HiddenField($name, $value)
	{
		?>
		<input type="hidden" name="<?php echo $name?>" id="<?php echo $name?>" 	value="<?php echo $value?>"/>
		<?php
	}

	public function FileUpload($name, $options, $value=null)
	{

		if(!isset($options["display-add"]) || (isset($options["display-add"]) && $options["display-add"]==true)){
		?>
		<input type="file" name="<?php echo $name?>" id="<?php echo $name?>"
		<?php
			if($options != NULL)
				foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
		?>
		 />
		<?php
		}
		if($value!=null){
			//echo '<div><a href="'.$value.'" target="blank"><i class="fa fa-paperclip" ></i> '.$this->dictionary->GetValue("view_attachment").'</a></div>';
			echo '<a href="'.$value.'" class="btn blue" target="blank"><i class="fa fa-file-pdf-o"></i> '.$this->dictionary->GetValue('view_attachment').'</a>';
		}
	}

	public function Label($name, $value, $options)
	{
		?>
		<p id="<?php echo $name?>"
		<?php
		if($options != NULL){
			foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
		}
		?>
		class="form-control-static"><?php echo nl2br($value)?></p>
		<?php
	}

	public function TextBox($name, $value, $options, $type="text")
	{
		?>
		<input type="<?php echo $type;?>" name="<?php echo $name?>" id="<?php echo $name?>" value="<?php echo $value?>"
		<?php
		if($options != NULL){
			foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
		}
		?>
		 maxlength="450" />
		 <?php
	}


	public function TextBoxList($name, $value, $options)
	{
		?>
			<input type="text" name="<?php echo $name?>" id="<?php echo $name?>" value="<?php echo $value?>"
			<?php
				if($options != NULL)
					foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
			?>
			 /><?php
		}

	public function Password($name, $value, $options)
	{
		?>
		<input type="password" name="<?php echo $name?>" id="<?php echo $name?>" value="<?php echo $value?>"
		<?php
			if($options != NULL)
				foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
		?>
		 />
		<?php
	}

	public function TextArea($name, $value, $options)
	{
		?>
		<textarea name="<?php echo $name?>" id="<?php echo $name?>"
			<?php
			if($options != NULL)
				foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';?>
		><?php echo $value?></textarea>
		<?php
	}

	public function Select($name, $value, $list, $options)
	{
		?>
		<select name="<?php echo $name?>" id="<?php echo $name?>"
			<?php

			if($options != NULL)
				foreach($options as $key=>$val)
					if($key != "data")	echo ' '.$key.'="'.$val.'"';
			?> >
			<?php
				if(isset($options["optional"]) && $options["optional"]!=""){

					if($options["optional"]=="true")
						//default optional text
						echo '<option value="">--</option>';
					else
						echo '<option value="">'.$options["optional"].'</option>';
				}

				for($i = 0; $i<count($list);$i++)
				{
					if(is_array($list[$i])){
						$id = $list[$i][0];
						$text = $list[$i][1];
					}else{
						$id = $list[$i];
						$text = $list[$i];
					}
					echo '<option value="'.$id.'"';

					if(is_array($value)){
						//loop over values
						foreach ($value as $i => $val) {
							//select value and unset it from values array
							if($id==$val) {
								echo " selected";
							}
						}
					}else{
				 		if($id==$value && $value != "") echo " selected";
				 	}


				 	//if extra data is required
				 	if($options != NULL && isset($options["data"])){
				 		foreach($options["data"] as $data_name => $data_field)
				 		{
				 			echo ' data-'.$data_name.'="'.$list[$i][$data_field].'" ';
				 		}
				 	}

				 	if(isset($options["dictionary"]) && $options["dictionary"]=="true")
						echo ' >'.$this->dictionary->GetValue($text).'</option>';
					else
						echo ' >'.$text.'</option>';

				}
			?>
		</select>
		<?php
	}

	public function CheckBox($name, $value, $list, $options)
	{
		//default flow of checkboxes is horizental
		$flow = "horizental";
		if(isset($list)){

			if(!is_array($list))
			{
				$list = array($list);
			}

			//vertical flow creates a div around each check box
			if(isset($options["items-flow"]) && $options["items-flow"]=="vertical"){
				$flow = "vertical";
				unset($options["flow"]); //remove flow option form optiosn array
			}

			if($flow == "vertical"){
				$list_class = "mt-checkbox-list";
			}else{
				$list_class = "mt-checkbox-inline";
			}
			//opening sourrounding div
			self::OpenDiv($list_class);
			{
				//loop over the checkboxes list
				for($i = 0; $i<count($list);$i++)
				{
					?>
					<label <?php echo "class='mt-checkbox'" ?>>
						<input type="checkbox"
						<?php
						if($options != NULL)
							foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';

						//in case only one checkbox use list text as id and ignore the text
						if(count($list)>1){
							$id = $list[$i][0];
							$text = $list[$i][1];
						}else{
							if(is_array($list[0])){
								$id = $list[0][0];
								$text = $list[0][1];
							}else{
								$id = $list[0];
								$text = "";
							}

						}

						if(isset($options["label"]) && count($list)==1){
							$text = $options["label"];
						}

						if(is_array($value)){
							//loop over values
							foreach ($value as $j => $val) {
								//select value and unset it from values array
								if($id==$val) {
									echo " checked";
									//unset($array[$j]);
								}
							}
						}else{
							if($id==$value) echo "checked";
						}

						//in case only one checkbox, don't use indexing to checbox names
						if(count($list)>1)
							$name_index = $id;
						else
							$name_index = "";
						if(isset($options["checkBoxList"]) && $options["checkBoxList"] == true)
						{
							$check_name = $name;
						}
						else
						{
							$check_name = $name.$name_index;
						}
						?>

						name="<?php echo $check_name; ?>" id="<?php echo $name.$name_index; ?>" value="<?php echo $id?>" > <?php echo $this->dictionary->GetValue($text);?>
						<span></span>
					</label>
					<?php
				}
			}
			self::CloseDiv();

		}
	}
	public function CheckBoxIcon($name, $value, $list, $options)
	{
		//default flow of checkboxes is horizental
		$flow = "horizental";
		if(isset($list)){

			if(!is_array($list))
			{
				$list = array($list);
			}

			//vertical flow creates a div around each check box
			if(isset($options["flow"]) && $options["flow"]=="vertical"){
				$flow = "vertical";
				unset($options["flow"]); //remove flow option form optiosn array
			}

			if($flow == "vertical"){
				$list_class = "mt-checkbox-list";
			}else{
				$list_class = "mt-checkbox-inline";
			}
			//opening sourrounding div
			self::OpenDiv($list_class);
			{
				//loop over the checkboxes list
				for($i = 0; $i<count($list);$i++)
				{
					$title="";
					if(count($list)>1){
					$title = $list[$i][2];
					}
					?>
	                <label <?php echo "class='mt-checkbox'" ?> title="<?php echo $title?>">
						<input type="checkbox"
						<?php
						if($options != NULL)
							foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';

						//in case only one checkbox use list text as id and ignore the text
						if(count($list)>1){
							$id = $list[$i][0];
							$icon = $list[$i][1];


						}else{
							$id = $list[0];
							$icon = "";
						}

						if(is_array($value)){
							//loop over values
							foreach ($value as $j => $val) {
								//select value and unset it from values array
								if($id==$val) {
									echo " checked";
									//unset($array[$j]);
								}
							}
						}else{
							if($id==$value) echo " checked";
						}

						//in case only one checkbox, don't use indexing to checbox names
						if(count($list)>1)
							$name_index = $id;
						else
							$name_index = "";
						if(isset($options["checkBoxList"]) && $options["checkBoxList"] == true)
						{
							$check_name = $name;
						}
						else
						{
							$check_name = $name.$name_index;
						}
						?>

					 	name="<?php echo $check_name; ?>" id="<?php echo $name.$name_index; ?>" value="<?php echo $id?>" > <?php echo "<i class='fa fa-".$icon." font-blue-soft'></i>"; ?>
						<span></span>

				 	</label>
	                <?php
				}
			}
			self::CloseDiv();

		}
	}


	public function RadioButton($name, $value, $list, $options)
	{
		//default flow of radios is horizental
		$flow = "horizental";
		//vertical flow creates a div around each check box
		if(isset($options["flow"]) && $options["flow"]=="vertical"){
			$flow = "vertical";
			unset($options["flow"]); //remove flow option form optiosn array
		}

		if($flow == "horizental") {
			$list_class = "mt-radio-inline";
		}else {
			$list_class = "mt-radio-list";
		}
		echo '<div class="'.$list_class.'">';
		for($i = 0; $i<count($list);$i++){
				$id = $list[$i][0];
				$text = $list[$i][1];
		?>
			<label class="mt-radio">
                  <input type="radio" name="<?php echo $name?>" value="<?php echo $id?>" <?php
				if($id==$value) echo " checked";
				if($options != NULL)
					foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
				?>><?php echo $this->dictionary->GetValue($text)?>
				<span></span>
            </label>

		<?php
		}
		echo '</div>';
	}
	public function RadioButtonIcon($name, $value, $list, $options)
	{
		//default flow of radios is horizental
		$flow = "horizental";
		//vertical flow creates a div around each check box
		if(isset($options["flow"]) && $options["flow"]=="vertical"){
			$flow = "vertical";
			unset($options["flow"]); //remove flow option form optiosn array
		}

		if($flow == "horizental") {
			$list_class = "mt-radio-inline";
		}else {
			$list_class = "mt-radio-list";
		}
		echo '<div class="'.$list_class.'">';
		for($i = 0; $i<count($list);$i++){
				$id = $list[$i][0];
				$text = $list[$i][1];
				$icon=$list[$i][2];
		?>
			<label class="mt-radio">
                  <input   type="radio" name="<?php echo $name?>" value="<?php echo $id?>" <?php
				if($id==$value) echo " checked";
				if($options != NULL)
					foreach($options as $key=>$val) echo ' '.$key.'="'.$val.'"';
				?>>
				<?php echo '<div class="color-icon '.$icon.'" title="'.$this->dictionary->GetValue($text).'" ></div>' ?>
				<span ></span>
            </label>

		<?php
		}
		echo '</div>';
	}

	public function Button($type, $name, $value, $options)
	{
		?>
		<button type="<?php echo $type?>" name="<?php echo $name?>" id="<?php echo $name?>"
			<?php
			if($options != NULL){
				foreach($options as $key=>$val) {
					if($key != "icon"){
						echo ' '.$key.'="'.$val.'"';
					}
				}
			}
			?>
		 >
			<?php
			if(isset($options["icon"])){
				echo '<i class="'.$options["icon"].'"></i> ';
			}
			echo $this->dictionary->GetValue($value);
			?>
		</button>
		<?php
	}

	public function ManageList($type, $list, $title){
    ?>
        <div class="span12 widget no-right-margin">
            <div class="widget-header"> <i class="icon-list"></i>
                <h3> <?php print $this->dictionary -> GetValue($title); ?> </h3>
            </div>
            <!-- /widget-header -->
            <div class="widget-content" id="content">
                <div class="input-prepend span12">
                    <?php
                    for($i = 0; $i<count($list);$i++){
                        $id = $list[$i][0];
                        $text = $list[$i][1];
                        ?>
                        <div class="span7">
                            <input type="hidden" name="list_value" value="<?php print $id; ?>" />
                            <input type="text" id="list_value" name="list_value" value="<?php print $text; ?>" readonly="readonly" />
                            <?php if((!isset($list[$i]["IS_EDITABLE"])) || (isset($list[$i]["IS_EDITABLE"]) && $list[$i]["IS_EDITABLE"] == 1)) { ?>
                            <span class="add-on btn edit" data-id="<?php print $id;?>"><i class="icon-pencil"></i></span>
                            <span class="add-on btn delete confirm" data-id="<?php print $id;?>"><i class="icon-minus-sign"></i></span>
                            <span class="add-on btn btn-danger cancel" data-id="<?php print $id;?>" style="display:none"><i class="icon-remove"></i></span>
                            <?php } ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="input-prepend span12">
                    <div class="span7 control-group">
                        <input type="text" id="new_reference" name="new_reference" placeholder=" <?php print $this->dictionary -> GetValue("Add_new"); ?> ">
                        <span class="add-on btn add_reference"><i class="icon-plus"></i></span>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

	//** END FORM SECTION**//


	//** TOP NAV **//

	public function OpenMainNav(){
		echo '<ul class="mainnav">';
	}

	public function CloseMainNav(){
		echo '</ul>';
	}

	public function DrawNavItem($text, $url, $icon=NULL){
		?>
		<li><a href="<?php echo $url;?>"> <i class="icon-<?php echo $icon;?>"></i>
		<span><?php echo $this->dictionary->GetValue($text); ?></span>
		</a></li>
		<?php
	}



	//** Table **//
	public function Table($data, $meta, $options)
	{
		//check if data is not empty
		if(is_array($data) && count($data)>0){
			//default options values
			$showInfo = $showActions = $showSubtotal = false;
			$showHeader = true;
			$tableClass = "table-striped";
			$tableHeadClass = "";
			$rowClass = "";

			//display info button
			if(isset($options["info"]) && $options["info"]=="true")
				$showInfo = true;
			//dispaly actions column
			if(isset($options["actions"]) && $options["actions"]=="true")
				$showActions = true;
			//display subtotal row
			if(isset($options["subtotal"]) && $options["subtotal"]!="")
				$showSubtotal = $options["subtotal"];
			//css calss of the table
			if(isset($options["tableClass"]))
				$tableClass = $options["tableClass"];
			//css class of header row
			if(isset($options["tableHeadClass"]))
				$tableHeadClass = $options["tableHeadClass"];
			//css class of row
			if(isset($options["rowClass"]))
				$rowClass = ' class="'.$options["rowClass"].'"';
			//display header row
			if(isset($options["header"]) && $options["header"]="false")
				$showHeader = false;
			?>
            <div class="table-responsive">
			<table class="table rtl <?php echo $tableClass;?> table-condensed">
				<?php if($showHeader){?>
				<thead>
					<tr class="<?php echo $tableHeadClass;?>">
						<?php
						foreach ($meta as $fieldInfo){
							if (strpos($fieldInfo["column"], "ACTION_COL") === 0){
								self::TH();
							}else{
								if(isset($fieldInfo["title"]))
									self::TH($this->dictionary->GetValue($fieldInfo["title"]));
								else
									self::TH($this->dictionary->GetValue($fieldInfo["column"]));
							}
						}
						if($showInfo) self::TH();
						if($showActions) self::TH();
						?>
					</tr>
				</thead>
				<?php } ?>
				<tbody>
				<?php
				for ($i=0; $i<count($data); $i++){
					echo "<tr". $rowClass .">";
					foreach ($meta as $fieldInfo){
						$cellOptions = array();

						//if cell has class, get it
						if (isset($fieldInfo["class"]) && $fieldInfo["class"]!="")
						{
							$cellOptions["class"] = $fieldInfo["class"];
						}
						if (isset($fieldInfo["style"]) && $fieldInfo["style"]!="")
						{
							$cellOptions["style"] = $fieldInfo["style"];
						}
						//if column is action
						if (strpos($fieldInfo["column"], "ACTION_COL") === 0 ){
							foreach($fieldInfo["buttons"] as &$button){
								$showAction = false;
								//this section is used to show action button based on record data
								if (isset($button["filter"]) && $button["filter"]!=""){
									$conditionStr = "";
									foreach ($button["filter"] as $field=>$val){
										//if value is simple operator is equal
										if(!is_array($val)){
											if($data[$i][$field] == $val){
												$showAction = true;
											}else{
												$showAction = false;
											}
										}
										else //if value is array, get operator from the array
										{
											$operator = $val["operator"];
											$value = $val["value"];
											if($value == '' || $value == NULL){
												$value ='""';
											}
											//evaluate the dynamic condition
											if($data[$i][$field] != '' && $value !=''){
												if($conditionStr == ""){
													$conditionStr = ' "'.$data[$i][$field].'"'.$operator.$value.' ';
												}else{
													$conditionStr .= ' && "'.$data[$i][$field].'"'.$operator.$value.' ';
												}
											}
										}
									}

									//evaluate the dynamic condition
									if(!$showAction){
										if($conditionStr !=''){
											$showAction = eval('return ('.$conditionStr.');');
										}else{
											$showAction = false;
										}
									}

								}else{
									$showAction = true;
								}
								//if filter condition is true, pass visible option equals to false
								if($showAction == false){
									$button["visible"]="0";
								}
							}
							//get key value
							$key=self::GetRowKey($data[$i], $options["key"]);
							//draw button cell
							self::DrawButtonTD($key, $fieldInfo);

						}else{//if column is text
							$text = $data[$i][$fieldInfo["column"]];
							//if this column needs translation, pass the text to translate function
							if(isset($fieldInfo["dictionary"]) && $fieldInfo["dictionary"]=="true")
							{
								$text = $this->dictionary->GetValue($data[$i][$fieldInfo["column"]]);
							}
							//draw cell
							self::TD($text, $cellOptions);
						}
					}
					echo "</tr>";
				}

				//section for subtotal row in reports
				if($showSubtotal!==false){
					echo "<tr>";
					$i=0;
					//loop over meta array to draw footer row
					foreach ($meta as $fieldInfo)
					{
						$cellOptions = array();
						if (isset($fieldInfo["class"]) && $fieldInfo["class"]!="")
						{
							$cellOptions["class"] = $fieldInfo["class"];
						}
						if (isset($fieldInfo["style"]) && $fieldInfo["style"]!="")
						{
							$cellOptions["style"] = $fieldInfo["style"];
						}
						if($i==0)
							self::TD($this->dictionary->GetValue("total"), $cellOptions);
						else if($i==count($meta)-2)
							self::TD($showSubtotal, $cellOptions);
						else
							self::TD("", $cellOptions);

						$i++;
					}
					echo "</tr>";
				}
				?>
				</tbody>
			</table>
            </div>
		<?php
		}else{
			?>
            <div class="table-responsive col-headroom">
			<table class="table rtl table-bordered">
				<tr>
					<td>
					<?php
					//empty data, show default text for empty result
					if(isset($options["default-text"]) && $options["default-text"]!=""){
						print $this->dictionary->GetValue($options["default-text"]);
					}else{
						print $this->dictionary->GetValue("no_data_found");
					}
					?>
					</td>
				</tr>
			</table>
             </div>
			<?php

		}
	}

	public function TH($text=NULL)
	{
		?>
		<th><div><span><?php if($text!=NULL) echo $text;?></span></div></th>
		<?php
	}

	public function TD($text="", $options=NULL)
	{
		//Default cell calss is clear
		?>
		<td
		<?php
		if($options!=NULL){
			foreach ($options as $key=>$value)
				echo ' '.$key.' = "'.$value.'"';
		}
		?> ><?php echo $text;?></td>
		<?php
	}

	public function DrawInfoTD($id)
	{
	?>
		<td class="td-action"><button class="btn btn-sm btn-info" rel="popoverschoolinfo" data-id="<?php echo $id?>"><?php print $this->dictionary->GetValue("Info"); ?></button></td>
		<?php
	}



	public function DrawButtonTD($key, $options)
	{
		if(isset($options["action-type"]))
			$type = $options["action-type"];
		else
			$type = "link";

		?>
		<td class="td-action <?php if(isset($options["class"])) echo $options["class"]; ?>"
						<?php if(isset($options["style"])) echo 'style="'.$options["style"].'"'; ?> >
			<?php
			foreach ($options["buttons"] as $button)
			{
				//for querystring
				if(isset($button["action-url"])){
					if (strpos($button["action-url"],'?') === false) {
						$button["action-url"] .='?';
					}
				}

				$button_class = "btn";
				if(isset($button['class'])){
					$button_class = $button['class'];
				}

				//if button is not marked as hidden, draw button
				if(!(isset($button["visible"]) && $button["visible"]==false)){
					if($type=="link") { //querystring buttons
					?>
						<?php
						//generate querystring
						$keyStr = "";
						foreach ($key as $data=>$value){
							$keyStr.= '&'.$data.'='.$value;
						}
						?>
						<a href="<?php echo $button["action-url"].$keyStr; ?>" class="btn  <?php echo $button_class; ?>"
						<?php
						if(isset($button["title"]))		echo 'title="'.$button["title"].'"';
						if(isset($button["target"]))	echo 'target="'.$button["target"].'"';

						?>

						>
						<?php
						if(isset($button['button-icon'])){
						?>
							<i class="btn-icon-only <?php echo $button['button-icon']; ?>"> </i>
						<?php
						}else if(isset($button['img-icon'])){
						?>
							<img src="../theme-img/icon/<?php echo $button['img-icon']; ?>" class="<?php echo $button_class;?>" />
						<?php
						}
						?>
						</a>

					<?php } else if($type=="ajax") { //Ajax buttons ?>

						<a href="javascript:;" class="btn <?php echo $button_class." ".$button['action-class']; ?>"
		            		<?php
		            		//add data key tags
		            		foreach ($key as $data=>$value){	echo "data-".$data.'="'.$value.'" ';	}

		            		//other options
		            		if(isset($button["title"]))
		            			echo 'title="'.$button["title"].'"';
		            		?>
		            	>
							<i class="fa fa-<?php echo $button['button-icon']; ?>"></i>
						</a>

					<?php
					}
				}
			}
			?>
		</td>
		<?php
	}




	public function DrawPagination($pageAddress, $pageNum, $totalPages, $total_records=NULL, $type="post")
	{
		if (strpos($pageAddress,'?') === false) {
			$pageAddress .='?';
		}else{
			$pageAddress .='&';
		}

		$data="";
		$addedPageNuma=false;

		?>
		<div class="form-horizontal">
			<form  class="form-horizontal" action="" method="post" >
				<fieldset>
					<div class="col-lg-12">
					<?php
					foreach( $_POST as $key => $value ) {


						if( is_array( $value ) ) {
							foreach( $value as $thing ) {
								//echo $thing;
							}
						} else {
							if($value!=null && $value!=""){
								echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
								if($key == "pageNum"){
									$addedPageNuma = true;
								}
							}
						}
					}

					if(!$addedPageNuma){
						if($pageNum==null || $pageNum=="")
							$pageNum=0;
						echo '<input type="hidden" id="pageNum" name="pageNum" value="'.$pageNum.'">';
					}

					//hide paging buttons if one page
					if($total_records > 0){
					?>
						<div class="row">
							<?php
							if($total_records!=NULL){
							?>
				            <div class="col-sm-6">
				       			<div class="dataTables_info" id="example-table_info"><?php echo $this->dictionary->GetValue("total_records").'&nbsp;'.$total_records;?> </div>
				       		</div>
             				<?php }?>
	            			<div class="col-sm-6">
								<div class="dataTables_paginate paging_bootstrap pull-right ">
								<?php
								//print '<span class="records-count">'.$this->dictionary->GetValue("total_records").'&nbsp;'.$total_records.'</span>';

								if($totalPages >= 1){
								?>
									<ul class="pagination <?php print $type; ?>" >
									<?php
									if($type=="post")
									{
										//use submit in paging links
										?>

										<?php
										if($pageNum > 0){ ?>
										<li><a href="javascript:;" data-pageNum="0" class="last-paging">&laquo;</a></li>
										<li><a href="javascript:;" data-pageNum="<?php echo $pageNum-1; ?>" >&lsaquo;</a>
										<?php } else {
											echo '<li><span>&laquo;</span></li>';
											echo '<li><span>&lsaquo;</span><li>';
										}

										for ($i=max(0,$pageNum-5); $i<=min(max(9,$pageNum+4),$totalPages); $i++)
									  	{
									  		if ($i == $pageNum)
												echo '<li class="active"><a class="disabled">'.($i+1).'</a></li>';
											else
												echo '<li><a href="javascript:;" data-pageNum="'.($i).'">'.($i+1).'</a></li>';
									  	}

										if($pageNum != $totalPages){ ?>
										<li><a href="javascript:;" data-pageNum="<?php echo $pageNum+1; ?>" >&rsaquo;</a></li>
									    <li><a href="javascript:;" data-pageNum="<?php echo $totalPages; ?>" class="first-paging">&raquo;</a></li>
										<?php } else {
											echo '<li><span>&rsaquo;</span></li>';
											echo '<li><span>&raquo;</span><li>';
										}
									}
									else
									{
										//user normal links
										if($pageNum > 0){ ?>
										<li><a href="<?php echo $pageAddress; ?>pageNum=0" class="last-paging">&laquo;</a></li>
										<li><a href="<?php echo $pageAddress; ?>pageNum=<?php echo $pageNum-1; ?>" >&lsaquo;</a>
										<?php } else {
											echo '<li><span>&laquo;</span></li>';
											echo '<li><span>&lsaquo;</span><li>';
										}

										for ($i=max(0,$pageNum-5); $i<=min(max(9,$pageNum+4),$totalPages); $i++)
									  	{
									  		if ($i == $pageNum)
												echo '<li calss="disabled"><span>'.($i+1).'</span></li>';
											else
												echo '<li><a href="'. $pageAddress .'pageNum='.($i).'">'.($i+1).'</a></li>';
									  	}

										if($pageNum != $totalPages){ ?>
										<li><a href="<?php echo $pageAddress; ?>pageNum=<?php echo $pageNum+1; ?>" >&rsaquo;</a></li>
									    <li><a href="<?php echo $pageAddress; ?>pageNum=<?php echo $totalPages; ?>" class="first-paging">&raquo;</a></li>
										<?php } else {
											echo '<li><span>&rsaquo;</span></li>';
											echo '<li><span>&raquo;</span><li>';
										}
									}
									?>
									</ul>
								<?php
								}
								?>
	                			</div>
							</div>
                		</div>

                	<?php }?>
					</div>
				</fieldset>
			</form>
		</div>
		<?php
	}


	public function DrawList($data, $fields, $actions, $options)
	{
		echo '<div class="todo-tasklist">';
		for ($i=0; $i<count($data); $i++)
		{
			self::DrawListItem($data[$i], $fields, $actions, $options);
		}
		echo '</div>';
	}

	public function DrawListItem($data, $fields, $actions, $options)
	{
		//check optional fields
		if(isset($fields["status-color"])){
			$borderColorField =$fields["status-color"];
		}else{
			$borderColorField="";
		}
		if(isset($fields["header"])){
			$headerField = $fields["header"];
		}else{
			$headerField = "";
		}
		if(isset($fields["picture"])){
			$pictureField = $fields["picture"];
		}else{
			$pictureField = "";
		}
		if(isset($fields["content"])){
			$contentField = $fields["content"];
		}else{
			$contentField = "";
		}
		if(isset($fields["date"])){
			$dateField = $fields["date"];
		}else{
			$dateField = "";
		}

		if(isset($fields["type"])){
			$typeField = $fields["type"];
		}else{
			$typeField = "";
		}

		if(isset($fields["unread"])){
			$unreadFiled = $fields["unread"];
		}else{
			$unreadFiled = "";
		}
		$borderColor = "green";//default

		$unread = "";
		if($borderColorField !=""){
			$borderColor = $data[$borderColorField];
		}else{
			$borderColor = "red";
		}
		echo '<div class="todo-tasklist-item todo-tasklist-item-border-'.$borderColor;

		if(isset($options["item-class"])){
			echo ' '.$options["item-class"];
		}
		if($unreadFiled != "" && $data[$unreadFiled]==0){
			$unread = " unread";
			echo $unread;
		}
		echo '"';
		if(isset($options["key"])){
			foreach ($options["key"] as $key=>$field){
				echo "data-".$key.'="'.$data[$field].'" ';
			}
		}
		if($typeField != ""){
			echo 'data-type="'.$data[$typeField].'" ';
		}
		echo '>';
		echo '<img class="todo-userpic pull-left" src="'.$data[$pictureField].'" width="27px" height="27px">';
		if($headerField != ""){
			echo '<div class="todo-tasklist-item-title">'.$data[$headerField];
			if($typeField != ""){
				if($data[$typeField] == "in"){
					echo '<i class="fa fa-download" aria-hidden="true" style="float: left; color: green"></i>';
				}else{
					echo '<i class="fa fa-upload" aria-hidden="true" style="float: left; color: orange"></i>';
				}
			}
			echo '</div>';
		}
		echo '<div class="todo-tasklist-item-text';
		if($unread != ""){
			echo " bold";
		}
		echo '">';
		if($contentField != ""){
			echo $data[$contentField];
		}
		echo '</div>';
		?>
			<div class="todo-tasklist-controls pull-left">
				<span class="todo-tasklist-date">
					<?php
					if(isset($fields["icon-color"])){
						$iconColor = $data[$fields["icon-color"]];
					}else{
						$iconColor = "";
					}
					if(isset($fields["icon"])){
						$icon = $data[$fields["icon"]];
					}else{
						$icon = "calendar";
					}

					if($dateField != ""){
						echo '<span class="label btn-xs bg-'.$iconColor.'"><span class="fa fa-'.$icon.'"></span></span> '.$data[$dateField] .'';
					}
					foreach($actions as $control){
						$keys = null;
						if(isset($options["key"])){
							foreach ($options["key"] as $key=>$field){
								$keys[$key] = $data[$field];
							}
						}
						self::DrawActionButton($control, $keys);
					} ?>
				</span>
			</div>
		</div>
		<?php
	}

	private function DrawActionButton($button, $key)
	{
		$button_class = "btn ";
		if(isset($button['class'])){
			$button_class .= $button['class'];
		}
		echo '<a href="javascript:;" class="'.$button_class.'"';
		//add data key tags
		if($key != null){
			foreach ($key as $data=>$value){
				echo "data-".$data.'="'.$value.'" ';
			}
		}
		//other options
		if(isset($button["title"])){
			echo 'title="'.$button["title"].'"';
		}
		echo ">";

		if(isset($button["show-text"]) && $button["show-text"]==false) {
			$show_text = false;
		}else{
			$show_text = true;
		}

		if(isset($button['button-icon'])){
			echo '<i class="fa fa-'.$button['button-icon'];
			if(!$show_text){
				echo " no_margin";
			}
			echo '"></i>';
		}
		if($show_text && isset($button["title"])){
			echo $button["title"];
		}
		echo '</a>';
	}


	public function OpenAccordion($id='accord1'){
		echo '<div class="panel-group accordion" id="'.$id.'">';
	}


	public function DrawAccordionList($data, $fields, $options=null){
		self::OpenAccordion();
		for($i=0; $i<count($data); $i++)
		{
			$title = $data[$i][$fields["title"]];
			$subtitle = $data[$i][$fields["subtitle"]];
			if(isset($fields["icon"])){
				$icon = $data[$i][$fields["icon"]];
			}else{
				$icon = "";
			}
			$open = false;
			if($i==0){
				$open = true;
			}
			self::OpenAccordionPanel($title, $subtitle, $icon, $open, $i);

			echo '<p>'.$data[$i][$fields["content"]].'</p>';
			if(isset($fields["attachment"]) && $data[$i][$fields["attachment"]] != null && $data[$i][$fields["attachment"]] != ""){
				echo '<a href="javascript:;" class="btn blue"><i class="fa fa-file-pdf-o"></i> '.$this->dictionary->GetValue('view_attachment').'</a>';
			}

			self::CloseAccordionPanel();
		}
		self::CloseAccordion();
	}

	public function OpenAccordionPanel($title="", $subtitle="", $icon="", $open=false, $panel_id, $parent_id='accord1')
	{
		?>
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
					<?php
					echo '<a class="accordion-toggle accordion-toggle-styled ';
					if(!$open){
						echo collapsed;
					}
					echo '" data-toggle="collapse" data-parent="#'.$parent_id.'" href="#collapse_'.$parent_id.'_'.$panel_id.'">';
					echo '<i class="fa fa-'.$icon.'"></i> ';
					echo $title;
					if($subtitle != ""){
						echo '<span class="accordion-subtitle">'.$subtitle.'</span>';
					}
					echo '</a>';

					?>
				</h4>
            </div>
            <div id="collapse_<?php echo $parent_id."_".$panel_id;?>" class="panel-collapse <?php if($open) {echo 'in';}else{echo 'collapse';}?>">
                <div class="panel-body">
		<?php
	}

	public function CloseAccordionPanel()
	{
		?>
				</div>
			</div>
		</div>
		<?php
	}

	public function CloseAccordion(){
		echo '</div>';
	}


	public function DrawMediaList($data, $fields, $options=null){
		echo '<ul class="media-list">';
		for($i=0; $i<count($data); $i++)
		{
			$title = $data[$i][$fields["title"]];
			$icon = $data[$i][$fields["icon"]];
			$iconColor = $data[$i][$fields["icon_color"]];
			$date =  $data[$i][$fields["date"]];
			$content =  $data[$i][$fields["content"]];
			$data[$i][$fields["content"]];
			$picture =  $data[$i][$fields["picture"]];

			echo '<li class="media">';
            echo '<a class="pull-left" href="javascript:;">';
            echo '<img class="todo-userpic" src="'.$picture.'" width="27px" height="27px">';
			echo '</a>';
            echo '<div class="media-body todo-comment">';
			echo '<p class="todo-comment-head">';
			echo '<span class="label btn-xs bg-'.$iconColor.'"><span class="fa fa-'.$icon.'"></span></span> ';
            echo '<span class="todo-comment-username">'.$title.'</span> &nbsp;';
            echo '<span class="todo-comment-date">'.$date.'</span>';
            echo '</p>';
            echo '<p class="todo-text-color">'.$content.'</p>';
            echo '</div>';
			echo '</li>';
		}
		echo '</ul>';
	}

	public function Alert($msg, $color="warning"){
		echo '<div class="alert alert-'.$color.'"><strong>';
		echo $this->dictionary->GetValue($msg);
		echo '</strong></div>';
	}


	public function DrawFilterValues($data){
		if($data != NULL){
		?>
			<div class="note-toolbar">
			<?php
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

				//$value = trim($value, "%");
			?>
				<span class="filter_name"><b><?php echo $this->dictionary->GetValue($name);?></b></span>
				<span class="filter_operator"><?php echo $this->dictionary->GetValue($op);?></span>
				<span class="filter_value"><?php echo $value;?></span>
				<span class="filter_separator"><?php echo ", ";?></span>
			<?php
			}
			?>
			</div>
		<?php
		}
	}

	//returns array of key fields for table
	protected function GetRowKey($dataRow, $optionsKey)
	{
		if(is_array($optionsKey)){
			$key = array();
			foreach ($optionsKey as $datakey=>$colName){
				$key[$datakey] = $dataRow[$colName];
			}
		}else{//else if key is passed as a single field name
			$key[$optionsKey]=$dataRow[$optionsKey];
		}
		return $key;
	}



	/*public function DrawTreeNode($nodeText, $nodeId, $display="none", $isSelectable = NULL, $allSelectable = 1, $icon=NULL, $nodeCatColor="#000", $iconClass=NULL, $is_top_node = false)
	{
		$showSelectButton = false;
		if(
			($allSelectable == 1) ||
		    ($isSelectable == "1" && $allSelectable == 0) ||
		    ($allSelectable == 2 && $is_top_node != true)  //option 2 means all selectable except first item in tree
		  )
		{
			$showSelectButton = true;
		}

		?>
		<li class= "parent_li" style="display:<?php echo $display; ?>">
			<a class="btn-mini select_node_button" href="javascript:;" data-id="<?php echo $nodeId;?>" data-text="<?php echo $nodeText;?>"
				<?php if(!$showSelectButton) print 'style="visibility:hidden"' ?> >
				<i class="fa fa-check"></i>
			</a>
			<!-- <span><i class="icon-plus-sign"></i> <?php echo $nodeText;?></span> -->

			<span>
				<a  href="javascript:;" data-id="<?php echo $nodeId;?>" data-text="<?php echo $nodeText;?>"><i class="<?php echo $iconClass;?>" style="color:<?php echo $nodeCatColor;?>"></i></a>
				<?php echo $nodeText;?>
			</span>

        </li>
		<?php
	}*/

	public function DrawTreeNode($nodeText, $nodeId, $nodePath, $display="none", $isSelectable = NULL, $allSelectable = 1)
	{
		$showSelectButton = false;
		if( ($isSelectable === NULL) ||
		($allSelectable == 1) ||
		($isSelectable == "1" && $allSelectable == 0)
		)
		{
			$showSelectButton = true;
		}

		?>
		<li style="display:<?php echo $display; ?>">
			<a class="btn-mini select_node_button" href="javascript:;" data-id="<?php echo $nodeId;?>" data-text="<?php echo $nodePath;?>"
				<?php if(!$showSelectButton) print 'style="visibility:hidden"' ?> >
				<i class="fa fa-check"></i>
			</a>
			<span><i class="fa fa-plus-square"></i> <?php echo $nodeText;?></span>
        </li>
		<?php
	}

	public function DrawTreeNodeCheck($nodeText, $nodeId, $nodePath, $display="none", $isSelectable = NULL, $allSelectable = 1, $coordinates = NULL, $color = NULL)
	{
		$showSelectButton = false;
		if( ($isSelectable === NULL) ||
		($allSelectable == 1) ||
		($isSelectable == "1" && $allSelectable == 0)
		)
		{
			$showSelectButton = true;
		}

		?>
		<li style="display:<?php echo $display; ?>">
			<input type="checkbox" value="<?php print $nodeId; ?>" id="<?php print $nodeId; ?>" data-path="<?php print $coordinates; ?>" data-color="<?php print $color; ?>"  data-id="<?php echo $nodeId;?>" />
			<span><i class="fa fa-plus-square"></i> <?php echo $nodeText;?></span>
        </li>
		<?php
	}


	public function DrawEditableTreeNode($nodeText, $nodeId, $nodeLvl=1, $allowEditable, $nodeStatus=NULL, $isSelectable=NULL, $display="none", $logo=null)
	{
		?>
    	<li class= "parent_li" style="display:<?php echo $display; ?>">
            <?php
            if(isset($allowEditable["Select"]) && $allowEditable["Select"]=="true"   ){
				?>
				<a class="btn-mini select_node_button" href="javascript:;" data-id="<?php echo $nodeId;?>" data-text="<?php echo $nodeText;?>" <?php if($isSelectable != "1") print 'style="visibility:hidden"' ?> >
				<i class="fa fa-check"></i>
			</a>
			<?php
			}
			?>
			<span><i class="fa fa-plus-square"></i> <?php echo $nodeText;?></span>
			<div class="treeButtons">
            <?php
            if(isset($allowEditable["Add"]) && $allowEditable["Add"]=="true"){
			?>
				<a class="<?php if($nodeStatus == 3){ echo " disabledlink " ; }else echo " add_node_button";?> " href="javascript:;" data-id="<?php echo $nodeId;?>" data-text="<?php echo $nodeText;?>" title="<?php echo $this->dictionary->GetValue("add_child");?>" <?php if($nodeStatus == 3){ echo " disabled " ; }?> ><i class="fa fa-plus"></i></a>
			<?php
			}
			if(isset($allowEditable["Edit"]) && $allowEditable["Edit"]=="true"){
			?>
				<a class="<?php if($nodeStatus == 3){ echo " disabledlink " ; }else echo " edit_node_button"?> " href="javascript:;" data-id="<?php echo $nodeId;?>" data-text="<?php echo $nodeText;?>" title="<?php echo $this->dictionary->GetValue("edit");?>" ><i class="fa fa-pencil"></i></a>
			<?php
			}
			if(isset($allowEditable["Delete"]) && $allowEditable["Delete"]=="true"){
				if($nodeStatus != 3){
				?>
					<a class=" delete_node_button" href="javascript:;" data-id="<?php echo $nodeId;?>" data-text="<?php echo $nodeText;?>" title="<?php echo $this->dictionary->GetValue("lock");?>" ><i class="fa fa-lock"></i></a>
				<?php
				}else{
				?>
					<a class=" unlock_node_button" href="javascript:;" data-id="<?php echo $nodeId;?>" data-text="<?php echo $nodeText;?>" title="<?php echo $this->dictionary->GetValue("unlock");?>" ><i class="fa fa-unlock"></i></a>
				<?php
				}
			}
			?>
			</div>
        </li>
		<?php
	}

	public function OpenTablePortlet()
	{
		?>
		<div class="portlet portlet-blue">
    		<div class="portlet-heading">
      			  <div class="portlet-title">
            		<h4></h4>
        	</div>
       	    <div class="clearfix"></div>
        </div>
        <div class="portlet-body">

		<?php

	}
	public function CloseTablePortlet()
	{
		?>
		 </div>
         	<!-- /.portlet-body -->
       </div>
    <!-- /.portlet -->
		<?php

	}

	public function DrawCardDashboard($data, $linkId, $detailTag )
	{		//Backgroun Color
		if( $data["COLOR"]) $bgColor = $data["COLOR"];
		else $bgColor = "gray";

		if(isset($data["LOGO"]) && $data["LOGO"] !="" ){$logoImg = $data["LOGO"];}
		else $logoImg = "card.png"

			//
	?>
    	<div class="col-lg-3 col-sm-6">
            <div class="circle-tile">
                <!--a href="cards.html?id= <?php echo $linkId;?>"-->
                    <div class="circle-tile-heading <?php echo $bgColor;?>">
                        <i class="fa  fa-fw fa-3x"><?php if($data["ICON"]) {?><img src="/<?php echo $this->root_directory;?>/attachment/product/<?php echo $data["ICON"];?>" width="50"><?php }?></i>
                    </div>

                <!--/a-->
                	<div class="circle-tile-content <?php echo $bgColor;?>">

                    <?php if($data["SELECTABLE"] && $data["SELECTABLE"] == 1){?>
                       		<a href="card_detail.php?product_id=<?php echo $data["NODE_ID"]?>">

                       <?php }else{?>
                      		  <a href="card_value_dashboard.php?product_id=<?php echo $data["NODE_ID"]?>">
					 	<?php }?>
							<?php if($detailTag == true){?>
                                <img src="/<?php echo $this->root_directory;?>/attachment/product/<?php echo $logoImg;?>"  alt="" width="180px" height="113px" >
                            <?php }?>
                            <div class="circle-tile-description text-faded">
                                   <?php echo $data["NODE_NAME"]?>
                            </div>
                            <div class="circle-tile-number text-faded">
                             <?php if(isset($data["SELECTABLE"]) && $data["SELECTABLE"] ==1 ){
								 		echo $data["CARD_COUNT"]." cards";
								  }elseif(isset($data["BALANCE"]) && $data["BALANCE"] !==""){
                               echo "رصيد ".$data['BALANCE']." دينار";
                            }?>
                            </div>
                		</a>
                         <span href="javascript:;" class="circle-tile-footer"> </span>



                </div>
            </div>
         </div>

	<?php }


	public function DrawBalanceSummaryDashboard($icon , $title, $value )
	{?>
	<div class="col-lg-3 col-sm-6">
          <div class="tile blue ">
            <div class="text-center">
             <a href="javascript:;" class="<?php echo $title ;?>">
              <div class="circle-tile-number text-faded"> <i class="fa <?php echo $icon ;?> fa-2x"></i> </div>
              <div class="circle-tile-number text-faded"><?php echo  $this->dictionary->GetValue($title);?> </div>
              </a>
              <a href="javascript:;" class="box-tile-footer <?php echo $title ;?>"><?php echo $value ;?></a> </div>
          </div>
        </div>

	<?php

	}
	public function DrawMainDashboard($icon , $title, $href, $target="_self" )
	{?>
     <div class="col-lg-4 col-sm-6">
        <div class="tile blue ">
            <div class="text-center">
            <a href="<?php echo $href?>" target="<?php echo $target?>">

            <div class="circle-tile-number text-faded">
                  <i class="fa <?php echo $icon?> fa-2x"></i>
            </div>
             <div class="circle-tile-number text-faded">
                <?php echo  $this->dictionary->GetValue($title);?>
             </div>
             </a>
            </div>
        </div>
    </div>


	<?php

	}

	public function DrawDropDownMenu($data, $unreadcCount = NULL,$options  )
	{
	?>
      <li class="dropdown">
        <a href="javascript:;" class="<?php echo $options["CLASS"];?>-link dropdown-toggle" data-toggle="dropdown">
            <i class="fa <?php echo $options["ICON"];?>"></i>
            <?php if($unreadcCount!= NULL && $unreadcCount!=0){?><span class="number"><?php echo $unreadcCount;?></span><i class="fa fa-caret-down"></i><?php }?>
        </a>
                    <ul class="dropdown-menu dropdown-scroll dropdown-<?php echo $options["CLASS"];?>">
    					<li class="dropdown-header">
                            <i class="fa <?php echo $options["ICON"];?> pull-<?php echo $options["ALIGN"]?>"></i>
                        </li>

    					<li id="<?php echo $options["MENU_ID"];?>">

                            <ul class="list-unstyled">
                                <?php
                                if($data!=null){
									for($i=0; $i< count($data); $i++)
									{
										if($data[$i]["IN_BALANCE"] != NULL)
							            {
							                $options["ICON_COLOR"] = "green";
							                $options["FA_CLASS"] = "fa-arrow-down";
							            }
							            elseif($data[$i]["OUT_BALANCE"] != NULL){
							                $options["ICON_COLOR"] = "red";
							                $options["FA_CLASS"] = "fa-arrow-up";
							            }
							            else
							            {
							                $options["ICON_COLOR"] = "blue";
							                $options["FA_CLASS"] = "fa-minus";
							            }
										echo '<li>';
										echo '<a href="'.$options["PREF"].'notification/notification_view.php?detailid='.$data[$i]["DETAIL_ID"].'">';
										echo '<div class="alert-icon '.$options["ICON_COLOR"].' pull-left">';
										echo '<i class="fa '.$options["FA_CLASS"].'"></i>';
										echo '</div>';
										if($data[$i]["STATUS"] == "unread")
										{
											echo '<strong>'.$this->dictionary->GetValue($data[$i]["OPERATION_TYPE"]).'</strong>';
										}
										else
										{
											echo $this->dictionary->GetValue($data[$i]["OPERATION_TYPE"]);
										}
										echo "<br />";
										echo '<span class="small pull-'.$options["ALIGN"].'">';
										echo '<strong><em>'.$data[$i]["DATE"].'</em></strong>';
										echo '</span>';
										echo '<div class="clearfix"></div> ';
										echo '</a>';

										echo'</li>';
								   }
								}else{
									echo "<li><a>".$this->dictionary->GetValue("no_data_found")."</a></li>";
								}?>
                            </ul>

                        </li>

    					<li class="dropdown-footer">
                            <a href="<?php echo $options["PREF"]?>notification/notification_list.php?list_type=<?php echo $options["TYPE"]?>"><?php echo $this->dictionary->GetValue("view_all");?></a>
                        </li>
    				</ul>
                </li>


	<?php }


	public function PrintAlerts($messages)
	{
		for($i=0; $i<count($messages); $i++)
		{
			switch ($messages[$i]["status"])
			{
				case 0:
					$title = "error";
					$alert_class = "alert-danger";
					$alert_icon = "icon-error-sign";
					break;
				case 1:
					$title = "warning";
					$alert_class = "alert-warning";
					$alert_icon = "icon-warning-sign";
					break;
				case 2:
					$title = "success";
					$alert_class = "alert-success";
					$alert_icon = "icon-ok-sign";
					break;
				default:
					$title = "info";
					$alert_class = "alert-info";
					$alert_icon = "icon-info-sign";
					break;
			}
			$this->Alert($this->dictionary->GetValue($messages[$i]["message"]));
		}
	}

	public function PrintMessages($messages, $action, $button_text="close")
	{
		$alert_class = "";
		$alert_icon = "";

		?>
		<div class="col-lg-12 pull-right">
		<div class="message">
		<?php
		for($i=0; $i<count($messages); $i++){
			switch ($messages[$i]["status"])
			{
				case 0:
					$title = "error";
					$alert_class = "alert-danger";
					$alert_icon = "icon-error-sign";
					break;
				case 1:
					$title = "warning";
					$alert_class = "alert-warning";
					$alert_icon = "icon-warning-sign";
					break;
				case 2:
					$title = "success";
					$alert_class = "alert-success";
					$alert_icon = "icon-ok-sign";
					break;
				default:
					$title = "info";
					$alert_class = "alert-info";
					$alert_icon = "icon-info-sign";
					break;
			}

			//var_dump($this->dictionary);
		?>
			<div>
        		<h3 id="myModalLabel" class="alert <?php print $alert_class;?>"><i class="<?php print $alert_icon;?>"></i>&nbsp; <?php print $this->dictionary->GetValue($title); ?></h3>
		    </div>
		    <div>
		    	<div class="shortcuts">
		      		<?php print $this->dictionary->GetValue($messages[$i]["message"]);?>
				</div>
		    </div>
		    <div class="hr"></div>
	    <?php
		}
		if($action == "modal"){
		?>
		    <div class="modal-footer ">
		        <button class="btn" data-dismiss="<?php print $action;?>" aria-hidden="true"><?php print $this->dictionary->GetValue("close"); ?></button>
		    </div>
	    <?php
		}else{
			echo '<meta charset="utf-8">';
			echo '<script src="/'.$this->root_directory.'/js/jquery1.10.2/jquery.min.js"></script>';
            echo '<script src="/'.$this->root_directory.'/js/helper.js"></script>';

            if(strpos($action, "url:") !== false){
            	$url = str_replace("url:","", $action);
  			?>
	  			<div class="modal-footer ">
			        <a href="<?php echo '../../'.$this->root_directory.$url;?>" class="btn  btn-default" ><?php print $this->dictionary->GetValue($button_text); ?></a>
			    </div>
		    <?php
		    }else{
		    ?>
		    	<div class="modal-footer ">
			        <button class="btn" onclick="javascript:window.history.back();" ><?php print $this->dictionary->GetValue("back"); ?></button>
			    </div>
		    <?php
		    }
		    ?>
            <script>
            $(document).ready(function($) {
            	var dir ="<?php echo $this->root_directory;?>";
          		if (!$("link[href*='/css/bootstrap.']").length)
    				$('<link href="/'+dir+'/css/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">').appendTo("head");

				if (!$("link[href*='/css/style.css']").length)
    				$('<link href="/'+dir+'/css/style.css" rel="stylesheet">').appendTo("head");

				if (!$("link[href*='/css/font-awesome.css']").length)
    				$('<link href="/'+dir+'/css/font-awesome.css" rel="stylesheet">').appendTo("head");
            });
            $(".logout_open").click(function(){
            	window.location.href = "/"+dir+"/logout.php"
            })
			</script>
			<?php
		}
		?>
		</div>
		</div>
	<?php
	}



	public function ConvertHTMLtoImage($t_strURL ,$t_iWidth, $t_iHeigh , $t_iRatioTypet=0 ){

		// Create instance ACAWebThumb.ThumbMaker
		$HTML_Converter = new COM("ACAWebThumb.ThumbMaker") or die("Create ACAWebThumb.ThumbMaker failed. Please make sure the component has been installed.");

		// Set the URL and start the snap job.
		$HTML_Converter->SetURL($t_strURL);
		if ( 0 == $HTML_Converter->StartSnap() ){

			// snap successful, set the thumbnail size and get image bytes
			$HTML_Converter->SetThumbSize ($t_iWidth, $t_iHeight, $t_iRatioType);

			//get image bytes by PNG format
			$t_arrThumbImageBytes = $HTML_Converter->GetImageBytes ("png");

			// output the image bytes to client browser
			if ( count($t_arrThumbImageBytes) > 0 ){

			// set the output header as PNG image, then output the thumbnail image bytes.
			header("Content-type: image/png");
				foreach($t_arrThumbImageBytes as $byte)
			echo chr($byte);
			}

		}

	}

	public function HelpMessage($help_message)
	{
		echo "<div class='note note-info'>".$this->dictionary->GetValue($help_message)."</div>";
	}

	public function SearchResultTable($data, $meta, $options)
	{
		//check if data is not empty
		if(is_array($data) && count($data)>0){

			$tableClass =$title=$body=$icon=$date = "";

			if(isset($options["tableClass"]))
					$tableClass = $options["tableClass"];
			if(isset($meta["title"])){
					$title =$meta["title"];}

			if(isset($meta["body"])){
					$body =$meta["body"];}

			if(isset($meta["date"])){
					$date =$meta["date"];}

			if(isset($meta["icon"])){
					$icon =$meta["icon"];}

			?>
			<div class="search-content-4 search-page">
            <div class="search-table table-responsive">
			<table  class="table rtl <?php echo $tableClass;?>" >

				<thead class="bg-blue">
					<tr>
						<!-- <th ><a href="javascript:;"><?php // echo $this->dictionary->GetValue($icon) ?><</a></th> -->
						<th ><a href="javascript:;"><?php echo $this->dictionary->GetValue($date)?></a></th>
						<th ><a href="javascript:;"><?php echo $this->dictionary->GetValue($title)?></a></th>
						<th ><a href="javascript:;"><?php echo $this->dictionary->GetValue($body)?></a></th>
						<th ><a href="javascript:;"><?php echo ""?></a></th>
					</tr>
				</thead>
				<tbody >
				<?php
				for ($i=0; $i<count($data); $i++){
					$key=array();
					$key=$options["key"];
					$id=$key["id"];
					echo "<tr>";
					//echo "<td class='table-status'><span class='fa fa-".$data[$i][$icon]." font-blue-soft'></span></td>";
					echo "<td class='table-date font-blue'>".$data[$i][$date]."</td>";
					echo "<td class='table-title'>"."<h3>".$data[$i][$title]."</h3>"."<p><span class='fa fa-".$data[$i][$icon]." font-blue-soft'></span>&nbsp".$data[$i][$meta["department"]]."-"."<a>"."[".$data[$i][$meta["document_number"]]."]"."</a></p></td>";
					echo "<td class='table-desc'>".$data[$i][$body]."</td>";
					if($id=="memo_id")
					{
						echo "<td class='table-download'><a href='doc_in_details.php?id=".$data[$i][$id]."'><i class='icon-doc font-green-soft'> </i></a></td>";
					}
					else if ($id=="doc_out_temp_id")
					{
						echo "<td class='table-download'><a href='doc_out_details.php?id=".$data[$i][$id]."'><i class='icon-doc font-green-soft'> </i></a></td>";
					}

					else if ($id=="client_application_id")
					{
						echo "<td class='table-download'><a href='client_application.php?id=".$data[$i][$id]."'><i class='icon-doc font-green-soft'> </i></a></td>";
					}
					echo "</tr>";
				}
				?>
				</tbody>
			</table>
            </div>
		<?php
		}else{
			?>
	            <div class="table-responsive col-headroom" >
					<table class="table rtl " style="background-color:white; border: solid 1px #3598dc; border-collapse: inherit;">
						<tr>
							<td>
							<?php
							//empty data, show default text for empty result
							if(isset($options["default-text"]) && $options["default-text"]!=""){
								print $this->dictionary->GetValue($options["default-text"]);
							}else{
								print $this->dictionary->GetValue("no_data_found");
							}
							?>
							</td>
						</tr>
					</table>
	            </div>
            </div>
			<?php
		}
	}

	public function Datatable($id, $data_url, $meta, $options)
	{
		//default options values
		$showInfo = $showActions = $showSubtotal = false;
		$showHeader = true;
		$tableClass = "table-hover table-bordered table-striped table-condensed";
		$tableHeadClass = "";
		$rowClass = "";
		$table_scrollable = "table-scrollable";

		//display info button
		if(isset($options["info"]) && $options["info"]=="true")
			$showInfo = true;
		//dispaly actions column
		if(isset($options["actions"]) && $options["actions"]=="true")
			$showActions = true;
		//display subtotal row
		if(isset($options["subtotal"]) && $options["subtotal"]!="")
			$showSubtotal = $options["subtotal"];
		//css calss of the table
		if(isset($options["tableClass"]))
			$tableClass = $options["tableClass"];
		//css class of header row
		if(isset($options["tableHeadClass"]))
			$tableHeadClass = $options["tableHeadClass"];
		//css class of row
		if(isset($options["rowClass"]))
			$rowClass = ' class="'.$options["rowClass"].'"';

		if(isset($options["table_scrollable"]) && $options["table_scrollable"]==false)
			$table_scrollable = "";


		if(isset($options["order"])){
			foreach($options["order"] as $col=>$sort){
				$orderStr = '['.$col.', "'.$sort.'"]';
			}
		}else{
			$orderStr = '[0, "asc"]';
		}
		?>
		<table id="<?php echo $id;?>" class="table rtl <?php echo $tableClass;?> ">
			<thead>
				<tr class="<?php echo $tableHeadClass;?>">
					<?php
					//loop over column headers
					foreach ($meta as $fieldInfo){
						if (strpos($fieldInfo["column"], "ACTION_COL") === 0){
							self::TH();
						}else{
							if(isset($fieldInfo["title"]))
								self::TH($this->dictionary->GetValue($fieldInfo["title"]));
							else
								self::TH($this->dictionary->GetValue($fieldInfo["column"]));
						}
					}
					if($showInfo) self::TH();
					if($showActions) self::TH();
					?>
				</tr>
			</thead>
			<?php
			//show footer row
			if(isset($options["footer"]) && $options["footer"]=="true"){
				echo "<tfoot><tr>";
				$c = 0;
				foreach ($meta as $fieldInfo){
					echo "<td>";
					if($c == 0) {
						echo $this->dictionary->GetValue("total").":";
					}else{
						if(isset($options["totals"][$c])){
							echo $options["totals"][$c];
						}
					}
					echo "</td>";
					$c++;
				}
				echo "</tr><tfoot>";
			}

			?>
		</table>

		<script>
		//javascript that draws the datatables
		$(document).ready(function() {
		    a = function() {
		        var e = $("#<?php echo $id;?>"),
		            t = e.dataTable({
						<?php
						//arabic language for data table
						if($this->language == "ARABIC"){
							echo '"language": {"url": "../assets/global/plugins/datatables/datatables.arabic.lang"},';
						}

						//callback function to be executed after table is rendered
						if(isset($options["callback"])){
							echo '"drawCallback":'.$options["callback"].',';
						}

						//order enable, disable
						if(isset($options["ordering"]) && $options["ordering"] == "false"){
							echo '"ordering": false,';
						}

						//paging enable, disable
						if(isset($options["paging"])){
							echo '"paging":'.$options["paging"].',';
							echo '"info": false,';
						}
						?>
						"bAutoWidth":false,
						"dom": "<'row'<'col-sm-12'<'<?php print $table_scrollable;?>'t>>><'row'<'col-md-5 col-sm-12'il><'col-md-7 col-sm-12'p>>",
		                "processing": true,
		                "serverSide": true,
		                "ajax": "<?php print $data_url;?>",
		                "sAjaxDataProp": "data",
		                buttons: [{
		                    extend: "print",
		                    className: "btn dark btn-outline"
		                }, {
		                    extend: "copy",
		                    className: "btn red btn-outline"
		                }, {
		                    extend: "pdf",
		                    className: "btn green btn-outline"
		                }, {
		                    extend: "excel",
		                    className: "btn yellow btn-outline "
		                }, {
		                    extend: "csv",
		                    className: "btn purple btn-outline "
		                }, {
		                    extend: "colvis",
		                    className: "btn dark btn-outline",
		                    text: "Columns"
		                }],
		                //responsive: !0,
		                order: [
		                    <?php echo $orderStr;?>
		                ],
		                lengthMenu: [
		                    [10, 25, 50, 100, -1],
		                    [10, 25, 50, 100, "All"]
		                ],
		                pageLength: 10,

		                columns: [
						<?php
						//loop over columns
						foreach ($meta as $fieldInfo)
						{
							//column width
							if(isset($fieldInfo["width"])){
								$widthStr = ', "width": "'.$fieldInfo["width"].'"';
							}else{
								$widthStr = "";
							}

							if (strpos($fieldInfo["column"], "ACTION_COL") === 0){
								echo "{ data: null, bSortable: false, render: function ( data, type, full, meta ) { return '";
								foreach($fieldInfo["buttons"] as $control){
									$keys = null;
									if(isset($options["key"])){
										$keys = $options["key"];
									}
									echo self::DatatableActionButton($control, $keys);
								}
								echo "'} $widthStr}";
							}else{
								echo "{ data: '".$fieldInfo["column"]."' $widthStr },";
							}
						}
						?>
		                ]
		        });
		        $("#sample_3_tools > li > a.tool-action").on("click", function() {
		            var e = $(this).attr("data-action");
		            t.DataTable().button(e).trigger()
		        })
		    }
		    a();
		} );
		</script>
		<?php
	}


	private function DatatableActionButton($button, $key)
	{
		$output = "";
		$button_class = "btn btn-sm";
		if(isset($button['class'])){
			$button_class .= $button['class'];
		}
		if(isset($button['action-class'])){
			$button_class .= " ".$button['action-class'];
		}
		$output .= '<a class="'.$button_class.'" ';

		if(isset($button['type']) && $button['type']=="link"){
			$url = $button['url']."?";
		}

		//add data key tags
		if($key != null){
			foreach ($key as $data=>$value){
				if(isset($button['type']) && $button['type']=="link"){
					//link
					$url .= '&'.$value.'=\'+full.'.$value.'+\'';
				}else{
					//ajax
					$output .= 'data-'.$data.'="\'+full.'.$value.'+\'" ';
				}
			}
		}

		if(isset($button['type']) && $button['type']=="link"){
			$output .= 'href="'.$url.'" ';
		}else{
			$output .= 'href="javascript:;" ';
		}

		//other options
		if(isset($button["title"])){
			$output .= ' title="'.$button["title"].'"';
		}
		if(isset($button["target"])){
			$output .= ' target="'.$button["target"].'"';
		}
		$output .= ">";

		if(isset($button["show-text"]) && $button["show-text"]==true) {
			$show_text = true;
		}else{
			$show_text = false;
		}

		if(isset($button['button-icon'])){
			$output .= '<i class="fa fa-'.$button['button-icon'];
			if(!$show_text){
				$output .= " no_margin";
			}
			$output .= '"></i> ';
		}
		if($show_text && isset($button["title"])){
			$output .= $button["title"];
		}
		$output .= '</a>';
		return $output;
	}


}
?>
