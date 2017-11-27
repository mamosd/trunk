<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Finance'),
                array('label'=>'Contractor'),
            );
    $baseUrl = Yii::app()->request->baseUrl;
?>

<?php if (isset($model->contractorId)) : ?>
<h1>Edit Contractor</h1>
<?php else : ?>
<h1>Add new Contractor</h1>
<?php endif; ?>

<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'contractor-form',
            'errorMessageCssClass'=>'formError',
    )); ?>

    <?php 
    echo $form->hiddenField($model, 'contractorId');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    
    <div id="tabs">
        <ul>
            <li>
                <a href="#tab-general">General</a>
            </li>
            <li>
                <a href="#tab-contact">Contact Details</a>
            </li>
            <li>
                <a href="#tab-vehicle">Vehicles</a>
            </li>
            <li>
                <a href="#tab-id">Id</a>
            </li>
            <li>
                <a href="#tab-banking">Banking</a>
            </li>
            <?php if (isset($model->contractorId)) : ?>
            <li>
                <a href="#tab-documents">Documents</a>
            </li>
            <?php endif; ?>
        </ul>
        
        <div id="tab-general">
            
            <div>
                <?php echo $form->labelEx($model,'code'); ?>
                <?php echo $form->textField($model,'code', array('size'=>'10')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($model,'firstName'); ?>
                <?php echo $form->textField($model,'firstName', array('size'=>'50')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($model,'lastName'); ?>
                <?php echo $form->textField($model,'lastName', array('size'=>'50')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($model,'accountNumber'); ?>
                <?php echo $form->textField($model,'accountNumber', array('size'=>'20')); ?>
            </div>

                      
            
            <div>
                <?php echo $form->labelEx($model,'division'); ?>
                <?php echo $form->dropDownList($model, 'division', $model->getDivisionOptions(), array('empty'=>'select one ->')); ?>
            </div>
            
            <div>
                <?php echo $form->labelEx($model,'type'); ?>
                <?php echo $form->dropDownList($model, 'type', $model->getTypeOptions(), array('empty'=>'select one ->')); ?>
            </div>

            <div>
                <?php echo $form->labelEx($model,'tax'); ?>
                <?php echo $form->dropDownList($model, 'tax', $model->getTaxOptions(), array('empty'=>'select one ->')); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'vatNo'); ?>
                <?php echo $form->textField($model,'vatNo', array('size'=>'20')); ?>
            </div>

            

            <div>
                <?php echo $form->labelEx($model,'parentContractor'); ?>
                <?php if (!$model->hasChildren())
                        echo $form->dropDownList($model, 'parentContractor', $model->getParentContractorOptions(), array('empty'=>'select one ->')); 
                      else
                        echo "<em>This contractor is already defined as parent.</em>";  
                          ?>
            </div>

            <div>
                <?php echo $form->labelEx($model,'isLive'); ?>
                <?php echo $form->checkBox($model,'isLive'); ?>
            </div>
            
            <div>
                <?php echo $form->labelEx($model,'contractStartDate'); ?>
                <?php echo $form->textField($model,'contractStartDate', array('size'=>'12', 'class' => 'dpicker')); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'contractFinishDate'); ?>
                <?php echo $form->textField($model,'contractFinishDate', array('size'=>'12', 'class' => 'dpicker')); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'passIssueDate'); ?>
                <?php echo $form->textField($model,'passIssueDate', array('size'=>'12', 'class' => 'dpicker')); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'passCancelDate'); ?>
                <?php echo $form->textField($model,'passCancelDate', array('size'=>'12', 'class' => 'dpicker')); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'passCancelBy'); ?>
                <?php echo $form->textField($model,'passCancelBy', array('size'=>'20')); ?>
            </div>
            
            
        </div>
        
        <div id="tab-contact">
            <div>
                <?php echo $form->labelEx($model,'email'); ?>
                <?php echo $form->textField($model,'email', array('size'=>'50')); ?>
            </div>  
            
            <div>
                <?php echo $form->labelEx($model,'telephone'); ?>
                <?php echo $form->textField($model,'telephone', array('size'=>'25')); ?>
            </div>
            
            <div>
                <?php echo $form->labelEx($model,'emergencyContactNumber'); ?>
                <?php echo $form->textField($model,'emergencyContactNumber', array('size'=>'15')); ?>
            </div>
            
            <fieldset>
                <legend>Address</legend>
                
                <div>
                    <?php echo $form->labelEx($model,'addressLine1'); ?>
                    <?php echo $form->textField($model,'addressLine1', array('size'=>'50')); ?>
                </div> 
                
                <div>
                    <?php echo $form->labelEx($model,'addressLine2'); ?>
                    <?php echo $form->textField($model,'addressLine2', array('size'=>'50')); ?>
                </div> 
                
                <div>
                    <?php echo $form->labelEx($model,'addressLine3'); ?>
                    <?php echo $form->textField($model,'addressLine3', array('size'=>'50')); ?>
                </div> 
                
                <div>
                    <?php echo $form->labelEx($model,'town'); ?>
                    <?php echo $form->textField($model,'town', array('size'=>'25')); ?>
                </div> 
                
                <div>
                    <?php echo $form->labelEx($model,'county'); ?>
                    <?php echo $form->textField($model,'county', array('size'=>'25')); ?>
                </div> 
                
                <div>
                    <?php echo $form->labelEx($model,'postcode'); ?>
                    <?php echo $form->textField($model,'postcode', array('size'=>'10')); ?>
                </div> 
                
            </fieldset>
        </div>
        
        <div id="tab-vehicle">
            <fieldset>
                <legend>Vehicle #1</legend>
                
                <div>
                    <?php echo $form->labelEx($model,'VIN01'); ?>
                    <?php echo $form->textField($model,'VIN01', array('size'=>'15')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'MOT01'); ?>
                    <?php echo $form->textField($model,'MOT01', array('size'=>'12', 'class' => 'dpicker')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'INS01'); ?>
                    <?php echo $form->textField($model,'INS01', array('size'=>'12', 'class' => 'dpicker')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'CMM01'); ?>
                    <?php echo $form->textField($model,'CMM01', array('size'=>'50')); ?>
                </div>
            </fieldset>
            
            <fieldset>
                <legend>Vehicle #2</legend>
                
                <div>
                    <?php echo $form->labelEx($model,'VIN02'); ?>
                    <?php echo $form->textField($model,'VIN02', array('size'=>'15')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'MOT02'); ?>
                    <?php echo $form->textField($model,'MOT02', array('size'=>'12', 'class' => 'dpicker')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'INS02'); ?>
                    <?php echo $form->textField($model,'INS02', array('size'=>'12', 'class' => 'dpicker')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'CMM02'); ?>
                    <?php echo $form->textField($model,'CMM02', array('size'=>'50')); ?>
                </div>
            </fieldset>
            
            <fieldset>
                <legend>Vehicle #3</legend>
                
                <div>
                    <?php echo $form->labelEx($model,'VIN03'); ?>
                    <?php echo $form->textField($model,'VIN03', array('size'=>'15')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'MOT03'); ?>
                    <?php echo $form->textField($model,'MOT03', array('size'=>'12', 'class' => 'dpicker')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'INS03'); ?>
                    <?php echo $form->textField($model,'INS03', array('size'=>'12', 'class' => 'dpicker')); ?>
                </div>
                
                <div>
                    <?php echo $form->labelEx($model,'CMM03'); ?>
                    <?php echo $form->textField($model,'CMM03', array('size'=>'50')); ?>
                </div>
            </fieldset>
        </div>
        
        <div id="tab-id">
            <div>
                <?php echo $form->labelEx($model,'immigrationStatus'); ?>
                <?php echo $form->textField($model,'immigrationStatus', array('size'=>'25')); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'idType'); ?>
                <?php echo $form->textField($model,'idType', array('size'=>'15')); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'idNumber'); ?>
                <?php echo $form->textField($model,'idNumber', array('size'=>'10')); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'idExpiryDate'); ?>
                <?php echo $form->textField($model,'idExpiryDate', array('size'=>'12', 'class' => 'dpicker')); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'nationalInsuranceNumber'); ?>
                <?php echo $form->textField($model,'nationalInsuranceNumber', array('size'=>'15')); ?>
            </div>
            
        </div>
        
        <div id="tab-banking">
            
            <div>
                <?php echo $form->labelEx($model,'bankName'); ?>
                <?php echo $form->textField($model,'bankName', array('size'=>'20')); ?>
            </div>
            
            <div>
                <?php echo $form->labelEx($model,'bankAccountNumber'); ?>
                <?php echo $form->textField($model,'bankSortCode', array('size'=>'7')); ?> /
                <?php echo $form->textField($model,'bankAccountNumber', array('size'=>'9')); ?>
            </div>
            
        </div>
        
        <?php if (isset($model->contractorId)) : ?>
        <div id="tab-documents">
            
            <?php if (Login::checkPermission(Permission::PERM__FUN__LSC__CONTRACTOR_EDIT)): ?>
            <fieldset>
                <br/>               
            <?php $this->widget('ext.EFineUploader.EFineUploader',
            array(
                  'id'=>'FineUploader',
                  'config'=>array(
                                  'autoUpload'=>true,
                                  'text' => array(
                                      'uploadButton' => 'Upload a Document'
                                  ),
                                  'request'=>array(
                                     'endpoint'=> $this->createUrl('finance/contractordocument', array('contractorId' => $model->contractorId)),
                                     'params'=>array('YII_CSRF_TOKEN'=>Yii::app()->request->csrfToken),
                                      ),
                                  'retry'=>array('enableAuto'=>true,'preventRetryResponseProperty'=>true),
                                  'chunking'=>array('enable'=>true,'partSize'=>100),//bytes
                                  'callbacks'=>array(
                                                   'onComplete'=>"js:function(id, name, response){ location.reload(); }",
                                                   'onError'=>"js:function(id, name, errorReason){ alert('Error: '+errorReason); }",
                                                    ),
                                  'validation'=>array(
                                            'allowedExtensions'=>array('pdf','tif','tiff','jpg'),
                                            'sizeLimit' => 10 * 1024 * 1024,//maximum file size in bytes
//                                            'minSizeLimit'=>2*1024*1024,// minimum file size in bytes
                                                     ),
                                 )
                 ));
           ?>
            </fieldset>
            <?php endif; ?>
            
            <?php $docs = $docsModel->getDocumentList(); 
            if (!empty($docs)) :
            ?>
            <h2>Documents</h2>
            <table class="listing fluid">
                <tr>
                    <th>Uploaded By</th>
                    <th>Date Uploaded</th>
                    <th>Document</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($docs as $doc): ?>
                <tr>
                    <td>
                        <?php echo $doc->uploadedBy->FriendlyName; ?>
                    </td>
                    <td>
                        <?php echo $doc->UploadedDate; ?>
                    </td>
                    <td>
                        <?php 
                            $aname = explode('/', $doc->FileName);
                            $name = array_pop($aname); ?>
                        <a href="<?php echo $baseUrl.$doc->FileName; ?>" target="_blank"><?php echo $name; ?></a>
                    </td>
                    <td>
                        <a href="#" class="disableDocument" rel="<?php echo $doc->Id; ?>">
                    <img src="<?php echo $baseUrl; ?>/img/icons/delete.png" title="Delete document" />
                </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
            <div class="infoBox" >
                There are no documents uploaded for this contractor.
            </div>
            <?php endif; ?>
                        
        </div>
        <?php endif; ?>
    </div>    
    
    
    
    

    <div class="titleWrap">
        <?php if (Login::checkPermission(Permission::PERM__FUN__LSC__CONTRACTOR_EDIT))
        //echo CHtml::submitButton('Submit', array('class'=>'formButton')); 
        echo CHtml::submitButton('Save', array('class'=>'formButton', 'name'=>'save'));
        echo "&nbsp;&nbsp;&nbsp;";
        echo CHtml::submitButton('Save and Exit', array('class'=>'formButton', 'name'=>'saveandexit'));
        ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('finance/contractors');?>">Cancel</a>
            </li>
        </ul>

    </div>


<?php
$this->endWidget(); ?>
</div>

<p id="demo"></p>

<script>
$(function(){
    
    $( "#tabs" ).tabs({
        select: function( event, ui ) {
            window.location.hash = ui.tab.hash;
        }
    }).tabs("select",window.location.hash);
    
    $(".dpicker").datepicker({
        //dateFormat: 'dd/mm/yy',
        dateFormat: 'yy-mm-dd',
        onSelect: function() {
            $(this).removeClass("error");
            $(this).change();
        }
//        beforeShowDay: function(date){ return [date.getDay() == 1,""]},
//        maxDate: '7'
    });
    
    $("#btnUploadDocs").click(function(){
        $("#contractor-docs-form").submit();
        return false;
    });
    
    $(".disableDocument").click(function(){
        if (confirm("Please confirm you wish to DELETE the selected Documents."))
        $.post("<?php echo $this->createUrl("finance/disabledocument") ?>",
            {id : $(this).attr('rel')},
            function(data){
                if (data.result == 'fail')
                    alert('Error : ' + data.error);
                else
                    location.reload();
            },
            'json'
            );
    
        return false;
        
        
    });
    
    
});

</script>