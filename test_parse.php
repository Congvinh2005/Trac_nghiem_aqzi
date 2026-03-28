<?php
// Test parse of docx file
$path = __DIR__ . '/CHương 1 - 6.docx';

// Try to find file
if (!file_exists($path)) {
    $files = glob(__DIR__ . '/*.docx');
    if (count($files) > 0) {
        $path = $files[0];
    } else {
        echo "No .docx file found\n";
        exit;
    }
}

echo "File: $path\n";
echo "Size: " . filesize($path) . "\n\n";

// Extract text from docx
$zip = new ZipArchive();
if ($zip->open($path) !== TRUE) {
    echo "Cannot open zip\n";
    exit;
}

$xmlContent = $zip->getFromName('word/document.xml');
$zip->close();

preg_match_all('/<w:p[ >].*?<\/w:p>/s', $xmlContent, $paraMatches);
$lines = [];
foreach ($paraMatches[0] as $paraXml) {
    preg_match_all('/<w:t[^>]*>([^<]*)<\/w:t>/s', $paraXml, $textMatches);
    $paraText = trim(implode('', $textMatches[1]));
    if (!empty($paraText)) {
        $lines[] = $paraText;
    }
}
$content = implode("\n", $lines);
$content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');

echo "=== RAW LINES (first 30) ===\n";
$rawLines = explode("\n", $content);
for ($j = 0; $j < min(30, count($rawLines)); $j++) {
    echo "[$j] " . $rawLines[$j] . "\n";
}

echo "\n=== PARSING ===\n";

// Parse
$content = str_replace("\t", ' ', $content);
$content = preg_replace('/[ \t]+/', ' ', $content);
$rawlines = preg_split('/\r\n|\r|\n/', $content);
$cleaned = [];
foreach ($rawlines as $l) {
    $l = trim($l);
    if (!empty($l)) $cleaned[] = $l;
}

$chapter_pattern  = '/^\s*(Chương|Chapter|Bài|Part|Phần)\s*\d+/iu';
$question_pattern = '/^\s*(\d+)\s*[.\/):-]\s*(.+)/u';
$answer_pattern   = '/^\s*([A-Da-d])\s*[.\/):-]\s*(.+)/u';

$q_counter = 0;
$i = 0;
$n = count($cleaned);

while ($i < $n) {
    $line = $cleaned[$i];
    if (preg_match($chapter_pattern, $line)) { $i++; continue; }
    
    $q_text = null;
    if (preg_match($question_pattern, $line, $m)) {
        $q_text = trim($m[2]);
        $i++;
    } elseif (preg_match('/\?\s*$/u', $line) && !preg_match($answer_pattern, $line)) {
        $q_text = $line;
        $i++;
    } else {
        $i++;
        continue;
    }

    $answers_raw = [];
    $letters = ['A', 'B', 'C', 'D'];
    while ($i < $n && count($answers_raw) < 4) {
        $al = $cleaned[$i];
        if (preg_match($chapter_pattern, $al)) break;
        if (preg_match($question_pattern, $al) && count($answers_raw) > 0) break;
        if (preg_match('/\?\s*$/u', $al) && !preg_match($answer_pattern, $al) && count($answers_raw) > 0) break;
        if (preg_match($answer_pattern, $al, $am)) {
            $answers_raw[] = trim($am[2]);
        } else {
            $answers_raw[] = $al;
        }
        $i++;
    }
    if (count($answers_raw) < 2) continue;
    while (count($answers_raw) < 4) $answers_raw[] = 'N/A';
    
    $q_counter++;
    echo "Câu $q_counter: $q_text\n";
    foreach ($answers_raw as $idx => $a) {
        echo "  {$letters[$idx]}. $a\n";
    }
    echo "\n";
}
echo "Total: $q_counter questions\n";
