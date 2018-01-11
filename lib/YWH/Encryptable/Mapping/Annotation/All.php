<?php

foreach (glob(__DIR__ . "/*.php") as $filename) {
    if (basename($filename, '.php') === 'All') {
        continue;
    }
    include_once $filename;
}
