<?php
	
	session_start();
	/*
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	When page loads, check if the user has logged in or signed up (in this particular case, the login or signup forms can only be submitted if all the
	entered info is correct, so whether the $_POST variable corresponding to the form submit button is checked).
	
	If the form has been submitted (user has successfully logged in/signed up), a session variable indicating that the user has logged in is set to true.
	This variable is later used to load the user's profile page rather than the login/signup pages.
	
	Additionally, a "nameDisplay" session variable is set which allows the user's name to be easily displayed in the profile page's header.
	*/
	if(isset($_POST['signUpSubmit']) or isset($_POST['logInSubmit'])) {
		$_SESSION['loggedIn'] = true;
		if(isset($_POST['signUpSubmit'])) {
			$_SESSION['nameDisplay'] = $_POST['nameSU'];
			$_SESSION['username'] = $_POST['usernameSU'];
		}
		else if(isset($_POST['logInSubmit'])) {
			$_SESSION['nameDisplay'] = $_POST['nameLI'];
			$_SESSION['username'] = $_POST['usernameLI'];
		}
	}
	
	//$historyLoaded is used to ensure that a user's history table is only read once per page load, but it is not a session variable since the history
	//table must be re-read from the database each time the page is loaded in case any new internships were saved
	$historyLoaded = false;
	if (isset($_POST['sendHistory'])) {
		$historyLoaded = true;
	}
	
	//Whether or not the history table has been read is written to a form for access by javascript
	echo "<input type='hidden' id='historyLoaded' value=$historyLoaded>";
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<style>
		<?php include 'styles.css' ?>
	</style>
	<title>Internship Database - Profile</title>
</head>

<body>
	<!-- divs with classes headerTopBG and headerBottomBorder are required to allow each page's header and its border to look as they did in design 
	models apporoved by end users -->
	<div class="headerTopBG"></div>
	<header>
		<!-- User's username will be retrieved and displayed in header if the user has logged in -->
		<?php
			if ($_SESSION['loggedIn']) {
				$nameDisplay = $_SESSION['nameDisplay'];
				echo "<h1>Profile - $nameDisplay</h1>";
			}
			else { //A default profile page header will be displayed if the user has not logged in
				echo "<h1>Profile</h1>";
			}
			
			//Displaying a "Log Out" button in the event that a user has logged in
			if ($_SESSION['loggedIn']) {
				echo '<a href="logout.php" class="logout">Log Out</a>';
			}
		?>
		<a href="userProfile.php"><img src="images/profilePageIcon.png" class="profIcon"></img></a>
		<nav id="mainNav">
			<a href="index.php">Home</a>
			<a href="internshipDB.php">Internship Database</a>
			<a href="pastInternships.php">Past Successes</a>
			<a href="REUTab.php">REU Information</a>
		</nav>
	</header>
	<div class="headerBottomBorder"></div>
	
	<main>
		<section class="pageContentMain">
		<?php
			//Info in session variables which indicate whether a user has logged in and what username has been used to log in are written into hidden
			//input fields to be access by javascript
			$loggedInFlag = $_SESSION['loggedIn'];
			$usernameToAccess = $_SESSION['username'];
			
			echo "<input type='hidden' id='loggedInFlag' value=$loggedInFlag>";
			echo "<input type='hidden' id='usernameToAccess' value=$usernameToAccess>";
			
			//Sorting saved internships alphabetically by company name
			function sortAlpha(&$unsorted) {
				for ($i = 0; $i < sizeof($unsorted) - 1; $i++) {
					for ($j = $i + 1; $j < sizeof($unsorted); $j++) {
						if (strcmp(strtolower($unsorted[$i]['company']), strtolower($unsorted[$j]['company'])) > 0) {
							$temp = $unsorted[$i];
							$unsorted[$i] = $unsorted[$j];
							$unsorted[$j] = $temp;
						}
					}
				}
			}
			
			//Building a more presentable "date posted" string based on pre-formatted date posted data stored in each internship's "posted" field
			function dateDisplay(&$rawDate) {
				$stringbuilderDate = ""; //Will contain the date formatted for nicer display
				$rawMonth = substr($rawDate, 5, 2); //Retrieving the month an internship was posted
				
				//Converting a numeric month into the corresponding month's name
				if ($rawMonth == "01")
					$stringbuilderDate = "January";
				else if ($rawMonth == "02")
					$stringbuilderDate = "February";
				else if ($rawMonth == "03")
					$stringbuilderDate = "March";
				else if ($rawMonth == "04")
					$stringbuilderDate = "April";
				else if ($rawMonth == "05")
					$stringbuilderDate = "May";
				else if ($rawMonth == "06")
					$stringbuilderDate = "June";
				else if ($rawMonth == "07")
					$stringbuilderDate = "July";
				else if ($rawMonth == "08")
					$stringbuilderDate = "August";
				else if ($rawMonth == "09")
					$stringbuilderDate = "September";
				else if ($rawMonth == "10")
					$stringbuilderDate = "October";
				else if ($rawMonth == "11")
					$stringbuilderDate = "November";
				else
					$stringbuilderDate = "December";
				
				//Retrieving and formatting remaining day and year data
				$rawDay = substr($rawDate, 8, 2);
				
				//Changing the suffix to be displayed behind the day depending on the day number contained in the date
				//For example, days ending in 1 (1, 11, 21, 31) should be displayed with an -st suffix and not the generic -th suffix
				$daySuffix = "th";
				if (substr($rawDay, 1, 1) == "1")
					$daySuffix = "st";
				else if (substr($rawDay, 1, 1) == "2")
					$daySuffix = "nd";
				else if (substr($rawDay, 1, 1) == "3")
					$daySuffix = "rd";
				
				if (substr($rawDay, 0, 1) == "0") { //Removing the leading "0" from a date's day if it is present
					$rawDay = substr($rawDay, 1, 1);
				}
				
				$rawYear = substr($rawDate, 0, 4);
				
				//Building date formatted for display
				$rawDate = $stringbuilderDate . " " . $rawDay . $daySuffix . ", " . $rawYear;
			}

