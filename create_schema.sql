-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Hostiteľ: localhost
-- Vygenerované: Po 22.Dec 2014, 16:27
-- Verzia serveru: 5.5.40-0ubuntu0.14.04.1
-- Verzia PHP: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

SET foreign_key_checks = 0;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_admin_users`
--

CREATE TABLE IF NOT EXISTS `cms_admin_users` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `encryption_key` char(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `permissions` text NOT NULL,
  `last_login` int(9) NOT NULL,
  `registration_time` int(9) NOT NULL,
  `cookie_login` tinyint(1) NOT NULL DEFAULT '0',
  `lang` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Sťahujem dáta pre tabuľku `cms_admin_users`
--

INSERT INTO `cms_admin_users` (`id`, `order`, `encryption_key`, `name`, `password`, `permissions`, `last_login`, `registration_time`, `cookie_login`, `lang`) VALUES
(1, 1, 'udXXJN6TR0Ag4qE6maNAmmrG3Hb2YO', 'admin', '$2a$08$gKcjVeryGqi06muZW8.d6.NK77nvRp2CK.gXOGrbzHgxesXpWkZaK', 'a:1:{i:0;s:1:"*";}', 1419261970, 1349449901, 1, 'sk');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_categories`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `parent_id` smallint(5) unsigned DEFAULT NULL,
  `lastmod` int(9) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `tpl` varchar(255) DEFAULT NULL,
  `changefreq` varchar(255) NOT NULL,
  `index` tinyint(1) NOT NULL DEFAULT '1',
  `sitemap_priority` decimal(2,1) NOT NULL DEFAULT '0.5',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_categories_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_categories_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `image` text,
  `description` text,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_communications`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_communications` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_communications_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_communications_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_coupons`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_coupons` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `code` char(6) NOT NULL,
  `count` smallint(5) unsigned NOT NULL,
  `discount` decimal(5,2) unsigned NOT NULL,
  `time_from` bigint(20) unsigned DEFAULT NULL,
  `time_to` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_coupons_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_coupons_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_currencies`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_currencies` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `course` double unsigned NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `decimals` tinyint(3) unsigned NOT NULL,
  `round` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Sťahujem dáta pre tabuľku `cms_eshop_currencies`
--

