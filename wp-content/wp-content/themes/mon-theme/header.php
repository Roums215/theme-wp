<?php
/**
 * HEADER.PHP - En-tête du site
 * 
 * Ce fichier est inclus via get_header() en haut de chaque page.
 * Il contient :
 * - La déclaration DOCTYPE et <html>
 * - Le <head> avec les meta et wp_head()
 * - Le header du site (logo + menu)
 * 
 * @package MonECommerceTheme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php 
    // OBLIGATOIRE : Permet à WordPress d'injecter les styles/scripts
    wp_head(); 
    ?>
</head>

<body <?php body_class(); ?>>

<?php 
// Permet aux plugins d'ajouter du contenu après <body>
wp_body_open(); 
?>

<div id="page" class="site">

    <header id="masthead" class="site-header">
        <div class="container">
            
            <!-- LOGO OU TITRE DU SITE -->
            <div class="site-branding">
                <?php
                if ( has_custom_logo() ) :
                    // Affiche le logo si défini dans l'admin
                    the_custom_logo();
                else :
                    // Sinon affiche le titre du site
                    ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    </h1>
                    <?php
                endif;
                ?>
            </div>

            <!-- MENU DE NAVIGATION -->
            <nav id="site-navigation" class="main-navigation">
                <?php
                // Affiche le menu "primary" défini dans l'admin
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                ));
                ?>
            </nav>

        </div>
    </header>

    <div id="content" class="site-content">
        <div class="container">