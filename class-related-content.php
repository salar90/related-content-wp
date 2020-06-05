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

    private function get_related_posts_query($post_limit = 4)
    {
        global $post;
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


} 