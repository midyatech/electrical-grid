<?php
//devexpress charts, dx charts
class Chart
{
    //color palettes
    var $palettes = array ('Default', 'Soft Pastel', 'Harmony Light', 'Pastel', 'Bright', 'Soft', 'Ocean', 'Vintage', 'Violet');

    function BarChart($report_data, $series, $argumentField, $chartId, $title="", $subtitle="", $type="bar", $options=null){
        $data = json_encode($report_data, JSON_NUMERIC_CHECK);
        ?>
        <script>
        $(function() {
            $("#<?php echo $chartId;?>").dxChart({
                palette: 'Soft Pastel',
                dataSource: <?php echo $data;?>,
                commonSeriesSettings: {
                    argumentField: "<?php echo $argumentField;?>",
                    type: "<?php echo $type;?>",
                    hoverMode: "allArgumentPoints",
                    selectionMode: "allArgumentPoints",
                    <?php
                    if(isset($options["label"]) && $options["label"] == true){
                    ?>
                    label: {
                        visible: true,
                        format: {
                            type: "fixedPoint",
                            precision: 0,
                        }
                    }
                    <?php } ?>
                },
                <?php if(isset($options["crosshair"])){?>
                   crosshair: {
                        enabled: true,
                        color: "#FFC0CB",
                        width: 1,
                        dashStyle:"dot",
                        label: {
                            visible: true,
                            backgroundColor: "#FFB7C5",
                            font: {
                            color: "#fff",
                            size: 12,
                            }
                        }
                    },
                <?php }?>
                size: {
                    <?php if(isset($options["height"])){
                        echo 'height: '.$options["height"];
                    }
                    ?>
                },
                commonAxisSettings: {
                    label: {
                        alignment: 'center',
                        overlappingBehavior: '<?php if(isset($options["overlappingBehavior"]) && $options["overlappingBehavior"] != '') echo $options["overlappingBehavior"]; else echo "stagger"; ?>',
                        rotationAngle: 45
                    },
                },
                margin: {
                    bottom: 20
                },
                argumentAxis: {
                    valueMarginsEnabled: false,
                    discreteAxisDivisionMode: "crossLabels",
                    grid: {
                        visible: true
                    }
                },
                valueAxis: [
                    {
                        grid: {
                            visible: true
                        },
                        // title: {
                        //     text: "Total Population, billions"
                        // },
                        label: {
                            format: "fixedPoint"
                        }
                    }
                ],
                series: [
                    <?php
                    if($type =="line"){
                        $hoverStr = " hoverStyle: {width: 5}, ";
                    }else{
                        $hoverStr =  "";
                    }
                    for($i=0; $i<count($series); $i++){
                        // if(isset($series[$i]["stack"])){
                        //     $stackStr = ', stack: '.$series[$i]["stack"];
                        // }
                        $stackStr = "";
                        echo "{ valueField: '".$series[$i]["valueField"]."', name: '".$series[$i]["name"]."', $stackStr $hoverStr },";
                    }

                    if($secondary_axis != null){
                        echo "{ ";
                        echo "axis: '".$secondary_axes["axis"]."',";
                        echo "type: '".$secondary_axes["type"]."',";
                        echo "valueField: '".$secondary_axes["valueField"]."',";
                        echo "name: '".$secondary_axes["name"]."',";
                        echo "color: '".$secondary_axes["color"]."'";
                        echo "}";
                    }
                    ?>
                ],
                legend: {
                    verticalAlignment: "bottom",
                    horizontalAlignment: "center",
                    itemTextPosition: "bottom"
                },
                title: {
                    text: "<?php echo $title; ?>",
                    subtitle: {
                        text: "<?php echo $subtitle; ?>"
                    }
                },
                "export": {
                    enabled: true
                },
                // tooltip: {
                //     enabled: true,
                //     location: "edge",
                //     customizeTooltip: function (arg) {
                //         return {
                //             text: arg.seriesName + " years: " + arg.valueText
                //         };
                //     }
                // }
                tooltip: {
                    location: 'edge',
                    enabled: true,
                    customizeTooltip: function (arg) {
                        return {
                            <?php
                            //text: arg.argumentText + " <br /> "+ arg.point.series.name + "  \n"+ "[ <b>"+ arg.valueText + "</b> ]"
                            //text: this.argumentText
                            if($type == "stackedBar"){
                                echo 'text: arg.seriesName + ": " + arg.valueText';
                            }else{
                                echo 'text: this.argumentText';
                            }
                            ?>
                        };
                    }
                }
            });
        });
        </script>
        <?php
    }

    function PieChart($report_data, $series, $argumentField, $chartId, $title="", $subtitle="", $type = "pie"){
        $data = json_encode($report_data, JSON_NUMERIC_CHECK);
        ?>
        <script>
        $(function() {
            $("#<?php echo $chartId;?>").dxPieChart({
                palette: 'Soft Pastel',
                dataSource: <?php echo $data;?>,
                commonSeriesSettings: {
                    hoverMode: "allArgumentPoints",
                    selectionMode: "allArgumentPoints",
                    type: "<?php echo $type;?>",
                    label: {
                        visible: true,
                        format: {
                            type: "fixedPoint",
                            precision: 0
                        }
                    }
                },
                margin: {
                    bottom: 20
                },
                argumentAxis: {
                    valueMarginsEnabled: false,
                    discreteAxisDivisionMode: "crossLabels",
                    grid: {
                        visible: true
                    }
                },
                series: [
                    <?php
                    for($i=0; $i<count($series); $i++){
                        echo "{ valueField: '".$series[$i]["valueField"]."', name: '".$series[$i]["name"]."', argumentField: '".$argumentField."', ";
                        ?>
                            label: {
                                visible: true,
                                format: "fixedPoint",
                                // customizeText: function (point) {
                                //     return point.argumentText + ": " + point.valueText ;
                                // },
                                customizeText: function(arg) {
                                    return arg.valueText + " ( " + arg.percentText + " ) \n"+ arg.argumentText;
                                },
                                connector: {
                                    visible: true,
                                    width: 1
                                }
                            }
                        <?php
                        echo "},";
                    }
                    ?>
                ],
                legend: {
                    visible: true,
                    //orientation: "horizontal",
                    horizontalAlignment: "center",
                    verticalAlignment: "bottom",
                    columnCount: 4,
                    itemTextPosition: "left",
                },
                title: {
                    text: "<?php echo $title; ?>",
                    subtitle: {
                        text: "<?php echo $subtitle; ?>"
                    }
                },
                "export": {
                    enabled: true
                },
                tooltip: {
                    enabled: true,
                    customizeTooltip: function (arg) {
                        return {
                            //text: arg.argumentText + " <br /> "+ arg.point.series.name + "  \n"+ "[ <b>"+ arg.valueText + "</b> ]"
                            //text: this.argumentText + "<br>" + this.seriesName + ": <b>" + this.valueText +"</b> <br> ( "+ arg.percentText +" )"
                            text:  this.argumentText
                        };
                    }
                }
            });
        });
        </script>
        <?php
    }
}
 ?>
