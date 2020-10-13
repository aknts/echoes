<?php
// Start the xml class
class Xml {

// We setup our path that all needed files are located.
const path=rootDir.'echoes/xml/';

// We check if the CA has any data inside. If it has return the data.
 public function checkCA () {

	$ca = $this->getCAdata();
	if ((!is_array($ca)) || (empty($ca['publicKey'])) || (empty($ca['privateKey'])) || (empty($ca['csr'])) || (empty($ca['cert']))) {

		return 1;	

	}

 return $ca;

 }

// We check if there is user in users.xml with an admin role.
 public function checkAdmin () {

	$valueUser = '';
        
	$xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

	        $xmlUser = $searchNode->getElementsByTagName( "User" );
		$valueUser = $xmlUser->item(0)->nodeValue;

	        $xmlRole = $searchNode->getElementsByTagName( "Role" );
		$valueRole = $xmlRole->item(0)->nodeValue;

		if ($valueRole == 'admin') {

			return $valueUser;

		}
	}

 return $valueUser;

 }

// Function to add a domain-password combination for a user.
 public function addPassword ($user,$domain,$username,$password,$comment,$signature) {

	$xml = new DOMDocument();
	$xml->load(self::path."passwords.xml");

	$f = $xml->createDocumentFragment();
	$f->appendXML("<Data><User>$user</User><Domain>$domain</Domain><Username>$username</Username><Password>$password</Password><Comment>$comment</Comment><Signature>$signature</Signature></Data>");
	$xml->documentElement->appendChild($f);
	$xml->save(self::path."passwords.xml");
 }
 
// A function to delete a user request password.
 public function delPassword ($searchUser,$searchDomain,$searchUsername) {

	$i = 0;
	$xml = new DOMDocument();
	$xml->load(self::path."passwords.xml");

	$searchNode = $xml->getElementsByTagName( "Data" );

	foreach( $searchNode as $searchNode ){

    		$xmlUser = $searchNode->getElementsByTagName( "User" );
    		$valueUser = $xmlUser->item(0)->nodeValue;

		$xmlDomain = $searchNode->getElementsByTagName( "Domain" );
		$valueDomain = $xmlDomain->item(0)->nodeValue;

		$xmlUsername = $searchNode->getElementsByTagName( "Username" );
		$valueUsername = $xmlUsername->item(0)->nodeValue;

    		if (($searchUser == $valueUser) && ($searchDomain == $valueDomain) && ($searchUsername == $valueUsername)) {

            		$del =  $searchNode->parentNode->removeChild($searchNode);
			$i++;
		}
	}

	$xml->save(self::path."passwords.xml");
 	return $i;
 }

// Function thar erases all passwords for a user. Used from the admin panel to erase passwords when deleting a user.
 public function delAllPasswords ($searchUser) {

	$i=0;
        $xml = new DOMDocument();
        $xml->load(self::path."passwords.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;

                if ($searchUser == $valueUser) {

                        $del =  $searchNode->parentNode->removeChild($searchNode);
                        $i++;
                }
        }

        $xml->save(self::path."passwords.xml");
        return $i;
 }

// Function to edit a domain-password combination.
 public function editPassword ($searchUser,$searchDomain,$searchUsername,$newPassword,$newSignature) {

        $xml = new DOMDocument();
        $xml->load(self::path."passwords.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ) {

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;

                $xmlDomain = $searchNode->getElementsByTagName( "Domain" );
                $valueDomain = $xmlDomain->item(0)->nodeValue;

                $xmlUsername = $searchNode->getElementsByTagName( "Username" );
                $valueUsername = $xmlUsername->item(0)->nodeValue;

                $xmlPassword = $searchNode->getElementsByTagName( "Password" );
                $valuePassword = $xmlPassword->item(0)->nodeValue;

                $xmlSignature = $searchNode->getElementsByTagName( "Signature" );
                $valueSignature = $xmlSignature->item(0)->nodeValue;

                if (($searchUser == $valueUser) && ($searchDomain == $valueDomain) && ($searchUsername == $valueUsername)) {

                        $valuePassword=$searchNode->getElementsByTagName("Password")->item(0)->nodeValue = $newPassword;
                        $valueSignature=$searchNode->getElementsByTagName("Signature")->item(0)->nodeValue = $newSignature;

                }
        }

        $xml->save(self::path."passwords.xml");

 }

