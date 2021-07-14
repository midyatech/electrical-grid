<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Remove LIST</title>
</head>

<body>
<script src="../../theme-js/jquery-1.7.2.min.js"></script>


		<?php  
		require_once realpath(__DIR__ . '/../..') . '/include/settings.php';
		require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
		require_once realpath(__DIR__ . '/../..').'/class/User.php';
		require_once realpath(__DIR__ . '/../..').'/class/Group.php';
		$html = new HTML($LANGUAGE);
		$user= new User();
		$group= new Group();
		
		$value = array(0,1,2);// for selected index
		
		$group_list = $user->UserNonAssosiatedGroups(23); // id and name
		$user_group_list = $user->GetUserGroups(23);		//echo $list[1];
		$optionsOptional = array("multiple"=>"multiple", "style"=>"width:200px; height:300px");
		?>
        <table style="width:40%;" cellpadding="5" cellspacing="5" dir="ltr">
        	<tr>
            <td><?php $html->Select("groupList", NULL, $group_list, $optionsOptional) ;?></td>
				<td>
                	<table>
                    	<tr><td><input type="button" id="btn_add" name="add"  value="ADD>>" style="width:100px; font-size:10px; text-align:left" /></td></tr>
                        <tr><td><input type="button" id="btn_addall" name="addAll" value="ADD ALL>>" style="width:100px; font-size:10px; text-align:left"  /></td></tr>
                        <tr><td><input type="button" id="btn_remove" name="remove"  value="<< Remove" style="width:100px; font-size:10px; text-align:left" /></td></tr>
                        <tr><td><input type="button" id="btn_removeAll" name="removeAll" value="<< Remove All" style="width:100px; font-size:10px; text-align:left" /></td></tr>
                    </table>
                </td>
       			<td><?php $html->Select("user_groupList", NULL, $user_group_list, $optionsOptional) ;?></td>

            </tr>
          
        </table>
</body>


</html>
<script>
	
$("#btn_addall").live('click', function() {
	swap_data("groupList" , "user_groupList");
	$('#groupList').children().remove();
	$(this).attr("disabled", "disabled");
	$("#btn_removeAll").removeAttr( "disabled" );		
});

$("#btn_add").live('click', function() {
	swap_selected_data("groupList","user_groupList" );
});
$("#btn_remove").live('click', function() {
	swap_selected_data("user_groupList","groupList" );
});

$("#btn_removeAll").live('click', function() {
	swap_data("user_groupList" ,"groupList"  );
	$('#user_groupList').children().remove();
	$(this).attr("disabled", "disabled");
	$("#btn_addall").removeAttr( "disabled" );		
});

function swap_data(sourcdID, toID){
	$from = document.getElementById(sourcdID);
	$to = document.getElementById(toID);
	for( var $i = 0; $i < $from.options.length; $i++ )
            {
				var option = document.createElement("option");
				option.value = $from.options[$i].value;
				option.text  = $from.options[$i].text;
				$to.appendChild(option);
				
			}
	
	}

function swap_selected_data(from, to) {
    var src = document.getElementById(from);
	var to = document.getElementById(to);
    for(var count= src.options.length-1; count >= 0; count--) {
		 if(src.options[count].selected == true) {
			var option = src.options[count];
			var optionTo = document.createElement("option");
			optionTo.text  = src.options[count].text;
			optionTo.value = src.options[count].value;
			try {
				src.remove(count, null); // standard
			 }catch(error) {
				src.remove(count); // IE
				to.appendChild(optionTo);
				
			 }
	 	}
    }
}

		</script>
		
	