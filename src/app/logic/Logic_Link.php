<?php

/**
* 
*/
class Logic_Link extends _Logic_App {

	public static function getLinkDataLimited(_DatabaseAccess $dao, $pid, $limit, $offset) {
		$ret = array();

		$sql = "select `id`, `name`, `type`, `url`, `disable`, `updated_at`, `pid`, (select count(1) from `access_keys` where `link_id` = `id`) as accesskey from (
					select `id`, `name`, `type`, `url`, `disable`, `created_at`, `updated_at`, `pid` from `link` where `type` = 0 and `pid` = ?
					union
					select `id`, `name`, `type`, '' as url, `disable`, `created_at`, `updated_at`, `pid` from `link` where `type` = 1 and `pid` = ?
				) t ";

		$sql.= " limit ? offset ?";

		$param = array($pid, $pid, $limit, $offset);
		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getLinkDataByPid(_DatabaseAccess $dao, $pid) {
		$sql = "select distinct `id`, `name` from link where `pid` = ?";
		$param = array($pid);

		return $dao->selectArray($sql, $param);
	}
 
	public static function getLinkDataById(_DatabaseAccess $dao, $id) {
		$sql = "select * from `link` where id = ? limit 1";

		$param = array($id);

		return $dao->selectOne($sql, $param);
	}

	public static function getLinkDataArrayById(_DatabaseAccess $dao, $id) {
		$sql = "select * from `link` where `id` = ?";
		$param = array($id);
		
		return $dao->selectArray($sql, $param);
	}

	public static function getLinkDataByAccessKey(_DatabaseAccess $dao, $accesskey) {
		$sql = "select `url`, `type`, `sample_size`, `request_limit`, `status` from `link` left join `partner` on link.id = partner.link_id where link.id = ? and k = ? and link.pid  = ?";

		$param = array($accesskey['link_id'], $accesskey['link_key'], $accesskey['pid']);

		return $dao->selectOne($sql, $param);
	}

	public static function insertLinkData(_DatabaseAccess $dao, $data) {
		try {
			if($data['type'] == 0) {
				$dao->insert('link', $data);
			} else {
				$idx = 1000;
				$param = array();
				foreach ($data['urls'] as $url) {
					$key = $idx++;
					$param[] = array(
						'id' => $data['id'],
						'k' => $key,
						'name' => $data['name'],
						'type' => $data['type'],
						'url' => $url,
						'pid' => $data['pid']
					);
				}

				$dao->insert_bulk('link', $param);
			}			
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function updateLinkData(_DatabaseAccess $dao, $data) {
		try {
			if($data['type'] == 0) {
				$condition = "id = ?";
				$condition_param = array($data['id']);

				$dao->update('link', $data, $condition, $condition_param);
			} else {
				$condition = "id = ?";
				$condition_param = array($data['id']);
				$dao->delete('link', $condition, $condition_param);

				$idx = 1000;
				$param = array();
				foreach ($data['urls'] as $url) {
					$key = $idx++;
					$param[] = array(
						'id' => $id,
						'k' => $key,
						'name' => $data['name'],
						'type' => $data['type'],
						'url' => $url,
						'pid' => $data['pid']
					);
				}

				$dao->insert_bulk('link', $param);
			}
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}
}