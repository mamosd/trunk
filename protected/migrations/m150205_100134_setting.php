<?php

class m150205_100134_setting extends CDbMigration
{
	public function up()
	{
            $this->createTable('setting', array (
                'Id' => 'pk',
                'Subsystem'       => 'varchar(255)',
                'Name'   => 'varchar(255)',
                'Value'     => 'text'
            ));
            
            $this->insert('setting', array (
                'Subsystem' => 'polestar',
                'Name'      => 'email-override',
                'Value'     => 'ramon.lujan@gmail.com;david@logicc.co.uk;seb.gudek@aktrion.com'
            ));
            
            $this->insert('setting', array (
                'Subsystem' => 'polestar',
                'Name'      => 'aktrion-supplier-id',
                'Value'     => '1'
            ));
	}

	public function down()
	{
            $this->dropTable('setting');
	}
}