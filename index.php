<?php
session_start();

// set php ini so the page doesn't time out for long requests
ini_set('max_execution_time', 300);

// sets up autoloading of composer dependencies
require 'vendor/autoload.php';

// sets up autoload (looks in classes/local/, classes/, and lib/ in that order)
#require 'src/PhpReports/PhpReports.php';
JDorn\PhpReports\PhpReports::init('config/config.php.sample');

Flight::route('/',function() {
	PhpReports::listReports();
});

Flight::route('/dashboards',function() {
	PhpReports::listDashboards();
});

Flight::route('/dashboard/@name',function($name) {
	PhpReports::displayDashboard($name);
});

// JSON list of reports (used for typeahead search)
Flight::route('/report-list-json',function() {
	header("Content-Type: application/json");
	header("Cache-Control: max-age=3600");

	echo PhpReports::getReportListJSON();
});

// if no report format is specified, default to html
Flight::route('/report',function() {
	PhpReports::displayReport($_REQUEST['report'],'html');
});

// reports in a specific format (e.g. 'html','csv','json','xml', etc.)
Flight::route('/report/@format',function($format) {
	PhpReports::displayReport($_REQUEST['report'],$format);
});

Flight::route('/edit',function() {
	PhpReports::editReport($_REQUEST['report']);
});

Flight::route('/set-environment',function() {
    header("Content-Type: application/json");
	$_SESSION['environment'] = $_REQUEST['environment'];

    echo '{ "status": "OK" }';
});

// email report
Flight::route('/email',function() {
	PhpReports::emailReport();	
});


Flight::start();
