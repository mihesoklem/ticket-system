<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header" style="overflow: visible; padding-right: 30px; width: 100%; box-sizing: border-box;">
            <div class="d-flex align-items-center" style="gap: 15px; flex-wrap: nowrap; width: 100%; min-width: 0;">
              <h3 class="card-title m-0"><?php echo lang( 'agent_performance' ); ?></h3>
              <div class="btn-group" role="group" style="display: flex; gap: 0; flex-wrap: nowrap; margin-left: auto; font-size: 0.8rem;">
                <a href="<?php echo env_url( 'admin/agent-performance?period=0' ); ?>" class="btn btn-sm btn-<?php echo ( $period === 0 && ! isset( $is_custom ) || ( isset( $is_custom ) && ! $is_custom ) ) ? 'primary' : 'secondary'; ?>" style="padding: 0.2rem 0.35rem; font-size: 0.8rem; white-space: nowrap;" title="All Time">
                  All
                </a>
                <a href="<?php echo env_url( 'admin/agent-performance?period=1' ); ?>" class="btn btn-sm btn-<?php echo ( $period === 1 ) ? 'primary' : 'secondary'; ?>" style="padding: 0.2rem 0.35rem; font-size: 0.8rem; white-space: nowrap;" title="Past 3 Days">
                  3D
                </a>
                <a href="<?php echo env_url( 'admin/agent-performance?period=2' ); ?>" class="btn btn-sm btn-<?php echo ( $period === 2 ) ? 'primary' : 'secondary'; ?>" style="padding: 0.2rem 0.35rem; font-size: 0.8rem; white-space: nowrap;" title="Past 7 Days">
                  7D
                </a>
                <a href="<?php echo env_url( 'admin/agent-performance?period=4' ); ?>" class="btn btn-sm btn-<?php echo ( $period === 4 ) ? 'primary' : 'secondary'; ?>" style="padding: 0.2rem 0.35rem; font-size: 0.8rem; white-space: nowrap;" title="Past 1 Month">
                  1M
                </a>
                <button type="button" class="btn btn-sm btn-<?php echo ( isset( $is_custom ) && $is_custom ) ? 'primary' : 'secondary'; ?>" id="custom-range-btn" title="Custom Date Range" style="padding: 0.2rem 0.35rem; font-size: 0.8rem; white-space: nowrap;" onclick="document.getElementById('custom-range-section').style.display = document.getElementById('custom-range-section').style.display === 'none' ? 'block' : 'none';">
                  Custom
                </button>
              </div>
            </div>
          </div>

          <!-- Custom Date Range Filter -->
          <div id="custom-range-section" class="card-body border-bottom pt-3 pb-3" style="background-color: #f8f9fa; display: <?php echo ( isset( $is_custom ) && $is_custom ) ? 'block' : 'none'; ?>;">
            <form method="get" class="form-inline" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
              <div class="form-group mb-0">
                <label for="from_date" style="margin-right: 5px;"><?php echo lang( 'from_date' ) ?? 'From Date'; ?>:</label>
                <input type="date" id="from_date" name="from_date" class="form-control" value="<?php echo html_escape( isset( $from_date ) ? $from_date : '' ); ?>" style="width: 150px;">
              </div>
              <div class="form-group mb-0">
                <label for="to_date" style="margin-right: 5px;"><?php echo lang( 'to_date' ) ?? 'To Date'; ?>:</label>
                <input type="date" id="to_date" name="to_date" class="form-control" value="<?php echo html_escape( isset( $to_date ) ? $to_date : '' ); ?>" style="width: 150px;">
              </div>
              <button type="submit" class="btn btn-sm btn-primary"><?php echo lang( 'filter' ) ?? 'Filter'; ?></button>
              <?php if ( isset( $is_custom ) && $is_custom ): ?>
              <a href="<?php echo env_url( 'admin/agent-performance' ); ?>" class="btn btn-sm btn-secondary"><?php echo lang( 'clear' ) ?? 'Clear'; ?></a>
              <?php endif; ?>
            </form>
            <?php if ( isset( $is_custom ) && $is_custom ): ?>
            <div style="margin-top: 10px; font-size: 12px; color: #666;">
              <?php echo lang( 'filtered' ) ?? 'Filtered'; ?>: <strong><?php echo $period_label; ?></strong>
            </div>
            <?php endif; ?>
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
                    <th class="text-center"><?php echo lang( 'reopened' ); ?></th>
                    <th class="text-center"><?php echo lang( 'open' ); ?></th>
                    <th class="text-center"><?php echo lang( 'closure_rate' ); ?></th>
                    <th class="text-center"><?php echo lang( 'reopened_rate' ); ?></th>
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
                      <?php if ( $agent->reopened > 0 ): ?>
                        <a href="<?php echo env_url( 'admin/tickets/all?assigned_to=' . $agent->id . '&reopened_awaiting=1' ); ?>" target="_blank" title="View reopened tickets for this agent">
                          <span class="badge badge-danger" style="cursor: pointer;"><?php echo intval( $agent->reopened ); ?></span>
                        </a>
                      <?php else: ?>
                        <span class="badge badge-danger">0</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-warning"><?php echo intval( $agent->open ); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-primary"><?php echo number_format( $agent->closure_rate, 1 ) . '%'; ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-danger"><?php echo number_format( $agent->reopened_rate, 1 ) . '%'; ?></span>
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
                      <?php if ( $summary->total_reopened > 0 ): ?>
                        <a href="<?php echo env_url( 'admin/tickets/all?reopened_awaiting=1' ); ?>" target="_blank" title="View all reopened tickets">
                          <span class="badge badge-danger" style="cursor: pointer;"><?php echo intval( $summary->total_reopened ); ?></span>
                        </a>
                      <?php else: ?>
                        <span class="badge badge-danger">0</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-warning"><?php echo intval( $summary->total_open ); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-primary"><?php echo number_format( $summary->closure_rate, 1 ) . '%'; ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-danger"><?php echo number_format( $summary->reopened_rate, 1 ) . '%'; ?></span>
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
