<?php
////////////////////////////////////
// DYNAMICALLY CREATE THE CSS //////
////////////////////////////////////
include_once(MG_DIR . '/functions.php');
include_once(MG_DIR . '/classes/loaders_switch.php');


// remove the HTTP/HTTPS for SSL compatibility
$safe_baseurl = str_replace(array('http:', 'https:', 'HTTP:', 'HTTPS:'), '', MG_URL);

$mobile_treshold 	= get_option('mg_mobile_treshold', 800);
$cells_margin 		= (int)get_option('mg_cells_margin');
$filters_align 		= get_option('mg_filters_align', 'left');
$side_filters_w 	= (int)get_option('mg_side_filters_width', 130);

?>
@import url("<?php echo $safe_baseurl; ?>/css/frontend.min.css?ver=<?php echo MG_VER ?>");

@import url("<?php echo $safe_baseurl; ?>/js/jquery.galleria/themes/mediagrid/galleria.mediagrid.css");
@import url("<?php echo $safe_baseurl; ?>/js/lc-micro-slider/lc-micro-slider.css");


/* preloader */
<?php mg_loaders_switch() ?>

/* cells margin */
.mg_box { 
  padding: <?php echo $cells_margin; ?>px; 
}

/* cells shadow  */
.mg_container:not(.mg_tu_detach) .mg_shadow_div,
.mg_container.mg_tu_detach .img_wrap {
	<?php if(get_option('mg_cells_shadow')) echo 'box-shadow: 0px 0px 3px rgba(25, 25, 25, 0.4);'; ?>
}

/* images border */
.img_wrap {
	border-style: solid;
    padding: <?php echo (int)get_option('mg_cells_img_border'); ?>px;
	
	<?php
	if(get_option('mg_img_border_color') && get_option('mg_img_border_opacity')) {
		$alpha_val = (int)get_option('mg_img_border_opacity') / 100;  
		
		echo 'background: '.mg_rgb2hex(get_option('mg_img_border_color')).';
		';  
	  
		if($alpha_val != 0) {
			echo 'background: '.mg_hex2rgba(get_option('mg_img_border_color'), $alpha_val).';'; 
		}
	}
	?>
    
    <?php
	if(get_option('mg_cells_border')) {
		echo 'border-width: 1px;';  
	} else {
		echo 'border-width: 0px;';	
	}
	echo 'border-color: '. get_option('mg_cells_border_color', '#444') .';';
	?> 
}


/* overlay colors */
.img_wrap .overlays .overlay,
.mg_inl_slider_wrap .lcms_content,
.mg_inl_slider_wrap .lcms_nav span {
	<?php
	echo 'background: '. get_option('mg_main_overlay_color', '#fff') .';';
	?>
}
.mg_inl_slider_wrap .lcms_content {
	<?php
	echo 'background: '. mg_hex2rgba(get_option('mg_main_overlay_color', '#ffffff'), 0.75) .';';
	?>
}
.img_wrap:hover .overlays .overlay,
.mg_touch_on .overlays .overlay {
   <?php
	// alpha
	$alpha_val = (int)get_option('mg_main_overlay_opacity') / 100;  
	
	echo '
	opacity: '.$alpha_val.';
	filter: alpha(opacity='.(int)get_option('mg_main_overlay_opacity').') !important;
	';
	?> 
}
.img_wrap .overlays .cell_more {
	<?php
	echo 'border-bottom-color: '. get_option('mg_second_overlay_color', '#474747') .';'
	?>
}
span.mg_overlay_tit,
.mg_inl_slider_wrap .lcms_content,
.mg_inl_slider_wrap .lcms_nav span:before {
	<?php 
	$col = (get_option('mg_overlay_title_color')) ? get_option('mg_overlay_title_color') : '#222';
	echo 'color: '.$col.';';
	?>
}
span.mg_overlay_tit,
.mg_inl_slider_wrap .lcms_content {    	
	<?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_ol_font_family', ''));
    if(!empty($font_family)) {echo 'font-family: "'.$font_family.'";';}
	?>
    font-size: <?php echo get_option('mg_ol_font_size', 14) ?>px;
}

/* icons color */
.img_wrap .overlays .cell_more span:before {
    color: <?php echo get_option('mg_icons_col') ?>;
}

/* border radius */
.mg_box, .mg_shadow_div, 
.mg_box .img_wrap,
.mg_box .img_wrap > div, .mg_inl_audio_img_wrap,
.mg_box .mg_img_wrap_inner,
.mg_box .img_wrap .overlays {
  border-radius: <?php echo (int)get_option('mg_cells_radius'); ?>px;
}
.mg_box .mg_title_under {
  border-bottom-left-radius: <?php echo (int)get_option('mg_cells_radius'); ?>px;
  border-bottom-right-radius: <?php echo (int)get_option('mg_cells_radius'); ?>px;
}


