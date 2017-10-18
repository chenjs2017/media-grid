<?php
// METABOXES FOR ITEMS EDITING


////////////////////////////////////////////
//// MG ITEMS
// register
function mg_register_metaboxes() {
	add_meta_box('mg_thumb_center_box', __('Thumbnail Center', 'mg_ml'), 'mg_thumb_center_box', 'mg_items', 'side', 'low');
	add_meta_box('mg_search_helper_box', __('Search Helper', 'mg_ml'), 'mg_search_helper_box', 'mg_items', 'side', 'low');
	add_meta_box('mg_item_opt_box', __('Item Options', 'mg_ml'), 'mg_item_opt_box', 'mg_items', 'normal', 'default');
}
add_action('admin_init', 'mg_register_metaboxes');


//////////////////////////
// THUMBNAIL CENTER

function mg_thumb_center_box() {
	require_once(MG_DIR . '/functions.php');	
	global $post;
	
	$tc = get_post_meta($post->ID, 'mg_thumb_center', true);
	if(!$tc) {$tc = 'c';}

	// array of sizes 
	$vals = mg_sizes();
	?>
    <div class="lcwp_sidebox_meta">
        <div class="misc-pub-section">
          <input type="hidden" value="<?php echo $tc; ?>" name="mg_thumb_center" id="mg_thumb_center" />
                
          <table class="mg_sel_thumb_center">
            <tr>
                <td id="mg_tl"></td>
                <td id="mg_t"></td>
                <td id="mg_tr"></td>
            </tr>
            <tr>
                <td id="mg_l"></td>
                <td id="mg_c"></td>
                <td id="mg_r"></td>
            </tr>
            <tr>
                <td id="mg_bl"></td>
                <td id="mg_b"></td>
                <td id="mg_br"></td>
            </tr>
          </table>
        </div>
    </div>

    <script type="text/javascript">
	jQuery(document).ready(function() {
		var mg_thumb_center = function(position) {
			jQuery('.mg_sel_thumb_center td').removeClass('thumb_center');
			jQuery('.mg_sel_thumb_center #mg_'+position).addClass('thumb_center');
			
			jQuery('#mg_thumb_center').val(position);	
		}
		mg_thumb_center( jQuery('#mg_thumb_center').val() );
		
		jQuery('body').delegate('.mg_sel_thumb_center td', 'click', function() {
			var new_position = jQuery(this).attr('id').substr(3);
			mg_thumb_center(new_position);
		});		
	});
    </script>
 
	<?php
	return true;		
}


//////////////////////////
// SEARCH HELPER

function mg_search_helper_box() {
	global $post;
	$helper = get_post_meta($post->ID, 'mg_search_helper', true);

	// array of sizes 
	$vals = mg_sizes();
	?>
    <div class="lcwp_sidebox_meta">
        <div class="misc-pub-section">
          <textarea name="mg_search_helper" rows="2" style="width: 100%;"><?php echo $helper ?></textarea>
        </div>
    </div>
	<?php
	return true;	
}



//////////////////////////
// ITEM OPTIONS

