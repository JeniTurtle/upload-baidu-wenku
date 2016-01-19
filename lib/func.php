<?php
/**
* @file func.php
* @author Tomorrow
* @description 文件格式
*  
**/

	define("NONE",0);
	define("WORD2003",1);
	define("EXCEL2003",2);
	define("PPT2003",3);
	define("WORD2007",4);
	define("EXCEL2007",5);
	define("PPT2007",6);
	define("PDF",7);
	define("TXT",8);
	define("WPS",9);
	define("ET",10);
	define("DPS",11);
	define("VSD",12);
	define("RTF",13);
	define("POT",14);
	define("PPS",15);
	define("EPUB",16);

	define("EXT_NONE",'');
	define("EXT_WORD2003",'doc');
	define("EXT_EXCEL2003",'xls');
	define("EXT_PPT2003",'ppt');
	define("EXT_WORD2007",'docx'); 
	define("EXT_EXCEL2007",'xlsx'); 
	define("EXT_PPT2007",'pptx'); 
	define("EXT_PDF",'pdf');
	define("EXT_TXT",'txt');
	define("EXT_WPS",'wps');
	define("EXT_ET",'et'); 
	define("EXT_DPS",'dps');
	define("EXT_VSD",'vsd');
	define("EXT_RTF",'rtf');
	define("EXT_POT",'pot');
	define("EXT_PPS",'pps');
	define("EXT_EPUB",'epub');    

	define("SHOW_TYPE_NONE",'');
	define("SHOW_TYPE_DOC",'doc');
	define("SHOW_TYPE_TXT",'txt');
	define("SHOW_TYPE_PPT",'ppt');

	function getTypeByExt($ext){
		$map=array(
			EXT_NONE=>NONE,
			EXT_WORD2003=>WORD2003,
			EXT_EXCEL2003=>EXCEL2003,
			EXT_PPT2003=>PPT2003,
			EXT_WORD2007=>WORD2007,
			EXT_EXCEL2007=>EXCEL2007,
			EXT_PPT2007=>PPT2007,
			EXT_PDF=>PDF,
			EXT_TXT=>TXT,
			EXT_WPS=>WPS,
			EXT_ET=>ET,
			EXT_DPS=>DPS,
			EXT_VSD=>VSD,
			EXT_RTF=>RTF,
			EXT_POT=>POT,
			EXT_PPS=>PPS,
			EXT_EPUB=>EPUB,
		);

		return isset($map[$ext])?$map[$ext]:NONE;
	}
?>