//RETRIEVING AND SORTING DATA
/*****************************************************************************************************************************/

			//If the user has logged in, read in the user's history from the database and generate the user's profile page
			if ($_SESSION['loggedIn']) {
				//If the user has logged in, this form will store the user's history information
				echo <<< MULTILINE
				<form method="post" action="userProfile.php" id="historyLoad">
					<input type="hidden" name="sendHistory" id="sendHistory">
				</form>
				MULTILINE;
				
				//Receiving and decoding user's history data each time the page is loaded
				if (isset($_POST['sendHistory'])) {
					$receiveJson = $_POST['sendHistory'];
					$decode = json_decode($receiveJson, true); //Value "true" decodes received data as an associative array
					sortAlpha($decode); //Sorting saved internships alphabetically
					
					//HISTORYALPHABETICAL stores a user's alphabetically-sorted history data and is defined as a constant since it should not be altered
					//and must be able to be easily accessed throughout the entire scope of the program
					define("HISTORYALPHABETICAL", $decode);
					
					$displayHistory = HISTORYALPHABETICAL;
					
					//Writing number of saved internships to a form to be accessed by javascript (required for internship removal)
					$historySize = sizeof($displayHistory);
					echo "<input type='hidden' value=$historySize id='historySize'>";
					
					if ($historySize == 0) { //Displaying a special message if no internships have been saved
						$nameDisplay = $_SESSION['nameDisplay'];
						echo <<< MULTILINE
							<h2>Seems Quiet Here...</h2>
							<p>Hello, $nameDisplay, this is your profile page! It looks a bit empty at the moment, but once you've bookmarked some 
							internships, they'll show up here. You can get started by navigating to our 
							<a href="internshipDB.php">Internship Database</a> page and bookmaring some internships you find interesting using the 
							"Save Internship" button in the top-right of each listing!</p>
						MULTILINE;
					}
					else { //Displaying saved internships if any are present
						echo "<section id='dbContainer'>";
						for ($i = 0; $i < sizeof($displayHistory); $i++) {
							$com = $displayHistory[$i]["company"];
							$nam = $displayHistory[$i]["name"];
							$lnk = $displayHistory[$i]["link"];
							$loc = $displayHistory[$i]["location"];
							$pay = $displayHistory[$i]["pay"];
							$ptd = $displayHistory[$i]["posted"];
							
							echo "<table class=dbTable>";
							
							echo <<< MULTILINE
								<tr>
									<td colspan='2'><h3>$com<span class='internshipPosition'> &mdash; $nam</span></h3></td>
									<td>
							MULTILINE;
							
							//Creating form for internship removal
							$idForm = ''.$i + 1;
							
							$idCompany = 'com'.$i + 1;
							$valueCompany = "$com";
							
							$idName = 'nam'.$i + 1;
							$valueName = "$nam";
							
							$idLocation = 'loc'.$i + 1;
							$valueLocation = "$loc";
							
							$idLink = 'lnk'.$i + 1;
							$valueLink = "$lnk";
							
							$idPay = 'pay'.$i + 1;
							$valuePay = "$pay";
							
							$idPosted = 'ptd'.$i + 1;
							$valuePosted = "$ptd";
							
							echo <<< MULTILINE
								<section class='bookmarkButtonContainer'>
									<form action='userProfile.php' method='post' id='$idForm'>
										<input type='hidden' name='company' id='$idCompany' value="$valueCompany">
										<input type='hidden' name='company' id='$idName' value="$valueName">
										<input type='hidden' name='company' id='$idLocation' value="$valueLocation">
										<input type='hidden' name='company' id='$idLink' value="$valueLink">
										<input type='hidden' name='company' id='$idPay' value="$valuePay">
										<input type='hidden' name='company' id='$idPosted' value="$valuePosted">
										<input type='submit' value='Remove'>
									</section></form></td>
							MULTILINE;
							
							echo <<< MULTILINE
								</td></tr>
								<tr>
									<td class='linkRow' colspan='3'><a href='$lnk' target='_blank' rel='noreferrer noopener'>$com</a></td>
								</tr>
								<tr>
									<td><b>Location:</b> $loc</td>
							MULTILINE;
							
							//Displaying pay rate and date posted information if it is available
							$extraCellDisplay = 0; //Required to ensure the correct number of table cells are dispalyed inline
							
							if ($pay != "0") {
								echo "<td class='locationPayDateInline'><b>Pay:</b> $$pay</td>";
							}
							else {
								$extraCellDisplay++;
							}
							
							if (strlen($ptd) != 0) {
								dateDisplay($ptd); //Formatting date for nicer display
								echo "<td class='locationPayDateInline'><b>Date Posted:</b> $ptd</td>";
							}
							else {
								$extraCellDisplay++;
							}
							
							//Displays any extra needed table cells inline with location/pay/date posted information
							while($extraCellDisplay != 0) {
								echo "<td></td>";
								$extraCellDisplay--;
							}
							
							echo "</tr></table>";
						}
						echo "</section>";
					}
				}
				else { //Display a loading graphic if a user's history data has not yet been read
					echo "<img src='images/loadingGraphic.gif' height='150px' width='150px'>";
				}
			}
			else { //If the user has not logged in, display either the signup or login page depending on which submit button in the below form is selected
				if (isset($_COOKIE["loggedOut"])) {
					echo <<< MULTILINE
						<script>
							alert("Logged out successfully!");
						</script>
					MULTILINE;
				}
				if (isset($_POST['loadPageSignUp']) or (!isset($_POST['loadPageSignUp']) and !isset($_POST['loadPageLogIn']))) {
					$signupid = "activeBox";
					$loginid = "inactiveBox";
				}
				else {
					$loginid = "activeBox";
					$signupid = "inactiveBox";
				}
				echo <<< MULTILINE
					<form method='post' id='loginBox' action='userProfile.php'>
						<input type='submit' id='$signupid' name='loadPageSignUp' value='Sign Up Here'>
						<input type='submit' id='$loginid' name='loadPageLogIn' value='Log In Here'>
					</form>
				MULTILINE;

				//Building signup form
				echo <<< MULTILINE
						<form method='post' class='dbSubmitContainer' action='userProfile.php' id='signUp'>
							<input type='hidden' id='formLoaded' value='SU'>
							<input type='text' name='nameSU' id='nameSU' placeholder='Name'>
							<input type='text' name='usernameSU' id='usernameSU' placeholder='Username'>
							<input type='password' name='passwordSU' id= 'passwordSU' placeholder='Password'>
							<input type='submit' name='signUpSubmit' value='Sign Up' form='signUp'>
						</form>
					MULTILINE;

				//Building login form
				echo <<< MULTILINE
						<form method='post' class='dbSubmitContainer' action='userProfile.php' id='logIn'>
							<input type='text' name='usernameLI' id='usernameLI' placeholder='Username'>
							<input type='password' name='passwordLI' id= 'passwordLI' placeholder='Password'>
							<input type='hidden' name='nameLI' id='nameLI'>
							<input type='submit' name='logInSubmit' value='Log In' form='logIn'>
						</form>
					MULTILINE;
				
				if (isset($_POST['loadPageSignUp']) or (!isset($_POST['loadPageSignUp']) and !isset($_POST['loadPageLogIn']))) {
					//Building the signup form which will be validated with javascript later
					echo <<< MULTILINE
						<script>
							document.getElementById('signUp').style.display='block';
							document.getElementById('logIn').style.display='none';
						</script>
						MULTILINE;
					}
				
				if (isset($_POST['loadPageLogIn'])) {
					//Building the login form which will be validated by javascript later
					echo <<< MULTILINE
						<script>
							document.getElementById('signUp').style.display='none';
							document.getElementById('logIn').style.display='block';
						</script>
						MULTILINE;
				}
			}
		?>
		
		<script type="module">
			//Importing needed methods and SDKs
			import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
			import { getDatabase, ref, set, get, remove, onValue } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-database.js";
			
			//Storing our Firebase configuration info
			const firebaseConfig = {
				apiKey: "AIzaSyCEs4fuVQHi2dwqnV6TJHSO1fZ6qx6kXc8",
				authDomain: "se-internship-database.firebaseapp.com",
				databaseURL: "https://se-internship-database-default-rtdb.firebaseio.com/",
				projectId: "se-internship-database",
				storageBucket: "se-internship-database.appspot.com",
				messagingSenderId: "520655988080",
				appId: "1:520655988080:web:573c2f7436e2acddf89bb5"
			};
					
			//Initializing Firebase
			const app = initializeApp(firebaseConfig);
			const db = getDatabase(app);
			
			//Getting users table reference
			const snapshot = await get(ref(db, "users"));
			
			/*
			When a user attempts to sign up or log in, this function will compare the entered username to all other usernames stored in the database.
			
			Returns true if the entered username matches an existing username in the database (username is unavailable when signing up/username is
			correct whenn logging in), returns false otherwise (username is available when signing up/username is incorrect when logging in).
			*/
			function determineMatch (userEntered) {
				let flag = false;
				
				snapshot.forEach(function(childSnapshot) {
					let userCompare = childSnapshot.child("username").val();
					if (userEntered == userCompare) {
						flag = true;
					}
				});
				
				return flag;
			}

