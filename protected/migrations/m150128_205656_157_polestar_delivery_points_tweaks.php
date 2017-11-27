<?php
class m150128_205656_157_polestar_delivery_points_tweaks extends CDbMigration {

    public function up() {
        $this->DropColumn('polestar_deliverypoint','NQ');
        $this->AddColumn('polestar_deliverypoint','Area','varchar(45)');
        $this->AddColumn('polestar_deliverypoint','Company','varchar(45)');
    }

    public function down() {
        $this->AddColumn('polestar_deliverypoint','NQ','varchar(20)');
        $this->DropColumn('polestar_deliverypoint','Area');
        $this->DropColumn('polestar_deliverypoint','Company');
    }
}