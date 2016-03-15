<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <textarea id="<?php echo esc_html($data['id']) ?>" name="traveler_booking_<?php echo esc_html($data['id']) ?>" class="form-control form-control-admin min-width-500"><?php echo esc_html($value) ?></textarea>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>

</tr>