<?php

function validPrefix($prefixes, $search) {
    foreach ($prefixes as $prefix) {
        if (stripos($search, $prefix) !== FALSE) {
            return true;
        }
    }
    return false;
}

function replace_content_inside_delimiters($start, $end, $new, $source) {
    return preg_replace('#('.preg_quote($start).')(.*?)('.preg_quote($end).')#si', '$1'.$new.'$3', $source);
}

function replace_content_using_delimiters($start, $end, $new, $source) {
    return preg_replace('#('.preg_quote($start).')(.*?)('.preg_quote($end).')#si', $new, $source);
}


function wikipediaSearch($search) {

    // $search = "Wie is Harrison Ford?"; 

    $prefixes = ["wie is", "wat is", "wie was", "wat was", "wat weet je over", "wat weet je van", "vertel meer over"];
    $remove = [". ", ", ", "?", "!", "de ", "het ", "een "];
    $source[0] = "Wikipedia zegt daar het volgende over: ";
    $source[1] = "Wikipedia heeft daar meerdere antwoorden op, dit is het eerste resultaat: ";

    if (validPrefix($prefixes, $search)) {

        $search = str_ireplace($remove, "", $search);
        $search = str_ireplace($prefixes, "", $search);
        $search = trim(strtolower($search));
    
        // echo $search."<br/>";

        $maxLength = 100;
        $needle = ".";

        $endPoint = "https://nl.wikipedia.org/w/api.php";
        $params = [
            "action" => "opensearch",
            "redirects" => "resolve",
            "search" => $search,
            "profile" => "strict",
            "limit" => "5",
            "format" => "json"
        ];

        $url = $endPoint . "?" . http_build_query($params);
        $ch = curl_init($url);
        // return $url; //debug
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        // return($output); //debug

        $result = json_decode($output, true);
        // var_dump($result);
        if (count($result[1]) == 0) {
            return "Het spijt me, ik weet daar het antwoord nog niet op.";
        }
        elseif (count($result[1]) == 1) {
            $attr = $source[0];
        } else {
            $attr = $source[1];
        }
        // echo "Found unique hit!<br />";
        $params = array();
         $params = [
            "action" => "query",
            "prop" => "extracts",
            "format" => "json",
            "exlimit" => "1",
            "exintro" => "explaintext",
            "exsectionformat" => "raw",
            "titles" => $result[1][0]    
        ]; 
        $url = $endPoint . "?" . http_build_query($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($output, true);
        $result = array_shift($result["query"]["pages"]);
        // var_dump($result);   
        $extract = $result["extract"];
        if (strlen($extract) > $maxLength && substr_count($extract, $needle) > 1) {
            $pos1 = strpos($extract, $needle);
            $pos2 = strpos($extract, $needle, $pos1 + strlen($needle));
            $extract = substr($extract, 0, $pos2+1);
        }
        if (stripos($extract, "kan verwijzen naar") !== FALSE) {
            // situation that I cannot handle at the moment
            // sometimes Wikipedia links to a redirect page
            return "Het spijt me, daar weet ik het antwoord nog niet op. Kun je de vraag specifieker stellen?";
        }
        // $extract = replace_content_using_delimiters(" (",")","",$extract);
        return $attr.strip_tags($extract); // return the extract
    } else {    
        return "Het spijt me, daar weet ik het antwoord nog niet op en ik kon het ook niet op Wikipedia vinden.";
    }  
}
    
?>
