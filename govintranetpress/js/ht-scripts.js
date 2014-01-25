function markDocumentLinks() {
	jQuery('a[href*=".pdf"]').addClass('pdfdocument').append(' (PDF)');
	jQuery('a[href*=".xls"]').addClass('xlsdocument').append(' (Excel)');
	jQuery('a[href*=".doc"]').addClass('docdocument').append(' (Word)');
	jQuery('a[href*=".ppt"]').addClass('pptdocument').append(' (Powerpoint)');
	jQuery('a[href*=".txt"]').addClass('txtdocument').append(' (Text)');
	jQuery('a[href*=".csv"]').addClass('xlsdocument').append(' (CSV)');
	jQuery('a[href*="mailto"]').prepend('<i class="glyphicon glyphicon-envelope"></i> ');
	return true;	
}

function gaTrackDownloadableFiles() {

     var links = jQuery('a');

     for(var i = 0; i < links.length; i++) {
          if (links[i].href.indexOf('.pdf') != "-1") {
               jQuery(links[i]).attr("onclick","javascript: _gaq.push(['_trackPageview', '"+links[i].href+"']);");
          } else if (links[i].href.indexOf('.csv') != "-1") {
               jQuery(links[i]).attr("onclick","javascript: _gaq.push(['_trackPageview', '"+links[i].href+"']);");
          } else if (links[i].href.indexOf('.doc') != "-1") {
               jQuery(links[i]).attr("onclick","javascript: _gaq.push(['_trackPageview', '"+links[i].href+"']);");
          } else if (links[i].href.indexOf('.ppt') != "-1") {
               jQuery(links[i]).attr("onclick","javascript: _gaq.push(['_trackPageview', '"+links[i].href+"']);");
          } else if (links[i].href.indexOf('.rtf') != "-1") {
               jQuery(links[i]).attr("onclick","javascript: _gaq.push(['_trackPageview', '"+links[i].href+"']);");
          } else if (links[i].href.indexOf('.xls') != "-1") {
               jQuery(links[i]).attr("onclick","javascript: _gaq.push(['_trackPageview', '"+links[i].href+"']);");
          } else if (links[i].href.indexOf('.jpg') != "-1") {
               jQuery(links[i]).attr("onclick","javascript: _gaq.push(['_trackPageview', '"+links[i].href+"']);");
          } else if (links[i].href.indexOf('.gif') != "-1") {
               jQuery(links[i]).attr("onclick","javascript: _gaq.push(['_trackPageview', '"+links[i].href+"']);");
          } else if (links[i].href.indexOf('.png') != "-1") {
               jQuery(links[i]).attr("onclick","javascript: _gaq.push(['_trackPageview', '"+links[i].href+"']);");
          }
     }
     return true;    
}

