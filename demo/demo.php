<?php
include_once('vendor/autoload.php');
PMVC\Load::plug();
PMVC\addPlugInFolder('./../../');
PMVC\plug('dotenv')->toPMVC('./.env');
$react = PMVC\plug('view_react');
$react->folder='/git/pmvc-git/pmvc-theme/hello_react';
$react->path='hello';
$react->set('text','hello world');
$react->process();