<?php 
// HIDING OVERLAYS
if(get_option('mg_hide_overlays')) : ?>
.mg_box .overlays {
	display: none !important;
}
<?php endif; ?>


/* TITLE UNDER */
.mg_title_under {
    <?php 
	echo 'color: '.get_option('mg_txt_under_color', '#333333').';';

	$pdg = (int)get_option('mg_cells_img_border'); 
	
	$pdg_t = ($pdg < 6) ? 6 : $pdg;
	$pdg_r = ($pdg < 10) ? 10 : $pdg; 
	$pdg_b = ($pdg < 10) ? 10 : $pdg; 
	$pdg_l = ($pdg < 6) ? 6 : $pdg;  
	 
	$cust_pdg = get_option('mg_tu_custom_padding');
	if(is_array($cust_pdg)) {
		if($cust_pdg[0] != '') {$pdg_t = $cust_pdg[0];}
		if($cust_pdg[1] != '') {$pdg_r = $cust_pdg[1];}
		if($cust_pdg[2] != '') {$pdg_b = $cust_pdg[2];}
		if($cust_pdg[3] != '') {$pdg_l = $cust_pdg[3];}
	}
	
	?>	
    padding-top: 	<?php echo $pdg_t; ?>px !important;
    padding-right: 	<?php echo $pdg_r; ?>px;
    padding-bottom: <?php echo $pdg_b; ?>px;
    padding-left: 	<?php echo $pdg_l; ?>px;
}
.mg_def_txt_under {  	
	<?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_txt_under_font_family', ''));
    if(!empty($font_family)) {echo 'font-family: "'.$font_family.'";';}
	?>
    font-size: <?php echo get_option('mg_txt_under_font_size', 15) ?>px;
}
.mg_tu_attach .mg_title_under {
	 <?php
	if(get_option('mg_cells_border')) {
	  echo '
	  	border-color: '. get_option('mg_cells_border_color', '#444') .';
		border-width: 0px 1px 1px;
    	border-style: solid;
		
		margin-top: -1px;
	  ';   
	}
	?> 
    
    <?php
	if(get_option('mg_img_border_color') && get_option('mg_img_border_opacity')) {
		$alpha_val = (int)get_option('mg_img_border_opacity') / 100;  
		
		echo 'background: '.mg_rgb2hex(get_option('mg_img_border_color')).';
		';  
	  
		if($alpha_val != 0) {
			echo 'background: '.mg_hex2rgba(get_option('mg_img_border_color'), $alpha_val).';'; 
		}
	}
	?>
}
.mg_tu_detach .mg_title_under {
	margin-top: 2px;
}


/* INLINE TEXT ITEMS */
<?php $it_padd = get_option('mg_inl_txt_padding', array('15', '15', '15', '15')); ?>
.mg_inl_txt_td {
	padding: <?php echo $it_padd[0].'px '.$it_padd[1].'px '.$it_padd[2].'px '.$it_padd[3].'px' ?>;
}
<?php if(get_option('mg_clean_inl_txt')) : ?>
.mg_inl_text .mg_shadow_div {box-shadow: none;}
.mg_inl_text .img_wrap {
	border-color: transparent;
    background: none; 
}
<?php endif; ?>


