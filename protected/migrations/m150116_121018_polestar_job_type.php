<?php

class m150116_121018_polestar_job_type extends CDbMigration
{
	public function up()
	{
            $this->createTable ( 'polestar_job_type', array (
                'Id'                => 'pk',
                'Name'              => 'varchar(50) not null',
                'Code'              => 'varchar(50)',
            ));
            
            $this->insert ( 'polestar_job_type', array (
                'Name'              => 'PRIORITY',
            ));
	}

	public function down()
	{
            $this->dropTable ( 'polestar_job_type' );
	}
}