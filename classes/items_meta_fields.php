<?php
// ITEMS META FIELDS FRAMEWORK - DECLARATION AND CODES

class mg_meta_fields {
	private $item_id; // static resource to know which item ID
	private $item_type; // static resource to know which item type
	private $item_keys; // meta keys associated to item - useful to set default values
	
	public $fields = array(); // fields meta structure
	public $groups = array(); // field groups
	
	public $index_to_save = array(); // which field indexes to validate and save - ADD CUSTOM INDEXES IN CUSTOM FIELDS
	
	
	
	/* INIT - declare item type */
	function __construct($item_id, $item_type) {
		$this->item_id = $item_id;
		$this->item_type = $item_type;
		
		$this->setup_fields();
		$this->setup_groups();
	}
	
	
	// setup fields
	private function setup_fields() {
		require_once(MG_DIR .'/functions.php');
		
		// switch for lightbox image's effect
		$lb_img_fx = array('' => __("No effect", 'mg_ml'), 'kenburns' => __('Ken Burns animation', 'mg_ml'));
		if($this->item_type != 'audio') {$lb_img_fx['zoom'] = __("Zoom on hover", 'mg_ml');}
		
		
		$fields = array(
			'mg_kenburns_fx' => array(
				'label' => __('Ken Burns effect?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> ($this->item_type == 'inl_slider') ? __("If checked applies Ken Burns effect to images", 'mg_ml') : __("If checked applies Ken Burns effect to grid's image (will discard Overlay Manager effects)", 'mg_ml'),
				'group' => ($this->item_type == 'inl_slider') ? 'slider_opts' : 'grid_item_opts', 
			), 
			'mg_static_show_overlay' => array(
				'label' => __('Display overlay?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked displays overlay for this item', 'mg_ml'),
				'group' => 'grid_item_opts', 
			), 
			
			'mg_slider_w_val' => array(
				'label' 	=> __("Slider's height", 'mg_ml'),
				'type'		=> 'val_n_type',
				'def'		=> 53,
				'max_val_len' => 4,
				'note'		=> __('% is related to its width', 'mg_ml'),
				'group' 	=> ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			), 
			'mg_slider_crop' => array(
				'label' => __("Image's display mode", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> mg_galleria_crop_methods(),
				'note'	=> __("Choose how images will be managed by slider", 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts',  
			), 
			'mg_slider_autoplay' => array(
				'label' => __('Autoplay slideshow?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked autoplays slider slideshow', 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			), 
			'mg_slider_thumbs' => array(
				'label' => __('Show thumbnails?', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> mg_galleria_thumb_opts(),
				'note'	=> __("Choose whether and how to show slider thumbnails", 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			), 
			'mg_slider_captions' => array(
				'label' => __('Display captions?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked displays slider image captions', 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			),
			'mg_slider_random' => array(
				'label' => __('Random images?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked, randomizes slider images', 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_slider' : 'slider_opts', 
			),
			
			'mg_video_use_poster' => array(
				'label' => __('Use featured image as video poster?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked, sets featured image as video poster', 'mg_ml'),
				'group' => 'video_opts', 
			),
			'mg_autoplay_inl_video' => array(
				'label' => __('Autoplay video?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> '',
				'group' => 'video_opts', 
			),
			
			'mg_soundcloud_url' => array(
				'label' => __("Soundcloud track's URL", 'mg_ml'),
				'type'	=> 'text',
				'note'	=> __('Filling this field, selected tracklist <strong>will be ignored</strong>', 'mg_ml'),
				'group' => 'audio_opts', 
			),
			
			'mg_link_url' => array(
				'label' => __("Link URL", 'mg_ml'),
				'type'	=> 'text',
				'note'	=> '',
				'group' => 'link_opts', 
			),
			'mg_link_target' => array(
				'label' => __('Link target', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'top' 	=> __('In the same page', 'mg_ml'), 
					'blank' => __('In a new page', 'mg_ml')
				),
				'note'	=> __('Choose how link will be opened', 'mg_ml'),
				'group' => 'link_opts', 
			),
			'mg_link_nofollow' => array(
				'label' => __('Use nofollow?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If enabled, uses rel="nofollow" on link', 'mg_ml'),
				'group' => 'link_opts', 
			), 
			'mg_cpt_source' => array(
				'label' => __('Post type and taxonomy', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> mg_get_cpt_with_tax(),
				'note'	=> __('Choose the post type and taxonomy to fetch the post from', 'mg_ml'),
				'group' => 'pc_opts', 
			),
			'mg_cpt_tax_term' => array(
				'label' => __("Taxonomy's term", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> mg_get_taxonomy_terms( mg_get_cpt_with_tax(true) ),
				'note'	=> __("Choose the taxonomy's term to fetch the post from", 'mg_ml'),
				'group' => 'pc_opts', 
			),
			'mg_post_query_offset' => array(
				'type'		=> 'slider',
				'label' 	=> __('Query offset', 'mg_ml'),
				'min_val' 	=> '0',
				'max_val' 	=> '40',
				'step' 		=> '1',
				'value' 	=> '',
				'def' 		=> 0,
				'note'		=> __('Sets how many posts to skip during the query', 'mg_ml'),
				'group' => 'pc_opts',
			), 
			'mg_use_item_feat_img' => array(
				'label' => __("Use item's featured image?", 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __("If checked, uses item's featured image in grids instead of post's one", 'mg_ml'),
				'group' => 'pc_opts', 
			), 
			'mg_hide_feat_img' => array(
				'label' => __('Hide featured image in lightbox?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __("If checked, hides posts featured image in lightbox", 'mg_ml'),
				'group' => 'pc_opts', 
			), 
			'mg_link_to_post' => array(
				'label' => __('Direct link to post?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked, turns item into a direct link', 'mg_ml'),
				'group' => 'pc_opts', 
			), 
			
			'mg_inl_txt_box_bg' => array(
				'label' => __('Box background color', 'mg_ml'),
				'type'	=> 'color',
				'note'	=> __('Leave blank to use the default one (transparent allowed)', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 
			'mg_inl_txt_bg_alpha' => array(
				'type'		=> 'slider',
				'label' 	=> __("Background color's opacity", 'mg_ml'),
				'min_val' 	=> '0',
				'max_val' 	=> '100',
				'step' 		=> '5',
				'value' 	=> '%',
				'def' 		=> 100,
				'note'		=> '',
				'group' 	=> 'inl_txt_opts',
			), 
			'mg_inl_txt_img_as_bg' => array(
				'label' => __('Use featured image as background?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked uses featured image as box background', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 
			'mg_inl_txt_color' => array(
				'label' => __('Text main color', 'mg_ml'),
				'type'	=> 'color',
				'note'	=> __('Leave blank to use the default one', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 
			'mg_inl_txt_vert_align' => array(
				'label' => __('Vertical alignment', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'middle' => __('middle', 'mg_ml'), 
					'top' => __('top', 'mg_ml'), 
					'bottom' => __('bottom', 'mg_ml')
				),
				'note'	=> __('Text vertical alignment in the box', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			),
			'mg_inl_txt_vert_align' => array(
				'label' => __('Vertical alignment', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'middle' 	=> __('middle', 'mg_ml'), 
					'top' 		=> __('top', 'mg_ml'), 
					'bottom'	=> __('bottom', 'mg_ml')
				),
				'note'	=> __("Text's vertical alignment in the box", 'mg_ml'),
				'group' => 'inl_txt_opts', 
			),
			'mg_inl_txt_no_resize' => array(
				'label' => __('Disable dynamic text resizing?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('Check only having complex elements to manage', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 
			'mg_inl_txt_custom_css' => array(
				'label' => __('Custom CSS (optional)', 'mg_ml'),
				'type'	=> 'textarea',
				'placeh'=> 'example - background-image: url(the.image.url.jpg);',
				'note'	=> __('custom CSS applied to the item. <strong>DO NOT use selectors</strong>', 'mg_ml'),
				'group' => 'inl_txt_opts', 
			), 

			'mg_spacer_vis' => array(
				'label' => __('Spacer visibility', 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'' => __('always visible', 'mg_ml'), 
					'hidden_desktop' => __('hidden by default', 'mg_ml'), 
					'hidden_mobile' => __('hidden on mobile', 'mg_ml')
				),
				'note'	=> __('Set spacer visibility in grid modes', 'mg_ml'),
				'group' => 'spacer_opts', 
			),
			
			
			#####################################
			
			
			### woocommerce fields
			'mg_wc_prod_cats' => array(
				'label' 	=> __('Product categories', 'mg_ml'),
				'type'		=> 'select',
				'multiple'	=> true,
				'val' 		=> mg_item_cats(),
				'note'		=> '',
				'group' 	=> 'woocomm', 
			),
			'mg_link_only' => array(
				'label' => __('Direct link to product?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked, turns item into a direct link', 'mg_ml'),
				'group' => 'woocomm', 
			),
			'mg_slider_add_featured' => array(
				'label' => __('Prepend featured image?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __('If checked, prepends featured image in slider', 'mg_ml'),
				'group' => 'wc_slider', 
			),
			
			
			#####################################
			
			
			### lightbox fields
			'mg_layout' => array(
				'label' => __("Lightbox Layout", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> mg_lb_layouts(),
				'note'	=> '',
				'group' => 'lightbox', 
			), 
			'mg_lb_max_w' => array( 
				'label' 	=> __("Lightbox max-width", 'mg_ml'),
				'type'		=> 'slider',
				'min_val' 	=> '280',
				'max_val' 	=> '2000',
				'step' 		=> '50',
				'value' 	=> 'px',
				'def' 		=> '',
				'note'		=> __('Leave blank to use global lightbox sizing', 'mg_ml'),
				'optional'	=> true,
				'group' 	=> 'lightbox',
			), 
			'mg_lb_img_display_mode' => array(
				'label' => __("Image's display mode", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> array(
					'feat_w'	=> __("Fill wrapper's width", 'mg_ml'),
					'img_w'		=> __("Avoid enlargements", 'mg_ml'),
				),
				'note'	=> __('Set how image will be managed in lightbox', 'mg_ml'),
				'group' => 'lightbox', 
			),
			'mg_img_maxheight' => array(
				'type'		=> 'slider',
				'label' 	=> __("Image's max-height", 'mg_ml'),
				'min_val' 	=> '100',
				'max_val' 	=> '1400',
				'step' 		=> '50',
				'value' 	=> 'px',
				'def' 		=> '',
				'note'		=> __('Leave blank to not resize lightbox image', 'mg_ml'),
				'optional'	=> true,
				'group' 	=> ($this->item_type == 'woocomm') ? 'wc_img' : 'lightbox',
			),
			'mg_lb_feat_match_txt' => array(
				'label' => __('Match contents height?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> __("If subject's height is smaller than texts, match it (only side-text layout)", 'mg_ml'),
				'group' => 'lightbox', 
			), 
			'mg_lb_img_fx' => array(
				'label' => __("Image's effect", 'mg_ml'),
				'type'	=> 'select',
				'val' 	=> $lb_img_fx,
				'note'	=> __("Choose which effects to apply to lightbox image", 'mg_ml'),
				'group' => ($this->item_type == 'woocomm') ? 'wc_img' : 'lightbox', 
			), 
			'mg_lb_contents_padding' => array(
				'label' 	=> __('Contents padding', 'mg_ml'),
				'type'		=> '4_numbers',
				'min_val' 	=> '0',
				'max_val' 	=> '50',
				'value' 	=> 'px',
				'def' 		=> array(0, 0, 0, 0),
				'note'		=> __('Set contents custom padding (top - right - bottom - left)', 'mg_ml'),
				'group' 	=> 'lightbox', 
			), 
			'mg_lb_no_comments' => array(
				'label' => __('Disable comments?', 'mg_ml'),
				'type'	=> 'checkbox',
				'note'	=> '',
				'group' => 'lightbox', 
			), 
			
			
			#####################################
			
			
			### custom structure fields
			// custom attributes
			'mg_cust_attr' => array(
				'type'			=> 'custom',
				'cust_callback' => 'cust_attr_f_code',
				'group' 		=> 'cust_attr',
			), 
			
			// custom icon
			'mg_cust_icon' => array(
				'type'			=> 'custom',
				'cust_callback' => 'cust_icon_f_code',
				'group' 		=> 'grid_item_opts',
			), 
			
			// image picker
			'mg_slider_img' => array(
				'type'			=> 'custom',
				'cust_callback' => 'slider_img_f_code',
				'group' 		=> 'slider_img',
			),
		
			// audio picker
			'mg_audio_tracks' => array(
				'type'			=> 'custom',
				'cust_callback' => 'audio_tracks_f_code',
				'group' 		=> 'tracklist',
			),
		
			// video picker
			'mg_video_url' => array(
				'type'			=> 'custom',
				'cust_callback' => 'video_url_f_code',
				'group' 		=> 'video_opts',
			),
		);	
		
		/* MG-FILTER - manage item meta fields */
		$this->fields = apply_filters('mg_item_meta_fields', $fields);
	}
	
	
	// setup groups
	private function setup_groups() {
		 $groups = array(
			'grid_item_opts'	=> __("Grid Item Options", 'mg_ml'),
			'slider_opts'		=> __('Slider Options', 'mg_ml'),
			'slider_img'		=> __('Slider Images', 'mg_ml'),
			'video_opts' 		=> __('Video Options', 'mg_ml'),
			'audio_opts' 		=> __('Audio Options', 'mg_ml'),
			'tracklist' 		=> __('Tracklist', 'mg_ml'),
			'link_opts' 		=> __('Link Options', 'mg_ml'),
			'pc_opts' 			=> __('Post Content Options', 'mg_ml'),
			'inl_txt_opts' 		=> __('Inline Text Options', 'mg_ml'),
			'spacer_opts' 		=> __('Spacer Options', 'mg_ml'),
			'lightbox' 			=> __('Lightbox Options', 'mg_ml'),
			'cust_attr' 		=> __('Custom Attributes', 'mg_ml'),

			'woocomm' 			=> __('Product Options', 'mg_ml'),
			'wc_img'			=> __('Without gallery images', 'mg_ml'),
			'wc_slider'			=> __('With gallery images', 'mg_ml'),
		);
		
		/* MG-FILTER - manage item meta groups */
		$this->groups = apply_filters('mg_item_meta_groups', $groups);	
	}
	

	////////////////////////////////////////////////////////////////////////////////////////
	
	
	/* get type fields 
	 * @return return (array) an associative array of fields split in groups
	 */
	public function type_fields() {
		switch($this->item_type) {
			
			// static image
			case 'simple_img' : 
				$f = array('mg_static_show_overlay', 'mg_kenburns_fx', 'mg_cust_icon'); break;
				
			// single image
			case 'single_img' : 
				$f = array('mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_img_display_mode', 'mg_img_maxheight', 'mg_lb_feat_match_txt', 'mg_lb_img_fx', 'mg_cust_attr'); break;
		
			// lightbox slider
			case 'img_gallery' : 
				$f = array('mg_slider_img', 'mg_slider_w_val', 'mg_slider_crop', 'mg_slider_autoplay', 'mg_slider_thumbs','mg_slider_captions', 'mg_slider_random', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_feat_match_txt', 'mg_cust_attr'); break;	
			
			// inline slider
			case 'inl_slider' : 
				$f = array('mg_slider_img', 'mg_slider_autoplay', 'mg_slider_captions', 'mg_slider_random', 'mg_kenburns_fx'); break;
			
			// lightbox video
			case 'video' : 
				$f = array('mg_video_url', 'mg_video_use_poster', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_feat_match_txt', 'mg_cust_attr'); break;	
				
			// inline video
			case 'inl_video' : 
				$f = array('mg_video_url', 'mg_video_use_poster', 'mg_autoplay_inl_video', 'mg_cust_icon'); break;	
				
			// lightbox audio
			case 'audio' : 
				$f = array('mg_audio_tracks', 'mg_soundcloud_url', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_img_display_mode', 'mg_img_maxheight', 'mg_lb_feat_match_txt', 'mg_lb_img_fx', 'mg_cust_attr'); break;	
				
			// inline audio
			case 'inl_audio' : 
				$f = array('mg_audio_tracks', 'mg_soundcloud_url', 'mg_kenburns_fx', 'mg_static_show_overlay', 'mg_cust_icon'); break;	
		
			// link
			case 'link' : 
				$f = array('mg_link_url', 'mg_link_target', 'mg_link_nofollow', 'mg_kenburns_fx', 'mg_cust_icon'); break;
		
			// custom content
			case 'lb_text' : 
				$f = array('mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_contents_padding'); break;
				
			// post contents
			case 'post_contents' : 
				$f = array('mg_cpt_source', 'mg_cpt_tax_term', 'mg_post_query_offset', 'mg_use_item_feat_img', 'mg_hide_feat_img', 'mg_link_to_post', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_img_display_mode', 'mg_img_maxheight', 'mg_lb_feat_match_txt', 'mg_lb_img_fx'); break;	
			
			// inline text
			case 'inl_text' : 
				$f = array('mg_inl_txt_box_bg', 'mg_inl_txt_bg_alpha', 'mg_inl_txt_img_as_bg', 'mg_inl_txt_color', 'mg_inl_txt_vert_align', 'mg_inl_txt_no_resize', 'mg_inl_txt_custom_css'); break;
			
			// spacer
			case 'spacer' : 
				$f = array('mg_spacer_vis'); break;
			
			// wooCommerce product
			case 'woocomm' : 
				$f = array('mg_wc_prod_cats', 'mg_link_only', 'mg_kenburns_fx', 'mg_cust_icon', 'mg_layout', 'mg_lb_max_w', 'mg_lb_feat_match_txt', 'mg_lb_img_display_mode', 'mg_img_maxheight', 'mg_lb_img_fx', 'mg_slider_w_val', 'mg_slider_crop', 'mg_slider_autoplay', 'mg_slider_thumbs', 'mg_slider_captions', 'mg_slider_random', 'mg_slider_add_featured'); break;
			
			
			default: $f = array(); break;	
		}	
		
		
		// add comments switch if they are active
		if(get_option('mg_lb_comments') && in_array('mg_lb_max_w', $f)) {
			$f[] = 'mg_lb_no_comments';	
		}
		
		
		/* MG-FILTER - manage which meta fields are assigned to item types */
		return apply_filters('mg_item_meta_to_type', $f, $this->item_type);	
	}
	
	
	
	/* return fields code */
	public function get_fields_code() {
		$raw_structure = array();
		$structure = array();
		$code = '';
		
		// know which meta keys item has got
		$this->item_keys = get_post_custom_keys($this->item_id);
		
		// get fields
		foreach($this->type_fields() as $f) {
			$raw_structure[$f] = $this->fields[$f];	
		}
		
		// grab groups and split fields in there
		foreach($raw_structure as $id => $args) {
			if(!isset($structure[ $args['group'] ])) {
				$structure[ $args['group'] ] = array();
			}
			
			$structure[ $args['group'] ][$id] = $args; 
		}
		
		// compose code
		foreach($structure as $group => $fields) {
			$code .= '
			<section class="mg_imf_group mg_imf_'.$group.'">
			<h4>'. $this->groups[$group] .'</h4>';
			
				foreach($fields as $fid => $args) {
					$code .= '
					<div class="mg_imf_field mg_imf_'.$fid.'">
						'. $this->opt_to_code($fid) .'
					</div>';	
				}
			
			$code .= '</section>';	
		}
		
		
		return $code;
	}
	
	
	/* 
	 * get validation indexes for item type 
	 * @return (array)
	 */
	public function get_fields_validation() {
		require_once(MG_DIR .'/functions.php');
		$indexes = array();
		
		foreach($this->type_fields() as $fid) {
			$f = $this->fields[$fid];
			
			if($f['type'] != 'custom') {
				$indexes[] = array('index'=>$fid, 'label'=>$f['label']);	
			}
			
			if($f['type'] == 'val_n_type') {
				$indexes[] = array('index'=>$fid.'_type', 'label'=>$f['label'].' type');		
			}
			
			
			// custom fields validation
			if($f['type'] == 'custom') {
				switch($fid) {
					
					case 'mg_cust_attr' :
						$co_indexes = mg_get_type_opt_indexes($this->item_type);
						if(is_array($co_indexes)) {
							foreach($co_indexes as $copt) {
								$indexes[] = array('index'=>$copt, 'label'=>$copt);
							}
						}
						break;
						
					case 'mg_cust_icon' :
						$indexes[] = array('index'=>'mg_cust_icon', 'label'=>'Custom Icon');
						break;	
					
					case 'mg_slider_img' :
						$indexes[] = array('index'=>'mg_slider_img', 'label'=>'Slider images');
						
						if($this->item_type == 'img_gallery') {
							$indexes[] = array('index'=>'mg_slider_vid', 'label'=>'Slider images video');
						}
						break;
						
					case 'mg_audio_tracks' :
						$indexes[] = array('index'=>'mg_audio_tracks', 'label'=>'Tracks');
						break;	
					
					case 'mg_video_url' :
						$indexes[] = array('index'=>'mg_video_url', 'label'=>'Video URL');
						break;	
				}
			}
		}		
		
		return $indexes;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////////////
	
	
		
	/* Passing field ID, returns its code basing on type */ 	
	public function opt_to_code($field_id) {
		if(!isset($this->fields[$field_id])) {return '';}
		
		$f 		= $this->fields[$field_id];
		$code 	= '';
		
		// set field value
		if(is_array($this->item_keys)) {
			if(!in_array($field_id, $this->item_keys)) {
				$val = (isset($f['def'])) ? $f['def'] : '';
			} else {
				$val = get_post_meta($this->item_id, $field_id, true);	
			}
		} else {
			$val = (isset($f['def'])) ? $f['def'] : '';	
		}
		
		
		### VALUE FILTER - hook to manage already existing values ###
		$val = $this->filter_field_val($field_id, $val);
		

		// default label block
		if(isset($f['label'])) {
			$def_label = '<label>'. $f['label'];
				if(isset($f['note']) && !empty($f['note'])) {$def_label .= '<em>'. $f['note'] .'</em>';}
			$def_label .= '</label>';
		}
		
		// switch by type
		switch($f['type']) {
			
			// text
			case 'text' :
				$ph = (isset($f['placeh'])) ? $f['placeh'] : ''; 
				$code = $def_label. '
				<input type="text" name="'. $field_id .'" value="'. esc_attr((string)$val) .'" placeholder="'. esc_attr($ph) .'" autocomplete="off" />';
				break;
				
			// select
			case 'select' :
				$multiple_attr = (isset($f['multiple']) && $f['multiple']) ? 'multiple="multiple"' : '';
				$multiple_name = (isset($f['multiple']) && $f['multiple']) ? '[]' : '';
				
				$code = $def_label. '
				<select data-placeholder="'. __('Select an option', 'mg_ml') .' .." name="'. $field_id . $multiple_name.'" class="lcweb-chosen" autocomplete="off" '.$multiple_attr.'>';
				
				foreach($f['val'] as $key => $name) {
					if(isset($f['multiple']) && $f['multiple']) {
						$sel = (in_array($key, (array)$val)) ? 'selected="selected"' : '';
					} else {
						$sel = ($key == (string)$val) ? 'selected="selected"' : '';
					}
					
					$code .= '<option value="'.$key.'" '.$sel.'>'. $name .'</option>';	
				}
				
				$code .= '</select>';
				break;
			
			// checkbox
			case 'checkbox' :
				$sel = ($val) ? 'checked="checked"' : '';
				$code = $def_label. '
				<input type="checkbox" name="'. $field_id .'" value="1" class="ip-checkbox" '.$sel.' autocomplete="off" />';
				break;
			
			// textarea
			case 'textarea' :
				$ph = (isset($f['placeh'])) ? $f['placeh'] : ''; 
				$code = $def_label. '
				<textarea name="'. $field_id .'" placeholder="'. esc_attr($ph) .'" autocomplete="off">'. (string)$val .'</textarea>';
				break;
			
			// slider
			case 'slider' :
				$code = $def_label. '
				<div class="lcwp_form">
					<div class="lcwp_slider" step="'. $f['step'] .'" max="'.$f['max_val'].'" min="'.$f['min_val'].'"></div>
					<input type="text" value="'. $val .'" name="'. $field_id .'" maxlength="'. strlen($f['max_val']) .'" class="lcwp_slider_input" autocomplete="off" />
					<span>'. $f['value'] .'</span>
				</div>';
				break;
			
			// color
			case 'color' :
				$code = $def_label. '
				<div class="lcwp_colpick">
                	<span class="lcwp_colblock" style="background-color: '. $val .';"></span>
                	<input type="text" name="'. $field_id .'" value="'. $val .'" autocomplete="off" />
                </div>';
				break;
			
			// value and type
			case 'val_n_type' :
				$code = $def_label. '
				<input type="text" class="lcwp_slider_input" name="'. $field_id .'" value="'. $val .'" maxlength="'. $f['max_val_len'] .'" style="height: 28px; width: 60px; display: inline-block; text-align: center;" autocomplete="off" />';
				
				$sel = (get_post_meta($this->item_id, $field_id .'_type', true) == 'px') ? 'selected="selected"' : '';
				$code .= '
				<select name="'. $field_id .'_type" style="height: 28px; position: relative; top: -3px; width: 60px;" autocomplete="off">
					<option value="%">%</option>
				  	<option value="px" '.$sel.'>px</option>
				</select>';
				break;
				
			// 4 numbers
			case '4_numbers' :
				if(!is_array($val) || count($val) != 4) {$val = $f['def'];}
				
				$maxlen = 'maxlength="'. strlen($f['max_val']) .'"';
				$min = 'min="'. (int)$f['min_val'] .'"';
				$max = 'max="'. (int)$f['max_val'] .'"';
				
				$code = $def_label;
				
				for($a=0; $a<4; $a++) {
					$code .= '<input type="number" name="'. $field_id .'[]" value="'. $val[$a] .'" '.$maxlen.' '.$min.' '.$max.' style="height: 28px; width: 60px; display: inline-block;" autocomplete="off" />' ;	
				}
				
				if(isset($f['value'])) {
					$code .= ' <span>'. $f['value'] .'</span>';
				}
				break;	
				
			
			// custom - use callback
			case 'custom' :
				$code =  call_user_func(array($this, $f['cust_callback']));
				break; 
		}
		
		return $code;
	}
		
	
	###############################################
	
	
	/* custom attribute fields code */
	public function cust_attr_f_code() {
		require_once(MG_DIR .'/functions.php');
		
		// convert types to implemented types (old versions mistakes..)
		if($this->item_type == 'single_img') {$type = 'image';}
		else {$type = $this->item_type;}
		
		
		// if no attributes for this type
		if(!get_option('mg_'. $type .'_opt')) {
			return '<p><em>'. __('No custom attributes created for this type', 'mg_ml') .' ..</em></p>';
		}
		
		$icons = get_option('mg_'. $type .'_opt_icon');
		$code = '';
		
		// compose
		$a = 0;
		foreach(get_option('mg_'. $type .'_opt') as $opt) {
			$val = get_post_meta($this->item_id, 'mg_'. $type .'_'.strtolower(lcwp_stringToUrl($opt)), true);
			$icon = (isset($icons[$a])) ? '<i class="mg_item_builder_opt_icon fa '.$icons[$a].'"></i> ' : '';
			
			$code .= '
			<div class="mg_imf_field">
				<label>'. $icon . mg_wpml_string($type, $opt) .'</label>
				<input type="text" name="mg_'.$type.'_'.strtolower(lcwp_stringToUrl($opt)).'" value="'. esc_attr((string)$val) .'" autocomplete="off" />
			</div>';
			
			$a++;
		}	
		
		return $code;
	}
	
	
	
	/* custom icon field's code */
	public function cust_icon_f_code() {
		include_once(MG_DIR . '/classes/lc_font_awesome_helper.php');
		include_once(MG_DIR . '/functions.php');
		
		$icon = get_post_meta($this->item_id, 'mg_cust_icon', true);
		$code = '
		<div class="mg_type_opt_icon_trigger">
			<label>'. __("Custom icon <em>To be used in secondary and custom overlays</em>", 'mg_ml') .'</label>
			<i class="fa '.$icon.'" title="set category icon" style="display: inline-block;"></i>
			<input type="hidden" name="mg_cust_icon" value="'.$icon.'" autocomplete="off" /> 
		</div>';
		
		
		// hidden code for lightbox
		$code .=  mg_fa_icon_picker_code( __('use default icon', 'mg_ml') );
		return $code;
	}
	
	
	
	/* images picker code */
	public function slider_img_f_code() {
		require_once(MG_DIR .'/functions.php');
		
		$vid_to_img = get_post_meta($this->item_id, 'mg_slider_vid', true); 
		if(empty($vid_to_img)) {$vid_to_img = array();}
		
		$slider_elem = mg_existing_sel( get_post_meta($this->item_id, 'mg_slider_img', true), $vid_to_img); 
		
		$code = '
		<div id="gallery_img_wrap">
        	<ul>
				'. mg_sel_slider_img_list($slider_elem) .'
            </ul>	
            <br class="lcwp_clear" />
		</div>
        <div style="clear: both; height: 20px;"></div>
              
		<div id="mg_img_search_wrap">
        	<input type="text" placeholder="'. __('search', 'mg_ml') .' .." class="mg_search_field" autocomplete="off" />
        	<span class="mg_search_btn" title="search"></span>
		</div>
			  
       	<h4>'. __('Choose images', 'mg_ml') .' <span class="mg_TB mg_upload_img add-new-h2">'. __('Manage Images', 'mg_ml') .'</span></h4>
		<div id="gallery_img_picker"></div>';
		
		// if inline slider - hide video attach option
		if($this->item_type == 'inl_slider') {
			$code .= '
			<style type="text/css">
			#gallery_img_wrap li i {
				display: none !important;	
			}
			</style>';	
		}
		
		return $code;
	}
	
	
	
	/* tracks picker code */
	public function audio_tracks_f_code() {
		require_once(MG_DIR .'/functions.php');
		$tracks = mg_existing_sel( get_post_meta($this->item_id, 'mg_audio_tracks', true));
		
		$code = '
		<div id="audio_tracks_wrap">
			<ul>';
			
			if(is_array($tracks)) {
				foreach($tracks as $track_id) {
					$track_title =  html_entity_decode(get_the_title($track_id), ENT_NOQUOTES, 'UTF-8');
					
					// if WP > 3.8 use iconic font
					if( (float)substr(get_bloginfo('version'), 0, 3) >= 3.8) {
						$icon = '<div class="mg_audio_icon dashicons-media-audio dashicons"></div>';
					} else {
						$icon = '<img src="'. MG_URL .'/img/audio_icon.png" />';	
					}

					$code .= '
					<li id="mgtl_'. $track_id .'">
						<input type="hidden" name="mg_audio_tracks[]" value="'. $track_id .'" />
						'.$icon.'
						<span title="remove track"></span>
						<p title="'.$track_title.'">'.mg_excerpt($track_title, 25).'</p>
					</li>';			
				}
			}
			else {
				$code .= '<p>'. __('No tracks selected', 'mg_ml') .' ..</p>';
			}
			
        $code .= '
            </ul>	
			<br class="lcwp_clear" />
		</div>
		<div style="clear: both; height: 20px;"></div>
		
		<div id="mg_audio_search_wrap">
		  <input type="text" placeholder="'. __('search', 'mg_ml') .' .." class="mg_search_field"  />
		  <span class="mg_search_btn" title="search"></span>
		</div>
		
		<h4>'. __('Choose tracks', 'mg_ml') .' <span class="mg_TB mg_upload_audio add-new-h2">'. __('Manage Tracks', 'mg_ml') .'</span></h4>
		<div id="audio_tracks_picker"></div>';	
		
		return $code;
	}
	
	
	
	/* video URL */
	public function video_url_f_code() {
		require_once(MG_DIR .'/functions.php');
		
		return '
		<label>'. __('Video URL', 'mg_ml') .'</label>
		<input type="text" value="'.get_post_meta($this->item_id, 'mg_video_url', true) .'" name="mg_video_url" /> 
		
		<img src="'. MG_URL .'/img/media-library-src.png" title="'. __('search in media library', 'mg_ml') .'" id="mg_video_src" />
		<p>'. __('Insert Youtube (<strong>http://youtu.be</strong>), Vimeo or Dailymotion clean video url. Otherwise select a video from your media library', 'mg_ml') .'</p>';
	} 
	


	////////////////////////////////////////////////////////////////////////////////////////



	/* echo javascript code used by item types */
	public function echo_type_js_code() {
		$t = $this->item_type;
		
		// image picker
		if($t == 'img_gallery' || $t == 'inl_slider') :
			?>
			<script type="text/javascript">
			var mg_img_pp = 26;
			
			// reload the selected images to check changes
			function mg_sel_img_reload() {
				var sel_img = jQuery.makeArray();	
				var sel_vid = jQuery.makeArray();	
				
				jQuery('#gallery_img_wrap li').each(function() {
					sel_img.push( jQuery(this).children('.mg_slider_img_field').val() );
					sel_vid.push( jQuery(this).children('.mg_slider_video_field').val() );
				});
				
				jQuery('#gallery_img_wrap ul').html('<div style="height: 30px;" class="lcwp_loading"></div>');
				
				var data = {
					action: 'mg_sel_img_reload',
					images: sel_img,
					videos: sel_vid
				};
				
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#gallery_img_wrap ul').html(response);
				});	
			}
			
			// change slider imges picker page
			jQuery('body').undelegate('.mg_img_pick_back, .mg_img_pick_next');
			jQuery('body').delegate('.mg_img_pick_back, .mg_img_pick_next', 'click', function() {
				var page = jQuery(this).attr('id').substr(4);
				mg_load_img_picker(page);
			});
			
			// change images per page
			jQuery('body').undelegate('#mg_img_pick_pp', 'change');
			jQuery('body').delegate('#mg_img_pick_pp', 'change', function() {
				var pp = jQuery(this).val();
				
				if( pp.length >= 2 ) {
					if( parseInt(pp) < 26 ) { mg_img_pp = 26;}
					else {mg_img_pp = pp;}
					
					mg_load_img_picker(1);
				}
			});
			
			// on search
			jQuery('body').undelegate('#mg_img_search_wrap .mg_search_btn');
			jQuery('body').delegate('#mg_img_search_wrap .mg_search_btn', 'click', function() {
				mg_load_img_picker(1);
			});
			
			// load slider images picker
			function mg_load_img_picker(page) {
				var data = {
					action: 'mg_img_picker',
					page: page,
					per_page: mg_img_pp,
					mg_search: jQuery('#mg_img_search_wrap .mg_search_field').val()
				};
				
				jQuery('#gallery_img_picker').html('<div style="height: 30px;" class="lcwp_loading"></div>');
				
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#gallery_img_picker').html(response);
				});	
				
				return true;
			}
			mg_load_img_picker(1);
			
			// add slider images
			jQuery('body').undelegate('#gallery_img_picker li', 'click');
			jQuery('body').delegate('#gallery_img_picker li', 'click', function() {
				var img_id = jQuery(this).children('figure').attr('rel');
				var img_url = jQuery(this).children('figure').attr('style');
				
				if( jQuery('#gallery_img_wrap ul > p').size() > 0 ) {jQuery('#gallery_img_wrap ul').empty();}
				
				jQuery('#gallery_img_wrap ul').append(
				'<li>'+
					'<input type="hidden" name="mg_slider_img[]" class="mg_slider_img_field" value="'+ img_id +'" />'+
					'<input type="hidden" name="mg_slider_vid[]" class="mg_slider_video_field" value="" autocomplete="off" />'+
					
					'<figure style="'+ img_url +'"></figure>'+
					'<span title="remove image"></span>'+
					'<i class="mg_slider_video_off" title="set as video slide"></i>'+
				'</li>');
				
				mg_sort();
			});
	
			
			// attach video to image slide
			jQuery('body').undelegate('#gallery_img_wrap li i', 'click');
			jQuery('body').delegate('#gallery_img_wrap li i', 'click', function() {
				var $parent = jQuery(this).parent();
				var val = $parent.find('.mg_slider_video_field').val();
				
				var new_val = prompt("<?php _e('Insert a Youtube / Vimeo video URL or set it to empty', 'mg_ml') ?>", val);
	
				if(new_val !== null) {
					if(new_val === '') {
						$parent.find('.mg_slider_video_field').val('');
						jQuery(this).removeClass('mg_slider_video_on').addClass('mg_slider_video_off');	
					}
					else if( new_val.indexOf('youtube.com/watch?v=') !== -1 || new_val.indexOf('vimeo.com/') !== -1) {
						$parent.find('.mg_slider_video_field').val(new_val);
						jQuery(this).removeClass('mg_slider_video_off').addClass('mg_slider_video_on');
					}
					else {
						alert("<?php _e('Invalid URL inserted', 'mg_ml'); ?>");
					}	
				}
			});
			</script>
			<?php
			
			
		// tracks upload and select
		elseif($t == 'audio' || $t == 'inl_audio') :
			?>
			<script type="text/javascript">
			var mg_audio_pp = 26;
			
			// reload the selected tracks to refresh their titles
			function mg_sel_tracks_reload() {
				var sel_tracks = jQuery.makeArray();	
				
				jQuery('#audio_tracks_wrap li').each(function() {
					var track_id = jQuery(this).children('input').val();
					sel_tracks.push(track_id);
				});
				
				jQuery('#audio_tracks_wrap ul').html('<div style="height: 30px;" class="lcwp_loading"></div>');
				
				var data = {
					action: 'mg_sel_audio_reload',
					tracks: sel_tracks
				};
				
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#audio_tracks_wrap ul').html(response);
				});	
			}
			
			// change tracks picker page
			jQuery('body').undelegate('.mg_audio_pick_back, .mg_audio_pick_next', 'click');
			jQuery('body').delegate('.mg_audio_pick_back, .mg_audio_pick_next', 'click', function() {
				var page = jQuery(this).attr('id').substr(4);
				mg_load_audio_picker(page);
			});
			
			// change tracks per page
			jQuery('body').undelegate('#mg_audio_pick_pp', 'change');
			jQuery('body').delegate('#mg_audio_pick_pp', 'change', function() {
				var pp = jQuery(this).val();
				
				if( pp.length >= 2 ) {
					if( parseInt(pp) < 26 ) { mg_audio_pp = 26;}
					else {mg_audio_pp = pp;}
					
					mg_load_audio_picker(1);
				}
			});
			
			// on search
			jQuery('body').undelegate('#mg_audio_search_wrap .mg_search_btn', 'click');
			jQuery('body').delegate('#mg_audio_search_wrap .mg_search_btn', 'click', function() {
				mg_load_audio_picker(1);
			});
			
			// load audio tracks picker
			function mg_load_audio_picker(page) {
				var data = {
					action: 'mg_audio_picker',
					page: page,
					per_page: mg_audio_pp,
					mg_search: jQuery('#mg_audio_search_wrap .mg_search_field').val()
				};
				
				jQuery('#audio_tracks_picker').html('<div style="height: 30px;" class="lcwp_loading"></div>');
				
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#audio_tracks_picker').html(response);
				});	
				
				return true;
			}
			mg_load_audio_picker(1);
			
			// add audio track
			jQuery('body').undelegate('#audio_tracks_picker li', 'click');
			jQuery('body').delegate('#audio_tracks_picker li', 'click', function() {
				var track_id = jQuery(this).attr('id').substr(5);
				var track_tit = jQuery(this).children('p').text();	
				
				if( jQuery('#audio_tracks_wrap ul > p').size() > 0 ) {jQuery('#audio_tracks_wrap ul').empty();}
				
				<?php 
				// if WP > 3.9 use iconic font
				$icon = ((float)substr(get_bloginfo('version'), 0, 3) >= 3.9) ? '<div class="mg_audio_icon dashicons-media-audio dashicons"></div>' : '<img src="'.MG_URL . '/img/audio_icon.png" />';	
				?>
				
				if( jQuery('#audio_tracks_wrap li#mgtl_'+ track_id).size() == 0) { 
					jQuery('#audio_tracks_wrap ul').append(
					'<li id="mgtl_'+ track_id +'">'+
						'<input type="hidden" name="mg_audio_tracks[]" value="'+ track_id +'" />'+
						'<?php echo $icon ?>'+
						'<span title="remove track"></span>'+
						'<p>'+ track_tit +'</p>'+
					'</li>');
					
					mg_sort();
				}
			});
			</script>
			<?php	
			
			
		// video upload and select
		elseif($t == 'video' || $t == 'inl_video') :
			?>		
			<script type="text/javascript">
			jQuery(document).undelegate('#mg_video_src', "click");
			jQuery(document).delegate('#mg_video_src', "click", function (e) {
				e.preventDefault();

				var wp_selector = wp.media({
					title: "<?php _e('Wordpress Video Management', 'mg_ml') ?>",
					button: { text: '<?php echo mg_sanitize_input( __('Select')) ?>' },
					library : { type : 'video'},
					multiple: false
				})
				.on('select', function() {
					var selection = wp_selector.state().get('selection').first().toJSON();
	
					var itemurl = selection.url;
					var video_pattern = /(^.*\.mp4|m4v|webm|ogv|wmv|flv*)/gi;
		  
					if(itemurl.match(video_pattern) ) {
					  jQuery('#mg_video_src').siblings('input[type=text]').val(itemurl);
					}
					else { alert('<?php echo mg_sanitize_input( __('Please select a valid video file for the WP player. Supported extensions:', 'mg_ml')); ?> mp4, m4v, webm, ogv, wmv, flv'); }
				})
				.open();
			});
			</script>
			<?php 
		
		
		// post contents - CPT terms async load
		elseif($t == 'post_contents') : 
			?>
			<script type="text/javascript">
			jQuery(document).undelegate('.mg_imf_mg_cpt_source select', 'change');
			jQuery(document).delegate('.mg_imf_mg_cpt_source select', 'change', function() {
				if(!mg_is_acting) {
					mg_is_acting = true
					
					var $wrap = jQuery('.mg_imf_mg_cpt_tax_term select').parent();
					$wrap.html('<div style="width: 30px; height: 60px;" class="lcwp_loading"></div>');
					
					var data = {
						action: 'mg_sel_cpt_source',
						cpt: jQuery(this).val()
					};
					
					jQuery.post(ajaxurl, data, function(response) {
						var txt = "<label><?php echo $this->fields['mg_cpt_tax_term']['label'] ?> <em><?php echo $this->fields['mg_cpt_tax_term']['note'] ?></em></label>";			
						$wrap.html( txt + response );
						
						mg_live_chosen();
						mg_is_acting = false;
					});		
				}
			});
			</script>
			<?php
		endif;	
		
		return true;
	}


	
	//////////////////////////////////////////////////////
	
	
	/* filter values for specific fields */
	private function filter_field_val($field_id, $val) {
		
		// lightbox layout - replace SIDE with side_tripartite
		if($field_id == 'mg_layout' && $val == 'side') {
			$val = 'side_tripartite';
		}
		
		
		return $val;	
	}
}

