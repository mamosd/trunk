<?php

class m141111_091131_add_table_deliverypoint_contact extends CDbMigration
{
	public function up()
	{
            $this->createTable('deliverypoint_contact', array('ContactId'=>'pk',
                                                            'DeliveryPointId'=>'int not null',
                                                            'type'=>'varchar(20) not null',
                                                            'department'=>'varchar(50) not null',
                                                            'name'=>'varchar(50) not null',
                                                            'surname'=>'varchar(50) not null',
                                                            'telNumber'=>'varchar(20) not null',
                                                            'mobileNumber'=>'varchar(20) not null',
                                                            'email'=>'varchar(50) not null'
                                                            ) );            
	}

	public function down()
	{
            $this->dropTable('deliverypoint_contact');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}