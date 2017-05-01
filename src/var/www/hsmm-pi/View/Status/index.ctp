<!-- File: /app/View/Status/index.ctp -->
<?php
    // $mesh_node_locations = array(
    //   "10.0.2.1" => array(
    //     "lat" => "30.00000",
    //     "lon" => "-96.00000"
    //   )
    // );
  var_dump($mesh_node_locations);
  var_dump($node_wifi_ip_address);
?>
<script>
  $(document).on('click', '.open-mapModal', function() {

    $('.longitude').text($(this).data('lon'));
    $('.latitude').text($(this).data('lat'));

    var latLng = {
      lat: $(this).data('lat'),
      lng: $(this).data('lon')
    }
    // map  = new google.maps.Map(document.getElementById('mapDiv'), {center:{latLng},zoom:15});
    // marker = new google.maps.Marker({
    //   position: latLng,
    //   map: map
    // });

    // listener = new google.maps.event.addListener(marker, 'click', function(){
    //   var infowindow = new google.maps.InfoWindow({
    //     content: marker.title,
    //     position: latLng
    //   });
    //   infowindow.open(map);
    // });

  });
</script>

<div class="page-header">
  <h1>Status&nbsp;
    <small><?php echo $node_name;?>
    <?php
      if (array_key_exists($node_wifi_ip_address, $mesh_node_locations)) {
        $location = $mesh_node_locations[$node_wifi_ip_address];
        if ($location != NULL) {
          echo "&nbsp;<a href=\"#mapModal\" data-lat=\"" . $location['lat'] . "\" data-lon=\"" . $location['lon'] . "\" role=\"button\" class=\"open-mapModal glyphicon glyphicon-globe unstyled-link\" data-toggle=\"modal\"></a>";
        }
      }
    ?>
    </small>
  </h1>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="well">
      <h3>Neighbors</h3>

      <?php
        if ($mesh_links != NULL && sizeof($mesh_links['links']) > 0) {
      ?>
        <table class="table table-striped table-bordered">
          <tr>
            <th>Hostname</th>
            <th>IP Address</th>
            <th>Link Quality</th>
          </tr>
          <?php
            $neighbor_ips = array();
            foreach ($mesh_links['links'] as $node) {
              $neighbor_ips[$node['remoteIP']] = 1;
          ?>
            <tr>
              <?php
                $node_hostname = $mesh_hosts[$node['remoteIP']];
                if (!$node_hostname) {
                  $node_hostname = $node['remoteIP'];
                }
              ?>
              <td><a href="http://<?php echo $node_hostname;?>:8080/"><?php echo $node_hostname;?></a>
                <?php
                  if (array_key_exists($node['remoteIP'], $mesh_node_locations)) {
                    $location = $mesh_node_locations[$node['remoteIP']];
                    if ($location != NULL) {
                      echo "&nbsp;<a href=\"#mapModal\" data-lat=\"" . $location['lat'] . "\" data-lon=\"" . $location['lon'] . "\" role=\"button\" class=\"open-mapModal glyphicon glyphicon-globe unstyled-link\" data-toggle=\"modal\"></a>";
                    }
                  }
                ?>
                <?php if (in_array($node['remoteIP'], $mesh_neighbors)) {?>
                  &nbsp;<i class="glyphicon glyphicon-star"></i>
                <?php }
                ?>
              </td>
              <td><?php echo $node['remoteIP'];?></td>
              <td>
                <div class="progress">
                  <div class="progress-bar" role="progressbar" style="width: <?php echo round($node['linkQuality'] * 100) . '%';?>;"><?php echo round($node['linkQuality'] * 100) . '%';?></div>
                </div>
              </td>
            </tr>
          <?php
            }
          ?><!-- /foreach $mesh_links -->
        </table>
      <?php
        } else {
      ?>
        <div class="alert alert-danger">
          <strong>Warning!</strong>.  There are no mesh links in range.  It's a bit quiet around here.
        </div>
      <?php
        }
      ?>
    </div><!-- /.well -->

    <?php
      $remote_nodes = array();
      foreach ($mesh_routes as $route) {
        if ($route['genmask'] < 32) continue;
        if (array_key_exists($route['destination'], $neighbor_ips)) continue;
        $node_hostname = $mesh_hosts[$route['destination']];
        if (!$node_hostname) {
          $node_hostname = $route['destination'];
        }
        if (substr($node_hostname, 0, 8) === "dtdlink.") continue;
        $route['hostname'] = $node_hostname;
        $remote_nodes[] = $route;
      }
      if (sizeof($remote_nodes) > 0) {
    ?>
      <div class="well">
        <h3>Remote Nodes</h3>
        <table class="table table-striped table-bordered">
          <tr>
            <th>Hostname</th>
            <th>IP Address</th>
            <th>Link Cost</th>
          </tr>
          <?php
            foreach ($remote_nodes as $node) {
          ?>
            <tr>
              <td><a href="http://<?php echo $node['hostname'];?>:8080/"><?php echo $node['hostname'];?></a>
              <?php
                if (array_key_exists($node['destination'], $mesh_node_locations)) {
                  $location = $mesh_node_locations[$node['destination']];
                  if ($location != NULL) {
                    echo "&nbsp;<a href=\"#mapModal\" data-lat=\"" . $location['lat'] . "\" data-lon=\"" . $location['lon'] . "\" role=\"button\" class=\"open-mapModal glyphicon glyphicon-globe unstyled-link\" data-toggle=\"modal\"></a>";
                  }
                }
              ?>
              </td>
              <td><?php echo $node['destination']; ?></td>
              <td><?php echo number_format($node['rtpMetricCost'] / 1024, 2); ?></td>
            </tr>
          <?php
            }
          ?>
        </table>
      </div><!-- /.well -->
    <?php
      }
    ?>
  </div><!-- /.col-md-8 -->

  <div class="col-md-4">
    <div class="well">
      <h3>Mesh Services</h3>

      <?php
        if ($mesh_services != NULL && sizeof($mesh_services) > 0) {
      ?>
        <table class="table table-striped table-bordered">
          <tr>
            <th>Service</th>
          </tr>
          <?php
            foreach ($mesh_services as $service) {
          ?>
            <tr>
              <td><a href="<?php echo $service[0];?>"><?php echo $service[2];?></a></td>
            </tr>
          <?php
            }
          ?>
        </table>
      <?php
        } else {
      ?>
      <div class="alert alert-info">There are no mesh services being announced at this time.</div>
      <?php
        }
      ?>
    </div><!-- /.well -->
  </div><!-- /.col-md-4 -->
</div><!-- /.row -->

<div class="row">
  <div class="col-md-8">
    <h6>HSMM-Pi Version:&nbsp;<?php echo Configure::read('App.version');?></h6>
  </div>
</div>

<!-- Modal -->
<div id="mapModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabelMap" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modalLabelMap">Node Location Map</h3>
      </div>
      <div class="modal-body">
        <div id='mapDiv' style="position:relative; width:500px; height:350px;"></div>
        <h5>Latitude: <span class="latitude"></span>, Longitude: <span class="longitude"></span> <small><span class="latitude"></span>, <span class="longitude"></span></small></h5>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
      </div>
    </div>
  </div>
</div>