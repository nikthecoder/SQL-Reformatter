<?php

function isSpecificKeyword($word)
{
    $keyWords = [
        'SELECT',
        'FROM',
        'JOIN',
        'WHERE',
        'AND',
        'OR',
        'HAVING',
        'LIKE',
    ];

    return in_array($word, $keyWords);
}

function isSpecificDoubleKeyword($word)
{
    $keyWords = [
        'INNER JOIN',
        'LEFT JOIN',
        'RIGHT JOIN',
        'ORDER BY',
        'GROUP BY',
    ];

    return in_array($word, $keyWords);
}

function createWhitespaceString($length)
{
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= ' ';
    }

    return $string;
}

function printArrayAsString(array $elements, $indentLength)
{
    $indent = createWhitespaceString($indentLength + 1);

    $elementsAsString      = implode(' ', $elements);
    $commaSeparatedStrings = preg_split('/,/', $elementsAsString);

    $length = count($commaSeparatedStrings);

    if ($length === 1) {
        echo ' ' . $commaSeparatedStrings[0] . PHP_EOL;

        return;
    }

    for ($i = 0; $i < $length; $i++) {

        if ($i === 0) {
            echo ' ' . $commaSeparatedStrings[$i] . ',' . PHP_EOL;
        } else if ($i === ($length - 1)) {
            echo $indent . $commaSeparatedStrings[$i] . PHP_EOL;
        } else {
            echo $indent . $commaSeparatedStrings[$i] . ',' . PHP_EOL;
        }
    }
}

function arrangeWordsInSections(array $words)
{
    $sections   = [];
    $keyword = '';
    $section    = [];

    $length = count($words);

    for ($i = 0; $i < $length; $i++) {

        $currentWord = $words[$i];

        if ($i === ($length - 1)) {
            $nextWord = '';
        } else {
            $nextWord = $words[$i + 1];
        }

        $doubleWord = $currentWord . ' ' . $nextWord;

        if (isSpecificKeyword($currentWord)) {
            $sections[] = [$keyword, $section];
            $keyword = $currentWord;
            $section    = [];
        } else if (isSpecificDoubleKeyword($doubleWord)) {
            $sections[] = [$keyword, $section];
            $keyword = $doubleWord;
            $section    = [];
            $i++;
        } else {
            $section[] = $currentWord;
        }
    }

    $sections[] = [$keyword, $section];

    return $sections;
}

function validateInput($args)
{
    $isValid = false;

    if (count($args) === 2) {
        $isValid = true;
    }

    if (!$isValid) {
        echo 'php query_analyzer <query>' . PHP_EOL;
        die(1);
    }
}

validateInput($argv);

$query = $argv[1];
$query = str_replace(array("\r\n", "\r", "\n"), " ", $query);

$words      = explode(' ', $query);
$querySections = arrangeWordsInSections($words);

echo 'QUERY ANALYSIS:' . PHP_EOL;

foreach ($querySections as $section) {
    $key     = $section[0];
    $content = $section[1];

    $indentLength = strlen($key) + 1;

    if (in_array($key, ['AND', 'OR'])) {
        echo '   ';
    }

    echo ' ' . $key . ' ';
    printArrayAsString($content, $indentLength);
}