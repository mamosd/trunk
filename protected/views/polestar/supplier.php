<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Polestar', 'url' => '#'),
                array('label'=>'Suppliers', 'url' => array('polestar/suppliers')),
                array('label'=>'Supplier'),
            );
    $baseUrl = Yii::app()->request->baseUrl;

    $okImage = $baseUrl . '/img/icons/accept.png';
    $cancelImage = $baseUrl . '/img/icons/cancel.png';

    $cs=Yii::app()->getClientScript();
    $cs->registerScriptFile($baseUrl.'/js/geotools-loader.js',CClientScript::POS_HEAD,array(
        "charset" => "ISO-8859-1"
    ));

?>

<style>
#mainWrap {
    display: table;
}
.listing.fluid tr:nth-child(even) td {background: none repeat scroll 0 0 #fff;}
.listing.fluid tr:nth-child(odd) td {background: none repeat scroll 0 0 #dfdfdf;}
</style>

<?php if (!$model->isNewRecord) : ?>
<h1>Edit Supplier</h1>
<?php else : ?>
<h1>Add new Supplier</h1>
<?php endif; ?>


<div class="standardForm">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'delivery-point-form',
    'errorMessageCssClass'=>'formError',
)); ?>

    <?php
    echo $form->hiddenField($model, 'Id');
    echo $form->errorSummary($model, "", "", array('class'=>'errorBox'));
    ?>

    <div id="tabs">
        <ul>
            <li>
                <a href="#tab-general">General</a>
            </li>
            <li>
                <a href="#tab-address">Address</a>
            </li>

            <?php if (!$model->isNewRecord): ?>
            <li>
                <a href="#tab-documents">Documents</a>
            </li>
            <?php endif; ?>
        </ul>

        <div id="tab-general">
            <div class="field">
                <?php
                echo $form->labelEx($model,'Name', FALSE);
                echo $form->textField($model,'Name', array('size' => 50));
                ?>
            </div>

            <div class="field">
                <?php
                echo $form->labelEx($model,'Code', FALSE);
                echo $form->textField($model,'Code');
                ?>
            </div>

            <div class="field">
                <?php
                echo $form->labelEx($model,'Live');
                echo $form->checkBox($model,'Live');
                ?>
            </div>

            <div class="field">
                <?php
                echo $form->labelEx($model,'VatNumber', FALSE);
                echo $form->textField($model,'VatNumber');
                ?>
            </div>

            <fieldset style="width:50%;">
                <legend>Bank Account Number</legend>

                <div>
                    <?php echo $form->labelEx($model,'BankName'); ?>
                    <?php echo $form->textField($model,'BankName', array('size'=>'20')); ?>
                </div>

                <div>
                    <?php echo $form->labelEx($model,'Bank Account Numer (sort code / account number)'); ?>
                    <?php echo $form->textField($model,'BankSortCode', array('size'=>'7')); ?> /
                    <?php echo $form->textField($model,'BankAccountNumber', array('size'=>'9')); ?>
                </div>
            </fieldset>

            <fieldset style="width:50%;">
                <legend>Account Numbers</legend>
                <?php foreach ($PrintCentres as $printCentre): ?>
                    <div>
                        <?php echo CHtml::checkBox("PrintCentre[{$printCentre->Id}]",in_array($printCentre->Id,$SupplierPrintCentreIds)); ?> <?php echo $printCentre->Name ?> ( <?php echo $printCentre->AccountNumber; ?>)
                    </div>
                <?php endforeach; ?>
            </fieldset>

            <fieldset>
                <legend>Contacts</legend>

                <div class="titleWrap">
                    <ul>
                    <li class="seperator">
                        <img height="16" width="16" alt="add" src="img/icons/add.png">
                            <a id="add_contact" href="javascript:void(0)" title="Add new Contact" class="colorBoxIFrame">Add Contact</a>
                    </li>
                </ul>
                </div>

                <table id="contacts" class="listing fluid" cellpadding="0" cellspacing="0" >
                <tbody>
                    <tr>
                      <th>Day/Night</th>
                      <th>Department</th>
                      <th>Name</th>
                      <th>Surname</th>
                      <th>Telephone No.</th>
                      <th>Ext. No.</th>
                      <th>Mobile No.</th>
                      <th>Email</th>
                      <th>Advice Sheet</th>
                      <th width="1">Actions</th>
                    </tr>
                    <?php foreach($model->Contacts as $contact): ?>
                    <?php if (!in_array($contact->Id, $Contacts)) continue; ?>
                    <tr>
                        <td><span class="type"><?php echo $contact->Type ?></span></td>
                        <td>
                            <?php echo CHtml::hiddenField("Contacts[]",$contact->Id); ?>
                            <span class="dept"><?php echo $contact->Department ?></span>
                        </td>
                        <td><span class="name"><?php echo $contact->Name ?></span></td>
                        <td><span class="surname"><?php echo $contact->Surname ?></span></td>
                        <td><span class="landline"><?php echo $contact->Telephone ?></span></td>
                        <td><span class="ext"><?php echo $contact->ExtensionNo ?></span></td>
                        <td><span class="mobile"><?php echo $contact->Mobile ?></span></td>
                        <td><span class="email"><?php echo $contact->Email ?></span></td>
                        <td>
                            <span class="asemail" style="display: none;"><?php echo $contact->ReceiveAdviceEmails ?></span>
                            <?php echo CHtml::image($contact->ReceiveAdviceEmails?$okImage:$cancelImage) ?>
                        </td>
                        <td style="white-space: nowrap;">
                            <a class="lnkEditContact"
                                href="javascript:void(0)">
                            <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
                            Edit</a>
                            <a class="lnkDeleteContact"
                                href="javascript:void(0)">
                            <img src="img/icons/cancel.png" alt="delete" width="16" height="16" />
                            Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php foreach ($NewContacts as $key => $contact): ?>
                    <tr>
                        <td><?php echo CHtml::dropDownList("NewContacts[{$key}][Type]",$contact['Type'], array('Day' => 'Day', 'Night' => 'Night')); ?></td>
                        <td><?php echo CHtml::textField("NewContacts[{$key}][Department]",$contact['Department'], array('size' => 12)); ?></td>
                        <td><?php echo CHtml::textField("NewContacts[{$key}][Name]",$contact['Name'], array('size' => 20)); ?></td>
                        <td><?php echo CHtml::textField("NewContacts[{$key}][Surname]",$contact['Surname'], array('size' => 20)); ?></td>
                        <td><?php echo CHtml::textField("NewContacts[{$key}][Telephone]",$contact['Telephone'], array('size' => 20)); ?></td>
                        <td><?php echo CHtml::textField("NewContacts[{$key}][ExtensionNo]",$contact['ExtensionNo'], array('size' => 5)); ?></td>
                        <td><?php echo CHtml::textField("NewContacts[{$key}][Mobile]",$contact['Mobile'], array('size' => 20)); ?></td>
                        <td><?php echo CHtml::textField("NewContacts[{$key}][Email]",$contact['Email'], array('size' => 40)); ?></td>
                        <td><?php echo CHtml::checkBox ("NewContacts[{$key}][ReceiveAdviceEmails]",isset($contact['ReceiveAdviceEmails']) ? $contact['ReceiveAdviceEmails'] : NULL); ?></td>
                        <td><a class="lnkDeleteContact" href="javascript:void(0)"> <img src="img/icons/cancel.png" alt="delete" width="16" height="16" />Delete</a></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
                </table>

            </fieldset>
        </div>

        <div id="tab-address">

            <div class="field">
                <?php
                echo $form->labelEx($model, 'Postcode');
                echo $form->textField($model, 'Postcode');
                ?>
                <button type="button" id="postcode_lookup">Find Address</button><br/>
                <div id="geotoolsselector" >&nbsp;</div>
            </div>

            <div class="field">
                <?php
                echo $form->labelEx($model,'Address');
                echo $form->textField($model,'Address1', array('size' => 50));
                ?>
            </div>
            <div><?php echo $form->textField($model,'Address2', array('size' => 50)); ?></div>
            <div><?php echo $form->textField($model,'Address3', array('size' => 50)); ?></div>
            <div><?php echo $form->textField($model,'Address4', array('size' => 50)); ?></div>

        </div>

        <?php if (!$model->isNewRecord): ?>
        <div id="tab-documents">
            <?php if (Login::checkPermission(Permission::PERM__FUN__POLESTAR__SUPPLIER_EDIT)) : ?> 
            <fieldset>
                <br/>
                <?php $this->widget('ext.EFineUploader.EFineUploader', array(
                    'id'=>'FineUploader',
                    'config'=>array(
                        'autoUpload'=>true,
                        'text' => array(
                            'uploadButton' => 'Upload a Document'
                        ),
                        'request'=>array(
                            'endpoint'=> $this->createUrl('polestar/supplierdocumentupload', array('id' => $model->Id, '#' => 'tab-documents')),
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
                        ),
                    )
                )); ?>
            </fieldset>
            <?php endif; ?>
            
            <h2>Documents</h2>
            <table class="listing fluid">
                <tr>
                    <th>Uploaded By</th>
                    <th>Date Uploaded</th>
                    <th>Document</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($model->Documents as $document): ?>
                <tr>
                    <td>
                        <?php echo CHtml::hiddenField("Documents[]",$document->Id); ?>
                        <?php echo $document->UploadedByUser->FriendlyName ?>
                    </td>
                    <td><?php echo $document->UploadedDate ?></td>
                    <td>
                        <a href="<?php echo $this->createUrl('polestar/supplierdocumentdownload', array ( 'id' => $document->Id ));?>"/>
                            <?php echo $document->FileName ?>
                        </a>
                    </td>
                    <td>
                        <?php if (Login::checkPermission(Permission::PERM__FUN__POLESTAR__SUPPLIER_EDIT)) : ?> 
                        <a href="#" class="lnkDeleteDocument" rel="-1">
                            <img src="<?php echo $baseUrl; ?>/img/icons/delete.png" title="Delete document" />
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>

    </div>

    <div class="titleWrap">
