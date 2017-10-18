<?php
// if deeplink is active - show lightbox on page's loading

add_action('wp_footer', 'mg_lightbox_deeplink', 999);
function mg_lightbox_deeplink() {
	include_once(MG_DIR .'/functions.php');
	
	// check deeplink existence
	if(!isset($GLOBALS['mg_deeplinks']) || !isset($GLOBALS['mg_deeplinks']['mgi'])) {
		return false;	
	}
	
	
	// check item existence and status
	$grid_id 	= $GLOBALS['mg_deeplinks']['mgi']['grid_id'];
	$item_id 	= $GLOBALS['mg_deeplinks']['mgi']['item_id'];
	$status 	= get_post_status($item_id);
	
	if($status != 'publish' || (is_user_logged_in() && !in_array($status, array('publish', 'draft', 'future')) )) {
		return false;	
	}

	// check item type - must have lightbox
	if(get_post_type($item_id) != 'product' && !in_array(get_post_meta($item_id, 'mg_main_type', true), array('single_img', 'img_gallery', 'video', 'audio', 'lb_text', 'post_contents'))) {
		return false;	
	}


	// print lightbox
	$touchswipe		= (get_option('mg_lb_touchswipe')) ? 'class="mg_touchswipe"' : '';  
	$modal_class 	= (get_option('mg_modal_lb')) ? 'mg_modal_lb' : 'mg_classic_lb';
	?>
    
	<div id="mg_lb_wrap" <?php echo $touchswipe ?> style="display: none;">
    	<div id="mg_lb_loader"><?php echo mg_preloader() ?></div>
        <div id="mg_lb_contents" class="mg_lb_pre_show_next">
        	<?php mg_lightbox($item_id, false, false); ?>
		</div>
        <div id="mg_lb_scroll_helper" class="<?php echo $modal_class ?>"></div>
        <div id="mg_deeplinked_lb" style="display: none;"></div>
	</div>
    
    <div id="mg_lb_background" class="<?php echo $modal_class ?>"></div>
    
    <?php // set lightbox contents var and show - use a little delay to let mediagrid.js codes to be executed
	echo '
	<script type="text/javascript">
	jQuery(document).ready(function(e) {
		var gid = '. (int)$grid_id .';
	   
		jQuery("#mg_lb_wrap").show();
		jQuery("#mg_lb_background").addClass("mg_lb_shown");
	   
		// check for item existence in the page - otherwise just show without prev/next 
		if(gid && jQuery("#mg_grid_'. $grid_id .' .mg_closed.mgi_'. $item_id .'").length) {
			jQuery("#mg_lb_contents").empty();
			
			$mg_sel_grid = jQuery("#mg_grid_'. $grid_id .'");
			mg_open_item('. $item_id .');
		}
		else {
		   jQuery("#mg_lb_contents").addClass("mg_lb_shown");
		   mg_open_item('. $item_id .', true);
		}
    });
	</script>';
}
