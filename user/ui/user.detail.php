<?php
include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/User.php';
require_once realpath(__DIR__ . '/../..').'/class/Tree.php';
require_once realpath(__DIR__ . '/../..').'/class/Uploader.php';

$user = new User();
$html = new HTML($LANGUAGE);
$tree = new Tree("DIR_TREE");
$uploader = new Uploader();

$dictionary = new Dictionary($LANGUAGE);
$dictionary->GetAllDictionary();
$description_key = "description_user_profile";

$languageArr = array(array(0=>"KURDISH", 1=>"KURDISH"), array(0=>"ARABIC", 1=>"ARABIC"),array(0=>"ENGLISH", 1=>"ENGLISH"));

$user_id = $USERID;
$userInfo = $user -> GetInfo($user_id);
$user_group_list = $user->GetUserGroups($user_id);
$operation = 'update';
$formMethod = 'post';
$formAction = 'code/user.update.code.php';
$options = array("class"=>"form-control", "flow"=>"horizental");
$options_readonly = array("class"=>"form-control", "disabled"=>"true", "flow"=>"horizental");

if($userInfo[0]["user_picture"] != null && $userInfo[0]["user_picture"] != ""){
    $user_pic = $FOLDERNAME.$uploader::BASE_PATH.$uploader::USER_PIC_PATH.$userInfo[0]["user_picture"];
}

$html->OpenWidget ($dictionary -> GetValue("User_details") ." - ".$userInfo[0]["NAME"] );
{
	$html->OpenForm ( $formAction, "form2", "horizental" );
	{
		$html->HelpMessage($description_key);

		$html->OpenWidget ("basic_info", null, null, 'light condensed', null);
		{
			$html->DrawFormField ( "text", "loginName", is_null($userInfo) ? NULL : $userInfo[0]["LOGIN"], NULL, $options_readonly );
			$html->DrawFormField ( "text", "DIRECTORATE_TEXT", is_null($userInfo) ? NULL : $tree -> GetPathString($userInfo[0]["user_department_node_id"]), NULL, $options_readonly );
			$html->DrawFormField ( "text", "ACCESS_DIR_TEXT", is_null($userInfo) ? NULL : $tree -> GetPathString($userInfo[0]["user_access_node_id"]), NULL, $options_readonly );
		}
		$html->CloseWidget();

		$html->OpenWidget ("user_permissions", null, null, 'light condensed', null);
		{
			echo '<ul class="list-group">';
			for($i =0 ; $i <count($user_group_list); $i++)
			{
				echo '<li class="list-group-item">'. $user_group_list[$i][1] .'</li>';
			}
            echo '</ul>';
		}
		$html->CloseWidget();

		$html->OpenWidget ("edit_info", null, null, 'light condensed', null);
		{
			$html->DrawFormField ( "text", "name", is_null($userInfo) ? NULL : $userInfo[0]["NAME"], NULL,  $options);

			$html->Literal($dictionary->GetValue("password_rules"));
			$html->DrawFormField ( "password", "password", NULL, NULL, $options );
			$html->DrawFormField ( "password", "confirm_password", NULL, NULL, $options );


			$html->DrawFormField ( "radio", "language", is_null($userInfo) ? "KURDISH" : $userInfo[0]["UI_LANGUAGE"], $languageArr, $options );

			$controls = array (
							array ( "type"=>"submit", "name"=>"Change", "value"=>"Save Changes", "list"=>NULL, "options"=>array ("class" => "btn green")),
							array ( "type"=>"hidden", "name"=>"user_id", "value"=>$user_id, "list"=>NULL, "options"=>NULL ),
							array ( "type"=>"hidden", "name"=>"operation", "value"=>$operation, "list"=>NULL, "options"=>NULL )
						);
		}
		$html->CloseWidget();

		$html->OpenWidget ("user_pic", null, null, 'light condensed', null);
		{
			$html->OpenSpan (6);
			{
				$html->DrawFormField ( "file", "user_pic", NULL, NULL, $options );
			}
			$html->CloseSpan();
			$html->OpenSpan (6);
			{
				echo '<div class="profile-userpic"><img src="'. $user_pic .'" class="img-responsive pull-right" alt=""> </div>';
			}
			$html->CloseSpan();
		}
		$html->CloseWidget();

		echo '<div class="clearfix"></div>';
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

    $('#form2').validate({
      	rules: {
			name: {
	            required: true
	        },
	        password: {
	            //minlength: 8,
	            check_password: "#name"
	        },
	        confirm_password: {
	            //minlength : 8,
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
