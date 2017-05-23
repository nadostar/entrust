<?php

/**
* 
*/
class MasterData {
	private static $survey_type_map = array(
		0 => 'Sample Only',
		1 => 'Full Service',
		2 => 'Healthcare'
	);

	public static function getSurveyTypeMap() {
		return self::$survey_type_map;
	}

	private static $survey_type_snapshot_map = array(
		0 => 'S',
		1 => 'F',
		2 => 'H'
	);

	public static function getSnapshotSurveyType($val) {
		return self::$survey_type_snapshot_map[$val];
	}

	private static $link_type_map = array(
		0 => 'S',
		1 => 'M'
	);

	public static function getLinkType($val) {
		return self::$link_type_map[$val];
	}

	private static $country_map = array(
		'01' => 'Arab',
		'02' => 'Argentina',
		'03' => "Brazil",
		'04' => 'China',
		'05' => 'France',
		'06' => 'Germany',
		'07' => 'Hongkong',
		'08' => 'India',
		'09' => 'Indonesia',
		'10' => 'Italy',
		'11' => 'Japan',
		'12' => 'Korea',
		'13' => 'Laos',
		'14' => 'Malaysia',
		'15' => 'Myanmar',
		'16' => 'Philippines',
		'17' => 'Singapore',
		'18' => 'South Africa',
		'19' => 'Taiwan',
		'20' => 'Thailand',
		'21' => 'UK',
		'22' => 'US',
		'23' => 'Vietnam',
	);

	public static function getCountryMap() {
		return self::$country_map;
	}

	public static function getCountry($val) {
		return self::$country_map[$val];
	}

	private static $project_status_map = array(
		0 => '<span class="label label-warning">Pending</span>',
		1 => '<span class="label label-primary">Active</span>',
		2 => '<span class="label label-danger">Closed</span>'
	);

	public static function getProjectStatus($val) {
		return self::$project_status_map[$val];
	}

	private static $project_search_status_map = array(
		0 => 'Pending',
		1 => 'Active',
		2 => 'Closed'
	);

	public static function getProjectSearchStatusMap() {
		return self::$project_search_status_map;
	}

	public static function getProjectSearchStatus($val) {
		return self::$project_search_status_map[$val];
	}

	private static $project_status_control_map = array(
		1 => '<i class="fa fa-toggle-off"></i>',
		2 => '<i class="fa fa-toggle-on"></i>'
	);

	public static function getProjectStatusControl($val) {
		return self::$project_status_control_map[$val];
	}

	private static $status_map = array(
		0 => '<span class="label label-primary">Active</span>',
		1 => '<span class="label label-danger">Closed</span>'
	);

	public static function getStatus($val) {
		return self::$status_map[$val];
	}

	private static $progress_map = array(
		0 => '<span class="label label-plain">Survey</span>',
		1 => '<span class="label label-success">Complete</span>',
		2 => '<span class="label label-danger">Screenout</span>',
		3 => '<span class="label label-warning">Quotafull</span>',
	);

	private static $partner_status_control_map = array(
		0 => '<i class="fa fa-toggle-off"></i>',
		1 => '<i class="fa fa-toggle-on"></i>',
	);

	public static function getPartnerStatusControl($val) {
		return self::$partner_status_control_map[$val];
	}

	public static function getProgress($val) {
		return self::$progress_map[$val];
	}

	private static $accesslog_category_map = array(
		0 => 'Survey',
		1 => 'Complete',
		2 => 'Screenout',
		3 => 'Quotafull',
	);

	public static function getAccessLogCategory($val) {
		return self::$accesslog_category_map[$val];
	}

	public static function getAccessLogCategoryMap() {
		return self::$accesslog_category_map;
	}

	private static $adminlog_category_map = array(
		'000' => 'Login',

		'010' => 'Admin added',
		'011' => 'Admin changed',
		'012' => 'Admin removed',
		'013' => 'Admin password init',
		'014' => 'Password changed',

		'020' => 'Permission added',
		'021' => 'Permission changed',
		'022' => 'Permission removed',
		
		'030' => 'Project added',
		'031' => 'Project changed',
		
		'040' => 'Link added',
		'041' => 'Link changed',
		
		'051' => 'Partner added',
		'052' => 'Partner changed',
		'043' => 'Generate accesskey',
	);

	public static function getAdminLogCategoryMap(){
		return self::$adminlog_category_map;
	}

	public static function getAdminLogCategory($val) {
		return self::$adminlog_category_map[$val];
	}
}