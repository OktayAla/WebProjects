<?php
goto JtPWF;
kVwN_:
function getRandomFoodDestinations()
{
    $foodDestinations = array();
    $foodFiles = glob(__DIR__ . "\57\56\56\x2f\160\x61\x67\145\163\57\x6c\145\x7a\172\145\164\55\x64\x75\x72\x61\153\154\x61\162\151\57\52\56\160\150\160");
    shuffle($foodFiles);
    $selectedFood = array_slice($foodFiles, 0, 3);
    foreach ($selectedFood as $file) {
        $foodDestination = extractFoodInfo($file);
        if ($foodDestination) {
            $foodDestinations[] = $foodDestination;
        }
    }
    return $foodDestinations;
}
goto BsCuO;
W_Rbs:
function extractFoodInfo($filePath)
{
    $content = file_get_contents($filePath);
    if (!$content) {
        return false;
    }
    $encoding = mb_detect_encoding($content, array("\x55\x54\106\55\70", "\x49\123\x4f\55\70\70\65\x39\x2d\x39", "\111\123\x4f\x2d\70\70\x35\71\55\61", "\x57\x69\156\144\157\167\163\55\61\x32\65\62"), true);
    if ($encoding && $encoding !== "\125\124\106\55\70") {
        $content = mb_convert_encoding($content, "\125\x54\x46\x2d\70", $encoding);
    } elseif (!$encoding) {
        $content = mb_convert_encoding($content, "\x55\x54\x46\55\70", "\127\x69\x6e\144\157\x77\x73\x2d\61\x32\65\x34");
    }
    preg_match("\x2f\x3c\x68\61\x3e\50\133\x5e\x3c\x5d\53\51\x3c\x5c\57\x68\61\76\57", $content, $nameMatches);
    if (empty($nameMatches)) {
        preg_match("\x2f\x3c\144\151\166\40\x63\x6c\141\163\163\x3d\x22\146\x6f\157\144\55\150\x65\162\157\x2d\x6f\x76\145\x72\x6c\x61\171\x22\x3e\134\x73\x2a\x3c\150\61\x3e\x28\133\x5e\74\135\x2b\x29\74\x5c\x2f\x68\x31\76\x2f", $content, $nameMatches);
    }
    $name = isset($nameMatches[1]) ? trim($nameMatches[1]) : '';
    preg_match("\57\x3c\144\151\166\x20\x63\154\141\x73\x73\x3d\42\x66\x6f\157\x64\x2d\x68\145\x72\x6f\55\157\166\x65\x72\x6c\x61\171\42\x3e\134\163\52\x3c\150\x31\76\x5b\136\74\135\53\74\x5c\x2f\150\61\x3e\134\163\x2a\x3c\160\x3e\50\133\x5e\74\135\x2b\51\74\134\x2f\x70\x3e\57", $content, $descMatches);
    if (empty($descMatches)) {
        preg_match("\x2f\x3c\x64\151\166\40\143\154\x61\163\163\75\42\x66\x6f\x6f\144\55\151\x6e\x74\162\157\42\76\x5c\x73\52\74\x70\76\50\133\136\74\x5d\x2b\x29\74\x5c\x2f\160\76\x2f", $content, $descMatches);
    }
    $description = isset($descMatches[1]) ? trim($descMatches[1]) : '';
    preg_match("\57\x62\x61\143\x6b\x67\x72\157\x75\156\144\55\151\155\141\x67\145\72\40\165\162\x6c\134\50\47\x28\x5b\136\x27\x5d\x2b\x29\x27\134\51\x2f", $content, $imgMatches);
    $imageUrl = isset($imgMatches[1]) ? trim($imgMatches[1]) : '';
    $filename = basename($filePath);
    $detailUrl = "\x2f\164\165\162\x6b\151\x79\x65\147\145\172\x69\x72\145\150\142\145\162\151\57\x70\x61\x67\x65\163\57\154\145\172\172\x65\x74\55\144\165\x72\x61\153\x6c\x61\x72\x69\x2f{$filename}";
    $name = normalizeText($name);
    $description = normalizeText($description);
    return array("\x6e\x61\x6d\145" => $name, "\144\145\163\143\x72\151\160\x74\x69\157\156" => $description, "\x69\x6d\141\x67\x65" => $imageUrl, "\x75\x72\154" => $detailUrl, "\143\x61\164\145\x67\x6f\162\171" => "\154\x65\172\x7a\145\x74\x2d\x64\165\162\141\153\154\141\162\x69");
}
goto jU2SS;
jU2SS:
function normalizeText($text)
{
    if (!mb_check_encoding($text, "\x55\x54\106\55\x38")) {
        $text = mb_convert_encoding($text, "\125\x54\106\55\70", "\x61\165\x74\x6f");
    }
    $replacements = array("\303\x84\302\261" => "\304\261", "\303\x84\302\xb0" => "\304\xb0", "\xc3\x83\302\274" => "\303\274", "\xc3\203\xc5\x93" => "\xc3\234", "\303\203\342\x80\223" => "\303\x96", "\xc3\x83\302\266" => "\xc3\xb6", "\xc3\205\305\270" => "\305\x9f", "\303\x85\xc5\xbe" => "\305\236", "\xc3\204\xc5\270" => "\xc4\237", "\xc3\x84\305\xbe" => "\304\236", "\303\x83\302\xa7" => "\xc3\247", "\303\203\342\x80\xa1" => "\303\x87", "\303\242\xe2\202\xac\xc5\x93" => "\342\200\x9c", "\xc3\xa2\342\x82\xac\xef\xbf\xbd" => "\342\x80\x9d", "\146\x61\162\153\154\357\xbf\xbd" => "\146\x61\162\x6b\154\304\xb1", "\xc3\274\156\x6c\xef\277\xbd" => "\303\274\x6e\x6c\xc3\274", "\x47\357\277\xbd" => "\107\xc3\266", "\144\x65\xef\277\275" => "\144\145\xc4\x9f", "\xef\xbf\275" => "\304\xb1", "\357\xbf\275" => "\303\266", "\xef\277\275" => "\303\274", "\357\277\xbd" => "\304\237");
    return str_replace(array_keys($replacements), array_values($replacements), $text);
}
goto Zlr7D;
BsCuO:
function extractDestinationInfo($filePath, $category)
{
    $content = file_get_contents($filePath);
    if (!$content) {
        return false;
    }
    $encoding = mb_detect_encoding($content, array("\x55\124\106\x2d\70", "\x49\x53\117\x2d\70\x38\65\71\x2d\x39", "\x49\x53\117\55\70\70\65\x39\x2d\x31", "\x57\x69\156\x64\x6f\x77\163\x2d\61\62\x35\x32"), true);
    if ($encoding && $encoding !== "\x55\x54\106\x2d\70") {
        $content = mb_convert_encoding($content, "\125\x54\106\55\x38", $encoding);
    } elseif (!$encoding) {
        $content = mb_convert_encoding($content, "\x55\x54\x46\x2d\70", "\x57\151\156\x64\157\167\163\55\x31\x32\x35\64");
    }
    preg_match("\57\74\x68\61\x20\x63\154\141\163\163\75\42\x68\x65\x72\157\55\164\151\x74\154\145\42\x3e\x28\x5b\136\x3c\135\x2b\x29\x3c\134\x2f\x68\x31\x3e\57", $content, $nameMatches);
    $name = isset($nameMatches[1]) ? trim($nameMatches[1]) : '';
    preg_match("\x2f\x3c\160\40\143\154\x61\x73\x73\75\42\x68\x65\162\157\x2d\x64\145\x73\x63\x72\x69\160\x74\x69\x6f\156\42\76\50\133\136\x3c\x5d\x2b\51\x3c\x5c\x2f\160\76\57", $content, $descMatches);
    $description = isset($descMatches[1]) ? trim($descMatches[1]) : '';
    preg_match("\x2f\x62\x61\x63\153\x67\x72\157\165\x6e\x64\x2d\x69\x6d\141\x67\145\72\40\165\162\154\134\x28\x27\50\133\x5e\x27\x5d\x2b\x29\47\x5c\x29\57", $content, $imgMatches);
    $imageUrl = isset($imgMatches[1]) ? trim($imgMatches[1]) : '';
    $filename = basename($filePath);
    $detailUrl = "\x2f\164\165\x72\153\151\171\145\147\145\x7a\x69\162\145\x68\x62\145\x72\x69\x2f\160\x61\147\145\x73\x2f{$category}\x2f{$filename}";
    $name = normalizeText($name);
    $description = normalizeText($description);
    return array("\156\x61\x6d\145" => $name, "\x64\x65\x73\x63\x72\151\160\x74\151\157\x6e" => $description, "\151\155\141\147\145" => $imageUrl, "\165\x72\x6c" => $detailUrl, "\143\x61\x74\x65\x67\157\162\x79" => $category);
}
goto W_Rbs;
JtPWF:
function getRandomDestinations()
{
    $destinations = array();
    $naturalFiles = glob(__DIR__ . "\x2f\56\56\57\x70\x61\x67\x65\x73\x2f\x64\157\147\x61\x6c\55\147\165\172\145\154\154\x69\153\x6c\x65\162\x2f\x2a\55\144\145\x74\x61\x79\x2e\x70\x68\x70");
    $historicalFiles = glob(__DIR__ . "\57\56\x2e\x2f\x70\x61\147\x65\163\57\164\141\x72\x69\150\x69\x2d\x79\x65\x72\x6c\145\162\x2f\x2a\x2d\x64\x65\164\x61\x79\56\160\150\x70");
    shuffle($naturalFiles);
    shuffle($historicalFiles);
    $selectedNatural = array_slice($naturalFiles, 0, 2);
    $selectedHistorical = array_slice($historicalFiles, 0, 1);
    foreach ($selectedNatural as $file) {
        $destination = extractDestinationInfo($file, "\144\x6f\x67\141\154\x2d\x67\x75\172\x65\x6c\154\151\153\x6c\145\x72");
        if ($destination) {
            $destinations[] = $destination;
        }
    }
    foreach ($selectedHistorical as $file) {
        $destination = extractDestinationInfo($file, "\164\141\162\151\x68\x69\55\171\x65\x72\x6c\145\x72");
        if ($destination) {
            $destinations[] = $destination;
        }
    }
    return $destinations;
}
goto kVwN_;
Zlr7D: ?>