<?php

/*
 *
 * Plugin Name: Common - Article
 * Description: Custom post type to be used with CAH The Florida Review website
 * Author: Austin Tindle + Alessandro Vecchi
 *
 */

/* Custom Post Type ------------------- */

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Load our CSS
function article_load_plugin_css() {
    wp_enqueue_style( 'article-plugin-style', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action( 'admin_enqueue_scripts', 'article_load_plugin_css' );

// Add create function to init
add_action('init', 'article_create_type');

// Create the custom post type and register it
function article_create_type() {
	$args = array(
	      'label' => 'Articles',
	        'public' => true,
	        'show_ui' => true,
	        'capability_type' => 'post',
	        'hierarchical' => false,
	        'rewrite' => array('slug' => 'article'),
			'menu_icon'  => 'dashicons-media-document',
	        'query_var' => true,
	        'supports' => array(
	            'title',
	            'excerpt',
	            'thumbnail')
	    );
	register_post_type( 'article' , $args );
}

add_action("admin_init", "article_init");
add_action('save_post', 'article_save');

// Add the meta boxes to our CPT page
function article_init() {
	add_meta_box("article-info-meta", "Basic Information", "article_meta_info", "article", "normal", "high");

	add_meta_box("article-body-meta", "Body", "article_meta_body", "article", "normal", "high");

	add_meta_box("article-abstract-meta", "Abstract", "article_meta_abstract", "article", "normal", "high");

	add_meta_box("article-image-meta", "Author Image", "article_meta_image", "article", "normal", "high");

	add_meta_box("article-author-meta", "Author Information", "article_meta_author", "article", "normal", "high");
}

// Information
function article_meta_info() {
	global $post; // Get global WP post var
    $custom = get_post_custom($post->ID); // Set our custom values to an array in the global post var

    // Form markup 
    include_once('views/info.php');
}

// Body
function article_meta_body() {
	global $post;
	global $settings;
	$custom = get_post_custom($post->ID);

	wp_editor($custom['body'][0], 'body', $settings['md']);
}

// Abstract
function article_meta_abstract() {
	global $post;
	global $settings;
	$custom = get_post_custom($post->ID);

	wp_editor($custom['abstract'][0], 'abstract', $settings['md']);
}

// Author Image
function article_meta_image() {
	global $post;
	global $settings;
	$custom = get_post_custom($post->ID);

	wp_editor($custom['auth-image'][0], 'auth-image', $settings['md']);
}

// Author Information
function article_meta_author() {
	global $post; // Get global WP post var
    $custom = get_post_custom($post->ID); // Set our custom values to an array in the global post var

    // Form markup 
    include_once('views/author.php');
}

// Save our variables
function article_save() {
	global $post;

	update_post_meta($post->ID, "authors", $_POST["authors"]);
	update_post_meta($post->ID, "issue", $_POST["issue"]);
	update_post_meta($post->ID, "start", $_POST["start"]);
	update_post_meta($post->ID, "end", $_POST["end"]);
	update_post_meta($post->ID, "pur-url", $_POST["pur-url"]);
	update_post_meta($post->ID, "doi", $_POST["doi"]);
	update_post_meta($post->ID, "body", $_POST["body"]);
	update_post_meta($post->ID, "abstract", $_POST["abstract"]);
	update_post_meta($post->ID, "auth-image", $_POST["auth-image"]);
}

// Settings array. This is so I can retrieve predefined wp_editor() settings to keep the markup clean
$settings = array (
	'sm' => array('textarea_rows' => 3),
	'md' => array('textarea_rows' => 6),
);


?>