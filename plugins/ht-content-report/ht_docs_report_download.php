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

/** Error reporting */
include($_SERVER['DOCUMENT_ROOT']."/wp-config.php");

$nonce = $_POST['nonce'];
// redirect if coming to this directly, not via the authenticated download page

if (!current_user_can('manage_options'))  {
	wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
}

if (!isset($_SERVER['HTTP_REFERER'])) { // direct request, not authenticated
	header("Location: https://" . $_SERVER['HTTP_HOST']);	
}
if ( !wp_verify_nonce($nonce, 'ht_document_report') ) { // not verified
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

$xquery = get_posts(array(
	'post_type'=>'attachment',
	'orderby'=>'title',
	'order'=>'ASC',
    'posts_per_page' => -1,
	'post_status'=>'inherit',
	'post_mime_type' => array( 'application' ),
	
));	
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', 'Title')
	->setCellValue('B1', 'URL')
	->setCellValue('C1', 'Author')
	->setCellValue('D1', 'Last modified')
	->setCellValue('E1', 'Taxonomy terms')
	->setCellValue('F1', 'Document type')
	->setCellValue('G1', 'A to Z')
	->setCellValue('H1', 'ID')
	->setCellValue('I1', 'Parent')
	;

$X = 2;		
		          
if ( count($xquery) > 0 ) foreach ( $xquery as $xq ){
	
	$tax = 'category';
	$cats = array();
	$xcats = wp_get_object_terms($xq->ID, $tax);
	if ( isset($xcats) ) foreach ( $xcats as $c ) $cats[] = $c->slug;

	$tags = array();
	$xtags = get_post_meta($xq->ID, 'document_type', true );
	if ( isset($xtags) ) foreach ( $xtags as $c ) $dt = get_term($c, 'document-type', OBJECT); $tags[] = $dt->slug;
	
	$atoz = array();
	if (is_taxonomy('media-a-to-z')) $xatoz = wp_get_object_terms($xq->ID, 'media-a-to-z');
	if ( isset($xatoz) ) foreach ( $xatoz as $c ) $atoz[] = $c->slug;
	if ( $xq->post_parent ) { $attachedto = get_permalink($xq->post_parent); } else { $attachedto = "Unattached"; }

	$authormeta = get_author_name($xq->post_author);
	$author = get_the_author_meta( 'user_email' , $xq->post_author);
	
    $objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A'.$X, $xq->post_title)
	    ->setCellValue('B'.$X, wp_get_attachment_url($xq->ID))
	    ->setCellValue('C'.$X, $author)
	    ->setCellValue('D'.$X, date('Y-m-d',strtotime($xq->post_modified)))
		->setCellValue('E'.$X, implode(', ', $cats))
		->setCellValue('F'.$X, implode(', ', $tags))
		->setCellValue('G'.$X, implode(', ', $atoz))
		->setCellValue('H'.$X, $xq->ID)
		->setCellValue('I'.$X, $attachedto)
		;
  
      $X++;        
	
}		
	
// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("GovIntranet")
                            ->setTitle("Document Report");
							 
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Document Report');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

ob_clean();
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="document-report-'.date('YmdHis').'.xls"');
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

