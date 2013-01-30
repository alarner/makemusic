-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: custsql-pow01
-- Generation Time: Jan 29, 2013 at 08:40 PM
-- Server version: 5.0.91
-- PHP Version: 4.4.9
-- 
-- Database: `matchmaker`
-- 
CREATE DATABASE `matchmaker` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `matchmaker`;

-- --------------------------------------------------------

-- 
-- Table structure for table `artists`
-- 

CREATE TABLE `artists` (
  `id` int(10) NOT NULL auto_increment,
  `groupname` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `promote` text NOT NULL,
  `electricity` varchar(1) NOT NULL,
  `time_midday` tinyint(1) NOT NULL,
  `time_afternoon` tinyint(1) NOT NULL,
  `time_lateafter` tinyint(1) NOT NULL,
  `time_evening` tinyint(1) NOT NULL,
  `perform_id` int(10) NOT NULL COMMENT 'N-newsletter, A-artist, V-venue',
  `last_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `user_id` int(10) NOT NULL,
  `blues` tinyint(1) NOT NULL,
  `cabaret` tinyint(1) NOT NULL,
  `classical` tinyint(1) NOT NULL,
  `electronic` tinyint(1) NOT NULL,
  `experimental` tinyint(1) NOT NULL,
  `country` tinyint(1) NOT NULL,
  `folk` tinyint(1) NOT NULL,
  `hiphop` tinyint(1) NOT NULL,
  `jazz` tinyint(1) NOT NULL,
  `kids` tinyint(1) NOT NULL,
  `latin` tinyint(1) NOT NULL,
  `opera` tinyint(1) NOT NULL,
  `pop` tinyint(1) NOT NULL,
  `reggae` tinyint(1) NOT NULL,
  `religious` tinyint(1) NOT NULL,
  `rock` tinyint(1) NOT NULL,
  `soul` tinyint(1) NOT NULL,
  `standards` tinyint(1) NOT NULL,
  `world` tinyint(1) NOT NULL,
  `other` tinyint(1) NOT NULL,
  `myspace` varchar(255) NOT NULL,
  `lca` tinyint(1) NOT NULL,
  `genre1` varchar(255) NOT NULL,
  `genre2` varchar(255) NOT NULL,
  `genre3` varchar(255) NOT NULL,
  `previousVenues` varchar(512) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `emailaddress` (`groupname`),
  FULLTEXT KEY `groupname` (`groupname`)
) ENGINE=MyISAM AUTO_INCREMENT=1351 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1351 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `cake_sessions`
-- 

CREATE TABLE `cake_sessions` (
  `id` varchar(255) NOT NULL default '',
  `data` text,
  `expires` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `locations`
-- 

CREATE TABLE `locations` (
  `id` int(10) NOT NULL auto_increment,
  `acl` tinyint(1) NOT NULL,
  `type` varchar(255) NOT NULL,
  `locationname` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `street_add1` varchar(255) NOT NULL,
  `street_add2` varchar(255) NOT NULL,
  `city` varchar(40) NOT NULL,
  `hood` varchar(40) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `ph_primary` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `promote` text NOT NULL,
  `electricity` varchar(1) NOT NULL,
  `time_midday` tinyint(1) NOT NULL,
  `time_afternoon` tinyint(1) NOT NULL,
  `time_lateafter` tinyint(1) NOT NULL,
  `time_evening` tinyint(1) NOT NULL,
  `user_id` int(10) NOT NULL,
  `last_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `surrounding` tinyint(1) NOT NULL,
  `blues` tinyint(1) NOT NULL,
  `cabaret` tinyint(1) NOT NULL,
  `classical` tinyint(1) NOT NULL,
  `country` tinyint(1) NOT NULL,
  `electronic` tinyint(1) NOT NULL,
  `experimental` tinyint(1) NOT NULL,
  `folk` tinyint(1) NOT NULL,
  `hiphop` tinyint(1) NOT NULL,
  `jazz` tinyint(1) NOT NULL,
  `kids` tinyint(1) NOT NULL,
  `latin` tinyint(1) NOT NULL,
  `opera` tinyint(1) NOT NULL,
  `pop` tinyint(1) NOT NULL,
  `reggae` tinyint(1) NOT NULL,
  `religious` tinyint(1) NOT NULL,
  `rock` tinyint(1) NOT NULL,
  `soul` tinyint(1) NOT NULL,
  `standards` tinyint(1) NOT NULL,
  `world` tinyint(1) NOT NULL,
  `other` tinyint(1) NOT NULL,
  `rain` varchar(1) NOT NULL,
  `rain_other` text NOT NULL,
  `poster` tinyint(1) default NULL,
  `onsite` tinyint(1) NOT NULL,
  `onsite_name` varchar(40) NOT NULL,
  `onsite_phone` varchar(20) NOT NULL,
  `genre1` varchar(255) NOT NULL,
  `genre2` varchar(255) NOT NULL,
  `genre3` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `emailaddress` (`locationname`)
) ENGINE=MyISAM AUTO_INCREMENT=531 DEFAULT CHARSET=utf8 AUTO_INCREMENT=531 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `performances`
-- 

CREATE TABLE `performances` (
  `id` int(10) NOT NULL auto_increment,
  `artist_id` int(10) NOT NULL,
  `location_id` int(10) NOT NULL COMMENT 'N-newsletter, A-artist, V-venue',
  `artist_confirmed` tinyint(1) NOT NULL,
  `location_confirmed` tinyint(1) NOT NULL,
  `admin_confirmed` tinyint(1) NOT NULL,
  `start_time` varchar(10) NOT NULL,
  `end_time` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `artist_notes` text NOT NULL,
  `location_notes` text NOT NULL,
  `admin_notes` text NOT NULL,
  `last_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `acl` tinyint(1) NOT NULL,
  `lca` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3058 DEFAULT CHARSET=utf8 AUTO_INCREMENT=3058 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(10) NOT NULL auto_increment,
  `status` tinyint(1) NOT NULL COMMENT '1-confirmed 0-unconfirmed',
  `newsletter_status` tinyint(1) NOT NULL COMMENT '1-confirmed 0-unconfirmed',
  `user_type` smallint(3) NOT NULL COMMENT '140-newsletter subscriber only, 160-artist, 180-location, 220-admin',
  `initial_interest` varchar(1) NOT NULL COMMENT 'do not use',
  `emailaddress` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(40) NOT NULL,
  `last_name` varchar(40) NOT NULL,
  `street_add1` varchar(255) NOT NULL,
  `street_add2` varchar(255) NOT NULL,
  `city` varchar(40) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip_code` varchar(20) NOT NULL COMMENT 'N-newsletter, A-artist, V-venue',
  `ph_primary` varchar(20) default NULL COMMENT 'example: 5555551212',
  `ph_mobile` varchar(20) NOT NULL COMMENT 'example: 5555551212',
  `last_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `rand` varchar(10) NOT NULL COMMENT 'random number used to generate a hash',
  `hash` varchar(255) NOT NULL COMMENT 'generated from email address/rand/zipcode',
  `contact` varchar(1) NOT NULL COMMENT 'E-email, P-phone, B-both',
  `salutation` varchar(5) NOT NULL COMMENT 'Mr, Mrs, Ms (no periods)',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `emailaddress` (`emailaddress`)
) ENGINE=MyISAM AUTO_INCREMENT=49332 DEFAULT CHARSET=utf8 AUTO_INCREMENT=49332 ;
