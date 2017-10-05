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
            #$data_color = end($data_tag);

              // Display
              #echo "Color: $data_color";
              #$data_tag = array_slice($data_tag, 0, -1);



            // Get price
            $prices = getByClass($dom2, 'packagePrice');
            foreach ($prices as $price) {

                // Push array
                array_push($data_price, $price->nodeValue);

                // Display
                #echo "Price: $price->nodeValue <br />";

            }


            // Get article number
            $articles = $finder2->query("//div[@id='itemNumber']");
            foreach ($articles as $article) {

                // Push array
                array_push($data_article, $article->nodeValue);

                // Display
                #echo "Article Number: $article->nodeValue <br />";

            }


            // Get detail
            $details = getByClass($dom2, 'salesArguments');
            foreach ($details as $detail) {

                // Get Detail again
                $text = explode('.', $detail->nodeValue);

                // Push array
                array_push($data_detail, $text[0]);

                // Display
                #echo "Detail: $text[0] <br />";

            }


            // Get dimensions
            $metrics = $finder2->query("//div[@id='metric']");
            $in_metric = array();
            foreach ($metrics as $metric) {

                // Push array
                $metric = $metric->nodeValue;
                $metric = explode("cm", $metric);
                foreach ($metric as $value) {
                    array_push($in_metric, $value);
                }
                array_push($data_metric, $in_metric);

                // Display
                /*
                echo "Metric: ";
                print_r($metric);
                echo "<br />";
                */
            }

            // Desginer
            $desgins = $finder2->query("//div[@id='designer']");
            foreach ($desgins as $desgin) {

                // Arry push
                array_push($data_desgin, $desgin->nodeValue);


                // Display
                #echo "Desginer: $desgin->nodeValue <br /><hr />";
            }
        }

        // Display Overall
        /*
        echo "<pre>";
        print_r($data_image);
        print_r($data_name);
        print_r($data_tag);
        #print_r($data_color);
        print_r($data_price);
        print_r($data_article);
        print_r($data_detail);
        print_r($data_metric);
        print_r($data_desgin);
        echo "</pre>";
        */

        // Create group array
        for ($i=0; $i < count($data_name) ; $i++) {
            $data[$i] = array(
                $data_name[$i] = array(
                    "image" => $data_image[$i],
                    "name" => $data_name[$i],
                    "tags" => $data_tag[$i],
                    "price" => $data_price[$i],
                    "article" => $data_article[$i],
                    "detail" => $data_detail[$i],
                    "metrics" => $data_metric[$i],
                    "desgin" => $data_desgin[$i]
                )
            );
        }

        // Map array
        $data = array_map('current', $data);

        // Display
        echo "<pre>";
        #print_r($data);
        echo "</pre>";





        // Connect sql
        $conn = mysqli_connect('localhost', 'root', '', 'ikea');
        // Insert to sql
        foreach ($data as $current_data) {
            $image = $current_data['image'];
            $name = $current_data['name'];

            $tag1 = $current_data['tags'][0];
            if (isset($current_data['tags'][1])) {
                $tag2 = $current_data['tags'][1];
            } else {
                $tag2 = NULL;
            }
            if (isset($current_data['taGs'][2])) {
                $tag3 = $current_data['tags'][3];
            } else {
                $tag3 = NULL;
            }

            $price = $current_data['price'];
            $article = $current_data['article'];
            $detail = $current_data['detail'];

            $metric1 = $current_data['metrics'][0];
            if (isset($current_data['metrics'][1])) {
                $metric2 = $current_data['metrics'][1];
            } else {
                $metric2 = NULL;
            }
            if (isset($current_data['metrics'][2])) {
                $metric3 = $current_data['metrics'][2];
            } else {
                $metric3 = NULL;
            }

            $desgin = $current_data['desgin'];

            $name = clearSpace($conn, $name);
            $detail = clearSpace($conn, $detail);
            $price = clearSpace($conn, $price);
            $tag1 = clearSpace($conn, $tag1);
            $tag2 = clearSpace($conn, $tag2);
            $tag3 = clearSpace($conn, $tag3);
            $metric1 = clearSpace($conn, $metric1);
            $metric2 = clearSpace($conn, $metric2);
            $metric3 = clearSpace($conn, $metric3);

            echo "Image: <img src='$image' width='120px' height='150px'> <br>";
            echo "Name: $name <br>";
            echo "Tags: $tag1 / $tag2 / $tag3 <br>";
            echo "Price: $price <br>";
            echo "Article: $article <br>";
            echo "Detail : $detail <br>";
            echo "Metrics: $metric1 / $metric2 / $metric3 <br>";
            echo "Desgin: $desgin <br><hr>";

            // Check exit
            $query = mysqli_query($conn, "select * from products where article = '$article'") or die(mysqli_error($conn));
            if (!mysqli_fetch_assoc($query)) {
            $query = mysqli_query($conn, "
                insert into products (image, name, price, article, detail, desgin, tag1, tag2, tag3, metric1, metric2, metric3)
                values ('$image', '$name', '$price', '$article', '$detail', '$desgin', '$tag1', '$tag2', '$tag3', '$metric1', '$metric2', '$metric3')") or die(mysqli_error($conn));
            }
        }


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

     function clearSpace($conn, $text) {
        return mysqli_real_escape_string($conn, str_replace(",", "", trim($text)));
     }

?>
