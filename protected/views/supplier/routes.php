<?php
    $this->pageTitle = Yii::app()->name.' - '.Yii::app()->user->role->Description;
    $this->breadcrumbs=array(
                array('label'=>'Home ('.Yii::app()->user->friendlyName.')'),
            );
    ?>

<div class="titleWrap">
    <h1>Routes &gt; <?php echo (($model->status == RouteInstance::STATUS_ACTIVE) ? 'Active' : 'All'); ?></h1>
    <ul>
        <li class="seperator">
            View 
            <a 
                class="<?php echo (($model->status == RouteInstance::STATUS_ACTIVE) ? 'highlighted' : ''); ?>"
                href="<?php echo $this->createUrl('supplier/routes', array('status' => RouteInstance::STATUS_ACTIVE));?>">active</a> |
            <a class="<?php echo (($model->status == "*") ? 'highlighted' : ''); ?>"
                href="<?php echo $this->createUrl('supplier/routes', array('status' => '*'));?>">all</a>
        </li>
    </ul>
</div>


<table class="listing fluid" cellpadding="0" cellspacing="0">
<tbody>
    <tr class="header">
        <th width="75">Date</th>
        <th >Route</th>
        <th width="100">Actions</th>
    </tr>
<?php
$idxclass = 1;
foreach($model->routes as $route): ?>
    <tr class="row<?php echo $idxclass; ?>">
        <td><?php echo $route->Date ?></td>
        <td><?php echo $route->RouteName ?></td>
        <td>
            <?php if ($route->Status == RouteInstance::STATUS_ACTIVE) : ?>
            <button title="Print notes" class="<?php echo ($route->IsPrinted != '1') ? 'warningbg ' : ''; ?>print-button"
                    rid="<?php echo $route->RouteInstanceId ?>"
                    href="<?php echo $this->createUrl('supplier/routeprint', array('id'=>$route->RouteInstanceId));?>">
                <img src="img/icons/printer.png">
            </button>
            <!--button title="Enter departure time" class="time-button"
                    <?php echo ($route->IsPrinted == '1') ? '' : 'disabled="disabled"' ?>
                    rid="<?php echo $route->RouteInstanceId ?>"
                    href="<?php echo $this->createUrl('supplier/routedeparturetime', array('id'=>$route->RouteInstanceId));?>">
                <img src="img/icons/clock_add.png">
            </button-->
            <button title="Enter delivery information" class="delivery-button"
                    <?php echo ($route->IsPrinted == '1') ? '' : 'disabled="disabled"' ?>
                    rid="<?php echo $route->RouteInstanceId ?>"
                    href="<?php echo $this->createUrl('supplier/routedeliveryinfo', array('id'=>$route->RouteInstanceId));?>">
                <img src="img/icons/table_edit.png">
            </button>
            <!--button title="Archive Route" class="archive-button"
                    prompt="This route is about to be archived. Are you sure you wish to proceed?"
                    href="<?php echo $this->createUrl('supplier/routearchive', array('id'=>$route->RouteInstanceId));?>"
                    rid="<?php echo $route->RouteInstanceId ?>">
                <img src="img/icons/disk.png">
            </button-->

            <?php else: ?>

            <button title="View notes" class="print-button"
                    rid="<?php echo $route->RouteInstanceId ?>"
                    href="<?php echo $this->createUrl('supplier/routeprint', array('id'=>$route->RouteInstanceId));?>">
                <img src="img/icons/eye.png">
            </button>
            <button title="View delivery summary" class="delivery-button"
                    <?php echo ($route->IsPrinted == '1') ? '' : 'disabled="disabled"' ?>
                    rid="<?php echo $route->RouteInstanceId ?>"
                    href="<?php echo $this->createUrl('supplier/routedeliveryinfo', array('id'=>$route->RouteInstanceId));?>">
                <img src="img/icons/table.png">
            </button>
            <!--button title="Activate Route" class="archive-button"
                    prompt=""
                    href="<?php echo $this->createUrl('supplier/routearchive', array('id'=>$route->RouteInstanceId, 'status'=> RouteInstance::STATUS_ACTIVE));?>"
                    rid="<?php echo $route->RouteInstanceId ?>">
                <img src="img/icons/door_out.png">
            </button-->

            <?php endif; ?>
        </td>
    </tr>
<?php
    $idxclass = ($idxclass == 1) ? 2 : 1;
endforeach; ?>
</tbody>
</table>

<script type="text/javascript">
    $(document).ready(function() {

        $(".print-button").colorbox({
            width:"900px",
            height:"600px",
            iframe:true,
            href: function(){
                return $(this).attr('href');
            },
            onLoad: addPrintButton,
            onClosed : function(){
                // get updated statuses
                $.getJSON("<?php echo $this->createUrl('supplier/routestatus') ?>", function(data){
                    if (data.routes != undefined)
                    {
                        for(var i = 0; i < data.routes.length; i++)
                        {
                            var route = data.routes[i];
                            if (route.isPrinted == "1")
                            {
                                $(".time-button[rid="+route.id+"]").removeAttr('disabled');
                                $(".delivery-button[rid="+route.id+"]").removeAttr('disabled');
                            }
                        }
                    }
                });
            }
        });

        $(".time-button").colorbox({
            width:"400px",
            height:"425px",
            iframe:true,
            href: function(){
                return $(this).attr('href');
            }
        });

        $(".delivery-button").colorbox({
            width:"900px",
            height:"600px",
            iframe:true,
            onLoad: addPrintButton,
            href: function(){
                return $(this).attr('href');
            },
            onClosed : function(){
                location.reload();
            }
        });

        $(".archive-button").click(function(){
            var bContinue = true;
            if ($(this).attr("prompt") != "")
                bContinue = confirm($(this).attr("prompt"));
            
            if (bContinue)
                $.getJSON($(this).attr("href"), function(data){
                    if (data.result == "OK")
                        location.reload();
                    else
                        alert(data.result);
                });
        });
    });

    function addPrintButton()
    {
        if ($("#cboxCurrent a").length == 0)
        {
            $("<a />").attr('href', '#').text('print').click(function(){
                printIframe("cboxIframe");
            }).appendTo("#cboxCurrent");
        }
        $("#cboxCurrent").attr('style', 'left:0;width:100%;').show();
    }

    function printIframe(id)
    {
        var iframe = document.frames ? document.frames[id] : document.getElementById(id);
        var ifWin = iframe.contentWindow || iframe;
        iframe.focus();
        ifWin.print();
        return false;
    }

</script>