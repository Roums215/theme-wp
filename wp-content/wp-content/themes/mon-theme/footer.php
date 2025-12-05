<?php
/**
 * FOOTER.PHP - Pied de page du site
 * 
 * Ce fichier est inclus via get_footer() en bas de chaque page.
 * Il ferme les balises ouvertes dans header.php et contient wp_footer()
 * 
 * @package MonECommerceTheme
 */
?>

        </div><!-- .container -->
    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            
            <!-- COPYRIGHT -->
            <p>
                &copy; <?php echo date( 'Y' ); ?> 
                <?php bloginfo( 'name' ); ?>. 
                Tous droits réservés.
            </p>

            <!-- MENU FOOTER (optionnel) -->
            <?php 
            if ( has_nav_menu( 'footer' ) ) :
                wp_nav_menu( array(
                    'theme_location'  => 'footer',
                    'menu_id'         => 'footer-menu',
                    'container'       => 'div',
                    'container_class' => 'footer-menu-container',
                ));
            endif;
            ?>
            
        </div>
    </footer>

</div><!-- #page -->

<?php 
// OBLIGATOIRE : Permet à WordPress d'injecter les scripts avant </body>
wp_footer(); 
?>

</body>
</html>