/* FILTERS AND SEARCH */
.mg_top_filters .mg_filter {
	text-align: <?php echo get_option('mg_filters_align', 'left'); ?>;
    padding: 0px <?php echo $cells_margin; ?>px;
}
.mg_mobile_filter {
	padding: 0px <?php echo $cells_margin; ?>px;
}
.mg_filter a.mgf,
.mg_mobile_filter_dd,
.mgf_search_form input, .mgf_search_form i:before {	
	color: <?php echo get_option('mg_filters_txt_color', '#444444'); ?>;
    font-size: <?php echo get_option('mg_filters_font_size', 14) ?>px;
}
.mg_filter a.mgf,
.mg_mobile_filter_dd,
.mgf_search_form input {
	<?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_filters_font_family', ''));
    if(!empty($font_family)) {echo 'font-family: "'.$font_family.'";';}
	?>
}
.mg_filter a.mgf:hover,
.mgf_search_form:hover input, .mgf_search_form:hover i:before {		
	color: <?php echo get_option('mg_filters_txt_color_h', '#666666'); ?> !important;
}
.mg_filter a.mgf.mg_cats_selected,
.mg_filter a.mgf.mg_cats_selected:hover,
.mgf_search_form:focus input, .mgf_search_form:focus i:before {		
	color: <?php echo get_option('mg_filters_txt_color_sel', '#222222'); ?> !important;
}
.mg_new_filters a.mgf,
.mgf_search_form input,
.mg_mobile_filter_dd {	
	background-color: <?php echo get_option('mg_filters_bg_color', '#ffffff'); ?>;
    border: 1px solid <?php echo get_option('mg_filters_border_color', '#999999'); ?>;
    border-radius: <?php echo (int)get_option('mg_filters_radius', 2); ?>px;
}
.mgf_search_form i::before {
	box-shadow: -1px 0 0 0 <?php echo mg_hex2rgba(get_option('mg_filters_border_color', '#999999'), 0.35); ?>;
}
.mg_new_filters a.mgf {	    
    <?php if($filters_align == 'right') : ?>
    margin-right: 0px !important;
    <?php else : ?>
    margin-left: 0px !important;
    <?php endif; ?>
}
.mg_new_filters a.mgf:hover,
.mgf_search_form input:hover, .mgf_search_form:hover i:before {	
	background-color: <?php echo get_option('mg_filters_bg_color_h', '#ffffff'); ?>;
    border: 1px solid <?php echo get_option('mg_filters_border_color_h', '#666666'); ?>;
}
.mgf_search_form:hover i::before {
	box-shadow: -1px 0 0 0 <?php echo mg_hex2rgba(get_option('mg_filters_border_color_h', '#66666'), 0.35); ?>;
}
.mg_new_filters a.mgf.mg_cats_selected,
.mg_new_filters a.mgf.mg_cats_selected:hover,
.mgf_search_form input:focus, .mgf_search_form i:before {	
	background-color: <?php echo get_option('mg_filters_bg_color_sel', '#ffffff'); ?>;
    border: 1px solid <?php echo get_option('mg_filters_border_color_sel', '#555555'); ?>;
}
.mg_left_col_filters .mg_filter,
.mg_right_col_filters .mg_filter {
	min-width: <?php echo $side_filters_w ?>px;	
    padding-top: <?php echo $cells_margin; ?>px;
}
.mg_has_search.mg_left_col_filters .mg_filter,
.mg_has_search.mg_right_col_filters .mg_filter {
	border-top: <?php echo (50 + $cells_margin) ?>px solid transparent;
}
.mg_left_col_filters .mgf_search_form,
.mg_right_col_filters .mgf_search_form {
	top: <?php echo $cells_margin; ?>px;
}
.mg_left_col_filters .mgf_search_form input,
.mg_right_col_filters .mgf_search_form input {
    max-width: <?php echo ($side_filters_w - 20) ?>px;
}

/* search + filter - positioning */
.mg_has_search.mg_no_filters .mgf_search_form,
.mg_has_search.mg_top_filters .mgf_search_form {
	border-width: 0px <?php echo $cells_margin; ?>px;
}
.mg_has_search.mg_top_filters .mgf_search_form {
    <?php if($filters_align == 'left') : ?>
    float: right;
    width: 25%;
    <?php elseif($filters_align == 'right') : ?>
    float: left;
    width: 25%;
    <?php endif;?>
}
.mg_has_search.mg_top_filters .mg_filter {
    <?php if($filters_align == 'left') : ?>
    float: left;
    width: 75%;
    <?php elseif($filters_align == 'right') : ?>
    float: right;
    width: 75%;
    <?php endif;?>
}
<?php if($filters_align == 'center') : ?>
.mg_has_search.mg_top_filters .mgf_search_form {
	width: 100%;
    max-width: 300px;
    left: 50%;
    
    -webkit-transform: 	translateX(-50%); 
	-ms-transform:  	translateX(-50%); 
	transform:			translateX(-50%); 
}
<?php endif; ?>

/* no-results box */
.mg_no_results {
	background-color: <?php echo get_option('mg_filters_bg_color', '#ffffff'); ?>;
    box-shadow: 0 0 0 1px <?php echo get_option('mg_filters_border_color', '#999999'); ?> inset;
    border-radius: <?php echo (int)get_option('mg_filters_radius', 2); ?>px;
    color: <?php echo get_option('mg_filters_txt_color', '#444444'); ?>;
}


/* responsiveness */
@media screen and (max-width:<?php echo $mobile_treshold ?>px) { 
	<?php if(get_option('mg_dd_mobile_filter')) : ?>
    .mg_filter {
    	display: none !important;
    }
    .mg_mobile_filter,
    .mg_mobile_filter_dd {
    	display: block !important;
    }
    <?php endif; ?>
    .mg_left_col_filters, 
    .mg_right_col_filters,
    .mg_left_col_filters .mg_container,
    .mg_right_col_filters .mg_container {
        display: block;
    }
    .mg_has_search.mg_top_filters .mgf_search_form,
    .mg_has_search.mg_top_filters .mg_filter {
    	float: none;
        width: 100%;
    }
    .mg_left_col_filters .mgf_search_form,
	.mg_right_col_filters .mgf_search_form {
    	width: 100%;
        position: relative;
        border-width: 0 <?php echo $cells_margin; ?>px;
        margin-bottom: 12px;
    }
    .mg_left_col_filters .mgf_search_form input,
	.mg_right_col_filters .mgf_search_form input {
    	max-width: none !important;
    } 
    .mgf_search_form input {
    	padding-top: 8px;
        padding-bottom: 8px;
    }
}

