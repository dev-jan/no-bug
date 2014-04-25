<?php
if (!defined("ROOTPATH")) {
	define("ROOTPATH", str_replace("core/../", "", substr(dirname(__FILE__). '/../', strlen($_SERVER['DOCUMENT_ROOT']))));
}

/* Version of the current Platform (e.g. v0.9-BETA), in Github its just "github-NIGHTLY" */
$versionname = "github-NIGHTLY";
$internalVersion = 0;

/* Compile Date (in Github its just the name of the current branch...) */
$compileDate = "master-branch";

/* Less compiler: Only for Development (not usefull on builds...) */ 
$lessLoader = '<script type="text/javascript" src="'.ROOTPATH.'js/less.js" ></script>';