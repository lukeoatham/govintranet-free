
<?php if (!isset($_COOKIE['htcookiebar']) && !is_user_logged_in() ) : ?>

<div id='cookiebar'>
	<?php dynamic_sidebar('cookiebar'); ?>
</div>

<?php endif; ?>