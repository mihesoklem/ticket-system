<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Dashboard Controller
 *
 * @author Shahzaib
 */
class Dashboard extends MY_Controller {
    
    /**
     * Dashboard Page
     *
     * @param  string $type
     * @return void
     */
    public function index( $type = 'user' )
    {
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        $this->load->model( 'Support_model' );
        
        $this->area = 'user';
        $method = 'load_public_template';
        $view = 'user_dashboard';
        
        if ( $type === 'admin' )
        {
            if ( ! $this->zuser->is_team_member() ) env_redirect( 'dashboard' );
            
            $method = 'load_panel_template';
            $recent_tickets_stats = [];
            $stats = [];
            $view = 'dashboard';
            $to_count = ['count' => true];
        
            if ( $this->zuser->has_permission( 'users' ) )
            {
                $this->load->model( 'User_model' );
                $data['data']['users'] = $this->User_model->limited_users( 7 );
                $stats['total_users'] = $this->User_model->get_of_total_count();
            }

            if ( ! $this->zuser->has_permission( 'all_tickets' ) )
            {
                $to_count['assigned'] = true;
            }
            
            $stats['all_tickets'] = $this->Support_model->tickets( $to_count );
            
            $to_count['status'] = 1;
            $stats['opened_tickets'] = $this->Support_model->tickets( $to_count );
            
            $to_count['status'] = 0;
            $stats['closed_tickets'] = $this->Support_model->tickets( $to_count );
            
            for ( $i = 6; $i >= 0; $i-- )
            {
                $month = date( 'n' ) - $i;
                $year = date( 'Y' );
                
                if ( $month < 1 )
                {
                    $month = $month + 12;
                    $year = $year - 1;
                }
                
                $time = mktime( 0, 0, 0, $month, 1, $year );
                $recent_tickets_count = $this->Support_model->get_tickets_count_by_month_year( "{$month}-{$year}" );
                $month_name = get_cf_date_by_user_timezone( 'F', $time );
                $recent_tickets_stats[$month_name] = $recent_tickets_count;
            }
            
            $stats['recent_tickets_stats'] = json_encode( $recent_tickets_stats );
            $data['data']['dashboard'] = $stats;
            
            $data['data']['scripts'] = [
                admin_lte_asset( 'plugins/chart.js/Chart.min.js', true ),
                get_assets_path( 'panel/js/chartjs_script.js' )
            ];
            
            // Recent tickets (last 7)
            $data['data']['recent_tickets'] = $this->db->query("
                SELECT t.id, t.subject, t.priority, t.status, t.sub_status, t.created_at,
                       cf.value as shop_name,
                       u.first_name as assignee_first, u.last_name as assignee_last
                FROM tickets t
                LEFT JOIN tickets_custom_fields cf ON cf.ticket_id = t.id AND cf.custom_field_id = 3
                LEFT JOIN users u ON u.id = t.assigned_to
                ORDER BY t.id DESC LIMIT 7
            ")->result();
            
            // Recent replies (last 7)
            $data['data']['recent_replies'] = $this->db->query("
                SELECT r.ticket_id, r.replied_at,
                       LEFT(REGEXP_REPLACE(r.message, '<[^>]+>', ''), 100) as message_preview,
                       u.first_name, u.last_name,
                       t.subject as ticket_subject,
                       t.created_at as ticket_created_at,
                       t.status as ticket_status
                FROM tickets_replies r
                LEFT JOIN users u ON u.id = r.user_id
                LEFT JOIN tickets t ON t.id = r.ticket_id
                ORDER BY r.id DESC LIMIT 7
            ")->result();
        }
        else if ( $type === 'user' )
        {
            $to_count = ['user_id' => $this->zuser->get( 'id' ), 'count' => true];
            $data['data']['all'] = $this->Support_model->tickets( $to_count );
            
            $to_count['status'] = 1;
            $data['data']['opened'] = $this->Support_model->tickets( $to_count );
            
            $to_count['status'] = 0;
            $data['data']['closed'] = $this->Support_model->tickets( $to_count );
            
            $data['data']['tickets'] = $this->Support_model->tickets([
                'user_id' => $this->zuser->get( 'id' ),
                'limit' => 5
            ]);
            
            // Recent replies on user's tickets
            $uid = $this->zuser->get( 'id' );
            $data['data']['user_recent_replies'] = $this->db->query("
                SELECT r.ticket_id, r.replied_at,
                       LEFT(REGEXP_REPLACE(r.message, '<[^>]+>', ''), 100) as message_preview,
                       u.first_name, u.last_name,
                       t.subject as ticket_subject,
                       t.created_at as ticket_created_at,
                       t.status as ticket_status
                FROM tickets_replies r
                LEFT JOIN users u ON u.id = r.user_id
                JOIN tickets t ON t.id = r.ticket_id AND t.user_id = {$uid}
                ORDER BY r.id DESC LIMIT 7
            ")->result();
        }
        else
        {
            redirect();
        }
        
        $data['title'] = lang( 'dashboard' );
        $data['view'] = $view;
        
        $this->{$method}( $data, false );
    }
    
    /**
     * Team Members Dashboard Page
     *
     * @return void
     */
    public function admin()
    {
        $this->index( 'admin' );
    }
}
