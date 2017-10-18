<?php

////////////////////////////////////////////////
////// ITEM TYPE CHANGE - LOAD FIELDS //////////
////////////////////////////////////////////////

function mg_item_meta_fields() {	
	include_once(MG_DIR .'/classes/items_meta_fields.php');
	include_once(MG_DIR . '/functions.php');
	
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {die('Cheating?');};
	if(!isset($_POST['item_id']) || !isset($_POST['item_type'])) {die('missing data');}
	
	$t = $_POST['item_type'];
	$imf = new mg_meta_fields($_POST['item_id'], $t);
	
	echo $imf->get_fields_code();		
	$imf->echo_type_js_code();
	
	die();	
}
add_action('wp_ajax_mg_item_meta_fields', 'mg_item_meta_fields');




////////////////////////////////////////////////
////// MEDIA IMAGE PICKER FOR SLIDERS //////////
////////////////////////////////////////////////

function mg_img_picker() {	
	require_once(MG_DIR . '/functions.php');
	$tt_path = MG_TT_URL; 
	
	if(!isset($_POST['page'])) {$page = 1;}
	else {$page = (int)addslashes($_POST['page']);}
	
	if(!isset($_POST['per_page'])) {$per_page = 15;}
	else {$per_page = (int)addslashes($_POST['per_page']);}
	
	$search = (isset($_POST['mg_search'])) ? $_POST['mg_search'] : '';
	$img_data = mg_library_images($page, $per_page, $search);
	
	echo '<ul>';
	
	if($img_data['tot'] == 0) {echo '<p>No images found .. </p>';}
	else {
		foreach($img_data['img'] as $img_id) {
			$thumb_data = wp_get_attachment_image_src($img_id, array(90, 90));
			echo '<li><figure style="background-image: url('. $thumb_data[0] .');" rel="'. $img_id .'"></figure></li>';
		}
	}
	
	echo '
	</ul>
	<br class="lcwp_clear" />
	<table cellspacing="0" cellpadding="5" style="border: 0; width: 100%; margin-top: 8px;">
		<tr>
			<td style="width: 35%;">';			
			if($page > 1)  {
				echo '<input type="button" class="mg_img_pick_back button-secondary" id="slp_'. ($page - 1) .'" name="mgslp_p" value="&laquo; '. __('Previous images', 'mg_ml') .'" />';
			}
			
		echo '</td><td style="width: 30%; text-align: center;">';
		
			if($img_data['tot'] > 0 && $img_data['tot_pag'] > 1) {
				echo '<em>'. __('page', 'mg_ml').' '.$img_data['pag'].' '. __('of', 'mg_ml') .' '.$img_data['tot_pag'].'</em> - <input type="text" size="2" name="mgslp_num" id="mg_img_pick_pp" value="'.$per_page.'" autocomplete="off" maxlength="3" /> <em>'. __('images per page', 'mg_ml') .'</em>';	
			}
			else { echo '<input type="text" size="2" name="mgslp_num" id="mg_img_pick_pp" value="'.$per_page.'" autocomplete="off" maxlength="3" /> <em>'. __('images per page', 'mg_ml') .'</em>';	}
			
		echo '</td><td style="width: 35%; text-align: right;">';
			if($img_data['more'] != false)  {
				echo '<input type="button" class="mg_img_pick_next button-secondary" id="slp_'. ($page + 1) .'" name="mgslp_n" value="'.__('Next images', 'mg_ml') .' &raquo;" />';
			}
		echo '</td>
		</tr>
	</table>
	';

	die();
}
add_action('wp_ajax_mg_img_picker', 'mg_img_picker');



///////////////////////////////////////////////////
////// MEDIA IMAGE PICKER - SELECTED RELOAD ///////
///////////////////////////////////////////////////
function mg_sel_img_reload() {	
	require_once(MG_DIR . '/functions.php');

	if(!isset($_POST['images'])) { $images = array();}
	else { $images = $_POST['images'];}
	
	if(!isset($_POST['videos'])) { $videos = array();}
	else { $videos = $_POST['videos'];}
	
	
	// get the titles and recreate tracks
	$arr = mg_existing_sel($images, $videos);
	
	echo mg_sel_slider_img_list($arr);
	die();
}
add_action('wp_ajax_mg_sel_img_reload', 'mg_sel_img_reload');



////////////////////////////////////////////////
////// MEDIA AUDIO PICKER  /////////////////////
////////////////////////////////////////////////

