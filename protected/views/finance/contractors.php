<?php
    $this->breadcrumbs=array(
                array('label'=>'Home', 'url'=>array(Yii::app()->user->role->HomeUrl)),
                array('label'=>'Finance'),
                array('label'=>'Contractors'),
            );
    $baseUrl = Yii::app()->request->baseUrl;
    
    $contractors = $model->listData;

$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/sorttable.js');

?>
<style>
    #mainWrap {
        width: 95% !important;
    }
    table.sortable th:not(.sorttable_sorted):not(.sorttable_sorted_reverse):not(.sorttable_nosort):after { 
        content: " \25B4\25BE" 
    }
    /*table.sortable tbody tr:nth-child(2n) td {
        background: #dfdfdf;
    }
    table.sortable tbody tr:nth-child(2n+1) td {
        background: #fff;
    }*/
</style>

<div class="titleWrap">
    <h1>Add/Edit Contractors (<?php echo count($contractors)?>)</h1>
    <ul>
        <li>
            Filter: 
            <select id="ddlFilter">
                <option value="*">All</option>
                <option selected="selected" value="ON">Live</option>
                <option value="OFF">Not Live</option>
            </select>
        </li>
        <li class="seperator">
            <img src="<?php echo $baseUrl; ?>/img/icons/add.png" alt="add" />
            <a href="<?php echo $this->createUrl('finance/contractor');?>" id="lnkNew">Add New</a>
        </li>
    </ul>
</div>

<table id="tblListing" class="listing fluid sortable" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th>Contract <br/>Type</th>
      <th>Id</th>
      <th>Code</th>
      <th>Name</th>
      <th class="live-flag">Live</th>
      <th>E-mail</th>
      <th>Account</th>
      <th>MOT Expiry Date</th>
      <th>Insurance Expiry Date</th>
      <th>Tax</th>
      <th width="10%" class="sorttable_nosort">Actions</th>
    </tr>
    </thead>
<tbody>
    <?php
    $count = count($contractors);
    for($i = 0; $i < $count; $i++):
        $c = $contractors[$i];
    ?>
        <tr>
            <td><?php echo $c->Data ?></td>
          <td><?php echo $c->ContractorId ?></td>
          <td><?php echo $c->Code ?></td>
          <td class="clientName">
                  <?php echo trim($c->FirstName." ".$c->LastName);
                          if (!empty($c->ParentContractorId))
                                  echo " (<strong>".trim($c->ParentFirstName." ".$c->ParentLastName)."</strong>)";
                          ?>
          </td>

          <td class="live-flag" sorttable_customkey="<?php echo $c->IsLive; ?>"><img src="<?php echo $baseUrl; ?>/img/icons/<?php echo ($c->IsLive == '1') ? 'accept' : 'cancel'; ?>.png" /></td>
          <td>
              <?php echo (empty($c->Email) ? '-' : $c->Email); ?>
          </td>
          
          <td>
              <?php if (!empty($c->AccountNumber)) : 
                    echo $c->AccountNumber;
              else:?>
                  <img src="<?php echo $baseUrl; ?>/img/icons/cancel.png" />
              <?php endif; ?>
          </td>
          
          <td>
              <?php echo (empty($c->MOTExpiryDate01) ? '-' : date('d/m/Y', CDateTimeParser::parse($c->MOTExpiryDate01, "yyyy-MM-dd"))); ?>
          </td>
          <td>
              <?php echo (empty($c->InsExpiryDate01) ? '-' : date('d/m/Y', CDateTimeParser::parse($c->InsExpiryDate01, "yyyy-MM-dd"))); ?>
          </td>
          
          <td>
              <?php echo (empty($c->TaxDescription) ? '-' : $c->TaxDescription); ?>
          </td>
          
          <td>
              <a href="<?php echo $this->createUrl('finance/contractor', array('id'=>$c->ContractorId));?>">
              <img src="<?php echo $baseUrl; ?>/img/icons/page_edit.png" alt="edit" width="16" height="16" />
              <?php if (Login::checkPermission(Permission::PERM__FUN__LSC__CONTRACTOR_EDIT)): ?>
              Edit
              <?php else: ?>
              View
              <?php endif; ?>
              </a>
          </td>
        </tr>
    <?php
    endfor;

    if ($count === 0) :
    ?>
        <tr class="row1">
            <td colspan="4">
                <div class="infoBox">There are no contractors setup on the system, <a href="<?php echo $this->createUrl('finance/contractor');?>">add a new one now</a>.</div>
            </td>
        </tr>
    <?php
    endif;
    ?>
</tbody>
</table>

<script>
    $(function(){
        
        var liveTH = null;
        var headers = document.getElementsByTagName("th");
        for (var i = 0; i < headers.length; i++)
            if (headers[i].className == 'live-flag')
                liveTH = headers[i];
        
        $("#ddlFilter").change(function(){ applyFilter($(this).val()); });
        
        $("#ddlFilter").change();
    });
    
    function applyFilter(filter){
        //var filter = $(this).val();
        var $tbl = $("#tblListing tbody");

        //sorttable.innerSortFunction.apply(liveTH, []);

        switch(filter)
        {
            case "*":
                $("tr", $tbl).show();
                break;
            case "ON":
                $("tr", $tbl).hide();
                $("tr", $tbl).find(".live-flag[sorttable_customkey='1']").each(function(){
                    $(this).parents('tr:first').show();
                });
                break;
            case "OFF":
                $("tr", $tbl).hide();
                $("tr", $tbl).find(".live-flag[sorttable_customkey='0']").each(function(){
                    $(this).parents('tr:first').show();
                });
                break;
        }
        
        repaintListing();
    }
    
    function repaintListing() {
        var $tbl = $("#tblListing tbody");
        $("tr:visible", $tbl).each(function(idx){
            $(this).removeClass('row1').removeClass('row2').addClass('row'+((idx%2)+1));
        });
    }
</script>