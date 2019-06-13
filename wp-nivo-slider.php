<?php
/*
Plugin Name: WP Nivo Slider
Plugin URI: http://www.tealictafrica.com/wp-nivo-slider/
Description: Creates a slider using js created by Gilbert Pellegrom. WordPressed by Rafael Cirolini, Forked by Alex Muturi
Version: 2.0.1
Author: Alex Muturi
Author URI: http://www.tealictafrica.com/
License: GPL2
*/

/*  Copyright 2010  WP Nivo Slider - Rafael Cirolini  (email : rafael@nerdhead.com.br) , Alex Muturi (alex@tealictafrica.com)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('admin_menu', 'wpns_add_menu');
add_action('admin_init', 'wpns_reg_function' );

register_activation_hook( __FILE__, 'wpns_activate' );

add_theme_support('post-thumbnails');

function wpns_add_menu() {
    $page = add_options_page('WP Nivo Slider', 'WP Nivo Slider', 'administrator', 'wpns_menu', 'wpns_menu_function');
    add_submenu_page('edit.php?post_type=slider', 'Settings', 'Settings', 'manage_options', 'slider-options', 'wpns_menu_function');
}

function wpns_reg_function() {
	register_setting( 'wpns-settings-group', 'wpns_posts' );
	register_setting( 'wpns-settings-group', 'wpns_effect' );
	register_setting( 'wpns-settings-group', 'wpns_slices' );
	register_setting( 'wpns-settings-group', 'wpns_animspeed' );
	register_setting( 'wpns-settings-group', 'wpns_pausetime' );
	register_setting( 'wpns-settings-group', 'wpns_width' );
	register_setting( 'wpns-settings-group', 'wpns_height' );
}

function wpns_activate() {
	add_option('wpns_posts','4');
	add_option('wpns_effect','random');
	add_option('wpns_slices','5');	
	add_option('wpns_animspeed','500');	
	add_option('wpns_pausetime','3000');	
}

wp_enqueue_script('nivo_slider', WP_PLUGIN_URL . '/wp-nivo-slider/js/jquery.nivo.slider.pack.js', array('jquery'), '2.3' );

// Initiate Custom Post Type
	add_action('init', 'create_slider');
	function create_slider() {
    	$slider_args = array(
        	'label' => __('WP Nivo Slider'),
			'singular_label' => __('slider'),
        	'public' => true,
        	'capability_type' => 'post',
        	'hierarchical' => false,
        	'rewrite' => true,
			'menu_icon' => WP_PLUGIN_URL .'/wp-nivo-slider/images/slider.gif',
        	'supports' => array('thumbnail', 'title', 'editor', 'page-attributes')
        );
    	register_post_type('slider',$slider_args);
	}	

	add_action("admin_init", "add_slider");

	// Save Metaboxes
	add_action('save_post', 'update_rotatorlink'); 
	add_action('save_post', 'update_rotator_new_window');                                                 
	add_action('save_post', 'update_rotatormediacontent');
		
	
	// Adds Metaboxes
	function add_slider(){
		add_meta_box("rotatorlink_details", "Add a link to this rotator slide ", "rotatorlink_options", "slider", "normal", "low");  
		add_meta_box("rotatormediacontent_details", "Optional: Add a video to play when this rotator slide is clicked", "rotatormediacontent_options", "slider", "normal", "low"); 	
	}
	
	
	// Print Metaboxes
	
	// Rotator Link
	function rotatorlink_options(){
		global $post;
 		
		$NIVO_rotatorlink =  get_post_meta($post->ID, 'NIVO_rotatorlink', true);
		$NIVO_new_window = get_post_meta($post->ID, 'NIVO_new_window', true);  
	?>
	<div id="portfolio-options">
		<input name="NIVO_rotatorlink" size="100" value="<?php echo $NIVO_rotatorlink; ?>" /> <input type = "checkbox" <?php if($NIVO_new_window == 'on') echo 'checked'; ?> name = "NIVO_new_window"> Open link in new window  <br />  
      <p><em>Ex: http://www.domain.com/pagename or /about. If you are linking to an external site, you must include http://</em></p>
	</div><!--end portfolio-options-->   
	<?php
	}
	  
	// Rotator Media
	function rotatormediacontent_options(){
		global $post;

		$NIVO_mediacontent = get_post_meta($post->ID, 'NIVO_mediacontent', true);
	?>
	<div id="portfolio-options">
		<textarea name="NIVO_mediacontent" style = "width: 100%; height: 100px"><?php echo $NIVO_mediacontent; ?></textarea> <br />
        <p><em>Paste an embed code from YouTube, Vimeo, or video player of your choice</em></p>
	</div><!--end portfolio-options-->   
	<?php
	}
	
	
	// Saves to Post Meta
	function update_rotatorlink(){
		global $post;
		update_post_meta($post->ID, "NIVO_rotatorlink", $_POST["NIVO_rotatorlink"]);
	}  
	
	function update_rotator_new_window(){
		global $post;
		update_post_meta($post->ID, "NIVO_new_window", $_POST["NIVO_new_window"]);
	}     
	
	function update_rotatormediacontent(){
		global $post;
		update_post_meta($post->ID, "NIVO_mediacontent", $_POST["NIVO_mediacontent"]);
	}


function show_nivo_slider() {
?>

<style type="text/css">
#slider {
	/*-moz-box-shadow:0 0 10px #333333;*/
	background:url("<?php echo WP_PLUGIN_URL . "/wp-nivo-slider/"; ?>images/loading.gif") no-repeat scroll 50% 50% #202834;
	width:<?php echo get_option('wpns_width'); ?>px; /* Change this to your images width */
    height:<?php echo get_option('wpns_height'); ?>px; /* Change this to your images height */
    margin-bottom:20px;
}
#slider img {
	position:absolute;
	top:0px;
	left:0px;
	display:none;
}
#slider a {
	border:0 none;
	display:block;
}
/* The Nivo Slider styles */
.nivoSlider {
	position:relative;
}
.nivoSlider img {
	position:absolute;
	top:0px;
	left:0px;
}
/* If an image is wrapped in a link */
.nivoSlider a.nivo-sliderImage {
	position:absolute;
	top:0px;
	left:0px;
	width:100%;
	height:100%;
	border:0;
	padding:0;
	margin:0;
	z-index:60;
	display:none;
}
/* The slices in the Slider */
.nivo-slice {
	display:block;
	position:absolute;
	z-index:50;
	height:100%;
}
/* Caption styles */
.nivo-caption {
	position:absolute;
	/*left:0px;
	bottom:0px;
	background:#000;
	color:#fff;
	opacity:0.8; /* Overridden by captionOpacity setting */
/*	width:100%;	
	z-index:89;
	position:absolute;*/
	overflow:hidden;
	left:29px;
	bottom:10px;
	background: url("<?php echo WP_PLUGIN_URL . "/wp-nivo-slider/"; ?>images/bg-slide.png") repeat;
	color:#5a5858;
	width:90%;
	z-index:89;
	height:100px;
	border:1px #cfcfcf solid;
	border-radius:3px;
	opacity: 1!important;
	-moz-opacity: 1!important;
	filter:alpha(opacity=1)!important;
}
/*.nivo-caption p {
	padding:5px;
	margin:0;
}
.nivo-caption a {
	display:inline !important;
}
.nivo-html-caption {
    display:none;
}*/

