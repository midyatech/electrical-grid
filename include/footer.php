            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->

        <?php //include 'quick-sidebar.php' ?>

        </div>
        <!-- END CONTAINER -->
        <!-- BEGIN FOOTER -->
        <div class="page-footer">
            <div class="page-footer-inner"> <?php echo date("Y");?> &copy; Midyatech
            <!--      <a href="http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes" title="Purchase Metronic just for 27$ and get lifetime updates for free" target="_blank">Purchase Metronic!</a>-->
            <div class="scroll-to-top">
                <i class="icon-arrow-up"></i>
            </div>
        </div>
        <!-- END FOOTER -->


        <div id="myModal" class="modal fade modal-scroll" tabindex="-1" role="basic" data-backdrop="static" data-keyboard="false" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                    </div>
                    <!--div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                    </div-->
                </div>
            </div>
        </div>

        <div id="subModal" class="modal fade modal-scroll" tabindex="-1" role="basic" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                    </div>
                    <!--div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                    </div-->
                </div>
            </div>
        </div>

        <div id="confirmModal" class="modal fade modal-scroll" tabindex="-1" role="basic" style="display: none;" data-result="0">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title"><?php echo $dictionary->GetValue("Confirmation"); ?></h4>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <div class="">
                            <button data-bb-handler="cancel" id="cancel" type="button" class="btn btn-default"><?php echo $dictionary->GetValue("Cancel"); ?></button>
                            <button data-bb-handler="confirm" id="ok" type="button" class="btn btn-primary"><?php echo $dictionary->GetValue("Are You Sure?"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading div -->
        <div id="loader" style="z-index: 1011; position: absolute; padding: 0px; margin: 0px;
            width: 100%; height: 100%; top: 0; left: 0;
            text-align: center; background-color: rgba(255, 255, 255, 0.7); border: solid 0px #efefef; cursor: wait; display:none">
            <span class="helper" style="display: inline-block;height: 100%;vertical-align: middle;"></span>
            <div class="loading-message loading-message-boxed"><img src="../assets/global/img/loading-spinner-grey.gif" align="">
                <span>&nbsp;&nbsp;LOADING...</span>
            </div>
        </div>
        <!-- End Loading div -->

        <!--[if lt IE 9]>
        <script src="../assets/global/plugins/respond.min.js"></script>
        <script src="../assets/global/plugins/excanvas.min.js"></script>
        <![endif]-->
        <!-- BEGIN CORE PLUGINS -->

        <script src="../assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <script src="../assets//global/plugins/bootstrap-growl/jquery.bootstrap-growl.min.js" type="text/javascript"></script>

        <script src="../assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->

        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="../assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="../assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->

        <!-- begin project scripts -->
        <script src="../js/helper.js" type="text/javascript"></script>
        <script src="../js/tree.js" type="text/javascript"></script>
        <script src="../assets/lib/bootstrap_datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
        <script src="../assets/lib/scollIntoView.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>

        <!-- end project scripts -->
        <script>
        //GetLocalStatus();
        </script>
    </body>


</html>
