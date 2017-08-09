<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'EBBSMateDownloader' ) ) {
	die();
}

class EBBSMateDownloader extends PavoBBSMateController {
	
	function __construct($board_id, $post_id, $file_name){
		$this->board_id = $board_id;
		$this->post_id = $post_id;
		$this->file_name = $file_name;
		
		$upload_dir = wp_upload_dir();
		$str = str_replace('\\', '/', $upload_dir['basedir']);
		$this->file_path = $str."/ebbsmate_attachments/".$post_id."/".$file_name;

		$this->filesize = filesize($this->file_path);
		$this->path_parts = pathinfo($this->file_path);
		$this->extension = $this->path_parts['extension'];
	}
	
	
	public function download(){
		$role_check = pavoebbsmate_post_role_check($this->board_id , $this->post_id , "readpost" );
		
		if($role_check["role_check"]){
			switch($this->extension) {
				case "txt":
					$mimeType = "text/plain";
				case "htm":
					$mimeType = "text/html";
				case "html":
					$mimeType = "text/html";
				case "php":
					$mimeType = "text/html";
				case "css":
					$mimeType = "text/css";
				case "js":
					$mimeType = "application/javascript";
				case "json":
					$mimeType = "application/json";
				case "xml":
					$mimeType = "application/xml";
				case "swf":
					$mimeType = "application/x-shockwave-flash";
				case "flv":
					$mimeType = "video/x-flv";
			
					// images
				case "png":
					$mimeType = "image/png";
				case "jpe":
					$mimeType = "image/jpeg";
				case "jpeg":
					$mimeType = "image/jpeg";
				case "jpg":
					$mimeType = "image/jpeg";
				case "gif":
					$mimeType = "image/gif";
				case "bmp":
					$mimeType = "image/bmp";
				case "ico":
					$mimeType = "image/vnd.microsoft.icon";
				case "tiff":
					$mimeType = "image/tiff";
				case "tif":
					$mimeType = "image/tiff";
				case "svg":
					$mimeType = "image/svg+xml";
				case "svgz":
					$mimeType = "image/svg+xml";
			
					// archives
				case "zip":
					$mimeType = "application/zip";
				case "rar":
					$mimeType = "application/x-rar-compressed";
				case "exe":
					$mimeType = "application/x-msdownload";
				case "msi":
					$mimeType = "application/x-msdownload";
				case "cab":
					$mimeType = "application/vnd.ms-cab-compressed";
			
					// audio/video
				case "mp3":
					$mimeType = "audio/mpeg";
				case "qt":
					$mimeType = "video/quicktime";
				case "mov":
					$mimeType = "video/quicktime";
			
					// adobe
				case "pdf":
					$mimeType = "application/pdf";
				case "psd":
					$mimeType = "image/vnd.adobe.photoshop";
				case "ai":
					$mimeType = "application/postscript";
				case "eps":
					$mimeType = "application/postscript";
				case "ps":
					$mimeType = "application/postscript";
			
					// ms office
				case "doc":
					$mimeType = "application/msword";
				case "rtf":
					$mimeType = "application/rtf";
				case "xls":
					$mimeType = "application/vnd.ms-excel";
				case "ppt":
					$mimeType = "application/vnd.ms-powerpoint";
			
					// open office
				case "odt":
					$mimeType = "application/vnd.oasis.opendocument.text";
				case "ods":
					$mimeType = "application/vnd.oasis.opendocument.spreadsheet";
				default:
					$mimeType = "application/octet-stream";
			}
			
			Header("Pragma: no-cache");
			Header("Cache-Control: cache, must-revalidate, post-check=0, pre-check=0");
			header("Expires: 0");
			header("Content-Description: File Transfer");
			header("Content-Type: ".$mimeType);
			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename=\"$this->file_name\"");
			header("Content-Transfer-Encoding: binary");
			Header("Content-Transfer-incoding: euc_kr");
			header("Content-Length: $this->filesize");
			
			flush();
			ob_clean();
			
			readfile($this->file_path);
			exit;
		}else{
			$message = "<script>alert('".$role_check["message"]."');</script>";
			echo $message;
// 			include PavoBoardMate::$PLUGIN_DIR.'templates/front/pavo-bbs-error.php';
			return;
		}
	}
}