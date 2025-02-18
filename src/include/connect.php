<?php

$settings = parse_ini_file("settings.ini");
$database = new Database($settings);