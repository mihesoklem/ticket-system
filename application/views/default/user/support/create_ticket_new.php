<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
$disabled = false;
?>
<div class="response-message no-radius"><?php echo alert_message(); ?></div>

<div class="z-page-form my-5 create-ticket extra-height-1">
  <form class="z-form" action="<?php user_action( 'support/create_ticket' ); ?>" method="post" enctype="multipart/form-data" data-csrf="manual">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="department" value="9">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          
          <?php if ( db_config( 'sp_verification_before_submit' ) && $this->zuser->get( 'is_verified' ) == 0 ) {
            $disabled = true; ?>
            <div class="alert alert-warning text-center"><?php echo lang( 'sp_everification_req' ); ?></div>
          <?php } else if ( db_config( 'create_ticket_page_message' ) && db_config( 'show_tp_message' ) == 1 ) { ?>
            <div class="alert bg-sub text-center text-white"><?php echo html_escape( db_config( 'create_ticket_page_message' ) ); ?></div>
          <?php } ?>
          
          <div class="shadow-sm wrapper">
            <h3 class="h5 mb-4 fw-bold border-bottom pb-2"><?php echo lang( 'create_ticket' ); ?></h3>
            
            <!-- Shop Name — Pre-filled from user profile, disabled -->
            <div class="mb-3">
              <label for="shop_name" class="form-label">Shop Name <span class="text-danger">*</span></label>
              <select id="shop_name" class="form-control" name="shop_name" required disabled>
                <option value="">-- Select Your Shop --</option>
                <?php if ( ! empty( $shop_options ) ) {
                  foreach ( $shop_options as $key => $shop ) { ?>
                    <option value="<?php echo html_escape( $key ); ?>" <?php echo ( $user_shop && $user_shop == $key ) ? 'selected' : ''; ?>><?php echo html_escape( $shop ); ?></option>
                <?php }
                } ?>
              </select>
              <!-- Hidden input submits actual value since disabled field doesn't POST -->
              <?php if ( ! empty( $user_shop ) ) { ?>
                <input type="hidden" name="shop_name" value="<?php echo html_escape( $user_shop ); ?>">
              <?php } ?>
            </div>
            
            <!-- Quick Issue Buttons -->
            <?php if ( ! empty( $quick_issues ) ) { ?>
            <div class="mb-4" id="quick-issue-section">
              <label class="form-label">What is the issue? <span class="text-danger">*</span></label>
              <div class="row g-2">
                <?php foreach ( $quick_issues as $button ) { ?>
                  <div class="col-6 col-md-4">
                    <button type="button" class="btn btn-outline-secondary w-100 quick-issue-btn py-3" 
                            data-label="<?php echo html_escape( $button->label ); ?>"
                            data-incident-type="<?php echo html_escape( $button->incident_type ); ?>"
                            data-priority="<?php echo html_escape( $button->priority ); ?>"
                            data-subject="<?php echo html_escape( $button->subject_template ); ?>"
                            data-message="<?php echo html_escape( $button->message_template ); ?>"
                            data-keywords="<?php echo html_escape( $button->keywords ); ?>">
                      <?php echo html_escape( $button->label ); ?>
                    </button>
                  </div>
                <?php } ?>
              </div>
            </div>
            <?php } ?>
            
            <!-- Subject + Priority -->
            <div class="row g-3 mb-3">
              <div class="col">
                <label for="subject" class="form-label"><?php echo lang( 'subject' ); ?> <span class="text-danger">*</span></label>
                <input type="text" id="subject" class="form-control" name="subject" required>
              </div>
              <div class="col">
                <label for="priority" class="form-label"><?php echo lang( 'priority' ); ?> <span class="text-danger">*</span></label>
                <select id="priority" class="form-control select2 search-disabled" name="priority" data-placeholder="<?php echo lang( 'choose_priority' ); ?>" required>
                  <option></option>
                  <option value="low"><?php echo lang( 'low' ); ?></option>
                  <option value="medium"><?php echo lang( 'medium' ); ?></option>
                  <option value="high"><?php echo lang( 'high' ); ?></option>
                </select>
              </div>
            </div>
            
            <!-- Incident Type (custom field id=2) -->
            <?php if ( ! empty( $fields ) ) {
              foreach ( $fields as $field ) {
                if ( $field->id == 2 ) {
                  $required = ( $field->is_required ) ? 'required' : '';
                  $options = explode( ',', $field->options ); ?>
                  <div class="mb-3">
                    <label class="form-label" for="cf-<?php echo html_escape( $field->id ); ?>">
                      <?php echo html_escape( $field->name ); ?>
                      <?php echo ( $field->is_required ) ? '<span class="text-danger">*</span>' : ''; ?>
                    </label>
                    <select class="form-control select2 search-disabled"
                            id="cf-<?php echo html_escape( $field->id ); ?>"
                            data-placeholder="<?php echo html_escape( $field->name ); ?>"
                            name="cf_<?php echo html_escape( $field->id ); ?>"
                            <?php echo html_escape( $required ); ?>>
                      <option></option>
                      <?php foreach ( $options as $key => $option ) { ?>
                        <option value="<?php echo html_escape( $key ); ?>"><?php echo html_escape( trim( $option ) ); ?></option>
                      <?php } ?>
                    </select>
                  </div>
            <?php }
              }
            } ?>
            
            <!-- Hidden Shop Name custom field (cf_3) -->
            <input type="hidden" id="cf_3_hidden" name="cf_3" value="">
            
            <!-- Area IT Assignment — Manual, same as current flow -->
            <div class="mb-3" id="area-it-wrapper" style="display:none;">
              <label for="area_it_user" class="form-label">Assign to Area IT <span class="text-danger">*</span></label>
              <select id="area_it_user" class="form-control" name="area_it_user" required>
                <option value="">-- Select IT Staff --</option>
              </select>
              <small class="form-text text-muted">IT staff available for your shop area</small>
            </div>
            
            <!-- Description (was Message) -->
            <div class="mb-3">
              <label for="message" class="form-label"><?php echo lang( 'description' ); ?> <span class="text-danger">*</span></label>
              <textarea id="message" class="form-control" name="message" rows="12" required></textarea>
            </div>
            
            <!-- Attachments -->
            <div class="mb-3">
              <label for="attachment" class="form-label"><?php echo lang( 'attach_files' ); ?></label>
              <input type="file" class="d-block" id="attachment" name="attachment" accept="<?php echo ALLOWED_ATTACHMENTS_EXT_HTML; ?>" multiple="true">
              <small id="attachment-guide" class="form-text"><?php echo lang( 'attach_file_tip' ); ?></small>
            </div>
            
            <?php if ( is_gr_togo() ) { ?>
              <div class="mb-3">
                <div class="g-recaptcha" data-sitekey="<?php echo html_escape( db_config( 'gr_public_key' ) ); ?>"></div>
              </div>
            <?php } ?>
            <div class="response-message"></div>
            <div class="border-top pt-3 clearfix">
              <button class="btn btn-sub btn-wide float-end" type="submit" <?php echo ( $disabled ) ? 'disabled' : ''; ?>><?php echo lang( 'submit' ); ?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
