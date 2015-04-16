<?php

function getUrlWithoutSubdirectoriesOfIndex($requesturi, $dir) {
	
	$requesturi = parse_url($requesturi, PHP_URL_PATH); // iba cesta, bez query položiek
	$requesturi = str_replace("\\", "/", $requesturi);  // nahradenie spätných lomítok za klasické
	$dir = str_replace("\\", "/", $dir);
	
	$url = str_replace(" ", "", trim($requesturi, "/")); // odstranenie lomitok zo začiatku a konca a odstranenie
	$dir = str_replace(" ", "", trim($dir, "/"));        // bieleho miesta

	// nájde najdlhšiu zhodu $url od začiatku a $dir od konca
	$match = 0;
	for ($i = 0; $i <= strlen($url); $i++) {
		if ($i > strlen($dir) - 1) {
			break;
		}
		if (substr($url, 0, $i) === substr($dir, strlen($dir) - $i)) {
			$match = $i;
		}
	}
	return trim(substr($url, $match), "/");
}

function appRootPath() {
    return __DIR__ . "/../../../../../../";
}