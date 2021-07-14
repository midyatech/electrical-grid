<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
//include_once realpath(__DIR__ . '/../..').'/include/settings.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..') . '/class/Tree.php';

$user = new User();
$html = new HTML($LANGUAGE);
$dirTree= new Tree("DIR_TREE");
$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_user_add";

$options = array("class"=>"form-control", "flow"=>"horizental");
$options_readonly = array("class"=>"form-control", "disabled"=>"true", "flow"=>"horizental");

$operation = 'insert';
$formMethod = 'post';
$formAction = 'code/user.insert.code.php';
$languageArr = array(array(0=>"KURDISH", 1=>"KURDISH"), array(0=>"ARABIC", 1=>"ARABIC"),array(0=>"ENGLISH", 1=>"ENGLISH"));
$operatingSystemArray = array(array(0=>"0" , 1=>"ANDROID"), array(0=>"1" , 1=>"WINDOWS"));
$html->OpenWidget ("User_Add");
{
	$html->OpenForm ( $formAction, "form2", "horizental" );
	{
		$html->HelpMessage($description_key);
		$html->DrawFormField ( "text", "name", NULL, NULL, $options );
		$html->DrawFormField ( "text", "loginName", NULL, NULL, $options );
		$html->Literal($dictionary->GetValue("password_rules"));
		$html->DrawFormField ( "password", "password", NULL, NULL, $options );
		$html->DrawFormField ( "password", "confirm_password", NULL, NULL, $options );
		// $dirTree = array (
		// 			array ( "type"=>"hidden", "name"=>"DIRECTORATE", "value"=>NULL, "list"=>NULL, "options"=>NULL ),
		// 			array ( "type"=>"text", "name"=>"DIRECTORATE_TEXT", "value"=>NULL, "list"=>NULL, "options"=>array("readonly"=>"readonly", "class"=>"open_agent_tree form-control", "tree"=>"") )
		// 	);
		// $html->DrawGenericFormField ( "DIRECTORATE", $dirTree );

		$dir_Tree = array (
				array ( "type"=>"hidden", "name"=>"DIRECTORATE", "value"=>NULL, "list"=>NULL, "options"=>NULL ),
				array ( "type"=>"text", "name"=>"DIRECTORATE_TEXT", "value"=>NULL, "list"=>NULL, "options"=>array("readonly"=>"readonly", "class"=>"open_agent_tree form-control", "tree"=>"") )
		);
		$html->DrawGenericFormField ( "DIRECTORATE", $dir_Tree, "horizental" );

		$dir_Tree = array (
				array ( "type"=>"hidden", "name"=>"ACCESS_DIR", "value"=>NULL, "list"=>NULL, "options"=>NULL ),
				array ( "type"=>"text", "name"=>"ACCESS_DIR_TEXT", "value"=>NULL, "list"=>NULL, "options"=>array("readonly"=>"readonly", "class"=>"open_access_tree form-control", "tree"=>"") )
		);
		$html->DrawGenericFormField ( "ACCESS_DIR", $dir_Tree, "horizental" );

		//$html->DrawFormField ( "checkbox", "web_browse", NULL, "1", null );
		$html->DrawFormField ( "radio", "language", "KURDISH", $languageArr, $options );


		$controls = array (
						array ( "type"=>"submit", "name"=>"Save", "value"=>"Save", "list"=>NULL, "options"=>array ("class" => "btn green")),
						array ( "type"=>"hidden", "name"=>"operation", "value"=>$operation, "list"=>NULL, "options"=>NULL )
					);
	}
	$html->CloseForm ($controls);
}
$html->CloseWidget();
?>


<script>
//functions for strong password
var LOWER = /[a-z]/,
UPPER = /[A-Z]/,
DIGIT = /[0-9]/,
DIGITS = /[0-9].*[0-9]/,
SPECIAL = /[^a-zA-Z0-9]/,
SAME = /^(.)\1+$/;

function rating(rate, message) {
	return {
		rate: rate,
		messageKey: message
	};
}
function uncapitalize(str) {
	return str.substring(0, 1).toLowerCase() + str.substring(1);
}
function passwordRating(password, username) {
	if (!password || password.length < 8)
	return rating(0, "too short");
	if (username && password.toLowerCase().match(username.toLowerCase()))
	return rating(0, "similar to username");
	if (SAME.test(password))
	return rating(1, "very weak");

	var lower = LOWER.test(password),
	upper = UPPER.test(password),
	digit = DIGIT.test(password),
	special = SPECIAL.test(password);
	//digits = DIGITS.test(password),

	if (lower && upper && digit && special){
		return rating(4, "strong");
	}else{
		return rating(2, "weak");
	}
}

$(document).ready(function(){
	$.validator.addMethod("check_password", function(value, element, usernameField) {
		// use untrimmed value
		var password = element.value,
		// get username for comparison, if specified
		username = $(typeof usernameField != "boolean" ? usernameField : []);

		if(password.length == 0){
			return true;
		}else{
			var rating = passwordRating(password, username.val());

			$(element).siblings("#messageKey").remove();
			if(rating.rate < 4){
				//invalid
				$('<span id="messageKey" style="color:#a94442">'+rating.messageKey+'</span>').insertAfter(element);
			}
			return rating.rate > 3;
		}
	}, "&nbsp;");
	
	$.validator.addMethod("check_password", function(value, element, usernameField) {
		// use untrimmed value
		var password = element.value,
		// get username for comparison, if specified
		username = $(typeof usernameField != "boolean" ? usernameField : []);

		if(password.length == 0){
			return true;
		}else{
			var rating = passwordRating(password, username.val());

			$(element).siblings("#messageKey").remove();
			if(rating.rate < 4){
				//invalid
				$('<span id="messageKey" style="color:#a94442">'+rating.messageKey+'</span>').insertAfter(element);
			}
			return rating.rate > 3;
		}
	}, "&nbsp;");

    $('#form2').validate(
    {
      	rules: {
	        name: {
	            required: true
	        },
	        loginName: {
	            required: true,
	            minlength: 5
	        },
	        DIRECTORATE_TEXT: {
	            required: true
	        },
			ACCESS_DIR_TEXT: {
			   required: true
		    },
			password: {
	            check_password: "#name"
	        },
	        confirm_password: {
	            equalTo : "#password"
	        }
      	},
		highlight: function(element) {
		  $(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
		  $(element).closest('.form-group').removeClass('has-error');
		},
		success: function(element) {
		  $(element).closest('.form-group').removeClass('has-error');
		},
		errorPlacement: function(error, element) {}
    });
}); // end document.ready
</script>
