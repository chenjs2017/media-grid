<?php
// METABOX FOR WOOCOMM PRODUCTS


// register
function mg_wc_metabox() {
	add_meta_box('mg_wc_thumb_center', __('Media Grid - Thumbnail Center', 'mg_ml'), 'mg_thumb_center_box', 'product', 'side', 'low');
	add_meta_box('mg_woocom_box', __('Media Grid Integration', 'mg_ml'), 'mg_woocom_box', 'product', 'normal', 'default');
}
add_action('admin_init', 'mg_wc_metabox');


//////////////////////////
// CONTENTS MANAGEMENT OPTIONS

function mg_woocom_box() {
	include_once(MG_DIR . '/functions.php');
	global $post;
	
	$enable_prod = get_post_meta($post->ID, 'mg_main_type', true); // simulate MG item to use single WP_query
	$item_layout = get_post_meta($post->ID, 'mg_layout', true);
	$lb_maxwidth = get_post_meta($post->ID, 'mg_lb_max_w', true);
	$prod_cats = get_post_meta($post->ID, 'mg_wc_prod_cats', true); if(!is_array($prod_cats)) {$prod_cats = array();}
	$link_only = get_post_meta($post->ID, 'mg_link_only', true);
	
	// single image
	$img_maxheight = get_post_meta($post->ID, 'mg_img_maxheight', true);
	
	// gallery
	$incl_featured = get_post_meta($post->ID, 'mg_slider_add_featured', true);
	$h_val = get_post_meta($post->ID, 'mg_slider_w_val', true);
	$h_type = get_post_meta($post->ID, 'mg_slider_w_type', true);
	
	if(!$h_val) {$h_val = get_option('mg_slider_main_w_val', 55);}
	if(!$h_type) {$h_type = $def_h_type = get_option('mg_slider_main_w_type', '%');}
	
	?>
    <div id="mg_item_meta_wrap" class="lcwp_mainbox_meta">
		<div id="mg_item_meta_type_choser">
            <span><?php _e("Enable product for grids?", 'mg_ml'); ?></span>
            <select data-placeholder="<?php _e('Select a layout', 'mg_ml') ?> .." name="mg_main_type" id="mg_wc_enable_prod" class="lcweb-chosen" autocomplete="off"> <?php // KEEP THIS NAME  ?>
              <option value="wc_auto">(<?php _e('as default', 'mg_ml') ?>)</option>
              <option value="wc_yes" <?php if($enable_prod == 'wc_yes') echo 'selected="selected"';?>><?php _e('Yes', 'mg_ml') ?></option>
              <option value="wc_no" <?php if($enable_prod == 'wc_no') echo 'selected="selected"';?>><?php _e('No', 'mg_ml') ?></option>
            </select>
            <span style="color: #969696; font-style: italic; padding-left: 20px;"><?php _e("Individual product enabling for grid usage - overrides global option", 'mg_ml'); ?></span>
        </div> 
      	
        <div id="mg_item_meta_f_wrap" <?php if($enable_prod == 'wc_no') {echo 'style="display: none;"';} ?>>
        	<?php 
			include_once(MG_DIR .'/classes/items_meta_fields.php');
			
			$imf_type = (empty($main_type)) ? 'simple_img' : $main_type;
			$imf = new mg_meta_fields($post->ID, 'woocomm');
			
			echo $imf->get_fields_code();
			$imf->echo_type_js_code();
			?>
        </div>
        
    </div>
    
    
    
    <?php /*
    <div class="lcwp_mainbox_meta">  
      <table class="widefat lcwp_table lcwp_metabox_table">
        <tr>
          <td class="lcwp_label_td"><?php _e("Enable product for grids?", 'mg_ml'); ?></td>
          <td class="lcwp_field_td">
              <select data-placeholder="<?php _e('Select a layout', 'mg_ml') ?> .." name="mg_main_type" id="mg_wc_enable_prod" class="lcweb-chosen" autocomplete="off">
                <option value="wc_auto">(<?php _e('as default', 'mg_ml') ?>)</option>
                <option value="wc_yes" <?php if($enable_prod == 'wc_yes') echo 'selected="selected"';?>><?php _e('Yes', 'mg_ml') ?></option>
                <option value="wc_no" <?php if($enable_prod == 'wc_no') echo 'selected="selected"';?>><?php _e('No', 'mg_ml') ?></option>
              </select>
          </td>     
          <td><span class="info"><?php _e("Individual product enabling for grid usage - overrides global option", 'mg_ml'); ?></span></td>
        </tr>
      </table>
      
      <div id="mg_wc_opt_wrap" <?php if($enable_prod !== false && $enable_prod == 'wc_no') {echo 'style="display: none;"';} ?>>
      <table class="widefat lcwp_table lcwp_metabox_table" style="margin-top: -8px;">
        <tr>
          <td class="lcwp_label_td"><?php _e("Lightbox Layout", 'mg_ml'); ?></td>
          <td class="lcwp_field_td">
              <select data-placeholder="<?php _e('Select a layout', 'mg_ml') ?> .." name="mg_layout" class="lcweb-chosen" autocomplete="off">
                <option value="full" <?php if($item_layout == 'full') echo 'selected="selected"';?>><?php _e('Full Width', 'mg_ml') ?></option>
                <option value="side" <?php if($item_layout == 'side') echo 'selected="selected"';?>><?php _e('Text on side', 'mg_ml') ?></option>
              </select>
          </td>     
          <td><span class="info"></span></td>
        </tr>
        <tr>
          <td class="lcwp_label_td"><?php _e("Lightbox content max-width", 'mg_ml'); ?></td>
          <td class="lcwp_field_td">
              <input type="text" name="mg_lb_max_w" value="<?php echo ((int)$lb_maxwidth == 0) ? '' : $lb_maxwidth; ?>" maxlength="4" style="width: 50px;"  /> px
          </td>     
          <td><span class="info"><?php _e('Leave blank to fit the content to the lightbox size', 'mg_ml') ?></span></td>
        </tr>
        <tr>
          <td class="lcwp_label_td"><?php _e("Product categories", 'mg_ml'); ?></td>
          <td class="lcwp_field_td" colspan="2">
              <select data-placeholder="<?php _e('Select categories', 'mg_ml') ?> .." name="mg_wc_prod_cats[]" multiple="multiple" class="lcweb-chosen" autocomplete="off" style="width: 80%; max-width: 700px;">
                <?php
				foreach(get_terms( 'mg_item_categories', 'hide_empty=0') as $cat) {
					$sel = (in_array($cat->term_id, $prod_cats)) ? 'selected="selected"' : '';
					echo '<option value="'. $cat->term_id .'" '.$sel.'>'.$cat->name.'</option>';
				}
				?>
              </select>
          </td>     
        </tr>
        <tr>
          <td class="lcwp_label_td"><?php _e("Link only?", 'mg_ml'); ?></td>
          <td class="lcwp_field_td lcwp_form">
              <?php $sel = ($link_only) ? 'checked="checked"' : ''; ?>
              <input type="checkbox" value="1" name="mg_link_only" class="ip-checkbox" <?php echo $sel; ?> />
          </td>
          <td><span class="info"><?php _e('If checked, grid item will link direcly to the product page', 'mg_ml') ?></span></td>
        </tr>
      </table> 
      
      <h4 style="font-style: italic; margin: 15px 0 2px;"><?php _e('Without gallery images', 'mg_ml') ?></h4>
      <table class="widefat lcwp_table lcwp_metabox_table"> 
        <tr>
          <td class="lcwp_label_td"><?php _e("Full image max-height", 'mg_ml'); ?></td>
          <td class="lcwp_field_td">
              <input type="text" name="mg_img_maxheight" value="<?php echo ((int)$img_maxheight == 0) ? '' : $img_maxheight; ?>" maxlength="4" style="width: 50px;"  /> px
          </td>     
          <td><span class="info"><?php _e('Leave blank to use the full-size image', 'mg_ml') ?></span></td>
        </tr>     
      </table> 
      
      <h4 style="font-style: italic; margin: 15px 0 2px;"><?php _e('With gallery images', 'mg_ml') ?></h4>
      <table class="widefat lcwp_table lcwp_metabox_table"> 
        <tr>
          <td class="lcwp_label_td"><?php _e("Slider height", 'mg_ml'); ?></td>
          <td class="lcwp_field_td">
              <input type="text" class="lcwp_slider_input" name="mg_slider_w_val" value="<?php echo $h_val; ?>" maxlength="3" style="width: 50px; display: inline-block; text-align: center;" >
              <select name="mg_slider_w_type" style="width: 50px; margin-left: -5px;" autocomplete="off">
                <option value="%">%</option>
                <option value="px" <?php if($h_type == 'px') {echo 'selected="selected"';} ?>>px</option>
              </select>  
          </td>
          <td><span class="info"><?php _e("Slider height (% is related to its width)", 'mg_ml'); ?></span></td>
        </tr>
        <tr>
          <td class="lcwp_label_td"><?php _e("Prepend featured image?", 'mg_ml'); ?></td>
          <td class="lcwp_field_td lcwp_form">
              <?php $sel = ($incl_featured) ? 'checked="checked"' : ''; ?>
              <input type="checkbox" value="1" name="mg_slider_add_featured" class="ip-checkbox" <?php echo $sel; ?> />
          </td>
          <td><span class="info"><?php _e("If checked, prepend featured image in slider", 'mg_ml'); ?></span></td>
        </tr>
      </table>  
      
      <div class="lcwp_form" style="margin-top: -8px;">
	  <?php echo mg_meta_opt_generator('img_gallery', $post); ?>
      </div>
      </div> <!-- opt wrap close -->
    </div>
	*/ ?>
    
    
    <?php // security nonce ?>
    <input type="hidden" name="mg_wc_noncename" value="<?php echo wp_create_nonce('lcwp_nonce') ?>" />
    
    <?php // ////////////////////// ?>
    
    <?php // SCRIPTS ?>
    <script src="<?php echo MG_URL; ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo MG_URL; ?>/js/lc-switch/lc_switch.min.js" type="text/javascript"></script>
    
    <script type="text/javascript">
	jQuery(document).ready(function($) {
		jQuery('body').delegate('#mg_wc_enable_prod', 'change', function() {
			if(jQuery(this).val() == 'wc_no') {
				jQuery('#mg_item_meta_f_wrap').slideUp(500);	
			} else {
				jQuery('#mg_item_meta_f_wrap').slideDown(500);
			}
		});
		
		/////////////////////////////////////////////////////////////////////

		//// custom icon - picker
		<?php mg_fa_icon_picker_js(); ?>
		
	});
	</script>
    <?php	
	return true;	
}



