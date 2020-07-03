
<h1><?php _e('Related Content', 'related-content') ?></h1>
<hr>

<form action="" method="post">
<input type="hidden" name="sg_related_content_save" value="1">
<div class="rc-row">
    <label>
        <?php _e('Display position', 'related-content') ?>
        <select name="display_position">
            <option <?php selected(SG_related_content()->get_settings('display_position'), 'inside_post_bottom') ?> value="inside_post_bottom">Post bottom, inside</option>
            <option <?php selected(SG_related_content()->get_settings('display_position'), 'after_post') ?> value="after_post">After post</option>
        </select>
    </label>
</div>

<div class="rc-row">
    <label>
        <?php _e('Post count', 'related-content') ?>
        <input value="<?php echo SG_related_content()->get_settings('post_count') ?>" name="post_count" type="number" min="2" max="6">
    </label>
</div>

<div class="rc-row">
    <label>
        <?php _e('Loading mode', 'related-content') ?>
        <select name="loading_mode" id="loading_mode">
            <option <?php selected(SG_related_content()->get_settings('loading_mode'), 'ajax') ?> value="ajax"><?php _e('Ajax', 'related-content') ?></option>
            <option <?php selected(SG_related_content()->get_settings('loading_mode'), 'static') ?>  value="static"><?php _e('Static', 'related-content') ?></option>
        </select>
    </label>
</div>

<div class="rc-row">
    <button type="submit" class="button"><?php _e('Save Settings', 'related-content') ?></button>
</div>



</form>