//RETRIEVING HISTORY
/*****************************************************************************************************************************/

			//Array for storing all retrieved history information
			let historyArr = [];
			let historyArrIndex = 0;
			
			//Only reading in history information if it has not already been read while on the current page (NOT SESSION)
			let historyLoaded = document.getElementById("historyLoaded").value;
			
			//Only reading in history information if a user has logged in
			let loggedInFlag = document.getElementById("loggedInFlag").value;
			
			if (!historyLoaded) {
				if (loggedInFlag) {
					//Accessing a user's history by retrieving their username to get the path to their history table in the database
					const usernameToAccess = document.getElementById("usernameToAccess").value;
					const historySnap = await get(ref(db, "users/" + usernameToAccess + "/history"));
					
					//Using history table reference to retrieve the value stored in each field of each item in the table. Then, the value in each field
					//is written as a key-value pair to an object in an array which will be converted to json format to be displayed.
					historySnap.forEach(function(childHistorySnap) {
						let company = childHistorySnap.child("company").val();
						let name = childHistorySnap.child("job_name").val();
						let link = childHistorySnap.child("link").val();
						let location = childHistorySnap.child("location").val();
						let pay = childHistorySnap.child("pay").val();
						let posted = childHistorySnap.child("date_posted").val();
						
						historyArr[historyArrIndex] = {};
						historyArr[historyArrIndex]["company"] = company;
						historyArr[historyArrIndex]["name"] = name;
						historyArr[historyArrIndex]["link"] = link;
						historyArr[historyArrIndex]["location"] = location;
						historyArr[historyArrIndex]["pay"] = pay;
						historyArr[historyArrIndex]["posted"] = posted;
						historyArrIndex++;
					});
					
					//Converting user's history table info to json format so that it may be properly parsed and displayed later
					let sendjson = JSON.stringify(historyArr);
					document.getElementById("sendHistory").value = sendjson;
					document.getElementById("historyLoad").submit();
				}
			}
