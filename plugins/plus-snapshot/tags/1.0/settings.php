<?php

/**
 * Add all the settings available in the premium version.
 *
 * @action admin_init
 */
function snapshot_plus_admin_init(){

	siteorigin_settings_add_field('general', 'search', 'checkbox');
	siteorigin_settings_add_field('general', 'search_menu_text', 'text');
	siteorigin_settings_add_field('general', 'attribution', 'checkbox');

	siteorigin_settings_add_field('appearance', 'style', 'select', null, array(
		'options' => array(
			'light' => __('Light', 'snapshot'),
			'dark' => __('Dark', 'snapshot'),
		)
	));

	siteorigin_settings_add_field('posts', 'video_autoplay', 'checkbox');
	siteorigin_settings_add_field('posts', 'video_hide_related', 'checkbox');
	siteorigin_settings_add_field('posts', 'video_default_hd', 'checkbox');

	siteorigin_settings_add_field('slider', 'posts', 'select', null, array(
		'options' => array(
			'date' => __('Post Date', 'snapshot'),
			'modified' => __('Modified Date', 'snapshot'),
			'rand' => __('Random', 'snapshot'),
			'comment_count' => __('By Comment Count', 'snapshot'),
		)
	));

	$category_options = array(
		0 => __('All', 'snapshot'),
	);
	$cats = get_categories();
	if(!empty($cats)){
		foreach(get_categories() as $cat){
			$category_options[$cat->term_id] = $cat->name;
		}
	}
	siteorigin_settings_add_field('slider', 'category', 'select', null, array(
		'options' => $category_options,
	));

	siteorigin_settings_add_field('comments', 'ajax', 'checkbox');
}
add_action('admin_init', 'snapshot_plus_admin_init', 11);