// Check if a domain-password combination exists.
 public function checkPassword ($searchUser,$searchDomain,$searchUsername) {

	$i = 0;
	$check = 0;
        $xml = new DOMDocument();
        $xml->load(self::path."passwords.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;

                $xmlDomain = $searchNode->getElementsByTagName( "Domain" );
                $valueDomain = $xmlDomain->item(0)->nodeValue;

                $xmlUsername = $searchNode->getElementsByTagName( "Username" );
                $valueUsername = $xmlUsername->item(0)->nodeValue;

                if (($searchUser == $valueUser) && ($searchDomain == $valueDomain) && ($searchUsername == $valueUsername)) {
			$i++;
                        $check = $i;
                } 
        }

	return $check;

 }

// Retrieve all users from xml file and return them to an array.
 public function returnUsers () {

        $users = array();
        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;
                $xmlPassword = $searchNode->getElementsByTagName( "Password" );
                $valuePassword = $xmlPassword->item(0)->nodeValue;
                $xmlName = $searchNode->getElementsByTagName( "Name" );
                $valueName = $xmlName->item(0)->nodeValue;
                $xmlLastname = $searchNode->getElementsByTagName( "Lastname" );
                $valueLastname = $xmlLastname->item(0)->nodeValue;
                $xmlEmail = $searchNode->getElementsByTagName( "Email" );
                $valueEmail = $xmlEmail->item(0)->nodeValue;
                $xmlRole = $searchNode->getElementsByTagName( "Role" );
                $valueRole = $xmlRole->item(0)->nodeValue;
                $xmlKeyid = $searchNode->getElementsByTagName( "Keyid" );
                $valueKeyid = $xmlKeyid->item(0)->nodeValue;

                $users[] = array('user' => $valueUser,'password' => $valuePassword,'name' => $valueName,'lastname' => $valueLastname,'email' => $valueEmail, 'role' => $valueRole, 'keyid' => $valueKeyid);
                
        }

 return $users;

 }
 
// Function to add a new user.
 public function addUser ($user,$password,$name,$lastname,$email,$role,$keyid) {

        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $f = $xml->createDocumentFragment();
        $f->appendXML("<Data><User>$user</User><Password>$password</Password><Name>$name</Name><Lastname>$lastname</Lastname><Email>$email</Email><Role>$role</Role><Keyid>$keyid</Keyid></Data>");
        $xml->documentElement->appendChild($f);
        $xml->save(self::path."users.xml");
 
 }

// A function to edit the users subject keyid, needed for check to see if user uses the latest ceertificate and not an old one.
 public function editUserSubKeyid ($searchUser,$newSubKeyid) {

        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ) {

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;

                $xmlKeyid = $searchNode->getElementsByTagName( "Keyid" );
                $valueKeyid = $xmlKeyid->item(0)->nodeValue;

                if ($searchUser == $valueUser) {

                        $valueKeyid=$searchNode->getElementsByTagName("Keyid")->item(0)->nodeValue = $newSubKeyid;

                }
        }

        $xml->save(self::path."users.xml");

 }

// Function to edit a user's password.
 public function editUserPassword ($searchUser,$newPassword) {

        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ) {

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;

                $xmlPassword = $searchNode->getElementsByTagName( "Password" );
                $valuePassword = $xmlPassword->item(0)->nodeValue;

                if ($searchUser == $valueUser) {

                        $valuePassword=$searchNode->getElementsByTagName("Password")->item(0)->nodeValue = $newPassword;

                }
        }

        $xml->save(self::path."users.xml");

 }

// Function to delete a user. We locate him by his username and email. Used only fro the admin panel.
 public function delUser ($user,$email) {

	$i = 0;
        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;

                $xmlEmail = $searchNode->getElementsByTagName( "Email" );
                $valueEmail = $xmlEmail->item(0)->nodeValue;

                if (($user == $valueUser) && ($email == $valueEmail)){

                        $del =  $searchNode->parentNode->removeChild($searchNode);
			$i++;

                }
        }

        $xml->save(self::path."users.xml");
	return $i;

 }

// We check is a user exists, needed for the application to work properly. 
 public function checkUserExists ($user) {

        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

	        $xmlUser = $searchNode->getElementsByTagName( "User" );
        	$valueUser = $xmlUser->item(0)->nodeValue;
		if ($valueUser == $user){
			$result = 1;
		}
	}
               
	 if (empty($result)) {

		return 0;

	} else {

		return $result;

	}

 }

// A function to check that the supplied email isn't already stored in our xml file.
 public function checkEmailExists ($email) {

        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

	        $xmlEmail = $searchNode->getElementsByTagName( "Email" );
        	$valueEmail = $xmlEmail->item(0)->nodeValue;
		if ($valueEmail == $email){
			$result = 1;
		}
	}
        if (empty($result)){
		return 0;
	} else {        
		return $result;
	}

 }

