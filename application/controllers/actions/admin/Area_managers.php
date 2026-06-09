<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Area Managers Actions Controller ( Admin )
 *
 * @version 1.0
 */
class Area_managers extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
        $this->load->model( 'Area_manager_model' );
    }
    
    /**
     * Add Area Manager
     *
     * @return void
     */
    public function add()
    {
        check_action_authorization( 'departments' );
        
        $name = trim( post( 'name' ) );
        $email = trim( post( 'email' ) );
        
        if ( empty( $name ) || empty( $email ) )
        {
            r_error( 'invalid_req' );
        }
        
        if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) )
        {
            r_error( 'invalid_req' );
        }
        
        $data = [
            'name' => do_secure( $name ),
            'email' => do_secure( $email ),
            'status' => 1,
            'updated_at' => time(),
            'created_at' => time()
        ];
        
        $id = $this->Area_manager_model->add_area_manager( $data );
        
        if ( ! empty( $id ) )
        {
            r_s_jump( 'admin/area_managers', 'Area manager added successfully' );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Update Area Manager
     *
     * @return void
     */
    public function update()
    {
        check_action_authorization( 'departments' );
        
        $id = intval( post( 'id' ) );
        $manager = $this->Area_manager_model->area_manager( $id );
        
        if ( empty( $manager ) ) r_error( 'invalid_req' );
        
        $name = trim( post( 'name' ) );
        $email = trim( post( 'email' ) );
        
        if ( empty( $name ) || empty( $email ) )
        {
            r_error( 'invalid_req' );
        }
        
        if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) )
        {
            r_error( 'invalid_req' );
        }
        
        $to_update = [
            'name' => do_secure( $name ),
            'email' => do_secure( $email ),
            'updated_at' => time()
        ];
        
        if ( $this->Area_manager_model->update_area_manager( $to_update, $id ) )
        {
            r_s_jump( 'admin/area_managers', 'Area manager updated successfully' );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Delete Area Manager
     *
     * @return void
     */
    public function delete()
    {
        check_action_authorization( 'departments' );
        
        $id = intval( post( 'id' ) );
        $manager = $this->Area_manager_model->area_manager( $id );
        
        if ( empty( $manager ) ) r_error( 'invalid_req' );
        
        // Soft delete - set status to 0
        $to_update = [
            'status' => 0,
            'updated_at' => time()
        ];
        
        if ( $this->Area_manager_model->update_area_manager( $to_update, $id ) )
        {
            r_s_jump( 'admin/area_managers', 'Area manager removed successfully' );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Assign Shop to Manager
     *
     * @return void
     */
    public function assign_shop()
    {
        check_action_authorization( 'departments' );
        
        $manager_id = intval( post( 'manager_id' ) );
        $shop_names = $this->input->post( 'shop_names' );
        
        if ( empty( $manager_id ) || empty( $shop_names ) || ! is_array( $shop_names ) )
        {
            r_error( 'invalid_req' );
        }
        
        $manager = $this->Area_manager_model->area_manager( $manager_id );
        
        if ( empty( $manager ) ) r_error( 'invalid_req' );
        
        $count = 0;
        
        foreach ( $shop_names as $shop_name )
        {
            $shop_name = trim( $shop_name );
            if ( empty( $shop_name ) ) continue;
            
            $data = [
                'shop_name' => do_secure( $shop_name ),
                'area_manager_id' => $manager_id,
                'created_at' => time()
            ];
            
            if ( $this->Area_manager_model->assign_shop( $data ) )
            {
                $count++;
            }
        }
        
        if ( $count > 0 )
        {
            r_s_jump( 'admin/area_managers', $count . ' shop(s) assigned successfully' );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Unassign Shop
     *
     * @return void
     */
    public function unassign_shop()
    {
        check_action_authorization( 'departments' );
        
        $id = intval( post( 'id' ) );
        
        if ( empty( $id ) ) r_error( 'invalid_req' );
        
        if ( $this->Area_manager_model->unassign_shop( $id ) )
        {
            r_s_jump( 'admin/area_managers', 'Shop unassigned successfully' );
        }
        
        r_error( 'went_wrong' );
    }
}