function mg_audio_picker() {	
	require_once(MG_DIR . '/functions.php');

	if(!isset($_POST['page'])) {$page = 1;}
	else {$page = (int)addslashes($_POST['page']);}
	
	if(!isset($_POST['per_page'])) {$per_page = 15;}
	else {$per_page = (int)addslashes($_POST['per_page']);}
	
	$search = (isset($_POST['mg_search'])) ? $_POST['mg_search'] : '';
	$audio_data = mg_library_audio($page, $per_page, $search);
	
	echo '<ul>';
	
	if($audio_data['tot'] == 0) {echo '<p>'. __('No audio files found', 'mg_ml') .' .. </p>';}
	else {
		// if WP > 3.9 use iconic font
		if( (float)substr(get_bloginfo('version'), 0, 3) >= 3.9) {
			$icon = '<div class="mg_audio_icon dashicons-media-audio dashicons"></div>';
		} else {
			$icon = '<img src="'.MG_URL . '/img/audio_icon.png" />';	
		}
		
		foreach($audio_data['tracks'] as $track) {
			echo '
			<li id="mgtp_'.$track['id'].'">
				'.$icon.'
				<p title="'.$track['title'].'">'.mg_excerpt($track['title'], 25).'</p>
			</li>';
		}
	}
	
	echo '
	</ul>
	<br class="lcwp_clear" />
	<table cellspacing="0" cellpadding="5" border="0" width="100%">
		<tr>
			<td style="width: 40%;">';			
			if($page > 1)  {
				echo '<input type="button" class="mg_audio_pick_back button-secondary" id="slp_'. ($page - 1) .'" name="mgslp_p" value="&laquo; '. __('Previous tracks', 'mg_ml') .'" />';
			}
			
		echo '</td><td style="width: 20%; text-align: center;">';
		
			if($audio_data['tot'] > 0 && $audio_data['tot_pag'] > 1) {
				echo '<em>page '.$audio_data['pag'].' '. __('of', 'mg_ml') .' '.$audio_data['tot_pag'].'</em> - <input type="text" size="2" name="mgslp_num" id="mg_audio_pick_pp" value="'.$per_page.'" autocomplete="off" maxlength="3" /> <em>'. __('tracks per page', 'mg_ml') .'</em>';		
			}
			else { echo '<input type="text" size="2" name="mgslp_num" id="mg_audio_pick_pp" value="'.$per_page.'" autocomplete="off" maxlength="3" /> <em>'. __('tracks per page', 'mg_ml') .'</em>'; }
			
		echo '</td><td style="width: 40%; text-align: right;">';
			if($audio_data['more'] != false)  {
				echo '<input type="button" class="mg_audio_pick_next button-secondary" id="slp_'. ($page + 1) .'" name="mgslp_n" value="'. __('Next tracks', 'mg_ml') .' &raquo;" />';
			}
		echo '</td>
		</tr>
	</table>
	';

	die();
}
add_action('wp_ajax_mg_audio_picker', 'mg_audio_picker');



///////////////////////////////////////////////////
////// MEDIA AUDIO PICKER - SELECTED RELOAD ///////
///////////////////////////////////////////////////
function mg_sel_audio_reload() {	
	require_once(MG_DIR . '/functions.php');
	
	if(!isset($_POST['tracks'])) { $tracks = array();}
	else { $tracks = $_POST['tracks'];}
	
	$tracks = mg_existing_sel($tracks);
	
	// get the titles and recreate tracks
	$new_tracks = '';
	if(!$tracks) {
		$new_tracks = '<p>'. __('No tracks selected', 'mg_ml') .' .. </p>';
	}
	else {
		// if WP > 3.9 use iconic font
		$icon = ((float)substr(get_bloginfo('version'), 0, 3) >= 3.9) ? '<div class="mg_audio_icon dashicons-media-audio dashicons"></div>' : '<img src="'.MG_URL . '/img/audio_icon.png" />';	
				
		foreach($tracks as $track_id) {
			$title = html_entity_decode(get_the_title($track_id), ENT_NOQUOTES, 'UTF-8');
			
			if($title) {
				$new_tracks .= '
				<li>
					<input type="hidden" name="mg_audio_tracks[]" value="'.$track_id.'" />
					'. $icon .'
					<span title="remove track"></span>
					<p>'.$title.'</p>
				</li>
				';
			}
		}
	}
	
	echo $new_tracks;
	die();
}
add_action('wp_ajax_mg_sel_audio_reload', 'mg_sel_audio_reload');



///////////////////////////////////////////////////
////// POST CONTENTS - CPT CHANGE -> LOAD TERMS ///
///////////////////////////////////////////////////
function mg_sel_cpt_source() {	
	require_once(MG_DIR . '/functions.php');
	
	if(isset($_POST['cpt'])) { $cpt = $_POST['cpt'];}
	else {die('missing CPT');}
	
	echo mg_get_taxonomy_terms($cpt, 'html');
	die();
}
add_action('wp_ajax_mg_sel_cpt_source', 'mg_sel_cpt_source');



////////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////
////// ADD GRID TERM ///////////////////////////
////////////////////////////////////////////////

