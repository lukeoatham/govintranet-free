function sortTableString(f,n){
  var rows = jQuery('#zerohits tbody  tr').get();
  rows.sort(function(a, b) {

    // get the text of n-th <td> of <tr> 
    var A = (jQuery(a).children('td').eq(n).text().toUpperCase());
    var B = (jQuery(b).children('td').eq(n).text().toUpperCase());
    if(A < B) {
     return -1*f;
    }
    if(A > B) {
     return 1*f;
    }
    return 0;
  });

  jQuery.each(rows, function(index, row) {
    jQuery('#zerohits').children('tbody').append(row);
  });
}
function sortTable(f,n){
  var rows = jQuery('#zerohits tbody  tr').get();
  rows.sort(function(a, b) {

    // get the text of n-th <td> of <tr> 
    var A = parseFloat(jQuery(a).children('td').eq(n).text().toUpperCase());
    var B = parseFloat(jQuery(b).children('td').eq(n).text().toUpperCase());
    if(A < B) {
     return -1*f;
    }
    if(A > B) {
     return 1*f;
    }
    return 0;
  });

  jQuery.each(rows, function(index, row) {
    jQuery('#zerohits').children('tbody').append(row);
  });
}
function sortTableDate(f,n){
  var rows = jQuery('#zerohits tbody  tr').get();
  rows.sort(function(a, b) {

    // get the text of n-th <td> of <tr> 
    var A = Date.parse(jQuery(a).children('td').eq(n).text().toUpperCase());
    var B = Date.parse(jQuery(b).children('td').eq(n).text().toUpperCase());
    if(A < B) {
     return -1*f;
    }
    if(A > B) {
     return 1*f;
    }
    return 0;
  });

  jQuery.each(rows, function(index, row) {
    jQuery('#zerohits').children('tbody').append(row);
  });
}
var f_l6m = 1; // flag to toggle the sorting order
var f_url = 1; // flag to toggle the sorting order
var f_l1y = 1; // flag to toggle the sorting order
var f_pdate = 1; // flag to toggle the sorting order
var f_mdate = 1; // flag to toggle the sorting order
jQuery('#url').click(function(){
    f_url *= -1; // toggle the sorting order
    var n = jQuery(this).prevAll().length;
    sortTableString(f_url,n);
});
jQuery('#l6m').click(function(){
    f_l6m *= -1; // toggle the sorting order
    var n = jQuery(this).prevAll().length;
    sortTable(f_l6m,n);
});
jQuery('#l1y').click(function(){
    f_l1y *= -1; // toggle the sorting order
    var n = jQuery(this).prevAll().length;
    sortTable(f_l1y,n);
});
jQuery('#pdate').click(function(){
    f_pdate *= -1; // toggle the sorting order
    var n = jQuery(this).prevAll().length;
    sortTableDate(f_pdate,n);
});
jQuery('#mdate').click(function(){
    f_mdate *= -1; // toggle the sorting order
    var n = jQuery(this).prevAll().length;
    sortTableDate(f_mdate,n);
});