#!/usr/bin/php
<?php
$root = dirname(dirname(dirname(__FILE__)));

$dirs = array(
  $root . '/app/controllers',
  $root . '/lib/Crucial'
);

$output = $root . '/www/docs';

// make a directory for output
mkdir($output);

// run the phpdoc command
$cmd = sprintf('phpdoc project:run -d "%s" -t "%s" --sourcecode', implode(',', $dirs), $output);
echo 'About to run: ' . $cmd . PHP_EOL;
echo '-----------------'. PHP_EOL;
`$cmd`;

// tar up and remove the docs folder
chdir($root . '/www');
`tar cvzf docs.tgz docs/`;
`rm -rf docs/`;