<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Area Managers Controller ( Admin )
 *
 * @version 1.0
 */
class Area_managers extends MY_Controller {
    
    /**
     * Area Managers List Page
     *
     * @return void
     */
    public function index()
    {
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        check_page_authorization( 'departments' );
        
        $this->load->model( 'Area_manager_model' );
        $this->load->model( 'Custom_field_model' );
        $this->set_admin_reference( 'tickets_and_chats' );
        $this->area = 'admin';
        
        // Get all area managers
        $managers = $this->Area_manager_model->area_managers();
        
        // Get shops for each manager
        if ( ! empty( $managers ) )
        {
            foreach ( $managers as &$manager )
            {
                $manager->shops = $this->Area_manager_model->get_shops( $manager->id );
            }
        }
        
        // Get all shop names from custom field (id=3 is Shop Name)
        $shop_field = $this->Custom_field_model->custom_field( 3 );
        $all_shops = [];
        if ( ! empty( $shop_field ) && ! empty( $shop_field->options ) )
        {
            $all_shops = array_map( 'trim', explode( ',', $shop_field->options ) );
        }
        
        // Get unassigned shops
        $unassigned_shops = $this->Area_manager_model->get_unassigned_shops( $all_shops );
        
        $data['data']['managers'] = $managers;
        $data['data']['unassigned_shops'] = $unassigned_shops;
        $data['data']['all_shops'] = $all_shops;
        $data['title'] = 'Area Managers';
        $data['view'] = 'area_managers';
        
        $this->load_panel_template( $data, false );
    }
}
