<?php

/*
Plugin Name: term_taxonomymeta (DB Table)
Plugin URI: https://github.com/benhuson/term_taxonomymeta
Description: Helper plugin which adds a term_taxonomymeta table in the database and adds support for 'term_taxonomy' meta using the core WordPress functions add_metadata(), update_metadata(), delete_metadata() and get_metadata().
Version: 0.1
Author: Ben Huson
Author URI: http://www.benhuson.co.uk
License: GPL
*/

global $term_taxonomymeta_table;
$term_taxonomymeta_table = new Term_TaxonomyMeta();

class Term_TaxonomyMeta {
	
	var $db_version = '0.1';
	var $table_name = 'term_taxonomymeta';
	var $meta_type  = 'term_taxonomy';
	
	/**
	 * Constructor
	 */
	function Term_TaxonomyMeta() {
		global $wpdb;
		$table_name = $this->table_name;
		$wpdb->$table_name = $wpdb->prefix . $table_name;
		register_activation_hook( __FILE__, array( $this, 'maybe_upgrade_db_schema' ) );
	}
	
	/**
	 * Maybe upgrade the DB schema
	 */
	function maybe_upgrade_db_schema() {
		global $wpdb;
		
		$installed_db_version = get_option( 'term_taxonomymeta_table_db_version' );
		
		if ( $installed_db_version != $this->db_version ) {
			$table_name = $wpdb->prefix . $this->table_name;
			if ( ! empty( $wpdb->charset ) )
				$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
			if ( ! empty( $wpdb->collate ) )
				$charset_collate .= " COLLATE {$wpdb->collate}";
			
			$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			  meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			  {$this->meta_type}_id bigint(20) NOT NULL default 0,
			  meta_key varchar(255) DEFAULT NULL,
			  meta_value longtext DEFAULT NULL,
			  UNIQUE KEY meta_id (meta_id)
			) {$charset_collate};";
			
			// Update Schema
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			update_option( 'term_taxonomymeta_table_db_version', $this->db_version );
		}
	}
	
}

?>