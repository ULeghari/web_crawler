<!DOCTYPE html>
<html>
    <head>
        <Title>Web Crawler</Title>
    </head>

    <body>
        <br>

        <?php

            // crawl functioon
            function crawl($url, $filename, $depth) {

                $robots = rtrim($url, '/') . '/robots.txt';
                //$robotstxt = @file_get_contents($robots);

                //if ($robotstxt===false) {
                    // no robots.txt file, it is safe
                //}

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
                $htmlContent = curl_exec($ch);
            
                // error handling
                if (curl_errno($ch)) {
                    echo "Error crawling $url: " . curl_error($ch) . "\n";
                    return null;
                }
            
                curl_close($ch);
            
                // add url at top of file
                file_put_contents($filename, $url . "\n", FILE_APPEND);
                file_put_contents($filename, $htmlContent, FILE_APPEND);
            
                return $htmlContent;
            }
            

            // parse function to extract urls in baaseurl and add to array
            function parse($html, $baseUrl, $depth) {
                $dom = new DOMDocument;
                @$dom->loadHTML($html);
                $xpath = new DOMXPath($dom);
                $links = $xpath->query('//a/@href');
                
                $extractedUrls = array();
                foreach ($links as $link) {
                    $url = urljoin($baseUrl, $link->nodeValue);
                    if ($url) {
                        $extractedUrls[] = array('url' => $url, 'depth' => $depth);
                    }
                }
                
                return $extractedUrls;
            }


            // extract the relevenat information (title and metadescripitn) from html function
            function extractInformation($html) {
                $dom = new DOMDocument;
                @$dom->loadHTML($html);

                // get title of url
                $titleNodeList = $dom->getElementsByTagName('title');
                $title = $titleNodeList->length > 0 ? $titleNodeList->item(0)->nodeValue : null;

                // get meta description of html
                $metaDescriptionNodeList = $dom->getElementsByTagName('meta');
                $metaDescription = null;

                foreach ($metaDescriptionNodeList as $metaTag) {
                    if ($metaTag->getAttribute('name') === 'description') {
                        $metaDescription = $metaTag->getAttribute('content');
                        break;
                    }
                }

                return array("title" => $title, "metaDescription" => $metaDescription);
            }
            
            function urljoin($base, $rel) {
                $rel = trim($rel, '/');
                $url = $rel;
            
                // if absolute URL, return it
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    return $url;
                }
            
                // if relative URL, join it with base URL
                $baseInfo = parse_url($base);
                if ($baseInfo) {
                    $path = isset($baseInfo['path']) ? $baseInfo['path'] : '';
                    $url = rtrim($baseInfo['scheme'] . '://' . $baseInfo['host'] . $path, '/') . '/' . ltrim($url, '/');
                }
            
                return $url;
            }
            
        ?>

    </body>

</html>