<?php
// Apache www directory, please change value if you are not using the default one
define( 'rootDir', '/var/www/' );

// Start session
session_set_cookie_params(0);
session_start();

// Include bootstap, loads needed libraries
require 'bootstrap.php';

// Application objects
$view = new View();
$initXml = new Xml();
$initDraw = new Draw();
$initLog = new Log();
$initTools = new Tools();

// Check if proper directory structure exists.
// If there isn't then create directories and files.
$initTools->checkDir(rootDir);

// Check for day log file and if not found one, create it.
$initLog->checkDayLogExists();

// Variables setup.
// The value of v controls which page the user sees.
$v = NULL;

// Check GET array for value of v 
if (!empty($_GET['v'])) {
	$v = $_GET['v'];
}

// Check if CA certificate is pressent or if an admin user has been registered.
// If one of the two is not true change value v to wizard.
if ((!is_array($initXml->checkCA())) || (empty($initXml->checkAdmin()))) {
	
	$v = 'wizard';

}

// Check if an admin user has logged in and if he is bound him to only the admin page.
if ((!empty($_SESSION['user'])) && ($_SESSION['user'] == $initXml->checkAdmin())) {

	$v = 'admin';

}

// Check if a user has logged in and if he is bound him to only the user page.
if ((!empty($_SESSION['user'])) && ($_SESSION['user'] !== $initXml->checkAdmin())) {

	$v = 'user';

}


// Show page that corresponds to the v value.
switch ($v) {
    case "wizard":
	$view->wizard();
	break;
    case "register":
        $view->register();
        break;
    case "user":
        $view->user();
        break;
    case "admin":
        $view->admin();
        break;
    case "login":
        $view->login();
        break;
    default:
	$view->index();
}

?>
