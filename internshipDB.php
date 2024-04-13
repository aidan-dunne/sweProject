<?php
	session_start();
	$currentPage;
	define("PERPAGE", 10); //Creating a constant for the amount of internships that may be displayed on a page
	define("NULLSEARCH", "");
	
	if(!isset($_POST['page'])) {
		$currentPage = 1;
	}
	else if(isset($_POST['next'])) {
		$currentPage = ++$_POST['page'];
	}
	else if(isset($_POST['previous'])) {
		$currentPage = --$_POST['page'];
	}

	$nameDisp = $_SESSION['nameDisplay'];
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
			//Retrieving a reference to our database's "internships" table
			const snapshot = await get(ref(db, "internships"));
			
			//Index variable used for building array of retrieved database information
			let dbArrIndex = 0;
			
			//Using "internships" database table reference to retrieve the value stored in each field of each item in the table. Then, the value in each
			//field is written as a key-value pair to an object in an array which will be converted to json format to be displayed.
			snapshot.forEach(function(childSnapshot) {
				let company = childSnapshot.child("company").val();
				let name = childSnapshot.child("job name").val();
				let citizenship = childSnapshot.child("citizenship").val();
				let underclassman = childSnapshot.child("underclassman").val();
				let location = childSnapshot.child("location").val();
				let link = childSnapshot.child("link").val();
				let pay = childSnapshot.child("pay").val();
				let remote = childSnapshot.child("remote").val();
				let posted = childSnapshot.child("date_posted").val();
				
				dbInfoArr[dbArrIndex]= {};

				// Database index needed for bookmarking system to allow buttons to recognize the specific databasae entry
				dbInfoArr[dbArrIndex]["dbIndex"] = dbArrIndex;

				dbInfoArr[dbArrIndex]["company"] = company;
				dbInfoArr[dbArrIndex]["name"] = name;
				dbInfoArr[dbArrIndex]["INTL"] = citizenship;
				dbInfoArr[dbArrIndex]["UCLASS"] = underclassman;
				dbInfoArr[dbArrIndex]['location'] = location;
				dbInfoArr[dbArrIndex]['link'] = link;
				dbInfoArr[dbArrIndex]['pay'] = pay;
				dbInfoArr[dbArrIndex]['RMT'] = remote;
				dbInfoArr[dbArrIndex]['posted'] = posted;
				dbArrIndex++;
			});
			
			//Converting the database info array to json format so that it may be properly parsed and displayed later and writing it into the hidden
			//input field of a form which will be submitted, allowing database information to be displayed with php.
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
		<?php 
			//Displaying a "Log Out" button in the event that a user has logged in
			if ($_SESSION['loggedIn']) {
				echo '<a href="logout.php" class="logout">Log Out</a>';
			}
		?>
		<nav id="mainNav">
			<a href="index.php">Home</a>
			<a href="internshipDB.php" class="currentPage">Internship Database</a>
			<a href="pastInternships.php">Past Successes</a>
			<a href="REUTab.php">REU Information</a>
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
				/*
				SEARCHING AND FILTERING WORKFLOW:
				
				ANY selected filters and any text entered in the database search field will be applied when the "Apply Filters" button is pressed or
				when the enter key is pressed within the database search field (since the "Apply Filters" submit button is the first listed in the
				filters form, this button is ALWAYS selected when enter is pressed). 
				
				ALL selected filters and text MUST be deselected/cleared when the "Clear Filters" button is pressed.
				
				The "I'm Feeling Lucky" button MUST ONLY be able to be selected when pressed (as previously noted, the enter key will NEVER select this
				button) and must display data which satisfies ALL selected filters and entered text (since the internships which will be displayed when
				the "I'm Feeling Lucky" button is pressed are determined AFTER internships which do not satisfy entered search text are removed from
				the display array and filter attribute number are calculated according to all applied filters every time the button is pressed, this
				will always be the case).
				
				ALL applied filters and search terms MUST be displayed when navigating through different database pages. Additionally, the page should
				be reset to page 1 each time a filter or search term is applied/cleared. This is accomplished through the use of hidden fields in the
				database page navigation form that share the same names as the filter input fields in the filter form. These hidden fields have values
				written to them based on what filters are applied and are automatically rewritten to and re-submitted when the next/previous page
				buttons are pressed. The moment any change is made and submitted in the filter form, the database page navigation form will NOT be
				submitted and will therefore reset to the first page.
				*/
				
				//Unsetting all filter-correspondant $_POST variables and any entered search text if the "Clear Filters" button is pressed
				if (isset($_POST['filterCLEARALL'])) {
					unset($_POST['filterINTL']);
					unset($_POST['filterUCLASS']);
					unset($_POST['filterRMT']);
					if (isset($_SESSION['allLocations'])) {
						$removeLocations = $_SESSION['allLocations'];
						for ($i = 0; $i < sizeof($removeLocations); $i++) {
							$removeFormLocation = preg_replace('/[\W]/', '', $removeLocations[$i]);
							unset($_POST[$removeFormLocation]);
						}
					}
					unset($_POST['sortBy']);
				}
				
				/* Search session management
				If a non-trivial search is made, it updates the lastsearch sesson variable 
				This session variable can then be referenced later, so searches don't clear any time the form is posted
				(Which happens quite often)*/
				if (isset($_POST['dbSearch']) && $_POST['dbSearch'] != NULLSEARCH) {
					$_SESSION['lastSearch'] = $_POST['dbSearch'];
				} else {
					// If a search isn't made, *and* no clearing operation is made, it resets the search
					$locationClearFlag = false;
					if(isset($_SESSION['allLocations'])) { //Checking if any location filters are selected
						$searchManagementLocations = $_SESSION['allLocations'];
						$numSelected = 0;
						for ($i = 0; $i < sizeof($searchManagementLocations); $i++) {
							$formLocation = preg_replace('/[\W]/', '', $searchManagementLocations[$i]);
							if (isset($_POST[$formLocation])) {
								$numSelected++;
							}
						}
						if ($numSelected == 0) {
							$locationClearFlag = true; //No location filters are selected, lastsearch should be reset to NULLSEARCH
						}
						
						if (!(isset($_POST['previous']) || isset($_POST['next']) || isset($_POST['filterINTL']) || isset($_POST['filterUCLASS']) || isset($_POST['filterRMT']))) {
							if ($locationClearFlag) {
								$_SESSION['lastSearch'] = NULLSEARCH;
							}
						}
					}
					else if (!(isset($_POST['previous']) || isset($_POST['next']) || isset($_POST['filterINTL']) || isset($_POST['filterUCLASS']) || isset($_POST['filterRMT']))) {
						$_SESSION['lastSearch'] = NULLSEARCH;
					}
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
				

				//If the database has not been loaded during the current browser session, a loading indicator is displayed
				if (!isset($_SESSION['dbLoaded'])){
					echo "<img src='images/loadingGraphic.gif' height='150px' width='150px'>";
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
				
				//Removing all info except the year, month, and day posted from the date stored in each internship's "posted" field
				function shortenDate(&$unshortened) {
					for ($i = 0; $i < sizeof($unshortened); $i++) {
						if (strlen($unshortened[$i]['posted']) != 0) {
							$unshortened[$i]['posted'] = substr($unshortened[$i]['posted'], 0, 10);
						} 
					}
				}
				
				//Creating an array of all internships' corresponding locations with no diuplicates
				function determineLocations (&$ref) {
					$allLocations = array();
					
					//Finding each internship's location
					for ($i = 0; $i < sizeof($ref); $i++) {
						$newLoc = $ref[$i]['location'];
						$duplicate = false;
						
						//Storing each internship's location in an array if it is not already present (a duplicate)
						for ($j = 0; $j < sizeof($allLocations); $j++) {
							if ($newLoc == $allLocations[$j]) {
								$duplicate = true;
							}
						}
						if (!$duplicate) {
							array_push($allLocations, $newLoc);
						}
					}
					
					//Sorting locations array alphabetically for display
					for ($i = 0; $i < sizeof($allLocations) - 1; $i++) {
						for ($j = $i + 1; $j < sizeof($allLocations); $j++) {
							if (strcmp(strtolower(preg_replace('/[\W]/', '', $allLocations[$i])), strtolower(preg_replace('/[\W]/', '', $allLocations[$j]))) > 0) {
								$temp = $allLocations[$i];
								$allLocations[$i] = $allLocations[$j];
								$allLocations[$j] = $temp;
							}
						}
					}
					$_SESSION['allLocations'] = $allLocations;
				}
				
				//Creating an array that contains the number of currently applied filters each internship satisfies.
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
					if (isset($_POST['filterRMT'])) { //Remote Positions Filter
						$fsFlag = true;
						for ($i = 0; $i < sizeof($ref); $i++) {
							if ($ref[$i]['RMT']) {
								$fan[$i]++;
							}
						}
					}
					
					//Locations filter
					$fanLocations = $_SESSION['allLocations'];
					for ($i = 0; $i < sizeof($fanLocations); $i++) {
						$fanFieldLocation = $fanLocations[$i]; //Used to check if a certain internship satisfies a certain location filter selection
						$fanFormLocation = preg_replace('/[\W]/', '', $fanLocations[$i]); //Used to check if certain location is selected
						if (isset($_POST[$fanFormLocation])) {
							$fsFlag = true;
							for ($j = 0; $j < sizeof($ref); $j++) {
								if ($fanFieldLocation == $ref[$j]['location']) {
									$fan[$j]++;
								}
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

				function sortNewest(&$internshipArray) {
					for ($i = 0; $i < sizeof($internshipArray); $i++) {
						for ($j = 0; $j < sizeof($internshipArray) - 1; $j++) { //Compare year, followed by month, followed by day
							if ((int)substr($internshipArray[$j]['posted'], 0, 4) < (int)substr($internshipArray[$j + 1]['posted'], 0, 4)) {
								$temp = $internshipArray[$j];
								$internshipArray[$j] = $internshipArray[$j + 1];
								$internshipArray[$j + 1] = $temp;
							}
							else if ((int)substr($internshipArray[$j]['posted'], 0, 4) == (int)substr($internshipArray[$j + 1]['posted'], 0, 4)) {
								if ((int)substr($internshipArray[$j]['posted'], 5, 2) < (int)substr($internshipArray[$j + 1]['posted'], 5, 2)) {
									$temp = $internshipArray[$j];
									$internshipArray[$j] = $internshipArray[$j + 1];
									$internshipArray[$j + 1] = $temp;
								}
								else if ((int)substr($internshipArray[$j]['posted'], 5, 2) == (int)substr($internshipArray[$j + 1]['posted'], 5, 2)) {
									if ((int)substr($internshipArray[$j]['posted'], 8, 2) < (int)substr($internshipArray[$j + 1]['posted'], 8, 2)) {
										$temp = $internshipArray[$j];
										$internshipArray[$j] = $internshipArray[$j + 1];
										$internshipArray[$j + 1] = $temp;
									}
								}
							}
							//If year, month, and day are all the same, don't swap to preserve alphabetical ordering
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
				
				//Receiving and decoding database data
				if (isset($_POST['postsendDB'])) {
					$receiveJson = $_POST['postsendDB'];
					$decode = json_decode($receiveJson, true); //Value "true" decodes received data as an associative array
					
					//Sorting received data alphabetically, removing unneeded data from "posted" field, and storing in a session variable
					sortAlpha($decode);
					shortenDate($decode);
					
					$_SESSION['alphabetical'] = $decode;
					
					//Recording in a session variable all locations that should appear in the locations filter
					determineLocations($decode);
				}
				
				//Formatting and displaying database data
				if (isset($_SESSION['alphabetical'])) {
					
					//Assigning each internship a filter attribute number (FAN)
					$filterAttributeNumbers = array();
					
					//$displayData will contain all database data formatted for display before any fitlers are applied
					$displayData = $_SESSION['alphabetical'];
					
					//Sorting internships according to the option selected by the user (data is alphabetically sorted by default, so nothing needs to be
					//done if this option is selected and the $_POST variable corresponding to sorting options can be unset)
					if (isset($_POST['sortBy']) and $_POST['sortBy'] == 'sortByAlpha') {
						unset($_POST['sortBy']);
					}
					else if (isset($_POST['sortBy']) and $_POST['sortBy'] != 'sortByAlpha') { //"Newest First" or "Oldest First" were selected
						//Creating and populating two temporary arrays with internships which list a post date and those which don't, respectively
						$dateTemp = array();
						$noDateTemp = array();
						for ($i = 0; $i < sizeof($displayData); $i++) {
							if ($displayData[$i]['posted'] == 0) {
								array_push($noDateTemp, $displayData[$i]);
							}
							else {
								array_push($dateTemp, $displayData[$i]);
							}
						}
						
						//Sorting the array of internships that list post dates by their dates based on which sorting option was selected by the user
						for ($i = 0; $i < sizeof($dateTemp); $i++) { //First, the array is sorted in descending order by date (newest dates first)
							for ($j = 0; $j < sizeof($dateTemp) - 1; $j++) { //Compare year, followed by month, followed by day
								if ((int)substr($dateTemp[$j]['posted'], 0, 4) < (int)substr($dateTemp[$j + 1]['posted'], 0, 4)) {
									$temp = $dateTemp[$j];
									$dateTemp[$j] = $dateTemp[$j + 1];
									$dateTemp[$j + 1] = $temp;
								}
								else if ((int)substr($dateTemp[$j]['posted'], 0, 4) == (int)substr($dateTemp[$j + 1]['posted'], 0, 4)) {
									if ((int)substr($dateTemp[$j]['posted'], 5, 2) < (int)substr($dateTemp[$j + 1]['posted'], 5, 2)) {
										$temp = $dateTemp[$j];
										$dateTemp[$j] = $dateTemp[$j + 1];
										$dateTemp[$j + 1] = $temp;
									}
									else if ((int)substr($dateTemp[$j]['posted'], 5, 2) == (int)substr($dateTemp[$j + 1]['posted'], 5, 2)) {
										if ((int)substr($dateTemp[$j]['posted'], 8, 2) < (int)substr($dateTemp[$j + 1]['posted'], 8, 2)) {
											$temp = $dateTemp[$j];
											$dateTemp[$j] = $dateTemp[$j + 1];
											$dateTemp[$j + 1] = $temp;
										}
									}
								}
								//If year, month, and day are all the same, don't swap to preserve alphabetical ordering
							}
						}
						//Array is already sorted in descending order and should only be reversed if the "Oldest First" option was selected
						if ($_POST['sortBy'] == 'sortByOld') {
							$dateTemp = array_reverse($dateTemp);
						}
						//Appending all internships with no post date listed at the end of the display array
						$displayData = array_merge($dateTemp, $noDateTemp);
					}
					
					if (strcmp($_SESSION['lastSearch'], NULLSEARCH) != 0) { // If no clearing operations have been made since last search
						$searchDisplayData = []; // to store correct results
						$searchValue = strtolower($_SESSION['lastSearch']); // to store 
						for ($i = 0; $i < sizeOf($displayData); $i++) {
							if (str_contains(strtolower($displayData[$i]['company']), $searchValue) || str_contains(strtolower($displayData[$i]['name']), $searchValue)) {
								$searchDisplayData[] = $displayData[$i];
							}
						}
						$displayData = $searchDisplayData;
					}
					
					$filterSetFlag = false; //True if any filter is applied, false if not. Used for displaying data.
					createFAN($filterAttributeNumbers, $displayData, $filterSetFlag);
					
					//Sorting FAN and database data arrays at the same time so that the database data array will be properly formatted for output
					//(internships will be listed in descending order according to each of their FANs)
					if ($filterSetFlag) { //Sorting of internships by FAN will not occur if no filters are currently selected (all FANs are 0)
						sortFAN($filterAttributeNumbers, $displayData);
					}
					
					//Calculating the highest number of pages that may be dislayed based on the number of internships that must be displayed and the
					//assumption that PERPAGE internships will be displayed per page
					if ($filterSetFlag) { //If a filter is set, maxPages is calculated based on the number of internships with a FAN of greater than 0
						$fanCounter = 0;
						for ($i = 0; $i < sizeOf($filterAttributeNumbers); $i++) {
							if ($filterAttributeNumbers[$i] != 0) {
								$fanCounter++;
							}
						}
						
						$_SESSION['maxPages'] = ceil($fanCounter / PERPAGE);
					}
					else { //Otherwise, maxPages is calculated based on total number of internships
						$_SESSION['maxPages'] = ceil(sizeOf($displayData) / PERPAGE);
					}
					
//DISPLAYING FILTERS AND PAGE BUTTONS
/*****************************************************************************************************************************/
					
					//Creating filter selection form (the form used to apply any filters)
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
					
					if (isset($_POST['filterRMT'])) { //Remote positions filter
						echo "<input type='checkbox' name='filterRMT' id='filterRMT' checked>";
					}
					else {
						echo "<input type='checkbox' name='filterRMT' id='filterRMT'>";
					}
					echo "<label for='filterRMT'>Remote</label>";
					
					//Locations filter
					echo "<section class='filterLOC' id='filterLOC'><span class='locationsAnchor' id='locationsAnchor' onclick='displayListLOC()'>Select a Location</span>";
					echo "<ul class='itemsLOC' id='itemsLOC'>";
					
					$allLocationsDisplay = $_SESSION['allLocations'];
					for ($i = 0; $i < sizeof($allLocationsDisplay); $i++) {
						$displayLocation = $allLocationsDisplay[$i]; //Used for display, contains original location string
						$formLocation = preg_replace('/[\W]/', '', $allLocationsDisplay[$i]); //Used for filtering, removes certain special chars
						if (isset($_POST[$formLocation])) {
							echo "<li id='itemsLOC'><input type='checkbox' checked name=$formLocation id=$formLocation class='selectLOC'>";
							echo "<label for=$formLocation class='dropdownLabelLOC'>$displayLocation</label></li>";
						}
						else {
							echo "<li id='itemsLOC'><input type='checkbox' name=$formLocation id=$formLocation class='selectLOC'>";
							echo "<label for=$formLocation class='dropdownLabelLOC'>$displayLocation</label></li>";
						}
					}
					
					echo "</ul></section>";
					
					//Sort-by filter
					echo "<section class='filterSORT' id='filterSORT'><span class='sortAnchor' id='sortAnchor' onclick='displayListSORT()'>Sort By</span>";
					echo "<ul class='itemsSORT' id='itemsSORT'>";
					if (!isset($_POST['sortBy'])){ //"Alphabetical" was selected as the sort option
						echo <<< MULTILINE
							<li id='itemsSORT'><input type='radio' name='sortBy' class='selectSORT' id='sortByAlpha' value='sortByAlpha' checked>
							<label for='sortByAlpha' class='dropdownLabelSORT'>Alphabetical</label></li>
							<li id='itemsSORT'><input type='radio' name='sortBy' class='selectSORT' id='sortByNew' value='sortByNew'>
							<label for='sortByNew' class='dropdownLabelSORT'>Newest First</label></li>
							<li id='itemsSORT'><input type='radio' name='sortBy' class='selectSORT' id='sortByOld' value='sortByOld'>
							<label for='sortByOld' class='dropdownLabelSORT'>Oldest First</label></li>
						MULTILINE;
					}
					else if ($_POST['sortBy'] == 'sortByNew') { //"Sort By Newest" was selected as the sort option
						echo <<< MULTILINE
							<li id='itemsSORT'><input type='radio' name='sortBy' class='selectSORT' id='sortByAlpha' value='sortByAlpha'>
							<label for='sortByAlpha' class='dropdownLabelSORT'>Alphabetical</label></li>
							<li id='itemsSORT'><input type='radio' name='sortBy' class='selectSORT' id='sortByNew' value='sortByNew' checked>
							<label for='sortByNew' class='dropdownLabelSORT'>Newest First</label></li>
							<li id='itemsSORT'><input type='radio' name='sortBy' class='selectSORT' id='sortByOld' value='sortByOld'>
							<label for='sortByOld' class='dropdownLabelSORT'>Oldest First</label></li>
						MULTILINE;
					}
					else { //"Sort By Oldest" was selected as the sort option
						echo <<< MULTILINE
							<li id='itemsSORT'><input type='radio' name='sortBy' class='selectSORT' id='sortByAlpha' value='sortByAlpha'>
							<label for='sortByAlpha' class='dropdownLabelSORT'>Alphabetical</label></li>
							<li id='itemsSORT'><input type='radio' name='sortBy' class='selectSORT' id='sortByNew' value='sortByNew'>
							<label for='sortByNew' class='dropdownLabelSORT'>Newest First</label></li>
							<li id='itemsSORT'><input type='radio' name='sortBy' class='selectSORT' id='sortByOld' value='sortByOld' checked>
							<label for='sortByOld' class='dropdownLabelSORT'>Oldest First</label></li>
						MULTILINE;
					}
					echo "</ul></section>";
				?>
				<script>
					//Hides any dropdown list currently displayed if any part of the screen is clicked outside of the list
					document.onclick = function (clickoffEvent) {
						if (clickoffEvent.target.id != "sortAnchor" && clickoffEvent.target.id != "itemsSORT" && clickoffEvent.target.className != "selectSORT" && clickoffEvent.target.className != "dropdownLabelSORT") {
							if (filterSORT.classList.contains("visible")) {
								filterSORT.classList.remove("visible");
							}
						}
						
						if (clickoffEvent.target.id != "locationsAnchor" && clickoffEvent.target.id != "itemsLOC" && clickoffEvent.target.className != "selectLOC" && clickoffEvent.target.className != "dropdownLabelLOC") {
							if (filterLOC.classList.contains("visible")) {
								filterLOC.classList.remove("visible");
							}
						}
					};
				
					//Displaying locations list when "Select a Location" dropdown button is clicked
					let filterLOC = document.getElementById("filterLOC");
					
					function displayListLOC() {
						if (filterLOC.classList.contains("visible")) {
							filterLOC.classList.remove("visible");
						}
						else {
							filterLOC.classList.add("visible");
						}
					}
					
					//Displaying sort options list when "Sort By" dropdown button is clicked
					let filterSORT = document.getElementById("filterSORT");
					
					function displayListSORT() {
						if (filterSORT.classList.contains("visible")) {
							filterSORT.classList.remove("visible");
						}
						else {
							filterSORT.classList.add("visible");
						}
					}
				</script>
				<?php	
					echo <<< MULTILINE
						<br>
						<section class='dbSubmitContainer'>
							<section>
								<input type='submit' value='Apply Filters' name='filterAPPLY'>
								<input type='submit' value='Clear Filters' name='filterCLEARALL'>
								<input type='submit' value="I'm Feeling Lucky" name='randomShips'>
							</section>
							<section>
							<label for='dbSearch'>Search the Database: </label>
					MULTILINE;
					
					if (isset($_SESSION['lastSearch']) and $_SESSION['lastSearch'] != NULLSEARCH) {
						$currentSearchVal = $_SESSION['lastSearch'];
						echo "<input type='text' name='dbSearch' id='dbSearch' placeholder='$currentSearchVal'>";
					}
					else {
						echo "<input type='text' name='dbSearch' id='dbSearch' placeholder='Press Enter to Search!'>";
					}
					
					echo <<< MULTILINE
							</section>
						</section>
						</form>
						<div id='filtersBottomBG'></div>
					MULTILINE;
					
					/*
					Creating previous/next page form
					
					The previous/next page buttons will only appear in the event that the "I'm Feeling Lucky" button has not been clicked (it is assumed
					that the number of internships that may be displayed per page is always 5 or greater) and in the event that there are enough
					internships (number of internships > PERPAGE) in the database or that satisfy selected filters that displaying them will require more
					than one page.
					*/
					if (!isset($_POST['randomShips']) and sizeof($displayData) > PERPAGE) {
						if (!$filterSetFlag or ($filterSetFlag and $fanCounter > PERPAGE)) {
							echo "<form method='post' action='internshipDB.php'>";
							
							//Ensuring correct filters are still applied upon moving to a new page (filters should only change when "Clear Filters" is
							//selected or when filters are deselected and "Apply Filters" is clicked)
							if (isset($_POST['filterINTL'])) {
								echo "<input type='hidden' name='filterINTL' value='true'>";
							}
							if (isset($_POST['filterUCLASS'])) {
								echo "<input type='hidden' name='filterUCLASS' value='true'>";
							}
							if (isset($_POST['filterRMT'])) {
								echo "<input type='hidden' name='filterRMT' value='true'>";
							}
							
							$reselectLocations = $_SESSION['allLocations'];
							for ($i = 0; $i < sizeof($reselectLocations); $i++) {
								$formLocation = preg_replace('/[\W]/', '', $reselectLocations[$i]);
								if (isset($_POST[$formLocation])) {
									echo "<input type='hidden' name=$formLocation value='true'>";
								}
							}
							
							if (isset($_POST['sortBy']) and $_POST['sortBy'] == 'sortByNew') {
								echo "<input type='hidden' name='sortBy' value='sortByNew'>";
							}
							else if (isset($_POST['sortBy']) and $_POST['sortBy'] == 'sortByOld') {
								echo "<input type='hidden' name='sortBy' value='sortByOld'>";
							}
							
							if ($currentPage == 1) { //Only the "Next Nage" option displayed when no previous page exists
								echo <<< MULTILINE
									<input type='hidden' name='page' value=$currentPage>
									<section id='pageOptionsFirst'>
										<input type='submit' name='next' value='Next Page ►'>
									</section>
								MULTILINE;
							}
							else if ($currentPage == $_SESSION['maxPages']) { //Only the "Previous Page" option displayed when no next page exists
								echo <<< MULTILINE
									<input type='hidden' name='page' value=$currentPage>
									<section id='pageOptionsLast'>
										<input type='submit' name='previous' value='◄ Previous Page'>
									</section>
								MULTILINE;
							}
							else { //Otherwise, display both "Previous Page" and "Next Page" options
								echo <<< MULTILINE
									<input type='hidden' name='page' value=$currentPage>
									<section id='pageOptions'>
										<input type='submit' name='previous' value='◄ Previous Page'>
										<input type='submit' name='next' value='Next Page ►'>
									</section>
								MULTILINE;
							}
							echo "</form>";
						}
					}
					
//DISPLAYING INTERNSHIPS
/*****************************************************************************************************************************/

					//Displaying a "no internships found" message if $displayData contains nothing (search has returned no results) or no internships
					//satisfy an applied filter
					if (sizeof($displayData) == 0 or ($filterSetFlag and $filterAttributeNumbers[0] == 0)) {
						echo <<< MULTILINE
							<section class='noResults'>
								<h3>Uh Oh &mdash; No Internships Found :(</h3>
								<p>If you've made a search or have applied some filters, we regret to inform you that we didn't find anything. We're
								sorry we don't have what you're looking for. We wish you the best of luck in your ongoing search!</p>
								<p>If you feel there's a mistake on our end, please feel free to contact us at andybernato@gmail.com or
								aidanmdunne03@gmail.com.</p>
							</section>
						MULTILINE;
					}
					else if ($filterSetFlag) { //Displaying data when a filter is set (internships with FANs of 0 are not displayed)
						if(isset($_POST['randomShips'])) { // I'm feeling lucky button

							// messing with the global displaydata array at this level causes issues, so I make a copy
							$tempDisplayData = $displayData;
							$luckyDisplayData = []; // storing randomly selected internships to be displayed

							//Finding largest filter number (required for displaying most relevant internships when "I'm Feeling Lucky" button is 
							//clicked)
							$maxFAN = $filterAttributeNumbers[0];
							
							/* This is where the magic happens 
							This acts functionally as a base case. If maxFAN ever hits -1, there are no more internships
							to check. Could instead be replaced with 'While sizeof luckydisplay < 5, but this stops
							the website from bricking if the scraper goes down and they click the button with filters applied*/
							while ($maxFAN != -1) {
								// the following collects all internships with a FAN equal to the current max and stores them in
								// hasmaxfan
								$hasMaxFan = [];
								for ($i = 0; $i < sizeof($tempDisplayData); $i++) {
									if ($maxFAN == $filterAttributeNumbers[$i]) {
										$hasMaxFan[] = $tempDisplayData[$i];
									}
								}
								
						// then, while there are still internships with the max filters and too many haven't already been collected
								while (sizeof($hasMaxFan) != 0 && sizeof($luckyDisplayData) < 5) {
									// Gets a random index, stores the item in that index in our final display array
									$randomIndex = rand(0, sizeof($hasMaxFan) - 1);
									$luckyDisplayData[] = $hasMaxFan[$randomIndex];

									// Then deletes the index from both arrays so duplicates don't happen
									array_splice($hasMaxFan, $randomIndex, 1);
									array_splice($tempDisplayData, $randomIndex, 1);
								}
								// Decrements this for the base case AND so it gets new internships during the hasmaxfan loop
								$maxFAN = $maxFAN - 1;
							}
							
							if ($_POST['sortBy'] == "sortByNew") {
								sortNewest($luckyDisplayData);
							} else if ($_POST['sortBy'] == "sortByOld") {
								sortNewest($luckyDisplayData);
								array_reverse($luckyDisplayData);
							} else {
								sortAlpha($luckyDisplayData);
							}
							$displayData = $luckyDisplayData;

						}
						
						$sizeTest = 0;
						while ($filterAttributeNumbers[$sizeTest] != 0) {
							$sizeTest++;
						}
						$sizeTest--;
						echo $sizeTest;
						
						echo "<input type='hidden' value=$sizeTest id='sizeTest'>";
						echo "<input type='hidden' value=$currentPage id='currentPage'>";
						
						for ($i = ($currentPage * PERPAGE) - PERPAGE; $i < $currentPage * PERPAGE; $i++) { //Displaying PERPAGE internships per page
							if ($i >= sizeOf($displayData)) {
								break;
							}
							$dbInd = $displayData[$i]['dbIndex'];
							$com = $displayData[$i]['company'];
							$nam = $displayData[$i]['name'];
							$loc = $displayData[$i]['location'];
							$lnk = $displayData[$i]['link'];
							$pay = $displayData[$i]['pay'];
							$ptd = $displayData[$i]['posted'];
							$fan = $filterAttributeNumbers[$i]; //Required for displaying only desired internships
							
							if ($fan > 0) {
								if ($_SESSION['loggedIn'] == true)
								{
									echo <<< MULTILINE
				
									<table class='dbTable'>
										<tr>
											<td colspan='2'><h3>$com<span class='internshipPosition'> &mdash; $nam</span></h3></td>
											<td></td>
										</tr>
										<tr>
											<td class='linkRow' colspan='3'><a href='$lnk' target='_blank' rel='noreferrer noopener'>$com</a></td>
										</tr>
										<tr>
											<td><b>Location:</b> $loc</td>
									MULTILINE;
								}
								else {
									echo <<< MULTILINE
				
									<table class='dbTable'>
										<tr>
											<td colspan='2'><h3>$com<span class='internshipPosition'> &mdash; $nam</span></h3></td>
										</tr>
										<tr>
											<td class='linkRow' colspan='3'><a href='$lnk' target='_blank' rel='noreferrer noopener'>$com</a></td>
										</tr>
										<tr>
											<td><b>Location:</b> $loc</td>
								MULTILINE;
								}
								
								//Displaying pay rate and date posted information if it is available
								$extraCellDisplay = 0; //Required to ensure the correct number of table cells are dispalyed inline
								
								if ($pay != 0) {
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
								
								echo "</tr><tr><td colspan='3'><section class='filterSat'>";
								
								//Displaying whether or not each internship satisfies certain selected filters
								if (isset($_POST['filterINTL']) and $displayData[$i]['INTL']) {
									echo "<p class='INTL'>Open to International Students</p>";
								}
								if (isset($_POST['filterUCLASS']) and $displayData[$i]['UCLASS']) {
									echo "<p class='UCLASS'>Open to Underclassmen</p>";
								}
								if (isset($_POST['filterRMT']) and $displayData[$i]['RMT']) {
									echo "<p class='RMT'>Remote</p>";
								} 
								
								$tagLocations = $_SESSION['allLocations'];
								for ($j = 0; $j < sizeof($tagLocations); $j++) {
									$tagDisplayLocation = $tagLocations[$j]; //Used to select internships on which the location tag should be displayed
									$tagFormLocation = preg_replace('/[\W]/', '', $tagLocations[$j]); //Checks which locations selected
									if (isset($_POST[$tagFormLocation]) and $tagDisplayLocation == $displayData[$i]['location']) {
										echo "<p class='LOC'>Location Match</p>";
									}
								}
								
								echo "</section></td></tr></table>";
							}
						}
					}
					else { //Displaying data when no filters are set
						if(isset($_POST['randomShips'])) { // Feeling lucky
							// uses a temp, because things break otherwise
							$tempDisplayData = $displayData;

							// This is where the 5 internships ultimately get stored
							$luckyDisplayData = [];

							// The first case stops this from bricking if there are less than 5 internships in the database
							// the second ensures only 5 internships get randomly picked 
							while (sizeof($tempDisplayData) != 0 && sizeof($luckyDisplayData) < 5) {
								// Gets a random index, stores the item in that index in our final display array
								$randomIndex = rand(0, sizeof($tempDisplayData) - 1);
								$luckyDisplayData[] = $tempDisplayData[$randomIndex];

								// Then deletes the index from tempdisplay, to avoid dupes 
								array_splice($tempDisplayData, $randomIndex, 1);
							}
							// Sets the display array to our randomly generated array of 5
							if ($_POST['sortBy'] == "sortByNew") {
								sortNewest($luckyDisplayData);
							} else if ($_POST['sortBy'] == "sortByOld") {
								sortNewest($luckyDisplayData);
								array_reverse($luckyDisplayData);
							} else {
								sortAlpha($luckyDisplayData);
							}
							$displayData = $luckyDisplayData;

						}
						
						$sizeTest = sizeof($displayData);
						echo "<input type='hidden' value=$sizeTest id='sizeTest'>";
						echo "<input type='hidden' value=$currentPage id='currentPage'>";
						for ($i = ($currentPage * PERPAGE) - PERPAGE; $i < $currentPage * PERPAGE; $i++) { //Displaying PERPAGE internships per page
							if ($i >= sizeOf($displayData)) {
								break;
							}
							$com = $displayData[$i]['company'];
							$nam = $displayData[$i]['name'];
							$loc = $displayData[$i]['location'];
							$lnk = $displayData[$i]['link'];
							$pay = $displayData[$i]['pay'];
							$ptd = $displayData[$i]['posted'];
							
							if ($_SESSION['loggedIn'] == true)
							{
								echo <<< MULTILINE
			
								<table class='dbTable'>
									<tr>
										<td colspan='2'><h3>$com<span class='internshipPosition'> &mdash; $nam</span></h3></td>
										<td>
								MULTILINE;
									
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
										<form action='internshipDB.php' method='post' id='$idForm'>
											<input type='hidden' name='company' id='$idCompany' value='$valueCompany'>
											<input type='hidden' name='company' id='$idName' value='$valueName'>
											<input type='hidden' name='company' id='$idLocation' value='$valueLocation'>
											<input type='hidden' name='company' id='$idLink' value='$valueLink'>
											<input type='hidden' name='company' id='$idPay' value='$valuePay'>
											<input type='hidden' name='company' id='$idPosted' value='$valuePosted'>
											<input type='submit' value='submitTest'>
										</form></td>
									</tr>
									<tr>
										<td class='linkRow' colspan='3'><a href='$lnk' target='_blank' rel='noreferrer noopener'>$com</a></td>
									</tr>
									<tr>
										<td><b>Location:</b> $loc</td>
							MULTILINE;
							}
							else {
								echo <<< MULTILINE
			
								<table class='dbTable'>
									<tr>
										<td colspan='2'><h3>$com<span class='internshipPosition'> &mdash; $nam</span></h3></td>
									</tr>
									<tr>
										<td class='linkRow' colspan='3'><a href='$lnk' target='_blank' rel='noreferrer noopener'>$com</a></td>
									</tr>
									<tr>
										<td><b>Location:</b> $loc</td>
							MULTILINE;
							}
							
							
							//Displaying pay rate and date posted information if it is available
							$extraCellDisplay = 0; //Required to ensure the correct number of table cells are dispalyed inline
							
							if ($pay != 0) {
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
					}
					echo "</section>";
				}
			?>
			<script type="module">

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
				//const appTwo = initializeApp(firebaseConfig);
				//const dBase = getDatabase(app);
				//const snapshotWrite = get(ref(db, "users"));

				let formsArr = [];
				let sizeTest = document.getElementById("sizeTest").value;
				let currentPage = document.getElementById("currentPage").value;
				
				for (let i = currentPage * 10 - 9; i <= currentPage * 10; i++) {
					if (i > sizeTest) {
						break;
					}
					
					formsArr[i - 1] = (document.getElementById("" + i));
					formsArr[i - 1].addEventListener("submit", function (event) {
						let com = document.getElementById("com" + i).value;
						alert(com);
						let nam = document.getElementById("nam" + i).value;
						alert(nam);
						let loc = document.getElementById("loc" + i).value;
						alert(loc);
						let lnk = document.getElementById("lnk" + i).value;
						alert(lnk);
						let pay = document.getElementById("pay" + i).value;
						alert(pay);
						let ptd = document.getElementById("ptd" + i).value;
						alert(ptd);
						let unameTag = "<?php echo $_SESSION['username'] ?>";
						alert(unameTag);

						set(ref(db, 'users/'+ unameTag + '/history/' + nam), {
							company: com,
							date_posted: ptd,
							job_name: nam,
							link: lnk,
							location: loc,
							pay: pay,
						});
						event.preventDefault();
					});
				}
			</script>
			<!-- Filter use information -->
			<h2>Filters</h2>
			<h3>International Student Filter</h3>
			<p>Our international students filter is very straightforward. This filter may be applied by selecting the box to the left of the "Open to
			International Students" text in the filters bar and clicking the "Apply Filters" button. When this filter is applied, only internships
			which do not list U.S. citizenship/the lack of need for visa support as application requirements. Additionally, each internship which 
			satisfies this filter will be displayed along with this tag to indicate that the internship position is open to international students:</p>
			<span class="filterSat"><p class="INTL">Open to International Students</p></span>
			<h3>Underclassman Filter</h3>
			<p>Like our international students filter, our underclassman filter is equally simple. This filter may be applied by selecting the box to the
			left of the "Open to Underclassmen" text in the filters bar and clicking the "Apply Filters" button. When this filter is applied, only
			internships which do not list being a rising senior as a requirement or otherwise specify that applications from freshmen and sophomores 
			will be considered. Each internship which satisfies this filter will be displayed along with this tag to indicate that the internship
			position is open to underclassmen:</p>
			<span class="filterSat"><p class="UCLASS">Open to Underclassmen</p></span>
			<h3>Remote Filter</h3>
			<p>Our remote positions filter is another simple filter and may be applied by selecting the box to the left of the "Remote" text in the
			filters bar and clicking the "Apply Filters" button. When this filter is applied, only internships which specify that the listed position
			is remote (not in-person or on-location) or has the option of being remote are listed. Each internship which satisfies this filter will be
			displayed along with this tag to indicate that the internship position is remote:</p>
			<span class="filterSat"><p class="RMT">Remote</p></span>
			<h3>Filter by Location</h3>
			<p>Our locations filter may be applied by selecting one or more options from the "Select a Location" dropdown list and clicking the "Apply
			Filters" button. When the locations filter is applied, only internships which are offered in your selected locations are listed. Each 
			internship which satisfies this filter will be displayed along with this tag to indicate that the internship is offered in a location you've
			selected:</p>
			<span class='filterSat'><p class='LOC'>Location Match</p></span>
		</section>
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, <small>and</small> Owen Murphy <small>with help from</small> James Calder, Adi Shah,
			<small>and</small> Paige Su &mdash; 2024.
		</footer>
	</main>
</body>
</html>