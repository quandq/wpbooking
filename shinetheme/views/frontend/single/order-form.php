<?php
$booking=Traveler_Booking::inst();
$extra_price=traveler_load_view('single/extra-price');
$form=$booking->get_order_form_by_post_id();
printf('<form onsubmit="return false" class="traveler_order_form">
			<input name="action" value="traveler_add_to_cart" type="hidden">
			<input name="post_id" value="%d" type="hidden">
		%s
		</form>',get_the_ID(),$extra_price.$form);
