<?php

/**
 * Element Controls
 */
 
 
include_once(MG_DIR .'/admin_menu.php'); // be sure tax are registered
register_taxonomy_mg_grids();
register_cpt_mg_item();

$grids = get_terms( 'mg_grids', 'hide_empty=0' );
$item_cats = get_terms( 'mg_item_categories', 'hide_empty=0' );
if(!is_array($grids)) {return false;}

// grids array
$grids_arr = array(); 
foreach($grids as $grid) {
	$grids_arr[] = array(
		'value' => $grid->term_id,
		'label' => $grid->name
	);
}

// filters array (use full list for now)
$filters_arr = array(
	0 => array(
		'value' => '',
		'label' => __('no initial filter', 'mg_ml')
	)
); 
foreach($item_cats as $cat) {
	$filters_arr[] = array(
		'value' => $cat->term_id,
		'label' => $cat->name
	);
}
 
 


/* FIELDS */
$fields =  array(
	'cat' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Grid', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => $grids_arr
		),
	),

	'title_under' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Title under items?', 'mg_ml'),
			'tooltip' => __('Moves overlay title beneath items', 'mg_ml'),
		),
		'options' => array(
			'choices' => array(
				array('value' => 0, 'label' => __('No', 'mg_ml')),
				array('value' => 1, 'label' => __('Yes - attached to item', 'mg_ml')),
				array('value' => 2, 'label' => __('Yes - detached from item', 'mg_ml')),
			)
		),
	),

	'search' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __('Enable search?', 'mg_ml'),
			'tooltip' => __('Enables search bar for grid items', 'mg_ml'),
		),
	),

	
	/************************/
	'filter' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __('Enable filters?', 'mg_ml'),
			'tooltip' => __('Allows items filtering by category', 'mg_ml'),
		),
	),

	'filters_align' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Filters position', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => array(
				array('value' => 'top', 'label' => __('on top', 'mg_ml')),
				array('value' => 'left', 'label' => __('left side', 'mg_ml')),
				array('value' => 'right', 'label' => __('right side', 'mg_ml')),
			)
		),
	),
	
	'hide_all' => array(
		'type'    => 'toggle',
		'ui' => array(
			'title'   => __('Hide "All" filter?', 'mg_ml'),
			'tooltip' => __('Hides the "All" option from filters', 'mg_ml'),
		),
	),
	
	'def_filter' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Default filter', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => $filters_arr
		),
	),
	/***********************/

	
	'r_width' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __('Relative Width', 'mg_ml'),
			'tooltip' => __('Relative width (in pixels) to calculate cells size.<br/>Leave empty to auto-calculate', 'mg_ml'),
		),
	),
	
	
	
	/*** STYLING ***/
	'cell_margin' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __('Grid Cells Margin', 'mg_ml'),
			'tooltip' => __('Custom cells margin in pixels. Leave empty to use default value', 'mg_ml'),
		),
	),
	'border_w' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __('Image Border Size', 'mg_ml'),
			'tooltip' => __('Custom images border in pixels. Leave empty to use default value', 'mg_ml'),
		),
	),
	'border_col' => array(
		'type'    => 'color',
		'ui' => array(
			'title'   => __('Image Border Color', 'mg_ml'),
			'tooltip' => __('Custom border color. Leave empty to use default value', 'mg_ml'),
		),
	),
	'border_rad' => array(
		'type'    => 'number',
		'ui' => array(
			'title'   => __('Cells Border Radius', 'mg_ml'),
			'tooltip' => __('Custom cells border radius in pixels. Leave empty to use default value', 'mg_ml'),
		),
	),
	'outline' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __("Display outer cell's border?", 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => array(
				array('value' => '', 'label' => __('As default', 'mg_ml')),
				array('value' => 1, 'label' => __('Yes', 'mg_ml')),
				array('value' => 0, 'label' => __('No', 'mg_ml')),
			)
		),
	),
	'outline_col' => array(
		'type'    => 'color',
		'ui' => array(
			'title'   => __('Outer Border Color', 'mg_ml'),
			'tooltip' => __('Custom outer border color. Leave empty to use default value', 'mg_ml'),
		),
	),
	'shadow' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __("Display cells shadow?", 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => array(
				array('value' => '', 'label' => __('As default', 'mg_ml')),
				array('value' => 1, 'label' => __('Yes', 'mg_ml')),
				array('value' => 0, 'label' => __('No', 'mg_ml')),
			)
		),
	),
	'txt_under_col' => array(
		'type'    => 'color',
		'ui' => array(
			'title'   => __('Text under images color', 'mg_ml'),
			'tooltip' => __('Custom color for text under images. Leave empty to use default value', 'mg_ml'),
		),
	),
);



///// OVERLAY MANAGER ADD-ON ///////////
if(defined('MGOM_DIR')) {
	register_taxonomy_mgom(); // be sure tax are registered
	$overlays = get_terms('mgom_overlays', 'hide_empty=0');
	
	$ol_arr = array(
		0 => array(
			'value' => '',
			'label' => __('default one', 'mg_ml')
		)
	);
	foreach($overlays as $ol) {
		$ol_arr[] = array(
			'value' => $ol->term_id,
			'label' => $ol->name
		);
	}
	
	$fields['overlay'] = array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __('Custom Overlay', 'mg_ml'),
			'tooltip' => '',
		),
		'options' => array(
			'choices' => $ol_arr
		),
	);
}
////////////////////////////////////////


return $fields;
