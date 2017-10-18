<?php
//  visual composer integration


function mg_on_visual_composer() {
    include_once(MG_DIR .'/admin_menu.php'); // be sure tax are registered
	register_taxonomy_mg_grids();
	register_cpt_mg_item();
	
	$grids = get_terms( 'mg_grids', 'hide_empty=0' );
	$item_cats = get_terms( 'mg_item_categories', 'hide_empty=0' );
	if(!is_array($grids)) {return false;}
	
	// grids array
	$grids_arr = array(); 
	foreach($grids as $grid) {
    	$grids_arr[ $grid->name ] = $grid->term_id;
    }
	
	// filters array (use full list for now)
	$filters_arr = array(
		__('no initial filter', 'mg_ml') => ''
	); 
	foreach($item_cats as $cat) {
    	$filters_arr[ $cat->name ] = $cat->term_id;
    }
	
	
	// filters enabling dependency
	$filters_dependency = array(
		'element'	=> 'filter',
		'value'		=> array('1'),
		'not_empty'	=> true,
	);
	
	
	// parameters
	$params = array(
		array(
			'group'			=> __('Main Parameters', 'mg_ml'),
			'type' 			=> 'dropdown',
			'class' 		=> 'mg_vc_cat',
			'heading' 		=> __('Grid', 'mg_ml'),
			'param_name' 	=> 'cat',
			'admin_label' 	=> true,
			'value' 		=> $grids_arr,
			'description'	=> __('Select a grid', 'mg_ml'),
		),
		array(
			'group'			=> __('Main Parameters', 'mg_ml'),
			'type' 			=> 'checkbox',
			'class' 		=> 'mg_search_bar',
			'param_name' 	=> 'search',
			'value' 		=> array(
				'<strong>'. __('Enable search?', 'mg_ml') .'</strong>' => 1
			),
			'description'	=> __('Enables search bar for grid items', 'mg_ml'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
		),
		array(
			'group'			=> __('Main Parameters', 'mg_ml'),
			'type' 			=> 'dropdown',
			'class' 		=> 'mg_title_under',
			'heading' 		=> __('Title under items?', 'mg_ml'),
			'param_name' 	=> 'title_under',
			'admin_label' 	=> true,
			'value' 		=> array(
				__('No', 'mg_ml') => 0,
				__('Yes - attached to item', 'mg_ml') => 1,
				__('Yes - detached from item', 'mg_ml') => 2,
			),
			//'description'	=> __('Moves overlay title beneath items', 'mg_ml'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
		),
		array(
			'group'			=> __('Main Parameters', 'mg_ml'),
			'type' 			=> 'checkbox',
			'class' 		=> 'mg_filter_grid',
			'param_name' 	=> 'filter',
			'value' 		=> array(
				'<strong>'. __('Enable filters?', 'mg_ml') .'</strong>' => 1
			),
			'description'	=> __('Allows items filtering by category', 'mg_ml'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
		),
		
		array(
			'group'			=> __('Main Parameters', 'mg_ml'),
			'dependency'	=> $filters_dependency,
			'type' 			=> 'dropdown',
			'class' 		=> 'mg_filters_align',
			'heading' 		=> __('Filters position', 'mg_ml'),
			'param_name' 	=> 'filters_align',
			'value' 		=> array(
				__('On top', 'mg_ml') => 'top',
				__('Left side', 'mg_ml') => 'left',
				__('Right side', 'mg_ml') => 'right',
			),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
		),
		array(
			'group'			=> __('Main Parameters', 'mg_ml'),
			'dependency'	=> $filters_dependency,
			'type' 			=> 'checkbox',
			'class' 		=> 'mg_hide_all',
			'param_name' 	=> 'hide_all',
			'value' 		=> array(
				'<strong>'. __('Hide "All" filter?', 'mg_ml') .'</strong>' => 1
			),
			'description'	=> __('Hides the "All" option from filters', 'mg_ml'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
		),
		array(
			'group'			=> __('Main Parameters', 'mg_ml'),
			'dependency'	=> $filters_dependency,
			'type' 			=> 'dropdown',
			'class' 		=> 'mg_def_filter',
			'heading' 		=> __('Default filter', 'mg_ml'),
			'param_name' 	=> 'def_filter',
			'value' 		=> $filters_arr,
			'description'	=> __('Choose a default filter to show the grid', 'mg_ml'),
		),
		
		array(
			'group'			=> __('Main Parameters', 'mg_ml'),
			'type' 			=> 'textfield',
			'class' 		=> 'mg_r_width',
			'heading' 		=> __('Relative Width', 'mg_ml'),
			'param_name' 	=> 'r_width',
			'admin_label' 	=> true,
			'value' 		=> '',
			'description'	=> __('Relative width (in pixels) to calculate cells size. Leave empty to auto-calculate', 'mg_ml'),
		),
		
		
		/* STYLING */
		array(
			'group'			=> __('Customizations', 'mg_ml'),
			'type' 			=> 'textfield',
			'class' 		=> 'mg_cells_margin',
			'heading' 		=> __('Grid Cells Margin', 'mg_ml'),
			'param_name' 	=> 'cell_margin',
			'admin_label' 	=> true,
			'value' 		=> '',
			'description'	=> __('Custom cells margin in pixels. Leave empty to use default value', 'mg_ml'),
		),
		array(
			'group'			=> __('Customizations', 'mg_ml'),
			'type' 			=> 'textfield',
			'class' 		=> 'mg_border_w',
			'heading' 		=> __('Image Border Size', 'mg_ml'),
			'param_name' 	=> 'border_w',
			'admin_label' 	=> true,
			'value' 		=> '',
			'description'	=> __('Custom images border in pixels. Leave empty to use default value', 'mg_ml'),
		),
		array(
			'group'			=> __('Customizations', 'mg_ml'),
			'type' 			=> 'colorpicker',
			'class' 		=> 'mg_border_color',
			'heading' 		=> __('Image Border Color', 'mg_ml'),
			'param_name' 	=> 'border_col',
			'admin_label' 	=> true,
			'value' 		=> '',
			'description'	=> __('Custom border color. Leave empty to use default value', 'mg_ml'),
		),
		array(
			'group'			=> __('Customizations', 'mg_ml'),
			'type' 			=> 'textfield',
			'class' 		=> 'mg_cells_radius',
			'heading' 		=> __('Cells Border Radius', 'mg_ml'),
			'param_name' 	=> 'border_rad',
			'admin_label' 	=> true,
			'value' 		=> '',
			'description'	=> __('Custom cells border radius in pixels. Leave empty to use default value', 'mg_ml'),
		),
		array(
			'group'			=> __('Customizations', 'mg_ml'),
			'type' 			=> 'dropdown',
			'class' 		=> 'mg_outline',
			'heading' 		=> __("Display outer cell's border?", 'mg_ml'),
			'param_name' 	=> 'outline',
			'admin_label' 	=> true,
			'value' 		=> array(
				__('As default', 'mg_ml') => '',
				__('Yes', 'mg_ml') => 1,
				__('No', 'mg_ml') => 0,
			),
		),
		array(
			'group'			=> __('Customizations', 'mg_ml'),
			'type' 			=> 'colorpicker',
			'class' 		=> 'mg_outline_color',
			'heading' 		=> __('Outer Border Color', 'mg_ml'),
			'param_name' 	=> 'outline_col',
			'admin_label' 	=> true,
			'value' 		=> '',
			'description'	=> __('Custom outer border color. Leave empty to use default value', 'mg_ml'),
		),
		array(
			'group'			=> __('Customizations', 'mg_ml'),
			'type' 			=> 'dropdown',
			'class' 		=> 'mg_shadow',
			'heading' 		=> __("Display cells shadow?", 'mg_ml'),
			'param_name' 	=> 'shadow',
			'admin_label' 	=> true,
			'value' 		=> array(
				__('As default', 'mg_ml') => '',
				__('Yes', 'mg_ml') => 1,
				__('No', 'mg_ml') => 0,
			),
		),
		array(
			'group'			=> __('Customizations', 'mg_ml'),
			'type' 			=> 'colorpicker',
			'class' 		=> 'mg_txt_under_color',
			'heading' 		=> __('Text under images color', 'mg_ml'),
			'param_name' 	=> 'txt_under_col',
			'admin_label' 	=> true,
			'value' 		=> '',
			'description'	=> __('Custom color for text under images. Leave empty to use default value', 'mg_ml'),
		),
	);
	
	
	///// OVERLAY MANAGER ADD-ON ///////////
	if(defined('MGOM_DIR')) {
		register_taxonomy_mgom(); // be sure tax are registered
		$overlays = get_terms('mgom_overlays', 'hide_empty=0');
		
		$ol_arr = array(
			__('default one', 'mg_ml') => ''
		);
		foreach($overlays as $ol) {
			$ol_arr[ $ol->name ] = $ol->term_id;	
		}
		
		$params[] = array(
			'group'			=> __('Main Parameters', 'mg_ml'),
			'type' 			=> 'dropdown',
			'class' 		=> 'mg_custom_overlay',
			'heading' 		=> __('Custom Overlay', 'mg_ml'),
			'param_name' 	=> 'overlay',
			'admin_label' 	=> true,
			'value' 		=> $ol_arr,
		);
	}
	///////////////////////////////////////
	
		  
	
	// compile
	vc_map(
        array(
            'name' 			=> 'Media Grid',
			'description'	=> __("Displays LCweb's Media Grid", 'mg_ml'),
            'base' 			=> 'mediagrid',
            'category' 		=> __("Content", "mg_ml"),
			'icon'			=> MG_URL .'/img/vc_icon.png',
            'params' 		=> $params,
			//'custom_markup' => load_template( MG_DIR .'/builders_integration/vc_custom_markup.php')
        )
    );
}
add_action( 'vc_before_init', 'mg_on_visual_composer');