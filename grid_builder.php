<?php require_once(MG_DIR . '/functions.php'); ?>

<div class="wrap lcwp_form">  
	<div class="icon32"><img src="<?php echo MG_URL .'/img/mg_icon.png'; ?>" alt="mediagrid" /><br/></div>
    <?php echo '<h2 class="lcwp_page_title" style="border: none;">' . __( 'Grid Builder', 'mg_ml') . "</h2>"; ?>  

    <div id="poststuff" class="metabox-holder has-right-sidebar" style="overflow: hidden;">
    	
        <?php // SIDEBAR ?>
        <div id="side-info-column" class="inner-sidebar">
          <form class="form-wrap">	
           
            <div id="add_grid_box" class="postbox lcwp_sidebox_meta">
            	<h3 class="hndle"><?php _e('Add Grid', 'mg_ml') ?></h3> 
				<div class="inside">
                  <div class="misc-pub-section-last">
                	<input type="text" name="mg_cells_margin" value="" id="add_grid" maxlenght="100" style="width: 200px;" autocomplete="off" placeholder="<?php _e('Grid Name', 'mg_ml') ?>" />
                    <input type="button" name="add_grid_btn" id="add_grid_btn" value="<?php _e('Add', 'mg_ml') ?>" class="button-primary" style="margin-left: 5px;" />
                  </div>  
                </div>
            </div>
            
            <div id="man_grid_box" class="postbox lcwp_sidebox_meta">
            	<h3 class="hndle"><?php _e('Grid List', 'mg_ml') ?></h3> 
                <div id="mg_src_grid_wrap">
                	<span id="mg_src_grid_btn"></span>
                	<input type="text" name="src_grid" id="mg_src_grid" value="" autocomplete="off" />
                </div>
				<div class="inside"></div>
            </div>
            
            <div id="save_grid_box" class="postbox lcwp_sidebox_meta" style="display: none; background: none; border: none;">
            	<input type="button" name="save-grid" value="<?php _e('Save grid', 'mg_ml') ?>" class="button-primary" />
                
                <?php if(get_option('mg_preview_pag')) : ?>
                <input type="button" id="preview_grid" value="<?php _e('Preview', 'mg_ml') ?>" class="button-secondary" pv-url="<?php echo get_permalink(get_option('mg_preview_pag')) ?>" style="margin-left: 18px;" />
                <?php endif; ?>
                
                <div style="width: 30px; padding: 0 0 0 7px; float: right;"></div>
            </div>
          </form>	
            
        </div>
    	
        <?php // PAGE CONTENT ?>
        <form class="form-wrap" id="grid_items_list">  
          <div id="post-body">
          <div id="post-body-content" class="mg_grid_content">
              <p><?php _e('Select a grid', 'mg_ml') ?> ..</p>
          </div>
          </div>
        </form>
        
        <br class="clear">
    </div>
    
</div>  

<?php // SCRIPTS ?>
<script src="<?php echo MG_URL; ?>/js/functions.js" type="text/javascript"></script>
<script src="<?php echo MG_URL; ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script src="<?php echo MG_URL; ?>/js/lc-switch/lc_switch.min.js" type="text/javascript"></script>
<script src="<?php echo MG_URL; ?>/js/jquery.masonry.min.js" type="text/javascript"></script>

