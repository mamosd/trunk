<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Polestar', 'url' => '#'),
                array('label'=>'Suppliers'),
            );
    $baseUrl = Yii::app()->request->baseUrl;
    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile($baseUrl.'/js/sorttable.js');

    $okImage = $baseUrl . '/img/icons/email.png';
    $cancelImage = $baseUrl . '/img/icons/cancel.png';
?>

<style>
    #mainWrap {
        display: table;
    }
    .route-link {
        cursor: pointer;
    }
    .vtop th {
        vertical-align: middle;
    }
    .white {
        background-color: #FFF;
    }
    .red {
        background-color: #f46464;
    }
    .blue {
        background-color: #6af56e;
    }
    .sortable-hand {
        cursor: pointer;
    }


    .selected-type {
        /*color: red;*/
    }

    .unselected-type {
        font-weight: normal !important;
        text-decoration: none !important;
    }

    .route-listing tr:hover td { background: #A3FFA3; }
    .highlighted-row td { background: #FFFF99 !important; }
    .ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active {
        background: #21E621 !important;
    }
    .ui-state-active a, .ui-state-active a:link, .ui-state-active a:visited {
        color: #fff !important;
    }
</style>

<div class="titleWrap">
    <h1>Add/Edit Suppliers</h1>
    <ul>
        <li>
            Filter:
            <select id="ddlFilter">
                <option value="*">All</option>
                <option selected="selected" value="ON">Live</option>
                <option value="OFF">Not Live</option>
            </select>
        </li>
        <?php if (Login::checkPermission(Permission::PERM__FUN__POLESTAR__SUPPLIER_EDIT)) : ?> 
        <li class="seperator"><img src="img/icons/add.png" alt="add" />
            <a href="<?php echo $this->createUrl('polestar/supplier');?>">Add New</a>
        </li>
        <?php endif; ?>
    </ul>
</div>

<table id="tblListing" class="listing fluid vtop sortable route-listing" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="1%">Supplier</th>
            <th width="1%">Address</th>
            <th width="5%">Name</th>
            <th>Contacts</th>
            <th width="1%">Live</th>
            <th width="1%">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        //for($i = 0; $i < $count; $i++):
        foreach ($suppliers as $supplier):
        ?>
            <tr class="row<?php echo (($i++%2)+1) ?>">
                <td style="white-space: nowrap;"><?php echo $supplier->Code ?></td>
              <td style="white-space: nowrap;">
                 <?php echo $supplier->Address1 ?><br/>
                 <?php echo $supplier->Address2 ?><br/>
                 <?php echo $supplier->Address3 ?><br/>
                 <?php echo $supplier->Address4 ?><br/>
                 <?php echo $supplier->Postcode ?><br/>
              </td>
              <td style="white-space: nowrap;"><?php echo $supplier->Name ?></td>
              <td style="white-space: nowrap;">
                <?php 
                if (!empty($supplier->Contacts)) : ?>
                  <table class="listing fluid">
                      <tr>
                          <th width="1">D/N</th>
                          <th width="10%">Dept</th>
                          <th width="30%">Name</th>
                          <th width="10%">Landline</th>
                          <th width="10%">Mobile</th>
                          <th width="30%">Email</th>
                          <th width="1">A/S</th>
                      </tr>
                  
                <?php  
                foreach ($supplier->Contacts as $contact): ?>
                      <tr class="<?php echo cycle("row1","row2") ?>">
                          <td><?php echo $contact->Type ?></td>
                          <td style="white-space: nowrap;"><?php echo $contact->Department ?></td>
                          <td style="white-space: nowrap;"><?php echo trim($contact->Name.' '.$contact->Surname) ?></td>
                          <td style="white-space: nowrap;">
                              <?php echo $contact->Telephone.((!empty($contact->ExtensionNo)) ? ' ext '.$contact->ExtensionNo : '') ?>
                          </td>
                          <td style="white-space: nowrap;"><?php echo $contact->Mobile ?></td>
                          <td><?php echo $contact->Email ?></td>
                          <td>
                              <?php echo CHtml::image($contact->ReceiveAdviceEmails?$okImage:$cancelImage) ?>
                          </td>
                      </tr>
                <?php endforeach; ?>
                    </table>
                <?php
                else: ?>
                    <div class="warningBox">No contacts yet added for this supplier</div>
                <?php 
                endif; ?>
              </td>
              <td class="live-flag" sorttable_customkey="<?php echo $supplier->Live; ?>">
              <img src="<?php echo $baseUrl ?>/img/icons/<?php echo $supplier->Live == 1 ? 'accept' : 'cancel' ?>.png" />

              </td>
              <td style="white-space: nowrap;">
                  <a href="#" class="full-view" rel="<?php echo $supplier->Id ?>">
                  <img src="img/icons/information.png" alt="edit" width="16" height="16" />
                  Full View</a><br/>
                  <a href="<?php echo $this->createUrl('polestar/supplier', array('id'=>$supplier->Id));?>">
                  <img src="img/icons/page_edit.png" alt="edit" width="16" height="16" />
                  Edit</a>
              </td>
            </tr>
        <?php
        endforeach; ?>
    </tbody>
    <?php
    if (empty($suppliers)) :
        ?>
        <tfoot>
            <tr class="row1">
                <td colspan="15">
                    <div class="infoBox">There are no suppliers setup on the system. 
                        <?php if (Login::checkPermission(Permission::PERM__FUN__POLESTAR__SUPPLIER_EDIT)) : ?> 
                        <a href="<?php echo $this->createUrl('polestar/supplier');?>">add a new one now</a>.
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </tfoot>
        <?php
        endif;
        ?>
</table>


<script>
$(function(){

    $('.route-listing td').click(function(){
        var cssClass = 'highlighted-row';
        var $row = $(this).parents('tr:first');
        if ($row.hasClass(cssClass))
            $row.removeClass(cssClass);
        else
            $row.addClass(cssClass);
    });

    $(".full-view").click(function(){
        var url = "<?php echo $this->createUrl('polestar/supplier_fullview', array('ui'=>'popUp'));?>";
        url += (url.indexOf("?") == -1) ? "?" : "&";
        url += "id=" + $(this).attr('rel');
        $.colorbox({href: url, width:"1000px", height:"500px", iframe:true});
        return false;
    });
});


</script>

<script>
    $(function(){

        var liveTH = null;
        var headers = document.getElementsByTagName("th");
        for (var i = 0; i < headers.length; i++)
            if (headers[i].className == 'live-flag')
                liveTH = headers[i];

        $("#ddlFilter").change(function(){
            var filter = $(this).val();
            var $tbl = $("#tblListing tbody:first");

            //sorttable.innerSortFunction.apply(liveTH, []);

            switch(filter)
            {
                case "*":
                    $("> tr", $tbl).show();
                    break;
                case "ON":
                    $("> tr", $tbl).hide();
                    $("> tr", $tbl).find(".live-flag[sorttable_customkey='1']").each(function(){
                        $(this).parents('tr:first').show();
                    });
                    break;
                case "OFF":
                    $("> tr", $tbl).hide();
                    $("> tr", $tbl).find(".live-flag[sorttable_customkey='0']").each(function(){
                        $(this).parents('tr:first').show();
                    });
                    break;
            }
            
            repaintTable();

        });
       
        $("#ddlFilter").change();
    });
    
    function repaintTable() {
        var $tbl = $("#tblListing tbody:first");
        $("> tr:visible", $tbl).each(function(idx){
            $(this).removeClass('row1')
                    .removeClass('row2')
                    .addClass('row'+((idx%2)+1));
        });
    }
</script>

