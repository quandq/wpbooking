<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:28 PM
 */
if (!class_exists('WPBooking_User')) {
	class WPBooking_User
	{
		static $_inst;

		function __construct()
		{
			add_action('init', array($this, '_add_shortcode'));
			add_action('wp_enqueue_scripts', array($this, '_add_scripts'));
			/**
			 * Login & Register handler
			 *
			 * @author dungdt
			 * @since 1.0
			 */
			add_action('init', array($this, '_login_register_handler'));

			/**
			 * Ajax Handler Upload Certificate before Register
			 * @author dungdt
			 * @since 1.0
			 */
			add_action('wp_ajax_nopriv_wpbooking_upload_certificate', array($this, '_ajax_upload_certificate'));
			/**
			 * Ajax Handler Upload Certificate before Register
			 * @author dungdt
			 * @since 1.0
			 */
			add_action('wp_ajax_wpbooking_upload_avatar', array($this, '_ajax_upload_avatar'));


			/**
			 * Send Email to User after Registration
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_action('wpbooking_register_success', array($this, '_send_registration_email'));
			add_action('wpbooking_partner_register_success', array($this, '_send_partner_registration_email'));

			/**
			 * Get Email Shortcode Content
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_filter('wpbooking_registration_email_shortcode', array($this, '_get_shortcode_content'), 10, 3);

			/**
			 * Preview Email
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_action('wp_ajax_wpbooking_register_email_preview',array($this,'_preview_email'));

			/**
			 * Handle Action in My Account Page eg Insert/Update Service
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_action('init',array($this,'_myaccount_page_handler'),20);

			/**
			 * Add Endpoints to My Account Page
			 *
			 * @since 1.0
			 * @author dungdt
			 *
			 */
			add_action( 'init', array( $this, 'add_endpoints' ) );

		}

		/**
		 * Upload Certificate Ajax Handler
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _ajax_upload_certificate()
		{
			$res = array(
				'status' => 1

			);
			if (!function_exists('wp_handle_upload')) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}

			if (empty($_FILES['image'])) {
				echo json_encode(array(
					'status'  => 0,
					'message' => esc_html__('You did not select any file', 'wpbooking')
				));
				die;
			}
			$uploadedfile = $_FILES['image'];

			$size_file = $uploadedfile["size"];

			if ($size_file > (1024 * 1024 * 2)) {
				$res['status'] = 0;
				$res['message'] = esc_html__('Max upload size is 2mb', 'wpbooking');
			} else {
				$allowed_file_types = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png');
				$overrides = array('test_form' => FALSE, 'mimes' => $allowed_file_types);

				$movefile = wp_handle_upload($uploadedfile, $overrides);

				if ($movefile && !isset($movefile['error'])) {
					$res['image'] = $movefile;

				} else {
					$res['status'] = FALSE;
					$res['message'] = $movefile['error'];
				}
			}

			echo json_encode($res);
			die;
		}
		/**
		 * Upload Certificate Ajax Handler
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _ajax_upload_avatar()
		{
			$res = array(
				'status' => 1

			);
			if (!function_exists('wp_handle_upload')) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}

			if (empty($_FILES['image'])) {
				echo json_encode(array(
					'status'  => 0,
					'message' => esc_html__('You did not select any file', 'wpbooking')
				));
				die;
			}
			$uploadedfile = $_FILES['image'];

			$size_file = $uploadedfile["size"];

			if ($size_file > (1024 * 1024 * 2)) {
				$res['status'] = 0;
				$res['message'] = esc_html__('Max upload size is 2mb', 'wpbooking');
			} else {
				$allowed_file_types = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png');
				$overrides = array('test_form' => FALSE, 'mimes' => $allowed_file_types);

				$movefile = wp_handle_upload($uploadedfile, $overrides);

				if ($movefile && !isset($movefile['error'])) {
					$res['image'] = $movefile;

				} else {
					$res['status'] = FALSE;
					$res['message'] = $movefile['error'];
				}
			}

			echo json_encode($res);
			die;
		}

		/**
		 * Login & Register handler
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _login_register_handler()
		{

			if (is_user_logged_in()) return FALSE;
			// Login
			if (WPBooking_Input::post('action') == 'wpbooking_do_login') {

				$creds['user_login'] = WPBooking_Input::post('login');
				$creds['user_password'] = WPBooking_Input::post('password');
				$creds['remember'] = WPBooking_Input::post('remember');

				$user = wp_signon($creds, FALSE);
				if (is_wp_error($user)) {
					wpbooking_set_message(esc_html__('Your Username or Password is not correct! Please try again', 'wpbooking'), 'danger');

				} else {
					// Login Success
					// Redirect if url is exists
					if ($redirect = WPBooking_Input::post('url')) {
						wp_redirect($redirect);
						die;
					}else{
						// redirect to account page
						wp_redirect(get_permalink(wpbooking_get_option('myaccount-page')));die;

					}
				}

			}


			// Register
			if (WPBooking_Input::post('action') == 'wpbooking_do_register') {
				$this->_do_register();
			}

			// Partner Register
			if (WPBooking_Input::post('action') == 'wpbooking_do_partner_register') {
				$this->_do_partner_register();

			}
		}

		/**
		 * Register for Normal User
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 */
		function _do_register()
		{
			$validate = new WPBooking_Form_Validator();
			$validate->set_rules('login', esc_html__('Username', 'wpbooking'), 'required|max_length[100]');
			$validate->set_rules('email', esc_html__('Email', 'wpbooking'), 'required|max_length[100]|valid_email');
			$validate->set_rules('password', esc_html__('Password', 'wpbooking'), 'required|min_length[6]|max_length[100]');
			$validate->set_rules('repassword', esc_html__('Re-Type Password', 'wpbooking'), 'required|min_length[6]|max_length[100]|matches[password]');
			$validate->set_rules('term_condition', esc_html__('Term & Condition', 'wpbooking'), 'required');

			$is_validated = TRUE;

			if (!$validate->run()) {
				wpbooking_set_message($validate->error_string(), 'danger');
				$is_validated = FALSE;
			}

			// Validate Username and Email exists
			if ($is_validated) {
				$user_id = username_exists(WPBooking_Input::post('login'));
				$user_email = WPBooking_Input::post('email');
				if ($user_id or email_exists($user_email)) {
					wpbooking_set_message(esc_html__('User already exists.  Password inherited.', 'wpbooking'), 'danger');
					$is_validated = FALSE;
				}
			}

			// Allow to add filter before register
			if ($is_validated) {
				$is_validated = apply_filters('wpbooking_register_validate', $is_validated);
			}


			if ($is_validated) {
				// Start Create User
				$user_email = WPBooking_Input::post('email');
				$user_name = WPBooking_Input::post('login');
				$password = WPBooking_Input::post('password');
				$user_id = wp_insert_user(array(
					'user_login' => $user_name,
					'user_pass'  => $password,
					'user_email' => $user_email
				));
				if (is_wp_error($user_id)) {

					wpbooking_set_message(esc_html__('Can not create user. Please try it again later', 'wpbooking'), 'danger');
					do_action('wpbooking_register_failed', $user_id);

				} else {

					wpbooking_set_message(esc_html__('Your account is registered successfully. You can login now', 'wpbooking'), 'success');

					// Hook after Register Success, maybe sending some email...etc
					/**
					 * @see WPBooking_User::_send_registration_email()
					 */
					do_action('wpbooking_register_success', $user_id);
				}
			}
		}

		/**
		 * Do Register for Partner
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _do_partner_register()
		{
			$validate = new WPBooking_Form_Validator();
			$validate->set_rules('login', esc_html__('Username', 'wpbooking'), 'required|max_length[100]');
			$validate->set_rules('email', esc_html__('Email', 'wpbooking'), 'required|max_length[100]|valid_email');
			$validate->set_rules('password', esc_html__('Password', 'wpbooking'), 'required|min_length[6]|max_length[100]');
			$validate->set_rules('repassword', esc_html__('Re-Type Password', 'wpbooking'), 'required|min_length[6]|max_length[100]|matches[password]');
			$validate->set_rules('service_type', esc_html__('Certificate', 'wpbooking'), 'required');
			$validate->set_rules('term_condition', esc_html__('Term & Condition', 'wpbooking'), 'required');

			$is_validated = TRUE;

			if (!$validate->run()) {
				wpbooking_set_message($validate->error_string(), 'danger');
				$is_validated = FALSE;

				return FALSE;
			}

			// Validate Username and Email exists
			if ($is_validated) {
				$user_id = username_exists(WPBooking_Input::post('login'));
				$user_email = WPBooking_Input::post('email');
				if ($user_id or email_exists($user_email)) {
					wpbooking_set_message(esc_html__('User already exists.  Password inherited.', 'wpbooking'), 'danger');
					$is_validated = FALSE;

					return FALSE;
				}
			}


			// Validate Certificate Upload
			if ($is_validated) {
				$is_select_service = FALSE;
				$service_type = WPBooking_Input::post('service_type');
				if (is_array($service_type) and !empty($service_type)) {
					foreach ($service_type as $k => $v) {
						if (!empty($v[$k]['name'])) $is_select_service = TRUE;
					}
				}

				if (!$is_select_service) {
					$is_validated = FALSE;
					wpbooking_set_message(esc_html__('Please select at lease one Service Type!', 'wpbooking'), 'danger');

					return FALSE;
				}
			}


			// Allow to add filter before register
			if ($is_validated) {
				$is_validated = apply_filters('wpbooking_partner_register_validate', $is_validated);
			}


			if ($is_validated) {
				// Start Create User
				$user_email = WPBooking_Input::post('email');
				$user_name = WPBooking_Input::post('login');
				$password = WPBooking_Input::post('password');
				$user_id = wp_insert_user(array(
					'user_login' => $user_name,
					'user_pass'  => $password,
					'user_email' => $user_email,
					'role'       => 'author'
				));
				if (is_wp_error($user_id)) {

					wpbooking_set_message(esc_html__('Can not create user. Please try it again later', 'wpbooking'), 'danger');
					do_action('wpbooking_partner_register_failed', $user_id);

				} else {
					// Update Status
					update_user_meta($user_id, 'wpbooking_register_as_partner', 1);
					// Service Access
					$service_type = WPBooking_Input::post('service_type');
					if (is_array($service_type) and !empty($service_type)) {
						foreach ($service_type as $k => $v) {
							if ($v['name']) {
								update_user_meta($user_id, 'wpbooking_service_type_access_' . $k, 1);
								if ($v['certificate']) update_user_meta($user_id, 'wpbooking_service_type_certificate_' . $k, $v['certificate']);
							} else {
								update_user_meta($user_id, 'wpbooking_service_type_access_' . $k, 0);
							}

						}
					}

					wpbooking_set_message(esc_html__('Your account is registered successfully. You can login now', 'wpbooking'), 'success');

					// Hook after Register Success, maybe sending some email...etc
					/**
					 * @see WPBooking_User::_send_partner_registration_email()
					 */
					do_action('wpbooking_partner_register_success', $user_id);
				}
			}
		}

		/**
		 * Hook Callback for Send Email after Registration, using template in admin
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $user_id
		 */
		function _send_registration_email($user_id)
		{
			$user_data = get_userdata($user_id);
			$title = $user_data->user_nicename . " - " . $user_data->user_email . " - " . $user_data->user_registered;
			$subject = sprintf(esc_html__('New Customer Partner: %s', 'wpbooking'), $title);

			// Send To Admin
			if (wpbooking_get_option('on_registration_email_admin') and wpbooking_get_option('registration_email_admin')) {
				$to = wpbooking_get_option('system_email');
				$content = do_shortcode(wpbooking_get_option('registration_email_admin'));
				$content = $this->replace_email_shortcode($content, $user_id);
				WPBooking_Email::inst()->send($to, $subject, $content);
			}

			// Send To Customer
			if (wpbooking_get_option('on_registration_email_customer') and wpbooking_get_option('registration_email_customer')) {
				$to = $user_data->user_email;
				$content = do_shortcode(wpbooking_get_option('registration_email_customer'));
				$content = $this->replace_email_shortcode($content, $user_id);

				WPBooking_Email::inst()->send($to, $subject, $content);
			}
		}

		/**
		 * Hook Callback for Send Email For PARTNER after Registration, using template in admin
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $user_id
		 */
		function _send_partner_registration_email($user_id)
		{
			$user_data = get_userdata($user_id);
			$title = $user_data->user_nicename . " - " . $user_data->user_email . " - " . $user_data->user_registered;
			$subject = sprintf(esc_html__('New Register Partner: %s', 'wpbooking'), $title);

			// Send To Admin
			if (wpbooking_get_option('on_registration_partner_email_admin') and wpbooking_get_option('registration_partner_email_to_admin')) {
				$to = wpbooking_get_option('system_email');
				$content = do_shortcode(wpbooking_get_option('registration_partner_email_to_admin'));
				$content = $this->replace_email_shortcode($content, $user_id);

				WPBooking_Email::inst()->send($to, $subject, $content);
			}

			// Send To Partner
			if (wpbooking_get_option('on_registration_partner_email_partner') and wpbooking_get_option('registration_partner_email_to_partner')) {
				$to = $user_data->user_email;
				$content = do_shortcode(wpbooking_get_option('registration_partner_email_to_partner'));
				$content = $this->replace_email_shortcode($content, $user_id);

				WPBooking_Email::inst()->send($to, $subject, $content);
			}


		}

		/**
		 * Replace Content with Shortcode
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $content
		 * @param $user_id
		 * @return mixed
		 */
		function replace_email_shortcode($content, $user_id)
		{
			$all_shortcodes = $this->get_email_shortcodes();

			if (!empty($all_shortcodes)) {
				foreach ($all_shortcodes as $k => $v) {
					$v = apply_filters('wpbooking_registration_email_shortcode', FALSE, $k, $user_id);
					$v = apply_filters('wpbooking_registration_email_shortcode_' . $k, $v, $user_id);
					$content = str_replace('['.$k.']', $v, $content);
				}
			}

			return $content;
		}

		/**
		 * Get All Available Email Shortcodes
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return array|mixed|void
		 */
		function get_email_shortcodes()
		{
			$all_shortcodes = array(
				'user_login'      => esc_html__('Your Username','wpbooking'),// Default Value for Preview
				'user_email'     => esc_html__('email@domain.com','wpbooking'),
				'profile_button' => '',
				'profile_url'    => esc_html__('http://domain.com/profile.php','wpbooking'),
			);

			$all_shortcodes = apply_filters('wpbooking_registration_email_shortcodes', $all_shortcodes);

			return $all_shortcodes;
		}

		/**
		 * Hook Callback for get Email Shortcode Content
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $content
		 * @param $shortcode
		 * @param $user_id
		 * @return bool|string
		 */
		function _get_shortcode_content($content, $shortcode, $user_id)
		{
			if (!$user = get_userdata($user_id)) return FALSE;

			switch ($shortcode) {
				case "user_login":
					return $user->user_login;
					break;

				case "user_email":
					return $user->user_email;
					break;

				case "profile_button":
					return wpbooking_admin_load_view('user/email-shortcodes/profile_url', array('user_id' => $user_id));
					break;

				case "profile_url":
					return get_edit_profile_url($user_id);
					break;
			}

			return $content;
		}

		/**
		 * Preview Registration Email
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _preview_email()
		{
			$allowed=array(
				'registration_email_customer',
				'registration_email_admin',
				'registration_partner_email_to_partner',
				'registration_partner_email_to_admin',
			);
			if(in_array(WPBooking_Input::get('email'),$allowed)){

				$content=wpbooking_get_option(WPBooking_Input::get('email'));
				$content = do_shortcode($content);

				// Apply Default Shortcode Content
				$all_shortcodes = $this->get_email_shortcodes();

				if (!empty($all_shortcodes)) {
					foreach ($all_shortcodes as $k => $v) {
						$content = str_replace('['.$k.']', $v, $content);
					}
				}

				$content=WPBooking_Email::inst()->apply_css($content);
				echo ($content);
				die;
			}
		}

		/**
		 * Add Js, CSS To Account Page
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _add_scripts()
		{
			if(get_query_var('service')){
				wp_enqueue_style('full-calendar',wpbooking_admin_assets_url('/css/fullcalendar.min.css'),FALSE,'1.1.6');

				wp_enqueue_script('moment-js',wpbooking_admin_assets_url('js/moment.min.js'),array('jquery'),null,true);

				wp_enqueue_script('full-calendar',wpbooking_admin_assets_url('js/fullcalendar.min.js'),array('jquery', 'moment-js'),null,true);

				wp_enqueue_script('fullcalendar-lang', wpbooking_admin_assets_url('/js/lang-all.js'), array('jquery'), null, true);

				wp_enqueue_script('wpbooking-calendar-room',wpbooking_admin_assets_url('js/wpbooking-calendar-room.js'),array('jquery','jquery-ui-datepicker'),null,true);
			}

			if(in_array(get_query_var('tab'),array('orders','booking_history')) and WPBooking_Input::get('subtab')=='calendar'){

				wp_enqueue_style('full-calendar',wpbooking_admin_assets_url('/css/fullcalendar.min.css'),FALSE,'1.1.6');

				wp_enqueue_script('moment-js',wpbooking_admin_assets_url('js/moment.min.js'),array('jquery'),null,true);

				wp_enqueue_script('full-calendar',wpbooking_admin_assets_url('js/fullcalendar.min.js'),array('jquery', 'moment-js'),null,true);

				wp_enqueue_script('fullcalendar-lang', wpbooking_admin_assets_url('/js/lang-all.js'), array('jquery'), null, true);

			}
		}

		/**
		 * Hook callback for Handle My Account Page Actions
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 */
		function _myaccount_page_handler()
		{


			$action=WPBooking_Input::post('action');
			switch($action){
				case "wpbooking_save_service":
					$validate=$this->validate_service();
					if($validate){
						if($service_id=get_query_var('service')){
							$service=get_post($service_id);
							// Update
							wp_update_post(array(
								'ID'=>$service_id,
								'post_title'=>WPBooking_Input::post('service_title'),
								'post_content'=>WPBooking_Input::post('service_content'),
								'post_author'=>$service->post_author
							));

							wpbooking_set_message(esc_html__('Update Successful','wpbooking'),'success');

							// Save Metabox
							//WPBooking_Metabox::inst()->do_save_metabox($service_id);

							do_action('wpbooking_after_user_update_service',$service_id);

						}else{
							// Insert
							$service_id=wp_insert_post(array(
								'post_title'=>WPBooking_Input::post('service_title'),
								'post_content'=>WPBooking_Input::post('service_content'),
							));

							if(!is_wp_error($service_id)){
								// Success
								wpbooking_set_message(esc_html__('Create Successful','wpbooking'),'success');

								// Save Metabox
								//WPBooking_Metabox::inst()->do_save_metabox($service_id);

								do_action('wpbooking_after_user_insert_service_success',$service_id);

								// Redirect To Edit Page
								$myaccount_page=get_permalink(wpbooking_get_option('myaccount-page'));
								$edit_url=$myaccount_page.'service/'.$service_id;
								wp_redirect(esc_url_raw($edit_url));
								die;

							}else{
								// Create Error
								wpbooking_set_message($service_id->get_error_message(),'danger');

								do_action('wpbooking_after_user_insert_service_error',$service_id);
							}


						}

					}
				break;

				// Update Profile
				case "wpbooking_update_profile":
					if(is_user_logged_in()){

						do_action('wpbooking_before_update_profile');

						$validate=new WPBooking_Form_Validator();
						$validate->set_rules('u_avatar',esc_html__('Avatar','wpbooking'),'max_length[500]');
						$validate->set_rules('u_display_name',esc_html__('Display Name','wpbooking'),'required|max_length[255]');
						$validate->set_rules('u_email',esc_html__('Email','wpbooking'),'required|max_length[255]|valid_email');
						$validate->set_rules('u_phone',esc_html__('Phone Number','wpbooking'),'required|max_length[255]');

						$is_validate=true;
						$is_updated=FALSE;

						if(!$validate->run()){
							$is_validate=FALSE;
							wpbooking_set_message($validate->error_string(),'danger');
						}

						$is_validate=apply_filters('wpbooking_update_profile_validate',$is_validate);

						if($is_validate){
							// Start Update
							$is_updated=wp_update_user(array(
								'ID'=>get_current_user_id(),
								'display_name'=>WPBooking_Input::post('u_display_name'),
								'user_email'=>WPBooking_Input::post('u_email')
							));

							if(is_wp_error($is_updated)){
								wpbooking_set_message($is_updated->get_error_message(),'danger');
							}else{
								wpbooking_set_message(esc_html__('Updated Successfully','wpbooking'),'success');
								// Update Success
								update_user_meta(get_current_user_id(),'phone_number',WPBooking_Input::post('u_phone'));
								update_user_meta(get_current_user_id(),'avatar',WPBooking_Input::post('u_avatar'));
							}
						}


						do_action('wpbooking_after_update_profile',$is_validate,$is_updated);
					}
					break;

				// Change Password
				case "wpbooking_change_password":
					if(is_user_logged_in()){

						do_action('wpbooking_before_change_password');

						$validate=new WPBooking_Form_Validator();
						$validate->set_rules('u_password',esc_html__('Password','wpbooking'),'required|max_length[255]');
						$validate->set_rules('u_new_password',esc_html__('New Password','wpbooking'),'required|max_length[255]');
						$validate->set_rules('u_re_new_password',esc_html__('New Password Again','wpbooking'),'required|max_length[255]|matches[u_new_password]');

						$is_validate=true;
						$is_updated=FALSE;

						if(!$validate->run()){
							$is_validate=FALSE;
							wpbooking_set_message($validate->error_string(),'danger');
						}

						global $current_user;
						get_currentuserinfo();

						if(!wp_check_password( WPBooking_Input::post('u_password'), $current_user->user_pass)){
							$is_validate=FALSE;
							wpbooking_set_message(esc_html__('Your Current Password is not correct','wpbooking'),'danger');
						}

						$is_validate=apply_filters('wpbooking_change_password_validate',$is_validate);

						if($is_validate){
							// Start Update
							$is_updated=wp_update_user(array(
								'ID'=>get_current_user_id(),
								'user_pass'=>WPBooking_Input::post('u_new_password'),
							));

							if(is_wp_error($is_updated)){
								wpbooking_set_message($is_updated->get_error_message(),'danger');
							}else{

								wpbooking_set_message(esc_html__('Password Changed Successfully','wpbooking'),'success');
							}
						}


						do_action('wpbooking_after_change_password',$is_validate,$is_updated);
					}
					break;

			}
		}

		/**
		 * Validate Post Data and Permission before Saving the Service
		 *
		 * @since 1.0
		 * @author dungdt
		 * @return bool
		 */
		function validate_service()
		{
			// Is Logged In?
			if(!is_user_logged_in()) return FALSE;

			$service=get_post(get_query_var('service'));

			$myaccount_page=get_permalink(wpbooking_get_option('myaccount-page'));

			// Service Exists
			if(!$service or $service->post_type!='wpbooking_service'){
				wpbooking_set_message(esc_html__('Service does not exists','wpbooking'),'danger');
				wp_redirect(add_query_arg(array('tab'=>'services'),$myaccount_page));
				die;
			}

			// Permission
			if($service->post_author!=get_current_user_id() or !current_user_can( 'manage_options' )){
				wpbooking_set_message(esc_html__('You do not have permission to access this page','wpbooking'),'danger');
				wp_redirect(add_query_arg(array('tab'=>'services'),$myaccount_page));
				die;
			}

			$validate=apply_filters('wpbooking_user_validate_service',true,$service);

			return $validate;

		}

		/**
		 * Hook Callback to create Endpoints in Account Page
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function add_endpoints()
		{
			// Tab
			add_rewrite_endpoint('tab',EP_PAGES);

			// Edit, Create Service
			add_rewrite_endpoint('service',EP_PAGES);

			// Detail Order
			add_rewrite_endpoint('order-detail',EP_PAGES);

			// update-profile
			add_rewrite_endpoint('update-profile',EP_PAGES);

			flush_rewrite_rules();

		}



		/**
		 * Get All Tabs in My Account Pages.
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function get_tabs()
		{
			$tabs=array(
				'profile'=>esc_html__('Profile','wpbooking'),
				'services'=>esc_html__('Services','wpbooking'),
				'booking_history'=>esc_html__('Booking History','wpbooking'),

			);
			if(current_user_can('publish_posts')){
				$tabs['orders']=esc_html__('Orders','wpbooking');
			}

			$tabs['inbox']=esc_html__('Inbox','wpbooking');

			return apply_filters('wpbooking_myaccount_tabs',$tabs);
		}

		function _myaccount_shortcode($attr = array(), $content = FALSE)
		{

			// Set Page Tabs
			if(get_query_var('order-detail')){
				set_query_var('tab','orders');
			}
			if(get_query_var('service')){
				set_query_var('tab','services');
			}
			if(get_query_var('update-profile')){
				set_query_var('tab','profile');
			}

			return wpbooking_load_view('account/index');
		}

		function _partner_register_shortcode()
		{
			return wpbooking_load_view('account/partner-register');
		}

		function _add_shortcode()
		{
			add_shortcode('wpbooking-myaccount', array($this, '_myaccount_shortcode'));
			add_shortcode('wpbooking-partner-register', array($this, '_partner_register_shortcode'));
		}

		function order_create_user($data=array())
		{
			$data=wp_parse_args($data,array(
				'user_email' => '',
				'first_name' => '',
				'last_name'  => '',
			));
			if(!$data['user_email']) return FALSE;

			$user_name = $this->generate_username();
			if ($user_name) {

				$create_user = wp_insert_user(array(
					'user_login' => $user_name,
					'user_email' => $data['user_email'],
					'first_name' => $data['first_name'],
					'last_name'  => $data['last_name'],

				));

				if (!is_wp_error($create_user)) {

					do_action('wpbooking_register_success',$create_user);
					return $create_user;
				}
			}

			return FALSE;

		}
		function generate_username()
		{
			$prefix=apply_filters('wpbooking_generated_username_prefix','wpbooking_');
			$user_name = $prefix.time() . rand(0, 999);
			if (username_exists($user_name)) return $this->generate_username();

			return $user_name;
		}
		static function inst()
		{
			if (!self::$_inst) self::$_inst = new self();

			return self::$_inst;
		}
	}

	WPBooking_User::inst();
}