function mg_add_grid_term() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {die('Cheating?');};
	if(!isset($_POST['grid_name'])) {die('data is missing');}
	$name = $_POST['grid_name'];
	
	$resp = wp_insert_term( $name, 'mg_grids', array(
		'slug' => sanitize_title($name),
		'description' => serialize(array('items' => array(), 'cats' => array()))
	));
	
	if(is_array($resp)) {die('success');}
	else {
		$err_mes = $resp->errors['term_exists'][0];
		die($err_mes);
	}
}
add_action('wp_ajax_mg_add_grid', 'mg_add_grid_term');



////////////////////////////////////////////////
////// LOAD GRID LIST //////////////////////////
////////////////////////////////////////////////

function mg_grid_list() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {die('Cheating?');};
	if(!isset($_POST['grid_page']) || !filter_var($_POST['grid_page'], FILTER_VALIDATE_INT)) {$pag = 1;}
	
	$pag = (int)$_POST['grid_page'];
	$per_page = 10;
	
	// search
	$search = (isset($_POST['grid_src'])) ? $_POST['grid_src']: '';
	if($search && !empty($search)) {
		$src_string = '&search='.$search;
	} else {
		$src_string = '';	
	}
	
	// get all grids
	$grids = get_terms( 'mg_grids', 'hide_empty=0'.$src_string );
	$total = count($grids);
	
	$tot_pag = ceil( $total / $per_page );
	
	
	if($pag > $tot_pag) {$pag = $tot_pag;}
	$offset = ($pag - 1) * $per_page;
	
	// get page terms
	$args =  array(
		'number' => $per_page,
		'offset' => $offset,
		'hide_empty' => 0
	);
	if($src_string != '') {
		$args['search'] = $search;	
	}
	$grids = get_terms('mg_grids', $args);

	// clean term array
	$clean_grids = array();
	
	foreach ( $grids as $grid ) {
		$clean_grids[] = array('id' => $grid->term_id, 'name' => $grid->name);
	}
	
	$to_return = array(
		'grids' => $clean_grids,
		'pag' => $pag, 
		'tot_pag' => $tot_pag
	);
    
	echo json_encode($to_return);
	die();
}
add_action('wp_ajax_mg_get_grids', 'mg_grid_list');



////////////////////////////////////////////////
////// CLONE GRID TERM /////////////////////////
////////////////////////////////////////////////

function mg_clone_grid() {
	if(!isset($_POST['grid_id']) || !isset($_POST['new_name']) || empty($_POST['new_name'])) {die('data is missing');}
	require_once(MG_DIR . '/functions.php');
	
	$grid_id = (int)$_POST['grid_id'];
	$name = $_POST['new_name'];
	
	$to_clone = mg_get_grid_data($grid_id);
	if(empty($to_clone) || !is_array($to_clone)) {
		die( __('Source grid not found', 'mg_ml') );	
	}
	
	
	$resp = wp_insert_term($name, 'mg_grids');
	if(is_wp_error($resp)) {
		echo $resp->get_error_message();
		die();
	} 
	else {	
		$store = mg_save_grid_data($resp['term_id'], $to_clone);
		
		if(is_wp_error($store)) {
			echo $store->get_error_message();
			die();
		} 
		else {	
			die('success');
		}
	}
}
add_action('wp_ajax_mg_clone_grid', 'mg_clone_grid');



////////////////////////////////////////////////
////// DELETE GRID TERM ////////////////////////
////////////////////////////////////////////////

function mg_del_grid_term() {
	if(!isset($_POST['grid_id'])) {die('data is missing');}
	$id = addslashes($_POST['grid_id']);
	
	$resp = wp_delete_term( $id, 'mg_grids');

	if($resp == '1') {die('success');}
	else {die('error during the grid deletion');}
}
add_action('wp_ajax_mg_del_grid', 'mg_del_grid_term');



////////////////////////////////////////////////
////// DISPLAY GRID BUILDER ////////////////////
////////////////////////////////////////////////

