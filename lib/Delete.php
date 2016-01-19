<?php
/**
* @file Delete.php
* @author Tomorrow
* @description 文库删除文件类
*  
**/

define('BASE_URL',     'http://cq02-iknow-tips00.cq02.baidu.com:8099/');
define('DELETE_URL',	BASE_URL . 'mischeck/submit/docfail');
define('ALLOW_BATCH_KEY',		'62df4e84dbf7ee35c036d84b228e11ad');

class Plat_Delete
{
	protected $_errno;
	protected $_errmsg;
	protected $_libPlatHttp; 
	protected $_rand;
	protected $_preRet;
	protected $_objectList;
	protected $_url;
	protected $_flag;
	protected $_smartKey;
	protected $_cookie;
	protected $_bduss;
	protected $_doc_id;
	protected $_oriRet;

	public function __construct($cookie)
	{
		$this->_libPlatHttp = new Plat_Http();
		$this->_errno = 0;
		$this->_errmsg = '';
		$this->_cookie = $cookie;
		$this->_libPlatHttp->setCookie($cookie);
	}

	public function deleteDoc($ct, $lu, $from, $doc_id, $direct_post, $opuid, $opuname, $token, $power, $t, $sub_status)
	{
		$data = array(
			'ct' => $ct,
			'lu' => $lu,
			'from' => $from,
			'doc_id' => $doc_id,
			'direct_post' => $direct_post,
			'opuid' => $opuid,
			'opuname' => $opuname,
			'token' => $token,
			'power' => $power,
			't' => $t,
			'sub_status' => $sub_status,
        );

        //$data = http_build_query($data);
		$data = $this->Array2ReqBody($data);
		$url = DELETE_URL; 

		$ret = $this->_libPlatHttp->Post($url, $data);
		$content = $this->_libPlatHttp->getContent();
		$error = $this->_libPlatHttp->getError();

		if ($error !== '') {
			$this->_errno = 500;
			$this->_errmsg = $error;
			Plat_Log::fatal($error, 500);
			Plat_Log::fatal("update error:$doc_id");
			return false;
		}

		$delete_ret = json_decode($content, true);

		return $delete_ret;
	}

	public function getErrno()
	{
		return $this->_errno;
	}

	public function getErrmsg()
	{
		return $this->_errmsg.":".$this->_rand;
	}

	public function getOriRet()
	{
		return $this->_oriRet;
	}

    private function Array2ReqBody(array $arr) {
        $reqBody = "";
        foreach ($arr as $key => $value) {
            $reqBody .= $key . "=" . urlencode(urlencode($value)) . "&";
        }
        $len = strlen($reqBody);
        return substr($reqBody, 0, $len - 1);
    }
}


/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
?>
