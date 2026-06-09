<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Area IT Model
 *
 * Handles area IT configuration and staff assignments.
 *
 * @version 1.0
 */
class Area_it_model extends MY_Model {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'area_it_config';
    }
    
    /**
     * Get All Areas
     *
     * @return array
     */
    public function areas()
    {
        $data['table'] = 'area_it_config';
        $data['order_by'] = 'area_prefix';
        $data['order'] = 'ASC';
        
        return $this->get( $data );
    }
    
    /**
     * Get Single Area
     *
     * @param  integer $id
     * @return object
     */
    public function area( $id )
    {
        $data['table'] = 'area_it_config';
        $data['where'] = ['id' => $id];
        
        return $this->get_one( $data );
    }
    
    /**
     * Get Area by Prefix
     *
     * @param  string $prefix
     * @return object
     */
    public function get_area_by_prefix( $prefix )
    {
        $data['table'] = 'area_it_config';
        $data['where'] = ['area_prefix' => $prefix];
        
        return $this->get_one( $data );
    }
    
    /**
     * Add Area
     *
     * @param  array $data
     * @return integer
     */
    public function add_area( $data )
    {
        return $this->add( $data );
    }
    
    /**
     * Update Area
     *
     * @param  integer $id
     * @param  array   $update_data
     * @return boolean
     */
    public function update_area( $id, $update_data )
    {
        $data['column_value'] = $id;
        $data['data'] = $update_data;
        
        return $this->update( $data );
    }
    
    /**
     * Delete Area
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_area( $id )
    {
        // Remove all staff assignments first
        $this->db->where( 'area_id', $id );
        $this->db->delete( 'area_it_staff' );
        
        $data['column_value'] = $id;
        return $this->remove( $data );
    }
    
    /**
     * Get IT Staff for an Area
     *
     * @param  integer $area_id
     * @return array
     */
    public function get_staff_by_area_id( $area_id )
    {
        $this->db->select( 's.id as assignment_id, s.area_id, s.user_id, u.first_name, u.last_name, u.email_address' );
        $this->db->from( 'area_it_staff s' );
        $this->db->join( 'users u', 'u.id = s.user_id', 'LEFT' );
        $this->db->where( 's.area_id', $area_id );
        $this->db->order_by( 'u.first_name', 'ASC' );
        
        return $this->db->get()->result();
    }
    
    /**
     * Get IT Staff by Area Prefix
     *
     * @param  string $prefix
     * @return array
     */
    public function get_staff_by_prefix( $prefix )
    {
        $this->db->select( 's.id as assignment_id, s.user_id, u.first_name, u.last_name, u.email_address, c.area_prefix, c.support_email' );
        $this->db->from( 'area_it_staff s' );
        $this->db->join( 'area_it_config c', 'c.id = s.area_id', 'LEFT' );
        $this->db->join( 'users u', 'u.id = s.user_id', 'LEFT' );
        $this->db->where( 'c.area_prefix', $prefix );
        $this->db->order_by( 'u.first_name', 'ASC' );
        
        return $this->db->get()->result();
    }
    
    /**
     * Add Staff to Area
     *
     * @param  array $data
     * @return integer
     */
    public function add_staff( $data )
    {
        $this->db->insert( 'area_it_staff', $data );
        return $this->db->insert_id();
    }
    
    /**
     * Remove Staff from Area
     *
     * @param  integer $assignment_id
     * @return boolean
     */
    public function remove_staff( $assignment_id )
    {
        $this->db->where( 'id', $assignment_id );
        return $this->db->delete( 'area_it_staff' );
    }
    
    /**
     * Get All Team Users (for dropdown)
     *
     * @return array
     */
    public function get_team_users()
    {
        $this->db->select( 'id, first_name, last_name, email_address' );
        $this->db->from( 'users' );
        $this->db->where_in( 'role', [1, 2] );
        $this->db->order_by( 'first_name', 'ASC' );
        
        return $this->db->get()->result();
    }
}
