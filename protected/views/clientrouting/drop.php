<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Client Routing', 'url' => '#'),
                array('label'=>'Drop')
            );
    
    $baseUrl = Yii::app()->request->baseUrl;

    $routeInfo = $model->details[0];
    $details = $model->details;
    $deliveryDate = CTimestamp::formatDate("d/m/Y",CDateTimeParser::parse($routeInfo->DeliveryDate, "yyyy-MM-dd"))
?>
<style>
    .data-col
    {
        text-align: center;
    }
    
    .data-col input
    {
        width: 90%;
        text-align: center;
    }
</style>

<h1><?php echo $routeInfo->ClientName.' ('.$routeInfo->PrintCentreName.') - '.$deliveryDate.' - '.$routeInfo->RouteId.'<br/>Wholesaler: '.$routeInfo->WholesalerAlias;  ?></h1>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'instance-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php
    echo $form->hiddenField($model, 'routeInstanceId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    
    $noTitles = count($details);
    if ($details[0]->ClientTitleId != '') :   
    ?>

    <table class="listing fluid vtop">
    <tr>
        <th>Title</th>
        <th>Pagination</th>
        <th>Weight</th>
        <th>Bundle Size</th>
        <th>Quantity</th>
    </tr>
    <?php 
    $titlesIdx = 0;
    foreach($details as $item): 
        $tId = $item->ClientRouteInstanceDropId;
        ?>
    <tr class="row<?php echo (($titlesIdx++ % 2) +1); ?>">
        <td style="white-space: nowrap">
            <span title="<?php echo $item->TitleId; ?>">
                <?php echo $item->TitleName; ?>
            </span>
        </td>
        <td class="data-col">
            <?php echo CHtml::textField("TtData[$tId][Pagination]", $item->PubPagination, array('class' => 'nbr req')); ?>
        </td>
        <td class="data-col">
            <?php echo CHtml::textField("TtData[$tId][Weight]", $item->PubWeight, array('class' => 'nbr req')); ?>
        </td>
        <td class="data-col">
            <?php echo CHtml::textField("TtData[$tId][BundleSize]", $item->BundleSize, array('class' => 'nbr req')); ?>
        </td>
        <td class="data-col">
            <?php echo CHtml::textField("TtData[$tId][Quantity]", $item->Quantity, array('class' => 'nbr req')); ?>
        </td>
        <td>
            <?php if($noTitles > 1): ?>
            <a href="#" class="del-link" rel="<?php echo $tId; ?>">
                <img height="16" width="16" alt="add" src="img/icons/delete.png" /> 
            </a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>
    
    <br/>
    
    <div class="titleWrap">
        <?php echo CHtml::submitButton('Save Entered Data', array('class'=>'formButton btn-submit')); ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png"/>
                <a href="#" onclick="parent.$.colorbox.close(); return false;">Cancel</a>
            </li>
        </ul>

    </div>
    <?php else: // no titles in drop ?>
    <div class="warningBox">
        There are no titles in this drop.
    </div>
    <?php endif; ?>
<?php
$this->endWidget(); ?>
    
    <hr />
    
    
    <fieldset>
        <legend>Add Title</legend>
        
        <div id="formNewTitle">
            
        <?php echo CHtml::hiddenField("NewTitle[DetailsId]", $details[0]->ClientRouteInstanceDetailsId); ?>
            
        <table class="listing fluid vtop">
        <tr>
            <th>Title</th>
            <th>Pagination</th>
            <th>Weight</th>
            <th>Bundle Size</th>
            <th>Quantity</th>
        </tr>
        <tr>
            <td>
                <?php 
                $titleInfo = $model->getTitleOptions();
                echo CHtml::dropDownList("NewTitle[Id]", '', $titleInfo['options'], array('empty' => '-- new title', 'options' => $titleInfo['attrs'])); 
                ?>
                <div id="divNewTitle">
                    <table>
                        <tr>
                            <td>Code</td>
                            <td>
                                <?php echo CHtml::textField("NewTitle[Code]", "", array('class' => 'ntreq')); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>
                                <?php echo CHtml::textField("NewTitle[Name]", "", array('class' => 'ntreq')); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>
                                <?php 
                                echo CHtml::dropDownList("NewTitle[Type]", '', array(
                                    'M' => 'Supplement/Magazine',
                                    'S' => 'Standard',
                                ));
                                //echo CHtml::dropDownList("NewTitle[Type]", '', $expressTitle->getOptionsExpressTitles());
                                
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td class="data-col">
                <?php echo CHtml::textField("NewTitle[Pagination]", "", array('class' => 'ntreq nbr')); ?>
            </td>
            <td class="data-col">
                <?php echo CHtml::textField("NewTitle[Weight]", "", array('class' => 'ntreq nbr')); ?>
            </td>
            <td class="data-col">
                <?php echo CHtml::textField("NewTitle[BundleSize]", "", array('class' => 'ntreq nbr')); ?>
            </td>
            <td class="data-col">
                <?php echo CHtml::textField("NewTitle[Quantity]", "", array('class' => 'ntreq nbr')); ?>
            </td>
        </tr>
        </table>
        </div>
        
        <br/>
        
        <button id="btnAddTitle">
            <img height="16" width="16" alt="add" src="img/icons/add.png"/> Add Title
        </button>
    </fieldset>
</div>

<script>
$(function(){
    $("#NewTitle_Id").change(function(){
        if ($(this).val() == '')
            $("#divNewTitle").show();
        else
            $("#divNewTitle").hide();
        
        // default pagination/bundle/weight
        var $opt = $('option:selected', $(this));
        $("#NewTitle_Pagination").val(($opt.attr('pagination') != undefined) ? $opt.attr('pagination') : "");
        $("#NewTitle_Weight").val(($opt.attr('weight') != undefined) ? $opt.attr('weight') : "");
        $("#NewTitle_BundleSize").val(($opt.attr('bundle') != undefined) ? $opt.attr('bundle') : "");
    });  
    
    $("#btnAddTitle").click(function(){
        var $btn = $(this);
        // validate
        if ($("#NewTitle_Id").val() != "")
        {
            $("#NewTitle_Code").removeClass("ntreq");
            $("#NewTitle_Name").removeClass("ntreq");
        }
        var bError = false;
        var toFocus = null;
        var msg = "";
        $("#formNewTitle .nbr").each(function(){
            if (!bError){
                var val = $.trim($(this).val());
                bError = (val != "") && (parseInt(val,10) != val);
                toFocus = (bError) ? $(this) : null;
                msg = (bError) ? "Only numeric values are allowed." : "";
            }
        });
        $("#formNewTitle .ntreq").each(function(){
            if (!bError){
                var val = $.trim($(this).val());
                bError = (val == "");
                toFocus = (bError) ? $(this) : null;
                msg = (bError) ? "A value is required." : "";
            }
        });
        if (bError)
        {
            alert(msg);
            toFocus.select();
            return false;
        }        
        
        var $inputs = $("#formNewTitle :input")
        var data = new Array();
        $inputs.each(function(){
            data.push($(this).attr('name') + "=" + $(this).val())
        })
        data = data.join("&");
        
         $.post("<?php echo $this->createUrl('clientrouting/droptitle') ?>",
                data,
                function(data){
                    if (data.error == 0)
                    {
                        $btn.html("Adding...");
                        location.reload();
                    }
                    else
                        alert("An error has occurred.");
                },
                'json');
        
        return false;
    });
    
    $(".del-link").click(function(){
        if (confirm("Are you sure you wish to delete this title from this drop?\nThis operation cannot be undone."))
        {
            $.post("<?php echo $this->createUrl('clientrouting/droptitledelete') ?>",
                "dropid="+$(this).attr("rel"),
                function(data){
                    if (data.error == 0)
                    {
                        location.reload();
                    }
                    else
                        alert("An error has occurred.");
                },
                'json');
        }
        return false;
    });
    
    $(".btn-submit").click(function(){
        var bError = false;
        var toFocus = null;
        var msg = "";
        $("#instance-form .nbr").each(function(){
            if (!bError){
                var val = $.trim($(this).val());
                bError = (val != "") && (parseInt(val,10) != val);
                toFocus = (bError) ? $(this) : null;
                msg = (bError) ? "Only numeric values are allowed." : "";
            }
        });
        $("#instance-form .req").each(function(){
            if (!bError){
                var val = $.trim($(this).val());
                bError = (val == "");
                toFocus = (bError) ? $(this) : null;
                msg = (bError) ? "A value is required." : "";
            }
        });
        
        if (bError)
        {
            alert(msg);
            toFocus.select();
            return false;
        }
    });
})
</script>