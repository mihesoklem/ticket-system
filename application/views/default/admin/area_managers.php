<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="not-in-form">
          <div class="response-message"></div>
        </div>

        <!-- Area Managers Card -->
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title">Area Managers</h3>
            <div class="card-tools ml-auto">
              <button class="btn btn-success text-sm" data-toggle="modal" data-target="#add-area-manager">
                <i class="fas fa-plus-circle mr-2"></i> Add Area Manager
              </button>
            </div>
          </div>
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th class="th-1">#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Assigned Shops</th>
                    <th class="text-right th-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ( ! empty( $managers ) ) : ?>
                    <?php foreach ( $managers as $manager ) : ?>
                      <tr id="record-<?php echo html_escape( $manager->id ); ?>">
                        <td><?php echo html_escape( $manager->id ); ?></td>
                        <td><strong><?php echo html_escape( $manager->name ); ?></strong></td>
                        <td><?php echo html_escape( $manager->email ); ?></td>
                        <td>
                          <?php if ( ! empty( $manager->shops ) ) : ?>
                            <?php foreach ( $manager->shops as $shop ) : ?>
                              <span class="badge badge-info mr-1 mb-1">
                                <?php echo html_escape( $shop->shop_name ); ?>
                                <form method="post" action="<?php admin_action( 'area_managers/unassign_shop' ); ?>" style="display:inline;">
                  <input type="hidden" name="z_csrf" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                  <input type="hidden" name="id" value="<?php echo html_escape( $shop->id ); ?>">
                                  <button type="submit" class="btn btn-xs p-0 ml-1 text-white" title="Remove" onclick="return confirm('Remove this shop assignment?');">
                                    <i class="fas fa-times"></i>
                                  </button>
                                </form>
                              </span>
                            <?php endforeach; ?>
                          <?php else : ?>
                            <span class="text-muted">No shops assigned</span>
                          <?php endif; ?>
                        </td>
                        <td class="text-right">
                          <button class="btn btn-sm btn-primary mr-1" data-toggle="modal" data-target="#edit-area-manager-<?php echo html_escape( $manager->id ); ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-sm btn-info mr-1" data-toggle="modal" data-target="#assign-shop-<?php echo html_escape( $manager->id ); ?>">
                            <i class="fas fa-store"></i> Assign Shop
                          </button>
                          <form method="post" action="<?php admin_action( 'area_managers/delete' ); ?>" style="display:inline;">
                  <input type="hidden" name="z_csrf" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="id" value="<?php echo html_escape( $manager->id ); ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this area manager?');">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        </td>
                      </tr>

                      <!-- Edit Modal for this manager -->
                      <div class="modal fade" id="edit-area-manager-<?php echo html_escape( $manager->id ); ?>" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <form method="post" action="<?php admin_action( 'area_managers/update' ); ?>">
                  <input type="hidden" name="z_csrf" value="<?php echo $this->security->get_csrf_hash(); ?>">
                              <div class="modal-header">
                                <h5 class="modal-title">Edit Area Manager</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                              </div>
                              <div class="modal-body">
                                <input type="hidden" name="id" value="<?php echo html_escape( $manager->id ); ?>">
                                <div class="form-group">
                                  <label>Name</label>
                                  <input type="text" name="name" class="form-control" value="<?php echo html_escape( $manager->name ); ?>" required>
                                </div>
                                <div class="form-group">
                                  <label>Email Address</label>
                                  <input type="email" name="email" class="form-control" value="<?php echo html_escape( $manager->email ); ?>" required>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>

                      <!-- Assign Shop Modal for this manager -->
                      <div class="modal fade" id="assign-shop-<?php echo html_escape( $manager->id ); ?>" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <form method="post" action="<?php admin_action( 'area_managers/assign_shop' ); ?>">
                  <input type="hidden" name="z_csrf" value="<?php echo $this->security->get_csrf_hash(); ?>">
                              <div class="modal-header">
                                <h5 class="modal-title">Assign Shop to <?php echo html_escape( $manager->name ); ?></h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                              </div>
                              <div class="modal-body">
                                <input type="hidden" name="manager_id" value="<?php echo html_escape( $manager->id ); ?>">
                                <div class="form-group">
                                  <label>Select Shops</label>
                                  <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all-shops">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-shops">Deselect All</button>
                                  </div>
                                  <select name="shop_names[]" class="form-control" multiple size="10" required>
                                    <?php if ( ! empty( $all_shops ) ) : ?>
                                      <?php foreach ( $all_shops as $shop ) : ?>
                                        <option value="<?php echo html_escape( $shop ); ?>"><?php echo html_escape( $shop ); ?></option>
                                      <?php endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                  <small class="text-muted">Hold Ctrl/Cmd to select multiple shops.</small>
                                  <?php if ( empty( $all_shops ) ) : ?>
                                    <small class="text-muted">No shops available.</small>
                                  <?php endif; ?>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Assign</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>

                    <?php endforeach; ?>
                  <?php else : ?>
                    <tr id="record-0">
                      <td colspan="5" class="text-center">No area managers found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Add Area Manager Modal -->
<div class="modal fade" id="add-area-manager" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="post" action="<?php admin_action( 'area_managers/add' ); ?>">
                  <input type="hidden" name="z_csrf" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add Area Manager</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter manager name" required>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Add Manager</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.select-all-shops').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var select = this.closest('.form-group').querySelector('select');
            for (var i = 0; i < select.options.length; i++) {
                select.options[i].selected = true;
            }
        });
    });
    document.querySelectorAll('.deselect-all-shops').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var select = this.closest('.form-group').querySelector('select');
            for (var i = 0; i < select.options.length; i++) {
                select.options[i].selected = false;
            }
        });
    });
});
</script>