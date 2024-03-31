<?php
	session_start();
?>

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
		
		//Retrieving the boolean php session variable value which indicates whether the database has been loaded
		let dbLoadedPHP = document.getElementById("dbLoadedPHP").value;
		
		//Array for storing all retrieved database information
		let dbInfoArr = [];
		
		//Database will only be loaded if it has not previously been loaded during the current browser session
		if (dbLoadedPHP) {
			//Do nothing -- database has already been loaded during the current browser session
		}
		else {
			/*
			Each time the get() method is called, the "await" keyword must be used in order to force the storing variable to wait until the object is loaded.
			The get() method is asynchronous, meaning that if the "await" keyword is not used, it will return an object Promise rather than the desired data
			(an object Promise is essentially a reference to an object which as not yet loaded). This step is what allows the actual info returned to be 
			accessed throughout the program (the onValue method returns a value only usable within its scope).
			*/
			for (let i = 0; i < 8; i++) { //Retrieving all database data and storing it for later conversion to json format
				let company = (await get(ref(db, "internships/" + i + "/company"))).val();
				let name = (await get(ref(db, "internships/" + i + "/job name"))).val();
				let citizenship = (await get(ref(db, "internships/" + i + "/citizenship"))).val();
				let underclassman = (await get(ref(db, "internships/" + i + "/underclassman"))).val();
				let location = (await get(ref(db, "internships/" + i + "/location"))).val();
				let link = (await get(ref(db, "internships/" + i + "/link"))).val();
				
				dbInfoArr[i]= {};
				dbInfoArr[i]["company"] = company;
				dbInfoArr[i]["name"] = name;
				dbInfoArr[i]["INTL"] = citizenship;
				dbInfoArr[i]["UCLASS"] = underclassman;
				dbInfoArr[i]['location'] = location;
				dbInfoArr[i]['link'] = link;
			}
			
			//Converting the database info array to json format so that it may be properly parsed and displayed later
			let sendjson = JSON.stringify(dbInfoArr);
			document.getElementById("postsendDB").value = (sendjson);
			
			//Submit form containing json format database data if the database has not been loaded during the current browser session
			document.getElementById("dbLoad").submit();
		}
	</script>
	
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
				Form used to send pulled database info to the server to be later accessed + displayed by php.
				
				In the main javascript section, document.getElementById("postsendDB").value = (sendjson); is used to write the retrieved data into a
				hidden input field. Once the form is submitted (via document.getElementById("dbLoad").submit()), the php code below is able to access
				the info sent by the form, which is stored in the $_POST variable.
			-->
			<form method="post" action="internshipDB.php" id="dbLoad">
				<input type="hidden" name="postsendDB" id="postsendDB">
			</form>
			<?php
				//Unsetting all filter variables if "Clear Filters" button is pressed
				if (isset($_POST['filterCLEARALL'])) {
					unset($_POST['filterINTL']);
					unset($_POST['filterUCLASS']);
				}
				
				//Stores whether the internship database has been loaded during a given session (used for displaying filters)
				if (isset($_POST['postsendDB'])) {
					$_SESSION['dbLoaded'] = true;
				}
				
				/*
				Form with a hidden input field which stores whether the internship database has been loaded during a given session. This field's value is
				read by javascript and used to determine whether the database should be loaded or not (javascript's session storage expires once a
				particular tab is closed, while phps session variables only expire once an entire browser window has been closed. This method allows use
				of javascript's suboptimal session storage to be avoided and prevents loading of the database more times than necessary)
				*/
				$dbLoadedPHP = $_SESSION['dbLoaded'];
				echo <<< MULTILINE
					<form>
						<input type='hidden' id='dbLoadedPHP' value=$dbLoadedPHP>
					</form>
				MULTILINE;
				
				//Creating filter selection form (the form used to apply any filters)
				if (isset($_SESSION['dbLoaded'])) { //Filter selection form is only displayed if database has been loaded
					echo "<section id='dbContainer'>";
					echo "<form method='post' action='internshipDB.php' id='dbFilters'>";
					
					//Determining which filters should be pre-selected when the page reloads based on which filters are currently applied
					if (isset($_POST['filterINTL'])) { //Open to International Students filter
						echo "<input type='checkbox' name='filterINTL' id='filterINTL' checked>";
					}
					else {
						echo "<input type='checkbox' name='filterINTL' id='filterINTL'>";
					}
					echo "<label for='filterINTL'>Open to International Students</label>";
					
					if (isset($_POST['filterUCLASS'])) { //Open to Underclassmen filter
						echo "<input type='checkbox' name='filterUCLASS' id='filterUCLASS' checked>";
					}
					else {
						echo "<input type='checkbox' name='filterUCLASS' id='filterUCLASS'>";
					}
					echo "<label for='filterUCLASS'>Open to Underclassmen</label>";
					
					//Finish creating filter selection form
					echo <<< MULTILINE
						<input type='submit' value='Apply Filters'>
						<input type='submit' value='Clear Filters' name='filterCLEARALL'>
						</form>
						<div id='filtersBottomBG'></div>
					MULTILINE;
				}
				else { //If the database has not been loaded during the current browser session, a loading indicator is displayed
					echo "<p id='dbPageLoading'>Loading...</p>";
				}
			
				//Sorting internships alphabetically by company name
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
				
				//Creating an array that contains the number of filters each internship satisfies.
				function createFAN(&$fan, $ref, &$fsFlag) {
					//Initializing FAN array to be all zeros
					for ($i = 0; $i < sizeof($ref); $i++) {
						array_push($fan, 0);
					}
					
					//Populating FAN array with each internship's associated FAN based upon filters currently applied
					if (isset($_POST['filterINTL'])) { //Open to International Students filter
						$fsFlag = true;
						for ($i = 0; $i < sizeof($ref); $i++) {
							if ($ref[$i]['INTL']) {
								$fan[$i] = $fan[$i] + 1;
							}
						}
					}
					if (isset($_POST['filterUCLASS'])) { //Open to Underclassmen filter
						$fsFlag = true;
						for ($i = 0; $i < sizeof($ref); $i++) {
							if ($ref[$i]['UCLASS']) {
								$fan[$i]++;
							}
						}
					}
				}
				
				//Sorting FAN array in descending order and database info array concurrently
				function sortFAN (&$fan, &$displayData) {
					
					//Performing bubble sort on the FAN and database data arrays simultaneously
					//Following this, $displayData will contain database data properly ordered for display
					for ($i = 0; $i < sizeof($fan); $i++) {
						for ($j = 0; $j < (sizeof($fan) - $i); $j++) {
							if ($fan[$j] < $fan[$j + 1]) {
								
								//Swapping elements in FAN array
								$temp = $fan[$j];
								$fan[$j] = $fan[$j + 1];
								$fan[$j + 1] = $temp;
								
								//Swapping elements in database data array
								$temp = $displayData[$j];
								$displayData[$j] = $displayData[$j + 1];
								$displayData[$j + 1] = $temp;
							}
						}
					}
				}
				
				/*************************************************************************************************************/
				
				$_SESSION['alphabetical']; //Session variable for alphabetically-sorted database data is currently unset
				
				//Receiving and decoding database data
				if (isset($_POST['postsendDB'])) {
					$receiveJson = $_POST['postsendDB'];
					$decode = json_decode($receiveJson, true); //Value "true" decodes received data as an associative array
					
					//Sorting received data alphabetically and storing in a session variable
					sortAlpha($decode);
					$_SESSION['alphabetical'] = $decode;
				}
				
				//Formatting and displaying database data
				if (isset($_SESSION['alphabetical'])) {
					
					//Assigning each internship a filter attribute number (FAN)
					$filterAttributeNumbers = array();
					$displayData = $_SESSION['alphabetical']; //$displayData will contain all database data formatted for display
					$filterSetFlag = false; //True if any filter is applied, false if not. Used for displaying data
					createFAN($filterAttributeNumbers, $displayData, $filterSetFlag);
					
					//Sorting FAN and database data arrays at the same time so that the database data array will be properly formatted for output
					//(internships will be listed in descending order according to each of their FANs)
					if ($filterSetFlag) { //Sorting of internships by FAN will not occur if no filters are currently selected (all FANs are 0)
						sortFAN($filterAttributeNumbers, $displayData);
					}
					
					//Displaying data
					if ($filterSetFlag) { //Displaying data when a filter is set (internships with FANs of 0 are not displayed)
						for ($i = 0; $i < sizeof($displayData); $i++) {
							$com = $displayData[$i]['company'];
							$nam = $displayData[$i]['name'];
							$loc = $displayData[$i]['location'];
							$lnk = $displayData[$i]['link'];
							$fan = $filterAttributeNumbers[$i]; //Required for displaying only desired internships
							
							if ($fan > 0) {
								echo <<< MULTILINE
									<table class='dbTable'>
										<tr>
											<td colspan='2'><h3>$com</h3></td>
										</tr>
										<tr>
											<td colspan='2'><a href='$lnk' target='_blank' rel='noreferrer noopener'>$com</a></td>
										</tr>
										<tr>
											<td><b>Position:</b> $nam</td>
											<td><b>Location:</b> $loc</td>
										</tr>
									</table>
								MULTILINE;
							}
						}
					}
					else { //Displaying data when no filters are set
						for ($i = 0; $i < sizeof($displayData); $i++) {
							$com = $displayData[$i]['company'];
							$nam = $displayData[$i]['name'];
							$loc = $displayData[$i]['location'];
							$lnk = $displayData[$i]['link'];
							
							echo <<< MULTILINE
								<table class='dbTable'>
									<tr>
										<td colspan='2'><h3>$com</h3></td>
									</tr>
									<tr>
										<td colspan='2'><a href='$lnk' target='_blank' rel='noreferrer noopener'>$com</a></td>
									</tr>
									<tr>
										<td><b>Position:</b> $nam</td>
										<td><b>Location:</b> $loc</td>
									</tr>
								</table>
							MULTILINE;
						}
					}
					
					echo "</section>";
				}
			?>
			<h2>Filters</h2>
			<h3>International Student Filter</h3>
		</section>
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, <small>and</small> Owen Murphy <small>with help from</small> James Calder, Adi Shah,
			<small>and</small> Paige Su &mdash; 2024.
		</footer>
	</main>
</body>
</html>