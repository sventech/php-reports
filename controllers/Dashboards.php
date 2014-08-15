<?php

use \JDorn\PhpReports as PhpReports;

class Dashboards extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load the necessary stuff...
        $this->load->helper(array('language', 'url', 'form', 'account/ssl'));
        $this->load->library(array('account/authentication', 'account/authorization'));
        $this->load->model('account/Account_model');
        $this->auth_data = $this->authentication->initialize(false);
        if(!$this->authentication->is_signed_in()) {
            redirect('/');
        } elseif($this->authorization->is_permitted( array('edit_content') ) ) {
            $this->can_edit = true;
        } elseif(!$this->authorization->is_permitted( array('view_content') ) ) {
            redirect('/');
        }
        $this->auth_data['section'] = 'dashboards';

        $this->load->helper('download');
        PhpReports\PhpReports::init('application/vendor/jdorn/php-reports/config/config.php.sample', 'application/vendor/jdorn/php-reports');
    }

    public function index() {
	PhpReports\PhpReports::listReports();
    }

    public function dashboards($name = null) {
        if(is_null($name)) {
	    PhpReports\PhpReports::listDashboards();
	} else {
	    PhpReports\PhpReports::displayDashboard($name);
        }
    }

    // JSON list of reports (used for typeahead search)
    public function report_list_json() {
	PhpReports\PhpReports::listReports();
	//header("Content-Type: application/json");
	//header("Cache-Control: max-age=3600");
	echo PhpReports\PhpReports::getReportListJSON();
    }

    public function report($format = null) {
        if(is_null($format)) {
            // if no report format is specified, default to html
	    PhpReports\PhpReports::displayReport($_REQUEST['report'],'html');
	} else {
            // reports in a specific format (e.g. 'html','csv','json','xml', etc.)
	    PhpReports\PhpReports::displayReport($_REQUEST['report'],$format);
        }
    }

    public function edit() {
	PhpReports\PhpReports::editReport($_REQUEST['report']);
    }

    public function set_environment() {
        header("Content-Type: application/json");
	$_SESSION['environment'] = $_REQUEST['environment'];

        echo '{ "status": "OK" }';
    }

    public function email() {
	PhpReports\PhpReports::editReport($_REQUEST['report']);
	PhpReports\PhpReports::emailReport();	
    }

}