/*** pagination button alignment ***/
.mg_pag_wrap {
	text-align: <?php echo get_option('mg_pag_align', 'center') ?>;
    right: <?php echo $cells_margin ?>px;
    left: <?php echo $cells_margin ?>px;
}
@media screen and (min-width:<?php echo ((int)$mobile_treshold + 1) ?>px) { 
	.mg_right_col_filters .mg_pag_wrap {
    	right: <?php echo ($side_filters_w + $cells_margin) ?>px;
	}
    .mg_left_col_filters .mg_pag_wrap {
    	left: <?php echo ($side_filters_w + $cells_margin) ?>px;
	}
}


/*** inline self-hosted video ***/
.mg_sh_inl_video video {
	background-color: <?php echo get_option('mg_img_border_color', '#000'); ?>;
}



/*** LIGHTBOX ***/
#mg_lb_loader {
	border-radius: <?php echo get_option('mg_lb_loader_radius', 18) ?>%;
}
#mg_lb_background {
	<?php  
    // color
    if(get_option('mg_item_overlay_color') && get_option('mg_item_overlay_color') != '') {
        $item_ol_col = get_option('mg_item_overlay_color');
    }
    else {$item_ol_col = '#fff';}  

    // pattern
    if(get_option('mg_item_overlay_pattern') && get_option('mg_item_overlay_pattern') != 'none') {
        $pat = 'url('. $safe_baseurl .'/img/patterns/'.get_option('mg_item_overlay_pattern').') repeat top left';
    }
    else {$pat = '';}
    
    echo 'background: '.$pat.' '.$item_ol_col.';';  
    ?>  
}
#mg_lb_background.mg_lb_shown,
#mg_lb_background.google_crawler {
	<?php 
	// alpha
    $alpha_val = (int)get_option('mg_item_overlay_opacity', 70) / 100;  
	echo '
	opacity: '.$alpha_val.';
    filter: alpha(opacity='.(int)get_option('mg_item_overlay_opacity').');';
	?>
}
#mg_lb_contents {
	<?php 
    $w = (get_option('mg_item_width')) ? get_option('mg_item_width') : 70;
    echo 'width: '.$w.'%;';
    
    $w = (get_option('mg_item_maxwidth')) ? get_option('mg_item_maxwidth') : 960;
    echo 'max-width: '.$w.'px;';
	
	$border_w = get_option('mg_lb_border_w', 0);
	if(!empty($border_w)) {
		echo 'border: '.$border_w.'px solid	'.get_option('mg_item_border_color', '#e5e5e5').';';
	}
	
	echo 'border-radius: '.(int)get_option('mg_item_radius').'px;';
	
	$padding = get_option('mg_lb_padding', 20);
	$lb_cmd_pos = get_option('mg_lb_cmd_pos', 'inside');
	
	$top_padd = ($lb_cmd_pos == 'inside' || $lb_cmd_pos == 'ins_hidden') ? 52 : $padding;
	echo 'padding: '.$top_padd.'px '.$padding.'px '.$padding.'px;';
    ?>
}
h1.mg_item_title {
	font-size: <?php echo get_option('mg_lb_title_font_size', 20) ?>px;
    line-height: <?php echo get_option('mg_lb_title_line_height', 29) ?>px;
    <?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_lb_title_font_family', ''));
    if(!empty($font_family)) {echo 'font-family: "'.$font_family.'";';}
	?>
}
.mg_item_text {
    font-size: <?php echo get_option('mg_lb_txt_font_size', 16) ?>px;
    line-height: <?php echo get_option('mg_lb_txt_line_height', 24) ?>px;
}
.mg_item_text,
ul.mg_cust_options {
	<?php
	$font_family = str_replace(array('"', "'"), '', get_option('mg_lb_txt_font_family', ''));
    if(!empty($font_family)) {echo 'font-family: "'.$font_family.'";';}
	?>
}


/* inner commands */
#mg_lb_ins_cmd_wrap {
	<?php if($lb_cmd_pos != 'round_hidden') : ?>
    left: <?php echo ($padding < 12) ? 12 : $padding ?>px;
    right: <?php echo ($padding < 12) ? 12 : $padding ?>px;
    <?php endif; ?>
    
    <?php
	// height ok until 27px padding - then change
	if($padding > 27) {
		$new_inner_cmd_height = 52 + ceil( ($padding - 27) * 0.75);
		echo 'height: '. $new_inner_cmd_height .'px;';	
	}
	else {
		$new_inner_cmd_height = 52;	
	}
	?>
}
<?php if($lb_cmd_pos != 'inside') : ?>
@media screen and (max-width: 860px) {
<?php endif; ?> 
	#mg_lb_contents {
		padding-top: <?php echo $new_inner_cmd_height ?>px;
	}
<?php if($lb_cmd_pos != 'inside') : ?>
}
<?php endif; ?> 


