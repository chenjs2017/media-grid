<?php

// get the current URL
function lcwp_curr_url() {
	$pageURL = 'http';
	
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://" . $_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];

	return $pageURL;
}
	

// get file extension from a filename
function lcwp_stringToExt($string) {
	$pos = strrpos($string, '.');
	$ext = strtolower(substr($string,$pos));
	return $ext;	
}


// get filename without extension
function lcwp_stringToFilename($string, $raw_name = false) {
	$pos = strrpos($string, '.');
	$name = substr($string,0 ,$pos);
	if(!$raw_name) {$name = ucwords(str_replace('_', ' ', $name));}
	return $name;	
}


// string to url format // NEW FROM v1.11 for non-latin characters 
function lcwp_stringToUrl($string){
	
	// if already exist at least an option, use the default encoding
	if(!get_option('mg_non_latin_char')) {
		$trans = array("à" => "a", "è" => "e", "é" => "e", "ò" => "o", "ì" => "i", "ù" => "u");
		$string = trim(strtr($string, $trans));
		$string = preg_replace('/[^a-zA-Z0-9-.]/', '_', $string);
		$string = preg_replace('/-+/', "_", $string);	
	}
	
	else {$string = trim(urlencode($string));}
	
	return $string;
}


// normalize a url string
function lcwp_urlToName($string) {
	$string = ucwords(str_replace('_', ' ', $string));
	return $string;	
}


// remove a folder and its contents
function lcwp_remove_folder($path) {
	if($objs = @glob($path."/*")){
		foreach($objs as $obj) {
			@is_dir($obj)? lcwp_remove_folder($obj) : @unlink($obj);
		}
	 }
	@rmdir($path);
	return true;
}


// convert HEX to RGB
function mg_hex2rgb($hex) {
   	// if is RGB or transparent - return it
   	$pattern = '/^#[a-f0-9]{6}$/i';
	if(empty($hex) || $hex == 'transparent' || !preg_match($pattern, $hex)) {return $hex;}
  
	$hex = str_replace("#", "", $hex);
   	if(strlen($hex) == 3) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	$rgb = array($r, $g, $b);
  
	return 'rgb('. implode(",", $rgb) .')'; // returns the rgb values separated by commas
}


// convert RGB to HEX
function mg_rgb2hex($rgb) {
   	// if is hex or transparent - return it
   	$pattern = '/^#[a-f0-9]{6}$/i';
	if(empty($rgb) || $rgb == 'transparent' || preg_match($pattern, $rgb)) {return $rgb;}

  	$rgb = explode(',', str_replace(array('rgb(', ')'), '', $rgb));
  	
	$hex = "#";
	$hex .= str_pad(dechex( trim($rgb[0]) ), 2, "0", STR_PAD_LEFT);
	$hex .= str_pad(dechex( trim($rgb[1]) ), 2, "0", STR_PAD_LEFT);
	$hex .= str_pad(dechex( trim($rgb[2]) ), 2, "0", STR_PAD_LEFT);

	return $hex; 
}


// hex color to RGBA
function mg_hex2rgba($hex, $alpha) {
	$rgba = str_replace(array('rgb', ')'), array('rgba', ', '.$alpha.')'), mg_hex2rgb($hex));
	return $rgba;	
}


// create youtube and vimeo embed url
function lcwp_video_embed_url($raw_url, $manual_autoplay = '') {
	if(strpos($raw_url, 'vimeo') !== false) {
		$code = substr($raw_url, (strrpos($raw_url, '/') + 1));
		$url = '//player.vimeo.com/video/'.$code.'?title=0&amp;byline=0&amp;portrait=0';
	}
	elseif(strpos($raw_url, 'youtu.be') !== false) {
		$code = substr($raw_url, (strrpos($raw_url, '/') + 1));
		$url = '//www.youtube.com/embed/'.$code.'?rel=0';	
	}
	elseif(strpos($raw_url, 'dailymotion.com') !== false || strpos($raw_url, 'dai.ly') !== false) {
		if(substr($raw_url, -1) == '/') {$raw_url = substr($raw_url, 0, -1);}
		$parts = explode('/', $raw_url);
		$arr = explode('_', end($parts));
		$url = '//www.dailymotion.com/embed/video/'.$arr[0];	
	}
	else {return 'wrong_url';}
	
	// autoplay
	if( (get_option('mg_video_autoplay') && $manual_autoplay !== false) || $manual_autoplay === true ) {
		$url .= (strpos($raw_url, 'dailymotion.com') !== false) ? '?autoPlay=1' : '&amp;autoplay=1';
	}
	
	return $url;
}


// given video URL - return self-hosted video sources for HTML player
function mg_sh_video_sources($video_url) {
	$ok_src = array();
	$allowed = array('mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv');
	$sources = explode(',', $video_url); 
		
	foreach($sources as $v_src) {
		$ext = substr(trim(lcwp_stringToExt($v_src)), 1);
		if(in_array($ext, $allowed)) {
			$ok_src[$ext] = trim($v_src);	
		}
	}
	
	$man_src = array();
	foreach($ok_src as $v_type => $url) {
		$man_src[] = '<source src="'.$url.'" type="video/'.$v_type.'">';
	}
	
	return (count($ok_src)) ? implode('', $man_src) : false;	
}


// get soundcloud embed code
function mg_get_soundcloud_embed($track_url, $inline = false, $lazyload = false) {
	
	// search for already queried tracks
	$cached = unserialize( get_option('mg_cached_soundcloud', '') );
	if(!is_array($cached)) {$cached = array();}
	
	// get track ID
	if(isset($cached[ $track_url ])) {
		$track_id = $cached[ $track_url ];	
	}
	else {
		// not cached - use cURL
		$pub = '69c06a70f88e8ec80a414ae55dab369c'; // soundcloud public key
		$url = 'https://api.soundcloud.com/resolve.json?url='. urlencode($track_url) .'&client_id='.$pub;
		
		@ini_set( 'memory_limit', '256M');
		$ch = curl_init();
	
		//curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		
		$data = (array)json_decode(curl_exec($ch));
		curl_close($ch);	
		
		// no track found
		if(!isset($data['status']) || $data['status'] != '302 - Found') {
			return '';	
		}
		
		// manage to get track ID
		$arr_1 = explode('?', $data['location']);
		$clean_1 = str_replace('.json', '', $arr_1[0]);
	
		$arr_2 = explode('/', $clean_1);
		$track_id = end($arr_2);
		
		// cache
		$cached[$track_url] = $track_id;
		update_option('mg_cached_soundcloud', serialize($cached));
	}
	

	$autoplay = ((get_option('mg_audio_autoplay') && !$inline) || ($inline && $lazyload)) ? 'true' : 'false';
	$inline_visual = ($inline) ? '&amp;visual=true' : '';
	$lazyload_code = ($lazyload) ? 'src="" data-lazy-src="' : 'src="'; 
	$z_index = ($inline && $lazyload) ? 'style="z-index: -1;"' : '';
	
	return '<iframe class="mg_soundcloud_embed" scrolling="no" frameborder="no" '.$lazyload_code.'//w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'. $track_id .'&amp;color=ff5500&amp;auto_play='.$autoplay.'&amp;hide_related=true&amp;show_artwork=true'.$inline_visual.'" '.$z_index.'></iframe>';
}


// know if woocommerce is active
function mg_woocomm_active() {
	return (in_array( 'woocommerce/woocommerce.php', apply_filters('active_plugins', get_option( 'active_plugins' )))) ? true : false;
}

///////////////////////////////////////////////////////

// get translated option name - WPML integration
function mg_wpml_string($type, $original_val) {
	if(function_exists('icl_t')){
		$typename = ($type == 'img_gallery') ? 'Image Gallery' : ucfirst($type);
		$index = $typename.' Attributes - '.$original_val;
		
		return icl_t('Media Grid - Item Attributes', $index, $original_val);
	}
	else{
		return $original_val;
	}
}


// mq/qtranslate - implement item categories translation
function mg_check_qtranslate() {
	if(is_plugin_active('mqtranslate/mqtranslate.php') || is_plugin_active('qtranslate/qtranslate.php')) {
	  add_action( 'mg_item_categories_add_form', 'qtrans_modifyTermFormFor');
	  add_action( 'mg_item_categories_edit_form', 'qtrans_modifyTermFormFor');
	}
}
if(function_exists('add_action')) {
	add_action('admin_init', 'mg_check_qtranslate');
}


///////////////////////////////////////////////////////

// sanitize input field values
function mg_sanitize_input($val) {
	global $wp_version;
	
	// not sanitize quotes  in WP 4.3 and newer
	if ($wp_version >= 4.3) {
		return trim(esc_attr($val));	
	}
	else {
		return trim(
			str_replace(array('\'', '"', '<', '>', '&'), array('&apos;', '&quot;', '&lt;', '&gt;', '&amp;'), (string)$val)
		);	
	}
}


// preloader code
function mg_preloader() {
	return '
	<div class="mg_loader">
		<div class="mgl_1"></div><div class="mgl_2"></div><div class="mgl_3"></div><div class="mgl_4"></div>
	</div>';	
}


