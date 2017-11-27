<?php

class m150205_080438_polestar_printcentre_login extends CDbMigration
{
	public function up()
	{
            $this->createTable('polestar_printcentre_login', array (
                'PrintCentreId' => 'int not null',
                'LoginId'       => 'int not null',
                'DateCreated'   => 'datetime',
                'CreatedBy'     => 'int not null'
            ));
	}

	public function down()
	{
            $this->dropTable('polestar_printcentre_login');
	}
}