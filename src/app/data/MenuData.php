<?php
/**
 * 管理メニュー情報管理クラス
 * 
 * @package		app.data
 * @author		xing
 */

class MenuData
{
	protected $allow_no_array;

	// System Menu
	const SYSTEM_ADMIN_ID = 101;
	const SYSTEM_ADMIN_NAME = 'Admin';
	const SYSTEM_ADMIN_URL = 'support/admin/';
	
	const SYSTEM_PERMISSION_ID = 102;
	const SYSTEM_PERMISSION_NAME = 'Permission';
	const SYSTEM_PERMISSION_URL = 'support/permission/';
		
	// Survey Menu
	const SURVEY_PROJECT_ID = 201;
	const SURVEY_PROJECT_NAME = 'Project';
	const SURVEY_PROJECT_URL = 'support/project/';
	
	const SURVEY_LINK_ID = 202;
	const SURVEY_LINK_NAME = 'Link';
	const SURVEY_LINK_URL = 'support/link/';

	const SURVEY_PARTNER_ID = 203;
	const SURVEY_PARTNER_NAME = 'Partner';
	const SURVEY_PARTNER_URL = 'support/partner/';

	const SURVEY_ACCESSKEY_ID = 204;
	const SURVEY_ACCESSKEY_NAME = 'AccessKey';
	const SURVEY_ACCESSKEY_URL = 'support/accesskey/';

	const SURVEY_BILLING_ID = 205;
	const SURVEY_BILLING_NAME = 'Billing';
	const SURVEY_BILLING_URL = 'support/billing/';

	const SURVEY_ANALYTICS_ID = 206;
	const SURVEY_ANALYTICS_NAME = 'Analytics';
	const SURVEY_ANALYTICS_URL = 'support/analytics/';

	// Entrust Menu
	const ENTRUST_ACCOUNT_ID = 301;
	const ENTRUST_ACCOUNT_NAME = 'Account';
	const ENTRUST_ACCOUNT_URL = 'support/account/';

	const ENTRUST_POINT_ID = 302;
	const ENTRUST_POINT_NAME = 'Point';
	const ENTRUST_POINT_URL = 'support/point/';

	const ENTRUST_EVENT_ID = 303;
	const ENTRUST_EVENT_NAME = 'Event';
	const ENTRUST_EVENT_URL = 'support/event/';

	// Logs Menu
	const LOG_ACTIVITY_ID = 401;
	const LOG_ACTIVITY_NAME = 'Activity logs';
	const LOG_ACTIVITY_URL = 'support/activitylog/';

	const LOG_ACCESS_ID = 402;
	const LOG_ACCESS_NAME = 'Access logs';
	const LOG_ACCESS_URL = 'support/accessLog/';

	const LOG_ADMIN_ID = 403;
	const LOG_ADMIN_NAME = 'Admin logs';
	const LOG_ADMIN_URL = 'support/adminLog/';

	const LOG_ERROR_REPORT_ID = 404;
	const LOG_ERROR_REPORT_NAME = 'Error Reports';
	const LOG_ERROR_REPORT_URL = 'support/errorLog/';

	const LOG_IP_ADDRESS_ID = 405;
	const LOG_IP_ADDRESS_NAME = 'IP Block logs';
	const LOG_IP_ADDRESS_URL = 'support/blockLog/';

	public static function getSystemMenu()
	{
		$menu_id_array = array(
			'SYSTEM_ADMIN',
			'SYSTEM_PERMISSION',
		);

		$res = array();
		foreach ($menu_id_array as $menu_tag) {
			$res[] = array(
				'id'   => constant('self::'.$menu_tag.'_ID'),
				'name' => constant('self::'.$menu_tag.'_NAME'),
				'url'  => constant('self::'.$menu_tag.'_URL'),
			);
		}
		return $res;
	}
	
	public static function getSurveyMenu()
	{
		$menu_id_array = array(
			'SURVEY_PROJECT',
			'SURVEY_LINK',
			'SURVEY_PARTNER',
			'SURVEY_ACCESSKEY',
			'SURVEY_BILLING',
			'SURVEY_ANALYTICS',
		);

		$res = array();
		foreach ($menu_id_array as $menu_tag) {
			$res[] = array(
				'id'   => constant('self::'.$menu_tag.'_ID'),
				'name' => constant('self::'.$menu_tag.'_NAME'),
				'url'  => constant('self::'.$menu_tag.'_URL'),
			);
		}
		return $res;
	}

	public static function getEntrustMenu()
	{
		$menu_id_array = array(
			'ENTRUST_ACCOUNT',
			'ENTRUST_POINT',
			'ENTRUST_EVENT',
		);
		
		$res = array();
		foreach ($menu_id_array as $menu_tag) {
			$res[] = array(
				'id'   => constant('self::'.$menu_tag.'_ID'),
				'name' => constant('self::'.$menu_tag.'_NAME'),
				'url'  => constant('self::'.$menu_tag.'_URL'),
			);
		}
		return $res;
	}

	public static function getLogMenu()
	{
		$menu_id_array = array(
			//'LOG_ACTIVITY',
			'LOG_ACCESS',
			'LOG_ADMIN',
			'LOG_ERROR_REPORT',
			'LOG_IP_ADDRESS',
		);
		
		$res = array();
		foreach ($menu_id_array as $menu_tag) {
			$res[] = array(
				'id'   => constant('self::'.$menu_tag.'_ID'),
				'name' => constant('self::'.$menu_tag.'_NAME'),
				'url'  => constant('self::'.$menu_tag.'_URL'),
			);
		}
		return $res;
	}

	public static function getEnableSystemMenu() {
		$enable_array = array();
		
		$menu_array = self::getSystemMenu();
		foreach ($menu_array as $row) {
			if (array_search($row['id'], self::allow_no_array) !== false) {
				$enable_array[] = $row;
			}
		}
		return $enable_array;
	}
	
	public static function getEnableSurveyMenu(){
		$enable_array = array();
	
		$menu_array = self::getSurveyMenu();
		foreach ($menu_array as $row) {
			if (array_search($row['id'], self::allow_no_array) !== false) {
				$enable_array[] = $row;
			}
		}
		return $enable_array;
	}

	public static function getEnableEntrustMenu(){
		$enable_array = array();
	
		$menu_array = self::getEntrustMenu();
		foreach ($menu_array as $row) {
			if (array_search($row['id'], self::allow_no_array) !== false) {
				$enable_array[] = $row;
			}
		}
		return $enable_array;
	}

	public static function getEnableLogMenu(){
		$enable_array = array();
	
		$menu_array = self::getLogMenu();
		foreach ($menu_array as $row) {
			if (array_search($row['id'], self::allow_no_array) !== false) {
				$enable_array[] = $row;
			}
		}
		return $enable_array;
	}
}
