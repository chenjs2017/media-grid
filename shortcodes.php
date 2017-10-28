<?php
// SHORTCODE DISPLAYING THE GRID

// [mediagrid] 
function mg_shortcode( $atts, $content = null ) {
	include_once(MG_DIR . '/functions.php');
	include_once(MG_DIR . '/classes/overlay_manager.php');
	
	extract( shortcode_atts( array(
		'cat' 			=> '',
		'filter' 		=> 0,
		'r_width' 		=> 'auto',
		'title_under' 	=> 0,
		'filters_align' => 'top',
		'hide_all' 		=> 0,
		'def_filter' 	=> 0,
		'search'		=> 0,
		
		'cell_margin'	=> '',
		'border_w'		=> '',
		'border_col'	=> '',
		'border_rad'	=> '',
		'outline'		=> '',
		'outline_col'	=> '',
		'shadow'		=> '',
		'txt_under_col'	=> '',
		
		'overlay'		=> 'default',
	), $atts ) );

	if($cat == '') {return '';}
	$grid_id = $cat;
	$grid_temp_id = uniqid();
	
	$grid_data = mg_get_grid_data($grid_id); 
	if(!is_array($grid_data['items']) || !count($grid_data['items'])) {return '';}
	
	
	// custom styling codes
	if($cell_margin !== '' || $border_w !== '' || $border_col !== '' || $border_rad !== '' || $outline !== '' || $outline_col !== '' || $shadow !== '' || $txt_under_col !== '') {
		$cs_pre = '#mg_grid_'.$grid_id.' ';
		$cust_styles = '<style type="text/css">';
		
		if($cell_margin !== '')		{$cust_styles .= $cs_pre.'.mg_box {padding: '. (int)$cell_margin .'px;}';}
		if($border_w !== '')  		{$cust_styles .= $cs_pre.'.img_wrap {padding: '. (int)$border_w .'px;}';}
		if($border_col !== '') 		{$cust_styles .= $cs_pre.'.img_wrap {background: '. $border_col .';}';}
		if($border_rad !== '')		{
			$cust_styles .= 
				$cs_pre.'.mg_box, '.$cs_pre.'.mg_shadow_div, '. 
				$cs_pre.'.mg_box .img_wrap, '.
				$cs_pre.'.mg_box .img_wrap > div, '.$cs_pre.'.mg_inl_audio_img_wrap, '.
				$cs_pre.'.mg_box .img_wrap .overlays {
					border-radius: '. (int)$border_rad .'px;
				}';
		}
		if($outline_col !== '') 	{$cust_styles .= $cs_pre.'.img_wrap {border-color: '.$outline_col.';}';}	
		if($txt_under_col !== '')	{$cust_styles .= $cs_pre.'.mg_title_under {color: '.$txt_under_col.';}';}	
		
		if($outline == 1) {
			$cust_styles .= $cs_pre.'.img_wrap {border-width: 1px;}';	
		} elseif($outline === 0) {
			$cust_styles .= $cs_pre.'.img_wrap {border-width: 0px;}';	
		}
		
		if($shadow == 1) {
			$cust_styles .= $cs_pre.'.mg_shadow_div {box-shadow: 0px 0px 3px rgba(25, 25, 25, 0.4);}';	
		} elseif($shadow === 0) {
			$cust_styles .= $cs_pre.'.mg_shadow_div {box-shadow: none;}';	
		}
		
		$cust_styles .= '</style>';
	}
	else {$cust_styles = '';}
	
	
	// search code template
	if($search) {
		// deeplinked value
		if(isset($GLOBALS['mg_deeplinks']) && isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]) && isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgs'])) {
			$deeplinked_search = esc_attr(urldecode($GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgs']));
		} else {
			$deeplinked_search = '';	
		}
		
		$search_code ='
		<form id="mgs_'.$grid_id.'" class="mgf_search_form">
			<input type="text" value="'.$deeplinked_search.'" placeholder="'. __('search', 'mg_ml') .' .." autocomplete="off" />
			<i class="fa fa-search"></i>
		</form>';
	} 
	else {$search_code = '';}

	
	// filters management
	if($filter) {
		$filters_code = mg_grid_terms_data($grid_id, $grid_data['cats'], 'html', $filters_align, $def_filter, $hide_all);
		
		if($filters_code) {
			$filter_type = (get_option('mg_use_old_filters')) ? 'mg_old_filters' : 'mg_new_filters';
			$desktop_filters = '<div id="mgf_'.$grid_id.'" class="mg_filter '.$filter_type.'">'. $filters_code .'</div>';
		
			// mobile dropdown 
			if(get_option('mg_dd_mobile_filter')) {
				$filters_code = mg_grid_terms_data($grid_id, $grid_data['cats'], 'dropdown', $filters_align, $def_filter, $hide_all);
				$mobile_filters = '<div id="mgmf_'.$grid_id.'" class="mg_mobile_filter">'. $filters_code .'<i></i></div>';
			}
			else {$mobile_filters = '';}
			
			// filters align class and code composition
			switch($filters_align) {
				case 'left' : 
					$filter_align_class = 'mg_left_col_filters'; 
					$pre_grid_filters = $desktop_filters  . $mobile_filters . $search_code;
					$after_grid_filters = '';
					break;
					
				case 'right' : 
					$filter_align_class = 'mg_right_col_filters'; 
					$pre_grid_filters = $mobile_filters . $search_code;
					$after_grid_filters = $desktop_filters;
					break;
					
				default : 
					$filter_align_class = 'mg_top_filters';
					$pre_grid_filters =  $desktop_filters . $mobile_filters . $search_code .'<div style="clear: both;"></div>';
					$after_grid_filters = ''; 
					break;	
			}
		}
	}
	else {
		$pre_grid_filters = $search_code;
		$after_grid_filters = ''; 
		$filter_align_class = 'mg_no_filters';
	}


	// deeplinking class
	$dl_class = (get_option('mg_disable_dl')) ? '' : 'mg_deeplink'; 
	
	// search box class
	$search_box_class = ($search) ? 'mg_has_search' : '';
	
	// has pages class
	$has_pag_class = mg_grid_has_pag($grid_data['items']) ? 'mg_has_pag' : '';
	
	// custom overlay class
	$cust_ol_class = '';
	if(defined('MGOM_DIR')) {
		if(!$overlay || $overlay == 'default') {
			$cust_ol_class = (get_option('mg_default_overlay', '')) ? 'mgom_'.get_option('mg_default_overlay', '') : '';
		} else {
			$cust_ol_class = 'mgom_'.$overlay;	
		}
	}

	### init
	$grid = '<div id="mg_wrap_'.$grid_id.'" class="mg_grid_wrap '.$dl_class.' '.$search_box_class.' '.$filter_align_class.' '.$has_pag_class.' '.$cust_ol_class.'">' . $pre_grid_filters;

	// title under - wrap class
	switch($title_under) {
		case 0 : $tit_under_class = ''; break;
		case 1 : $tit_under_class = 'mg_grid_title_under mg_tu_attach'; break;
		case 2 : $tit_under_class = 'mg_grid_title_under mg_tu_detach'; break;	
	}
	
	// image overlay code 
	$ol_man = new mg_overlay_manager($overlay, $title_under);
	
	// pagination 
	$curr_pag = 1;
	
	// grid container
	$grid .= $cust_styles . '<div id="mg_grid_'.$grid_id.'" class="mg_container mg_container_'.$grid_temp_id.' '.$tit_under_class.' '.$search_box_class.' '.$ol_man->txt_vis_class.'" rel="'.$r_width.'" data-mg-pag="'.$curr_pag.'" '.$ol_man->img_fx_attr.'>' . mg_preloader();
	
	/////////////////////////
	// grid contents
		
	$max_width = get_option('mg_maxwidth', 1200);
	$mobile_treshold = get_option('mg_mobile_treshold', 800);
	$thumb_q = get_option('mg_thumb_q', 85);
	
	// custom icons global array
	if(!isset($GLOBALS['mg_items_cust_icon'])) {
		$GLOBALS['mg_items_cust_icon'] = array();		
	}

	$item_count = sizeof($grid_data['items']);
	if ($item_count >0 && $grid_data['items'][$item_count -1]  ) {
		$seed = $grid_data['items'][$item_count - 1]; 	
		if (get_post_meta($seed['id'], 'mg_main_type', true) == 'post_contents') {
			$post_arr = mg_post_contents_get_post ($seed['id'], true);
			$count = sizeof($post_arr);
			for ($i = 1 ; $i < $count; $i++) {
				$item = $seed;
				$item['id'] .= '_' . $post_arr[$i]; 
				array_push($grid_data['items'], $item);
			}
		}
	} 
