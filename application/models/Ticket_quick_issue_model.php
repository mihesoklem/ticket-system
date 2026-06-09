<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Ticket Quick Issue Model
 *
 * @version 1.0
 */
class Ticket_quick_issue_model extends CI_Model {

    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'ticket_quick_issues';
    }

    /**
     * Get All Active Buttons
     *
     * @return array
     */
    public function get_all_active()
    {
        $this->db->where( 'is_active', 1 );
        $this->db->order_by( 'sort_order', 'ASC' );
        $query = $this->db->get( $this->table );
        
        return $query->result();
    }

    /**
     * Get Button by ID
     *
     * @param  integer $id
     * @return object|null
     */
    public function get( $id )
    {
        $this->db->where( 'id', $id );
        $query = $this->db->get( $this->table );
        
        return $query->row();
    }

    /**
     * Add New Button
     *
     * @param  array $data
     * @return mixed
     */
    public function add( $data )
    {
        $data['created_at'] = time();
        
        return $this->db->insert( $this->table, $data );
    }

    /**
     * Update Button
     *
     * @param  integer $id
     * @param  array   $data
     * @return boolean
     */
    public function update( $id, $data )
    {
        $this->db->where( 'id', $id );
        
        return $this->db->update( $this->table, $data );
    }

    /**
     * Delete Button (Soft)
     *
     * @param  integer $id
     * @return boolean
     */
    public function deactivate( $id )
    {
        $this->db->where( 'id', $id );
        
        return $this->db->update( $this->table, ['is_active' => 0] );
    }
}
