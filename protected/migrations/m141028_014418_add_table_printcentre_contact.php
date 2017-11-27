<?php

class m141028_014418_add_table_printcentre_contact extends CDbMigration
{
	public function up()
	{
            $this->createTable('printcentre_contact', array('ContactId'=>'pk',
                                                            'PrintCentreId'=>'int not null',
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
            $this->dropTable('printcentre_contact');
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