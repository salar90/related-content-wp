<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class SG_Related_Content{
    
    function init()
    {
        $this->register_hooks();
    }

    public function register_hooks()
    {
        // Admin hooks
        if(is_admin()){

        }

        //non-admin hooks
        add_filter('the_content', [$this, 'the_content_filter'], 20);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_front_styles']);
    }

    function enqueue_front_styles(){
        wp_enqueue_style('sg-related-content', plugin_dir_url(__FILE__) . '/style.css');
    }

    function the_content_filter($content)
    {
        global $post;
        if(empty($post)){
            return $content;
        }
        $related_posts_query = $this->get_related_posts_query();
        $content .= $this->get_widget_html($post, $related_posts_query);
        return $content;
    }

    private function get_widget_html($post, $related_posts_query, $args = [])
    {
        ob_start();
        include __DIR__ . '/template.php';
        $output = ob_get_clean();
        wp_reset_postdata();
        return $output;
    }

    private function get_related_posts_query()
    {
        global $post;
        $tag_terms = wp_get_post_terms($post->ID);
        $cat_terms = wp_get_post_terms($post->ID, 'category');
        
        $query_args = [
            'post__in' => [1]
        ];
        $query = new WP_Query($query_args);
        // var_dump($query->posts);
        return $query;
    }


} 