// preloader types
function mg_preloader_types($type = false) {
	$types = array(
		'default' 				=> __('Default loader', 'mg_ml'),
		'rotating_square' 		=> __('Rotating square', 'mg_ml'),
		'overlapping_circles' 	=> __('Overlapping circles', 'mg_ml'),
		'stretch_rect' 			=> __('Stretching rectangles', 'mg_ml'),
		'spin_n_fill_square'	=> __('Spinning & filling square', 'mg_ml'),
		'pulsing_circle' 		=> __('Pulsing circle', 'mg_ml'),
		'spinning_dots'			=> __('Spinning dots', 'mg_ml'),
		'appearing_cubes'		=> __('Appearing cubes', 'mg_ml'),
		'folding_cube'			=> __('Folding cube', 'mg_ml'),
		'old_style_spinner'		=> __('Old-style spinner', 'mg_ml'),
		'minimal_spinner'		=> __('Minimal spinner', 'mg_ml'),
		'spotify_like'			=> __('Spotify-like spinner', 'mg_ml'),
		'vortex'				=> __('Vortex', 'mg_ml'),
		'bubbling_dots'			=> __('Bubbling Dots', 'mg_ml'),
		'overlapping_dots'		=> __('Overlapping dots', 'mg_ml'),
		'fading_circles'		=> __('Fading circles', 'mg_ml'),
	);
	return (!$type) ? $types : $types[$type];
}


// custom type options - indexes 
function mg_main_types() {
	return array(
		'image'			=> __('Image', 'mg_ml'), 
		'img_gallery' 	=> __('Image Gallery', 'mg_ml'), 
		'video' 		=> __('Video', 'mg_ml'), 
		'audio' 		=> __('Audio', 'mg_ml')
	);	
}


// given the item main type slug - return the name
function mg_item_types($type = false) {
	$types = array(
		'simple_img' 	=> __('Static Image', 'mg_ml'),
		'single_img' 	=> __('Single Image', 'mg_ml'),
		'img_gallery' 	=> __('Images Slider', 'mg_ml'),
		'inl_slider' 	=> __('Inline Slider', 'mg_ml'),
		'video' 		=> __('Video', 'mg_ml'),
		'inl_video' 	=> __('Inline Video', 'mg_ml'),
		'audio'			=> __('Audio', 'mg_ml'),
		'inl_audio'		=> __('Inline Audio', 'mg_ml'),
		'link'			=> __('Link', 'mg_ml'),
		'lb_text'		=> __('Custom Content', 'mg_ml'),
		'post_contents'	=> __('Post Contents', 'mg_ml'),
		'inl_text'		=> __('Inline Text', 'mg_ml'),
		'spacer'		=> __('Spacer', 'mg_ml'),
		'woocom'		=> __('WooCommerce', 'mg_ml'),
	);
	return (!$type) ? $types : $types[$type];
}


