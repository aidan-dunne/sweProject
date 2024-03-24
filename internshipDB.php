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
			<p>[DATABASE INTERFACE PLACEHOLDER]</p>
			<h2>Filters</h2>
			<h3>International Student Filter</h3>
			<p id="test"></p>
		</section>
		<script type="module">
			  // Import the functions you need from the SDKs you need
  				import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
  				import { getDatabase, ref, set, get, onValue } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-database.js";

  				document.getElementById("test").innerHTML="Test";
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
				const bookRef = ref(db, "book/Book1/Author");

				onValue(bookRef, (snapshot) => {
  					const data = snapshot.val();
  					document.getElementById("test").innerHTML=(data);
				});

		</script>
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, <small>and</small> Owen Murphy <small>with help from</small> James Calder, Adi Shah,
			<small>and</small> Paige Su &mdash; 2024.
		</footer>
	</main>
</body>
</html>