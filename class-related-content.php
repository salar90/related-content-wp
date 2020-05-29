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
    }

    function the_content_filter($content)
    {
        $content .= "<br> This will be at the end.";
        return $content;
    }


} 