<?php
    
$title = $_POST['title'] ?? '';
$artist = $_POST['artist'] ?? '';
$fullText = $_POST['fullText'] ?? '';

$output = '';

//Full text search
if (!empty($fullText)) {
    $query = [
        'size' => 50,
        'query' => [
            'multi_match' => [
                'query' => $fullText,
                'fields' => ['title', 'artist']
            ]
        ]
    ];
}
//Keyword search
elseif (empty($fullText) && (!empty($title) || !empty($artist))) {
    
    $search = [];

    if (!empty($title)) {
        $search[] = ['term' => ['title.keyword' => $title]];
    }
    if (!empty($artist)) {
        $search[] = ['term' => ['artist.keyword' => $artist]];
    }

    $query = [
        'size' => 50,
        'query' => [
            'bool' => [
                'must' => $search
            ]
        ]
    ];
} elseif (empty($fullText)) {
    $output = "Please enter a song title or artist to search.";
}

if (isset($query)) {
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
            $output .= "<strong>" . htmlspecialchars($row['title'] ?? '') . "</strong><br>";
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
    $output = "Please enter a search term.";
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

    <div id="searchForms" >
        <!-- Keyword Search -->
        <div id="Keyword">
            <form method="POST" action="">
                <label for="keyWord">Keyword Search:</label><br><br>
                <label for="artist">Title:</label><br>
                <input type="text" name="title" id="title" placeholder="Search for Title" value="<?php echo htmlspecialchars($title); ?>"><br>
                <label for="artist">Artist:</label><br>
                <input type="text" name="artist" id="artist" placeholder="Search for Artist" value="<?php echo htmlspecialchars($artist); ?>"><br>

                <input type="submit" value="Search">
            </form>
        </div>
        <!-- Fulltext Search -->
        <div id="FullText">
            <form method="POST" action="">
                <label for="fullText">Full Text Search:</label><br><br>
                <label for="artist">Title and/or Artist:</label><br>
                <input type="text" name="fullText" id="fullText" placeholder="Search Title and/or Artist" value="<?php echo htmlspecialchars($fullText); ?>"><br>
                <input type="submit" value="Search">
            </form>
        </div>

    </div>


    <div id="outputField">
        <?php echo $output; ?>
    </div>
</body>
</html>
