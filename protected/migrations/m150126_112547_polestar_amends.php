<?php

class m150126_112547_polestar_amends extends CDbMigration
{
	public function up()
	{
            $this->addColumn('polestar_job', 'AgreedPrice', 'DECIMAL(10,2) NULL');
            $this->addColumn('polestar_job_history', 'AgreedPrice', 'DECIMAL(10,2) NULL');
	}

	public function down()
	{
            $this->dropColumn('polestar_job', 'AgreedPrice');
            $this->dropColumn('polestar_job_history', 'AgreedPrice');
	}
}