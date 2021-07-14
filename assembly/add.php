<?php
include '../include/header.php';
?>
<style>
    .enclosure{
        border: 2px solid #666;
        border-radius: 15px !important;
    }
    .col{
        padding-right:100px;
        padding-left:100px;
    }
    .chat-form{
        background-color: #ccc !important;
    }
    .chat-form.option2{
        background-color: #fff !important;
        border: 1px solid #ccc;
    }
    .mt-widget-3{
        background-color:#fff;
    }
    .mt-widget-3 .mt-head{
        margin-bottom: 0;
    }
    .mt-widget-3 .mt-head .mt-head-button{
        padding: 0;
    }
    .form-control{
        padding-left: 6px;
        padding-right: 6px;
    }
    .col-8{
        width: 12.5%;
        float: left;
        padding: 0 10px;
    }
    .mt-body-actions-icons{
        padding: 0 15px;
    }
</style>
<div class="row form">
    <form role="form" class="form-horizontal form-row-seperated">
        <div class="col-lg-12 wizard">
            <div class="row">
                <div class="col-lg-4 col-md-offset-4">
                    <div class="chat-form">
                        <div class="">
                            <input class="form-control" type="text" placeholder="Enclosure SN" tabindex="1">
                        </div>
                    </div>
                </div>
            </div>

            <div class="well enclosure">
                <div class="row">
                    <div class="col-lg-4 col-md-offset-4">
                        <div class="chat-form option2">
                            <div class="col-lg-4">
                                <img src="../img/wifi-router-bw.png" width="100">
                            </div>
                            <div class="col-lg-8">
                                <input class="form-control" type="text" placeholder="Gateway SN" style="margin-top:34px"  tabindex="2">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12" style="height: 40px">
                            &nbsp;
                    </div>
                </div>

                <div class="row">

                    <div class="col-8">
                        <div class="mt-widget-3">
                            <div class="mt-head bg-white">
                                <img src="../img/meter-bw.png" width="100" style=" display:block;margin:auto;">
                                <div class="mt-head-button">
                                    <div class="mt-action-buttons ">

                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn red btn-outline">
                                                <input type="radio" name="ph1" id="ph1_3" value="3" autocomplete="off"> Three
                                            </label>
                                            <label class="btn blue btn-outline active">
                                                <input type="radio" name="ph1" id="ph1_1" value="1" autocomplete="off" checked> Single
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mt-body-actions-icons">
                                <div class="btn-group btn-group btn-group-justified">
                                    <input type="text" name="meter_1" class="form-control"  tabindex="3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-8">
                        <div class="mt-widget-3">
                            <div class="mt-head bg-white">
                                <img src="../img/meter-bw.png" width="100" style=" display:block;margin:auto;">
                                <div class="mt-head-button">
                                    <div class="mt-action-buttons ">

                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn red btn-outline">
                                                <input type="radio" name="ph2" id="ph2_3" value="3" autocomplete="off"> Three
                                            </label>
                                            <label class="btn blue btn-outline active">
                                                <input type="radio" name="ph2" id="ph2_1" value="1" autocomplete="off" checked> Single
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mt-body-actions-icons">
                                <div class="btn-group btn-group btn-group-justified">
                                    <input type="text" name="meter_2" class="form-control" tabindex="4">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="mt-widget-3">
                            <div class="mt-head bg-white">
                                <img src="../img/meter-bw.png" width="100" style=" display:block;margin:auto;">
                                <div class="mt-head-button">
                                    <div class="mt-action-buttons ">

                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn red btn-outline">
                                                <input type="radio" name="ph3" id="ph3_3" value="3" autocomplete="off"> Three
                                            </label>
                                            <label class="btn blue btn-outline active">
                                                <input type="radio" name="ph3" id="ph3_1" value="1" autocomplete="off" checked> Single
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mt-body-actions-icons">
                                <div class="btn-group btn-group btn-group-justified">
                                    <input type="text" name="meter_3" class="form-control" tabindex="5">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="mt-widget-3">
                            <div class="mt-head bg-white">
                                <img src="../img/meter-bw.png" width="100" style=" display:block;margin:auto;">
                                <div class="mt-head-button">
                                    <div class="mt-action-buttons ">

                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn red btn-outline">
                                                <input type="radio" name="ph4" id="ph4_3" value="3" autocomplete="off"> Three
                                            </label>
                                            <label class="btn blue btn-outline active">
                                                <input type="radio" name="ph4" id="ph4_1" value="1" autocomplete="off" checked> Single
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mt-body-actions-icons">
                                <div class="btn-group btn-group btn-group-justified">
                                    <input type="text" name="meter_4" class="form-control" tabindex="6">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="mt-widget-3">
                            <div class="mt-head bg-white">
                                <img src="../img/meter-bw.png" width="100" style=" display:block;margin:auto;">
                                <div class="mt-head-button">
                                    <div class="mt-action-buttons ">

                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn red btn-outline">
                                                <input type="radio" name="ph5" id="ph5_3" value="3" autocomplete="off"> Three
                                            </label>
                                            <label class="btn blue btn-outline active">
                                                <input type="radio" name="ph5" id="ph5_1" value="1" autocomplete="off" checked> Single
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mt-body-actions-icons">
                                <div class="btn-group btn-group btn-group-justified">
                                    <input type="text" name="meter_5" class="form-control" tabindex="7">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="mt-widget-3">
                            <div class="mt-head bg-white">
                                <img src="../img/meter-bw.png" width="100" style=" display:block;margin:auto;">
                                <div class="mt-head-button">
                                    <div class="mt-action-buttons ">

                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn red btn-outline">
                                                <input type="radio" name="ph6" id="ph6_3" value="3" autocomplete="off"> Three
                                            </label>
                                            <label class="btn blue btn-outline active">
                                                <input type="radio" name="ph6" id="ph6_1" value="1" autocomplete="off" checked> Single
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mt-body-actions-icons">
                                <div class="btn-group btn-group btn-group-justified">
                                    <input type="text" name="meter_6" class="form-control" tabindex="8">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="mt-widget-3">
                            <div class="mt-head bg-white">
                                <img src="../img/meter-bw.png" width="100" style=" display:block;margin:auto;">
                                <div class="mt-head-button">
                                    <div class="mt-action-buttons ">

                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn red btn-outline">
                                                <input type="radio" name="ph7" id="ph7_3" value="3" autocomplete="off"> Three
                                            </label>
                                            <label class="btn blue btn-outline active">
                                                <input type="radio" name="ph7" id="ph7_1" value="1" autocomplete="off" checked> Single
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mt-body-actions-icons">
                                <div class="btn-group btn-group btn-group-justified">
                                    <input type="text" name="meter_7" class="form-control" tabindex="9">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="mt-widget-3">
                            <div class="mt-head bg-white">
                                <img src="../img/meter-bw.png" width="100" style=" display:block;margin:auto;">
                                <div class="mt-head-button">
                                    <div class="mt-action-buttons ">

                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn red btn-outline">
                                                <input type="radio" name="ph8" id="ph8_3" value="3" autocomplete="off"> Three
                                            </label>
                                            <label class="btn blue btn-outline active">
                                                <input type="radio" name="ph8" id="ph8_1" value="1" autocomplete="off" checked> Single
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="mt-body-actions-icons">
                                <div class="btn-group btn-group btn-group-justified">
                                    <input type="text" name="meter_8" class="form-control" tabindex="10">
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-lg btn-primary pull-right" >Save</button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
$(function() {
    //set focus to next element on enter
    $("input[tabindex]").each(function () {
        $(this).on("keypress", function (e) {
            if( event.keyCode == 13 || event.keyCode == 17 || event.keyCode == 74 ){
                e.preventDefault();
            }

            if (e.keyCode === 13)
            {
                var nextElement = $('[tabindex="' + (this.tabIndex + 1) + '"]');
                if (nextElement.length) {
                    $('[tabindex="' + (this.tabIndex + 1) + '"]').focus();
                    e.preventDefault();
                } else{
                    $('[tabindex="1"]').focus();
                }
                return false;
            }
        });
    });
});


</script>
<?php
//include '../include/footer.php';
?>
<script src="js/survey.js"></script>
<link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
