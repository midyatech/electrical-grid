<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 0px">
            <li class="sidebar-toggler-wrapper hide">
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                <div class="sidebar-toggler">
                    <span></span>
                </div>
                <!-- END SIDEBAR TOGGLER BUTTON -->
            </li>
            <?php
                if(
                        $user-> CheckPermission($USERID, "permission_admin_user") == 1 ||
                        $user-> CheckPermission($USERID, "permission_admin_group") == 1 ||
                        $user-> CheckPermission($USERID, "permission_map_area") == 1 ||
                        $user-> CheckPermission($USERID, "permission_admin_map_query") == 1 ||
                        $user-> CheckPermission($USERID, "permission_user_log_list") == 1 ||
                        $user-> CheckPermission($USERID, "permission_daily_dashboard") == 1
                ){
            ?>
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-wrench"></i>
                    <span class="title"><?php echo $dictionary->GetValue("admin");?></span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <?php if ($user-> CheckPermission($_SESSION["user_id"],"permission_admin_user") != 0){?>
                    <li class="nav-item  ">
                        <a href="../adminsection/user_list.php" class="nav-link ">
                            <i class="fa fa-user"></i>
                            <span class="title"><?php echo $dictionary->GetValue("users");?></span>
                        </a>
                    </li>
                    <?php } if ($user-> CheckPermission($_SESSION["user_id"],"permission_user_log_list") != 0){?>
                    <li class="nav-item  ">
                        <a href="../adminsection/user_log_list.php" class="nav-link ">
                            <i class="fa fa-group"></i>
                            <span class="title"><?php echo $dictionary->GetValue("user_log_list");?></span>
                        </a>
                    </li>
                    <?php } if ($user-> CheckPermission($_SESSION["user_id"],"permission_admin_group") != 0){?>
                    <li class="nav-item  ">
                        <a href="../adminsection/group_list.php" class="nav-link ">
                            <i class="fa fa-group"></i>
                            <span class="title"><?php echo $dictionary->GetValue("groups");?></span>
                        </a>
                    </li>
                    <?php } if( $user-> CheckPermission($USERID, "permission_map_area") == 1 ){ ?>
                    <li class="nav-item">
                        <a href="../adminsection/map_area.php" class="nav-link ">
                            <span class="title">
                            <i class="fa fa-map"></i>
                            <?php echo $dictionary->GetValue("map_area");?></span>
                        </a>
                    </li>
                    <?php } if( $user-> CheckPermission($USERID, "permission_admin_map_query") == 1 ){ ?>
                    <li class="nav-item">
                        <a href="../adminsection/map_search.php" class="nav-link ">
                            <span class="title">
                            <i class="fa fa-map"></i>
                            <?php echo $dictionary->GetValue("map_search");?></span>
                        </a>
                    </li>
                    <?php } if( $user-> CheckPermission($USERID, "permission_daily_dashboard") == 1 ){ ?>
                    <li class="nav-item">
                        <a href="../adminsection/daily_dashboard.php" class="nav-link ">
                            <span class="title">
                            <i class="fa fa-map"></i>
                            <?php echo $dictionary->GetValue("big_zigma");?></span>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </li>

            <?php
                }
                if(
                        $user-> CheckPermission($USERID, "permission_add_survey") == 1 ||
                        //$user-> CheckPermission($USERID, "permission_map") == 1 ||
                        $user-> CheckPermission($USERID, "permission_list_survey_count") == 1 ||
                        $user-> CheckPermission($USERID, "permission_list_survey_summary") == 1 ||
                        //$user-> CheckPermission($USERID, "permission_user_salary") == 1 ||
                        //$user-> CheckPermission($USERID, "permission_list_survey") == 1 ||
                        $user-> CheckPermission($USERID, "permission_add_gateway") == 1 ||
                        $user-> CheckPermission($USERID, "permission_add_line") == 1
                ){
            ?>

            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-map-marker"></i>
                    <span class="title">
                    <?php echo $dictionary->GetValue("survey");?></span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">

                    <?php if($user-> CheckPermission($USERID, "permission_add_survey") == 1 ) { ?>
                    <li class="nav-item  ">
                        <a href="../survey/add_survey.php" class="nav-link ">
                            <i class="fa fa-plus"></i>
                            <span class="title"><?php echo $dictionary->GetValue("survey");?></span>
                        </a>
                    </li>
                    <?php } /* if($user-> CheckPermission($USERID, "permission_map") == 1 ) { ?>
                    <li class="nav-item start ">
                        <a href="../survey/map.php" class="nav-link ">
                            <i class="fa fa-map"></i>
                            <span class="title"><?php echo $dictionary->GetValue("map");?></span>
                        </a>
                    </li>
                    <?php } */ if($user-> CheckPermission($USERID, "permission_list_survey_count") == 1 ) { ?>
                    <li class="nav-item  ">
                        <a href="../survey/service_point_count.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("service_point_count");?></span>
                        </a>
                    </li>
                    </li>
                    <?php } if($user-> CheckPermission($USERID, "permission_list_survey_summary") == 1 ) { ?>
                    <li class="nav-item  ">
                        <a href="../survey/service_point_area_summary.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("service_point_summary");?></span>
                        </a>
                    </li>
                    <?php } /* if($user-> CheckPermission($USERID, "permission_user_salary") == 1 ) { ?>
                    <li class="nav-item  ">
                        <a href="../survey/user_salary.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("user_salary");?></span>
                        </a>
                    </li>
                    <?php }  if($user-> CheckPermission($USERID, "permission_list_survey") == 1 ) { ?>
                    <li class="nav-item  ">
                        <a href="../survey/survey_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("survey_list");?></span>
                        </a>
                    </li>
                    <?php } */ if($user-> CheckPermission($USERID, "permission_add_gateway") == 1 ) { ?>
                    <li class="nav-item  ">
                        <a href="../survey/add_gateway.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("add_gateway");?></span>
                        </a>
                    </li>
                    <?php } if($user-> CheckPermission($USERID, "permission_add_line") == 1 ) { ?>
                    <li class="nav-item  ">
                        <a href="../survey/add_line.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("add_line");?></span>
                        </a>
                    </li>
                    <?php } ?>

                    <li class="nav-item  ">
                        <a href="../survey/update_grid.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("update_grid");?></span>
                        </a>
                    </li>

                </ul>
            </li>

            <?php } if(
                        $user-> CheckPermission($USERID, "permission_list_assembly") == 1 ||
                        $user-> CheckPermission($USERID, "permission_add_assembly") == 1 ||
                        $user-> CheckPermission($USERID, "permission_list_enclosure") == 1 ||
                        //$user-> CheckPermission($USERID, "permission_meter_list") == 1 ||
                        $user-> CheckPermission($USERID, "permission_gateway_list") == 1 ||
                        //$user-> CheckPermission($USERID, "permission_add_enclosure") == 1 ||
                        $user-> CheckPermission($USERID, "permission_add_enclosure_and_meter") == 1 ||
                        //$user-> CheckPermission($USERID, "permission_enclosure_count") == 1 ||
                        //$user-> CheckPermission($USERID, "permission_enclosure_tracing") == 1 ||
                        $user-> CheckPermission($USERID, "permission_iccid_list") == 1 ||
                        $user-> CheckPermission($USERID, "permission_assembly_team_admin") == 1 ||
                        //$user-> CheckPermission($USERID, "permission_assembly_import") == 1 ||
                        //$user-> CheckPermission($USERID, "permission_team_progress") == 1 ||
                        $user-> CheckPermission($USERID, "permission_project_progress") == 1 ||

                        $user-> CheckPermission($USERID, "permission_add_enclosure_user") == 1


                    ) { ?>

            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cubes"></i>
                    <span class="title">
                    <?php echo $dictionary->GetValue("Assembly");?></span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <?php
                    if($user-> CheckPermission($USERID, "permission_list_assembly") == 1 ) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/assembly_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("assembly_list");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_add_assembly") == 1) { ?>
                        <li class="nav-item start ">
                            <a href="../assembly/add_assembly.php" class="nav-link ">
                                <i class="fa fa-plus"></i>
                                <span class="title"><?php echo $dictionary->GetValue("Add_Assembly");?></span>
                            </a>
                        </li>
                        <li class="nav-item start ">
                            <a href="../assembly/add_vanstock.php" class="nav-link ">
                                <i class="fa fa-plus"></i>
                                <span class="title"><?php echo $dictionary->GetValue("add_vanstock");?></span>
                            </a>
                        </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_assembly_team_admin") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/teams.php" class="nav-link ">
                            <i class="fa fa-users"></i>
                            <span class="title"><?php echo $dictionary->GetValue("teams");?></span>
                        </a>
                    </li>
                    <?php } /*
                    if($user-> CheckPermission($USERID, "permission_assembly_import") == 1) { ?>
                        <li class="nav-item start ">
                            <a href="../assembly/import.php" class="nav-link ">
                                <i class="fa fa-download"></i>
                                <span class="title"><?php echo $dictionary->GetValue("import_data");?></span>
                            </a>
                        </li>
                    <?php } */
                    if($user-> CheckPermission($USERID, "permission_add_enclosure") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/scan_enclosure.php" class="nav-link ">
                            <i class="fa fa-tasks"></i>
                            <span class="title"><?php echo $dictionary->GetValue("my_work");?></span>
                        </a>
                    </li>
                    <?php }

                    if($user-> CheckPermission($USERID, "permission_add_enclosure") == 1 || $user-> CheckPermission($USERID, "permission_add_enclosure_user") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/enclosure_meters.php" class="nav-link ">
                            <i class="fa fa-plus"></i>
                            <span class="title"><?php echo $dictionary->GetValue("enclosure_meters");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_project_progress") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/project_progress.php" class="nav-link ">
                            <i class="fa fa-line-chart"></i>
                            <span class="title"><?php echo $dictionary->GetValue("project_progress");?></span>
                        </a>
                    </li>
                    <?php } /*
                    if($user-> CheckPermission($USERID, "permission_team_progress") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/team_progress.php" class="nav-link ">
                            <i class="fa fa-line-chart"></i>
                            <span class="title"><?php echo $dictionary->GetValue("team_progress");?></span>
                        </a>
                    </li>
                    <?php } */
                    if($user-> CheckPermission($USERID, "permission_list_enclosure") == 1 ) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/enclosure_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("enclosure_list");?></span>
                        </a>
                    </li>
                    <li class="nav-item start ">
                        <a href="../assembly/enclosure_meter_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("enclosure_meter_list");?></span>
                        </a>
                    </li>
                    <?php }
                    /*
                    if($user-> CheckPermission($USERID, "permission_enclosure_count") == 1 ) { ?>
                    <li class="nav-item  ">
                        <a href="../assembly/enclosure_count.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("enclosure_summary");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_enclosure_tracing") == 1 ) { ?>
                    <li class="nav-item  ">
                        <a href="../assembly/enclosure_tracing.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("enclosure_tracing");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_meter_list") == 1 ) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/meter_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("meter_list");?></span>
                        </a>
                    </li>
                    <?php } */
                    if($user-> CheckPermission($USERID, "permission_gateway_list") == 1 ) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/gateway_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("gateway_list");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_iccid_list") == 1 ) { ?>
                    <li class="nav-item start ">
                        <a href="../assembly/iccid_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("iccid_list");?></span>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </li>









            <?php } if(
                        $user-> CheckPermission($USERID, "permission_shipping_trace_list") == 1 ||
                        $user-> CheckPermission($USERID, "permission_shipping_list") == 1 ||
                        $user-> CheckPermission($USERID, "permission_shipping_order") == 1
                    ) { ?>

            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cubes"></i>
                    <span class="title">
                    <?php echo $dictionary->GetValue("shipping");?></span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <?php
                    if($user-> CheckPermission($USERID, "permission_shipping_trace_list") == 1 ) { ?>
                    <li class="nav-item start ">
                        <a href="../shipping/shipping_trace_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("shipping_trace_list");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_shipping_list") == 1 ) { ?>
                    <li class="nav-item start ">
                        <a href="../shipping/shipping_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("shipping_list");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_shipping_order") == 1 ) { ?>
                    <li class="nav-item start ">
                        <a href="../shipping/shipping_order.php" class="nav-link ">
                            <i class="fa fa-plus"></i>
                            <span class="title"><?php echo $dictionary->GetValue("shipping_order");?></span>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </li>











            <?php }
                if(
                    //$user-> CheckPermission($USERID, "permission_installation") == 1 ||
                    $user-> CheckPermission($USERID, "permission_enclosure_installation") == 1 ||
                    //$user-> CheckPermission($USERID, "permission_edit_enclosure") == 1 ||
                    $user-> CheckPermission($USERID, "permission_enclosure_installation_list") == 1 ||
                    $user-> CheckPermission($USERID, "permission_meter_installation_list") == 1 ||
                    $user-> CheckPermission($USERID, "permission_enclosure_installation_summary_list") == 1 ||
                    $user-> CheckPermission($USERID, "permission_installation_summary") == 1 ||
                    $user-> CheckPermission($USERID, "permission_installation_problem_count") == 1 ||
                    $user-> CheckPermission($USERID, "permission_installation_update_enclosure") == 1
                ) {
            ?>
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-wrench"></i>
                    <span class="title">
                    <?php echo $dictionary->GetValue("Installation");?></span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <?php
                    if($user-> CheckPermission($USERID, "permission_enclosure_installation") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../installation/installation.php" class="nav-link ">
                            <i class="fa fa-wrench"></i>
                            <span class="title"><?php echo $dictionary->GetValue("install_meters");?></span>
                        </a>
                    </li>
                    <?php } /*
                    if($user-> CheckPermission($USERID, "permission_edit_enclosure") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../installation/edit_enclosure.php" class="nav-link ">
                            <i class="fa fa-pencil-square-o"></i>
                            <span class="title"><?php echo $dictionary->GetValue("edit_enclosure");?></span>
                        </a>
                    </li>
                    <?php } */

                    if($user-> CheckPermission($USERID, "permission_installation_update_enclosure") == 1) { ?>
                        <li class="nav-item start ">
                            <a href="../installation/update_enclosure.php" class="nav-link ">
                                <i class="fa fa-exchange"></i>
                                <span class="title"><?php echo $dictionary->GetValue("update_enclosure");?></span>
                            </a>
                        </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_installation_summary") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../installation/installation_summary.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("installation_summary");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_meter_installation_list") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../installation/enclosure_installation_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("enclosure_installation_list");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_enclosure_installation_list") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../installation/meter_installation_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("meter_installation_list");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_enclosure_installation_summary_list") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../installation/enclosure_installation_summary_list.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("enclosure_installation_summary_list");?></span>
                        </a>
                    </li>
                    <?php }
                    if($user-> CheckPermission($USERID, "permission_installation_problem_count") == 1) { ?>
                    <li class="nav-item start ">
                        <a href="../installation/installation_problem_count.php" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title"><?php echo $dictionary->GetValue("installation_problem_count");?></span>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>



            <?php  if($user-> CheckPermission($USERID, "permission_supply_chain") == 1) { ?>
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-link"></i>
                    <span class="title">
                    <?php echo $dictionary->GetValue("Supply Chain");?></span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item start ">
                        <a href="../supplychain/stock.php" class="nav-link ">
                            <i class="fa fa-check-square-o"></i>
                            <span class="title"><?php echo $dictionary->GetValue("Dashboard");?></span>
                        </a>

                        <a href="../supplychain/transfer_orders.php" class="nav-link ">
                            <i class="fa fa-exchange"></i>
                            <span class="title"><?php echo $dictionary->GetValue("Transfer Orders");?></span>
                        </a>

                        <a href="../supplychain/transfer_order.php" class="nav-link ">
                            <i class="fa fa-plus-square"></i>
                            <span class="title"><?php echo $dictionary->GetValue("Create Order");?></span>
                        </a>

                        <a href="../supplychain/search.php" class="nav-link ">
                            <i class="fa fa-search"></i>
                            <span class="title"><?php echo $dictionary->GetValue("Advanced Search");?></span>
                        </a>

                    </li>
                </ul>
            </li>
            <?php } ?>







            <?php
                if($user-> CheckPermission($USERID, "permission_user_detail") == 1) {
            ?>
            <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-wrench"></i>
                    <span class="title">
                    <?php echo $dictionary->GetValue("User_settings");?></span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item ">
                        <a href="../user/user_detail.php" class="nav-link ">
                            <span class="title">
                            <i class="fa icon-user"></i>
                            <?php echo $dictionary->GetValue("User_settings");?></span>
                        </a>
                    </li>
                    <li class="nav-item start ">
                        <a href="../user/map_settings.php" class="nav-link ">
                            <i class="fa fa-map-marker"></i>
                            <span class="title"><?php echo $dictionary->GetValue("map_settings");?></span>
                        </a>
                    </li>
                    <li class="nav-item start ">
                        <a href="../SmartMeter.apk" class="nav-link ">
                            <i class="fa fa-download"></i>
                            <span class="title"><?php echo $dictionary->GetValue("download_app");?></span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php } ?>
            <li class="nav-item">
                <a href="../logout.php" class="nav-link ">
                    <span class="title">
                    <i class="fa icon-key"></i>
                    <?php echo $dictionary->GetValue("logout");?></span>
                </a>
            </li>
        </ul>
        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->
