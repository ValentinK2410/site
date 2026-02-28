<?php
/**
 * Парсер CSS для извлечения точных значений типографики Apple Newsroom
 */

// Загружаем CSS файлы
$cssFiles = [
    'site' => file_get_contents('/Users/valentink2410/.cursor/projects/Users-valentink2410-PhpstormProjects-site/agent-tools/a8459c76-81d7-48f0-b053-2f177b0d8ce7.txt'),
    'tiles' => file_get_contents('/Users/valentink2410/.cursor/projects/Users-valentink2410-PhpstormProjects-site/agent-tools/9c98a109-9422-4531-b32f-c12292ee2ae9.txt'),
    'landing' => file_get_contents('/Users/valentink2410/.cursor/projects/Users-valentink2410-PhpstormProjects-site/agent-tools/23724fce-10d7-46a7-b69d-c15ac702b896.txt'),
];

// Функция для извлечения CSS правил по селектору
function extractCSSRules($css, $selector) {
    $pattern = '/' . preg_quote($selector, '/') . '\s*\{([^}]+)\}/';
    if (preg_match($pattern, $css, $matches)) {
        return $matches[1];
    }
    return null;
}

// Функция для извлечения конкретного свойства
function extractProperty($rules, $property) {
    $pattern = '/' . preg_quote($property, '/') . '\s*:\s*([^;]+);/';
    if (preg_match($pattern, $rules, $matches)) {
        return trim($matches[1]);
    }
    return null;
}

// Функция для поиска всех вхождений свойства в CSS
function findAllProperties($css, $property) {
    $pattern = '/' . preg_quote($property, '/') . '\s*:\s*([^;]+);/';
    preg_match_all($pattern, $css, $matches);
    return array_unique($matches[1]);
}

echo "=== АНАЛИЗ CSS APPLE NEWSROOM ===\n\n";

// 1. Поиск font-family
echo "1. FONT-FAMILY VALUES:\n";
foreach ($cssFiles as $name => $css) {
    $fontFamilies = findAllProperties($css, 'font-family');
    if (!empty($fontFamilies)) {
        echo "  Из файла {$name}.css:\n";
        foreach (array_slice($fontFamilies, 0, 5) as $ff) {
            echo "    - " . $ff . "\n";
        }
    }
}

// 2. Поиск body стилей
echo "\n2. BODY STYLES:\n";
foreach ($cssFiles as $name => $css) {
    if (preg_match('/body\s*\{([^}]+)\}/', $css, $matches)) {
        echo "  Из файла {$name}.css:\n";
        echo "    " . substr($matches[1], 0, 200) . "...\n";
    }
}

// 3. Поиск размеров шрифтов
echo "\n3. FONT-SIZE VALUES (первые 20):\n";
$allFontSizes = [];
foreach ($cssFiles as $name => $css) {
    $sizes = findAllProperties($css, 'font-size');
    $allFontSizes = array_merge($allFontSizes, $sizes);
}
$allFontSizes = array_unique($allFontSizes);
sort($allFontSizes);
foreach (array_slice($allFontSizes, 0, 20) as $size) {
    echo "  - " . $size . "\n";
}

// 4. Поиск font-weight
echo "\n4. FONT-WEIGHT VALUES:\n";
$allWeights = [];
foreach ($cssFiles as $name => $css) {
    $weights = findAllProperties($css, 'font-weight');
    $allWeights = array_merge($allWeights, $weights);
}
$allWeights = array_unique($allWeights);
sort($allWeights);
foreach ($allWeights as $weight) {
    echo "  - " . $weight . "\n";
}

// 5. Поиск letter-spacing
echo "\n5. LETTER-SPACING VALUES:\n";
$allSpacing = [];
foreach ($cssFiles as $name => $css) {
    $spacing = findAllProperties($css, 'letter-spacing');
    $allSpacing = array_merge($allSpacing, $spacing);
}
$allSpacing = array_unique($allSpacing);
foreach (array_slice($allSpacing, 0, 15) as $sp) {
    echo "  - " . $sp . "\n";
}

// 6. Поиск цветов
echo "\n6. COLOR VALUES (первые 20):\n";
$allColors = [];
foreach ($cssFiles as $name => $css) {
    $colors = findAllProperties($css, 'color');
    $allColors = array_merge($allColors, $colors);
}
$allColors = array_unique($allColors);
foreach (array_slice($allColors, 0, 20) as $color) {
    echo "  - " . $color . "\n";
}

// 7. Поиск background-color
echo "\n7. BACKGROUND-COLOR VALUES (первые 15):\n";
$allBgColors = [];
foreach ($cssFiles as $name => $css) {
    $bgColors = findAllProperties($css, 'background-color');
    $allBgColors = array_merge($allBgColors, $bgColors);
}
$allBgColors = array_unique($allBgColors);
foreach (array_slice($allBgColors, 0, 15) as $bgColor) {
    echo "  - " . $bgColor . "\n";
}

// 8. Поиск line-height
echo "\n8. LINE-HEIGHT VALUES (первые 20):\n";
$allLineHeights = [];
foreach ($cssFiles as $name => $css) {
    $lineHeights = findAllProperties($css, 'line-height');
    $allLineHeights = array_merge($allLineHeights, $lineHeights);
}
$allLineHeights = array_unique($allLineHeights);
foreach (array_slice($allLineHeights, 0, 20) as $lh) {
    echo "  - " . $lh . "\n";
}

// 9. Поиск специфичных классов
echo "\n9. СПЕЦИФИЧНЫЕ КЛАССЫ:\n";
$specificClasses = [
    '.tile-headline',
    '.tile-label',
    '.tile-date',
    '.section-content',
    'nav',
    'article',
];

foreach ($specificClasses as $class) {
    foreach ($cssFiles as $name => $css) {
        if (strpos($css, $class) !== false) {
            echo "  Класс {$class} найден в {$name}.css\n";
            break;
        }
    }
}

echo "\n=== АНАЛИЗ ЗАВЕРШЕН ===\n";
