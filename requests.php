<?php

include 'config.php';

$url = $_GET['url'];
$url .= $key;

function getSSLPageContents($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, "instancedev.com");

		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$result = curl_exec($ch);

		curl_close($ch);
		return $result;
}

echo(getSSLPageContents($url));

?>