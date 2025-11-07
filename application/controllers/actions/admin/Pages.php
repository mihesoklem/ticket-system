<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Pages Controller ( Admin, Actions )
 *
 * @author Shahzaib
 */
class Pages extends MY_Controller {
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in ) r_s_jump( 'login' );
        
        check_action_authorization( 'pages' );
        
        $this->load->library( 'form_validation' );
        $this->load->model( 'Page_model' );
    }
    
    /**
     * Update Page Input Handling.
     *
     * @return void
     */
    public function update_page()
    {
        if ( $this->form_validation->run( 'page' ) )
        {
            $id = intval( post( 'id' ) );
            
            $data = [
                'meta_description' => do_secure( post( 'meta_description' ) ),
                'meta_keywords' => do_secure( post( 'meta_keywords' ) ),
                'content' => do_secure( post( 'content' ), true )
            ];
            
            if ( $this->Page_model->update_page( $data, $id ) )
            {
                r_s_jump( 'admin/pages', 'updated' );
            }
            
            r_error( 'not_updated' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Create Custom Page Input Handling.
     *
     * @return  void
     * @version 1.8
     */
    public function create_custom_page()
    {
        if ( $this->form_validation->run( 'custom_page' ) )
        {
            $data = [
                'meta_description' => do_secure( post( 'meta_description' ) ),
                'meta_keywords' => do_secure( post( 'meta_keywords' ) ),
                'name' => do_secure( post( 'name' ) ),
                'content' => do_secure( post( 'content' ), true ),
                'slug' => do_secure( post( 'slug' ) ),
                'visibility' => only_binary( post( 'visibility' ) ),
                'created_by' => $this->zuser->get( 'id' ),
                'created_at' => time()
            ];
            
            if ( empty( $data['slug'] ) )
            {
                $data['slug'] = $this->Page_model->custom_pages_slug( $data['name'] );
            }
            
            if ( $this->Page_model->custom_page( $data['slug'], 'slug' ) )
            {
                r_error( 'slug_exists' );
            }
            
            $id = $this->Page_model->add_custom_page( $data );
            
            if ( ! empty( $id ) )
            {
                r_s_jump( 'admin/pages/custom', 'added' );
            }
            
            r_error( 'went_wrong' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Update Custom Page Input Handling.
     *
     * @return  void
     * @version 1.8
     */
    public function update_custom_page()
    {
        if ( $this->form_validation->run( 'custom_page' ) )
        {
            $id = intval( post( 'id' ) );
            
            $data = [
                'meta_description' => do_secure( post( 'meta_description' ) ),
                'meta_keywords' => do_secure( post( 'meta_keywords' ) ),
                'name' => do_secure( post( 'name' ) ),
                'content' => do_secure( post( 'content' ), true ),
                'slug' => do_secure( post( 'slug' ) ),
                'visibility' => only_binary( post( 'visibility' ) )
            ];
            
            if ( empty( $data['slug'] ) )
            {
                $data['slug'] = $this->Page_model->custom_pages_slug( $data['name'], $id );
            }
            
            if ( $this->Page_model->is_cp_exists_by( $data['slug'], $id ) )
            {
                r_error( 'slug_exists' );
            }
            
            if ( $this->Page_model->update_custom_page( $data, $id ) )
            {
                r_s_jump( 'admin/pages/custom', 'updated' );
            }
            
            r_error( 'not_updated' );
        }
        
        d_r_error( validation_errors() );
    }
    
    /**
     * Delete Custom Page
     *
     * @return  void
     * @version 1.8
     */
    public function delete_custom_page()
    {
        $id = intval( post( 'id' ) );
        
        $data = $this->Page_model->custom_page( $id );
        
        if ( ! empty( $data ) )
        {
            if ( $this->Page_model->delete_custom_page( $id ) )
            {
                r_s_jump( 'admin/pages/custom', 'deleted' );
            }
            
            r_error( 'went_wrong' );
        }
        
        r_error( 'invalid_req' );
    }
}
