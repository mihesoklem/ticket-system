<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Support Controller ( User, Actions )
 *
 * @author Shahzaib
 */
class Support extends MY_Controller {
    
    /**
     * Logged in User ID
     *
     * @var integer
     */
    private $user_id;
    
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library( 'form_validation' );
        $this->load->model( 'Support_model' );
        
        $this->user_id = $this->zuser->get( 'id' );
    }
    
    /**
     * Resend Ticket Verification Email
     *
     * @return  void
     * @version 1.6
     */
    public function resend_ticket_email()
    {
        if ( ! is_email_settings_filled() ) r_error( 'missing_email_config_a' );
        
        $id = intval( post( 'id' ) );
        $security_key = do_secure( post( 'security_key' ) );
        
        if ( empty( $security_key ) || empty( $id ) ) r_error( 'invalid_req' );
        
        $ticket = $this->Support_model->guest_ticket( $security_key, $id );
        
        if ( empty( $ticket ) ) r_error( 'invalid_req' );
        
        if ( $ticket->status == 0 ) r_error( 'ticket_closed' );
        
        if ( $ticket->last_email_attempt > subtract_time( '1 minutes' ) )
        {
            r_error( 'resend_wait_one_minute' );
        }
        
        if ( $ticket->email_attempts >= MAX_EMAIL_RESEND_LIMIT )
        {
            r_error( 'reached_limited' );
        }
        
        if ( $ticket->is_verified == 1 ) r_error( 'already_verified' );
        
        $email_address = $ticket->email_address;
        $link = 'ticket/guest/' . $ticket->security_key . "/{$id}";
        $hook = 'ticket_created_guest';
        
        send_guest_ticket_notification( $email_address, $link, $hook, $id );
        
        $this->Support_model->increment_email_sending_attempt( $id );
        
        log_ticket_activity( 'ticket_email_resent', $id );
        
        r_s_jump( $link, 'email_sent_command' );
    }
    
    /**
     * Create Chat Modal Input Handling.
     *
     * @return  void
     * @version 1.4
     */
    public function create_chat()
    {
        if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
        if ( db_config( 'sp_live_chatting' ) == 0 ) r_error( 'temp_disabled' );
        
        if ( db_config( 'sp_verification_before_submit' ) && $this->zuser->get( 'is_verified' ) == 0 )
        {
            r_error( 'invalid_req' );
        }
        
        if ( ! is_chat_available() ) r_error( 'no_chat_available' );
        
        if ( $this->form_validation->run( 'create_chat' ) )
        {
            if ( is_active_chat() ) r_error( 'already_chatting' );
            
            $data = [
                'subject' => do_secure( post( 'subject' ) ),
                'message' => do_secure( post( 'message' ), true ),
                'user_id' => $this->user_id,
                'department_id' => intval( post( 'department' ) ),
                'created_month_year' => date( 'n-Y' ),
                'created_at' => time()
            ];
            
            $department = $this->Support_model->department( $data['department_id'] );
            
            if ( empty( $department ) )
            {
                r_error( 'invalid_department' );
            }
            
            $id = $this->Support_model->add_chat( $data );
            
            if ( ! empty( $id ) )
            {
                $track = json_encode(
                [
                    'person' => md5( $this->zuser->get( 'id' ) ),
                    'chat_id' => $id
                ]);
                
                set_cookie( CHAT_COOKIE, $track, strtotime( '+1 day' ) );
                
                inform_department_users( $department, $id, 'chat' );
                
                $body = ['chat_id' => $id, 'body_type' => 'starting'];
                
                $data = [
                    'chat_header' => read_view( 'user/responses/chat_header' ),
                    'chat_body' => read_view( 'user/responses/chat_body', $body )
                ];
                
                r_user_chat_starting( $data );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Add Chat Reply Input Handling.
     *
     * @return  void
     * @version 1.4
     */
    public function add_reply_chat()
    {
        if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
        $chat_id = intval( get_chat_data_from_json() );
        $my_id = $this->zuser->get( 'id' );
        $chat = $this->Support_model->chat( $chat_id, $my_id );
        $to_update = [];
        
        if ( empty( $chat ) ) r_error( 'invalid_req' );
        
        if ( $chat->status == 0 ) r_error( 'chat_ended' );
        
        if ( $this->form_validation->run( 'add_reply' ) )
        {
            $data = [
                'chat_id' => $chat_id,
                'user_id' => $my_id,
                'message' => do_secure( post( 'reply' ), true ),
                'replied_at' => time()
            ];
            
            $to_update['sub_status'] = 1;
            $to_update['is_read'] = 0;
            
            $id = $this->Support_model->add_chat_reply( $data );
            
            if ( ! empty( $id ) )
            {
                $this->Support_model->update_chat( $to_update, $chat_id );
                
                r_reset_form();
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( form_error( 'reply' ) );
    }
    
    /**
     * Get Chat Messages
     *
     * @return  void
     * @version 1.4
     */
    public function get_chat_messages()
    {
        if ( ! $this->zuser->is_logged_in )
        {
            r_user_chat_replies( ['logged_in' => 'false', 'message' => err_lang( 'login_to_chat' )] );
        }
        
        $id = intval( get_chat_data_from_json() );
        
        $chat = $this->Support_model->chat( $id, $this->user_id );
        
        if ( empty( $chat ) )
        {
            $body = ['body_type' => 'ending'];
            
            if ( get_cookie( CHAT_COOKIE ) && is_logged_in_user_chat_person() )
            {
                delete_cookie( CHAT_COOKIE );
            }
            
            $data['chat_body'] = read_view( 'user/responses/chat_body', $body );
            $data['chat_ended'] = 'true';
        }
        else
        {
            $data['having_replies'] = 'false';
            
            if ( $chat->status == 1 )
            {
                $last_reply_id = intval( post( 'last_reply_id' ) );
                $replies = $this->Support_model->chat_replies( $id, $last_reply_id );
                $body = ['body_type' => 'replies', 'replies' => $replies];
                
                if ( ! empty( $replies ) ) $data['having_replies'] = 'true';
                
                $data['chat_body'] = read_view( 'user/responses/chat_body', $body );
                $data['chat_ended'] = 'false';
            }
            else if ( $chat->status == 0 )
            {
                $body = ['body_type' => 'ending'];
                $data['chat_body'] = read_view( 'user/responses/chat_body', $body );
                $data['chat_ended'] = 'true';
                
                delete_cookie( CHAT_COOKIE );
            }
            
            r_user_chat_replies( $data );
        }
    }
    
    /**
     * End Chat
     *
     * @return  void
     * @version 1.4
     */
    public function end_chat()
    {
        $id = intval( get_chat_data_from_json() );
        
        $data = $this->Support_model->chat( $id, $this->user_id );
        
        if ( empty( $data ) ) r_error( 'invalid_req' );
        
        if ( $this->Support_model->end_chat( $id ) )
        {
            delete_cookie( CHAT_COOKIE );
            
            $body = ['body_type' => 'ending'];
            $html = read_view( 'user/responses/chat_body', $body );
            
            r_user_chat_ending( $html );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Create Ticket Page Input Handling.
     *
     * @return void
     */
    public function create_ticket()
    { 
        if ( post( 'from_guest' ) == 1 )
        {
            if ( db_config( 'sp_guest_ticketing' ) == 0 ) r_error_gr( 'temp_disabled' );
            
            if ( ! is_email_settings_filled() ) r_error_gr( 'missing_email_config_a' );
            
            $form_validation_key = 'create_ticket_guest';
        }
        else
        {
            if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
            
            if ( db_config( 'sp_verification_before_submit' ) && $this->zuser->get( 'is_verified' ) == 0 )
            {
                r_error_gr( 'invalid_req' );
            }
            
            $form_validation_key = 'create_ticket';
        }
        
        $attachments_data = [];
        
        if ( $this->form_validation->run( $form_validation_key ) )
        {
            if ( ! submit_gr() ) r_error_gr( 'recaptcha' );
            
            $dept_id = intval( post( 'department' ) );
            if ( empty( $dept_id ) ) $dept_id = 9; // Default to IT department
            
            $data = [
                'subject' => do_secure( post( 'subject' ) ),
                'message' => do_secure( post( 'message' ), true ),
                'priority' => do_secure_l( post( 'priority' ) ),
                'department_id' => $dept_id,
                'created_month_year' => date( 'n-Y' ),
                'created_at' => time()
            ];
            
            if ( post( 'from_guest' ) == 1 )
            {
                $data['email_address'] = do_secure_l( post( 'email_address' ) );
                $data['security_key'] = get_short_random_string();
                $data['is_verified'] = 0;
            }
            else
            {
                $data['user_id'] = $this->user_id;
            }
            
            $department = $this->Support_model->department( $data['department_id'] );
            
            if ( empty( $department ) )
            {
                r_error_gr( 'invalid_department' );
            }
            
            if ( ! in_array( $data['priority'], ['low', 'medium', 'high'] ) )
            {
                r_error_gr( 'invalid_priority' );
            }
            
            $mcfi = manage_custom_field_input( null, true );
            
            if ( $mcfi !== true ) r_error_gr( $mcfi );
            
            // Area IT assignment - mandatory for area shops, auto-assign if not selected
            $area_it_user = intval( post( 'area_it_user' ) );
            
            if ( empty( $area_it_user ) )
            {
                // Check if this shop belongs to an area with IT staff
                $shop_name_key = intval( post( 'shop_name' ) );
                $shop_field = $this->db->select( 'options' )->where( 'id', 3 )->get( 'custom_fields' )->row();
                if ( ! empty( $shop_field ) )
                {
                    $shop_opts = array_map( 'trim', explode( ',', $shop_field->options ) );
                    $selected_shop = isset( $shop_opts[$shop_name_key] ) ? $shop_opts[$shop_name_key] : '';
                    
                    if ( ! empty( $selected_shop ) && strpos( $selected_shop, ' - ' ) !== false )
                    {
                        $area_prefix = trim( explode( ' - ', $selected_shop )[0] );
                        
                        $this->load->model( 'Area_it_model' );
                        $area_staff = $this->Area_it_model->get_staff_by_prefix( $area_prefix );
                        
                        if ( ! empty( $area_staff ) )
                        {
                            // Auto-assign to first available IT staff for this area
                            $area_it_user = $area_staff[0]->user_id;
                        }
                    }
                }
            }
            
            if ( ! empty( $area_it_user ) )
            {
                $data['assigned_to'] = $area_it_user;
            }
            
            $id = $this->Support_model->add_ticket( $data );
            
            if ( ! empty( $id ) )
            {
                log_ticket_activity( 'ticket_created_user', $id );
                
                // Log assignment activity if area IT was selected
                if ( ! empty( $area_it_user ) )
                {
                    log_ticket_activity( 'ticket_assigned', $id, $area_it_user );
                }
                
                manage_custom_field_input( $id );
                
                if ( ! empty( $_FILES ) )
                {
                    $this->load->library( 'ZFiles' );
                    
                    $attachments_data['ticket_id'] = $id;
                    
                    foreach ( $_FILES as $key => $file )
                    {
                        if ( ! empty( $_FILES[$key]['tmp_name'] ) )
                        {
                            $file = $this->zfiles->upload_attachment( $key );
                            
                            $attachments_data['attachment_name'] = do_secure( $file['client_name'] );
                            $attachments_data['attachment'] = $file['file_name'];
                            
                            $this->Support_model->save_ticket_attachment( $attachments_data );
                        }
                    }
                }
                
                if ( post( 'from_guest' ) == 1 )
                {
                    $email_address = $data['email_address'];
                    $link = 'ticket/guest/' . $data['security_key'] . "/{$id}";
                    $hook = 'ticket_created_guest';
                    $suc = sprintf( suc_lang( 'ticket_created_guest' ), $email_address );
                    $slug = $link;
                    $text_type = '';
                    
                    send_guest_ticket_notification( $email_address, $link, $hook, $id );
                }
                else
                {
                    $slug = "user/support/ticket/{$id}";
                    $suc = 'ticket_created';
                    $text_type = 'lang';
                }
                
                
                // Fetch complete ticket data with all details before sending email
                $ticket_data = $this->Support_model->ticket( $id );
                
                // Send confirmation email to registered user
                send_user_ticket_confirmation( $this->user_id, $id, $ticket_data );
                inform_department_users( $department, $id );

                // Notify area manager
                notify_area_manager( $id, 'created' );
                
                // Send assignment email to the Area IT person
                if ( ! empty( $area_it_user ) && is_email_settings_filled() )
                {
                    $this->load->model( 'User_model' );
                    $it_user = $this->User_model->get_by_id( $area_it_user );
                    
                    if ( ! empty( $it_user ) && is_notifications_enabled( $area_it_user ) )
                    {
                        $hook = 'ticket_assigned';
                        $language = get_user_closer_language( $it_user->language );
                        $tpl = $this->Tool_model->email_template_by_hook_and_lang( $hook, $language );
                        
                        if ( ! empty( $tpl ) )
                        {
                            $this->db->select( 'subject, priority, message' );
                            $this->db->where( 'id', $id );
                            $t_info = $this->db->get( 'tickets' )->row();
                            
                            $assign_placeholders = [
                                '{USER_NAME}' => $it_user->first_name . ' ' . $it_user->last_name,
                                '{TICKET_URL}' => env_url( "admin/tickets/ticket/{$id}" ),
                                '{TICKET_ID}' => $id,
                                '{TICKET_SUBJECT}' => ! empty( $t_info->subject ) ? $t_info->subject : '',
                                '{TICKET_PRIORITY}' => ! empty( $t_info->priority ) ? ucfirst( $t_info->priority ) : '',
                                '{TICKET_MESSAGE}' => ! empty( $t_info->message ) ? $t_info->message : '',
                                '{SHOP_NAME}' => get_ticket_shop_name( $id ),
                                '{SITE_NAME}' => db_config( 'site_name' )
                            ];
                            
                            $assign_subject = replace_placeholders( $tpl->subject, $assign_placeholders );
                            $assign_message = replace_placeholders( $tpl->template, $assign_placeholders );
                            
                            $this->load->library( 'ZMailer' );
                            $this->zmailer->send_email( $it_user->email_address, $assign_subject, $assign_message );
                        }
                    }
                }
                
                r_s_jump( $slug, $suc, $text_type );
            }
            
            r_error_gr( 'went_wrong' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Add Reply Input Handling.
     *
     * @return void
     */
    public function add_reply()
    {
        $ticket_id = intval( post( 'id' ) );
        $security_key = do_secure( post( 'security_key' ) );
        $attachments_data = [];
        
        if ( ! empty( $security_key ) )
        {
            $data = $this->Support_model->guest_ticket( $security_key, $ticket_id );
        }
        else
        {
            if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
            
            $data = $this->Support_model->ticket( $ticket_id, $this->user_id );
        }
        
        if ( empty( $data ) ) r_error_gr( 'invalid_req' );
        
        $assigned_to = $data->assigned_to;
        
        if ( $data->status == 0 ) r_error_gr( 'ticket_closed' );
        
        if ( ! empty( $security_key ) )
        {
            if ( $data->is_verified == 0 ) r_error_gr( 'invalid_req' );
            
            $url = "ticket/guest/{$security_key}/{$ticket_id}";
        }
        else
        {
            $url = "user/support/ticket/{$ticket_id}";
        }
        
        if ( post( 'solved' ) && ! post( 'reply' ) )
        {
            if ( $data->sub_status != 3 )
            {
                $is_read = ( $data->last_reply_area == 1 ) ? 1 : 0;
                
                $to_update = ['sub_status' => 3, 'is_read' => $is_read, 'last_reply_area' => 2];
                
                $this->Support_model->update_ticket( $to_update, $ticket_id );
                
                log_ticket_activity( 'ticket_solved_user', $ticket_id );
                
                r_s_jump( $url, 'ticket_solved' );
            }
            
            r_error_gr( 'invalid_req' );
        }
        
        if ( $this->form_validation->run( 'add_reply' ) )
        {
            if ( ! submit_gr() ) r_error_gr( 'recaptcha' );
            
            $data = [
                'ticket_id' => $ticket_id,
                'message' => do_secure( post( 'reply' ), true ),
                'replied_at' => time()
            ];
            
            if ( empty( $security_key ) )
            {
                $data['user_id'] = $this->user_id;
            }
            
            $id = $this->Support_model->add_reply( $data );
            
            if ( ! empty( $id ) )
            {
                log_ticket_activity( 'ticket_replied_user', $ticket_id );
                
                if ( $assigned_to != null )
                {
                    send_reply_notification( $assigned_to, $ticket_id, $id, 'user', $this->user_id );
                }
                

                // Notify area manager
                notify_area_manager( $ticket_id, 'replied' );
                if ( ! empty( $_FILES ) )
                {
                    $this->load->library( 'ZFiles' );
                    
                    $attachments_data['ticket_reply_id'] = $id;
                    
                    foreach ( $_FILES as $key => $file )
                    {
                        if ( ! empty( $_FILES[$key]['tmp_name'] ) )
                        {
                            $file = $this->zfiles->upload_attachment( $key );
                            
                            $attachments_data['attachment_name'] = do_secure( $file['client_name'] );
                            $attachments_data['attachment'] = $file['file_name'];
                            
                            $this->Support_model->save_ticket_reply_attachment( $attachments_data );
                        }
                    }
                }
                
                $sub_status = ( post( 'solved' ) == 1 ) ? 3 : 1;
                
                $this->Support_model->update_ticket(
                    [
                        'sub_status' => $sub_status,
                        'last_reply_area' => 2,
                        'is_read' => 0
                    ],
                    $ticket_id
                );
                
                r_s_jump( $url, 'ticket_replied' );
            }
            
            r_error_gr( 'went_wrong' );
        }
        
        d_r_error( form_error( 'reply' ) );
    }
    
    /**
     * Re-open Ticket
     *
     * @return void
     */
    public function reopen_ticket()
    {
        if ( db_config( 'sp_allow_ticket_reopen' ) == 0 )
        {
            r_error( 'invalid_req' );
        }
        
        $security_key = do_secure( post( 'security_key' ) );
        $id = intval( post( 'id' ) );
        
        if ( ! empty( $security_key ) )
        {
            $data = $this->Support_model->guest_ticket( $security_key, $id );
        }
        else
        {
            if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
            
            $data = $this->Support_model->ticket( $id, $this->user_id );
        }
        
        if ( empty( $data ) ) r_error( 'invalid_req' );
        
        if ( $this->Support_model->reopen_ticket( $id ) )
        {
            if ( ! empty( $security_key ) )
            {
                $url = "ticket/guest/{$security_key}/{$id}";
            }
            else
            {
                $url = "user/support/ticket/{$id}";
            }
                
            log_ticket_activity( 'ticket_reopened_user', $id );

                // Notify area manager
                notify_area_manager( $id, 'reopened' );
            r_s_jump( $url, 'ticket_reopened' );
        }
        
        r_error( 'invalid_req' );
    }
    
    /**
     * Close Ticket
     *
     * @return void
     */
    public function close_ticket()
    {
        $security_key = do_secure( post( 'security_key' ) );
        $id = intval( post( 'id' ) );
        $closing_reply = trim( post( 'closing_reply' ) );
        $closed_by = '';
        
        // Closing reply is mandatory
        if ( empty( $closing_reply ) )
        {
            r_error( 'reply_required_before_close' );
        }
        
        if ( ! empty( $security_key ) )
        {
            $data = $this->Support_model->guest_ticket( $security_key, $id );
            $closed_by = null;
        }
        else
        {
            if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
            $data = $this->Support_model->ticket( $id, $this->user_id );
        }
        
        if ( empty( $data ) ) r_error( 'invalid_req' );
        
        // Save the closing reply
        $reply_user_id = ! empty( $security_key ) ? null : $this->user_id;
        $reply_data = [
            'ticket_id' => $id,
            'user_id' => $reply_user_id,
            'message' => do_secure( $closing_reply, true ),
            'attachment' => '',
            'attachment_name' => '',
            'replied_at' => time()
        ];
        
        $reply_id = $this->Support_model->add_reply( $reply_data );
        
        if ( $this->Support_model->close_ticket( $id, $closed_by ) )
        {
            if ( ! empty( $security_key ) )
            {
                $url = "ticket/guest/{$security_key}/{$id}";
            }
            else
            {
                $url = "user/support/ticket/{$id}";
            }
            
            log_ticket_activity( 'ticket_replied_user', $id );
            log_ticket_activity( 'ticket_closed_user', $id );
            
            // Notify assigned IT
            if ( ! empty( $data->assigned_to ) && ! empty( $reply_id ) )
            {
                send_reply_notification( $data->assigned_to, $id, $reply_id, 'user', $reply_user_id );
            }

            // Notify area manager
            notify_area_manager( $id, 'closed' );
            r_s_jump( $url, 'ticket_closed' );
        }
        
        r_error( 'invalid_req' );
    }
}
