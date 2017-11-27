<?php
    if($model->printCentres === null) {
    }
    else if(count($model->printCentres) == 0) {
        echo '<div class="warningBox">There is no information on the system for the criteria provided.</div>';
    }
    else {
        foreach($model->printCentres as $printCentre) {
            $delivered = $printCentre['totals']->PalletsDelivered;
            $collected = $printCentre['totals']->PalletsCollected;
            $balance = $delivered - $collected;
            echo "<h3>Print Centre: $printCentre[printCentreName] (Delivered: $delivered, " .
                 " Returned: $collected, Balance: $balance)</h3>";
            $columns = array();
            if($model->reportType == 'pc') {
                $columns[] = 'DTDate:text:Date';
            }
            $columns[] = 'SupplierName:text:Supplier';
            $columns[] = 'PalletsDelivered:text:Total Out';
            $columns[] = 'PalletsCollected:text:Total Returned';
            $columns[] = array('header' => 'Outstanding', 'type' => 'raw',
                'value' => '$data->PalletsDelivered - $data->PalletsCollected',
            );
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => $printCentre['printCentreDataProvider'],
                'columns' => $columns,
                ));
            if($model->reportType == 'mr') {
                ?>
                <table width="100%">
                    <tr>
                        <td style="width: 40px">
                        </td>
                        <td>
                            <?php
                                foreach($printCentre['suppliers'] as $supplier) {
                                    echo '<h4>Supplier: '  . $supplier['supplierName'] . ', Print Centre: ' . $printCentre['printCentreName'] . '</h4>';
                                    $this->widget('zii.widgets.grid.CGridView', array(
                                        'dataProvider' => $supplier['supplierDataProvider'],
                                        'columns' => array(
                                            'DeliveryPointName:text:Wholesaler',
                                            'PalletsDelivered:text:Total Delivered',
                                            'PalletsCollected:text:Total Returned',
                                            array('header' => 'Outstanding', 'type' => 'raw',
                                                  'value' => '$data->PalletsDelivered - $data->PalletsCollected',),
                                           ),
                                        'selectableRows'=>1,
                                        'selectionChanged'=>'function(id){ tmp = parseInt($.fn.yiiGridView.getSelection(id)); if(isNaN(tmp)) return; ' .
                                        ' $("#AdminReportingPallet_reportType3").attr("checked", true); ' .
                                        ' $("#AdminReportingPallet_supplier").attr("disabled", "disabled"); ' .
                                        ' $("#AdminReportingPallet_deliveryPoint").removeAttr("disabled"); ' .
                                        ' $("#AdminReportingPallet_deliveryPoint").val(tmp); ' .
                                        ' $("#btnSubmit").click(); } ',
                                        'htmlOptions'=>array('class'=>'grid-view mgrid_table',),
                                    ));
                                }
                            ?>
                        </td>
                    </tr>
                </table>
                <?php
            }
        }
    }
?>