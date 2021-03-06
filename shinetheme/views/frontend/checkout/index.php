<?php
$booking = WPBooking_Checkout_Controller::inst();
$cart = $booking->get_cart();
if (empty($cart)) {
    wpbooking_set_message(__('Sorry! Your cart is currently empty', 'wpbooking'), 'danger');
}
echo wpbooking_get_message();
if (empty($cart)) {
    return;
}
?>
<div class="wpbooking-checkout-wrap">
    <div class="wpbooking_checkout_form">
        <div class="wpbooking-checkout-review-order">
            <div class="wpbooking-review-order">
                <?php echo wpbooking_load_view('checkout/review') ?>
            </div>
        </div>
        <div class="wpbooking-checkout-form wpbooking-bootstrap">
            <form action="<?php echo home_url('/') ?>" onsubmit="return false" method="post" novalidate>
                <input class="wpbooking_check_empty_cart" type="hidden" value="true">
                <div class="row">
                    <div class="col-md-7">
                        <div class="checkout-form-wrap">
                            <h5 class="checkout-form-title"><?php esc_html_e('Billing Information', 'wpbooking') ?></h5>
                            <h5 class="checkout-form-sub-title">
                                <?php
                                if(is_user_logged_in()){
                                    esc_html_e('Billing Information', 'wpbooking');
                                }else{
                                    esc_html_e("Already have an account?","wpbooking");
                                    $url_check_out = get_permalink(wpbooking_get_option('checkout_page'));
                                ?>
                                     <a href="<?php echo esc_url(add_query_arg(array("wpbooking_redirect"=>$url_check_out),wp_login_url())); ?>" class="text-color"><?php esc_html_e('Login',"wpbooking") ?></a>
                                <?php } ?>
                            </h5>
                            <input name="action" value="wpbooking_do_checkout" type="hidden">
                            <div class="billing_information">
                                <div class="row">
                                    <?php
                                    $field_form_billing = $booking->get_billing_form_fields();
                                    if(!empty($field_form_billing)){?>
                                        <?php foreach($field_form_billing as $k=>$v) {
                                            $data = wp_parse_args($v, array(
                                                'title'=>'',
                                                'desc'=>'',
                                                'placeholder'=>'',
                                                'type'=>'text',
                                                'name'=>'',
                                                'size'=>'12',
                                                'required'=>false,
                                            ));
                                            $value = '';
                                            if(is_user_logged_in()) {
                                                $customer_id = get_current_user_id();
                                                $key = str_ireplace("user_","",$v['name']);
                                                if($key == 'email'){
                                                    $value = get_the_author_meta( 'email', $customer_id );
                                                }else{
                                                    $value= get_user_meta($customer_id,$key,true);
                                                }
                                            }
                                            ?>
                                            <div class="col-md-<?php echo esc_html($data['size']) ?>">
                                                <div class="form-group">
                                                    <label for="<?php echo esc_html($data['name']) ?>"><?php echo esc_html($data['title']) ?> <?php if($data['required']) echo '<span class="required">*</span>'; ?></label>
                                                    <?php if($data['type'] != 'textarea'){ ?>
                                                        <input type="<?php echo esc_attr($data['type']) ?>" class="form-control only_number"  id="<?php echo esc_html($data['name']) ?>" name="<?php echo esc_html($data['name']) ?>" placeholder="<?php echo esc_html($data['placeholder']) ?>" <?php if($data['required']) echo 'required'; ?> value="<?php echo esc_html($value) ?>">
                                                        <span class="desc"><?php echo esc_html($data['desc']) ?></span>
                                                    <?php }else{ ?>
                                                        <textarea name="<?php echo esc_html($data['name']) ?>" class="form-control" rows="4" placeholder="<?php echo esc_html($data['placeholder']) ?>" <?php if($data['title']) echo 'required'; ?>><?php echo esc_html($value) ?></textarea>
                                                        <span class="desc"><?php echo esc_html($data['desc']) ?></span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <?php echo wpbooking_load_view('checkout/cart-total-box') ?>
                        <h5 class="checkout-form-title"><?php esc_html_e('Payment Method', 'wpbooking') ?></h5>
                        <div class="wpbooking-gateways">
                            <?php echo wpbooking_load_view('checkout/gateways') ?>
                        </div>
                        <div class="wpbooking-captcha">
                            <?php echo wpbooking_load_view('checkout/captcha') ?>
                        </div>
                        <div class="form-group">
                            <label for="term_condition">
                                <input type="checkbox" id="term_condition" name="term_condition"  value="1">
                                <?php
                                $page_term_condition = wpbooking_get_option('term-page');
                                $link = '<a class="term_condition" href="'.esc_url(get_permalink($page_term_condition)).'">'.esc_attr__("terms and conditions").'</a>';
                                printf(esc_html__(" I have read and accept the %s","wpbooking"),$link);
                                ?>
                            </label>
                        </div>
                        <div class="checkout-submit-button">
                            <button type="submit" class="wb-btn wb-btn-primary wb-btn-md submit-button" disabled><?php _e('CHECKOUT', 'wpbooking') ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
