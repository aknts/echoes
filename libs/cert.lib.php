<?php
// Begin Cert class
class Cert {

// Generates a new key pair, a csr request which sings and generates a x509 certificate.
// Everything is returned with an array for further user.
// If the CA value at the begining is empty a self signed certificate is being generated.
// Log entries are being generated for examination day.
 public function getKeysCsrCert ($dn, $ca, $privateCaKey, $days) {

	$initLog = new Log();

	$newKeyPair = openssl_pkey_new(array("private_key_bits" => 2048,"private_key_type" => OPENSSL_KEYTYPE_RSA));
        $csr = openssl_csr_new($dn, $newKeyPair, array('digest_alg' => 'sha256'));
	if ($ca == NULL) {
		$privateCaKey = $newKeyPair;
	}
	$x509 = openssl_csr_sign($csr, $ca, $privateCaKey, $days, array('digest_alg' => 'sha256'));
	openssl_pkey_export($newKeyPair, $privateKey);
	$details = openssl_pkey_get_details($newKeyPair);
	$publicKey = $details['key'];
	openssl_csr_export($csr, $csrExport);
	openssl_x509_export($x509, $certExport);
	$initLog->writeLog('User with email '.$dn['emailAddress'].' created a new key pair.',$newKeyPair);
	$initLog->writeLog('User with email '.$dn['emailAddress'].' created a new csr request.',$csr);
	$initLog->writeLog('CSR request created by user with email '.$dn['emailAddress'].' is now signed.',$x509);
	$initLog->writeLog('Public and private key details for user with email '.$dn['emailAddress'].'.',$details);

	return array("privateKey"=>$privateKey, "publicKey"=>$publicKey, "csr"=>$csrExport, "cert"=>$certExport);
 }

// A small function to renew CA's certificate.
 public function renewCACert () {

        $initLog = new Log();
	$initXml = new Xml();	

        $x509 = openssl_csr_sign($initXml->getCAdata()['csr'], NULL, $initXml->getCAdata()['privateKey'], 365, array('digest_alg' => 'sha256'));
        openssl_x509_export($x509, $certExport);
        $initLog->writeLog('CSR request for CA\'s certificate renewal is now signed.',$x509);
        
	return $certExport;
 }

// Function that generates on the fly the sKey and the authHash of the user for further use. 
 public function usersKeyauthHash ($username,$password) {

	$initLog = new Log();

	$sKey = hash_pbkdf2("sha256", $password, $username, 2000, 16);
	$authHash = hash_pbkdf2("sha256", $sKey, $password, 1000, 16);

	$initLog->writeLog('Symmetric key for user '.$username.' generated.',$sKey);
	$initLog->writeLog('Authentication hash for user '.$username.' generated.',$authHash);

	return array("sKey"=>$sKey,"authHash"=>$authHash);
 }

// Encrypt supplied domain password with the user's skey and returned it
 public function encryptPassword ($sKey,$password) {
	
	$initLog = new Log();

	$iv = $sKey;	

	$encryptedPassword = openssl_encrypt($password, "AES-128-CBC", $sKey, $options=OPENSSL_RAW_DATA, $iv);

	$initLog->writeLog('A password with value '.$password.' was encrypted with the symmetric key '.$sKey.'.');
	
	return $encryptedPassword;
 }

// Function that decrypts a user's domain password and returns it for print at the Show function in the web interface.
 public function decryptPassword ($sKey,$password) {
	$initLog = new Log();
	$iv = $sKey;
        $originalPassword = openssl_decrypt($password, "AES-128-CBC", $sKey, $options=OPENSSL_RAW_DATA, $iv);
	$initLog->writeLog('A password with value '.$password.' was decrypted with the symmetric key '.$sKey.'.');
        return $originalPassword;
 }

// Creates on the fly tha hash for a domain password and returns it for further use.
 public function verifyPassword ($password,$domain,$privateKey,$sKey) {
	$initLog = new Log();
	$shaPassword = openssl_digest($password,"sha1",$raw_output=TRUE);
	$pairData = $shaPassword.','.$domain;
	openssl_sign($pairData, $signedData, $privateKey, OPENSSL_ALGO_SHA256);
	$iv = $sKey;
	$encryptedSignedData = openssl_encrypt($signedData, "AES-128-CBC", $sKey, $options=OPENSSL_RAW_DATA, $iv);
	$initLog->writeLog('A password with value '.$password.' was submited for verification.');
	$initLog->writeLog('A password digest with value '.$shaPassword.' was created.');
	$initLog->writeLog('A password-domain pair was created with the following value.',$pairData);
	$initLog->writeLog('A passoword-domain pair was signed with the following value.',$signedData);
	$initLog->writeLog('A passoword-domain signed pair was encrypt with the following value.',$encryptedSignedData);
	return $encryptedSignedData;
 }

// Small function to easy out the process to get data out of a certificate.
 public function readX509 ($cert) {
	$data = openssl_x509_parse($cert);
	return $data;
 }

// Just puts specific portions from a certificate to an array to easy out usage. 
 public function issuerData ($cert) { 

	$return = array(
		"country" => $cert['issuer']['C'],
		"state" => $cert['issuer']['ST'],
		"locality" => $cert['issuer']['L'],
		"organization" => $cert['issuer']['O'],
		"unit" => $cert['issuer']['OU'],
		"name" => $cert['issuer']['CN'],
		"email" => $cert['issuer']['emailAddress']);
	return $return;

 }

// Finds the KeyID value, needed for verification that the certification is signed by our CA.
 public function findKeyid ($cert) {	
 	$keyID = substr($cert['extensions']['authorityKeyIdentifier'],6);
 return $keyID;
 }

// Finds the SubjectKeyID value. Needed for checking if the certificate that the user uses is the most recent one.
 public function findSubKeyid ($cert) {	
 	$keyID = $cert['extensions']['subjectKeyIdentifier'];
 return $keyID;
 }

// Function that checks a user's certificate if it has expired or is not generated from our CA.
// Returns values that help define how the app must proceed.
 public function checkCert ($userCert,$caCert) {

	$dateFrom = $userCert['validFrom_time_t'];
	$dateTo = $userCert['validTo_time_t'];

	if (time() < $dateTo) {

		$keyCA = $this->findKeyid($caCert);
		$keyUser = $this->findKeyid($userCert);

		if ($keyCA === $keyUser) {
			$result = 0;
		} else {
			$result = 1;
		}
	} else {
		$result = 2;
	}
	return $result;
 }

}
?>
