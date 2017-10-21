<?php
defined ( "BASEPATH" ) or exit ( "No direct script access allowed" );
/**
 * This is the framework oriented way.
 *
 * @author Birathiban
 *        
 */
class Migration_add_products extends CI_Migration {
	public function up() {
		$this->dbforge->add_field ( array (
				'id' => array (
						'type' => 'INT',
						'constraint' => 11,
						'unsigned' => FALSE,
						'auto_increment' => TRUE 
				),
				'productName' => array (
						'type' => 'text',
						'constraint' => 11,
						'null' => FALSE 
				)
		) );
		
		$this->dbforge->add_key ( 'id', TRUE );
		$this->dbforge->create_table ( 'products' );
		$this->db->query ( "ALTER TABLE products ENGINE = InnoDB" );
	}
	public function down() {
		$this->dbforge->drop_table ( 'products' );
	}
}
