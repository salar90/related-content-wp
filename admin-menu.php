
<h1><?php _e('Related Content', 'related-content') ?></h1>
<hr>

<form action="">
<div class="rc-row">
    <label>
        <?php _e('Display position', 'related-content') ?>
        <select name="display_position">
            <option value="inside_post_bottom">Post bottom, inside</option>
            <option value="after_post">After post</option>
        </select>
    </label>
</div>

<div class="rc-row">
    <label>
        <?php _e('Post count', 'related-content') ?>
        <input name="post_count" type="number" min="2" max="6">
    </label>
</div>



</form>