//	var_dump($grid_data);


	foreach($grid_data['items'] as $item) {
		$post_id = $item['id'];
		$orig_post_id = $post_id; // keep it for custom behaviors
		if (strpos($post_id, '_')) {
			$post_id = explode('_', $post_id)[0];
		}
		$item_classes = array();
		
		// pagination management
		if($post_id == 'paginator') {
			$curr_pag++;
			continue;	
		}
		
		// WPML - check translation
		if(function_exists('icl_object_id') ) {
			$post_id = icl_object_id($post_id, 'mg_items', true);	
		}
		
		// get main type
		$main_type = get_post_meta($post_id, 'mg_main_type', true);
		
		// woocomm
		if(get_post_type($post_id) == 'product') {
			$main_type = 'woocom';
		} 
		
		$orig_main_type = $main_type; // keep it for checks
		
		// check post status
		if(get_post_status($post_id) != 'publish') {continue;}
		
		// post contents - get related post data
		if($orig_main_type == 'post_contents') {
			$offset = -1;
			if ($item['offset']) {
				$offset = $item['offset'];
			}
				
			$post = mg_post_contents_get_post($orig_post_id, false, $offset);
			if(!$post) {continue;}
			$filename = get_the_post_thumbnail_url($post);
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			if ($ext == 'gif') {
				continue;
			}
			
			$pc_direct_link = get_post_meta($post_id, 'mg_link_to_post', true);
			$post_id = $post->ID;
		}
		else {
			$pc_direct_link = false;
		}
		
		
		// image-based operations
		if($main_type != 'spacer') {
			// thumbs image size
			$thb_w = ceil($max_width * mg_size_to_perc($item['w']));
			$thb_h = ceil($max_width * mg_size_to_perc($item['h']));
			
			if(!isset($item['m_w'])) {
				$item['m_w'] = $item['w'];
				$item['m_h'] = $item['h'];
			}
			$m_thb_w = ceil($mobile_treshold * mg_size_to_perc($item['m_w']));
			$m_thb_h = ceil($mobile_treshold * mg_size_to_perc($item['m_h']));
			
			
			if(!in_array($main_type, array('inl_slider', 'inl_text'))) {
				
				// custom icon check
				$cust_icon = get_post_meta($orig_post_id, 'mg_cust_icon', true);
				if($cust_icon) {
					$GLOBALS['mg_items_cust_icon'][$orig_post_id] = $cust_icon;
				}
				
				// where to pick image
				$img_subj_post_id = ($orig_main_type != 'post_contents' || ($orig_main_type == 'post_contents' && get_post_meta($orig_post_id, 'mg_use_item_feat_img', true))) ? $orig_post_id : $post_id;
				
				// thumb url and center
				$img_id = get_post_thumbnail_id($img_subj_post_id);
				$thumb_center = (get_post_meta($img_subj_post_id, 'mg_thumb_center', true)) ? get_post_meta($img_subj_post_id, 'mg_thumb_center', true) : 'c'; 
				
				if($img_id) {
					// main thumb
					if($item['h'] != 'auto') {
						$thumb_url = mg_thumb_src($img_id, $thb_w, $thb_h, $thumb_q, $thumb_center);
					} else {
						$thumb_url = (mg_img_is_gif($img_id)) ? mg_img_id_to_fullsize_url($img_id) : mg_thumb_src($img_id, $thb_w, false, $thumb_q, $thumb_center);
					}
					
					// mobile thumb
					if($item['m_h'] != 'auto') {
						$mobile_url = mg_thumb_src($img_id, $m_thb_w, $m_thb_h, $thumb_q, $thumb_center);
					} else {
						$mobile_url = (mg_img_is_gif($img_id)) ? mg_img_id_to_fullsize_url($img_id) : mg_thumb_src($img_id, $m_thb_w, false, $thumb_q, $thumb_center);
					}
				} 
				else {
					$thumb_url = '';
					$mobile_url = '';
					$item_classes[] = 'mg_no_feat_img';
				}
			}
			
			
			// item title
			$item_title = get_the_title($post_id);
			
			// image ALT attribute
			$img_alt = strip_tags( mg_sanitize_input($item_title) );
			
			// title under switch
			if(!empty($title_under)) {
				$img_ol = '<div class="overlays">' . $ol_man->get_img_ol($post_id) . '</div>';
				$txt_under = $ol_man->get_txt_under($post_id);
			} 
			else {
				$img_ol = '<div class="overlays">' . $ol_man->get_img_ol($post_id) . '</div>';
				$txt_under = '';
			}
			
			// if overlays are hidden
			if(get_option('mg_hide_overlays')) {
				$img_ol = '';	
			}
			
			
			// image proportions for the "auto" height
			$ratio = '';	
			if(($item['h'] == 'auto' || $item['m_h'] == 'auto') && $main_type != 'inl_text') {
				$img_info = wp_get_attachment_image_src($img_id, 'full');
				
				if($img_info[2]) {
					$ratio_val = (float)$img_info[2] / (float)$img_info[1];
					$ratio = 'data-ratio="'.$ratio_val.'"';
				}
			}
		}
		
		
		//////////////////////////////
		/*** item types - classes ***/
		
		// type class
		switch($main_type) {
			case 'single_img'	: $item_classes[] = 'mg_image'; break;	
			case 'img_gallery'	: $item_classes[] = 'mg_gallery'; break;	
			case 'simple_img'	: $item_classes[] = 'mg_static_img'; break;
			default 			: $item_classes[] = 'mg_'. $main_type; break;	 
		}
		
		// transitions class
		if(!in_array($main_type, array('inl_slider','inl_video','inl_audio','inl_text','spacer'))) {
			$item_classes[] = 'mg_transitions';
		}
		
		// lightbox trigger class
		if(in_array($orig_main_type, array('single_img','img_gallery','video','audio','lb_text')) || ($orig_main_type == 'woocom' && !get_post_meta($post_id, 'mg_link_only', true)) || ($orig_main_type == 'post_contents' && !$pc_direct_link)) {
			$item_classes[] = 'mg_closed';
		}
		
		// no overlay class fot static/inline audio items
		if(in_array($orig_main_type, array('simple_img', 'inl_audio')) && !get_post_meta($post_id, 'mg_static_show_overlay', true)) {
			$item_has_no_ol = true;
			$item_classes[] = 'mg_item_no_ol';
		}
		else {$item_has_no_ol = false;}
		
		// spacer - visibility class
		if($orig_main_type == 'spacer') {
			$vis = get_post_meta($post_id, 'mg_spacer_vis', true);
			if($vis) {$item_classes[] = 'mg_spacer_'.$vis;}	
		}
		
		// term classes, for filters - set before "post contents" post_id change
		$term_classes = mg_item_term_classes($orig_post_id);
		
		// classes wrap-up
		$classes_array = array_merge(array('mgi_'.$orig_post_id, 'mg_pag_'.$curr_pag), $item_classes);
		$add_classes = implode(' ', $classes_array) .' '. $term_classes;
		
		
		////////////////////////////
		/*** items custom css ***/
		
		// inline texts custom colors
		if($orig_main_type == 'inl_text') {
			$img_wrap_css = 'style="';
			
			// background and colors
			if(get_post_meta($post_id, 'mg_inl_txt_color', true)) {$img_wrap_css .= 'color: '.get_post_meta($post_id, 'mg_inl_txt_color', true).';';}
			if(get_post_meta($post_id, 'mg_inl_txt_box_bg', true)) {$img_wrap_css .= 'background-color: '.get_post_meta($post_id, 'mg_inl_txt_box_bg', true).';';}
			if((int)get_post_meta($post_id, 'mg_inl_txt_bg_alpha', true)) {
				$alpha = (int)get_post_meta($post_id, 'mg_inl_txt_bg_alpha', true) / 100; 
				$img_wrap_css .= 'background-color: '.mg_hex2rgba( get_post_meta($post_id, 'mg_inl_txt_box_bg', true), $alpha).';';
			}
			
			$img_id = get_post_thumbnail_id($post_id);
			if(!empty($img_id) && get_post_meta($post_id, 'mg_inl_txt_img_as_bg', true)) {
				$img_url = wp_get_attachment_image_src($img_id, 'medium');	
				$img_wrap_css .= ' background-image: url('. $img_url[0] .'); background-size: cover; background-position: center center;';
			}

			$img_wrap_css .= '"';
		}
		else {$img_wrap_css = '';}
		
		
		
		/////////////////////////////
		/*** search attribute ***/
		if($search && $main_type != 'spacer') {
			$search_helper = get_post_meta($post_id, 'mg_search_helper', true);
			$search_attr = 'data-mg-search="'. str_replace('"', '', strtolower($img_alt)) .' '.$search_helper.'"';	
		}
		else {$search_attr = '';}
		

		
		/*** item block ***/
		// first part
		$grid .= '
		<div id="'.uniqid().'" class="mg_box mg_pre_show col'.$item['w'].' row'.$item['h'].' m_col'.$item['m_w'].' m_row'.$item['m_h'].' '.$add_classes.'" rel="pid_'.$orig_post_id.'" '.$ratio.' 
			data-mgi_w="'.mg_size_to_perc($item['w'], 1).'" data-mgi_h="'.mg_size_to_perc($item['h'], 1).'" data-mgi_mw="'.mg_size_to_perc($item['m_w'], 1).'" data-mgi_mh="'.mg_size_to_perc($item['m_h'], 1).'" data-mg_pag="'.$curr_pag.'" '.$search_attr.'>';
			
			// text under control
			$have_txt_under = (!in_array($main_type, array('inl_slider', 'simple_img', 'inl_text')) || empty($item_has_no_ol)) ? true : false;
			$txt_under_class = ($have_txt_under) ? 'mg_has_txt_under' : '';
			
			if($main_type != 'spacer') {
				$grid .= '
				<div class="mg_shadow_div">
					<div class="img_wrap '.$txt_under_class.'" '.$img_wrap_css.'>
						<div>';
						
						// link type - start tag
						if($main_type == 'link') {
							$nofollow = (get_post_meta($post_id, 'mg_link_nofollow', true) == '1') ? 'rel="nofollow"' : '';
							$grid .= '<a href="'.get_post_meta($post_id, 'mg_link_url', true).'" target="_'.get_post_meta($post_id, 'mg_link_target', true).'" '.$nofollow.' class="mg_link_elem">';
						}
						
						// woocomm link-only item
						elseif($orig_main_type == 'woocom' && !in_array('mg_closed', $item_classes)) {
							$grid .= '<a href="'.get_permalink($post_id).'" class="mg_link_elem">';
						}
						elseif($orig_main_type == 'post_contents' && $pc_direct_link) {
	
							$grid .= '<a href="'.get_permalink($post_id).'" class="mg_link_elem">';
						}
						
							/*** inner contents for lightbox and inline types ***/
							// inline slider
							if($main_type == 'inl_slider') {
								$slider_img = get_post_meta($post_id, 'mg_slider_img', true);
								$autoplay = (get_post_meta($post_id, 'mg_slider_autoplay', true)) ? 'mg_autoplay_slider' : '';
								$captions = get_post_meta($post_id, 'mg_slider_captions', true);
								$ken_burns = (get_post_meta($post_id, 'mg_kenburns_fx', true)) ? 'mg_kenburns_slider' : '';

								$grid .= '
								<div id="'.uniqid().'" class="mg_inl_slider_wrap '.$ken_burns.' '.$autoplay.'">
									<ul style="display: none;">';
								  
								if(is_array($slider_img)) {
									if(get_post_meta($post_id, 'mg_inl_slider_random', true)) {
										shuffle($slider_img);	
									}

									foreach($slider_img as $img_id) {
										
										// WPML integration - get translated ID
										if(function_exists('icl_object_id')) {
											$img_id = icl_object_id($img_id, 'attachment', true);	
										}
										
										// resize if is not an animated gif
										if(!mg_img_is_gif($img_id)) {
											if($ken_burns) {
												// resizers scale only to lower side - use wordpress thumbs
												$kb_img_h = ($item['h'] != 'auto' && $item['m_h'] != 'auto') ? (max($thb_h, $m_thb_h) * 1.25) : 0;
												$kb_img_src = wp_get_attachment_image_src($img_id, array((max($thb_w, $m_thb_w) * 1.25), $kb_img_h));
												$slider_thumb = $kb_img_src[0]; 
											}
											else {
												$sizes = mg_inl_slider_img_sizes( wp_get_attachment_image_src($img_id, 'full') , $max_width, $item);
												$slider_thumb = mg_thumb_src($img_id, $sizes['w'], $sizes['h'], $thumb_q);
											}
										}
										else {
											$slider_thumb = mg_img_id_to_fullsize_url($img_id);
										}
										
										
										if($captions == 1) {
										   $img_data = get_post($img_id);
										   $caption = (empty($img_data->post_content)) ? '' : trim($img_data->post_content);
										}
										else {$caption = '';}
										
										$grid .= '
										<li lcms_img="'.$slider_thumb.'">
											'.$caption.'<noscript><img src="'.$slider_thumb.'" alt="'.mg_sanitize_input($caption).'" /></noscript>
										</li>';
									}
								}
						
								// slider wrap closing
								$grid .= '</ul></div>'; 
							}
							
							// inline video
							if($main_type == 'inl_video') {
								$video_url = get_post_meta($post_id, 'mg_video_url', true);
								$poster = (get_post_meta($post_id, 'mg_video_use_poster', true) && $thumb_url) ? true : false;
								$autoplay = (get_post_meta($post_id, 'mg_autoplay_inl_video', true) && !$poster) ? true : false;
								$z_index = ($poster) ? 'style="z-index: -1;"' : '';
								
								// self-hosted
								if(lcwp_video_embed_url($video_url) == 'wrong_url') {
									$sources = mg_sh_video_sources($video_url);

									if(!$sources) {
										$grid .= '<p><em>Video extension not supported ..</em></p>';	
									}
									else {
										$preload = ($poster) ? 'meta' : 'auto';
										$autoplay = ($autoplay) ? 'mg_video_autoplay' : '';
										
										$grid .= 
										'<div id="'.uniqid().'" class="mg_sh_inl_video mg_me_player_wrap mg_self-hosted-video '.$autoplay.'" '.$z_index.'>
											<video width="100%" height="100%" controls="controls" preload="'.$preload.'">
											  '.$sources.'
											</video>
										</div>';
									}
								
								}
								else {
									$url_to_use = ($poster) ? '' : lcwp_video_embed_url($video_url, $autoplay);
									$autoplay_url = ($poster) ? 'data-autoplay-url="'. lcwp_video_embed_url($video_url, true). '"' : '';
									
									$grid .= '<iframe class="mg_video_iframe" src="'.$url_to_use.'" frameborder="0" allowfullscreen '.$autoplay_url.' '.$z_index.'></iframe>';	
								}
							}
							
							// inline audio
							if($main_type == 'inl_audio') {
								$soundcloud = get_post_meta($post_id, 'mg_soundcloud_url', true);
								
								if(!empty($soundcloud)) {
									$sc_lazyload = ($item_has_no_ol) ? false : true;
									$grid .= mg_get_soundcloud_embed($soundcloud, true, $sc_lazyload) . '<div class="mg_inl_audio_img_wrap">';	
								}
								else {
									$preload = (!in_array('mg_item_no_ol', $item_classes)) ? 'auto' : 'metadata'; 
									$tracklist = get_post_meta($post_id, 'mg_audio_tracks', true);
									
									// player
									$args = array(
										'posts_per_page'	=> -1,
										'orderby'			=> 'post__in',
										'post_type'       	=> 'attachment',
										'post__in'			=> $tracklist
									);
									$tracks = get_posts($args);
									$player_id = uniqid();
						
									$grid .= '
									<div id="'.$player_id.'" class="mg_me_player_wrap mg_inl_audio_player">
										<audio controls="controls" preload="'.$preload.'" width="100%">';
											foreach($tracks as $track) {$grid .= '<source src="'. $track->guid .'" type="'. $track->post_mime_type .'">';}
									$grid .= '
										</audio>';
										
										// tracklist
										$tot = (is_array($tracklist)) ? count($tracklist) : 0;
										if($tot > 1) {
											$grid .= '<ol id="'.$player_id.'-tl" class="mg_audio_tracklist mg_inl_audio_tracklist" style="display: none;">';
											
											$a = 1;
											foreach($tracks as $track) {
												$current = ($a == 1) ? 'mg_current_track' : '';
												$grid .= '<li mg_track="'. $track->guid .'" rel="'.$a.'" class="'.$current.'">'. $track->post_title .'</li>';
												$a++;
											}
											
											$grid .= '</ol>';
										}
										
									$grid .= '</div>
									<div class="mg_inl_audio_img_wrap">';
								}
							}
							
							// inline text
							if($main_type == 'inl_text') {
								$no_txt_resize_class = (get_post_meta($post_id, 'mg_inl_txt_no_resize', true)) ? 'mg_inl_txt_no_resize' : '';
								
								$grid .= '<table class="mg_inl_txt_table"><tbody><tr>
									<td class="mg_inl_txt_td '.$no_txt_resize_class.'" style="vertical-align: '.get_post_meta($post_id, 'mg_inl_txt_vert_align', true).'; '. esc_attr( (string)get_post_meta($post_id, 'mg_inl_txt_custom_css', true)) .'">
										'. do_shortcode(wpautop(get_post_field('post_content', $post_id))) .'
									</td>
								</tr></tbody></table>';	
							}
							
							
							// standard lightbox types and inline video with poster
							if(!in_array($main_type, array('inl_slider', 'inl_video', 'inl_text')) || ($main_type == 'inl_video' && $poster)) {
								
								// no image for soundcloud inline audio + no overlay
								if($main_type == 'inl_audio' && $soundcloud && $item_has_no_ol) {}
								else {
									
									// ken burns maybe?
									if(get_post_meta($img_subj_post_id, 'mg_kenburns_fx', true)) {
										
										// resizers scale only to lower side - use wordpress thumbs
										$kb_img_h = ($item['h'] != 'auto' && $item['m_h'] != 'auto') ? (max($thb_h, $m_thb_h) * 1.25) : 0;
										$kb_img_src = (mg_img_is_gif($img_id)) ? wp_get_attachment_image_src($img_id, 'full') : wp_get_attachment_image_src($img_id, array((max($thb_w, $m_thb_w) * 1.25), $kb_img_h));	
										
										$kenburns_code =
										'<div class="mg_kenburnsed_item mg_kenburns_slider">
											<ul style="display: none;"><li lcms_img="'. $kb_img_src[0] .'"></li></ul>
										</div>';
										
										$kb_main_img_vis = 'style="visibility: hidden !important; z-index: 1;"';
									} else {
										$kenburns_code = '';
										$kb_main_img_vis = '';
									}
									
									// if is kenburns and item always have height - preload kb image
									if($kenburns_code && $kb_img_h) {
										$mobile_url = $thumb_url = $kb_img_src[0];
									}
									
									$grid .= '
									<div class="mg_img_wrap_inner">
										'.$kenburns_code.'
										<img src="" class="thumb" alt="'.$img_alt.'" data-fullurl="'.$thumb_url.'" data-mobileurl="'.$mobile_url.'" '.$kb_main_img_vis.' />
									</div>
									<noscript>
										<img src="'.$thumb_url.'" alt="'.$img_alt.'" />
									</noscript>';
								}
								
								// inline audio - close inner wrapper
								if($main_type == 'inl_audio') {
									$grid .= '</div>';	
								}
								
								// overlays
								if(empty($item_has_no_ol)) {
									$grid .= $img_ol;
								}
							} 
							
							// SEO deeplink trick
							if(!empty($dl_class) && !in_array($main_type, array('simple_img','inl_slider','inl_video','inl_audio','inl_text','link')) ) {
								$grid .= '<a href="'. mg_item_deeplinked_url($post_id, $item_title) .'" class="mg_seo_dl_link">\'</a>';
							}

						
						// link type - end tag	
						if($main_type == 'link' || ($orig_main_type == 'woocom' && !in_array('mg_closed', $item_classes)) || ($orig_main_type == 'post_contents' && $pc_direct_link)) {	
							$grid .= '</a>'; 
						}

					$grid .= '
						</div>
					</div>';
					
					// overlays under
					if($have_txt_under) {
						$grid .= $txt_under;
					}
			
				$grid .= '</div>';		
			}
			
		// close main div
		$grid .= '</div>';	
		
	} // end foreach and close grid
	$grid .= '</div>'.$after_grid_filters;
			
	
	/////////////////////////		
	// pagination buttons
	if($has_pag_class) {
		$tot_pag = $curr_pag;
		
		// layout classes
		$pag_layout = get_option('mg_pag_layout', 'standard'); 
		$pl_class = '';
		
		if($pag_layout == 'standard') 		{$pl_class .= 'mg_pag_standard';}
		if($pag_layout == 'only_num') 		{$pl_class .= 'mg_pag_onlynum';}
		if($pag_layout == 'only_arr') 		{$pl_class .= 'mg_only_arr';}
		if($pag_layout == 'only_arr_dt') 	{$pl_class .= 'mg_only_arr_dt';}
	
		// deeplinked page
		if(isset($GLOBALS['mg_deeplinks']) && isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]) && isset($GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgp'])) {
			$dl_pag = (int)$GLOBALS['mg_deeplinks']['gid_'.$grid_id]['mgp'];
			if(empty($dl_pag)) {$curr_pag = 1;}
			if($dl_pag > $tot_pag) {$curr_pag = $tot_pag;}
		} 
		else {
			$curr_pag = 1;
		}
	
		// compose
		$grid .= '<div id="mgp_'.$grid_id.'" class="mg_pag_wrap '.$pl_class.' mg_pag_'.get_option('mg_pag_style', 'light').'" data-init-pag="'.$curr_pag.'" data-tot-pag="'.$tot_pag.'">';
			
			// mid nav - layout code
			if($pag_layout == 'standard') {
				$mid_code = '<div class="mg_nav_mid"><div>'. __('page', 'mg_ml') .' <span>'.$curr_pag.'</span> '. __('of', 'mg_ml') .' '.$tot_pag.'</div></div>';	
			}
			elseif($pag_layout == 'only_num') {
				$mid_code = '<div class="mg_nav_mid"><div><span>'.$curr_pag.'</span> <font>-</font> '.$tot_pag.'</div></div>';	
			}
			else {
				$mid_code = '';
			}
			
			// disabled class management
			$prev_dis = ($curr_pag == 1) ? 'mg_pag_disabled' : '';
			$next_dis = ($curr_pag == $tot_pag) ? 'mg_pag_disabled' : '';
			
			$grid .= '
			<div class="mg_prev_page '.$prev_dis.'"><i></i></div>
			'.$mid_code.'
			<div class="mg_next_page '.$next_dis.'"><i></i></div>';		

		$grid .= '</div>';
	} // pagination end
	
	
	// grid end
	$grid .= '</div>';

	// js - init grid
	$grid .= '
	<script type="text/javascript">
	jQuery(document).ready(function($) { 
		jQuery(window).trigger("mg_pre_grid_init", ["'. $grid_temp_id .'"]);
		if(typeof(mg_block_init) != "undefined" && typeof(mg_block_init["'. $grid_temp_id .'"]) != "undefined") {return true;}
	
		if(typeof(mg_async_init) == "function" ) {
			mg_async_init('.$grid_id.', '.$curr_pag.');
		}';
		
		
		//////////////////////////////////////////////////
		// OVERLAY MANAGER ADD-ON
		if(defined('MGOM_URL')) {
			$grid .= '
			if(typeof(mgom_hub) == "function" ) {
				mgom_hub('.$grid_id.');
			}
			';	
		}
		//////////////////////////////////////////////////
		

	$grid .= '	
	});
	</script>';



	return str_replace(array("\r", "\n", "\t", "\v"), '', $grid);
}
add_shortcode('mediagrid', 'mg_shortcode');
