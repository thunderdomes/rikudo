/**
 *   set focus on selected form element
 */

function appSetFocus(el_id){
    if(document.getElementById(el_id)){
        document.getElementById(el_id).focus();
    }    
}

/**
 *   Change location (go to another page)
 */
function appGoTo(page, params){
	var params_ = (params != null) ? params : "";
    document.location.href = 'index.php?'+page + params_;
}

/**
 *   Change location (go to another page)
 */
function appGoToPage(page, params, method){
	var params_ = (params != null) ? params : "";
	var method_ = (method != null) ? method : "";
	
	if(method_ == "post"){		
		var m_form = document.createElement('form');
			m_form.setAttribute('id', 'frmTemp');
			m_form.setAttribute('action', page);
			m_form.setAttribute('method', 'post');
		document.body.appendChild(m_form);
		
		params_ = params_.replace("?", "");
		var vars = params_.split("&");
		var pair = "";
		for(var i=0;i<vars.length;i++) { 
			pair = vars[i].split("="); 
			var input = document.createElement('input');
				input.setAttribute('type', 'hidden');
				input.setAttribute('name', pair[0]);
				input.setAttribute('id', pair[0]);
				input.setAttribute('value', unescape(pair[1]));
			document.getElementById('frmTemp').appendChild(input);
		}
		document.getElementById('frmTemp').submit();
	}else{
		document.location.href = page + params_;		
	}
}

/**
 *   Change location (go to current page)
 */
function appGoToCurrent(page, params){
	var page_  = (page != null) ? page : "index.php";
	var params_ = (params != null) ? params : "";
    document.location.href = page_ + params_;
}

/**
 *   Open popup window
 */
function appOpenPopup(page){
	new_window = window.open(page, "blank", "location=1,status=1,scrollbars=1,width=400,height=300");
    new_window.moveTo(100,100);
	if(window.focus) new_window.focus();
}

/**
 *   set cookie
 */
function appSetCookie(name, value, days) {
    if (days){
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = '; expires='+date.toGMTString();
    }
    else var expires = '';
    document.cookie = name+'='+value+expires+'; path=/';
}

/**
 *   get cookie
 */
function appGetCookie(name) {
	if (document.cookie.length > 0){
		start_c = document.cookie.indexOf(name + "=");
		if(start_c != -1){
			start_c += (name.length + 1);
			end_c = document.cookie.indexOf(";", start_c);
			if(end_c == -1) end_c = document.cookie.length;
			return unescape(document.cookie.substring(start_c,end_c));
		}
	}	
	return "";
}

/**
 *   get menu status
 */
function appGetMenuStatus(ind){
	var status = document.getElementById("side_box_content_"+ind).style.display;
	if(status == "none"){			
		return "none";
	}else{
		return "";
	}
}

/**
 *   toggle viewing of element
 */
function appToggleElementView(current_val, target_val, el, status1, status2){
	var status1 = (status1 != null) ? status1 : "none";
	var status2 = (status2 != null) ? status2 : "";
    if(!document.getElementById(el)){
		return false;
	}else{	
        if(current_val == target_val) document.getElementById(el).style.display = status1;
		else document.getElementById(el).style.display = status2;
    }  
}

/**
 *   toggle rss
 */
function appToggleRss(val){
	if(val == 1){
		if(document.getElementById("rss_feed_type")){
			document.getElementById("rss_feed_type").disabled = false;
		}
	}else{
		if(document.getElementById("rss_feed_type")){
			document.getElementById("rss_feed_type").disabled = true;
		}
	}
}

/**
 *   email validation
 */
function appIsEmail(str){
	var at="@";
	var dot=".";
	var lat=str.indexOf(at);
	var lstr=str.length;
	var ldot=str.indexOf(dot);
	if (str.indexOf(at)==-1) return false; 

	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr) return false;
	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr) return false;
	if (str.indexOf(at,(lat+1))!=-1) return false;
	if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot) return false;
	if (str.indexOf(dot,(lat+2))==-1) return false;
	if (str.indexOf(" ")!=-1) return false;

 	return true;
}

/**
 *  submit site search
 */
function appPerformSearch(page, kwd){
	if(kwd != null) document.forms['frmQuickSearch'].keyword.value = kwd;
	document.forms['frmQuickSearch'].p.value = page;
	document.forms['frmQuickSearch'].submit();
}

/**
 *   toggle element
 */
function appToggleElement(key){
	jQuery("#"+key).toggle("fast");
}

/**
 *   hide element
 */
