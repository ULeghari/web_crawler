<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Engine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Search Engine</h1>

    <form action="" method="get">
        <label for="searchStr">What are you looking for?:</label>
        <input type="text" id="searchStr" name="searchStr" required>
        <button type="submit">Search</button>
        <label for="caseSensitive">Case Sensitive:</label>
        <input type="checkbox" id="caseSensitive" name="caseSensitive">
        <label for="useRegex">Regular Expression:</label>
        <input type="checkbox" id="useRegex" name="useRegex">
    </form>


    <?php

        include 'functions.php';

        $found = false;
        
        if (isset($_GET['searchStr'])) {
            $searchStr = $_GET['searchStr'];
            $caseSensitive = isset($_GET['caseSensitive']);
            $useRegex = isset($_GET['useRegex']);
        
            // iterate through all the saved text files
            $files = glob('*.txt');
            foreach ($files as $file) {
                $content = file_get_contents($file);
        
                $lines = explode("\n", $content);
        
                // get URL from the first line of the file
                $url = isset($lines[0]) ? trim($lines[0]) : '';
                $content = implode("\n", array_slice($lines, 1));
        
                // if string found in the content
                if (
                    (!$caseSensitive && stripos($content, $searchStr) !== false) || /*for normal searhc*/
                    ($caseSensitive && strpos($content, $searchStr) !== false) ||   /*for case sensitive search*/
                    ($useRegex && preg_match("/$searchStr/", $content))             /*for using regular exression*/
                ) {
                    // extract information from the corresponding file
                    $info = extractInformation($content);
        
                    echo "<div class='search-result'>";
                    echo "<h2 class='search-result-title'><a href=\"$url\">{$info['title']}</a></h2>";
        
                    if (!empty($info['metaDescription'])) {
                        $metaDescription = substr($info['metaDescription'], 0, 150);
                        echo "<p class='search-result-meta'>$metaDescription...</p>";
                    }
        
                    echo "<p class='search-result-url'><a href='$url'>$url</a></p>";
                    echo "</div>";
        
                    $found = true;
                }
            }
        
            // if no match found, display a message
            if ($found == false) {
                echo "<p><b>No match found for $searchStr!  :(</b></p>";
            }
        }
                
    ?>
</body>
</html>
