<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<style>
		<?php include 'styles.css' ?>
	</style>
	<title>Internship Database - Home</title>
</head>

<body>
	<!-- divs with classes headerTopBG and headerBottomBorder are required to allow each page's header and its border to look as they did in design 
	models apporoved by end users -->
	<div class="headerTopBG"></div>
	<header>
		<h1>Home</h1>
		<a href="userProfile.php"><img src="images/profilePageIcon.png" class="profIcon"></img></a>
		<nav id="mainNav">
			<a href="index.php" class="currentPage">Home</a>
			<a href="internshipDB.php">Internship Database</a>
			<a href="pastInternships.php">Companies and Programs</a>
			<a href="REUTab.php">REUs</a>
		</nav>
	</header>
	<div class="headerBottomBorder"></div>
	
	<main>
		<section class="pageContentMain">
			<h2>Welcome &mdash; your job search starts here!</h2>
			<p>Whether you're looking for an internship that fits your skillset, an REU in a research area that interests you, or a company at which
			Truman students like you have had success at in the past, it can all be found here. </p>
			<h3>Resources</h3>
			<p>With our website, we aim to make the internship-searching process more accessible and less stressful for groups like
			international students and underclassmen who may have a difficult time finding internships that will consider their
			applications. Our <a href="internshipDB.php">internship database</a> offers a range of easy-to-use search filters which will allow you to refine your internship search
			and more efficiently find internships that are perfect for you. Additionally, our <a href="pastInternships.php">Companies and Programs</a> 
			page provides a list of companies that offer internship programs at which Truman students have been successful at in the past.</p>
			<p>Our <a href="REUTab.php">Research Experiences for Undergraduates (REU)</a> page offers an explanation of what REUs are, why you 
			may want to consider one over an internship, and provides a link to the National Science Foundation's REU database.</p>
			<h3>Whatever it is you're looking for, we hope we can help you find it!</h3>
		</section>
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, <small>and</small> Owen Murphy <small>with help from</small> James Calder, Adi Shah,
			<small>and</small> Paige Su &mdash; 2024.
		</footer>
	</main>
</body>
</html>