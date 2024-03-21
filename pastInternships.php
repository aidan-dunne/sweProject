<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="Stylesheet" href="styles.css">
	<title>Internship Database - Companies and Programs</title>
</head>

<body>
	<header>
		<h1>Companies and Programs</h1>
		<nav>
			<a href="index.php">Home</a>
			<a href="pastInternships.php">Companies and Programs</a>
			<a href="REUTab.php">REUs</a>
		</nav>
	</header>
	<!-- I've reached out to Modus Bonedus to get some alumni contributions,
	so hopefully I should have some data to populate this all with soon. Should probably also consider some way
	to reach out to current students -->
	<main>
		<h2>About</h2>
		<section id="homepagemain">
			To help you figure out which companies you should prioritize applying for, we thought it might be useful to include
			a list of companies/programs Truman students have been accepted into as underclassmen in the past. That means these 
			should all be internships you're guaranteed to have at least <i>some</i> chance of getting. 
		</section>
		<body>
		<?php 
		$intern_json = file_get_contents('past_interns_list.json');
		$decoded_json = json_decode($intern_json, true);
		$interns_list = $decoded_json['pastInterns'];
		foreach($interns_list as $intern)
		{
			$name = $intern["name"];
			$company = $intern["company"];
			$term = $intern["date"];
			$email = $intern["email"];
			$age = $intern["year"];
			$international = $intern["international"];
			$description = $intern["description"];
			echo "Person name: $name";
			echo "Company: $company";
		}
		?>
		</body>
		<footer>
			Created by Andy Bernatow, Cole Bracken, Aidan Dunne, Owen Murphy &mdash; 2024.
		</footer>
	</main>
</body>
</html>