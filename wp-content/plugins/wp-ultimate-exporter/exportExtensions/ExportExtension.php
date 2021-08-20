<?php
/**
 * WP Ultimate Exporter plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

namespace Smackcoders\SMEXP;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class ExportExtension {

	public $response = array();
	public  $headers = array();
	public  $module;	
	public  $exportType = 'csv';
	public $optionalType = null;	
	public $conditions = array();	
	public $eventExclusions = array();
	public $fileName;	
	public $data = array();	
	public $heading = true;	
	public $delimiter = ',';
	public $enclosure = '"';
	public $auto_preferred = ",;\t.:|";
	public $output_delimiter = ',';
	public $linefeed = "\r\n";
	public $export_mode;
	public $export_log = array();
	public $limit;
	protected static $instance = null,$mapping_instance,$export_handler,$post_export,$woocom_export,$review_export,$ecom_export;
	protected $plugin,$activateCrm,$crmFunctionInstance;
	public $plugisnScreenHookSuffix=null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			ExportExtension::$export_handler = ExportHandler::getInstance();
			ExportExtension::$post_export = PostExport::getInstance();
			ExportExtension::$woocom_export = WooCommerceExport::getInstance();
			ExportExtension::$review_export = CustomerReviewExport::getInstance();
			self::$instance->doHooks();
		}
		return self::$instance;
	}	

	public  function doHooks(){
		$plugin_pages = ['com.smackcoders.csvimporternew.menu'];
		require_once WP_PLUGIN_DIR . '/wp-ultimate-exporter/wp-exp-hooks.php';
		global $plugin_ajax_hooks;

		$request_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
		$request_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		if (in_array($request_page, $plugin_pages) || in_array($request_action, $plugin_ajax_hooks)) {
			add_action('wp_ajax_parse_data',array($this,'parseData'));
			add_action('wp_ajax_total_records', array($this, 'totalRecords'));
		}
	}


	public function totalRecords(){
		global $wpdb;
		//$module = 'WooCommerce';
		$module = sanitize_text_field($_POST['module']);
		//$_POST['optionalType'] = '';
	//	$optionalType = sanitize_text_field($_POST['optionalType']);
		$optionalType = isset($_POST['optionalType'])?sanitize_text_field($_POST['optionalType']):'';
		if ($module == 'WooCommerceOrders') {
			$module = 'shop_order';
		}
		elseif ($module == 'WooCommerceCoupons') {
			$module = 'shop_coupon';
		}
		elseif ($module == 'WooCommerceRefunds') {
			$module = 'shop_order_refund';
		}
		elseif ($module == 'WooCommerceVariations') {
			$module = 'product_variation';
		}
		elseif($module == 'WPeCommerceCoupons'){
			$module = 'wpsc-coupon';
		}
		elseif($module == 'Users'){
			$get_available_user_ids = "select DISTINCT ID from $wpdb->users u join $wpdb->usermeta um on um.user_id = u.ID";
			$availableUsers = $wpdb->get_col($get_available_user_ids);
			$total = count($availableUsers);
			return $total;
		}
		elseif($module == 'Tags'){
			$get_all_terms = get_tags('hide_empty=0');
			return count($get_all_terms);
			wp_die();
		}
		elseif($module == 'Categories'){
			$get_all_terms = get_categories('hide_empty=0');
			return count($get_all_terms);
			wp_die();
		}
		elseif($module == 'CustomPosts' && $optionalType == 'nav_menu_item'){
			$get_menu_ids = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}terms AS t LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'nav_menu' ", ARRAY_A);
			echo wp_json_encode(count($get_menu_ids));
			wp_die();
		}
		elseif($module == 'CustomPosts' && $optionalType == 'widgets'){
			echo wp_json_encode(1);
			wp_die();
		}
		else {
			$optional_type = NULL;
			if($module == 'CustomPosts') {
				$optional_type = $optionalType;
			}
			$module = ExportExtension::$post_export->import_post_types($module,$optional_type);
		}

		$get_post_ids = "select DISTINCT ID from $wpdb->posts";
		$get_post_ids .= " where post_type = '$module'";

		/**
		 * Check for specific status
		 */
		if($module == 'shop_order'){
			$get_post_ids .= " and post_status in ('wc-completed','wc-cancelled','wc-on-hold','wc-processing','wc-pending')";

		}elseif ($module == 'shop_coupon') {
			$get_post_ids .= " and post_status in ('publish','draft','pending')";

		}elseif ($module == 'shop_order_refund') {

		}elseif ($module == 'forum') {
			$get_post_ids .= " and post_status in ('publish','draft','future','private','pending','hidden')";
		}
		elseif ($module == 'topic') {
			$get_post_ids .= " and post_status in ('publish','draft','future','open','pending','closed','spam')";
		}
		elseif ($module == 'reply') {
			$get_post_ids .= " and post_status in ('publish','spam','pending')";
		}
		$get_post_ids .= " and post_status in ('publish','draft','future','private','pending')";
		$get_total_row_count = $wpdb->get_col($get_post_ids);
		$total = count($get_total_row_count);
		return $total;
	}

	/**
	 * ExportExtension constructor.
	 * Set values into global variables based on post value
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	public  function parseData(){

		if(!empty($_POST)) {

			$this->module          = sanitize_text_field($_POST['module']);
			$this->exportType      = isset( $_POST['exp_type'] ) ? sanitize_text_field( $_POST['exp_type'] ) : 'csv';
			$conditions =  str_replace("\\" , '' , sanitize_text_field($_POST['conditions']));
			$conditions = json_decode($conditions, True);
			$conditions['specific_period']['to'] = date("Y-m-d", strtotime($conditions['specific_period']['to']) );
			$conditions['specific_period']['from'] = date("Y-m-d", strtotime($conditions['specific_period']['from']) );
			$this->conditions      = isset( $conditions ) && ! empty( $conditions ) ? $conditions : array();
			if($this->module == 'Taxonomies' || $this->module == 'CustomPosts' ){
				$this->optionalType    = sanitize_text_field($_POST['optionalType']);
			}
			else{
				$this->optionalType    = $this->getOptionalType($this->module);
			}
			$eventExclusions = str_replace("\\" , '' , sanitize_text_field(isset($_POST['eventExclusions']) ? sanitize_text_field($_POST['eventExclusions']) :''));
			$eventExclusions = json_decode($eventExclusions, True);
			$this->eventExclusions = isset( $eventExclusions ) && ! empty( $eventExclusions ) ? $eventExclusions : array();
			$this->fileName        = isset( $_POST['fileName'] ) ? sanitize_text_field( $_POST['fileName'] ) : '';
			if(empty($_POST['offset'] )  || sanitize_text_field($_POST['offset']) == 'undefined'){
				$this->offset = 0 ;
			}
			else{
				$this->offset          = isset( $_POST['offset'] ) ? sanitize_text_field( $_POST['offset'] ) : 0;
			}
			if(!empty($_POST['limit'] )){
				$this->limit           = isset( $_POST['limit'] ) ? sanitize_text_field( $_POST['limit'] ) : 1000;
			}
			else{
				$this->limit           = 50;
			}
			if(!empty($this->conditions['delimiter']['optional_delimiter'])){
				$this->delimiter = $this->conditions['delimiter']['optional_delimiter'] ? $this->conditions['delimiter']['optional_delimiter']: ',';
			}
			elseif(!empty($this->conditions['delimiter']['delimiter'])){
				$this->delimiter = $this->conditions['delimiter']['delimiter'] ? $this->conditions['delimiter']['delimiter'] : ',';
				if($this->delimiter == '{Tab}'){
					$this->delimiter = " ";
				}
				elseif($this->delimiter == '{Space}'){
					$this->delimiter = " ";	
				}
			}

			$this->export_mode = 'normal';
			$this->checkSplit = isset( $_POST['is_check_split'] ) ? sanitize_text_field( $_POST['is_check_split'] ) : 'false';
			$this->exportData();
		}
	}

	public function commentsCount($mode = null) {
		global $wpdb;
		self::generateHeaders($this->module, $this->optionalType);
		$get_comments = "select * from {$wpdb->prefix}comments";
		// Check status
		if($this->conditions['specific_status']['is_check'] == 'true') {
			if($this->conditions['specific_status']['status'] == 'Pending')
				$get_comments .= " where comment_approved = '0'";
			elseif($this->conditions['specific_status']['status'] == 'Approved')
				$get_comments .= " where comment_approved = '1'";
			else
				$get_comments .= " where comment_approved in ('0','1')";
		}
		else
			$get_comments .= " where comment_approved in ('0','1')";
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			if($this->conditions['specific_period']['from'] == $this->conditions['specific_period']['to']){
				$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "'";
			}else{
				$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "' and comment_date <= '" . $this->conditions['specific_period']['to'] . "'";
			}
		}
		// Check for specific authors
		if($this->conditions['specific_authors']['is_check'] == '1') {
			if(isset($this->conditions['specific_authors']['author'])) {
				$get_comments .= " and comment_author_email = '".$this->conditions['specific_authors']['author']."'"; 
			}
		}
		$get_comments .= " order by comment_ID";
		$comments = $wpdb->get_results( $get_comments );
		$totalRowCount = count($comments);
		return $totalRowCount;
	}

	public function getOptionalType($module){
		if($module == 'Tags'){
			$optionalType = 'post_tag';
		}
		elseif($module == 'Posts'){
			$optionalType = 'posts';
		}
		elseif($module == 'Pages'){
			$optionalType = 'pages';
		} 
		elseif($module == 'Categories'){
			$optionalType = 'category';
		} 
		elseif($module == 'Users'){
			$optionalType = 'users';
		}
		elseif($module == 'Comments'){
			$optionalType = 'comments';
		}
		elseif($module == 'CustomerReviews'){
			$optionalType = 'wpcr3_review';
		}
		elseif($module == 'WooCommerce' || $module == 'WooCommerceOrders' || $module == 'WooCommerceCoupons' || $module == 'WooCommerceRefunds' || $module == 'WooCommerceVariations' || $module == 'Marketpress' ){
			$optionalType = 'product';
		}
		elseif($module == 'WooCommerce'){
			$optionalType = 'product';
		}
		elseif($module == 'WPeCommerce'){
			$optionalType = 'wpsc-product';
		}
		elseif($module == 'WPeCommerce' ||$module == 'WPeCommerceCoupons'){
			$optionalType = 'wpsc-product';
		}
		return $optionalType;
	}

	/**
	 * set the delimiter
	 */
	public function setDelimiter($conditions)
	{		
		if (isset($conditions['optional_delimiter']) && $conditions['optional_delimiter'] != '') {
			return $conditions['optional_delimiter'];
		}
		elseif(isset($conditions['delimiter']) && $conditions['delimiter'] != 'Select'){
			if($conditions['delimiter'] == '{Tab}')
				return "\t";
			elseif ($conditions['delimiter'] == '{Space}')
				return " ";
			else
				return $conditions['delimiter'];
		}
		else{
			return ',';
		}
	}

	/**
	 * Export records based on the requested module
	 */
	public function exportData( ) {
		switch ($this->module) {
		case 'Posts':
		case 'Pages':
		case 'CustomPosts':
		case 'WooCommerce':
		case 'Marketpress':
		case 'WooCommerceVariations':
		case 'WooCommerceOrders':
		case 'WooCommerceCoupons':
		case 'WooCommerceRefunds':
		case 'WPeCommerce':
		case 'WPeCommerceCoupons':
		case 'eShop':
			self::FetchDataByPostTypes();
			break;
		case 'Users':
			self::FetchUsers();
			break;
		case 'Comments':
			self::FetchComments();
			break;
		case 'CustomerReviews':
			ExportExtension::$review_export->FetchCustomerReviews($this->module,$this->mode, $this->optionalType, $this->conditions,$this->offset,$this->limit);
			break;
		case 'Categories':
			ExportExtension::$post_export->FetchCategories($this->mode,$this->module,$this->optionalType);
			break;
		case 'Tags':
			ExportExtension::$post_export->FetchTags($this->mode,$this->module,$this->optionalType);
		case 'Taxonomies':
			ExportExtension::$woocom_export->FetchTaxonomies($this->mode,$this->module,$this->optionalType);
			break;

		}
	}

	/**
	 * Fetch users and their meta information
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchUsers($mode = null) {
		global $wpdb;
		self::generateHeaders($this->module, $this->optionalType);
		$get_available_user_ids = "select DISTINCT ID from {$wpdb->prefix}users u join {$wpdb->prefix}usermeta um on um.user_id = u.ID";
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			if($this->conditions['specific_period']['from'] == $this->conditions['specific_period']['to']){
				$get_available_user_ids .= " where u.user_registered >= '" . $this->conditions['specific_period']['from'] . "'";
			}else{
				$get_available_user_ids .= " where u.user_registered >= '" . $this->conditions['specific_period']['from'] . "' and u.user_registered <= '" . $this->conditions['specific_period']['to'] . "'";
			}
		}
		$availableUsers = $wpdb->get_col($get_available_user_ids);
		$this->totalRowCount = count($availableUsers);
		$get_available_user_ids .= " order by ID asc limit $this->offset, $this->limit";
		$availableUserss = $wpdb->get_col($get_available_user_ids);
		if(!empty($availableUserss)) {
			$whereCondition = '';
			foreach($availableUserss as $userId) {
				if($whereCondition != ''){
					$whereCondition = $whereCondition . ',' . $userId;
				}else{
					$whereCondition = $userId;
				}
				// Prepare the user details to be export
				$query_to_fetch_users = "SELECT * FROM {$wpdb->prefix}users where ID in ($whereCondition);";
				$users = $wpdb->get_results($query_to_fetch_users);
				if(!empty($users)) {
					foreach($users as $userInfo) {
						foreach($userInfo as $userKey => $userVal) {
							$this->data[$userId][$userKey] = $userVal;
						}
					}
				}
				// Prepare the user meta details to be export
				$query_to_fetch_users_meta = $wpdb->prepare("SELECT user_id, meta_key, meta_value FROM  {$wpdb->prefix}users wp JOIN {$wpdb->prefix}usermeta wpm  ON wpm.user_id = wp.ID where ID= %d", $userId);
				$userMeta = $wpdb->get_results($query_to_fetch_users_meta);

				$wptypesfields = get_option('wpcf-usermeta');
				$wptypesfields = get_option('wpcf-usermeta');

				if(!empty($wptypesfields)){
					$i = 1;
					foreach ($wptypesfields as $key => $value) {
						$typesf[$i] = 'wpcf-'.$key;
						$typeOftypesField[$typesf[$i]] = $value['type']; 
						$i++;
					}
				}
				if(!empty($userMeta)) {
					foreach($userMeta as $userMetaInfo) {
						if($userMetaInfo->meta_key == 'wp_capabilities') {
							$userRole = $this->getUserRole($userMetaInfo->meta_value);
							$this->data[ $userId ][ 'role' ] = $userRole;
						}
						elseif($userMetaInfo->meta_key == 'description') {
							$this->data[ $userId ][ 'biographical_info' ] = $userMetaInfo->meta_value;
						}
						elseif($userMetaInfo->meta_key == 'comment_shortcuts') {
							$this->data[ $userId ][ 'enable_keyboard_shortcuts' ] = $userMetaInfo->meta_value;
						}
						elseif($userMetaInfo->meta_key == 'show_admin_bar_front') {
							$this->data[ $userId ][ 'show_toolbar' ] = $userMetaInfo->meta_value;
						}
						elseif($userMetaInfo->meta_key == 'rich_editing') {
							$this->data[ $userId ][ 'disable_visual_editor' ] = $userMetaInfo->meta_value;
						}
						elseif($userMetaInfo->meta_key == 'locale') {
							$this->data[ $userId ][ 'language' ] = $userMetaInfo->meta_value;
						}
						elseif (isset($typesf) && in_array($userMetaInfo->meta_key, $typesf)) {
							$typeoftype = $typeOftypesField[$userMetaInfo->meta_key];
							if(is_serialized($userMetaInfo->meta_value)){
								$typefileds = unserialize($userMetaInfo->meta_value);
								$typedata = "";
								foreach ($typefileds as $key2 => $value2) {
									if(is_array($value2)){
										foreach ($value2 as $key3 => $value3) {
											$typedata .= $value3.',';
										}
									}
									else
										$typedata .= $value2.',';
								}
								if(preg_match('/wpcf-/',$userMetaInfo->meta_key)){
									$userMetaInfo->meta_key = preg_replace('/wpcf-/','', $userMetaInfo->meta_key );
									$this->data[$userId][ $userMetaInfo->meta_key ] = substr($typedata, 0, -1);
								}
							}
							elseif ($typeoftype == 'date') {
								$this->data[$userId][ $userMetaInfo->meta_key ] = date('Y-m-d', $userMetaInfo->meta_value);
							}
							$multi_row = '_'.$userMetaInfo->meta_key.'-sort-order';

							$multi_data = get_user_meta($userId,$multi_row);
							$multi_data = $multi_data[0];
							if(is_array($multi_data)){
								foreach($multi_data as $k => $mid){
									$m_data = $this->get_common_post_metadata($mid);
									if($typeoftype == 'date')
										$multi_data[$k] = date('Y-m-d H:i:s',$m_data['meta_value']);
									else
										$multi_data[$k] = $m_data['meta_value'];			                                      				       }
								$this->data[$userId][ $userMetaInfo->meta_key ] = implode('|',$multi_data);
								if(preg_match('/wpcf-/',$userMetaInfo->meta_key)){
									$userMetaInfo->meta_key = preg_replace('/wpcf-/','', $userMetaInfo->meta_key );

									$this->data[$userId][ $userMetaInfo->meta_key ] = implode('|',$multi_data);
								}
							}
							else{
								if(preg_match('/wpcf-/',$userMetaInfo->meta_key)){
									$userMetaInfo->meta_key = preg_replace('/wpcf-/','', $userMetaInfo->meta_key );
									$this->data[$userId][ $userMetaInfo->meta_key ] = $userMetaInfo->meta_value;
								}
							}
						}


						else {

							$this->data[ $userId ][ $userMetaInfo->meta_key ] = $userMetaInfo->meta_value;
						}
					}
					// Prepare the buddy meta details to be export
					if(is_plugin_active('buddypress/bp-loader.php')){
						$query_to_fetch_buddy_meta = $wpdb->prepare("SELECT user_id,field_id,value,name FROM {$wpdb->prefix}bp_xprofile_data bxd inner join {$wpdb->prefix}users wp  on bxd.user_id = wp.ID inner join {$wpdb->prefix}bp_xprofile_fields bxf on bxf.id = bxd.field_id where user_id=%d",$userId);
						$buddy = $wpdb->get_results($query_to_fetch_buddy_meta);
						if(!empty($buddy)) {
						foreach($buddy as $buddyInfo) {
							foreach($buddyInfo as $field_id => $value) {
								$this->data[$userId][$buddyInfo->name] = $buddyInfo->value;
								}
							}
						}	
					}
					ExportExtension::$post_export->getPostsMetaDataBasedOnRecordId($userId, $this->module, $this->optionalType);
				}
			}
		}

		$result = self::finalDataToExport($this->data, $this->module);
		if($mode == null)
			self::proceedExport($result);
		else
			return $result;
	}

	public function mergeWithUserMeta($acf_field_values){

		foreach($acf_field_values as $acf_field_value){

		}
	}

	/**
	 * Fetch all Comments
	 * @param $mode
	 *
	 * @return array
	 */
	public function FetchComments($mode = null) {
		global $wpdb;
		self::generateHeaders($this->module, $this->optionalType);
		$get_comments = "select * from {$wpdb->prefix}comments";
		// Check status
		if(isset($this->conditions['specific_status']['is_check']) && $this->conditions['specific_status']['is_check'] == 'true') {
			if($this->conditions['specific_status']['status'] == 'Pending')
				$get_comments .= " where comment_approved = '0'";
			elseif($this->conditions['specific_status']['status'] == 'Approved')
				$get_comments .= " where comment_approved = '1'";
			else
				$get_comments .= " where comment_approved in ('0','1')";
		}
		else
			$get_comments .= " where comment_approved in ('0','1')";
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			if($this->conditions['specific_period']['from'] == $this->conditions['specific_period']['to']){
				$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "'";
			}else{
				$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "' and comment_date <= '" . $this->conditions['specific_period']['to'] . "'";
			}
		}
		// Check for specific authors
		if($this->conditions['specific_authors']['is_check'] == '1') {
			if(isset($this->conditions['specific_authors']['author'])) {
				$get_comments .= " and comment_author_email = '".$this->conditions['specific_authors']['author']."'"; 
			}
		}
		$comments = $wpdb->get_results( $get_comments );
		$this->totalRowCount = count($comments);
		$get_comments .= " order by comment_ID asc limit $this->offset, $this->limit";
		$limited_comments = $wpdb->get_results( $get_comments );
		if(!empty($limited_comments)) {
			foreach($limited_comments as $commentInfo) {
				$user_id=$commentInfo->user_id;
				if(!empty($user_id)) {
					$users_login =  $wpdb->get_results("SELECT user_login FROM {$wpdb->prefix}users WHERE ID = '$user_id'");		
					foreach($users_login as $users_key => $users_value){
						foreach($users_value as $u_key => $u_value){
							$users_id=$u_value;
						}
					}
				}
				foreach($commentInfo as $commentKey => $commentVal) {
					$this->data[$commentInfo->comment_ID][$commentKey] = $commentVal;
					$this->data[$commentInfo->comment_ID]['user_id'] = isset($users_id) ? $users_id :'';
				}
			}
		}
		$result = self::finalDataToExport($this->data, $this->module);
		if($mode == null)
			self::proceedExport($result);
		else
			return $result;
	}

	/**
	 * Generate CSV headers
	 *
	 * @param $module       - Module to be export
	 * @param $optionalType - Exclusions
	 */
	public function generateHeaders ($module, $optionalType) {
		global $csv_class;
		if($module == 'CustomPosts' || $module == 'Taxonomies'){
			$default = $csv_class->get_fields($optionalType);
		}
		else{
			$default = $csv_class->get_fields($module);
		}

		$headers = [];
		foreach ($default as $key => $fields) {
			foreach($fields as $groupKey => $fieldArray) {

				foreach ( $fieldArray as $fKey => $fVal ) {
					if (is_array($fVal) || is_object($fVal)){
						foreach ( $fVal as $rKey => $rVal ) {
							if(!in_array($rVal['name'], $headers))
								$headers[] = $rVal['name'];
						}
					}
				}

			}
		}
		if(isset($this->eventExclusions['is_check']) && $this->eventExclusions['is_check'] == 'true') :
			$headers_with_exclusion = self::applyEventExclusion($headers);
		$this->headers = $headers_with_exclusion;
		else:
		$this->headers = $headers;
		endif;
	}

	/**
	 * Fetch data by requested Post types
	 * @param $mode
	 * @return array
	 */
	public function FetchDataByPostTypes ($mode = null) {
		if(empty($this->headers))
			$this->generateHeaders($this->module, $this->optionalType);
		$recordsToBeExport = ExportExtension::$post_export->getRecordsBasedOnPostTypes($this->module, $this->optionalType, $this->conditions,$this->offset,$this->limit,$this->headers);
		if(!empty($recordsToBeExport)) {
			foreach($recordsToBeExport as $postId) {
				$this->data[$postId] = $this->getPostsDataBasedOnRecordId($postId);
				$exp_module = $this->module; 
				if($exp_module == 'Posts' ||  $exp_module == 'CustomPosts' ||$exp_module == 'Pages'||$exp_module == 'WooCommerce'){
					$this->getPolylangData($postId,$this->optionalType,$exp_module);
				}	
				ExportExtension::$post_export->getPostsMetaDataBasedOnRecordId($postId, $this->module, $this->optionalType);
				$this->getTermsAndTaxonomies($postId, $this->module, $this->optionalType);

				if($this->module == 'WooCommerce')
					ExportExtension::$woocom_export->getProductData($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerceRefunds')
					ExportExtension::$woocom_export->getWooComCustomerUser($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerceOrders')
					ExportExtension::$woocom_export->getWooComOrderData($postId, $this->module, $this->optionalType);
				if($this->module == 'WooCommerceVariations')
					ExportExtension::$woocom_export->getProductData($postId, $this->module, $this->optionalType);
				if($this->module == 'WPeCommerce')
					ExportExtension::$ecom_export->getEcomData($postId, $this->module, $this->optionalType);
				if($this->module == 'WPeCommerceCoupons')
					ExportExtension::$ecom_export->getEcomCouponData($postId, $this->module, $this->optionalType);

				if($this->optionalType == 'lp_course')
					ExportExtension::$woocom_export->getCourseData($postId);
				if($this->optionalType == 'lp_lesson')
					ExportExtension::$woocom_export->getLessonData($postId);
				if($this->optionalType == 'lp_quiz')
					ExportExtension::$woocom_export->getQuizData($postId);
				if($this->optionalType == 'lp_question')
					ExportExtension::$woocom_export->getQuestionData($postId);
				if($this->optionalType == 'lp_order')
					ExportExtension::$woocom_export->getOrderData($postId);

				
				if($this->optionalType == 'nav_menu_item')
					ExportExtension::$woocom_export->getMenuData($postId);

				if($this->optionalType == 'widgets')
				self::$instance->getWidgetData($postId,$this->headers);				
			}
		}

		$result = self::finalDataToExport($this->data, $this->module);

		if($mode == null)
			self::proceedExport( $result );
		else
			return $result;
	}	

	public function getWidgetData($postId, $headers){

		global $wpdb;
		$get_sidebar_widgets = get_option('sidebars_widgets');

		$total_footer_arr = [];
	
		foreach($get_sidebar_widgets as $footer_key => $footer_arr){
			if($footer_key != 'wp_inactive_widgets' || $footer_key != 'array_version'){
				if( strpos($footer_key, 'sidebar') !== false ){
					$get_footer = explode('-', $footer_key);
					$footer_number = $get_footer[1];

					foreach($footer_arr as $footer_values){
						$total_footer_arr[$footer_values] = $footer_number;
					}
				}
			}
		}
		
		foreach ($headers as $key => $value){
			$get_widget_value[$value] = $wpdb->get_row("SELECT option_value FROM {$wpdb->prefix}options where option_name = '{$value}'", ARRAY_A);
			
			$header_key = explode('widget_', $value);
			
			if ($value == 'widget_recent-posts'){
				$recent_posts = unserialize($get_widget_value[$value]['option_value']); 
				$recent_post = '';
				foreach($recent_posts as $dk => $dv){
					if($dk != '_multiwidget'){
						$post_key = $header_key[1].'-'.$dk;
						$recent_post .= $dv['title'].','.$dv['number'].','.$dv['show_date'].'->'.$total_footer_arr[$post_key].'|';
					}
				}
				$recent_post = rtrim($recent_post , '|');
			}
			elseif ($value == 'widget_pages'){
				$recent_pages = unserialize($get_widget_value[$value]['option_value']); 
				$recent_page = '';
				foreach($recent_pages as $dk => $dv){
					if(isset($dv['exclude'])){
						$exclude_value = str_replace(',', '/', $dv['exclude']);
					}

					if($dk != '_multiwidget'){
						$page_key = $header_key[1].'-'.$dk;
						$recent_page .= $dv['title'].','.$dv['sortby'].','.$exclude_value.'->'.$total_footer_arr[$page_key].'|';
					}
				}
				$recent_page = rtrim($recent_page , '|');
			}
			elseif ($value == 'widget_recent-comments'){
				$recent_comments = unserialize($get_widget_value[$value]['option_value']); 
				$recent_comment = '';
				foreach($recent_comments as $dk => $dv){
					if($dk != '_multiwidget'){
						$comment_key = $header_key[1].'-'.$dk;
						$recent_comment .= $dv['title'].','.$dv['number'].'->'.$total_footer_arr[$comment_key].'|';
					}
				}
				$recent_comment = rtrim($recent_comment , '|');
			}
			elseif ($value == 'widget_archives'){
				$recent_archives = unserialize($get_widget_value[$value]['option_value']); 
				$recent_archive = '';
				foreach($recent_archives as $dk => $dv){
					if($dk != '_multiwidget'){
						$archive_key = $header_key[1].'-'.$dk;
						$recent_archive .= $dv['title'].','.$dv['count'].','.$dv['dropdown'].'->'.$total_footer_arr[$archive_key].'|';
					}
				}
				$recent_archive = rtrim($recent_archive , '|');
			}
			elseif ($value == 'widget_categories'){
				$recent_categories = unserialize($get_widget_value[$value]['option_value']); 
				$recent_category = '';
				foreach($recent_categories as $dk => $dv){
					if($dk != '_multiwidget'){
						$cat_key = $header_key[1].'-'.$dk;
						$recent_category .= $dv['title'].','.$dv['count'].','.$dv['hierarchical'].','.$dv['dropdown'].'->'.$total_footer_arr[$cat_key].'|';
					}
				}
				$recent_category = rtrim($recent_category , '|');
			}
		}
			
		$this->data[$postId]['widget_recent-posts'] = $recent_post;
		$this->data[$postId]['widget_pages'] = $recent_page;
		$this->data[$postId]['widget_recent-comments'] = $recent_comment;
		$this->data[$postId]['widget_archives'] = $recent_archive;
		$this->data[$postId]['widget_categories'] = $recent_category;
	}

	/**
	 * Function used to fetch the Terms & Taxonomies for the specific posts
	 *
	 * @param $id
	 * @param $type
	 * @param $optionalType
	 */
	public function getTermsAndTaxonomies ($id, $type, $optionalType) {
		$TermsData = array();

		if($type == 'WooCommerce' || $type == 'Marketpress' || ($type == 'CustomPosts' && $type == 'WooCommerce')) {
			$type = 'product';
			$postTags = '';
			$taxonomies = get_object_taxonomies($type);
			$get_tags = get_the_terms( $id, 'product_tag' );
			if($get_tags){
				foreach($get_tags as $tags){
					$postTags .= $tags->name . ',';
				}
			}
			$postTags = substr($postTags, 0, -1);
			$this->data[$id]['product_tag'] = $postTags;
			foreach ($taxonomies as $taxonomy) {
				$postCategory = '';
				if($taxonomy == 'product_cat' || $taxonomy == 'product_category'){
					$get_categories = get_the_terms( $id, $taxonomy );
					if($get_categories){
						$postCategory = $this->hierarchy_based_term_name($get_categories, $taxonomy) ;
						// foreach($get_categories as $category){
						// 	$postCategory .= $this->hierarchy_based_term_name($category, $taxonomy) . ',';
						// }
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id]['product_category'] = $postCategory;
				}else{
					$get_categories = get_the_terms( $id, $taxonomy );
					if($get_categories){
						$postCategory = $this->hierarchy_based_term_name($get_categories, $taxonomy) ;
						// foreach($get_categories as $category){
						// 	$postCategory .= $this->hierarchy_based_term_name($category, $taxonomy) . ',';
						// }
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id][$taxonomy] = $postCategory;
				}
			}
			if(($type == 'WooCommerce' && $type != 'CustomPosts') || $type == 'Marketpress' ) {
				$product = wc_get_product	($id);
				$pro_type = $product->get_type();
				switch ($pro_type) {
				case 'simple':
					$product_type = 1;
					break;
				case 'grouped':
					$product_type = 2;
					break;
				case 'external':
					$product_type = 3;
					break;
				case 'variable':
					$product_type = 4;
					break;
				case 'subscription':
					$product_type = 5;
					break;
				case 'variable-subscription':
					$product_type = 6;
					break;
				case 'bundle':
					$product_type = 7;
					break;
				default:
					$product_type = 1;
					break;
				}
				$this->data[$id]['product_type'] = $product_type;
			}

			//product_shipping_class
			$shipping = get_the_terms( $id, 'product_shipping_class' );
			if($shipping){
				$taxo_shipping = $shipping[0]->name;			
				$this->data[$id][ 'product_shipping_class' ] = $taxo_shipping;
			}
			//product_shipping_class
		} else if($type == 'WPeCommerce') {
			$type = 'wpsc-product';
			$postTags = $postCategory = '';
			$taxonomies = get_object_taxonomies($type);
			$get_tags = get_the_terms( $id, 'product_tag' );
			if($get_tags){
				foreach($get_tags as $tags){
					$postTags .= $tags->name.',';
				}
			}
			$postTags = substr($postTags,0,-1);
			$this->data[$id]['product_tag'] = $postTags;
			foreach ($taxonomies as $taxonomy) {
				$postCategory = '';
				if($taxonomy == 'wpsc_product_category'){
					$get_categories = wp_get_post_terms( $id, $taxonomy );
					if($get_categories){
						$postCategory = $this->hierarchy_based_term_name($get_categories, $taxonomy);
						// foreach($get_categories as $category){
						// 	$postCategory .= $this->hierarchy_based_term_name($category, $taxonomy).',';
						// }
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id]['product_category'] = $postCategory;
				}else{
					$get_categories = wp_get_post_terms( $id, $taxonomy );
					if($get_categories){
						$postCategory = $this->hierarchy_based_term_name($get_categories, $taxonomy);
						// foreach($get_categories as $category){
						// 	$postCategory .= $this->hierarchy_based_term_name($category, $taxonomy).',';
						// }
					}
					$postCategory = substr($postCategory, 0 , -1);
					$this->data[$id]['product_category'] = $postCategory;
				}
			}
		} else {
			global $wpdb;
			$postTags = $postCategory = '';
			$taxonomyId = $wpdb->get_col($wpdb->prepare("select term_taxonomy_id from {$wpdb->prefix}term_relationships where object_id = %d", $id));
			foreach($taxonomyId as $taxonomy) {
				$taxo[] = get_term($taxonomy);
			}
			if(!empty($taxonomyId)) {
				foreach($taxonomyId as $taxonomy) {
					$taxonomyType = $wpdb->get_col($wpdb->prepare("select taxonomy from {$wpdb->prefix}term_taxonomy where term_taxonomy_id = %d", $taxonomy));
					if(!empty($taxonomyType)) {
						foreach($taxonomyType as $taxanomy_name) {
							if($taxanomy_name == 'category'){
								$termName = 'post_category';
							}else{
								$termName = $taxanomy_name;
							}
							if(in_array($termName, $this->headers)) {
								$this->data[$id][$termName] = NULL;
								if($termName != 'post_tag') {

									$taxonomyData = $wpdb->get_col($wpdb->prepare("select name from {$wpdb->prefix}terms where term_id = %d",$taxonomy));
									if(!empty($taxonomyData)) {

										if(isset($TermsData[$termName])){
											$this->data[$id][$termName] = $TermsData[$termName] . ',' . $taxonomyData[0];
										}else{
											$get_exist_data = $this->data[$id][$termName];
										}

										if( $get_exist_data == '' ){
											$this->data[$id][$termName] = $taxonomyData[0];
										}else {
											$taxonomyID = $wpdb->get_col($wpdb->prepare("select term_id from {$wpdb->prefix}terms where name = %s",$taxonomyData[0]));
											$postterm = substr($this->hierarchy_based_term_name($taxo, $taxanomy_name), 0 , -1);
											$this->data[$id][$termName] = $postterm;
											//$this->data[$id][$termName] = $get_exist_data . ',' . $this->hierarchy_based_term_name(get_term($taxonomy), $taxanomy_name);
											//$this->data[$id][$taxanomy_name] = $get_exist_data . '|' . $this->hierarchy_based_term_name(get_term($taxonomy), $taxanomy_name);
										}

									}
								} else {
									if(!isset($TermsData['post_tag'])) {
										$get_tags = wp_get_post_tags($id, array('fields' => 'names'));
										foreach ($get_tags as $tags) {
											$postTags .= $tags . ',';
										}
										$postTags = substr($postTags, 0, -1);
										if( $this->data[$id][$termName] == '' ) {
											$this->data[$id][$termName] = $postTags;
										}
									}
								}
								if(!isset($TermsData['category'])){
									$get_categories = wp_get_post_categories($id, array('fields' => 'names'));
									foreach ($get_categories as $category) {
										$postCategory .= $category . ',';
									}
									$postCategory = substr($postCategory, 0, -1);
									$this->data[$id]['category'] = $postCategory;
								}

							}
							else{
								$this->data[$id][$termName] = '';
							}
						}
					}					
				}
			}
		}
	}

	/**
	 * Get user role based on the capability
	 * @param null $capability  - User capability
	 * @return int|string       - Role of the user
	 */
	public function getUserRole ($capability = null) {
		if($capability != null) {
			$getRole = unserialize($capability);
			foreach($getRole as $roleName => $roleStatus) {
				$role = $roleName;
			}
			return $role;
		} else {
			return 'subscriber';
		}
	}

	/**
	 * Get activated plugins
	 * @return mixed
	 */
	public function get_active_plugins() {
		$active_plugins = get_option('active_plugins');
		return $active_plugins;
	}

	public function array_to_xml( $data, &$xml_data ) {
		foreach( $data as $key => $value ) {
			if( is_numeric($key) ){
				$key = 'item'; //dealing with <0/>..<n/> issues
			}
			if( is_array($value) ) {
				$subnode = $xml_data->addChild($key);
				$this->array_to_xml($value, $subnode);
			} else {
				$xml_data->addChild("$key",htmlspecialchars("$value"));
			}
		}
	}
	public function getPolylangData ($id,$optional_type,$exp_module) {
		global $wpdb;
		global $sitepress;
		$terms=$wpdb->get_results("select term_taxonomy_id from $wpdb->term_relationships where object_id ='{$id}'");
		$terms_id=json_decode(json_encode($terms),true);
		foreach($terms_id as $termkey => $termvalue){
			$termids=$termvalue['term_taxonomy_id'];
			$check=$wpdb->get_var("select taxonomy from $wpdb->term_taxonomy where term_id ='{$termids}'");
			if($check == 'category'){
				$category=$wpdb->get_var("select name from $wpdb->terms where term_id ='{$termids}'");
				//$this->data[$id]['post_category'] = $category;
			}
			elseif($check =='language'){
				$language=$wpdb->get_var("select description from $wpdb->term_taxonomy where term_id ='{$termids}'");
				$lang=unserialize($language);
				$langcode=explode('_',$lang['locale']);
				$lang_code=$langcode[0];
				$this->data[$id]['language_code'] = $lang_code;
			}
			elseif($check == 'post_translations'){
				 $description=$wpdb->get_var("select description from $wpdb->term_taxonomy where term_id ='{$termids}'");
				 $desc=unserialize($description);
				 $post_id = is_array($desc)? array_values($desc): array(); 
				 $postid = min($post_id);
				 
				 $post_title=$wpdb->get_var("select post_title from $wpdb->posts where ID ='{$postid}'");
				 $this->data[$id]['translated_post_title'] = $post_title;
			}
			elseif($check == 'post_tag'){
				$tag=$wpdb->get_var("select name from $wpdb->terms where term_id ='{$termids}'");
				//$this->data[$id]['post_tag'] = $tag;
				
			}
		}
	}

	/**
	 * Export Data
	 * @param $data
	 */
	public function proceedExport ($data) {
		$upload_dir = ABSPATH .'wp-content/uploads/smack_uci_uploads/exports/';
		if(!is_dir($upload_dir)) {
			wp_mkdir_p($upload_dir);
		}
		$base_dir = wp_upload_dir();
		$upload_url = $base_dir['baseurl'].'/smack_uci_uploads/exports/';
		chmod($upload_dir, 0777);
		if($this->checkSplit == 'true'){
			$i = 1;
			while ( $i != 0) {
				$file = $upload_dir . $this->fileName .'_'.$i.'.' . $this->exportType;
				if(file_exists($file)){
					$allfiles[$i] = $file;
					$i++;
				}
				else
					break;
			}
			$fileURL = $upload_url . $this->fileName.'_'.$i.'.' .$this->exportType;
		}
		else{
			$file = $upload_dir . $this->fileName .'.' . $this->exportType;
			$fileURL = $upload_url . $this->fileName.'.' .$this->exportType;
		}


		$spsize = 100;
		if ($this->offset == 0) {
			if(file_exists($file))
				unlink($file);
		}

		$checkRun = "no";
		if($this->checkSplit == 'true' && ($this->totalRowCount - $this->offset) > 0){
			$checkRun = 'yes';
		}
		if($this->checkSplit != 'true'){
			$checkRun = 'yes';
		}

		if($checkRun == 'yes'){
			if($this->exportType == 'xml'){
				$xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
				$this->array_to_xml($data,$xml_data);
				$result = $xml_data->asXML($file);
			}else{
				if($this->exportType == 'json')
					$csvData = json_encode($data);
				else
					$csvData = $this->unParse($data, $this->headers);
				try {

					file_put_contents( $file, $csvData, FILE_APPEND | LOCK_EX );
					//$this->splitCSV($file, $ex, $spsize, $this->exportType);
			} catch (\Exception $e) {
				// TODO - write exception in log
			}
			}
			}


			$this->offset = $this->offset + $this->limit;

			$filePath = $upload_dir . $this->fileName . '.' . $this->exportType;
			$filename = $fileURL;
			if(($this->offset) > ($this->totalRowCount) && $this->checkSplit == 'true'){
				$allfiles[$i] = $file;
				$zipname = $upload_dir . $this->fileName .'.' . 'zip';
				$zip = new \ZipArchive;
				$zip->open($zipname, \ZipArchive::CREATE);
				foreach ($allfiles as $allfile) {
					$newname = str_replace($upload_dir, '', $allfile);
					$zip->addFile($allfile, $newname);
			}
			$zip->close();
			$fileURL = $upload_url . $this->fileName.'.'.'zip';
			foreach ($allfiles as $removefile) {
				unlink($removefile);
			}
			$filename = $upload_url . $this->fileName.'.'.'zip';
			}
			if($this->checkSplit == 'true' && !($this->offset) > ($this->totalRowCount)){
				$responseTojQuery = array('success' => false, 'new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $zipname, 'exported_path' => $zipname,'export_type'=>$this->exportType);
			}
			elseif($this->checkSplit == 'true' && (($this->offset) > ($this->totalRowCount))){
				$responseTojQuery = array('success' => true, 'new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $fileURL, 'exported_path' => $fileURL,'export_type'=>$this->exportType);
			}
			elseif(!(($this->offset) > ($this->totalRowCount))){
				$responseTojQuery = array('success' => false, 'new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $filename, 'exported_path' => $filePath,'export_type'=>$this->exportType);
			}
			else{
				$responseTojQuery = array('success' => true, 'new_offset' => $this->offset, 'limit' => $this->limit, 'total_row_count' => $this->totalRowCount, 'exported_file' => $filename, 'exported_path' => $filePath,'export_type'=>$this->exportType);
			}

			if($this->export_mode == 'normal'){
				echo wp_json_encode($responseTojQuery);
				wp_die();
			}
			elseif($this->export_mode == 'FTP'){
				$this->export_log = $responseTojQuery;
			}
			}

			/**
			 * Fetch ACF field information to be export
			 * @param $recordId - Id of the Post (or) Page (or) Product (or) User
			 */
			public function FetchACFData ($recordId) {

			}

			/**
			 * Get post data based on the record id
			 * @param $id       - Id of the records
			 * @return array    - Data based on the requested id.
			 */
			public function getPostsDataBasedOnRecordId ($id) {
				global $wpdb;
				$PostData = array();
				$query1 = $wpdb->prepare("SELECT wp.* FROM {$wpdb->prefix}posts wp where ID=%d", $id);
				$result_query1 = $wpdb->get_results($query1);
				if (!empty($result_query1)) {
					foreach ($result_query1 as $posts) {
						foreach ($posts as $post_key => $post_value) {
							if ($post_key == 'post_status') {
								if (is_sticky($id)) {
									$PostData[$post_key] = 'Sticky';
									$post_status = 'Sticky';
								} else {
									$PostData[$post_key] = $post_value;
									$post_status = $post_value;
								}
							} else {
								$PostData[$post_key] = $post_value;
							}
							if ($post_key == 'post_password') {
								if ($post_value) {
									$PostData['post_status'] = "{" . $post_value . "}";
								} else {
									$PostData['post_status'] = $post_status;
								}
							}

							if($post_key == 'post_author'){
								$user_info = get_userdata($post_value);
								$PostData['post_author'] = $user_info->user_login;
							}
						}
					}
				}

				return $PostData;
			}

			public function getAttachment($id)
			{
				global $wpdb;
				$get_attachment = $wpdb->prepare("select guid from {$wpdb->prefix}posts where ID = %d AND post_type = %s", $id, 'attachment');
				$attachment = $wpdb->get_results($get_attachment);
				$attachment_file = $attachment[0]->guid;
				return $attachment_file;

			}

			public function getRepeater($parent)
			{
				global $wpdb;
				$get_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}posts where post_parent = %d", $parent), ARRAY_A);
				$i = 0;
				foreach ($get_fields as $key => $value) {
					$array[$i] = $value['post_excerpt'];
					$i++;
				}
				return $array;	
			}

			/**
			 * Get types fields
			 * @return array    - Types fields
			 */
			public function getTypesFields() {
				$getWPTypesFields = get_option('wpcf-fields');
				$typesFields = array();
				if(!empty($getWPTypesFields) && is_array($getWPTypesFields)) {
					foreach($getWPTypesFields as $fKey){
						$typesFields[$fKey['meta_key']] = $fKey['name'];
					}
				}
				return $typesFields;
			}

			/**
			 * Final data to be export
			 * @param $data     - Data to be export based on the requested information
			 * @return array    - Final data to be export
			 */
			public function finalDataToExport ($data, $module = false) {
				$result = array();
				foreach ($this->headers as $key => $value) {
					if(empty($value)){
						unset($this->headers[$key]);
					}
				}

				// Fetch Category Custom Field Values
				if($module){
					if($module == 'Categories'){
						return $this->fetchCategoryFieldValue($data, $this->module);
					}
				}

				foreach ( $data as $recordId => $rowValue ) {
					foreach ($this->headers as $hKey) {
						if(array_key_exists($hKey, $rowValue) && (!empty($rowValue[$hKey])) ){
							$result[$recordId][$hKey] = $this->returnMetaValueAsCustomerInput($rowValue[$hKey], $hKey);
						}
						else{
							$key = $hKey;
							$rowValue['post_type'] = '';
							// Replace the third party plugin name from the fieldname
							$key = $this->replace_prefix_aioseop_from_fieldname($key);
							$key = $this->replace_prefix_yoast_wpseo_from_fieldname($key);
							$key = $this->replace_prefix_wpcf_from_fieldname($key);
							$key = $this->replace_prefix_wpsc_from_fieldname($key);
							$key = $this->replace_underscore_from_fieldname($key);
							$key = $this->replace_wpcr3_from_fieldname($key);
							// Change fieldname depends on the post type
							$key = $this->change_fieldname_depends_on_post_type($rowValue['post_type'], $key);

							if(isset($rowValue['wpcr3_'.$key])){
								$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue['wpcr3_'.$key], $hKey);
							}else{
								if(isset($rowValue['_yoast_wpseo_'.$key])){ // Is available in yoast plugin
									$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue['_yoast_wpseo_'.$key]);
								}
								else if(isset($rowValue['_aioseop_'.$key])){ // Is available in all seo plugin
									$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue['_aioseop_'.$key]);
								}
								else if(isset($rowValue['_'.$key])){ // Is wp custom fields
									$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue['_'.$key], $hKey);
								}
								else if($aioseo_field_value= $this->getaioseoFieldValue($rowValue['ID'])){
									$rowValue['og_title'] = $aioseo_field_value[0]->og_title;
									$rowValue['og_description']= $aioseo_field_value[0]->og_description;
									$rowValue['custom_link'] = $aioseo_field_value[0]->canonical_url;
									$rowValue['og_image_type'] = $aioseo_field_value[0]->og_image_type;
									$rowValue['og_image_custom_fields'] = $aioseo_field_value[0]->og_image_custom_fields;
									$rowValue['og_video'] = $aioseo_field_value[0]->og_video;
									$rowValue['og_object_type'] = $aioseo_field_value[0]->og_object_type;
									$value=$aioseo_field_value[0]->og_article_tags;
									$article_tags = json_decode($value);
									$og_article_tags=$article_tags[0]->value;
									$rowValue['og_article_tags'] = $og_article_tags;
									$rowValue['og_article_section'] = $aioseo_field_value[0]->og_article_section;
									$rowValue['twitter_use_og'] = $aioseo_field_value[0]->twitter_use_og;
									$rowValue['twitter_card'] = $aioseo_field_value[0]->twitter_card;
									$rowValue['twitter_image_type'] = $aioseo_field_value[0]->twitter_image_type;
									$rowValue['twitter_image_custom_fields'] = $aioseo_field_value[0]->twitter_image_custom_fields;
									$rowValue['twitter_title'] = $aioseo_field_value[0]->twitter_title;
									$rowValue['twitter_description'] = $aioseo_field_value[0]->twitter_description;
									$rowValue['robots_default'] = $aioseo_field_value[0]->robots_default;
									// $rowValue['robots_noindex'] = $aioseo_field_value[0]->robots_noindex;
									$rowValue['robots_noarchive'] = $aioseo_field_value[0]->robots_noarchive;
									$rowValue['robots_nosnippet'] = $aioseo_field_value[0]->robots_nosnippet;
									// $rowValue['robots_nofollow'] = $aioseo_field_value[0]->robots_nofollow;
									$rowValue['robots_noimageindex'] = $aioseo_field_value[0]->robots_noimageindex;
									$rowValue['noodp'] = $aioseo_field_value[0]->robots_noodp;
									$rowValue['robots_notranslate'] = $aioseo_field_value[0]->robots_notranslate;
									$rowValue['robots_max_snippet'] = $aioseo_field_value[0]->robots_max_snippet;
									$rowValue['robots_max_videopreview'] = $aioseo_field_value[0]->robots_max_videopreview;
									$rowValue['robots_max_imagepreview'] = $aioseo_field_value[0]->robots_max_imagepreview;
									$rowValue['aioseo_title'] = $aioseo_field_value[0]->title;
									$rowValue['aioseo_description'] = $aioseo_field_value[0]->description;
									$key=$aioseo_field_value[0]->keyphrases;
									
									$key1=json_decode($key);
									$rowValue['keyphrases'] = $key1->focus->keyphrase;									
								}
								else{
									$rowValue[$key] = isset($rowValue[$key]) ? $rowValue[$key] :'';
									$rowValue[$key] = $this->returnMetaValueAsCustomerInput($rowValue[$key], $hKey);
								}
							}
							global  $wpdb;
							//Added for user export
							if($key =='user_login')
							{
								$wpsc_query = $wpdb->prepare("select ID from {$wpdb->prefix}users where user_login =%s", $rowValue['user_login']);
								$wpsc_meta = $wpdb->get_results($wpsc_query,ARRAY_A);
							}
							if(isset($rowValue['_bbp_forum_type']) && ($rowValue['_bbp_forum_type'] =='forum'||$rowValue['_bbp_forum_type']=='category' )){
								if($key =='Visibility'){
									$rowValue[$key]=$rowValue['post_status'];
								}
								if($key =='bbp_moderators') {
									$get_forum_moderator_ids = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = $recordId AND meta_key = '_bbp_moderator_id' ", ARRAY_A);
									$forum_moderators = '';
									foreach($get_forum_moderator_ids as $get_moderator_id){
										$forum_user_meta = get_user_by('id', $get_moderator_id['meta_value']);
										$forum_user = $forum_user_meta->data->user_login;
										$forum_moderators .= $forum_user. ',';
									}

									$rowValue[$key] = rtrim($forum_moderators, ',');
								}

							}
							if($key =='topic_status' ||$key =='author' ||$key =='topic_type' ){
								$rowValue['topic_status']=$rowValue['post_status'];
								$rowValue['author']=$rowValue['post_author'];
								if($key =='topic_type'){
									$Topictype =get_post_meta($rowValue['_bbp_forum_id'],'_bbp_sticky_topics');
									$topic_types = get_option('_bbp_super_sticky_topics');
									$rowValue['topic_type']='normal';
									if($Topictype){
										foreach($Topictype as $t_type){
											if($t_type['0']== $recordId){
												$rowValue['topic_type']='sticky';
											}
										}
									}elseif(!empty($topic_types)){
										foreach($topic_types as $top_type){
											if($top_type == $rowValue['ID']){
												$rowValue['topic_type']='super sticky';
											}
										}
									}
								}	
							}if($key =='reply_status'||$key =='reply_author'){
							$rowValue['reply_status']=$rowValue['post_status'];
							$rowValue['reply_author']=$rowValue['post_author'];
								}
							if(array_key_exists($hKey, $rowValue)){
								$result[$recordId][$hKey] = $rowValue[$hKey];
							}else{
								$result[$recordId][$hKey] = '';
							}
						}	
					}		
				}
				return $result;
			}

			function get_common_post_metadata($meta_id){
				global $wpdb;
				$mdata = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}usermeta WHERE umeta_id = %d", $meta_id) ,ARRAY_A);
				return $mdata[0];
			}

			function get_common_unserialize($serialize_data){
				return unserialize($serialize_data);
			}

			/**
			 * Create CSV data from array
			 * @param array $data       2D array with data
			 * @param array $fields     field names
			 * @param bool $append      if true, field names will not be output
			 * @param bool $is_php      if a php die() call should be put on the first
			 *                          line of the file, this is later ignored when read.
			 * @param null $delimiter   field delimiter to use
			 * @return string           CSV data (text string)
			 */
			public function unParse ( $data = array(), $fields = array(), $append = false , $is_php = false, $delimiter = null) {
				if ( !is_array($data) || empty($data) ) $data = &$this->data;
				if ( !is_array($fields) || empty($fields) ) $fields = &$this->titles;
				if ( $delimiter === null ) $delimiter = $this->delimiter;

				$string = ( $is_php ) ? "<?php header('Status: 403'); die(' '); ?>".$this->linefeed : '' ;
				$entry = array();

				// create heading
				if ($this->offset == 0 || $this->checkSplit == 'true') {
					if ( $this->heading && !$append && !empty($fields) ) {
						foreach( $fields as $key => $value ) {
							$entry[] = $this->_enclose_value($value);
			}
			$string .= implode($delimiter, $entry).$this->linefeed;
			$entry = array();
			}
			}

			// create data
			foreach( $data as $key => $row ) {
				foreach( $row as $field => $value ) {
					$entry[] = $this->_enclose_value($value);
			}
			$string .= implode($delimiter, $entry).$this->linefeed;
			$entry = array();
			}
			return $string;
			}

			/**
			 * Enclose values if needed
			 *  - only used by unParse()
			 * @param null $value
			 * @return mixed|null|string
			 */
			public function _enclose_value ($value = null) {
				if ( $value !== null && $value != '' ) {
					$delimiter = preg_quote($this->delimiter, '/');
					$enclosure = preg_quote($this->enclosure, '/');
					if($value[0]=='=') $value="'".$value; # Fix for the Comma separated vulnerabilities.
					if ( preg_match("/".$delimiter."|".$enclosure."|\n|\r/i", $value) || ($value[0] == ' ' || substr($value, -1) == ' ') ) {
						$value = str_replace($this->enclosure, $this->enclosure.$this->enclosure, $value);
						$value = $this->enclosure.$value.$this->enclosure;
					}
					else
						$value = $this->enclosure.$value.$this->enclosure;
				}
				return $value;
			}

	/**
	 * Apply exclusion before export
	 * @param $headers  - Apply exclusion headers
	 * @return array    - Available headers after applying the exclusions
	 */
			public function applyEventExclusion ($headers) {
				$header_exclusion = array();
				foreach ($headers as $hVal) {
					if(array_key_exists($hVal, $this->eventExclusions['exclusion_headers']['header'])) {
						$header_exclusion[] = $hVal;
					}
				}
				return $header_exclusion;
			}

			public function replace_prefix_aioseop_from_fieldname($fieldname){
				if(preg_match('/_aioseop_/', $fieldname)){
					return preg_replace('/_aioseop_/', '', $fieldname);
				}

				return $fieldname;
			}
			public function getaioseoFieldValue($post_id){
				if (is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php') || is_plugin_active('all-in-one-seo-pack-pro/all_in_one_seo_pack.php'))
				{
					global $wpdb;
					$aioseo_slug =$wpdb->get_results("SELECT * FROM {$wpdb->prefix}aioseo_posts WHERE post_id='$post_id' ");
					return $aioseo_slug;
				}

			}

			public function replace_prefix_pods_from_fieldname($fieldname){
				if(preg_match('/_pods_/', $fieldname)){
					return preg_replace('/_pods_/', '', $fieldname);
				}

				return $fieldname;
			}

			public function replace_prefix_yoast_wpseo_from_fieldname($fieldname){

				if(preg_match('/_yoast_wpseo_/', $fieldname)){
					$fieldname = preg_replace('/_yoast_wpseo_/', '', $fieldname);

					if($fieldname == 'focuskw') {
						$fieldname = 'focus_keyword';
					}else if($fieldname == 'bread-crumbs-title') { // It is comming as bctitle nowadays
						$fieldname = 'bctitle';
					}elseif($fieldname == 'metadesc') {
						$fieldname = 'meta_desc';
					}
				}

				return $fieldname;
			}

			public function replace_prefix_wpcf_from_fieldname($fieldname){
				if(preg_match('/_wpcf/', $fieldname)){
					return preg_replace('/_wpcf/', '', $fieldname);
				}

				return $fieldname;
			}

			public function replace_prefix_wpsc_from_fieldname($fieldname){
				if(preg_match('/_wpsc_/', $fieldname)){
					return preg_replace('/_wpsc_/', '', $fieldname);
				}

				return $fieldname;
			}

			public function replace_wpcr3_from_fieldname($fieldname){
				if(preg_match('/wpcr3_/', $fieldname)){
					$fieldname = preg_replace('/wpcr3_/', '', $fieldname);
				}

				return $fieldname;
			}

			public function change_fieldname_depends_on_post_type($post_type, $fieldname){
				if($post_type == 'wpcr3_review'){
					switch ($fieldname) {
					case 'ID':
						return 'review_id';
					case 'post_status':
						return 'status';
					case 'post_content':
						return 'review_text';
					case 'post_date':
						return 'date_time';
					default:
						return $fieldname;
					}
				}
				if($post_type == 'shop_order_refund'){
					switch ($fieldname) {
					case 'ID':
						return 'REFUNDID';
					default:
						return $fieldname;
					}
				}else if($post_type == 'shop_order'){
					switch ($fieldname) {
					case 'ID':
						return 'ORDERID';
					case 'post_status':
						return 'order_status';
					case 'post_excerpt':
						return 'customer_note';
					case 'post_date':
						return 'order_date';
					default:
						return $fieldname;
					}
				}else if($post_type == 'shop_coupon'){
					switch ($fieldname) {
					case 'ID':
						return 'COUPONID';
					case 'post_status':
						return 'coupon_status';
					case 'post_excerpt':
						return 'description';
					case 'post_date':
						return 'coupon_date';
					case 'post_title':
						return 'coupon_code';
					default:
						return $fieldname;
					}
				}else if($post_type == 'product_variation'){
					switch ($fieldname) {
					case 'ID':
						return 'VARIATIONID';
					case 'post_parent':
						return 'PRODUCTID';
					case 'sku':
						return 'VARIATIONSKU';
					default:
						return $fieldname;
					}
				}

				return $fieldname;
			}

			public function replace_underscore_from_fieldname($fieldname){
				if(preg_match('/_/', $fieldname)){
					$fieldname = preg_replace('/^_/', '', $fieldname);
				}

				return $fieldname;
			}

			public function fetchCategoryFieldValue($categories){

				global $wpdb;
				$bulk_category = [];

				foreach($categories as $category_id => $category){
					$term_meta = get_term_meta($category_id);
					$single_category = [];
					foreach($this->headers as $header){

						if($header == 'name'){
							$cato[] = get_term($category_id);
							$single_category[$header] = $this->hierarchy_based_term_cat_name($cato, 'category');
							//$single_category[$header] = $this->hierarchy_based_term_name(get_term($category_id), 'category');
							continue;
						}

						if(array_key_exists($header, $category)){
							$single_category[$header] = $category[$header];
						}else{
							if(isset($term_meta[$header])){
								$single_category[$header] = $this->returnMetaValueAsCustomerInput($term_meta[$header]);
							}else{
								$single_category[$header] = null;
							}
						}
					}
					array_push($bulk_category, $single_category);
				}
				return $bulk_category;
			}

			public function returnMetaValueAsCustomerInput($meta_value, $header = false){

				if(is_array($meta_value)){
					$meta_value = $meta_value[0];
					if(!empty($meta_value)){
						if(is_serialized($meta_value)){
							return unserialize($meta_value);
						}else if(is_array($meta_value)){
							return implode('|', $meta_value);
						}else if(is_string($meta_value)){
							return $meta_value;
						}else if($this->isJSON($meta_value) === true){
							return json_decode($meta_value);
						}

						return $meta_value;
					}

					return $meta_value;
				}else{
					if(is_serialized($meta_value)){
						$meta_value = unserialize($meta_value);
						if(is_array($meta_value)){
							return implode('|', $meta_value);	
						}
						return $meta_value;
					}else if(is_array($meta_value)){
						return implode('|', $meta_value);
					}else if(is_string($meta_value)){
						return $meta_value;
					}else if($this->isJSON($meta_value) === true){
						return json_decode($meta_value);
					}
				}

				return $meta_value;
			}

			public function isJSON($meta_value) {
				$json = json_decode($meta_value);
				return $json && $meta_value != $json;
			}

			public function hierarchy_based_term_name($term, $taxanomy_type){

				$tempo = array();
				$termo = '';
				$i=0;
				foreach($term as $termkey => $terms){
					$tempo[] = $terms->name;
					$temp_hierarchy_terms = [];
					
					if(!empty($terms->parent)){
						$temp1 = $terms->name;
						//$termo = '';
						$i++;
						
						$termexp = explode(',',$termo);
						
						
						$termo = implode(',',$termexp);
						//$termo = implode(',',$termunset);
						
						$temp_hierarchy_terms[] = $terms->name;
						$hierarchy_terms = $this->call_back_to_get_parent($terms->parent, $taxanomy_type, $tempo, $temp_hierarchy_terms);
						$termo .= $this->split_terms_by_arrow($hierarchy_terms).',';
						
	
					}else{
						
					    //if($terms->parent == 0){
						if(in_array($terms->name,$tempo)){
						
								$termo .= $terms->name.',';
						
						}
					}
				}
				return $termo;

				// $temp_hierarchy_terms = [];
				// if(!empty($term->parent)){
				// 	$temp_hierarchy_terms[] = $term->name;
				// 	$hierarchy_terms = $this->call_back_to_get_parent($term->parent, $taxanomy_type, $temp_hierarchy_terms);
				// 	return $this->split_terms_by_arrow($hierarchy_terms);

				// }else{
				// 	return $term->name;
				// }
			}

			public function hierarchy_based_term_cat_name($term, $taxanomy_type){
				$tempo = array();
				$termo = '';
				foreach($term as $terms){
					$tempo[] = $terms->name;
					$temp_hierarchy_terms = [];
					if(!empty($terms->parent)){
						$temp_hierarchy_terms[] = $terms->name;
						$hierarchy_terms = $this->call_back_to_get_parent($terms->parent, $taxanomy_type, $tempo, $temp_hierarchy_terms);
						 
						 $termo = $this->split_terms_by_arrow($hierarchy_terms);

					}else{
						$termo = $terms->name;
						
					}
				}
				return $termo;
			}
			public function call_back_to_get_parent($term_id, $taxanomy_type,$tempo, $temp_hierarchy_terms = []){
				$term = get_term($term_id, $taxanomy_type);
				if(!empty($term->parent)){
					if(in_array($term->name,$tempo)){
						
						$temp_hierarchy_terms[] = $term->name;
					
						$temp_hierarchy_terms = $this->call_back_to_get_parent($term->parent, $taxanomy_type,$tempo, $temp_hierarchy_terms);
					}
					else{
						$temp_hierarchy_terms[] = '';
				
						$temp_hierarchy_terms = $this->call_back_to_get_parent($term->parent, $taxanomy_type,$tempo, $temp_hierarchy_terms);
					}
					
				}else{
					if(in_array($term->name,$tempo)){
						$temp_hierarchy_terms[] = $term->name;
					}
					else{
						$temp_hierarchy_terms[] = '';
					}
				}
				return $temp_hierarchy_terms;
			}
			// public function call_back_to_get_parent($term_id, $taxanomy_type, $temp_hierarchy_terms = []){

			// 	$term = get_term($term_id, $taxanomy_type);
			// 	if(!empty($term->parent)){
			// 		$temp_hierarchy_terms[] = $term->name;
			// 		$temp_hierarchy_terms = $this->call_back_to_get_parent($term->parent, $taxanomy_type, $temp_hierarchy_terms);
			// 	}else{
			// 		$temp_hierarchy_terms[] = $term->name;
			// 	}

			// 	return $temp_hierarchy_terms;
			// }

			public function split_terms_by_arrow($hierarchy_terms){

				krsort($hierarchy_terms);
				return implode('>', $hierarchy_terms);
			}
}

return new exportExtension();
