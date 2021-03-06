<?php
/**
 *@since 1.0.0
 **/

$old_data = (isset( $data['custom_data'] ) ) ? esc_html( $data['custom_data'] ) : get_post_meta( $post_id, esc_html( $data['id'] ), true);

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$class.=' width-'.$data['width'];
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );


?>
<div class="form-table wpbooking-settings field-check-in <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <label class="from-group-col">
                    <?php esc_html_e('from','wpbooking') ?>
                    <select class="form-control small" name="checkin_from">
                        <option value=""><?php esc_html_e('Please Select','wpbooking') ?></option>
                        <?php $d=array('07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00');
                            foreach($d as $time){
                                printf('<option value="%s" %s >%s</option>',$time,selected(get_post_meta(get_the_ID(),'checkin_from',true),$time,false),$time);
                            }
                        ?>
                    </select>
                </label>
                <label class="from-group-col">
                    <?php esc_html_e('to (optional)','wpbooking') ?>
                    <select class="form-control small" name="checkin_to">
                        <option value=""><?php esc_html_e('Please Select','wpbooking') ?></option>
                        <?php $d=array('12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30','00:00');
                            foreach($d as $time){
                                printf('<option value="%s" %s>%s</option>',$time,selected(get_post_meta(get_the_ID(),'checkin_to',true),$time,false),$time);
                            }
                        ?>
                    </select>
                </label>
            </div>
        </div>
        <div class="metabox-help"><?php echo balanceTags( $data['desc'] ) ?></div>
    </div>
</div>