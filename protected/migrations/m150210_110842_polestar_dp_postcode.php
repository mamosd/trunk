<?php

class m150210_110842_polestar_dp_postcode extends CDbMigration
{
	public function up()
	{
            $this->AddColumn('polestar_deliverypoint', 'SanitizedPostcode', 'varchar(10)');
	}

	public function down()
	{
            $this->DropColumn('polestar_deliverypoint', 'SanitizedPostcode');
	}
}