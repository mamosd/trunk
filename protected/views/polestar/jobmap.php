<?php 
    $job = $model->getJob();
?>


<style type="text/css">
    #map-canvas { 
        height: 375px;
        margin: 0;
        padding: 0;
        width: 500px;
    }
    .loads td {
        vertical-align: middle !important;
    }
</style>

<h1>Job Mileage Information - <?php echo $job->Ref ?></h1>

<fieldset>

<?php
foreach ($model->messages as $msg) :
    $cssClass = $msg['class'].'Box';
    $text = $msg['message'];
    ?>
    <div class="<?php echo $cssClass ?>">
        <?php echo $text ?>
    </div>
<?php
endforeach;

$mapData = $model->getLatestDirections();

if (!isset($mapData)):
?>
    <div class="errorBox">No routing information available to display</div>
<?php
else:
    
    $route = $mapData->routes[0];
    
    $boundSWLat = $route->bounds->southwest->lat;
    $boundSWLng = $route->bounds->southwest->lng;
    
    $boundNELat = $route->bounds->northeast->lat;
    $boundNELng = $route->bounds->northeast->lng;
    
    $apiKey = Setting::get('polestar', 'google-api-key');
    
    $range = range('A', 'Z');
    $markerLetters = array_merge(array('>'), $range);
    $markersUrl = array();
    ?>
    <div class="infoBox">Latest routing information available displayed below.</div>

    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key=<?php echo $apiKey ?>">
    </script>
    <script type="text/javascript">
      function initialize() {
        var bounds = new google.maps.LatLngBounds(
                    new google.maps.LatLng(<?php echo $boundSWLat ?>, <?php echo $boundSWLng ?>),
                    new google.maps.LatLng(<?php echo $boundNELat ?>, <?php echo $boundNELng ?>)
                );
        
        var mapOptions = {
          center: bounds.getCenter(),
          streetViewControl: false,
          mapTypeControlOptions : { mapTypeIds : [] }
        };
        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);
        map.fitBounds(bounds);
        
        <?php 
        $legs = $route->legs;
        $legCount = count($legs);
        for ($i = 0; $i < $legCount; $i++):
            $leg = $legs[$i];
            $letter = $markerLetters[$i];
            $color = ($i === 0) ? 'DFF2BF' : 'BDE5F8';
            $markerLetters[PolestarJobMap::doSanitizePostcode($leg->start_address)] = array('letter' => $letter, 'color' => $color);
            ?>
            addMarker(map, 
                    <?php echo $leg->start_location->lat ?>, 
                    <?php echo $leg->start_location->lng ?>, 
                    "<?php echo addslashes($leg->start_address) ?>",
                    "<?php echo $letter ?>",
                    "<?php echo $color ?>");
            <?php
            if ($i == ($legCount-1)): 
                $letter = $markerLetters[$i+1];
                $color = 'FFBABA';
                $markerLetters[PolestarJobMap::doSanitizePostcode($leg->end_address)] = array('letter' => $letter, 'color' => $color);
                ?>
            addMarker(map, 
                    <?php echo $leg->end_location->lat ?>, 
                    <?php echo $leg->end_location->lng ?>, 
                    "<?php echo addslashes($leg->end_address) ?>",
                    "<?php echo $letter ?>",
                    "<?php echo $color ?>");
            <?php
            endif;
            
            foreach ($leg->steps as $step) :
            ?>
            addDirectionsPoly(map, "<?php echo addslashes($step->polyline->points) ?>");
        <?php 
            endforeach;
        endfor; ?>
      }
      
      function addDirectionsPoly(map, encoded) {
        var depoly = google.maps.geometry.encoding.decodePath(encoded);
        var poly = new google.maps.Polyline({
                map:map, 
                path:depoly,
                strokeColor: "#FF0000",
                strokeOpacity: 0.5,
                strokeWeight: 4
            });
      }
      
      function addMarker(map, lat, lng, address, letter, colour) {
        var ll = new google.maps.LatLng(lat, lng);
        
        var pinImage = new google.maps.MarkerImage(
                        "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld="
                                        + letter + "|" + colour, new google.maps.Size(21,
                                        34), new google.maps.Point(0, 0),
                        new google.maps.Point(10, 34));
        
        var mrk = new google.maps.Marker({
            position: ll,
            map: map,
            icon : pinImage,
            title: address
        });
      }
      
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    
    <table class="listing fluid">
        <tr>
            <td width="1">
                <div id="map-canvas"></div>
            </td>
            <td style="vertical-align: top;">
                <div class="stackedForm">
                <fieldset>
                    <legend>Job Details</legend>
                    <div class="field">
                        <?php echo CHtml::label('Total Mileage', FALSE); 
                        echo $job->TotalMileage;
                        ?>
                    </div>
                    
                    <table class="listing fluid points">
                    <tr>
                        <td></td>
                        <th colspan="5">Collection</th>
                    </tr>
                    <tr>
                        <td></td>
                        <th width="50%">Postcode</th>
                        <th width="50%">Mileage</th>
                    </tr>
                    <?php 
                    $points = array_merge(array($job), $job->CollectionPoints);
                    foreach($points as $point): 
                        $icon = NULL;
                        foreach ($markerLetters as $markerAddress => $info) {
                            $pc = PolestarJobMap::doSanitizePostcode($point->CollPostcode);
                            if (stristr($markerAddress, $pc))
                                $icon = $info;
                        }
                        $url = (isset($icon)) ? "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=".$icon['letter']."|".$icon['color'] : '';
                        
                        ?>
                    <tr class='<?php echo cycle('row1', 'row2'); ?>'>
                        <td>
                            <?php if (!empty($url)): ?>
                                <img src="<?php echo $url ?>" height="25"/>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $point->CollPostcode; ?></td>
                        <td><?php echo $point->Mileage; ?></td>
                    </tr>
                    <?php endforeach;?>
                    </table>
                    
                </fieldset>
                
                <fieldset>
                    <legend>Loads Information</legend>
                    
                    <table class="listing fluid loads">
                    <tr>
                        <td colspan="2"></td>
                        <th colspan="6">Delivery</th>
                    </tr>
                    <tr>
                        <td></td>
                        <th>PolestarLoadRef</th>
                        <th width="50%">Postcode</th>
                        <th width="50%">Mileage</th>
                    </tr>
                    <?php foreach($job->Loads as $load): 
                        if ($load->StatusId == PolestarStatus::CANCELLED_ID) // do not show cancelled loads
                            continue;
                        
                        $icon = NULL;
                        foreach ($markerLetters as $markerAddress => $info) {
                            $pc = PolestarJobMap::doSanitizePostcode($load->DelPostcode);
                            if (stristr($markerAddress, $pc))
                                $icon = $info;
                        }
                        $url = (isset($icon)) ? "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=".$icon['letter']."|".$icon['color'] : '';
                        
                        ?>
                    <tr class='<?php echo cycle('row1', 'row2'); ?>'>
                        <td>
                            <?php if (!empty($url)): ?>
                                <img src="<?php echo $url ?>" height="25"/>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $load->Ref; ?></td>
                        <td><?php echo $load->DelPostcode; ?></td>
                        <td><?php echo $load->Mileage; ?></td>
                    </tr>
                    <?php endforeach;?>
                    </table>
                        
                </fieldset>
                </div>
            </td>
        </tr>
    </table>

<?php
endif;
?>
</fieldset>