<?php
// Start View class.
class View {

// Register function, is being used during the registration process.
 public function register () {

// We load all needed objects.
	$initDraw = new Draw();
	$initXml = new Xml();
	$initTools = new Tools();
	$initCert = new Cert();

// Just start by printing the head.
	$initDraw->showHead();

// Check if m value is empty or not.
	if (!empty($_GET['m'])) {
		$m = $_GET['m'];
	} else {
		$m = NULL;
	}

// And now print anything m is assigned to.
	$initDraw->showMsg($m);

// Register new user, create keys and certificate.
// First we need everything filled.
	if ((!empty($_POST)) && (count($_POST) == 5) && ($_POST['rfn'] != NULL) && ($_POST['rln'] != NULL) && ($_POST['rem'] != NULL) && ($_POST['rus'] != NULL) && ($_POST['rpw'] != NULL)) {

// Now we check if the values have only numbers or letters, if the email is an actual email and finally the password length. Minimum 10 charactes, maximum 20.		
		if (($initTools->checkCharacters($_POST['rfn']) !== 0 ) && ($initTools->checkCharacters($_POST['rln']) !== 0) && ($initTools->checkEmail($_POST['rem']) !== 0) && ($initTools->checkCharacters($_POST['rus']) !== 0) && ($initTools->checkLength($_POST['rpw']) !== 0) && ($initTools->checkCharacters($_POST['rpw']) !== 0)) {

			// Check if username is being used, if it is redirect with proper message.
                        if ($initXml->checkUserExists($_POST['rus']) !== 0) {
                                header('Location: index.php?v=wizard&m=rue');
                                die;
                        }
			// Check if the email is being used, if it is redirect with proper message.
                        if ($initXml->checkEmailExists($_POST['rem']) !== 0) {
                                header('Location: index.php?v=wizard&m=ree');
                                die;
			}
			// Checks are finished, generate sKey and authHash. 
			$userPassword = $initCert->usersKeyauthHash($_POST['rus'],$_POST['rpw']);

			// Start building needed data for a new certificate.
			// CA contains CA's certificate and keys.
			// dn is an array with values for the CA's certificate and some user data.
			$ca = $initXml->getCAdata();
			$readCert=$initCert->readX509($ca['cert']);
			$dn = array(
				"countryName" => $readCert['issuer']['C'],
				"stateOrProvinceName" => $readCert['issuer']['ST'],
				"localityName" => $readCert['issuer']['L'],
				"organizationName" => $readCert['issuer']['O'],
				"organizationalUnitName" => $readCert['issuer']['OU'],
				"commonName" => $_POST['rfn'].' '.$_POST['rln'],
				"emailAddress" =>  $_POST['rem']
			);

			// Now generate a certificate for our user, valid for 180 days.
			$userCert=$initCert->getKeysCsrCert($dn,$ca['cert'],$ca['privateKey'],"180");

			// Save subkeyid to know which was the last one that our user generated.
			$subID = $initCert->findSubKeyid($initCert->readX509($userCert['cert']));

			// Save the user to our users.xml
			$initXml->addUser($_POST['rus'],$userPassword['authHash'],$_POST['rfn'],$_POST['rln'],$_POST['rem'],'user',$subID);

			// An array with the certificate, csr and keys is being created.
			// Filenames are also included. With this array a zip file is being generated and send to user's browser for download.
			$files = array(
 				array(rtrim($userCert['privateKey']),"privateKey.pem"),
				array(rtrim($userCert['publicKey']),"publicKey.pem"),
				array(rtrim($userCert['csr']),"csr.pem"),
				array(rtrim($userCert['cert']),"cert.pem")
			);
 
			// The zipfile is being created.
 			$zipFile=$initTools->createArchive($files,'echoes-'.$_POST['rus'].'-'.time().'');

			// Zipfile is stored to a session variable to survive the http redirects to allow the download.
			$_SESSION['rzf'] = $zipFile;

			// And we redirect for download.
                        header('Location: index.php?v=login&m=urg');
                } else {
			// Redirect if anything goes wrong.
                        header('Location: index.php?v=register&a=zulu&m=uaf');
		}
	}

	// If anything of the previous don't happend print our register form and foot.
	$initDraw->showRegisterForm();
	$initDraw->showFoot();
 } 


// User function, the page that a user sees
 public function user () {

// We initiate objects
	$initDraw = new Draw();
	$initXml = new Xml();
	$initCert = new Cert();
	$initTools = new Tools();

	// Check is session's variables are set and if token is properly passed with the http redirection. If not redirect back to login screen.
	if ((empty($_SESSION['token'])) || (empty($_SESSION['user'])) || ($_SESSION['token'] !== $_GET['t'])) {
		header('Location: index.php?v=login');
		die();
	}
	
	// Check if an add password form was submitted by checking the corresponding variables.
	// Again we check if they are empty or not.
	if ((!empty($_POST)) && (count($_POST) == 4) && (!empty($_POST['adpwd'])) && (!empty($_POST['adpwu'])) && (!empty($_POST['adpwp']))) {
		
		// Check if the domain username combination exists, redirect if it is.
		if ($initXml->checkPassword($_SESSION['user'],$_POST['adpwd'],$_POST['adpwu']) !== 0) {
			header('Location: index.php?v=user&t='.$_GET['t'].'&m=pwe');
			die;
		} 
		
		// Encrypt password and store is with users data. We base64 encode data because the encrypted password breaks the xml file.
		$pwd = $initCert->encryptPassword($_SESSION['sKey'],strip_tags($_POST['adpwp']));
		$initXml->addPassword($_SESSION['user'],$_POST['adpwd'],$_POST['adpwu'],base64_encode($pwd),$_POST['adpwc'],base64_encode($initCert->verifyPassword($pwd,$_POST['adpwd'],$initXml->getCAdata()['privateKey'],$_SESSION['sKey'])));
	}	

	// Check if an edit password was submitted by checking the corresponding variables
	if ((!empty($_POST)) && (count($_POST) == 4) && (!empty($_POST['edpwd'])) && (!empty($_POST['edpwu'])) && (!empty($_POST['edpwp']))) {
		
		// We check if domain username combination exists, if not redirect.
		if ($initXml->checkPassword($_SESSION['user'],$_POST['edpwd'],$_POST['edpwu']) == 0) {
			header('Location: index.php?v=user&t='.$_GET['t'].'&m=pwde');
			die;
		}

		// Encrypt new password and edit domain's password combination.
		$pwd = $initCert->encryptPassword($_SESSION['sKey'],strip_tags($_POST['edpwp']));
		$initXml->editPassword($_SESSION['user'],$_POST['edpwd'],$_POST['edpwu'],base64_encode($pwd),base64_encode($initCert->verifyPassword($pwd,$_POST['edpwd'],$initXml->getCAdata()['privateKey'],$_SESSION['sKey'])));
	}


	// Check is a user edited his password. 
	if ((!empty($_POST)) && (count($_POST) == 3) && (!empty($_POST['eupwdo'])) && (!empty($_POST['eupwdn'])) && (!empty($_POST['eupwdr']))) {

		// We check again the old, new and second try passwords for valid characters and length.
		if (($initTools->checkCharacters($_POST['eupwdo']) == '0') || ($initTools->checkLength($_POST['eupwdo']) == '0') || ($initTools->checkCharacters($_POST['eupwdn']) == '0') || ($initTools->checkLength($_POST['eupwdn']) == '0') || ($initTools->checkCharacters($_POST['eupwdr']) == '0') || ($initTools->checkLength($_POST['eupwdr']) == '0')) {
                        header('Location: index.php?v=user&t='.$_GET['t'].'&a=cupwd&m=ict');
                        die();
                }

		// Generate old password hash and compare it with the one stored to check if it is the same, redirect with error message.
                $userPassword = $initCert->usersKeyauthHash($_SESSION['user'],$_POST['eupwdo']);
                if ($userPassword['authHash'] !== $initXml->returnUserPassword($_SESSION['user'])) {
                        header('Location: index.php?v=user&t='.$_GET['t'].'&a=cupwd&m=woup');
                        die();
                }

		// Check if the user submitted the new password twice and both have the same value. Redirect with error message in case of not being the same.
		if ($_POST['eupwdn'] !== $_POST['eupwdr']) {
                        header('Location: index.php?v=user&t='.$_GET['t'].'&a=cupwd&m=rpiw');
                        die();
		}

		// Generate new password hash and compare it with the old one generated earlier to see if it is the same. Redirect if they are the same.
                $newUserPassword = $initCert->usersKeyauthHash($_SESSION['user'],$_POST['eupwdn']);
                if ($userPassword['authHash'] == $newUserPassword['authHash']) {
                        header('Location: index.php?v=user&t='.$_GET['t'].'&a=cupwd&m=sonp');
                        die();
                }

		// Update users password with the new hash.
		$initTools->changeUserPassword($_SESSION['user'],$_POST['eupwdn']);
		header('Location: index.php?v=user&t='.$_GET['t'].'&m=upc');
		die;

	}

	// Print head, menu and message. 
	$initDraw->showHead();
	$initDraw->showUserMenu();
	if (!empty($_GET['m'])) {
		$m = $_GET['m'];
	} else {
		$m = NULL;
	}
	$initDraw->showMsg($m);

	if (!empty($_GET['a'])) { $a = $_GET['a']; } else { $a = NULL; }

	// A switch for the different actions that the user page has.
	switch ($a) {
		// Prints the form for the user to add a new domain password combination.
		case "adpw":
		$initDraw->showAddPasswordForm();
		break;

		// Log outs our user. Unset variables and destroy session.
		// If the user pressed to renew his certificate, he is forced to logout and download the new certificate.
		// For this reason session is being destroyed from the login page when the user lands there.
		// This is done for the zipfile that is stored in the crn session variable to survive the http redirects.
		case "lo":
		unset($_SESSION['token']);
		unset($_SESSION['user']);
		unset($_SESSION['ed']);
		if (empty($_SESSION['crn'])) {
 			session_unset();
 			session_destroy();
		}
		header('Location: index.php?v=login');
		die();
		break;

		// Prints the show password form, showing the decrypted password.
		// Data is being exchanged between pages with base64 encode and decode between transitions. 
		case "shpw":
		if (!empty($_GET['d'])){
			$data = explode(":::::",base64_decode($_GET['d']));
			$retData = $initXml->returnPassword($_SESSION['user'],$data[0],$data[1]);
			$initDraw->showPassword($retData[0],$retData[1],$initCert->decryptPassword($_SESSION['sKey'],base64_decode($retData[2])),$retData[3]);
		}
		break;
		
		// Prints the edit form for a user to edit a domain-password combination.
		// Again data is being passed through base64 encode-decode functions. 
		case "edpw":
		if (!empty($_GET['d'])){
			$data = explode(":::::",base64_decode($_GET['d']));
			$initDraw->showEditPasswordForm($data[0],$data[1],$data[2]);
		}
		break;

		// Deletes a domain password combination and redirects to main user page. 
		case "delpw":
		if (!empty($_GET['d'])){
			$data = explode(":::::",base64_decode($_GET['d']));
			$initXml->delPassword($_SESSION['user'],$data[0],$data[1]);
			header('Location: index.php?v=user&t='.$_GET['t'].'&m=pwdel');
		}
		break;

		// Prints the edit form for a user to change his password.	
		case "cupwd":
			$initDraw->showEditUserPasswordForm();
		break;

		// The certification renewal process.
		// Same procedure as the one followed at registration.
		// This time the values for the dn are filled from our users.xml file based on the user logged in.
		// Finally the user is forced to log out and user the new certificate to login.
		case "crn":
                        $ca = $initXml->getCAdata();
			$userData = $initXml->returnUserNames($_SESSION['user']);
			$userEmail = $initXml->returnUserEmail($_SESSION['user']);
                        $readCert=$initCert->readX509($ca['cert']);
                        $dn = array(
                                "countryName" => $readCert['issuer']['C'],
                                "stateOrProvinceName" => $readCert['issuer']['ST'],
                                "localityName" => $readCert['issuer']['L'],
                                "organizationName" => $readCert['issuer']['O'],
                                "organizationalUnitName" => $readCert['issuer']['OU'],
                                "commonName" => $userData['Name'].' '.$userData['Lastname'],
                                "emailAddress" =>  $userEmail
                        );

                        $userCert=$initCert->getKeysCsrCert($dn,$ca['cert'],$ca['privateKey'],"180");
			$subID = $initCert->findSubKeyid($initCert->readX509($userCert['cert']));
			$initXml->editUserSubKeyid($_SESSION['user'],$subID);
                        $files = array(
                                array(rtrim($userCert['privateKey']),"privateKey.pem"),
                                array(rtrim($userCert['publicKey']),"publicKey.pem"),
                                array(rtrim($userCert['csr']),"csr.pem"),
                                array(rtrim($userCert['cert']),"cert.pem")
                        );

                        $zipFile=$initTools->createArchive($files,'echoes-'.$_SESSION['user'].'-'.time().'');
			$_SESSION['crn'] = $zipFile;
			header('Location: index.php?v=user&t='.$_GET['t'].'&a=lo');
			die();
		break;

		// Our user page main page
		default:

		// We setup a default page value and the length of the table with the user's entries.
		$page = 1;
		$length = 10;

		// Retrieve user's passwords
		$pswds = $initXml->returnPasswords($_SESSION['user']);
		
		// Calculate and print pager and table.
		$cpswds = count($pswds);
		$max = ceil($cpswds/$length);
		if (!empty($_GET['p'])) {
			$page = $_GET['p'];	
		}

		$initDraw->showPager('user','',$page,$max);
		$pswds = $initTools->arrayPagination($pswds,$length,$page);
		$initDraw->showUserPasswords($pswds);
	}
	
	// Print foot.
	$initDraw->showFoot();


 }

// Wizard page
 public function wizard () {

	$pass = 0;
	$m = NULL;

	$initDraw = new Draw();
	$initXml = new Xml();
	$initCert = new Cert();
	$initTools = new Tools();

	// We block access to wizard if a certificate exists and an admin has been defined.
	if ((is_array($initXml->checkCA())) && (!empty($initXml->checkAdmin()))) {
		header('Location: index.php?v=index');
		die();
	}

	// We check if we have received anything from the ca wizard form
        if ((count($_POST) == 7) && ((empty($_POST['wcac'])) || (empty($_POST['wcas'])) || (empty($_POST['wcal'])) || (empty($_POST['wcao'])) || (empty($_POST['wcau'])) || (empty($_POST['wcan'])) || (empty($_POST['wcae']))))  {
		header('Location: index.php?v=wizard&m=wcaef');
		die();
	} else {
		$pass++;
	}

	// We check for allowed characters and if the supplies email is a valid address.
	if ((count($_POST) == 7) && (($initTools->checkCharacters($_POST['wcac']) === 0 ) || ($initTools->checkCharacters($_POST['wcas']) === 0)  || ($initTools->checkCharacters($_POST['wcal']) === 0) || ($initTools->checkCharacters($_POST['wcao']) === 0) || ($initTools->checkCharacters($_POST['wcau']) === 0) || ($initTools->checkCharacters($_POST['wcan']) === 0) || ($initTools->checkEmail($_POST['wcae']) === 0))) {
		header('Location: index.php?v=wizard&m=wcawd');
		die();
	} else {
		$pass++;
	}

	// We check the submitted country code if it is only capital letters and if it is maximum 4 characters.
	if ((count($_POST) == 7) && ($initTools->checkCountry($_POST['wcac']) === 0)) {
		header('Location: index.php?v=wizard&m=wcawcc');
		die();
	} else {
		$pass++;
	}

	// In each step the app increments the pass variable. So if anything goes ok, pass will be 3. If yes allow the app to create CA's certificate and keys.
	if ((count($_POST) == 7) && ($pass == 3)) {

                $dn = array(
                "countryName" => $_POST['wcac'],
                "stateOrProvinceName" => $_POST['wcas'],
                "localityName" => $_POST['wcal'],
                "organizationName" => $_POST['wcao'],
                "organizationalUnitName" => $_POST['wcau'],
                "commonName" => $_POST['wcan'],
                "emailAddress" => $_POST['wcae']
                );

		// Generating the new CA certificate and redirect in case of success or failure.
		// No downloadins here. If the admin user needs the files we can download them from his interface.
                $ca = $initTools->newCAcert($dn);

                if ($ca == 1) {
                        header('Location: index.php?v=wizard&m=wcas');
                } else {
                        header('Location: index.php?v=wizard&m=wcaf');
                }
        }

        // Register new admin user, create keys and certificate
	// Again we check if the app received anything from admin wizard form and has some value.
        if ((!empty($_POST)) && (count($_POST) == 5) && ($_POST['wfn'] != NULL) && ($_POST['wln'] != NULL) && ($_POST['wem'] != NULL) && ($_POST['wus'] != NULL) && ($_POST['wpw'] != NULL)) {

		// We check for allowed characters, if the email is a valid address and the password string is of a specified length.
                if (($initTools->checkCharacters($_POST['wfn']) !== 0 ) && ($initTools->checkCharacters($_POST['wln']) !== 0) && ($initTools->checkEmail($_POST['wem']) !== 0) && ($initTools->checkCharacters($_POST['wus']) !== 0) && ($initTools->checkLength($_POST['wpw']) !== 0) && ($initTools->checkCharacters($_POST['wpw']) !== 0)) {

			// We check if a user with the same username exists.
                        if ($initXml->checkUserExists($_POST['wus']) !== 0) {
                                header('Location: index.php?v=wizard&m=rue');
                                die;
                        }

			// We check if the email is being used from someone.
                        if ($initXml->checkEmailExists($_POST['wem']) !== 0) {
                                header('Location: index.php?v=wizard&m=ree');
                                die;
			}

			// Generate user sKey and authHash
                        $userPassword = $initCert->usersKeyauthHash($_POST['wus'],$_POST['wpw']);

			// Now build the dn array to create the certificate for our user and generate his keys.
			// Also get the CA data, certificates and keys.
                        $ca = $initXml->getCAdata();
                        $readCert=$initCert->readX509($ca['cert']);
                        $dn = array(
                                "countryName" => $readCert['issuer']['C'],
                                "stateOrProvinceName" => $readCert['issuer']['ST'],
                                "localityName" => $readCert['issuer']['L'],
                                "organizationName" => $readCert['issuer']['O'],
                                "organizationalUnitName" => $readCert['issuer']['OU'],
                                "commonName" => $_POST['wfn'].' '.$_POST['wln'],
                                "emailAddress" =>  $_POST['wem']
                        );

			// Generating new keys and new certificate for our user 
                        $userCert=$initCert->getKeysCsrCert($dn,$ca['cert'],$ca['privateKey'],"180");

			// Find the Subject KeyID from the new certificate
			$subID = $initCert->findSubKeyid($initCert->readX509($userCert['cert']));

			// We add the admin user, we add the admin role to this account to force the app after logging in to show him the admin panel.
                        $initXml->addUser($_POST['wus'],$userPassword['authHash'],$_POST['wfn'],$_POST['wln'],$_POST['wem'],'admin',$subID);

			// Build array with files and filenams for download.
                        $files = array(
                                array(rtrim($userCert['privateKey']),"privateKey.pem"),
                                array(rtrim($userCert['publicKey']),"publicKey.pem"),
                                array(rtrim($userCert['csr']),"csr.pem"),
                                array(rtrim($userCert['cert']),"cert.pem")
                        );

			// Create zip file and assign data to a session variabla to survice the following http redirect.
			// Redirect user to initiate download of zip file.
			// Redirect user in case of failure.
                        $zipFile=$initTools->createArchive($files,'echoes-aa-'.$_POST['wus'].'-'.time().'');
                        $_SESSION['rzf'] = $zipFile;
                        header('Location: index.php?v=login&m=was');
			die();
                } else {
                        header('Location: index.php?v=wizard&m=waf');
			die();
                }
        }

	// Check if a message is received.
	if (!empty($_GET['m'])) {
		$m = $_GET['m'];
	}

	// Print our head and message.
	$initDraw->showHead();
	$initDraw->showMsg($m);

	// If no CA certificate is found, print the CA wizard.
	// The CA certificate must be present before anything else happens.
	if (!is_array($initXml->checkCA())) {
		$initDraw->showCAWizard();
	}

	// Check that there is a CA certificate installed and if there is an admin user.
	// If no admin user is found, print admin wizard.
	if ((is_array($initXml->checkCA())) && (empty($initXml->checkAdmin()))) {
		$initDraw->showAdminWizard();
	}
	
	// Print foot to complete page.
	$initDraw->showFoot();
 }

// Main index page
// We just initiate some objects and print the needed html to allow user to choose between registration and login.
 public function index () {

	$initDraw = new Draw();
	$initXml = new Xml();
	$initDraw->showHead();
	$initDraw->showIndexMain();
	$initDraw->showFoot();

 }

// The login page
 public function login () {

	$initXml = new Xml();
	$initTools = new Tools();
	$initCert = new Cert();
	$initDraw = new Draw();

	// We check if some GET variable are empty or not.
	// Make php no reporting errors for empty variables.

	$m = NULL;
	if (!empty($_GET['m'])) {
		$m = $_GET['m'];
	}
	if (!empty($_SESSION['crn'])) {
		$m = 'pla';
	}
	$dl = NULL;
	if (!empty($_GET['dl'])) {
		$dl = $_GET['dl'];
	}

	// In this page first we print everything and then we check anything.
	// This is because the login page also forces the downloads of the zip files.
	// This way the user sees the login screen before the dowload start or else he would see a blank page.
       	$initDraw->showHead();
	$initDraw->showMsg($m);
	$initDraw->showLoginForm();
       	$initDraw->showFoot();

	// The following two if, force the browser in two redirects. The later one is the first that is being executed and this is because we want to show our user the login screen.
	// The first if includes the send2Browser function that forces the actual http header to our browser and start the download.
	// This way our user sees something and downloadds his files.
	// These two for the user certificate renewal process.
        if ((!empty($_SESSION['crn'])) && ($dl == 1)) {
                $zipFile = $_SESSION['crn'];
                $initTools->send2Browser($zipFile[0],$zipFile[1]);
                session_unset();
                session_destroy();
                die();
        }
	
        if (!empty($_SESSION['crn'])) {
		header( "refresh:5;url=index.php?v=login&m=was&dl=1" );
		die();
	}

	// The following two are for the admin wizard.
	if (($m == 'was') && ($dl == 1)) {
		$zipFile = $_SESSION['rzf'];
		$initTools->send2Browser($zipFile[0],$zipFile[1]);
		session_unset();
		session_destroy();
		die();
	}

	if ($m == 'was') {
		header( "refresh:5;url=index.php?v=login&m=was&dl=1" );
		die();
	}

	// The following two is for the user registration.
	if (($m == 'urg') && ($dl == 1)) {
		$zipFile = $_SESSION['rzf'];
		$initTools->send2Browser($zipFile[0],$zipFile[1]);
		session_unset();
		session_destroy();
		die();
	}

	if ($m == 'urg') {
		header( "refresh:5;url=index.php?v=login&m=urg&dl=1" );
		die();
	}

	// We check if we have received anything from the login form.
	if ((!empty($_POST)) && (count($_POST) == 3)){ 

		// We check if there is an empty variable, redirect if any of the variable is.
		if ((empty($_POST['lus'])) || (empty($_POST['lpw'])) || (empty($_POST['lcert']))) {
			header('Location: index.php?v=login&m=fnf');
			die();
		}
		
		// We check for a penalty. If the user is banned permanently, we redirect him with a message.
		// If not we check the penalty, if it has expired we let him pass. 
		$penalty = $initXml->returnPenalty($_POST['lus']);

		if (!empty($penalty)) {
			if ($penalty[1] == 0) {
				header('Location: index.php?v=login&m=aib');
				die();
			}
			$expirationDate = $penalty[0]+($penalty[1]*60);
			if (time() < $expirationDate) {
				header('Location: index.php?v=login&m=pip&d='.base64_encode($expirationDate).'');
				die();
			}
		}
		
		// We check if the user exists.
		if ($initXml->checkUserExists($_POST['lus']) == '0') {
			header('Location: index.php?v=login&m=ude');
			die();
		}

		// We check for allowed characters and password length.
		if (($initTools->checkCharacters($_POST['lus']) == '0') || ($initTools->checkCharacters($_POST['lpw']) == '0') || ($initTools->checkLength($_POST['lpw']) == '0')) {
			$initTools->checkPenalty($_POST['lus'],$penalty);
			header('Location: index.php?v=login&m=ict');
			die();
		}

		
		// Calculate user password hash and skey.
		$userPassword = $initCert->usersKeyauthHash($_POST['lus'],$_POST['lpw']);

		// Check authhash is the same with the one stored.
		if ($userPassword['authHash'] !== $initXml->returnUserPassword($_POST['lus'])) {
			$initTools->checkPenalty($_POST['lus'],$penalty);
			header('Location: index.php?v=login&m=wup');
			die();
		}

		// Check that the email that is inside the certificate is the same with the one stored in our database.
		// Added security step to verify that the user users his certificate and not someone else's. 
		if ($initCert->readX509($_POST['lcert'])['subject']['emailAddress'] !== $initXml->returnUserEmail($_POST['lus'])) {
			$initTools->checkPenalty($_POST['lus'],$penalty);
			header('Location: index.php?v=login&m=nyc');
			die();
		}
		
		// Get CA's data
		$cacert = $initXml->getCAdata();
		
		// Check if user's certificate has expired and is singed by the CA.
		if ($initCert->checkCert($initCert->readX509($_POST['lcert']),$initCert->readX509($cacert['cert'])) == '2') {
			$initTools->checkPenalty($_POST['lus'],$penalty);
			header('Location: index.php?v=login&m=che');
			die();
		}

		// Same check for a different value, just making things easier for user, reporting a different message.
		if ($initCert->checkCert($initCert->readX509($_POST['lcert']),$initCert->readX509($cacert['cert'])) == '1') {
			$initTools->checkPenalty($_POST['lus'],$penalty);
			header('Location: index.php?v=login&m=cns');
			die();
		}

		// We check the subject keyid of the supplied certificate is the same with the one stored.
		// We needs this to check if it this the latest certificate and not an old one that the user might have.
		if ($initCert->findSubKeyid($initCert->readX509($_POST['lcert'])) !== $initXml->returnUserSubKeyid(($_POST['lus']))) {
			header('Location: index.php?v=login&m=civ');
			die();
		}

		// Same check as before but for the proper value, if found setup the SESSION variables to let user see his interface.
		if ($initCert->checkCert($initCert->readX509($_POST['lcert']),$initCert->readX509($cacert['cert'])) == '0') {
			$initXml->delPenalty($_POST['lus']);
			$token = bin2hex(openssl_random_pseudo_bytes(64));
			$_SESSION['token'] = $token;
			$_SESSION['sKey'] = $userPassword['sKey'];
			$_SESSION['user'] = $_POST['lus'];
			$_SESSION['ed'] = $initCert->readX509($_POST['lcert'])['validTo_time_t'];

			if ($_SESSION['user'] == $initXml->checkAdmin()) {
				header('Location: index.php?v=admin&t='.$token.'&m=als');
				die();
			}

			header('Location: index.php?v=user&t='.$token.'&m=uls');
			die();
		}
	}
	
 }

// The admin page
 public function admin () {

	if (!empty($_GET['a'])) { $a = $_GET['a']; } else { $a = NULL; }

	$initXml = new Xml();
	$initTools = new Tools();
	$initCert = new Cert();
	$initDraw = new Draw();
	$initLog = new Log();

	// Print head and check for our token. Finally print message.
       	$initDraw->showHead();
	
	if ((!empty($_SESSION['token'])) && ($_SESSION['token'] == $_GET['t'])) {

	 $initDraw->showAdminMenu();
	 if (!empty($_GET['m'])) {
		 $m = $_GET['m'];
	} else {
		$m = NULL;
	}
	$initDraw->showMsg($m);

	// Admin switch for the different actions that the admin user can do.
	switch($a) {
		// Receives the filename to be delete and erases it. Redirects back to log page.
		case "dl":
		 $initLog->deleteLog(base64_decode($_GET['d']));
		 header('Location: index.php?v=admin&t='.$_GET['t'].'&a=sl');
		 break;
	
		// Print actual log file, might take some time in case of many entries.
		case "rl":
	 	 $filename = base64_decode($_GET['d']);
		 $text = $initLog->printLog($filename);
		 $initDraw->showLogFile($filename,$text);
		 break;

		// Print the log files list.
		case "sl":
		 $initDraw->showLogFiles($initLog->listLogFiles());
		break;

		// Receives data with which users needs to be deleted and erases him plus all his passwords. 
		case "eudel":
		 $delUser = explode(":::::",base64_decode($_GET['d']));
		 if ($initXml->delUser($delUser[0],$delUser[1]) !== 0) {
		 	$initXml->delAllPasswords($delUser[0]);
		 }
		 header('Location: index.php?v=admin&t='.$_GET['t'].'&a=eu');
		break;

		// Print table with all users.
		case "eu":
		 $users = $initXml->returnUsers();
		 $initDraw->showUsers($users);
		break;

		// Receives data which penalty to delete and erases it.
		case "vpdel":
		 $delPenalty = base64_decode($_GET['d']);
		 $initXml->delPenalty($delPenalty);
		 header('Location: index.php?v=admin&t='.$_GET['t'].'&a=vp');
		break;

		// Print table with all penalties.
		case "vp":
		 $penalties = $initXml->returnPenalties();
		 $initDraw->showPenalties($penalties);
		break;

		// Download CA certificate.
		case "dcak":
		 $ca = $initXml->getCAdata();
		 $files = array(
 		 	array(rtrim($ca['privateKey']),"privateKey.pem"),
			array(rtrim($ca['publicKey']),"publicKey.pem"),
			array(rtrim($ca['csr']),"csr.pem"),
			array(rtrim($ca['cert']),"cert.pem")
		 );
 		 $zipFile=$initTools->createArchive($files,'echoes-ca-'.time().'');
		 $initTools->send2Browser($zipFile[0],$zipFile[1]);
		 die();
		break;

		// Reset application. Clean's all xml files and destroy's session and redirect's to index to force the wizard.
		case "rs":
		 $initXml->createXml('passwords');
		 $initXml->createXml('users');
		 $initXml->createXml('ca');
		 $initXml->createXml('penalties');
		 unset($_SESSION['token']);
 		 session_unset();
 		 session_destroy();
		 header('Location: index.php');
		break;

		// Unsets all session variables, also the session is being destroyed thus logging out the user.
		// An if exists in the flow in case of certificate renewal and we need to logout the user but force the download of the new certificate.
		case "lo":
		 unset($_SESSION['token']);
		 unset($_SESSION['user']);
		 unset($_SESSION['ed']);
		 if (empty($_SESSION['crn'])) {
 		 	session_unset();
 		 	session_destroy();
		 }
		 header('Location: index.php?v=login');
		break;

		// Renew procedure for admin's certificate.
		// Same logic as the one used in the user page.
		// We get the firstname and lastname of admin user plus his email and build the dn array.  
                case "crn":
                        $ca = $initXml->getCAdata();
                        $userData = $initXml->returnUserNames($_SESSION['user']);
                        $userEmail = $initXml->returnUserEmail($_SESSION['user']);
                        $readCert=$initCert->readX509($ca['cert']);
                        $dn = array(
                                "countryName" => $readCert['issuer']['C'],
                                "stateOrProvinceName" => $readCert['issuer']['ST'],
                                "localityName" => $readCert['issuer']['L'],
                                "organizationName" => $readCert['issuer']['O'],
                                "organizationalUnitName" => $readCert['issuer']['OU'],
                                "commonName" => $userData['Name'].' '.$userData['Lastname'],
                                "emailAddress" =>  $userEmail
                        );
			
			// We generate a new certificate for admin user valid for 180 days.
                        $userCert=$initCert->getKeysCsrCert($dn,$ca['cert'],$ca['privateKey'],"180");

			// We keep the Subject KeyID to check during login that he is using the latest certificate.
                        $subID = $initCert->findSubKeyid($initCert->readX509($userCert['cert']));
                        $initXml->editUserSubKeyid($_SESSION['user'],$subID);

			// We build an array of files and filenames for our zip download.
                        $files = array(
                                array(rtrim($userCert['privateKey']),"privateKey.pem"),
                                array(rtrim($userCert['publicKey']),"publicKey.pem"),
                                array(rtrim($userCert['csr']),"csr.pem"),
                                array(rtrim($userCert['cert']),"cert.pem")
                        );

			// We generate the zip file and save it to a SESSSION variable to survice the http redirect and force a download.
                        $zipFile=$initTools->createArchive($files,'echoes-aa-'.$_SESSION['user'].'-'.time().'');
                        $_SESSION['crn'] = $zipFile;
                        header('Location: index.php?v=admin&t='.$_GET['t'].'&a=lo');
                        die();
		 break;

		// Renew procedure for CA's certificate. Only the admin can perform this action.
		// To  make things easier, most login is included in the renewCAcert function.
		// After we get the new certificate, we save it and redirect to the same admin page with a success message.
                case "cacrn":

			$newCACert=$initCert->renewCACert();
			$initXml->saveNewCACert($newCACert);
                        header('Location: index.php?v=admin&t='.$_GET['t'].'&m=ncac');

		 break;
		
		// In case that no action is define we just print the admin menu.
		default:
		 $initDraw->showAdminPanel();
	 }
	}
	
	// Finally print foot to complete page.
	$initDraw->showFoot();

 }

}

?>
