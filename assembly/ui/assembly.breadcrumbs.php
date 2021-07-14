<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="assembly_list.php"><?php echo $dictionary->GetValue("assembly_list");?></a>
        </li>
        <?php
        if (isset($_GET["id"]) || isset($_GET["tid"])) {
            $id = $_GET["id"];
            $ao_text = $dictionary->GetValue("assembly_order_details");
            echo '<li>
                <i class="fa fa-circle"></i>';
                if (!isset($_GET["aoid"])) {
                    echo '<span>'.$ao_text.'</span>';
                } else {
                    $aoid = $_GET["aoid"];
                    echo '<a href="assembly_details.php?id='.$aoid.'">'.$ao_text.'</a>';
                }
            echo '</li>';

            $curPageName = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
            $text = $dictionary->GetValue("enclsure_configuration");

            if (isset($_GET["aoid"])) {
                if (!isset($_GET["ecid"])) {
                    echo '<li>
                        <i class="fa fa-circle"></i>
                        <span>'.$text.'</span>
                    </li>';
                } else {
                    //scan_enclosure_user.php?ecid=3&aoid=164&tid=136420
                    $aoid = $_GET["aoid"];
                    $id = $_GET["tid"];
                    echo '<li>
                        <i class="fa fa-circle"></i>
                        <a href="assembly_transformer_details.php?aoid='.$aoid.'&id='.$id.'">'.$text.'</a>
                    </li>';

                    if(isset($_GET["sn"])) { //ecid=35&aoid=422&tid=111701
                        echo '<li>
                            <i class="fa fa-circle"></i>
                            <a href="scan_enclosure_user.php?ecid='.$_GET["ecid"].'&aoid='.$aoid.'&tid='.$_GET["tid"].'">'.$dictionary->GetValue("enclosure").'</a>
                        </li>';
                        echo '<li>
                            <i class="fa fa-circle"></i>
                            <span>'.$dictionary->GetValue("meters").'</span>
                        </li>';
                    } else {
                        echo '<li>
                            <i class="fa fa-circle"></i>
                            <span>'.$dictionary->GetValue("enclosure").'</span>
                        </li>';
                    }
                }

            }
        }
        ?>
    </ul>
</div>
<br>