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
	
	<main>
		<section class="pageContentMain">
		</section>
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, <small>and</small> Owen Murphy <small>with help from</small> James Calder, Adi Shah,
			<small>and</small> Paige Su &mdash; 2024.
		</footer>
	</main>
</body>
</html>