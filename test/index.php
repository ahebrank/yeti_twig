<?php

require_once('../src/Yeti.php');
use Newcity\Yeti;

echo Yeti::replace_img('<img src="http://placehold.it/300x300" alt="test replacement">');

print_r(Yeti::parse_path("https://example.com/800/600"));
print_r(Yeti::parse_path("https://example.com/800x600"));