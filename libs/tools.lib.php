<?php
// Start Tool class
class Tools {

// Small function to calculate pagination, based on length and current page.
 public function arrayPagination ($pswds,$length,$page) {

 $offset = $page*$length-$length;
 $pswds = array_slice($pswds,$offset,$length,'preserve_keys');
 return $pswds;
	
 }

// Function that check if the all needed directories exist and if not create them. Also creates all the needed file if it has just created all the directories.
 public function checkDir ($dir) {

 $initXml = new Xml();

 $i = 0;

  if (!is_writable($dir)) {
        die('Cannot write to '.$dir.' ! Please check you server and allow access to apache user!');
  }

  if (!file_exists($dir.'echoes')) {
   if (!mkdir($dir.'echoes')) {
        die('Cannot create '.$dir.'echoes directory! Please check you server and allow access to apache user!');
   }
   $i++;
  }

  if (!file_exists($dir.'echoes/xml')) {
   if (!mkdir($dir.'echoes/xml')) {
        die('Cannot create '.$dir.'echoes/xml directory! Please check you server and allow access to apache user!');
   }
   $i++;
  }

  if (!file_exists($dir.'echoes/logs')) {
   if (!mkdir($dir.'echoes/logs')) {
        die('Cannot create '.$dir.'echoes/logs directory ! Please check you server and allow access to apache user!');
   }
   $i++;
  }

  if ($i == 3) {
        $initXml->createXml('users');
        $initXml->createXml('passwords');
        $initXml->createXml('penalties');
        $initXml->createXml('ca');
  } 
 }

// Check if country value for CA wizard is ony capital and maximum 4 characters. 
 public function checkCountry ($string) {
	if ((!preg_match('/[^A-Z\.]/', $string)) && (strlen($string) <= 4)) {
		return $string;
	} else {
		return 0;
 	}
 }

// The function that changes a user's password and calculates all the domains passwords again with the new sKey.
 public function changeUserPassword($user,$password) {
	
	$initCert = new Cert();
	$initXml = new Xml();

	$userPassword = $initCert->usersKeyauthHash($user,$password);
	$initXml->editUserPassword($user,$userPassword['authHash']);

	$domainPasswords = $initXml->dumpPasswords($user);
	foreach ($domainPasswords as $domainPassword) {
		$cleanPassword=$initCert->decryptPassword($_SESSION['sKey'],base64_decode($domainPassword['password']));
        	$pwd = $initCert->encryptPassword($userPassword['sKey'],strip_tags($cleanPassword));
        	$initXml->editPassword($user,$domainPassword['domain'],$domainPassword['username'],base64_encode($pwd),base64_encode($initCert->verifyPassword($pwd,$domainPassword['domain'],$initXml->getCAdata()['privateKey'],$userPassword['sKey'])));
	}	
	$_SESSION['sKey'] = $userPassword['sKey'];
 }

// A function to check and add penalty for a user. If the user pass the ten wrong tries, it bans him.
 public function checkPenalty($user,$penalty) {

	$initXml = new Xml();

	if (empty($penalty)) {
		$initXml->addPenalty($user,time(),2);
	} else {
		if ((0 < $penalty[1]) && ($penalty[1] < 1024)) {
			$initXml->editPenalty($user,time(),$penalty[1]+$penalty[1]);
		} else {
			$initXml->editPenalty($user,time(),0);
		}
	}
 }

// A function to create CA's certificate, the proecdure is kinda the same as a new user's.
 public function newCAcert ($dn) {

	$initCert = new Cert();
	$initXml = new Xml();

	$newKeysCsrCert=$initCert->getKeysCsrCert($dn,NULL,NULL,"365");
	$initXml->createXml('ca');
	$initXml->saveCAdata($newKeysCsrCert['privateKey'],$newKeysCsrCert['publicKey'],$newKeysCsrCert['csr'],$newKeysCsrCert['cert']);

	if ((is_array($initXml->getCAdata())) && (!empty($initXml->getCAdata()['privateKey']))) {
		return 1;
	} else {
		return 0;
	}
 }

// A function to check if the supplied string has an email format.
 public function checkEmail ($string) {
	if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
		return $string;
	} else {
		return 0;
	}
 }

// A function that checks if the string has only the allowed characters. This way we leave out any malicious characters.
 public function checkCharacters ($string) {
	if (!preg_match('/[^a-zA-Z0-9\.]/', $string)) {
		return $string;
	} else {
		return 0;
 	}
 }

// We check the length of our input, we don't want to fill out with dumps of text.
 public function checkLength ($string) {
	if ((strlen($string) >= 10) && (strlen($string) <= 20)) {
		return $string;
	} else {
		return 0;
	}
 }

// A function to send all the needed http headers to force the browser to start a download with our zip file.
 public function send2Browser ($file,$filename) {

	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($filename));
 		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: public');
		header('Pragma: public');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file));
		ob_end_clean();
		readfile($file);

	}
	
	unlink($file);

 }

// A function that builds our zip file in the /tmp system directory and returns it for download.
 public function createArchive ($files,$filename) {

	$zipfilename = $filename;
	$zipfilename .= ".zip";

	$zipfile = tempnam("/tmp","tmp");
    
	// Create object
	$zip = new ZipArchive();
 
	// Open output file for writing
	if ($zip->open($zipfile, ZIPARCHIVE::CREATE) !== TRUE) {
		die ("Could not open archive");
	}
 
	// Add files to zip archive
	foreach ($files as $file) {
		$zip->addFromString($file[1], $file[0]) or die ("ERROR: Could not add file");    
	}
	$zip->addFromString("README.txt", "Your zip archive include's your pulic and private keys, CSR and certificate.");
 
 
	// Close and save archive
	$zip->close();
 
	return array($zipfile,$zipfilename);
 }

}
?>
