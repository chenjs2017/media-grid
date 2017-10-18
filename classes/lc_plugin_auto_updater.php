<?php
/* 
 * Automatic updates delivery for Envato premium plugins - with purchase verifier
 * Inspired by Abid Omar class https://github.com/omarabid/Self-Hosted-WordPress-Plugin-repository
 * 
 * @version:	1.2
 * @author :	Luca Montanari aka LCweb
 * @website:	http://www.lcweb.it
 * @license:	Commercial license
 */
 
if(!class_exists('lc_wp_autoupdate')) : 
 
class lc_wp_autoupdate {
	// website URL
	private $site_url;
	
	// endpoint signature - to add safety to endpoint calls
	private $signature;
	
	// update remove host URL
	private $update_endpoint;
	
	// plugins data as fetched from WP data
	private $plugin_data;
	
	// Plugin's current version
	private $current_version;

	// Plugin Slug (plugin_directory/plugin_file.php)
	private $plugin_slug;

	//Plugin name (plugin_file) calculated on plugin_slug
	private $slug;
	

	// AJAX action name - to be used in settings template javascript
	public $ajax_action_name;
	
	// verified purchases - associative array - array($plugin_slug => site url)
	private $verified_purch;
	
	// purchase data - associative array - array($plugin_slug => array(username => .. , purch_code => ..))
	private $purch_data;

	
	// $upgrader_post_install_callback - function name
	private $upgr_post_inst_cb;

	// has got a new update?
	private $update_avail = false;
	
	
	// (inactive updater) - is there a new update to notify? contains new version number
	private $iu_update_avail = false;
	
	
	/**
	 * Initialize a new instance of the WordPress Auto-Update class
	 * @param string $plugin_filepath
	 * @param string $update_endpoint
	 * @param string $signature
	 *
	 * @param string $upgrader_post_install_callback - use a function name to trigger actions after update complete
	 * @param bool $avoid_files_deletion - useful to keep cache files
	 * @param string $upgrader_process_complete_callback - TODO
	 */
	public function __construct($plugin_filepath, $update_endpoint, $signature, $upgrader_post_install_callback = false, $avoid_files_deletion = false, $upgrader_process_complete_callback = false) {
		$this->plugin_data = get_plugin_data($plugin_filepath);
		
		$this->site_url = $_SERVER['SERVER_NAME'];
		$this->signature = $signature;
		
		// Set the class public variables
		$this->current_version = $this->plugin_data['Version'];
		$this->update_endpoint = $update_endpoint;

		// Set the Plugin Slug	
		$this->plugin_slug = plugin_basename($plugin_filepath);
		list($t1, $t2) = explode( '/', $this->plugin_slug);
		$this->slug = str_replace( '.php', '', $t2);		


		///////////////
		
		
		// setup wizard
		add_filter('admin_head', array(&$this, 'plugins_list_wizard_css'));
		add_filter('plugin_row_meta', array(&$this, 'plugins_list_wizard_btn'), 100, 2);
		add_filter('admin_footer', array(&$this, 'plugins_list_wizard_js'));
		
		
		///////////////


		// callback to force plugin update check
		if(isset($_GET['lcwpau_force_check'])) {
			add_action('admin_init', array(&$this, 'force_updates_check'));
		}
		if(isset($_GET['lcwpau_force_check_done'])) {
			add_action('admin_init', array(&$this, 'force_updates_check_js_redirect2'));
		}
		
		
		///////////////

		
		// Override requests for plugin information
		add_filter('plugins_api', array($this, 'set_plugin_info'), 20, 3);

		// define the alternative API for updating checking
		add_filter('pre_set_site_transient_update_plugins', array( &$this, 'check_update' ));

		// Define the alternative response for information checking
		add_filter('plugins_api', array( &$this, 'check_info' ), 10, 3);
		
		
		///////////////
		
		
		// plugin's version check if updater isn't active
		$this->iu_check_for_updates();
		
		
		// register AJAX function to validate purchase data
		$this->ajax_action_name = 'lcwpau_'. md5($this->plugin_slug);
		add_action('wp_ajax_'. $this->ajax_action_name, array( &$this, 'purch_verifier_ajax'));
		
		
		///////////////
		
		
		// upgrader_post_install hook callback
		if(!empty($upgrader_post_install_callback)) {
			$this->upgr_post_inst_cb = $upgrader_post_install_callback;
			add_filter('upgrader_post_install', array($this, 'upgrader_post_install_callback'), 10, 3);
		}
		
		
		///////////////
		
		
		// avoid old plugin files deletion
		if($avoid_files_deletion) {
			add_filter('upgrader_package_options', array($this, 'avoid_old_files_deletion'), 999);
		}
		
		
		/* to eventually remove files/folders
		if($upgrader_process_complete_callback) {
			//add_action('upgrader_process_complete'); 
			/*
			if ( ! $wp_filesystem->exists($this_plugin_dir) ) //If it's already vanished.
				$deleted = $wp_filesystem->delete($this_plugin_dir, true);
		}*/
	}
	