/* inner lb cmd boxed */
<?php if(get_option('mg_lb_inner_cmd_boxed')) : ?>
#mg_lb_inside_nav > * > i,
.mg_lb_nav_inside div span,
#mg_inside_close {
	background: <?php echo get_option('mg_item_cmd_bg', '#f1f1f1') ?>;
	border-radius: 2px;
}
#mg_inside_close:before {
	font-size: 18px;
    top: 5px;
    left: 5px;
    display: block;
}
#mg_lb_inside_nav > * > i:before {
    font-size: 15px;
    top: -1px;
    vertical-align: middle;
}
.mg_inside_nav_next > i:before {
	margin-right: -3px;
}
.mg_lb_nav_inside div span {
	padding: 5px 9px 4px; 
}
<?php endif; ?>


/* lb rounded closing btn */
<?php if($lb_cmd_pos == 'round_hidden') : ?>
#mg_lb_ins_cmd_wrap {
	max-height: 0;
}
#mg_inside_close {
	border-radius: 50%;
    padding: 8px;
    top: -12px;
    right: -25px;
    background: <?php echo get_option('mg_item_bg_color', '#fff') ?>;
    
    <?php 
	$border_w = get_option('mg_lb_border_w', 0);
	?>
    box-shadow: -1px 1px 1px <?php echo $border_w ?>px rgba(0, 0, 0, 0.05);
}
.mg_close_lb::after {
    border-radius: 50%;
    box-shadow: 0 0 0 <?php echo $border_w ?>px #e2e2e2;
    content: "";
    display: block;
    height: 100%;
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
}
<?php 
endif;


// lb contents padding - at least 22px
if($padding != 22) :
  $diff = 22 - $padding;
  $new_padd = ($diff < 0) ? 0 : $diff;
?>
.mg_layout_full .mg_item_content {
	padding: 14px <?php echo $new_padd ?>px <?php echo $new_padd ?>px;	
}
.mg_lb_layout:not(.mg_layout_full) .mg_item_content {
    padding: <?php echo $new_padd ?>px;
}
@media screen and (max-width: 860px) { 
    .mg_lb_layout:not(.mg_layout_full) .mg_item_content {
		padding: 14px <?php echo $new_padd ?>px <?php echo $new_padd ?>px !important;	
	}		
}
<?php endif; ?>

/* side text - desktop mode - inside cmd - top padding */
<?php if($lb_cmd_pos == 'inside' || $lb_cmd_pos == 'ins_hidden') : ?>
@media screen and (min-width: 860px) { 
    .mg_lb_layout:not(.mg_layout_full) .mg_item_content {
        padding-top: <?php echo ($lb_cmd_pos == 'inside') ? 3 : 0; ?>px !important;	
    }
}
<?php endif; ?>


/* colors - shadow */
#mg_lb_contents,
#mg_lb_loader {
    <?php 
	echo 'color: '.get_option('mg_item_txt_color', '#333').';';
	echo 'background-color: '.get_option('mg_item_bg_color', '#fff').';';

	$lb_shadow = get_option('mg_lb_shadow', 'soft');
    if($lb_shadow == 'soft') {
		echo 'box-shadow: 0 2px 5px rgba(10, 10, 10, 0.4);';  
    } 
    elseif($lb_shadow == 'heavy') {
		echo 'box-shadow: 0 6px 8px rgba(10, 10, 10, 0.6);';     
    }
	?>
}
#mg_lb_loader {
	<?php if($lb_shadow != 'none') : ?>
	box-shadow: 0px 2px 5px rgba(10, 10, 10, 0.5);	
    <?php endif; ?>
}
.mg_cust_options,
#mg_lb_comments_wrap {
	border-color: <?php echo get_option('mg_item_hr_color', '#ccc') ?>;
}


/* icons and loader */
.mg_close_lb:before, .mg_nav_prev > i:before, .mg_nav_next > i:before,
.mg_galleria_slider_wrap .galleria-thumb-nav-left:before, .mg_galleria_slider_wrap .galleria-thumb-nav-right:before,
#mg_socials span:before,
#mg_woo_item_added i:before {
	color: <?php echo get_option('mg_item_icons_color', '#333333') ?>;
}
 

/* navigation elements background color and border radius */
.mg_lb_nav_side *,
.mg_lb_nav_side_basic,
.mg_lb_nav_top > i, .mg_lb_nav_top > div, .mg_lb_nav_top > div *,
#mg_top_close {
	background-color: <?php echo get_option('mg_item_bg_color', '#fff') ?>; 
}

<?php
/* lightbox navigation - commands position */
$cmd_pos = get_option('mg_lb_cmd_pos', 'inside'); 

// commands NOT inside
if($cmd_pos != 'inside') : 
	$radius = (int)get_option('mg_item_radius'); if($radius > 15) {$radius = 15;}
	$border_w = get_option('mg_lb_border_w', 0); if($border_w > 5) {$border_w = 5;}
	$border_col = get_option('mg_item_border_color', '#e5e5e5');
	$txt_col = get_option('mg_item_txt_color', '#333');
