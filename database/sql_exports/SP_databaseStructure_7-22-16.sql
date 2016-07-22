CREATE DATABASE  IF NOT EXISTS `gaig_users` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `gaig_users`;
-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: localhost    Database: gaig_users
-- ------------------------------------------------------
-- Server version	5.6.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `default_emailsettings`
--

DROP TABLE IF EXISTS `default_emailsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `default_emailsettings` (
  `DFT_UserId` int(11) NOT NULL,
  `DFT_MailServer` text COLLATE utf8_unicode_ci NOT NULL,
  `DFT_MailPort` text COLLATE utf8_unicode_ci NOT NULL,
  `DFT_Username` text COLLATE utf8_unicode_ci NOT NULL,
  `DFT_CompanyName` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`DFT_UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_tracking`
--

DROP TABLE IF EXISTS `email_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_tracking` (
  `EML_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `EML_Ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `EML_Host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `EML_Username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `EML_ProjectName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `EML_AccessTimestamp` datetime NOT NULL,
  PRIMARY KEY (`EML_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `PRJ_ProjectId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PRJ_ProjectName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `PRJ_ComplexityType` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `PRJ_TargetType` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `PRJ_ProjectAssignee` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `PRJ_ProjectStart` date NOT NULL,
  `PRJ_ProjectLastActive` date NOT NULL,
  `PRJ_ProjectStatus` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `PRJ_ProjectTotalUsers` int(11) NOT NULL,
  `PRJ_EmailViews` int(11) NOT NULL,
  `PRJ_WebsiteViews` int(11) NOT NULL,
  `PRJ_ProjectTotalReports` int(11) NOT NULL,
  PRIMARY KEY (`PRJ_ProjectId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `RPT_ReportId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RPT_EmailSubject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `RPT_UserEmail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `RPT_OriginalFrom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `RPT_ReportDate` date NOT NULL,
  PRIMARY KEY (`RPT_ReportId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sent_email`
--

DROP TABLE IF EXISTS `sent_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sent_email` (
  `SML_EmailId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SML_UserId` int(11) NOT NULL,
  `SML_ProjectName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SML_SentTimestamp` datetime NOT NULL,
  PRIMARY KEY (`SML_EmailId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `USR_UserId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `USR_Username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `USR_Email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `USR_FirstName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `USR_LastName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `USR_UniqueURLId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `USR_Password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `USR_ProjectMostRecent` int(11) DEFAULT NULL,
  `USR_ProjectPrevious` int(11) DEFAULT NULL,
  `USR_ProjectLast` int(11) DEFAULT NULL,
  PRIMARY KEY (`USR_UserId`),
  UNIQUE KEY `users_usr_username_unique` (`USR_Username`),
  UNIQUE KEY `users_usr_email_unique` (`USR_Email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_tracking`
--

DROP TABLE IF EXISTS `website_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_tracking` (
  `WBS_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `WBS_Ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `WBS_Host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `WBS_BrowserAgent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `WBS_ReqPath` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `WBS_Username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `WBS_ProjectName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `WBS_AccessTimestamp` datetime NOT NULL,
  PRIMARY KEY (`WBS_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-22 13:19:50
