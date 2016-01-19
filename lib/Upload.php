<?php
 
/**
 * @file Upload.php
 * @author Tomorrow
 * @description 文库上传类
 *  
 **/

$base_urls = array(
	'http://wenku.baidu.com/',
);

$base_url = $base_urls[0];


define('BASE_URL', $base_url);
define('DOCINFO_BASE_URL', $base_url);
define('PRE_URL', BASE_URL.'doc/submit/preupload?');
define('NEW_UPLOAD', BASE_URL.'doc/submit/newupload?ct=20001&bduss=%s&object_key=%s&ext=%s&size=%s&smart=%s&flag=%s&doc_md5=%s&object_md5=%s&title=%s');
define('COMPLETE_URL', BASE_URL.'doc/submit/uploadcomplete?');
define('DOCINFO_URL', DOCINFO_BASE_URL.'doc/interface/getdocinfo?');
define('AUTOCLASS_URL', BASE_URL.'doc/interface/autoclass?');
define('UPDATE_URL', BASE_URL.'doc/submit/update?');
define('ALLOW_BATCH_KEY', '62df4e84dbf7ee35c036d84b228e11ad');

class Plat_Upload {
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
		$this->_libPlatHttp->setCookie($cookie.';batchKey='.ALLOW_BATCH_KEY);
	}

	protected function genUploadKey($fileSize) {
		$today = date("Ymd");
		// $todayHstr = fcrypt_id_2hstr('doc19820829!', $today, 0);
		$smartKey = md5($fileSize.$today);
		$this->_smartKey = $smartKey;
		return $smartKey;
	}

	protected function proUrl($url)
	{
		$url = $url . '&rand=' . $this->_rand;
		$this->_url = $url;
		return $url;
	}

	protected function preUpload($title, $ext, $doc_md5, $size)
	{
		if (empty($title) || empty($ext) || empty($doc_md5) || empty($size)) {
			$this->_errmsg = 'preUpload error param';
			$this->_errno = 403;
			Plat_Log::fatal('preUpload error param', 403);
			return false;
		}

		$smart = self::genUploadKey($size);

		$data = array(
			'title' => $title,
			'ext' => $ext,
			'doc_md5' => $doc_md5,
			'smart' => $smart,
			'size' => $size,
		);
		$data = http_build_query($data);
		$url = self::proUrl(PRE_URL);

		$ret = $this->_libPlatHttp->Post($url, $data);
		$content = $this->_libPlatHttp->getContent();
		$error = $this->_libPlatHttp->getError();
		$this->_oriRet[] = $content;
		if ($error !== '') {
			$this->_errno = 500;
			$this->_errmsg = $error;
			Plat_Log::fatal($error, 500);
			return false;
		}

		$this->_preRet = json_decode($content, true);
		if ($this->_preRet['errno'] != 0 && $this->_preRet['errno'] != 41) {
			$this->_errno = $this->_preRet['errno'];
			$this->_errmsg = 'server failed:'.$content;
			Plat_Log::fatal($this->_errmsg, $this->_errno);
			return false;
		}

		//Plat_Log::debug('preupload done');
		return true;
	}

	protected function newUpload($title, $ext, $doc_md5, $size, $path)
	{
		$this->_objectList = array();
		$object_size = $this->_preRet['object_size'];
		$count =ceil($size / $object_size);
		$file = file_get_contents($path);
		for ($i=0; $i<$count; $i++) {
			$start = $object_size * $i;
			$object = substr($file, $start, $object_size);
			$object_md5 = md5($object);
			//cookie=%s&object_key=%s&ext=%s&size=%s&smart=%s&flag=%s&doc_md5=%s&object_md5=%s&title=%s
			$url = sprintf(NEW_UPLOAD, str_replace('BDUSS=', '', $this->_cookie), $i, $ext, $size, 
				$this->_smartKey, $this->_flag, $doc_md5, $object_md5, $title);
			$url = self::proUrl($url);

			$flag = 0;
			for ($j=0; $j<5; $j++) {
				$this->_libPlatHttp->PostFile($url, $object, $title.".$ext");
				$content = $this->_libPlatHttp->getContent();
				$error = $this->_libPlatHttp->getError();
				$this->_oriRet[] = $content;
				if ($error != '') {
					$this->_errno = 501;
					$this->_errmsg = $error;
					Plat_Log::fatal($error, $this->_errno);
					continue;
				}

				$tmp = $content;
				$content = json_decode($content, true);
				if (!is_array($content) || $content['errno'] != 0) {
					$this->_errno = $content['errno'];
					$this->_errmsg = "upload object:$i failed";
					Plat_Log::fatal($url, $this->_errno);
					continue;
				}

				$this->_errno = 0;
            	$this->_errmsg = '';
				$flag = 1;
				break;
			}

			if (1 == $flag) {
				$this->_objectList[] = $object_md5;
			}
			else {
				return false;
			}
		}

		//Plat_Log::debug('newupload done');
		return true;
	}

	protected function complete($title, $ext, $doc_md5, $size)
	{
		$data = array(
			'fold_id' => 0,
			'object_info' => @implode(',', $this->_objectList),
			'doc_md5' => $doc_md5,
            'title' => $title,
            'ext' => $ext,
			'smart' => $this->_smartKey,
            'size' => $size,
			'flag' => $this->_flag,
        );
        $data = http_build_query($data);
        $url = self::proUrl(COMPLETE_URL);

		$flag = 0;
		for ($i=0; $i<5; $i++) {
			$ret = $this->_libPlatHttp->Post($url, $data);
			$content = $this->_libPlatHttp->getContent();
			$error = $this->_libPlatHttp->getError();
			$this->_oriRet[] = $content;
			if ($error !== '') {
				$this->_errno = 502;
				$this->_errmsg = $error;
				Plat_Log::fatal($error, $this->_errno);
				continue;
			}
			
			preg_match("/type=\"(\d+)\" docid=\"(\w+)\"/", $content, $out);
			$error = $out[1];
			$this->_doc_id = $out[2];
			if ($error != 0 && $error != 41) {
				$this->_errno = 502;
				$this->_errmsg = $error;
				Plat_Log::fatal($content, $this->_errno);
				//sleep(1);
				continue;
			}

			$this->_errno = 0;
            $this->_errmsg = '';
			$flag = 1;
			break;
		}

		if ($flag != 1) {
			return false;
		}
		
		//Plat_Log::debug('complete done');
		return true;
	}

	public function run($title, $ext, $path, $flag=0)
	{
		$this->_preRet = array();
		$this->_rand = time() . '_' . rand(100,200);
		$this->_errno = 0;
		$this->_errmsg = '';
		$this->_oriRet = array();
		$this->_flag = $flag;

		//Plat_Log::addNotice('rand', $this->_rand);
		//Plat_Log::debug('rand:'.$this->_rand);
		//Plat_Log::addNotice('title', $title);
		Plat_Log::debug('title:'.$title);

		if (!is_file($path)) {
			$this->_errno = 404;
			$this->_errmsg = 'file not found:'.$title;
			Plat_Log::fatal($this->_errmsg, $this->_errno);
			return false;
		}
		
		$doc_md5 = md5_file($path);
		$size = filesize($path);

		//Plat_Log::addNotice('size', $size);
		//Plat_Log::addNotice('doc_md5', $doc_md5);
		//Plat_Log::debug('size:'.$size);
		//Plat_Log::debug('doc_md5:'.$doc_md5);

		$ret = $this->preUpload($title, $ext, $doc_md5, $size);
		if (!$ret) {
		    Plat_Log::debug('preUpload failed');
			return false;
		}
		$repeat = $this->_preRet['repeat'];
		if (!$repeat) {
			$ret = $this->newUpload($title, $ext, $doc_md5, $size, $path);
			if (!$ret) {
				return false;
			}
		}
		
		$ret = $this->complete($title, $ext, $doc_md5, $size);
		if (!$ret) {
			return false;
		}
		
		//Plat_Log::addNotice('doc_id', $this->_doc_id);
		Plat_Log::debug('doc_id:'.$this->_doc_id);
		//Plat_Log::notice('upload success', 0);
		//Plat_Log::debug('upload success');
		return $this->_doc_id;
	}

	public function update($title, $summary, $cid1, $cid2, $cid3, $cid4, $tag_str, $privacy, $flag,
	$pay_price, $free_page, $downloadable, $doc_id, $new_upload)
	{
		$data = array(
			'title_arr[0]' => $title,
			'summary_arr[0]' => $summary,
			'cid1_arr[0]' => $cid1,
			'cid2_arr[0]' => $cid2,
			'cid3_arr[0]' => $cid3,
			'cid4_arr[0]' => $cid4,
			'tag_str_arr[0]' => $tag_str,
			'privacy_arr[0]' => $privacy,
			'flag' => $flag,
			'pay_price_arr[0]' => $pay_price,
			'free_page_arr[0]' => $free_page,
			'downloadable_arr[0]' => $downloadable,
			'ct' => 20002,
			'oe' => 'json',
			'retType' => 'newResponse',
			'doc_id_arr[0]' => $doc_id,
			'new_upload' => $new_upload,
			'mod' => 1,
			'encode' => 'utf8',
        );

        //$data = http_build_query($data);
		$data = $this->Array2ReqBody($data);
		$url = UPDATE_URL; 

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

		$update_ret = json_decode($content, true);

		return $update_ret;
	}

	public function updateFree($title, $summary, $cid1, $cid2, $cid3, $cid4, $tag_str, $privacy, $flag, $downloadable, $doc_id, $new_upload)
    {
		$data = array(
			'title_arr[0]' => $title,
			'summary_arr[0]' => $summary,
			'cid1_arr[0]' => $cid1,
			'cid2_arr[0]' => $cid2,
			'cid3_arr[0]' => $cid3,
			'cid4_arr[0]' => $cid4,
			'tag_str_arr[0]' => $tag_str,
			'privacy_arr[0]' => $privacy,
			'flag' => $flag,
			'price_arr[0]' => 0,
			'price' => $price,
			'downloadable_arr[0]' => $downloadable,
			'ct' => 20002,
			'oe' => 'json',
			'retType' => 'newResponse',
			'doc_id_arr[0]' => $doc_id,
			'new_upload' => $new_upload,
			'mod' => 1,
			'encode' => 'utf8',
        );

        //$data = http_build_query($data);
		$data = $this->Array2ReqBody($data);
		$url = UPDATE_URL; 

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

		$update_ret = json_decode($content, true);

		return $update_ret;
	}

	public function getDocInfo($doc_id=null, $type='json')
	{
		if( empty($doc_id) )
		{
			$doc_id = $this->_doc_id;
		}

		$data = array(
			'idlist' => $doc_id,
			'type' => $type,
        );

        $data = http_build_query($data);
		$url = DOCINFO_URL; 

		$ret = $this->_libPlatHttp->Post($url, $data);
		$content = $this->_libPlatHttp->getContent();
		$error = $this->_libPlatHttp->getError();

		if ($error !== '') {
			$this->_errno = 500;
			$this->_errmsg = $error;
			Plat_Log::fatal($error, 500);
			return false;
		}

		$doc_info = json_decode($content, true);

		return $doc_info;
	}

	public function getAutoClass($doc_id, $title, $size, $type, $index=1, $summary='', $app='getClass')
	{
		$data = array(
			'app' => $app,
			'doc_id' => $doc_id,
			'title' => $title,
			'summary' => $summary,
			'size' => $size,
			'type' => $type,
			'index' => $index,
        );

        $data = http_build_query($data);
		$url = AUTOCLASS_URL; 

		$ret = $this->_libPlatHttp->Post($url, $data);
		$content = $this->_libPlatHttp->getContent();
		$error = $this->_libPlatHttp->getError();

		if ($error !== '') {
			$this->_errno = 500;
			$this->_errmsg = $error;
			Plat_Log::fatal($error, 500);
			return false;
		}

		$auto_class = json_decode($content, true);

		return $auto_class;
	}

//	public function testUrl($url)
//	{
//		$data = array(
//			'app' => $app,
//			'doc_id' => $doc_id,
//			'title' => $title,
//			'summary' => $summary,
//			'size' => $size,
//			'type' => $type,
//			'index' => $index,
//        );
//
//        $data = http_build_query($data);
//		$ret = $this->_libPlatHttp->Post($url, $data);
//		$content = $this->_libPlatHttp->getContent();
//		$error = $this->_libPlatHttp->getError();
//
//		if ($error !== '') {
//			$this->_errno = 500;
//			$this->_errmsg = $error;
//			Plat_Log::fatal($error, 500);
//			return false;
//		}
//
//		return $content;
//	}

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

?>