// Find a user's password hash and return it.
 public function returnUserPassword ($user) {

        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

	        $xmlUser = $searchNode->getElementsByTagName( "User" );
        	$valueUser = $xmlUser->item(0)->nodeValue;
	        $xmlPassword = $searchNode->getElementsByTagName( "Password" );
        	$valuePassword = $xmlPassword->item(0)->nodeValue;
		if ($valueUser == $user){
			$result = $valuePassword;
		}	
	}

	if (empty($result)) {
		return 0;
	} else {
 		return $result;
	}

 }

// Find a user's email and return it.
 public function returnUserEmail ($user) {

        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

	        $xmlUser = $searchNode->getElementsByTagName( "User" );
        	$valueUser = $xmlUser->item(0)->nodeValue;
	        $xmlEmail = $searchNode->getElementsByTagName( "Email" );
        	$valueEmail = $xmlEmail->item(0)->nodeValue;
		if ($valueUser == $user){
			$result = $valueEmail;
		} 
	}
        if (empty($result)){
		return 0;
	} else {        
		 return $result;
	}

 }

// Return user's firstname and last name
 public function returnUserNames ($user) {

        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");
	$result = 0;

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;
                $xmlName = $searchNode->getElementsByTagName( "Name" );
                $valueName = $xmlName->item(0)->nodeValue;
                $xmlLastname = $searchNode->getElementsByTagName( "Lastname" );
                $valueLastname = $xmlLastname->item(0)->nodeValue;
                if ($valueUser == $user){
                        $result = array('Name' => $valueName, 'Lastname' => $valueLastname);
                }
        }

	return $result;

 }

// Returns a user's Subject KeyID.
 public function returnUserSubKeyid ($user) {

        $xml = new DOMDocument();
        $xml->load(self::path."users.xml");
        $result = 0;

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;
                $xmlKeyid = $searchNode->getElementsByTagName( "Keyid" );
                $valueKeyid = $xmlKeyid->item(0)->nodeValue;
                if ($valueUser == $user){
                        $result = $valueKeyid;
                }
        }

        return $result;

 }

// Return all user's passwords. Before building an array with all password, it checks them if they pass the verification process.
// Different status is printed for each password.
public function returnPasswords ($user) {

	$initCert = new Cert();

	$passwords = array();
        $xml = new DOMDocument();
        $xml->load(self::path."passwords.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

	        $xmlUser = $searchNode->getElementsByTagName( "User" );
        	$valueUser = $xmlUser->item(0)->nodeValue;
	        $xmlDomain = $searchNode->getElementsByTagName( "Domain" );
        	$valueDomain = $xmlDomain->item(0)->nodeValue;
	        $xmlUsername = $searchNode->getElementsByTagName( "Username" );
        	$valueUsername = $xmlUsername->item(0)->nodeValue;
	        $xmlPassword = $searchNode->getElementsByTagName( "Password" );
        	$valuePassword = $xmlPassword->item(0)->nodeValue;
	        $xmlComment = $searchNode->getElementsByTagName( "Comment" );
        	$valueComment = $xmlComment->item(0)->nodeValue;
	        $xmlSignature = $searchNode->getElementsByTagName( "Signature" );
        	$valueSignature = $xmlSignature->item(0)->nodeValue;

		if ($valueUser == $user){
			$checkSignature = $initCert->verifyPassword(base64_decode($valuePassword),$valueDomain,$this->getCAdata()['privateKey'],$_SESSION['sKey']);
			if ($checkSignature == base64_decode($valueSignature)) {
				$passwords[] = array($valueDomain,$valueUsername,$valuePassword,$valueComment,"OK");
			} else {
				$passwords[] = array($valueDomain,$valueUsername,$valuePassword,$valueComment,"Corrupted");
			}
		}
	}
                
 return $passwords;

 }

