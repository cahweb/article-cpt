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
	        'show_in_rest'       => true,
  			'rest_base'          => 'article',
  			'rest_controller_class' => 'WP_REST_Posts_Controller',
	        'supports' => array(
	            'title',
	            'excerpt',
	            'editor',
	            'thumbnail')
	    );
	register_post_type( 'article' , $args );
}

add_action( 'init', 'add_category_taxonomy_to_article' );

function add_category_taxonomy_to_article() {
	register_taxonomy_for_object_type( 'category', 'article' );
}

add_action("admin_init", "article_init");
add_action('save_post', 'article_save');

// Add the meta boxes to our CPT page
function article_init() {
	add_meta_box("article-info-meta", "Basic Information", "article_meta_info", "article", "normal", "high");

	add_meta_box("article-abstract-meta", "Abstract", "article_meta_abstract", "article", "normal", "high");

	add_meta_box("article-author-meta", "Author Information", "article_meta_author", "article", "normal", "high");

	add_meta_box("article-review-meta", "For Reviews Only", "article_meta_review", "article", "normal", "high");
}

add_filter( 'kdmfi_featured_images', function( $featured_images ) {
    $args = array(
        'id' => 'author-image',
        'desc' => 'A picture of the author.',
        'label_name' => 'Author Image',
        'label_set' => 'Set Author Image',
        'label_remove' => 'Remove Author Image',
        'label_use' => 'Set Author Image',
        'post_type' => array( 'article' ),
    );

    $featured_images[] = $args;

    return $featured_images;
});

// Information
function article_meta_info() {
	global $post; // Get global WP post var
    $custom = get_post_custom($post->ID); // Set our custom values to an array in the global post var

    // Form markup 
    include_once('views/info.php');
}

// Abstract
function article_meta_abstract() {
	global $post;
	global $settings;
	$custom = get_post_custom($post->ID);

	wp_editor($custom['abstract'][0], 'abstract', $settings['md']);
}

// Author Information
function article_meta_author() {
	global $post; // Get global WP post var
    $custom = get_post_custom($post->ID); // Set our custom values to an array in the global post var

    // Form markup 
    wp_editor($custom['auth-info'][0], 'auth-info', $settings['md']);

    include_once('views/author.php');
}

// For Reviews Only
function article_meta_review() {
	global $post;
	global $settings;
	$custom = get_post_custom($post->ID);

	include_once("views/review.php");
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
	update_post_meta($post->ID, "auth-url", $_POST["auth-url"]);
	update_post_meta($post->ID, "auth-info", $_POST["auth-info"]);
	update_post_meta($post->ID, "auth-rev", $_POST["auth-rev"]);
	update_post_meta($post->ID, "title-rev", $_POST["title-rev"]);
	update_post_meta($post->ID, "url-rev", $_POST["url-rev"]);


}

// Settings array. This is so I can retrieve predefined wp_editor() settings to keep the markup clean
$settings = array (
	'sm' => array('textarea_rows' => 3),
	'md' => array('textarea_rows' => 6),
);


?>