<script type="text/javascript">
jQuery(document).ready(function($) {
	var is_adding_grid = false;
	var is_loading_grid = false;
	
	var mg_sel_grid = 0;
	var mg_grid_pag = 1;
	var src_str = ''; // grid search
	
	var mg_mobile = false;
	var mg_easy_sorting = false;
	
	// init - load grids
	mg_load_grids();
	
	
	// easy sorting mode toggle
	jQuery('body').delegate('#mg_easy_sorting_toggle', "click", function() {
		jQuery(this).toggleClass('mg_active');
		
		// deactivate
		if(jQuery('#mg_sortable').hasClass('mg_easy_sorting')) {
			mg_easy_sorting = false;
			
			jQuery(this).find('span').text('OFF');
			jQuery('#mg_sortable').removeClass('mg_easy_sorting');	
			
			size_boxes('.mg_box');
			masonerize();
		} 
		
		// activate
		else {
			mg_easy_sorting = true;
			
			jQuery(this).find('span').text('ON');
			jQuery('#mg_sortable').addClass('mg_easy_sorting');
			jQuery('#visual_builder_wrap, #mg_sortable li').removeAttr('style');
			
			$container.masonry('destroy');	
		}
		
		jQuery('#mg_sortable').sortable( "refreshPositions" );
	});
	
	
	// expanded mode toggle
	jQuery('body').delegate('#mg_expand_builder', "click", function() {
		if(jQuery('#wpcontent').hasClass('mg_expanded_builder')) {
			jQuery('#wpcontent').removeClass('mg_expanded_builder');	
		} else {
			jQuery('#wpcontent').addClass('mg_expanded_builder');	
		}
		
		jQuery(window).trigger('resize');
	});
	
	
	// preview grid
	jQuery('body').delegate('#preview_grid', "click", function() {
		var url = jQuery(this).attr('pv-url') + '?mg_preview=' + mg_sel_grid;
		window.open(url,'_blank');
	});
	
	
	
	/*** ITEMS PICKER ***/
	
	// items cat choose
	jQuery('body').delegate('#mh_grid_cats', "change", function() {
		
		jQuery('#mg_gb_item_picker').animate({'opacity' : 0.7}, 200);
		jQuery('#mg_gb_item_search').val('').trigger('keyup');
		
		var data = {
			action:		'mg_item_cat_posts',
			items_cat:	jQuery(this).val(),
			items_type:	jQuery('#mg_gb_item_type').val()
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#mg_gb_item_picker').html(response);
			jQuery('#mg_gb_item_picker').animate({'opacity' : 1}, 200);
			
			if(!jQuery('#mg_gb_item_picker li').length) {
				jQuery('#mg_gb_item_picker').html("<span><?php _e('No items found', 'mg_ml') ?> ..</span>");
			}
		});	
	});
	
	// items type choose -> simulate cat change 
	jQuery('body').delegate('#mg_gb_item_type', "change", function() {
		jQuery('#mh_grid_cats').trigger('change');	
	});
	
	
	// items search
	jQuery('body').on('keyup', "#mg_gb_item_search", function() {
		if(typeof(mg_gbis_acting) != 'undefined') {clearTimeout(mg_gbis_acting);}
		mg_gbis_acting = setTimeout(function() {
			
			var src_string = jQuery.trim( jQuery("#mg_gb_item_search").val().toLowerCase() );
			src_string = src_string.replace(',', '').replace('.', '').replace('?', ''); 
			
			if(src_string.length > 2) {
				jQuery('.mg_gbis_del').fadeIn(200);
				
				var src_arr = src_string.split(' ');
				var matching = jQuery.makeArray();

				// cyle and check eac searched term 
				jQuery('#mg_gb_item_picker li').each(function(i, elem) {
					jQuery.each(src_arr, function(i, word) {						
						
						if( jQuery(elem).find('div').attr('search-helper').indexOf(word) !== -1 ) {
							jQuery(elem).show();
						} else {
							jQuery(elem).hide();
						}
					});
				});
				
				if(jQuery('#mg_gb_item_picker li:visible').length) {
					jQuery('#mg_gb_item_picker span').remove();	
				} else {
					if(!jQuery('#mg_gb_item_picker span').length) {
						jQuery('#mg_gb_item_picker').prepend("<span><?php _e('No items found', 'mg_ml') ?> ..</span>");	
					}
				}
			}
			else {
				jQuery('.mg_gbis_del').fadeOut(200);
				jQuery('#mg_gb_item_picker span').remove();	
				jQuery('#mg_gb_item_picker li').show();
			}
		}, 300);
	});
	
	jQuery('body').on('click', '.mg_gbis_mag', function() {
		jQuery("#mg_gb_item_search").trigger('keyup');
	});
	
	jQuery('body').on('click', '.mg_gbis_del', function() {
		jQuery("#mg_gb_item_search").val('').trigger('keyup');
	});
	
	
	// expand/compress
	jQuery('body').delegate('.mg_gbis_show_all', "click", function() {	
		if(jQuery(this).hasClass('shown')) {
			jQuery(this).removeClass('shown').text("(<?php _e('expand', 'mg_ml') ?>)");
			
			jQuery('#mg_gb_item_picker').css('max-height', '113px');
		}
		else {
			jQuery(this).addClass('shown').text("(<?php _e('collapse', 'mg_ml') ?>)");
			jQuery('#mg_gb_item_picker').css('max-height', 'none');	
		}
	});
	
	
	// items dropdown thumbnails toggle
	jQuery('body').delegate('#mh_grid_item', "change", function() {
		var sel = jQuery(this).val();
		
		jQuery('.mg_dd_items_preview img').hide();
		jQuery('.mg_dd_items_preview img').each(function() {
			if( jQuery(this).attr('alt') == sel ) {jQuery(this).fadeIn();}
		});	
	});
	
	
	// add item
	jQuery('body').delegate('#mg_gb_item_picker li', "click", function() {
		var $subj = jQuery(this); 
		$subj.animate({'opacity' : 0.8}, 200);

		var data = {
			action: 'mg_add_item_to_builder',
			item_id: $subj.attr('rel'),
			mg_mobile: (mg_mobile) ? 1 : 0 
		};
		jQuery.post(ajaxurl, data, function(response) {
			if( jQuery('#visual_builder_wrap ul .mg_box').size() == 0 ) {jQuery('#visual_builder_wrap ul').empty();}
			
			jQuery('#add_item_btn div').empty();
			jQuery('#visual_builder_wrap ul').<?php echo (get_option('mg_builder_behav', 'append') == 'prepend') ? 'prepend' : 'append'; ?>( response );

			if(!mg_easy_sorting) {
				size_boxes('.mg_box');
				$container.masonry('reload');
			}
			
			mg_items_num_pos();
			$subj.animate({'opacity' : 1}, 200);
		});
	});
	
	
	
	/*** IN-BUILDER FUNCTIONS ***/
	
	// add paginator block
	jQuery('body').delegate('#mg_add_paginator', "click", function() {
		if( jQuery('#visual_builder_wrap ul .mg_box').size() == 0 ) {jQuery('#visual_builder_wrap ul').empty();}

		jQuery('#visual_builder_wrap ul').append('<?php echo str_replace("'", "\'", mg_paginator_item()); ?>');
		
		if(!mg_easy_sorting) {
			$container.masonry('reload');
		}
	});
	
	
	// remove item
	jQuery('body').delegate('.del_item', "click", function() {
		if(confirm('<?php echo mg_sanitize_input( __('Remove the item?', 'mg_ml')) ?>')) {
			jQuery(this).parent().parent().fadeOut('fast', function() {
				
				if(!mg_easy_sorting) {
					$container.masonry('remove', jQuery(this) );
					$container.masonry('reload');	
				}
				
				jQuery(this).remove();
				mg_items_num_pos();
			});
		}
	});
	
	
	/*** GRIDS LIST ACTIONS ***/

	// grid selection
	jQuery('body').delegate('#man_grid_box input[type=radio]', 'click', function() {
		if(is_loading_grid) {return false;}
		
		is_loading_grid = true;
		mg_sel_grid = parseInt(jQuery(this).val());
		var grid_title = jQuery(this).parent().siblings('.mg_grid_tit').text();

		jQuery('.mg_grid_content').html('<div style="height: 30px;" class="lcwp_loading"></div>');

		var data = {
			action: 'mg_grid_builder',
			grid_id: mg_sel_grid 
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('.mg_grid_content').html(response);
			
			// add the title
			jQuery('.mg_grid_content > h2').html(grid_title);
			
			// savegrid box
			jQuery('#save_grid_box').fadeIn();
			
			mg_live_chosen();
			
			masonerize();
			size_boxes('.mg_box');
			$container.masonry('reload');
			mg_items_num_pos();
			
			mg_mobile = false;	
			is_loading_grid = false;
		});	
	});
	
	
	// add grid
	jQuery('#add_grid_btn').click(function() {
		var grid_name = jQuery.trim( jQuery('#add_grid').val() );
		if(is_adding_grid || !grid_name) {return false;}
		
		is_adding_grid = true;
		jQuery('#add_grid_btn').css('opacity', 0.65);
		
		var data = {
			action: 'mg_add_grid',
			grid_name: jQuery.trim( jQuery('#add_grid').val() ),
			lcwp_nonce: '<?php echo wp_create_nonce('lcwp_nonce') ?>'
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			var resp = jQuery.trim(response); 
			
			if(resp == 'success') {
				mg_toast_message('success', "<?php echo mg_sanitize_input( __('Grid added', 'mg_ml')) ?>");
				jQuery('#add_grid').val('');
				
				mg_grid_pag = 1;
				mg_load_grids();
			}
			else {
				mg_toast_message('error', resp);
			}
			
			jQuery('#add_grid_btn').css('opacity', 1);
			is_adding_grid = false;
		});	
	});
	
	
	// search grids in list
	jQuery('body').delegate('#mg_src_grid_btn', 'click', function() {
		src_str = jQuery('#mg_src_grid').val(); 
		mg_grid_pag = 1;
		mg_load_grids();
	});
	jQuery('#mg_src_grid').keypress(function(event){
		if(event.keyCode === 13){
			src_str = jQuery('#mg_src_grid').val(); 
			mg_grid_pag = 1;
			mg_load_grids();
		}
		
		event.cancelBubble = true;
		if(event.stopPropagation) event.stopPropagation();
   	});
	
	
	// manage grids pagination
	// prev
	jQuery('body').delegate('#mg_prev_grids', 'click', function() {
		mg_grid_pag = mg_grid_pag - 1;
		mg_load_grids();
	});
	// next
	jQuery('body').delegate('#mg_next_grids', 'click', function() {
		mg_grid_pag = mg_grid_pag + 1;
		mg_load_grids();
	});
	
	
	// load grid list
	function mg_load_grids() {
		jQuery('#man_grid_box .inside').html('<div style="height: 30px; width: 100%;" class="lcwp_loading"></div>');
		
		var data = {
			'action': 'mg_get_grids',
			'grid_page': mg_grid_pag,
			'grid_src': src_str,
			'lcwp_nonce': '<?php echo wp_create_nonce('lcwp_nonce') ?>'
		};
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			dataType: "json",
			success: function(response){	
				jQuery('#man_grid_box .inside').empty();
				
				// get elements
				mg_grid_pag = response.pag;
				var mg_grid_tot_pag = response.tot_pag;
				var mg_grids = response.grids;	

				var a = 0;
				jQuery.each(mg_grids, function(k, v) {	
					if( mg_sel_grid == v.id) {var sel = 'checked="checked"';}
					else {var sel = '';}
				
					jQuery('#man_grid_box .inside').append(
					'<div class="misc-pub-section-last">'+
						'<span><input type="radio" name="gl" value="'+ v.id +'" '+ sel +' autocomplete="off" /></span>'+
						'<span class="mg_grid_tit" style="padding-left: 7px;" title="Grid ID #'+ v.id +'">'+ v.name +'</span>'+
						'<span class="mg_del_grid" id="gdel_'+ v.id +'"></span>'+
						'<small class="mg_clone" rel="'+ v.id +'" title="<?php echo esc_attr( __('clone grid', 'mg_ml')) ?>"></small>'+
						'<div style="clear: both;"></div>'+
					'</div>');
					
					a = a + 1;
				});
				
				if(a == 0) {
					jQuery('#man_grid_box .inside').html('<p><?php echo mg_sanitize_input( __('No existing grids', 'mg_ml')) ?></p>');
					jQuery('#man_grid_box h3.hndle').html('<?php echo mg_sanitize_input( __('Grid List', 'mg_ml')) ?>');
				}
				else {
					// manage pagination elements
					if(mg_grid_tot_pag > 1) {
						jQuery('#man_grid_box h3.hndle').html('<?php echo mg_sanitize_input( __('Grid List', 'mg_ml')) ?> (<?php echo mg_sanitize_input( __('pag', 'mg_ml')) ?> '+mg_grid_pag+' <?php echo mg_sanitize_input( __('of', 'mg_ml')) ?> '+mg_grid_tot_pag+')\
						<span id="mg_next_grids">&raquo;</span><span id="mg_prev_grids">&laquo;</span>');
					} else {
						jQuery('#man_grid_box h3.hndle').html('<?php echo mg_sanitize_input( __('Grid List', 'mg_ml')) ?>');	
					}
					
					// different cases
					if(mg_grid_pag <= 1) { jQuery('#mg_prev_grids').hide(); }
					if(mg_grid_pag >= mg_grid_tot_pag) {jQuery('#mg_next_grids').hide();}	
				}
			}
		});	
	}
	
	
	// clone grid
	jQuery('body').delegate('.mg_clone', 'click', function() {
		var grid_id  = jQuery(this).attr('rel');
		
		var new_name = prompt("<?php echo str_replace('"', '\"', __("New grid's name", 'mg_ml')) ?>", "");
    	if(new_name == null) {return false;}
		
		var data = {
			action: 'mg_clone_grid',
			grid_id: grid_id,
			new_name: new_name
		};
			
		jQuery.post(ajaxurl, data, function(response) {
			var resp = jQuery.trim(response); 
	
			if(resp == 'success') {
				mg_grid_pag = 1;
				mg_load_grids();
				
				mg_toast_message('success', "<?php echo mg_sanitize_input( __('Grid cloned', 'mgom_ml')) ?>");
			} else {
				mg_toast_message('error', response);
			}
		});
	});
	
	
	// delete grid
	jQuery('body').delegate('.mg_del_grid', 'click', function() {
		$target_grid_wrap = jQuery(this).parent(); 
		var grid_id  = jQuery(this).attr('id').substr(5);
		
		if(confirm('<?php echo mg_sanitize_input( __('Delete definitively the grid?', 'mg_ml')) ?>')) {
			var data = {
				action: 'mg_del_grid',
				grid_id: grid_id
			};
			
			jQuery.post(ajaxurl, data, function(response) {
				var resp = jQuery.trim(response); 
				
				if(resp == 'success') {
					// if is this one opened
					if(mg_sel_grid == grid_id) {
						jQuery('.mg_grid_content').html('<p><?php echo mg_sanitize_input( __('Select a grid', 'mg_ml')) ?> ..</p>');
						mg_sel_grid = 0;
						
						// savegrid box
						jQuery('#save_grid_box').fadeOut();
					}
					
					$target_grid_wrap.slideUp(function() {
						jQuery(this).remove();
						
						if( jQuery('#man_grid_box .inside .misc-pub-section-last').size() == 0) {
							jQuery('#man_grid_box .inside').html('<p><?php echo mg_sanitize_input( __('No existing grids', 'mg_ml')) ?></p>');
						}
					});	
				}
				else {alert(resp);}
			});
		}
	});
	
	
	// save grid
	jQuery('body').delegate('#save_grid_box input', 'click', function() {
		var items_list = jQuery.makeArray();
		var items_width = jQuery.makeArray();
		var items_height = jQuery.makeArray();
		var items_m_width = jQuery.makeArray();
		var items_m_height = jQuery.makeArray();
		
		// catch data
		jQuery('#visual_builder_wrap .mg_box').each(function() {
			var item_id = jQuery(this).children('input').val();
            items_list.push(item_id);
			
            items_width.push( jQuery(this).find('.select_w').val() );
            items_height.push( jQuery(this).find('.select_h').val() );
			
			items_m_width.push( jQuery(this).find('.select_m_w').val() );
            items_m_height.push( jQuery(this).find('.select_m_h').val() );
        });
		
		// ajax
		var data = {
			action: 'mg_save_grid',
			grid_id: mg_sel_grid,
			items_list: items_list,
			items_width: items_width,
			items_height: items_height,
			items_m_width: items_m_width,
			items_m_height: items_m_height
		};
		
		jQuery('#save_grid_box div').html('<div style="height: 30px;" class="lcwp_loading"></div>');
		
		jQuery.post(ajaxurl, data, function(response) {
			var resp = jQuery.trim(response); 
			jQuery('#save_grid_box div').empty();
			
			if(resp == 'success') {
				mg_toast_message('success', "<?php echo mg_sanitize_input( __('Grid saved', 'mg_ml')) ?>");
			} else {
				mg_toast_message('error', resp);
			}
		});	
	});
	
	
	<!-- masonerize the preview -->
	
	// masonry init
	function masonerize() {
		$container = jQuery('#visual_builder_wrap');
		
		$cont_width = $container.width();
		$container.css('min-height', $cont_width+'px').css('height', 'auto');
		
		$mg_msn_obj = $container.masonry({
			isAnimated: true,
			columnWidth: 1,
			resize: true,
			itemSelector: '.mg_box',
			transitionDuration: '0.15s'
		});
		
		$container.addClass('masonry');
		sortable_masonry();
		
		return true;	
	}
	
	// cells class to decimal percentage
	function get_size(shape) {
		switch(shape) {
		  case '1_10': var perc = 0.1; break;
		  case '1_9': var perc = 0.111; break;
		  case '1_8': var perc = 0.125; break;
		  case '1_7': var perc = 0.142; break;
		  
		  case '5_6': var perc = 0.83; break;
		  case '1_6': var perc = 0.16; break;
		  
		  case '4_5': var perc = 0.80; break;
		  case '3_5': var perc = 0.60; break;
		  case '2_5': var perc = 0.40; break;
		  case '1_5': 
		  case 'auto':var perc = 0.20; break;
		  
		  case '3_4': var perc = 0.75; break;
		  case '1_4': var perc = 0.25; break;
		  
		  case '2_3': var perc = 0.66; break;
		  case '1_3': var perc = 0.33; break;
		  
		  case '1_2': var perc = 0.50; break;
		  default   : var perc = 1; break;
		}
		return perc; 	
	}
	
	
	function get_height($subj) {
		<?php 
		$sizes = mg_sizes();
		$sizes[] = 'auto';
		
		foreach($sizes as $size) : ?> 
		if( $subj.hasClass('row<?php echo $size ?>') ) { 
			var size = get_size('<?php echo $size ?>');
			var hsize = $container.width() * size;
		}
		<?php endforeach; ?>	 

		return hsize;
	}

	
	// apply sizes to boxes
	function size_boxes(target) {
		jQuery(target).each(function(index) {
			jQuery(this).css('height', get_height(jQuery(this)) );
		});
		return true;	
	}
	
	// on page resize
	jQuery(window).resize(function() {
		if(mg_easy_sorting) {return false;}
		
		if(jQuery('.mg_box').size() && jQuery('#visual_builder_wrap.masonry').size()) {
			
			if(typeof(mg_is_resizing) != 'undefined') {clearTimeout(mg_is_resizing);}
			mg_is_resizing = setTimeout(function() {
				size_boxes('.mg_box');
				jQuery('#visual_builder_wrap.masonry').masonry('reload');
			}, 75);
		}
	});
	
	/*** standard layout - live sizing ***/
	// box resize width
	jQuery('body').delegate('#mg_sortable .select_w', 'change', function() {
		$focus_box = jQuery(this).parents('.mg_box');
		
		var orig_w = $focus_box.attr('mg-width');
		var new_w = jQuery(this).val();
		
		$focus_box.removeClass('col'+orig_w);
		$focus_box.addClass('col'+new_w);
		$focus_box.attr('mg-width', new_w);
		
		if(!mg_easy_sorting) {
			size_boxes('.mg_box');
			$container.masonry('reload');
		}
	});
	
	
	// box resize height
	jQuery('body').delegate('#mg_sortable .select_h', 'change', function() {
		$focus_box = jQuery(this).parents('.mg_box');
		
		var orig_h = $focus_box.attr('mg-height');
		var new_h = jQuery(this).val();
		
		$focus_box.removeClass('row'+orig_h);
		$focus_box.addClass('row'+new_h);
		$focus_box.attr('mg-height', new_h);
		
		if(!mg_easy_sorting) {
			size_boxes('.mg_box');
			$container.masonry('reload');
		}
	});
	
	
	/*** mobile layout - live sizing ***/
	// box resize width
	jQuery('body').delegate('#mg_sortable .select_m_w', 'change', function() {
		$focus_box = jQuery(this).parents('.mg_box');
		
		var orig_w = $focus_box.attr('mg-m-width');
		var new_w = jQuery(this).val();
		
		$focus_box.removeClass('col'+orig_w);
		$focus_box.addClass('col'+new_w);
		$focus_box.attr('mg-m-width', new_w);
		
		if(!mg_easy_sorting) {
			size_boxes('.mg_box');
			$container.masonry('reload');
		}
	});
	
	
	// box resize height
	jQuery('body').delegate('#mg_sortable .select_m_h', 'change', function() {
		$focus_box = jQuery(this).parents('.mg_box');
		
		var orig_h = $focus_box.attr('mg-m-height');
		var new_h = jQuery(this).val();
		
		$focus_box.removeClass('row'+orig_h);
		$focus_box.addClass('row'+new_h);
		$focus_box.attr('mg-m-height', new_h);
		
		if(!mg_easy_sorting) {
			size_boxes('.mg_box');
			$container.masonry('reload');
		}
	});
	/*************************************/
	
	
	// mobile mode toggle 
	jQuery('body').delegate('#mg_mobile_view_toggle', 'click', function() {
		if(jQuery('#visual_builder_wrap').hasClass('mg_mobile_builder')) {
			jQuery(this).removeClass('mg_active');
			jQuery(this).find('span').text('OFF');
			jQuery('#visual_builder_wrap').removeClass('mg_mobile_builder').addClass('mg_desktop_builder');
			
			// change items sizing classes from mobile to standard
			jQuery('#mg_sortable .mg_box').each(function() {
                var $s = jQuery(this);
				$s.removeClass('col'+ $s.attr('mg-m-width')).removeClass('row'+ $s.attr('mg-m-height'))
					.addClass('col'+ $s.attr('mg-width')).addClass('row'+ $s.attr('mg-height'));
            });
			
			// bulk sizes 
			jQuery('#mg_bulk_mw_chosen, #mg_bulk_mh_chosen').hide();
			jQuery('#mg_bulk_w_chosen, #mg_bulk_h_chosen').show();
			
			mg_mobile = false;
			jQuery('#mg_sortable .select_w').first().trigger('change');	
		}
		else {
			jQuery(this).addClass('mg_active');
			jQuery(this).find('span').text('ON');
			jQuery('#visual_builder_wrap').removeClass('mg_desktop_builder').addClass('mg_mobile_builder');	
			
			// change items sizing classes from standard to mobile
			jQuery('#mg_sortable .mg_box').each(function() {
                var $s = jQuery(this);

				$s.removeClass('col'+ $s.attr('mg-width')).removeClass('row'+ $s.attr('mg-height'))
					.addClass('col'+ $s.attr('mg-m-width')).addClass('row'+ $s.attr('mg-m-height'));
            });
			
			// bulk sizes 
			jQuery('#mg_bulk_mw_chosen, #mg_bulk_mh_chosen').show();
			jQuery('#mg_bulk_w_chosen, #mg_bulk_h_chosen').hide();
			
			mg_mobile = true;	
			jQuery('#mg_sortable .select_m_w').first().trigger('change');
		}
	});
	
	
	//// bulk sizing system
	// width
	jQuery('body').delegate('#mg_bulk_w_btn', 'click', function() {
		if(confirm("<?php _e('Every grid item will be affected, continue?') ?>")) {
			var val = (mg_mobile) ? jQuery('#mg_bulk_mw').val() : jQuery('#mg_bulk_w').val();
			var dd_class = (mg_mobile) ? '.select_m_w' : '.select_w';
			
			jQuery('#mg_sortable .mg_box '+dd_class+' option').attr('selected', false);
			jQuery('#mg_sortable .mg_box '+dd_class+' option[value="'+val+'"]').attr('selected', 'selected');
			
			jQuery('#mg_sortable '+dd_class).trigger('change');
		}
	});
	
	// height
	jQuery('body').delegate('#mg_bulk_h_btn', 'click', function() {
		if(confirm("<?php _e('Every grid item will be affected, continue?') ?>")) {
			var val = (mg_mobile) ? jQuery('#mg_bulk_mh').val() : jQuery('#mg_bulk_h').val();
			var dd_class = (mg_mobile) ? '.select_m_h' : '.select_h';
			
			if(val == 'auto') {
				jQuery('#mg_sortable .mg_box').not('.mg_inl_slider_type, .mg_inl_video_type').find(dd_class+' option').attr('selected', false);
				jQuery('#mg_sortable .mg_box').not('.mg_inl_slider_type, .mg_inl_video_type').find(dd_class+' option[value="'+val+'"]').attr('selected', 'selected');
			} else {
				jQuery('#mg_sortable .mg_box '+dd_class+' option').attr('selected', false);
				jQuery('#mg_sortable .mg_box '+dd_class+' option[value="'+val+'"]').attr('selected', 'selected');
			}
				
			jQuery('#mg_sortable '+dd_class).trigger('change');
		}
	});
	
	
	// sortable masonry
	function sortable_masonry() {
		jQuery('#mg_sortable').sortable({
			tolerance: "pointer",
			items: '.mg_box',
			handle: 'h3',
			opacity: 0.8,
			distance: 10,
			scrollSensivity: 30,
			delay: 50,
			percentPosition: true,
			placeholder: {
		        element: function(currItem) {
					return jQuery("<li class='mg_box masonry mg_placeholder' style='height: " + currItem.outerHeight(false) + "px; width: " + currItem.outerWidth(false) +"px; left: "+ currItem.css('left') +"; top: "+ currItem.css('top') +";'></li>")[0];
		        },
		        update: function(container, p) {
					return;
		        }
		    },
			helper: function(event, element) {
				var clone = $(element).clone();
				clone.removeClass('mg_box');
				element.removeClass('mg_box');
				return clone;
			},
			change: function(event, ui){
				if(!mg_easy_sorting) {
					mg_defer_masonry_reload();
				}
			},
			beforeStop: function(event, ui){
				ui.item.addClass("mg_box");
				
				if(!mg_easy_sorting) {
					var placeh_pos = ui.placeholder.position();
					ui.item.css('left', placeh_pos.left).css('top', placeh_pos.top);
					
					mg_defer_masonry_reload();
				}
				
				mg_items_num_pos(true);
			}
		});                                
	};
	
	
	function mg_defer_masonry_reload() {
		if(mg_easy_sorting) {return false;}
		if(typeof(defer_masonry_reload) != 'undefined') {clearTimeout(defer_masonry_reload);}
		
		defer_masonry_reload = setTimeout(function() {
			$container.masonry('reload');
		}, 50);	
	}
	
	
	// move item with arrows
	jQuery(document).delegate('.mg_move_item_bw, .mg_move_item_fw', 'click', function(ui) {
		var $item = jQuery(this).parents('li');
		
		// backwards
		if(jQuery(this).hasClass('mg_move_item_bw')) {
			$prev = $item.prev();
			if(!$prev.is('li')) {return false;}	
			
			$item.detach().insertBefore($prev);
		}
		
		// forwards
		else {
			$next = $item.next();
			if(!$next.is('li')) {return false;}	
			
			$item.detach().insertAfter($next);
		}
		
		mg_items_num_pos();
		$container.masonry('reload');
	});
	
	
	// items numeric position 
	var mg_items_num_pos = function(use_delay) {
		var delay = (typeof(use_delay) == 'undefined') ? 0 : 50;
		
		setTimeout(function() {
			jQuery('#mg_sortable .mg_item_num').each(function(i, v) {
			   jQuery(this).text('('+ (i+1) +')'); 
			});	
		}, delay);
	}
	
	
	<!-- other -->


	// toast message for ajax operations
	mg_toast_message = function(type, text) {
		if(!jQuery('#lc_toast_mess').length) {
			jQuery('body').append('<div id="lc_toast_mess"></div>');
			
			jQuery('head').append(
			'<style type="text/css">' +
			'#lc_toast_mess,#lc_toast_mess *{-moz-box-sizing:border-box;box-sizing:border-box}#lc_toast_mess{background:rgba(20,20,20,.2);position:fixed;top:0;right:-9999px;width:100%;height:100%;margin:auto;z-index:99999;opacity:0;filter:alpha(opacity=0);-webkit-transition:opacity .15s ease-in-out .05s,right 0s linear .5s;-ms-transition:opacity .15s ease-in-out .05s,right 0s linear .5s;transition:opacity .15s ease-in-out .05s,right 0s linear .5s}#lc_toast_mess.lc_tm_shown{opacity:1;filter:alpha(opacity=100);right:0;-webkit-transition:opacity .3s ease-in-out 0s,right 0s linear 0s;-ms-transition:opacity .3s ease-in-out 0s,right 0s linear 0s;transition:opacity .3s ease-in-out 0s,right 0s linear 0s}#lc_toast_mess:before{content:"";display:inline-block;height:100%;vertical-align:middle}#lc_toast_mess>div{position:relative;padding:13px 16px!important;border-radius:2px;box-shadow:0 2px 17px rgba(20,20,20,.25);display:inline-block;width:310px;margin:0 0 0 50%!important;left:-155px;top:-13px;-webkit-transition:top .2s linear 0s;-ms-transition:top .2s linear 0s;transition:top .2s linear 0s}#lc_toast_mess.lc_tm_shown>div{top:0;-webkit-transition:top .15s linear .1s;-ms-transition:top .15s linear .1s;transition:top .15s linear .1s}#lc_toast_mess>div>span:after{font-family:dashicons;background:#fff;border-radius:50%;color:#d1d1d1;content:"";cursor:pointer;font-size:23px;height:15px;padding:5px 9px 7px 2px;position:absolute;right:-7px;top:-7px;width:15px}#lc_toast_mess>div:hover>span:after{color:#bbb}#lc_toast_mess .lc_error{background:#fff;border-left:4px solid #dd3d36}#lc_toast_mess .lc_success{background:#fff;border-left:4px solid #7ad03a}' +
			'</style>');	
			
			// close toast message
			jQuery(document.body).off('click tap', '#lc_toast_mess');
			jQuery(document.body).on('click tap', '#lc_toast_mess', function() {
				jQuery('#lc_toast_mess').removeClass('lc_tm_shown');
			});
		}
		
		// setup
		if(type == 'error') {
			jQuery('#lc_toast_mess').empty().html('<div class="lc_error"><p>'+ text +'</p><span></span></div>');	
		} else {
			jQuery('#lc_toast_mess').empty().html('<div class="lc_success"><p>'+ text +'</p><span></span></div>');	
			
			setTimeout(function() {
				jQuery('#lc_toast_mess.lc_tm_shown span').trigger('click');
			}, 2150);	
		}
		
		// use a micro delay to let CSS animations act
		setTimeout(function() {
			jQuery('#lc_toast_mess').addClass('lc_tm_shown');
		}, 30);	
	}
	
});
</script>