// pagination layouts
function mg_pag_layouts($type = false) {
	$types = array(
		'standard' 	 	=> __('Commands + full text', 'mg_ml'),
		'only_num'  	=> __('Commands + page numbers', 'mg_ml'),
		'only_arr'		=> __('Only arrows', 'mg_ml'),
		'only_arr_dt'	=> __('Only arrows - detached', 'mg_ml')
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}


// deeplinked elements list
function mg_elem_to_deeplink($type = false) {
	$types = array(
		'item' 		=> __("Item's lightbox", 'mg_ml'), 
		'category'	=> __("Category filter", 'mg_ml'), 
		'search'	=> __("Items search", 'mg_ml'),
		'page'		=> __("Grid pagination", 'mg_ml'),
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}	
}



// lightbox command layouts
function mg_lb_cmd_layouts($type = false) {
	$types = array(
		'inside' 	 	=> __('Inside lightbox', 'mg_ml'),
		'top' 			=> __('Detached - top of the page', 'mg_ml'),
		'side'			=> __('Detached - on sides', 'mg_ml'),
		'side_basic'	=> __('Detached - on sides (basic)', 'mg_ml'),
		'ins_hidden'	=> __('Inside - hidden navigation', 'mg_ml'),
		'hidden'		=> __('Detached - hidden navigation', 'mg_ml'),
		'round_hidden'	=> __('Rounded - hidden navigation', 'mg_ml')
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}


// lightbox layouts
function mg_lb_layouts($type = false) {
	$types = array(
		'full' 					=> __('Full Width', 'mg_ml'), 
		'side_tripartite' 		=> __('Text on right side - one third', 'mg_ml'),
		'side_tripartite_tol' 	=> __('Text on left side - one third', 'mg_ml'),
		'side_bipartite' 		=> __('Text on right side - one half', 'mg_ml'),
		'side_bipartite_tol' 	=> __('Text on left side - one half', 'mg_ml'),
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}


// slider cropping methods
function mg_galleria_crop_methods($type = false) {
	$types = array(
		'true' 		=> __('Fit, center and crop', 'mg_ml'),
		'false' 	=> __('Scale down', 'mg_ml'),
		'height'	=> __('Scale to fill the height', 'mg_ml'),
		'width'		=> __('Scale to fill the width', 'mg_ml'),
		'landscape'	=> __('Fit images with landscape proportions', 'mg_ml'),
		'portrait' 	=> __('Fit images with portrait proportions', 'mg_ml')
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}


// lightbox slider effects
function mg_galleria_fx($type = false) {
	$types = array(
		'fadeslide' => __('Fade and slide', 'mg_ml'),
		'fade' 		=> __('Fade', 'mg_ml'),
		'flash'		=> __('Flash', 'mg_ml'),
		'pulse'		=> __('Pulse', 'mg_ml'),
		'slide'		=> __('Slide', 'mg_ml'),
		''			=> __('None', 'mg_ml')
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}


// inline slider effects
function mg_inl_slider_fx($type = false) {
	$types = array(
		'fadeslide' => __('Fade and slide', 'mg_ml'),
		'fade' 		=> __('Fade', 'mg_ml'),
		'slide'		=> __('Slide', 'mg_ml'),
		'zoom-in'	=> __('Zoom-in', 'mg_ml'),
		'zoom-out'	=> __('Zoom-out', 'mg_ml'),
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}


// slider thumbs visibility options
function mg_galleria_thumb_opts($type = false) {
	$types = array(
		'always'	=> __('Always', 'mg_ml'),
		'yes' 		=> __('Yes with toggle button', 'mg_ml'),
		'no' 		=> __('No with toggle button', 'mg_ml'),
		'never' 	=> __('Never', 'mg_ml'),
	);
	
	if($type === false) {return $types;}
	else {return $types[$type];}
}


// lightbox bg effects
function mg_lb_bg_showing_fx() {
	$opts = array(
		'' => __("no effect", 'mg_ml'),
		'zoom-in' 	=> __("zoom-in", 'mg_ml'),
		'zoom-out' 	=> __("zoom-out", 'mg_ml'),
		'zoom-flip' => __("zoom & flip", 'mg_ml'),
		'skew' 		=> __("skew", 'mg_ml'),
		
		'symm_vert' => __("symmetrical vertical", 'mg_ml'),
		'symm_horiz' => __("symmetrical horizontal", 'mg_ml'),
		
		'genie_t_side' => __("genie | top side", 'mg_ml'),
		'genie_r_side' => __("genie | right side", 'mg_ml'),
		'genie_b_side' => __("genie | bottom side", 'mg_ml'),
		'genie_l_side' => __("genie | left side", 'mg_ml'),
		
		'slide_corn_tr' => __("slide | top-right corner", 'mg_ml'),
		'slide_corn_br' => __("slide | bottom-right corner", 'mg_ml'),
		'slide_corn_bl' => __("slide | bottom-left corner", 'mg_ml'),
		'slide_corn_tl' => __("slide | top-left corner", 'mg_ml'),
		
		'slide_t_side' => __("slide | top side", 'mg_ml'),
		'slide_r_side' => __("slide | right side", 'mg_ml'),
		'slide_b_side' => __("slide | bottom side", 'mg_ml'),
		'slide_l_side' => __("slide | left side", 'mg_ml'),
	);	
	
	return $opts;
}


// item categories array
function mg_item_cats() {
	$cats = array();
	
	foreach(get_terms( 'mg_item_categories', 'hide_empty=0') as $cat) {
		$cats[ $cat->term_id ] = $cat->name;
	}	
	return $cats;
}


// easings
function mg_easings() {
	$opts = array(
		'ease' => __("ease", 'mg_ml'),
		'linear' => __("linear", 'mg_ml'),
		'ease-in' => __("ease-in", 'mg_ml'),
		'ease-out' => __("ease-out", 'mg_ml'),
		'ease-in-out' => __("ease-in-out", 'mg_ml'),
		'ease-in-back' => __("ease-in-back", 'mg_ml'),
		'ease-out-back' => __("ease-out-back", 'mg_ml'),
		'ease-in-out-back' => __("ease-in-out-back", 'mg_ml')
	);	
	
	return $opts;
}

// litteral easing to CSS code
function mg_easing_to_css($easing) {
	switch($easing) {
		case 'ease' : $code = 'ease'; break;
		case 'linear' : $code = 'linear'; break;
		case 'ease-in' : $code = 'ease-in'; break;
		case 'ease-out' : $code = 'ease-out'; break;
		case 'ease-in-out' : $code = 'ease-in-out'; break;
		case 'ease-in-back' : $code = 'cubic-bezier(0.600, -0.280, 0.735, 0.045)'; break;
		case 'ease-out-back' : $code = 'cubic-bezier(0.175, 0.885, 0.320, 1.275)'; break;
		case 'ease-in-out-back' : $code = 'cubic-bezier(0.680, -0.550, 0.265, 1.550)'; break;
	}
	
	return $code;
}

// litteral easing to CSS code - old webkit (safari on Win)
function mg_easing_to_css_ow($easing) {
	switch($easing) {
		case 'ease' : $code = 'ease'; break;
		case 'linear' : $code = 'linear'; break;
		case 'ease-in' : $code = 'ease-in'; break;
		case 'ease-out' : $code = 'ease-out'; break;
		case 'ease-in-out' : $code = 'ease-in-out'; break;
		case 'ease-in-back' : $code = 'cubic-bezier(0.600, 0, 0.735, 0.045)'; break;
		case 'ease-out-back' : $code = 'cubic-bezier(0.175, 0.885, 0.320, 1)'; break;
		case 'ease-in-out-back' : $code = 'cubic-bezier(0.680, 0, 0.265, 1)'; break;
	}
	
	return $code;
}


// image ID to path
function mg_img_id_to_path($img_src) {
	if(is_numeric($img_src)) {
		$wp_img_data = wp_get_attachment_metadata((int)$img_src);
		if($wp_img_data) {
			$upload_dirs = wp_upload_dir();
			$img_src = $upload_dirs['basedir'] . '/' . $wp_img_data['file'];
		}
	}
	
	return $img_src;
}


// thumbnail source switch between timthumb and ewpt
function mg_thumb_src($img_id, $width = false, $height = false, $quality = 80, $alignment = 'c', $resize = 1, $canvas_col = 'FFFFFF', $fx = array()) {
	if(!$img_id) {return '';}
	
	if(get_option('mg_use_timthumb')) {
		$thumb_url = MG_TT_URL.'?src='.mg_img_id_to_path($img_id).'&w='.$width.'&h='.$height.'&a='.$alignment.'&q='.$quality.'&zc='.$resize.'&cc='.$canvas_col;
	} else {
		$thumb_url = easy_wp_thumb($img_id, $width, $height, $quality, $alignment, $resize, $canvas_col , $fx);
	}	
	
	return $thumb_url;
}
 

// image ID to full-size URL 
function mg_img_id_to_fullsize_url($img_id) {
	$src = wp_get_attachment_image_src($img_id, 'full');
	return (!is_wp_error($src) && is_array($src)) ? $src[0] : false;	
}
 
 
// know if image is a gif
function mg_img_is_gif($img_id) {
	$src = wp_get_attachment_image_src($img_id, 'full');
	return (substr(strtolower($src[0]), -4) == '.gif') ? true : false;
}
 

// get the patterns list 
function mg_patterns_list() {
	$patterns = array();
	$patterns_list = scandir(MG_DIR."/img/patterns");
	
	foreach($patterns_list as $pattern_name) {
		if($pattern_name != '.' && $pattern_name != '..') {
			$patterns[] = $pattern_name;
		}
	}
	return $patterns;	
}


// check if there is at leat one custom option
function mg_cust_opt_exists() {
	$types = mg_main_types();
	$exists = false;
	
	foreach($types as $type => $name) {
		if(get_option('mg_'.$type.'_opt') && count(get_option('mg_'.$type.'_opt')) > 0) {$exists = true; break;}	
	}
	return $exists;
}


// sizes array
function mg_sizes() {
	return array(
		'1_1',
		'1_2',
		
		'1_3',
		'2_3',
		
		'1_4',
		'3_4',
		
		'1_5',
		'2_5',
		'3_5',
		'4_5',
		
		'1_6',
		'5_6',
		
		'1_7',
		'1_8',
		'1_9',
		'1_10'
	);
}

// mobile sizes array
function mg_mobile_sizes() {
	return array(
		'1_1',
		'1_2',	
		
		'1_3',
		'2_3',
		
		'1_4',
		'3_4',
	);
}


// sizes to percents
function mg_size_to_perc($size, $leave_auto = false) {
	if($leave_auto && $size == 'auto') {return 'auto';}
	
	switch($size) {
		case '1_10': $perc = 0.1; break;
		case '1_9': $perc = 0.111; break;
		case '1_8': $perc = 0.125; break;
		case '1_7': $perc = 0.142; break;
		
		case '5_6': $perc = 0.83; break;
		case '1_6': $perc = 0.166; break;
		
		case '4_5': $perc = 0.80; break;
		case '3_5': $perc = 0.60; break;
		case '2_5': $perc = 0.40; break;
		case '1_5':
		case 'auto':$perc = 0.20; break;
		
		case '3_4': $perc = 0.75; break;
		case '1_4': $perc = 0.25; break;
		
		case '2_3': $perc = 0.666; break;
		case '1_3': $perc = 0.333; break;
		
		case '1_2': $perc = 0.50; break;
		default :	$perc = 1; break;
	}
	
	return $perc;
}


// get image sizes for inline slider || $wp_data[1] = w / $wp_data[2] = h 
function mg_inl_slider_img_sizes($wp_data, $grid_max_width, $grid_item) {
	$mobile_tres = get_option('mg_mobile_treshold', 800);
	
	// find item max width
	$nw = $grid_max_width * mg_size_to_perc($grid_item['w']);
	$mw = $mobile_tres * mg_size_to_perc($grid_item['m_w']);
	$item_max_w = max($nw, $mw);
	
	// find item max height
	$nh = $grid_max_width * mg_size_to_perc($grid_item['h']);
	$mh = $mobile_tres * mg_size_to_perc($grid_item['m_h']);
	$item_max_h = max($nh, $mh);
	
	$img_sizes = array();
	$img_sizes['w'] = ($item_max_w < $wp_data[1]) ? $item_max_w : $wp_data[1];
	$img_sizes['h'] = ($item_max_h < $wp_data[2]) ? $item_max_h : $wp_data[2];
	
	return $img_sizes; 	
}


// print type attribute fields
function mg_get_type_opt_fields($type, $post) {
	if(!get_option('mg_'.$type.'_opt')) {return false;}
	$icons = get_option('mg_'.$type.'_opt_icon');
	
	$copt = '
	<h4>'. __('Custom Attributes', 'mg_ml') .'</h4>
	<table class="widefat lcwp_table lcwp_metabox_table mg_user_opt_table">';	
	
	$a = 0;
	foreach(get_option('mg_'.$type.'_opt') as $opt) {
		$val = get_post_meta($post->ID, 'mg_'.$type.'_'.strtolower(lcwp_stringToUrl($opt)), true);
		$icon = (isset($icons[$a])) ? '<i class="mg_item_builder_opt_icon fa '.$icons[$a].'"></i> ' : '';
		
		$copt .= '
		<tr>
          <td class="lcwp_label_td">'.$icon . mg_wpml_string($type, $opt).'</td>
          <td class="lcwp_field_td">
		  	<input type="text" name="mg_'.$type.'_'.strtolower(lcwp_stringToUrl($opt)).'" value="'.mg_sanitize_input($val).'" />
          </td>     
          <td><span class="info"></span></td>
        </tr>';
		
		$a++;
	}
	
	$copt .= '</table>';
	return $copt;
}


// get type options indexes from the main type
function mg_get_type_opt_indexes($type) {
	if($type == 'simple_img' || $type == 'link') {return false;}
	
	if($type == 'single_img') {$copt_id = 'image';}
	else {$copt_id = $type;}

	if(!get_option('mg_'.$copt_id.'_opt')) {return false;}
	
	$indexes = array();
	foreach(get_option('mg_'.$copt_id.'_opt') as $opt) {
		$indexes[] = 'mg_'.$copt_id.'_'.strtolower(lcwp_stringToUrl($opt));
	}
	
	return $indexes;	
}


// prepare the array of not empty custom options for an item
function mg_item_copts_array($type, $post_id) {
	if($type == 'single_img') {$type = 'image';}
	$copts = get_option('mg_'.$type.'_opt');
	
	$arr = array();
	if(is_array($copts)) {
		foreach($copts as $copt) {
			$val = get_post_meta($post_id, 'mg_'.$type.'_'.strtolower(lcwp_stringToUrl($copt)), true);
			
			if($val && $val != '') {
				$arr[$copt] = $val;	
			}
		}
	}
	return $arr;
}


// get custom post types and taxonomies
function mg_get_cpt_with_tax($onlyfirst = false) {
	$cpt = get_post_types(array('show_ui' => true, 'publicly_queryable' => true), 'objects');
	$usable = array(); 

	foreach($cpt as $pt) {
		if(in_array($pt->name, array('attachment', 'revision', 'nav_menu_item', 'mg_items'))) {continue;} // exclude known ones
		if(!post_type_supports($pt->name, 'thumbnail')) {continue;} // exclude ones without featured image
		
		$tax = get_object_taxonomies($pt->name, 'objects');
		
		// add only if has a taxonomy
		if(is_array($tax) && !empty($tax)) {
			$tax_array = array();
			
			foreach($tax as $slug => $data) {
				if(in_array($slug, array('post_format'))) {continue;}
				$tax_array[$slug] = $data->labels->name;	
			}
			
			$usable[ $pt->name ] = array(
				'name' => $pt->labels->name,
				'tax' => $tax_array
			);		
		}
	}
	
	$to_return = array();
	
	$a = 0;
	foreach($usable as $slug => $data) {

		$b = 0;
		foreach($data['tax'] as $tax_slug => $tax_name) {
			$val = $slug.'|||'.$tax_slug;
			if($a == 0 && $b == 0) {$first_cpt_cat = $val;}
			
			$to_return[ $val ] = $data['name'].' - '.$tax_name;
			$b++;
		}
		$a++;
	}
	
	return ($onlyfirst && isset($first_cpt_cat)) ? $first_cpt_cat : $to_return;
}


// given cpt + taxonomy - get taxonomy terms in a select field
function mg_get_taxonomy_terms($cpt_tax, $return = 'array') {
	$arr = explode('|||', $cpt_tax);
	$cats = get_terms($arr[1], 'orderby=name&hide_empty=0');
	
	if($return == 'html') {
		$code = '
		<select data-placeholder="'. __('Select a term', 'mg_ml') .' .." name="mg_cpt_tax_term" class="lcweb-chosen">
			<option value="">'. __('all', 'mg_ml') .'</option>';
			
			if(is_array($cats)) {
				foreach($cats as $cat ) {
					$code .= '<option value="'.$cat->term_id.'">'.$cat->name.'</option>'; 
				}
			}
	
		return $code . '</select>'; 
	}
	else {
		$data = array('' => __('all', 'mg_ml'));
		if(is_array($cats)) {
			foreach($cats as $cat ) {
				$data[ $cat->term_id ] = $cat->name;
			}
		}
		
		return $data;	
	}
}


// save grid data - compressing if available
function mg_save_grid_data($grid_id, $arr) {
	$str = serialize($arr);
	$slug = uniqid();
	
	if(function_exists('gzcompress') && function_exists('gzuncompress')) {
		$str = base64_encode(gzcompress($str, 9));
		$slug = 'mg_gzc_' . $slug;
	}
	
	// update grid term
	return wp_update_term($grid_id, 'mg_grids', array('slug' => $slug, 'description' => $str));	
}


// get grid contents - uncompressing || returns associative array('items' => array(), 'cats' => array())
function mg_get_grid_data($grid_id) {
	
	$term = get_term_by('id', $grid_id, 'mg_grids');
	if(empty($term->description)) {return array('items' => array(), 'cats' => array());}

	// if supported - uncompress
	if(strpos($term->slug, 'mg_gzc_') !== false) {
		if(function_exists('gzcompress') && function_exists('gzuncompress')) {
			$data = gzuncompress(base64_decode($term->description));
		}
	}
	else {$data = $term->description;}
	
	return (array)unserialize($data); 
}


//////////////////////////////////////////////////////////


// get related post for Post Contents item type
function mg_post_contents_get_post($item_id) {
	$cpt_tax_arr = explode('|||', get_post_meta($item_id, 'mg_cpt_source', true));
	$term = get_post_meta($item_id, 'mg_cpt_tax_term', true); 
	
	$args = array(
		'post_type' => $cpt_tax_arr[0],  
		'post_status' => 'publish', 
		'posts_per_page' => 1,
		'offset' => (int)get_post_meta($item_id, 'mg_post_query_offset', true),
		'meta_query' => array( 
			array( 'key' => '_thumbnail_id')
		)
	);
	
	if($term) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => $cpt_tax_arr[1],
				'field' => 'id',
				'terms' => $term,
				'include_children' => true
			)
		);	
	} else {
		$args['taxonomy'] = $cpt_tax_arr[1];
	}
	
	$query = new WP_query($args);
	return (count($query->posts)) ? $query->posts[0] : false;	
}


// woocommerce integration - get product attributes
function mg_wc_prod_attr($prod_obj){
    $attributes = $prod_obj->get_attributes();
 		
	$prod_attr = array();
    if (!$attributes) {return $prod_attr;}
	
    foreach ($attributes as $attribute) {

        // skip variations
        if(isset($attribute['variation']) && $attribute['variation']) {continue;}


        if($attribute['is_taxonomy']) {
            $terms = wp_get_post_terms($prod_obj->get_id(), $attribute['name'], 'all');
 
            // get the taxonomy
            $tax = $terms[0]->taxonomy;
 
            // get the tax object
            $tax_object = get_taxonomy($tax);
 
            // get tax label
            if ( isset ($tax_object->labels->name) ) {
                $tax_label = $tax_object->labels->name;
            } elseif ( isset( $tax_object->label ) ) {
                $tax_label = $tax_object->label;
            }
 
            foreach ($terms as $term) {
            	if(isset($prod_attr[$tax_label])) {
					$prod_attr[$tax_label][] = $term->name;
				} else {
					$prod_attr[$tax_label] = array($term->name);	
				}
			}
        } else {
 			if(isset($prod_attr[ $attribute['name'] ])) {
				$prod_attr[ $attribute['name'] ][] = $attribute['value'];
			} else {
				$prod_attr[ $attribute['name'] ] = array($attribute['value']);	
			}
        }
    }

    return $prod_attr;
}


// return lightbox custom options / attributes code
function mg_lb_cust_opts_code($post_id, $type, $wc_prod = false) {
	if($type == 'single_img') {$type = 'image';}
	$code = '';
	
	if(!$wc_prod) {
		$type_opts = get_option('mg_'.$type.'_opt');
		$cust_opt = mg_item_copts_array($type, $post_id); 
		$icons = get_option('mg_'.$type.'_opt_icon');
	
		if(count($cust_opt) > 0) {
			$code .= '<ul class="mg_cust_options">';
			
			$a=0;
			foreach($type_opts as $opt) {
				if(isset($cust_opt[$opt])) {				
					$icon = (isset($icons[$a]) && !empty($icons[$a])) ? '<i class="mg_cust_opt_icon fa '.$icons[$a].'"></i> ' : '';
					$code .= '<li>'.$icon.'<span>'.mg_wpml_string($type, $opt).'</span> '.do_shortcode(str_replace(array('&lt;', '&gt;'), array('<', '>'), $cust_opt[$opt])).'</li>';
				}
				$a++;
			}
			
			$code .= '</ul>';
		}
	}
	
	// woocomm attributes
	else {
		$prod_attr = mg_wc_prod_attr($wc_prod);
		if(is_array($prod_attr) && count($prod_attr) > 0 && !get_option('mg_wc_hide_attr')) {
			$code .= '<ul class="mg_cust_options">';
					
			foreach($prod_attr as $attr => $val) {					
				$icon = get_option('mg_wc_attr_'.sanitize_title($attr).'_icon');
				$icon_code = (!empty($icon)) ? '<i class="mg_cust_opt_icon fa '.$icon.'"></i> ' : '';
				
				$code .= '<li>'.$icon_code.'<span>'.$attr.'</span> '.do_shortcode(implode(', ', $val)).'</li>';
			}
					
			// add rating if allowed and there's any
			if(get_post_field('comment_status', $post_id) != 'closed' && $wc_prod->get_rating_count() > 0) {
				$rating = round((float)$wc_prod->get_average_rating());
				$empty_stars = 5 - $rating;
			
				$code .= '<li class="mg_wc_rating"><span>'. __('Rating', 'mg_ml') .'</span>';
				for($a=0; $a < $rating; $a++) 		{$code .= '<i class="fa fa-star"></i>';}
				for($a=0; $a < $empty_stars; $a++) 	{$code .= '<i class="fa fa-star-o"></i>';}
				$code .= '</li>';
			}
			
			$code .= '</ul>';
		}
	}
	
	return $code;
}


// giving an array of items categories, return the published items
function mg_get_cat_items($cat) {
	if(!$cat) {return false;}
	
	// post types
	$post_types = array('mg_items');
	if(mg_woocomm_active()) {$post_types[] = 'product';}
	
	// meta query to allow selective WC products
	$not_in = array('wc_no');
	if(!mg_woocomm_active() || !get_option('mg_integrate_wc')) {$not_in = array_merge($not_in, array(false, 'wc_auto'));}
	
	$args = array(
		'post_type' => $post_types,
		'post_status' => 'publish', 
		'posts_per_page' => 3000, 
		'orderby' => 'title',
		'order' => 'ASC',
		'meta_query' => array( 
			array(
				'key' => 'mg_main_type',
				'value' => $not_in,
				'compare' => 'NOT IN'
			)
		)
	);
	
	// filter by cat
	if($cat != 'all') {
		$term_data = get_term_by( 'id', $cat, 'mg_item_categories');	
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'mg_item_categories', 
				'field' => 'slug', //(string) - Select taxonomy term by ('id' or 'slug')
				'terms' => $term_data->slug,
				'operator' => 'IN'
			)
		);   	
	}	
	
	$query = new WP_Query($args);
	$items = $query->posts;

	
	$items_list = array();
	foreach($items as $item) {
		$post_id = $item->ID;
		$img_id = get_post_thumbnail_id($post_id);
		$type = ($item->post_type  == 'product') ? 'woocom' : get_post_meta($post_id, 'mg_main_type', true);
		
		// show only items with featured image
		if(!empty($img_id) || in_array($type, array('spacer', 'inl_slider', 'inl_video', 'post_contents', 'inl_text'))) {
			$items_list[] = array(
				'id'	=> $post_id, 
				'title'	=> $item->post_title, 
				'type' 	=> $type,
				'img' => $img_id
			);
		}
	}
	return $items_list;
}


// given an array of post_id, retrieve the data for the builder
function mg_grid_builder_items_data($items) {
	if(!is_array($items) || count($items) == 0) {return false;}
	
	$items_data = array();
	foreach($items as $item_id) {	
		$items_data[] = array(
			'id'	=> $item_id, 
			'title'	=> get_the_title($item_id), 
			'type' 	=> get_post_meta($item_id, 'mg_main_type', true)
		);
	}
	
	return $items_data;
}


// paginator builder structure
function mg_paginator_item() {
	$code = '
	<li mg-m-height="1_4" mg-m-width="1_1" mg-height="1_10" mg-width="1_1" class="mg_box mg_paginator_type col1_1 row1_10">
	  <input type="hidden" value="paginator" name="grid_items[]" />
	  
	  <input type="hidden" value="0" name="items_w[]" class="select_w" />
	  <input type="hidden" value="0" name="items_h[]" class="select_h" />
	  <input type="hidden" value="0" name="items_mobile_w[]" class="select_m_w" />
	  <input type="hidden" value="0" name="items_mobile_h[]" class="select_m_h" />
	  
	  <div class="handler">
		  <div title="'. __('remove pagination', 'mg_ml') .'" class="del_item"></div>
		  <h3>
			  <img src="'.MG_URL. '/img/type_icons/paginator.png" height="19" width="19" class="thumb" alt="" />
			  '. __('Pagination block', 'mg_ml') .'
		  </h3>
	  </div>
	</li>
	';
	
	return str_replace(array("\r", "\n", "\t", "\v"), '', $code);; // remove space for JS usage
}


// get the images from the WP library
function mg_library_images($page = 1, $per_page = 15, $search = '') {
	$query_images_args = array(
		'post_type' => 'attachment', 
		'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
		'post_status' => 'inherit', 
		'posts_per_page' => $per_page, 
		'paged' => $page
	);
	if(isset($search) && !empty($search)) {
		$query_images_args['s'] = $search;	
	}
	
	$query_images = new WP_Query( $query_images_args );
	$images = array();
	
	foreach ( $query_images->posts as $image) { 
		$images[] = $image->ID;		
	}
	
	// global images number
	$img_num = $query_images->found_posts;
	
	// calculate the total
	$tot_pag = ceil($img_num / $per_page);
	
	// can show more?
	$shown = $per_page * $page;
	($shown >= $img_num) ? $more = false : $more = true; 
	
	return array(
		'img'		=> $images, 
		'pag' 		=> $page, 
		'tot_pag' 	=>$tot_pag, 
		'more' 		=> $more, 
		'tot' 		=> $img_num
	);
}


// get the audio files from the WP library
function mg_library_audio($page = 1, $per_page = 15, $search = '') {
	$query_audio_args = array(
		'post_type' => 'attachment', 
		'post_mime_type' =>'audio', 
		'post_status' => 'inherit', 
		'posts_per_page' => $per_page, 
		'paged' => $page
	);
	if(isset($search) && !empty($search)) {
		$query_audio_args['s'] = $search;	
	}
	
	$query_audio = new WP_Query( $query_audio_args );
	$tracks = array();
	
	foreach ( $query_audio->posts as $audio) { 
		$tracks[] = array(
			'id'	=> $audio->ID,
			'url' 	=> $audio->guid, 
			'title' => $audio->post_title
		);
	}
	
	// global images number
	$track_num = $query_audio->found_posts;
	
	// calculate the total
	$tot_pag = ceil($track_num / $per_page);
	
	// can show more?
	$shown = $per_page * $page;
	($shown >= $track_num) ? $more = false : $more = true; 
	
	return array('tracks' => $tracks, 'pag' => $page, 'tot_pag' =>$tot_pag  ,'more' => $more, 'tot' => $track_num);
}


// given an array of selected images or tracks - returns only existing ones
function mg_existing_sel($media, $rel_videos = false) {
	if(is_array($media)) {
		$new_array = array();
		$a = 0;
		
		foreach($media as $media_id) {
			if(is_object( get_post($media_id) )) {
				if($rel_videos === false) {
					$new_array[] = $media_id;
				} else {
					$vid = (isset($rel_videos[$a])) ? $rel_videos[$a] : '';
					$new_array[] = array('img' => $media_id, 'video' => $vid);
				}
			}
			$a++;
		}
		
		if(count($new_array) == 0) {return false;}
		else {return $new_array;}
	}
	else {return false;}	
}


// create selected slider image list - starts from array of associative array
function mg_sel_slider_img_list($data) {
	if(!is_array($data)) {return '<p>'. __('No images selected', 'mg_ml') .' .. </p>';}
	$code = '';
	
	foreach($data as $elem) {
		
		if($elem['video']) {
			$span_title = __('Edit video URL', 'mg_ml');
			$span_class = 'mg_slider_video_on'; 	
		} else {
			$span_title = __('set as video slide', 'mg_ml');
			$span_class = 'mg_slider_video_off'; 		
		}
		
		$thumb_data = wp_get_attachment_image_src($elem['img'], array(90, 90));
		
		$code .= '
		<li>
			<input type="hidden" name="mg_slider_img[]" class="mg_slider_img_field" value="'. $elem['img'] .'" />
			<input type="hidden" name="mg_slider_vid[]" class="mg_slider_video_field" value="'. $elem['video'] .'" autocomplete="off" />
			
			<figure style="background-image: url('. $thumb_data[0] .');"></figure>
			<span title="remove image"></span>
			<i title="'.$span_title.'" class="'.$span_class.'"></i>
		</li>';	
	}
	
	return $code;
}


// update grid terms once the item is updated
function mg_upd_item_upd_grids($item_id) {
	$grids = get_terms('mg_grids', 'hide_empty=0');
	if(!is_array($grids)) {return false;}
	
	foreach($grids as $grid) {
		$grid_data = mg_get_grid_data($grid->term_id);
		if(!is_array($grid_data['items'])) {continue;}
		
		// check if item is part of the grid
		$exists = false;
		foreach($grid_data['items'] as $item) {
			if($item_id == $item['id']) {
				$exists = true;
				break;	
			}
		}
		
		// if the item is in the grid
		if($exists) {
			// save the terms list for the posts
			$terms_array = array();
			foreach($grid_data['items'] as $item) {
				$pid_terms = wp_get_post_terms($item['id'], 'mg_item_categories', array("fields" => "ids"));
				foreach($pid_terms as $pid_term) { $terms_array[] = $pid_term; }	
			}
			
			mg_save_grid_data($grid->term_id, array('items'=> $grid_data['items'], 'cats'=> array_unique($terms_array)) );
		}
	}
}


// return the grid categories by the chosen order
function mg_order_grid_cats($terms) {
	$ordered = array();
	
	// better system -> supported from PHP v5.4
	if( (float)substr(PHP_VERSION, 0, 3) >= 5.4) {
		asort($terms, SORT_NUMERIC); // prior sort by ID
		
		// sort by order, using term_id to take advantage of natural sorting
		foreach($terms as $term_id) {
			$ord = (int)get_option("mg_cat_".$term_id."_order");	
			$ordered[ ($ord .'-'. $term_id) ] = $term_id;
		}
	
		ksort($ordered, SORT_NATURAL);
		return $ordered;	
	}
	
	// old system
	else {
		foreach($terms as $term_id) {
			$ord = (int)get_option("mg_cat_".$term_id."_order");
			
			// check the final order
			while( isset($ordered[$ord]) ) {
				$ord++;	
			}
			
			$ordered[$ord] = $term_id;
		}
		
		ksort($ordered, SORT_NUMERIC);
		return $ordered;		
	}
}


// get the grid terms data  - create filters structure
function mg_grid_terms_data($grid_id, $terms, $return = 'html', $filters_align = 'top', $selected = false, $hide_all = false) {
	
	if(!$terms || !is_array($terms)) {return false;}
	else {
		$terms = mg_order_grid_cats($terms);
		$all_txt = get_option('mg_all_filter_txt', __('All', 'mg_ml'));
		
		// check for deeplinked selection
		if(isset($GLOBALS['mg_deeplinks']) && isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]) && isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgc'])) {
			$deeplinked_sel = $GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgc'];
		}
	
		$terms_data = array();
		$true_sel = $selected;
	
		$a = 0;
		foreach($terms as $term) {
			// check deeplinked selection
			if(isset($deeplinked_sel) && $term == $deeplinked_sel) {
				$true_sel = $term;	
			}
			
			// WPML - check translation
			if(function_exists('icl_object_id')) {
				$term = icl_object_id($term, 'mg_item_categories', true);	
			}
			
			$term_data = get_term_by('id', $term, 'mg_item_categories');
			if(is_object($term_data)) {
				$terms_data[$a] = array('id' => $term, 'name' => $term_data->name, 'slug' => $term_data->slug); 		
				$a++;
			}
		}
		
		// override if is deeplinking "all"
		if(isset($deeplinked_sel) && $deeplinked_sel == '*') {
			$true_sel = '*';	
		}
		
	
		if($return == 'array') {return $terms_data;}
		elseif($return == 'dropdown') {
			$code = '<select class="mg_mobile_filter_dd" autocomplete="off">';
			
			if(!$hide_all) {
				$code .= '<option value="*">'. $all_txt .'</option>';	
			}
			
			foreach($terms_data as $term) {
				$sel = ($true_sel == $term['id']) ? 'selected="selected"' : '';
				$code .= '<option value="'.$term['id'].'" '.$sel.'>'.$term['name'].'</option>';	
			}
				
			return $code . '</select>';	
		}
		else {
			$all_sel = (!$true_sel || $true_sel == '*') ? 'mg_cats_selected' : '';
			$def_sel = (!$selected || $selected == '*') ? 'mg_def_filter' : '';
			$grid_terms_list = (!$hide_all) ? '<a class="'. $all_sel .' '. $def_sel .' mgf_all mgf" rel="*" href="javascript:void(0)">'. $all_txt .'</a>' : '';
			
			if($filters_align == 'right' || $filters_align == 'left') {$separator = '<br/>';}
			else {$separator = (get_option('mg_use_old_filters')) ? '<span>/</span>' : '';}

			$a = 0;
			foreach($terms_data as $term) {
				$true_sep = ($a == 0 && $hide_all) ? '' : $separator; 
				
				$sel 		= ($true_sel == $term['id']) ? 'mg_cats_selected' : '';
				$def_sel 	= ($selected == $term['id']) ? 'mg_def_filter' : '';
				
				// icon code
				$icon = get_option("mg_cat_".$term['id']."_icon");
				if(!empty($icon)) {
					$icon_code = '<i class="mg_cat_icon fa '.$icon.'"></i>';	
				} 
				else {$icon_code = '';}
				
				// icon position
				if($filters_align == 'right') {
					$pre_icon = '';
					$after_icon = $icon_code;	
				} else {
					$pre_icon = $icon_code;
					$after_icon = '';	
				}
				
				$grid_terms_list .= $true_sep.'<a rel="'.$term['id'].'" class="mgf_id_'.$term['id'].' '.$sel.' '.$def_sel.' mgf" href="javascript:void(0)">'.$pre_icon.$term['name'].$after_icon.'</a>';
				
				$a++;	
			}
			return $grid_terms_list;
		}
	}
}


