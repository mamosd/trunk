<?php

class m150121_090646_add_table_express_titles extends CDbMigration
{
	public function up()
	{
            $this->createTable('express_title', array('titleId'=>'pk',
                                                            'name'=>'varchar(100) DEFAULT null',
                                                            'IsLive'=>'smallint(6) not null DEFAULT 1',
                                                            ) );
	}

	public function down()
	{
            $this->dropTable('express_title');
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