	///////////////////////////////////////////////////////////////

	
	/* 
	 * inactive updater - performs a check to know if there are updates available 
	 * @return (text) javascript code to ynamically append warning or empty string
	 */
	private function iu_check_for_updates() {
		if(!is_admin() || $this->checked_purch_code()) {return false;}
		$transient_name = 'lcwpau_'. md5($this->plugin_slug) .'_iu_check'; 
		
		// check transient or perform remote call
		$last_ver_num = get_transient($transient_name);
		
		if(empty($last_ver_num) || isset($_REQUEST['lcwpau_force_check'])) {
			$last_ver_num = $this->getRemote('version_number');
			
			if(empty($last_ver_num)) {return false;}
			else {
				set_transient($transient_name, $last_ver_num, 3600); // check each hour	
			}	
		}
		
		
		
		// act if there's a new version
		if( (float)$last_ver_num > (float)$this->plugin_data['Version'] ) { // version compre fails comparing (eg.) 1.09 and 1.1
			$this->iu_update_avail = $last_ver_num;
			
			// change updates count -  only from WP 3.5
			if((float)substr(get_bloginfo('version'), 0, 3) >= 3.5) {
				add_filter('wp_get_update_data', array($this, 'iu_increase_plugins_update_count'));
			}
			
			add_action('core_upgrade_preamble', array($this, 'iu_upgrade_core_message'));
			
			// plugin's list warning
			add_action("after_plugin_row_".$this->plugin_slug, array($this, 'iu_plugins_list_warning'), 10, 3);
		}
	}
	
	
	/* update notifier for inactive updater - increase plugins update count */
	public function iu_increase_plugins_update_count($update_data) {
		$curr_plug_count = (float)$update_data['counts']['plugins'];
		$update_data['counts']['plugins'] = $curr_plug_count + 1;
		
		$curr_tot_count = (float)$update_data['counts']['total'];
		$update_data['counts']['total'] = $curr_tot_count + 1;
		
		return $update_data;
	}
	
	
	/* update notifier for inactive updater - print message in WP updates core page */
	public function iu_upgrade_core_message() {
		echo '<h3>'. $this->plugin_data['Name'] .'</h3>
		<p>'. __('New version available.') .' '. __('Enable updater to get version', 'lcwpau') .' '. $this->iu_update_avail .'</p>';	
	}
	
	
	/* update notifier for inactive updater - print message in plugins list */
	public function iu_plugins_list_warning($plugin_file, $plugin_data, $status) {
		?>
		<tr class="active plugin-update-tr">
        	<td colspan="3" class="plugin-update colspanchange">
            	<div class="update-message notice inline notice-warning notice-alt">
                	<p><?php echo __('New version available.') .' '. __('Enable updater to get version', 'lcwpau') .' '. $this->iu_update_avail ?></p>
                </div>
           	</td>
        </tr>
        <?php
	}


	/**********************************************************************************/


	/**
	 * upgrader_post_install hook callback - recall function
	 */
	public function upgrader_post_install_callback($response, $hook_extra, $result) {
		if(isset($hook_extra['plugin']) && $hook_extra['plugin'] == $this->plugin_slug) {
			if(function_exists($this->upgr_post_inst_cb)) {
				call_user_func_array($this->upgr_post_inst_cb, array());	
			}
		}

		return $response;
	}
	


