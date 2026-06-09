<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="response-message"><?php echo alert_message(); ?></div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <form class="z-form" action="<?php admin_action( 'support/create_ticket' ); ?>" method="post" enctype="multipart/form-data" data-csrf="manual">
          <div class="response-message"></div>
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title"><?php echo lang( 'create_ticket' ); ?></h3>
            </div>
            <div class="card-body">
              
              <!-- Quick Issue Buttons (New) -->
              <?php if ( ! empty( $quick_issues ) ) { ?>
              <div class="mb-4" id="quick-issue-section">
                <label class="font-weight-bold"><?php echo lang( 'quick_issue' ); ?></label>
                <div class="row g-2">
                  <?php foreach ( $quick_issues as $button ) { ?>
                    <div class="col-6 col-md-4">
                      <button type="button" class="btn btn-outline-secondary w-100 quick-issue-btn py-3"
                              data-label="<?php echo html_escape( $button->label ); ?>"
                              data-incident-type="<?php echo html_escape( $button->incident_type ); ?>"
                              data-priority="<?php echo html_escape( $button->priority ); ?>"
                              data-subject="<?php echo html_escape( $button->subject_template ); ?>"
                              data-message="<?php echo html_escape( $button->message_template ); ?>">
                        <?php echo html_escape( $button->label ); ?>
                      </button>
                    </div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="subject"><?php echo lang( 'subject' ); ?> <span class="required">*</span></label>
                  <input type="text" id="subject" class="form-control" name="subject" required>
                </div>
                <div class="form-group col-md-6">
                  <label for="priority"><?php echo lang( 'priority' ); ?> <span class="required">*</span></label>
                  <select id="priority" data-placeholder="<?php echo lang( 'choose_priority' ); ?>" class="form-control select2 search-disabled" name="priority" required>
                    <option></option>
                    <option value="low"><?php echo lang( 'low' ); ?></option>
                    <option value="medium"><?php echo lang( 'medium' ); ?></option>
                    <option value="high"><?php echo lang( 'high' ); ?></option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="department"><?php echo lang( 'department' ); ?> <span class="required">*</span></label>
                  <select id="department" data-placeholder="<?php echo lang( 'select_department' ); ?>" class="form-control select2 search-disabled" name="department" required>
                    <option></option>
                    <?php if ( ! empty( $departments ) ) {
                      foreach ( $departments as $department ) { ?>
                      <option value="<?php echo html_escape( $department->id ); ?>"><?php echo html_escape( $department->name ); ?></option>
                    <?php }
                    } ?>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <?php if ( get( 'type' ) == 'unregistered_users' ) { ?>
                    <label for="email-address">
                      <?php echo lang( 'email_address' ); ?> <span class="required">*</span>
                      <i class="fas fa-info-circle text-sm" data-toggle="tooltip" data-placement="top" title="<?php echo lang( 'customer_email_tip' ); ?>"></i>
                    </label>
                    <input type="email" id="email-address" class="form-control" name="email_address" required>
                  <?php } else { ?>
                    <label for="customer"><?php echo lang( 'customer' ); ?> <span class="required">*</span></label>
                    <select id="customer" data-placeholder="<?php echo lang( 'select_customer' ); ?>" class="form-control select2" name="customer" required>
                      <option></option>
                      <?php if ( ! empty( $customers ) ) {
                        foreach ( $customers as $customer ) { ?>
                        <option value="<?php echo html_escape( $customer->id ); ?>"><?php echo html_escape( $customer->first_name . ' ' . $customer->last_name ); ?> ( <?php echo html_escape( $customer->username ); ?> )</option>
                      <?php }
                      } ?>
                    </select>
                  <?php } ?>
                </div>
              </div>
              
              <?php load_view( 'common/custom_fields' ); ?>
              
              <div class="form-group">
                <label for="message"><?php echo lang( 'description' ); ?> <span class="required">*</span></label>
                <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
              </div>
              
              <label for="attachment"><?php echo lang( 'attach_files' ); ?></label>
              <input type="file" class="d-block" id="attachment" name="attachment" accept="<?php echo ALLOWED_ATTACHMENTS_EXT_HTML; ?>" multiple="true">
              <small id="attachment-guide" class="form-text text-muted"><?php echo lang( 'attach_file_tip' ); ?></small>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary float-right text-sm">
                <i class="fas fa-check-circle mr-2"></i> <?php echo lang( 'submit' ); ?>
              </button>
            </div>
          </div>
          <input type="hidden" name="type" value="<?php echo html_escape( get( 'type' ) ); ?>">
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    var subjectInput = document.getElementById('subject');
    var messageTextarea = document.getElementById('message');
    var prioritySelect = document.getElementById('priority');
    var now = new Date();
    var timestampStr = now.getFullYear() + '-' + 
                       String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(now.getDate()).padStart(2, '0') + ' ' + 
                       String(now.getHours()).padStart(2, '0') + ':' + 
                       String(now.getMinutes()).padStart(2, '0');
    
    $('.quick-issue-btn').on('click', function() {
        var btn = $(this);
        var label = btn.data('label');
        var priority = btn.data('priority');
        var subjectTpl = btn.data('subject');
        var messageTpl = btn.data('message');
        
        $('.quick-issue-btn').removeClass('btn-sub').addClass('btn-outline-secondary');
        btn.removeClass('btn-outline-secondary').addClass('btn-sub');
        
        if (subjectTpl) {
            var subject = subjectTpl.replace('{label}', label);
            subjectInput.value = subject;
        }
        
        if (messageTpl) {
            var message = messageTpl.replace('{label}', label).replace('{timestamp}', timestampStr);
            messageTextarea.value = message;
        }
        
        if (priority) {
            prioritySelect.value = priority;
            $(prioritySelect).trigger('change');
        }
    });
    
    function detectPriority() {
        var text = (subjectInput.value + ' ' + messageTextarea.value).toLowerCase();
        var highKeywords = ['wifi', 'internet', 'connection', 'offline', 'network', 'router',
                             'pos', 'frozen', 'crash', 'system down', 'not loading',
                             'login', 'password', 'locked out', 'cannot access',
                             'cash', 'payment', 'deposit', 'register', 'till',
                             'urgent', 'critical', 'shop closed', 'not trading'];
        
        for (var i = 0; i < highKeywords.length; i++) {
            if (text.indexOf(highKeywords[i]) !== -1) {
                if (prioritySelect.value !== 'high') {
                    prioritySelect.value = 'high';
                    $(prioritySelect).trigger('change');
                }
                return;
            }
        }
    }
    
    $(subjectInput).on('input blur', detectPriority);
    $(messageTextarea).on('input blur', detectPriority);
});
</script>