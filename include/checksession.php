<?php
if (session_id() == '')
	session_start();



if( !isset($_SESSION["user_id"]) ){
    require_once 'config.php';
	//header('Location: '.ROOT_DIR.'/index.php');
    ?>
    <script>
        window.location.assign("<?php echo 'http://95.216.201.198';?>");
    </script>
    <?php
	die();
}
// spl_autoload_register(function($class) {
//     switch($class){
//         case "HTML":
//         {
//             if (is_readable(realpath(__DIR__ . '/..')."/class/HtmlHelper.php")) {
//                 require_once realpath(__DIR__ . '/..').'/class/HtmlHelper.php';
//             } else if(realpath(__DIR__ . '/../..').'/class/HtmlHelper.php'){
//                 require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
//             }
//             break;
//         }
//         default:
//         {
//             if (is_readable(realpath(__DIR__ . '/..')."/class/$class.php")) {
//                 require_once realpath(__DIR__ . '/..')."/class/$class.php";
//             } else if (is_readable((__DIR__ . '/..')."/class/$class.class.php")) {
//                 require_once realpath(__DIR__ . '/..')."/class/$class.class.php";
//             } else if (is_readable(realpath(__DIR__ . '/../..')."/class/$class.php")) {
//                 require_once realpath(__DIR__ . '/../..')."/class/$class.php";
//             } else if (is_readable((__DIR__ . '/../..')."/class/$class.class.php")) {
//                 require_once realpath(__DIR__ . '/../..')."/class/$class.class.php";
//             }
//             break;
//         }
//     }
// });
?>
