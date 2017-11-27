# --------------------------------------------------------
# Host:                         web2.logicc.co.uk
# Server version:               5.1.54
# Server OS:                    pc-linux-gnu
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-03-08 10:22:01
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
# Dumping data for table aktrionl_logistics.auditlogin: 36 rows
DELETE FROM `auditlogin`;
/*!40000 ALTER TABLE `auditlogin` DISABLE KEYS */;
INSERT INTO `auditlogin` (`AuditLoginId`, `Action`, `LoginId`, `UserName`, `Browser`, `RemoteIp`, `DateCreated`) VALUES
	(1, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-04 18:15:29'),
	(2, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-04 18:16:44'),
	(3, 'login', 7, 'supplier1', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-04 18:16:49'),
	(4, 'logout', 7, 'supplier1', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-04 18:17:03'),
	(5, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-04 18:23:04'),
	(6, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-04 18:23:45'),
	(7, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-04 18:24:30'),
	(8, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 12:45:09'),
	(9, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13', '81.136.179.170', '2011-03-07 12:49:32'),
	(10, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 13:20:35'),
	(11, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 13:23:25'),
	(12, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13', '81.136.179.170', '2011-03-07 13:23:45'),
	(13, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13', '81.136.179.170', '2011-03-07 13:23:55'),
	(14, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13', '81.136.179.170', '2011-03-07 13:33:55'),
	(15, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13', '81.136.179.170', '2011-03-07 13:34:13'),
	(16, 'login', 1, 'admin', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/4.0; GTB6.3; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.5.30729; .NET4.0C; .NET CLR 3.0.30729)', '77.98.101.187', '2011-03-07 13:40:35'),
	(17, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13', '81.136.179.170', '2011-03-07 13:51:09'),
	(18, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13', '81.136.179.170', '2011-03-07 13:54:00'),
	(19, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13', '81.136.179.170', '2011-03-07 14:09:52'),
	(20, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13', '81.136.179.170', '2011-03-07 14:10:00'),
	(21, 'login', 1, 'admin', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0b12) Gecko/20100101 Firefox/4.0b12', '81.136.179.170', '2011-03-07 14:11:52'),
	(22, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0b12) Gecko/20100101 Firefox/4.0b12', '81.136.179.170', '2011-03-07 14:14:22'),
	(23, 'login', 7, 'supplier1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0b12) Gecko/20100101 Firefox/4.0b12', '81.136.179.170', '2011-03-07 14:14:27'),
	(24, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 16:53:20'),
	(25, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 17:24:30'),
	(26, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 17:25:44'),
	(27, 'login', 7, 'supplier1', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 17:25:50'),
	(28, 'logout', 7, 'supplier1', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 17:27:09'),
	(29, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 17:33:29'),
	(30, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 17:37:56'),
	(31, 'login', 7, 'supplier1', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 17:38:02'),
	(32, 'logout', 7, 'supplier1', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 17:38:55'),
	(33, 'login', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 17:39:00'),
	(34, 'login', 1, 'admin ', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/4.0; GTB6.3; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.5.30729; .NET4.0C; .NET CLR 3.0.30729)', '77.98.101.187', '2011-03-07 18:40:31'),
	(35, 'login', 1, 'admin', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/4.0; GTB6.3; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.5.30729; .NET4.0C; .NET CLR 3.0.30729)', '77.98.101.187', '2011-03-07 18:40:48'),
	(36, 'logout', 1, 'admin', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.14) Gecko/20110218 Firefox/3.6.14', '87.223.44.162', '2011-03-07 20:53:00');
/*!40000 ALTER TABLE `auditlogin` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.client: 1 rows
DELETE FROM `client`;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` (`ClientId`, `Name`, `Address`, `ContactPerson`, `TelephoneNumber`, `DateCreated`, `DateUpdated`) VALUES
	(1, 'Newsquest', NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `client` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.clientlogin: 7 rows
DELETE FROM `clientlogin`;
/*!40000 ALTER TABLE `clientlogin` DISABLE KEYS */;
INSERT INTO `clientlogin` (`ClientLoginId`, `LoginId`, `ClientId`, `DateUpdated`) VALUES
	(3, 8, 1, '2010-12-01 12:57:15'),
	(4, 9, 1, '2010-12-01 12:57:52'),
	(5, 10, 1, '2010-12-01 12:58:17'),
	(6, 11, 1, '2010-12-01 12:58:48'),
	(7, 12, 1, '2010-12-01 12:59:12'),
	(8, 13, 1, '2010-12-01 12:59:40'),
	(9, 14, 1, '2010-12-01 13:00:10');
/*!40000 ALTER TABLE `clientlogin` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.deliverypoint: 7 rows
DELETE FROM `deliverypoint`;
/*!40000 ALTER TABLE `deliverypoint` DISABLE KEYS */;
INSERT INTO `deliverypoint` (`DeliveryPointId`, `AccountNumber`, `Name`, `Address`, `PostalCode`, `TelephoneNumber`, `County`, `DeliveryComments`, `DateCreated`, `DateUpdated`, `UpdatedBy`) VALUES
	(1, '203', '89 ALLECTUS WAY', '89 ALLECTUS WAY', 'CM8 1NY', '01376 520537', 'WITHAM', '', '2011-03-04 19:02:18', '2011-03-04 19:02:18', 'admin'),
	(2, '204', '1 SILVER STREET', '1 SILVER STREET', 'CM8 3QQ', '01376 583725', 'SILVER END', '', '2011-03-04 19:02:18', '2011-03-04 19:02:18', 'admin'),
	(3, '205', '38 VERNON WAY', '38 VERNON WAY', 'CM7 9TU', '01376 342186', 'BRAINTREE', '', '2011-03-04 19:02:18', '2011-03-04 19:02:18', 'admin'),
	(4, 'OFFICE COPIES', 'WAREHOUSE BASILDON', '', '', '01268 469370', '', '', '2011-03-04 19:02:18', '2011-03-04 19:02:18', 'admin'),
	(5, '040', '44 HANGING HILL LANE', '44 HANGING HILL LANE', 'CM13 2HY', '', 'HUTTON', '', '2011-03-04 19:02:18', '2011-03-04 19:02:18', 'admin'),
	(6, '041', '64 BYRON ROAD', '64 BYRON ROAD', 'CM13 2SA', '', 'HUTTON', '', '2011-03-04 19:02:18', '2011-03-04 19:02:18', 'admin'),
	(7, '042', '23 ELEANOR WAY', '23 ELEANOR WAY', 'CM14 5AQ', '', 'WARLEY', '', '2011-03-04 19:02:18', '2011-03-04 19:02:18', 'admin');
/*!40000 ALTER TABLE `deliverypoint` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.login: 10 rows
DELETE FROM `login`;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` (`LoginId`, `UserName`, `FriendlyName`, `Password`, `LoginRoleId`, `IsActive`, `DateCreated`, `DateUpdated`, `UpdatedBy`) VALUES
	(1, 'admin', 'Site Administrator', '1234', 1, 1, '2010-10-26 20:03:07', '2010-11-12 14:06:00', 'admin'),
	(7, 'supplier1', 'TAG', '1234', 3, 1, '2010-10-28 19:01:06', '2011-03-04 19:10:04', 'admin'),
	(8, 'newsquest-nw', 'Newsquest (North West) (05)', '1234', 2, 1, '2010-10-28 19:03:01', '2010-12-01 12:57:15', 'admin'),
	(9, 'newsquest-yne', 'Newsquest (Yorks and NE) (01)', '1234', 2, 1, '2010-12-01 12:57:52', '2010-12-01 12:57:52', 'admin'),
	(10, 'newsquest-essex', 'Newsquest Essex (21)', '1234', 2, 1, '2010-12-01 12:58:17', '2010-12-01 12:58:17', 'admin'),
	(11, 'newsquest-london', 'Newsquest London (23)', '1234', 2, 1, '2010-12-01 12:58:48', '2010-12-01 12:58:48', 'admin'),
	(12, 'newsquest-mls', 'Newsquest Midlands South (12)', '1234', 2, 1, '2010-12-01 12:59:12', '2010-12-01 12:59:12', 'admin'),
	(13, 'newsquest-s', 'Newsquest Southern (25)', '1234', 2, 1, '2010-12-01 12:59:40', '2010-12-01 12:59:40', 'admin'),
	(14, 'newsquest-wsw', 'Newsquest Wales and South West (14)', '1234', 2, 1, '2010-12-01 13:00:10', '2010-12-01 13:00:10', 'admin'),
	(15, 'supplier2', 'Boyes', '1234', 3, 1, '2011-03-01 14:37:08', '2011-03-01 14:37:08', 'admin');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.loginrole: 3 rows
DELETE FROM `loginrole`;
/*!40000 ALTER TABLE `loginrole` DISABLE KEYS */;
INSERT INTO `loginrole` (`LoginRoleId`, `Description`, `HomeUrl`) VALUES
	(1, 'Administrator', 'admin/index'),
	(2, 'Client', 'client/titles'),
	(3, 'Supplier', 'supplier/routes');
/*!40000 ALTER TABLE `loginrole` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.order: 1 rows
DELETE FROM `order`;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` (`OrderId`, `TitleId`, `RouteId`, `Pagination`, `BundleSize`, `PublicationDate`, `DeliveryDate`, `Status`, `DateCreated`, `DateUpdated`, `UpdatedBy`) VALUES
	(1, 21, 4, 25, 100, '02/03/2011', '02/03/2011', 'SUBMITTED - PRINTED', '2011-03-07 17:37:36', '2011-03-07 17:37:36', 'admin');
/*!40000 ALTER TABLE `order` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.orderdetails: 2 rows
DELETE FROM `orderdetails`;
/*!40000 ALTER TABLE `orderdetails` DISABLE KEYS */;
INSERT INTO `orderdetails` (`OrderDetailsId`, `OrderId`, `Sequence`, `DeliveryPointId`, `Copies`) VALUES
	(1, 1, 1, 1, 1524),
	(2, 1, 2, 6, 823);
/*!40000 ALTER TABLE `orderdetails` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.orderhistory: 0 rows
DELETE FROM `orderhistory`;
/*!40000 ALTER TABLE `orderhistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `orderhistory` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.printcentre: 6 rows
DELETE FROM `printcentre`;
/*!40000 ALTER TABLE `printcentre` DISABLE KEYS */;
INSERT INTO `printcentre` (`PrintCentreId`, `Name`, `Address`, `DateCreated`, `DateUpdated`, `UpdatedBy`) VALUES
	(4, 'Oxford', 'Osney Mead\r\nOxford\r\nOX2 0EJ', '2010-10-29 17:56:17', '2010-10-29 17:56:17', NULL),
	(5, 'Southampton', 'Newspaper House\r\nTest Lane\r\nRedbridge\r\nSouthampton\r\nSO16 9JX', '2010-11-01 12:38:48', '2010-11-12 13:41:47', 'admin'),
	(6, 'Worcester', 'Berrows House\r\nHylton Road\r\nWorcester\r\nWR2 5JX', '2010-12-01 12:42:28', NULL, NULL),
	(7, 'Weymouth', 'Fleet House\r\nChesil Fields\r\nHampshire Road\r\nWeymouth\r\nDT4 9XD', '2010-12-01 12:42:30', NULL, NULL),
	(8, 'Glasgow', '200 Renfield Street\r\nGlasgow\r\nScotland\r\nG2 3QB', '2010-12-01 12:42:31', NULL, NULL),
	(9, 'Mold North Wales', 'Mold Business Park\r\nMold\r\nFlintshire\r\nCH7 1XY', NULL, NULL, NULL);
/*!40000 ALTER TABLE `printcentre` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.route: 4 rows
DELETE FROM `route`;
/*!40000 ALTER TABLE `route` DISABLE KEYS */;
INSERT INTO `route` (`RouteId`, `SupplierId`, `Name`, `ShowDetailed`, `DateCreated`, `DateUpdated`, `UpdatedBy`) VALUES
	(1, 1, 'Brentwood & Braintree Weekly News', 1, '2011-03-04 19:06:21', '2011-03-04 19:06:21', 'admin'),
	(2, 1, 'Lewisham News Shopper', 1, '2011-03-07 13:59:33', '2011-03-07 13:59:33', 'admin'),
	(3, 1, 'Echo Monday', 1, '2011-03-07 17:34:40', '2011-03-07 17:34:40', 'admin'),
	(4, 1, 'Basildon Recorder', 0, '2011-03-07 17:35:43', '2011-03-07 17:35:43', 'admin');
/*!40000 ALTER TABLE `route` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.routedetails: 15 rows
DELETE FROM `routedetails`;
/*!40000 ALTER TABLE `routedetails` DISABLE KEYS */;
INSERT INTO `routedetails` (`RouteId`, `TitleId`, `DeliveryPointId`) VALUES
	(1, 28, 1),
	(1, 28, 2),
	(1, 28, 3),
	(1, 28, 4),
	(1, 29, 4),
	(1, 29, 5),
	(1, 29, 6),
	(1, 29, 7),
	(2, 8, 2),
	(2, 8, 7),
	(3, 2, 2),
	(3, 2, 5),
	(3, 2, 7),
	(4, 21, 1),
	(4, 21, 6);
/*!40000 ALTER TABLE `routedetails` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.routeinstance: 1 rows
DELETE FROM `routeinstance`;
/*!40000 ALTER TABLE `routeinstance` DISABLE KEYS */;
INSERT INTO `routeinstance` (`RouteInstanceId`, `RouteId`, `SupplierId`, `VehicleId`, `Date`, `DepartureTime`, `Status`, `DateCreated`, `UpdatedBy`, `DateDetailsCreated`, `DetailsCreatedBy`) VALUES
	(1, 4, 1, 18, '02/03/2011', '01:25', 'ARCHIVED', '2011-03-07 17:37:36', 'admin', NULL, NULL);
/*!40000 ALTER TABLE `routeinstance` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.routeinstancedetails: 2 rows
DELETE FROM `routeinstancedetails`;
/*!40000 ALTER TABLE `routeinstancedetails` DISABLE KEYS */;
INSERT INTO `routeinstancedetails` (`RouteInstanceDetailsId`, `RouteInstanceId`, `OrderDetailsId`, `DeliveryTime`, `PalletsCollected`, `PalletsDelivered`) VALUES
	(1, 1, 1, NULL, NULL, NULL),
	(2, 1, 2, '03:52', 14, 15);
/*!40000 ALTER TABLE `routeinstancedetails` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.routeinstanceorder: 1 rows
DELETE FROM `routeinstanceorder`;
/*!40000 ALTER TABLE `routeinstanceorder` DISABLE KEYS */;
INSERT INTO `routeinstanceorder` (`RouteInstanceOrderId`, `RouteInstanceId`, `OrderId`) VALUES
	(1, 1, 1);
/*!40000 ALTER TABLE `routeinstanceorder` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.supplier: 18 rows
DELETE FROM `supplier`;
/*!40000 ALTER TABLE `supplier` DISABLE KEYS */;
INSERT INTO `supplier` (`SupplierId`, `DefaultVehicleId`, `Name`, `ContactPerson`, `TelephoneNumber`, `DateCreated`, `DateUpdated`, `UpdatedBy`) VALUES
	(1, 18, 'TAG', 'Geoff Phipps', '0208 6802404', NULL, '2011-03-04 18:00:00', 'admin'),
	(2, 5, 'Brice', 'Adrian Brice', '01239 654446', NULL, '2011-03-04 17:57:32', 'admin'),
	(3, 6, 'Crowndell', 'Martyn Kenyon', '01905 756288', NULL, '2011-03-04 17:57:47', 'admin'),
	(4, 7, 'Dash', 'Steven Dash', '01209 313130', NULL, '2011-03-04 17:57:55', 'admin'),
	(5, 17, 'Smiths News', 'Tom Rodger', '07730 303883', NULL, '2011-03-04 17:59:52', 'admin'),
	(6, 16, 'Roberts', 'Stephen Roberts', '02380 613778', NULL, '2011-03-04 17:59:45', 'admin'),
	(7, 10, 'Farebrothers', 'Brian Farebrother', '0151 342 8197', NULL, '2011-03-04 17:58:17', 'admin'),
	(8, 13, 'Menzies', 'Robert Sproull', '0141 643 3408', NULL, '2011-03-04 17:59:19', 'admin'),
	(9, 15, 'Parkside', 'Chris Mines', '07540 707122', NULL, '2011-03-04 17:59:33', 'admin'),
	(10, 9, 'ESL', 'Will Irlam', '01925 605400', NULL, '2011-03-04 17:58:11', 'admin'),
	(11, 20, 'Whites Logistics', 'Darren Lothian', '01386 552200', NULL, '2011-03-04 18:00:13', 'admin'),
	(12, 12, 'LT Baynham', 'Grant Baynham', '01432 273298', NULL, '2011-03-04 17:59:09', 'admin'),
	(13, 19, 'TWE Transport', 'Terry', '01295 262299', NULL, '2011-03-04 18:00:06', 'admin'),
	(14, 11, 'Intercounty Distribution', 'Nick Coombes', '01566 772476', NULL, '2011-03-04 17:59:03', 'admin'),
	(15, 8, 'EDG Group Ltd', 'Darren Edghill', '01268 288 432', NULL, '2011-03-04 17:58:03', 'admin'),
	(16, 14, 'Oakwood Transport Dist', 'Damien Winstanley', '0207 474 2686       ', NULL, '2011-03-04 17:59:25', 'admin'),
	(17, 4, 'Boyes Conning            ', 'Mark Stone', '02380 601 224       ', NULL, '2011-03-04 17:57:26', 'admin'),
	(18, 3, 'AAA', 'Jay', '07980 136024', NULL, '2011-02-24 13:52:18', 'admin');
/*!40000 ALTER TABLE `supplier` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.supplierlogin: 2 rows
DELETE FROM `supplierlogin`;
/*!40000 ALTER TABLE `supplierlogin` DISABLE KEYS */;
INSERT INTO `supplierlogin` (`SupplierLoginId`, `LoginId`, `SupplierId`, `DateUpdated`) VALUES
	(3, 7, 1, '2011-03-04 19:10:04'),
	(4, 15, 17, '2011-03-01 14:37:08');
/*!40000 ALTER TABLE `supplierlogin` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.title: 44 rows
DELETE FROM `title`;
/*!40000 ALTER TABLE `title` DISABLE KEYS */;
INSERT INTO `title` (`TitleId`, `ClientLoginId`, `PrintCentreId`, `Name`, `PrintDay`, `OffPressTime`, `WeightPerPage`, `DateCreated`, `DateUpdated`, `UpdatedBy`) VALUES
	(1, 3, 4, 'Gazette-Mon', '0', '23:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(2, 3, 4, 'Echo-Mon', '0', '23:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(3, 3, 4, 'Colchester Weekly News', '1', '20:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(4, 3, 4, 'Gazette-Tue', '1', '23:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(5, 3, 4, 'Echo-Tue', '1', '23:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(6, 3, 4, 'Nth Kent Shopper', '2', '13:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(7, 3, 4, 'S-end&Castlepoint Std', '2', '5:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(8, 3, 4, 'Lewisham News Shopper', '2', '1:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(9, 3, 4, 'W.Telegraph Property', '2', '15:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(10, 3, 4, 'South Wales Guardian', '2', '15:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(11, 3, 4, 'Western Telegraph', '2', '15:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(12, 3, 4, 'St Albans Review(& Welwyn)', '2', '16:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(13, 3, 4, 'Maldon & Burnham Standard', '2', '18:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(14, 3, 4, 'Braintree & Witham Times', '2', '18:45', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(15, 3, 4, 'Stroud News & Journal', '2', '20:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(16, 3, 4, 'Echo - Wed', '2', '23:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(17, 3, 4, 'Gazette - Wed', '2', '23:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(18, 3, 4, 'Redditch Advertiser', '3', '7:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(19, 3, 4, 'Bromsgrove Advertiser', '3', '7:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(20, 3, 4, 'Wilts & Gloucester Standard', '3', '13:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(21, 3, 4, 'Basildon Recorder', '3', '7:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(22, 3, 4, 'Thurrock Gazette ', '3', '13:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(23, 3, 4, 'Burnham & Highbridge Weekly News', '3', '9:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(24, 3, 4, 'Somerset County Gazette', '3', '18:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(25, 3, 4, 'Hereford Times', '3', '17:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(26, 3, 4, 'Gazette - Thu', '3', '23:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(27, 3, 4, 'Echo - Thu', '3', '23:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(28, 3, 4, 'Braintree & Witham W. News', '4', '7:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(29, 3, 4, 'Brentwood Weekly News', '4', '7:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(30, 3, 4, 'Chelmsford Weekly News', '4', '7:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(31, 3, 4, 'Harrow Times', '4', '6:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(32, 3, 4, 'Stourbridge News', '4', '9:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(33, 3, 4, 'Halstead Gazette', '4', '15:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(34, 3, 4, 'Essex County Standard', '4', '16:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(35, 3, 4, 'Harwich & Manningtree Std', '4', '16:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(36, 3, 4, 'Haringey Independent', '4', '17:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(37, 3, 4, 'Waltham & Epping Ind.', '4', '18:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(38, 3, 4, 'Richmond & Twik.Times', '4', '20:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(39, 3, 4, 'Watford Observer', '4', '20:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(40, 3, 4, 'Bucks Free Press', '4', '21:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(41, 3, 4, 'Gazette - Fri', '4', '23:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(42, 3, 4, 'Echo - Fri', '4', '23:00', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(43, 3, 4, 'Worcester News - Sat', '5', '23:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin'),
	(44, 3, 4, 'South Wales Argus - Sat', '5', '23:30', '27.5', '2011-03-04 18:42:24', '2011-03-04 18:42:24', 'admin');
/*!40000 ALTER TABLE `title` ENABLE KEYS */;

# Dumping data for table aktrionl_logistics.vehicle: 18 rows
DELETE FROM `vehicle`;
/*!40000 ALTER TABLE `vehicle` DISABLE KEYS */;
INSERT INTO `vehicle` (`VehicleId`, `SupplierId`, `Description`, `Capacity`) VALUES
	(4, 17, 'Van', 25000),
	(3, 18, 'Van', 25000),
	(5, 2, 'Van', 25000),
	(6, 3, 'Van', 25000),
	(7, 4, 'Van', 25000),
	(8, 15, 'Van', 25000),
	(9, 10, 'Van', 25000),
	(10, 7, 'Van', 25000),
	(11, 14, 'Van', 25000),
	(12, 12, 'Van', 25000),
	(13, 8, 'Van', 25000),
	(14, 16, 'Van', 25000),
	(15, 9, 'Van', 25000),
	(16, 6, 'Van', 25000),
	(17, 5, 'Van', 25000),
	(18, 1, 'Van', 25000),
	(19, 13, 'Van', 25000),
	(20, 11, 'Van', 25000);
/*!40000 ALTER TABLE `vehicle` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