// get the terms of a grid item - return the CSS class
function mg_item_term_classes($post_id) {
	$pid_classes = array();
	
	$pid_terms = wp_get_post_terms($post_id, 'mg_item_categories', array("fields" => "ids"));
	foreach($pid_terms as $pid_term) { $pid_classes[] = 'mgc_'.$pid_term; }	
	
	return implode(' ', $pid_classes);	
}


// find if grid has got pages
function mg_grid_has_pag($items) {
	$has_pag = false;
	
	foreach($items as $item) {
		if($item['id'] == 'paginator') {
			$has_pag = true;
			break;	
		}
	}
	
	return $has_pag;	
}


// create the frontend css and js
function mg_create_frontend_css() {	
	ob_start();
	require(MG_DIR.'/frontend_css.php');
	
	$css = ob_get_clean();
	if(trim($css) != '') {
		if(!@file_put_contents(MG_DIR.'/css/custom.css', $css, LOCK_EX)) {$error = true;}
	}
	else {
		if(file_exists(MG_DIR.'/css/custom.css'))	{ unlink(MG_DIR.'/css/custom.css'); }
	}
	
	if(isset($error)) {return false;}
	else {return true;}
}


// custom excerpt
function mg_excerpt($string, $max) {
	$num = strlen($string);
	
	if($num > $max) {
		$string = substr($string, 0, $max) . '..';
	}
	
	return $string;
}


