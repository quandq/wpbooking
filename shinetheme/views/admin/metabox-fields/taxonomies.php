<?php 
/**
*@since 1.0.0
**/


$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );

$taxs = get_object_taxonomies( 'wpbooking_service', 'objects' );

if( count( $taxs ) ){
	unset( $taxs['wpbooking_location'] );
	unset($taxs['wpbooking_extra_service']);
}

if(empty($taxs)) return FALSE;
foreach ($taxs as $tax_id=>$tax){

	$old = array();
	$old_terms = wp_get_post_terms( get_the_ID(), $tax_id );
	if( !empty( $old_terms ) && is_array( $old_terms ) ){
		foreach( $old_terms as $old_term ){
			$old[] = (int) $old_term->term_id;
		}
	}
	?>
	<div class="form-table wpbooking-settings wpbooking-form-group wb-taxonomy-field" >
		<div class="st-metabox-left">
			<label ><?php echo esc_html($tax->label) ?></label>
		</div>
		<div class="st-metabox-right">
			<div class="list-terms-checkbox">
			<?php $terms=get_terms($tax_id,array('hide_empty' => false));
			 if(!empty($terms) and !is_wp_error($terms)){
			 	?>
					<?php foreach($terms as $term){
						$selected=FALSE;
						if(in_array( $term->term_id, $old )){
							$selected='checked';
						}
						printf('<div class="term-checkbox">
								<label><input type="checkbox" name="%s" value="%s" %s ><span>%s</span></label>
							</div>',$name.'['.$tax_id.'][]',$term->term_id,$selected,$term->name);
						}
					?>
			 	<?php
			 }
			 ?>
			</div>
			<div class="add-new-terms">
				<input type="text" class="term-name form-control" placeholder="<?php printf(esc_html__('%s name','wpbooking'),$tax->label) ?>">
				<a href="#" onclick="return false" class="button wb-btn-add-term" data-name="<?php echo esc_attr($name) ?>" data-tax="<?php echo esc_attr($tax_id) ?>"><?php esc_html_e('Add New','wpbooking') ?> <i class="fa fa-spin  fa-spinner loading-icon"></i></a>
			</div>

		</div>
	</div>
	<?php
}
?>