	/**
	 * Avoid old plugin files deletion on update
	 */
	public function avoid_old_files_deletion($options) {
		if(isset($options['hook_extra']['plugin']) && $options['hook_extra']['plugin'] == $this->plugin_slug) {
			$options["clear_destination"] = false;
			$options["abort_if_destination_exists"] = false;
		}
		
		return $options;
	}
	


	/**
	 * Forces a new check for updates - alternatively call wp-admin/update-core.php?force-check=1 
	 */
	public function force_updates_check() {
		global $wpdb;
		$wpdb->query("UPDATE ". $wpdb->prefix ."options SET option_value = '' WHERE option_name = '_site_transient_update_plugins'");
		add_action('admin_head', array(&$this, 'force_updates_check_js_redirect'), 1);
	}

	public function force_updates_check_js_redirect() {
		?>
		<script type="text/javascript">
		var d = new Date();
		var url = window.location.href + '&' + d.getTime();
		window.location.replace( url.replace('lcwpau_force_check', 'lcwpau_force_check_done') );
		</script>
		<?php		
	}
	
	public function force_updates_check_js_redirect2() {
		?>
		<script type="text/javascript">
		var url = window.location.href;
		window.location.replace( url.replace('lcwpau_force_check_done', 'lcwpau_force_check_refreshed') );
		</script>
		<?php		
	}

	
	/**********************************************************************************/


	public function plugins_list_wizard_css() {
		global $current_screen;
		if($current_screen->id != 'plugins' && $current_screen->id != 'plugins-network') {return false;}
		
		?>
        <style type="text/css">
		.lcwpau_wizard_btn {
			color: #fff; 
			padding: 2px 10px 3px; 
			border-radius: 2px;	
			opacity: 0.65;
			
			-webkit-transition: all .15s ease-in-out !important;
			-ms-transition: 	all .15s ease-in-out !important;
			transition: 		all .15s ease-in-out !important;
		}
		.lcwpau_wizard_btn:hover,
		.lcwpau_wizard_btn:focus {
			box-shadow: none;
			opacity: 1;	
			color: #fff;
		}
		</style>
        <?php	
	}

	
	public function plugins_list_wizard_btn($links, $file) {
		if($file == $this->plugin_slug) {
			
			// as first - fix misleading link to homonymous WP plugins - only if no update available
			if(!$this->update_avail && isset($links[2]) && strpos($links[2], 'plugin-install.php?tab=plugin-information') !== false) {
				$links[2] = '<a href="'. $this->plugin_data['PluginURI'] .'" target="_blank">'. __('Visit plugin site') .'</a>';	
			}
			
			// add wizard button
			$checked 	= $this->checked_purch_code();
			$text 		= ($checked) ? __('Updater active', 'lcwpau') : __('Setup updater', 'lcwpau');
			$btn_style 	= ($checked) ? 'background-color: #46b450;' : 'background-color: #dc3232;'; 
			
			// inactive updater - check anyway for updates
			$update_warn = ($this->iu_update_avail) ? '<script type="text/javascript">jQuery(document).ready(function() {
				jQuery(".active[data-plugin=\''. $this->plugin_slug .'\']").addClass("update");	
			});</script>' : '';
			
