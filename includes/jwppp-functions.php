<?php
/**
* JW PLAYER 7 FOR WORDPRESS
*/

//ADD META BOX
function jwppp_add_meta_box() {

	$jwppp_get_types = get_post_types();
	$exclude = array('attachment', 'nav_menu_item');
	$screens = array();


	foreach($jwppp_get_types as $type) {
		if(sanitize_text_field(get_option('jwppp-type-' . $type) == 1)) {
			array_push($screens, $type);
		}
	}


	foreach ( $screens as $screen ) {

		add_meta_box(
			'myplugin_sectionid',
			__( 'JW Player 7', 'myplugin_textdomain' ),
			'myplugin_meta_box_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'jwppp_add_meta_box' );


function myplugin_meta_box_callback( $post ) {

	wp_nonce_field( 'jwppp_save_meta_box_data', 'jwppp_meta_box_nonce' );

	$video_url = get_post_meta( $post->ID, '_jwppp-video-url', true );
	$video_title = get_post_meta($post->ID, '_jwppp-video-title', true);
	$video_description = get_post_meta($post->ID, '_jwppp-video-description', true);
	$jwppp_embed_video = sanitize_text_field(get_option('jwppp-embed-video'));
	

	echo '<label for="_jwppp-video-url">';
	echo '<strong>' . __( 'Video URL', 'jwppp' ) . '</strong>';
	echo '</label> ';
	echo '<p><input type="text" id="_jwppp-video-url" name="_jwppp-video-url" placeholder="' . __('Add the URL of your video (YouTube or self-hosted)', 'jwppp') . '" value="' . esc_attr( $video_url ) . '" size="60" /></p>';

	echo '<a class="button more-options">' . __('More options', 'jwppp') . '</a>';

	?>

	<script>
	jQuery(function() {
		jQuery('.jwppp-more-options').hide();
		jQuery('.more-options').click(function() {
			jQuery('.jwppp-more-options').toggle('fast');
			// jQuery('.more-options').text('Less options');
			jQuery(this).text(function(i, text) {
				return text == 'More options' ? 'Less options' : 'More options';
			});
		});
	
	});
	</script>

	<?php
	echo '<div class="jwppp-more-options" style="margin-top:2rem;">';
	echo '<label for="_jwppp-video-image">';
	echo '<strong>' . __( 'Video poster image', 'jwppp' ) . ' | </strong><a href="http://www.ilghera.com/product/jw-player-7-for-wordpress-premium/" target="_blank">Upgrade</a>';
	echo '</label> ';
	echo '<p><input type="text" id="_jwppp-video-image" name="_jwppp-video-image" placeholder="' . __('Add a different poster image for this video', 'jwppp') . '" size="60" disabled="disabled" /></p>';

	echo '<label for="_jwppp-video-title">';
	echo '<strong>' . __( 'Video title', 'jwppp' ) . '</strong>';
	echo '</label> ';
	echo '<p><input type="text" id="_jwppp-video-title" name="_jwppp-video-title" placeholder="' . __('Add a title to your video', 'jwppp') . '" value="' . esc_attr( $video_title ) . '" size="60" /></p>';

	echo '<label for="_jwppp-video-description">';
	echo '<strong>' . __( 'Video description', 'jwppp' ) . '</strong>';
	echo '</label> ';
	echo '<p><input type="text" id="_jwppp-video-description" name="_jwppp-video-description" placeholder="' . __('Add a description to your video', 'jwppp') . '" value="' . esc_attr( $video_description ) . '" size="60" /></p>';

	echo '<p>';
	echo '<label for="_jwppp-single-embed">';
	echo '<input type="checkbox" id="_jwppp-single-embed" name="_jwppp-single-embed" value="1" disabled="disabled"';
	echo ($jwppp_embed_video == 1) ? ' checked="checked"' : '';
	echo ' />';
	echo '<strong>' . __('Allow to embed this video', 'jwppp') . '</strong> | <a href="http://www.ilghera.com/product/jw-player-7-for-wordpress-premium/" target="_blank">Upgrade</a>';
	echo '</label>';
	echo '<input type="hidden" name="single-embed-hidden" value="1" />';
	echo '</p>';

	echo '</div>';
	
}

function jwppp_save_meta_box_data( $post_id ) {

	if ( ! isset( $_POST['jwppp_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['jwppp_meta_box_nonce'], 'jwppp_save_meta_box_data' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	if ( ! isset( $_POST['_jwppp-video-url'] ) ) {
		return;
	}
	if ( ! isset( $_POST['_jwppp-video-title'] ) ) {
		return;
	}

	if ( ! isset( $_POST['_jwppp-video-description'] ) ) {
		return;
	}

	$video = sanitize_text_field($_POST['_jwppp-video-url']);
	$title = sanitize_text_field($_POST['_jwppp-video-title']);
	$description = sanitize_text_field($_POST['_jwppp-video-description']);

	update_post_meta( $post_id, '_jwppp-video-url', $video );
	update_post_meta( $post_id, '_jwppp-video-title', $title );
	update_post_meta( $post_id, '_jwppp-video-description', $description );
	
}
add_action( 'save_post', 'jwppp_save_meta_box_data' );


//SCRIPT AND LICENCE KEY FOR JW PLAYER
function jwppp_add_header_code() {
	$library = sanitize_text_field(get_option('jwppp-library'));
	$licence = sanitize_text_field(get_option('jwppp-licence'));
	if($library != null) {
		echo '​<script src="' . $library . '"></script>';
	}
	if($licence != null) {
		echo '<script>jwplayer.key="' . $licence . '";</script>';
	}
}
add_filter('wp_head', 'jwppp_add_header_code');


//GET ALL VIDEO POSTS
function jwppp_get_video_posts() {
	global $wpdb;
	$query = "SELECT * FROM $wpdb->postmeta WHERE meta_key = '_jwppp-video-url' AND meta_value <> ''";
	$posts = $wpdb->get_results($query);
	$video_posts = array();
	foreach($posts as $post) {
		array_push($video_posts, $post->post_id);
	}
	return $video_posts;
}

//JW PLAYER CODE
function jwppp_video_code() {

		//GET THE OPTIONS
		$video_title = get_post_meta(get_the_ID(), '_jwppp-video-title', true);
		$video_description = get_post_meta(get_the_ID(), '_jwppp-video-description', true);
		$jwppp_player_width = sanitize_text_field(get_option('jwppp-player-width'));
		$jwppp_player_height = sanitize_text_field(get_option('jwppp-player-height'));
		$jwppp_skin = sanitize_text_field(get_option('jwppp-skin'));
		$active_share = sanitize_text_field(get_option('jwppp-active-share'));		
		$jwppp_embed_video = sanitize_text_field(get_option('jwppp-embed-video'));
		$jwppp_video_url = get_post_meta(get_the_ID(), '_jwppp-video-url', true);
		$youtube1 = 'https://www.youtube.com/watch?v=';
		$youtube2 = 'https://youtu.be/';
		$youtube_embed = 'https://www.youtube.com/embed/';

		if(strpos($jwppp_video_url, $youtube1) !== false) {
			$jwppp_embed_url = str_replace($youtube1, $youtube_embed, $jwppp_video_url);
		} else if(strpos($jwppp_video_url, $youtube2) !== false) {
			$jwppp_embed_url = str_replace($youtube2, $youtube_embed, $jwppp_video_url);	
		} else {
			$jwppp_embed_url = $jwppp_video_url;
		}

		$jwppp_show_related = sanitize_text_field(get_option('jwppp-show-related'));
		$jwppp_show_ads = sanitize_text_field(get_option('jwppp-active-ads'));

		$output = "<div id='myElement'>";		
		$output .= __('Loading the player...', 'jwppp');
		$output .= "</div>\n"; 
		$output .= "<script type='text/javascript'>\n";
			$output .= "var playerInstance = jwplayer(\"myElement\");\n";
			$output .= "playerInstance.setup({\n";
			    $output .= "file: '" . get_post_meta(get_the_ID(), '_jwppp-video-url', true) . "',\n"; 
			    $output .= "image: '" . get_option('jwppp-poster-image') . "',\n";

			    $output .= "width: '";
			    $output .= ($jwppp_player_width != null) ? $jwppp_player_width : '640';
			    $output .= "',\n";
			    $output .= "height: '";
			    $output .= ($jwppp_player_height != null) ? $jwppp_player_height : '360';
			    $output .= "',\n";				   

			    if($jwppp_skin != 'none') {
			    	$output .= "skin: {\n";
			    	$output .= "name: '" . $jwppp_skin . "'\n";
			    	$output .= "},\n";
			    }

			    // $output .= "'stretching': 'exactfit',\n";
			    if($video_title) {
				    $output .= "title: '" . $video_title . "',\n";
				}
				if($video_description) {
				    $output .= "description: '" . $video_description . "',\n";
				}

				if($active_share == 1) {
					$output .= "sharing: {\n";
						$jwppp_share_heading = sanitize_text_field(get_option('jwppp-share-heading'));
						if($jwppp_share_heading != null) {
							$output .= "heading: '" . $jwppp_share_heading . "',\n";
						} else {
							$output .= "heading: '" . __('Share Video', 'jwppp') . "',\n"; 
						}
						if($jwppp_embed_video == 1) {
							$output .= "code: '<iframe src=\"" . $jwppp_embed_url . "\"  width=\"640\"  height=\"360\"  frameborder=\"0\"  scrolling=\"auto\"></iframe>'\n";
						}
					$output .= "},\n";
				}

		  $output .= "})\n";
		$output .= "</script>\n";

		return $output;
}

//ADD PLAYER TO THE CONTENT
function jwppp_add_player($content) {
	global $post;
	$type = get_post_type($post->ID);
	if(is_singular() && (sanitize_text_field(get_option('jwppp-type-' . $type)) == 1) && (get_post_meta($post->ID, '_jwppp-video-url', true))) {
		$video = jwppp_video_code();
		$position = get_option('jwppp-position');
		if($position == 'after-content') {
			$content = $content . $video;
		} else {
			$content = $video . $content;
		}
		
	} else {
		$content = $content;
	}

	return $content;
}
add_filter('the_content', 'jwppp_add_player');