function mg_grid_builder() {
	include_once(MG_DIR . '/functions.php');
	$tt_path = MG_TT_URL;
	
	if(!isset($_POST['grid_id'])) {die('data is missing');}
	$grid_id = addslashes($_POST['grid_id']);

	// item categories list
	$item_cats = get_terms( 'mg_item_categories', 'hide_empty=0' );
	?>
    <?php 
	if( (float)substr(get_bloginfo('version'), 0, 3) >= 3.8) {
   		echo '<span id="mg_expand_builder" title="'. __('expand builder', 'mg_ml') .'"></span>';
	}
	?>
    <h2></h2>
    
    <div id="mg_grid_builder_cat" class="postbox">
      <h3 class="hndle"><?php _e('Add Grid Items', 'mg_ml') ?></h3>
      <div class="inside">
    
        <div class="lcwp_mainbox_meta">
          <table class="widefat lcwp_table lcwp_metabox_table" style="margin-bottom: 0;">
            <tr>
              <td>
              	<div class="mg_gb_half_w">
                    <label style="width: 145px;"><?php _e("Item Categories", 'mg_ml'); ?></label>
                    <select data-placeholder="><?php _e('Select item categories', 'mg_ml') ?> .." name="mh_grid_cats" id="mh_grid_cats" class="lcweb-chosen" style="width: 200px;" autocomplete="off">
                      <option value="all"><?php _e('Any', 'mg_ml') ?></option>
                        <?php 
                        foreach($item_cats as $cat) {
                            echo '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
                        }
                        ?>
                    </select>
              	</div>
                 
                <div class="mg_gb_half_w"> 
                	<label style="width: 145px;"><?php _e("Item Type", 'mg_ml'); ?></label>
                    
                    <select data-placeholder="><?php _e('Select item type', 'mg_ml') ?> .." name="mg_gb_item_type" id="mg_gb_item_type" class="lcweb-chosen" style="width: 200px;" autocomplete="off">
                      <option value="all"><?php _e('Any', 'mg_ml') ?></option>
                        <?php 
                        foreach(mg_item_types() as $id => $name) {
                            echo '<option value="'.$id.'">'.$name.'</option>';
                        }
                        ?>
                    </select>
                </div>    
              </td>     
            </tr>
              
            <tr><td style="padding: 7px !important;"><hr/></td></tr>   
                        
            <tr>
              <td style="padding-bottom: 0 !important; padding-top: 7px !important;">
                <div>
                    <label style="width: 145px;"><?php _e("Select items", 'mg_ml'); ?></label>
                    <input type="text" name="mg_gb_item_search" id="mg_gb_item_search" style="width: 314px; padding-right: 28px;" placeholder="<?php _e('search items', 'mg_ml') ?>" autocomplete="off" />
                    
                    <i class="mg_gbis_mag" title="<?php _e('search', 'mg_ml') ?>"></i>
                    <i class="mg_gbis_del" title="<?php _e('cancel', 'mg_ml') ?>"></i>
                    
                    <a href="javascript:void(0)" class="mg_gbis_show_all">(<?php _e('expand', 'mg_ml') ?>)</a>
                </div>
                
                <ul id="mg_gb_item_picker">
                    <?php 
                    $post_list = mg_item_cat_posts('all'); 
                    
                    if(!$post_list) {echo '<span>'. __('No items found', 'mg_ml') .' ..</span>';}
                    else {echo $post_list;}
                    ?>
                </ul>
              </td>
            </tr>
            
            <tr><td style="padding: 7px !important;"><hr/></td></tr>
            
            <tr>
              <td>
              	<div class="mg_gb_half_w mg_gb_bulk_wrap">
                	<label><?php _e("Bulk items width", 'mg_ml'); ?></label>
                    <select data-placeholder="><?php _e('Select a size', 'mg_ml') ?> .." name="mg_bulk_w" id="mg_bulk_w" class="lcweb-chosen" autocomplete="off">
					  <?php 
                      foreach(mg_sizes() as $size) {
                          echo '<option value="'.$size.'">'.str_replace('_', '/', $size).'</option>';
                      }
                      ?>
                    </select>
                    <select data-placeholder="><?php _e('Select a size', 'mg_ml') ?> .." name="mg_bulk_w" id="mg_bulk_mw" class="lcweb-chosen" autocomplete="off" style="display: none;">
                      <?php 
                      foreach(mg_mobile_sizes() as $size) {
                          echo '<option value="'.$size.'">'.str_replace('_', '/', $size).'</option>';
                      }
                      ?>
                    </select>
                    <input type="button" name="bulk_size" value="<?php _e('Set', 'mg_ml') ?>" class="button-secondary" id="mg_bulk_w_btn" />
                </div>
                
                <div class="mg_gb_half_w mg_gb_bulk_wrap">
                	<label><?php _e("Bulk items height", 'mg_ml'); ?></label>
                    <select data-placeholder="><?php _e('Select a size', 'mg_ml') ?> .." name="mg_bulk_h" id="mg_bulk_h" class="lcweb-chosen" autocomplete="off">
                      <?php 
                      foreach(mg_sizes() as $size) {
                          echo '<option value="'.$size.'">'.str_replace('_', '/', $size).'</option>';
                      }
                      echo '<option value="auto">'. __('auto', 'mg_ml') .'</option>';
                      ?>
                    </select>
                    <select data-placeholder="><?php _e('Select a size', 'mg_ml') ?> .." name="mg_bulk_h" id="mg_bulk_mh" class="lcweb-chosen" autocomplete="off" style="display: none;">
                      <?php 
                      foreach(mg_mobile_sizes() as $size) {
                          echo '<option value="'.$size.'">'.str_replace('_', '/', $size).'</option>';
                      }
                      echo '<option value="auto">'. __('auto', 'mg_ml') .'</option>';
                      ?>
                    </select>
                    <input type="button" name="bulk_size" value="<?php _e('Set', 'mg_ml') ?>" class="button-secondary" id="mg_bulk_h_btn" />
                </div>
                
              </td>

            </tr>
          </table>  
        <div>  
      </div>
	</div>
    </div>
    </div>
    
    <div class="postbox">
      <h3 class="hndle">
	  	<?php _e('Grid Preview', 'mg_ml') ?>
        <a href="javascript:void(0)" id="mg_mobile_view_toggle"><?php _e('mobile view', 'mg_ml') ?> <span><?php _e('OFF', 'mg_ml') ?></span></a>
        <a href="javascript:void(0)" id="mg_easy_sorting_toggle"><?php _e('easy sorting', 'mg_ml') ?> <span><?php _e('OFF', 'mg_ml') ?></span></a>
        <a href="javascript:void(0)" id="mg_add_paginator"><?php _e('add pagination block', 'mg_ml') ?></a>
      </h3>
      <div class="inside">
		<div id="visual_builder_wrap" class="mg_desktop_builder">
        
		<ul id="mg_sortable">
          <?php
		  // get grid data
		  $grid_data = mg_get_grid_data($grid_id); 
		  
		  if(is_array($grid_data['items'])) {
			$a = 0;  
            foreach($grid_data['items'] as $k => $item) {
			  
			  // paginator block
			  if($item['id'] == 'paginator') {
				echo mg_paginator_item();
				continue;  
			  }
			  
			  // normal execution
			  if( get_post_status($item['id']) == 'publish' ) {
				  
				  $item_type = get_post_meta($item['id'], 'mg_main_type', true);
				  $orig_item_type = $item_type; // keep it for checks
				  
				  if(get_post_type($item['id']) == 'product') {
					 $item_type = 'woocom';	  
				  }
				  
				  $type_text = ($item_type == 'woocom') ? 'WooCommerce' : mg_item_types($item_type);
				  
				  $w_sizes = mg_sizes();
				  $h_sizes = mg_sizes();
				  $mw_sizes = mg_mobile_sizes();
				  $mh_sizes = mg_mobile_sizes();
				   
				  $item_w = $item['w'];
				  $item_h = $item['h'];   
				  
				  // mobile sizes
				  $mobile_w = (isset($item['m_w'])) ? $item['m_w'] : $item_w;  
				  $mobile_h = (isset($item['m_h'])) ? $item['m_h'] : $item_h; 
				  
				  // check mobile limits
				  $mobile_w = (in_array($mobile_w, mg_mobile_sizes())) ? $mobile_w : '1_2';
				  $mobile_h = (in_array($mobile_h, mg_mobile_sizes()) || $mobile_h == 'auto') ? $mobile_h : '1_3';
				  
				  // add height == auto if type != inline slider or inline video
				  if(!in_array($orig_item_type, array('inl_slider', 'inl_video', 'inl_audio', 'spacer'))) {
					  $h_sizes[] = 'auto'; 
					  $mh_sizes[] = 'auto'; 
				  }
				  
				  // visibility class for spacer
				  if($item_type == 'spacer') {
					  $vis = get_post_meta($item['id'], 'mg_spacer_vis', true);
					  $spacer_vis = ($vis) ? 'mg_spacer_'.$vis : '';
				  } 
				  else {$spacer_vis = '';}

				  // item thumb
				  if(in_array($orig_item_type, array('inl_slider', 'inl_video', 'post_contents', 'inl_text', 'spacer'))) {
					  $item_thumb = '<img src="'.MG_URL. '/img/type_icons/'.$item_type.'.png" height="19" width="19" class="thumb" alt="" />';	
				  } else {
					  $thumb_data = wp_get_attachment_image_src(get_post_thumbnail_id($item['id']), array(48, 48));
					  $item_thumb = '<img src="'.$thumb_data[0].'" class="thumb true_thumb" alt="" />';	
				  }	
				  	
				  $sizes = mg_sizes();
					
				  echo '
				  <li class="mg_box mg_'.$item_type.'_type col'.$item_w.' row'.$item_h.' '.$spacer_vis.'" id="box_'.mt_rand().$item['id'].'" mg-width="'.$item_w.'" mg-height="'.$item_h.'" mg-m-width="'.$mobile_w.'" mg-m-height="'.$mobile_h.'">
					<input type="hidden" name="grid_items[]" value="'.$item['id'].'" />
					<div class="handler" name="'.$item['id'].'">
						<div class="del_item" title="'. __('remove item', 'mg_ml') .'"></div>
						<a href="'.get_admin_url().'post.php?post='.$item['id'].'&action=edit" class="edit_item" target="_blank" title="'. __('edit item', 'mg_ml') .'"></a>
						<h3>
							'.$item_thumb.'
							'.strip_tags(get_the_title($item['id'])).'
						</h3>
						<p style="padding-top: 6px;">'. $type_text .'</p>
						<p class="mg_builder_standard_sizes">';
						
						// choose the width
						echo __('Width', 'mg_ml').' <select name="items_w[]" class="select_w mg_items_sizes_dd">'; 
							
							foreach($w_sizes as $size) {
								($size == $item_w) ? $sel = 'selected="selected"' : $sel = '';
								echo '<option value="'.$size.'" '.$sel.' autofocus>'.str_replace('_', '/', $size).'</option>';	
							}
						
						echo '</select> <br/>  '. __('Height', 'mg_ml').' <select name="items_h[]" class="select_h mg_items_sizes_dd">';

							foreach($h_sizes as $size) {
								($size == $item_h) ? $sel = 'selected="selected"' : $sel = '';
								echo '<option value="'.$size.'" '.$sel.'>'.str_replace('_', '/', $size).'</option>';	
							}

				  echo '</select></p>
				  <p class="mg_builder_mobile_sizes">';

						echo __('Width', 'mg_ml').' <select name="items_mobile_w[]" class="select_m_w mg_items_sizes_dd">'; 
							
							foreach($mw_sizes as $size) {
								($size == $mobile_w) ? $sel = 'selected="selected"' : $sel = '';
								echo '<option value="'.$size.'" '.$sel.' autofocus>'.str_replace('_', '/', $size).'</option>';	
							}
						
						echo '</select> <br/>  '. __('Height', 'mg_ml').' <select name="items_mobile_h[]" class="select_m_h mg_items_sizes_dd">';

							foreach($mh_sizes as $size) {
								($size == $mobile_h) ? $sel = 'selected="selected"' : $sel = '';
								echo '<option value="'.$size.'" '.$sel.'>'.str_replace('_', '/', $size).'</option>';	
							}

				  echo '</select></p>
				  	<p>
						<span class="mg_move_item_bw" title="'. __('move item backwards', 'mg_ml') .'"></span>
						<span class="mg_item_num"></span>
						<span class="mg_move_item_fw" title="'. __('move item forwards', 'mg_ml') .'"></span>
					</p>
				  
					</div>
				  </li>';
			  }
			  $a++;
            }
          }
		  else {echo '<p>'. __('No items in the grid', 'mg_ml') .' ..</p>';}
          ?>

       </ul>
       </div> 
         
	</div>
    </div>
    </div>
    
	<?php
	die();
}
add_action('wp_ajax_mg_grid_builder', 'mg_grid_builder');



