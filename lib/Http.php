<?php
    
class Plat_Http {
    const HEAD = true;
    const NOHEAD = false;
    const NOBODY = true;
    const BODY = false;
    
    private $curl_handle;
    private $data;
    private $cookie;
    public $error;
    
    public function __construct()           
    {
        $this->curl_handle = curl_init();
    }

    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
    }
    
    public function Get($url, $show_header=false)
    {
	    $ch = $this->curl_handle;    
	    curl_setopt($ch, CURLOPT_URL,$url);     
	    curl_setopt($ch, CURLOPT_HEADER, $show_header);    
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);     
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		//curl_setopt($ch, CURLOPT_PROXY, "http://10.65.44.168:8888");
	    if(isset($this->cookie))curl_setopt($ch, CURLOPT_COOKIE,$this->cookie);

	    $data = curl_exec($ch);        
	    $this->data = curl_exec($this->curl_handle);
	    if($this->data === false)
	    {
		    $this->error = curl_error($this->curl_handle);
	    }
    }


    public function Post($url,$data='',$show_header=false,$nobody=false,$header=array())
    {
        $this->error='';
        unset($this->data);
        
        curl_setopt($this->curl_handle,CURLOPT_URL,$url);
        if($show_header)curl_setopt($this->curl_handle,CURLOPT_HEADER, $show_header);
        curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl_handle, CURLOPT_POST, true);
        curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->curl_handle, CURLOPT_TIMEOUT, 3);
		//curl_setopt($this->curl_handle, CURLOPT_PROXY, "http://10.65.44.168:8888");
        if(isset($this->cookie))curl_setopt($this->curl_handle, CURLOPT_COOKIE,$this->cookie);
        if($nobody)curl_setopt($this->curl_handle, CURLOPT_NOBODY, true);

        $this->data = curl_exec($this->curl_handle);
        if($this->data === false)
        {
            $this->error = curl_error($this->curl_handle);
        }
    }

    public function PostFile($url,$content,$filename,$postname='object_file')
    {
        $this->error = '';
        unset($this->data);

        if (is_file($filename)) {
            $data = array(
                $postname => new CURLFile(realpath($filename)),
            );
        }
        else {
            $tmpfile = tempnam('./', 'tmp');
            file_put_contents($tmpfile, $content);
            $data = array(
                $postname => new CURLFile(realpath($tmpfile)),
            );
        }

        curl_setopt($this->curl_handle,CURLOPT_URL,$url);
        //if($show_header)curl_setopt($this->curl_handle,CURLOPT_HEADER, $show_header);
        curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl_handle, CURLOPT_POST, true);
        curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl_handle, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->curl_handle, CURLOPT_TIMEOUT, 15);
        if(isset($this->cookie))curl_setopt($this->curl_handle, CURLOPT_COOKIE,$this->cookie);
        if(!empty($nobody))curl_setopt($this->curl_handle, CURLOPT_NOBODY, true);

        $this->data = curl_exec($this->curl_handle);
        if($this->data === false)
        {
            $this->error = curl_error($this->curl_handle);
        }

        if (is_file($tmpfile)) {
            unlink($tmpfile);
        }

    }

    public function getContent()
    {
        return $this->data;
    }
    public function Close()
    {
        curl_close($this->curl_handle);
    }
    public function getError()
    {
        return $this->error;
    }
}
    
?>
