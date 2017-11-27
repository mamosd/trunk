<?php

class m150123_181257_add_isLive_to_client_title extends CDbMigration
{
	public function up()
	{
            $this->addColumn('client_title', 'IsLive', 'SMALLINT(6) not null DEFAULT 1');
	}

	public function down()
	{
            $this->dropColumn('client_title', 'IsLive');
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