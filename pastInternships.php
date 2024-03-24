<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<!-- This might not be wholly necessary but I couldn't figure out how to get the PHP to see the file otherwise -->
	<style>
		<?php include 'styles.css' ?>
	</style>
	<title>Internship Database - Companies and Programs</title>
</head>

<body>
	<!-- divs with classes headerTopBG and headerBottomBorder are required to allow each page's header and its border to look as they did in design 
	models apporoved by end users -->
	<div class="headerTopBG"></div>
	<header>
		<h1>Companies and Programs</h1>
		<a href="userProfile.php"><img src="images/profilePageIcon.png" class="profIcon"></img></a>
		<nav id="mainNav">
			<a href="index.php">Home</a>
			<a href="internshipDB.php">Internship Database</a>
			<a href="pastInternships.php" class="currentPage">Companies and Programs</a>
			<a href="REUTab.php">REUs</a>
		</nav>
	</header>
	<div class="headerBottomBorder"></div>
	
	<!-- I've reached out to Modus Bonedus to get some alumni contributions,
	so hopefully I should have some data to populate this all with soon. Should probably also consider some way
	to reach out to current students -->
	<main>
		<section class="pageContentMain">
			<h2>Past Internship Successes</h2>
			<p>To better aid you in your internship search and to give you a list of companies that you may want to prioritize applying at, this page
			contains a list of companies and internship programs at which Truman students have found success at in the past. Within each list entry,
			you'll find the name of the company at which the student applied and a link to its careers page, a description of the internship and work 
			the student performed, and some information about the student themselves.</p>
			<p>Some students' have also allowed thier contact information to be listed in case you'd like to talk to them about their internship
			experience or would like to seek their help with the application process.</p>
			<br>
		<?php 
		$intern_json = file_get_contents('past_interns_list.json');
		$decoded_json = json_decode($intern_json, true);
		$interns_list = $decoded_json['pastInterns'];
		foreach($interns_list as $intern)
		{
			$name = $intern["name"];
			$company = $intern["company"];
			$website = $intern["website"];
			$term = $intern["date"];
			$email = $intern["email"];
			$age = $intern["year"];
			$international = $intern["international"];
			$description = $intern["description"];
			echo ("
				<table class='pastTable'>
					<tr class='noPad'>
					  <td colspan='3'><h2>$company</h2></td>
					</tr>
					<tr>
					  <td colspan='3'><a href='$website' target='_blank' rel='noreferrer noopener'>$company Careers</a></td>
					</tr>
					<tr>
					  <td colspan='3'><b>Intern:</b> $name <span class='pastSmallText'>($email)</span></td>
					</tr>
					<tr>
					  <td><b>Internship Term:</b> $term </td>
					  <td><b>Intern Year:</b> $age</td>
					  <td><b>Intern Nationality:</b> $international</td>
					</tr>
					<tr class='noPad'>
					  <td><h3>Internship Description</h3></td>
					</tr>
					<tr>
					  <td colspan='3'>$description <span class='pastSmallText'>&mdash; $name</span></td>
					</tr>
				</table>
			");
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