<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="response-message no-radius"><?php echo alert_message(); ?></div>

<div class="z-listing-tickets z-count bordered extra-height-1 container my-5">
  <div class="row no-flex sep-col text-center">
    <div class="col-lg-4 col-sm-6 mb-3">
      <div class="text-white bg-sub p-4 rounded shadow-sm">
        <p class="h2 mb-0 fw-bold"><?php echo html_escape( $opened ); ?></p>
        <p class="mb-0"><a href="<?php echo env_url( 'user/support/tickets/opened' ); ?>" class="a text-white"><?php echo lang( 'opened_tickets' ); ?></a></p>
      </div>
    </div>
    <!-- /col -->
    <div class="col-lg-4 col-sm-6 mb-3">
      <div class="text-white bg-sub p-4 rounded shadow-sm">
        <p class="h2 mb-0 fw-bold"><?php echo html_escape( $closed ); ?></p>
        <p class="mb-0"><a href="<?php echo env_url( 'user/support/tickets/closed' ); ?>" class="a text-white"><?php echo lang( 'closed_tickets' ); ?></a></p>
      </div>
    </div>
    <!-- /col -->
    <div class="col-lg-4 col-sm-6 mb-3">
      <div class="text-white bg-sub p-4 rounded shadow-sm">
        <p class="h2 mb-0 fw-bold"><?php echo html_escape( $all ); ?></p>
        <p class="mb-0"><a href="<?php echo env_url( 'user/support/tickets/all' ); ?>" class="a text-white"><?php echo lang( 'all_tickets' ); ?></a></p>
      </div>
    </div>
    <!-- /col -->
  </div>
  <!-- /.row -->
  <div class="row mb-4 mt-3">
    <div class="col">
      <h2 class="h4 fw-bold mb-0"><?php echo lang( 'recent_tickets' ); ?></h2>
    </div>
    <!-- /col -->
  </div>
  <div class="shadow-sm">
    <div class="row">
      <div class="col">
        <?php
        if ( ! empty( $tickets ) )
        {
          foreach ( $tickets as $ticket ) { ?>
            <div class="list-item">
              <div class="row">
                <div class="col-lg-1 ps-lg-3 pe-lg-1 align-self-center">
                  <h3 class="h5 mb-lg-0 rounded text-center bg-sub text-white py-2 fw-bold"><?php echo ticket_replies_count( $ticket->id ); ?></h3>
                </div>
                <!-- /col -->
                <div class="col-lg-8">
                  <p class="h5 mb-2 mb-lg-1 fw-bold">
                    <a href="<?php echo env_url( 'user/support/ticket/' . html_escape( $ticket->id ) ); ?>"><?php echo replace_some_with_actuals( html_escape( $ticket->subject ) );?></a>
                    
                    <?php if ( $ticket->is_read == 0 && ( $ticket->sub_status == 2 || $ticket->sub_status == 3 ) ) { ?>
                      <span class="ms-1 badge bg-danger"><?php echo lang( 'unread' ); ?></span>
                    <?php } ?>
                  </p>
                  <span class="text-secondary me-2 small basic-info"><i class="fas fa-fingerprint"></i> <?php printf( lang( 'request_id' ), html_escape( $ticket->id ) ); ?></span>
                  <span class="text-secondary small basic-info d-block d-sm-inline-block">
                    <i class="far fa-clock"></i>
                    <?php echo lang( 'last_activity' ); ?>:
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
                </div>
                <!-- /col -->
                <div class="col-lg-3">
                  <div class="float-lg-end mt-2 mt-lg-0 text-lg-end">
                    <span class="badge <?php echo ticket_sub_status_color( $ticket->sub_status ); ?>"><?php echo manage_ticket_sub_status( $ticket->sub_status ); ?></span>                    
                    <div>
                      <span class="mt-1 badge <?php echo ticket_status_color( $ticket->status ); ?>"><?php echo manage_ticket_status( $ticket->status ); ?></span>
                    </div>
                  </div>
                </div>
                <!-- /col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.list-item -->
            <?php }
          } else {
        ?>
          <div class="list-item">
            <div class="row">
              <div class="col">
                <div class="text-center">
                  <img class="not-found mt-2 mb-4" src="<?php illustration_by_color( 'not_found' ); ?>" alt="">
                  <h2 class="h4 fw-bold"><?php echo lang( 'no_records_found' ); ?></h2>
                </div>
              </div>
              <!-- /col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.list-item -->
        <?php } ?>
      </div>
      <!-- /col -->
    </div>
    <!-- /.row -->
    
    <!-- Recent Comments on Your Tickets -->
    <?php if ( ! empty( $user_recent_replies ) ) { ?>
    <div class="row mt-4">
      <div class="col-lg-8 offset-lg-2">
        <div class="shadow-sm wrapper">
          <h3 class="h5 mb-3 fw-bold border-bottom pb-2"><i class="fas fa-comments text-success"></i> Recent Comments on Your Tickets</h3>
          <?php foreach ( $user_recent_replies as $rr ) { ?>
            <div class="py-2 border-bottom">
              <div class="d-flex justify-content-between">
                <strong class="small"><?php echo html_escape( $rr->first_name . ' ' . $rr->last_name ); ?></strong>
                <small class="text-muted"><?php echo get_date_time_by_timezone( $rr->replied_at ); ?></small>
              </div>
              <a href="<?php echo env_url( 'user/support/ticket/' . html_escape( $rr->ticket_id ) ); ?>" class="small text-muted">
                Ticket #<?php echo html_escape( $rr->ticket_id ); ?> — <?php echo html_escape( mb_strimwidth( $rr->ticket_subject, 0, 40, '...' ) ); ?>
              </a>
              <?php
                $u_created_day = strtotime( date( 'Y-m-d', $rr->ticket_created_at ) );
                $u_today = strtotime( date( 'Y-m-d' ) );
                $u_age = intval( ( $u_today - $u_created_day ) / 86400 );
                if ( $rr->ticket_status == 0 ) { $u_cls = 'badge-secondary'; $u_txt = 'Closed'; }
                else if ( $u_age == 0 ) { $u_cls = 'badge-success'; $u_txt = 'Today'; }
                else if ( $u_age == 1 ) { $u_cls = 'badge-success'; $u_txt = '1 day ago'; }
                else if ( $u_age <= 3 ) { $u_cls = 'badge-warning'; $u_txt = $u_age . ' days ago'; }
                else { $u_cls = 'badge-danger'; $u_txt = $u_age . ' days ago'; }
              ?>
              <span class="badge <?php echo $u_cls; ?> ml-1"><?php echo $u_txt; ?></span>
              <p class="small mb-0 mt-1"><?php echo html_escape( mb_strimwidth( $rr->message_preview, 0, 120, '...' ) ); ?></p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php } ?>
    
  </div>
</div>
<!-- /.container -->