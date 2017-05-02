<?php
/**
 * Common Action
 *
 * @package    app.action
 * @author     xing
 */

require_once __DIR__.'/../_Action_App.php';
require_once __DIR__.'/../../data/AdminData.php';
require_once __DIR__.'/../../data/MenuData.php';
require_once __DIR__.'/../../data/Category.php';
require_once __DIR__.'/../../data/MasterData.php';
require_once __DIR__.'/../../util/SimplePager.php';
require_once __DIR__.'/../../output/Output_HTML_App.php';
require_once __DIR__.'/../../output/Output_TWIG_App.php';

require_once __DIR__.'/../../../fw/core/output/Output_CSV.php';

abstract class _Action_Support extends _Action_App {
	
    const PAGER_ARM_LENGTH = 5;

    const LOGIN_SESSION_KEY = 'LoginInfo';

    const ADMIN_PW_SALT = 'E2-sdWJT';

    protected $is_login_check = Env::IS_LOGIN_CHECK;

    protected $login_session = null;
    
    protected $menu_allow_no = null;

    protected $is_trace_operate_history = false;

    protected $ip_address = null;
    
    /**
     * @see _Action_App::initialize()
     */
    protected function initialize()
    {
        parent::initialize();
        
        if($this->is_login_check) {
        	if (Util_Session::exist(self::LOGIN_SESSION_KEY) !== true) {
        		$this->redirect_url = Env::APP_URL.'support/session_off/';
        		$this->redirect();
        		exit();
        	}
        }
        
        if ($this->is_login_check && !$this->checkAllowedServerAccess($_SERVER['REMOTE_ADDR']) ) {
            if (Util_Session::exist(self::LOGIN_SESSION_KEY) !== true) {
                $this->redirect_url = Env::APP_URL.'support/session_off/';
                $this->redirect();
                exit();
            }
        }
        
        $this->login_session = Util_Session::get(self::LOGIN_SESSION_KEY, false);
        $this->ip_address = $_SERVER['REMOTE_ADDR'];
        
    }
    /**
     * @see _Action::postAction()
     */
    protected function postAction() {
        parent::postAction();
    }
    
    /**
     * @see _Action::onError()
     */
    protected function onError(Exception $e) {
        parent::onError($e);
    }
    
    /**
     * (non-PHPdoc)
     * @see _Action_App::preAction()
     */
    protected function preAction()
    {
        parent::preAction();
        $this->output = new Output_HTML_App();

        if (!empty($this->login_session)) {
			
        	// 메뉴 접근 권한 확인
        	if (!$this->checkPagePermission()) {
        		$this->redirect_url =  Env::APP_URL.'support/authentication_error/';
        		return;
        	}
        	
            $this->output->assign('LOGIN_USER', $this->login_session);
        }
    }

    /**
     * 암호화 처리 md5
     *
     * @param $original_word 입력된 암호
     */
    protected function cryptPassword($original_word)
    {
        return md5($original_word.self::ADMIN_PW_SALT);
    }
    
    protected function checkPagePermission()
    {
    	if (!empty($this->menu_allow_no)) {
    		return $this->login_session->isAuthenticationMenu($this->menu_allow_no);
    	}
    	return true;
    }
    
    /**
     * URL회득
     */
    protected function getURL($host, $path = '', $param = '')
    {
        $ret = 'http://' . $host . Env::SEND_URL_BASE;
        if ($path != '') {
            $ret .= '/' . $path;
        }
        if ($param != '') {
            $ret .= '?' . $param;
        }
        return $ret;
    }

