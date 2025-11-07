<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="response-message no-radius"><?php echo alert_message(); ?></div>

<div class="z-listing-ticket-replies container my-5 extra-height-1">
  <div class="row">
    <div class="col-lg-8">
      <div class="list-wrapper shadow-sm">
        <div class="list-wrapper-header border-bottom clearfix">
          <h3 class="fw-bold"><?php echo replace_some_with_actuals( html_escape( $ticket->subject ) ); ?></h3>
          <span class="badge bg-sub mb-1 mb-md-0"><i class="fas fa-fingerprint"></i> <?php printf( lang( 'request_id' ), html_escape( $ticket->id ) ); ?></span>
        </div>
        <!-- /.clearfix -->
        <div class="list-item d-flex">
          <div class="user-pic-parent">
            <?php if ( ! empty( $ticket->user_id ) ) { ?>
              <img class="user-pic" src="<?php echo user_picture( html_esc_url( $ticket->user_picture ) ); ?>" alt="<?php echo html_escape( $ticket->first_name . ' ' . $ticket->last_name ); ?>">
            <?php } else { ?>
              <img class="user-pic" src="<?php echo user_picture( html_esc_url( DEFAULT_USER_IMG ) ); ?>" alt="">
            <?php } ?>
          </div>
          <!-- /.user-pic-parent -->
          <div class="px-3">
            <p class="mt-1 mb-0 fw-bold">
            <?php
            if ( ! empty( $ticket->user_id ) )
            {
                echo html_escape( $ticket->first_name . ' ' . $ticket->last_name );
            }
            else
            {
            ?>
              <?php echo lang( 'customer' ); ?> <span class="text-muted small mb-1 fw-normal">&mdash; <?php echo html_escape( $ticket->email_address ); ?></span>
            <?php } ?>
            </p>
            <p class="message-time text-muted small mb-1"><?php echo get_date_time_by_timezone( html_escape( $ticket->created_at ) ); ?></p>
            <p class="message">
              <?php echo nl2br( make_text_links( replace_some_with_actuals( html_escape( $ticket->message ) ) ) ); ?>
              
              <?php if ( ! empty( $fields ) && is_custom_fields_having_value( $fields ) ) { ?>
                <div class="mt-3 mb-3">
                  <span class="d-block fw-bold text-sub"><?php echo lang( 'additional_information' ); ?>:</span>
                  <?php foreach ( $fields as $field )
                  {
                    if ( ! empty( $field->value ) ) { ?>
                    <p class="mb-0 mt-1"><span><?php echo html_escape( $field->name ); ?>:</span> <?php echo nl2br( html_escape( $field->value ) ); ?></p>
                  <?php } } ?>
                </div>
                <!-- /.mt-3 -->
              <?php } ?>
              
              <?php if ( ! empty( $ticket->attachment_name ) ) { ?>
                <strong class="mt-2 d-block text-sub"><?php echo lang( 'attachment' ); ?>:</strong>
                
                <?php if ( is_image_file( $ticket->attachment ) ) { ?>
                  <img
                    class="rounded d-block mt-2 cursor-pointer shadow attached-img popup-img-attachment"
                    src="<?php echo attachments_uploads( html_escape( $ticket->attachment ) ); ?>"
                    alt="Attachment">
                <?php } ?>
                
                <span class="d-block small">
                  <i class="fas fa-paperclip text-muted me-1"></i>
                  <a class="no-site-color" href="<?php echo attachments_uploads( html_escape( $ticket->attachment ) ); ?>" download>
                    <span data-bs-toggle="tooltip" title="<?php echo html_escape( $ticket->attachment_name ); ?>">
                      <?php echo html_escape( long_to_short_name( $ticket->attachment_name ) ); ?>
                    </span>
                  </a>
                </span>
              <?php } else { ?>
                
                <?php
                
                $ticket_main_attachments = get_ticket_attachments( $ticket->id );
                
                if ( ! empty( $ticket_main_attachments ) ) { ?>
                  
                  <strong class="mt-2 d-block text-sub"><?php echo lang( 'attachments' ); ?>:</strong>
                  
                  <?php foreach ( $ticket_main_attachments as $ticket_main_attachment ) { ?>
                    
                    <?php if ( is_image_file( $ticket_main_attachment->attachment ) ) { ?>
                      <img
                        class="rounded d-block mt-2 cursor-pointer shadow attached-img popup-img-attachment"
                        src="<?php echo attachments_uploads( html_escape( $ticket_main_attachment->attachment ) ); ?>"
                        alt="Attachment">
                    <?php } ?>
                    
                    <span class="d-block small">
                      <i class="fas fa-paperclip text-muted me-1"></i>
                      <a class="no-site-color" href="<?php echo attachments_uploads( html_escape( $ticket_main_attachment->attachment ) ); ?>" download>
                        <span data-bs-toggle="tooltip" title="<?php echo html_escape( $ticket_main_attachment->attachment_name ); ?>">
                          <?php echo html_escape( long_to_short_name( $ticket_main_attachment->attachment_name ) ); ?>
                        </span>
                      </a>
                    </span>
                  <?php } ?>
                <?php } ?>
                
              <?php } ?>
            </p>
          </div>
        </div>
        <!-- /.list-item -->
        <?php if ( ! empty( $replies ) ) {
          foreach ( $replies as $reply ) {
            $reply_attachments = get_ticket_reply_attachments( $reply->id );
            $display_attachments_label = true; ?>
            <div class="list-item d-flex" id="section-<?php echo md5( $reply->id ); ?>">
              <div class="user-pic-parent">
                <?php if ( ! empty( $ticket->user_id ) ) { ?>
                  <img class="user-pic" src="<?php echo user_picture( html_esc_url( $reply->user_picture ) ); ?>" alt="<?php echo html_escape( $reply->first_name . ' ' . $reply->last_name ); ?>">
                <?php } else { ?>
                  <img class="user-pic" src="<?php echo user_picture( html_esc_url( DEFAULT_USER_IMG ) ); ?>" alt="">
                <?php } ?>
              </div>
              <!-- /.user-pic-parent -->
              <div class="px-3">
                <p class="mt-1 mb-0 fw-bold">
                <?php
                if ( ! empty( $reply->user_id ) )
                {
                    echo html_escape( $reply->first_name . ' ' . $reply->last_name );
                }
                else
                {
                ?>
                  <?php echo lang( 'customer' ); ?> <span class="text-muted small mb-1 fw-normal">&mdash; <?php echo html_escape( $ticket->email_address ); ?></span>
                <?php } ?>
                </p>
                <p class="message-time text-muted small mb-1"><?php echo get_date_time_by_timezone( html_escape( $reply->replied_at ) ); ?></p>
                <p class="message">
                  <?php echo nl2br( make_text_links( replace_some_with_actuals( html_escape( $reply->message ) ) ) ); ?>
                  
                  <?php if ( ! empty( $reply->attachment_name ) ) {
                    $display_attachments_label = false; ?>
                    <strong class="mt-2 d-block text-sub"><?php echo lang( 'attachments' ); ?>:</strong>
                    
                    <?php if ( is_image_file( $reply->attachment ) ) { ?>
                      <img
                        class="rounded d-block mt-2 cursor-pointer shadow attached-img popup-img-attachment"
                        src="<?php echo attachments_uploads( html_escape( $reply->attachment ) ); ?>"
                        alt="Attachment">
                    <?php } ?>
                    
                    <span class="d-block small">
                      <i class="fas fa-paperclip text-muted me-1"></i>
                      <a class="no-site-color" href="<?php echo attachments_uploads( html_escape( $reply->attachment ) ); ?>" download>
                        <span data-bs-toggle="tooltip" title="<?php echo html_escape( $reply->attachment_name ); ?>">
                          <?php echo html_escape( long_to_short_name( $reply->attachment_name ) ); ?>
                        </span>
                      </a>
                    </span>
                  <?php } ?>
                  
                  <?php if ( ! empty( $reply_attachments ) ) { ?>
                    
                    <?php if ( $display_attachments_label ) { ?>
                      <strong class="mt-2 d-block text-sub"><?php echo lang( 'attachments' ); ?>:</strong>
                    <?php } ?>
                    
                    <?php foreach ( $reply_attachments as $reply_attachment ) { ?>
                      
                      <?php if ( is_image_file( $reply_attachment->attachment ) ) { ?>
                        <img
                          class="rounded d-block mt-2 cursor-pointer shadow attached-img popup-img-attachment"
                          src="<?php echo attachments_uploads( html_escape( $reply_attachment->attachment ) ); ?>"
                          alt="Attachment">
                      <?php } ?>
                      
                      <span class="d-block small">
                        <i class="fas fa-paperclip text-muted me-1"></i>
                        <a class="no-site-color" href="<?php echo attachments_uploads( html_escape( $reply_attachment->attachment ) ); ?>" download>
                          <span data-bs-toggle="tooltip" title="<?php echo html_escape( $reply_attachment->attachment_name ); ?>">
                            <?php echo html_escape( long_to_short_name( $reply_attachment->attachment_name ) ); ?>
                          </span>
                        </a>
                      </span>
                    <?php } ?>
                  <?php } ?>
                </p>
              </div>
            </div>
            <!-- /.list-item -->
          <?php }
          } else { ?>
            <div class="text-center">
              <span class="d-block text-muted"><?php echo lang( 'no_replies' ); ?></span>
            </div>
        <?php } ?>
      </div>
      <!-- /.list-wrapper -->
      <div class="card border-0 shadow-sm mt-3 z-ticket-reply">
        
        <?php if ( $ticket->status != 0 ) { ?>
          
          <?php if ( $ticket->is_verified == 0 ) { ?>
            <div class="alert alert-warning mb-1">
              <p class="mb-0 text-center"><?php echo lang( 'verify_ticket_email_msg' ); ?></p>
            </div>
            <!-- /.alert -->
            <p class="text-primary text-center small"><?php echo lang( 'havent_received_email' ); ?></p>
          <?php } ?>
          
          <form class="z-form" action="<?php user_action( 'support/add_reply' ); ?>" method="post" enctype="multipart/form-data" data-csrf="manual">
            <div class="response-message"></div>
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="mb-3">
              <label for="your-reply" class="form-label"><?php echo lang( 'your_reply' ); ?> <span class="text-danger">*</span></label>
              <textarea id="your-reply" class="form-control" name="reply" rows="6"></textarea>
              
              <?php if ( $ticket->sub_status != 3 ) { ?>
                <small class="form-text"><?php echo lang( 'your_reply_opt' ); ?></small>
              <?php } ?>
              
            </div>
            <!-- /.mb-3 -->
            
            <?php if ( $ticket->sub_status != 3 ) { ?>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="solved" name="solved">
                <label class="form-check-label pr-top-1" for="solved"><?php echo lang( 'mark_as_solved' ); ?></label>
              </div>
              <!-- /.mb-3 -->
            <?php } ?>
            
            <div class="mb-3">
              <label for="attachment" class="form-label"><?php echo lang( 'attach_files' ); ?></label>
              <input type="file" class="d-block" id="attachment" name="attachment" accept="<?php echo ALLOWED_ATTACHMENTS_EXT_HTML; ?>" multiple="true">
              <small class="form-text"><?php echo lang( 'attach_file_tip' ); ?></small>
            </div>
            <!-- /.mb-3 -->
            <?php if ( is_gr_togo() ) { ?>
              <div class="mb-3">
                <div class="g-recaptcha" data-sitekey="<?php echo html_escape( db_config( 'gr_public_key' ) ); ?>"></div>
              </div>
              <!-- /.mb-3 -->
            <?php } ?>
            <div class="border-top pt-3">
              <button class="btn btn-sub btn-wide float-end" type="submit" <?php echo ( $ticket->is_verified == 0 ) ? 'disabled' : ''; ?>><?php echo lang( 'submit' ); ?></button>
            </div>
            <!-- /.pt-3 -->
            <input type="hidden" name="security_key" value="<?php echo html_escape( $security_key ); ?>">
            <input type="hidden" name="id" value="<?php echo html_escape( $ticket->id ); ?>">
          </form>
        <?php } else { ?>
          <div class="text-center">
            <span class="d-block"><?php echo lang( 'ticket_closed_msg' ); ?></span>
          </div>
        <?php } ?>
      </div>
      <!-- /.card -->
    </div>
    <!-- /col -->
    <div class="col-lg-4">
      <div class="detail-as-list shadow-sm mt-3 mt-lg-0 clearfix">
        <ul class="nav flex-column pb-0 mb-0">
          <li class="nav-item">
            <strong><?php echo lang( 'department' ); ?></strong>
            <span class="float-end">
              <?php
              if ( ! empty( $ticket->department ) ) { ?>
                <span class="float-end" data-bs-toggle="tooltip" title="<?php echo html_escape( $ticket->department ); ?>">
                  <?php echo html_escape( long_to_short_name( $ticket->department ) ); ?>
                </span>
              <?php
              }
              else
              {
                  echo lang( 'unknown' );
              }
              ?>
            </span>
          </li>
          
          <?php if ( ! empty( $ticket->assigned_to && ! empty( $ticket->au_first_name ) ) ) { ?>
            <li class="nav-item">
              <strong><?php echo lang( 'assigned_to' ); ?></strong>
              <span class="float-end" data-bs-toggle="tooltip" title="<?php echo html_escape( $ticket->au_first_name . ' ' . $ticket->au_last_name ); ?>">
                <?php echo html_escape( long_to_short_name( $ticket->au_first_name . ' ' . $ticket->au_last_name  ) ); ?>
              </span>
            </li>
          <?php } ?>
          
          <li class="nav-item">
            <strong><?php echo lang( 'status' ); ?></strong>
            <span class="float-end badge <?php echo ticket_sub_status_color( $ticket->sub_status ); ?>">
              <?php echo manage_ticket_sub_status( $ticket->sub_status ); ?>
            </span>
          </li>
          <li class="nav-item">
            <strong><?php echo lang( 'priority' ); ?></strong>
            <span class="float-end badge <?php echo ticket_priority_color( $ticket->priority ); ?>"><?php echo lang( html_escape( $ticket->priority ) ); ?></span>
          </li>
          <li class="nav-item">
            <strong><?php echo lang( 'last_activity' ); ?></strong>
            <span class="float-end">
              <?php
              if ( ! empty( $ticket->updated_at ) )
              {
                  $time = $ticket->updated_at;
              }
              else
              {
                  $time = $ticket->created_at;
              }
              
              echo get_date_time_by_timezone( html_escape( $time ) );
              ?>
            </span>
          </li>
          <li class="nav-item">
            <strong><?php echo lang( 'created' ); ?></strong>
            <span class="float-end"><?php echo get_date_time_by_timezone( html_escape( $ticket->created_at ) ); ?></span>
          </li>
        </ul>
        <div class="text-center border-top mt-3">
          <?php if ( $ticket->status != 0 ) { ?>
            <form class="z-form pt-3 d-inline-block" method="post" action="<?php user_action( 'support/close_ticket' ); ?>" data-csrf="manual">
              <div class="response-message"></div>
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <button type="submit" class="btn btn-wide btn-outline-danger mt-1"><?php echo lang( 'close_ticket' ); ?></button>
              <input type="hidden" name="security_key" value="<?php echo html_escape( $security_key ); ?>">
              <input type="hidden" name="id" value="<?php echo html_escape( $ticket->id ); ?>">
            </form>
          <?php } else if ( db_config( 'sp_allow_ticket_reopen' ) ) { ?>
            <form class="z-form pt-3 d-inline-block" method="post" action="<?php user_action( 'support/reopen_ticket' ); ?>" data-csrf="manual">
              <div class="response-message"></div>
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <button type="submit" class="btn btn-wide btn-outline-success mt-1"><?php echo lang( 'reopen_ticket' ); ?></button>
              <input type="hidden" name="security_key" value="<?php echo html_escape( $security_key ); ?>">
              <input type="hidden" name="id" value="<?php echo html_escape( $ticket->id ); ?>">
            </form>
          <?php } ?>
          
          <?php if ( db_config( 'sp_guest_ticketing' ) ) { ?>
            <div class="pt-3 d-inline-block">
              <a href="<?php echo env_url( 'create_ticket' ); ?>" class="btn btn-wide btn-outline-primary mt-1 ms-2"><?php echo lang( 'create_new' ); ?></a>
            </div>
          <?php } ?>
          
        </div>
        <!-- /.border-top -->
      </div>
      <!-- /.detail-as-list -->
    </div>
    <!-- /col -->
  </div>
  <!-- /.row -->
</div>
<!-- /.container -->

<?php load_modals( ['user/resend_ticket_email', 'user/ticket_attachments'] ); ?>