<?php   
    if (Login::checkPermission(Permission::PERM__FUN__POLESTAR__SUPPLIER_EDIT)) {
        echo CHtml::submitButton('Save', array('class'=>'formButton', 'name'=>'save'));
        echo "&nbsp;&nbsp;&nbsp;";
        echo CHtml::submitButton('Save and Exit', array('class'=>'formButton', 'name'=>'saveandexit'));
    }
        ?>
        <ul>
            <li class="seperator">
                <img height="16" width="16" alt="add" src="img/icons/cancel.png">
                <a href="<?php echo $this->createUrl('polestar/suppliers');?>">Cancel</a>
            </li>
        </ul>

    </div>

<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
var new_contact_id = <?php echo count($NewContacts); ?>;
var base_input_name="PolestarSupplier";
$(function(){
    $("#add_contact").click(function(){
        addNewContact();
    });
    
    $(".lnkEditContact").click(function(){
        var pthis = $(this);
        var $row = pthis.parents("tr:first");
        var $newRow = addNewContact($row);
        $row.before($newRow).remove();
    });
    
    $("#postcode_lookup").click(function(){
        //cp_obj_1.doLookup();
        var postcode = $("#PolestarSupplier_Postcode").val();
        var g = new GeoTools("<?php echo Yii::app()->params['geotools_token'] ?>");
        g.retrieve(postcode,function(result){
            $("#geotoolsselector").html("");
            if (result.length == 0){
                $("#geotoolsselector").html("No results");
            } else if (result.length == 1){
                var r = result[0];
                $("#PolestarSupplier_Address1").val(r.Address1);
                $("#PolestarSupplier_Address2").val(r.Address2);
                $("#PolestarSupplier_Address3").val(r.Address3);
                $("#PolestarSupplier_Address4").val(r.Address4);
            } else {
                var cs = document.createElement("select");
                cs.onchange = function(){
                    var r = result[cs.selectedIndex];
                    $("#PolestarSupplier_Address1").val(r.Address1);
                    $("#PolestarSupplier_Address2").val(r.Address2);
                    $("#PolestarSupplier_Address3").val(r.Address3);
                    $("#PolestarSupplier_Address4").val(r.Address4);
                    $("#geotoolsselector").html("");
                };
                $("<option/>").attr('value','').text('Postcode OK - select one of the addresses below').appendTo(cs);
                for (var i = 0; i < result.length; i++){
                    var opt = document.createElement("option");
                    opt.value = i;
                    opt.text = result[i]['Description'];
                    cs.appendChild(opt);
                }

                $("#geotoolsselector").append(cs);

            }
        });
        return false;
    });
});

