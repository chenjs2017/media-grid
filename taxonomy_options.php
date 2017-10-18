<?php
include_once(MG_DIR . '/functions.php');


// add the order field
add_action('mg_item_categories_add_form_fields','mg_cat_cust_fields', 10, 2 );
add_action('mg_item_categories_edit_form_fields' , "mg_cat_cust_fields", 10, 2);

function mg_cat_cust_fields($tax_data) {
   //check for existing taxonomy meta for term ID
   if(is_object($tax_data)) {
	  $term_id = $tax_data->term_id;
	  $icon = get_option("mg_cat_".$term_id."_icon");
	  $order = (int)get_option("mg_cat_".$term_id."_order");
	}
	else {
		$icon = '';
		$order = 0;
	}
	
	// creator layout
	if(!is_object($tax_data)) :
?>
		<div class="form-field">
            <label><?php _e('Icon', 'mg_ml') ?></label>
            <div class="mg_type_opt_icon_trigger">
            	<i class="fa <?php echo $icon ?>" title="set category icon" style="display: inline-block;"></i>
                <input type="hidden" name="mg_cat_icon" value="<?php echo $icon ?>" autocomplete="off" /> 
            </div>
            <p><?php _e('Category icon, shown before the category name', 'mg_ml') ?></p>
        </div>
		<div class="form-field">
            <label><?php _e('Order', 'mg_ml') ?></label>
           	<input type="text" name="mg_cat_order" value="<?php echo $order ?>" maxlength="3" style="width: 35px;" /> 
            <p><?php _e('Category order used in grid filters', 'mg_ml') ?></p>
        </div>
	<?php
	else:
	?>
    <tr class="form-field">
      <th scope="row" valign="top"><label><?php _e('Icon', 'mg_ml') ?></label></th>
      <td>
        <div class="mg_type_opt_icon_trigger">
            <i class="fa <?php echo $icon ?>" title="set category icon" style="display: inline-block;"></i>
            <input type="hidden" name="mg_cat_icon" value="<?php echo $icon ?>" /> 
        </div>
        <p class="description"><?php _e('Category icon, shown before the category name', 'mg_ml') ?></p>
      </td>
    </tr>
	<tr class="form-field">
      <th scope="row" valign="top"><label><?php _e('Order', 'mg_ml') ?></label></th>
      <td>
        <input type="text" name="mg_cat_order" value="<?php echo $order ?>" maxlength="3" style="width: 35px;" />
        <p class="description"><?php _e('Category order used in grid filters', 'mg_ml') ?></p>
      </td>
    </tr>
<?php
	endif;
}


// save the fields
add_action('created_mg_item_categories', 'save_mg_cat_cust_fields', 10, 2);
add_action('edited_mg_item_categories', 'save_mg_cat_cust_fields', 10, 2);

function save_mg_cat_cust_fields( $term_id ) {
    if ( isset($_POST['mg_cat_icon']) ) {
        update_option("mg_cat_".$term_id."_icon", $_POST['mg_cat_icon']); 
    }
	else {delete_option("mg_cat_".$term_id."_icon");}
	
	
	if ( isset($_POST['mg_cat_order']) ) { 
		update_option("mg_cat_".$term_id."_order", (int)$_POST['mg_cat_order']);
	}
	else {delete_option("mg_cat_".$term_id."_order");}
}


// clean options if a cat is deleted
add_action('delete_term', 'mg_clean_term_options', 10, 4);

function mg_clean_term_options($term, $tt_id, $taxonomy, $deleted_term) {
	delete_option("mg_cat_".$tt_id."_icon");
	delete_option("mg_cat_".$tt_id."_order");
}




/////////////////////////////
// manage taxonomy table
add_filter( 'manage_edit-mg_item_categories_columns', 'mg_cat_cust_fields_column_headers', 10, 1);
add_filter( 'manage_mg_item_categories_custom_column', 'mg_cat_cust_fields_column_row', 10, 3);


// add the table column
function mg_cat_cust_fields_column_headers($columns) {
    $prepend_cols = array();
	$append_cols = array();
	
	$prepend_cols['icon'] = __("Icon", 'mg_ml');
    $append_cols['order'] = __("Order", 'mg_ml');
	
	if(count($prepend_cols) > 0) {
		$columns = array_slice($columns, 0, 1, true) + $prepend_cols + array_slice($columns, 1, count($columns)-1, true);
	}
    return array_merge($columns, $append_cols);
}


// fill the custom column row
function mg_cat_cust_fields_column_row( $row_content, $column_name, $term_id){
	
	if($column_name == 'icon') {
		return '<i class="fa fa-lg '.get_option("mg_cat_".$term_id."_icon").'"></i>';	
	}
	elseif($column_name == 'order') {
		return (int)get_option("mg_cat_".$term_id."_order");
	}
	else {return '&nbsp;';}
}



/////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////
// REMOVE THE PARENT FIELD FOR THE CUSTOM TEXONOMY
// ADD ICON MANAGEMENT SYSTEM
function mg_edit_item_cat_js(){
    include_once(MG_DIR . '/functions.php');
	global $current_screen;
    
	// remove parent field
	if($current_screen->id == 'edit-mg_item_categories') {
		?>
		<script type="text/javascript">
		jQuery(document).ready( function($) {
			jQuery('#parent').parents('.form-field').remove();
		});
		</script>
		<?php
    }
	
	
	// icon wizard
	if($current_screen->id == 'edit-mg_item_categories') {
		?>
        <?php 
		// ICONS LIST CODE 
		echo mg_fa_icon_picker_code( __('no icon', 'mg_ml') );
		?>
		
        <script type="text/javascript">
		// launch option icon wizard
		<?php mg_fa_icon_picker_js(); ?>
		</script>
        <?php
	}
}
add_action('admin_footer-edit-tags.php', 'mg_edit_item_cat_js');
add_action('admin_footer-term.php', 'mg_edit_item_cat_js');


