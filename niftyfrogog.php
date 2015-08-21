<?php
/*
	Plugin Name: NiftyFrog OG
	Plugin URI: http://niftyfrog.com/plugins/niftyfrog.php/
	Description: Places meta tags in your blog's header, so a suitable image and description show, when crossposting to Facebook or generating a Twitter Card.
	Version: 0.4
	Author: Michelle Thompson
	Author URI: http://niftyfrog.com/
	License: GPLv3
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Keep track for running update functions
if ( !defined( 'NFOG_VERSION_NUM' ) ):
    define( 'NFOG_VERSION_NUM', '0.4' );
endif;

// Add admin menu item
function nf_og_menu_item() {
	$thispage = add_options_page( 'NiftyFrog OG Options', 'NF OG Options', 'manage_options', 'nf-og-options', 'nf_og_setup_screen' );
	add_action( 'admin_print_styles-' . $thispage, 'admin_nf_styles' );
	//add_action( 'admin_print_scripts-' . $thispage, 'admin_nf_scripts' );
}

// Enable options, and register css and js    
function nf_og_options_init(){
	register_setting( 'niftyfrogog', 'nfogoptions', 'check_input' );
	wp_register_style( 'nfadminstyles', plugins_url('styles.css', __FILE__) );
}

// Delete options from DB
function nf_og_options_del(){
	delete_option( 'nfogoptions' );
}

// Disable options
function nf_og_options_undo(){
	unregister_setting( 'niftyfrogog', 'nfogoptions', 'check_input' );
	wp_dequeue_style( 'nfadminstyles' );
	wp_deregister_style( 'nfadminstyles' );
}

// Update options and dequeue and deregister unused scripts
function nf_og_update_files() {
	$options = get_option('nfogoptions');
	if ( !isset( $options['nfogversion'] ) ):
		$options = array_merge( $options, array( 'nfogversion' => NFOG_VERSION_NUM ) );
		update_option( 'nfogoptions', $options );
		wp_dequeue_script( 'nfadminjs' );
		wp_deregister_script( 'nfadminjs' );
	endif;
}

// Sanitize user form input
function check_input( $input ){
	// Format version number
	$input['nfogversion'] = preg_replace("/[^0-9\.]/", "", $input['nfogversion']);
	
	// Strip tags, and make sure image url starts correctly
	$this_def_img = trim( strip_tags( $input['defaultimg'] ) );
	if ( preg_match( '~((http|https)://)(.+?)~', $this_def_img ) ):
		$input['defaultimg'] = $this_def_img;
	else:
		$input['defaultimg'] = 'no'; // Signal not to add as a meta tag in page output;
	endif;
	
	// Strip out all but numbers and commas from FB ID
	$this_fb_userid = preg_replace( array( '/[^\d,]/', '/(?<=,),+/', '/^,+/', '/,+$/' ), '', $input['fbuserid'] );
	$this_fb_userid = trim( $this_fb_userid );
	if ( $this_fb_userid === '' ):
		$input['fbuserid'] = 'no'; // Signal not to add as a meta tag in page output
	else:
		$input['fbuserid'] = $this_fb_userid;		
	endif;
	
	// Strip out all but letters, numbers, and underscores from Twitter name
	$this_twit_userid = str_ireplace( 'Twitter user ID not set', '', $input['twuserid'] );
	$this_twit_userid = preg_replace( '/[^\w\d_]+/', '', $this_twit_userid );
	if ( $this_twit_userid === '' ):
		$input['twuserid'] = 'no'; // Signal not to add as a meta tag in page output
	else:
		$input['twuserid'] = $this_twit_userid;		
	endif;
	
	return $input;
}

// Print the admin screen 
function nf_og_setup_screen() {
	print '<div class="nfadmincontainer">';
	print '<h3>NiftyFrog OG Options</h3>';	
	print '<form method="post" action="options.php">';
	settings_fields('niftyfrogog');
	$options = get_option('nfogoptions');
	
	if ( isset( $options['nfogversion'] ) && $options['nfogversion'] === NFOG_VERSION_NUM ):
		$nf_version = $options['nfogversion'];
	else:
		$nf_version = NFOG_VERSION_NUM;
	endif;
		
	$fb_user_id = 'Facebook user ID not set';
	if ( isset( $options['fbuserid'] ) && $options['fbuserid'] !=='' && $options['fbuserid'] !== 'no' ):
		$fb_user_id = $options['fbuserid'];
	endif;
	
	$twit_user_id = 'Twitter user ID not set';
	if ( isset( $options['twuserid'] ) && $options['twuserid'] !=='' && $options['twuserid'] !== 'no' ):
		$twit_user_id = $options['twuserid'];
	endif;
	
	$current_img = 'Default image not set';
	$img_view_line = '';	
	if ( isset( $options['defaultimg'] ) && $options['defaultimg'] !== '' && $options['defaultimg'] !== 'no' ):
		$current_img = $options['defaultimg'];
		$img_view_line = '<p>Current default image:<br /><img src="' . $current_img . '" title="Current Image" class="defaultimg" /></p>';
	endif;	
	
	print '<p>Enter your Facebook <em>numeric</em> user ID here. Facebook uses this information to determine who has access to page insights, and who can administer Facebook apps for your page. You can enter multiple ID&#39;s, separated by commas. This plugin will generate image og tags with or without this.</p>';
	print '<div class="nfogrow"><div class="nfoglabel">Your numeric Facebook ID:</div>';
	print '<input type="text" name="nfogoptions[fbuserid]" value="' . $fb_user_id . '" size="33" onClick="this.setSelectionRange(0, this.value.length)" /></div>';	
	print '<p>Enter your Twitter user ID here, without the "&#64;" sign. Twitter uses this information for card analytics.</p>';
	print '<div class="nfogrow"><div class="nfoglabel">Your Twitter ID:</div>';
	print '<input type="text" name="nfogoptions[twuserid]" value="' . $twit_user_id . '" size="33" onClick="this.setSelectionRange(0, this.value.length)" /></div>';	
	print '<p>The default image you specify here will be used in the meta tags that are created, if no featured image is set for, and no other image is found in, a post or page. Enter the full URL, beginning with http://, to your default image. Make sure the image size is at least 200px by 200px, so that Facebook will recognize it.</p>'; 
	print '<div class="nfogrow"><div class="nfoglabel">Full URL to your default image:</div>';
	print '<input type="text" name="nfogoptions[defaultimg]" value="' . $current_img . '" size="33" onClick="this.setSelectionRange(0, this.value.length)" /></div>';
	print '<input type="hidden" name="nfogoptions[nfogversion]" value="' . $nf_version . '" />';	
	print '<div class="nfogrow"><input type="submit" value="Submit" /></div>';
	print '</form>';
	print $img_view_line;
	print '<p>There are no other options to set. This plugin uses the information from your individual posts to create the necessary meta tags.</p>';
	print '</div>';
	print_donate_button();
}

// Print meta tags for header
function print_meta_tags() {
	$options = get_option('nfogoptions');
	$nfog_fb_userid = 'no';
	if ( isset( $options['fbuserid'] ) && $options['fbuserid'] !== '' ):
		$nfog_fb_userid = $options['fbuserid'];
	endif;	
	$nfog_tw_userid = 'no';
	if ( isset( $options['twuserid'] ) && $options['twuserid'] !== '' ):
		$nfog_tw_userid = $options['twuserid'];
	endif;		
	$nfog_title = get_bloginfo('name');
	$nfog_type = 'website';
	$nfog_url = get_bloginfo('url');
	$nfog_descr = get_bloginfo('description');
	$nfog_img = 'no';	
	if ( isset( $options['defaultimg'] ) && $options['defaultimg'] !== '' ):
		$nfog_img = $options['defaultimg'];
	endif;
	$no_img_msg = '<!-- No Default Image set -->';
	$no_fb_userid_msg = '<!-- No Facebook Admin ID set -->';
	$no_tw_userid_msg = '<!-- No Twitter ID set -->';
	if(is_single() || is_page()):
		global $post;
		$cross_post_id = $post->ID; // Get the post ID
		$nfog_title = get_the_title( $cross_post_id );
		$nfog_type = 'article';
		$nfog_url = get_permalink( $cross_post_id );
		
		// Get post content
		$thisPost = get_post( $cross_post_id );
		$cpcontent = $thisPost->post_content;
		
		// Check for images, and retrieve the featured, first, or default image as the og image
		if( has_post_thumbnail( $cross_post_id ) ):
			$featured_img_id = get_post_thumbnail_id( $cross_post_id );
			$nfog_img = wp_get_attachment_url( $featured_img_id );
		else:	
			if( strpos( $cpcontent, "<img" ) ):
				$first_img = '';
				$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $cpcontent, $matches);
				$nfog_img = $matches [1] [0];
			endif;
		endif;
		
		// Get post excerpt, or use the content, and tidy it
		if ( $thisPost->post_excerpt ):
		$cpcontent = $thisPost->post_excerpt;
		endif;
		$nfog_descr = strip_tags( strip_shortcodes( $cpcontent ) );
		$nfog_descr = str_replace( array( "\n", "\t", "\r" ), '', $nfog_descr );
		// Shorten and add ... if more than 200 chars
		if ( strlen( utf8_decode( $nfog_descr ) ) > 200 ):
			$word_cutoff = strrpos( substr( $nfog_descr, 0, 197 ), ' ' );
			$nfog_descr = trim( substr( $nfog_descr, 0, $word_cutoff ) ) . '...';
		endif;	
	endif;
	
	// Print the meta tags
	print "\n<!-- NiftyFrog OG for Facebook and Twitter Crossposting -->\n";	
	if ( $nfog_tw_userid === 'no' ):
		print $no_tw_userid_msg . "\n";		
	else:
		print '<meta name="twitter:card" content="summary"' . " />\n";
		print '<meta property="twitter:site" content="&#64;' . $nfog_tw_userid . "\" />\n";
	endif;
	if ( $nfog_fb_userid === 'no' ):
		print $no_fb_userid_msg . "\n";		
	else:		
		print '<meta property="fb:admins" content="' . $nfog_fb_userid . "\" />\n";
	endif;
	print '<meta property="og:site_name" content="' . htmlentities( get_bloginfo('name'), ENT_COMPAT, 'UTF-8', FALSE ) . "\" />\n";
	print '<meta property="og:title" content="' . htmlentities( $nfog_title, ENT_COMPAT, 'UTF-8', FALSE ) . "\" />\n";
	print '<meta property="og:type" content="' . $nfog_type . "\" />\n";
	print '<meta property="og:url" content="' . $nfog_url . "\" />\n";
	print '<meta property="og:description" content="' . htmlentities( $nfog_descr, ENT_COMPAT, 'UTF-8', FALSE ) . "\" />\n";
	if ( $nfog_img === 'no' ):
		print $no_img_msg . "\n";
	else:
		print '<meta property="og:image" content="' . $nfog_img . "\" />\n";
	endif;
	print "<!-- End NiftyFrog OG for Facebook and Twitter Crossposting -->\n\n";
}

// enqueue scripts
function admin_nf_scripts() {}

// enqueue styles
function admin_nf_styles() {
	wp_enqueue_style( 'nfadminstyles' );
}

function print_donate_button() {
	print '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">';
	print '<div class="nfogrow"><div class="nfoglabel regfontwt">My coding is fueled by coffee. If you find this plugin helpful, feel free to buy me a cup of my favorite brew.</div> ';
	print '<input type="hidden" name="cmd" value="_s-xclick">';
	print '<input type="hidden" name="hosted_button_id" value="CZA4ZWJUVVZHN">';
	print '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"></div>';
	print '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';
	print '</form>';
}

add_action('admin_init', 'nf_og_options_init' );
add_action('admin_init', 'nf_og_update_files' );
add_action( 'admin_menu', 'nf_og_menu_item' );
add_action( 'wp_head', 'print_meta_tags' );
register_deactivation_hook( __FILE__, 'nf_og_options_undo' );
register_uninstall_hook( __FILE__, 'nf_og_options_del' );

?>