// font-awesome icon picker - hidden lightbox code
function mg_fa_icon_picker_code($no_icon_text) {
	include_once(MG_DIR . '/classes/lc_font_awesome_helper.php');
	$fa = new lc_fontawesome_helper;
	
	$code = '
	<div id="mg_icons_list" style="display: none;">
		<div class="mg_lb_icons_wizard">
			<p rel="" class="mgtoi_no_icon"><a>'. $no_icon_text .'</a></p>';
		
			foreach($fa->sorted_icons as $cat => $icons) {
				$code .= '<h4>'. $cat .'</h4>';
				
				foreach($icons as $iid => $unicode) {
					$idata = $fa->icons[$iid];
					$code .= '<i rel="'.$idata->class.'" class="fa '.$idata->class.'" title="'.$idata->name.'"></i>';
				}
			}
	
	return $code .'
		</div>
	</div>';
}


// font-awesome icon picker - javascript code - direct print
function mg_fa_icon_picker_js() {
	?>
    var $sel_type_opt = false;
	var sel_type_icon = '';
	
	jQuery('body').delegate('.mg_type_opt_icon_trigger i', "click", function() {
		$sel_type_opt = jQuery(this).parent();
		sel_type_icon = jQuery(this).parents('.mg_type_opt_icon_trigger').find('input').val(); 
		
		tb_show('<?php _e('Choose an icon', 'mg_ml') ?>' , '#TB_inline?inlineId=mg_icons_list');
		setTimeout(function() {
			jQuery('#TB_ajaxContent').css('width', 'auto');
			jQuery('#TB_ajaxContent').css('height', (jQuery('#TB_window').height() - 47) );
			
			jQuery('.mg_lb_icons_wizard i').removeClass('mg_lb_sel_icon');	
			if(sel_type_icon) {
				jQuery('.mg_lb_icons_wizard .'+sel_type_icon).addClass('mg_lb_sel_icon');	
			}
		}, 50);
	});
	jQuery(window).resize(function() {
		if( jQuery('#TB_ajaxContent .mg_lb_icons_wizard').size() > 0 ) {
			jQuery('#TB_ajaxContent').css('height', (jQuery('#TB_window').height() - 47) );	
		}
	});
	
	
	// select icon
	jQuery('body').delegate('#TB_ajaxContent .mg_lb_icons_wizard > p, #TB_ajaxContent .mg_lb_icons_wizard > i', "click", function() {
		var val = jQuery(this).attr('rel');
		
		$sel_type_opt.find('input').val(val);
		$sel_type_opt.find('i').attr('class', 'fa '+val);
		
		tb_remove();
		$sel_type_opt = false;
	});
    <?php
}


