<?php
// Start Draw class
class Draw {

// Head function, yeap it's the head nothing more, nothing less.
// Loads our bootstrap framework for a nice ui, some css, jquery and final a loader for page transitions.
// Also some html beyond the head, to load properly the body and start building the rest of the page.
 public function showHead () {

	echo '<!DOCTYPE html>'."\n";
	echo '<html>'."\n";
	echo '<head>'."\n";
	echo '<title>Echoes - A cloud password manager</title>'."\n";
	echo '<meta charset="utf-8">'."\n";
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">'."\n";
	echo '<link rel="stylesheet" href="css/bootstrap.min.css">'."\n";
	echo '<script src="js/jquery.min.js"></script>'."\n";
	echo '<script src="js/bootstrap.min.js"></script>'."\n";
	echo '<script src="js/loader.js"></script>'."\n";
	echo '<link href="css/loader.css" rel="stylesheet">'."\n";
	echo '</head>'."\n";
	echo '<body onload="myFunction()">'."\n";
	echo '<div class="jumbotron text-center">'."\n";
	echo '<h1><a href="index.php" style="color:black;">Echoes</a></h1>'."\n";   
	echo '<p>A cloud password manager.</p>'."\n";
	echo '</div>'."\n";
	echo '<div id="loader"></div>'."\n";
	echo '<div class="container animate-bottom" id="myDiv">'."\n";

 }

// Foot function, just closes the body's starting div's and completes page.
 public function showFoot () {

	echo '</div>'."\n";
	echo '</div>'."\n";
	echo '</body>'."\n";
	echo '</html>';

 }
 
// Penalties table, printing anything in the penalties.xml file plus build the delete links if the user wants to erase a penalty.
// Useful for admin, testing the application and needs a way to erase login penalties.
 public function showPenalties ($data) {

        echo '<table id="penaltiesTable" class="table table-hover">'."\n";
        echo '<thead>'."\n";
        echo '<tr>'."\n";
        echo '<td>User</td>'."\n";
        echo '<td>Date Added</td>'."\n";
        echo '<td>Penalty</td>'."\n";
        echo '<td>Actions</td>'."\n";
        echo '</tr>'."\n";
        echo '</thead>'."\n";
        echo '<tfoot>'."\n";
        echo '<tr>'."\n";
        echo '<td>'."\n";
        echo '<a href="index.php?v=admin&t='.$_GET['t'].'" type="button" class="btn btn-default">Back</a>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '</tr>'."\n";
        echo '</tfoot>'."\n";
        echo '<tbody>'."\n";

        foreach ($data as $key=>$value) {
                echo '<tr>'."\n";
                echo '<td>'.$value['user'].'</td>'."\n";
                echo '<td>'.date('d-m-Y H:i:s', $value['date']).'</td>'."\n";
                echo '<td>'.$value['penalty'].'</td>'."\n";
                echo '<td><a href="index.php?v=admin&t='.$_GET['t'].'&a=vpdel&d='.base64_encode($value['user']).'">Delete</a></td>'."\n";
                echo '</tr>'."\n";
        }

        echo '</tbody>'."\n";
        echo '</table>'."\n";
 }

// Priting all the log file. Receives an array with all the filenames in the directory and builds the corresponding actions.
// Actions are show and delete. Show prins the contents, delete erases the file.
 public function showLogFiles($data) {
        echo '<table id="logFilesTable" class="table table-hover">'."\n";
        echo '<thead>'."\n";
        echo '<tr>'."\n";
        echo '<td>File</td>'."\n";
        echo '<td>Actions</td>'."\n";
        echo '</tr>'."\n";
        echo '</thead>'."\n";
        echo '<tfoot>'."\n";
        echo '<tr>'."\n";
        echo '<td>'."\n";
        echo '<a href="index.php?v=admin&t='.$_GET['t'].'" type="button" class="btn btn-default">Back</a>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '</tr>'."\n";
        echo '</tfoot>'."\n";
        echo '<tbody>'."\n";

        foreach ($data as $key=>$value) {
                echo '<tr>'."\n";
                echo '<td>'.$value.'</td>'."\n";
                echo '<td><a href="index.php?v=admin&t='.$_GET['t'].'&a=rl&d='.base64_encode($value).'">View</a> - <a href="index.php?v=admin&t='.$_GET['t'].'&a=dl&d='.base64_encode($value).'">Delete</a></td>'."\n";
                echo '</tr>'."\n";
        }

        echo '</tbody>'."\n";
        echo '</table>'."\n";

 }

// Prints a textarea containing the log files contents. Just a text dump function. Prints also filename.
 public function showLogFile($filename,$data) {
echo '
  <h2>Viewing file:</h2>
  <div class="panel-group">
    <div class="panel panel-default">
      <div class="panel-heading">'.$filename.'</div>
      <div class="panel-body">
        <textarea class="form-control" rows="23">'.$data.'</textarea>
      </div>
    </div>
  </div>
<a href="index.php?v=admin&t='.$_GET['t'].'&a=sl" type="button" class="btn btn-default">Back</a>
';
 }

// The register form, just a form with some bootstrap classes to pretify the ui.
 public function showRegisterForm () {
 echo '
  <h2>Fill out the following form. All fields are necessary!</h2>
  <form class="form-horizontal" action="index.php?v=register" method="post">
    <div class="form-group">
      <label class="control-label col-sm-2" for="rfn">Firstname:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="rfn" placeholder="Enter firstname" name="rfn">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="rln">Lastname:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="rln" placeholder="Enter lastname" name="rln">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="rem">Email:</label>
      <div class="col-sm-10">
        <input type="email" class="form-control" id="rem" placeholder="Enter email" name="rem">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="rus">Username:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="rus" placeholder="Enter username" name="rus">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="rpw">Password:</label>
      <div class="col-sm-10">          
        <input type="password" class="form-control" id="rpw" placeholder="Enter password" name="rpw">
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
	<a href="index.php" type="button" class="btn btn-default">Back</a>
      </div>
    </div>
  </form>
 ';
 }

// The login form. Use an one line text area to paste the certificate.
// One line was choosen to hide the rest of the certificate and avoid screen capture software.
// A password field might be better suited to hide the content but on the other hand a password is a word not a text blob.
// Thus the one liner text area was chosen.
 public function showLoginForm () {
echo '
  <h2>Insert your credentials and your certificate.</h2>
  <form class="form-horizontal" action="index.php?v=login" method="post">
    <div class="form-group">
      <label class="control-label col-sm-2" for="lus">Username:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="lus" placeholder="Enter username" name="lus">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="lpw">Password:</label>
      <div class="col-sm-10">          
        <input type="password" class="form-control" id="lpw" placeholder="Enter password" name="lpw">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="lcert">Certificate:</label>
      <div class="col-sm-10">          
        <textarea class="form-control" id="lcert" placeholder="Paste certificate" name="lcert" rows="1"></textarea>
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
	<a href="index.php" type="button" class="btn btn-default">Back</a>
      </div>
    </div>
  </form>
';
 }

// Just our index. Two options, register or login.
 public function showIndexMain () {
echo '
<h2>Welcome! Please choose.</h2>
  <a href="index.php?v=register" class="btn btn-default btn-lg btn-block" role="button">Register</a>
  <a href="index.php?v=user" class="btn btn-default btn-lg btn-block" role="button">Login</a>
';
 }

// A big switch for prinitng info between http redirects. Receives msgid and prints the corresponding text.
 public function showMsg ($msgid = NULL) {

	switch ($msgid) {
	case 'wcas':
	echo '<div class="sysmsg">A CA certificate has been installed!</div>'."\n";
	break;
	case 'wcaf':
	echo '<div class="sysmsgerror">A CA certificate has not been installed!</div>'."\n";
	echo '<div class="sysmsgerror">Check your filesystem if the application has proper rights!</div>'."\n";
	break;
	case 'wcaef':
	echo '<div class="sysmsgerror">Please fill all the fields!</div>'."\n";
	break;
	case 'wcawd':
	echo '<div class="sysmsgerror">You have supplied wrong data. Please use only characters and numbers!</div>'."\n";
	break;
	case 'wcawcc':
	echo '<div class="sysmsgerror">You have supplied wrong country code. Maximum three capital letters are allowed!</div>'."\n";
	break;
	case 'ncac':
	echo '<div class="sysmsg">CA\'s crtificate has been renewed!</div>'."\n";
	break;
	case 'was':
	echo '<div class="sysmsg">Congratulations, the admin user has been added!</div>'."\n";
	break;
	case 'waf':
	echo '<div class="sysmsgerror">There was a problem! Please fill again the form with proper values!</div>'."\n";
	break;
	case 'uas':
	echo '<div class="sysmsg">Congratulations, the  user has been added!</div>'."\n";
	break;
	case 'ulf':
	echo '<div class="sysmsgerror">There was a problem! Please try again!</div>'."\n";
	break;
	case 'ulo':
	echo '<div class="sysmsg">Thank you for using Echoes!</div>'."\n";
	break;
	case 'urg':
	echo '<div class="sysmsg">Thank you for registering!</div>'."\n";
	break;
	case 'urf':
	echo '<div class="sysmsgerror">There was an error! Please try again!</div>'."\n";
	break;
	case 'rue':
	echo '<div class="sysmsgerror">Username is not available!</div>'."\n";
	break;
	case 'urf':
	echo '<div class="sysmsg">Please register using the following form. All fields are necessary!</div>'."\n";
	break;
	case 'ree':
	echo '<div class="sysmsg">Email is being used, choose a different one!</div>'."\n";
	break;
	case 'fnf':
	echo '<div class="sysmsgerror">You haven\'t filled everything!</div>'."\n";
	break;
	case 'ict':
	echo '<div class="sysmsgerror">Invalid credentials! Please try again.</div>'."\n";
	break;
	case 'ude':
	echo '<div class="sysmsgerror">User doesn\'t exist. Please try again.</div>'."\n";
	break;
	case 'wup':
	echo '<div class="sysmsgerror">User password is not right. Please try again.</div>'."\n";
	break;
	case 'nyc':
	echo '<div class="sysmsgerror">Certificate is not valid. Provide the right certificate.</div>'."\n";
	break;
	case 'che':
	echo '<div class="sysmsgerror">Certificate has expired, please contact administrator!</div>'."\n";
	break;
	case 'cns':
	echo '<div class="sysmsgerror">The certificate is not valid, please contact administrator!</div>'."\n";
	break;
	case 'civ':
	echo '<div class="sysmsgerror">The certificate is not valid or has been renewed. Please try with the right one!</div>'."\n";
	break;
	case 'uls':
	echo '<div class="sysmsg">Welcome '.$_SESSION['user'].'!</div>'."\n";
	break;
	case 'pwe':
	echo '<div class="sysmsgerror">A password already exists for this domain and username combination!</div>'."\n";
	break;
	case 'pwde':
	echo '<div class="sysmsgerror">Password for this domain and username combination doesn\'t exist!.</div>'."\n";
	break;
	case 'pwdel':
	echo '<div class="sysmsg">Password deleted!</div>'."\n";
	break;
	case 'pip':
	echo '<div class="sysmsgerror">Penalty in place! Please login after '.date('d-m-Y H:i:s', base64_decode($_GET['d'])).'!</div>'."\n";
	break;
	case 'aib':
	echo '<div class="sysmsgerror">Account is banned! Please contact administrator!</div>'."\n";
	break;
	case 'upc':
	echo '<div class="sysmsg">Your password has changed!</div>'."\n";
	break;
	case 'woup':
	echo '<div class="sysmsgerror">Wrong old password! Try again!</div>'."\n";
	break;
	case 'rpiw':
	echo '<div class="sysmsgerror">You haven\'t supplied the same new password twice. Try again!</div>'."\n";
	break;
	case 'eff':
	echo '<div class="sysmsgerror">You haven\'t filled everything needed. Try again!</div>'."\n";
	break;
	case 'sonp':
	echo '<div class="sysmsgerror">Old and new password are the same. Try again!</div>'."\n";
	break;
	case 'pla':
	echo '<div class="sysmsgerror">A new certificate was generated for you! Please login again!</div>'."\n";
	break;
	case 'als':
	echo '<div class="sysmsg">Welcome to the administration panel!</div>'."\n";
	break;
	default:
	break;
	}
 }

// The CA's certificate wizard form. Defines some values for our certificate. A list with all the allowed values for country is being used.
// This was a needed approach because openssl that is being used produces an error is a not valid value is used.
 public function showCAWizard () {
 echo '
  <h2>Welcome to Echoes. Please generate a root certificate to use application.</h2>
  <form class="form-horizontal" action="index.php?v=wizard" method="post">
    <div class="form-group">
      <label class="control-label col-sm-2" for="wcac">Country:</label>
      <div class="col-sm-10">
		<select id="wcac" name="wcac" class="form-control">
				<option value="GR">Greece</option>
				<option value="US">United States of America</option>
				<option value="CA">Canada</option>
				<option value="AX">Åland Islands</option>
				<option value="AD">Andorra</option>
				<option value="AE">United Arab Emirates</option>
				<option value="AF">Afghanistan</option>
				<option value="AG">Antigua and Barbuda</option>
				<option value="AI">Anguilla</option>
				<option value="AL">Albania</option>
				<option value="AM">Armenia</option>
				<option value="AN">Netherlands Antilles</option>
				<option value="AO">Angola</option>
				<option value="AQ">Antarctica</option>
				<option value="AR">Argentina</option>
				<option value="AS">American Samoa</option>
				<option value="AT">Austria</option>
				<option value="AU">Australia</option>
				<option value="AW">Aruba</option>
				<option value="AZ">Azerbaijan</option>
				<option value="BA">Bosnia and Herzegovina</option>
				<option value="BB">Barbados</option>
				<option value="BD">Bangladesh</option>
				<option value="BE">Belgium</option>
				<option value="BF">Burkina Faso</option>
				<option value="BG">Bulgaria</option>
				<option value="BH">Bahrain</option>
				<option value="BI">Burundi</option>
				<option value="BJ">Benin</option>
				<option value="BM">Bermuda</option>
				<option value="BN">Brunei Darussalam</option>
				<option value="BO">Bolivia</option>
				<option value="BR">Brazil</option>
				<option value="BS">Bahamas</option>
				<option value="BT">Bhutan</option>
				<option value="BV">Bouvet Island</option>
				<option value="BW">Botswana</option>
				<option value="BZ">Belize</option>
				<option value="CA">Canada</option>
				<option value="CC">Cocos (Keeling) Islands</option>
				<option value="CF">Central African Republic</option>
				<option value="CH">Switzerland</option>
				<option value="CI">Cote D\'Ivoire (Ivory Coast)</option>
				<option value="CK">Cook Islands</option>
				<option value="CL">Chile</option>
				<option value="CM">Cameroon</option>
				<option value="CN">China</option>
				<option value="CO">Colombia</option>
				<option value="CR">Costa Rica</option>
				<option value="CS">Czechoslovakia (former)</option>
				<option value="CV">Cape Verde</option>
				<option value="CX">Christmas Island</option>
				<option value="CY">Cyprus</option>
				<option value="CZ">Czech Republic</option>
				<option value="DE">Germany</option>
				<option value="DJ">Djibouti</option>
				<option value="DK">Denmark</option>
				<option value="DM">Dominica</option>
				<option value="DO">Dominican Republic</option>
				<option value="DZ">Algeria</option>
				<option value="EC">Ecuador</option>
				<option value="EE">Estonia</option>
				<option value="EG">Egypt</option>
				<option value="EH">Western Sahara</option>
				<option value="ER">Eritrea</option>
				<option value="ES">Spain</option>
				<option value="ET">Ethiopia</option>
				<option value="FI">Finland</option>
				<option value="FJ">Fiji</option>
				<option value="FK">Falkland Islands (Malvinas)</option>
				<option value="FM">Micronesia</option>
				<option value="FO">Faroe Islands</option>
				<option value="FR">France</option>
				<option value="FX">France, Metropolitan</option>
				<option value="GA">Gabon</option>
				<option value="GB">Great Britain (UK)</option>
				<option value="GD">Grenada</option>
				<option value="GE">Georgia</option>
				<option value="GF">French Guiana</option>
				<option value="GG">Guernsey</option>
				<option value="GH">Ghana</option>
				<option value="GI">Gibraltar</option>
				<option value="GL">Greenland</option>
				<option value="GM">Gambia</option>
				<option value="GN">Guinea</option>
				<option value="GP">Guadeloupe</option>
				<option value="GQ">Equatorial Guinea</option>
				<option value="GS">S. Georgia and S. Sandwich Isls.</option>
				<option value="GT">Guatemala</option>
				<option value="GU">Guam</option>
				<option value="GW">Guinea-Bissau</option>
				<option value="GY">Guyana</option>
				<option value="HK">Hong Kong</option>
				<option value="HM">Heard and McDonald Islands</option>
				<option value="HN">Honduras</option>
				<option value="HR">Croatia (Hrvatska)</option>
				<option value="HT">Haiti</option>
				<option value="HU">Hungary</option>
				<option value="ID">Indonesia</option>
				<option value="IE">Ireland</option>
				<option value="IL">Israel</option>
				<option value="IM">Isle of Man</option>
				<option value="IN">India</option>
				<option value="IO">British Indian Ocean Territory</option>
				<option value="IS">Iceland</option>
				<option value="IT">Italy</option>
				<option value="JE">Jersey</option>
				<option value="JM">Jamaica</option>
				<option value="JO">Jordan</option>
				<option value="JP">Japan</option>
				<option value="KE">Kenya</option>
				<option value="KG">Kyrgyzstan</option>
				<option value="KH">Cambodia</option>
				<option value="KI">Kiribati</option>
				<option value="KM">Comoros</option>
				<option value="KN">Saint Kitts and Nevis</option>
				<option value="KR">Korea (South)</option>
				<option value="KW">Kuwait</option>
				<option value="KY">Cayman Islands</option>
				<option value="KZ">Kazakhstan</option>
				<option value="LA">Laos</option>
				<option value="LC">Saint Lucia</option>
				<option value="LI">Liechtenstein</option>
				<option value="LK">Sri Lanka</option>
				<option value="LS">Lesotho</option>
				<option value="LT">Lithuania</option>
				<option value="LU">Luxembourg</option>
				<option value="LV">Latvia</option>
				<option value="LY">Libya</option>
				<option value="MA">Morocco</option>
				<option value="MC">Monaco</option>
				<option value="MD">Moldova</option>
				<option value="ME">Montenegro</option>
				<option value="MG">Madagascar</option>
				<option value="MH">Marshall Islands</option>
				<option value="MK">Macedonia</option>
				<option value="ML">Mali</option>
				<option value="MM">Myanmar</option>
				<option value="MN">Mongolia</option>
				<option value="MO">Macau</option>
				<option value="MP">Northern Mariana Islands</option>
				<option value="MQ">Martinique</option>
				<option value="MR">Mauritania</option>
				<option value="MS">Montserrat</option>
				<option value="MT">Malta</option>
				<option value="MU">Mauritius</option>
				<option value="MV">Maldives</option>
				<option value="MW">Malawi</option>
				<option value="MX">Mexico</option>
				<option value="MY">Malaysia</option>
				<option value="MZ">Mozambique</option>
				<option value="NA">Namibia</option>
				<option value="NC">New Caledonia</option>
				<option value="NE">Niger</option>
				<option value="NF">Norfolk Island</option>
				<option value="NG">Nigeria</option>
				<option value="NI">Nicaragua</option>
				<option value="NL">Netherlands</option>
				<option value="NO">Norway</option>
				<option value="NP">Nepal</option>
				<option value="NR">Nauru</option>
				<option value="NT">Neutral Zone</option>
				<option value="NU">Niue</option>
				<option value="NZ">New Zealand (Aotearoa)</option>
				<option value="OM">Oman</option>
				<option value="PA">Panama</option>
				<option value="PE">Peru</option>
				<option value="PF">French Polynesia</option>
				<option value="PG">Papua New Guinea</option>
				<option value="PH">Philippines</option>
				<option value="PK">Pakistan</option>
				<option value="PL">Poland</option>
				<option value="PM">St. Pierre and Miquelon</option>
				<option value="PN">Pitcairn</option>
				<option value="PR">Puerto Rico</option>
				<option value="PS">Palestinian Territory</option>
				<option value="PT">Portugal</option>
				<option value="PW">Palau</option>
				<option value="PY">Paraguay</option>
				<option value="QA">Qatar</option>
				<option value="RE">Reunion</option>
				<option value="RO">Romania</option>
				<option value="RS">Serbia</option>
				<option value="RU">Russian Federation</option>
				<option value="RW">Rwanda</option>
				<option value="SA">Saudi Arabia</option>
				<option value="SB">Solomon Islands</option>
				<option value="SC">Seychelles</option>
				<option value="SE">Sweden</option>
				<option value="SG">Singapore</option>
				<option value="SH">St. Helena</option>
				<option value="SI">Slovenia</option>
				<option value="SJ">Svalbard and Jan Mayen Islands</option>
				<option value="SK">Slovak Republic</option>
				<option value="SL">Sierra Leone</option>
				<option value="SM">San Marino</option>
				<option value="SN">Senegal</option>
				<option value="SR">Suriname</option>
				<option value="ST">Sao Tome and Principe</option>
				<option value="SU">USSR (former)</option>
				<option value="SV">El Salvador</option>
				<option value="SZ">Swaziland</option>
				<option value="TC">Turks and Caicos Islands</option>
				<option value="TD">Chad</option>
				<option value="TF">French Southern Territories</option>
				<option value="TG">Togo</option>
				<option value="TH">Thailand</option>
				<option value="TJ">Tajikistan</option>
				<option value="TK">Tokelau</option>
				<option value="TM">Turkmenistan</option>
				<option value="TN">Tunisia</option>
				<option value="TO">Tonga</option>
				<option value="TP">East Timor</option>
				<option value="TR">Turkey</option>
				<option value="TT">Trinidad and Tobago</option>
				<option value="TV">Tuvalu</option>
				<option value="TW">Taiwan</option>
				<option value="TZ">Tanzania</option>
				<option value="UA">Ukraine</option>
				<option value="UG">Uganda</option>
				<option value="UM">US Minor Outlying Islands</option>
				<option value="US">United States</option>
				<option value="UY">Uruguay</option>
				<option value="UZ">Uzbekistan</option>
				<option value="VA">Vatican City State (Holy See)</option>
				<option value="VC">Saint Vincent and the Grenadines</option>
				<option value="VE">Venezuela</option>
				<option value="VG">Virgin Islands (British)</option>
				<option value="VI">Virgin Islands (U.S.)</option>
				<option value="VN">Viet Nam</option>
				<option value="VU">Vanuatu</option>
				<option value="WF">Wallis and Futuna Islands</option>
				<option value="WS">Samoa</option>
				<option value="YE">Yemen</option>
				<option value="YT">Mayotte</option>
				<option value="ZA">South Africa</option>
				<option value="ZM">Zambia</option>
				<option value="COM">US Commercial</option>
				<option value="EDU">US Educational</option>
				<option value="GOV">US Government</option>
				<option value="INT">International</option>
				<option value="MIL">US Military</option>
				<option value="NET">Network</option>
				<option value="ORG">Non-Profit Organization</option>
				<option value="ARPA">Old style Arpanet</option>
		</select>
	</div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wcas">State:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="wcas" placeholder="Enter state" name="wcas">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wcal">Locality:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="wcal" placeholder="Enter locality" name="wcal">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wcao">Organization:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="wcao" placeholder="Enter organization" name="wcao">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wcau">Unit:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="wcau" placeholder="Enter unit" name="wcau">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wcan">Name:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="wcan" placeholder="Enter name" name="wcan">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wcae">Email:</label>
      <div class="col-sm-10">
        <input type="email" class="form-control" id="wcae" placeholder="Enter email" name="wcae">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
        <a href="index.php" type="button" class="btn btn-default">Back</a>
      </div>
    </div>
  </form>
 ';
 }

// The admin user wizard form. It has the same fields as the user registration form but the names are different.
// The different names make this form to register as an admin user and not a simple one.
// It show up only if there isn't an admin user defined in the users.xml
 public function showAdminWizard () {
 echo '
  <h2>Welcome to Echoes! Please register an admin account to use the application!</h2>
  <form class="form-horizontal" action="index.php?v=wizard" method="post">
    <div class="form-group">
      <label class="control-label col-sm-2" for="wfn">Firstname:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="wfn" placeholder="Enter firstname" name="wfn">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wln">Lastname:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="wln" placeholder="Enter lastname" name="wln">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wem">Email:</label>
      <div class="col-sm-10">
        <input type="email" class="form-control" id="wem" placeholder="Enter email" name="wem">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wus">Username:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="wus" placeholder="Enter username" name="wus">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="wpw">Password:</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" id="wpw" placeholder="Enter password" name="wpw">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
        <a href="index.php" type="button" class="btn btn-default">Back</a>
      </div>
    </div>
  </form>
 ';
 }

// Print the admin menu, we use the Xml and Cert class to print the CA's certificate expiration date.
// Also admin's certificate expiration date is being printed. Renewal buttons are being printed also.
 public function showAdminMenu () {

	$initXml = new Xml();
	$initCert = new Cert();

	$expireDate = $initCert->readX509($initXml->getCAdata()['cert']);

echo '
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="index.php?v=admin&t='.$_GET['t'].'">Home</a>
    </div>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="index.php?v=admin&t='.$_GET['t'].'&a=lo"><span class="glyphicon glyphicon-log-in"></span> ['.$_SESSION['user'].'] Logout</a></li>
    </ul>
  </div>
</nav>
<div>CA\'s certificate expires at '.date('d-m-Y H:i:s', $expireDate['validTo_time_t']).' <a href="index.php?v=admin&t='.$_GET['t'].'&a=cacrn" type="button" class="btn btn-default btn-sm">Renew</a></div><br />
<div>Your certificate expires at '.date('d-m-Y H:i:s', $_SESSION['ed']).' <a href="index.php?v=admin&t='.$_GET['t'].'&a=crn" type="button" class="btn btn-default btn-sm">Renew</a></div><br />
';
 }

// Prints the admin panel, with all the options as links to different pages. 
 public function showAdminPanel () {
 	echo'
<br />
<div>
<a href="index.php?v=admin&t='.$_GET['t'].'&a=sl" class="btn btn-default btn-lg btn-block" role="button">Show logs</a>
<a href="index.php?v=admin&t='.$_GET['t'].'&a=eu" class="btn btn-default btn-lg btn-block" role="button">Show users</a>
<a href="index.php?v=admin&t='.$_GET['t'].'&a=vp" class="btn btn-default btn-lg btn-block" role="button">View penalties</a>
<a href="index.php?v=admin&t='.$_GET['t'].'&a=dcak" class="btn btn-default btn-lg btn-block" role="button">Download application\'s certificate and keys</a>
<a href="index.php?v=admin&t='.$_GET['t'].'&a=rs" class="btn btn-default btn-lg btn-block" role="button">Reset application</a>
</div>
';
 }

// Prints the user menu and his certificate expiration date with renewal button.
 public function showUserMenu () {
echo '
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="index.php?v=user&t='.$_GET['t'].'">Home</a>
    </div>
    <ul class="nav navbar-nav">
      <li><a href="index.php?v=user&t='.$_GET['t'].'&a=cupwd">Change password</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="index.php?v=user&t='.$_GET['t'].'&a=lo"><span class="glyphicon glyphicon-log-in"></span> ['.$_SESSION['user'].'] Logout</a></li>
    </ul>
  </div>
</nav>
<div>Your certificate expires at '.date('d-m-Y H:i:s', $_SESSION['ed']).' <a href="index.php?v=user&t='.$_GET['t'].'&a=crn" type="button" class="btn btn-default btn-sm">Renew</a></div><br />
';
 }

// Print a table with all the users and a delete button if the admin wants to erased him.
// Deleting a user erases also the passwords.
  public function showUsers ($data) {

	$initXml = new Xml();

        echo '<table id="usersTable" class="table table-hover">'."\n";
        echo '<thead>'."\n";
        echo '<tr>'."\n";
        echo '<td>User</td>'."\n";
        echo '<td>Password</td>'."\n";
        echo '<td>Firstname</td>'."\n";
        echo '<td>Lastnamename</td>'."\n";
        echo '<td>Email</td>'."\n";
        echo '<td>Role</td>'."\n";
        echo '<td>Subject Key ID</td>'."\n";
        echo '<td>Actions</td>'."\n";
        echo '</tr>'."\n";
        echo '</thead>'."\n";
        echo '<tfoot>'."\n";
        echo '<tr>'."\n";
        echo '<td>'."\n";
	echo '<a href="index.php?v=admin&t='.$_GET['t'].'" type="button" class="btn btn-default">Back</a>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '<td>'."\n";
        echo '</td>'."\n";
        echo '</tr>'."\n";
        echo '</tfoot>'."\n";
        echo '<tbody>'."\n";

        foreach ($data as $key=>$value) {
                echo '<tr>'."\n";
                echo '<td>'.$value['user'].'</td>'."\n";
                echo '<td>'.$value['password'].'</td>'."\n";
                echo '<td>'.$value['name'].'</td>'."\n";
                echo '<td>'.$value['lastname'].'</td>'."\n";
                echo '<td>'.$value['email'].'</td>'."\n";
                echo '<td>'.$value['role'].'</td>'."\n";
                echo '<td>'.$value['keyid'].'</td>'."\n";
		if ($value['user'] !== $initXml->checkAdmin()) {
                echo '<td><a href="index.php?v=admin&t='.$_GET['t'].'&a=eudel&d='.base64_encode($value['user'].':::::'.$value['email']).'">Delete</a></td>'."\n";
		} else {
		echo '<td></td>';
		}
                echo '</tr>'."\n";
        }

        echo '</tbody>'."\n";
        echo '</table>'."\n";

 }

// A pager function, uses supplied data to calcuate which page of results are printed and to show the corresponding links for the rest of the data.
// Needed if the user has many stored passwords. If the data hasn't exceed the results per page limit, prints buttons but in a disable state.  
 public function showPager ($view,$action=NULL,$page=NULL,$mpage) {
 $ppage=$page-1;
 $npage=$page+1;
 echo '<ul class="pager">';
 if ($page < 2) {
  echo '<li class="previous disabled"><a>Previous</a></li>';
 } else {
  echo '<li class="previous"><a href="index.php?v='.$view.'&t='.$_GET['t'];

    if (!empty($action)) {
       echo '&a='.$action;
    }

 echo '&p='.$ppage.'">Previous</a></li>';
 }
 
 if ($mpage < $npage) {
 
 echo '<li class="next disabled"><a>Next</a></li>';
 
 } else {

 echo '<li class="next"><a href="index.php?v='.$view.'&t='.$_GET['t'];
 
 if (!empty($action)) {
   echo '&a='.$action;
 }
 
 echo '&p='.$npage.'">Next</a></li>';
 }
 echo '</ul>';
 }

// Print all the passwords of a user. Also the calculates and prints actions links.
// A user can show password, change it or delete it. Also an add button for the user to add his password.
 public function showUserPasswords ($data) {

	echo '<table id="passwdTable" class="table table-hover">'."\n";
	echo '<thead>'."\n";
	echo '<tr>'."\n";
	echo '<td>Domain</td>'."\n";
	echo '<td>Username</td>'."\n";
	echo '<td>Comment</td>'."\n";
	echo '<td>Actions</td>'."\n";
	echo '<td>Status</td>'."\n";
	echo '</tr>'."\n";
	echo '</thead>'."\n";
	echo '<tfoot>'."\n";
	echo '<tr>'."\n";
	echo '<td>'."\n";
	echo '<a href="index.php?v=user&t='.$_GET['t'].'&a=adpw" type="button" class="btn btn-default">Add</a>'."\n";
	echo '</td>'."\n";
	echo '<td>'."\n";
	echo '</td>'."\n";
	echo '<td>'."\n";
	echo '</td>'."\n";
	echo '<td>'."\n";
	echo '</td>'."\n";
	echo '<td>'."\n";
	echo '</td>'."\n";
	echo '</tr>'."\n";
	echo '</tfoot>'."\n";
	echo '<tbody>'."\n";
	
	foreach ($data as $key=>$value) {
		echo '<tr>'."\n";
		echo '<td>'.$value[0].'</td>'."\n";
		echo '<td>'.$value[1].'</td>'."\n";
		echo '<td>'.$value[3].'</td>'."\n";
		echo '<td><a href="index.php?v=user&t='.$_GET['t'].'&a=shpw&d='.base64_encode($value[0].":::::".$value[1].":::::".$value[3]).'">Show</a> - <a href="index.php?v=user&t='.$_GET['t'].'&a=edpw&d='.base64_encode($value[0].":::::".$value[1].":::::".$value[3]).'">Edit</a> - <a href="index.php?v=user&t='.$_GET['t'].'&a=delpw&d='.base64_encode($value[0].":::::".$value[1].":::::".$value[3]).'">Delete</a></td>'."\n";
		echo '<td>'.$value[4].'</td>'."\n";
		echo '</tr>'."\n";
	}

	echo '</tbody>'."\n";
	echo '</table>'."\n";

 }

// A form for the user to input a domain password.
 public function showAddPasswordForm () {
echo '
  <h2>Fill out the following form. All fields are necessary!</h2>
  <form class="form-horizontal" action="index.php?v=user&t='.$_GET['t'].'" method="post">
    <div class="form-group">
      <label class="control-label col-sm-2" for="adpwd">Domain:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="adpwd" placeholder="Enter domain" name="adpwd">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="adpwu">Username:</label>
      <div class="col-sm-10">          
        <input type="text" class="form-control" id="adpwu" placeholder="Enter domain\'s username" name="adpwu">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="adpwp">Password:</label>
      <div class="col-sm-10">          
        <input type="password" class="form-control" id="adpwp" placeholder="Enter domain\'s password" name="adpwp">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="adpwc">Comment:</label>
      <div class="col-sm-10">          
        <input type="text" class="form-control" id="adpwc" placeholder="Enter a comment" name="adpwc">
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
	<a href="index.php?v=user&t='.$_GET['t'].'" type="button" class="btn btn-default">Back</a>
      </div>
    </div>
  </form>
';
 }

// A form that prints all the data of a domain except password that is empty for the user to fill out a new one.
 public function showEditPasswordForm ($domain,$username,$comment) {
echo '
  <h2>Fill out the following form. All fields are necessary!</h2>
  <form class="form-horizontal" action="index.php?v=user&t='.$_GET['t'].'" method="post">
    <div class="form-group">
      <label class="control-label col-sm-2" for="edpwd">Domain:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="edpwd" placeholder="Enter domain" name="edpwd" value="'.$domain.'" readonly>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="edpwu">Username:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="edpwu" placeholder="Enter domain\'s username" value="'.$username.'" name="edpwu" readonly>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="edpwp">Password:</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" id="edpwp" placeholder="Enter domain\'s password" name="edpwp">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="edpwc">Comment:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="edpwc" placeholder="Enter a comment" name="edpwc" value="'.$comment.'"readonly>
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
	<a href="index.php?v=user&t='.$_GET['t'].'" type="button" class="btn btn-default">Back</a>
      </div>
    </div>
  </form>
';
 }
 
// A form for the user to change his password.
 public function showEditUserPasswordForm () {
echo '
  <h2>Fill out the following form. All fields are necessary!</h2>
  <form class="form-horizontal" action="index.php?v=user&t='.$_GET['t'].'" method="post">
    <div class="form-group">
      <label class="control-label col-sm-2" for="eupwdo">Old password:</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" id="eupwdo" placeholder="Enter old password" name="eupwdo">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="eupwdn">New password:</label>
      <div class="col-sm-10">          
        <input type="password" class="form-control" id="eupwdn" placeholder="Enter the new password" name="eupwdn">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="eupwdr">Repeat new password:</label>
      <div class="col-sm-10">          
        <input type="password" class="form-control" id="eupwdr" placeholder="Enter again the new password" name="eupwdr">
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
	<a href="index.php?v=user&t='.$_GET['t'].'" type="button" class="btn btn-default">Back</a>
      </div>
    </div>
  </form>
';
 }
 

// Print's the decrypted password for the user to copy it.
 public function showPassword ($domain,$username,$password,$comment) {
echo '
<h2>Details</h2>
  <div class="panel-group">
    <div class="panel panel-default">
      <div class="panel-heading">Domain:</div>
      <div class="panel-body">'.$domain.'</div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">Username:</div>
      <div class="panel-body">'.$username.'</div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">Password:</div>
      <div class="panel-body">'.$password.'</div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">Comment:</div>
      <div class="panel-body">'.$comment.'</div>
    </div>
  </div>
<a href="index.php?v=user&t='.$_GET['t'].'" type="button" class="btn btn-default">Back</a>
';
 }
}
?>
