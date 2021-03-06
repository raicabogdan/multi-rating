<?php 

/**
 * Filters the_content()
 *
 * @param $content
 * @return filtered content
 */
function mr_filter_the_content( $content ) {

	$general_settings = ( array ) get_option( Multi_Rating::GENERAL_SETTINGS );

	if ( ! in_the_loop() || ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) )
		return $content;

	// get the post id
	global $post;
	
	$post_id = null;
	if ( !isset( $post_id ) && isset( $post ) ) {
		$post_id = $post->ID;
	} else if ( !isset($post) && !isset( $post_id ) ) {
		return; // No post id available to display rating form
	}
	
	// check if post type is enabled
	if ( ! MR_Utils::check_post_type_enabled( $post_id ) ) {
		return $content;
	}
 
	// use default rating form position
	$rating_form_position = get_post_meta( $post->ID, Multi_Rating::RATING_FORM_POSITION_POST_META, true );
	if ( $rating_form_position == Multi_Rating::DO_NOT_SHOW ) {
		return $content;
	}
	
	$position_settings = ( array ) get_option( Multi_Rating::POSITION_SETTINGS );
	
	// use default rating form position
	if ( $rating_form_position == '' ) {
		$rating_form_position = $position_settings[ Multi_Rating::RATING_FORM_POSITION_OPTION ];
	}

	$rating_form = null;
	if ( $rating_form_position == 'before_content' || $rating_form_position == 'after_content' ) {
		$rating_form = Multi_Rating_API::display_rating_form(
				array(
						'post_id' => $post_id,
						'echo' => false,
						'class' => $rating_form_position . ' mr-filter'
		));
	}
	
	if ( $rating_form_position == '' ) {
		remove_filter( 'the_conent', 'mr_filter_the_content' );
		return $content;
	}

	$filtered_content = '';

	if ( $rating_form_position == 'before_content' && $rating_form != null ) {
		$filtered_content .= $rating_form;
	}

	$filtered_content .= $content;

	if ( $rating_form_position == 'after_content' && $rating_form != null ) {
		$filtered_content .= $rating_form;
	}
	
	// only apply filter once.. hopefully, this is the post content...
	if ( in_the_loop() && ( is_single() || is_page() || is_attachment() ) ) {
		remove_filter( 'the_content', 'mr_filter_the_content' );
	}

	return $filtered_content;
}
add_filter( 'the_content', 'mr_filter_the_content' );


/**
 * Filters the_title()
 *
 * @param $title
 * @return filtered title
 */
function mr_filter_the_title( $title ) {

	$general_settings = (array) get_option( Multi_Rating::GENERAL_SETTINGS );

	if ( ! in_the_loop() || ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) )
		return $title;

	// get the post id
	global $post;
	
	$post_id = null;
	if ( ! isset( $post_id ) && isset( $post ) ) {
		$post_id = $post->ID;
	} else if ( !isset( $post ) && ! isset( $post_id ) ) {
		return; // No post id available to display rating form
	}
	
	// check if post type is enabled
	if ( ! MR_Utils::check_post_type_enabled( $post_id ) ) {
		return $title;
	}
	
	$rating_results_position = get_post_meta( $post->ID, Multi_Rating::RATING_RESULTS_POSITION_POST_META, true );
	if ( $rating_results_position == Multi_Rating::DO_NOT_SHOW ) {
		return $title;
	}
	
	$position_settings = (array) get_option( Multi_Rating::POSITION_SETTINGS );
	
	// use default rating results position
	if ( $rating_results_position == '' ) {
		$rating_results_position = $position_settings[ Multi_Rating::RATING_RESULTS_POSITION_OPTION ];
	}

	$rating_result = Multi_Rating_API::display_rating_result(
			array(
					'post_id' => $post_id,
					'echo' => false,
					'show_date' => false,
					'show_rich_snippets' => true,
					'class' => $rating_results_position . ' mr-filter'
			));
	
	if ( $rating_results_position == '' ) {
		remove_filter( 'the_title', 'mr_filter_the_title' );
		return $title;
	}

	$filtered_title = '';

	if ( $rating_results_position == 'before_title' && $rating_result != null ) {
		$filtered_title .= $rating_result;
	}

	$filtered_title .= $title;

	if ( $rating_results_position == 'after_title' && $rating_result != null ) {
		$filtered_title .= $rating_result;
	}
	
	// only apply filter once... hopefully, this is the post title...
	if ( in_the_loop() && ( is_single() || is_page() || is_attachment() ) ) {
		remove_filter( 'the_title', 'mr_filter_the_title' );
	}

	return $filtered_title;
}
add_filter( 'the_title', 'mr_filter_the_title' );