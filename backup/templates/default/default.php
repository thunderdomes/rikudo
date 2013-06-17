<?php

// *** Make sure the file isn"t accessed directly
defined("APPHP_EXEC") or die("Restricted Access");
//--------------------------------------------------------------------------

header("content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>	
    <title><?php echo Application::Get("tag_title"); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="<?php echo Application::Get("tag_keywords"); ?>" />
	<meta name="description" content="<?php echo Application::Get("tag_description"); ?>" />

    <base href="<?php echo APPHP_BASE; ?>" /> 
	<link rel="SHORTCUT ICON" href="images/icons/apphp.ico" />
  
    <link href="templates/<?php echo Application::Get("template");?>/css/style.css" type="text/css" rel="stylesheet" />
	<!--[if IE]>
	<link href="templates/<?php echo Application::Get("template");?>/css/styleIE.css" type="text/css" rel="stylesheet" />
	<![endif]-->

	<!-- Opacity Module -->
	<link href="modules/opacity/opacity.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="modules/opacity/opacity.js"></script>

	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/main.js"></script>

	<!-- LyteBox v3.22 Author: Markus F. Hay Website: http://www.dolem.com/lytebox -->
	<link rel='stylesheet' href='modules/lytebox/css/lytebox.css' type='text/css' media='screen' />
	<script type='text/javascript' language='javascript' src='modules/lytebox/js/lytebox.js'></script>
	
    <?php echo GalleryAlbums::SetLibraries(); ?>    	
	<?php
	    $banner_image = "";
		if(!@$objLogin->IsLoggedIn() || Application::Get("preview") == "yes"){			
			draw_banners_top($banner_image);
        }
    ?>		

</head>

<body dir="<?php echo Application::Get("lang_dir");?>">
<a name="top"></a>
<div id="wrap">
	
	<!-- HEADER -->
	<?php include_once "templates/".Application::Get("template")."/header.php"; ?>
	
		<!-- header-box begin -->
		<?php
			if(!@$objLogin->IsLoggedIn()){
				echo "<div id='header-wrap'>";
				echo $banner_image;
				echo "</div>";
			}else{
				echo "<div id='header-wrap-logged'></div>";
			}		
		?>
		<!-- header-box end -->
	
	
	<div id="languages-wrap">
		<!-- languages -->
		<?php				
			$objLang = new Languages();				
			if($objLang->GetLanguagesCount() > 1){
				echo "<div style='margin-right:6px;float:left;'>"._LANGUAGES."</div>";			
				echo "<div style='margin-top:3px;margin-right:3px;float:left;'>";
				$objLang->DrawLanguagesBar();
				echo "</div>";
			}
			
		?>
		<!-- currencies -->
		<div class="nav_currencies">
		<?php			
			echo Currencies::GetCurrenciesDDL();
		?>
		</div>		
	</div>

	<?php
		// Draw header menu
		Menu::DrawHeaderMenu();
	?>		  

	<div id="content-wrap">
		<div id="left-column<?php echo "-".Application::Get("defined_left"); ?>">
			<!-- LEFT COLUMN -->
			<?php
				// Draw menu tree
				Menu::DrawMenu("left");						
				// Draw login menu
				@$objLogin->DrawLoginLinks();       
			?>                            
			<!-- END OF LEFT COLUMN -->				
		</div>

		<div id="content<?php echo "-".Application::Get("defined_right"); ?>">
			<!-- MAIN CONTENT -->
			<?php					
				if((Application::Get("page") != "") && file_exists("page/".Application::Get("page").".php")){
					include_once("page/".Application::Get("page").".php");
				}else if((Application::Get("client") != "") && file_exists("client/".Application::Get("client").".php")){
					if(Modules::IsModuleInstalled("clients")){	
						include_once("client/".Application::Get("client").".php");
					}else{
						include_once("client/404.php");
					}
				}else if((Application::Get("admin") != "") && file_exists("admin/".Application::Get("admin").".php")){
					include_once("admin/".Application::Get("admin").".php");
				}else{
					if(Application::Get("template") == "admin"){
						include_once("admin/home.php");
					}else{
						include_once("page/pages.php");										
					}
				}
			?>
			<!-- END OF MAIN CONTENT -->			
		</div>
		
		<!-- FOOTER -->
		<?php include_once "templates/".Application::Get("template")."/footer.php"; ?>
	</div>
</div>

<?php Rooms::DrawSearchAvailabilityFooter(); ?>

</body>
</html>