jQuery(document).ready(function($) {
	
	// switch theme menu pages
	jQuery('.lcwp_opt_menu').click(function() {
		curr_opt = jQuery('.curr_item').attr('id').substr(5);
		var opt_id = jQuery(this).attr('id').substr(5);
		
		if(!jQuery('#form_'+opt_id).is(':visible')) {
			// remove curr
			jQuery('.curr_item').removeClass('curr_item');
			jQuery('#form_'+curr_opt).hide();
			
			// show selected
			jQuery(this).addClass('curr_item');
			jQuery('#form_'+opt_id).show();	
		}
	});
	
	
	// colorpicker
	mg_colpick = function () {
		jQuery('.lcwp_colpick input').each(function() {
			var curr_col = jQuery(this).val().replace('#', '');
			jQuery(this).colpick({
				layout:'rgbhex',
				submit:0,
				color: curr_col,
				onChange:function(hsb,hex,rgb, el, fromSetColor) {
					if(!fromSetColor){ 
						jQuery(el).val('#' + hex);
						jQuery(el).parents('.lcwp_colpick').find('.lcwp_colblock').css('background-color','#'+hex);
					}
				}
			}).keyup(function(){
				jQuery(this).colpickSetColor(this.value);
				jQuery(this).parents('.lcwp_colpick').find('.lcwp_colblock').css('background-color', this.value);
			});  
		});
	}
	mg_colpick();
	
	
	// sliders
	mg_slider_opt = function() {
		var a = 0; 
		jQuery('.lcwp_slider').each(function(idx, elm) {
			var sid = 'slider'+a;
			jQuery(this).attr('id', sid);	
		
			var svalue = parseInt(jQuery("#"+sid).next('input').val());
			var minv = parseInt(jQuery("#"+sid).attr('min'));
			var maxv = parseInt(jQuery("#"+sid).attr('max'));
			var stepv = parseInt(jQuery("#"+sid).attr('step'));
			
			jQuery('#' + sid).slider({
				range: "min",
				value: svalue,
				min: minv,
				max: maxv,
				step: stepv,
				slide: function(event, ui) {
					jQuery('#' + sid).next().val(ui.value);
				}
			});
			
			// workaround to keep user-specified value (specially if empty)
			jQuery('#'+sid).next('input').on('keyup', function() {
				var val = jQuery(this).val();
				if(!jQuery.isNumeric(val) ) {val = ''}
				
				jQuery(this).attr('user_val', val);
			});
			
			// what if slider forces a value but user wants another one?
			jQuery('#'+sid).next('input').change(function() {
				var $subj = jQuery(this);
				var val = parseInt($subj.val());
				
				var minv = parseInt(jQuery("#"+sid).attr('min'));
				var maxv = parseInt(jQuery("#"+sid).attr('max'));
				
				if($subj.attr('user_val') != 'undefined') {
					setTimeout(function() {
						var user_val = $subj.attr('user_val');
						
						if(user_val === '') {
							jQuery('#'+sid).slider("value", minv);		
						} else {
							jQuery('#'+sid).slider("value", user_val);		
						}
						 
						$subj.val(user_val);
						$subj.removeAttr('user_val'); 
					}, 1);	
				}
			});
			
			// if no value specified - set slider to have min value
			if(jQuery("#"+sid).next('input').val() === '') {
				jQuery('#'+sid).slider("value", minv);	
			}
			
			a = a + 1;
		});
	}
	mg_slider_opt();
	
	
	// custom checks
	mg_live_checks = function() {
		jQuery('.ip-checkbox').lc_switch('YES', 'NO');
	}
	mg_live_checks();
	
	
	// chosen
	mg_live_chosen = function() {
		jQuery('.lcweb-chosen').each(function() {
			var w = jQuery(this).css('width');
			jQuery(this).chosen({width: w}); 
		});
		jQuery(".lcweb-chosen-deselect").chosen({allow_single_deselect:true});
	}
	mg_live_chosen();
	
	
	//////////////////////////////////////////
	// tinymce btn

	jQuery('body').delegate('#mg_editor_btn', "click", function () {
		setTimeout(function() {
			var mg_H = 520;
			var mg_W = 555;
			
			tb_show( 'Media Grid', '#TB_inline?height='+mg_H+'&width='+mg_W+'&inlineId=mg_popup_container' );

			jQuery('#TB_window').addClass('mg_tinymce_lb_wrap').removeAttr('style');
			jQuery('#TB_ajaxContent').addClass('mg_tinymce_lb').removeAttr('style');
			jQuery('#TB_overlay').css('z-index', 999998);
			
			jQuery(".mg_tinymce_lb").tabs({
				active: 0	
			});
				
			jQuery('.mg_popup_ip').lc_switch('YES', 'NO'); // custom checks
			mg_mce_colpick(); // colorpicker
			
			jQuery('#TB_ajaxContent #mg_grid_choose').trigger('change');	
		}, 1);	
	});
	
	
	// colorpicker
	function mg_mce_colpick() {
		jQuery('.mg_tinymce_lb_wrap .lcwp_colpick input').each(function() {
			var curr_col = jQuery(this).val().replace('#', '');
			jQuery(this).colpick({
				layout:'rgbhex',
				submit:0,
				color: curr_col,
				onChange:function(hsb,hex,rgb, el, fromSetColor) {
					if(!fromSetColor){ 
						jQuery(el).val('#' + hex);
						jQuery(el).parents('.lcwp_colpick').find('.lcwp_colblock').css('background-color','#'+hex);
					}
				}
			}).keyup(function(){
				jQuery(this).colpickSetColor(this.value);
				jQuery(this).parents('.lcwp_colpick').find('.lcwp_colblock').css('background-color', this.value);
			});  
		});
	}
	
	
	// toggle filters options visibility
	jQuery('body').delegate('.mg_scw_filter_toggle input', 'lcs-statuschange', function() {
		if( jQuery(this).is(':checked') ) {
			jQuery('.mg_scw_ff').slideDown('fast');	
		} else {
			jQuery('.mg_scw_ff').slideUp('fast');	
		}
	});
	
	
	// populate default filter dropdown on grid chosing
	jQuery('body').delegate('#TB_ajaxContent #mg_grid_choose', 'change', function() {
		var sel = jQuery(this).val();
		jQuery('#TB_ajaxContent #mg_def_filter').empty();

		jQuery.each(mg_def_f[sel], function(k, v) {
			jQuery('#TB_ajaxContent #mg_def_filter').prepend('<option value="'+ k +'">'+ v +'</option>');
		});
		jQuery('#TB_ajaxContent #mg_def_filter option').first().attr('selected', 'selected');
		
		jQuery("#TB_ajaxContent #mg_def_filter").trigger("chosen:updated");
	});
	
	
	// add the shortcode to the grid
	jQuery('body').delegate('.mg_tinymce_lb_wrap #mg_insert_grid', "click", function (e) {
		var $subj = jQuery('.mg_tinymce_lb_wrap');
		
		var gid = $subj.find('#mg_grid_choose').val();
		var sc = '[mediagrid cat="'+gid+'"';
		
		//  titles under
		if( $subj.find('#mg_title_under').val() != 0) {
			sc += ' title_under="'+ $subj.find('#mg_title_under').val() +'"';
		}
		
		//  search bar
		if( $subj.find('#mg_search_bar').is(':checked') ) {
			sc += ' search="1"';
		}

		// filter
		if( $subj.find('#mg_filter_grid').is(':checked') ) {
			var filter = 1;
			sc += ' filter="'+filter+'"';
		} 
		else {var filter = 0;}
		
		
		// filter options
		if(filter) {
			// hide "all" filter
			if( $subj.find('#mg_filters_align').val() != 'top' ) {
				sc += ' filters_align="'+ $subj.find('#mg_filters_align').val() +'"';
			}
			
			// hide "all" filter
			if( $subj.find('#mg_hide_all').is(':checked') ) {
				sc += ' hide_all="1"';
			}
			
			// select default filter
			if( $subj.find('#mg_def_filter').val() != '' ) {
				sc += ' def_filter="'+ $subj.find('#mg_def_filter').val() +'"';
			}
		}
		
		
		// relative width
		if( jQuery.trim($subj.find('#mg_grid_w').val()) != '' ) {
			sc += ' r_width="'+ jQuery.trim($subj.find('#mg_grid_w').val()) +'"';
		}
		
		// custom overlay - add-on
		if( $subj.find('#mg_custom_overlay').size() > 0 && $subj.find('#mg_custom_overlay').val() != '' ) {
			sc += ' overlay="'+ $subj.find('#mg_custom_overlay').val() +'"';	
		}

		////////////////////////////////////////////
		
		// custom cells margin
		if($subj.find('#mg_cells_margin').val() != '') {
			sc += ' cell_margin="'+ parseInt($subj.find('#mg_cells_margin').val()) +'"';	
		}
		
		// custom borders width
		if($subj.find('#mg_border_w').val() != '') {
			sc += ' border_w="'+ parseInt($subj.find('#mg_border_w').val()) +'"';	
		}
		
		// custom borders color
		if($subj.find('#mg_border_color').val() != '') {
			sc += ' border_col="'+ $subj.find('#mg_border_color').val() +'"';	
		}
		
		// custom border radius
		if($subj.find('#mg_cells_radius').val() != '') {
			sc += ' border_rad="'+ parseInt($subj.find('#mg_cells_radius').val()) +'"';	
		}
		
		// custom outline display
		if($subj.find('#mg_outline').val() != '') {
			sc += ' outline="'+ parseInt($subj.find('#mg_outline').val()) +'"';	
		}
		
		// custom outline color
		if($subj.find('#mg_outline_color').val() != '') {
			sc += ' outline_col="'+ $subj.find('#mg_outline_color').val() +'"';	
		}

		// custom shadow display
		if($subj.find('#mg_shadow').val() != '') {
			sc += ' shadow="'+ parseInt($subj.find('#mg_shadow').val()) +'"';	
		}

		// custom outline color
		if($subj.find('#mg_txt_under_color').val() != '') {
			sc += ' txt_under_col="'+ $subj.find('#mg_txt_under_color').val() +'"';	
		}

		////////////////////////////////////////////

		sc += ']';
		
		// inserts the shortcode into the active editor
		if( jQuery('#wp-content-editor-container > textarea').is(':visible') ) {
			var val = jQuery('#wp-content-editor-container > textarea').val() + sc;
			jQuery('#wp-content-editor-container > textarea').val(val);	
		}
		else {tinymce.activeEditor.execCommand('mceInsertContent', false, sc);}
		
		// closes Thickbox
		tb_remove();
	});
	
});