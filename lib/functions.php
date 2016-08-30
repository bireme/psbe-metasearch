<?php

// translation
function translate($label, $group=NULL) {
    global $texts, $lang;

    // labels on texts.ini must be array key without spaces
    $label_norm = preg_replace('/[&,\'\s]+/', '_', $label);

    if($group == NULL) {
        if(isset($texts[$label_norm]) and $texts[$label_norm] != "") {
            return $texts[$label_norm];
        }
    } else {
        if(isset($texts[$group][$label_norm]) and $texts[$group][$label_norm] != "") {
            return $texts[$group][$label_norm];
        }
    }

    // case translation not found return original label ucfirst
    return ucfirst($label);
}

// return if a label has translation at texts.ini
function has_translation($label, $group=NULL) {
    global $texts, $lang;

    // labels on texts.ini must be array key without spaces
    $label_norm = preg_replace('/[&,\'\s]+/', '_', $label);

    if($group == NULL) {
        return (isset($texts[$label_norm]) and $texts[$label_norm] != "");
    } else {
        return (isset($texts[$group][$label_norm]) and $texts[$group][$label_norm] != "");
    }
}


// funcao retirada da pagina http://www.php.net/utf8_encode
function isUTF8($string){
    if (is_array($string)) {
        $enc = implode('', $string);
        return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    }else{
        return (utf8_encode(utf8_decode($string)) == $string);
    }
}

// function to work when PHP directive magic_quotes_gpc is OFF
function addslashes_array($a){
    if(is_array($a)){
        foreach($a as $n=>$v){
            $b[$n]=addslashes_array($v);
        }
        return $b;
    }else{
        if ($a != ''){
            return addslashes($a);
        }
    }
}

// remove accents of a UTF-8 string
function remove_accents($string) {
    if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

    $chars = array(
    // Decompositions for Latin-1 Supplement
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
    // Euro Sign
    chr(226).chr(130).chr(172) => 'E',
    // GBP (Pound) Sign
    chr(194).chr(163) => '');

    $string = strtr($string, $chars);

    return $string;
}

function normalize_line_end($s) {
    define('CR', "\r");          // Carriage Return: Mac
    define('LF', "\n");          // Line Feed: Unix
    define('CRLF', "\r\n");      // Carriage Return and Line Feed: Windows
    define('BR', '<br />' . LF); // HTML Break

    // Normalize line endings using Global
    // Convert all line-endings to Windows format
    $s = str_replace(CR, CRLF, $s);
    $s = str_replace(LF, CRLF, $s);

    // Don't allow out-of-control blank lines
    $s = preg_replace("/\n{2,}/", CRLF, $s);

    return $s;
}

function xml_attribute($object, $attribute)
{
    if(isset($object[$attribute]))
        return (string) $object[$attribute];
}

/* Twig Extensions */
function custom_template($filename) {
    if( file_exists(CUSTOM_TEMPLATE_PATH . $filename) ) {
        return str_replace(TEMPLATE_PATH, "", CUSTOM_TEMPLATE_PATH) . $filename;
    }

    return $filename;
}

function occ($params) {
    $separator = ', ';

    extract($params);

    if ( is_array($element) ){
        for ($occ = 0; $occ <  count($element); $occ++) {
            if ($occ > 0){
                $output .= $separator . " ";
            }
            if ( $translate == true ){
               $output .= translate($element[$occ], $group);
            }else{
                $output .= $element[$occ];
            }
        }
    }else{
        if ( $translate == true ){
            $output = translate($element, $group);
        }else{
            $output = $element[$occ];
        }
    }

    return $output;

}

function filter_substring_after($text, $needle = '-'){
    if (strpos($text, $needle) !== false){
        return substr($text, strpos($text, $needle)+strlen($needle));
    }else{
        return "";
    }
}

function filter_substring_before($text, $needle = '-'){
    if (strpos($text, $needle) !== false){
        return substr($text, 0,strpos($text, $needle));
    }else{
        return "";
    }
}

function filter_contains($text, $needle){
    return strpos($text, $needle) !== false;
}

function filter_starts_with($text, $needle){
    $text = trim($text);
    $needle = trim($needle);

    $length = strlen($needle);

    return (substr($text, 0, $length) === $needle);
}


function filter_truncate($text, $length = 30, $preserve = false, $separator = '...') {
    if (strlen($text) > $length) {
        if ($preserve) {
            if (false !== ($breakpoint = strpos($text, ' ', $length))) {
                $length = $breakpoint;
            }
        }
        return substr($text, 0, $length) . $separator;
    }

    return $text;
}

function filter_slugify($text) {
    // replace non letter or digits by -
    $text = remove_accents($text);
    $text = preg_replace('/[^a-z0-9]/i', '-', $text);

    // trim
    $text = trim($text, '-');

    // lowercase
    $text = strtolower($text);

    return $text;
}

function filter_subfield($text, $id) {
    // check for old language code compatibility (pt=p, es=e, en=i)
    if (strlen($id) == 2){
        $id = ( $id == 'en' ? 'i' : substr($id,0,1) );
    }

    $subfields = array();

    $sub_list = preg_split('/\^/', $text);
    foreach($sub_list as $sub){
        $sub_id = substr($sub,0,1);
        $subfields[$sub_id] = substr($sub,1);
    }

    return $subfields[$id];
}


function find_in_array($array, $key, $val) {
    foreach ($array as $item){
        if (isset($item[$key]) && $item[$key] == $val){
            return true;
        }
    }
    return false;
}

function validate_token($access_token) {
    /*
    Verifies that an access-token is valid
    Returns false on fail, and an e-mail on success
    */
    global $config;

    $valid_token = false;
    $oauth2_userinfo_url = $config['oauth2_userinfo_url'];
    $opts = array(
      'http'=>array(
        'method'=>"GET",
        'header'=>"Authorization: Bearer " . $access_token . "\r\n"
      )
    );
    $context = stream_context_create($opts);

    // open request using the HTTP headers set above
    $http_request = @file_get_contents($oauth2_userinfo_url, false, $context);

    //print_r($http_response_header);

    // check for response header 200 OK
    if (strpos($http_response_header[0],' 200 ') !== false) {
        $user_info = json_decode($http_request, true);

        // check for email attribute for validate token
        if (array_key_exists('email', $user_info)){
            $valid_token = true;
        }
    }

    return $valid_token;
}

function post_it($url, $params){

    parse_str($params, $data);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;

}

?>
