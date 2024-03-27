<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<style>
		<?php include 'styles.css' ?>
	</style>
	<title>Internship Database - Database</title>
</head>

<body>
	<!-- divs with classes headerTopBG and headerBottomBorder are required to allow each page's header and its border to look as they did in design 
	models apporoved by end users -->
	<div class="headerTopBG"></div>
	<header>
		<h1>Internship Database</h1>
		<a href="userProfile.php"><img src="images/profilePageIcon.png" class="profIcon"></img></a>
		<nav id="mainNav">
			<a href="index.php">Home</a>
			<a href="internshipDB.php" class="currentPage">Internship Database</a>
			<a href="pastInternships.php">Companies and Programs</a>
			<a href="REUTab.php">REUs</a>
		</nav>
	</header>
	<div class="headerBottomBorder"></div>
	
	<main>
		<section class="pageContentMain">
			<h2>Internships that Fit You</h2>
			<p>Our internship database contains a large variety of computer science internships and our website provides you many filters which we
			hope will allow you to effectively search for and find the internships most interesting to you and most applicable to your skill set.
			These filters will help you greatly refine your search based on whether internships are open to certain groups of students, where
			internships are located, whether internships are remote or in person, and various other critera. Each of our filters is explained in-depth 
			later in this page.</p>
			<h2>Internship Database</h2>
			
			<!-- 
				Form used to send pulled database info to the server to be accessed + displayed by php.
				
				In the javascript section, document.getElementById("postsendDB").value = (strTest); is used to write the retrieved data into the hidden
				input field. Once the form is submitted (using POST method so users can't see any extra info in the url), the php code below accesses
				the info sent by the form, which is stored in the $_POST variable.
			-->
			<form method="post" action="internshipDB.php">
				<input type="hidden" name="postsendDB" id="postsendDB">
				<input type="submit" value="Load Database!">
			</form>
			<?php
				//EXAMPLE TO SHOW HOW ORDERING ALG WILL WORK
				//Testing arrays
				/*
				$companyarr = array(
					array("company" => "a", "title" => "internA", "INTL" => true),
					array("company" => "b", "title" => "internB", "INTL" => true),
					array("company" => "c", "title" => "internC", "INTL" => false),
				);
				
				//Calculating filter attribute values
				$valuesarr = array();
				for ($subarr = 0; $subarr < sizeof($companyarr); $subarr++) {
					array_push($valuesarr, 0);
					if ($companyarr[$subarr]['INTL']) {
						$valuesarr[$subarr]++;
					}
				}
				
				//$valuesarr must be bubble-sorted here
				
				//Display test for php array
				for ($subarr = 0; $subarr < sizeof($companyarr); $subarr++) {
					if ($valuesarr[$subarr] > 0) {
						echo "<p>Company: ".$companyarr[$subarr]['company']."</p>";
						echo "<p>Title: ".$companyarr[$subarr]['title']."</p>";	
					}
				}
				*/
				
				//EXAMPLE TO SHOW FORM-SENT JSON DISPLAY
				/*
				if (isset($_POST['postsend'])) {
					$jsontemp = $_POST['postsend'];
					
					$decoded = json_decode($jsontemp, true);
					for ($i = 0; $i < 2; $i++) {
						echo "<p>Name: ".$decoded[$i]['name']."</p>";
						echo "<p>Email: ".$decoded[$i]['email']."</p>";
					}
				}
				*/
				
				//IGNORE THE COMMENTED OUT STUFF ABOVE ^^^
				//EXAMPLE TO SHOW FORM-SENT DATABASE DATA (NOT JSON FORMATTED -- JUST TESTING FOR RIGHT NOW)
				if (isset($_POST['postsendDB'])) {
					$displayDataDB = $_POST['postsendDB'];
					echo "<p>".$displayDataDB."</p>";
				}
			?>
			
			<h2>Filters</h2>
			<h3>International Student Filter</h3>
		</section>
		
		<script type="module">
			  // Import the functions you need from the SDKs you need
				import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
				import { getDatabase, ref, set, get, onValue } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-database.js";
				// Your web app's Firebase configuration
				const firebaseConfig = {
					apiKey: "AIzaSyCEs4fuVQHi2dwqnV6TJHSO1fZ6qx6kXc8",
					authDomain: "se-internship-database.firebaseapp.com",
					databaseURL: "https://se-internship-database-default-rtdb.firebaseio.com/",
					projectId: "se-internship-database",
					storageBucket: "se-internship-database.appspot.com",
					messagingSenderId: "520655988080",
					appId: "1:520655988080:web:573c2f7436e2acddf89bb5"
				};
				
				// Initialize Firebase
  				const app = initializeApp(firebaseConfig);
  				const db = getDatabase(app);
				//const bookRef = ref(db, "book/Book1/Author");
				
				/*
				EXPLANATION:
				
				The get() function returns an object Promise (which indicates that an object will be sent once the thing that provides it -- in this case
				firebase db -- has loaded). In order to get the object being queried and not just "[object Promise]," an asynchronous function must be
				used which returns a reference back to that Promise.
				*/
				async function getTitle() {
					const titleSnap = await get(ref(db, "book/Book1/Title"));
					return titleSnap.val();
				}
				
				/*
				EXPLANATION:
				
				When the get() function (encapsulated in the getTitle() funciton) is called, the "await" keyword must be used in order to force the
				storing variable to wait until the object is loaded. THIS step is what allows the actual info returned to be accessed throughout the
				program.
				*/
				let respTest = await getTitle();
				
				//Some testing to ensure that the returned info can be appended to strings (required for sending the info in the format I want)
				let strTest = "Title (pulled from database): ";
				strTest += respTest + "!";
				
				//Writing the info pulled from the database into a form to be sent to php
				document.getElementById("postsendDB").value = strTest; //There it is baby!!!
				
				/*************************************************************/
				//Testing stuff -- disregard
				/*
				let infojson = JSON.stringify([
					{name: "Aidan", email: "Email"},
					{name: "Owen", email: "Omail"}
				]);
				document.getElementById("postsend").value = infojson;
				*/
		</script>
		
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, <small>and</small> Owen Murphy <small>with help from</small> James Calder, Adi Shah,
			<small>and</small> Paige Su &mdash; 2024.
		</footer>
	</main>
</body>
</html>