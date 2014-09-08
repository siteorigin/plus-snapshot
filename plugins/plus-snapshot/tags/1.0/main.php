<?php

if( !defined('SITEORIGIN_IS_PREMIUM') ) define('SITEORIGIN_IS_PREMIUM', true);

include plugin_dir_path(__FILE__) . '/customizer/customizer.php';
include plugin_dir_path(__FILE__) . '/ajax-comments/ajax-comments.php';
include plugin_dir_path(__FILE__) . '/css/css.php';
include plugin_dir_path(__FILE__) . '/widgets/widgets.php';

include plugin_dir_path(__FILE__) . '/customizer.php';
include plugin_dir_path(__FILE__) . '/settings.php';

function snapshot_plus_plugin_init(){
	if( siteorigin_setting('comments_ajax') ){
		siteorigin_ajax_comments_activate();
	}
}
add_action('init', 'snapshot_plus_plugin_init', 11);

/**
 * Enqueue snapshot premium's scripts
 *
 * @action wp_enqueue_scripts
 */
function snapshot_plus_enqueue_scripts(){
	wp_enqueue_style('snapshot-spritemaps', get_template_directory_uri().'/premium/sprites.css', array(), SITEORIGIN_THEME_VERSION);

	if(siteorigin_setting('general_search')){
		wp_enqueue_script('snapshot-search', get_template_directory_uri().'/premium/js/search.js', array('jquery'), SITEORIGIN_THEME_VERSION);
		wp_localize_script('snapshot-search', 'snapshotSearch', array(
			'menuText' => siteorigin_setting('general_search_menu_text')
		) );
	}

	if(siteorigin_setting('appearance_style') != 'light'){
		wp_enqueue_style('snapshot-style', plugin_dir_url( __FILE__ ).'styles/style-'.siteorigin_setting('appearance_style').'.css', array(), SITEORIGIN_THEME_VERSION);
	}
}
add_action('wp_enqueue_scripts', 'snapshot_plus_enqueue_scripts');

/**
 * Render the video meta box
 */
function snapshot_plus_meta_box_video_render(){
	global $post;
	$video = get_post_meta($post->ID, 'snapshot_post_video', true);
	?>
	<input type="text" name="snapshot_post_video" class="widefat" value="<?php echo esc_attr($video) ?>" />
	<p class="description"><?php _e('Enter the full url of a oEmbed video (YouTube, Vimeo, etc).', 'snapshot') ?></p>
	<?php
}

function snapshot_plus_save_post($post_id, $post){
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( !current_user_can('edit_post', $post_id) ) return;
	if(!isset($_POST['snapshot_post_video'])) return;

	update_post_meta($post_id, 'snapshot_post_video', $_POST['snapshot_post_video']);
}
add_action('save_post', 'snapshot_plus_save_post', 10, 2);

/**
 * Add the search button to the navigation menu
 *
 * @param $items
 * @param $args
 * @return string
 */
function snapshot_plus_wp_nav_menu_items($items, $args){
	if(siteorigin_setting('general_search') && $args->theme_location == 'main-menu'){
		$items .= '<li id="main-menu-search"><a href="#">'.siteorigin_setting('general_search_menu_text').'</a></li>';
	}
	return $items;
}
add_filter('wp_nav_menu_items', 'snapshot_plus_wp_nav_menu_items', 10, 2);

/**
 * @param $post_id
 */
function snapshot_plus_video_viewer($post_id){
	$video = get_post_meta($post_id, 'snapshot_post_video', true);
	global $wp_embed;

	$code = $wp_embed->shortcode(array('width' => 960), $video);
	$code = apply_filters('snapshot_video_embed_code', $code);
	echo $code;
}

function snapshot_plus_filter_video_embed_code($code){
	if(siteorigin_setting('posts_video_autoplay') || siteorigin_setting('posts_video_hide_related') || siteorigin_setting('posts_video_default_hd')) {
		$code = preg_replace_callback('/src="([^"]*)"/', 'snapshot_plus_video_change_autoplay_callback', $code);
	}
	echo $code;
}
add_filter('snapshot_video_embed_code', 'snapshot_plus_filter_video_embed_code');

function snapshot_plus_video_change_autoplay_callback($matches){
	$url = $matches[1];
	if(siteorigin_setting('posts_video_autoplay')){
		$url = add_query_arg('autoplay', 1, $url);
	}
	if(siteorigin_setting('posts_video_hide_related')){
		$url = add_query_arg('rel', 0, $url);
	}
	if(siteorigin_setting('posts_video_default_hd')){
		$url = add_query_arg('vq', 'hd1080', $url);
	}

	return 'src="' .$url. '"';
}

function snapshot_plus_slider_query_args($args){
	// Add the category setting
	$cat = siteorigin_setting('slider_category');
	if(!empty($cat)){
		$args['cat'] = intval($cat);
	}

	// Add the order setting
	$args['orderby'] = siteorigin_setting('slider_posts');

	return $args;
}
add_filter('snapshot_slider_query_args', 'snapshot_plus_slider_query_args');

function snapshot_plus_widgets_init(){
	register_widget( 'SiteOrigin_Widgets_GoogleMap' );
	register_widget( 'SiteOrigin_Widgets_Video' );
}
add_action('widgets_init', 'snapshot_plus_widgets_init');

function snapshot_plus_search_bar(){
	include plugin_dir_path(__FILE__).'/tpl/searchbar.php';
}