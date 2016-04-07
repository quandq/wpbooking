<?php
if(function_exists( 'traveler_add_field_form_builder' )) {
    traveler_add_field_form_builder( array(
            "title"    => __( "TextArea" , 'traveler-booking' ) ,
            "name"     => 'traveler_booking_textarea' ,
            "category" => 'Standard Fields' ,
            "options"  => array(
                array(
                    "type"             => "required" ,
                    "title"            => __( "Set as <strong>required</strong>" , 'traveler-booking' ) ,
                    "desc"             => "" ,
                    'edit_field_class' => 'traveler-col-md-12' ,
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Title" , 'traveler-booking' ) ,
                    "name"             => "title" ,
                    "desc"             => __( "Title" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Name" , 'traveler-booking' ) ,
                    "name"             => "name" ,
                    "desc"             => __( "Name" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "ID" , 'traveler-booking' ) ,
                    "name"             => "id" ,
                    "desc"             => __( "ID" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Class" , 'traveler-booking' ) ,
                    "name"             => "class" ,
                    "desc"             => __( "Class" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "textarea" ,
                    "title"            => __( "Value" , 'traveler-booking' ) ,
                    "name"             => "value" ,
                    "desc"             => __( "Value" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-12' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Rows" , 'traveler-booking' ) ,
                    "name"             => "rows" ,
                    "desc"             => __( "Rows" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Columns" , 'traveler-booking' ) ,
                    "name"             => "columns" ,
                    "desc"             => __( "Columns" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,

            )
        )
    );
}
if(!function_exists( 'traveler_sc_booking_textarea' )) {
    function traveler_sc_booking_textarea( $attr , $content = false )
    {
        $data = shortcode_atts(
            array(
                'is_required' => 'off' ,
                'title'        => '' ,
                'name'        => '' ,
                'id'          => '' ,
                'class'       => '' ,
                'value'       => '' ,
                'rows'        => '' ,
                'columns'     => '' ,
            ) , $attr , 'traveler_booking_textarea' );
        extract( $data );
        $required = "";
        $rule = "";
        if($is_required == "on") {
            $required = "required";
            $rule .= "required";
        }
        Traveler_Admin_Form_Build::inst()->add_form_field($title ,$name,array('data'=>$data,'rule'=>$rule));
        return '<textarea name="' . $name . '" id="' . $id . '" class="' . $class . '" rows="' . $rows . '" cols="' . $columns . '" ' . $required . ' >' . $value . '</textarea>';
    }
}
add_shortcode( 'traveler_booking_textarea' , 'traveler_sc_booking_textarea' );