?>
/* top closing button */
#mg_top_close {
	border-style: solid;
    border-color: <?php echo $border_col ?>;
	border-width: 0 0 <?php echo $border_w ?>px <?php echo $border_w ?>px;
    border-radius: 0 0 0 <?php echo $radius ?>px;
}
<?php endif; 

// top position
if($cmd_pos == 'top') : ?>
/* top nav - custom radius and borders */
#mg_lb_top_nav > * > div {
	margin-left: <?php echo $border_w ?>px;
}
#mg_lb_top_nav .mg_nav_prev i {
	border-width: 0 0 <?php echo $border_w ?>px 0;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
#mg_lb_top_nav .mg_nav_next i,
#mg_lb_top_nav > * > div img {
	border-width: 0 <?php echo $border_w ?>px <?php echo $border_w ?>px 0;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
    border-radius: 0 0 <?php echo $radius ?>px 0;
}
#mg_lb_top_nav > * > div {
	border-width: 0 <?php echo $border_w ?>px <?php echo $border_w ?>px <?php echo $border_w ?>px;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
    color: <?php echo $txt_col ?>;
}
	<?php if($lb_shadow != 'none') : ?>
    #mg_lb_top_nav > div:first-child {
        box-shadow: 0px 2px 3px rgba(10, 10, 10, 0.3);	
    }
    #mg_lb_top_nav > div:last-child {
        box-shadow: 3px 2px 3px rgba(10, 10, 10, 0.2);	
    }
    #mg_lb_top_nav > div:hover > div, #mg_top_close {
        box-shadow: 0px 2px 3px rgba(10, 10, 10, 0.3);	
    }
    #mg_lb_top_nav > div:hover img {
        box-shadow: 2px 2px 2px rgba(10, 10, 10, 0.2);	
    }
	<?php endif; ?>


<?php elseif($cmd_pos == 'side') : ?>
/* top nav - custom radius and borders */
.mg_side_nav_prev span {
	border-radius: 0 <?php echo $radius ?>px <?php echo $radius ?>px 0;
	border-width: <?php echo $border_w ?>px <?php echo $border_w ?>px <?php echo $border_w ?>px 0;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
.mg_side_nav_next span {
	border-radius: <?php echo $radius ?>px 0 0 <?php echo $radius ?>px;
	border-width: <?php echo $border_w ?>px 0 <?php echo $border_w ?>px <?php echo $border_w ?>px;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
.mg_side_nav_prev > img {
	border-radius: 0 <?php echo $radius ?>px 0 0;
	border-width: <?php echo $border_w ?>px <?php echo $border_w ?>px 0 0;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
.mg_side_nav_next > img {
	border-radius: <?php echo $radius ?>px 0 0 0;
	border-width: <?php echo $border_w ?>px 0 0 <?php echo $border_w ?>px;
    border-style: solid;
    border-color: <?php echo $border_col ?>; 
}
.mg_side_nav_next div {
	border-radius: 0 0 0 <?php echo $radius ?>px;
    color: <?php echo $txt_col ?>;
}
.mg_side_nav_prev div {
	border-radius: 0 0 <?php echo $radius ?>px 0;
    color: <?php echo $txt_col ?>;
}
	<?php if(!empty($border_w)): ?>
    .mg_side_nav {
        height: <?php echo 68 + ($border_w * 2); ?>px;
    }
	.mg_side_nav > span {
    	width: <?php echo 42 + $border_w; ?>px;
    }
    .mg_side_nav > i {
    	margin-top: <?php echo $border_w; ?>px;
    }
    .mg_side_nav > div {
        width: <?php echo 338 - $border_w; ?>px;
    }
    <?php endif; ?>
    
    <?php if($lb_shadow != 'none') : ?>
    .mg_side_nav span, #mg_top_close {
        box-shadow: 0px 2px 3px rgba(10, 10, 10, 0.3);	
    }
    .mg_side_nav img {
        box-shadow: 0px -1px 1px rgba(10, 10, 10, 0.2);	
    }
    
    @media screen and (min-width:760px) {
    	#mg_lb_wrap {
        	padding-left: 55px;
            padding-right: 55px;
        }
    }
    <?php endif; ?>
    
    
<?php elseif($cmd_pos == 'side_basic') : ?>
/* top nav - custom radius and borders */
.mg_lb_nav_side_basic {
	border-radius: <?php echo $radius ?>px;
	border: <?php echo $border_w ?>px solid <?php echo $border_col ?>;
}
#mg_top_close {
	border-width: <?php echo $border_w ?>px;
    margin-top: 15px;
    margin-right: 15px;
    box-sizing: border-box;
}
#mg_top_close:before {
	top: 10px;
}
	<?php if(!empty($border_w)): ?>
    .mg_lb_nav_side_basic {
        height: <?php echo 68 + ($border_w * 2); ?>px;
    	width: <?php echo 44 + $border_w; ?>px;
    }
    <?php endif; ?>
    
    <?php if($lb_shadow != 'none') : ?>
    .mg_lb_nav_side_basic, #mg_top_close {
        box-shadow: 0px 2px 3px rgba(10, 10, 10, 0.3);	
    }
    
    @media screen and (min-width:760px) {
    	#mg_lb_wrap {
        	padding-left: 55px;
            padding-right: 55px;
        }
    }
    <?php endif; ?>
