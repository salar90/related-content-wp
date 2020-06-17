<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class SG_Related_Content{

    protected $optionsKey = 'sg_rc_settings';
    function init()
    {
        $this->register_hooks();
    }

    public function register_hooks()
    {
        // Admin hooks
        if(is_admin()){

        }

        add_action( 'admin_menu', [$this, 'register_menu_page'] );
        
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_styles'] );
        
        add_action('wp_ajax_nopriv_sg_related_posts', [$this, 'related_posts_ajax']);
        add_action('wp_ajax_sg_related_posts', [$this, 'related_posts_ajax']);

        //non-admin hooks
        add_filter('the_content', [$this, 'the_content_filter'], 20);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_front_styles']);
    }

    function menu_page()
    {
        $is_submit = filter_input(INPUT_POST, 'sg_related_content_save') == '1';

        if($is_submit){
            $newSettings = [
                'display_position' => filter_input(INPUT_POST, 'display_position'),
                'post_count' => filter_input(INPUT_POST, 'post_count')
            ];
            update_option($this->optionsKey, $newSettings);
        }

        include __DIR__ . '/admin-menu.php';
    }

    function enqueue_admin_styles( $hook ) {
        if ( 'toplevel_page_related-content' != $hook ) {
            return;
        }
        wp_enqueue_style( 'related_content', plugin_dir_url( __FILE__ ) . 'admin-styles.css' );
    }

    function register_menu_page() {
        add_menu_page(
            __( 'Related Content', 'related-content' ),
            __( 'Related Content', 'related-content' ),
            'manage_options',
            'related-content',
            [$this, 'menu_page'],
            'dashicons-admin-links',
            6
        );
    }

    function enqueue_front_styles(){
        global $post;
        wp_enqueue_style('sg-related-content', plugin_dir_url(__FILE__) . '/style.css');

        if(is_singular('post')){
            wp_enqueue_script('sd-related-content-script', plugin_dir_url(__FILE__) . '/front.js', ['jquery'],false, true);
            
            wp_localize_script(
                'sd-related-content-script', 
                'related_content_object',
                [
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'post_id' => $post->ID ?? null
                ] 
            );

        }
    }

    function the_content_filter($content)
    {
        global $post;
        if(empty($post) || !is_singular('post')){
            return $content;
        }
        $related_posts_query = $this->get_related_posts_query();
        $content .= $this->get_widget_html($post, $related_posts_query);
        wp_reset_postdata();
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

    private function get_related_posts_query($post_id = null, $post_limit = 4)
    {
        // get post from ID or GLOBALS
        $post = get_post($post_id);

        $tag_terms = wp_get_post_terms($post->ID);

        
        $cat_terms = wp_get_post_terms($post->ID, 'category');

        $countTags = array_reduce($tag_terms,function($count,$term){
            $count += $term->count;
            return $count;
        }, 0);

        $countCats = array_reduce($cat_terms,function($count,$term){
            $count += $term->count;
            return $count;
        }, 0);
        

        $tag_terms = array_map(function($term)use($countTags){
            return [
                'term_id' => $term->term_id,
                'name' => $term->name,
                'power' => intval((1 - $term->count / $countTags) * 100) * 1 
            ];
        }, $tag_terms);

        $cat_terms = array_map(function($term)use($countCats){
            return [
                'term_id' => $term->term_id,
                'name' => $term->name,
                'power' => intval((1 - $term->count / $countCats) * 100) * 1.5
            ];
        }, $cat_terms);
        
        
        $posts = [];
        foreach($tag_terms as $term){
            $args = [
                'posts_per_page' => 100,
                'tag_id' => $term['term_id'],
            ];
            $query = new WP_Query($args);
            
            foreach($query->posts as $post_data){
                $post_id = $post_data->ID;
                if(isset($posts[$post_id])){
                    $posts[$post_id]['power'] += $term['power'];
                }else{
                    $posts[$post_id] = [
                        'id' => $post_id,
                        'title' => $post_data->post_title,
                        'power' => $term['power']
                    ];
                }
            }
        }

        foreach($cat_terms as $term){
            $args = [
                'posts_per_page' => 100,
                'tag_id' => $term['term_id'],
                'fields' => 'ids'
            ];
            $query = new WP_Query($args);
            foreach($query->posts as $post_data){
                $post_id = $post_data->ID;
                if(isset($posts[$post_id])){
                    $posts[$post_id]['power'] += $term['power'];
                }else{
                    $posts[$post_id] = [
                        'id' => $post_id,
                        'title' => $post_data->post_title,
                        'power' => $term['power']
                    ];
                }
            }
        }

        foreach($posts as $key=>$post_data){
            $similarity = 0;
            similar_text($post_data['title'], $post->post_title, $similarity);
            $posts[$key]['power'] += $similarity;
        }


        usort($posts, function($a, $b){
            return $b['power'] - $a['power'];
        });
        $posts = array_slice($posts,0, $post_limit);

        $post_ids = array_map(function($post){
            return $post['id'];
        }, $posts);

        $post_ids = array_filter($post_ids, function($id)use($post){
            return $post->ID != $id;
        });

        

        $query_args = [
            'post__in' => $post_ids,
            'posts_per_page' => $post_limit,
            'orderby' => 'post__in',
            'ignore_sticky_posts' => 1
        ];
        
        $query = new WP_Query($query_args);
        
        return $query;
    }

    function related_posts_ajax()
    {
        $count = filter_input(INPUT_GET, 'count', FILTER_SANITIZE_NUMBER_INT);
        $post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);

        $related_posts_query = $this->get_related_posts_query($post_id, $count);

        $entries = [];

        while($related_posts_query->have_posts()){
            $related_posts_query->the_post();
            $post = get_post();
            $thumbnail_id = get_post_thumbnail_id();
            $entry = [
                'id' => $post->ID,
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'url' => get_the_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(null, 'medium'),
                'srcset' => wp_get_attachment_image_srcset($thumbnail_id)
            ];
            $entries[] = $entry;
        }

        $response = [
            'entries' => $entries,
            'time' => date(DATE_ISO8601)
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        die();  
    }

    public function get_settings($key = null, $forceRefresh = false)
    {
        
        if(empty($this->settings) || $forceRefresh){
            $settings = get_option($this->optionsKey);
        }
        
        if(empty($settings)){
            $settings = [
                'post_count' => 4,
                'display_position' => 'inside_post_bottom'
            ];
            update_option($this->optionsKey, $settings);
        }

        if(empty($key)){
            return $settings;
        }elseif(isset($settings[$key])){
            return $settings[$key];
        }
        return null;
    }

} 