<?php
require __DIR__ . "/../vendor/autoload.php";
if (!empty($_ENV["MUSICCAST_LIVE_TESTS"])) {
    echo "\nWARNING: These tests will make changes to the MusicCast devices on the network:\n";
    $warnings = [
        "Music will play",
        "Volume will be changed"
    ];
    foreach ($warnings as $warning) {
        echo "    * {$warning}\n";
    }
    $sleep = 5;
    echo "\nTests will run in " . $sleep . " seconds";
    for ($i = 0; $i < $sleep; $i++) {
        echo ".";
        sleep(1);
    }
    echo "\n";
}
