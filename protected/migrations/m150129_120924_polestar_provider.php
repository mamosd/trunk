<?php

class m150129_120924_polestar_provider extends CDbMigration
{
	public function up()
	{
            $this->AddColumn('polestar_job', 'ProviderId', 'int');
            $this->AddColumn('polestar_job_history', 'ProviderId', 'int');
	}

	public function down()
	{
            $this->DropColumn('polestar_job', 'ProviderId');
            $this->DropColumn('polestar_job_history', 'ProviderId');
	}
}