    /**
     * Env에 정의 된 패턴을 사용하여 허용 된 IP 주소에서 액세스 여부를 조사
     */
    protected function checkAllowedServerAccess($remote_addr)
    {
        foreach( Env::$ALLOWED_SERVER_PTN_ARRAY as $ptn ) {
            if( preg_match($ptn,$remote_addr)===1 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Env 선두에 정의 된의 WEB 서버에 URL을 가져옵니다.
     */
    protected function getFirstWebURL($path = '', $param = '')
    {
        return $this->getURL(Env::$WEB_SERVER_ARRAY[0], $path, $param);
    }

    /**
     * Env 선두에 정의 된의 WEB 서버에 요청한다.
     */
    protected function sendRequestFirstWeb($path = '', $param = '')
    {
        return $this->curl_get_contents($this->getFirstWebURL($path, $param));
    }

    /**
     * Env 선두에 정의 된의 WEB 서버에 요청한다.
     */
    protected function sendRequestFirstWebJson($path = '', $param = '')
    {
        if ($param == '') {
            $param .= 'json=1';
        } else {
            $param .= '&json=1';
        }
        return json_decode($this->sendRequestFirstWeb($path, $param), true);
    }

    /**
     * Env에 정의 된 인덱스의 WEB 서버에 요청한다.
     */
    protected function sendRequestWebJson($idx, $path = '', $param = '')
    {
        if ($param == '') {
            $param .= 'json=1';
        } else {
            $param .= '&json=1';
        }
        return json_decode($this->curl_get_contents($this->getURL(Env::$WEB_SERVER_ARRAY[$idx], $path, $param)), true);
    }
	
    /**
     * 	Env에 정의 된 인덱스의 WEB 서버에 요청한다. 또한 응답은 기다리지 않는다.
     */
    protected function sendAsyncRequestWeb($idx, $path = '', $param = '')
    {
        if ($path != '') {
            $path = '/' . $path;
        }

        if ($param == '') {
            $param .= 'json=1';
        } else {
            $param .= '&json=1';
        }
        $path .= '?' . $param;
        $path = Env::SEND_URL_BASE . $path;
        return $this->async_request(Env::$WEB_SERVER_ARRAY[$idx], $path);
    }

    /**
     * 요청 결과를 기다리지 않고 요청 (비동기 같은 요청)
     */
    protected function async_request($domain, $path, $port = 80)
    {
        $fp = fsockopen($domain, $port, $errno, $errstr, 5);
        if (!$fp) {
            return false;
        } else {
            $out = "GET " . $path . " HTTP/1.0\r\n";
            $out .= "Host: ".$domain."\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            fclose($fp);
        }
        return true;
    }

    /**
     * Env에 정의 된 모든 WEB 서버에 요청한다.
     */
    protected function sendRequestAllWeb($path = '', $param = '')
    {
        $ret = array();
        foreach (Env::$WEB_SERVER_ARRAY as $host) {
            $ret[] = $this->curl_get_contents($this->getURL($host, $path, $param));
        }
        return $ret;
    }

    /**
     * CSV 파일
     */
    protected function getCsvFiles($path)
    {
        $result = array();

        $dh = opendir($path);
        while ($file_name = readdir($dh))
        {
            $len = strlen($file_name);
            if ($len > 4) {
                $ext = substr($file_name, $len - 4);
                $ext = strtolower($ext);
                if ($ext == '.csv') {
                    $file_size = filesize($path.$file_name);
                    $file_size = $this->format_bytes($file_size);
                    $time_stamp = date('Y-m-d H:i:s', filemtime($path.$file_name));
                    $result[] = array(
                        'file_name' => $file_name,
                        'file_size' => $file_size,
                        'time_stamp' => $time_stamp
                    );
                }
            }
        }
        return $result;
    }
    
    /**
     * 파일 크기
     */
    protected function format_bytes($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return round($size, 2).$units[$i];
    }

    /**
     * CSV 파일 이동
     */
    protected function moveCSV($path)
    {
        $save_file = $path.$_FILES['upload_file']['name'];
        if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $save_file) === false) {
            throw new Exception("일시적으로 파일 복사에 실패했습니다.");
        }
    }

    protected function moveZIP($path)
    {
        //一時ファイルをUNZIP
        $tmp_file = $_FILES['upload_file']['tmp_name'];
        $zh = zip_open($tmp_file);
        if (!is_resource($zh)) {
            throw new Exception("ZIP파일의 오픈에 실패했습니다.");
        }

        //UNZIP処理開始
        //(ディレクトリを含まない1ファイルだけを圧縮している物を前提とする)
        $zip_entry = zip_read($zh);
        if ($zip_entry === false) {
            throw new Exception("ZIP파일 로딩에 실패했습니다.");
        }

        //解凍するファイル内にディレクトリが含まれていればエラーとする
        $unzip_file_name = zip_entry_name($zip_entry);
        //windows環境で動作させると
        //DIRECTORY_SEPARATORは\
        //unzip_file_nameのディレクトリは/
        //その為何も考えずにスラッシュでディレクトリと判定
        if (strpos($unzip_file_name, '/') !== false) {
            throw new Exception("ZIP파일에 디렉토리가 포함되어 있습니다.");
        }

        //拡張子のチェック
        $info = pathinfo($unzip_file_name);
        $ext =  strtolower($info['extension']);
        if ($ext != 'csv') {
            throw new Exception("ZIP파일에 CSV 이외의 파일이 압축되어 있습니다.");
        }

        //ファイル書き込み
        $save_file = $path.$unzip_file_name;
        $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        file_put_contents($save_file, $fstream);
        //ファイルが消せなくなると困るのでパーミッション変更しておく
        chmod($save_file, 0666);

        //リソース解放
        zip_entry_close($zip_entry);
        zip_close($zh);
    }

    protected function sendJsonResult($result_map) {
    	$this->output = null;
    	$json_output = json_encode($result_map);
    	echo $json_output;
    }
    
    protected function setErrorMessage($message) {
    	$result_map = array();
    	if(strlen($message) > 0) {
    		$result_map['status'] = false;
    		$result_map['message'] = $message;
    
    		$this->resultPut($result_map);
    		exit();
    	}
    }
}
