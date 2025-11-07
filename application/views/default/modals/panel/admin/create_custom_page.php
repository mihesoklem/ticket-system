<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!-- Create Custom Page Modal: -->
<div class="modal" id="create-custom-page">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form class="z-form" action="<?php admin_action( 'pages/create_custom_page' ); ?>" method="post">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo lang( 'create_custom_page' ); ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- /.modal-header -->
        <div class="modal-body">
          <div class="response-message"></div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="name"><?php echo lang( 'name' ); ?> <span class="required">*</span></label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <!-- /.form-group -->
            <div class="form-group col-md-6">
              <label for="slug">
                <?php echo lang( 'slug' ); ?>
                <i class="fas fa-info-circle text-sm" data-toggle="tooltip" title="<?php echo lang( 'slug_tip' ); ?>"></i>
              </label>
              <input type="text" class="form-control" id="slug" name="slug">
            </div>
            <!-- /.form-group -->
          </div>
          <!-- /.form-row -->
          <div class="form-group">
            <label for="textarea"><?php echo lang( 'content' ); ?> <span class="required">*</span></label>
            <textarea class="form-control textarea" id="textarea" name="content"></textarea>
          </div>
          <!-- /.form-group -->
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="visibility"><?php echo lang( 'visibility' ); ?></label>
              <select id="visibility" class="form-control select2 search-disabled" name="visibility">
                <option value="1"><?php echo lang( 'public' ); ?></option>
                <option value="0"><?php echo lang( 'hidden' ); ?></option>
              </select>
            </div>
            <!-- /.form-group -->
            <div class="form-group col-md-6">
              <label for="meta-keywords"><?php echo lang( 'meta_keywords' ); ?></label>
              <input type="text" class="form-control" id="meta-keywords" name="meta_keywords">
            </div>
            <!-- /.form-group -->
          </div>
          <!-- /.form-row -->
          <label for="meta-description"><?php echo lang( 'meta_description' ); ?></label>
          <textarea class="form-control" id="meta-description" name="meta_description" rows="2"></textarea>
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
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->