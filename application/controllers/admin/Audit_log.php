<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Audit_log extends MY_Controller {
    
    public function index()
    {
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        check_page_authorization( 'departments' );
        
        $this->set_admin_reference( 'tickets_and_chats' );
        $this->area = 'admin';
        
        $data['data']['logs'] = [];
        $data['data']['total'] = 0;
        $data['data']['page'] = 1;
        $data['data']['per_page'] = 50;
        $data['data']['total_pages'] = 0;
        $data['data']['filter_user'] = 0;
        $data['data']['filter_entity'] = '';
        $data['data']['entities'] = [];
        $data['data']['team_users'] = [];
        $data['title'] = 'Audit Log';
        $data['view'] = 'audit_log';
        
        $this->load_panel_template( $data, false );
    }
}
