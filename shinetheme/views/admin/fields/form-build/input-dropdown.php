<?php
$class = "traveler-col-md-6";
if(!empty($data['edit_field_class'])){
    $class = $data['edit_field_class'];
}
?>
<div class="<?php echo esc_html($class) ?>">
    <div class="traveler-build-group ">
        <label class="control-label"><?php echo balanceTags($data['title']) ?>:</label>
        <select class="item" data-name-shortcode="<?php echo esc_attr($parent) ?>" name="<?php echo balanceTags($data['name']) ?>" id="<?php echo balanceTags($data['name']) ?>">
            <?php if(!empty($data['options'])){
                    foreach($data['options'] as $k=>$v){
                        echo '<option value="'.$v.'">'.$k.'</option>';
                    }
            } ?>

        </select>
        <i class="desc"><?php echo esc_html($data['desc'])  ?></i>
    </div>
</div>