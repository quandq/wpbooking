<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/13/2016
 * Time: 1:33 PM
 */
?>
<div class="wpbooking-field-title">
	<h4 class="field-title">
		<?php echo esc_html($data['label']) ?>
		<?php if(!empty($data['help_popover'])){
			printf('<span class="wb-help-popover" data-placement="bottom" data-toggle="popover" data-content="%s" data-trigger="hover"><i class="fa fa-question-circle"></i></span>',$data['help_popover']);
		}?>
	</h4>
	<p class="field-desc">
		<?php echo esc_html($data['desc']) ?>
	</p>
</div>
<div class="clear"></div>