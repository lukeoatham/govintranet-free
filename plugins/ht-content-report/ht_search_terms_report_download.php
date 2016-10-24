<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2013 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** include WordPress defaults */
include($_SERVER['DOCUMENT_ROOT']."/wp-config.php");

$nonce = $_POST['nonce'];
// redirect if coming to this directly or not via the authenticated download page

if (!current_user_can('manage_options'))  {
	wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
}

if (!isset($_SERVER['HTTP_REFERER'])) { // direct request, not authenticated
	header("Location: https://" . $_SERVER['HTTP_HOST']);	
}
if ( !wp_verify_nonce($nonce, 'ht_search_terms_report') ) { // not verified
	header("Location: https://" . $_SERVER['HTTP_HOST']);	
}

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

$tzone = get_option('timezone_string');
date_default_timezone_set($tzone);

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();

$startdate = $_POST['startdate'];
$enddate = $_POST['enddate'];
$numterms = intval($_POST['numterms']);
if ( !$numterms ) $numterms = 100;

if ( $startdate ) $startdate = date('Y-m-d',strtotime($startdate));
if ( $enddate ) $enddate = date('Y-m-d',strtotime($enddate));
if ( $startdate && !$enddate ) $enddate = date('Y-m-d');

global $wpdb;
$prefix = $wpdb->prefix;

if ( $startdate && $enddate ){
	$xquery = $wpdb->get_results("select count(id) as qcount, query as qterms from ".$prefix."relevanssi_log where time >= '".$startdate."' and time <= '".$enddate."' group by query order by count(id) desc limit ".$numterms.";");
	
} else {
	$xquery = $wpdb->get_results("select count(id) as qcount, query as qterms from ".$prefix."relevanssi_log group by query order by count(id) desc limit ".$numterms.";");
	
}

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', __('Count','govintranet'))
	->setCellValue('B1', __('Terms','govintranet'))
	->setCellValue('C1', $startdate . " - " . $enddate )
	;

$X = 2;		
		          
if ( $xquery ) foreach ( $xquery as $line ){
    $objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A'.$X, $line->qcount)
	    ->setCellValue('B'.$X, $line->qterms)
		;
  
      $X++;        
}		
	
// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("GovIntranet")
                            ->setTitle(__("Search Terms Report","govintranet"));
							 
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle(__('Search Terms Report','govintranet'));
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

ob_clean();
// Redirect output to a client's web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="search-terms-report-'.date('YmdHis').'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;