// item deeplink URL - for grids
function mg_item_deeplinked_url($item_id, $item_title) {
	$base_url = get_option('mg_sitemap_baseurl', get_site_url());
	$txt = (empty($item_title)) ? '' : '/'. sanitize_title($item_title);
	
	if(strpos($base_url, '?') === false) {
		return $base_url .'?mgi_='.$item_id.$txt;	
	}  else {
		return $base_url .'&mgi_='.$item_id.$txt;		
	}
}


// lightbox image optimizer - serve best wordpress-managed image depending on featured space sizes
function mg_lb_image_optimizer($img_id, $layout, $lb_max_w, $img_display_mode = 'mg_lb_img_fill_w', $img_max_h = false, $feat_match_txt = false) {
	
	// calculate image's max width
	if(strpos($layout, 'tripartite')) {
		$img_max_w = ceil($lb_max_w * 0.65);	
	}
	elseif(strpos($layout, 'bipartite') !== false) {
		$img_max_w = ceil($lb_max_w / 2);		
	}
	else {
		$img_max_w = $lb_max_w;		
	}
	
	
	// max-height and not fill nor match -> use LC resizing system 
	if($img_max_h && $img_display_mode == 'mg_lb_img_auto_w' && !$feat_match_txt) {
		$canvas_color = substr(get_option('mg_item_bg_color', '#ffffff'), 1);
		return	mg_thumb_src($img_id, $img_max_w, $img_max_h, $quality = 95, $thumb_center = 'c', $resize = 3, $canvas_color);
	}
	
	else {
		$src = wp_get_attachment_image_src($img_id, array($lb_max_w, 0));	
		return $src[0];	
	}
}


// lightbox navigation code
function mg_lb_nav_code($prev_next = array('prev' => 0, 'next' => 0), $layout = 'inside') {
	if((!$prev_next['prev'] && !$prev_next['next']) || $layout == 'hidden') {return '';}
	
	// thumb sizes for layout
	switch($layout) {
		case 'inside' 	: $ts = array('w'=>60, 'h'=>60); break;	
		case 'top' 		: $ts = array('w'=>150, 'h'=>150); break;
		case 'side' 	: $ts = array('w'=>340, 'h'=>120); break;
	}
	
	$code = '';
	foreach($prev_next as $dir => $item_id) {
		$active = (!empty($item_id)) ? 'mg_nav_active' : '';
		$side_class = ($layout == 'side') ? 'mg_side_nav' : '';
		$side_vis = ($layout == 'side') ? 'style="display: none;"' : '';
		$thumb_center = (get_post_meta($item_id, 'mg_thumb_center', true)) ? get_post_meta($item_id, 'mg_thumb_center', true) : 'c';
		
		$code .= '
		<div class="mg_lb_nav_'.$layout.' mg_nav_'.$dir.' mg_'.$layout.'_nav_'.$dir.' '.$active.' '.$side_class.'" rel="'.$item_id.'" '.$side_vis.'>
			<i></i>';
			
			if($layout == 'side') {
				$code .= '<span></span>';	
			}
			
			if(!empty($item_id)) {
				$title = get_the_title($item_id);
				
				if($layout == 'inside') {
					$code .= '<div><span>'.$title.'</span></div>';
				}
				elseif($layout == 'top') {
					$thumb = mg_thumb_src(get_post_thumbnail_id($item_id), $ts['w'], $ts['h'], 80, $thumb_center);
					$code .= '<div>'.$title.'<img src="'.$thumb.'" alt="'.mg_sanitize_input($title).'" /></div>';
				}
				elseif($layout == 'side') {
					$thumb = mg_thumb_src(get_post_thumbnail_id($item_id), $ts['w'], $ts['h'], 70, $thumb_center);
					$code .= '<div>'.$title.'</div><img src="'.$thumb.'" alt="'.mg_sanitize_input($title).'" />';
				}
			}
			
		$code .= '</div>';
	}	
	return $code;
}


