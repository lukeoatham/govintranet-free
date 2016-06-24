jQuery(document).ready(function($) {
	jQuery( "#startdatepicker" ).datepicker();
	jQuery( "#startdatepicker" ).datepicker("option", "dateFormat","yy-mm-dd");
	jQuery( "#enddatepicker" ).datepicker();
	jQuery( "#enddatepicker" ).datepicker("option", "dateFormat","yy-mm-dd");
});