<?php

$host = "localhost";
$dbname = "Exjobb1";
$user = "postgres";
$password = "Studier2022!";

$title = $_POST['title'] ?? '';
$artist = $_POST['artist'] ?? '';
$output = '';

// Create connection
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if (!$conn) {
    $output = "Database connection failed.";
} elseif (!empty($title) || !empty($artist)) {
    // Dynamically build the query
    if (!empty($title) && !empty($artist)) {
        $query = 'SELECT * FROM "Songs" WHERE title ILIKE $1 AND artist ILIKE $2';
        $params = array("%$title%", "%$artist%");
    } elseif (!empty($title)) {
        $query = 'SELECT * FROM "Songs" WHERE title = $1';
        $params = array("%$title%");
    } elseif (!empty($artist)) {
        $query = 'SELECT * FROM "Songs" WHERE artist ILIKE $1';
        $params = array("%$artist%");
    }

    $result = pg_query_params($conn, $query, $params);

    if ($result && pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $output .= "<div style='margin-bottom:10px;'>";
            $output .= "<strong>🎵 " . htmlspecialchars($row['title']) . "</strong><br>";
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
} else {
    $output = "Please enter a song title or artist to search.";
}

?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exjobb</title>
    <link rel="stylesheet" href="Exjobb.css">
</head>
<body>
    <h1>Welcome to My Simple Website</h1>
    <p>Type something in the search box below:</p>

    <div class="search-container">
        <form method="POST" action="">
            <label for="title">Song Title:</label><br>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>"><br><br>
    
            <label for="artist">Artist:</label><br>
            <input type="text" name="artist" id="artist" value="<?php echo htmlspecialchars($artist); ?>"><br><br>
    
            <input type="submit" value="Search">
        </form>
    </div>

    <script>
        function searchFunction() {
            let query = document.getElementById("searchInput").value;
            alert("You searched for: " + query);
        }
    </script>
    

    <div id="outputField">
        <?php echo $output; ?>
    </div>

</body>
</html>