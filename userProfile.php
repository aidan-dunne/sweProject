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
		<!-- User's username will be retrieved from our database and displayed in header -->
		<h1>[USERNAME]'S Profile</h1>
		<nav id="mainNav">
			<a href="index.php">Home</a>
			<a href="internshipDB.php">Internship Database</a>
			<a href="pastInternships.php">Companies and Programs</a>
			<a href="REUTab.php">REUs</a>
		</nav>
	</header>
	<div class="headerBottomBorder"></div>
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
		set(ref(db, "users/testUser"), {
   			username: "test",
    		password: "test",
    		name_of_user: "test_person",
		});
	</script>
	
	<main>
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, <small>and</small> Owen Murphy <small>with help from</small> James Calder, Adi Shah,
			<small>and</small> Paige Su &mdash; 2024.
		</footer>
	</main>
</body>
</html>