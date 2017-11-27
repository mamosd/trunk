<?php

class m141021_052712_109_nqp_delete_ability extends CDbMigration
{
	public function up()
	{
            $this->addColumn('route', 'IsLive', 'SMALLINT not null DEFAULT 1');            
            $this->addColumn('title', 'IsLive', 'SMALLINT not null DEFAULT 1');
            
            $this->execute("CREATE OR REPLACE VIEW alltitles
                    AS
                    select `t`.`TitleId` AS `TitleId`,`t`.`Name` AS `Name`,`t`.`WeightPerPage` AS `WeightPerPage`,`p`.`Name` AS `PrintCentreName`,`cl`.`ClientLoginId` AS `ClientLoginId`,`cl`.`LoginId` AS `LoginId`,`cl`.`ClientId` AS `ClientId`,`c`.`Name` AS `ClientName`,`l`.`UserName` AS `LoginName`
                        , l.FriendlyName as LoginFriendlyName                        
                        , t.IsLive
                        , t.PrintDay
                        , t.OffPressTime
                    from (((((`title` `t` 
                    left join `printcentre` `p` on((`t`.`PrintCentreId` = `p`.`PrintCentreId`))) 
                    left join `clientlogin` `cl` on((`t`.`ClientLoginId` = `cl`.`ClientLoginId`))) 
                    left join `client` `c` on((`cl`.`ClientId` = `c`.`ClientId`))) 
                    left join `login` `l` on((`cl`.`LoginId` = `l`.`LoginId`))) 
                    );");
            
            $this->execute("CREATE OR REPLACE VIEW alltitlenextpublication
                    AS
                    select `t`.`TitleId` AS `TitleId`,`t`.`PrintCentreId` AS `PrintCentreId`,`t`.`Name` AS `Name`,`t`.`PrintDay` AS `PrintDay`,(curdate() + interval if(((`t`.`PrintDay` + 1) <= dayofweek(curdate())),((7 - dayofweek(curdate())) + (`t`.`PrintDay` + 1)),((`t`.`PrintDay` + 1) - dayofweek(curdate()))) day) AS `NextPublication`,`t`.`OffPressTime` AS `OffPressTime`,`t`.`WeightPerPage` AS `WeightPerPage`,`pc`.`Name` AS `PrintCentreName`,`tr`.`RouteId` AS `RouteId`,`r`.`Name` AS `RouteName`,`r`.`ShowDetailed` AS `ShowDetailed` 
                    , t.IsLive
                    from (((`title` `t` 
                    left join `printcentre` `pc` on((`t`.`PrintCentreId` = `pc`.`PrintCentreId`))) 
                    left join `alltitleroute` `tr` on((`t`.`TitleId` = `tr`.`TitleId`))) 
                    left join `route` `r` on((`tr`.`RouteId` = `r`.`RouteId`)));");
            
            $this->execute("CREATE OR REPLACE VIEW alltitlecontrol
                    AS
                    select `t`.`PrintCentreId` AS `PrintCentreId`,`t`.`PrintCentreName` AS `PrintCentreName`,`t`.`RouteId` AS `RouteId`,`t`.`RouteName` AS `RouteName`,`t`.`TitleId` AS `TitleId`,`t`.`Name` AS `TitleName`,`t`.`PrintDay` AS `PrintDay`,`t`.`NextPublication` AS `NextPublication`,`ri`.`Date` AS `DeliveryDate`,`ri`.`DTDate` AS `DTDate`,`t`.`OffPressTime` AS `OffPressTime`,`t`.`WeightPerPage` AS `WeightPerPage`,`ri`.`RouteInstanceId` AS `RouteInstanceId`,`ri`.`Status` AS `Status`,`ri`.`IsPrinted` AS `IsPrinted`,`ri`.`SupplierId` AS `SupplierId`,`ri`.`SupplierName` AS `SupplierName`,`ritw`.`TotalCopies` AS `TotalCopies`,`ritw`.`TotalWeight` AS `TotalWeight`,`ri`.`ShowDetailed` AS `ShowDetailed`,(case when (`ri`.`ShowDetailed` = 1) then (`ri`.`IsPrinted` and (not(exists(select 1 AS `1` from `routeinstancedetails` `d` where ((`d`.`RouteInstanceId` = `ri`.`RouteInstanceId`) and isnull(`d`.`DeliveryTime`)))))) else (`ri`.`IsPrinted` and exists(select 1 AS `1` from `routeinstancedetails` `d` where ((`d`.`RouteInstanceId` = `ri`.`RouteInstanceId`) and (`d`.`DeliveryTime` is not null)))) end) AS `DeliveryTimeEntered`,`ritw`.`TotalPalletsCollected` AS `TotalPalletsCollected`,`ritw`.`TotalPalletsDelivered` AS `TotalPalletsDelivered`,`ri`.`DepartureTime` AS `DepartureTime`,(case when (`ri`.`RouteInstanceId` is not null) then (select sum(`aritw2`.`TotalWeight`) AS `sum(aritw2.TotalWeight)` from `allrouteinstancetitleweight` `aritw2` where (`aritw2`.`RouteInstanceId` = `ri`.`RouteInstanceId`) group by `aritw2`.`RouteInstanceId`) else NULL end) AS `TotalRouteInstanceWeight`,`ri`.`VehicleDescription` AS `VehicleDescription`,`ri`.`VehicleCapacity` AS `VehicleCapacity` 
                    , t.IsLive
                    from ((`alltitlenextpublication` `t` 
                    left join `allrouteinstancesuppliers` `ri` on((`t`.`RouteId` = `ri`.`RouteId`))) 
                    left join `allrouteinstancetitleweight` `ritw` on(((`ritw`.`RouteInstanceId` = `ri`.`RouteInstanceId`) and (`ritw`.`TitleId` = `t`.`TitleId`)))) 
                    union 
                    select `t`.`PrintCentreId` AS `PrintCentreId`,`t`.`PrintCentreName` AS `PrintCentreName`,`t`.`RouteId` AS `RouteId`,`t`.`RouteName` AS `RouteName`,`t`.`TitleId` AS `TitleId`,`t`.`Name` AS `TitleName`,`t`.`PrintDay` AS `PrintDay`,`t`.`NextPublication` AS `NextPublication`,NULL AS `DeliveryDate`,NULL AS `DTDate`,`t`.`OffPressTime` AS `OffPressTime`,`t`.`WeightPerPage` AS `WeightPerPage`,NULL AS `RouteInstanceId`,NULL AS `Status`,NULL AS `IsPrinted`,NULL AS `SupplierId`,NULL AS `SupplierName`,NULL AS `TotalCopies`,NULL AS `TotalWeight`,NULL AS `ShowDetailed`,0 AS `DeliveryTimeEntered`,NULL AS `TotalPalletsCollected`,NULL AS `TotalPalletsDelivered`,NULL AS `DepartureTime`,NULL AS `TotalRouteInstanceWeight`,NULL AS `VehicleDescription`,NULL AS `VehicleCapacity` 
                    , t.IsLive
                    from `alltitlenextpublication` `t` 
                    where (not(exists(select 1 AS `Not_used` from `allrouteinstancesuppliers` `ris` where ((`ris`.`RouteId` = `t`.`RouteId`) and (`ris`.`DTDate` = `t`.`NextPublication`))))) ;");
                    
	}

	public function down()
	{
            $this->execute("CREATE OR REPLACE VIEW alltitles
                    AS
                    select `t`.`TitleId` AS `TitleId`,`t`.`Name` AS `Name`,`t`.`WeightPerPage` AS `WeightPerPage`,`p`.`Name` AS `PrintCentreName`,`cl`.`ClientLoginId` AS `ClientLoginId`,`cl`.`LoginId` AS `LoginId`,`cl`.`ClientId` AS `ClientId`,`c`.`Name` AS `ClientName`,`l`.`UserName` AS `LoginName`
                    from (((((`title` `t` 
                    left join `printcentre` `p` on((`t`.`PrintCentreId` = `p`.`PrintCentreId`))) 
                    left join `clientlogin` `cl` on((`t`.`ClientLoginId` = `cl`.`ClientLoginId`))) 
                    left join `client` `c` on((`cl`.`ClientId` = `c`.`ClientId`))) 
                    left join `login` `l` on((`cl`.`LoginId` = `l`.`LoginId`))) 
                    );");
            $this->execute("CREATE OR REPLACE VIEW alltitlenextpublication
                    AS
                    select `t`.`TitleId` AS `TitleId`,`t`.`PrintCentreId` AS `PrintCentreId`,`t`.`Name` AS `Name`,`t`.`PrintDay` AS `PrintDay`,(curdate() + interval if(((`t`.`PrintDay` + 1) <= dayofweek(curdate())),((7 - dayofweek(curdate())) + (`t`.`PrintDay` + 1)),((`t`.`PrintDay` + 1) - dayofweek(curdate()))) day) AS `NextPublication`,`t`.`OffPressTime` AS `OffPressTime`,`t`.`WeightPerPage` AS `WeightPerPage`,`pc`.`Name` AS `PrintCentreName`,`tr`.`RouteId` AS `RouteId`,`r`.`Name` AS `RouteName`,`r`.`ShowDetailed` AS `ShowDetailed` 
                    from (((`title` `t` 
                    left join `printcentre` `pc` on((`t`.`PrintCentreId` = `pc`.`PrintCentreId`))) 
                    left join `alltitleroute` `tr` on((`t`.`TitleId` = `tr`.`TitleId`))) 
                    left join `route` `r` on((`tr`.`RouteId` = `r`.`RouteId`)));");
            
            $this->execute("CREATE OR REPLACE VIEW alltitlecontrol
                    AS
                    select `t`.`PrintCentreId` AS `PrintCentreId`,`t`.`PrintCentreName` AS `PrintCentreName`,`t`.`RouteId` AS `RouteId`,`t`.`RouteName` AS `RouteName`,`t`.`TitleId` AS `TitleId`,`t`.`Name` AS `TitleName`,`t`.`PrintDay` AS `PrintDay`,`t`.`NextPublication` AS `NextPublication`,`ri`.`Date` AS `DeliveryDate`,`ri`.`DTDate` AS `DTDate`,`t`.`OffPressTime` AS `OffPressTime`,`t`.`WeightPerPage` AS `WeightPerPage`,`ri`.`RouteInstanceId` AS `RouteInstanceId`,`ri`.`Status` AS `Status`,`ri`.`IsPrinted` AS `IsPrinted`,`ri`.`SupplierId` AS `SupplierId`,`ri`.`SupplierName` AS `SupplierName`,`ritw`.`TotalCopies` AS `TotalCopies`,`ritw`.`TotalWeight` AS `TotalWeight`,`ri`.`ShowDetailed` AS `ShowDetailed`,(case when (`ri`.`ShowDetailed` = 1) then (`ri`.`IsPrinted` and (not(exists(select 1 AS `1` from `routeinstancedetails` `d` where ((`d`.`RouteInstanceId` = `ri`.`RouteInstanceId`) and isnull(`d`.`DeliveryTime`)))))) else (`ri`.`IsPrinted` and exists(select 1 AS `1` from `routeinstancedetails` `d` where ((`d`.`RouteInstanceId` = `ri`.`RouteInstanceId`) and (`d`.`DeliveryTime` is not null)))) end) AS `DeliveryTimeEntered`,`ritw`.`TotalPalletsCollected` AS `TotalPalletsCollected`,`ritw`.`TotalPalletsDelivered` AS `TotalPalletsDelivered`,`ri`.`DepartureTime` AS `DepartureTime`,(case when (`ri`.`RouteInstanceId` is not null) then (select sum(`aritw2`.`TotalWeight`) AS `sum(aritw2.TotalWeight)` from `allrouteinstancetitleweight` `aritw2` where (`aritw2`.`RouteInstanceId` = `ri`.`RouteInstanceId`) group by `aritw2`.`RouteInstanceId`) else NULL end) AS `TotalRouteInstanceWeight`,`ri`.`VehicleDescription` AS `VehicleDescription`,`ri`.`VehicleCapacity` AS `VehicleCapacity` 
                    from ((`alltitlenextpublication` `t` 
                    left join `allrouteinstancesuppliers` `ri` on((`t`.`RouteId` = `ri`.`RouteId`))) 
                    left join `allrouteinstancetitleweight` `ritw` on(((`ritw`.`RouteInstanceId` = `ri`.`RouteInstanceId`) and (`ritw`.`TitleId` = `t`.`TitleId`)))) 
                    union 
                    select `t`.`PrintCentreId` AS `PrintCentreId`,`t`.`PrintCentreName` AS `PrintCentreName`,`t`.`RouteId` AS `RouteId`,`t`.`RouteName` AS `RouteName`,`t`.`TitleId` AS `TitleId`,`t`.`Name` AS `TitleName`,`t`.`PrintDay` AS `PrintDay`,`t`.`NextPublication` AS `NextPublication`,NULL AS `DeliveryDate`,NULL AS `DTDate`,`t`.`OffPressTime` AS `OffPressTime`,`t`.`WeightPerPage` AS `WeightPerPage`,NULL AS `RouteInstanceId`,NULL AS `Status`,NULL AS `IsPrinted`,NULL AS `SupplierId`,NULL AS `SupplierName`,NULL AS `TotalCopies`,NULL AS `TotalWeight`,NULL AS `ShowDetailed`,0 AS `DeliveryTimeEntered`,NULL AS `TotalPalletsCollected`,NULL AS `TotalPalletsDelivered`,NULL AS `DepartureTime`,NULL AS `TotalRouteInstanceWeight`,NULL AS `VehicleDescription`,NULL AS `VehicleCapacity` 
                    from `alltitlenextpublication` `t` 
                    where (not(exists(select 1 AS `Not_used` from `allrouteinstancesuppliers` `ris` where ((`ris`.`RouteId` = `t`.`RouteId`) and (`ris`.`DTDate` = `t`.`NextPublication`))))) ;");
            
            $this->dropColumn('route', 'IsLive');
            $this->dropColumn('title', 'IsLive');
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