<?php
class m150402_100757_special_instructions extends CDbMigration {
    
    public function up() {
        $this->AddColumn('polestar_load','LoadSpecialInstructions','varchar(250)');
    }
    
    public function down() {
        $this->DropColumn('polestar_load','LoadSpecialInstructions');
    }
    
}