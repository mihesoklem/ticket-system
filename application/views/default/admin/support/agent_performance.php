<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title"><?php echo lang( 'agent_performance' ); ?></h3>
            <div class="card-tools ml-auto">
              <div class="btn-group">
                <a href="<?php echo env_url( 'admin/agent-performance?period=0' ); ?>" class="btn btn-sm btn-<?php echo ( $period === 0 ) ? 'primary' : 'secondary'; ?>">
                  <?php echo lang( 'all_time' ); ?>
                </a>
                <a href="<?php echo env_url( 'admin/agent-performance?period=1' ); ?>" class="btn btn-sm btn-<?php echo ( $period === 1 ) ? 'primary' : 'secondary'; ?>">
                  <?php echo lang( 'past_3_days' ); ?>
                </a>
                <a href="<?php echo env_url( 'admin/agent-performance?period=2' ); ?>" class="btn btn-sm btn-<?php echo ( $period === 2 ) ? 'primary' : 'secondary'; ?>">
                  <?php echo lang( 'past_7_days' ); ?>
                </a>
                <a href="<?php echo env_url( 'admin/agent-performance?period=4' ); ?>" class="btn btn-sm btn-<?php echo ( $period === 4 ) ? 'primary' : 'secondary'; ?>">
                  <?php echo lang( 'past_1_month' ); ?>
                </a>
              </div>
            </div>
          </div>

          <div class="card-body pt-0 pb-0">
            <?php if ( ! empty( $agents ) ): ?>
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th><?php echo lang( 'agent_name' ); ?></th>
                    <th class="text-center"><?php echo lang( 'total_assigned' ); ?></th>
                    <th class="text-center"><?php echo lang( 'closed' ); ?></th>
                    <th class="text-center"><?php echo lang( 'open' ); ?></th>
                    <th class="text-center"><?php echo lang( 'closure_rate' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody text-sm">
                  <?php foreach ( $agents as $agent ): 
                    $row_class = '';
                    if ( $agent->closure_rate >= 85 ) {
                      $row_class = 'high-performer';
                    } elseif ( $agent->closure_rate < 70 ) {
                      $row_class = 'low-performer';
                    }
                  ?>
                  <tr class="<?php echo $row_class; ?>">
                    <td class="agent-name font-weight-bold">
                      <?php echo html_escape( $agent->first_name . ' ' . $agent->last_name ); ?>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-info"><?php echo intval( $agent->total_assigned ); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-success"><?php echo intval( $agent->closed ); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-warning"><?php echo intval( $agent->open ); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-primary"><?php echo number_format( $agent->closure_rate, 1 ) . '%'; ?></span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  
                  <!-- Summary Row -->
                  <tr class="summary-row font-weight-bold" style="background-color: #ecf0f1;">
                    <td><?php echo lang( 'total' ); ?></td>
                    <td class="text-center">
                      <span class="badge badge-info"><?php echo intval( $summary->total_assigned ); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-success"><?php echo intval( $summary->total_closed ); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-warning"><?php echo intval( $summary->total_open ); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-primary"><?php echo number_format( $summary->closure_rate, 1 ) . '%'; ?></span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info" role="alert">
              <?php echo lang( 'no_records_found' ); ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .high-performer {
    background-color: #d5f4e6 !important;
  }
  .low-performer {
    background-color: #fadbd8 !important;
  }
  .agent-name {
    color: #2c3e50;
  }
  .summary-row {
    border-top: 2px solid #999;
  }
</style>