////////////////////////////////////////////////
////// GET ITEM CATEGORIES POSTS ///////////////
////////////////////////////////////////////////

function mg_item_cat_posts($fnc_cat = false) {	
	require_once(MG_DIR . '/functions.php');

	// if is not called directly
	if(!$fnc_cat) {
		if(!isset($_POST['items_cat'])) {die('data is missing');}
		
		$cat = $_POST['items_cat'];
		$type = (!isset($_POST['items_type']) || $_POST['items_type'] == 'all') ? false : $_POST['items_type'];
	}
	else {
		$cat = $fnc_cat;	
		$type = false;
	}

	$items_list = mg_get_cat_items($cat);	
	if(!$items_list) {return false;}
	
	$code = '';
	foreach($items_list as $item) {
		$t = $item['type'];
		
		// item type filter
		if($type) {
			if($t != $type) {continue;}	
		}
		
		
		// special bg
		if(in_array($t, array('inl_slider', 'inl_video', 'inl_text', 'post_contents', 'spacer'))) {
			$bg = 'background: url('.MG_URL.'/img/type_icons/'.$t.'.png) no-repeat center center #7fc241;';	
		} 
		else {
			$thumb_data = wp_get_attachment_image_src($item['img'], 'medium');
			$bg = 'background-image: url('.$thumb_data[0].');';		
		}
		
		// preview icon
		if(in_array($t, array('simple_img', 'inl_slider', 'inl_video', 'link', 'inl_text', 'spacer'))) {
			$preview = '';	
		} else {
			$preview = '<a class="mgi_preview" href="'. site_url() .'?_escaped_fragment_=mg_ld='. $post->ID .'" title="'. __('preview item', 'mg_ml') .'" target="_blank"></a>';	
		}
		
		// compose
		$code .= '
		<li style="'. $bg .'" rel="'.$item['id'].'" title="'. __('add to grid', 'mg_ml') .'">
			<p>
				<i class="mgi_type mgi_'.$t.'" title="'. mg_item_types($t) .'"></i>
				<a class="mgi_edit" href="'.get_admin_url().'post.php?post='.$item['id'].'&action=edit" title="'. __('edit item', 'mg_ml') .'" target="_blank"></a>
				'. $preview .'
			</p>

			<div title="'. esc_attr($item['title']) .'" search-helper="'. esc_attr(strtolower($item['title'])) .'">
				'.$item['title'].'
			</div>
		</li>';
	}

	
	if($fnc_cat == false) {die( $code );}
	else {return $code;}
}
add_action('wp_ajax_mg_item_cat_posts', 'mg_item_cat_posts');