$("body").on('click','.lnkDeleteContact', function(){
    var pthis = $(this);
    pthis.parents("tr").remove();
});

$("body").on('click','.lnkDeleteDocument', function(){
    var pthis = $(this);
    pthis.parents("tr").remove();
});

function addNewContact(_baseRow){
    var tr = $("<tr/>");
    var isNew = (_baseRow == undefined);
    if (isNew)
        _baseRow = tr;
    
    tr.append( $("<td/>").append(
            $("<select/>").attr("name","NewContacts[" + new_contact_id +"][Type]").append(
                    $("<option/>").attr('value', 'Day').text('Day')
                ).append(
                    $("<option/>").attr('value', 'Night').text('Night')
                ).val(
                    $('.type', _baseRow).text()
                )
            ) );
    tr.append( $("<td/>").append( $("<input/>").attr("name","NewContacts[" + new_contact_id +"][Department]").attr('size',13    ).val(
                    $('.dept', _baseRow).text()
                ) ) );
    tr.append( $("<td/>").append( $("<input/>").attr("name","NewContacts[" + new_contact_id +"][Name]").attr('size',20).val(
                    $('.name', _baseRow).text()
                ) ) );
    tr.append( $("<td/>").append( $("<input/>").attr("name","NewContacts[" + new_contact_id +"][Surname]").attr('size',20).val(
                    $('.surname', _baseRow).text()
                ) ) );
    tr.append( $("<td/>").append( $("<input/>").attr("name","NewContacts[" + new_contact_id +"][Telephone]").attr('size',20).val(
                    $('.landline', _baseRow).text()
                ) ) );
    tr.append( $("<td/>").append( $("<input/>").attr("name","NewContacts[" + new_contact_id +"][ExtensionNo]").attr('size',5).val(
                    $('.ext', _baseRow).text()
                ) ) );
    tr.append( $("<td/>").append( $("<input/>").attr("name","NewContacts[" + new_contact_id +"][Mobile]").attr('size',20).val(
                    $('.mobile', _baseRow).text()
                ) ) );
    tr.append( $("<td/>").append( $("<input/>").attr("name","NewContacts[" + new_contact_id +"][Email]").attr('size',40).val(
                    $('.email', _baseRow).text()
                ) ) );
    var asCheck = $("<input type='checkbox' value='1'/>").attr("name","NewContacts[" + new_contact_id +"][ReceiveAdviceEmails]").attr('size',13);
    if ($('.asemail', _baseRow).text() == '1')
        asCheck.attr('checked', 'checked');
    tr.append( $("<td/>").append( asCheck ) );
    tr.append( $("<td/>").append( $('<a class="lnkDeleteContact" href="javascript:void(0)"> <img src="img/icons/cancel.png" alt="delete" width="16" height="16" /> Delete</a>') )  );

    new_contact_id++;

    if (isNew)
        $("#contacts").append(tr);
    else
        return tr;
}
</script>