<?php endif; ?>


<?php 
$lb_bg_fx = get_option('mg_lb_bg_fx', 'genie_b_side');
if(!empty($lb_bg_fx)) :
?>
/* lightbox background effect */
#mg_lb_background.mg_lb_shown {
    animation: mg_lb_bg_showup <?php echo ((int)get_option('mg_lb_bg_fx_time', 500) / 1000) ?>s forwards <?php echo mg_easing_to_css(get_option('mg_lb_bg_fx_easing', 'ease')) ?>; 
    -webkit-animation: mg_lb_bg_showup <?php echo ((int)get_option('mg_lb_bg_fx_time', 500) / 1000) ?>s forwards <?php echo mg_easing_to_css_ow(get_option('mg_lb_bg_fx_easing', 'ease')) ?>;	
}

<?php
switch($lb_bg_fx) {
	case 'zoom-in' :
		$start 	= 'scale(0.6)';
		$end 	= 'scale(1)';
		break;	
		
	case 'zoom-out' :
		$start 	= 'scale(1.4)';
		$end 	= 'scale(1)';
		break;	
	
	case 'zoom-flip' :
		$start 	= 'rotateX(-135deg) scale(0.1); transform-style: preserve-3d; transform-origin: 50% 50%';
		$end 	= 'rotateX(180deg) scale(1); transform-style: preserve-3d; transform-origin: 50% 50%';
		break;	
	
	case 'skew' :
		$start 	= 'skewX(55deg)';
		$end 	= 'skewX(0deg)';
		break;	
		
	case 'symm_vert' :
		$start 	= 'rotateY(90deg)';
		$end 	= 'rotateY(0deg)';
		break;	
		
	case 'symm_horiz' :
		$start 	= 'rotateX(90deg)';
		$end 	= 'rotateX(0deg)';
		break;		
	
	case 'genie_t_side' :
		$start 	= 'translateY(-60%) scale(0)';
		$end 	= 'translateY(0) scale(1)';
		break;	
	
	case 'genie_r_side' :
		$start 	= 'translateX(60%) scale(0)';
		$end 	= 'translateX(0) scale(1)';
		break;	
	
	case 'genie_b_side' :
	default:
		$start 	= 'translateY(60%) scale(0)';
		$end 	= 'translateY(0) scale(1)';
		break;	
		
	case 'genie_l_side' :
		$start 	= 'translateX(-60%) scale(0)';
		$end 	= 'translateX(0) scale(1)';
		break;	
			
	case 'slide_corn_tr' :
		$start 	= 'translate(75%, -75%)';
		$end 	= 'translate(0, 0)';
		break;
		
	case 'slide_corn_br' :
		$start 	= 'translate(75%, 75%)';
		$end 	= 'translate(0, 0)';
		break;
		
	case 'slide_corn_bl' :
		$start 	= 'translate(-75%, 75%)';
		$end 	= 'translate(0, 0)';
		break;			
	
	case 'slide_corn_tl' :
		$start 	= 'translate(-75%, -75%)';
		$end 	= 'translate(0, 0)';
		break;					
		
	
	case 'slide_t_side' :
		$start 	= 'translate(0, -85%)';
		$end 	= 'translate(0, 0)';
		break;		
		
	case 'slide_r_side' :
		$start 	= 'translate(85%, 0)';
		$end 	= 'translate(0, 0)';
		break;		
		
	case 'slide_b_side' :
		$start 	= 'translate(0, 85%)';
		$end 	= 'translate(0, 0)';
		break;		
		
	case 'slide_l_side' :
		$start 	= 'translate(-85%, 0)';
		$end 	= 'translate(0, 0)';
		break;											
}
?>

@-webkit-keyframes mg_lb_bg_showup {
    0% 		{-webkit-transform: <?php echo $start ?>;}
    100% 	{-webkit-transform: <?php echo $end ?>;}
}
@keyframes mg_lb_bg_showup {
    0%		{transform: <?php echo $start ?>;}
    100% 	{transform: <?php echo $end ?>;}
}
<?php endif; ?>

/* lightbox entrance effect */
<?php 
switch(get_option('mg_lb_entrance_fx', 'static_fade')) : 
	case 'static_fade' :