//////////////////////////
// SAVING METABOX

function mg_wc_meta_save($post_id) {
	if(isset($_POST['mg_wc_noncename'])) {
		// authentication checks
		if (!wp_verify_nonce($_POST['mg_wc_noncename'], 'lcwp_nonce')) return $post_id;
		if (!current_user_can('edit_post', $post_id)) return $post_id;
		
		
		include_once(MG_DIR.'/functions.php');
		include_once(MG_DIR.'/classes/simple_form_validator.php');
		include_once(MG_DIR .'/classes/items_meta_fields.php');
				
		$validator = new simple_fv;
		$indexes = array();
		
		$indexes[] = array('index'=>'mg_main_type', 'label'=>'Enable product');
		$indexes[] = array('index'=>'mg_thumb_center', 'label'=>'Thumbnail Center');
		
		// type options
		$imf = new mg_meta_fields($post_id, 'woocomm');
		$indexes = array_merge($indexes, (array)$imf->get_fields_validation());
		
		
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
			update_post_meta($post_id, $key, $fdata[$key]); 
		}
		
		
		// assign mg cats to this product
		if(!is_array($fdata['mg_wc_prod_cats'])) {$fdata['mg_wc_prod_cats'] = array();}
		wp_set_post_terms($post_id, $fdata['mg_wc_prod_cats'], 'mg_item_categories', $append = false);

		// update grid categories
		mg_upd_item_upd_grids($post_id);
	}

    return $post_id;
}
add_action('save_post','mg_wc_meta_save');