//REMOVING SAVED INTERNSHIPS
/*****************************************************************************************************************************/
			else {
				if (loggedInFlag) { //Functionality is only present if history has loaded and user has logged in
					let formsArr = [];
					let historySize = document.getElementById("historySize").value;
					
					for (let i = 1; i <= historySize; i++) {
						formsArr[i - 1] = (document.getElementById("" + i));
						formsArr[i - 1].addEventListener("submit", function (event) {
							let nam = document.getElementById("nam" + i).value;
							let unameTag = "<?php echo $_SESSION['username'] ?>";
							
							//Sanitizing internship name for use as part of paths in our database
							let pathName = nam.replace(/[^a-zA-Z0-9]/g, '');
							
							//Removing all data stored under a certain sanitized internship name (key)
							let removeRef = ref(db, "users/" + unameTag + "/history/" + pathName);
							remove(removeRef);
							
							//No preventDefault(), page should reload when form is submitted to show changes applied by the "Remove" button
						});
					}
				}
			}
			
//SIGNUP AND LOGIN FUNCTIONALITY
/*****************************************************************************************************************************/

			//Getting a reference to the signup form (built earlier in php) and assigning it an event listener which listens the "form submitted" event
			let signInForm = document.getElementById("signUp");
			signInForm.addEventListener("submit", function (event) { //When the signup form is submitted, check entered username availability
				let usernameSU = document.getElementById("usernameSU").value;
				let passwordSU = document.getElementById("passwordSU").value;
				let nameSU = document.getElementById("nameSU").value;

				//Ensuring that the user does not leave the username or password fields blank
				if (usernameSU === "") {
					alert("Error: Please input a username.");
					event.preventDefault();
				}
				else if (nameSU === "") {
					alert("Error: Please input a name.");
					event.preventDefault();
				}
				else if (passwordSU === "") {
					alert("Error: Please input a password.");
					event.preventDefault();
				}
				else {
					//Calling the determineMatch function to check if the user-entered username is available
					let matchFlagSU = determineMatch(usernameSU);
					if (matchFlagSU) {
						alert("That username is already in use!");
						event.preventDefault();
					}
					else {
						set(ref(db, 'users/'+ usernameSU), {
							username: usernameSU,
							password: passwordSU,
							name_of_user: nameSU,
							history: "null",
						});
					}
				}
			});
	
			//Getting a reference to the login form (built earlier in php) and assigning it an event listener which listens the "form submitted" event
			let logInForm = document.getElementById("logIn");
			logInForm.addEventListener("submit", function (event) {
				let userLI = document.getElementById("usernameLI").value;
				let passwordLI = document.getElementById("passwordLI").value;
				let passFlagLI = false;
				
				//Checks whether the entered username exists in the database (matches any currently stored username)
				let UNameFlagLI = determineMatch(userLI);
				
				//If the entered username does not exist in the database, alert the user that their entered username is incorrect
				if (!UNameFlagLI) {
					alert("Incorrect username.");
					event.preventDefault();
				}
				
				let dbEntry = snapshot.child(userLI);
				
				let dbUname = dbEntry.child("username").val();
				let dbPass = dbEntry.child("password").val();
				let dbName = dbEntry.child("name_of_user").val();
				
				//If the entered username exists, check that the password stored and the password entered match
				if (dbPass === passwordLI) {
					document.getElementById("nameLI").value = dbName;
				}
				else { //Prevent login form submission if the entered password is invalid
					alert("Incorrect password.");
					event.preventDefault();
				}
			});
		</script>
		</section>
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, <small>and</small> Owen Murphy <small>with help from</small> James Calder, Adi Shah,
			<small>and</small> Paige Su &mdash; 2024.
		</footer>
	</main>
</body>
</html>