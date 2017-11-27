<?php
/**
 * DEPRECATED - now using activitylog instead
 */

    $baseUrl = Yii::app()->request->baseUrl;
    $allowedPCOptions = $printCentres;
?>
<style>
.status-cell {
    /*text-transform: uppercase;*/
    font-weight: bold;
    padding: 7px !important;
    text-align: center;
    white-space: nowrap;
}
.amended {
    color: black !important;
    background-color: yellow !important;
    text-shadow: none !important;
}
.cancelled {
    color: white !important;
    background-color: red !important;
    text-shadow: none !important;
}
.confirmed {
    color: white !important;
    background-color: green !important;
    text-shadow: none !important;
}
.data-completed {
    color: white !important;
    background-color: green !important;
    text-shadow: none !important;
}
.late-advice {
    color: white !important;
    background-color: blue !important;
    text-shadow: none !important;
}
.newly-added {
    color: white !important;
    background-color: black !important;
    text-shadow: none !important;
}
.same-day {
    color: black !important;
    background-color: orange !important;
    text-shadow: none !important;
}
.booked {
    color: white !important;
    background-color: green !important;
    text-shadow: none !important;
}    
</style>


<div id="dialog-loading" title="Please wait" style="display:none;">
    <div class="infoBox">Loading information...</div>
</div>

<script>
    function showLoader() {
        $( "#dialog-loading" ).dialog({
                resizable: false,
                height:100,
                modal: true
        });
    }

    function hideLoader() {
        if ($( "#dialog-loading" ).hasClass('ui-dialog-content'))
            $( "#dialog-loading" ).dialog('destroy');
    }
</script>


<div class="titleWrap">
<h1>Activity</h1>

<ul>
    <li>
        <a href="#" id="btnRefresh">
            <img src="<?php echo $baseUrl; ?>/img/icons/page_refresh.png" />
            Refresh
        </a>
    </li>
</ul>
</div>

<div id="pc-tabs">
  <ul>
      <?php
        $activeIdx = 0;
        $idx = 0;
        foreach ($allowedPCOptions as $pcid => $pcname) {
            if ($pcid == $model->printCentreId) {
                echo '<li><a href="#tabs-1">'.$pcname.'</a></li>';
                $activeIdx = $idx;
            }
            else {
                $url = $this->createUrl('polestar/activity', array('id' => $pcid));
                echo '<li><a href="'.$url.'">'.$pcname.'</a></li>';
            }
            $idx++;
        }
      ?>
  </ul>
    <div id="tabs-1">
        <div id="list-container">
        <?php
        $this->renderPartial('activity_pc', array(
                                'model' => $model
                            ));
        ?>
        </div>
    </div>
</div>

<script>
$(function(){
    showLoader();
    
    $( "#pc-tabs" ).tabs({
      active: <?php echo $activeIdx ?>,
      beforeLoad: function( event, ui ) {
        var $lnk = $('a:first', ui.tab);
        showLoader();
        location.href = $lnk.attr('href');
        return false;
      }
    });
    
    $("#btnRefresh").click(refreshList);
    
    setInterval(refreshList, 10000);
    
    hideLoader();
});

function refreshList() {
    showLoader();
    $.get(
        "<?php echo $this->createUrl("polestar/activity", array('id' => $model->printCentreId, 'ajax' => 1)) ?>",
        null,
        function(data) {
            $("#list-container").html(data);
            hideLoader();
        },
        'html'
    );
    return false;
}
</script>
