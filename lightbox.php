<?php
// ajax lightbox trigger
function mg_ajax_lightbox() {
	if(isset($_POST['mg_lb']) && $_POST['mg_lb'] == 'mg_lb_content') {
		
		include_once(MG_DIR . '/functions.php');
		header('Content-Type: text/html; charset=utf-8');
	
		//if(!isset($_POST['pid']) || !filter_var($_POST['pid'], FILTER_VALIDATE_INT)) {die('item id is missing');}
		$pid = addslashes($_POST['pid']);
		
		$prev = (isset($_POST['prev_id'])) ? (int)$_POST['prev_id'] : false;
		$next = (isset($_POST['next_id'])) ? (int)$_POST['next_id'] : false;
		mg_lightbox($pid, $prev, $next);
		die();
	}
}
add_action('wp_loaded', 'mg_ajax_lightbox', 999);



// lightbox code
function mg_lightbox($post_id, $prev_item = false, $next_item = false) {
	include_once(MG_DIR . '/functions.php');
	$orig_post_id = $post_id; // keep it for custom behaviors
	if (strpos($post_id, '_')) {
       $post_id = explode('_', $post_id)[0];
    }

	$post_data = get_post($post_id);
	$GLOBALS['post'] = $post_data; 
	
	// check for publish items
	if($post_data->post_status != 'publish') {
		echo 'Item not found';
		return false;
	}
	
	// track real post ID
	$final_post_id = $post_id;
	
	
	// post type 
	if($post_data->post_type == 'product') {
		// simulate standard type and add flag	
		$wc_prod = new WC_Product($post_id);
		
		// Woocomm v3 compatibility
		$wc_gallery = (method_exists($wc_prod, 'get_gallery_image_ids')) ? $wc_prod->get_gallery_image_ids() : $wc_prod->get_gallery_attachment_ids(); 
			
		$type = (is_array($wc_gallery) && count($wc_gallery) > 0) ? 'img_gallery' : 'single_img';
		$show_feat = true;
	}
	else {
		$type = get_post_meta($post_id, 'mg_main_type', true);
		$wc_prod = false;
		
		// post contents type - manage resulting type and true post ID
		if($type == 'post_contents') {
			$post = mg_post_contents_get_post($orig_post_id);
			
			if(!$post) {die('no posts found');}
			else {
				// if WooCommerce product -> recall
				if($post->post_type == 'product') {
					mg_lightbox($post->ID, $prev_item, $next_item); 
					return true;
				}
				else {
					$pc_post_id 	= $post->ID;	
					$final_post_id 	= $pc_post_id;
					$pc_post_data 	= $post;
					$show_feat 		= (get_post_meta($post_id, 'mg_hide_feat_img', true)) ? false : true;  
				}
			}
		}
		else {$show_feat = true;}
	}


	// layout
	$layout = get_post_meta($post_id, 'mg_layout', true);
	if($layout == 'side') {$layout = 'side_tripartite';} // retrocompatibility
	
	$touchswipe = (get_option('mg_lb_touchswipe')) ? 'mg_touchswipe' : '';
	$item_title = (isset($pc_post_id)) ? $pc_post_data->post_title : $post_data->post_title;
	$featured = '';
	
	// image display mode
	if(in_array($type, array('single_img', 'audio', 'post_contents'))) {
		$img_display_mode = (get_post_meta($post_id, 'mg_lb_img_display_mode', true) == 'img_w') ? 'mg_lb_img_auto_w' : 'mg_lb_img_fill_w'; 
	} else {
		$img_display_mode = '';	
	}
	
	// image max height
	$img_max_h = (int)get_post_meta($post_id, 'mg_img_maxheight', true);
	
	// contents match height
	$feat_match_txt = ($layout != 'full' && get_post_meta($post_id, 'mg_lb_feat_match_txt', true)) ? 'mg_lb_feat_match_txt' : '';
	
	// canvas color for TT
	$tt_canvas = substr(get_option('mg_item_bg_color', '#ffffff'), 1);
	
	// maxwidth control
	$lb_max_w = (int)get_option('mg_item_maxwidth', 960);
	if($lb_max_w == 0) {$lb_max_w = 960;}

	// Thumb center
	$tt_center = (get_post_meta($post_id, 'mg_thumb_center', true)) ? get_post_meta($post_id, 'mg_thumb_center', true) : 'c'; 
	
	// lightbox max width for the item
	$fc_max_w = (int)get_post_meta($post_id, 'mg_lb_max_w', true);
	if(!$fc_max_w || $fc_max_w < 280) {$fc_max_w = false;} 
	$new_lb_max_w = ($fc_max_w) ? $fc_max_w : $lb_max_w;
	
	// item featured image for socials
	$fi_img_id = (isset($pc_post_id)) ? get_post_thumbnail_id($pc_post_id) : get_post_thumbnail_id($post_id);
	$fi_src = wp_get_attachment_image_src($fi_img_id, 'medium');
	$fi_src_pt = wp_get_attachment_image_src($fi_img_id, 'full'); // pinterest - use full one
	
	// image block for single_item + woocommerce + post contents + audio
	if(in_array($type, array('single_img', 'audio', 'post_contents'))) {
		$img_id = (isset($pc_post_id)) ? get_post_thumbnail_id($pc_post_id) : get_post_thumbnail_id($post_id);	
		$feat_img_url = mg_lb_image_optimizer($img_id, $layout, $new_lb_max_w, $img_display_mode, $img_max_h, $feat_match_txt);
		$img_fx = get_post_meta($post_id, 'mg_lb_img_fx', true);
		
		if($img_fx == 'kenburns') {
			$kenburns_code =
			'<div class="mg_kenburnsed_item mg_kenburns_slider">
				<ul style="display: none;"><li lcms_img="'. $feat_img_url .'"></li></ul>
			</div>';
			
			$kb_main_img_vis = 'style="visibility: hidden !important; z-index: 1;"';
		}
		else {
			$feat_img_url = mg_lb_image_optimizer($img_id, $layout, $new_lb_max_w, $img_display_mode, $img_max_h, $feat_match_txt);
			$kenburns_code = $kb_main_img_vis = '';
		}
		
		// image zoom attr 
		$img_zoom_attr = (in_array($type, array('single_img', 'post_contents')) && $img_fx == 'zoom') ? 'zoom-image="'. $fi_src_pt[0] .'"' : '';

		$feat_img_code = mg_preloader().
			'<div id="mg_lb_feat_img_wrap">	
				'.$kenburns_code.'
				<img src="'. $feat_img_url .'" '. $img_zoom_attr .' alt="'.mg_sanitize_input(strip_tags($item_title)).'" '.$kb_main_img_vis.' />'.
			'</div>';	
	}

		
	


	///////////////////////////
	// TYPES - SPECIFIC CODES
	
	if($type == 'single_img' || isset($pc_post_id)) {
		$featured = ($show_feat) ? $feat_img_code : '';
	}
	
	
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	elseif($type == 'img_gallery') {
		$slider_img 	= (isset($wc_gallery)) ? $wc_gallery : get_post_meta($post_id, 'mg_slider_img', true);
		$attach_video 	= (isset($wc_gallery)) ? false : get_post_meta($post_id, 'mg_slider_vid', true);
		
		$style = get_option('mg_slider_style', 'light');
		$slider_id = uniqid();
		$autoplay = (get_post_meta($post_id, 'mg_slider_autoplay', true)) ? 'true' : 'false';
		
		// slider height
		$def_h_val = get_option('mg_slider_main_w_val', 55);
		$def_h_type = get_option('mg_slider_main_w_type', '%');
		$h_val = get_post_meta($post_id, 'mg_slider_w_val', true);
		$h_type = get_post_meta($post_id, 'mg_slider_w_type', true);
		
		if(!$h_val) {$h_val =  $def_h_val;}
		if(!$h_type) {$h_type =  $def_h_type;}
		$height = $h_val.$h_type;
		
		// slider proportions parameter
		if(strpos($height, '%') !== false) {
			$val = (int)str_replace("%", "", $height) / 100;
			$proportions_param = 'data-asp-ratio="'. $val .'"';
			$proportions_class = "mg_galleria_responsive";
			
			$slider_h = '';
			$stage_max_h = $val;
		} 
		else {
			$proportions_param = 'data-slider-h="'. $height .'"';	
			$proportions_class = "";
			
			$slider_h = 'height: '.$height.';';
			$stage_max_h = $h_val;
		}
		
		// images management
		$crop = get_post_meta($post_id, 'mg_slider_crop', true);
		if(!$crop) {$crop = 'true';}
		
		// slider thumbs visibility
		$thumbs_visibility = get_post_meta($post_id, 'mg_slider_thumbs', true);
		$thumbs_class = ($thumbs_visibility == 'yes' || $thumbs_visibility == 'always') ? 'mg_galleria_slider_show_thumbs' : '';
		
		// thumbs CSS code
		if($thumbs_visibility == 'always' || $thumbs_visibility == 'never') {
			$css_code = '.mg_galleria_slider_wrap .galleria-mg-toggle-thumb {display: none !important;}';	
		} else {
			$css_code = '';	
		}
		if(!$thumbs_visibility || $thumbs_visibility == 'no' || $thumbs_visibility == 'never') {
			$css_code .= '.mg_galleria_slider_wrap .galleria-thumbnails-container {opacity: 0; filter: alpha(opacity=0);}';	
		}
		
		
		$featured = '
		<style type="text/css">
			'.$css_code.'
		</style>
		
		<script type="text/javascript"> 
		mg_galleria_img_crop = "'.$crop.'";
		mg_slider_autoplay["#'.$slider_id.'"] = '.$autoplay.';
		</script>	
		
		<div id="'.$slider_id.'" 
			class="mg_galleria_slider_wrap mg_show_loader mg_galleria_slider_'.$style.' '.$thumbs_class.' '.$proportions_class.' mgs_'.$post_id.' noSwipe" 
			style="width: 100%; '.$slider_h.'" '.$proportions_param.'
		>';
		  
		if(is_array($slider_img)) {
			if(get_post_meta($post_id, 'mg_slider_random', true)) {
				shuffle($slider_img);	
			}
			
			// woocommerce - if prepend first image
			if(isset($wc_gallery) && get_post_meta($post_id, 'mg_slider_add_featured', true)) {
				array_unshift($slider_img, $fi_img_id);
			}
			
			// compose slider structure
			$a = 0;
			foreach($slider_img as $img_id) {
				
				// WPML integration - get translated ID
				if(function_exists('icl_object_id')) {
					$img_id = icl_object_id($img_id, 'attachment', true);	
				}
				
				if(get_post_meta($post_id, 'mg_slider_captions', true) == 1) {
					$img_data = get_post($img_id);
				   	$caption_code = trim(strip_tags(apply_filters('the_content', $img_data->post_content), 'br'));
				}
				else {$caption_code = '';}
					 
				$img_url = mg_lb_image_optimizer($img_id, $layout, $new_lb_max_w);
				$thumb = mg_thumb_src($img_id, 100, 69, $thumb_q = 85, 'c');	
				
				// video slide integration
				if(is_array($attach_video) && isset($attach_video[$a]) && !empty($attach_video[$a])) {
					$featured .= '
					<a href="'. $attach_video[$a] .'">
						<img src="'.mg_sanitize_input($thumb).'" data-image="'.$img_url.'" data-description="'.mg_sanitize_input($caption_code).'" />
					</a>';
				}
				else {
					$featured .= '
					<a href="'.$img_url.'">
						<img src="'.mg_sanitize_input($thumb).'" data-big="'.$img_url.'" data-description="'.mg_sanitize_input($caption_code).'" />
					</a>';
				}
				
				$a++;
			  }
		  }

		  $featured .= '<div style="clear: both;"></div>
		  </div>'; // slider wrap closing
		  
		  // slider init
		  $featured .= '<script type="text/javascript"> 
		  jQuery(document).ready(function($) {
			  if(typeof(mg_galleria_init) == "function") { 
				  mg_galleria_show("#'.$slider_id.'");
				  
				  setTimeout(function() {
				  	mg_galleria_init("#'.$slider_id.'");
				  }, 150);
			  }
		  });
		  </script>';
	}
		
		
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////		
	elseif($type == 'video') {
		$src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
		$video_url = get_post_meta($post_id, 'mg_video_url', true);
		
		$video_w = ($layout == 'full') ?  960 : (960 * 0.675);
		$video_h = $video_w * 0.56;
		
		// poster
		if(get_post_meta($post_id, 'mg_video_use_poster', true) == 1) {
			$img_id = get_post_thumbnail_id($post_id);
			$poster_img = mg_lb_image_optimizer($img_id, $layout, $new_lb_max_w);
			$poster = true;
		}
		else {
			$poster_img = '';
			$poster = false;
		}
		
		if(lcwp_video_embed_url($video_url) == 'wrong_url') {
			
			// get video sources
			$sources = mg_sh_video_sources($video_url);

			if(!$sources) {
				$featured = '<p><em>Video extension not supported ..</em></p>';	
			}
			else {
				$autoplay = (get_option('mg_video_autoplay') && !$poster) ? 'mg_video_autoplay' : '';
				$poster_attr = (!empty($poster_img)) ? 'poster="'.$poster_img.'"' : ''; 
				$preload_poster = (!$poster_attr) ? '' : mg_preloader().'<img src="'.$poster_img.'" />';
				
				$featured = 
				'<div id="mg_lb_video_wrap" class="mg_me_player_wrap mg_self-hosted-video '.$autoplay.'">
					<video width="100%" height="'.$video_h.'" controls="controls" preload="auto" '.$poster_attr.'>
					  '.$sources.'
					</video> 
					'.$preload_poster.'
				</div>';
			}
		} 
		else {
			if($poster) {
				$v_url =  lcwp_video_embed_url($video_url, false);

				$ifp = mg_preloader() . '
				<div id="mg_ifp_ol" class="fa fa-play" style="display: none;"></div>
				<div id="mg_lb_video_poster" data-autoplay-url="'. lcwp_video_embed_url($video_url, true) .'" style="background-image: url('. $poster_img .');"></div>
				<img src="'. $poster_img .'" alt="'.mg_sanitize_input(strip_tags($item_title)).'" style="display: none;" />
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery(document).delegate("#mg_ifp_ol, #mg_lb_video_poster", "touchstart", function() {
						jQuery("#mg_lb_video_poster").trigger("click");
					});
				});
				</script>';
			}
			else {
				$v_url = lcwp_video_embed_url($video_url);
				$ifp = '';
			}
			
			$featured = '
			<div id="mg_lb_video_wrap">
				'.$ifp.'
				<iframe class="mg_video_iframe" width="100%" height="'.$video_h.'" src="'. $v_url .'" frameborder="0" allowfullscreen></iframe>
			</div>
			';
		}
	}
	
	
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	elseif($type == 'audio') {
		// check for soundcloud embedding
		$soundcloud = get_post_meta($post_id, 'mg_soundcloud_url', true);
		if(!empty($soundcloud)) {
			$featured = mg_get_soundcloud_embed($soundcloud);	
		}
		else {
			$tracklist = get_post_meta($post_id, 'mg_audio_tracks', true);
			$show_tracklist = (count($tracklist) > 0 && get_option('mg_show_tracklist')) ? 'mg_show_tracklist' : '';
			$autoplay = (get_option('mg_audio_autoplay')) ? 'mg_audio_autoplay' : '';

			// player
			$args = array(
				'posts_per_page'	=> -1,
				'orderby'			=> 'post__in',
				'post_type'       	=> 'attachment',
				'post__in'			=> $tracklist
			);
			$tracks = get_posts($args);
			$player_id = uniqid();

			$featured = $feat_img_code .'

			<div id="'.$player_id.'" class="mg_me_player_wrap mg_lb_audio_player '.$show_tracklist.' '.$autoplay.'" style="display: none;">
				<audio controls="controls" preload="auto" width="100%">';
					foreach($tracks as $track) {$featured .= '<source src="'. $track->guid .'" type="'. $track->post_mime_type .'">';}
			$featured .= '
				</audio>';
				
				// tracklist
				$tot = (is_array($tracklist)) ? count($tracklist) : 0;
				if($tot > 1) {
					$tl_display = ($show_tracklist) ? '' : 'style="display: none;"';
					$featured .= '<ol class="mg_audio_tracklist" '.$tl_display.'>';
					
					$a = 1;
					foreach($tracks as $track) {
						$current = ($a == 1) ? 'mg_current_track' : '';
						$featured .= '<li mg_track="'. $track->guid .'" rel="'.$a.'" class="'.$current.'">'. $track->post_title .'</li>';
						$a++;
					}
					
					$featured .= '</ol>';
				}
				
			$featured .= '</div>';
		}
	}

	
	//////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////
	if($type == 'lb_text') {
		
		// custom contents lightbox - set custom padding and force layout to full
		$layout = 'full';
		
		$lbt_padding = get_post_meta($post_id, 'mg_lb_contents_padding', true);
		if(!is_array($lbt_padding) || count($lbt_padding) != 4) {$lbt_padding = array(0, 0, 0, 0);}
		?>
        <style type="text/css">
		div.mg_item_content.mg_lb_txt_fx {
			padding: <?php for($a=0; $a<4; $a++) {echo (int)$lbt_padding[$a].'px ' ;} ?>;	
		}
		</style>
        <?php
	}
	
	
	
	
	///////////////////////////
	// custom CSS to manage image's max height
	if(in_array($type, array('single_img', 'audio', 'post_contents')) && isset($img_max_h) && $img_max_h) {
		
		// if want to fill featured space
		if($img_display_mode == 'mg_lb_img_fill_w') {
			echo '
			<style type="text/css">
			.mg_item_featured:not(.mg_lb_feat_matched) #mg_lb_feat_img_wrap {
				height: '.$img_max_h .'px;
				max-height: '.$img_max_h .'px;
                background-image: url('. $feat_img_url .');
            }
            .mg_item_featured:not(.mg_lb_feat_matched) #mg_lb_feat_img_wrap img {
				display: none !important;
				min-width: 100% !important;
				min-height: 100% !important;
			}
            </style>';
		}
		else {
		?>
			<style type="text/css">
            #mg_lb_feat_img_wrap {
                text-align: center;	
            }
            #mg_lb_feat_img_wrap > img,
            #mg_lb_feat_img_wrap > a > img {
                display: inline-block;
                width: auto;
                max-height: <?php echo $img_max_h ?>px;
            }
			.mg_lb_feat_matched #mg_lb_feat_img_wrap img { /* avoid interferences between match-feat-h and max-h */
                max-height: none !important;
            }
            </style>
        <?php
		}
	}
	
	
	///////////////////////////
	// INNER CODE	
 
	/*** lightbox command codes ***/ 
	$cmd_mode = get_option('mg_lb_cmd_pos', 'inside');	
	?>
    <div id="mg_lb_ins_cmd_wrap" <?php if(!in_array($cmd_mode, array('inside', 'ins_hidden', 'round_hidden'))) {echo 'style="display: none;"';} ?>>
        <div id="mg_inside_close" class="mg_close_lb"></div>
        
        <div id="mg_lb_inside_nav" class="noSwipe" <?php if(in_array($cmd_mode, array('hiden', 'ins_hidden', 'round_hidden'))) {echo 'style="display: none; visibility: hidden;"';} ?>>
            <?php echo mg_lb_nav_code(array('prev' => $prev_item, 'next' => $next_item), 'inside'); ?>
        </div>
    </div>    
    
    <?php 
	if(!in_array($cmd_mode, array('inside', 'ins_hidden', 'round_hidden'))) {
		if($cmd_mode == 'top') {
			$code = '
			<div id="mg_top_close" class="mg_close_lb" style="display: none;"></div>
			<div id="mg_lb_top_nav" style="display: none;">'. mg_lb_nav_code(array('prev' => $prev_item, 'next' => $next_item), $cmd_mode) .'</div>';
		} else {
			$code = '
			<div id="mg_top_close" class="mg_close_lb" style="display: none;"></div>'.
			mg_lb_nav_code(array('prev' => $prev_item, 'next' => $next_item), $cmd_mode);	
		}
		
		echo '
		<script type="text/javascript">
		jQuery("#mg_top_close, #mg_lb_top_nav, .mg_lb_nav_side, .mg_lb_nav_side_basic").remove();
		
		jQuery("#mg_lb_contents").before("'. str_replace(array("\r", "\n", "\t", "\v"), '', str_replace('"', '\"', $code)) .'");
		jQuery("#mg_lb_top_nav, .mg_side_nav, #mg_top_close").fadeIn(250);
		
		if(navigator.appVersion.indexOf("MSIE 8.") != -1) {
			jQuery(".mg_side_nav > div").css("top", 0);	
		}
		</script>';	
	}
	?>
    
    
	<?php 
	/*** internal contents ***/ 
	
	?>
    <div id="mg_lb_<?php echo $post_id ?>" class="mg_lb_layout mg_layout_<?php echo $layout; ?> mg_lb_<?php echo $type; ?>">
      <div>
      
      	<?php if($type != 'lb_text' && $show_feat) : ?>
		<div class="mg_item_featured <?php echo $img_display_mode.' '. $feat_match_txt ?>">
			<?php echo $featured; ?>
		</div>
        <?php endif; ?>
        
		<div class="mg_item_content <?php echo (get_option('mg_lb_no_txt_fx')) ? '' : 'mg_lb_txt_fx'; ?>">
			<?php 
			/* custom options - woocommerce attributes */
			if(isset($pc_post_id)) {$opts = '';}
			else {$opts = mg_lb_cust_opts_code($post_id, $type, $wc_prod);}

			/* title and options wrap */
			if($layout == 'full' && !empty($opts)) {echo '<div class="mg_content_left">';} 
				$title = apply_filters('the_title', $item_title);
				echo '<h1 class="mg_item_title">'. apply_filters('the_title', $item_title) .'</h1>';
            	echo $opts;
            if($layout == 'full' && !empty($opts)) {echo '</div>';}
			
			
			// adding support to Visual Composer shortcodes
			if(class_exists('WPBMap') && method_exists('WPBMap','addAllMappedShortcodes')) {
			   WPBMap::addAllMappedShortcodes();
			}
			?>
            
			<?php 
			if(isset($pc_post_id)) {
				echo '<button class="button-secondary"> <a href="' . get_permalink($pc_post_data) .'">visit</a> </button>';
				/*
				$video = get_post_meta($pc_post_id,'webbupointfinder_item_video', true);
				var_dump($video);
				if ($video) {
					echo '<button class="button-secondary"> <a href="' . $video[0] .'" target="_blank">video</a> </button>';
				}
				*/
			}
			?>
            
			<div class="mg_item_text <?php if($layout == 'full' && empty($cust_opt)) {echo 'mg_widetext';} ?>">
				<?php 
				$subj = (isset($pc_post_id)) ? $pc_post_data->post_content : $post_data->post_content;
				echo do_shortcode( apply_filters('the_content', $subj)); 
				?>
                
                <?php 
				// add-to-cart for woocommerce
				if($wc_prod && !get_option('mg_wc_hide_add_to_cart')) {
					echo 
					'<div id="mg_woo_cart_btn_wrap" rel="'.$post_id.'">'. 
						do_shortcode('[add_to_cart id="'.$post_id.'" style=""]');
						
						// show a message if product has been added to cart
						if(isset($_REQUEST['add-to-cart']) && $_REQUEST['add-to-cart'] == $post_id) {
							echo '<p id="mg_woo_item_added"><i class="fa fa-check-square" aria-hidden="true"></i><span>'. __('Product added to cart!', 'mg_ml') .'</span></p>';	
						}
					
					echo
					'</div>';
				} 
				?> 
                
                <?php
				// know if lightbox has to show socials
				$has_socials = (get_option('mg_facebook') || get_option('mg_twitter') || get_option('mg_pinterest') || (get_option('mg_googleplus') && (is_array($deeplinked_elems) && in_array('item', $deeplinked_elems)))) ? true : false;
				
				
				// COMMENTS
				$GLOBALS['mg_comments']->get_comments($post_id, $final_post_id, $title, $has_socials);
				?>
            </div>
           
            
            <?php 
			// SOCIALS
			if($has_socials) : 
			  	$deeplinked_elems = get_option('mg_deeplinked_elems', array_keys(mg_elem_to_deeplink()) );
				$share_curr_url = urlencode(lcwp_curr_url());  
			 
			  	if(isset($pc_post_id)) {$post_id = $pc_post_id;}
			?>
              <div id="mg_socials" class="mgls_<?php echo get_option('mg_lb_socials_style', 'squared') ?>">
            	<ul>
                  <?php if(get_option('mg_facebook')): ?>
                  <li id="mg_fb_share">
					<a onClick="window.open('https://www.facebook.com/sharer?u=<?php echo $share_curr_url ?>&display=popup','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)"><span title="<?php _e('Share it!', 'mg_ml') ?>"></span></a>
                  </li>
                  <?php endif; ?>
                  
                  
                  <?php if(get_option('mg_twitter')): ?>
                  <li id="mg_tw_share">
					<a onClick="window.open('https://twitter.com/share?text=<?php echo urlencode('Check out "'.get_the_title($post_id).'" on '.get_bloginfo('name')); ?>&url=<?php echo $share_curr_url ?>','sharer','toolbar=0,status=0,width=548,height=325');" href="javascript: void(0)"><span title="<?php _e('Tweet it!', 'mg_ml') ?>"></span></a>
                  </li>
                  <?php endif; ?>
                  
                  
                  <?php if(get_option('mg_pinterest')): ?>
                  <li id="mg_pn_share">
                  	<a onClick="window.open('http://pinterest.com/pin/create/button/?url=<?php echo $share_curr_url ?>&media=<?php echo urlencode($fi_src_pt[0]); ?>&description=<?php echo urlencode(get_the_title($post_id)); ?>','sharer','toolbar=0,status=0,width=680,height=470');" href="javascript: void(0)"><span title="<?php _e('Pin it!', 'mg_ml') ?>"></span></a>
                  </li>
                  <?php endif; ?>
                  
                  
                  <?php if(get_option('mg_googleplus') && (is_array($deeplinked_elems) && in_array('item', $deeplinked_elems))) :
				  ?>
                  <li id="mg_gp_share">
                  	<a onClick="window.open('https://plus.google.com/share?url=<?php echo $share_curr_url ?>','sharer','toolbar=0,status=0,width=490,height=360');" href="javascript: void(0)"><span title="<?php _e('Share it!', 'mg_ml') ?>"></span></a>
                  </li>
                  <?php endif; ?>
                </ul>
                
              </div>
            <?php endif; ?>
            
			<br style="clear: both;" />
		</div>
        
        <?php if($layout != 'full') : ?>
        <div style="display: block; clear: both;"></div>
        <?php endif; ?>
      </div>
	</div> 
	<?php
	
	
	// lightbox custom (item-based) max width
	if($fc_max_w) : ?>
    <style type="text/css">
	#mg_lb_contents {
		max-width: <?php echo $fc_max_w ?>px;
	}
	</style>
	<?php endif; 
	
	
	// if direct opening - trigger lazyload JS function
	?>
	<script type="text/javascript">
	jQuery(document).ready(function(e) {
		<?php if($type == 'video') : ?>
		mg_video_player('#mg_lb_video_wrap');
		<?php endif; ?>
		
		mg_lb_lazyload();
		mg_pause_inl_players();
		
		<?php if($type != 'lb_text') : ?>
		mg_lb_realtime_actions();
		<?php endif; ?>
	});
	</script>
	<?php	
	
	
	
	// image zoom
	if(isset($img_zoom_attr) && $img_zoom_attr) :
	?>
    <script type="text/javascript" src="<?php echo MG_URL ?>/js/EasyZoom/easyzoom.min.js"></script>
	<script type="text/javascript">
	jQuery(document).ready(function(e) {
		jQuery('.mg_item_featured').addClass('mg_lb_img_zoom');
		jQuery('.mg_item_featured img').wrap('<a href="'+ jQuery('.mg_item_featured img').attr('zoom-image') +'"></a>');
		
		var $easyzoom = jQuery('.mg_item_featured').easyZoom( {
			loadingNotice	: "<?php echo addslashes(__('loading image', 'mg_ml')) ?>"
		});

	});
	</script>
    <?php
	endif;
}

