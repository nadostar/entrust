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

	private static $country_map = array(
		'CN' => 'China',
		'JP' => 'Japan',
		'KR' => "Korea",
		'US' => 'United States',
		'UK' => 'United Kindom',
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

	private static $status_map = array(
		0 => '<span class="label label-primary">Active</span>',
		1 => '<span class="label label-danger">Closed</span>'
	);

	public static function getStatus($val) {
		return self::$status_map[$val];
	}

	private static $progress_map = array(
		0 => '<span class="label label-plain">Survey</span>',
		1 => '<span class="label label-success">Complate</span>',
		2 => '<span class="label label-danger">Screenout</span>',
		3 => '<span class="label label-warning">Quotafull</span>',
	);

	public static function getProgress($val) {
		return self::$progress_map[$val];
	}
}