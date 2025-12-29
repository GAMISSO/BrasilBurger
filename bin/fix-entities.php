<?php
$dir = __DIR__ . '/../src/Entity';
$files = glob($dir . '/*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    $new = str_replace('\\$', '$', $content);
    if ($new !== $content) {
        file_put_contents($file, $new);
        echo "Fixed: $file\n";
    }
}
echo "Done.\n";
