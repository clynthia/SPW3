-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2014 at 07:47 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `senior_project_website`
--

-- --------------------------------------------------------

--
-- Table structure for table `spw_milestones`
--

CREATE TABLE IF NOT EXISTS `spw_milestones` (
  `milestone_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `milestone_name` varchar(140) CHARACTER SET latin1 NOT NULL,
  `path_to_folder` varchar(100) CHARACTER SET latin1 NOT NULL,
  `due_date` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`milestone_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `spw_milestones`
--

INSERT INTO `spw_milestones` (`milestone_id`, `milestone_name`, `path_to_folder`, `due_date`) VALUES
(32, 'Requirements Analysis', './milestones/Requirements_Analysis/', '2014-03-12'),
(33, 'Poster Presentation', './milestones/Poster_Presentation/', '2014-05-09'),
(35, 'Feasibility Study', './milestones/Feasibility_Study/', '2014-03-11'),
(36, 'Final Presentation', './milestones/Final_Presentation/', '2014-09-05'),
(37, 'Design Document', './milestones/Design_Document/', '2014-03-11');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
