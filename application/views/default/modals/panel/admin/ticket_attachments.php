<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!-- View Ticket Image Attachment ( Admin ): -->
<div class="modal" id="view-ticket-attachment">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img id="for-popup-attachment-img" class="img-fluid align-img-center" alt="Attachment">
      </div>
      <!-- /.modal-body -->
      <div class="modal-footer">
        <a id="popup-attachment-img-download" class="btn btn-primary text-sm" download>
          <i class="fas fa-save mr-2"></i> <?php echo lang( 'download' ); ?>
        </a>
        <button type="button" class="btn btn-danger text-sm" data-dismiss="modal">
          <i class="fas fa-times-circle mr-2"></i> <?php echo lang( 'close' ); ?>
        </a>
      </div>
      <!-- /.modal-footer -->
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->