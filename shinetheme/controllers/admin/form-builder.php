<?php
if(!defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}

if(!class_exists( 'Traveler_Admin_Form_Build' )) {
    class Traveler_Admin_Form_Build extends Traveler_Controller
    {
        private static $_inst;

        public static $traveler_param = array();

        protected  $traveler_list_field_form_build = array();

        function __construct()
        {
            add_action( 'admin_menu' , array( $this , "register_traveler_booking_sub_menu_page" ) );

            add_action( 'init' , array( $this , '_add_post_type' ) , 5 );
            add_action( 'init' , array( $this , '_load_default_shortcodes' ) );

            // add script and style
            add_action( 'admin_enqueue_scripts' , array( $this , "_add_scripts" ) );

            add_action( 'admin_init' , array( $this , "_save_layout" ) );
            add_action( 'admin_init' , array( $this , "_del_layout" ) );

        }

        function add_form_field($title , $name , $data){
            if(!empty($name)){
                $data['title'] = $title;
                $this->traveler_list_field_form_build[$name] = $data;
            }
        }
        function get_form_fields($form_id){

			$this->_clear_fields();
			$post=get_post($form_id);

			if($post)
			{
				do_shortcode($post->post_content);
				return $this->traveler_list_field_form_build;
			}

        }
		function _clear_fields()
		{
			$this->traveler_list_field_form_build=array();
		}

        function _load_default_shortcodes()
        {
            Traveler_Loader::inst()->load_library(array(
                'shortcodes/form-build-default/text',
				'shortcodes/form-build-default/email',
               	'shortcodes/form-build-default/textarea',
              	'shortcodes/form-build-default/dropdown',
              	'shortcodes/form-build-default/checkbox',
              	'shortcodes/form-build-default/radio',
              	'shortcodes/form-build-default/submit-button',
            ));
        }
        function _add_scripts()
        {

        }

        function _get_list_type_layout()
        {
            return apply_filters( 'traveler_build_form_list_type_layout' , array(
                'Single Hotel' ,
                'Single Room' ,
                'Booking'
            ) );
        }

        function traveler_add_field_form_builder( $option = array() )
        {
            self::$traveler_param[ ] = $option;
        }

        function traveler_get_all_field()
        {

            $list_field  =array();
            if(!empty(self::$traveler_param)){
                foreach(self::$traveler_param as $k=>$v){
                    $list_field [$v['category']][] = $v;
                }
            }
            return $list_field;
        }

        function _get_list_layout()
        {
            $query = array(
                'post_type'      => 'traveler_form' ,
                'posts_per_page' => -1 ,
            );
            query_posts( $query );
            $list_layout = array();
            while( have_posts() ) {
                the_post();
                $type_layout = get_post_meta( get_the_ID() , 'type_layout' , true );
                if(empty( $type_layout )) {
                    $type_layout = 'Other';
                }
                $list_layout[ $type_layout ][ ] = array(
                    'id'   => get_the_ID() ,
                    'name' => get_the_title()
                );
            }
            wp_reset_query();
            return $list_layout;
        }

        function _del_layout()
        {
            if(Traveler_Input::request( 'del_layout' )) {
                wp_delete_post( Traveler_Input::request( 'del_layout' ) , true );
            }
        }

        function _save_layout()
        {
            if(!empty( $_POST[ 'traveler_booking_btn_save_layout' ] ) and wp_verify_nonce( $_REQUEST[ 'traveler_booking_save_layout' ] , "traveler_booking_action" )) {
                $current_user = wp_get_current_user();
                $form_id    = Traveler_Input::request( "form_builder_id" );
                $title        = Traveler_Input::request( "traveler-title" );
                $type         = 'update';
                if(empty( $form_id )) {
                    $type = 'create';
                }
                if(!empty( $title )) {
                    if(empty( $form_id )) {
                        $my_layout = array(
                            'post_title'   => Traveler_Input::request( "traveler-title" ) ,
                            'post_content' => stripslashes( Traveler_Input::request( "traveler-content-build" ) ) ,
                            'post_status'  => 'publish' ,
                            'post_author'  => $current_user->ID ,
                            'post_type'    => 'traveler_form' ,
                            'post_excerpt' => ''
                        );
                        $form_id = wp_insert_post( $my_layout );
                    } else {
                        $my_layout = array(
                            'ID'           => $form_id ,
                            'post_title'   => Traveler_Input::request( "traveler-title" ) ,
                            'post_content' => stripslashes( Traveler_Input::request( "traveler-content-build" ) ) ,
                        );
                        wp_update_post( $my_layout );
                    }
                    if(!empty( $form_id )) {
                        $type_layout = Traveler_Input::request( "traveler-layout-type" );
                        update_post_meta( $form_id , 'type_layout' , $type_layout );
                        if($type == 'update') {
                            traveler_set_admin_message( __("Update layout successfully !","traveler-booking") , 'success' );

                        } else {
                            traveler_set_admin_message( __("Create layout successfully !","traveler-booking"), 'success' );
                            wp_redirect( add_query_arg(array('page'=>Traveler_Input::request('page'),'form_builder_id'=>$form_id),admin_url('admin.php')) );
                            exit();
                        }

                    } else {
                        if($type == 'update') {
                            traveler_set_admin_message( __("Error : Update layout not successfully !","traveler-booking") , 'error' );
                        } else {
                            traveler_set_admin_message( __('Error : Create layout not successfully !',"traveler-booking") , 'error' );
                        }
                    }
                } else {
                    if($type == 'update') {
                        traveler_set_admin_message( __('Error : Update layout not successfully !','traveler-booking') , 'error' );
                    } else {
                        traveler_set_admin_message( __('Error : Create layout not successfully !',"traveler-booking") , 'error' );
                    }
                }


            }
        }

        function register_traveler_booking_sub_menu_page()
        {

            $menu_page = $this->get_menu_page();

            add_submenu_page(
                $menu_page[ 'parent_slug' ] ,
                $menu_page[ 'page_title' ] ,
                $menu_page[ 'menu_title' ] ,
                $menu_page[ 'capability' ] ,
                $menu_page[ 'menu_slug' ] ,
                $menu_page[ 'function' ]
            );
        }

        function get_menu_page()
        {

            $menu_page = Traveler()->get_menu_page();
            $page      = array(
                'parent_slug' => $menu_page[ 'menu_slug' ] ,
                'page_title'  => __( 'Form Builder' , 'traveler-booking' ) ,
                'menu_title'  => __( 'Form Builder' , 'traveler-booking' ) ,
                'capability'  => 'manage_options' ,
                'menu_slug'   => 'traveler_booking_page_form_builder' ,
                'function'    => array( $this , 'callback_traveler_booking_sub_menu_form_builder' )
            );

            return apply_filters( 'traveler_setting_menu_args' , $page );

        }

        function callback_traveler_booking_sub_menu_form_builder()
        {
            echo $this->admin_load_view( 'form-builder' );
        }

        function _get_all_shortcode_in_content( $content = false )
        {
            $shortcode = array();
            if(!empty( $content )) {
                $pattern = get_shortcode_regex();
                preg_match_all( '/' . $pattern . '/s' , $content , $matches2 );
                if(!empty( $matches2[ 0 ] )) {
                    $shortcode = $matches2[ 0 ];
                }
            }
            return $shortcode;
        }

        function _add_post_type()
        {
            $labels = array(
                'name'               => _x( 'Form Builder' , 'post type general name' , 'traveler-booking' ) ,
                'singular_name'      => _x( 'Form Builder' , 'post type singular name' , 'traveler-booking' ) ,
                'menu_name'          => _x( 'Form Builder' , 'admin menu' , 'traveler-booking' ) ,
                'name_admin_bar'     => _x( 'Form Builder' , 'add new on admin bar' , 'traveler-booking' ) ,
                'add_new'            => _x( 'Add New' , 'service' , 'traveler-booking' ) ,
                'add_new_item'       => __( 'Add New Form Builder' , 'traveler-booking' ) ,
                'new_item'           => __( 'New Form Builder' , 'traveler-booking' ) ,
                'edit_item'          => __( 'Edit Form Builder' , 'traveler-booking' ) ,
                'view_item'          => __( 'View Form Builder' , 'traveler-booking' ) ,
                'all_items'          => __( 'All Form Builders' , 'traveler-booking' ) ,
                'search_items'       => __( 'Search Form Builders' , 'traveler-booking' ) ,
                'parent_item_colon'  => __( 'Parent Form Builders:' , 'traveler-booking' ) ,
                'not_found'          => __( 'No form builder found.' , 'traveler-booking' ) ,
                'not_found_in_trash' => __( 'No form builder found in Trash.' , 'traveler-booking' )
            );

            $args = array(
                'labels'             => $labels ,
                'description'        => __( 'Description.' , 'traveler-booking' ) ,
                'public'             => true ,
                'publicly_queryable' => true ,
                'show_ui'            => true ,
                'show_in_menu'       => false ,
                'query_var'          => true ,
                'rewrite'            => array( 'slug' => 'form_builder' ) ,
                'capability_type'    => 'post' ,
                'has_archive'        => true ,
                'hierarchical'       => false ,
                //'menu_position'      => '59.9',
                'supports'           => array( 'title' , 'editor' )
            );

            register_post_type( 'traveler_form' , $args );
        }

        static function inst()
        {
            if(!self::$_inst) {
                self::$_inst = new self();
            }
            return self::$_inst;
        }

    }

    Traveler_Admin_Form_Build::inst();
}