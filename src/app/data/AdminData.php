<?php

/**
 * 
 * CMS admin data
 * 
 * @author xing
 *
 */

class AdminData 
{
	protected $admin_id;
	protected $email;
	protected $name;
	protected $password;
	protected $permission_id;
	protected $permission_name;
	protected $allow_no_array;

	public function getAdminId() { return $this->admin_id; }
	public function getEmail() { return $this->email; }
	public function getName() { return $this->name; }
	public function getPermissionId() { return $this->permission_id; }
	public function getPermissionName() { return $this->permission_name; }

	public function __construct($adminData, $allowData)
	{
		$this->admin_id = $adminData['admin_id'];
		$this->email = $adminData['email'];
		$this->name = $adminData['name'];
		$this->permission_id = $adminData['permission_id'];
		$this->permission_name = $adminData['permission_name'];

		$this->setAllowData($allowData);
	}

	public function setAllowData($allowData) 
	{
		$this->allow_no_array = array();
		foreach ($allowData as $row) {
			$this->allow_no_array[] = $row['allow_no'];
		}
	}
	
	public function isAuthenticationMenu($menu_id)
	{
		if (array_search($menu_id, $this->allow_no_array) === false) {
			return false;
		} else {
			return true;
		}
	}
	
	public function getEnableSystemMenu()
	{
		$enable_array = array();
	
		$menu_array = MenuData::getSystemMenu();
		foreach ($menu_array as $row) {
			if (array_search($row['id'], $this->allow_no_array) !== false) {
				$enable_array[] = $row;
			}
		}
		return $enable_array;
	}
	
	public function getEnableSurveyMenu()
	{
		$enable_array = array();
	
		$menu_array = MenuData::getSurveyMenu();
		foreach ($menu_array as $row) {
			if (array_search($row['id'], $this->allow_no_array) !== false) {
				$enable_array[] = $row;
			}
		}
		return $enable_array;
	}

	public function getEnableEntrustMenu()
	{
		$enable_array = array();
	
		$menu_array = MenuData::getEntrustMenu();
		foreach ($menu_array as $row) {
			if (array_search($row['id'], $this->allow_no_array) !== false) {
				$enable_array[] = $row;
			}
		}
		return $enable_array;
	}

	public function getEnableLogMenu()
	{
		$enable_array = array();
	
		$menu_array = MenuData::getLogMenu();
		foreach ($menu_array as $row) {
			if (array_search($row['id'], $this->allow_no_array) !== false) {
				$enable_array[] = $row;
			}
		}
		return $enable_array;
	}
	
}