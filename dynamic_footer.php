<?php
// global vars and mediaelement inclusion if not in the page - prevent right click


// frontent JS on header or footer
if(!get_option('mg_js_head')) {
	add_action('wp_footer', 'mg_dynamic_js', 90);
} else { 
	add_action('wp_head', 'mg_dynamic_js', 850);
}

function mg_dynamic_js() {
	include_once(MG_DIR.'/functions.php');
	
	$delayed_fx 	= (get_option('mg_delayed_fx')) ? 'false' : 'true';
	$modal_class 	= (get_option('mg_modal_lb')) ? 'mg_modal_lb' : 'mg_classic_lb';
	$box_border 	= (get_option('mg_cells_border')) ? 1 : 0;
	$lb_vert_center = (get_option('mg_lb_not_vert_center')) ? 'false' : 'true';
	$lb_touchswipe 	= (get_option('mg_lb_touchswipe')) ? 'true' : 'false';
	$woocom 		= (mg_woocomm_active()) ? 'true' : 'false';
	?>
	<script type="text/javascript">
	// Media Grid global dynamic vars
	mg_boxMargin = <?php echo (int)get_option('mg_cells_margin') ?>;
	mg_boxBorder = <?php echo $box_border ?>;
	mg_imgPadding = <?php echo (int)get_option('mg_cells_img_border') ?>;
	mg_delayed_fx = <?php echo $delayed_fx ?>;
	mg_filters_behav = '<?php echo get_option('mg_filters_behav', 'standard') ?>';
	mg_lightbox_mode = "<?php echo $modal_class ?>";
	mg_lb_carousel = <?php echo (get_option('mg_lb_carousel')) ? 'true' : 'false'; ?>;
	mg_lb_touchswipe = <?php echo $lb_touchswipe ?>;
	mg_audio_loop = <?php echo (get_option('mg_audio_loop_by_default')) ? 'true' : 'false'; ?>;
	mg_rtl = <?php echo (get_option('mg_rtl_grid')) ? 'false' : 'true'; ?>;
	mg_mobile = <?php echo get_option('mg_mobile_treshold', 800) ?>; 
	
	mg_no_results_txt = "<?php echo addslashes(get_option('mg_no_results_txt', __('no results', 'mg_ml'))) ?>";
	mg_deeplinked_elems = ['<?php echo implode("','", (array)get_option('mg_deeplinked_elems', array_keys(mg_elem_to_deeplink()) )) ?>'];
	mg_full_deeplinking = <?php echo (get_option('mg_full_deeplinking')) ? 'true' : 'false'; ?>;

	// Galleria global vars
	mg_galleria_fx = '<?php echo get_option('mg_slider_fx', 'fadeslide') ?>';
	mg_galleria_fx_time = <?php echo get_option('mg_slider_fx_time', 400) ?>; 
	mg_galleria_interval = <?php echo get_option('mg_slider_interval', 3000) ?>;
	
    // LC micro slider vars
	mg_inl_slider_fx = '<?php echo get_option('mg_inl_slider_fx', 'fadeslide') ?>';
	mg_inl_slider_fx_time = <?php echo get_option('mg_inl_slider_fx_time', 400) ?>; 
	mg_inl_slider_intval = <?php echo get_option('mg_inl_slider_interval', 3000) ?>;
	mg_inl_slider_touch = <?php echo (get_option('mg_inl_slider_no_touch')) ? 'false' : 'true'; ?>;
	mg_inl_slider_pause_on_h = <?php echo (get_option('mg_inl_slider_pause_on_h')) ? 'true' : 'false'; ?>;
	mg_kenburns_timing = <?php echo (int)get_option('mg_kenburns_timing', 9000) ?>;
    </script>	
	<?php
    
	// if prevent right click
	if(get_option('mg_disable_rclick')) :
		?>
        <script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('body').delegate('.mg_grid_wrap *, #mg_lb_wrap *, #mg_wp_video_wrap .wp-video *, #mg_lb_contents img, #mg_lb_contents .wp-video *', "contextmenu", function(e) {
                e.preventDefault();
            });
		});
		</script>
        <?php	
	endif;
}



