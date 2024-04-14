<?php
	session_start();
	/*
	When page loads, check if the user has logged in or signed up (in this particular case, the login or signup forms can only be submitted if all the
	entered info is correct, so whether the $_POST variable corresponding to the form submit button is checked).
	
	If the form has been submitted (user has successfully logged in/signed up), a session variable indicating that the user has logged in is set to true.
	This variable is later used to load the user's profile page rather than the login/signup pages.
	
	Additionally, a "usernameDisplay" session variable is set which allows the user's username to be easily displayed in the profile page's header.
	*/
	if(isset($_POST['signUpSubmit'])) {
		$_SESSION['loggedIn'] = true;
		$_SESSION['usernameDisplay'] = $_POST['usernameSU'];
		$_SESSION['nameDisplay'] = $_POST['nameSU'];
	}
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
				$usernameDisplay = $_SESSION['nameDisplay'];
				echo "<h1>Profile - $usernameDisplay</h1>";
			}
			else { //A default profile page header will be displayed if the user has not logged in
				echo "<h1>Profile</h1>";
			}
		?>
		<nav id="mainNav">
			<a href="index.php">Home</a>
			<a href="internshipDB.php">Internship Database</a>
			<a href="pastInternships.php">Companies and Programs</a>
			<a href="REUTab.php">REUs</a>
			<?php 
				if ($_SESSION['loggedIn']) {
					echo '<a href="logout.php">Logout</a>';
				}
			?>
		</nav>
	</header>
	<div class="headerBottomBorder"></div>
	<script type="module">
			//Importing needed methods and SDKs
			import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
			import { getDatabase, ref, set, get, onValue } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-database.js";
			
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
			When a user attempts to sign up for the first time, this function will compare the entered username to all other usernames stored in the
			database to ensure that the entered username is available (does not match an existing username).
			
			Returns true if the entered username is unavailable (matches a pre-existing username), returns false if the entered username is available.
			*/
			function determineMatch (userEntered) {
				let flag = false;
				
				snapshot.forEach(function(childSnapshot) {
					let userCompare = childSnapshot.child("username").val();
					if (userEntered ==  userCompare) {
						flag = true;
					}
				});
				
				return flag;
			}; 

			let historyArr = [];
				let historyIndex = 0;
				snapshot.forEach(function(childSnaphot) {
					alert ("insidefunc");
					let historyElement = childSnapshot.child('history');
					alert("this one worked (no it has not)");
					let company = historyElement.child("company").val();
					alert('and here');
					let name = childSnapshot.child("history").child("job_name").val();
					let location = childSnapshot.child("history").child("location").val();
					let link = childSnapshot.child("history").child("link").val();
					let pay = childSnapshot.child("history").child("pay").val();
					let posted = childSnapshot.child("history").child("date_posted").val();
					alert('and here');
					historyArr[historyIndex]= {};

					historyArr[historyIndex]["dbIndex"] = historyIndex;

					historyArr[historyIndex]["company"] = company;
					historyArr[historyIndex]["name"] = name;
					historyArr[historyIndex]['location'] = location;
					historyArr[historyIndex]['link'] = link;
					historyArr[historyIndex]['pay'] = pay;
					historyArr[historyIndex]['posted'] = posted;
					historyIndex++;
				});
				alert ("I see you");
				let sendjson = JSON.stringify(dbInfoArr);

				document.getElementById("sendHistory").value = (sendjson);
			
				document.getElementById("loadHistory").submit();
	
			
			//Flag variable that indicates whether the username a user entered when signing up is available
			let matchFlagSU = false;
			
			//Getting a reference to the signup form (built earlier in php) and assigning it an event listener which listens the "form submitted" event
			let signInForm = document.getElementById("signUp");
			signInForm.addEventListener("submit", function (event) { //When the signup form is submitted, check entered username availability
				let usernameSU = document.getElementById("usernameSU").value;
				
				//Calling the determineMatch function to check if the user-entered username is available
				matchFlagSU = determineMatch(usernameSU);
				if (matchFlagSU) { //If username is unavailable, send alert and prevent form from being submitted
					alert("Error: that username is already in use!");
					event.preventDefault();
				}
				else { //Otherwise, pull other fields from submitted form and write them to the database
					let nameSU = document.getElementById("nameSU").value;
					let passwordSU = document.getElementById("passwordSU").value;

					set(ref(db, "users/"+usernameSU), {
						username: usernameSU,
						password: passwordSU,
						name_of_user: nameSU,
					});
				}
			});
			
			//Syntax for writing to database
			/*
			set(ref(db, "users/testUser"), {
				username: "test",
				password: "test",
				name_of_user: "test_person",
			});
			*/
		</script>
	
	<main>
		<section class="pageContentMain">
		<p id="testlol"></p>
		<form method="post" action="userProfile.php" id="loadHistory">
			<input type="hidden" name="sendHistory" id="sendHistory">
		</form>
		<?php
			if ($_SESSION['loggedIn']) {
				//Load user profile page once the user has sucessfully signed up or logged in
				echo "<p>User has logged in!</p>";
				echo "done";

				$receiveJson = $_POST['sendHistory'];
				$displayHistory = json_decode($receiveJson, true);
				echo $displayHistory;
				echo sizeof($displayHistory);
				echo $displayHistory[1]['pay'];
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
		</section>
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, <small>and</small> Owen Murphy <small>with help from</small> James Calder, Adi Shah,
			<small>and</small> Paige Su &mdash; 2024.
		</footer>
	</main>
</body>
</html>