INSERT INTO `cms_eshop_currencies` (`id`, `order`, `name`, `course`, `symbol`, `decimals`, `round`) VALUES
(1, 1, 'EUR', 1, '€', 2, 2);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_customers`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_customers` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `regtime` int(9) NOT NULL,
  `encryption_key` char(30) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `ico` decimal(8,0) DEFAULT NULL,
  `dic` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `psc` varchar(10) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `customer_group_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_group_id` (`customer_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_customer_groups`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_customer_groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `coef` decimal(5,2) unsigned NOT NULL DEFAULT '1.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_customer_groups_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_customer_groups_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_distributors`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_distributors` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_manufacturers`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_manufacturers` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_orders`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `time` int(9) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `ico` decimal(8,0) DEFAULT NULL,
  `dic` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `psc` varchar(10) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `order_state_id` smallint(5) unsigned NOT NULL,
  `transport_name` varchar(255) NOT NULL,
  `transport_price` decimal(16,5) unsigned NOT NULL DEFAULT '0.00000',
  `payment_name` varchar(255) NOT NULL,
  `payment_price` decimal(16,5) unsigned NOT NULL DEFAULT '0.00000',
  `communication` varchar(255) NOT NULL,
  `coupon_name` varchar(255) DEFAULT NULL,
  `coupon_discount` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `no_invoice` tinyint(1) NOT NULL DEFAULT '0',
  `message` text,
  `currency_name` varchar(255) NOT NULL,
  `currency_course` double unsigned NOT NULL,
  `currency_symbol` varchar(10) NOT NULL,
  `currency_decimals` tinyint(3) unsigned NOT NULL,
  `currency_round` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_order_data`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_order_data` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(16,5) unsigned NOT NULL DEFAULT '0.00000',
  `quantity` smallint(5) unsigned NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `ean` varchar(50) DEFAULT NULL,
  `tax` decimal(5,2) NOT NULL,
  `manufacturer` varchar(255) DEFAULT NULL,
  `distributor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_order_states`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_order_states` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(7) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_order_states_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_order_states_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `email_send` tinyint(1) NOT NULL DEFAULT '0',
  `email_subject` varchar(255) DEFAULT NULL,
  `email_content` text,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_payments`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_payments` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `transport_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(16,5) unsigned NOT NULL,
  `price_free` decimal(16,5) unsigned NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  KEY `transport_id` (`transport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_products`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_products` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `lastmod` int(9) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `tpl` varchar(255) DEFAULT NULL,
  `changefreq` varchar(255) NOT NULL,
  `index` tinyint(1) NOT NULL DEFAULT '1',
  `sitemap_priority` decimal(2,1) NOT NULL DEFAULT '0.5',
  `sku` varchar(50) DEFAULT NULL,
  `ean` varchar(50) DEFAULT NULL,
  `quantity` smallint(5) unsigned NOT NULL,
  `tax_id` smallint(5) unsigned NOT NULL,
  `manufacturer_id` smallint(5) unsigned DEFAULT NULL,
  `distributor_id` smallint(5) unsigned DEFAULT NULL,
  `product_parameter_group_id` smallint(5) unsigned DEFAULT NULL,
  `product_type_id` smallint(5) unsigned DEFAULT NULL,
  `product_gallery_id` smallint(5) unsigned DEFAULT NULL,
  `image` text,
  PRIMARY KEY (`id`),
  KEY `tax_id` (`tax_id`),
  KEY `manufacturer_id` (`manufacturer_id`),
  KEY `distributor_id` (`distributor_id`),
  KEY `product_parameter_group_id` (`product_parameter_group_id`),
  KEY `product_type_id` (`product_type_id`),
  KEY `product_gallery_id` (`product_gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_products_in_categories`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_products_in_categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `category_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_products_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_products_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `price` decimal(16,5) unsigned NOT NULL DEFAULT '0.00000',
  `description` text,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_products_signs`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_products_signs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `sign_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `sign_id` (`sign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_galleries`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_galleries` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `primary_image_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `primary_image_id` (`primary_image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_galleries_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_galleries_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_gallery_images`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_gallery_images` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `product_gallery_id` smallint(5) unsigned NOT NULL,
  `url` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_gallery_id` (`product_gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_gallery_images_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_gallery_images_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `alt` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_parameters`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_parameters` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `product_parameter_group_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_parameter_group_id` (`product_parameter_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_parameters_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_parameters_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_parameter_data`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_parameter_data` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `parameter_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`,`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_parameter_data_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_parameter_data_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `value` text NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_parameter_groups`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_parameter_groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_types`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_types` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_type_variables`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_type_variables` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `product_type_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_type_id` (`product_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_type_variable_values`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_type_variable_values` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `product_type_variable_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_type_variable_id` (`product_type_variable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_type_variable_values_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_type_variable_values_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_variables`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_variables` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `product_type_variable_id` smallint(5) unsigned NOT NULL,
  `product_type_variable_value_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `product_type_variable_id` (`product_type_variable_id`),
  KEY `product_type_variable_value_id` (`product_type_variable_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_variants`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_variants` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `variant_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `variant_id` (`variant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_variant_data`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_variant_data` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `variant` text NOT NULL,
  `quantity` smallint(5) unsigned NOT NULL,
  `ean` varchar(50) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `product_gallery_id` smallint(5) unsigned DEFAULT NULL,
  `image` text,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `product_gallery_id` (`product_gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_variant_data_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_variant_data_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `price` decimal(16,5) unsigned NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_variant_parameters`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_variant_parameters` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `variant` text NOT NULL,
  `parameter_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parameter_id` (`parameter_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_product_variant_parameters_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_product_variant_parameters_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `value` text NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_relevant_products`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_relevant_products` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `relevant_product_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `relevant_product_id` (`relevant_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_signs`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_signs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `price_impact` enum('coef','price') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_signs_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_signs_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` text,
  `coef` decimal(5,2) DEFAULT '1.00',
  `price` decimal(16,5) DEFAULT '0.00000',
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_taxes`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_taxes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tax` decimal(5,2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_transports`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_transports` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(16,5) unsigned NOT NULL,
  `price_free` decimal(16,5) unsigned NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_variants`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_variants` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_variants_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_variants_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_variant_values`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_variant_values` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `variant_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `variant_id` (`variant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_eshop_variant_values_lang`
--

CREATE TABLE IF NOT EXISTS `cms_eshop_variant_values_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_sessions`
--

CREATE TABLE IF NOT EXISTS `cms_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_banned_ips`
--

CREATE TABLE IF NOT EXISTS `cms_system_banned_ips` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_categories`
--

CREATE TABLE IF NOT EXISTS `cms_system_categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `parent_id` smallint(5) unsigned DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `image` text,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_categories_lang`
--

CREATE TABLE IF NOT EXISTS `cms_system_categories_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_config`
--

CREATE TABLE IF NOT EXISTS `cms_system_config` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

--
-- Sťahujem dáta pre tabuľku `cms_system_config`
--

INSERT INTO `cms_system_config` (`id`, `key`, `value`) VALUES
(1, 'active_theme_id', '1'),
(2, 'multilang', '0'),
(3, 'hp_lang_segment', '0'),
(4, 'homepage', '{"type":"page","value":"1"}'),
(5, 'default_lang_id', '1'),
(6, 'page_404', '{"type":"page","value":"1"}'),
(7, 'order_id_format', 'MDO'),
(8, 'default_product_tpl', 'default'),
(9, 'remember_product_parameters', '1'),
(10, 'remember_product_variables', '1'),
(11, 'remember_product_variants', '1'),
(12, 'remember_product_variant_parameters', '1'),
(13, 'product_only_one_alias', '0'),
(14, 'product_alias_redirect', '1'),
(15, '_global_meta_title_prefix', NULL),
(16, '_global_meta_description', NULL),
(17, '_global_meta_keywords', NULL),
(18, 'page_only_one_alias', '0'),
(19, 'page_alias_redirect', '1'),
(20, 'default_category_tpl', 'default'),
(21, 'category_only_one_alias', '0'),
(22, 'category_alias_redirect', '1'),
(23, 'service_alias_redirect', '1'),
(24, '_global_meta_title_suffix', NULL),
(25, 'default_order_state_id', '1'),
(26, 'product_sold', '{"type":"page","value":"1"}'),
(27, 'unpublish_page', '{"type":"page","value":"1"}'),
(28, 'unpublish_product', '{"type":"page","value":"1"}'),
(29, 'unpublish_category', '{"type":"page","value":"1"}'),
(30, 'unpublish_service', '{"type":"page","value":"1"}'),
(31, 'robots', NULL),
(32, 'generate_sitemap', '1'),
(33, 'default_currency_id', '1'),
(34, 'sitemap_pages', '1'),
(35, 'sitemap_products', '0'),
(36, 'sitemap_categories', '0'),
(37, 'sitemap_services', '1'),
(38, 'default_customer_group_id', NULL),
(39, 'default_customer_group_id', NULL),
(40, 'default_customer_group_id', NULL),
(41, 'default_customer_group_id', NULL),
(42, 'email_from_name', ''),
(43, 'email_from_email', '');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_config_lang`
--

CREATE TABLE IF NOT EXISTS `cms_system_config_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `value` text,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Sťahujem dáta pre tabuľku `cms_system_config_lang`
--

INSERT INTO `cms_system_config_lang` (`id`, `lang_id`, `value`) VALUES
(15, 1, NULL),
(16, 1, NULL),
(17, 1, NULL),
(24, 1, NULL);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_domains`
--

CREATE TABLE IF NOT EXISTS `cms_system_domains` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `domain` varchar(255) NOT NULL,
  `lang_id` smallint(5) unsigned DEFAULT NULL,
  `theme_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `theme_id` (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_emails`
--

CREATE TABLE IF NOT EXISTS `cms_system_emails` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email_wrap_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_wrap_id` (`email_wrap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_emails_lang`
--

CREATE TABLE IF NOT EXISTS `cms_system_emails_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_email_copies`
--

CREATE TABLE IF NOT EXISTS `cms_system_email_copies` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_email_wraps`
--

CREATE TABLE IF NOT EXISTS `cms_system_email_wraps` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_email_wraps_lang`
--

CREATE TABLE IF NOT EXISTS `cms_system_email_wraps_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `content` text NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_href_attributes`
--

CREATE TABLE IF NOT EXISTS `cms_system_href_attributes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_labels`
--

CREATE TABLE IF NOT EXISTS `cms_system_labels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `code` varchar(50) NOT NULL,
  `href` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_langs`
--

CREATE TABLE IF NOT EXISTS `cms_system_langs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned DEFAULT NULL,
  `lang` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `currency_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`lang`),
  UNIQUE KEY `lang_code` (`code`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Sťahujem dáta pre tabuľku `cms_system_langs`
--

INSERT INTO `cms_system_langs` (`id`, `order`, `lang`, `code`, `currency_id`) VALUES
(1, 1, 'sk', 'sk', 1);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_lists`
--

CREATE TABLE IF NOT EXISTS `cms_system_lists` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `list_type_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `list_type_id` (`list_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_list_types`
--

CREATE TABLE IF NOT EXISTS `cms_system_list_types` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `primary_variable_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `primary_variable_id` (`primary_variable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_list_type_variables`
--

CREATE TABLE IF NOT EXISTS `cms_system_list_type_variables` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `list_type_id` smallint(5) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `add` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '0',
  `field_type` varchar(255) NOT NULL,
  `rules` text,
  PRIMARY KEY (`id`),
  KEY `list_type_id` (`list_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_menus`
--

CREATE TABLE IF NOT EXISTS `cms_system_menus` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_menu_links`
--

CREATE TABLE IF NOT EXISTS `cms_system_menu_links` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` smallint(5) unsigned NOT NULL,
  `parent_link_id` smallint(5) unsigned DEFAULT NULL,
  `order` smallint(5) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `href` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `parent_link_id` (`parent_link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_menu_links_lang`
--

CREATE TABLE IF NOT EXISTS `cms_system_menu_links_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `text` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_pages`
--

CREATE TABLE IF NOT EXISTS `cms_system_pages` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `page_type_id` smallint(5) unsigned NOT NULL,
  `parent_page_id` smallint(5) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `lastmod` int(9) NOT NULL,
  `tpl` varchar(255) DEFAULT NULL,
  `changefreq` varchar(255) NOT NULL,
  `index` tinyint(1) NOT NULL DEFAULT '1',
  `sitemap_priority` decimal(2,1) NOT NULL DEFAULT '0.5',
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `created` bigint(9) unsigned NOT NULL,
  `viewed` bigint(9) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `page_type_id` (`page_type_id`),
  KEY `parent_page_id` (`parent_page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Sťahujem dáta pre tabuľku `cms_system_pages`
--

INSERT INTO `cms_system_pages` (`id`, `order`, `public`, `page_type_id`, `parent_page_id`, `name`, `lastmod`, `tpl`, `changefreq`, `index`, `sitemap_priority`, `admin_id`, `created`, `viewed`) VALUES
(1, 1, 1, 1, NULL, 'Domovská stránka', 1419261573, NULL, 'monthly', 1, 0.5, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_pages_in_categories`
--

CREATE TABLE IF NOT EXISTS `cms_system_pages_in_categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` smallint(5) unsigned NOT NULL,
  `category_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_pages_lang`
--

CREATE TABLE IF NOT EXISTS `cms_system_pages_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Sťahujem dáta pre tabuľku `cms_system_pages_lang`
--

INSERT INTO `cms_system_pages_lang` (`id`, `lang_id`, `alias`, `meta_title`, `meta_description`, `meta_keywords`) VALUES
(1, 1, 'domovska-stranka', '', '', '');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_page_types`
--

CREATE TABLE IF NOT EXISTS `cms_system_page_types` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `tpl` varchar(255) NOT NULL,
  `parameters` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Sťahujem dáta pre tabuľku `cms_system_page_types`
--

INSERT INTO `cms_system_page_types` (`id`, `order`, `name`, `class`, `method`, `tpl`, `parameters`) VALUES
(1, 1, 'Domovská stránka', NULL, NULL, 'homepage', NULL);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_page_type_variables`
--

CREATE TABLE IF NOT EXISTS `cms_system_page_type_variables` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `page_type_id` smallint(5) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `add` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '0',
  `field_type` varchar(255) NOT NULL,
  `rules` text,
  PRIMARY KEY (`id`),
  KEY `page_type_id` (`page_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_panels`
--

CREATE TABLE IF NOT EXISTS `cms_system_panels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `panel_type_id` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `panel_type_id` (`panel_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_panels_in_positions`
--

CREATE TABLE IF NOT EXISTS `cms_system_panels_in_positions` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `panel_id` smallint(5) unsigned NOT NULL,
  `position_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `panel_id` (`panel_id`),
  KEY `position_id` (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_panel_types`
--

CREATE TABLE IF NOT EXISTS `cms_system_panel_types` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `tpl` varchar(255) NOT NULL,
  `parameters` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Sťahujem dáta pre tabuľku `cms_system_panel_types`
--

INSERT INTO `cms_system_panel_types` (`id`, `order`, `name`, `class`, `method`, `tpl`, `parameters`) VALUES
(1, 1, 'Menu', 'default_panel_type_library', 'menu', 'menu', NULL);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_panel_type_variables`
--

CREATE TABLE IF NOT EXISTS `cms_system_panel_type_variables` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `panel_type_id` smallint(5) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `add` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '0',
  `field_type` varchar(255) NOT NULL,
  `rules` text,
  PRIMARY KEY (`id`),
  KEY `panel_type_id` (`panel_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Sťahujem dáta pre tabuľku `cms_system_panel_type_variables`
--

INSERT INTO `cms_system_panel_type_variables` (`id`, `order`, `panel_type_id`, `name`, `title`, `info`, `add`, `edit`, `field_type`, `rules`) VALUES
(3, 3, 1, 'menu_id', 'Menu', NULL, 1, 1, 'menu', 'trim|required|item_exists_system[menus]'),
(4, 4, 1, 'levels', 'Počet úrovní', NULL, 1, 1, 'input', 'trim|intval|plus'),
(5, 5, 1, 'menu_open', 'Tag - Menu otvorenie', NULL, 1, 1, 'input', 'max_length[255]'),
(6, 6, 1, 'menu_close', 'Tag - Menu zatvorenie', NULL, 1, 1, 'input', 'max_length[255]'),
(7, 7, 1, 'link', 'Tag - Odkaz', NULL, 1, 1, 'input', 'trim|max_length[255]'),
(8, 8, 1, 'link_open', 'Tag - Odkaz otvorenie', NULL, 1, 1, 'input', 'trim|max_length[255]'),
(9, 9, 1, 'link_close', 'Tag - Odkaz zatvorenie', NULL, 1, 1, 'input', 'trim|max_length[255]'),
(10, 10, 1, 'link_separator', 'Tag - Oddelenie odkazov', NULL, 1, 1, 'input', 'trim|max_length[255]');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_parts`
--

CREATE TABLE IF NOT EXISTS `cms_system_parts` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_parts_lang`
--

CREATE TABLE IF NOT EXISTS `cms_system_parts_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `content` text NOT NULL,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_positions`
--

CREATE TABLE IF NOT EXISTS `cms_system_positions` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_redirects`
--

CREATE TABLE IF NOT EXISTS `cms_system_redirects` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `from` text NOT NULL,
  `to` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_resources`
--

CREATE TABLE IF NOT EXISTS `cms_system_resources` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `theme_id` smallint(5) unsigned NOT NULL,
  `type` set('css','js') NOT NULL,
  `url` text NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `global` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `theme_id` (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_resource_rels`
--

CREATE TABLE IF NOT EXISTS `cms_system_resource_rels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `resource_id` smallint(5) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `order` smallint(5) unsigned NOT NULL,
  `type` set('page_category','page_type','page','panel_type','panel','product_category','product','service') NOT NULL,
  `page_type_id` smallint(5) unsigned DEFAULT NULL,
  `page_id` smallint(5) unsigned DEFAULT NULL,
  `panel_type_id` smallint(5) unsigned DEFAULT NULL,
  `panel_id` smallint(5) unsigned DEFAULT NULL,
  `product_category_id` smallint(5) unsigned DEFAULT NULL,
  `product_id` smallint(5) unsigned DEFAULT NULL,
  `page_category_id` smallint(5) unsigned DEFAULT NULL,
  `service_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_type_id` (`page_type_id`),
  KEY `page_id` (`page_id`),
  KEY `panel_type_id` (`panel_type_id`),
  KEY `panel_id` (`panel_id`),
  KEY `product_category_id` (`product_category_id`),
  KEY `product_id` (`product_id`),
  KEY `page_category_id` (`page_category_id`),
  KEY `resource_id` (`resource_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_services`
--

CREATE TABLE IF NOT EXISTS `cms_system_services` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_service_id` smallint(5) unsigned DEFAULT NULL,
  `order` smallint(5) unsigned NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `lastmod` int(9) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tpl` varchar(255) NOT NULL,
  `class` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `parameters` text,
  `changefreq` varchar(255) NOT NULL,
  `index` tinyint(1) NOT NULL DEFAULT '1',
  `sitemap_priority` decimal(2,1) NOT NULL DEFAULT '0.5',
  PRIMARY KEY (`id`),
  KEY `parent_service_id` (`parent_service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_services_lang`
--

CREATE TABLE IF NOT EXISTS `cms_system_services_lang` (
  `id` smallint(5) unsigned NOT NULL,
  `lang_id` smallint(5) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  KEY `id` (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_system_themes`
--

CREATE TABLE IF NOT EXISTS `cms_system_themes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `order` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `folder` varchar(255) NOT NULL,
  `css_folder` varchar(255) DEFAULT NULL,
  `js_folder` varchar(255) DEFAULT NULL,
  `favicon` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Sťahujem dáta pre tabuľku `cms_system_themes`
--

INSERT INTO `cms_system_themes` (`id`, `order`, `name`, `folder`, `css_folder`, `js_folder`, `favicon`) VALUES
(1, 1, 'default', 'default', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_user_page_type_data_1`
--

CREATE TABLE IF NOT EXISTS `cms_user_page_type_data_1` (
  `id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Sťahujem dáta pre tabuľku `cms_user_page_type_data_1`
--

INSERT INTO `cms_user_page_type_data_1` (`id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cms_user_panel_type_data_1`
--

CREATE TABLE IF NOT EXISTS `cms_user_panel_type_data_1` (
  `id` smallint(5) unsigned NOT NULL,
  `menu_id` smallint(5) unsigned NOT NULL,
  `levels` tinyint(4) unsigned NOT NULL,
  `menu_open` varchar(255) DEFAULT NULL,
  `menu_close` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `link_open` varchar(255) DEFAULT NULL,
  `link_close` varchar(255) DEFAULT NULL,
  `link_separator` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `cms_eshop_categories`
--
ALTER TABLE `cms_eshop_categories`
  ADD CONSTRAINT `cms_eshop_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `cms_eshop_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_categories_lang`
--
ALTER TABLE `cms_eshop_categories_lang`
  ADD CONSTRAINT `cms_eshop_categories_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_categories_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_communications_lang`
--
ALTER TABLE `cms_eshop_communications_lang`
  ADD CONSTRAINT `cms_eshop_communications_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_communications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_communications_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_coupons_lang`
--
ALTER TABLE `cms_eshop_coupons_lang`
  ADD CONSTRAINT `cms_eshop_coupons_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_coupons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_coupons_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_customer_groups_lang`
--
ALTER TABLE `cms_eshop_customer_groups_lang`
  ADD CONSTRAINT `cms_eshop_customer_groups_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_customer_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_customer_groups_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_order_data`
--
ALTER TABLE `cms_eshop_order_data`
  ADD CONSTRAINT `cms_eshop_order_data_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `cms_eshop_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_order_states_lang`
--
ALTER TABLE `cms_eshop_order_states_lang`
  ADD CONSTRAINT `cms_eshop_order_states_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_order_states` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_order_states_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_payments`
--
ALTER TABLE `cms_eshop_payments`
  ADD CONSTRAINT `cms_eshop_payments_ibfk_1` FOREIGN KEY (`transport_id`) REFERENCES `cms_eshop_transports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_products`
--
ALTER TABLE `cms_eshop_products`
  ADD CONSTRAINT `cms_eshop_products_ibfk_1` FOREIGN KEY (`tax_id`) REFERENCES `cms_eshop_taxes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_products_ibfk_5` FOREIGN KEY (`manufacturer_id`) REFERENCES `cms_eshop_manufacturers` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `cms_eshop_products_ibfk_6` FOREIGN KEY (`distributor_id`) REFERENCES `cms_eshop_distributors` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `cms_eshop_products_ibfk_7` FOREIGN KEY (`product_parameter_group_id`) REFERENCES `cms_eshop_product_parameter_groups` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `cms_eshop_products_ibfk_8` FOREIGN KEY (`product_type_id`) REFERENCES `cms_eshop_product_types` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `cms_eshop_products_ibfk_9` FOREIGN KEY (`product_gallery_id`) REFERENCES `cms_eshop_product_galleries` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Obmedzenie pre tabuľku `cms_eshop_products_in_categories`
--
ALTER TABLE `cms_eshop_products_in_categories`
  ADD CONSTRAINT `cms_eshop_products_in_categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_products_in_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `cms_eshop_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_products_lang`
--
ALTER TABLE `cms_eshop_products_lang`
  ADD CONSTRAINT `cms_eshop_products_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_products_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_products_signs`
--
ALTER TABLE `cms_eshop_products_signs`
  ADD CONSTRAINT `cms_eshop_products_signs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_products_signs_ibfk_2` FOREIGN KEY (`sign_id`) REFERENCES `cms_eshop_signs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_galleries`
--
ALTER TABLE `cms_eshop_product_galleries`
  ADD CONSTRAINT `cms_eshop_product_galleries_ibfk_1` FOREIGN KEY (`primary_image_id`) REFERENCES `cms_eshop_product_gallery_images` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_galleries_lang`
--
ALTER TABLE `cms_eshop_product_galleries_lang`
  ADD CONSTRAINT `cms_eshop_product_galleries_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_product_galleries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_galleries_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_gallery_images`
--
ALTER TABLE `cms_eshop_product_gallery_images`
  ADD CONSTRAINT `cms_eshop_product_gallery_images_ibfk_1` FOREIGN KEY (`product_gallery_id`) REFERENCES `cms_eshop_product_galleries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_gallery_images_lang`
--
ALTER TABLE `cms_eshop_product_gallery_images_lang`
  ADD CONSTRAINT `cms_eshop_product_gallery_images_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_product_gallery_images` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_gallery_images_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_parameters`
--
ALTER TABLE `cms_eshop_product_parameters`
  ADD CONSTRAINT `cms_eshop_product_parameters_ibfk_1` FOREIGN KEY (`product_parameter_group_id`) REFERENCES `cms_eshop_product_parameter_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_parameters_lang`
--
ALTER TABLE `cms_eshop_product_parameters_lang`
  ADD CONSTRAINT `cms_eshop_product_parameters_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_product_parameters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_parameters_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_parameter_data`
--
ALTER TABLE `cms_eshop_product_parameter_data`
  ADD CONSTRAINT `cms_eshop_product_parameter_data_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_parameter_data_lang`
--
ALTER TABLE `cms_eshop_product_parameter_data_lang`
  ADD CONSTRAINT `cms_eshop_product_parameter_data_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_product_parameter_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_parameter_data_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_type_variables`
--
ALTER TABLE `cms_eshop_product_type_variables`
  ADD CONSTRAINT `cms_eshop_product_type_variables_ibfk_1` FOREIGN KEY (`product_type_id`) REFERENCES `cms_eshop_product_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_type_variable_values`
--
ALTER TABLE `cms_eshop_product_type_variable_values`
  ADD CONSTRAINT `cms_eshop_product_type_variable_values_ibfk_1` FOREIGN KEY (`product_type_variable_id`) REFERENCES `cms_eshop_product_type_variables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_type_variable_values_lang`
--
ALTER TABLE `cms_eshop_product_type_variable_values_lang`
  ADD CONSTRAINT `cms_eshop_product_type_variable_values_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_product_type_variable_values` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_type_variable_values_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_variables`
--
ALTER TABLE `cms_eshop_product_variables`
  ADD CONSTRAINT `cms_eshop_product_variables_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_variables_ibfk_2` FOREIGN KEY (`product_type_variable_id`) REFERENCES `cms_eshop_product_type_variables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_variables_ibfk_3` FOREIGN KEY (`product_type_variable_value_id`) REFERENCES `cms_eshop_product_type_variable_values` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_variants`
--
ALTER TABLE `cms_eshop_product_variants`
  ADD CONSTRAINT `cms_eshop_product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_variants_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `cms_eshop_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_variant_data`
--
ALTER TABLE `cms_eshop_product_variant_data`
  ADD CONSTRAINT `cms_eshop_product_variant_data_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_variant_data_ibfk_2` FOREIGN KEY (`product_gallery_id`) REFERENCES `cms_eshop_product_galleries` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_variant_data_lang`
--
ALTER TABLE `cms_eshop_product_variant_data_lang`
  ADD CONSTRAINT `cms_eshop_product_variant_data_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_product_variant_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_variant_data_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_variant_parameters`
--
ALTER TABLE `cms_eshop_product_variant_parameters`
  ADD CONSTRAINT `cms_eshop_product_variant_parameters_ibfk_1` FOREIGN KEY (`parameter_id`) REFERENCES `cms_eshop_product_parameters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_variant_parameters_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_product_variant_parameters_lang`
--
ALTER TABLE `cms_eshop_product_variant_parameters_lang`
  ADD CONSTRAINT `cms_eshop_product_variant_parameters_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_product_variant_parameters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_product_variant_parameters_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_relevant_products`
--
ALTER TABLE `cms_eshop_relevant_products`
  ADD CONSTRAINT `cms_eshop_relevant_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_relevant_products_ibfk_2` FOREIGN KEY (`relevant_product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_signs_lang`
--
ALTER TABLE `cms_eshop_signs_lang`
  ADD CONSTRAINT `cms_eshop_signs_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_signs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_signs_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_transports`
--
ALTER TABLE `cms_eshop_transports`
  ADD CONSTRAINT `cms_eshop_transports_ibfk_1` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_variants_lang`
--
ALTER TABLE `cms_eshop_variants_lang`
  ADD CONSTRAINT `cms_eshop_variants_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_variants_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_eshop_variant_values_lang`
--
ALTER TABLE `cms_eshop_variant_values_lang`
  ADD CONSTRAINT `cms_eshop_variant_values_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_eshop_variant_values` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_eshop_variant_values_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_categories`
--
ALTER TABLE `cms_system_categories`
  ADD CONSTRAINT `cms_system_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `cms_system_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_categories_lang`
--
ALTER TABLE `cms_system_categories_lang`
  ADD CONSTRAINT `cms_system_categories_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_system_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_categories_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_config_lang`
--
ALTER TABLE `cms_system_config_lang`
  ADD CONSTRAINT `cms_system_config_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_system_config` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_config_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_domains`
--
ALTER TABLE `cms_system_domains`
  ADD CONSTRAINT `cms_system_domains_ibfk_1` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `cms_system_domains_ibfk_2` FOREIGN KEY (`theme_id`) REFERENCES `cms_system_themes` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Obmedzenie pre tabuľku `cms_system_emails`
--
ALTER TABLE `cms_system_emails`
  ADD CONSTRAINT `cms_system_emails_ibfk_1` FOREIGN KEY (`email_wrap_id`) REFERENCES `cms_system_email_wraps` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Obmedzenie pre tabuľku `cms_system_emails_lang`
--
ALTER TABLE `cms_system_emails_lang`
  ADD CONSTRAINT `cms_system_emails_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_system_emails` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_emails_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_email_wraps_lang`
--
ALTER TABLE `cms_system_email_wraps_lang`
  ADD CONSTRAINT `cms_system_email_wraps_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_system_email_wraps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_email_wraps_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_langs`
--
ALTER TABLE `cms_system_langs`
  ADD CONSTRAINT `cms_system_langs_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `cms_eshop_currencies` (`id`);

--
-- Obmedzenie pre tabuľku `cms_system_lists`
--
ALTER TABLE `cms_system_lists`
  ADD CONSTRAINT `cms_system_lists_ibfk_1` FOREIGN KEY (`list_type_id`) REFERENCES `cms_system_list_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_list_types`
--
ALTER TABLE `cms_system_list_types`
  ADD CONSTRAINT `cms_system_list_types_ibfk_1` FOREIGN KEY (`primary_variable_id`) REFERENCES `cms_system_list_type_variables` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Obmedzenie pre tabuľku `cms_system_list_type_variables`
--
ALTER TABLE `cms_system_list_type_variables`
  ADD CONSTRAINT `cms_system_list_type_variables_ibfk_2` FOREIGN KEY (`list_type_id`) REFERENCES `cms_system_list_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_menu_links`
--
ALTER TABLE `cms_system_menu_links`
  ADD CONSTRAINT `cms_system_menu_links_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `cms_system_menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_menu_links_ibfk_2` FOREIGN KEY (`parent_link_id`) REFERENCES `cms_system_menu_links` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Obmedzenie pre tabuľku `cms_system_menu_links_lang`
--
ALTER TABLE `cms_system_menu_links_lang`
  ADD CONSTRAINT `cms_system_menu_links_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_system_menu_links` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_menu_links_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_pages`
--
ALTER TABLE `cms_system_pages`
  ADD CONSTRAINT `cms_system_pages_ibfk_1` FOREIGN KEY (`page_type_id`) REFERENCES `cms_system_page_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_pages_ibfk_2` FOREIGN KEY (`parent_page_id`) REFERENCES `cms_system_pages` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Obmedzenie pre tabuľku `cms_system_pages_in_categories`
--
ALTER TABLE `cms_system_pages_in_categories`
  ADD CONSTRAINT `cms_system_pages_in_categories_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `cms_system_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_pages_in_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `cms_system_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_pages_lang`
--
ALTER TABLE `cms_system_pages_lang`
  ADD CONSTRAINT `cms_system_pages_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_system_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_pages_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_page_type_variables`
--
ALTER TABLE `cms_system_page_type_variables`
  ADD CONSTRAINT `cms_system_page_type_variables_ibfk_1` FOREIGN KEY (`page_type_id`) REFERENCES `cms_system_page_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_panels`
--
ALTER TABLE `cms_system_panels`
  ADD CONSTRAINT `cms_system_panels_ibfk_1` FOREIGN KEY (`panel_type_id`) REFERENCES `cms_system_panel_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_panels_in_positions`
--
ALTER TABLE `cms_system_panels_in_positions`
  ADD CONSTRAINT `cms_system_panels_in_positions_ibfk_1` FOREIGN KEY (`panel_id`) REFERENCES `cms_system_panels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_panels_in_positions_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `cms_system_positions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_panel_type_variables`
--
ALTER TABLE `cms_system_panel_type_variables`
  ADD CONSTRAINT `cms_system_panel_type_variables_ibfk_1` FOREIGN KEY (`panel_type_id`) REFERENCES `cms_system_panel_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_parts_lang`
--
ALTER TABLE `cms_system_parts_lang`
  ADD CONSTRAINT `cms_system_parts_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_system_parts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_parts_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_resources`
--
ALTER TABLE `cms_system_resources`
  ADD CONSTRAINT `cms_system_resources_ibfk_1` FOREIGN KEY (`theme_id`) REFERENCES `cms_system_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_resource_rels`
--
ALTER TABLE `cms_system_resource_rels`
  ADD CONSTRAINT `cms_system_resource_rels_ibfk_1` FOREIGN KEY (`page_type_id`) REFERENCES `cms_system_page_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_resource_rels_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `cms_system_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_resource_rels_ibfk_3` FOREIGN KEY (`panel_type_id`) REFERENCES `cms_system_panel_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_resource_rels_ibfk_4` FOREIGN KEY (`panel_id`) REFERENCES `cms_system_panels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_resource_rels_ibfk_5` FOREIGN KEY (`product_category_id`) REFERENCES `cms_eshop_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_resource_rels_ibfk_6` FOREIGN KEY (`product_id`) REFERENCES `cms_eshop_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_resource_rels_ibfk_7` FOREIGN KEY (`page_category_id`) REFERENCES `cms_system_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_resource_rels_ibfk_8` FOREIGN KEY (`resource_id`) REFERENCES `cms_system_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_system_services`
--
ALTER TABLE `cms_system_services`
  ADD CONSTRAINT `cms_system_services_ibfk_1` FOREIGN KEY (`parent_service_id`) REFERENCES `cms_system_services` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Obmedzenie pre tabuľku `cms_system_services_lang`
--
ALTER TABLE `cms_system_services_lang`
  ADD CONSTRAINT `cms_system_services_lang_ibfk_2` FOREIGN KEY (`lang_id`) REFERENCES `cms_system_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cms_system_services_lang_ibfk_3` FOREIGN KEY (`id`) REFERENCES `cms_system_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_user_page_type_data_1`
--
ALTER TABLE `cms_user_page_type_data_1`
  ADD CONSTRAINT `cms_user_page_type_data_1_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_system_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cms_user_panel_type_data_1`
--
ALTER TABLE `cms_user_panel_type_data_1`
  ADD CONSTRAINT `cms_user_panel_type_data_1_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cms_system_panels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

SET foreign_key_checks = 1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
