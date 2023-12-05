<!DOCTYPE html>
<html>
    <head>
        <Title>Web Crawler</Title>
    </head>

    <body>
        <h1>Web Crawler</h1>

        <br>

        <?php

        include 'functions.php';
            
            $urlQueue = array();

            $seedUrl = "https://www.techtarget.com/whatis/definition/wiki/";

            $maxDepth = 3;

            $urlQueue[] = array('url' => $seedUrl, 'depth' => 0);

            while (!empty($urlQueue)) {
                $current = array_shift($urlQueue);
                $currentUrl = $current['url'];
                $currentDepth = $current['depth'];

                $htmlfile = md5($currentUrl) . '.txt';

                try {

                    // get html content
                    $htmlContent = crawl($currentUrl, $htmlfile, $currentDepth);

                    if ($htmlContent && $currentDepth < $maxDepth) {
                        // extract title and meta description information
                        $info = extractInformation($htmlContent);
                        echo "URL: $currentUrl<br>";
                        echo "Title: {$info['title']}<br>";
                        echo "Meta Description: {$info['metaDescription']}<br>";
                        echo "<br>";

                        // parse and add urls to the queue
                        if ($info['metaDescription'] !== "Meta description not found") {
                            $extractedUrls = parse($htmlContent, $currentUrl, $currentDepth + 1);
                            $urlQueue = array_merge($urlQueue, $extractedUrls);
                        }
                    }

                } catch (Exception $e) {
                    echo "Exception: " . $e->getMessage() . "\n";
                }
            }
            
        ?>

    </body>

</html>