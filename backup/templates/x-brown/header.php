<?php

// *** Make sure the file isn"t accessed directly
defined("APPHP_EXEC") or die("Restricted Access");
//--------------------------------------------------------------------------

$hotel_info = Hotel::GetHotelInfo();

?>

<!-- header -->
<div id="header" <?php echo (Application::Get("page") != "home" || (Application::Get("page") == "home" && Application::Get("admin") != "") || (Application::Get("page") == "home" && Application::Get("client") != "")) ? "style='height:207px;'" : ""; ?>>
	<div class="row-1">
		<div class="wrapper">
			<div class="logo <?php echo "f".Application::Get("defined_left"); ?>">
				<h1><a href="<?php echo APPHP_BASE; ?>index.php"><?php echo (@$objLogin->IsLoggedInAsAdmin()) ? _ADMIN_PANEL : $objSiteDescription->DrawHeader("header_text"); ?></a></h1>
				<em>Hotel</em>
				<strong>
					<?php
						if(@$objLogin->IsLoggedInAsAdmin() && Application::Get("preview") == "yes"){
							echo "<a class='header' href='index.php?preview=no'>"._BACK_TO_ADMIN_PANEL."</a>";						
						}else{
							echo $objSiteDescription->GetParameter("slogan_text");;				
						}
					?>
				</strong>
			</div>
			<div class="phones <?php echo "f".Application::Get("defined_right"); ?>">
				<?php echo $hotel_info['phone']; ?><br />
				<?php echo $hotel_info['fax']; ?>
			</div>
		</div>
	</div>

	<?php if(Application::Get("page") != "home" || (Application::Get("page") == "home" && Application::Get("admin") != "") || (Application::Get("page") == "home" && Application::Get("client") != "")){ ?>
		<div class="row-4">
			<div class='row-4-inner'>
				<?php 
					// Draw header menu
					Menu::DrawHeaderMenu();	
				?>		  
			</div>
		</div>
	<?php }else{ ?>
		<div class="row-2">
			<div class="indent">
				<!-- header-box begin -->				
				<div class="header-box<?php echo "_".Application::Get("lang_dir"); ?>">
					<!-- BANNERS -->
					<div class="inner">
					<?php
						echo $banner_image;
						// Draw header menu
						Menu::DrawHeaderMenu();	
					?>		  
					</div>
			   </div>
			   <!-- header-box end -->
			</div>	
		</div>
	<?php } ?>
	

	<div class="row-3">

		<?php if(!$objLogin->IsLoggedInAsAdmin()){ ?>
			
			<!-- language -->
			<div class="nav_language <?php echo "f".Application::Get("defined_left"); ?>">		
				<?php				
					$objLang  = new Languages();				
					if($objLang->GetLanguagesCount() > 1){
						echo "<div style='margin-right:6px;float:left;'>"._LANGUAGES."</div>";			
						echo "<div style='margin-top:5px;float:left;'>";
						$objLang->DrawLanguagesBar();
						echo "</div>";
					}
					
				?>		
			</div>		
		
			<!-- currencies -->
			<div class="nav_currencies <?php echo "f".Application::Get("defined_left"); ?>">
			<?php			
				echo Currencies::GetCurrenciesDDL();
			?>
			</div>
			<?php
				echo Search::DrawQuickSearch();
			?>
		<?php } ?>
	</div>
</div>
