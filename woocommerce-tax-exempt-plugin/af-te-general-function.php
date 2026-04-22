<?php

function af_te_download_file( $file_path, $aftax_upload_file_name ) {


	$ext = pathinfo($aftax_upload_file_name, PATHINFO_EXTENSION);


	header('Content-Description: File Transfer');
	header('Content-Type: application/' . $ext);
	header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_path));

	ob_clean();
	flush();
	readfile($file_path, true);
	wp_die();
}
