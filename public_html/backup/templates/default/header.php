<?php

// *** Make sure the file isn"t accessed directly
defined("APPHP_EXEC") or die("Restricted Access");
//--------------------------------------------------------------------------

$hotel_info = Hotel::GetHotelInfo();

?>

<!-- header -->
<div id="header">
	<div class="site_name" <?php echo "f".Application::Get("defined_left"); ?>>
		<a href="<?php echo APPHP_BASE; ?>index.php"><?php echo (@$objLogin->IsLoggedInAsAdmin()) ? _ADMIN_PANEL : $objSiteDescription->DrawHeader("header_text"); ?></a>
		<br />
		<strong>
		<?php
			if(@$objLogin->IsLoggedInAsAdmin() && Application::Get("preview") == "yes"){
				echo "<a class='header' href='index.php?preview=no'>"._BACK_TO_ADMIN_PANEL."</a>";						
			}else{
				echo $objSiteDescription->GetParameter("slogan_text");				
			}
		?>
		</strong>
	</div>
	<?php
        echo Search::DrawQuickSearch();
	?>
	<div class="phones <?php echo "f".Application::Get("defined_right"); ?>">
		<?php echo $hotel_info['phone']; ?><br />
		<?php echo $hotel_info['fax']; ?>
	</div>
</div>