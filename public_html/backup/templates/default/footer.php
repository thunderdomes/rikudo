<?php

// *** Make sure the file isn"t accessed directly
defined("APPHP_EXEC") or die("Restricted Access");
//--------------------------------------------------------------------------

?>
<div id="footer">
	<div id="footer_wrapper">
        <div class="fleft">
            <?php 
                // Draw footer menu
                Menu::DrawFooterMenu();	
            ?>		  		
        </div>
		
        <div class="fright">
            <?php echo $objSiteDescription->DrawFooter(); ?>
            <?php echo "&nbsp;".get_divider()."&nbsp;"; ?>
            <?php
                if($objSettings->GetParameter("rss_feed")){
                    echo "<a href='feeds/rss.xml' title='RSS Feed'><img src='templates/".Application::Get("template")."/images/rss.png' alt='RSS Feed' border='0' /></a>&nbsp;";
                }
            ?>
            
			<form name="frmLogout" id="frmLogout" action="index.php" method="post">
            <?php if(@$objLogin->IsLoggedIn()){ ?>
                <input type="hidden" name="submit_logout" value="logout">
                <a class="main_link" href="javascript:appFormSubmit('frmLogout');"><?php echo _BUTTON_LOGOUT; ?></a>
            <?php }else{ ?>
                <a class="main_link" href="index.php?client=login"><?php echo _CLIENT_LOGIN; ?></a>
                <?php echo "&nbsp;".get_divider()."&nbsp;"; ?>
                <a class="main_link" href="index.php?admin=login"><?php echo _ADMIN_LOGIN; ?></a>
            <?php } ?>
            </form>
        </div>
	</div>
</div>			

