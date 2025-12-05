<?php
/**
 * INDEX.PHP - Template par défaut
 * 
 * C'est le template de secours (fallback).
 * Si aucun autre template ne correspond à la page demandée,
 * WordPress utilise celui-ci.
 * 
 * CONTIENT : La boucle WordPress (The Loop)
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<main id="main" class="site-main">
    
    <?php
    /* ========================================
       LA BOUCLE WORDPRESS (The Loop)
       ========================================
       
       have_posts() → Vérifie s'il y a des articles
       the_post()   → Prépare l'article courant
       
       Fonctions disponibles dans la boucle :
       - the_title()     → Titre
       - the_content()   → Contenu complet
       - the_excerpt()   → Résumé
       - the_permalink() → URL
       - the_ID()        → Identifiant
       - get_the_date()  → Date
    */
    
    if ( have_posts() ) :
        
        while ( have_posts() ) : 
            the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header">
                    <h2 class="entry-title">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                    <div class="entry-meta">
                        <?php echo get_the_date(); ?>
                    </div>
                </header>

                <div class="entry-content">
                    <?php the_excerpt(); ?>
                </div>
                
                <a href="<?php the_permalink(); ?>" class="btn">
                    Lire la suite
                </a>
                
            </article>

            <?php
        endwhile;

        // Pagination (articles précédents/suivants)
        the_posts_navigation();

    else :
        ?>
        <p>Aucun article trouvé.</p>
        <?php
    endif;
    ?>
    
</main>

<?php
get_footer();