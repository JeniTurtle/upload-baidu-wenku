<?php
	class Log {

		public $outputLevel;
		protected $filepath;
		protected $fieldSep;
		protected $paramSep;

	 	const LEVEL_ALL   = 0x11111111;
  		const LEVEL_NOTIC = 0x00000001;
  		const LEVEL_DEBUG = 0x00000010;
        const LEVEL_WARNI = 0x00000100;
        const LEVEL_ERROR = 0x00010000;
 		const LEVEL_OFF   = 0x00000000;	        

		public function Log($filepath) {
			$this->outputLevel = 0x11111111;
			$this->filepath = $filepath;

			$this->fieldSep = "  ".chr(8);
			$this->paramSep = " ".chr(7);
		}

		public function notice($str, $file = "", $line = 0, $errno = 0, 
							   $arrArgs = null, $arrTimes = null) {
			if ((self::LEVEL_NOTIC & $this->outputLevel) > 0) {
				$this->processLog($str, 0, $file, $line, $arrTimes, 
								  $arrArgs, 'NOTIC');
			}
        }
		public function debug($str, $file = "", $line = 0, $errno = 0, 
							  $arrArgs = null, $arrTimes = null) {
			if((self::LEVEL_DEBUG & $this->outputLevel) > 0) {
				$this->processLog($str, 0, $file, $line, $arrTimes, 
								  $arrArgs, 'DEBUG');
			}
        }
		public function warning($str, $file, $line, $errno, 
							  $arrArgs = null, $arrTimes = null) {
			if((self::LEVEL_WARNI & $this->outputLevel) > 0) {
				$this->processLog($str, $errno, $file, $line, $arrTimes, 
								  $arrArgs, 'WARNI');
			}
        }
		public function error($str, $file, $line, $errno, 
							  $arrArgs = null, $arrTimes = null) {
			if((self::LEVEL_ERROR & $this->outputLevel) > 0){
				$this->processLog($str, $errno, $file, $line, $arrTimes, 
								  $arrArgs, 'ERROR');
			}
		}
        private function processLog($str, $errno, $file, $line, 
									$arrTimes, $arrArgs, $level) {
			$filepath = $this->filepath;	
			if ($level == 'WARNI' || $level == 'ERROR') {
				$filepath .= '.wf';
			}
			if (!is_string($str)) {
				$str = '';
			}
			$pos = strrpos($file, '/');
			if (false === $pos || $pos >= strlen($file) - 1) {
				$filename = 'unknown';
			} else {
				$filename = substr($file, $pos + 1);	
			}

			$timesStr = '';
			if (is_array($arrTimes) && ($cnt = count($arrTimes)) > 0) {
				$i = 0;
				foreach ($arrTimes as $key => $value) {
					if ($i == $cnt - 1) {
						$timesStr .= $key.':'.$value;
					} else {
						$timesStr .= $key.':'.$value.$this->paramSep;
					}
					$i ++;
				}
			}

			$argsStr = '';
			if (is_array($arrArgs) && ($cnt = count($arrArgs)) > 0) {
				$i = 0;
				foreach ($arrArgs as $key => $value) {
					if ($i == $cnt - 1) {	
						$argsStr .= $key.':'.$value;
					} else {
						$argsStr .= $key.':'.$value.$this->paramSep;
					}
					$i ++;
				}
			}
			$logId = 1;
			if (defined("LOG_ID")) {
				$logId = LOG_ID;
			}
			$content = $level.$this->fieldSep
					  .$logId.$this->fieldSep
				  	  .date('Ymd H:i:s').$this->fieldSep
				      .intval($errno).$this->fieldSep
					  .$filename.':'.intval($line).$this->fieldSep
					  .$str	
				      .$timesStr.$this->fieldSep
		 		      .$argsStr.$this->fieldSep
					  ."\n";
			file_put_contents($filepath, $content, FILE_APPEND);
		}
	}
?>