function mg_item_opt_box() {
	require_once(MG_DIR . '/functions.php');
	global $post;
	
	$main_type = get_post_meta($post->ID, 'mg_main_type', true);
	if(!$main_type) {$main_type = '';}
	
	$item_layout = get_post_meta($post->ID, 'mg_layout', true);
	$lb_maxwidth = get_post_meta($post->ID, 'mg_lb_max_w', true);
	$img_maxheight = get_post_meta($post->ID, 'mg_img_maxheight', true);
	?>
    <div id="mg_item_meta_wrap" class="lcwp_mainbox_meta">
		<div id="mg_item_meta_type_choser">
            <span><?php _e("Item Type", 'mg_ml'); ?></span>
            <select data-placeholder="<?php _e('Select type', 'mg_ml') ?> .." name="mg_main_type" id="mg_main_type" class="lcweb-chosen" autocomplete="off">
			  <?php 
              $types = mg_item_types();
              if(isset($types['woocom'])) {unset( $types['woocom'] );}
              
              foreach($types as $key => $val) {
                  ($key == $main_type) ? $sel = 'selected="selected"' : $sel = '';
                  echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>'; 
              }
              ?>
        	</select>
        </div> 
      	
        <div id="mg_item_meta_f_wrap">
			<?php 
			include_once(MG_DIR .'/classes/items_meta_fields.php');
			
			$imf_type = (empty($main_type)) ? 'simple_img' : $main_type;
			$imf = new mg_meta_fields($post->ID, $imf_type);
			
			echo $imf->get_fields_code();
			$imf->echo_type_js_code();
			?>
        </div>
    </div>
    
    <?php // security nonce ?>
    <input type="hidden" name="mg_item_noncename" value="<?php echo wp_create_nonce('lcwp_nonce') ?>" />
    
    <?php // ////////////////////// ?>
    
    <?php // SCRIPTS ?>
    <script src="<?php echo MG_URL; ?>/js/colpick/js/colpick.min.js" type="text/javascript"></script>
    <script src="<?php echo MG_URL; ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo MG_URL; ?>/js/lc-switch/lc_switch.min.js" type="text/javascript"></script>
    
    <script type="text/javascript">
	jQuery(document).ready(function($) {
		mg_is_acting = false;
		var lcwp_nonce = '<?php echo wp_create_nonce('lcwp_nonce') ?>';
		
		// lightbox live preview
		<?php 
		if(!empty($main_type) && !in_array($main_type, array('simple_img','link','inl_slider', 'inl_audio', 'inl_video','inl_text','spacer'))) : ?>
			var lb_preview_link = 
			'<div class="misc-pub-section-last">'+
				'<a href="<?php echo site_url(); ?>?mgi_=<?php echo $post->ID; ?>" target="_blank" id="mg_item_preview_link"><?php echo addslashes(__("Item's lightbox preview", 'mg_ml')) ?> &raquo;</a>'+
			'</div>';
			
			jQuery('#major-publishing-actions').addClass('misc-pub-section');
			jQuery('#submitpost').parent().append(lb_preview_link);
		<?php endif; ?>
		

		// item type switch
		jQuery(document).delegate('#mg_main_type', "change", function (e) {
			if(<?php if(empty($main_type)) {echo '1 == 1 || ';} ?> confirm("<?php _e("Changing item type unsaved data will be lost. Continue?", 'mg_ml') ?>")) {
				
				// loader and new options
				var $wrap = jQuery('#mg_item_meta_f_wrap');
				$wrap.html('<div style="height: 100px; background: url(<?php echo MG_URL ?>/img/loader_big.gif) no-repeat center center transparent;"></div>');
				
				var data = {
					action: 	'mg_item_meta_fields',
					item_id:	<?php echo $post->ID ?>,
					item_type: 	jQuery(this).val(),
					lcwp_nonce:	lcwp_nonce
				};
				jQuery.post(ajaxurl, data, function(response) {
					$wrap.html(response);
					
					mg_colpick();
					mg_slider_opt();
					mg_live_chosen();
					mg_live_checks();
				});			
			}
		});


		/////////////////////////////////////////////////////////////////////

		//// custom icon - picker
		<?php mg_fa_icon_picker_js(); ?>

		/////////////////////////////////////////////////////////////////////


		//// custom file uploader for gallery and audio
		mg_TB = 0;
		
		// open tb and hide tabs
		jQuery('body').delegate('.mg_TB', 'click', function(e) {
			
			if( jQuery(this).hasClass('mg_upload_img') ) {mg_TB_type = 'img';}
			else {mg_TB_type = 'audio';}
			
			// thickBox
			if(typeof(wp.media) == 'undefined') {
				mg_TB = 1;
				post_id = jQuery('#post_ID').val();
				
				if(mg_TB_type == 'img') {
					tb_show('', '<?php echo admin_url(); ?>media-upload.php?post_id='+post_id+'&amp;type=image&amp;TB_iframe=true');
				}
				else {
					tb_show('', '<?php echo admin_url(); ?>media-upload.php?post_id='+post_id+'&amp;type=audio&amp;TB_iframe=true');	
				}
				
				mg_media_man = setInterval(function() {
					if(mg_TB == 1) {
						if( jQuery('#TB_iframeContent').contents().find('#tab-type_url').is('hidden') ) { return false;	}
						
						jQuery('#TB_iframeContent').contents().find('#tab-type_url').hide();
						jQuery('#TB_iframeContent').contents().find('#tab-gallery').hide();
						clearInterval(mg_media_man);
					}
				}, 1);
			}
			
			// new lightbox management
			else {
				e.preventDefault();
				var title = (mg_TB_type == 'img') ? 'Image' : 'Audio';
				var subj = (mg_TB_type == 'img') ? 'image' : 'audio';
				
				var custom_uploader = wp.media({
					title: 'WP '+ title +' Management',
					button: { text: 'Ok' },
					library : { type : subj},
					multiple: false
				})
				.on('select close', function() {
					if(mg_TB_type == 'img') { 
						mg_load_img_picker(1); 
						mg_sel_img_reload();
					}
					else {
						mg_load_audio_picker(1);	
						mg_sel_tracks_reload();
					}
				})
				.open();	
			}
		});

		// reload picker on thickbox unload
		jQuery(window).bind('tb_unload', function() {
			if(mg_TB == 1) {
				if(mg_TB_type == 'img') { 
					mg_load_img_picker(1); 
					mg_sel_img_reload();
				}
				else {
					mg_load_audio_picker(1);	
					mg_sel_tracks_reload();
				}
				
				mg_TB = 0;		
			}
		});

	
		////////////////////////
		
		
		//// images & audio
		// remove item
		jQuery('body').delegate('#gallery_img_wrap ul li span, #audio_tracks_wrap ul li span', 'click', function() {
			jQuery(this).parent().remove();	
			
			if( jQuery('#gallery_img_wrap ul li').size() == 0 ) {jQuery('#gallery_img_wrap ul').html('<p><?php echo mg_sanitize_input( __('No images selected', 'mg_ml')) ?>  .. </p>');}
			if( jQuery('#audio_tracks_wrap ul li').size() == 0 ) {jQuery('#audio_tracks_wrap ul').html('<p><?php echo mg_sanitize_input( __('No tracks selected', 'mg_ml')) ?> .. </p>');}
		});
		
		
		// sort items
		 mg_sort = function() { 
			jQuery( "#gallery_img_wrap ul, #audio_tracks_wrap ul" ).sortable();
			jQuery( "#gallery_img_wrap ul, #audio_tracks_wrap ul" ).disableSelection();
		}
		mg_sort();
		

		// fix for chosen overflow
		jQuery('#wpbody, #wpbody-content').css('overflow', 'visible');
		
		// fix for subcategories
		jQuery('#mg_item_categories-adder').remove();
	});
	</script>
       
    <?php	
	return true;	
}






