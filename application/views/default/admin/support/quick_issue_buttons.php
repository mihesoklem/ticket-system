<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="not-in-form">
          <div class="response-message"></div>
        </div>
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title">Quick Issue Buttons</h3>
            <div class="card-tools ml-auto">
              <button class="btn btn-success text-sm" data-toggle="modal" data-target="#add-quick-issue">
                <i class="fas fa-plus-circle mr-2"></i> <?php echo lang( 'add_new' ); ?>
              </button>
            </div>
          </div>
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th class="th-1"><?php echo lang( 'id' ); ?></th>
                    <th><?php echo lang( 'label' ); ?></th>
                    <th><?php echo lang( 'incident_type' ); ?></th>
                    <th><?php echo lang( 'priority' ); ?></th>
                    <th><?php echo lang( 'active' ); ?></th>
                    <th><?php echo lang( 'sort_order' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'actions' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody text-sm">
                  <?php
                  if ( ! empty( $buttons ) )
                  {
                    foreach ( $buttons as $button ) {
                      $id = $button->id; ?>
                      <tr id="record-<?php echo html_escape( $id ); ?>">
                        <td><?php echo html_escape( $id ); ?></td>
                        <td><?php echo html_escape( $button->label ); ?></td>
                        <td><?php echo html_escape( $button->incident_type ); ?></td>
                        <td>
                          <span class="badge badge-<?php echo $button->priority === 'high' ? 'danger' : ( $button->priority === 'medium' ? 'warning' : 'success' ); ?>">
                            <?php echo html_escape( ucfirst( $button->priority ) ); ?>
                          </span>
                        </td>
                        <td>
                          <span class="badge badge-<?php echo $button->is_active ? 'success' : 'secondary'; ?>">
                            <?php echo $button->is_active ? lang( 'yes' ) : lang( 'no' ); ?>
                          </span>
                        </td>
                        <td><?php echo html_escape( $button->sort_order ); ?></td>
                        <td class="text-right">
                          <button class="btn btn-xs btn-info" data-toggle="modal" data-target="#edit-quick-issue-<?php echo html_escape( $id ); ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                        </td>
                      </tr>
                  <?php }
                  } else { ?>
                    <tr>
                      <td colspan="7" class="text-center"><?php echo lang( 'no_records_found' ); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