.nivo-caption div{ 
	position:relative;
	}

.nivo-caption div h1{ 
	color:#fff; 
	font-size:18px; 
	text-transform:uppercase;
	font-weight:600; 
	padding-top:10px;  
	padding-bottom:0;
	position:absolute; 
	left:0px; top:0px; 
	margin-bottom:0;
	font-family:'Open Sans', sans-serif;
	opacity:1;
/*	filter: alpha(opacity=70);
*/}

.nivo-caption div p{ 
	color:#444;
	font-size:14px;
	text-align:left;
	line-height:20px;
	margin:0 0 12px 12px;
	opacity:1;
/*	filter: alpha(opacity=80);
*/}
/*.nivo-caption p  a{
	background:#999999;
	font-weight:bold;
	padding:0;
	position:absolute;
	right:79px;
	top:58px;
	font-size:19px;
	color:#fff;
	text-transform:uppercase;
	text-decoration:none;
	opacity:1;
    filter: alpha(opacity=80);
	width:105px;
	height:37px;
}
.nivo-caption p  a:hover{ background-position:left bottom;}*/
.nivo-caption > div {
	margin:0;
	padding:5px;
	text-align:left;
	font-size:14px;
}
.nivo-caption strong {
	color:#4d4f51;
	font-size:17px;
}	
.nivo-caption h2 {
	font-size:19px;
	line-height:normal;
	font-family:'Open Sans', sans-serif;
	color:#c64b4b;
	font-weight:400;
	margin-bottom:5px;
}
.nivo-caption div span {
	display:block;
	font-size:24px;
	color:#272626;
	line-height:27px;
	padding-top:24px;
	padding-bottom:5px;
	font-weight:normal;
}
.nivo-caption a {
	display:inline !important;
}

