<?php
/*
Plugin Name: Replace Post Link
Description: Replace post link from custom field
Author: Takuro Hishikawa, Megumi Themes
Version: 0.1
*/

class replace_post_link {
	
	/** Custom field key
	*/
	private $field_name;

	// singleton instance
	private static $instance;

	public static function instance() {
		if ( isset( self::$instance ) )
			return self::$instance;

		self::$instance = new replace_post_link;
		self::$instance->run_init();
		return self::$instance;
	}

	private function __construct() {
		/** Do nothing **/
	}
	
	function run_init() {
		$this->field_name = apply_filters( 'replace_post_link_field', 'replace_post_link' );
		add_action( 'init', array( $this, 'add_filter' ) );
	}
	
	function add_filter() {
		if (!is_admin()) {
			add_filter( 'post_link', array( $this, 'replace_post_link' ), 10, 3 );
			add_filter( 'page_link', array( $this, 'replace_page_link' ), 10, 3 );
			add_filter( 'post_type_link', array( $this, 'replace_post_type_link' ), 10, 4 );
			add_filter( 'comments_open', array( $this, 'comments_open' ), 10, 2 );
		}
	}
	
	function replace_post_link( $permalink, $post, $leavename ) {
		$key = $this->get_field_name();
		$replace_link = esc_url( $post->$key );
		if ( !empty($replace_link) && ($replace_link != $permalink) ) {
			return $replace_link;
		}
		
		return $permalink;
	}
	
	function replace_page_link( $link, $post_id, $sample ) {
		$post = get_post( $post_id );
		$key = $this->get_field_name();
		$replace_link = esc_url( $post->$key );
		if ( !empty($replace_link) && ($replace_link != $link) ) {
			return $replace_link;
		}
		
		return $link;
	}
	
	function replace_post_type_link( $post_link, $post, $leavename, $sample ) {
		$key = $this->get_field_name();
		$replace_link = esc_url( $post->$key );
		if ( !empty($replace_link) && ($replace_link != $post_link) ) {
			return $replace_link;
		}
		
		return $post_link;
	}
	
	function comments_open( $open, $post_id ) {
		if ($open) {
			$post = get_post( $post_id );
			$key = $this->get_field_name();
			$link = esc_url( $post->$key );
			if (!empty($link)) {
				return false;
			}
		}
		
		return $open;
	}
	
	function get_field_name() {
		return $this->field_name;
	}
	
}

$replace_post_link = replace_post_link::instance();

// conditional function
function is_link_replaced($post = false) {
	$post = get_post( $post );
	$key = replace_post_link::instance()->get_field_name();
	$link = esc_url( $post->$key );
	if (!empty($link)) {
		return true;
	}
	
	return false;
}