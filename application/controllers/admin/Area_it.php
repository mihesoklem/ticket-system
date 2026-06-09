<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Area IT Controller ( Admin )
 *
 * @version 1.0
 */
class Area_it extends MY_Controller {
    
    /**
     * Area IT Config Page
     *
     * @return void
     */
    public function index()
    {
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        check_page_authorization( 'departments' );
        
        $this->load->model( 'Area_it_model' );
        $this->set_admin_reference( 'tickets_and_chats' );
        $this->area = 'admin';
        
        // Get all areas with their staff
        $areas = $this->Area_it_model->areas();
        
        if ( ! empty( $areas ) )
        {
            foreach ( $areas as &$area )
            {
                $area->staff = $this->Area_it_model->get_staff_by_area_id( $area->id );
            }
        }
        
        // Get team users for add staff dropdown
        $team_users = $this->Area_it_model->get_team_users();
        
        $data['data']['areas'] = $areas;
        $data['data']['team_users'] = $team_users;
        $data['title'] = 'Area IT Configuration';
        $data['view'] = 'area_it';
        
        $this->load_panel_template( $data, false );
    }
}