// get the upload directory (for WP MU)
function mg_wpmu_upload_dir() {
	$dirs = wp_upload_dir();
	$basedir = $dirs['basedir'] . '/YEAR/MONTH';
	 
	 
	return $basedir;	
}

///////////////////////////////////////////////////////////////////


// predefined grid styles 
function mg_predefined_styles($style = '') {
	$styles = array(
		/*** LIGHTS ***/
		'Light - Standard' => array(
			'mg_cells_margin' => 5,
			'mg_cells_img_border' => 4,
			'mg_cells_radius' => 1,
			'mg_cells_border' => 0,
			'mg_cells_shadow' => 1,
			'mg_item_radius' => 4,
			'mg_lb_border_w' => 3,
			'mg_item_radius' => 3, 
			
			'mg_loader_color' => '#888888',
			'mg_cells_border_color' => '#CCCCCC',
			'mg_img_border_color' => '#ffffff',
			'mg_img_border_opacity' => 100,
			'mg_main_overlay_color' => '#FFFFFF',
			'mg_main_overlay_opacity' => 80,
			'mg_second_overlay_color' => '#555555',
			'mg_icons_col' => '#ffffff',
			'mg_overlay_title_color' => '#222222',
			'mg_txt_under_color' => '#333333',
			
			'mg_item_overlay_color' => '#FFFFFF',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#FFFFFF',
			'mg_item_border_color' => '#e2e2e2',
			'mg_item_txt_color' => '#222222',
			'mg_item_icons' => '#555555',
			'mg_item_icons_color' => '#777777',
			
			'mg_filters_txt_color' => '#666666', 
			'mg_filters_bg_color' => '#ffffff',
			'mg_filters_border_color' => '#bbbbbb', 
			'mg_filters_txt_color_h' => '#535353', 
			'mg_filters_bg_color_h' => '#fdfdfd', 
			'mg_filters_border_color_h' => '#777777',
			'mg_filters_txt_color_sel' => '#333333', 
			'mg_filters_bg_color_sel' => '#e5e5e5', 
			'mg_filters_border_color_sel' => '#aaaaaa',
			
			'preview' => 'light_standard.jpg'
		),
	
		'Light - Minimal' => array(
			'mg_cells_margin' => 5,
			'mg_cells_img_border' => 3,
			'mg_cells_radius' => 2,
			'mg_cells_border' => 1,
			'mg_cells_shadow' => 0,
			'mg_item_radius' => 2,
			'mg_lb_border_w' => 0,
			'mg_item_radius' => 0,
			
			'mg_loader_color' => '#888888',
			'mg_cells_border_color' => '#CECECE',
			'mg_img_border_color' => '#ffffff',
			'mg_img_border_opacity' => 0,
			'mg_main_overlay_color' => '#FFFFFF',
			'mg_main_overlay_opacity' => 80,
			'mg_second_overlay_color' => '#555555',
			'mg_icons_col' => '#ffffff',
			'mg_overlay_title_color' => '#222222',
			'mg_txt_under_color' => '#333333',
			
			'mg_item_overlay_color' => '#FFFFFF',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#FFFFFF',
			'mg_item_border_color' => '#e2e2e2',
			'mg_item_txt_color' => '#222222',
			'mg_item_icons' => '#444444',
			'mg_item_icons_color' => '#666666',
			
			'mg_filters_txt_color' => '#666666', 
			'mg_filters_bg_color' => '#ffffff',
			'mg_filters_border_color' => '#bbbbbb', 
			'mg_filters_txt_color_h' => '#535353', 
			'mg_filters_bg_color_h' => '#fdfdfd', 
			'mg_filters_border_color_h' => '#777777',
			'mg_filters_txt_color_sel' => '#333333', 
			'mg_filters_bg_color_sel' => '#e5e5e5', 
			'mg_filters_border_color_sel' => '#aaaaaa',
			
			'preview' => 'light_minimal.jpg'
		),
		
		'Light - No Border' => array(
			'mg_cells_margin' => 5,
			'mg_cells_img_border' => 0,
			'mg_cells_radius' => 2,
			'mg_cells_border' => 0,
			'mg_cells_shadow' => 1,
			'mg_item_radius' => 4,
			'mg_lb_border_w' => 0,
			'mg_item_radius' => 2,
			
			'mg_loader_color' => '#888888',
			'mg_cells_border_color' => '#CCCCCC',
			'mg_img_border_color' => '#ffffff',
			'mg_img_border_opacity' => 0,
			'mg_main_overlay_color' => '#FFFFFF',
			'mg_main_overlay_opacity' => 80,
			'mg_second_overlay_color' => '#555555',
			'mg_icons_col' => '#efefef',
			'mg_overlay_title_color' => '#222222',
			'mg_txt_under_color' => '#333333',
			
			'mg_item_overlay_color' => '#FFFFFF',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#FFFFFF',
			'mg_item_border_color' => '#e2e2e2',
			'mg_item_txt_color' => '#222222',
			'mg_item_icons' => '#555555',
			'mg_item_icons_color' => '#666666',
			
			'mg_filters_txt_color' => '#666666', 
			'mg_filters_bg_color' => '#ffffff',
			'mg_filters_border_color' => '#bbbbbb', 
			'mg_filters_txt_color_h' => '#535353', 
			'mg_filters_bg_color_h' => '#fdfdfd', 
			'mg_filters_border_color_h' => '#777777',
			'mg_filters_txt_color_sel' => '#333333', 
			'mg_filters_bg_color_sel' => '#e5e5e5', 
			'mg_filters_border_color_sel' => '#aaaaaa',
			
			'preview' => 'light_noborder.jpg'
		),
		
		'Light - Photo Wall' => array(
			'mg_cells_margin' => 0,
			'mg_cells_img_border' => 0,
			'mg_cells_radius' => 0,
			'mg_cells_border' => 0,
			'mg_cells_shadow' => 1,
			'mg_item_radius' => 4,
			'mg_lb_border_w' => 3,
			'mg_item_radius' => 0,
			
			'mg_loader_color' => '#888888',
			'mg_cells_border_color' => '#CCCCCC',
			'mg_img_border_color' => '#ffffff',
			'mg_img_border_opacity' => 0,
			'mg_main_overlay_color' => '#FFFFFF',
			'mg_main_overlay_opacity' => 80,
			'mg_second_overlay_color' => '#555555',
			'mg_icons_col' => '#efefef',
			'mg_overlay_title_color' => '#222222',
			'mg_txt_under_color' => '#333333',
			
			'mg_item_overlay_color' => '#FFFFFF',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#FFFFFF',
			'mg_item_border_color' => '#e2e2e2',
			'mg_item_txt_color' => '#222222',
			'mg_item_icons' => '#444444',
			'mg_item_icons_color' => '#777777',
			
			'mg_filters_txt_color' => '#666666', 
			'mg_filters_bg_color' => '#ffffff',
			'mg_filters_border_color' => '#bbbbbb', 
			'mg_filters_txt_color_h' => '#535353', 
			'mg_filters_bg_color_h' => '#fdfdfd', 
			'mg_filters_border_color_h' => '#777777',
			'mg_filters_txt_color_sel' => '#333333', 
			'mg_filters_bg_color_sel' => '#e5e5e5', 
			'mg_filters_border_color_sel' => '#aaaaaa',
			
			'preview' => 'light_photowall.jpg'
		),
		
		'Light - Title Under Items' => array(
			'mg_cells_margin' => 5,
			'mg_cells_img_border' => 3,
			'mg_cells_radius' => 2,
			'mg_cells_border' => 1,
			'mg_cells_shadow' => 0,
			'mg_item_radius' => 4,
			'mg_lb_border_w' => 3,
			'mg_item_radius' => 2,
			
			'mg_loader_color' => '#888888',
			'mg_cells_border_color' => '#CCCCCC',
			'mg_img_border_color' => '#ffffff',
			'mg_img_border_opacity' => 100,
			'mg_main_overlay_color' => '#dddddd',
			'mg_main_overlay_opacity' => 0,
			'mg_second_overlay_color' => '#ffffff',
			'mg_icons_col' => '#777777',
			'mg_overlay_title_color' => '#222222',
			'mg_txt_under_color' => '#333333',
			
			'mg_item_overlay_color' => '#FFFFFF',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#FFFFFF',
			'mg_item_border_color' => '#e2e2e2',
			'mg_item_txt_color' => '#222222',
			'mg_item_icons' => '#555555',
			'mg_item_icons_color' => '#777777',
			
			'mg_filters_txt_color' => '#666666', 
			'mg_filters_bg_color' => '#ffffff',
			'mg_filters_border_color' => '#bbbbbb', 
			'mg_filters_txt_color_h' => '#535353', 
			'mg_filters_bg_color_h' => '#fdfdfd', 
			'mg_filters_border_color_h' => '#777777',
			'mg_filters_txt_color_sel' => '#333333', 
			'mg_filters_bg_color_sel' => '#e5e5e5', 
			'mg_filters_border_color_sel' => '#aaaaaa',
			
			'preview' => 'light_tit_under.jpg'
		),
	
		/*** DARKS ***/
		'Dark - Standard' => array(
			'mg_cells_margin' => 5,
			'mg_cells_img_border' => 4,
			'mg_cells_radius' => 1,
			'mg_cells_border' => 0,
			'mg_cells_shadow' => 1,
			'mg_item_radius' => 4,
			'mg_lb_border_w' => 3,
			'mg_item_radius' => 3, 
			
			'mg_loader_color' => '#ffffff',
			'mg_cells_border_color' => '#999999',
			'mg_img_border_color' => '#373737',
			'mg_img_border_opacity' => 80,
			'mg_main_overlay_color' => '#222222',
			'mg_main_overlay_opacity' => 90,
			'mg_second_overlay_color' => '#bbbbbb',
			'mg_icons_col' => '#ffffff',
			'mg_overlay_title_color' => '#ffffff',
			'mg_txt_under_color' => '#ffffff',
			
			'mg_item_overlay_color' => '#222222',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#343434',
			'mg_item_border_color' => '#5f5f5f',
			'mg_item_txt_color' => '#ffffff',
			'mg_item_icons' => '#eeeeee',
			'mg_item_icons_color' => '#eeeeee',
			
			'mg_filters_txt_color' => '#efefef', 
			'mg_filters_bg_color' => '#6a6a6a',
			'mg_filters_border_color' => '#666666', 
			'mg_filters_txt_color_h' => '#ffffff', 
			'mg_filters_bg_color_h' => '#5f5f5f', 
			'mg_filters_border_color_h' => '#444444',
			'mg_filters_txt_color_sel' => '#ffffff', 
			'mg_filters_bg_color_sel' => '#4f4f4f', 
			'mg_filters_border_color_sel' => '#424242',
			
			'preview' => 'dark_standard.jpg'
		),
	
		'Dark - Minimal' => array(
			'mg_cells_margin' => 5,
			'mg_cells_img_border' => 4,
			'mg_cells_radius' => 2,
			'mg_cells_border' => 1,
			'mg_cells_shadow' => 0,
			'mg_item_radius' => 2,
			'mg_lb_border_w' => 0,
			'mg_item_radius' => 0,
			
			'mg_loader_color' => '#ffffff',
			'mg_cells_border_color' => '#555555',
			'mg_img_border_color' => '#373737',
			'mg_img_border_opacity' => 0,
			'mg_main_overlay_color' => '#222222',
			'mg_main_overlay_opacity' => 90,
			'mg_second_overlay_color' => '#bbbbbb',
			'mg_icons_col' => '#ffffff',
			'mg_overlay_title_color' => '#ffffff',
			'mg_txt_under_color' => '#ffffff',
			
			'mg_item_overlay_color' => '#222222',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#343434',
			'mg_item_border_color' => '#5f5f5f',
			'mg_item_txt_color' => '#ffffff',
			'mg_item_icons' => '#ffffff',
			'mg_item_icons_color' => '#ffffff',
			
			'mg_filters_txt_color' => '#efefef', 
			'mg_filters_bg_color' => '#6a6a6a',
			'mg_filters_border_color' => '#666666', 
			'mg_filters_txt_color_h' => '#ffffff', 
			'mg_filters_bg_color_h' => '#5f5f5f', 
			'mg_filters_border_color_h' => '#444444',
			'mg_filters_txt_color_sel' => '#ffffff', 
			'mg_filters_bg_color_sel' => '#4f4f4f', 
			'mg_filters_border_color_sel' => '#424242',
			
			'preview' => 'dark_minimal.jpg'
		),
		
		'Dark - No Border' => array(
			'mg_cells_margin' => 5,
			'mg_cells_img_border' => 0,
			'mg_cells_radius' => 2,
			'mg_cells_border' => 0,
			'mg_cells_shadow' => 1,
			'mg_item_radius' => 4,
			'mg_lb_border_w' => 0,
			'mg_item_radius' => 2,
			
			'mg_loader_color' => '#ffffff',
			'mg_cells_border_color' => '#999999',
			'mg_img_border_color' => '#373737',
			'mg_img_border_opacity' => 80,
			'mg_main_overlay_color' => '#222222',
			'mg_main_overlay_opacity' => 90,
			'mg_second_overlay_color' => '#bbbbbb',
			'mg_icons_col' => '#555555',
			'mg_overlay_title_color' => '#ffffff',
			'mg_txt_under_color' => '#ffffff',
			
			'mg_item_overlay_color' => '#222222',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#343434',
			'mg_item_border_color' => '#5f5f5f',
			'mg_item_txt_color' => '#ffffff',
			'mg_item_icons' => '#f4f4f4',
			'mg_item_icons_color' => '#eeeeee',
			
			'mg_filters_txt_color' => '#efefef', 
			'mg_filters_bg_color' => '#6a6a6a',
			'mg_filters_border_color' => '#666666', 
			'mg_filters_txt_color_h' => '#ffffff', 
			'mg_filters_bg_color_h' => '#5f5f5f', 
			'mg_filters_border_color_h' => '#444444',
			'mg_filters_txt_color_sel' => '#ffffff', 
			'mg_filters_bg_color_sel' => '#4f4f4f', 
			'mg_filters_border_color_sel' => '#424242',
			
			'preview' => 'dark_noborder.jpg'
		),
		
		'Dark - Photo Wall' => array(
			'mg_cells_margin' => 0,
			'mg_cells_img_border' => 0,
			'mg_cells_radius' => 0,
			'mg_cells_border' => 0,
			'mg_cells_shadow' => 1,
			'mg_item_radius' => 4,
			'mg_lb_border_w' => 3,
			'mg_item_radius' => 0,
			
			'mg_loader_color' => '#ffffff',
			'mg_cells_border_color' => '#999999',
			'mg_img_border_color' => '#373737',
			'mg_img_border_opacity' => 80,
			'mg_main_overlay_color' => '#222222',
			'mg_main_overlay_opacity' => 90,
			'mg_second_overlay_color' => '#bbbbbb',
			'mg_icons_col' => '#555555',
			'mg_overlay_title_color' => '#ffffff',
			'mg_txt_under_color' => '#ffffff',
			
			'mg_item_overlay_color' => '#222222',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#343434',
			'mg_item_border_color' => '#5f5f5f',
			'mg_item_txt_color' => '#ffffff',
			'mg_item_icons' => '#f4f4f4',
			'mg_item_icons_color' => '#ffffff',
			
			'mg_filters_txt_color' => '#efefef', 
			'mg_filters_bg_color' => '#6a6a6a',
			'mg_filters_border_color' => '#666666', 
			'mg_filters_txt_color_h' => '#ffffff', 
			'mg_filters_bg_color_h' => '#5f5f5f', 
			'mg_filters_border_color_h' => '#444444',
			'mg_filters_txt_color_sel' => '#ffffff', 
			'mg_filters_bg_color_sel' => '#4f4f4f', 
			'mg_filters_border_color_sel' => '#424242',
			
			'preview' => 'dark_photowall.jpg'
		),
		
		'Dark - Title Under Items' => array(
			'mg_cells_margin' => 5,
			'mg_cells_img_border' => 3,
			'mg_cells_radius' => 2,
			'mg_cells_border' => 1,
			'mg_cells_shadow' => 0,
			'mg_item_radius' => 4,
			'mg_lb_border_w' => 3,
			'mg_item_radius' => 2,
			
			'mg_loader_color' => '#ffffff',
			'mg_cells_border_color' => '#ffffff',
			'mg_img_border_color' => '#3a3a3a',
			'mg_img_border_opacity' => 100,
			'mg_main_overlay_color' => '#222222',
			'mg_main_overlay_opacity' => 0,
			'mg_second_overlay_color' => '#9b9b9b',
			'mg_icons_col' => '#666666',
			'mg_overlay_title_color' => '#ffffff',
			'mg_txt_under_color' => '#ffffff',
			
			'mg_item_overlay_color' => '#222222',
			'mg_item_overlay_opacity' => 80,
			'mg_item_bg_color' => '#343434',
			'mg_item_border_color' => '#5f5f5f',
			'mg_item_txt_color' => '#ffffff',
			'mg_item_icons' => '#f4f4f4',
			'mg_item_icons_color' => '#eeeeee',
			
			'mg_filters_txt_color' => '#efefef', 
			'mg_filters_bg_color' => '#6a6a6a',
			'mg_filters_border_color' => '#666666', 
			'mg_filters_txt_color_h' => '#ffffff', 
			'mg_filters_bg_color_h' => '#5f5f5f', 
			'mg_filters_border_color_h' => '#444444',
			'mg_filters_txt_color_sel' => '#ffffff', 
			'mg_filters_bg_color_sel' => '#4f4f4f', 
			'mg_filters_border_color_sel' => '#424242',
			
			'preview' => 'dark_tit_under.jpg'
		),
	);
		
		
	if($style == '') {return $styles;}
	else {return $styles[$style];}	
}
