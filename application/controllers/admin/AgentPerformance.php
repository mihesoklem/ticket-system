<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Agent Performance Controller ( Admin )
 *
 * @author  System
 * @version 1.4
 */
class AgentPerformance extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        check_page_authorization( 'reports' );
        
        $this->load->model( 'Report_model' );
        
        $this->area = 'admin';
        $this->sub_area = 'support';  // CRITICAL: This was missing!
    }
    
    /**
     * Agent Performance Report Page
     *
     * @return void
     */
    public function index()
    {
        $this->set_admin_reference( 'support' );
        
        // Get period from request, default to all time (0)
        $period = $this->input->get( 'period' );
        $period = ( ! empty( $period ) ) ? intval( $period ) : 0;
        
        $stamp = $this->get_period_timestamp( $period );
        
        // Get agent performance data
        $data['data']['agents'] = $this->Report_model->agent_performance( $stamp );
        $data['data']['summary'] = $this->Report_model->agent_performance_summary( $stamp );
        $data['data']['period'] = $period;
        $data['data']['period_label'] = $this->get_period_language_key( $period );
        
        $data['title'] = lang( 'agent_performance' );
        $data['view'] = 'agent_performance';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Get Period Language Key
     *
     * @param  integer $period
     * @return string
     */
    private function get_period_language_key( $period )
    {
        if ( $period === 1 ) $key = 'past_3_days';
        else if ( $period === 2 ) $key = 'past_7_days';
        else if ( $period === 3 ) $key = 'past_2_weeks';
        else if ( $period === 4 ) $key = 'past_1_month';
        else if ( $period === 5 ) $key = 'past_3_months';
        else if ( $period === 6 ) $key = 'past_6_months';
        else if ( $period === 7 ) $key = 'past_12_months';
        else $key = 'all_time';
        
        return $key;
    }
    
    /**
     * Get Period Timestamp
     *
     * @param  integer $period
     * @return integer
     */
    private function get_period_timestamp( $period )
    {
        if ( $period === 1 ) $stamp = subtract_time( '3 days' );
        else if ( $period === 2 ) $stamp = subtract_time( '7 days' );
        else if ( $period === 3 ) $stamp = subtract_time( '14 days' );
        else if ( $period === 4 ) $stamp = subtract_time( '1 month' );
        else if ( $period === 5 ) $stamp = subtract_time( '3 months' );
        else if ( $period === 6 ) $stamp = subtract_time( '6 months' );
        else if ( $period === 7 ) $stamp = subtract_time( '12 months' );
        else $stamp = 0;
        
        return $stamp;
    }
}
