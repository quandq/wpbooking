<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <?php if(!empty($data['value'])){ ?>
            <?php
            $data_value = traveler_get_option($data['id'],$data['std']);
            ?>
            <select class="form-control form-control-admin min-width-500" name="traveler_booking_<?php echo esc_html($data['id']) ?>">
                <?php foreach($data['value'] as $key=>$value){ ?>
                    <option <?php if($data_value == $key) echo "selected"; ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value) ?></option>
                <?php } ?>
            </select>
        <?php } ?>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>
