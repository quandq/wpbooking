<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/15/2016
 * Time: 10:01 AM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if(!class_exists('Traveler_Controller'))
{
	class Traveler_Controller
	{
		function __construct()
		{

		}

		function load_view($view,$data=array())
		{
			return traveler_load_view($view,$data);
		}

		function admin_load_view($view,$data=array())
		{
			return traveler_admin_load_view($view,$data);
		}
	}
}