			// append button
			$links['lcwpau_wizard_btn'] = '<a href="#TB_inline?width=600&height=550&inlineId='. md5($this->plugin_slug) .'_wizard" class="thickbox lcwpau_wizard_btn" style="'.$btn_style.'">'. $text  .'</a>' . $update_warn;
			
		}
		
		return $links;
	}
	

	
	public function plugins_list_wizard_js() {
		global $current_screen;
		if($current_screen->id != 'plugins' && $current_screen->id != 'plugins-network') {return false;}
		
		// hidden code to include in lightbox
		echo '<div id="'. md5($this->plugin_slug) .'_wizard" style="display: none;">';
		echo $this->purch_verifier_template();
		echo '</div>';
		
		// JS to launch lightbox and verify
		if(!isset($GLOBALS['lcwpau_base_wizard_js_added'])) {
			?>
            <script type="text/javascript">
			jQuery(document).ready(function(e) {
                jQuery(document.body).delegate('.lcwpau_wizard_btn', 'click', function() {
					setTimeout(function() {
						var h = 210;
						
						jQuery('#TB_ajaxContent').removeAttr('style');
						jQuery('#TB_window').removeClass('thickbox-loading').css('max-height', h);
						jQuery('#TB_closeWindowButton').css('text-decoration', 'none');
						
						jQuery('#TB_window').css("top", ((jQuery(window).height() - h) / 4));
						jQuery('#TB_window').css("margin-top", ((jQuery(window).height() - h) / 4));
					}, 5);
				});
				
				
				//remote verifier
				var lcwpau_acting = false;
				
				jQuery(document.body).delegate("#TB_window .lcwpau_ajax", "click", function() {
					var $subj = jQuery(this);
					var $wrap = jQuery(this).parents("table");
					
					if(lcwpau_acting) {return false;}
					if(!$wrap.find("input[name=lcwpau_username]").val() || !$wrap.find("input[name=lcwpau_purch_code]").val()) {
						return false;
					}
	
					lcwpau_acting = true;
					$wrap.find('.lcwpau_mess_wrap p').remove();
					$wrap.find('.lcwpau_mess_wrap').prepend('<p style="color: #d54e21; padding: 0;"><img src="<?php echo admin_url() ?>/images/spinner.gif" alt="loading.." /></p>');

					var data = {
						action		: $wrap.attr("rel"),
						username 	: $wrap.find("input[name=lcwpau_username]").val(),
						purch_code 	: $wrap.find("input[name=lcwpau_purch_code]").val(),
					};
					jQuery.post(ajaxurl, data, function(response) {
						var resp = jQuery.trim(response);
						
						if(resp == "success") {
							$wrap.find('.lcwpau_mess_wrap p').remove();
							$wrap.find("tr").not(".lcwpau_validation_ok").hide();
							$wrap.find(".lcwpau_validation_ok").show();	
						}
						else {
							$wrap.find('.lcwpau_mess_wrap p').html('<strong>'+ resp +'</strong>');
						}
						
						lcwpau_acting = false;
					})
					.error(function() { 
						$subj.css("opacity", 1);
						lcwpau_acting = false;
					
					   alert("<?php _e("Unknown error", 'lcwpau') ?> .."); 
					});	
				}); 
			  
				jQuery("body").delegate("#TB_window .lcwpau_revoke", "click", function() {
					if(lcwpau_acting) {return false;}
					var $wrap = jQuery(this).parents("table");
					
					$wrap.find("tr").not(".lcwpau_validation_ok").show();
					$wrap.find(".lcwpau_validation_ok").hide();
				}); 	
			});
			</script>
            <?php
		}
		
		$GLOBALS['lcwpau_base_wizard_js_added'] = true;
		return true;
	}
	


	
	/**
	 * template to be used in plugin settings panel to store username and purchase code
	 * @return string
	 */
	private function purch_verifier_template() {
		if($this->checked_purch_code()) {
			$to_setup_vis = 'style="display: none;"';
			$setupped_vis = '';
		} else {
			$to_setup_vis = '';
			$setupped_vis = 'style="display: none;"';
		}
		
		$code = '
		<h2 style="font-size: 20px; font-weight: normal; margin-bottom: 15px;">'. __("Automatic Updates - Purchase Validation", 'lcwpau') .' 
			<small style="padding-left: 7px; font-size: 12px;">
				<a style="color: #0073aa; text-decoration: none;" href="http://support.lcweb.it/wp-content/plugins/envaticket/img/how_get_purcode.gif" target="_blank">('. __('how to get purchase code?', 'lcwpau') .')</a>
			</small>
		</h2>
        <table class="widefat" style="margin-bottom: 35px;" rel="'. $this->ajax_action_name .'">
		  <tr '.$to_setup_vis.'>
            <td style="width: 15%; min-width: 240px;">
				<input type="text" name="lcwpau_username" placeholder="'. __('Envato username', 'lcwpau') .'" maxlength="255" autocomplete="off" style="width: 90%;" />
			</td>
			<td style="width: 15%; min-width: 300px;">
				<input type="text" name="lcwpau_purch_code" placeholder="'. __('Purchase code', 'lcwpau') .'" maxlength="36" autocomplete="off" style="width: 90%;" />
			</td>
			<td>
				<input type="button" value="'. __('Validate', 'lcwpau') .'" class="button-primary lcwpau_ajax" />
			</td>
		  </tr>
		  <tr '.$to_setup_vis.'>	
            <td colspan="3" class="lcwpau_mess_wrap" style="border-top: 1px solid #e9e9e9; padding-top: 12px; padding-bottom: 12px;">
			'. __("<strong style='color: #d54e21;'>IMPORTANT</strong>: purchase code can be used only for ONE domain. Use it <strong>only</strong> in final production website!", 'lcwpau') .'
			</td>
          </tr>
		  
		  <tr class="lcwpau_validation_ok" '.$setupped_vis.'>
		  	<td colspan="3" style="vertical-align: middle">
				<p style="position: relative; top: 4px;">'. __("Purchase successfully validated!", 'lcwpau') .'</p>
				<input type="button" value="'. __("Use new credentials", 'lcwpau') .'" class="button-primary lcwpau_revoke" style="margin: 7px 0 10px;" />
			</td>
		  </tr>
		 </table>';
		 
		 return $code;
	} 
	 
	 
	/**
	 * purchase data check - ajax operation 
	 * @return string
	 */
	public function purch_verifier_ajax() {
		error_reporting(E_ALL); // debug
		
		if(!isset($_POST['username']) || empty($_POST['username'])) {
			die( __('Username missing', 'lcwpau') );	
		}
		if(!isset($_POST['purch_code']) || strlen((string)$_POST['purch_code']) != 36) {
			die( __('Purchase code missing or incorrect', 'lcwpau') );	
		}
		
		$response = $this->check_purchase($_POST['username'], $_POST['purch_code']);
		
		echo ($response === true) ? 'success' : $response;
		die();
	}
	


	/**
	 * purchase code checked? 
	 * load envato username and purchase code in properties and check if lcwpau_valid_purchase flag is ok
	 * could be used also to setup $verified_purch and $purch_data
	 *
	 * @return bool
	 */
	public function checked_purch_code() {
		$this->verified_purch = get_option('lcwpau_valid_purchase');
		$this->purch_data = get_option('lcwpau_purch_data');
		
		// no options - setup them
		if(!is_array($this->verified_purch) || !is_array($this->purch_data)) {
			$this->verified_purch = array();
			update_option('lcwpau_valid_purchase', $this->verified_purch);	
			
			$this->purch_data = array();
			update_option('lcwpau_purch_data', $this->purch_data);	
			return false;	
		}
		
		if(isset($this->verified_purch[$this->plugin_slug]) && $this->verified_purch[$this->plugin_slug] == $this->site_url && isset($this->purch_data[$this->plugin_slug])) {
			return true;
		}
		return false;
	}
	


	/**
	 * Check purchase code
	 *
	 * @param string $envato_username
	 * @param string $purch_code
	 * @return bool|string - true if successful purchase or error message string
	 */
	public function check_purchase($envato_username, $purch_code) {
		$this->checked_purch_code(); // load properties
		
		if(!empty($envato_username) && !empty($purch_code)) {
			$this->purch_data[ $this->plugin_slug ] = array(
				'username' 		=> $envato_username, 
				'purch_code' 	=> $purch_code,
			);
		}
		else {return __('Empty username or purchase code', 'lcwpau');}
		
		// Get the remote version
		$params = array(
			'username' 		=> $envato_username, 
			'purch_code' 	=> $purch_code,
			'site_url'		=> $this->site_url
		);
		$response = $this->getRemote('purch_validation', $params);
		
		
		if($response == 'valid') {
			$this->verified_purch[ $this->plugin_slug ] = $this->site_url;
			update_option('lcwpau_valid_purchase', $this->verified_purch);
			
			update_option('lcwpau_purch_data', $this->purch_data);	
			return true;
		}
		
		else {
			if(isset($this->verified_purch)) {unset($this->verified_purch[ $this->plugin_slug ]);}
			unset($this->purch_data[ $this->plugin_slug ]);
			
			update_option('lcwpau_valid_purchase', $this->verified_purch);
			update_option('lcwpau_purch_data', $this->purch_data);	

			switch($response) {
				case 'item_not_found' 		: $err_mess = __('Item not found', 'lcwpau'); 
				break;
				
				case 'already_redeemed' 	: $err_mess = __('Purchase code already redeemed on another domain', 'lcwpau'); 
				break;
				
				case 'envato_server_error' 	: $err_mess = __('Error connecting to Envato servers', 'lcwpau'); 
				break;
				
				case 'invalid_data' 		: $err_mess = __('Wrong username or purchase code', 'lcwpau'); 
				break;
				
				default: $err_mess = $response;
				break;
			}
			
			return $err_mess;		
		}
	}


	
	/**********************************************************************************/

	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @return object $ response
	 */
	 public function set_plugin_info($false, $action, $response) {
        if(!$this->checked_purch_code()) {return $false;}
		if(!isset($response->slug) || $response->slug != $this->slug) {return $false;}
		
		// Get the remote version
		$remote_version = $this->getRemote('version', false, true);	

		if(is_object($remote_version) && (float)$this->current_version < (float)$remote_version->new_version /*version_compare( $this->current_version, $remote_version->new_version, '<' ) version compre fails comparing (eg.) 1.09 and 1.1 */ ) {
			$response->last_updated 	= $remote_version->last_updated;
			$response->slug 			= $this->slug;
			$response->name 			= $remote_version->name;
			$response->plugin_name 		= $this->plugin_slug;
			$response->version 			= $remote_version->new_version;
		   
			$response->sections 		= $remote_version->sections;
			$response->requires 		= $remote_version->requires;
			$response->tested 			= $remote_version->tested;
			$response->download_link 	= $remote_version->package;
			
			$this->update_avail = true;
			return $response;
		}
		
        return $false;
    }



	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 * @return object $ transient
	 */
	public function check_update($transient) {
		if(empty($transient) || !$this->checked_purch_code()) {return $transient;}

		// Get the remote version
		$remote_version = $this->getRemote('version', false, true);	

		// If a newer version is available, add the update
		if(is_object($remote_version) && (float)$this->current_version < (float)$remote_version->new_version /*version_compare( $this->current_version, $remote_version->new_version, '<' )*/ ) {
			$obj = new stdClass();
			$obj->name 			= $remote_version->name;
			$obj->slug 			= $this->slug;
			$obj->new_version 	= $remote_version->new_version;
			$obj->url 			= $remote_version->url;
			$obj->plugin 		= $this->plugin_slug;
			$obj->requires 		= $remote_version->requires;
			$obj->tested 		= $remote_version->tested;
			$obj->package 		= $remote_version->package;
			
			$this->update_avail = true;
			$transient->response[$this->plugin_slug] = $obj;
		}
		return $transient;
	}



	/**
	 * Add our self-hosted description to the filter
	 *
	 * @param boolean $false
	 * @param array $action
	 * @param object $arg
	 * @return bool|object
	 */
	public function check_info($false, $action, $arg) {
		if(!$this->checked_purch_code()) {return false;}
		
		if (isset($arg->slug) && $arg->slug === $this->slug) {
			$obj = $this->getRemote('info', false, true);
			$obj->slug = $this->slug;
			return $obj;
		}
		
		return false;
	}



	/**
	 * Return the remote version
	 * 
	 * @param string $action
	 * @param array $add_params (additional parameters)
	 * @param bool $auto_params (whether to add purchase data and domain automatically from class properties)
	 *
	 * @return string $remote_version
	 */
	public function getRemote($action = '', $add_params = array(), $auto_params = false) {
		$params = array(
			'body' => array(
				'action'	=> $action,
				'sign' 		=> $this->signature,
				'subj'		=> $this->plugin_slug
			),
		);
		
		
		// if is asking for download link - add purchasing details
		if(!empty($add_params) && is_array($add_params)) {
			foreach($add_params as $k => $v) {
				$params['body'][$k] = $v;	
			}
		}
		
		// auto parameters loading
		if($auto_params) {
			$params['body']['username']		= $this->purch_data[$this->plugin_slug]['username'];
			$params['body']['purch_code']	= $this->purch_data[$this->plugin_slug]['purch_code'];
			$params['body']['site_url']		= $this->site_url;
		}
		
		
		// Make the POST request
		$request = wp_remote_post($this->update_endpoint, $params);
		
		// Check if response is valid
		if(!is_wp_error($request) && wp_remote_retrieve_response_code($request) == 200) {
			return maybe_unserialize($request['body']);
		}
		if(!is_wp_error($request) && wp_remote_retrieve_response_code($request) == 400) {
			return $request['body'];
		}
		else {
			//var_dump($request); // debug	
		}
		return false;
	}
}

endif;