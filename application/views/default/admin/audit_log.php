<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="response-message"><?php echo alert_message(); ?></div>
        
        <div class="card">
          <div class="card-header d-flex align-items-center border-bottom-0">
            <h3 class="card-title"><i class="fas fa-history"></i> Audit Log</h3>
            <div class="card-tools ml-auto">
              <form action="<?php echo env_url( 'admin/audit_log' ); ?>" class="d-inline-block form-inline mr-2">
                <select class="form-control text-sm search-field mr-1 mb-2 mb-sm-0" name="user_id">
                  <option value="">All Users</option>
                  <?php if ( ! empty( $team_users ) ) {
                    foreach ( $team_users as $tu ) { ?>
                      <option value="<?php echo html_escape( $tu->id ); ?>" <?php echo ( $filter_user == $tu->id ) ? 'selected' : ''; ?>>
                        <?php echo html_escape( $tu->first_name . ' ' . $tu->last_name ); ?>
                      </option>
                  <?php }
                  } ?>
                </select>
                <select class="form-control text-sm search-field mr-1 mb-2 mb-sm-0" name="entity">
                  <option value="">All Entities</option>
                  <?php if ( ! empty( $entities ) ) {
                    foreach ( $entities as $e ) { ?>
                      <option value="<?php echo html_escape( $e->entity ); ?>" <?php echo ( $filter_entity == $e->entity ) ? 'selected' : ''; ?>>
                        <?php echo html_escape( $e->entity ); ?>
                      </option>
                  <?php }
                  } ?>
                </select>
                <button class="btn btn-primary text-sm btn-user-search" type="submit"><i class="fas fa-search"></i></button>
              </form>
            </div>
          </div>
          <div class="card-body p-0">
            <?php if ( ! empty( $logs ) ) { ?>
              <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                  <thead>
                    <tr>
                      <th width="160">Date & Time</th>
                      <th width="140">User</th>
                      <th width="100">Action</th>
                      <th width="140">Entity</th>
                      <th>Details</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ( $logs as $log ) { ?>
                      <tr>
                        <td><small><?php echo date( 'M d, Y H:i', $log->created_at ); ?></small></td>
                        <td>
                          <strong><?php echo html_escape( $log->user_name ); ?></strong>
                        </td>
                        <td>
                          <?php
                            $badge_class = 'badge-secondary';
                            if ( $log->action === 'create' ) $badge_class = 'badge-success';
                            else if ( $log->action === 'update' ) $badge_class = 'badge-info';
                            else if ( $log->action === 'delete' ) $badge_class = 'badge-danger';
                            else if ( $log->action === 'add_staff' ) $badge_class = 'badge-primary';
                            else if ( $log->action === 'remove_staff' ) $badge_class = 'badge-warning';
                          ?>
                          <span class="badge <?php echo $badge_class; ?>"><?php echo html_escape( $log->action ); ?></span>
                        </td>
                        <td><small class="text-muted"><?php echo html_escape( $log->entity ); ?></small></td>
                        <td><?php echo html_escape( $log->details ); ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
              
              <?php if ( $total_pages > 1 ) { ?>
                <div class="card-footer clearfix">
                  <ul class="pagination pagination-sm m-0 float-right">
                    <?php if ( $page > 1 ) { ?>
                      <li class="page-item">
                        <a class="page-link" href="<?php echo env_url( 'admin/audit_log?page=' . ( $page - 1 ) . ( $filter_user ? '&user_id=' . $filter_user : '' ) . ( $filter_entity ? '&entity=' . urlencode( $filter_entity ) : '' ) ); ?>">&laquo;</a>
                      </li>
                    <?php } ?>
                    <?php for ( $i = max( 1, $page - 2 ); $i <= min( $total_pages, $page + 2 ); $i++ ) { ?>
                      <li class="page-item <?php echo ( $i == $page ) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo env_url( 'admin/audit_log?page=' . $i . ( $filter_user ? '&user_id=' . $filter_user : '' ) . ( $filter_entity ? '&entity=' . urlencode( $filter_entity ) : '' ) ); ?>"><?php echo $i; ?></a>
                      </li>
                    <?php } ?>
                    <?php if ( $page < $total_pages ) { ?>
                      <li class="page-item">
                        <a class="page-link" href="<?php echo env_url( 'admin/audit_log?page=' . ( $page + 1 ) . ( $filter_user ? '&user_id=' . $filter_user : '' ) . ( $filter_entity ? '&entity=' . urlencode( $filter_entity ) : '' ) ); ?>">&raquo;</a>
                      </li>
                    <?php } ?>
                  </ul>
                  <small class="text-muted">Showing <?php echo count( $logs ); ?> of <?php echo $total; ?> entries</small>
                </div>
              <?php } ?>
              
            <?php } else { ?>
              <div class="text-center text-muted py-5">No audit log entries found.</div>
            <?php } ?>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>
