<?php
class m150115_181945_default_polestar_vehicles extends CDbMigration {

    public function up() {
        $this->insert ( 'polestar_vehicle', array (
            'Name'              => 'VAN',
        ));
        $this->insert ( 'polestar_vehicle', array (
            'Name'              => '7.5T',
        ));
        $this->insert ( 'polestar_vehicle', array (
            'Name'              => '18T',
        ));
        $this->insert ( 'polestar_vehicle', array (
            'Name'              => '26T',
        ));
        $this->insert ( 'polestar_vehicle', array (
            'Name'              => 'SD',
        ));
        $this->insert ( 'polestar_vehicle', array (
            'Name'              => 'DD',
        ));
    }

    public function down() {
        $this->delete ( 'polestar_vehicle', "Name = 'VAN'");
        $this->delete ( 'polestar_vehicle', "Name = '7.5T'");
        $this->delete ( 'polestar_vehicle', "Name = '18T'");
        $this->delete ( 'polestar_vehicle', "Name = '26T'");
        $this->delete ( 'polestar_vehicle', "Name = 'SD'");
        $this->delete ( 'polestar_vehicle', "Name = 'DD'");
    }

}