<?php
if($related_posts_query->have_posts()):
?>
<div class="sg_related_posts">
<h3 class="sg_related_posts_title"><?php apply_filters('sg_related_content_widget_title', _e('You might also read', 'related-content')) ?></h3>
<ul class="sg_related_posts_list">
<?php 
    while($related_posts_query->have_posts()):
        $related_posts_query->the_post();
?>
<li>
    <a href="<?php the_permalink() ?>">
        <?php the_post_thumbnail() ?>
        <?php the_title() ?>
    </a>
</li>
<?php endwhile ?>
</ul>
</div>

<?php else: ?>
no related posts
<?php endif ?>