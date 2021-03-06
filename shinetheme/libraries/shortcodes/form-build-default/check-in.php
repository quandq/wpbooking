<?php
if (!class_exists('WPBooking_Form_Check_In_Field')) {
	class WPBooking_Form_Check_In_Field extends WPBooking_Abstract_Formbuilder_Field
	{
		static $_inst;

		function __construct()
		{
			$this->field_id = 'check_in';
			$this->field_data = array(
				"title"    => __("Check-in", 'wpbooking'),
				"category" => __("Specific Fields", 'wpbooking'),
				"options"  => array(
					array(
						"type"             => "required",
						"title"            => __("Set as <strong>required</strong>", 'wpbooking'),
						"desc"             => "",
						'edit_field_class' => 'wpbooking-col-md-12',
					),
					array(
						"type"             => "text",
						"title"            => __("Title", 'wpbooking'),
						"name"             => "title",
						"desc"             => __("Title", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "label",
						"title"            => __("Name", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => "check_in",
						"desc"             => __("This is default attribute, you can not change it", 'wpbooking'),
					),
					array(
						"type"             => "text",
						"title"            => __("CSS ID (optional)", 'wpbooking'),
						"name"             => "id",
						"desc"             => __("ID", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("CSS Class (optional)", 'wpbooking'),
						"name"             => "class",
						"desc"             => __("Class", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("Value (optional)", 'wpbooking'),
						"name"             => "value",
						"desc"             => __("Value", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
					array(
						"type"             => "text",
						"title"            => __("Placeholder (optional)", 'wpbooking'),
						"name"             => "placeholder",
						"desc"             => __("Placeholder", 'wpbooking'),
						'edit_field_class' => 'wpbooking-col-md-6',
						'value'            => ""
					),
				)
			);
			parent::__construct();
		}

		function shortcode($attr = array(), $content = FALSE)
		{
			$data = wp_parse_args($attr,
				array(
					'is_required' => 'off',
					'title'       => '',
					'name'        => 'check_in',
					'id'          => '',
					'class'       => '',
					'value'       => '',
					'placeholder' => '',
					'size'        => '',
					'maxlength'   => '',
				));
			extract($data);
			$array = array(
				'id'          => $id,
				'class'       => $class.' wpbooking-field-date-start',
				'value'       => $value,
				'placeholder' => $placeholder,
				'size'        => $size,
				'maxlength'   => $maxlength,
				'name'        => $name
			);

			$required = "";
			$rule = array();
			if ($this->is_required($attr)) {
				$required = "required";
				$rule [] = "required";
				$array['class'].=' required';
			}
			if (!empty($maxlength)) {
				$rule [] = "max_length[".$maxlength."]";
			}

			parent::add_field($name, array('data' => $data, 'rule' => implode('|', $rule)));

			if($this->is_hidden($attr)) return FALSE;

			$a = FALSE;
			if($check_in=WPBooking_Input::get('check_in')){
				$array['value']=$check_in;
			}

			foreach ($array as $key => $val) {
				if ($val) {
					$a .= ' ' . $key . '="' . $val . '"';
				}
			}


			$html=array('<div class="wb-field-datepicker wb-field">');
			if(!empty($data['title'])){
			    $title=wpbooking_get_translated_string($data['title']);
                if($required) $title.=' <span class=required >*</span>';
				$html[]=sprintf('<p><label>%s</label></p>',$title);
			}
			$html[]= '<label><input readonly type="text" '.$a.' /><i class="fa fa-calendar"></i></label></div>';

			return implode("\r\n",$html);
		}

		function get_value($form_item_data,$post_id)
		{
			return isset($form_item_data['value']) ? $form_item_data['value'] : FALSE;
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Form_Check_In_Field::inst();

}

