<?php

echo "Checking GD extension status...\n";

if (extension_loaded('gd')) {
    echo "GD extension is loaded.\n";
} else {
    echo "GD extension is NOT loaded.\n";
}

echo "\nChecking for imagejpeg function (global namespace)...\n";
if (function_exists('imagejpeg')) {
    echo "Function imagejpeg() EXISTS in the global namespace.\n";
} else {
    echo "Function imagejpeg() DOES NOT EXIST in the global namespace.\n";
}

echo "\nChecking for \\imagejpeg function (explicit global namespace)...\n";
if (function_exists('\\imagejpeg')) {
    echo "Function \\imagejpeg() EXISTS (explicit global).\n";
} else {
    echo "Function \\imagejpeg() DOES NOT EXIST (explicit global).\n";
}

echo "\nPHP Version: " . PHP_VERSION . "\n";
echo "Loaded php.ini: " . php_ini_loaded_file() . "\n";

// Check for common GD functions
$gd_functions = [
    'imagecreate',
    'imagecreatetruecolor',
    'imagecolorallocate',
    'imagefill',
    'imagestring',
    'imagepng',
    'imagegif'
];

echo "\nChecking other GD functions (global namespace):\n";
foreach ($gd_functions as $func) {
    if (function_exists($func)) {
        echo "- {$func}(): EXISTS\n";
    } else {
        echo "- {$func}(): DOES NOT EXIST\n";
    }
}

?>