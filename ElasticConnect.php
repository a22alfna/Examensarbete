<?php
$title = $_POST['title'] ?? '';
$artist = $_POST['artist'] ?? '';
$output = '';

if (!empty($title) || !empty($artist)) {
    $search = [];

    if (!empty($title)) {
        $search[] = [
            'match' => [
                'title' => $title  
            ]
        ];
    }

    if (!empty($artist)) {
        $search[] = [
            'match' => [
                'artist' => $artist 
            ]
        ];
    }

    // Elasticsearch query
    $query = [
        'size' => 50,
        'query' => [
            'bool' => [
                'must' => $search
            ]
        ]
    ];

    // CURL-anrop till Elasticsearch
    $ch = curl_init('http://localhost:9200/songs_index_new/_search');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data['hits']['hits'])) {
        foreach ($data['hits']['hits'] as $hit) {
            $row = $hit['_source'];
            $output .= "<div style='margin-bottom:10px;'>";
            $output .= "<strong> " . htmlspecialchars($row['title'] ?? '') . "</strong><br>";
            $output .= "Artist: " . htmlspecialchars($row['artist'] ?? '') . "<br>";
            $output .= "Rank: " . htmlspecialchars($row['rank'] ?? '') . "<br>";
            $output .= "Date: " . htmlspecialchars($row['date'] ?? '') . "<br>";
            $output .= "Url: " . htmlspecialchars($row['url'] ?? '') . "<br>";
            $output .= "Region: " . htmlspecialchars($row['region'] ?? '') . "<br>";
            $output .= "Chart: " . htmlspecialchars($row['chart'] ?? '') . "<br>";
            $output .= "Trend: " . htmlspecialchars($row['trend'] ?? '') . "<br>";
            $output .= "Streams: " . htmlspecialchars($row['streams'] ?? '') . "<br>";
            $output .= "</div>";
        }
    } else {
        $output = "No results found.";
    }
} else {
    $output = "Please enter a song title or artist to search.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exjobb – Elasticsearch</title>
    <link rel="stylesheet" href="Exjobb.css">
</head>
<body>
    <div id="welcome">
        <h1>Elasticsearch Search</h1>
        <p>Type something in the search box below:</p>
    </div>

    <div class="search-container">
        <form method="POST" action="">
            <label for="title">Song Title:</label><br>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>"><br><br>

            <label for="artist">Artist:</label><br>
            <input type="text" name="artist" id="artist" value="<?php echo htmlspecialchars($artist); ?>"><br><br>

            <input type="submit" value="Search">
        </form>
    </div>

    <div id="outputField">
        <?php echo $output; ?>
    </div>
</body>
</html>
