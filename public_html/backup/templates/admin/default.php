<?php
// *** Make sure the file isn"t accessed directly
defined("APPHP_EXEC") or die("Restricted Access");
//--------------------------------------------------------------------------

header("content-type: text/html; charset=utf-8");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title><?php echo $objSiteDescription->GetParameter("tag_title"); ?> :: <?php echo _ADMIN_PANEL; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?php echo $objSiteDescription->GetParameter("tag_description"); ?>" />
	<meta name="keywords" content="<?php echo $objSiteDescription->GetParameter("tag_keywords"); ?>" />

    <base href="<?php echo APPHP_BASE; ?>" /> 
	
	<link href="templates/<?php echo Application::Get("template");?>/css/style.css" type="text/css" rel="stylesheet" />
	<!--[if IE]>
	<link href="templates/<?php echo Application::Get("template");?>/css/styleIE.css" type="text/css" rel="stylesheet" />
	<![endif]-->

	<link rel="SHORTCUT ICON" href="images/icons/apphp.ico" />
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="templates/<?php echo Application::Get("template"); ?>/js/menu.js"></script>	

    <style type="text/css">
        #__menu_box { float:<?php //echo $text_alignment_left; ?>; } 		
        #__content_box  { float:<?php //echo $text_alignment_right; ?>; } 		    
    </style>
</head>    
<body dir="<?php echo Application::Get("lang_dir");?>">

<div id="mainWrapper">
<div id="headerWrapper">

	<?php include_once "templates/".Application::Get("template")."/header.php"; ?>

	<table id="contentMainWrapper" cellSpacing="0" cellPadding="0" width="100%" border="0">
	<tbody>
	<tr>
		<td id="navColumnLeft">
			<div id="navColumnLeftWrapper">
				<!-- LEFT COLUMN -->
				<?php                        
					@$objLogin->DrawLoginLinks();
					if(!Application::Get("preview")){
						if(Application::Get("admin") == "login"){
							echo "<br>"._LOGIN_PAGE_MSG;
						}else if(Application::Get("admin") == "password_forgotten"){
							echo "<br>"._PASSWORD_FORGOTTEN_PAGE_MSG;
						}						
					}
				?>
				<!-- END OF LEFT COLUMN -->			
			</div>		
	    </td>
		<td id="navColumnMain" valign="top">		
			<div id="indexDefault" class="center_column">
			<div id="indexDefaultMainContent">			
			<div class="center_box_wrapper">
				<!-- MAIN CONTENT -->
				<?php		
					if((Application::Get("page") != "") && file_exists("page/".Application::Get("page").".php")){
						include_once("page/".Application::Get("page").".php");
					}else if ((Application::Get("client") != "") && file_exists("client/".Application::Get("client").".php")){
						include_once("client/".Application::Get("client").".php");
					}else if((Application::Get("admin") != "") && !preg_match("/mod_/", Application::Get("admin")) && file_exists("admin/".Application::Get("admin").".php")){
						include_once("admin/".Application::Get("admin").".php");	
					}else if((Application::Get("admin") != "") && preg_match("/mod_/", Application::Get("admin")) && file_exists("admin/modules/".Application::Get("admin").".php")){
						include_once("admin/modules/".Application::Get("admin").".php");	
					}else{
						if(Application::Get("template") == "admin"){
							include_once("admin/home.php");
						}else{										
							include_once("page/pages.php");										
						}
					}
				?>
			</div>
			</div>
			</div>			
		</td>		
	</tr>
	</tbody>
	</table>
</div>
</div>
	
<?php
	if(@$objLogin->IsLoggedInAsAdmin()){
		echo "<script type='text/javascript'>set_active_menu_count(".@$objLogin->GetActiveMenuCount().");</script>";
	}
?>

</body>
</html>