<?php 
include_once(MG_DIR . '/functions.php');

// item types array
$types = mg_main_types();

$wooc_active = mg_woocomm_active();
if($wooc_active) {$wc_attr = wc_get_attribute_taxonomies();}
?>

<div class="wrap lcwp_form">  
	<div class="icon32"><img src="<?php echo MG_URL.'/img/mg_icon.png'; ?>" alt="mediagrid" /><br/></div>
    <?php echo '<h2 class="lcwp_page_title" style="border: none;">' . __( 'Media Grid Settings', 'mg_ml') . "</h2>"; ?>  

    <?php
	// HANDLE DATA
	if(isset($_POST['lcwp_admin_submit'])) { 
		if (!isset($_POST['pg_nonce']) || !wp_verify_nonce($_POST['pg_nonce'], __FILE__)) {die('<p>Cheating?</p>');};
		include(MG_DIR . '/classes/simple_form_validator.php');		
		
		$validator = new simple_fv;
		$indexes = array();
		
		$indexes[] = array('index'=>'mg_loader', 'label'=>'Preloader');
		$indexes[] = array('index'=>'mg_cells_margin', 'label'=>__('Cells Margin', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_cells_img_border', 'label'=>__('Image Border', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_cells_radius', 'label'=>__('Cells Border Radius', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_cells_border', 'label'=>__('Cells Outer Border', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_cells_shadow', 'label'=>__('Cells Shadow', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_maxwidth', 'label'=>__( 'Grid max width', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_mobile_treshold', 'label'=>__('Mobile layout treshold', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_thumb_q', 'label'=>__( 'Thumbnail quality', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_tu_custom_padding', 'label'=>__( 'Title under images - custom padding', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_inl_txt_padding', 'label'=>__('Inline texts padding', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_clean_inl_txt', 'label'=>'Clean inline text box');
		$indexes[] = array('index'=>'mg_delayed_fx', 'label'=>'Show all items without delay');
		$indexes[] = array('index'=>'mg_hide_overlays', 'label'=>'Hide overlays');
			
		$indexes[] = array('index'=>'mg_filters_behav', 'label'=>'Filtered items behavior');
		$indexes[] = array('index'=>'mg_filters_align', 'label'=>'Filters Alignment', 'mg_ml');
		$indexes[] = array('index'=>'mg_dd_mobile_filter', 'label'=>'Use dropdown on mobile screens');
		$indexes[] = array('index'=>'mg_side_filters_width', 'label'=>__('Side filters - column width', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_use_old_filters', 'label'=>'Use old filters style');
		$indexes[] = array('index'=>'mg_all_filter_txt', 'label'=>__('"All" filter\'s text', 'mg_ml'), 'required'=>true);
		$indexes[] = array('index'=>'mg_no_results_txt', 'label'=>__('"no items found" text', 'mg_ml'), 'required'=>true);
		
		$indexes[] = array('index'=>'mg_pag_style', 'label'=>__( 'Pagination button style', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_pag_align', 'label'=>__( 'Pagination button alignment', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_pag_layout', 'label'=>__( 'Pagination button layout', 'mg_ml' ));		
		
		$indexes[] = array('index'=>'mg_inl_slider_fx', 'label'=>'Inline slider transition effect');
		$indexes[] = array('index'=>'mg_inl_slider_fx_time', 'label'=>__( 'Inline slider transition duration', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_inl_slider_interval', 'label'=>__( 'Inline slider - Slideshow interval', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_inl_slider_no_touch', 'label'=>'Inline slider - disable touchswipe');
		$indexes[] = array('index'=>'mg_inl_slider_pause_on_h', 'label'=>'Inline slider - pause on hover');
		
		$indexes[] = array('index'=>'mg_facebook', 'label'=>__( 'Facebook Button', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_twitter', 'label'=>__( 'Twitter Button', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_pinterest', 'label'=>__( 'Pinterest Button', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_googleplus', 'label'=>__( 'Google+ Button', 'mg_ml' ));
		
		$indexes[] = array('index'=>'mg_deeplinked_elems', 'label'=>'Deeplinked elements');
		$indexes[] = array('index'=>'mg_full_deeplinking', 'label'=>'Use full deeplinking?');
		$indexes[] = array('index'=>'mg_sitemap_baseurl', 'label'=>__('XML sitemap - items base-url', 'mg_ml'), 'type'=>'url');
		
		$indexes[] = array('index'=>'mg_integrate_wc', 'label'=>'Integrate WooCommerce');
		$indexes[] = array('index'=>'mg_wc_hide_add_to_cart', 'label'=>'WooCommerce - Hide add-to-cart button');
		$indexes[] = array('index'=>'mg_wc_hide_attr', 'label'=>'WooCommerce - Hide product attributes');
		
		$indexes[] = array('index'=>'mg_preview_pag', 'label'=>__( 'Preview container', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_builder_behav', 'label'=>'Grid builder - add item behavior');
		$indexes[] = array('index'=>'mg_disable_rclick', 'label'=>__( 'Disable right click', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_kenburns_timing', 'label'=>__("Ken Berns effect's timing", 'mg_ml'), 'type'=>'int');
		
		$indexes[] = array('index'=>'mg_force_inline_css', 'label'=>__( 'Force inline css usage', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_ewpt_force', 'label'=>'EWPT Forcing system');
		$indexes[] = array('index'=>'mg_use_timthumb', 'label'=>'Use TimThumb');
		$indexes[] = array('index'=>'mg_js_head', 'label'=>'Javascript in Head');
		$indexes[] = array('index'=>'mg_rtl_grid', 'label'=>__('Display grids in RTL mode', 'mg_ml' ));
		
		$indexes[] = array('index'=>'mg_lb_loader_radius', 'label'=>__('Lightbox loader - border radius', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_item_width', 'label'=>__('Lightbox percentage width', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_item_maxwidth', 'label'=>__('Lightbox maximum width', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_lb_padding', 'label'=>__( 'Lightbox padding', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_lb_border_w', 'label'=>__( 'Lightbox border width', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_item_radius', 'label'=>__( 'Lightbox border radius', 'mg_ml' ), 'type'=>'int');	
		$indexes[] = array('index'=>'mg_lb_shadow', 'label'=>'Lightbox shadow style');
		$indexes[] = array('index'=>'mg_lb_no_txt_fx', 'label'=>'Disable lightbox showing texts fx');	
		$indexes[] = array('index'=>'mg_modal_lb', 'label'=>'Use Lightbox modal mode');	
		$indexes[] = array('index'=>'mg_lb_touchswipe', 'label'=>'Use touchswipe in lightbox');	
		$indexes[] = array('index'=>'mg_lb_carousel', 'label'=>'Carousel navigation');
		$indexes[] = array('index'=>'mg_lb_socials_style', 'label'=>'Lightbox socials style');
		$indexes[] = array('index'=>'mg_lb_cmd_pos', 'label'=>'Lightbox commands position');
		$indexes[] = array('index'=>'mg_lb_inner_cmd_boxed', 'label'=>'Boxed layout for inner lightbox commands?');
		$indexes[] = array('index'=>'mg_lb_entrance_fx', 'label'=>'Lightbox animations');
		$indexes[] = array('index'=>'mg_lb_bg_fx', 'label'=>'Lightbox background effect');
		$indexes[] = array('index'=>'mg_lb_bg_fx_time', 'label'=>__('Lightbox background effect - timing', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_lb_bg_fx_easing', 'label'=>'Lightbox background effect - easing');
		
		$indexes[] = array('index'=>'mg_video_autoplay', 'label'=>'Video player autoplay');	
		$indexes[] = array('index'=>'mg_audio_autoplay', 'label'=>'Audio player autoplay');
		$indexes[] = array('index'=>'mg_show_tracklist', 'label'=>'Display full tracklistlist');
		$indexes[] = array('index'=>'mg_audio_loop_by_default', 'label'=>'Players to loop by default');

		$indexes[] = array('index'=>'mg_slider_style', 'label'=>'Lightbox slider style');
		$indexes[] = array('index'=>'mg_slider_fx', 'label'=>'Lightbox slider transition effect');
		$indexes[] = array('index'=>'mg_slider_fx_time', 'label'=>__( 'Lightbox slider - transition duration', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_slider_interval', 'label'=>__( 'Lightbox slider - Slideshow interval', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_slider_main_w_val', 'label'=>__( 'Lightbox slider - Global width', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_slider_main_w_type', 'label'=>'Lightbox slider - Global width type');
		
		$indexes[] = array('index'=>'mg_lb_comments', 'label'=>'Lightbox comments');
		$indexes[] = array('index'=>'mg_lbc_disqus_shortname', 'label'=>'Disqus shortname');
		$indexes[] = array('index'=>'mg_lbc_fb_app_id', 'label'=>'FB comments - app ID');
		$indexes[] = array('index'=>'mg_lbc_fb_style', 'label'=>'FB comments - style');
		
		$indexes[] = array('index'=>'mg_loader_color', 'label'=>__( 'Loader color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_cells_border_color', 'label'=>__( 'Cells border color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_img_border_color', 'label'=>__( 'Image Border Color', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_img_border_opacity', 'label'=>__( 'Image Border Opacity', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_main_overlay_color', 'label'=>__( 'Main Overlay Color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_main_overlay_opacity', 'label'=>__( 'Main Overlay Opacity', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_second_overlay_color', 'label'=>__( 'Second Overlay Color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_icons_col', 'label'=>__( 'Icons Color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_overlay_title_color', 'label'=>__( 'Second Overlay Color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_txt_under_color', 'label'=>__('Text under images color', 'mg_ml' ), 'type'=>'hex');

		$indexes[] = array('index'=>'mg_filters_txt_color', 'label'=>__( 'Filters and search Text Color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_filters_bg_color', 'label'=>__( 'Filters and search Background Color', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_filters_border_color', 'label'=>__( 'Filters and search Border Color', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_filters_txt_color_h', 'label'=>__( 'Filters and search Text Color - hover status', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_filters_bg_color_h', 'label'=>__( 'Filters and search Background Color - hover status', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_filters_border_color_h', 'label'=>__( 'Filters and search Border Color - hover status', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_filters_txt_color_sel', 'label'=>__( 'Filters and search Text Color - selected status', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_filters_bg_color_sel', 'label'=>__( 'Filters and search Background Color - selected status', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_filters_border_color_sel', 'label'=>__( 'Filters Border Color - selected status', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_filters_radius', 'label'=>__( 'Filter and search Border Radius', 'mg_ml' ), 'type'=>'int');

		$indexes[] = array('index'=>'mg_item_overlay_color', 'label'=>__( 'Lightbox Overlay color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_item_overlay_opacity', 'label'=>__( 'Lightbox Overlay opacity', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_item_overlay_pattern', 'label'=>__( 'Lightbox Overlay pattern', 'mg_ml' ));
		$indexes[] = array('index'=>'mg_item_bg_color', 'label'=>__( 'Lightbox background color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_item_border_color', 'label'=>__( 'Lightbox border color', 'mg_ml' ), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_item_txt_color', 'label'=>__('Lightbox text color', 'mg_ml'), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_item_hr_color', 'label'=>__('Lightbox separators color', 'mg_ml'), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_item_icons_color', 'label'=>__('Lightbox icons color', 'mg_ml'), 'type'=>'hex');
		$indexes[] = array('index'=>'mg_item_cmd_bg', 'label'=>__('Lightbox commands - background color', 'mg_ml'), 'type'=>'hex');
		
		$indexes[] = array('index'=>'mg_ol_font_size', 'label'=>__( 'Main overlay - font size', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_mobile_ol_font_size', 'label'=>__( 'Main overlay - font size (on mobile)', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_ol_font_family', 'label'=>'Main overlay - font family');
		$indexes[] = array('index'=>'mg_txt_under_font_size', 'label'=>__( 'Title under items - font size', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_mobile_txt_under_font_size', 'label'=>__( 'Title under items - font size (on mobile)', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_txt_under_font_family', 'label'=>'Title under items - font family');
		$indexes[] = array('index'=>'mg_filters_font_size', 'label'=>__( 'Filters and search - font size', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_mobile_filters_font_size', 'label'=>__( 'Filters and search - font size (on mobile)', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_filters_font_family', 'label'=>'Filters and search - font family');
		
		$indexes[] = array('index'=>'mg_lb_title_font_size', 'label'=>__( 'Lightbox title - font size', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_lb_title_line_height', 'label'=>__( 'Lightbox title - line height', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_mobile_lb_title_font_size', 'label'=>__( 'Lightbox title - font size (on mobile)', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_mobile_lb_title_line_height', 'label'=>__( 'Lightbox title - line height (on mobile)', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_lb_title_font_family', 'label'=>'Lightbox title - font family');
		$indexes[] = array('index'=>'mg_lb_txt_font_size', 'label'=>__( 'Lightbox text - font size', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_lb_txt_line_height', 'label'=>__( 'Lightbox text - line height', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_mobile_lb_txt_font_size', 'label'=>__( 'Lightbox text - font size (on mobile)', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_mobile_lb_txt_line_height', 'label'=>__( 'Lightbox text - line height (on mobile)', 'mg_ml' ), 'type'=>'int');
		$indexes[] = array('index'=>'mg_lb_txt_font_family', 'label'=>'Lightbox text - font family');
		
		$indexes[] = array('index'=>'mg_custom_css', 'label'=>__( 'Custom CSS', 'mg_ml' ));
		
		if(is_multisite() && get_option('mg_use_timthumb')) {
			$indexes[] = array('index'=>'mg_wpmu_path', 'label'=>__('JS for old jQuery', 'mg_ml'), 'required'=>true);		
		}

		// type options
		foreach($types as $type => $name) {
			$indexes[] = array('index'=>'mg_'.$type.'_opt_icon', 'label' => $name.' option icon');
			$indexes[] = array('index'=>'mg_'.$type.'_opt', 'label' => $name.' '.__('Options', 'mg_ml'), 'max_len'=>150);	
		}
		
		// woocommerce attributes
		if(isset($wc_attr) && is_array($wc_attr)) {
			foreach($wc_attr as $attr) {
				$indexes[] = array('index'=>'mg_wc_attr_'.sanitize_title($attr->attribute_label).'_icon', 'label' => $attr->attribute_label.' attr icon');	
			}
		}

		//// overlay manager add-on ////////
		if(defined('MGOM_DIR')) {
			$indexes[] = array('index'=>'mg_default_overlay', 'label'=>__( 'Default Overlay', 'mg_ml' ));
		}
		////////////////////////////////////

		$validator->formHandle($indexes);
		$fdata = $validator->form_val;

		// lightbox comments - be sure required data is set
		if(isset($fdata['mg_lb_comments']) && $fdata['mg_lb_comments']) {
			
			if($fdata['mg_lb_comments'] == 'disqus' && empty($fdata['mg_lbc_disqus_shortname'])) {
				$validator->custom_error[__('Disqus shortname', 'mg_ml')] = __('missing value', 'mg_ml');
			}
			else if($fdata['mg_lb_comments'] == 'fb' && empty($fdata['mg_lbc_fb_app_id'])) {
				$validator->custom_error[__('Facebook app ID', 'mg_ml')] = __('missing value', 'mg_ml');	
			}
		}


		// attributes builder custom validation
		foreach($types as $type => $name) {
			if($fdata['mg_'.$type.'_opt']) {
				$a = 0;
				foreach($fdata['mg_'.$type.'_opt'] as $opt_val) {
					if(trim($opt_val) == '') {unset($fdata['mg_'.$type.'_opt'][$a]);}
					$a++;
				}
				
				if( count(array_unique($fdata['mg_'.$type.'_opt'])) < count($fdata['mg_'.$type.'_opt']) ) {
					$validator->custom_error[$name.' '.__('Options', 'mg_ml')] = __('There are duplicate values', 'mg_ml');
				}
			}
		}
		
		$error = $validator->getErrors();
		
		if($error) {echo '<div class="error"><p>'.$error.'</p></div>';}
		else {
			// clean data and save options
			foreach($fdata as $key=>$val) {
				if(!is_array($val)) {
					$fdata[$key] = stripslashes($val);
				}
				else {
					$fdata[$key] = array();
					foreach($val as $arr_val) {$fdata[$key][] = stripslashes($arr_val);}
				}
				
				if($fdata[$key] === false) {delete_option($key);}
				else {
					update_option($key, $fdata[$key]);	
				}
			}
			
			// create frontend.css else print error
			if(!get_option('mg_inline_css')) {
				if(!mg_create_frontend_css()) {
					update_option('mg_inline_css', 1);	
					echo '<div class="updated"><p>'. __('An error occurred during dynamic CSS creation. The code will be used inline anyway', 'mg_ml') .'</p></div>';
				}
				else {delete_option('mg_inline_css');}
			}
			
			echo '<div class="updated"><p><strong>'. __('Options saved.', 'mg_ml') .'</strong></p></div>';
		}
	}
	
	else {  
		// Normal page display
		$fdata['mg_loader'] = get_option('mg_loader');  
		$fdata['mg_cells_margin'] = get_option('mg_cells_margin', 5);  
		$fdata['mg_cells_img_border'] = get_option('mg_cells_img_border', 3);  
		$fdata['mg_cells_radius'] = get_option('mg_cells_radius', 2); 
		$fdata['mg_cells_border'] = get_option('mg_cells_border'); 
		$fdata['mg_cells_shadow'] = get_option('mg_cells_shadow'); 
		$fdata['mg_maxwidth'] = get_option('mg_maxwidth', 1500); 
		$fdata['mg_mobile_treshold'] = get_option('mg_mobile_treshold', 800);
		$fdata['mg_thumb_q'] = get_option('mg_thumb_q', 85);
		$fdata['mg_tu_custom_padding'] = get_option('mg_tu_custom_padding', array('8', '15', '8', '15'));
		$fdata['mg_inl_txt_padding'] = get_option('mg_inl_txt_padding', array('15', '15', '15', '15'));
		$fdata['mg_clean_inl_txt'] = get_option('mg_clean_inl_txt');
		$fdata['mg_delayed_fx'] = get_option('mg_delayed_fx');
		$fdata['mg_hide_overlays'] = get_option('mg_hide_overlays');
		
		$fdata['mg_filters_behav'] = get_option('mg_filters_behav');
		$fdata['mg_filters_align'] = get_option('mg_filters_align');
		$fdata['mg_dd_mobile_filter'] = get_option('mg_dd_mobile_filter');
		$fdata['mg_side_filters_width'] = get_option('mg_side_filters_width', 130);
		$fdata['mg_use_old_filters'] = get_option('mg_use_old_filters');
		$fdata['mg_all_filter_txt'] = get_option('mg_all_filter_txt', 'All');
		$fdata['mg_no_results_txt'] = get_option('mg_no_results_txt', __('no results', 'mg_ml'));
		
		$fdata['mg_pag_style'] = get_option('mg_pag_style');
		$fdata['mg_pag_align'] = get_option('mg_pag_align');
		$fdata['mg_pag_layout'] = get_option('mg_pag_layout');
		
		$fdata['mg_inl_slider_fx'] = get_option('mg_inl_slider_fx', 'fadeslide');
		$fdata['mg_inl_slider_fx_time'] = get_option('mg_inl_slider_fx_time', 400);
		$fdata['mg_inl_slider_interval'] = get_option('mg_inl_slider_interval', 3000);
		$fdata['mg_inl_slider_no_touch'] = get_option('mg_inl_slider_no_touch');
		$fdata['mg_inl_slider_pause_on_h'] = get_option('mg_inl_slider_pause_on_h');

		$fdata['mg_facebook'] = get_option('mg_facebook');
		$fdata['mg_twitter'] = get_option('mg_twitter');  
		$fdata['mg_pinterest'] = get_option('mg_pinterest'); 
		$fdata['mg_googleplus'] = get_option('mg_googleplus'); 
		
		$fdata['mg_deeplinked_elems'] = get_option('mg_deeplinked_elems', array_keys(mg_elem_to_deeplink()) );
		$fdata['mg_full_deeplinking'] = get_option('mg_full_deeplinking');
		$fdata['mg_sitemap_baseurl'] = get_option('mg_sitemap_baseurl', get_site_url());
		
		$fdata['mg_integrate_wc'] = get_option('mg_integrate_wc'); 
		$fdata['mg_wc_hide_add_to_cart'] = get_option('mg_wc_hide_add_to_cart'); 
		$fdata['mg_wc_hide_attr'] = get_option('mg_wc_hide_attr'); 
		
		$fdata['mg_preview_pag'] = get_option('mg_preview_pag'); 
		$fdata['mg_builder_behav'] = get_option('mg_builder_behav'); 
		$fdata['mg_disable_rclick'] = get_option('mg_disable_rclick');
		$fdata['mg_kenburns_timing'] = get_option('mg_kenburns_timing', 9000);
		
		$fdata['mg_force_inline_css'] = get_option('mg_force_inline_css');
		$fdata['mg_ewpt_force'] = get_option('mg_ewpt_force'); 
		$fdata['mg_use_timthumb'] = get_option('mg_use_timthumb'); 
		$fdata['mg_js_head'] = get_option('mg_js_head');  
		$fdata['mg_rtl_grid'] = get_option('mg_rtl_grid'); 
		$fdata['mg_wpmu_path'] = get_option('mg_wpmu_path'); 
		
		$fdata['mg_lb_loader_radius'] = get_option('mg_lb_loader_radius', 18); 
		$fdata['mg_item_width'] = get_option('mg_item_width', 70); 
		$fdata['mg_item_maxwidth'] = get_option('mg_item_maxwidth', 960);
		$fdata['mg_lb_padding'] = get_option('mg_lb_padding', 20);
		$fdata['mg_lb_border_w'] = get_option('mg_lb_border_w', 0);
		$fdata['mg_item_radius'] = get_option('mg_item_radius', 2);
		$fdata['mg_lb_shadow'] = get_option('mg_lb_shadow', 'soft');
		$fdata['mg_lb_no_txt_fx'] = get_option('mg_lb_no_txt_fx');
		$fdata['mg_modal_lb'] = get_option('mg_modal_lb'); 
		$fdata['mg_lb_touchswipe'] = get_option('mg_lb_touchswipe');
		$fdata['mg_lb_carousel'] = get_option('mg_lb_carousel');
		$fdata['mg_lb_socials_style'] = get_option('mg_lb_socials_style');
		$fdata['mg_lb_cmd_pos'] = get_option('mg_lb_cmd_pos');
		$fdata['mg_lb_inner_cmd_boxed'] = get_option('mg_lb_inner_cmd_boxed');
		$fdata['mg_lb_entrance_fx'] = get_option('mg_lb_entrance_fx');
		$fdata['mg_lb_bg_fx'] = get_option('mg_lb_bg_fx', 'genie_b_side');
		$fdata['mg_lb_bg_fx_time'] = get_option('mg_lb_bg_fx_time', 500);
		$fdata['mg_lb_bg_fx_easing'] = get_option('mg_lb_bg_fx_easing', 'ease');
		
		$fdata['mg_video_autoplay'] = get_option('mg_video_autoplay');
		$fdata['mg_audio_autoplay'] = get_option('mg_audio_autoplay');
		$fdata['mg_show_tracklist'] = get_option('mg_show_tracklist');	
		$fdata['mg_audio_loop_by_default'] = get_option('mg_audio_loop_by_default');	
		
		$fdata['mg_slider_style'] = get_option('mg_slider_style');
		$fdata['mg_slider_fx'] = get_option('mg_slider_fx', 'fadeslide');
		$fdata['mg_slider_fx_time'] = get_option('mg_slider_fx_time', 400);
		$fdata['mg_slider_interval'] = get_option('mg_slider_interval', 3000);
		$fdata['mg_slider_main_w_val'] = get_option('mg_slider_main_w_val', 55);
		$fdata['mg_slider_main_w_type'] = get_option('mg_slider_main_w_type', '%');	
		
		$fdata['mg_lb_comments'] = get_option('mg_lb_comments');
		$fdata['mg_lbc_disqus_shortname'] = get_option('mg_lbc_disqus_shortname');
		$fdata['mg_lbc_fb_app_id'] = get_option('mg_lbc_fb_app_id');
		$fdata['mg_lbc_fb_style'] = get_option('mg_lbc_fb_style');
		
		$fdata['mg_loader_color'] = get_option('mg_loader_color', '#888888'); 
		$fdata['mg_cells_border_color'] = get_option('mg_cells_border_color', '#cccccc'); 
		$fdata['mg_img_border_color'] = get_option('mg_img_border_color', '#fdfdfd');  
		$fdata['mg_img_border_opacity'] = get_option('mg_img_border_opacity', 100); 
		$fdata['mg_main_overlay_color'] = get_option('mg_main_overlay_color', '#FFFFFF'); 
		$fdata['mg_main_overlay_opacity'] = get_option('mg_main_overlay_opacity', 80); 
		$fdata['mg_second_overlay_color'] = get_option('mg_second_overlay_color', '#555555');
		$fdata['mg_icons_col'] = get_option('mg_icons_col', '#ffffff'); 
		$fdata['mg_overlay_title_color'] = get_option('mg_overlay_title_color', '#222222');
		$fdata['mg_txt_under_color'] = get_option('mg_txt_under_color', '#333333');
		
		$fdata['mg_filters_txt_color'] = get_option('mg_filters_txt_color', '#444444'); 
		$fdata['mg_filters_bg_color'] = get_option('mg_filters_bg_color', '#ffffff');
		$fdata['mg_filters_border_color'] = get_option('mg_filters_border_color', '#999999'); 
		$fdata['mg_filters_txt_color_h'] = get_option('mg_filters_txt_color_h', '#666666'); 
		$fdata['mg_filters_bg_color_h'] = get_option('mg_filters_bg_color_h', '#ffffff'); 
		$fdata['mg_filters_border_color_h'] = get_option('mg_filters_border_color_h', '#666666');
		$fdata['mg_filters_txt_color_sel'] = get_option('mg_filters_txt_color_sel', '#222222'); 
		$fdata['mg_filters_bg_color_sel'] = get_option('mg_filters_bg_color_sel', '#ffffff'); 
		$fdata['mg_filters_border_color_sel'] = get_option('mg_filters_border_color_sel', '#555555');
		$fdata['mg_filters_radius'] = get_option('mg_filters_radius', 2); 
		
		$fdata['mg_item_overlay_color'] = get_option('mg_item_overlay_color', '#FFFFFF'); 
		$fdata['mg_item_overlay_opacity'] = get_option('mg_item_overlay_opacity', 80); 
		$fdata['mg_item_overlay_pattern'] = get_option('mg_item_overlay_pattern'); 
		$fdata['mg_item_bg_color'] = get_option('mg_item_bg_color', '#FFFFFF'); 
		$fdata['mg_item_border_color'] = get_option('mg_item_border_color', '#e5e5e5'); 
		$fdata['mg_item_txt_color'] = get_option('mg_item_txt_color', '#262626');
		$fdata['mg_item_hr_color'] = get_option('mg_item_hr_color', '#cccccc');
		$fdata['mg_item_icons_color'] = get_option('mg_item_icons_color', '#333333');
		$fdata['mg_item_cmd_bg'] = get_option('mg_item_cmd_bg', '#f1f1f1');
		
		$fdata['mg_ol_font_size'] = get_option('mg_ol_font_size', 14);
		$fdata['mg_mobile_ol_font_size'] = get_option('mg_mobile_ol_font_size', 12);
		$fdata['mg_ol_font_family'] = get_option('mg_ol_font_family');
		$fdata['mg_txt_under_font_size'] = get_option('mg_txt_under_font_size', 15);
		$fdata['mg_mobile_txt_under_font_size'] = get_option('mg_mobile_txt_under_font_size', 13);
		$fdata['mg_txt_under_font_family'] = get_option('mg_txt_under_font_family');
		$fdata['mg_filters_font_size'] = get_option('mg_filters_font_size', 14);
		$fdata['mg_mobile_filters_font_size'] = get_option('mg_mobile_filters_font_size', 12);
		$fdata['mg_filters_font_family'] = get_option('mg_filters_font_family');
		
		$fdata['mg_lb_title_font_size'] = get_option('mg_lb_title_font_size', 20);
		$fdata['mg_lb_title_line_height'] = get_option('mg_lb_title_line_height', 29);
		$fdata['mg_mobile_lb_title_font_size'] = get_option('mg_mobile_lb_title_font_size', 17);
		$fdata['mg_mobile_lb_title_line_height'] = get_option('mg_mobile_lb_title_line_height', 25);
		$fdata['mg_lb_title_font_family'] = get_option('mg_lb_title_font_family');
		$fdata['mg_lb_txt_font_size'] = get_option('mg_lb_txt_font_size', 16);
		$fdata['mg_lb_txt_line_height'] = get_option('mg_lb_txt_line_height', 24);
		$fdata['mg_mobile_lb_txt_font_size'] = get_option('mg_mobile_lb_txt_font_size', 14);
		$fdata['mg_mobile_lb_txt_line_height'] = get_option('mg_mobile_lb_txt_line_height', 22);
		$fdata['mg_lb_txt_font_family'] = get_option('mg_lb_txt_font_family');
		
		$fdata['mg_custom_css'] = get_option('mg_custom_css'); 
		
		//// overlay manager add-on
		if(defined('MGOM_DIR')) {$fdata['mg_default_overlay'] = get_option('mg_default_overlay');}
		//////

		// custom options
		foreach($types as $type => $name) {
			$fdata['mg_'.$type.'_opt_icon'] = get_option('mg_'.$type.'_opt_icon');
			$fdata['mg_'.$type.'_opt'] = get_option('mg_'.$type.'_opt'); 
		}
		
		// woocommerce attributes
		if(isset($wc_attr) && is_array($wc_attr)) {
			foreach($wc_attr as $attr) {
				$fdata['mg_wc_attr_'.sanitize_title($attr->attribute_label).'_icon'] = get_option('mg_wc_attr_'.sanitize_title($attr->attribute_label).'_icon');	
			}
		}
		
		// fix for secondary overlay color v2.3 to v2.4
		if(!preg_match('/^#[a-f0-9]{6}$/i', $fdata['mg_icons_col']) && !isset($_POST['mg_icons_col'])) {$fdata['mg_icons_col'] = '#ffffff';}
	}  
	?>


	<br/>
    <div id="tabs">
    <form name="lcwp_admin" method="post" class="form-wrap" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    	
    <ul class="tabNavigation">
    	<li><a href="#layout_opt"><?php _e('Main Options', 'mg_ml') ?></a></li>
        <li><a href="#lightbox_opt"><?php _e('Lightbox', 'mg_ml') ?></a></li>
        <li><a href="#color_opt"><?php _e('Colors', 'mg_ml') ?></a></li>
        <li><a href="#typography"><?php _e('Typography', 'mg_ml') ?></a></li>
        <li><a href="#opt_builder"><?php _e('Item Attributes', 'mg_ml') ?></a></li>
        <li><a href="#advanced"><?php _e('Custom CSS', 'mg_ml') ?></a></li>
    </ul>    
        
    
    <div id="layout_opt"> 
    	<h3><?php _e("Predefined Styles", 'mg_ml'); ?></h3>
        
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Choose a style", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select data-placeholder="<?php _e('Select a style', 'mg_ml') ?> .." name="mg_pred_styles" id="mg_pred_styles" class="lcweb-chosen" autocomplete="off">
                	<option value="" selected="selected"></option>
                  <?php 
                  $styles = mg_predefined_styles();
                  foreach($styles as $style => $val) { 
				  	echo '<option value="'.$style.'">'.$style.'</option>'; 
				  }
                  ?>
                </select>
            </td>
            <td>
            	<input type="button" name="mg_set_style" id="mg_set_style" value="<?php _e('Set', 'mg_ml') ?>" class="button-secondary" />
            </td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Preview", 'mg_ml'); ?></td>
            <td class="lcwp_field_td" colspan="2">
            	<?php
                foreach(mg_predefined_styles() as $style => $val) { 
				  echo '<img src="'.MG_URL.'/img/pred_styles_demo/'.$val['preview'].'" class="mg_styles_preview" alt="'.$style.'" style="display: none;" />';	
				}
				?>
            </td>
          </tr>
        </table>
        
       
        <h3><?php _e("Grid Layout", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Preloader", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
            	<select name="mg_loader" class="lcweb-chosen" data-placeholder="<?php _e("Select a preloader", 'mg_ml'); ?> .." autocomplete="off">
					<?php
                    foreach(mg_preloader_types() as $key => $val) { 
                        $sel = ($key == $fdata['mg_loader']) ? 'selected="selected"' : '';
                        echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';	
                    }
                    ?>
                </select>
            </td>
            <td><span class="info"><?php _e('Set which preloader to use for grids and lightbox', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Grid Cells Margin", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="25" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_cells_margin']; ?>" name="mg_cells_margin" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Set space between cells', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Image Border Size", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="20" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_cells_img_border']; ?>" name="mg_cells_img_border" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Set cells border size', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Cells Border Radius", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
            	<div class="lcwp_slider" step="1" max="100" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_cells_radius']; ?>" name="mg_cells_radius" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Set cells border radius', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Display outer cell border?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_cells_border'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_cells_border" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked displays the cells external border', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Display cell's shadow?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_cells_shadow'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_cells_shadow" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked displays a soft shadow around cells', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Grid max width", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="50" max="2500" min="850"></div>
                <input type="text" value="<?php echo(int)$fdata['mg_maxwidth']; ?>" name="mg_maxwidth" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Set the maximum width of the grid (used only for thumbnails sharpness, default: 960)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Mobile layout treshold", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="20" max="900" min="500"></div>
                <input type="text" value="<?php echo(int)$fdata['mg_mobile_treshold']; ?>" name="mg_mobile_treshold" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Set browser width treshold to use mobile mode (default: 800)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Thumbnails quality", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="100" min="30"></div>
                <input type="text" value="<?php echo(int)$fdata['mg_thumb_q']; ?>" name="mg_thumb_q" class="lcwp_slider_input" />
                <span>%</span>
            </td>
            <td><span class="info"><?php _e('Set the thumbnail quality. Low value = lighter but fuzzier images (default: 85%)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Title under images - custom padding", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo $fdata['mg_tu_custom_padding'][0]; ?>" name="mg_tu_custom_padding[]" class="lcwp_slider_input" />
                <input type="text" value="<?php echo $fdata['mg_tu_custom_padding'][1]; ?>" name="mg_tu_custom_padding[]" class="lcwp_slider_input" />
                <input type="text" value="<?php echo $fdata['mg_tu_custom_padding'][2]; ?>" name="mg_tu_custom_padding[]" class="lcwp_slider_input" />
                <input type="text" value="<?php echo $fdata['mg_tu_custom_padding'][3]; ?>" name="mg_tu_custom_padding[]" class="lcwp_slider_input" /> px
            </td>
            <td><span class="info"><?php _e('Custom padding values for title under images (top-left-bottom-right) - leave empty to use default one', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Inline texts padding", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo $fdata['mg_inl_txt_padding'][0]; ?>" name="mg_inl_txt_padding[]" class="lcwp_slider_input" />
                <input type="text" value="<?php echo $fdata['mg_inl_txt_padding'][1]; ?>" name="mg_inl_txt_padding[]" class="lcwp_slider_input" />
                <input type="text" value="<?php echo $fdata['mg_inl_txt_padding'][2]; ?>" name="mg_inl_txt_padding[]" class="lcwp_slider_input" />
                <input type="text" value="<?php echo $fdata['mg_inl_txt_padding'][3]; ?>" name="mg_inl_txt_padding[]" class="lcwp_slider_input" /> px
            </td>
            <td><span class="info"><?php _e('Padding values for inline texts (top-left-bottom-right)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Clean inline text box?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_clean_inl_txt'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_clean_inl_txt" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, remove shadows, borders and background for inline text boxes', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Show items without delay?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_delayed_fx'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_delayed_fx" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, show grid items without delayed effect', 'mg_ml') ?></span></td>
          </tr>
		  <tr>
            <td class="lcwp_label_td"><?php _e("Hide overlays?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php $sel = ($fdata['mg_hide_overlays'] == 1) ? 'checked="checked"' : ''; ?>
                <input type="checkbox" value="1" name="mg_hide_overlays" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, hides image overlays on any grid', 'mg_ml') ?></span></td>
          </tr>

          <?php
          //// overlay manager add-on //////////////
		  //////////////////////////////////////////
		  if(defined('MGOM_DIR')) : ?>
          <tr>
            <td class="lcwp_label_td"><?php _e("Default Overlay", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_default_overlay" class="lcweb-chosen" data-placeholder="<?php _e("Select an overlay", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="">(<?php _e('original one', 'mg_ml') ?>)</option>
                  
                  <?php
				  $overlays = get_terms('mgom_overlays', 'hide_empty=0');
				  foreach($overlays as $ol) {
						$sel = ($ol->term_id == $fdata['mg_default_overlay']) ? 'selected="selected"' : '';
						echo '<option value="'.$ol->term_id.'" '.$sel.'>'.$ol->name.'</option>'; 
				  }
				  ?>
                </select>  
            </td>
            <td><span class="info"><?php _e("Choose the default overlay to apply", 'mg_ml'); ?> - overlay manager add-on</span></td>
          </tr>
		  <?php endif;
          //////////////////////////////////////////
          ?>
        </table> 
        
        
        <h3><?php _e("Item filters", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Filtered items behavior", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_filters_behav" class="lcweb-chosen" data-placeholder="<?php _e("Select a style", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="standard"><?php _e('Hide and reorder', 'mg_ml') ?></option>
                  <option value="opacity" <?php if($fdata['mg_filters_behav'] == 'opacity') {echo 'selected="selected"';} ?>><?php _e('Reduce opacity', 'mg_ml') ?></option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select filtered items behavior", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Filters alignment", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_filters_align" class="lcweb-chosen" data-placeholder="<?php _e("Select a style", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="left"><?php _e('Left', 'mg_ml') ?></option>
                  <option value="center" <?php if($fdata['mg_filters_align'] == 'center') {echo 'selected="selected"';} ?>><?php _e('Center', 'mg_ml') ?></option>
                  <option value="right" <?php if($fdata['mg_filters_align'] == 'right') {echo 'selected="selected"';} ?>><?php _e('Right', 'mg_ml') ?></option>
                </select>  
            </td>
            <td><span class="info"><?php _e('Select the filters alignment - for "on top" position', 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Use dropdown on mobile mode?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_dd_mobile_filter'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_dd_mobile_filter" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, replace filters with a dropdown on mobile mode', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Filters on side - column width", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="10" max="350" min="90"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_side_filters_width']; ?>" name="mg_side_filters_width" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Column's width for side filter positions (default: 130)", 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Use old filters style?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_use_old_filters'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_use_old_filters" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, use the old Media Grid filters style', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e('"All" filter text', 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_all_filter_txt']) ?>" name="mg_all_filter_txt" />
            </td>
            <td><span class="info"><?php _e('Set a different text for the "ALL" filter', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e('"no results" text', 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_no_results_txt']) ?>" name="mg_no_results_txt" />
            </td>
            <td><span class="info"><?php _e('Set a different text for message shown when no items are found searching or filtering', 'mg_ml') ?></span></td>
          </tr>
        </table>
        
        
        <h3><?php _e("Pagination Settings", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Style", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_pag_style" class="lcweb-chosen" data-placeholder="<?php _e("Select a style", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="light">Light</option>
                  <option value="dark" <?php if($fdata['mg_pag_style'] == 'dark') {echo 'selected="selected"';} ?>>Dark</option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select the pagination buttons style", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Button alignment", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_pag_align" class="lcweb-chosen" data-placeholder="<?php _e("Select an option", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="center">Center</option>
                  <option value="left" <?php if($fdata['mg_pag_align'] == 'left') {echo 'selected="selected"';} ?>>Left</option>
                  <option value="right" <?php if($fdata['mg_pag_align'] == 'right') {echo 'selected="selected"';} ?>>Right</option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select the pagination button alignment", 'mg_ml'); ?></span></td>
          </tr>	
          <tr>
            <td class="lcwp_label_td"><?php _e("Standard pagination - Layout", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_pag_layout" class="lcweb-chosen" data-placeholder="<?php _e("Select a layout", 'mg_ml'); ?> .." autocomplete="off">
				  <?php	
                  foreach(mg_pag_layouts() as $key => $val) {
                      ($key == $fdata['mg_pag_layout']) ? $sel = 'selected="selected"' : $sel = '';
                      echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                  }
                  ?>
                </select> 
            </td>
            <td><span class="info"><?php _e("Select the layout to use for the standard pagination elements", 'mg_ml'); ?></span></td>
          </tr>	
        </table>
        
        
        <h3><?php _e("Inline slider", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Transition effect", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_inl_slider_fx" class="lcweb-chosen" data-placeholder="<?php _e("Select a transition", 'mg_ml'); ?> .." autocomplete="off">
				  <?php	
                  foreach(mg_inl_slider_fx() as $key => $val) {
					  ($key == $fdata['mg_inl_slider_fx']) ? $sel = 'selected="selected"' : $sel = '';
					  echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                  }
                  ?>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select the transition effect between slides", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Transition duration", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="50" max="1000" min="100"></div>
                <input type="text" value="<?php echo $fdata['mg_inl_slider_fx_time']; ?>" name="mg_inl_slider_fx_time" class="lcwp_slider_input" />
                <span>ms</span>
            </td>
            <td><span class="info"><?php _e("How much time the transition takes (in milliseconds - default 400)", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Slideshow interval", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="500" max="10000" min="1000"></div>
                <input type="text" value="<?php echo $fdata['mg_inl_slider_interval']; ?>" name="mg_inl_slider_interval" class="lcwp_slider_input" />
                <span>ms</span>
            </td>
            <td><span class="info"><?php _e("How long each slide will be shown (in milliseconds - default 3000)", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Disable touchswipe integration?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php $sel = ($fdata['mg_inl_slider_no_touch'] == 1) ? 'checked="checked"' : ''; ?>
                <input type="checkbox" value="1" name="mg_inl_slider_no_touch" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked disables the touchswipe integration', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Pause slideshow on hover?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php $sel = ($fdata['mg_inl_slider_pause_on_h'] == 1) ? 'checked="checked"' : ''; ?>
                <input type="checkbox" value="1" name="mg_inl_slider_pause_on_h" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked pauses the slideshow on mouse hover', 'mg_ml') ?></span></td>
          </tr> 
        </table>   
        
        <h3><?php _e("Socials", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Display the Facebook button?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_facebook'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_facebook" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked displays the Facebook button in lightbox', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Display the Twitter button?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_twitter'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_twitter" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked displays the Twitter button in lightbox', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Display the Pinterest button?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_pinterest'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_pinterest" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked displays the Pinterest button in lightbox', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Display the Google+ button?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_googleplus'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_googleplus" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked displays the Google+ button in lightbox (<strong>only with items deeplinking</strong>)', 'mg_ml') ?></span></td>
          </tr>
        </table> 
        
        <h3><?php _e("Deeplinking <small>(system adding URL parameters for direct linking)</small>", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e('Elements to deeplink', 'mg_ml'); ?></td>
            <td colspan="2">
            	<select name="mg_deeplinked_elems[]" class="lcweb-chosen" data-placeholder="<?php _e("Select an option", 'mg_ml'); ?> .." multiple="multiple" autocomplete="off" style="width: 95%; max-width: 715px;">
                  <?php
				  foreach(mg_elem_to_deeplink() as $key => $val) {	
				  	  $sel = (is_array($fdata['mg_deeplinked_elems']) && in_array($key, $fdata['mg_deeplinked_elems'])) ? 'selected="selected"' : '';
                      echo '<option value="'.$key.'" '.$sel.'>'. $val .'</option>';
				  }
                  ?>
                </select>  
                <p><span class="info"><?php _e('Choose which grid systems will have their direct URL', 'mg_ml'); ?></span></p>
            </td>
          </tr>
          <tr>
              <td class="lcwp_label_td"><?php _e('Use full deeplinking?', 'mg_ml'); ?></td>
              <td class="lcwp_field_td">
                  <?php $sel = ($fdata['mg_full_deeplinking'] == 1) ? 'checked="checked"' : ''; ?>
                  <input type="checkbox" value="1" name="mg_full_deeplinking" class="ip-checkbox" <?php echo $sel; ?> />
              </td>
              <td><span class="info"><?php _e("If checked, a browser's history step is created for grid operations of the same type", 'mg_ml'); ?></span></td>
          </tr>
          <tr><td colspan="3" style="padding-bottom: 0;"></td></tr>
          <tr>
          	<td class="lcwp_label_td"><?php _e('Items XML sitemap location', 'mg_ml') ?></td>
          	<td colspan="2">
            	<a href="<?php echo MG_URL .'/items_xml_sitemap.php' ?>" target="_blank" style="color: #21759b !important;"><strong><?php echo MG_URL .'/items_xml_sitemap.php' ?></strong></a>
            </td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e('XML sitemap - items base-url', 'mg_ml'); ?></td>
            <td colspan="2">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_sitemap_baseurl']) ?>" name="mg_sitemap_baseurl" style="width: 95%; max-width: 715px;" />
                <p><span class="info"><?php _e('Set a custom base-url to use for items link in the sitemap. By default is homepage URL', 'mg_ml'); ?></span></p>
            </td>
          </tr>
        </table>
        
        <?php 
		// woocommerce options
		if($wooc_active) : ?>
            <h3><?php _e("WooCommerce", 'mg_ml'); ?></h3>
            <table class="widefat lcwp_table">
              <tr>
                <td class="lcwp_label_td"><?php _e('Enable WooCommerce integration?', 'mg_ml'); ?></td>
                <td class="lcwp_field_td">
					<?php ($fdata['mg_integrate_wc'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                    <input type="checkbox" value="1" name="mg_integrate_wc" class="ip-checkbox" <?php echo $sel; ?> />
                </td>
                <td><span class="info"><?php _e('If checked allow products usage in grids', 'mg_ml'); ?></span></td>
              </tr>
              <tr>
                <td class="lcwp_label_td"><?php _e('Hide "add to cart" button?', 'mg_ml'); ?></td>
                <td class="lcwp_field_td">
					<?php ($fdata['mg_wc_hide_add_to_cart'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                    <input type="checkbox" value="1" name="mg_wc_hide_add_to_cart" class="ip-checkbox" <?php echo $sel; ?> />
                </td>
                <td><span class="info"><?php _e("If checked hide the AJAX add-to-cart button in lightbox", 'mg_ml'); ?></span></td>
              </tr>
              <tr>
                <td class="lcwp_label_td"><?php _e("Hide product attributes?", 'mg_ml'); ?></td>
                <td class="lcwp_field_td">
					<?php ($fdata['mg_wc_hide_attr'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                    <input type="checkbox" value="1" name="mg_wc_hide_attr" class="ip-checkbox" <?php echo $sel; ?> />
                </td>
                <td><span class="info"><?php _e("If checked hide product attributes in lightbox", 'mg_ml'); ?></span></td>
              </tr>
            </table> 
        <?php endif; ?> 
        
        
        <?php 
		// Timthumb basepath on multisite
		if(is_multisite() && get_option('mg_use_timthumb')) : ?>
            <h3><?php _e("Timthumb basepath", 'mg_ml'); ?> <small>(<?php _e('for MU installations', 'mg_ml') ?>)</small></h3>
            <table class="widefat lcwp_table">
              <tr>
                <td class="lcwp_label_td"><?php _e("Basepath of the WP MU images", 'mg_ml'); ?></td>
                <td>
                    <?php if(!$fdata['mg_wpmu_path'] || trim($fdata['mg_wpmu_path']) == '') { $fdata['mg_wpmu_path'] = mg_wpmu_upload_dir();} ?>
                    <input type="text" value="<?php echo $fdata['mg_wpmu_path'] ?>" name="mg_wpmu_path" style="width: 90%;" />
                    
                    <p class="info" style="margin-top: 3px;">By default is: 
                    	<span style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #727272;"><?php echo mg_wpmu_upload_dir(); ?></span>
                    </p>
                </td>
              </tr> 
            </table> 
        <?php endif; ?>    
        
        <h3><?php _e("Various", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Preview container", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
            	<select name="mg_preview_pag" class="lcweb-chosen" data-placeholder="<?php _e("Select a page", 'mg_ml'); ?> .." autocomplete="off">
                  <option value=""></option>
                  <?php
                  foreach(get_pages() as $pag) {
                      ($fdata['mg_preview_pag'] == $pag->ID) ? $selected = 'selected="selected"' : $selected = '';
                      echo '<option value="'.$pag->ID.'" '.$selected.'>'.$pag->post_title.'</option>';
                  }
                  ?>
                </select>  
            </td>
            <td><span class="info"><?php _e("Choose the page to use as preview container", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Grid builder - add item behavior", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
            	<select name="mg_builder_behav" class="lcweb-chosen" data-placeholder="<?php _e("Select an option", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="append"><?php _e('Append item', 'mg_ml') ?></option>
                  <option value="prepend" <?php if($fdata['mg_builder_behav'] == 'prepend') {echo 'selected="selected"';} ?>><?php _e('Prepend item', 'mg_ml') ?></option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Choose items addition behavior in grid builder", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Disable right click" ); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_disable_rclick'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_disable_rclick" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('Check to disable right click on grid and lightbox images', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Ken Berns effect's timing", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="200" max="20000" min="3000"></div>
                <input type="text" value="<?php echo $fdata['mg_kenburns_timing']; ?>" name="mg_kenburns_timing" class="lcwp_slider_input" />
                <span>ms</span>
            </td>
            <td><span class="info"><?php _e("Set how long Ken Burns effect takes to play once (in milliseconds). <strong>Doesn't</strong> apply to inline slider", 'mg_ml'); ?></span></td>
          </tr>
        </table>  
        
        <h3><?php _e("Advanced", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Use custom CSS inline?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_force_inline_css'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_force_inline_css" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td>
            	<span class="info"><?php _e('If checked, uses custom CSS inline (useful for multisite installations)', 'mg_ml') ?></span>
            </td>
          </tr>
          <tr <?php if($fdata['mg_use_timthumb']) {echo 'style="display: none;"';} ?>>
            <td class="lcwp_label_td"><?php _e("Use Easy WP Thumbs forcing system?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_ewpt_force'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_ewpt_force" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td>
            	<span class="info"><?php _e('Try forcing thumbnails creation, check it ONLY if you note missing thumbnails', 'mg_ml') ?></span>
            </td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Use TimThumb?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_use_timthumb'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_use_timthumb" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td>
            	<span class="info"><?php _e('If checked, use Timthumb instead of Easy WP Thumbs', 'mg_ml') ?></span>
            </td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Use javascript in the head?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_js_head'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_js_head" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td>
            	<span class="info"><?php _e('Put javascript in the website head, check it ONLY IF you notice some incompatibilities', 'mg_ml') ?></span>
            </td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("RTL mode", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_rtl_grid'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_rtl_grid" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td>
            	<span class="info"><?php _e('Displays grids in RTL mode (eg. for hebrew)', 'mg_ml') ?></span>
            </td>
          </tr>
        </table>
        
        <?php if(!get_option('mg_use_timthumb')) {ewpt_wpf_form();} ?>
    </div>



	<div id="lightbox_opt">
    	<h3><?php _e("Item's Lightbox", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Lightbox loader's border radius", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="50" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_lb_loader_radius']; ?>" name="mg_lb_loader_radius" class="lcwp_slider_input" />
                <span>%</span>
            </td>
            <td><span class="info"><?php _e("Use 50% to render a circle - default: 18)", 'mg_ml') ?></span></td>
          </tr>     
          <tr>
            <td class="lcwp_label_td"><?php _e("Elastic width", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="100" min="30"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_item_width']; ?>" name="mg_item_width" class="lcwp_slider_input" />
                <span>%</span>
            </td>
            <td><span class="info"><?php _e('Width percentage of the lightbox in relation to the screen (default: 70)', 'mg_ml') ?></span></td>
          </tr>       
          <tr>
            <td class="lcwp_label_td"><?php _e("Maximum width", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo (int)$fdata['mg_item_maxwidth']; ?>" name="mg_item_maxwidth" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Maximum width in pixels for the lightbox (default: 960)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Padding", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="40" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_lb_padding']; ?>" name="mg_lb_padding" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Set lightbox padding (default 20 - if commands are inside, top padding is 40px)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Border width", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="20" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_lb_border_w']; ?>" name="mg_lb_border_w" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Set the border lightbox border width', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Border radius", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="20" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_item_radius']; ?>" name="mg_item_radius" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Set the border radius for the lightbox corners', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Shadow style", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_lb_shadow" class="lcweb-chosen" data-placeholder="<?php _e("Select a style", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="none"><?php _e('No shadow', 'mg_ml') ?></option>
                  <option value="soft" <?php if($fdata['mg_lb_shadow'] == 'soft') echo 'selected="selected"' ?>><?php _e('Soft', 'mg_ml') ?></option>
                  <option value="heavy" <?php if($fdata['mg_lb_shadow'] == 'heavy') echo 'selected="selected"' ?>><?php _e('Heavy', 'mg_ml') ?></option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select lightbox shadow style", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Disable text showing effect?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_lb_no_txt_fx'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_lb_no_txt_fx" class="ip-checkbox" <?php echo $sel; ?> /> 
            </td>
            <td><span class="info"><?php _e("If checked, disables text showing effect on lightbox opening", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Use as modal?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_modal_lb'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_modal_lb" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td>
            	<span class="info"><?php _e('If checked, only the close button will hide the lightbox', 'mg_ml') ?></span>
            </td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Use touchSwipe?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_lb_touchswipe'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_lb_touchswipe" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, use the touchSwipe navigation on mobile devices', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Carousel mode?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_lb_carousel'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_lb_carousel" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, navigation restart once reached last item', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Socials style", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_lb_socials_style" class="lcweb-chosen" data-placeholder="<?php _e("Select a style", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="squared"><?php _e('Squared', 'mg_ml') ?></option>
                  <option value="rounded" <?php if($fdata['mg_lb_socials_style'] == 'rounded') echo 'selected="selected"' ?>><?php _e('Rounded', 'mg_ml') ?></option>
                  <option value="minimal" <?php if($fdata['mg_lb_socials_style'] == 'minimal') echo 'selected="selected"' ?>><?php _e('Minimal', 'mg_ml') ?></option>
                  <option value="old" <?php if($fdata['mg_lb_socials_style'] == 'old') echo 'selected="selected"' ?>><?php _e('Old style (images)', 'mg_ml') ?></option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select the style for social icons", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Commands position", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_lb_cmd_pos" class="lcweb-chosen" data-placeholder="<?php _e("Select an option", 'mg_ml'); ?> .." autocomplete="off">
                 <?php	
                  foreach(mg_lb_cmd_layouts() as $key => $val) {
                      ($key == $fdata['mg_lb_cmd_pos']) ? $sel = 'selected="selected"' : $sel = '';
                      echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                  }
                  ?>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select lightbox commands position. On mobile, detached will be moved inside", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Boxed layout for inner lightbox commands?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_lb_inner_cmd_boxed'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_lb_inner_cmd_boxed" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Lightbox entrance effect", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_lb_entrance_fx" class="lcweb-chosen" data-placeholder="<?php _e("Select an option", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="static_fade"><?php _e('Static fade', 'mg_ml') ?></option>
                  <option value="slide_bounce" <?php if($fdata['mg_lb_entrance_fx'] == 'slide_bounce') echo 'selected="selected"' ?>><?php _e('Slide and bounce', 'mg_ml') ?></option>
                  <option value="slide_fade" <?php if($fdata['mg_lb_entrance_fx'] == 'slide_fade') echo 'selected="selected"' ?>><?php _e('Slide and fade', 'mg_ml') ?></option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select which lightbox animations to use", 'mg_ml'); ?></span></td>
          </tr>
        </table>  
        
        <h3><?php _e("Lightbox Background", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">   
          <tr>
            <td class="lcwp_label_td"><?php _e("Background showing effect", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_lb_bg_fx" class="lcweb-chosen" data-placeholder="<?php _e("Select an effect", 'mg_ml'); ?> .." autocomplete="off">
				  <?php 
				  foreach(mg_lb_bg_showing_fx() as $fx_id => $fx_name) {
					$sel = ($fdata['mg_lb_bg_fx'] == $fx_id) ? 'selected="selected"' : '';  
					echo '<option value="'.$fx_id.'" '.$sel.'>'. ucfirst($fx_name) .'</option>'; 
				  }
				  ?>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select lightbox background showing effect", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Background effect's duration", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="100" max="2000" min="100"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_lb_bg_fx_time']; ?>" name="mg_lb_bg_fx_time" class="lcwp_slider_input" />
                <span>ms</span>
            </td>
            <td><span class="info"><?php _e('Choose how long background showing animation takes (in milliseconds)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Background effect's easing", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_lb_bg_fx_easing" class="lcweb-chosen" data-placeholder="<?php _e("Select an option", 'mg_ml'); ?> .." autocomplete="off">
				  <?php 
				  foreach(mg_easings() as $e_id => $e_name) {
					$sel = ($fdata['mg_lb_bg_fx_easing'] == $e_id) ? 'selected="selected"' : '';  
					echo '<option value="'.$e_id.'" '.$sel.'>'. ucfirst($e_name) .'</option>'; 
				  }
				  ?>
                </select>  
            </td>
            <td><span class="info"><?php _e("Choose which easing background animation has to use", 'mg_ml'); ?></span></td>
          </tr>
        </table> 
        
       	<h3><?php _e("Audio & video players", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Autoplay videos?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_video_autoplay'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_video_autoplay" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, autoplays lightbox videos', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Autoplay tracks?" ); ?></td>
            <td class="lcwp_field_td">
                <?php ($fdata['mg_audio_autoplay'] == 1) ? $sel = 'checked="checked"' : $sel = ''; ?>
                <input type="checkbox" value="1" name="mg_audio_autoplay" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, autoplays lightbox audio elements', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Show tracklist by default?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php $sel = ($fdata['mg_show_tracklist'] == 1) ? 'checked="checked"' : ''; ?>
                <input type="checkbox" value="1" name="mg_show_tracklist" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, shows the player tracklist by default', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Enable loop by default?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <?php $sel = ($fdata['mg_audio_loop_by_default'] == 1) ? 'checked="checked"' : ''; ?>
                <input type="checkbox" value="1" name="mg_audio_loop_by_default" class="ip-checkbox" <?php echo $sel; ?> />
            </td>
            <td><span class="info"><?php _e('If checked, enables loop function by default', 'mg_ml') ?></span></td>
          </tr> 
        </table>
        
        <h3><?php _e("Lightbox slider", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Style", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_slider_style" class="lcweb-chosen" data-placeholder="<?php _e("Select a style", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="light">Light</option>
                  <option value="dark" <?php if($fdata['mg_slider_style'] == 'dark') {echo 'selected="selected"';} ?>>Dark</option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select the slider style", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Height", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" class="lcwp_slider_input" name="mg_slider_main_w_val" value="<?php echo $fdata['mg_slider_main_w_val']; ?>" maxlength="3">
                <select name="mg_slider_main_w_type" style="width: 50px; margin-left: -5px;">
                  <option value="%">%</option>
                  <option value="px" <?php if($fdata['mg_slider_main_w_type'] == 'px') {echo 'selected="selected"';} ?>>px</option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Default slider height (% is related to the width)", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Transition effect", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_slider_fx" class="lcweb-chosen" data-placeholder="<?php _e("Select a transition", 'mg_ml'); ?> .." autocomplete="off">
                  <?php	
                  foreach(mg_galleria_fx() as $key => $val) {
					  ($key == $fdata['mg_slider_fx']) ? $sel = 'selected="selected"' : $sel = '';
					  echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
                  }
                  ?>
                </select>  
            </td>
            <td><span class="info"><?php _e("Select the transition effect between slides", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Transition duration", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="50" max="1000" min="100"></div>
                <input type="text" value="<?php echo $fdata['mg_slider_fx_time']; ?>" name="mg_slider_fx_time" class="lcwp_slider_input" />
                <span>ms</span>
            </td>
            <td><span class="info"><?php _e("How much time the transition takes (in milliseconds - default 400)", 'mg_ml'); ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Slideshow interval", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="500" max="8000" min="1000"></div>
                <input type="text" value="<?php echo $fdata['mg_slider_interval']; ?>" name="mg_slider_interval" class="lcwp_slider_input" />
                <span>ms</span>
            </td>
            <td><span class="info"><?php _e("How long each slide will be shown (in milliseconds - default 3000)", 'mg_ml'); ?></span></td>
          </tr>
        </table>

        <h3><?php _e("Comments", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Use comments?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_lb_comments" class="lcweb-chosen" data-placeholder="<?php _e("Select a style", 'mg_ml'); ?> .." autocomplete="off">
                  <option value=""><?php _e('No', 'mg_ml') ?></option>
                  <option value="disqus" <?php if($fdata['mg_lb_comments'] == 'disqus') {echo 'selected="selected"';} ?>>Disqus</option>
                  <option value="fb" <?php if($fdata['mg_lb_comments'] == 'fb') {echo 'selected="selected"';} ?>>Facebook</option>
                </select>  
            </td>
            <td><span class="info"><?php _e("Append a comments form after contents?", 'mg_ml'); ?></span></td>
          </tr>
          
          <tr class="mg_lbc_disqus" <?php if($fdata['mg_lb_comments'] != 'disqus') {echo 'style="display: none;"';} ?>>
            <td class="lcwp_label_td"><?php _e("Disqus shortname", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_lbc_disqus_shortname']) ?>" name="mg_lbc_disqus_shortname" maxlength="15" /> 
            </td>
            <td><span class="info"><?php _e("Use the <a href='https://help.disqus.com/customer/portal/articles/466208' target='_blank'>Disqus shortname</a>", 'mg_ml'); ?></span></td>
          </tr>
          
          <tr class="mg_lbc_fb" <?php if($fdata['mg_lb_comments'] != 'fb') {echo 'style="display: none;"';} ?>>
            <td class="lcwp_label_td"><?php _e("Facebook App ID", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_lbc_fb_app_id']) ?>" name="mg_lbc_fb_app_id" maxlength="15" />
            </td>
            <td><span class="info"><?php _e("To use Facebook comments you must have an <a href='https://developers.facebook.com/docs/apps/register' target='_blank'>app ID</a> to use", 'mg_ml'); ?>.<br/><?php _e('Remember to setup moderators <a href="https://developers.facebook.com/tools/comments" target="_blank">here</a>', 'mg_ml') ?></span></td>
          </tr>
          <tr class="mg_lbc_fb" <?php if($fdata['mg_lb_comments'] != 'fb') {echo 'style="display: none;"';} ?>>
            <td class="lcwp_label_td"><?php _e("Form style?", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <select name="mg_lbc_fb_style" class="lcweb-chosen" data-placeholder="<?php _e("Select a style", 'mg_ml'); ?> .." autocomplete="off">
                  <option value="light"><?php _e('Light', 'mg_ml') ?></option>
                  <option value="dark" <?php if($fdata['mg_lbc_fb_style'] == 'dark') {echo 'selected="selected"';} ?>><?php _e('Dark', 'mg_ml') ?></option>
                </select>  
            </td>
            <td><span class="info"></span></td>
          </tr>
    	</table>
	</div>



	<div id="color_opt">
    	<h3><?php _e("Grid Items", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Loader Color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_loader_color']; ?>;"></span>
                	<input type="text" name="mg_loader_color" value="<?php echo $fdata['mg_loader_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Loading animation color', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Cells Outer Border Color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_cells_border_color']; ?>;"></span>
                	<input type="text" name="mg_cells_border_color" value="<?php echo $fdata['mg_cells_border_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('The cells outer border color', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Image Border Color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo mg_rgb2hex($fdata['mg_img_border_color']); ?>;"></span>
                	<input type="text" name="mg_img_border_color" value="<?php echo mg_rgb2hex($fdata['mg_img_border_color']); ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('The cells image border color', 'mg_ml') ?> - <?php _e('accept also "transparent" value', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Image Border Opacity", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="10" max="100" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_img_border_opacity']; ?>" name="mg_img_border_opacity" class="lcwp_slider_input" />
                <span>%</span>
            </td>
            <td><span class="info"><?php _e('Set the CSS3 image border opacity', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Main Overlay Color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_main_overlay_color']; ?>;"></span>
                	<input type="text" name="mg_main_overlay_color" value="<?php echo $fdata['mg_main_overlay_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Color of the main overlay that appears on item mouseover', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Main Overlay Opacity", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="10" max="100" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_main_overlay_opacity']; ?>" name="mg_main_overlay_opacity" class="lcwp_slider_input" />
                <span>%</span>
            </td>
            <td><span class="info"><?php _e('Opacity of the main overlay that appears on item mouseover', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Secondary Overlay Color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_second_overlay_color']; ?>;"></span>
                	<input type="text" name="mg_second_overlay_color" value="<?php echo $fdata['mg_second_overlay_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Color of the secondary overlay that appears on item mouseover', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Secondary Overlay Icons Color", 'mg_ml'); ?></td>
           	<td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_icons_col']; ?>;"></span>
                	<input type="text" name="mg_icons_col" value="<?php echo $fdata['mg_icons_col']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Color of the icons in the secondary overlay', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Overlay Title Color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_overlay_title_color']; ?>;"></span>
                	<input type="text" name="mg_overlay_title_color" value="<?php echo $fdata['mg_overlay_title_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Color of the item title that appear on the main overlay', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Text under images color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_txt_under_color']; ?>;"></span>
                	<input type="text" name="mg_txt_under_color" value="<?php echo $fdata['mg_txt_under_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Text color for "titles under items" mode', 'mg_ml') ?></span></td>
          </tr> 
        </table> 

		<h3><?php _e("Item filters and search bar", 'mg_ml'); ?></h3>
		<table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Text Color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_filters_txt_color']; ?>;"></span>
                	<input type="text" name="mg_filters_txt_color" value="<?php echo $fdata['mg_filters_txt_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Filters and search text color - default status', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Background Color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_filters_bg_color']; ?>;"></span>
                	<input type="text" name="mg_filters_bg_color" value="<?php echo $fdata['mg_filters_bg_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Filters and search background color - default status', 'mg_ml') ?> <?php _e('(not for old style)', 'mg_ml') ?> - <?php _e('accept also "transparent" value', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Border Color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_filters_border_color']; ?>;"></span>
                	<input type="text" name="mg_filters_border_color" value="<?php echo $fdata['mg_filters_border_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Filters and search border color - default status', 'mg_ml') ?> <?php _e('(not for old style)', 'mg_ml') ?> - <?php _e('accept also "transparent" value', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Text Color (on mouse hover)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_filters_txt_color_h']; ?>;"></span>
                	<input type="text" name="mg_filters_txt_color_h" value="<?php echo $fdata['mg_filters_txt_color_h']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Filters and search text color - mouse hover status', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Background Color (on mouse hover)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_filters_bg_color_h']; ?>;"></span>
                	<input type="text" name="mg_filters_bg_color_h" value="<?php echo $fdata['mg_filters_bg_color_h']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Filters and search background color - mouse hover status', 'mg_ml') ?> <?php _e('(not for old style)', 'mg_ml') ?> - <?php _e('accept also "transparent" value', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Border Color (on mouse hover)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_filters_border_color_h']; ?>;"></span>
                	<input type="text" name="mg_filters_border_color_h" value="<?php echo $fdata['mg_filters_border_color_h']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Filters and search border color - mouse hover status', 'mg_ml') ?> <?php _e('(not for old style)', 'mg_ml') ?> - <?php _e('accept also "transparent" value', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Text Color (selected filter)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_filters_txt_color_sel']; ?>;"></span>
                	<input type="text" name="mg_filters_txt_color_sel" value="<?php echo $fdata['mg_filters_txt_color_sel']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Filters and search text color - selected status', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Background Color (selected filter)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_filters_bg_color_sel']; ?>;"></span>
                	<input type="text" name="mg_filters_bg_color_sel" value="<?php echo $fdata['mg_filters_bg_color_sel']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Filters and search background color - selected status', 'mg_ml') ?> <?php _e('(not for old style)', 'mg_ml') ?> - <?php _e('accept also "transparent" value', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td class="lcwp_label_td"><?php _e("Border Color (selected filter)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">   
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_filters_border_color_sel']; ?>;"></span>
                	<input type="text" name="mg_filters_border_color_sel" value="<?php echo $fdata['mg_filters_border_color_sel']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Filters and search border color - selected status', 'mg_ml') ?> <?php _e('(not for old style)', 'mg_ml') ?> - <?php _e('accept also "transparent" value', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Border Radius", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="20" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_filters_radius']; ?>" name="mg_filters_radius" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e('Set the border radius for each filter', 'mg_ml') ?> (<?php _e('not for old style', 'mg_ml') ?>)</span></td>
          </tr>
        </table>  
        
       	<h3><?php _e("Lightbox", 'mg_ml'); ?></h3>
		<table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Overlay color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
				<div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_item_overlay_color']; ?>;"></span>
                	<input type="text" name="mg_item_overlay_color" value="<?php echo $fdata['mg_item_overlay_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Fullscreen lightbox overlay color', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Overlay Opacity", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="10" max="100" min="0"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_item_overlay_opacity']; ?>" name="mg_item_overlay_opacity" class="lcwp_slider_input" />
                <span>%</span>
            </td>
            <td><span class="info"><?php _e('Fullscreen lightbox overlay opacity', 'mg_ml') ?></span></td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Overlay Pattern", 'mg_ml'); ?></td>
            <td class="lcwp_field_td" colspan="2">
            	<input type="hidden" value="<?php echo $fdata['mg_item_overlay_pattern']; ?>" name="mg_item_overlay_pattern" id="mg_item_overlay_pattern" />
            
            	<div class="mg_setting_pattern <?php if(!$fdata['mg_item_overlay_pattern'] || $fdata['mg_item_overlay_pattern'] == 'none') {echo 'mg_pattern_sel';} ?>" id="mgp_none"> no pattern </div>
                
                <?php 
				foreach(mg_patterns_list() as $pattern) {
					($fdata['mg_item_overlay_pattern'] == $pattern) ? $sel = 'mg_pattern_sel' : $sel = '';  
					echo '<div class="mg_setting_pattern '.$sel.'" id="mgp_'.$pattern.'" style="background: url('.MG_URL.'/img/patterns/'.$pattern.') repeat top left transparent;"></div>';		
				}
				?>
            </td>
          </tr>
          
          <tr>
            <td class="lcwp_label_td"><?php _e("Background color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_item_bg_color']; ?>;"></span>
                	<input type="text" name="mg_item_bg_color" value="<?php echo $fdata['mg_item_bg_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Lightbox background color (default: #FFFFFF)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Border color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_item_border_color']; ?>;"></span>
                	<input type="text" name="mg_item_border_color" value="<?php echo $fdata['mg_item_border_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Lightbox border color (default: #E5E5E5)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Text color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_item_txt_color']; ?>;"></span>
                	<input type="text" name="mg_item_txt_color" value="<?php echo $fdata['mg_item_txt_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Lightbox main text color (default: #222222)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Separators color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_item_hr_color']; ?>;"></span>
                	<input type="text" name="mg_item_hr_color" value="<?php echo $fdata['mg_item_hr_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Lightbox blocks separator color (default: #ccc)', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Icons color", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_item_icons_color']; ?>;"></span>
                	<input type="text" name="mg_item_icons_color" value="<?php echo $fdata['mg_item_icons_color']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Color for lightbox commands, loader and social icons (default: #333333)', 'mg_ml') ?></span></td>
          </tr>       
          <tr>
            <td class="lcwp_label_td"><?php _e("Commands background", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: <?php echo $fdata['mg_item_cmd_bg']; ?>;"></span>
                	<input type="text" name="mg_item_cmd_bg" value="<?php echo $fdata['mg_item_cmd_bg']; ?>" />
                </div>
            </td>
            <td><span class="info"><?php _e('Background color for "inside boxed" and "upon" commands (default: #f1f1f1)', 'mg_ml') ?></span></td>
          </tr>      
        </table>  
    </div>
    
    
    
    <div id="typography">
    	<h3><?php _e("Grid Items", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Main overlay - font size", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="25" min="8"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_ol_font_size']; ?>" name="mg_ol_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set the overlay's title font size (default: 14px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Main overlay - font size (on mobile)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="25" min="8"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_mobile_ol_font_size']; ?>" name="mg_mobile_ol_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set the overlay's title font size on mobile (default: 12px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Main overlay - font family", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_ol_font_family']) ?>" name="mg_ol_font_family" />
            </td>
            <td><span class="info"><?php _e("Set the overlay's title font name - leave empty to use the default one", 'mg_ml') ?></span></td>
          </tr>
          <tr><td colspan="3"></td></tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Title under items - font size", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="25" min="8"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_txt_under_font_size']; ?>" name="mg_txt_under_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set the title under font size (default: 15px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Title under items - font size (on mobile)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="25" min="8"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_mobile_txt_under_font_size']; ?>" name="mg_mobile_txt_under_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set the title under font size on mobile (default: 13px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Title under items - font family", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_txt_under_font_family']) ?>" name="mg_txt_under_font_family" />
            </td>
            <td><span class="info"><?php _e("Set the title under font name - leave empty to use the default one", 'mg_ml') ?></span></td>
          </tr>
		</table> 
         
        <h3><?php _e("Filters and search bar", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Filters and search - font size", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="19" min="8"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_filters_font_size']; ?>" name="mg_filters_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set filters and search bar font size (default: 14px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Filters and search - font size (on mobile)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="19" min="8"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_mobile_filters_font_size']; ?>" name="mg_mobile_filters_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set filters and search bar font size on mobile (default: 12px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Filters and search - font family", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_filters_font_family']) ?>" name="mg_filters_font_family" />
            </td>
            <td><span class="info"><?php _e("Set filters and search bar font name - leave empty to use the default one", 'mg_ml') ?></span></td>
          </tr>
		</table>
        
        <h3><?php _e("Item's Lightbox", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's title - font size", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="30" min="14"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_lb_title_font_size']; ?>" name="mg_lb_title_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set item's title font size (default: 20px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's title - line height", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="45" min="15"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_lb_title_line_height']; ?>" name="mg_lb_title_line_height" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set item's title line height (default: 29px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's title - font size (on mobile)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="30" min="14"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_mobile_lb_title_font_size']; ?>" name="mg_mobile_lb_title_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set filters font size on mobile (default: 17px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's title - line height (on mobile)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="45" min="15"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_mobile_lb_title_line_height']; ?>" name="mg_mobile_lb_title_line_height" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set item's title line height on mobile (default: 25px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's title - font family", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_lb_title_font_family']) ?>" name="mg_lb_title_font_family" />
            </td>
            <td><span class="info"><?php _e("Set item's title font name - leave empty to use the default one", 'mg_ml') ?></span></td>
          </tr>
          <tr><td colspan="3"></td></tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's text - font size", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="20" min="8"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_lb_txt_font_size']; ?>" name="mg_lb_txt_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set item's text font size (default: 16px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's text - line height", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="30" min="10"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_lb_txt_line_height']; ?>" name="mg_lb_txt_line_height" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set item's text line height (default: 24px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's text - font size (on mobile)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="20" min="8"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_mobile_lb_txt_font_size']; ?>" name="mg_mobile_lb_txt_font_size" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set filters font size on mobile (default: 14px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's text - line height (on mobile)", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <div class="lcwp_slider" step="1" max="30" min="10"></div>
                <input type="text" value="<?php echo (int)$fdata['mg_mobile_lb_txt_line_height']; ?>" name="mg_mobile_lb_txt_line_height" class="lcwp_slider_input" />
                <span>px</span>
            </td>
            <td><span class="info"><?php _e("Set item's text line height on mobile (default: 22px)", 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td"><?php _e("Item's text - font family", 'mg_ml'); ?></td>
            <td class="lcwp_field_td">
                <input type="text" value="<?php echo mg_sanitize_input($fdata['mg_lb_txt_font_family']) ?>" name="mg_lb_txt_font_family" />
            </td>
            <td><span class="info"><?php _e("Set item's text font name - leave empty to use the default one", 'mg_ml') ?></span></td>
          </tr>
		</table>
    </div>
    
    
    
    <div id="opt_builder">
    <?php 
	// WPML sync button
	if(function_exists('icl_register_string')) {
		echo '
		<p id="mg_wpml_opt_sync_wrap">
			<input type="button" value="'. __('Sync with WPML', 'mg_ml').'" class="button-secondary" />
			<span><em>'. __('Save the options before sync', 'mg_ml') .'</em></span>
		</p>';	
	}
	
	if($wooc_active) {unset($types['woocom']);}
	foreach($types as $type => $name) :
	?>
		<h3 style="border: none;">
			<?php echo $name.' '.__('Attributes', 'mg_ml') ?>
            <a id="opt_<?php echo $type; ?>" class="add_option add-opt-h3"><?php _e('Add option', 'mg_ml') ?></a>
        </h3>
        <table class="widefat lcwp_table" id="<?php echo $type; ?>_opt_table" style="width: 100%; max-width: 450px;">
          <thead>
          <tr>
          	<th style="width: 30px;"><?php _e('Icon', 'mg_ml') ?></th>
            <th><?php _e('Attribute Name', 'mg_ml') ?></th>
            <th></th>
          	<th style="width: 20px;"></th>
            <th style="width: 20px;"></th>
          </tr>
          </thead>
          <tbody>
          	<?php
			if(is_array($fdata['mg_'.$type.'_opt'])) {
				$a = 0;
				foreach($fdata['mg_'.$type.'_opt'] as $type_opt) {
					$icon = (isset($fdata['mg_'.$type.'_opt_icon'][$a])) ? $fdata['mg_'.$type.'_opt_icon'][$a] : '';
					
					echo '
					<tr>
						<td class="mg_type_opt_icon_trigger">
							<i class="fa '.mg_sanitize_input($icon).'" title="set option icon"></i>
							<input type="hidden" name="mg_'.$type.'_opt_icon[]" value="'.mg_sanitize_input($icon).'" autocomplete="off" />
						</td>
						<td class="lcwp_field_td">
							<input type="text" name="mg_'.$type.'_opt[]" value="'.mg_sanitize_input($type_opt).'" maxlenght="150" />
						</td>
						<td></td>
						<td><span class="lcwp_move_row"></span></td>
						<td><span class="lcwp_del_row"></span></td>
					</tr>
					';	
					
					$a++;
				}
			}
			?>
          </tbody>
        </table>
	<?php endforeach; ?>
    
    <?php 
	// WOOCOMMERCE ATTRIBUTES
	if($wooc_active) :
		$type = 'woocom';
	?>
    	<h3 style="border: none;"><?php echo __('WooCommerce Product', 'mg_ml').' '.__('Attributes', 'mg_ml') ?></h3>
        <table class="widefat lcwp_table" id="woocom_opt_table" style="width: 100%; max-width: 450px;">
          <thead>
          <tr>
          	<th style="width: 30px;"><?php _e('Icon', 'mg_ml') ?></th>
            <th><?php _e('Attribute Name', 'mg_ml') ?></th>
          </tr>
          </thead>
          <tbody>
            <?php
			if(is_array($wc_attr)) {
				foreach($wc_attr as $attr) {
					$icon = (isset($fdata['mg_wc_attr_'.sanitize_title($attr->attribute_label).'_icon'])) ? $fdata['mg_wc_attr_'.sanitize_title($attr->attribute_label).'_icon'] : '';
					
					echo '
					<tr>
						<td class="mg_type_opt_icon_trigger">
							<i class="fa '.mg_sanitize_input($icon).'" title="set option icon"></i>
							<input type="hidden" name="mg_wc_attr_'.sanitize_title($attr->attribute_label).'_icon" value="'.mg_sanitize_input($icon).'" />
						</td>
						<td class="lcwp_field_td">
							'. $attr->attribute_label .'
						</td>
					</tr>';	
				}
			}
			?>
          </tbody>
        </table>
    <?php endif; ?>
    
    </div>
    
    <div id="advanced">    
        <h3><?php _e("Custom CSS", 'mg_ml'); ?></h3>
        <table class="widefat lcwp_table">
          <tr>
            <td class="lcwp_field_td">
            	<textarea name="mg_custom_css" style="width: 100%" rows="18"><?php echo $fdata['mg_custom_css']; ?></textarea>
            </td>
          </tr>
        </table>
        
        <h3><?php _e("Elements Legend", 'mg_ml'); ?></h3> 
        <table class="widefat lcwp_table">  
          <tr>
            <td class="lcwp_label_td">.mg_filter</td>
            <td><span class="info">Grid filter container (each filter is a <xmp><a></xmp> element, each separator is a <xmp><span></xmp> element)</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">.mg_grid_wrap</td>
            <td><span class="info">Grid container</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">.mg_box</td>
            <td><span class="info">Single item box</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">.mg_overlay_tit</td>
            <td><span class="info">Main overlay title</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">.mg_title_under</td>
            <td><span class="info">Title under item</span></td>
          </tr>
          		
          <tr>
            <td class="lcwp_label_td">#mg_lb_background</td>
            <td><span class="info">Lightbox - fullscreen background</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">#mg_lb_loader</td>
            <td><span class="info">Lightbox - Item loader during the opening</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">#mg_lb_contents</td>
            <td><span class="info">Lightbox - Item body</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">#mg_close</td>
            <td><span class="info">Lightbox - Close item command</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">#mg_nav</td>
            <td><span class="info">Lightbox - Item navigator container</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">.mg_item_title</td>
            <td><span class="info">Lightbox - Item title</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">.mg_item_text</td>
            <td><span class="info">Lightbox - Item text</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">.mg_cust_options</td>
            <td><span class="info">Lightbox - Item options container (each option is a <xmp><li></xmp> element)</span></td>
          </tr>
          <tr>
            <td class="lcwp_label_td">.mg_socials</td>
            <td><span class="info">Lightbox - Item socials container (each social is a <xmp><li></xmp> element)</span></td>
          </tr>
          
        </table> 
    </div> 
   
   	<input type="hidden" name="pg_nonce" value="<?php echo wp_create_nonce(__FILE__) ?>" /> 
    <input type="submit" name="lcwp_admin_submit" value="<?php _e('Update Options', 'mg_ml' ) ?>" class="button-primary" />  
    
	</form>
    </div>
</div>  


<?php 
// ICONS LIST CODE 
echo mg_fa_icon_picker_code( __('no icon', 'mg_ml') );
?>


<?php // SCRIPTS ?>
<script src="<?php echo MG_URL; ?>/js/functions.js" type="text/javascript"></script>
<script src="<?php echo MG_URL; ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script src="<?php echo MG_URL; ?>/js/lc-switch/lc_switch.min.js" type="text/javascript"></script>
<script src="<?php echo MG_URL; ?>/js/colpick/js/colpick.min.js" type="text/javascript"></script>

<script type="text/javascript" charset="utf8" >
jQuery(document).ready(function($) {

	// lightbox comments toggle
	jQuery(document).delegate('select[name=mg_lb_comments]', 'change', function(e) {
		switch(jQuery(this).val()) {
			
			case 'disqus' :
				jQuery('.mg_lbc_disqus').show();	
				jQuery('.mg_lbc_fb').hide();	
				break;
			
			case 'fb' :
				jQuery('.mg_lbc_fb').show();	
				jQuery('.mg_lbc_disqus').hide();	
				break;
			
			default :
				jQuery('.mg_lbc_disqus, .mg_lbc_fb').hide();	
				break;
		}	
	});
	
	
	///////////////////////////////////
	
	<?php 
	// WPML sync button
	if(function_exists('icl_register_string')) :
	?>
	jQuery('body').delegate('#mg_wpml_opt_sync_wrap input', 'click', function() {
		jQuery('#mg_wpml_opt_sync_wrap span').html('<div style="width: 30px;" class="lcwp_loading"></div>');
		
		var data = {action: 'mg_options_wpml_sync'};
		jQuery.post(ajaxurl, data, function(response) {
			var resp = jQuery.trim(response);
			
			if(resp == 'success') {jQuery('#mg_wpml_opt_sync_wrap span').html('<?php _e('Options synced succesfully', 'mg_ml'); ?>!');}
			else {jQuery('#mg_wpml_opt_sync_wrap span').html('<?php _e('Error syncing', 'mg_ml'); ?> ..');}
		});	
	});
	<?php endif; ?>
	
	
	// set a predefined style 
	jQuery('body').delegate('#mg_set_style', 'click', function() {
		var sel_style = jQuery('#mg_pred_styles').val();
		
		if(confirm('<?php _e('It will overwrite your current settings, continue?', 'mg_ml') ?>') && sel_style != '') {
			var data = {
				action: 'mg_set_predefined_style',
				style: sel_style,
				lcwp_nonce: '<?php echo wp_create_nonce('lcwp_nonce') ?>'
			};
			
			jQuery(this).parent().html('<div style="width: 30px; height: 30px;" class="lcwp_loading"></div>');
			
			jQuery.post(ajaxurl, data, function(response) {
				window.location.href = location.href;
			});	
		}
	});
	
	
	// predefined style  preview toggle
	jQuery('body').delegate('#mg_pred_styles', "change", function() {
		var sel = jQuery('#mg_pred_styles').val();
		
		jQuery('.mg_styles_preview').hide();
		jQuery('.mg_styles_preview').each(function() {
			if( jQuery(this).attr('alt') == sel ) {jQuery(this).fadeIn();}
		});
	});
	
	
	// select a pattern
	jQuery('body').delegate('.mg_setting_pattern', 'click', function() {
		var pattern = jQuery(this).attr('id').substr(4);
		
		jQuery('.mg_setting_pattern').removeClass('mg_pattern_sel');
		jQuery(this).addClass('mg_pattern_sel'); 
		
		jQuery('#mg_item_overlay_pattern').val(pattern);
	});
	
	///////////////////////////////////////////////////////
	
	// launch option icon wizard
	<?php mg_fa_icon_picker_js(); ?>
	
	
	// add options
	jQuery('.add_option').click(function(){
		var type_subj = jQuery(this).attr('id').substr(4);
		
		var optblock = '<tr>\
			<td class="mg_type_opt_icon_trigger">\
				<i class="fa" title="set option icon"></i>\
				<input type="hidden" name="mg_'+type_subj+'_opt_icon[]" value="" />\
			</td>\
			<td class="lcwp_field_td"><input type="text" name="mg_'+type_subj+'_opt[]" maxlenght="150" /></td>\
			<td></td>\
		    <td><span class="lcwp_move_row"></span></td>\
			<td><span class="lcwp_del_row"></span></td>\
		</tr>';

		jQuery('#'+type_subj + '_opt_table tbody').append(optblock);
	});
	
	// remove opt 
	jQuery('body').delegate('.lcwp_del_row', "click", function() {
		if(confirm('<?php _e('Delete the option', 'mg_ml') ?>?')) {
			jQuery(this).parent().parent().slideUp(function() {
				jQuery(this).remove();
			});	
		}
	});
	
	// sort opt
	jQuery('#opt_builder table').each(function() {
        jQuery(this).children('tbody').sortable({ handle: '.lcwp_move_row' });
		jQuery(this).find('.lcwp_move_row').disableSelection();
    });
	
	
	// tabs
	jQuery("#tabs").tabs();
	
	
	//// keep tab shown even on settings save - must be used after tabs initialization
	var lcwp_settings_deeplink = function() {
		var $form = jQuery("#tabs > form").first();
		var form_act = $form.attr('action');
		
		// initial setup
		var init_tab = jQuery('li.ui-tabs-active.ui-state-active a').attr('href');
		$form.attr('action', form_act + init_tab);
		jQuery('html, body').animate({'scrollTop': 0}, 0); // scroll to top
		
		// on click
		jQuery(document.body).delegate('.ui-tabs-nav a', 'click', function() {
			$form.attr('action', form_act + jQuery(this).attr('href'));
		});
	}
	lcwp_settings_deeplink();
});
</script>