<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Ticket_analytics extends MY_Controller {
    
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        check_page_authorization( 'reports' );
        
        $this->area = 'admin';
    }
    
    public function index()
    {
        $this->set_admin_reference( 'others' );
        
        // Filters
        $date_from = $this->input->get( 'from' );
        $date_to = $this->input->get( 'to' );
        $filter_area = $this->input->get( 'area' );
        $filter_type = $this->input->get( 'type' );
        $filter_shop = $this->input->get( 'shop' );
        
        $where_date = '';
        $where_area = '';
        $where_type = '';
        $where_shop = '';
        
        if ( ! empty( $date_from ) )
        {
            $ts_from = strtotime( $date_from . ' 00:00:00' );
            if ( $ts_from ) { $where_date .= ' AND t.created_at >= ' . intval( $ts_from ); }
        }
        if ( ! empty( $date_to ) )
        {
            $ts_to = strtotime( $date_to . ' 23:59:59' );
            if ( $ts_to ) { $where_date .= ' AND t.created_at <= ' . intval( $ts_to ); }
        }
        if ( ! empty( $filter_area ) )
        {
            $safe_area = $this->db->escape_like_str( $filter_area );
            $where_area = " AND cf3.value LIKE '{$safe_area} - %'";
            if ( $filter_area === 'Head Office' ) $where_area = " AND cf3.value = 'Head Office'";
        }
        if ( ! empty( $filter_type ) )
        {
            $safe_type = $this->db->escape_like_str( $filter_type );
            $where_type = " AND cf2.value = '{$safe_type}'";
        }
        if ( ! empty( $filter_shop ) )
        {
            $safe_shop = $this->db->escape_like_str( $filter_shop );
            $where_shop = " AND cf3.value = '{$safe_shop}'";
        }
        
        // Combined filter for queries that use both cf3 and cf2
        $where_all = $where_date . $where_area . $where_type . $where_shop;
        // For queries using only cf3 (area/shop)
        $where_cf3 = $where_date;
        if ( ! empty( $filter_area ) )
        {
            $safe_area = $this->db->escape_like_str( $filter_area );
            $area_cond = " AND cf.value LIKE '{$safe_area} - %'";
            if ( $filter_area === 'Head Office' ) $area_cond = " AND cf.value = 'Head Office'";
            $where_cf3 .= $area_cond;
        }
        if ( ! empty( $filter_shop ) )
        {
            $safe_shop = $this->db->escape_like_str( $filter_shop );
            $where_cf3 .= " AND cf.value = '{$safe_shop}'";
        }
        // For queries using only cf2 (type)
        $where_cf2 = $where_date;
        if ( ! empty( $filter_type ) )
        {
            $safe_type = $this->db->escape_like_str( $filter_type );
            $where_cf2 .= " AND cf.value = '{$safe_type}'";
        }
        
        // Summary counts (needs joins for area/type/shop filters)
        if ( ! empty( $filter_area ) || ! empty( $filter_type ) || ! empty( $filter_shop ) )
        {
            $sum_joins = '';
            $sum_where = $where_date;
            if ( ! empty( $filter_area ) || ! empty( $filter_shop ) )
            {
                $sum_joins .= " JOIN tickets_custom_fields cf3s ON cf3s.ticket_id = t.id AND cf3s.custom_field_id = 3";
                if ( ! empty( $filter_shop ) ) {
                    $safe_shop = $this->db->escape_like_str( $filter_shop );
                    $sum_where .= " AND cf3s.value = '{$safe_shop}'";
                } else {
                    $safe_area = $this->db->escape_like_str( $filter_area );
                    if ( $filter_area === 'Head Office' ) $sum_where .= " AND cf3s.value = 'Head Office'";
                    else $sum_where .= " AND cf3s.value LIKE '{$safe_area} - %'";
                }
            }
            if ( ! empty( $filter_type ) )
            {
                $safe_type = $this->db->escape_like_str( $filter_type );
                $sum_joins .= " JOIN tickets_custom_fields cf2s ON cf2s.ticket_id = t.id AND cf2s.custom_field_id = 2";
                $sum_where .= " AND cf2s.value = '{$safe_type}'";
            }
            $summary = $this->db->query("
                SELECT COUNT(*) as total,
                    SUM(CASE WHEN t.status = 1 THEN 1 ELSE 0 END) as open_count,
                    SUM(CASE WHEN t.status = 0 THEN 1 ELSE 0 END) as closed_count,
                    SUM(CASE WHEN t.assigned_to IS NULL THEN 1 ELSE 0 END) as unassigned
                FROM tickets t {$sum_joins}
                WHERE 1=1 {$sum_where}
            ")->row();
        }
        else
        {
            $summary = $this->db->query("
                SELECT COUNT(*) as total,
                    SUM(CASE WHEN t.status = 1 THEN 1 ELSE 0 END) as open_count,
                    SUM(CASE WHEN t.status = 0 THEN 1 ELSE 0 END) as closed_count,
                    SUM(CASE WHEN t.assigned_to IS NULL THEN 1 ELSE 0 END) as unassigned
                FROM tickets t
                WHERE 1=1 {$where_date}
            ")->row();
        }
        
        // Issues by area
        $area_data = $this->db->query("
            SELECT SUBSTRING_INDEX(cf.value, ' - ', 1) as area, COUNT(*) as count
            FROM tickets t
            JOIN tickets_custom_fields cf ON cf.ticket_id = t.id AND cf.custom_field_id = 3
            WHERE cf.value != '' {$where_cf3}
            GROUP BY area ORDER BY count DESC
        ")->result();
        
        // Issues by incident type
        $type_data = $this->db->query("
            SELECT cf.value as incident_type, COUNT(*) as count
            FROM tickets t
            JOIN tickets_custom_fields cf ON cf.ticket_id = t.id AND cf.custom_field_id = 2
            WHERE cf.value != '' {$where_cf2}
            GROUP BY cf.value ORDER BY count DESC
        ")->result();
        
        // Top 10 shops
        $shop_data = $this->db->query("
            SELECT cf.value as shop, COUNT(*) as count
            FROM tickets t
            JOIN tickets_custom_fields cf ON cf.ticket_id = t.id AND cf.custom_field_id = 3
            WHERE cf.value != '' {$where_cf3}
            GROUP BY cf.value ORDER BY count DESC LIMIT 10
        ")->result();
        
        // Monthly trend
        $trend_joins = '';
        $trend_where = $where_date;
        if ( ! empty( $filter_area ) || ! empty( $filter_shop ) )
        {
            $trend_joins .= " JOIN tickets_custom_fields cf3t ON cf3t.ticket_id = t.id AND cf3t.custom_field_id = 3";
            if ( ! empty( $filter_shop ) ) {
                $safe_shop = $this->db->escape_like_str( $filter_shop );
                $trend_where .= " AND cf3t.value = '{$safe_shop}'";
            } else {
                $safe_area = $this->db->escape_like_str( $filter_area );
                if ( $filter_area === 'Head Office' ) $trend_where .= " AND cf3t.value = 'Head Office'";
                else $trend_where .= " AND cf3t.value LIKE '{$safe_area} - %'";
            }
        }
        if ( ! empty( $filter_type ) )
        {
            $safe_type = $this->db->escape_like_str( $filter_type );
            $trend_joins .= " JOIN tickets_custom_fields cf2t ON cf2t.ticket_id = t.id AND cf2t.custom_field_id = 2";
            $trend_where .= " AND cf2t.value = '{$safe_type}'";
        }
        $trend_data = $this->db->query("
            SELECT DATE_FORMAT(FROM_UNIXTIME(t.created_at), '%Y-%m') as month, COUNT(*) as count
            FROM tickets t {$trend_joins}
            WHERE 1=1 {$trend_where}
            GROUP BY month ORDER BY month ASC
        ")->result();
        
        // Repeat offender shops - merged view with incident type + top issue
        $repeat_data = $this->db->query("
            SELECT cf3.value as shop, cf2.value as incident_type,
                CASE
                    WHEN LOWER(t.subject) LIKE '%printer%' OR LOWER(t.subject) LIKE '%print%' THEN 'Printer'
                    WHEN LOWER(t.subject) LIKE '%tv%' OR LOWER(t.subject) LIKE '%display%' OR LOWER(t.subject) LIKE '%virtual%' THEN 'TV / Display'
                    WHEN LOWER(t.subject) LIKE '%internet%' OR LOWER(t.subject) LIKE '%network%' OR LOWER(t.subject) LIKE '%data%' OR LOWER(t.subject) LIKE '%offline%' THEN 'Network'
                    WHEN LOWER(t.subject) LIKE '%machine%' OR LOWER(t.subject) LIKE '%pc%' OR LOWER(t.subject) LIKE '%system unit%' OR LOWER(t.subject) LIKE '%not turning%' OR LOWER(t.subject) LIKE '%not booting%' OR LOWER(t.subject) LIKE '%not working%' THEN 'Machine / PC'
                    ELSE 'Other'
                END as top_issue,
                COUNT(*) as count
            FROM tickets t
            JOIN tickets_custom_fields cf3 ON cf3.ticket_id = t.id AND cf3.custom_field_id = 3
            JOIN tickets_custom_fields cf2 ON cf2.ticket_id = t.id AND cf2.custom_field_id = 2
            WHERE cf3.value != '' AND cf3.value != 'Head Office' {$where_all}
            GROUP BY cf3.value, cf2.value, top_issue
            HAVING count >= 3 ORDER BY count DESC LIMIT 25
        ")->result();
        
        // Get distinct values for filter dropdowns
        $all_areas = $this->db->query("SELECT DISTINCT SUBSTRING_INDEX(value, ' - ', 1) as area FROM tickets_custom_fields WHERE custom_field_id = 3 AND value != '' ORDER BY area")->result();
        $all_types = $this->db->query("SELECT DISTINCT value as incident_type FROM tickets_custom_fields WHERE custom_field_id = 2 AND value != '' ORDER BY value")->result();
        $all_shops = $this->db->query("SELECT DISTINCT value as shop FROM tickets_custom_fields WHERE custom_field_id = 3 AND value != '' ORDER BY value")->result();
        
        $data['data']['summary'] = $summary;
        $data['data']['area_data'] = json_encode( $area_data );
        $data['data']['type_data'] = json_encode( $type_data );
        $data['data']['shop_data'] = json_encode( $shop_data );
        $data['data']['trend_data'] = json_encode( $trend_data );
        $data['data']['repeat_data'] = $repeat_data;
        $data['data']['date_from'] = $date_from;
        $data['data']['date_to'] = $date_to;
        $data['data']['filter_area'] = $filter_area;
        $data['data']['filter_type'] = $filter_type;
        $data['data']['filter_shop'] = $filter_shop;
        $data['data']['all_areas'] = $all_areas;
        $data['data']['all_types'] = $all_types;
        $data['data']['all_shops'] = $all_shops;
        $data['title'] = 'Ticket Analytics';
        $data['view'] = 'ticket_analytics';
        
        $this->load_panel_template( $data, false );
    }
}