a.slider_more{
    background:#0099CC;
    border: 1px solid #3990AB;
    color: #FFFFFF;
    border-radius: 2px 2px 2px 2px;
    cursor: pointer;
    font-weight: 400;
    height: 28px;
    line-height: 28px;
    margin: 10px 16px 0 10px;
    min-width: 54px;
    outline: 0 none;
    padding: 8px 15px;
    text-align: center;
    text-decoration:none;
	opacity:1;
}

.nivo-caption a.slider_more:after {
    content: " ›";
    font-size: 1.6em;
    position: relative;
    top: 1px;
}

a.slider_more:hover, a.slider_more:active {
    background-color: #1E799A;
    background-image: none;
    border-color: #30B7E6;
}

/* Direction nav styles (e.g. Next & Prev) */
.nivo-directionNav a {
	position:absolute;
	top:73%;
	z-index:99;
	cursor:pointer;
}
.nivo-prevNav {
	left:0px;
}
.nivo-nextNav {
	right:0px;
}
.nivo-controlNav {
	bottom:-30px;	
	left:47%;
	position:absolute;
}
.nivo-controlNav a {
	background:url("<?php echo WP_PLUGIN_URL . "/wp-nivo-slider/"; ?>images/bullets.png") no-repeat scroll 0 0 transparent;
	border:0 none;
	display:block;
	float:left;
	height:10px;
	margin-right:3px;
	text-indent:-9999px;
	width:10px;
}
.nivo-controlNav a.active {
	background-position:-10px 0;
}
.nivo-controlNav a {
	cursor:pointer;
	position:relative;
	z-index:99;
}
.nivo-controlNav a.active {
	font-weight:bold;
}
.nivo-directionNav a {
	background:url("<?php echo WP_PLUGIN_URL . "/wp-nivo-slider/"; ?>images/arrows.png") no-repeat scroll 0 0 transparent;
	border:0 none;
	display:block;
	/*height:34px;*/
	text-indent:-9999px;
	/*width:32px;*/
	width:49px;
	height:50px;
	text-indent:-9999px;
	border:0;
}
a.nivo-nextNav {
	background-position:-32px 0;
	right:60px;
	background:url("<?php echo WP_PLUGIN_URL . "/wp-nivo-slider/"; ?>images/next.png") 0 bottom no-repeat;
	border-radius:32px;
}
a.nivo-prevNav {
	left:900px;
	background:url("<?php echo WP_PLUGIN_URL . "/wp-nivo-slider/"; ?>images/prev.png") 0 bottom no-repeat;
	border-radius:32px;
}

a.nivo-nextNav:hover, a.nivo-prevNav:hover {
	background-position:0 0;	
}
</style>

<script type="text/javascript">
jQuery(window).load(function() {
	jQuery('#slider').nivoSlider({
		effect:'<?php echo get_option('wpns_effect'); ?>',
		slices:<?php echo get_option('wpns_slices'); ?>,
		animSpeed:<?php echo get_option('wpns_animspeed'); ?>, //Slide transition speed
        pauseTime:<?php echo get_option('wpns_pausetime'); ?>,
        startSlide:0, //Set starting Slide (0 index)
        directionNav:false, //Next & Prev
        directionNavHide:false, //Only show on hover
        controlNav:false, //1,2,3...
        controlNavThumbs:false, //Use thumbnails for Control Nav
        controlNavThumbsFromRel:false, //Use image rel for thumbs
        controlNavThumbsSearch: '.jpg', //Replace this with...
        controlNavThumbsReplace: '_thumb.jpg', //...this in thumb Image src
        keyboardNav:true, //Use left & right arrows
        pauseOnHover:true, //Stop animation while hovering
        manualAdvance:false, //Force manual transitions
        captionOpacity:0.8, //Universal caption opacity
        beforeChange: function(){},
        afterChange: function(){},
        slideshowEnd: function(){}, //Triggers after all slides have been shown
        lastSlide: function(){}, //Triggers when last slide is shown
        afterLoad: function(){} //Triggers when slider has loaded
	});
});
</script>
		
<div id="slider">

