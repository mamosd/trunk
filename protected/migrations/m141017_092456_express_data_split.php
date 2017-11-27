<?php

class m141017_092456_express_data_split extends CDbMigration
{
	public function up()
	{
            $this->execute("CREATE TABLE IF NOT EXISTS `client_printcentre` (
                            `PrintCentreId` int(11) NOT NULL AUTO_INCREMENT,
                            `Name` varchar(45) DEFAULT NULL,
                            `Address` varchar(250) DEFAULT NULL,
                            `DateCreated` datetime DEFAULT NULL,
                            `DateUpdated` datetime DEFAULT NULL,
                            `UpdatedBy` varchar(45) DEFAULT NULL,
                            `ClientId` int(11) DEFAULT NULL,
                            `Enabled` int(11) NOT NULL DEFAULT '0',
                            PRIMARY KEY (`PrintCentreId`)
                          ) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;");
            
            $this->execute("/*!40000 ALTER TABLE `client_printcentre` DISABLE KEYS */;
                            INSERT INTO `client_printcentre` (`PrintCentreId`, `Name`, `Address`, `DateCreated`, `DateUpdated`, `UpdatedBy`, `ClientId`, `Enabled`) VALUES
                                    (2, 'Broughton', '** used on client routing project **\r\n** Express Only **', '2012-11-26 13:38:54', '2014-10-09 12:03:33', 'admin', 2, 1),
                                    (4, 'Luton', '** used on client routing project **\r\n** Express Only **', '2012-11-26 13:38:55', '2014-10-09 12:03:39', 'admin', 2, 1);
                            /*!40000 ALTER TABLE `client_printcentre` ENABLE KEYS */;");
            
            $this->execute("CREATE TABLE IF NOT EXISTS `client_vehicle` (
                            `VehicleId` int(11) NOT NULL AUTO_INCREMENT,
                            `SupplierId` int(11) NOT NULL,
                            `Description` varchar(45) DEFAULT NULL,
                            `Capacity` int(45) unsigned DEFAULT NULL,
                            `ClientId` int(11) DEFAULT NULL,
                            PRIMARY KEY (`VehicleId`),
                            KEY `fk_Vehicle_Supplier1` (`SupplierId`),
                            KEY `ClientId` (`ClientId`)
                          ) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;");
            
            $this->execute("/*!40000 ALTER TABLE `client_vehicle` DISABLE KEYS */;
                        INSERT INTO `client_vehicle` (`VehicleId`, `SupplierId`, `Description`, `Capacity`, `ClientId`) VALUES
                                (8, 0, 'Van', NULL, 2),
                                (9, 0, '7.5t', NULL, 2),
                                (10, 0, '18t', NULL, 2),
                                (11, 0, '44t', NULL, 2);
                        /*!40000 ALTER TABLE `client_vehicle` ENABLE KEYS */;");
            
            $this->execute("CREATE OR REPLACE VIEW `client_route_info` AS 
                        select `r`.`ClientRouteId` AS `ClientRouteId`,`r`.`ClientId` AS `ClientId`,`c`.`Name` AS `ClientName`,`r`.`RouteId` AS `RouteId`,`r`.`Weekday` AS `Weekday`,`r`.`PrintCentreId` AS `PrintCentreId`,`pc`.`Name` AS `PrintCentreName`,`r`.`VehicleId` AS `VehicleId`,`v`.`Description` AS `VehicleDescription`,time_format(`r`.`DepartureTime`,_utf8'%H:%i') AS `DepartureTime`,`r`.`Comments` AS `Comments`,`r`.`DetailsEntered` AS `DetailsEntered`,`d`.`ClientRouteDetailsId` AS `ClientRouteDetailsId`,`d`.`ClientWholesalerId` AS `ClientWholesalerId`,`ws`.`WholesalerId` AS `WholesalerId`,`ws`.`Name` AS `WholesalerName`,`ws`.`FriendlyName` AS `WholesalerAlias`,time_format(`d`.`ArrivalTime`,_utf8'%H:%i') AS `ArrivalTime`,time_format(`d`.`NPATime`,_utf8'%H:%i') AS `NPATime`,`d`.`Comments` AS `DetailsComments` 
                        from (((((`client_route` `r` 
                                left join `client_route_details` `d` on((`r`.`ClientRouteId` = `d`.`ClientRouteId`))) 
                                left join `client_printcentre` `pc` on((`r`.`PrintCentreId` = `pc`.`PrintCentreId`))) 
                                left join `client` `c` on((`c`.`ClientId` = `r`.`ClientId`))) 
                                left join `client_vehicle` `v` on((`v`.`VehicleId` = `r`.`VehicleId`))) 
                                left join `client_wholesaler` `ws` on((`ws`.`ClientWholesalerId` = `d`.`ClientWholesalerId`))) ;");
            
            $this->execute("CREATE OR REPLACE VIEW `client_route_instance_info` AS 
                        select `r`.`ClientRouteInstanceId` AS `ClientRouteInstanceId`,`r`.`ClientId` AS `ClientId`,`c`.`Name` AS `ClientName`,`r`.`RouteId` AS `RouteId`,`r`.`DeliveryDate` AS `DeliveryDate`,`r`.`PrintCentreId` AS `PrintCentreId`,`pc`.`Name` AS `PrintCentreName`,`r`.`VehicleId` AS `VehicleId`,`v`.`Description` AS `VehicleDescription`,time_format(`r`.`DepartureTime`,_utf8'%H:%i') AS `DepartureTime`,time_format((case when (`r`.`DepartureTime` between _utf8'17:00' and _utf8'24:00') then `r`.`DepartureTime` else addtime(`r`.`DepartureTime`,_utf8'24:00') end),_utf8'%H:%i') AS `DepartureTimeSort`,time_format(`r`.`DepartureTimeActual`,_utf8'%H:%i') AS `DepartureTimeActual`,time_format((case when (`r`.`DepartureTimeActual` between _utf8'17:00' and _utf8'24:00') then `r`.`DepartureTimeActual` else addtime(`r`.`DepartureTimeActual`,_utf8'24:00') end),_utf8'%H:%i') AS `DepartureTimeActualSort`,`d`.`ClientRouteInstanceDetailsId` AS `ClientRouteInstanceDetailsId`,ifnull(`wsm`.`ClientWholesalerId`,`d`.`ClientWholesalerId`) AS `ClientWholesalerId`,ifnull(`wsm`.`WholesalerId`,`ws`.`WholesalerId`) AS `WholesalerId`,ifnull(`wsm`.`Name`,`ws`.`Name`) AS `WholesalerName`,ifnull(`wsm`.`FriendlyName`,`ws`.`FriendlyName`) AS `WholesalerAlias`,ifnull(`wsm`.`Address1`,`ws`.`Address1`) AS `WSAddress1`,ifnull(`wsm`.`Address2`,`ws`.`Address2`) AS `WSAddress2`,ifnull(`wsm`.`Address3`,`ws`.`Address3`) AS `WSAddress3`,ifnull(`wsm`.`Address4`,`ws`.`Address4`) AS `WSAddress4`,ifnull(`wsm`.`Address5`,`ws`.`Address5`) AS `WSAddress5`,`dr`.`ClientTitleId` AS `ClientTitleId`,`t`.`TitleId` AS `TitleId`,`t`.`Name` AS `TitleName`,`t`.`TitleType` AS `TitleType`,`dr`.`PubPagination` AS `PubPagination`,`dr`.`PubWeight` AS `PubWeight`,`dr`.`BundleSize` AS `BundleSize`,`dr`.`Quantity` AS `Quantity`,time_format(`d`.`ArrivalTime`,_utf8'%H:%i') AS `ArrivalTime`,time_format((case when (`d`.`ArrivalTime` between _utf8'17:00' and _utf8'24:00') then `d`.`ArrivalTime` else addtime(`d`.`ArrivalTime`,_utf8'24:00') end),_utf8'%H:%i') AS `ArrivalTimeSort`,time_format(`d`.`ArrivalTimeActual`,_utf8'%H:%i') AS `ArrivalTimeActual`,time_format((case when (`d`.`ArrivalTimeActual` between _utf8'17:00' and _utf8'24:00') then `d`.`ArrivalTimeActual` else addtime(`d`.`ArrivalTimeActual`,_utf8'24:00') end),_utf8'%H:%i') AS `ArrivalTimeActualSort`,time_format(`d`.`NPATime`,_utf8'%H:%i') AS `NPATime`,time_format((case when (`d`.`NPATime` between _utf8'17:00' and _utf8'24:00') then `d`.`NPATime` else addtime(`d`.`NPATime`,_utf8'24:00') end),_utf8'%H:%i') AS `NPATimeSort`,`d`.`PlasticPalletsCollected` AS `PlasticPalletsCollected`,`d`.`PlasticPalletsDelivered` AS `PlasticPalletsDelivered`,`d`.`WoodenPalletsCollected` AS `WoodenPalletsCollected`,`d`.`WoodenPalletsDelivered` AS `WoodenPalletsDelivered`,`d`.`DateUpdated` AS `DateUpdated`,`d`.`Comments` AS `Comments`,`dr`.`ClientRouteInstanceDropId` AS `ClientRouteInstanceDropId` 
                        from ((((((((`client_route_instance` `r` 
                                left join `client_route_instance_details` `d` on((`d`.`ClientRouteInstanceId` = `r`.`ClientRouteInstanceId`))) 
                                left join `client_route_instance_drop` `dr` on((`dr`.`ClientRouteInstanceDetailsId` = `d`.`ClientRouteInstanceDetailsId`))) 
                                left join `client_printcentre` `pc` on((`r`.`PrintCentreId` = `pc`.`PrintCentreId`))) 
                                left join `client` `c` on((`c`.`ClientId` = `r`.`ClientId`))) 
                                left join `client_vehicle` `v` on((`v`.`VehicleId` = `r`.`VehicleId`))) 
                                left join `client_wholesaler` `ws` on((`ws`.`ClientWholesalerId` = `d`.`ClientWholesalerId`))) 
                                left join `client_title` `t` on((`t`.`ClientTitleId` = `dr`.`ClientTitleId`))) 
                                left join `client_wholesaler` `wsm` on((`ws`.`MainClientWholesalerId` = `wsm`.`ClientWholesalerId`))) ;");
            
            $this->execute("CREATE OR REPLACE VIEW `client_route_kpi` AS 
                        select `r`.`ClientId` AS `ClientId`,`r`.`DeliveryDate` AS `DeliveryDate`,`r`.`PrintCentreId` AS `PrintCentreId`,`pc`.`Name` AS `PrintCentreName`,`r`.`RouteId` AS `RouteId`,`r`.`DepartureTime` AS `DepartureTime`,time_format((case when (`r`.`DepartureTime` between '17:00' and '24:00') then `r`.`DepartureTime` else addtime(`r`.`DepartureTime`,'24:00') end),'%H:%i') AS `DepartureTimeCompare`,`r`.`DepartureTimeActual` AS `DepartureTimeActual`,time_format((case when (`r`.`DepartureTimeActual` between '17:00' and '24:00') then `r`.`DepartureTimeActual` else addtime(`r`.`DepartureTimeActual`,'24:00') end),'%H:%i') AS `DepartureTimeActualCompare`,`r`.`VehicleId` AS `VehicleId`,`v`.`Description` AS `VehicleDescription`,ifnull(`w`.`MainClientWholesalerId`,`d`.`ClientWholesalerId`) AS `WholesalerId`,ifnull(`wmain`.`Name`,`w`.`Name`) AS `WholesalerName`,ifnull(`wmain`.`FriendlyName`,`w`.`FriendlyName`) AS `WholesalerAlias`,`d`.`ArrivalTime` AS `ArrivalTime`,time_format((case when (`d`.`ArrivalTime` between '17:00' and '24:00') then `d`.`ArrivalTime` else addtime(`d`.`ArrivalTime`,'24:00') end),'%H:%i') AS `ArrivalTimeCompare`,`d`.`ArrivalTimeActual` AS `ArrivalTimeActual`,time_format((case when (`d`.`ArrivalTimeActual` between '17:00' and '24:00') then `d`.`ArrivalTimeActual` else addtime(`d`.`ArrivalTimeActual`,'24:00') end),'%H:%i') AS `ArrivalTimeActualCompare`,`d`.`NPATime` AS `NPATime`,time_format((case when (`d`.`NPATime` between '17:00' and '24:00') then `d`.`NPATime` else addtime(`d`.`NPATime`,'24:00') end),'%H:%i') AS `NPATimeCompare` 
                        from (((((`client_route_instance` `r` 
                                left join `client_route_instance_details` `d` on((`d`.`ClientRouteInstanceId` = `r`.`ClientRouteInstanceId`))) 
                                left join `client_wholesaler` `w` on((`w`.`ClientWholesalerId` = `d`.`ClientWholesalerId`))) 
                                left join `client_wholesaler` `wmain` on((`wmain`.`ClientWholesalerId` = `w`.`MainClientWholesalerId`))) 
                                left join `client_printcentre` `pc` on((`pc`.`PrintCentreId` = `r`.`PrintCentreId`))) 
                                left join `client_vehicle` `v` on((`v`.`VehicleId` = `r`.`VehicleId`))) ;");
	}

	public function down()
	{
		echo "m141017_092456_express_data_split does not support migration down.\n";
		return false;
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