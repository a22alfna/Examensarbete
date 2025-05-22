<?php

$host = "localhost";
$dbname = "Exjobb1";
$user = "postgres";
$password = "Studier2022!";

$title = $_POST['title'] ?? '';
$artist = $_POST['artist'] ?? '';
$fullText = $_POST['fullText'] ?? '';

$output = '';

// Create connection
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");
//Full text search
if (!$conn) {
    $output = "Database connection failed.";
}elseif (!empty($fullText)) {
    $tsquery = preg_replace('/\s+/', ' & ', $fullText);
    
    $query = "SELECT * FROM \"Songs\" 
              WHERE to_tsvector('english', title || ' ' || artist) @@ to_tsquery('english', $1)";
    $params = array($tsquery);

    $result = pg_query_params($conn, $query, $params);

    if ($result && pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $output .= "<div style='margin-bottom:10px;'>";
            $output .= "<strong> " . htmlspecialchars($row['title']) . "</strong><br>";
            $output .= "Artist: " . htmlspecialchars($row['artist']) . "<br>";
            $output .= "Rank: " . htmlspecialchars($row['rank']) . "<br>";
            $output .= "Date: " . htmlspecialchars($row['date']) . "<br>";
            $output .= "Url: " . htmlspecialchars($row['url']) . "<br>";
            $output .= "Region: " . htmlspecialchars($row['region']) . "<br>";
            $output .= "Chart: " . htmlspecialchars($row['chart']) . "<br>";
            $output .= "Trend: " . htmlspecialchars($row['trend']) . "<br>";
            $output .= "Streams: " . htmlspecialchars($row['streams']) . "<br>";
            $output .= "</div>";
        }
    } else {
        $output = "No results found.";
    }
}

//Keyword search
if (!$conn) {
    $output = "Database connection failed.";
}elseif (empty($fullText) && (!empty($title) || !empty($artist))) {

    if (!empty($title) && !empty($artist)) {
        $query = 'SELECT * FROM "Songs" WHERE title ILIKE $1 AND artist ILIKE $2';
        $params = array("$title%", "$artist%"); //%$title% "%$artist%" för partial search
    } elseif (!empty($title)) {
        $query = 'SELECT * FROM "Songs" WHERE title ILIKE $1';
        $params = array("$title%"); // %$title% för partial search
    } elseif (!empty($artist)) {
        $query = 'SELECT * FROM "Songs" WHERE artist ILIKE $1';//ILIKE $1 utbytt till $artist för exakta keyword sök
        $params = array("$artist%"); //"%$artist%" för partial search
    }

    $result = pg_query_params($conn, $query, $params); 

    if ($result && pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $output .= "<div style='margin-bottom:10px;'>";
            $output .= "<strong> " . htmlspecialchars($row['title']) . "</strong><br>";
            $output .= "Artist: " . htmlspecialchars($row['artist']) . "<br>";
            $output .= "Rank: " . htmlspecialchars($row['rank']) . "<br>";
            $output .= "Date: " . htmlspecialchars($row['date']) . "<br>";
            $output .= "Url: " . htmlspecialchars($row['url']) . "<br>";
            $output .= "Region: " . htmlspecialchars($row['region']) . "<br>";
            $output .= "Chart: " . htmlspecialchars($row['chart']) . "<br>";
            $output .= "Trend: " . htmlspecialchars($row['trend']) . "<br>";
            $output .= "Streams: " . htmlspecialchars($row['streams']) . "<br>";
            $output .= "</div>";;
        }
    } else {
        $output = "No results found.";
    }
} elseif (empty($fullText)) {
    $output = "Please enter a song title or artist to search.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exjobb</title>
    <link rel="stylesheet" href="Exjobb.css">
</head>
<body>

    <div id="welcome">
        <h1>PostgreSQL Search</h1>
        <p>Type something in the search box below:</p>
    </div>
    
    <div id="searchForms" >
        <!-- Keyword Search -->
        <div id="Keyword">
            <form method="POST" action="">
                <h2>Keyword search</h2>
                <label for="title">Title:</label><br>
                <input id="title" type="text" name="title" placeholder="Search for Title" value="<?php echo htmlspecialchars($title); ?>"><br>
                <label for="artist">Artist:</label><br>
                <input id="artist" type="text" name="artist" placeholder="Search for Artist" value="<?php echo htmlspecialchars($artist); ?>"><br>

                <input type="submit" id="SearchKeyword" value="Search">
            </form>
        </div>
        <!-- Fulltext Search -->
        <div id="FullText">
            <form method="POST" action="">
                <h2>Fulltext Search</h2>
                <label for="fullText">Title and/or Artist:</label><br>
                <input id="fullText" type="text" name="fullText" placeholder="Search Title and/or Artist" value="<?php echo htmlspecialchars($fullText); ?>"><br>
                <input type="submit" id="SearchFulltext" value="Search">
            </form>
        </div>

    </div>
      

    <div id="outputField">
        <?php echo $output; ?>
    </div>

</body>
</html>