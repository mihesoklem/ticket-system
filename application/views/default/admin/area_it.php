<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="response-message"><?php echo alert_message(); ?></div>
        
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Area IT Configuration</h3>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAreaModal">
              <i class="fas fa-plus"></i> Add Area
            </button>
          </div>
          <div class="card-body">
            <?php if ( ! empty( $areas ) ) { ?>
              <?php foreach ( $areas as $area ) { ?>
                <div class="card mb-3">
                  <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt text-primary"></i>
                        <?php echo html_escape( $area->area_prefix ); ?>
                      </h5>
                      <small class="text-muted">
                        Support Email: <?php echo html_escape( $area->support_email ); ?>
                      </small>
                    </div>
                    <div>
                      <button class="btn btn-info btn-xs edit-area-btn" 
                              data-id="<?php echo html_escape( $area->id ); ?>"
                              data-prefix="<?php echo html_escape( $area->area_prefix ); ?>"
                              data-email="<?php echo html_escape( $area->support_email ); ?>"
                              data-toggle="modal" data-target="#editAreaModal">
                        <i class="fas fa-edit"></i> Edit
                      </button>
                      <button class="btn btn-success btn-xs add-staff-btn"
                              data-id="<?php echo html_escape( $area->id ); ?>"
                              data-prefix="<?php echo html_escape( $area->area_prefix ); ?>"
                              data-toggle="modal" data-target="#addStaffModal">
                        <i class="fas fa-user-plus"></i> Add Staff
                      </button>
                    </div>
                  </div>
                  <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Email</th>
                          <th width="100">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ( ! empty( $area->staff ) ) { ?>
                          <?php foreach ( $area->staff as $staff ) { ?>
                            <tr>
                              <td><?php echo html_escape( $staff->first_name . ' ' . $staff->last_name ); ?></td>
                              <td><?php echo html_escape( $staff->email_address ); ?></td>
                              <td>
                                <form action="<?php admin_action( 'area_it/remove_staff' ); ?>" method="post" style="display:inline;" onsubmit="return confirm('Remove this IT staff from the area?');">
                                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                  <input type="hidden" name="assignment_id" value="<?php echo html_escape( $staff->assignment_id ); ?>">
                                  <button type="submit" class="btn btn-danger btn-xs">
                                    <i class="fas fa-trash"></i> Remove
                                  </button>
                                </form>
                              </td>
                            </tr>
                          <?php } ?>
                        <?php } else { ?>
                          <tr><td colspan="3" class="text-center text-muted">No IT staff assigned to this area</td></tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              <?php } ?>
            <?php } else { ?>
              <div class="text-center text-muted py-5">No areas configured yet.</div>
            <?php } ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Add Area Modal -->
<div class="modal fade" id="addAreaModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php admin_action( 'area_it/add_area' ); ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add New Area</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Area Prefix <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="area_prefix" placeholder="e.g. Accra, Kumasi, Tadi" required>
            <small class="form-text text-muted">Must match the prefix used in Shop Names (e.g. "Accra" for "Accra - Achimota")</small>
          </div>
          <div class="form-group">
            <label>Support Email</label>
            <input type="email" class="form-control" name="support_email" placeholder="e.g. support.accra@kmkentertainment.com">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Area</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Area Modal -->
<div class="modal fade" id="editAreaModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php admin_action( 'area_it/update_area' ); ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="area_id" id="edit-area-id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Area</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Area Prefix <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="area_prefix" id="edit-area-prefix" required>
          </div>
          <div class="form-group">
            <label>Support Email</label>
            <input type="email" class="form-control" name="support_email" id="edit-area-email">
          </div>
        </div>
        <div class="modal-footer">
          <form action="<?php admin_action( 'area_it/delete_area' ); ?>" method="post" style="display:inline; margin-right:auto;">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="area_id" id="delete-area-id">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this area and all its staff assignments?');">Delete Area</button>
          </form>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php admin_action( 'area_it/add_staff' ); ?>" method="post">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="area_id" id="add-staff-area-id">
        <div class="modal-header">
          <h5 class="modal-title">Add IT Staff to <span id="add-staff-area-name"></span></h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Select Team Member <span class="text-danger">*</span></label>
            <select class="form-control select2" name="user_id" required style="width:100%;">
              <option value="">-- Select --</option>
              <?php if ( ! empty( $team_users ) ) { ?>
                <?php foreach ( $team_users as $user ) { ?>
                  <option value="<?php echo html_escape( $user->id ); ?>">
                    <?php echo html_escape( $user->first_name . ' ' . $user->last_name . ' (' . $user->email_address . ')' ); ?>
                  </option>
                <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Staff</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    // Edit Area modal
    $('.edit-area-btn').on('click', function() {
        $('#edit-area-id').val($(this).data('id'));
        $('#delete-area-id').val($(this).data('id'));
        $('#edit-area-prefix').val($(this).data('prefix'));
        $('#edit-area-email').val($(this).data('email'));
    });
    
    // Add Staff modal
    $('.add-staff-btn').on('click', function() {
        $('#add-staff-area-id').val($(this).data('id'));
        $('#add-staff-area-name').text($(this).data('prefix'));
    });
});
</script>
