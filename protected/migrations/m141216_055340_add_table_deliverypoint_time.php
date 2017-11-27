<?php

class m141216_055340_add_table_deliverypoint_time extends CDbMigration
{
	public function up()
	{
            $this->createTable('deliverypoint_time', array('DeliveryPointId'=>'int not null',
                                                            'type'=>'varchar(20) not null',
                                                            'day'=>'varchar(15) not null',
                                                            'StartTime'=>'varchar(10) not null',
                                                            'EndTime'=>'varchar(10) not null',
                                                            ) );
            $this->createIndex('unique_value', 'deliverypoint_time', 'DeliveryPointId,type,day', 'True');
	}

	public function down()
	{
            $this->dropTable('deliverypoint_time');
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