$(document).ready(function() {
    var areaItMap = <?php echo isset( $area_it_map ) ? $area_it_map : '{}'; ?>;
    var shopSelect = document.getElementById('shop_name');
    var itWrapper = $('#area-it-wrapper');
    var itSelect = $('#area_it_user');
    var hiddenCf3 = document.getElementById('cf_3_hidden');
    var subjectInput = document.getElementById('subject');
    var messageTextarea = document.getElementById('message');
    var prioritySelect = document.getElementById('priority');
    var cf2Select = document.getElementById('cf-2');
    var now = new Date();
    var timestampStr = now.getFullYear() + '-' + 
                       String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(now.getDate()).padStart(2, '0') + ' ' + 
                       String(now.getHours()).padStart(2, '0') + ':' + 
                       String(now.getMinutes()).padStart(2, '0');
    var shopNameText = shopSelect.options[shopSelect.selectedIndex] ? shopSelect.options[shopSelect.selectedIndex].text.trim() : '';
    
    // Sync cf_3 hidden field from shop_name
    hiddenCf3.value = shopSelect.value;
    
    // Handle Area IT dropdown population
    shopSelect.addEventListener('change', function() {
        var selectedOption = shopSelect.options[shopSelect.selectedIndex];
        var selectedText = selectedOption ? selectedOption.text.trim() : '';
        var selectedValue = shopSelect.value;
        
        hiddenCf3.value = selectedValue;
        shopNameText = selectedText;
        
        if (!selectedText || selectedText === '-- Select Your Shop --') {
            itWrapper.hide();
            itSelect.html('<option value="">-- Select IT Staff --</option>');
            itSelect.removeAttr('required');
            return;
        }
        
        var parts = selectedText.split(' - ');
        var areaPrefix = parts[0].trim();
        
        if (areaItMap[areaPrefix] && areaItMap[areaPrefix].length > 0) {
            itSelect.html('<option value="">-- Select IT Staff --</option>');
            areaItMap[areaPrefix].forEach(function(staff) {
                itSelect.append('<option value="' + staff.user_id + '">' + staff.name + '</option>');
            });
            itWrapper.show();
            itSelect.attr('required', 'required');
        } else {
            itWrapper.hide();
            itSelect.html('<option value="">-- Select IT Staff --</option>');
            itSelect.removeAttr('required');
        }
    });
    
    // Trigger change to populate IT dropdown on load if shop pre-selected
    if (shopSelect.value) {
        $(shopSelect).trigger('change');
    }
    
    // Quick Issue Button click handler
    $('.quick-issue-btn').on('click', function() {
        var btn = $(this);
        var label = btn.data('label');
        var incidentType = btn.data('incident-type');
        var priority = btn.data('priority');
        var subjectTpl = btn.data('subject');
        var messageTpl = btn.data('message');
        
        // Highlight selected button
        $('.quick-issue-btn').removeClass('btn-sub').addClass('btn-outline-secondary');
        btn.removeClass('btn-outline-secondary').addClass('btn-sub');
        
        // Set subject
        if (subjectTpl) {
            var subject = subjectTpl.replace('{label}', label).replace('{shop_name}', shopNameText);
            subjectInput.value = subject;
        }
        
        // Set message/description
        if (messageTpl) {
            var message = messageTpl.replace('{label}', label)
                                    .replace('{shop_name}', shopNameText)
                                    .replace('{timestamp}', timestampStr);
            messageTextarea.value = message;
        }
        
        // Set priority
        if (priority) {
            prioritySelect.value = priority;
            $(prioritySelect).trigger('change');
        }
        
        // Set incident type (cf_2)
        if (cf2Select && incidentType) {
            var cf2Options = cf2Select.options;
            for (var i = 0; i < cf2Options.length; i++) {
                if (cf2Options[i].text.trim() === incidentType) {
                    cf2Select.value = cf2Options[i].value;
                    $(cf2Select).trigger('change');
                    break;
                }
            }
        }
    });
    
    // Smart priority detection from subject/message typing
    function detectPriority() {
        var text = (subjectInput.value + ' ' + messageTextarea.value).toLowerCase();
        var highKeywords = ['wifi', 'internet', 'connection', 'offline', 'network', 'router', 'signal',
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