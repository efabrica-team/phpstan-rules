<?php

// TODO pass somehow dirs to include if don't want to parse all

$rootDir = getcwd();

// credits: https://stackoverflow.com/questions/22761554/how-to-get-all-class-names-inside-a-particular-namespace
$allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir, FilesystemIterator::KEY_AS_PATHNAME|FilesystemIterator::CURRENT_AS_FILEINFO|FilesystemIterator::SKIP_DOTS));
return new RegexIterator($allFiles, '/\.phpstorm.meta.php/');