////////////////////////////////////////////////
////// ADD AN ITEM TO THE VISUAL BUILDER ///////
////////////////////////////////////////////////

function mg_add_item_to_builder() {	
	include_once(MG_DIR . '/functions.php');
	$tt_path = MG_TT_URL;
	
	if(!isset($_POST['item_id'])) {die('data is missing');}
	
	$item_id = addslashes($_POST['item_id']);
	$mobile_mode = (isset($_POST['mg_mobile']) && $_POST['mg_mobile']) ? true : false;
			  
	$items_data = mg_grid_builder_items_data( array($item_id) );         
	foreach($items_data as $item) {
		$w_sizes = mg_sizes();
		$h_sizes = mg_sizes();
		$mw_sizes = mg_mobile_sizes();
		$mh_sizes = mg_mobile_sizes();
		
		$item_w = '1_4';
		$item_h = '1_4';
		
		$mobile_w = '1_2';
		$mobile_h = '1_3';
		
		// add height == auto if type != inline slider or inline video
		if(!in_array($item['type'], array('inl_slider', 'inl_video', 'inl_audio', 'spacer'))) {
			$h_sizes[] = 'auto'; 
			$mh_sizes[] = 'auto';
		}
		
		// visibility class for spacer
		if($item['type'] == 'spacer') {
			$vis = get_post_meta($item_id, 'mg_spacer_vis', true);
			$spacer_vis = ($vis) ? 'mg_spacer_'.$vis : '';
		} 
		else {$spacer_vis = '';}

		// item thumb
		if(in_array($item['type'], array('inl_slider', 'inl_video', 'post_contents', 'inl_text', 'spacer'))) {
			$item_thumb = '<img src="'.MG_URL. '/img/type_icons/'.$item['type'].'.png" height="19" width="19" class="thumb" alt="" />';	
		} else {
			$thumb_data = wp_get_attachment_image_src(get_post_thumbnail_id($item_id), array(48, 48));
			$item_thumb = '<img src="'.$thumb_data[0].'" class="thumb true_thumb" alt="" />';	
		}	
		
		// size classes 
		$w_class = ($mobile_mode) ? $mobile_w : $item_w;
		$h_class = ($mobile_mode) ? $mobile_h : $item_h;
		
		// item type
		$item_type = (get_post_type($item['id']) == 'product') ? 'WooCommerce' : mg_item_types($item['type']);
		
		echo '
		<li class="mg_box mg_'.$item['type'].'_type col'.$w_class.' row'.$h_class.' '.$spacer_vis.'" id="box_'.mt_rand().$item['id'].'" mg-width="'.$item_w.'" mg-height="'.$item_h.'"  mg-m-width="'.$mobile_w.'" mg-m-height="'.$mobile_h.'">
		  <input type="hidden" name="grid_items[]" value="'.$item['id'].'" />
		  <div class="handler" name="'.$item['id'].'">
		  	  <div class="del_item"></div>
			  <a href="'.get_admin_url().'post.php?post='.$item['id'].'&action=edit" class="edit_item" target="_blank" title="'.__('edit item', 'mg_ml').'"></a>
			  <h3>
			    '.$item_thumb.'
			  	'.strip_tags($item['title']).'
			  </h3>
			  <p style="padding-top: 6px;">'. $item_type .'</p>
			  <p class="mg_builder_standard_sizes">';
						
				// choose the width
				echo __('Width', 'mg_ml').' <select name="items_w[]" class="select_w mg_items_sizes_dd">'; 
					
					foreach($w_sizes as $size) {
						($size == $item_w) ? $sel = 'selected="selected"' : $sel = '';
						echo '<option value="'.$size.'" '.$sel.'>'.str_replace('_', '/', $size).'</option>';	
					}
				
				echo '</select> <br/>  '. __('Height', 'mg_ml').' <select name="items_h[]" class="select_h mg_items_sizes_dd">';
					foreach($h_sizes as $size) {
						($size == $item_h) ? $sel = 'selected="selected"' : $sel = '';
						echo '<option value="'.$size.'" '.$sel.'>'.str_replace('_', '/', $size).'</option>';	
					}
	
			  echo '</select></p>
			  <p class="mg_builder_mobile_sizes">';
			  
				  echo __('Width', 'mg_ml').' <select name="items_mobile_w[]" class="select_m_w mg_items_sizes_dd">'; 
					  
					  foreach($mw_sizes as $size) {
						  ($size == $mobile_w) ? $sel = 'selected="selected"' : $sel = '';
						  echo '<option value="'.$size.'" '.$sel.'>'.str_replace('_', '/', $size).'</option>';	
					  }
				  
				  echo '</select> <br/>  '. __('Height', 'mg_ml').' <select name="items_mobile_h[]" class="select_m_h mg_items_sizes_dd">';
					  foreach($mh_sizes as $size) {
						  ($size == $mobile_h) ? $sel = 'selected="selected"' : $sel = '';
						  echo '<option value="'.$size.'" '.$sel.'>'.str_replace('_', '/', $size).'</option>';	
					  }
		
		  	echo '</select></p>
	  		
			<p>
				<span class="mg_move_item_bw" title="'. __('move item backwards', 'mg_ml') .'"></span>
				<span class="mg_item_num"></span>
				<span class="mg_move_item_fw" title="'. __('move item forwards', 'mg_ml') .'"></span>
			</p>
		  </div>
		</li>';	
			
	}
	
	die();	
}
add_action('wp_ajax_mg_add_item_to_builder', 'mg_add_item_to_builder');



