<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Area Manager Model
 *
 * Handles area manager CRUD and shop-manager assignments.
 *
 * @version 1.0
 */
class Area_manager_model extends MY_Model {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'area_managers';
    }
    
    /**
     * Get All Area Managers
     *
     * @param  string $order
     * @return object
     */
    public function area_managers( $order = 'ASC' )
    {
        $data['order'] = $order;
        $data['where']['status'] = 1;
        
        return $this->get( $data );
    }
    
    /**
     * Get Single Area Manager
     *
     * @param  integer $id
     * @return object
     */
    public function area_manager( $id )
    {
        $data['column_value'] = $id;
        
        return $this->get_one( $data );
    }
    
    /**
     * Add Area Manager
     *
     * @param  array $data
     * @return mixed
     */
    public function add_area_manager( $data )
    {
        return $this->add( $data );
    }
    
    /**
     * Update Area Manager
     *
     * @param  array   $to_update
     * @param  integer $id
     * @return boolean
     */
    public function update_area_manager( $to_update, $id )
    {
        $data['column_value'] = $id;
        $data['data'] = $to_update;

        return $this->update( $data );
    }
    
    /**
     * Delete Area Manager
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_area_manager( $id )
    {
        $data['column_value'] = $id;
        
        return $this->delete( $data );
    }
    
    /**
     * Get Shops Assigned to a Manager
     *
     * @param  integer $manager_id
     * @return object
     */
    public function get_shops( $manager_id )
    {
        $data['where']['area_manager_id'] = $manager_id;
        $data['table'] = 'shop_area_manager';
        $data['orderby_column'] = 'shop_name';
        $data['order'] = 'ASC';
        
        return $this->get( $data );
    }
    
    /**
     * Get Area Manager by Shop Name
     *
     * Looks up which manager is assigned to a given shop.
     * Returns the area_manager object or null (first match).
     *
     * @param  string $shop_name
     * @return object|null
     */
    public function get_manager_by_shop( $shop_name )
    {
        $this->db->select( 'am.*' );
        $this->db->from( 'shop_area_manager sam' );
        $this->db->join( 'area_managers am', 'am.id = sam.area_manager_id' );
        $this->db->where( 'sam.shop_name', $shop_name );
        $this->db->where( 'am.status', 1 );
        
        $result = $this->db->get()->row();
        
        return ! empty( $result ) ? $result : null;
    }
    
    /**
     * Get All Area Managers by Shop Name
     *
     * Returns all managers assigned to a given shop.
     *
     * @param  string $shop_name
     * @return array
     */
    public function get_managers_by_shop( $shop_name )
    {
        $this->db->select( 'am.*' );
        $this->db->from( 'shop_area_manager sam' );
        $this->db->join( 'area_managers am', 'am.id = sam.area_manager_id' );
        $this->db->where( 'sam.shop_name', $shop_name );
        $this->db->where( 'am.status', 1 );
        
        $result = $this->db->get()->result();
        
        return ! empty( $result ) ? $result : [];
    }
    
    /**
     * Assign Shop to Manager
     *
     * @param  array $data
     * @return mixed
     */
    public function assign_shop( $data )
    {
        return $this->add( $data, 'shop_area_manager' );
    }
    
    /**
     * Unassign Shop
     *
     * @param  integer $id
     * @return boolean
     */
    public function unassign_shop( $id )
    {
        $data['where']['id'] = $id;
        $data['table'] = 'shop_area_manager';
        
        return $this->delete( $data );
    }
    
    /**
     * Get All Assignments (with manager info)
     *
     * @return object
     */
    public function get_all_assignments()
    {
        $this->db->select( 'sam.*, am.name as manager_name, am.email as manager_email' );
        $this->db->from( 'shop_area_manager sam' );
        $this->db->join( 'area_managers am', 'am.id = sam.area_manager_id' );
        $this->db->order_by( 'sam.shop_name', 'ASC' );
        
        return $this->db->get()->result();
    }
    
    /**
     * Check if Manager Has Assignments
     *
     * @param  integer $manager_id
     * @return boolean
     */
    public function has_assignments( $manager_id )
    {
        $this->db->where( 'area_manager_id', $manager_id );
        
        return $this->db->count_all_results( 'shop_area_manager' ) > 0;
    }
    
    /**
     * Get Unassigned Shop Names
     *
     * Returns shop names from the custom field options that are not yet assigned.
     *
     * @param  array $all_shops  Array of all shop name strings
     * @return array
     */
    public function get_unassigned_shops( $all_shops )
    {
        $assigned = $this->db->select( 'shop_name' )
                             ->get( 'shop_area_manager' )
                             ->result_array();
        
        $assigned_names = array_column( $assigned, 'shop_name' );
        
        return array_diff( $all_shops, $assigned_names );
    }
}
