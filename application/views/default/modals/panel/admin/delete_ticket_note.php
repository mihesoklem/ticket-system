<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<?php if ( ! empty( $notes ) ) {
  foreach ( $notes as $note ) {
    $id = $note->id; ?>
  <div class="modal" id="delete-ticket-note-<?php echo intval( $id ); ?>">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <form class="z-form" action="<?php echo admin_action( 'support/delete_ticket_note' ); ?>" method="post">
          <div class="modal-body">
            <div class="response-message"></div>
            <p><?php echo lang( 'sure_delete' ); ?></p>
          </div>
          <!-- /.modal-body -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary text-sm" data-dismiss="modal">
              <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'no' ); ?>
            </button>
            <button type="submit" class="btn btn-danger text-sm">
              <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'yes' ); ?>
            </button>
            <input type="hidden" name="id" value="<?php echo intval( $id ); ?>">
          </div>
          <!-- /.modal-footer -->
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
<?php } } ?>