/* add mediaelement only if not used before - quick edit link for WP users */
function mg_dynamic_mediael() {
	?>
    <script type="text/javascript">
	if(typeof(MediaElementPlayer) != 'function') {	
		jQuery(document).ready(function(e) {
            
			var s = document.createElement("script");
				
			s.type = "text/javascript";
			s.id = "mediaelement-js";
			s.src = "<?php echo MG_URL ?>/js/mediaelement/mediaelement-and-player.min.js";
			
			var head = document.getElementsByTagName('head');	
			jQuery('head').append("<link rel='stylesheet' href='<?php echo MG_URL ?>/js/mediaelement/mediaelementplayer.min.css' type='text/css' media='all' />");
	
			var body = document.getElementsByTagName('body');
			jQuery('body').append(s);
		});
	}
	
	<?php
	// logged users helper - direct link to edit items
	if(current_user_can('edit_posts'))  : ?>
	jQuery(document).ready(function() {
		jQuery(document).delegate('.mg_box', 'mouseenter', function() {  //==>> mouseenter mouseleave implementation
			var iid = jQuery(this).attr("rel").substr(4);
			if(jQuery('#mg_quick_edit_btn.mgqeb_'+iid).length) {return false;}
			
			if(typeof(mg_remove_qeb) != 'undefined') {clearTimeout(mg_remove_qeb);}
			if(jQuery('#mg_quick_edit_btn').length) {jQuery('#mg_quick_edit_btn').remove();}
			
			var item_pos = jQuery(this).offset();
			var item_padding = parseInt( jQuery(this).css('padding-top'));
			var css_pos = 'style="top: '+ (item_pos.top + item_padding) +'px; left: '+ (item_pos.left + item_padding) +'px;"';
			
			var link = "<?php echo admin_url() ?>post.php?post="+ iid +"&action=edit";
			var icon = '<i class="fa fa-pencil" aria-hidden="true"></i>';
			
			jQuery('body').append('<a id="mg_quick_edit_btn" class="ggqeb_'+iid+'" href="'+ link +'" target="_blank" title="<?php _e('edit', 'mg_ml') ?>" '+css_pos+'>'+ icon +'</>');		
		});
		jQuery(document).delegate('.mg_box', 'mouseleave', function() {
			if(typeof(mg_remove_qeb) != 'undefined') {clearTimeout(mg_remove_qeb);}
			mg_remove_qeb = setTimeout(function() {
				if(jQuery('#mg_quick_edit_btn').length) {jQuery('#mg_quick_edit_btn').remove();}
			}, 700);
		});
	});
	<?php endif; ?>
	</script>
    <?php	
}
add_action('wp_footer', 'mg_dynamic_mediael', 9999);



/* custom item icons */
function mg_items_cust_icon_css() {
	if(!isset($GLOBALS['mg_items_cust_icon']) || !is_array($GLOBALS['mg_items_cust_icon']) || !count($GLOBALS['mg_items_cust_icon'])) {
		return false;	
	}
	
	include_once(MG_DIR . '/classes/lc_font_awesome_helper.php');
	$fa = new lc_fontawesome_helper;
	
	echo '<style type="text/css">';
    
	foreach($GLOBALS['mg_items_cust_icon'] as $item_id => $icon_id) {
		$icon_id = str_replace('fa-', '', $icon_id);
		
		echo '
		.mgi_'.$item_id.' .cell_more span:before, 
		.mgi_'.$item_id.' .mgom_subj_icon span:before {
			font-family: FontAwesome !important;
			content: "\\'. $fa->icons[$icon_id]->unicode .'" !important;
		}
		.mgi_'.$item_id.' .cell_more span:before {
			font-size: 18px !important;	
		}'; 	
	}
	
	echo '</style>';
}
add_action('wp_footer', 'mg_items_cust_icon_css', 1);