function appHideElement(key){
	jQuery("#"+key).hide("fast");
}

/**
 *   show element
 */
function appShowElement(key){
	jQuery("#"+key).show("fast");
}

/**
 *  toggle by jQuery
 */
function appToggleJQuery(el){
	jQuery('.'+el).toggle("fast");
}

/**
 *  toggle by class
 */
function appToggleByClass(el){
	jQuery('.'+el).toggle("fast");
}

/**
 *  submit form 
 */
function appFormSubmit(frm_name, vars){
	if(document.getElementById(frm_name)){
		if(vars != null){
			vars_parts = vars.split('=');
			for(var i=0; i<vars_parts.length; i+=2) {
				if(document.getElementById(vars_parts[i])) document.getElementById(vars_parts[i]).value = vars_parts[i+1];
			}
		}	
		document.getElementById(frm_name).submit();					
	}									
}								

/**
 *  submit site quick search
 */
function appQuickSearch(){
	var keyword = document.frmQuickSearch.keyword.value;
	if(keyword == '' || keyword.indexOf("...") != -1){
		return false;
	}else{
		document.frmQuickSearch.submit();
		return true;
	}
}

/////////////////////////////////////////////////////////////////////////////

/**
 *   toggle elements
 */
function appToggleElements(key1, lnk1, key2, lnk2, key3, lnk3){
	jQuery("#"+key1).show("fast");
	jQuery("#"+lnk1).attr("style", "font-weight:bold");
	if(jQuery("#"+key2)){
		jQuery("#"+key2).hide("fast");
		jQuery("#"+lnk2).attr("style", "font-weight:normal");
	}
	if(jQuery("#"+key3)){
		jQuery("#"+key3).hide("fast");
		jQuery("#"+lnk3).attr("style", "font-weight:normal");
	}
}

/**
 *   toggle readonly state of element
 */
function appToggleElementReadonly(current_val, target_val, el, target_status, default_status, is_readonly){
	var target_status = (target_status != null) ? target_status : false;
	var default_status = (default_status != null) ? default_status : false;
	var is_readonly = (is_readonly != null) ? is_readonly : true;
    if(!document.getElementById(el)){
		return false;
	}else{
		//alert(current_val +"=="+ target_val+target_status);
		
		//alert(document.getElementById(el).readOnly);
		if(is_readonly){
			if(current_val == target_val) document.getElementById(el).readOnly = target_status;
			else document.getElementById(el).readOnly = default_status;
		}else{
			if(current_val == target_val) document.getElementById(el).disabled = target_status;
			else document.getElementById(el).disabled = default_status;
		}
    }  
}

//--------------------------------------------------------------------------
// invoice preview (used for admin and client)
function appInvoicePreview(){
	var new_window = window.open('html/templates/invoice.tpl.html','blank','location=0,status=0,toolbar=0,height=480,width=680,scrollbars=yes,resizable=1,screenX=100,screenY=100');
	if(window.focus) new_window.focus();
	var message = document.getElementById('divInvoiceContent').innerHTML;
	// remove skype tags
	message = message.replace('begin_of_the_skype_highlighting', ''); 
	message = message.replace('end_of_the_skype_highlighting', ''); 
	message = '<html><body><div style=\"width:99%;height:24px;margin:0px;padding:4px 5px;background-color:#e1e2e3;\"><div style=\"float:left;\">INVOICE</div><div style=\"float:right;\">[ <a href=\"javascript:void(0);\" onclick=\"javascript:window.print();\">Print</a> ]</div></div>' + message + '</body></html>';
	new_window.document.write(message); 
}

/**
 *  Show Popup window
 */
function appPopupWindow(template_file, element_id){
	var element_id = (element_id != null) ? element_id : false;
	var new_window = window.open('html/'+template_file,'name','height=500,width=600,scrollbars=yes,screenX=100,screenY=100');
	new_window.location.reload();
	if(window.focus) new_window.focus();
	if(element_id){
		var el = document.getElementById(element_id);		
		if(el.type == undefined){
			var message = el.innerHTML;	
		}else{
			var message = el.value;	
		}		
		var reg_x = /\n/gi;
		var replace_string = '<br> \n';
		message = message.replace(reg_x, replace_string);		
		new_window.document.writeln(message);
	}
}

function appShowTermsAndConditions(){
    document.getElementById('light').style.display='block';
    document.getElementById('fade').style.display='block';
}

function appCloseTermsAndConditions(){
    document.getElementById('light').style.display='none';
    document.getElementById('fade').style.display='none';
}

