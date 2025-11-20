<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Report Model
 *
 * @author  Shahzaib
 * @version 1.4
 */
class Report_model extends MY_Model {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'reports';
    }
    
    /**
     * Reports
     *
     * @param  boolean $count
     * @param  integer $limit
     * @param  integer $offset
     * @return mixed
     */
    public function reports( $count = false, $limit = 0, $offset = 0 )
    {
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        if ( $count === true )
        {
            return $this->get_count( $data );
        }
        
        return $this->get( $data );
    }
    
    /**
     * Report
     *
     * @param  integer $id
     * @return object
     */
    public function report( $id )
    {
        $data['column_value'] = $id;
        $data['table'] = 'reports';
        
        return $this->get_one( $data );
    }
    
    /**
     * Users Count
     *
     * @param  integer $period
     * @return integer
     */
    public function users( $period )
    {
        $data['table'] = 'users';
        
        if ( ! empty( $period ) )
        {
            $data['where'] = ['registered_at >=' => $period];
        }
        
        return $this->get_count( $data );
    }
    
    /**
     * Opened Tickets Count
     *
     * @param  integer $period
     * @return integer
     */
    public function opened_tickets( $period )
    {
        $data['table'] = 'tickets';
        $data['where']['status'] = 1;
        
        if ( ! empty( $period ) )
        {
            $data['where']['created_at >='] = $period;
        }
        
        return $this->get_count( $data );
    }
    
    /**
     * Closed Tickets Count
     *
     * @param  integer $period
     * @return integer
     */
    public function closed_tickets( $period )
    {
        $data['table'] = 'tickets';
        $data['where']['status'] = 0;
        
        if ( ! empty( $period ) )
        {
            $data['where']['created_at >='] = $period;
        }
        
        return $this->get_count( $data );
    }
    
    /**
     * Solved Tickets Count
     *
     * @param  integer $period
     * @return integer
     */
    public function solved_tickets( $period )
    {
        $data['table'] = 'tickets';
        $data['where']['sub_status'] = 3;
        
        if ( ! empty( $period ) )
        {
            $data['where']['created_at >='] = $period;
        }
        
        return $this->get_count( $data );
    }
    
    /**
     * Total Tickets Count
     *
     * @param  integer $period
     * @return integer
     */
    public function total_tickets( $period )
    {
        $data['table'] = 'tickets';
        
        if ( ! empty( $period ) )
        {
            $data['where']['created_at >='] = $period;
        }
        
        return $this->get_count( $data );
    }
    
    /**
     * Active Chats Count
     *
     * @param  integer $period
     * @return integer
     */
    public function active_chats( $period )
    {
        $data['table'] = 'chats';
        $data['where']['status'] = 1;
        
        if ( ! empty( $period ) )
        {
            $data['where']['created_at >='] = $period;
        }
        
        return $this->get_count( $data );
    }
    
    /**
     * Ended Chats Count
     *
     * @param  integer $period
     * @return integer
     */
    public function ended_chats( $period )
    {
        $data['table'] = 'chats';
        $data['where']['status'] = 0;
        
        if ( ! empty( $period ) )
        {
            $data['where']['created_at >='] = $period;
        }
        
        return $this->get_count( $data );
    }
    
    /**
     * Total Chats Count
     *
     * @param  integer $period
     * @return integer
     */
    public function total_chats( $period )
    {
        $data['table'] = 'chats';
        
        if ( ! empty( $period ) )
        {
            $data['where']['created_at >='] = $period;
        }
        
        return $this->get_count( $data );
    }
    
    /**
     * Add Report
     *
     * @param  array $data
     * @return mixed
     */
    public function add_report( $data )
    {
        return $this->add( $data );
    }
    
    /**
     * Delete Report
     *
     * @param  integer $id
     * @return boolean
     */
    public function delete_report( $id )
    {
        $data['column_value'] = $id;
        
        return $this->delete( $data );
    }

    /**
     * Get Agent Performance Report
     *
     * @param  integer $period
     * @return array
     */
    public function agent_performance( $period = 0 )
    {
        // Get all users who have assigned tickets
        $this->db->select('u.id, u.first_name, u.last_name', FALSE);
        $this->db->from('users u');
        $this->db->join('tickets t', 't.assigned_to = u.id', 'inner');
        $this->db->where('t.assigned_to IS NOT NULL', NULL, FALSE);
        $this->db->group_by('u.id');
        
        // Add date filter if period is set
        if ( $period > 0 ) {
            $this->db->where('t.created_at >=', $period);
        }
        
        $query = $this->db->get();
        $agents = $query->result();
        
        $result = array();
        
        // For each agent, count their tickets
        foreach ( $agents as $agent )
        {
            $agent_id = $agent->id;
            
            // Count total assigned
            $this->db->where('assigned_to', $agent_id);
            if ( $period > 0 ) {
                $this->db->where('created_at >=', $period);
            }
            $total = $this->db->count_all_results('tickets');
            
            // Count closed
            $this->db->where('assigned_to', $agent_id);
            $this->db->where('status', 0);
            if ( $period > 0 ) {
                $this->db->where('created_at >=', $period);
            }
            $closed = $this->db->count_all_results('tickets');
            
            // Count open
            $this->db->where('assigned_to', $agent_id);
            $this->db->where('status', 1);
            if ( $period > 0 ) {
                $this->db->where('created_at >=', $period);
            }
            $open = $this->db->count_all_results('tickets');
            
            // Build result object
            $obj = new stdClass();
            $obj->id = $agent->id;
            $obj->first_name = $agent->first_name;
            $obj->last_name = $agent->last_name;
            $obj->total_assigned = $total;
            $obj->closed = $closed;
            $obj->open = $open;
            $obj->closure_rate = ( $total > 0 ) ? round( ( $closed / $total ) * 100, 1 ) : 0;
            
            $result[] = $obj;
        }
        
        // Sort by closed DESC
        usort( $result, function( $a, $b ) {
            return $b->closed - $a->closed;
        });
        
        return $result;
    }

    /**
     * Get Agent Performance Summary (Totals)
     *
     * @param  integer $period
     * @return object
     */
    public function agent_performance_summary( $period = 0 )
    {
        // Count all assigned tickets
        $this->db->where('assigned_to !=', NULL);
        if ( $period > 0 ) {
            $this->db->where('created_at >=', $period);
        }
        $total_assigned = $this->db->count_all_results('tickets');
        
        // Count all closed
        $this->db->where('assigned_to !=', NULL);
        $this->db->where('status', 0);
        if ( $period > 0 ) {
            $this->db->where('created_at >=', $period);
        }
        $total_closed = $this->db->count_all_results('tickets');
        
        // Count all open
        $this->db->where('assigned_to !=', NULL);
        $this->db->where('status', 1);
        if ( $period > 0 ) {
            $this->db->where('created_at >=', $period);
        }
        $total_open = $this->db->count_all_results('tickets');
        
        $summary = new stdClass();
        $summary->total_assigned = $total_assigned;
        $summary->total_closed = $total_closed;
        $summary->total_open = $total_open;
        $summary->closure_rate = ( $total_assigned > 0 ) ? round( ( $total_closed / $total_assigned ) * 100, 1 ) : 0;
        
        return $summary;
    }
}
