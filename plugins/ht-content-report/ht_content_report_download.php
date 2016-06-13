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
if ( !wp_verify_nonce($nonce, 'ht_content_report') ) { // not verified
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

$pt = $_POST['ptype'];
$ps = $_POST['pstat'];
if ( !$pt ) $pt = array('page');
if ( !$ps ) $ps = array('publish','draft','future','pending');

$xquery = new WP_Query(array(
	'post_type' => $pt,
	'posts_per_page' => -1,
	'paged' => $paged,
	'post_status' => $ps,
	'order' => 'ASC',
	'orderby' => 'ID menu_order',
));

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', 'Title')
	->setCellValue('B1', 'URL')
	->setCellValue('C1', 'Author')
	->setCellValue('D1', 'Last modified')
	->setCellValue('E1', 'Status')
	->setCellValue('F1', 'Taxonomy terms')
	->setCellValue('G1', 'Tags')
	->setCellValue('H1', 'A to Z')
	->setCellValue('I1', 'Search keywords')
	->setCellValue('J1', 'Relevanssi pin')
	->setCellValue('K1', 'ID')
	->setCellValue('L1', 'Order')
	->setCellValue('M1', 'Parent')
	;

$X = 2;		
		          
if ( $xquery->have_posts() ) while ( $xquery->have_posts()){
	$xquery->the_post();
	
	switch ($post->post_type) {
	    case 'news':
	        $tax = 'news-type';
	        break;
	    case 'news-update':
	        $tax = 'news-update-type';
	        break;
	    case 'event':
	        $tax = 'event-type';
	        break;
	    case 'blog':
	        $tax = 'blog-category';
	        break;
	    default:
		$tax = 'category';
	}
	
	$cats = array();
	$xcats = wp_get_object_terms(get_the_id(), $tax);
	if ( isset($xcats) ) foreach ( $xcats as $c ) $cats[] = $c->slug;

	$tags = array();
	$xtags = wp_get_object_terms(get_the_id(), 'post_tag');
	if ( isset($xtags) ) foreach ( $xtags as $c ) $tags[] = $c->slug;

	$atoz = array();
	$xatoz = wp_get_object_terms(get_the_id(), 'a-to-z');
	if ( isset($xatoz) ) foreach ( $xatoz as $c ) $atoz[] = $c->slug;

	$keywords = get_post_meta(get_the_id(),'keywords',true);
	if ( !$keywords ) $keywords = '';
	$skeywords = get_post_meta(get_the_id(),'_relevanssi_pin',true);
	if ( !$skeywords ) $skeywords = '';


    $objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A'.$X, get_the_title())
	    ->setCellValue('B'.$X, get_permalink())
	    ->setCellValue('C'.$X, get_the_author_meta( 'user_email' , $post->post_author))
	    ->setCellValue('D'.$X, get_the_modified_date('Y-m-d'))
		->setCellValue('E'.$X, get_post_status())
		->setCellValue('F'.$X, implode(', ', $cats))
		->setCellValue('G'.$X, implode(', ', $tags))
		->setCellValue('H'.$X, implode(', ', $atoz))
		->setCellValue('I'.$X, $keywords)
		->setCellValue('J'.$X, $skeywords)
		->setCellValue('K'.$X, get_the_id())
		->setCellValue('L'.$X, $post->menu_order)
		->setCellValue('M'.$X, $post->post_parent)
		;
  
      $X++;        
	
}		
	
// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("GovIntranet")
                            ->setTitle("Content Report");
							 
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Content Report');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

ob_clean();
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="content-report-'.date('YmdHis').'.xls"');
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