// Same as the previous function but without the verification process.
public function dumpPasswords ($user) {

        $initCert = new Cert();

        $passwords = array();
        $xml = new DOMDocument();
        $xml->load(self::path."passwords.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

                $xmlUser = $searchNode->getElementsByTagName( "User" );
                $valueUser = $xmlUser->item(0)->nodeValue;
                $xmlDomain = $searchNode->getElementsByTagName( "Domain" );
                $valueDomain = $xmlDomain->item(0)->nodeValue;
                $xmlUsername = $searchNode->getElementsByTagName( "Username" );
                $valueUsername = $xmlUsername->item(0)->nodeValue;
                $xmlPassword = $searchNode->getElementsByTagName( "Password" );
                $valuePassword = $xmlPassword->item(0)->nodeValue;
                $xmlComment = $searchNode->getElementsByTagName( "Comment" );
                $valueComment = $xmlComment->item(0)->nodeValue;
                $xmlSignature = $searchNode->getElementsByTagName( "Signature" );
                $valueSignature = $xmlSignature->item(0)->nodeValue;

                if ($valueUser == $user){
			$passwords[] = array('domain' => $valueDomain,'username' => $valueUsername,'password' => $valuePassword,'comment' => $valueComment,'signature' => $valueSignature);
                }
        }

 return $passwords;

 }

// Returns a user's specific password.
 public function returnPassword ($user,$domain,$username) {

        $xml = new DOMDocument();
        $xml->load(self::path."passwords.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

	        $xmlUser = $searchNode->getElementsByTagName( "User" );
        	$valueUser = $xmlUser->item(0)->nodeValue;
	        $xmlDomain = $searchNode->getElementsByTagName( "Domain" );
        	$valueDomain = $xmlDomain->item(0)->nodeValue;
	        $xmlUsername = $searchNode->getElementsByTagName( "Username" );
        	$valueUsername = $xmlUsername->item(0)->nodeValue;
	        $xmlPassword = $searchNode->getElementsByTagName( "Password" );
        	$valuePassword = $xmlPassword->item(0)->nodeValue;
	        $xmlComment = $searchNode->getElementsByTagName( "Comment" );
        	$valueComment = $xmlComment->item(0)->nodeValue;
		if (($valueUser == $user) && ($valueDomain == $domain) && ($valueUsername == $username)) {
			$passwords = array($valueDomain,$valueUsername,$valuePassword,$valueComment);
		}
	}
                
 return $passwords;

 }

// Save CA data, certificate, csr and keys.
 public function saveCAdata ($privateKey,$publicKey,$CSR,$cert) {

        $xml = new DOMDocument();
        $xml->load(self::path."ca.xml");

        $f = $xml->createDocumentFragment();
        $f->appendXML("<Data><PrivateKey>$privateKey</PrivateKey><PublicKey>$publicKey</PublicKey><CSR>$CSR</CSR><Certificate>$cert</Certificate></Data>");
        $xml->documentElement->appendChild($f);
        $xml->save(self::path."ca.xml");
 }

// Retrieve CA's certificate, csr and keys from xml file. 
 public function getCAdata () {

	$valuepublicKey = '';
	$valueprivateKey = '';;
	$valueCsr = '';
	$valueCertificate = '';


	if (!file_exists(self::path."ca.xml")) {
		return 0;
	}
 
        $xml = new DOMDocument();
        $xml->load(self::path."ca.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

                $xmlpublicKey = $searchNode->getElementsByTagName( "PublicKey" );
                $valuepublicKey = $xmlpublicKey->item(0)->nodeValue;

		$xmlprivateKey = $searchNode->getElementsByTagName( "PrivateKey" );
                $valueprivateKey = $xmlprivateKey->item(0)->nodeValue;

		$xmlCsr = $searchNode->getElementsByTagName( "CSR" );
                $valueCsr = $xmlCsr->item(0)->nodeValue;

                $xmlCertificate = $searchNode->getElementsByTagName( "Certificate" );
                $valueCertificate = $xmlCertificate->item(0)->nodeValue;
        }

        return array('publicKey' => $valuepublicKey,'privateKey' => $valueprivateKey, 'csr' => $valueCsr,'cert' => $valueCertificate);
 }

// Function to save only the new CA certificate, part of the renewal process. 
 public function saveNewCACert ($newCert) {
	
	$xml = new DOMDocument();
	$xml->load(self::path."ca.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ) {

        	$xmlCertificate = $searchNode->getElementsByTagName( "Certificate" );
        	$valueCertificate = $xmlCertificate->item(0)->nodeValue;

	        $valueCertificate=$searchNode->getElementsByTagName("Certificate")->item(0)->nodeValue = $newCert;

        }
        
	$xml->save(self::path."ca.xml");

 }

// Function that simply add a penalty value for a user. 
 public function addPenalty ($user,$date,$penalty) {

	$xml = new DOMDocument();
	$xml->load(self::path."penalties.xml");

	$f = $xml->createDocumentFragment();
	$f->appendXML("<Data><User>$user</User><Date>$date</Date><Penalty>$penalty</Penalty></Data>");
	$xml->documentElement->appendChild($f);
	$xml->save(self::path."penalties.xml");
 }

