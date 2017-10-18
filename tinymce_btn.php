<?php
// implement tinymce button

add_action('media_buttons', 'mg_editor_btn', 19);
add_action('admin_footer', 'mg_editor_btn_content');


//action to add a custom button to the content editor
function mg_editor_btn($context) {
	$img = MG_URL . '/img/mg_icon_tinymce.png';
  
	//append the icon
	echo '
	<a class="thickbox" id="mg_editor_btn" title="Media Grid">
	  <img src="'.$img.'" />
	</a>';
}


function mg_editor_btn_content() {
	if(strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'], 'post-new.php')) :
	include_once(MG_DIR . '/functions.php');
?>

    <div id="mg_popup_container" style="display:none;">
      <?php 
	  // get the grids
	  $grids = get_terms( 'mg_grids', 'hide_empty=0' );
	  
	  if(!is_array($grids)) {echo '<span>'. __('No grids found', 'mg_ml') .' ..</span>';}
	  else {
	  ?>
      <ul class="tabNavigation" id="mg_sc_tabs_wrap">
          <li><a href="#mg_sc_main"><?php _e('Main parameters', 'pc_ml') ?></a></li>
          <li><a href="#mg_sc_cust_style"><?php _e('Customizations', 'pc_ml') ?></a></li>
      </ul> 
      
      <div id="mg_sc_main">
      	<table class="widefat" cellspacing="0">
          <tr>
            <td><?php _e('Grid', 'mg_ml') ?></td>
      		<td colspan="2">
            	<select id="mg_grid_choose" data-placeholder="<?php _e('Select a grid', 'mg_ml') ?> .." name="mg_grid" class="lcweb-chosen" autocomplete="off" style="width: 90%;">
				<?php 
                foreach ( $grids as $grid ) {
                    echo '<option value="'.$grid->term_id.'">'.$grid->name.'</option>';
                }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><?php _e('Title under items?', 'mg_ml') ?></td>
      		<td>
              <select id="mg_title_under" data-placeholder="<?php _e('Select an option', 'mg_ml') ?> .." name="mg_title_under" class="lcweb-chosen" autocomplete="off" style="width: 210px;">
				<option value="0"><?php _e('No', 'mg_ml') ?></option>
                <option value="1"><?php _e('Yes - attached to item', 'mg_ml') ?></option>
                <option value="2"><?php _e('Yes - detached from item', 'mg_ml') ?></option>
              </select>
            </td>
            <td><span class="info"><?php _e('Moves overlay title beneath items', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td><?php _e('Enable search?', 'mg_ml') ?></td>
      		<td class="lcwp_form">
            	<input type="checkbox" name="mg_search_bar" value="1" class="mg_popup_ip" id="mg_search_bar" autocomplete="off" />
            </td>
            <td><span class="info"><?php _e('Enables search bar for grid items', 'mg_ml') ?></span></td>
          </tr> 
          
          <tr>
            <td><?php _e('Enable filters?', 'mg_ml') ?></td>
      		<td class="lcwp_form mg_scw_filter_toggle">
            	<input type="checkbox" name="filter_grid" value="1" class="mg_popup_ip" id="mg_filter_grid" autocomplete="off" />
            </td>
            <td><span class="info"><?php _e('Allows items filtering by category', 'mg_ml') ?></span></td>
          </tr> 
          <tr class="mg_scw_ff" style="display: none;">
            <td><?php _e('Filters position', 'mg_ml') ?></td>
      		<td class="lcwp_form" colspan="2">
            	<select id="mg_filters_align" data-placeholder="<?php _e('Select an overlay', 'mg_ml') ?> .." name="mg_filters_align" class="lcweb-chosen" style="width: 90%;">
					<option value="top">(<?php _e('On top', 'mg_ml') ?>)</option>
                    <option value="left">(<?php _e('Left side', 'mg_ml') ?>)</option>
                    <option value="right">(<?php _e('Right side', 'mg_ml') ?>)</option>
                </select>    
            </td>
          </tr> 
          <tr class="mg_scw_ff" style="display: none;">
            <td><?php _e('Hide "All" filter?', 'mg_ml') ?></td>
      		<td class="lcwp_form">
            	<input type="checkbox" name="hide_all" value="1" class="mg_popup_ip" id="mg_hide_all" autocomplete="off" />
            </td>
            <td><span class="info"><?php _e('Hides the "All" option from filters', 'mg_ml') ?></span></td>
          </tr>
          <tr class="mg_scw_ff" style="display: none;">
            <td><?php _e('Default filter', 'mg_ml') ?></td>
      		<td class="lcwp_form" colspan="2">
            	<select id="mg_def_filter" data-placeholder="<?php _e('Select a filter', 'mg_ml') ?> .." name="mg_def_filter" class="lcweb-chosen" autocomplete="off" style="width: 90%;">
            	</select>
            </td>
          </tr>
          
          <tr>
            <td><?php _e('Relative Width', 'mg_ml') ?></td>
      		<td><input type="text" name="mg_grid_w" id="mg_grid_w" class="mg_scsw_number lcwp_slider_input" maxlength="4" autocomplete="off" /> px</td>
            <td><span class="info"><?php _e('Relative width to calculate cells size. Leave empty to auto-calculate', 'mg_ml') ?></span></td>
          </tr> 
          
          <?php 
		  ///// OVERLAY MANAGER ADD-ON ///////////
		  ////////////////////////////////////////
		  if(defined('MGOM_DIR')) : ?>
          <tr>
            <td><?php _e('Custom Overlay', 'mg_ml') ?></td>
      		<td colspan="2">
            	<select id="mg_custom_overlay" data-placeholder="<?php _e('Select an overlay', 'mg_ml') ?> .." name="mg_custom_overlay" class="lcweb-chosen" style="width: 370px;">
					<option value="">(<?php _e('default one', 'mg_ml') ?>)</option>
					
					<?php
					$overlays = get_terms('mgom_overlays', 'hide_empty=0');
					foreach($overlays as $ol) {
						  $sel = (isset($fdata) && $ol->term_id == $fdata['mg_default_overlay']) ? 'selected="selected"' : '';
						  echo '<option value="'.$ol->term_id.'" '.$sel.'>'.$ol->name.'</option>'; 
					}
					?>
              </select>
            </td>
          </tr> 
          <?php endif;
		  ////////////////////////////////////////
		  ?>   
        </table>  
      </div>   
      
      
      <div id="mg_sc_cust_style">
      	<table class="widefat" cellspacing="0">
          <tr>
            <td><?php _e('Grid Cells Margin', 'mg_ml') ?></td>
      		<td><input type="text" name="mg_cells_margin" id="mg_cells_margin" class="mg_scsw_number lcwp_slider_input" autocomplete="off" /> px</td>
            <td><span class="info"><?php _e('Custom cells margin. Leave empty to use default value', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td><?php _e('Image Border Size', 'mg_ml') ?></td>
      		<td><input type="text" name="mg_border_w" id="mg_border_w" class="mg_scsw_number lcwp_slider_input" autocomplete="off" /> px</td>
            <td><span class="info"><?php _e('Custom images border. Leave empty to use default value', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td><?php _e('Image Border Color', 'mg_ml') ?></td>
      		<td>
            	<div class="lcwp_colpick">
                	<span class="lcwp_colblock"></span>
                	<input type="text" name="mg_border_color" id="mg_border_color" value="" autocomplete="off" />
                </div>
            </td>
            <td><span class="info"><?php _e('Custom border color. Leave empty to use default value', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td><?php _e('Cells Border Radius', 'mg_ml') ?></td>
      		<td><input type="text" name="mg_cells_radius" id="mg_cells_radius" class="mg_scsw_number lcwp_slider_input" autocomplete="off" /> px</td>
            <td><span class="info"><?php _e('Custom cells border radius. Leave empty to use default value', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td><?php _e("Display outer cell's border?", 'mg_ml') ?></td>
      		<td>
            	<select id="mg_outline" data-placeholder="<?php _e('Select an option', 'mg_ml') ?> .." name="mg_outline" class="lcweb-chosen" autocomplete="off" style="width: 120px;">
            		<option value=""><?php _e('As default', 'mg_ml') ?></option>
                    <option value="1"><?php _e('Yes', 'mg_ml') ?></option>
                    <option value="0"><?php _e('No', 'mg_ml') ?></option>
                </select>
            </td>
            <td><span class="info"><?php _e('Whether to display outer border', 'mg_ml') ?></span></td>
          </tr> 
          <tr>
            <td><?php _e('Outer Border Color', 'mg_ml') ?></td>
      		<td>
            	<div class="lcwp_colpick">
                	<span class="lcwp_colblock"></span>
                	<input type="text" name="mg_outline_color" id="mg_outline_color" value="" autocomplete="off" />
                </div>
            </td>
            <td><span class="info"><?php _e('Custom outer border color. Leave empty to use default value', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td><?php _e("Display cells shadow?", 'mg_ml') ?></td>
      		<td>
            	<select id="mg_shadow" data-placeholder="<?php _e('Select an option', 'mg_ml') ?> .." name="mg_shadow" class="lcweb-chosen" autocomplete="off" style="width: 120px;">
            		<option value=""><?php _e('As default', 'mg_ml') ?></option>
                    <option value="1"><?php _e('Yes', 'mg_ml') ?></option>
                    <option value="0"><?php _e('No', 'mg_ml') ?></option>
                </select>
            </td>
            <td><span class="info"><?php _e('Whether to display cells shadow', 'mg_ml') ?></span></td>
          </tr>
          <tr>
            <td><?php _e('Text under images color', 'mg_ml') ?></td>
      		<td>
            	<div class="lcwp_colpick">
                	<span class="lcwp_colblock"></span>
                	<input type="text" name="mg_txt_under_color" id="mg_txt_under_color" value="" autocomplete="off" />
                </div>
            </td>
            <td><span class="info"><?php _e('Custom color for text under images. Leave empty to use default value', 'mg_ml') ?></span></td>
          </tr> 
        </table>
      </div>  
      <?php } ?>
      
      <p style="padding-left: 25px;">
      	<input type="button" value="<?php _e('Insert Grid', 'mg_ml') ?>" name="mg_insert_grid" id="mg_insert_grid" class="button-primary" />
      </p>  
    </div>
	
    
    <?php 
	// javascript var containing grid filters list 
	if(is_array($grids)) :
	?>
	<script type="text/javascript">
	mg_def_f = jQuery.makeArray();
	<?php 
	foreach($grids as $grid) {
		$arr = array('' => __('no initial filter', 'mg_ml'));
		
		$grid_data = mg_get_grid_data($grid->term_id); 
		$filters = mg_grid_terms_data($grid->term_id, $grid_data['cats'], $return = 'array');
		
		if(is_array($filters)) {
			foreach($filters as $filter) {
				$arr[ $filter['id'] ] = $filter['name'];	
			}
		}
		
		echo 'mg_def_f["'.$grid->term_id.'"] = '. json_encode($arr).';'; 
	}
	?>
	</script>
    <?php endif; ?>



    <?php // SCRIPTS ?>
    <script src="<?php echo MG_URL; ?>/js/functions.js" type="text/javascript"></script>
	<script src="<?php echo MG_URL; ?>/js/colpick/js/colpick.min.js" type="text/javascript"></script>
	<script src="<?php echo MG_URL; ?>/js/chosen/chosen.jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo MG_URL; ?>/js/lc-switch/lc_switch.min.js" type="text/javascript"></script>
<?php
	endif;
	return true;
}
