<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!-- Add Ticket Note Modal: -->
<div class="modal close-after" id="add-ticket-note">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form class="z-form" action="<?php admin_action( 'support/add_ticket_note' ); ?>" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo lang( 'add_note' ); ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- /.modal-header -->
        <div class="modal-body">
          <div class="response-message"></div>
          <div class="form-group">
            <label for="note-add"><?php echo lang( 'message' ); ?> <span class="required">*</span></label>
            <textarea class="form-control" id="note-add" name="note" rows="5" required></textarea>
          </div>
          <!-- /.form-group -->
          <label for="attachment-add"><?php echo lang( 'attachment' ); ?></label>
          <input type="file" class="d-block" id="attachment-add" name="attachment" accept="<?php echo ALLOWED_ATTACHMENTS_EXT_HTML; ?>">
          <small class="form-text text-muted"><?php echo lang( 'attach_file_tip' ); ?></small>
        </div>
        <!-- /.modal-body -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary text-sm" data-dismiss="modal">
            <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'close' ); ?>
          </button>
          <button type="submit" class="btn btn-primary text-sm">
            <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'submit' ); ?>
          </button>
        </div>
        <!-- /.modal-footer -->
        <input type="hidden" name="id" value="<?php echo intval( $ticket->id ); ?>">
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->