////////////////////////////////////////////
////// SAVE GRID ITEMS /////////////////////
////////////////////////////////////////////

function mg_save_grid() {	
	include_once(MG_DIR . '/functions.php');
	
	if(!isset($_POST['grid_id']) || !(int)$_POST['grid_id']) {die('grid ID is missing');}
	$grid_id = (int)$_POST['grid_id'];
	
	if(!isset($_POST['items_list'])) {die('items list is missing');}
	$items_list = $_POST['items_list'];
	
	if(!isset($_POST['items_width'])) {die('items width is missing');}
	$items_width = $_POST['items_width'];
	
	if(!isset($_POST['items_height'])) {die('items height is missing');}
	$items_height = $_POST['items_height'];
	
	if(!isset($_POST['items_m_width'])) {die('items mobile width is missing');}
	$mobile_width = $_POST['items_m_width'];
	
	if(!isset($_POST['items_m_height'])) {die('items mobile height is missing');}
	$mobile_height = $_POST['items_m_height'];
	
	
	// create grid array
	$arr = array('items' => array());
	if(is_array($items_list)) {
		for($a=0; $a < count($items_list); $a++) {
			$arr['items'][] = array(
				'id'	=> $items_list[$a],
				'w' 	=> $items_width[$a],
				'h' 	=> $items_height[$a],
				'm_w' 	=> $mobile_width[$a],
				'm_h' 	=> $mobile_height[$a]
			);	
		}
	}
	
	// add posts term list
	$terms_array = array();
	foreach($items_list as $post_id) {
		$pid_terms = wp_get_post_terms($post_id, 'mg_item_categories', array("fields" => "ids"));
		foreach($pid_terms as $pid_term) { $terms_array[] = $pid_term; }	
	}
	$terms_array = array_unique($terms_array);
	$arr['cats'] = $terms_array;
	
	// update grid term
	$answer = mg_save_grid_data($grid_id, $arr);
	
	if(is_wp_error($answer)) {
		echo $answer->get_error_message();
	} else {
		echo 'success';
	}
	die();				
}
add_action('wp_ajax_mg_save_grid', 'mg_save_grid');