?>
    #mg_lb_contents.mg_lb_pre_show_prev,
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_switching_prev,
    #mg_lb_contents.mg_lb_switching_next,
    #mg_lb_contents.mg_closing_lb {
        -webkit-transform: 	scale(0.95) translate3d(0,8px,0);
        transform: 			scale(0.95) translate3d(0,8px,0);
        
        -webkit-transition: opacity .25s ease-in, transform .5s ease; 
        transition: 		opacity .25s ease-in, transform .5s ease; 
    }
    #mg_lb_contents.mg_lb_shown {
        -webkit-transition: opacity .25s ease-in, transform .5s ease; 
        transition: 		opacity .25s ease-in, transform .5s ease; 
    }
<?php 
	break;
	case 'slide_bounce' :
?>
	#mg_lb_contents {
        opacity: 1;	
        
        -webkit-transition: all .5s cubic-bezier(0.680, -0.150, 0.265, 1.350); 
        transition: 		all .5s cubic-bezier(0.260, 0, 0.300, 1.200); 
    }
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_pre_show_prev {
        -webkit-transition-duration: 	0s !important;
        transition-duration: 			0s !important; 	
    }
    #mg_lb_contents.mg_lb_pre_show_prev,
    #mg_lb_contents.mg_lb_switching_next,
    #mg_lb_contents.mg_closing_lb {
        -webkit-transform: 	translateX(-80vw);
        transform: 			translateX(-80vw);
    }
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_switching_prev {
        -webkit-transform: 	translateX(80vw);
        transform: 			translateX(80vw);
    }
    #mg_lb_contents.mg_lb_shown {
        -webkit-transform: 	translateX(0);
        transform: 			translateX(0);
    }
<?php 
	break;
	case 'slide_fade' :
?>	
	#mg_lb_contents {
        -webkit-transition: all .45s ease-out; 
        transition: 		all .45s ease-out; 
    }
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_pre_show_prev {
        -webkit-transition-duration: 	0s !important;
        transition-duration: 			0s !important; 	
    }
    #mg_lb_contents.mg_lb_pre_show_prev,
    #mg_lb_contents.mg_lb_switching_next,
    #mg_lb_contents.mg_closing_lb {
        -webkit-transform: 	translateX(-20%) rotateY(10deg) scale(0.9);
        transform: 			translateX(-20%) rotateY(10deg) scale(0.9);
    }
    #mg_lb_contents.mg_lb_pre_show_next,
    #mg_lb_contents.mg_lb_switching_prev {
        -webkit-transform: 	translateX(20%) rotateY(10deg) scale(0.9);
        transform: 			translateX(20%) rotateY(10deg) scale(0.9);
    }
    #mg_lb_contents.mg_lb_shown {
        -webkit-transform: 	translateX(0) rotateY(0deg) scale(1);
        transform: 			translateX(0) rotateY(0deg) scale(1);
    }
<?php
	break;    
endswitch; 
?>

/* spacer visibility */
@media screen and (min-width: <?php echo ($mobile_treshold + 1) ?>px) { 
    .mg_spacer_hidden_desktop {
    	max-width: 0;
        max-height: 0;
        padding: 0;
    }
}
@media screen and (max-width: <?php echo $mobile_treshold ?>px) { 
    .mg_spacer_hidden_mobile {
    	max-width: 0;
        max-height: 0;
        padding: 0;
    }
}


/* responsive typography */
@media screen and (max-width: <?php echo $mobile_treshold ?>px) { 
	span.mg_overlay_tit,
	.mg_inl_slider_wrap .lcms_content {    	
    	font-size: <?php echo get_option('mg_mobile_ol_font_size', 12) ?>px;
    }
	.mg_def_txt_under {  	
    	font-size: <?php echo get_option('mg_mobile_txt_under_font_size', 13) ?>px;
    }
    .mg_filter a.mgf,
    .mg_mobile_filter_dd,
    .mgf_search_form input, .mgf_search_form i:before {
    	font-size: <?php echo get_option('mg_mobile_filters_font_size', 12) ?>px;
    }
}
@media screen and (max-width: 760px) { 
    h1.mg_item_title {
        font-size: <?php echo get_option('mg_mobile_lb_title_font_size', 17) ?>px;
        line-height: <?php echo get_option('mg_mobile_lb_title_line_height', 25) ?>px;
    }
    .mg_item_text {
        font-size: <?php echo get_option('mg_mobile_lb_txt_font_size', 14) ?>px;
        line-height: <?php echo get_option('mg_mobile_lb_txt_line_height', 22) ?>px;
    }
} 


/* lb image zoom */
.mg_item_featured .easyzoom-notice,
.mg_item_featured .easyzoom-flyout {
	background: <?php echo get_option('mg_item_bg_color', '#fff') ?>;
    color: <?php echo get_option('mg_item_txt_color', '#333') ?>; 
}
  
<?php 
// custom CSS
echo get_option('mg_custom_css');
