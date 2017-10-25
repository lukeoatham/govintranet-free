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

$pt = isset($_POST['ptype']) ? $_POST['ptype']: array('page');
$ps = isset($_POST['pstat']) ? $_POST['pstat']: array('publish','draft','future','pending');
$ppp = isset($_POST['ppp']) ? $_POST['ppp']: -1;
$paged = isset($_POST['paged']) ? $_POST['paged']: 1;
$startdate = isset($_POST['startdate']) ? date('Y-m-d',strtotime($_POST['startdate'])) : '';
$enddate = isset($_POST['enddate']) ? date('Y-m-d',strtotime($_POST['enddate'])) : '';
$datetype = isset($_POST['dates']) ? $_POST['dates']: '';

$tempquery = array(
	'post_type' => $pt,
	'posts_per_page' => $ppp,
	'post_status' => $ps,
	'fields' => 'ids',
	'order' => 'ASC',
	'orderby' => 'ID menu_order',
	'paged' => $paged,
);

if ( $enddate != "" && $datetype == "published" ):
	$tempquery['date_query'] = array(
		array(
			'column' => 'post_date',
			'before' => $enddate,
		),
		array(
			'column' => 'post_date',
			'after'  => $startdate,
		),
		'inclusive' => true
	);	
endif;

if ( $enddate != "" && $datetype == "modified" ):
	$tempquery['date_query'] = array(
		array(
			'column' => 'post_modified',
			'before' => $enddate,
		),
		array(
			'column' => 'post_modified',
			'after'  => $startdate,
		),
		'inclusive' => true
	);	
endif;

$xquery = new WP_Query($tempquery);
	
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', __('Title','govintranet'))
	->setCellValue('B1', __('URL','govintranet'))
	->setCellValue('C1', __('Author','govintranet'))
	->setCellValue('D1', __('Published','govintranet'))
	->setCellValue('E1', __('Last modified','govintranet'))
	->setCellValue('F1', __('Status','govintranet'))
	->setCellValue('G1', __('Category/type','govintranet'))
	->setCellValue('H1', __('Tags','govintranet'))
	->setCellValue('I1', __('A to Z','govintranet'))
	->setCellValue('J1', __('Search keywords','govintranet'))
	->setCellValue('K1', __('Relevanssi pin','govintranet'))
	->setCellValue('L1', __('Hide in search','govintranet'))	
	->setCellValue('M1', __('ID','govintranet'))
	->setCellValue('N1', __('Order','govintranet'))
	->setCellValue('O1', __('Parent','govintranet'))
	->setCellValue('P1', __('Post type','govintranet'))
	->setCellValue('Q1', __('Attachments','govintranet'))	
	->setCellValue('R1', __('Teams','govintranet'))	
	;

$X = 2;		
		          
if ( $xquery->have_posts() ) while ( $xquery->have_posts()){
	$xquery->the_post();
	
	switch (get_post_type( $id )) {
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
	$hide = get_post_meta(get_the_id(),'_relevanssi_hide_post',true);
	if ( !$hide ) $hide = '';

	$doc_attachments = array();
	$media = get_attached_media( 'application' );
	if ( $media ){
		foreach ( $media as $m ){
			$doc_attachments[] = get_permalink($m->ID);
		}
	}

	$media = get_field('document_attachments');
	if ( $media ) {
		foreach ($media as $ca){
			$c = $ca['document_attachment'];
			if ( isset($c['ID']) ) $doc_attachments[] = get_permalink($c['ID']);
		}
	}

	$related_teams = array();
	$teams = get_post_meta(get_the_id(), 'related_team', true);
	if ( $teams ) {
		foreach ($teams as $ca){
			if ( $ca ) $related_teams[] = get_permalink($ca);
		}
	}

	$xcats = implode(', ', $cats);
	if ( !$xcats ) $xcats = "=NA()";	
	$xtags = implode(', ', $tags);
	if ( !$xtags ) $xtags = "=NA()";	
	$xatoz = implode(', ', $atoz);
	if ( !$xatoz ) $xatoz = "=NA()";	
	$xdoc_attachments = implode(', ', $doc_attachments);
	if ( !$xdoc_attachments ) $xdoc_attachments = "=NA()";	
	$xrelated_teams = implode(', ', $related_teams);
	if ( !$xrelated_teams ) $xrelated_teams = "=NA()";	
	if ( !$keywords ) $keywords = "=NA()";	
	if ( !$skeywords ) $skeywords = "=NA()";	
	if ( !$hide ) $hide = "=NA()";	

	$post_line = get_post($id);

    $objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A'.$X, get_the_title())
	    ->setCellValue('B'.$X, get_permalink())
	    ->setCellValue('C'.$X, get_the_author_meta( 'user_email' , $post_line->post_author))
	    ->setCellValue('D'.$X, get_the_date('Y-m-d'))
	    ->setCellValue('E'.$X, get_the_modified_date('Y-m-d'))
		->setCellValue('F'.$X, get_post_status())
		->setCellValue('G'.$X, $xcats)
		->setCellValue('H'.$X, $xtags)
		->setCellValue('I'.$X, $xatoz)
		->setCellValue('J'.$X, $keywords)
		->setCellValue('K'.$X, $skeywords)
		->setCellValue('L'.$X, $hide)
		->setCellValue('M'.$X, get_the_id())
		->setCellValue('N'.$X, $post_line->menu_order)
		->setCellValue('O'.$X, $post_line->post_parent)
		->setCellValue('P'.$X, get_post_type( $id ))
		->setCellValue('Q'.$X, $xdoc_attachments)		
		->setCellValue('R'.$X, $xrelated_teams)		
		;
  
      $X++;        
	
}		
	
// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("GovIntranet")
                            ->setTitle(__("Content Report","govintranet"));
							 
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle(__('Content Report','govintranet'));
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

ob_clean();
// Redirect output to a client's web browser (Excel5)
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