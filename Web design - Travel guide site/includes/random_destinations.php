<?php

/**
 * @return array
 */
function getRandomDestinations() {
    $destinations = [];
    
    $naturalFiles = glob(__DIR__ . '/../pages/dogal-guzellikler/*-detay.php');
    $historicalFiles = glob(__DIR__ . '/../pages/tarihi-yerler/*-detay.php');
    
    shuffle($naturalFiles);
    shuffle($historicalFiles);
    
    $selectedNatural = array_slice($naturalFiles, 0, 2);
    $selectedHistorical = array_slice($historicalFiles, 0, 1);
    
    foreach ($selectedNatural as $file) {
        $destination = extractDestinationInfo($file, 'dogal-guzellikler');
        if ($destination) {
            $destinations[] = $destination;
        }
    }
    
    foreach ($selectedHistorical as $file) {
        $destination = extractDestinationInfo($file, 'tarihi-yerler');
        if ($destination) {
            $destinations[] = $destination;
        }
    }
    
    return $destinations;
}

/**
 * @return array
 */
function getRandomFoodDestinations() {
    $foodDestinations = [];
    
    $foodFiles = glob(__DIR__ . '/../pages/lezzet-duraklari/*.php');
    
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

/**
 * @param string $filePath
 * @param string $category
 * @return array|false 
 */
function extractDestinationInfo($filePath, $category) {
    // Read file content
    $content = file_get_contents($filePath);
    if (!$content) {
        return false;
    }
    
    preg_match('/<h1 class="hero-title">([^<]+)<\/h1>/', $content, $nameMatches);
    $name = isset($nameMatches[1]) ? trim($nameMatches[1]) : '';
    
    preg_match('/<p class="hero-description">([^<]+)<\/p>/', $content, $descMatches);
    $description = isset($descMatches[1]) ? trim($descMatches[1]) : '';
    
    preg_match('/background-image: url\(\'([^\']+)\'\)/', $content, $imgMatches);
    $imageUrl = isset($imgMatches[1]) ? trim($imgMatches[1]) : '';
    
    $filename = basename($filePath);
    $detailUrl = "/turkiyegezirehberi/pages/{$category}/{$filename}";
    
    return [
        'name' => $name,
        'description' => $description,
        'image' => $imageUrl,
        'url' => $detailUrl,
        'category' => $category
    ];
}

/**
 * @param string $filePath
 * @return array|false 
 */
function extractFoodInfo($filePath) {
    // Read file content
    $content = file_get_contents($filePath);
    if (!$content) {
        return false;
    }
    
    preg_match('/<h1>([^<]+)<\/h1>/', $content, $nameMatches);
    if (empty($nameMatches)) {
        preg_match('/<div class="food-hero-overlay">\s*<h1>([^<]+)<\/h1>/', $content, $nameMatches);
    }
    $name = isset($nameMatches[1]) ? trim($nameMatches[1]) : '';
    
    preg_match('/<div class="food-hero-overlay">\s*<h1>[^<]+<\/h1>\s*<p>([^<]+)<\/p>/', $content, $descMatches);
    if (empty($descMatches)) {
        preg_match('/<div class="food-intro">\s*<p>([^<]+)<\/p>/', $content, $descMatches);
    }
    $description = isset($descMatches[1]) ? trim($descMatches[1]) : '';
    
    preg_match('/background-image: url\(\'([^\']+)\'\)/', $content, $imgMatches);
    $imageUrl = isset($imgMatches[1]) ? trim($imgMatches[1]) : '';
    
    $filename = basename($filePath);
    $detailUrl = "/turkiyegezirehberi/pages/lezzet-duraklari/{$filename}";
    
    return [
        'name' => $name,
        'description' => $description,
        'image' => $imageUrl,
        'url' => $detailUrl,
        'category' => 'lezzet-duraklari'
    ];
}
?>