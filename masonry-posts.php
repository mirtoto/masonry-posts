<?php
/*
Plugin Name: Masonry Posts
Plugin URI: https://plus.google.com/111596376378866683071
Description: WordPress Masonry Posts plugin
Version: 1.0.0.0
Author: Mirosław Toton
Author URI: https://plus.google.com/111596376378866683071
License: GPL2
*/
/*
Copyright 2014 Miroslaw Toton (email: mirtoto@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ABSPATH'))
{
	exit;
}

require_once('includes/aq_resizer.php');
require_once('includes/class-masonry-posts.php');
require_once('includes/class-masonry-posts-settings.php');

function Masonry_Posts()
{
	$instance = Masonry_Posts::instance(__FILE__, '1.0.0.0');
	if (is_null($instance->settings))
	{
		$instance->settings = Masonry_Posts_Settings::instance($instance);
	}
	
	return $instance;
}

Masonry_Posts();