<?php 
	$n_posts = get_option('wpns_posts');  //No of posts to show in the slider
	
	query_posts( 'post_type=slider&posts_per_page='.$n_posts ); if( have_posts() ) : while( have_posts() ) : the_post(); 

	$title = get_the_title();
	$desc = get_the_excerpt(); 
	$link = get_post_permalink();
	$rotatorLink = get_post_meta( get_the_ID(), 'NIVO_rotatorlink', true );     
	$slider_link = ($rotatorLink !='')?  $rotatorLink : $link;

    $rotatorNewWindow = get_post_meta( get_the_ID(), 'NIVO_new_window', true );   
    $target = ($rotatorNewWindow !='')? '_blank' : '_self';

	if(has_post_thumbnail()) : ?>
	<div> 
		<?php the_post_thumbnail('slider-thumb',array('title'=>$title,'desc'=>$desc,'link'=>$slider_link,'target'=>$target)); ?>
	</div>
	<?php endif; ?>
	<?php endwhile; endif;?>
	<?php wp_reset_query();?>
</div>

<?php } 

function wpns_menu_function() {

?>

<div class="wrap">
<h2>WP Nive Slider</h2>
 
<form method="post" action="options.php">
    <?php settings_fields( 'wpns-settings-group' ); ?>
    <table class="form-table">
      
        <tr valign="top">
        <th scope="row">Number of posts to show in the slider</th>
        <td>
        <label>
        <input type="text" name="wpns_posts" id="wpns_posts" size="7" value="<?php echo get_option('wpns_posts'); ?>" />
        </label>
        </tr>

        <tr valign="top">
        <th scope="row">Number of slices</th>
        <td>
        <label>
        <input type="text" name="wpns_slices" id="wpns_slices" size="7" value="<?php echo get_option('wpns_slices'); ?>" />
        </label>
        </tr>
        
        <tr valign="top">
        <th scope="row">Type of Animation</th>
        <td>
        <label>
        <?php $effect = get_option('wpns_effect'); ?>
        <select name="wpns_effect" id="wpns_effect">
        	<option value="random" <?php if($effect == 'random') echo 'selected="selected"'; ?>>Random</option>
        	<option value="sliceDown" <?php if($effect == 'sliceDown') echo 'selected="selected"'; ?> >sliceDown</option>
        	<option value="sliceDownLeft" <?php if($effect == 'sliceDownLeft') echo 'selected="selected"'; ?> >sliceDownLeft</option>
        	<option value="sliceUp" <?php if($effect == 'sliceUp') echo 'selected="selected"'; ?> >sliceUp</option>
        	<option value="sliceUpLeft" <?php if($effect == 'sliceUpLeft') echo 'selected="selected"'; ?> >sliceUpLeft</option>
        	<option value="sliceUpDown" <?php if($effect == 'sliceUpDown') echo 'selected="selected"'; ?> >sliceUpDown</option>
        	<option value="sliceUpDownLeft" <?php if($effect == 'sliceUpDownLeft') echo 'selected="selected"'; ?> >sliceUpDownLeft</option>
        	<option value="fold" <?php if($effect == 'fold') echo 'selected="selected"'; ?> >fold</option>
        	<option value="fade" <?php if($effect == 'fade') echo 'selected="selected"'; ?> >fade</option>
        </select>
        </label>
        </tr>

        <tr valign="top">
			<td>This speed of slide animation.</td>
        </tr>

        <tr valign="top">
        <th scope="row">Animation speed</th>
        <td>
        <label>
        <input type="text" name="wpns_animspeed" id="wpns_animspeed" size="7" value="<?php echo get_option('wpns_animspeed'); ?>" />
        </label>
        </tr>

        <tr valign="top">
			<td>Pause duration between slides.</td>
        </tr>

        <tr valign="top">
        <th scope="row">Pause time</th>
        <td>
        <label>
        <input type="text" name="wpns_pausetime" id="wpns_pausetime" size="7" value="<?php echo get_option('wpns_pausetime'); ?>" />
        </label>
        </tr>
		
		<tr valign="top">
			<td colspan=2>This is size of yours images. This plugin do not resize images.</td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Width</th>
        <td>
        <label>
        <input type="text" name="wpns_width" id="wpns_width" size="7" value="<?php echo get_option('wpns_width'); ?>" />px
        </label>
        </tr>
		
		<tr valign="top">
        <th scope="row">Height</th>
        <td>
        <label>
        <input type="text" name="wpns_height" id="wpns_height" size="7" value="<?php echo get_option('wpns_height'); ?>" />px
        </label>
        </tr>
    
    </table>
 
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
 
</form>
</div>

<?php } ?>
