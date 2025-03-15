<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function getNobetciEczaneler() {
    $url = "https://www.denizli.bel.tr/Default.aspx?k=NobetciEczaneler";
    $html = @file_get_contents($url);

    if ($html === FALSE) {
        error_log("Hata: URL'den veri çekilemedi.");
        return ['error' => 'Veri çekilemedi'];
    }

    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $xpath = new DOMXPath($dom);

    $eczaneler = [
        'merkezefendi' => [],
        'pamukkale' => []
    ];

    function parseEczane($xpath, $path) {
        $node = $xpath->query($path)->item(0);
        if (!$node) {
            error_log("Hata: Eczane düğümü bulunamadı: " . $path);
            return null;
        }

        $isim = trim($xpath->query('./b/text()', $node)->item(0)->nodeValue);

        $adres = '';
        $ilce = '';
        $telefon = '';
        $childNodes = $node->childNodes;

        foreach ($childNodes as $child) {
            if ($child->nodeType == XML_TEXT_NODE) {
                $text = trim($child->nodeValue);
                if (!empty($text)) {
                    if (empty($adres)) {
                        $adres = $text;
                    } elseif (empty($ilce)) {
                        $ilce = $text;
                    } elseif (empty($telefon)) {
                        $telefon = $text;
                    }
                }
            }
        }

        return [
            'isim' => $isim,
            'adres' => $adres,
            'ilce' => $ilce,
            'telefon' => $telefon
        ];
    }

    // Merkezefendi
    for ($i = 3; $i <= 7; $i++) {
        $path = "//*[@id='ctl14_rightcontent']/div[" . $i . "]/div";
        $eczane = parseEczane($xpath, $path);
        if ($eczane) {
            $eczaneler['merkezefendi'][] = $eczane;
        } else {
            error_log("Hata: Merkezefendi eczane bilgisi çekilemedi: " . $i);
        }
    }

    // Pamukkale
    for ($i = 8; $i <= 11; $i++) {
        $path = "//*[@id='ctl14_rightcontent']/div[" . $i . "]/div";
        $eczane = parseEczane($xpath, $path);
        if ($eczane) {
            $eczaneler['pamukkale'][] = $eczane;
        } else {
            error_log("Hata: Pamukkale eczane bilgisi çekilemedi: " . $i);
        }
    }

    return $eczaneler;
}

$data = getNobetciEczaneler();

if (isset($data['error'])) {
    http_response_code(500);
    echo json_encode(['error' => $data['error']], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
?>