<?php

    # Check has POST
    if (!isset($_POST['url'])) {
        header('location:index.php');
        exit();
    }


    # Explode
    $urls = explode(PHP_EOL, $_POST['url']);
    $page = 0;
    foreach ($urls as $url) {
        $page++;
        echo "<br /><br />หน้าที่: $page <br /><hr />";
        DOM($url);
    }


    function DOM($url) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom -> loadHTMLFile($url);
        intoProduct($dom);
    }

    function intoProduct($dom) {
        $domObjs = getByClass($dom, 'productLink');

        # Variable
        $data_image = array();
        $data_name = array();
        $data_tag = array();
        $data_price = array();
        $data_article = array();
        $data_detail = array();
        $data_color = array();
        $data_metric = array();
        $data_desgin = array();


        foreach ($domObjs as $domObj) {
            $ahref = "http://www.ikea.com" . $domObj->getAttribute('href');

            # DOM Current Page
            $dom2 = new DOMDocument();
            $dom2 -> loadHTMLFile($ahref);
            $finder2 = new DomXPath($dom2);


            // Get all image
            $images = $finder2->query("//div[@class='rightContentContainer']/img");
            foreach ($images as $image) {

                // Create url
                $img_url = "http://www.ikea.com" . $image->getAttribute('src');

                // Push to array
                array_push($data_image, $img_url);

                // Display
                #echo "<img src='$img_url' width='100px' height='120px'/><br />";
            }



            // Get name
            $names = getByClass($dom2, 'productName');
            foreach ($names as $name) {

                // Push array
                array_push($data_name, $name->nodeValue);

                // Display
                #echo "ชื่อ: $name->nodeValue <br />";
            }



            // Get tag
            $tags = getByClass($dom2, 'productType');
            $in_tag = array();
            foreach ($tags as $tag) {

                // Push array
                $tag = $tag->nodeValue;
                $tag = explode(",", $tag);
                foreach ($tag as $value) {
                    array_push($in_tag, $value);
                }
                array_push($data_tag, $in_tag);

            }



            // Get Color
            $data_color = end($data_tag);
            #$data_tag = array_slice($data_tag, 0, -1);



            // Get price
            $prices = getByClass($dom2, 'packagePrice');
            foreach ($prices as $price) {

                // Push array
                array_push($data_price, $price->nodeValue);
                #echo "Price: $price->nodeValue <br />";

            }


            // Get article number
            $articles = $finder2->query("//div[@id='itemNumber']");
            foreach ($articles as $article) {

                // Push array
                array_push($data_$article, $$article->nodeValue);
                #echo "Article Number: $article->nodeValue <br />";

            }


            // Get detail
            $details = getByClass($dom2, 'salesArguments');
            foreach ($details as $detail) {

                // Get Detail again
                $text = explode('.', $detail->nodeValue);
                // Push array
                #echo "Detail: $text[0] <br />";
            }
            // Get Color
            #echo "Color: $data_color <br />";


            // Get dimensions
            $metrics = $finder2->query("//div[@id='metric']");
            foreach ($metrics as $metric) {
                $metric = $metric->nodeValue;
                $metric = explode("cm", $metric);
                echo "Metric: ";
                print_r($metric);
                echo "<br />";
            }

            // Desginer
            $desgins = $finder2->query("//div[@id='designer']");
            foreach ($desgins as $desgin) {
                echo "Desginer: $desgin->nodeValue <br /><hr />";
            }
        }
        echo "<pre>";
        print_r($data_image);
        print_r($data_name);
        print_r($data_tag);
        echo "</pre>";
    }
    function getByClass($dom, $class1, $class2 = NULL) {
        /* Return Element was select by classname */
        $finder = new DomXPath($dom);

        if (!isset($class2)) {
            return $finder->query("//*[contains(@class, '$class1')]");
        } else {
            return $finder->query("//*[contains(@class, '$class1')]/*[contains(@class, '$class2')]");
        }

    }

?>