// Returns a penalty value for a user.
 public function returnPenalty ($user) {

        $xml = new DOMDocument();
        $xml->load(self::path."penalties.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){

	        $xmlUser = $searchNode->getElementsByTagName( "User" );
        	$valueUser = $xmlUser->item(0)->nodeValue;
	        $xmlDate = $searchNode->getElementsByTagName( "Date" );
        	$valueDate = $xmlDate->item(0)->nodeValue;
	        $xmlPenalty = $searchNode->getElementsByTagName( "Penalty" );
        	$valuePenalty = $xmlPenalty->item(0)->nodeValue;
		if ($valueUser == $user) {
			$return = array($valueDate,$valuePenalty);
		}
	}
 	if (empty($return)){

		return NULL;

	} else {

 		return $return;
	}

 }

// Deletes a penalty value for a specific user.
 public function delPenalty ($searchUser) {

	$i = 0;
	$xml = new DOMDocument();
	$xml->load(self::path."penalties.xml");

	$searchNode = $xml->getElementsByTagName( "Data" );

	foreach( $searchNode as $searchNode ){

    		$xmlUser = $searchNode->getElementsByTagName( "User" );
    		$valueUser = $xmlUser->item(0)->nodeValue;

    		if ($searchUser == $valueUser) {

            		$del =  $searchNode->parentNode->removeChild($searchNode);
			$i++;
		}
	}

	$xml->save(self::path."penalties.xml");
 	return $i;
 }

// Function to edit a penalty for a user. Renews also date of the new penalty.
 public function editPenalty ($user,$date,$penalty) {

	$xml = new DOMDocument();
	$xml->load(self::path."penalties.xml");

	$searchNode = $xml->getElementsByTagName( "Data" );

	foreach( $searchNode as $searchNode ) {

		$xmlUser = $searchNode->getElementsByTagName( "User" );
		$valueUser = $xmlUser->item(0)->nodeValue;
		$xmlDate = $searchNode->getElementsByTagName( "Date" );
		$valueDate = $xmlDate->item(0)->nodeValue;
		$xmlPenalty = $searchNode->getElementsByTagName( "Penalty" );
		$valuePenalty = $xmlPenalty->item(0)->nodeValue;

		if ($user == $valueUser) {
			$valueDate=$searchNode->getElementsByTagName("Date")->item(0)->nodeValue = $date;
			$valuePenalty=$searchNode->getElementsByTagName("Penalty")->item(0)->nodeValue = $penalty;

		}
	}

	$xml->save(self::path."penalties.xml");
 }

// Retrive all penalties for admin panel usage.
 public function returnPenalties () {

        $penalties = array();
        $xml = new DOMDocument();
        $xml->load(self::path."penalties.xml");

        $searchNode = $xml->getElementsByTagName( "Data" );

        foreach( $searchNode as $searchNode ){
	        
		$xmlUser = $searchNode->getElementsByTagName( "User" );
        	$valueUser = $xmlUser->item(0)->nodeValue;
	        $xmlDate = $searchNode->getElementsByTagName( "Date" );
        	$valueDate = $xmlDate->item(0)->nodeValue;
	        $xmlPenalty = $searchNode->getElementsByTagName( "Penalty" );
        	$valuePenalty = $xmlPenalty->item(0)->nodeValue;
		
		$penalties[] = array('user' => $valueUser,'date' => $valueDate,'penalty' => $valuePenalty);

        }

	 return $penalties;

 }

// Function used to initiate a specific file. User during the wizard and if admin requests for a full app reset.
 public function createXml ($filename) {

	if (($filename == "passwords") || ($filename == "users") || ($filename == "ca") || ($filename == "configuration") || ($filename == "penalties")) {

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>';
	
		switch ($filename) {
		 case "passwords":
	 	 $xmlData .= '<Passwords></Passwords>';
	 	 break;
	 	 case "users":
	 	 $xmlData .= '<Users></Users>';
	 	 break;
	 	 case "ca":
	 	 $xmlData .= '<Keystore></Keystore>';
	 	 break;
		 case "configuration":
	 	 $xmlData .= '<Configuration></Configuration>';
	 	 break;
		 case "penalties":
		 $xmlData .= '<Penalties></Penalties>';
		 break;
		}

		$xmlFile = new DOMDocument;
		$xmlFile->preserveWhiteSpace = FALSE;
		$xmlFile->loadXML($xmlData);
		$xmlFile->save(self::path."$filename.xml");

	}

 }

}
?>