//////////////////////////
// SAVING METABOXES

function mg_items_meta_save($post_id) {
	if(isset($_POST['mg_item_noncename'])) {
		// authentication checks
		if (!wp_verify_nonce($_POST['mg_item_noncename'], 'lcwp_nonce')) return $post_id;
		if (!current_user_can('edit_post', $post_id)) return $post_id;
		
		
		include_once(MG_DIR.'/functions.php');
		include_once(MG_DIR.'/classes/simple_form_validator.php');
		include_once(MG_DIR .'/classes/items_meta_fields.php');
				
		$validator = new simple_fv;
		$indexes = array();
		
		$indexes[] = array('index'=>'mg_thumb_center', 'label'=>'Thumbnail Center');
		$indexes[] = array('index'=>'mg_search_helper', 'label'=>'Search Helper');
		$indexes[] = array('index'=>'mg_main_type', 'label'=>'Item Type');
		
		// type options
		if(isset($_POST['mg_main_type']) && !empty($_POST['mg_main_type'])) {
			$imf = new mg_meta_fields($post_id, $_POST['mg_main_type']);
			$indexes = array_merge($indexes, (array)$imf->get_fields_validation());
		}
		
		$validator->formHandle($indexes);
		
		$fdata = $validator->form_val;
		$error = $validator->getErrors();

		// clean data
		foreach($fdata as $key=>$val) {
			if(!is_array($val)) {
				$fdata[$key] = stripslashes($val);
			}
			else {
				$fdata[$key] = array();
				foreach($val as $arr_val) {$fdata[$key][] = stripslashes($arr_val);}
			}
		}

		// save data
		foreach($fdata as $key=>$val) {
			
			// search helper - sanitize
			if($key == 'mg_search_helper') {
				$fdata[$key] = str_replace(array('"', '<', '>'), '', $fdata[$key]);	
			}
			
			update_post_meta($post_id, $key, $fdata[$key]); 
		}
		
		// update grid categories
		mg_upd_item_upd_grids($post_id);
	}

    return $post_id;
}
add_action('save_post', 'mg_items_meta_save');




//////////////////////////
// WARNING IF FEATURED IMAGE IS NOT SET

add_action('admin_notices', 'mg_item_featured_image');
function mg_item_featured_image(){
	global $current_screen;
	
	if ($current_screen->id == 'mg_items' && $current_screen->parent_base == 'edit') {
     	global $post;
		$main_type = get_post_meta($post->ID, 'mg_main_type', true);

		if(!in_array($main_type, array('inl_slider','inl_video','post_contents','inl_text','spacer')) && get_the_post_thumbnail($post->ID) == '') {
			echo '<div class="error"><p>'. __('Warning - This item has not a featured image', 'mg_ml') .'</p></div>';		
		}
	}
}
