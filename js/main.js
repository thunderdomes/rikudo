function appSetFocus(a){if(document.getElementById(a)){document.getElementById(a).focus();}}function appGoTo(b,c){var a=(c!=null)?c:"";document.location.href="index.php?"+b+a;
}function appGoToPage(h,d,a){var k=(d!=null)?d:"";var b=(a!=null)?a:"";if(b=="post"){var c=document.createElement("form");c.setAttribute("id","frmTemp");
c.setAttribute("action",h);c.setAttribute("method","post");document.body.appendChild(c);k=k.replace("?","");var g=k.split("&");var e="";for(var f=0;f<g.length;
f++){e=g[f].split("=");var j=document.createElement("input");j.setAttribute("type","hidden");j.setAttribute("name",e[0]);j.setAttribute("id",e[0]);j.setAttribute("value",unescape(e[1]));
document.getElementById("frmTemp").appendChild(j);}document.getElementById("frmTemp").submit();}else{document.location.href=h+k;}}function appGoToCurrent(b,d){var c=(b!=null)?b:"index.php";
var a=(d!=null)?d:"";document.location.href=c+a;}function appSetNewCurrency(b,d){var c=(b!=null)?b:"index.php";var a=(d!=null)?d:"";document.location.href=c.replace("__CUR__",a);
}function appOpenPopup(a){new_window=window.open(a,"blank","location=1,status=1,scrollbars=1,width=400,height=300");new_window.moveTo(100,100);if(window.focus){new_window.focus();
}}function appSetCookie(c,d,e){if(e){var b=new Date();b.setTime(b.getTime()+(e*24*60*60*1000));var a="; expires="+b.toGMTString();}else{var a="";}document.cookie=c+"="+d+a+"; path=/";
}function appGetCookie(a){if(document.cookie.length>0){start_c=document.cookie.indexOf(a+"=");if(start_c!=-1){start_c+=(a.length+1);end_c=document.cookie.indexOf(";",start_c);
if(end_c==-1){end_c=document.cookie.length;}return unescape(document.cookie.substring(start_c,end_c));}}return"";}function appGetMenuStatus(b){var a=document.getElementById("side_box_content_"+b).style.display;
if(a=="none"){return"none";}else{return"";}}function appToggleElementView(a,d,e,c,b){var c=(c!=null)?c:"none";var b=(b!=null)?b:"";if(!document.getElementById(e)){return false;
}else{if(a==d){document.getElementById(e).style.display=c;}else{document.getElementById(e).style.display=b;}}}function appToggleRss(a){if(a==1){if(document.getElementById("rss_feed_type")){document.getElementById("rss_feed_type").disabled=false;
}}else{if(document.getElementById("rss_feed_type")){document.getElementById("rss_feed_type").disabled=true;}}}function appIsEmail(f){var a="@";var b=".";
var e=f.indexOf(a);var c=f.length;var d=f.indexOf(b);if(f.indexOf(a)==-1){return false;}if(f.indexOf(a)==-1||f.indexOf(a)==0||f.indexOf(a)==c){return false;
}if(f.indexOf(b)==-1||f.indexOf(b)==0||f.indexOf(b)==c){return false;}if(f.indexOf(a,(e+1))!=-1){return false;}if(f.substring(e-1,e)==b||f.substring(e+1,e+2)==b){return false;
}if(f.indexOf(b,(e+2))==-1){return false;}if(f.indexOf(" ")!=-1){return false;}return true;}function appPerformSearch(b,a){if(a!=null){document.forms.frmQuickSearch.keyword.value=a;
}document.forms.frmQuickSearch.p.value=b;document.forms.frmQuickSearch.submit();}function appToggleElement(a){jQuery("#"+a).toggle("fast");}function appHideElement(a){if(a.indexOf("#")!=-1||a.indexOf(".")!=-1){jQuery(a).hide("fast");
}else{jQuery("#"+a).hide("fast");}}function appShowElement(a){if(a.indexOf("#")!=-1||a.indexOf(".")!=-1){jQuery(a).show("fast");}else{jQuery("#"+a).show("fast");
}}function appToggleJQuery(a){jQuery("."+a).toggle("fast");}function appToggleByClass(a){jQuery("."+a).toggle("fast");}function appFormSubmit(a,e){if(document.getElementById(a)){if(e!=null){var d=e.split("&");
var f="";for(var c=0;c<d.length;c++){f=d[c].split("=");for(var b=0;b<f.length;b+=2){if(document.getElementById(f[b])){document.getElementById(f[b]).value=f[b+1];
}}}}document.getElementById(a).submit();}}function appQuickSearch(){var a=document.frmQuickSearch.keyword.value;if(a==""||a.indexOf("...")!=-1){return false;
}else{document.frmQuickSearch.submit();return true;}}function appToggleTabs(c,a){jQuery("#content"+c).show("fast");jQuery("#tab"+c).attr("style","font-weight:bold");
for(var b=0;b<a.length;b++){if(a[b]!=c){jQuery("#content"+a[b]).hide("fast");jQuery("#tab"+a[b]).attr("style","font-weight:normal");}}}function appToggleElementReadonly(a,b,c,f,e,d){var f=(f!=null)?f:false;
var e=(e!=null)?e:false;var d=(d!=null)?d:true;if(!document.getElementById(c)){return false;}else{if(d){if(a==b){document.getElementById(c).readOnly=f;
}else{document.getElementById(c).readOnly=e;}}else{if(a==b){document.getElementById(c).disabled=f;}else{document.getElementById(c).disabled=e;}}}}function appPreview(f){var g="";
var d="";var a="";var b="";if(f=="invoice"){g="invoice.tpl.html";d="divInvoiceContent";a="INVOICE";}else{if(f=="booking"){g="description.tpl.html";d="divDescriptionContent";
a="BOOKING";}else{if(f=="description"){g="description.tpl.html";d="divDescriptionContent";a="DESCRIPTION";}}}var e=window.open("html/templates/"+g,"blank","location=0,status=0,toolbar=0,height=480,width=680,scrollbars=yes,resizable=1,screenX=100,screenY=100");
if(window.focus){e.focus();}var c=document.getElementById(d).innerHTML;c=c.replace(/<[//]{0,1}(FORM|INPUT|LABEL)[^><]*>/g,"");b="<style>";b+="TABLE.tblReservationDetails { border:1px solid #d1d2d3 }";
b+="TABLE.tblReservationDetails THEAD TR { background-color:#e1e2e3;font-weight:bold;font-size:13px; }";b+="TABLE.tblReservationDetails TR TD SPAN { background-color:#e1e2e3; }";
b+="TABLE.tblExtrasDetails { border:1px solid #d1d2d3 }";b+="TABLE.tblExtrasDetails THEAD TR { background-color:#e1e2e3;font-weight:bold;font-size:13px; }";
b+="TABLE.tblExtrasDetails TR TD SPAN { background-color:#e1e2e3; }";b+="INPUT, SELECT, IMG { display:none; }";b+="@media print { .non-printable { display:none; } }	";
b+="</style>";c="<html><head>"+b+'</head><body><div class="non-printable" style="width:99%;height:24px;margin:0px;padding:4px 5px;background-color:#e1e2e3;"><div style="float:left;">'+a+'</div><div style="float:right;">[ <a href="javascript:void(0);" onclick="javascript:window.print();">Print</a> ] [ <a href="javascript:void(0);" onclick="javascript:window.close();">Close</a> ]</div></div>'+c+"</body></html>";
e.document.open();e.document.write(c);e.document.close();}function appPopupWindow(g,a){var a=(a!=null)?a:false;var f=window.open("html/"+g,"PopupWindow","height=500,width=600,toolbar=0,location=0,menubar=0,scrollbars=yes,screenX=100,screenY=100");
if(window.focus){f.focus();}if(a){var c=document.getElementById(a);if(c.type==undefined){var d=c.innerHTML;}else{var d=c.value;}var b=/\n/gi;var e="<br> \n";
d=d.replace(b,e);f.document.open();f.document.write(d);f.document.close();}}function appShowTermsAndConditions(){document.getElementById("light").style.display="block";
document.getElementById("fade").style.display="block";}function appCloseTermsAndConditions(){document.getElementById("light").style.display="none";document.getElementById("fade").style.display="none";
}