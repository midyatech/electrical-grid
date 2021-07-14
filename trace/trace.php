<?php
include_once realpath(__DIR__ . '/../..').'/include/checksession.php';
//include_once realpath(__DIR__ . '/../..').'/include/checkpermission.php';
require_once realpath(__DIR__ . '/../..').'/class/HtmlHelper.php';
require_once realpath(__DIR__ . '/../..').'/class/SupplyChain.class.php';
require_once realpath(__DIR__ . '/../..').'/class/Dictionary.php';
//require_once realpath(__DIR__ . '/../..') . '/class/User.php';

$dictionary= new Dictionary("ENGLISH");
$html = new HTML('ENGLISH');
?>
<style>
    .timeline-badge {
        width: 70px;
        height: 70px;
        background-color: #ccc;
        border-radius: 50%!important;
        z-index: 5;
        border: 0;
        overflow: hidden;
        position: relative;
    }
    .bg-font-red {
        color: #fff!important;
    }
    .border-after-grey-steel:after, .border-before-grey-steel:before, .border-grey-steel {
        border-color: #e9edef!important;
    }
    .mt-timeline-icon>i {
        top: 50%;
        left: 80%;
        transform: translateY(-50%) translateX(-50%);
        font-size: 24px;
    }
    .timeline-badge i, .timeline-badge i {
        /* top: 1px; */
        position: relative;
    }
    .bg-blue {
        background: #3598dc!important;
    }
    .bg-green-turquoise {
        background: #36D7B7!important;
    }
    .bg-purple-medium {
        background: #BF55EC!important;
    }
    .bg-blue-steel {
        background: #4B77BE!important;
    }
    .bg-green-jungle {
        background: #26C281!important;
    }
    .bg-blue-chambray {
        background: #2C3E50!important;
    }
</style>
<h3>Trace for Item EE6550   </h3>
<div class="timeline">
    <!-- TIMELINE ITEM -->
    <div class="timeline-item">
        <div class="timeline-badge mt-timeline-icon bg-green-jungle  bg-font-red border-grey-steel">
            <i class="icon-arrow-down"></i>
        </div>
        <div class="timeline-body">
            <div class="timeline-body-arrow"> </div>
            <div class="timeline-body-head">
                <div class="timeline-body-head-caption">
                    <span class="timeline-body-alerttitle font-red-intense">Transfer Order #1</span>
                    <span class="timeline-body-time font-grey-cascade">2021-01-23 7:45 AM</span>
                </div>
                <div class="timeline-body-head-actions"> </div>
            </div>
            <div class="timeline-body-content">
                <a href="javascript:;" class="timeline-body-title font-blue-madison">Assembly Warehouse</a>
                <span class="font-grey-cascade">Receive</span>  (By User 1)
            </div>
        </div>
    </div>
    <!-- END TIMELINE ITEM -->

    <!-- TIMELINE ITEM -->
    <div class="timeline-item">
        <div class="timeline-badge mt-timeline-icon bg-blue-steel bg-font-red border-grey-steel">
            <i class="icon-wrench"></i>
        </div>
        <div class="timeline-body">
            <div class="timeline-body-arrow"> </div>
            <div class="timeline-body-head">
                <div class="timeline-body-head-caption">
                    <span class="timeline-body-alerttitle font-red-intense">Transfer Order #2</span>
                    <span class="timeline-body-time font-grey-cascade">2021-01-27 9:00 AM</span>
                </div>
                <div class="timeline-body-head-actions"> </div>
            </div>
            <div class="timeline-body-content">
                <a href="javascript:;" class="timeline-body-title font-blue-madison">Enclosure E6550</a>
                <span class="font-grey-cascade">Assemble</span>  (By User 3)
            </div>
        </div>
    </div>
    <!-- END TIMELINE ITEM -->

    <!-- TIMELINE ITEM -->
    <div class="timeline-item">
        <div class="timeline-badge mt-timeline-icon bg-green-jungle  bg-font-red border-grey-steel">
            <i class="icon-arrow-down"></i>
        </div>
        <div class="timeline-body">
            <div class="timeline-body-arrow"> </div>
            <div class="timeline-body-head">
                <div class="timeline-body-head-caption">
                    <span class="timeline-body-alerttitle font-red-intense">Transfer Order #3</span>
                    <span class="timeline-body-time font-grey-cascade">2021-02-01 11:15 AM</span>
                </div>
                <div class="timeline-body-head-actions"> </div>
            </div>
            <div class="timeline-body-content">
                <a href="javascript:;" class="timeline-body-title font-blue-madison">Installation Warehouse</a>
                <span class="font-grey-cascade">Receive</span>  (By User 3)
            </div>
        </div>
    </div>
    <!-- END TIMELINE ITEM -->

    <!-- TIMELINE ITEM -->
    <div class="timeline-item">
        <div class="timeline-badge mt-timeline-icon bg-red  bg-font-red border-grey-steel">
            <i class="icon-action-undo"></i>
        </div>
        <div class="timeline-body">
            <div class="timeline-body-arrow"> </div>
            <div class="timeline-body-head">
                <div class="timeline-body-head-caption">
                    <span class="timeline-body-alerttitle font-red-intense">Transfer Order #4</span>
                    <span class="timeline-body-time font-grey-cascade">2021-02-01 11:15 AM</span>
                </div>
                <div class="timeline-body-head-actions"> </div>
            </div>
            <div class="timeline-body-content">
                <a href="javascript:;" class="timeline-body-title font-blue-madison">Assemble Warehouse</a>
                <span class="font-grey-cascade">Return</span> (By User 4)
            </div>
        </div>
    </div>
    <!-- END TIMELINE ITEM -->

    <!-- TIMELINE ITEM -->
    <div class="timeline-item">
        <div class="timeline-badge mt-timeline-icon bg-green-jungle  bg-font-red border-grey-steel">
            <i class="icon-arrow-down"></i>
        </div>
        <div class="timeline-body">
            <div class="timeline-body-arrow"> </div>
            <div class="timeline-body-head">
                <div class="timeline-body-head-caption">
                    <span class="timeline-body-alerttitle font-red-intense">Transfer Order #5</span>
                    <span class="timeline-body-time font-grey-cascade">2021-02-01 11:15 AM</span>
                </div>
                <div class="timeline-body-head-actions"> </div>
            </div>
            <div class="timeline-body-content">
                <a href="javascript:;" class="timeline-body-title font-blue-madison">Installation Warehouse</a>
                <span class="font-grey-cascade">Receive</span>  (By User 3)
            </div>
        </div>
    </div>
    <!-- END TIMELINE ITEM -->

    <!-- TIMELINE ITEM -->
    <div class="timeline-item">
        <div class="timeline-badge mt-timeline-icon bg-blue-chambray bg-font-red border-grey-steel">
            <i class="icon-home"></i>
        </div>
        <div class="timeline-body">
            <div class="timeline-body-arrow"> </div>
            <div class="timeline-body-head">
                <div class="timeline-body-head-caption">
                    <span class="timeline-body-alerttitle font-red-intense">Transfer Order #6</span>
                    <span class="timeline-body-time font-grey-cascade">2021-01-23 7:45 AM</span>
                </div>
                <div class="timeline-body-head-actions"> </div>
            </div>
            <div class="timeline-body-content">
                <a href="javascript:;" class="timeline-body-title font-blue-madison">Point #4298</a>
                <span class="font-grey-cascade">Install</span>  (By User 5)
            </div>
        </div>
    </div>
    <!-- END TIMELINE ITEM -->
</div>