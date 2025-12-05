<?php
/**
 * 404.PHP - Page d'erreur 404
 * 
 * S'affiche automatiquement quand une page n'existe pas
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<main id="main" class="site-main">
    
    <div class="error-404">
        
        <h1>404</h1>
        
        <h2>Page non trouvée</h2>
        
        <p>Désolé, la page que vous recherchez n'existe pas.</p>
        
        <a href="<?php echo home_url( '/' ); ?>" class="btn">
            Retour à l'accueil
        </a>
        
    </div>
    
</main>

<?php
get_footer();