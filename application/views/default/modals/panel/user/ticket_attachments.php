<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!-- View Ticket Image Attachment ( User ): -->
<div class="modal" id="view-ticket-attachment">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img id="for-popup-attachment-img" class="img-fluid align-img-center" alt="Attachment">
      </div>
      <!-- /.modal-body -->
      <div class="modal-footer">
        <a id="popup-attachment-img-download" class="btn btn-primary btn-wide" download>
          <small><i class="fas fa-save me-2"></i> <?php echo lang( 'download' ); ?></small>
        </a>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
          <small><i class="fas fa-times-circle me-2"></i> <?php echo lang( 'close' ); ?></small>
        </a>
      </div>
      <!-- /.modal-footer -->
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->