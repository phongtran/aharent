<?php

if(!defined('ABSPATH')){
	exit;
}

if(!class_exists('WC_Cancel_Guest',false)){
	class WC_Cancel_Guest{

		public $slug ='';
		public $args = array();

		function __construct($args){
			$this->args = $args;
			$this->slug = $args['slug'];
		}

		public function guest_page($posts){
			global $wp,$wp_query;
			$page_slug = $this->slug;
			if(strtolower($wp->request) == $page_slug || $wp->query_vars['page_id'] == $page_slug){

				$post = new stdClass;
				$post->post_author = 1;
				$post->post_name = $page_slug;
				$post->guid = get_home_url(get_current_blog_id(),'/'.$page_slug);
				$post->post_title = isset($this->args['post_title']) ? $this->args['post_title']  : __('Order Details','wc-cancel-order');
				$post->post_content = isset($this->args['post content']) ? $this->args['post content']  : '[wc_cancel_order_details]';
				$post->ID = -42;
				$post->post_type = 'wc-cancel-order-page';
				$post->post_status = 'static';
				$post->comment_status = 'closed';
				$post->ping_status = 'closed';
				$post->comment_count = 0;
				$post->post_date = current_time('mysql');
				$post->post_date_gmt = current_time('mysql',1);

				$post = (object) array_merge((array)$post,(array)$this->args);
				$posts = NULL;
				$posts[] = $post;

				$wp_query->is_page = true;
				$wp_query->is_singular = true;
				$wp_query->is_home = false;
				$wp_query->is_archive = false;
				$wp_query->is_category = false;
				unset($wp_query->query["error"]);
				$wp_query->query_vars["error"]="";
				$wp_query->is_404 = false;
			}
			return $posts;
		}
	}
}

