<?php
/**
 * SIDEBAR.PHP - Barre latérale
 * 
 * Affiche les widgets via get_sidebar()
 * 
 * @package MonECommerceTheme
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    return;
}
?>

<aside id="secondary" class="widget-area">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>