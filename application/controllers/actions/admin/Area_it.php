<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Area IT Actions Controller ( Admin )
 *
 * @version 1.1 - Added audit logging
 */
class Area_it extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        $this->load->model( 'Area_it_model' );
    }
    
    /**
     * Log an admin action to the audit log
     *
     * @param  string  $action
     * @param  string  $entity
     * @param  integer $entity_id
     * @param  string  $details
     * @return void
     */
    private function audit_log( $action, $entity, $entity_id = null, $details = '' )
    {
        $this->db->insert( 'admin_audit_log', [
            'user_id' => $this->zuser->get( 'id' ),
            'user_name' => $this->zuser->get( 'first_name' ) . ' ' . $this->zuser->get( 'last_name' ),
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entity_id,
            'details' => $details,
            'created_at' => time()
        ]);
    }
    
    /**
     * Add Area
     *
     * @return void
     */
    public function add_area()
    {
        check_action_authorization( 'departments' );
        
        $area_prefix = trim( post( 'area_prefix' ) );
        $support_email = trim( post( 'support_email' ) );
        
        if ( empty( $area_prefix ) )
        {
            r_error( 'invalid_req' );
        }
        
        $data = [
            'area_prefix' => do_secure( $area_prefix ),
            'support_email' => do_secure( $support_email ),
            'created_at' => time()
        ];
        
        $id = $this->Area_it_model->add_area( $data );
        
        if ( ! empty( $id ) )
        {
            $this->audit_log( 'create', 'area_it_config', $id, "Added area: {$area_prefix}, email: {$support_email}" );
            r_s_jump( 'admin/area_it', 'Area added successfully' );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Update Area
     *
     * @return void
     */
    public function update_area()
    {
        check_action_authorization( 'departments' );
        
        $id = intval( post( 'area_id' ) );
        $area_prefix = trim( post( 'area_prefix' ) );
        $support_email = trim( post( 'support_email' ) );
        
        if ( empty( $id ) || empty( $area_prefix ) )
        {
            r_error( 'invalid_req' );
        }
        
        $area = $this->Area_it_model->area( $id );
        if ( empty( $area ) ) r_error( 'invalid_req' );
        
        $old_prefix = $area->area_prefix;
        $old_email = $area->support_email;
        
        $update_data = [
            'area_prefix' => do_secure( $area_prefix ),
            'support_email' => do_secure( $support_email )
        ];
        
        if ( $this->Area_it_model->update_area( $id, $update_data ) )
        {
            $changes = [];
            if ( $old_prefix !== $area_prefix ) $changes[] = "prefix: '{$old_prefix}' -> '{$area_prefix}'";
            if ( $old_email !== $support_email ) $changes[] = "email: '{$old_email}' -> '{$support_email}'";
            $this->audit_log( 'update', 'area_it_config', $id, implode( ', ', $changes ) );
            
            r_s_jump( 'admin/area_it', 'Area updated successfully' );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Delete Area
     *
     * @return void
     */
    public function delete_area()
    {
        check_action_authorization( 'departments' );
        
        $id = intval( post( 'area_id' ) );
        
        if ( empty( $id ) ) r_error( 'invalid_req' );
        
        $area = $this->Area_it_model->area( $id );
        $area_name = ! empty( $area ) ? $area->area_prefix : 'unknown';
        
        if ( $this->Area_it_model->delete_area( $id ) )
        {
            $this->audit_log( 'delete', 'area_it_config', $id, "Deleted area: {$area_name}" );
            r_s_jump( 'admin/area_it', 'Area deleted successfully' );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Add Staff to Area
     *
     * @return void
     */
    public function add_staff()
    {
        check_action_authorization( 'departments' );
        
        $area_id = intval( post( 'area_id' ) );
        $user_id = intval( post( 'user_id' ) );
        
        if ( empty( $area_id ) || empty( $user_id ) )
        {
            r_error( 'invalid_req' );
        }
        
        $area = $this->Area_it_model->area( $area_id );
        if ( empty( $area ) ) r_error( 'invalid_req' );
        
        // Get user name for audit log
        $this->load->model( 'User_model' );
        $user = $this->User_model->get_by_id( $user_id );
        $staff_name = ! empty( $user ) ? $user->first_name . ' ' . $user->last_name : "user_id:{$user_id}";
        
        $data = [
            'area_id' => $area_id,
            'user_id' => $user_id,
            'created_at' => time()
        ];
        
        $id = $this->Area_it_model->add_staff( $data );
        
        if ( ! empty( $id ) )
        {
            $this->audit_log( 'add_staff', 'area_it_staff', $id, "Added {$staff_name} to {$area->area_prefix}" );
            r_s_jump( 'admin/area_it', 'IT staff added successfully' );
        }
        
        r_error( 'went_wrong' );
    }
    
    /**
     * Remove Staff from Area
     *
     * @return void
     */
    public function remove_staff()
    {
        check_action_authorization( 'departments' );
        
        $assignment_id = intval( post( 'assignment_id' ) );
        
        if ( empty( $assignment_id ) ) r_error( 'invalid_req' );
        
        // Get details before deleting for audit log
        $this->db->select( 's.user_id, u.first_name, u.last_name, c.area_prefix' );
        $this->db->from( 'area_it_staff s' );
        $this->db->join( 'users u', 'u.id = s.user_id', 'LEFT' );
        $this->db->join( 'area_it_config c', 'c.id = s.area_id', 'LEFT' );
        $this->db->where( 's.id', $assignment_id );
        $record = $this->db->get()->row();
        
        $details = 'unknown';
        if ( ! empty( $record ) )
        {
            $details = "Removed {$record->first_name} {$record->last_name} from {$record->area_prefix}";
        }
        
        if ( $this->Area_it_model->remove_staff( $assignment_id ) )
        {
            $this->audit_log( 'remove_staff', 'area_it_staff', $assignment_id, $details );
            r_s_jump( 'admin/area_it', 'IT staff removed successfully' );
        }
        
        r_error( 'went_wrong' );
    }
}
