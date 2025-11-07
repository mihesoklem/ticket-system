<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="not-in-form">
          <div class="response-message"><?php echo alert_message(); ?></div>
        </div>
        <!-- /.not-in-form -->
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title"><?php echo lang( 'custom_pages' ); ?></h3>
            <div class="card-tools ml-auto">
              <button class="btn btn-success text-sm" data-toggle="modal" data-target="#create-custom-page">
                <i class="fas fa-plus-circle mr-2"></i> <?php echo lang( 'create_custom_page' ); ?>
              </button>
            </div>
            <!-- /.card-tools -->
          </div>
          <!-- /.card-header -->
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th class="th-1"><?php echo lang( 'id' ); ?></th>
                    <th><?php echo lang( 'name' ); ?></th>
                    <th class="text-right"><?php echo lang( 'visibility' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'creator' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'updated' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'created' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'action' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody text-sm">
                  <?php
                  if ( ! empty( $pages ) )
                  {
                    foreach ( $pages as $page ) { ?>
                      <tr>
                        <td><?php echo html_escape( $page->id ); ?></td>
                        <td>
                          <?php echo html_escape( $page->name ); ?>
                          <span class="text-muted text-sm d-block"><?php echo '/page/' . html_escape( $page->slug ); ?></span>
                        </td>
                        <td class="text-right">
                          <?php
                          if ( $page->visibility == 1 )
                          {
                              echo '<span class="badge badge-success">' . lang( 'public' ) . '</span>';
                          }
                          else
                          {
                              echo '<span class="badge badge-danger">' . lang( 'hidden' ) . '</span>';
                          }
                          ?>
                        </td>
                        <td class="text-right">
                          <?php
                          if ( ! empty( $page->first_name ) )
                          {
                              if ( $this->zuser->has_permission( 'users' ) )
                              {
                                  echo '<a href="' . env_url( 'admin/users/edit_user/' . html_escape( $page->created_by ) ) . '" target="_blank">';
                                  echo html_escape( $page->first_name . ' ' . $page->last_name );
                                  echo '</a>';
                              }
                              else
                              {
                                  echo html_escape( $page->first_name . ' ' . $page->last_name );
                              }
                          }
                          else
                          {
                              echo lang( 'user_deleted' );
                          }
                          ?>
                        </td>
                        <td class="text-right"><?php manage_updated_at( html_escape( $page->updated_at ) ); ?></td>
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $page->created_at ) ); ?></td>
                        <td class="text-right">
                          <div class="btn-group">
                            <a href="<?php echo env_url( 'page/' . html_escape( $page->slug ) ); ?>" class="btn btn-sm btn-info" target="_blank">
                              <span class="fas fa-eye"></span>
                            </a>
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#edit-custom-page-<?php echo html_escape( $page->id ); ?>">
                              <span class="fas fa-edit"></span>
                            </button>
                            <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $page->id ); ?>" data-toggle="modal" data-target="#delete">
                              <i class="fas fa-trash tool-c"></i>
                            </button>
                          </div>
                          <!-- /.btn-group -->
                        </td>
                      </tr>
                      <?php }
                    } else {
                  ?>
                    <tr>
                      <td colspan="7"><?php echo lang( 'no_records_found' ); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <!-- /.table-responsive -->
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php load_modals( ['admin/create_custom_page', 'admin/custom_pages', 'delete'] ); ?>