<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    
    <!-- Filters -->
    <div class="card card-outline card-primary mb-3">
      <div class="card-body py-2">
        <form action="<?php echo env_url( 'admin/ticket_analytics' ); ?>" method="get" class="form-inline flex-wrap">
          <label class="mr-2 font-weight-bold">Filters:</label>
          <select class="form-control form-control-sm mr-2 mb-1" name="area">
            <option value="">All Areas</option>
            <?php if ( ! empty( $all_areas ) ) {
              foreach ( $all_areas as $a ) { ?>
                <option value="<?php echo html_escape( $a->area ); ?>" <?php echo ( $filter_area == $a->area ) ? 'selected' : ''; ?>><?php echo html_escape( $a->area ); ?></option>
            <?php }
            } ?>
          </select>
          <select class="form-control form-control-sm mr-2 mb-1" name="type">
            <option value="">All Incident Types</option>
            <?php if ( ! empty( $all_types ) ) {
              foreach ( $all_types as $at ) { ?>
                <option value="<?php echo html_escape( $at->incident_type ); ?>" <?php echo ( $filter_type == $at->incident_type ) ? 'selected' : ''; ?>><?php echo html_escape( $at->incident_type ); ?></option>
            <?php }
            } ?>
          </select>
          <select class="form-control form-control-sm mr-2 mb-1" name="shop" id="shopFilter">
            <option value="">All Shops</option>
            <?php if ( ! empty( $all_shops ) ) {
              foreach ( $all_shops as $as ) { ?>
                <option value="<?php echo html_escape( $as->shop ); ?>" <?php echo ( $filter_shop == $as->shop ) ? 'selected' : ''; ?>><?php echo html_escape( $as->shop ); ?></option>
            <?php }
            } ?>
          </select>
          <input type="date" class="form-control form-control-sm mr-1 mb-1" name="from" value="<?php echo html_escape( $date_from ); ?>" title="From date">
          <span class="mr-1 mb-1">to</span>
          <input type="date" class="form-control form-control-sm mr-2 mb-1" name="to" value="<?php echo html_escape( $date_to ); ?>" title="To date">
          <button type="submit" class="btn btn-primary btn-sm mr-2 mb-1"><i class="fas fa-filter"></i> Apply</button>
          <a href="<?php echo env_url( 'admin/ticket_analytics' ); ?>" class="btn btn-secondary btn-sm mb-1">Reset</a>
        </form>
      </div>
    </div>
    
    <!-- Active Filters Display -->
    <?php if ( ! empty( $filter_area ) || ! empty( $filter_type ) || ! empty( $filter_shop ) || ! empty( $date_from ) || ! empty( $date_to ) ) { ?>
      <div class="mb-2">
        <small class="text-muted">Active filters: </small>
        <?php if ( ! empty( $filter_area ) ) { ?><span class="badge badge-primary mr-1">Area: <?php echo html_escape( $filter_area ); ?></span><?php } ?>
        <?php if ( ! empty( $filter_type ) ) { ?><span class="badge badge-success mr-1">Type: <?php echo html_escape( $filter_type ); ?></span><?php } ?>
        <?php if ( ! empty( $filter_shop ) ) { ?><span class="badge badge-info mr-1">Shop: <?php echo html_escape( $filter_shop ); ?></span><?php } ?>
        <?php if ( ! empty( $date_from ) || ! empty( $date_to ) ) { ?><span class="badge badge-secondary">Date: <?php echo html_escape( $date_from ); ?> — <?php echo html_escape( $date_to ); ?></span><?php } ?>
      </div>
    <?php } ?>
    
    <!-- Summary Cards -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3><?php echo intval( $summary->total ); ?></h3>
            <p>Total Tickets</p>
          </div>
          <div class="icon"><i class="fas fa-ticket-alt"></i></div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3><?php echo intval( $summary->open_count ); ?></h3>
            <p>Open Tickets</p>
          </div>
          <div class="icon"><i class="fas fa-envelope-open"></i></div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
          <div class="inner">
            <h3><?php echo intval( $summary->closed_count ); ?></h3>
            <p>Closed Tickets</p>
          </div>
          <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3><?php echo intval( $summary->unassigned ); ?></h3>
            <p>Unassigned</p>
          </div>
          <div class="icon"><i class="fas fa-user-slash"></i></div>
        </div>
      </div>
    </div>
    
    <!-- Charts Row 1 -->
    <div class="row">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Issues by Area</h3></div>
          <div class="card-body"><canvas id="areaChart" height="250"></canvas></div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-tools"></i> Issues by Incident Type</h3></div>
          <div class="card-body"><canvas id="typeChart" height="250"></canvas></div>
        </div>
      </div>
    </div>
    
    <!-- Charts Row 2 -->
    <div class="row">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-store"></i> Top 10 Shops by Ticket Volume</h3></div>
          <div class="card-body"><canvas id="shopChart" height="300"></canvas></div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-line"></i> Tickets Over Time</h3></div>
          <div class="card-body"><canvas id="trendChart" height="300"></canvas></div>
        </div>
      </div>
    </div>
    
    <!-- Repeat Offender Shops -->
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-danger">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-exclamation-triangle text-danger"></i> Repeat Offender Shops</h3>
            <small class="text-muted ml-2">(3+ tickets — grouped by shop, incident type, and what's failing)</small>
          </div>
          <div class="card-body p-0">
            <?php if ( ! empty( $repeat_data ) ) { ?>
              <table class="table table-sm table-striped mb-0">
                <thead>
                  <tr>
                    <th>Shop</th>
                    <th>Incident Type</th>
                    <th>Top Issue</th>
                    <th class="text-center">Count</th>
                    <th>Severity</th>
                    <th>Action Needed</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ( $repeat_data as $r ) {
                    $count = intval( $r->count );
                    if ( $count >= 20 ) { $sev_badge = 'badge-danger'; $sev = 'Critical'; $action = 'Replace equipment'; $act_badge = 'badge-danger'; }
                    else if ( $count >= 10 ) { $sev_badge = 'badge-warning'; $sev = 'High'; $action = 'Investigate root cause'; $act_badge = 'badge-warning'; }
                    else if ( $count >= 5 ) { $sev_badge = 'badge-info'; $sev = 'Medium'; $action = 'Investigate root cause'; $act_badge = 'badge-info'; }
                    else { $sev_badge = 'badge-secondary'; $sev = 'Low'; $action = 'Monitor'; $act_badge = 'badge-secondary'; }
                  ?>
                    <tr>
                      <td><?php echo html_escape( $r->shop ); ?></td>
                      <td><span class="badge badge-secondary"><?php echo html_escape( $r->incident_type ); ?></span></td>
                      <td><?php echo html_escape( $r->top_issue ); ?></td>
                      <td class="text-center">
                        <a href="<?php echo env_url( 'admin/tickets/all?shop=' . urlencode( $r->shop ) . '&incident_type=' . urlencode( $r->incident_type ) ); ?>" class="font-weight-bold">
                          <?php echo $count; ?>
                        </a>
                      </td>
                      <td><span class="badge <?php echo $sev_badge; ?>"><?php echo $sev; ?></span></td>
                      <td><span class="badge <?php echo $act_badge; ?>"><?php echo $action; ?></span></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            <?php } else { ?>
              <div class="text-center text-muted py-4">No repeat offenders found for this period.</div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>

<!-- Chart.js -->
<script src="<?php admin_lte_asset( 'plugins/chart.js/Chart.min.js' ); ?>"></script>
<script>
$(document).ready(function() {
    var areaColors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1'];
    var typeColors = ['#007bff', '#28a745', '#ffc107', '#dc3545'];
    
    var areaData = <?php echo $area_data; ?>;
    new Chart(document.getElementById('areaChart').getContext('2d'), {
        type: 'horizontalBar',
        data: {
            labels: areaData.map(function(d) { return d.area; }),
            datasets: [{ label: 'Tickets', data: areaData.map(function(d) { return parseInt(d.count); }), backgroundColor: areaData.map(function(d, i) { return areaColors[i % areaColors.length]; }) }]
        },
        options: { responsive: true, legend: { display: false }, scales: { xAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
    });
    
    var typeData = <?php echo $type_data; ?>;
    new Chart(document.getElementById('typeChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: typeData.map(function(d) { return d.incident_type + ' (' + d.count + ')'; }),
            datasets: [{ data: typeData.map(function(d) { return parseInt(d.count); }), backgroundColor: typeColors.slice(0, typeData.length) }]
        },
        options: { responsive: true, legend: { position: 'bottom' } }
    });
    
    var shopData = <?php echo $shop_data; ?>;
    var shopColors = shopData.map(function(d) {
        if (d.shop.indexOf('Accra') === 0) return '#007bff';
        if (d.shop.indexOf('Kumasi') === 0) return '#28a745';
        if (d.shop.indexOf('Takoradi') === 0) return '#ffc107';
        return '#6c757d';
    });
    new Chart(document.getElementById('shopChart').getContext('2d'), {
        type: 'horizontalBar',
        data: {
            labels: shopData.map(function(d) { return d.shop; }),
            datasets: [{ label: 'Tickets', data: shopData.map(function(d) { return parseInt(d.count); }), backgroundColor: shopColors }]
        },
        options: { responsive: true, legend: { display: false }, scales: { xAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
    });
    
    var trendData = <?php echo $trend_data; ?>;
    var monthLabels = trendData.map(function(d) {
        var parts = d.month.split('-');
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[parseInt(parts[1]) - 1] + ' ' + parts[0];
    });
    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{ label: 'Tickets', data: trendData.map(function(d) { return parseInt(d.count); }), borderColor: '#007bff', backgroundColor: 'rgba(0, 123, 255, 0.1)', fill: true, tension: 0.3 }]
        },
        options: { responsive: true, legend: { display: false }, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
    });
});
</script>
