<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

/** Main Section */
$wp_customize->add_section('sg_related_content', array(
    'title'    => __('Related Content', 'related-content'),
    'priority' => 120,
));

/** Display Position option */
$wp_customize->add_setting( $this->optionsKey . '[display_mode]', array(
    'default'        => 'post_bottom',
    'capability'     => 'edit_theme_options',
    'type'           => 'option',
));

$wp_customize->add_control($this->optionsKey . '_display_mode', array(
    'label'      => __('Display mode', 'related-content'),
    'section'    => 'sg_related_content',
    'settings'   =>  $this->optionsKey . '[display_mode]',
    'type'    => 'select',
    'choices' => array(
        'post_bottom' => __('After Post Content', 'related-content'),
        'disabled' => __('Disabled', 'related-content'),
    ),
));


/** Loading mode option */
$wp_customize->add_setting( $this->optionsKey . '[loading_mode]', array(
    'default'        => 'ajax',
    'capability'     => 'edit_theme_options',
    'type'           => 'option',
));

$wp_customize->add_control($this->optionsKey . '_loading_mode', array(
    'label'      => __('Loading mode', 'related-content'),
    'section'    => 'sg_related_content',
    'settings'   =>  $this->optionsKey . '[loading_mode]',
    'type'    => 'select',
    'choices' => array(
        'ajax' => __('Ajax', 'related-content'),
        'static' => __('Static', 'related-content'),
    ),
));

/** Display Count */
$wp_customize->add_setting( $this->optionsKey . '[post_count]', array(
    'default'        => '4',
    'capability'     => 'edit_theme_options',
    'type'           => 'option'
));

$wp_customize->add_control($this->optionsKey .'_post_count', array(
    'label'      => __('Post count', 'related-content'),
    'section'    => 'sg_related_content',
    'settings'   => $this->optionsKey .'[post_count]',
));

