<!DOCTYPE HTML>
<html lang="en">
<?php
   /*********************************************************************************************
	*                                         index.php                                          *
	*-------------------------------------------------------------------------------------------*
	* 	Written By: William Mace 																*
	*	Date: 03/28/2019																		*
	*-------------------------------------------------------------------------------------------*
	*	Descriotion:																			*
	*		This is the login screen for estimating.  It uses the the adLDAP library to use  	*
	*		Active Directory authentication.													*
	*-------------------------------------------------------------------------------------------*
	*  Paremeters: 													                      		*
	*		none																			  	*
	*-------------------------------------------------------------------------------------------*
	* 	Modifications:																		  	*
	*	Date:		By:					Description:										  	*
	*	12/31/2000	William Mace															  	*
	*********************************************************************************************/

//Initialize variables 	
$logout="";
$username="";
$password="";
$formage=""; 
$failed = 0;
$cookie_expiration_time=2592000;
$remberme=0;

//If logout is passed to set the logout variable
if (isset($_GET['logout'])) { 
	$logout = $_GET['logout'];
}

//If logout is yer then destroy the session
if ($logout == "yes") { //destroy the session
	session_start();
	$_SESSION = array();
	session_destroy();
}

//force the browser to use ssl (STRONGLY RECOMMENDED!!!!!!!!)//
//if ($_SERVER["SERVER_PORT"] != 443){ 
//    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']); 
//	print "test";
//    exit(); 
//}

//you should look into using PECL filter or some form of filtering here for POST variables

//Gets the username and sets it 
if (isset($_POST["username"])) {
	$username = strtoupper($_POST["username"]); //remove case sensitivity on the username
}

if (isset($_POST["password"])) {
	$password = $_POST["password"];
}

if (isset($_POST["formage"])) {
	$formage = $_POST["formage"];
}

if (isset($_POST["oldform"])) { //prevent null bind

	if ($username != NULL && $password != NULL){
		//include the class and create a connection
		include (dirname(__FILE__) . "/adLDAP/adLDAP.php");
        try {
		    $adldap = new adLDAP();
        }
        catch (adLDAPException $e) {
            echo $e; 
            exit();   
        }
		
		//authenticate the user
		if ($adldap->authenticate($username, $password)){
			//establish your session and redirect
			session_start();
			$_SESSION["username"] = $username;
            $_SESSION["userinfo"] = $adldap->user()->info($username);
			$_SESSION["lastactivity"]=time();
			$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . "/estimating/home.php";
			header($redir);
			exit;
		}
	}
	$failed = 1;
}



?>

	<head>
	<?php
		include "header.php";
	?>
	<link rel="stylesheet" href="css/index.css">
		<title>Durex Quote & Estimating</title>	
	</head>
	<body class="text-center">
<?php	

?>
		<form class="form-signin" method='post' action='<?php echo $_SERVER["PHP_SELF"]; ?>'>
			<input type='hidden' name='oldform' value='1'>
			<img class="mb-4" src="/images/DurexIndcrm.jpg" alt="">
			<h1>Estimating</h1>
			<?php if ($failed){ echo ("<div style='color:red; font-size:20px;font-weight:700;'>Login Failed!</div>\n"); } ?>
			<?php if ($logout=="yes") { echo ("<div style='color:green; font-size:18px;font-weight:700;'>You have successfully logged out.</div>"); } ?>
			<h2 class="h3 mb-3 font-weight-normal">Please sign in</h1>
			<label for="username" class="sr-only">User Name</label>
			  <input type="text" id="username" name="username" class="form-control" placeholder="User Name" value="<?php if ($username!='' && !$failed) { print $username; } ?>" required autofocus>
			  <label for="password" class="sr-only">Password</label>
			  <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>

			  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		</form>

	</body>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