/////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////
////// SET PREDEFINED GRID STYLES //////////////
////////////////////////////////////////////////

function mg_set_predefined_style() {
	if(!isset($_POST['lcwp_nonce']) || !wp_verify_nonce($_POST['lcwp_nonce'], 'lcwp_nonce')) {die('Cheating?');};
	if(!isset($_POST['style'])) {die('data is missing');}
	$style = $_POST['style'];
	
	require_once(MG_DIR . '/functions.php');
	
	$style_data = mg_predefined_styles($style);
	
	// additive settings if is a fresh installation
	if(!get_option('mg_item_width')) {
		$style_data['mg_item_width'] = 70;
		$style_data['mg_item_maxwidth'] = 960;	
	}
	
	// set option values
	foreach($style_data as $opt => $val) {
		if($opt != 'preview') {
			update_option($opt, $val);				
		}
	}
	
	if(!get_option('mg_inline_css')) {
		mg_create_frontend_css();
	}

	die();
}
add_action('wp_ajax_mg_set_predefined_style', 'mg_set_predefined_style');



////////////////////////////////////////////////
////// SYNC OPTIONS WITH WPML //////////////////
////////////////////////////////////////////////

function mg_options_wpml_sync() {
	if(!function_exists('icl_register_string')) {die('error');}
	
	require_once(MG_DIR . '/functions.php');
	$already_saved = get_option('mg_wpml_synced_opts');
	$to_save = array();
	
	foreach(mg_main_types() as $type => $name) {
		$type_opts = get_option('mg_'.$type.'_opt');
		$typename = ($type == 'img_gallery') ? 'Image Gallery' : ucfirst($type);
		
		if(is_array($type_opts) && count($type_opts) > 0) {
			foreach($type_opts as $opt) {
				$index = $typename.' Options - '.$opt;
				$to_save[$index] = $index;
				
				icl_register_string('Media Grid - Item Options', $index, $opt);	
				
				if(isset($already_saved[$index])) {unset($already_saved[$index]);}
			}
		}
	}
	
	if(is_array($already_saved) && count($already_saved) > 0) {
		foreach($already_saved as $opt) {
			icl_unregister_string('Media Grid - Item Options', $opt);	
		}
	}
	
	update_option('mg_wpml_synced_opts', $to_save);	
	die('success');
}
add_action('wp_ajax_mg_options_wpml_sync', 'mg_options_wpml_sync');
