<?php
/**
 * SINGLE.PHP - Template pour un article unique
 * 
 * TEMPLATE HIERARCHY (ordre de recherche) :
 * 1. single-{post-type}-{slug}.php
 * 2. single-{post-type}.php
 * 3. single.php  ← CE FICHIER
 * 4. singular.php
 * 5. index.php
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<main id="main" class="site-main">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
                
                <div class="entry-meta">
                    <span>Publié le <?php echo get_the_date(); ?></span>
                    <span>par <?php the_author(); ?></span>
                </div>
            </header>

            <?php 
            // HOOK PERSONNALISÉ : permet d'ajouter du contenu ici
            do_action( 'mon_theme_article_start' ); 
            ?>

            <?php 
            // Image à la une
            if ( has_post_thumbnail() ) : 
                ?>
                <div class="entry-thumbnail">
                    <?php the_post_thumbnail( 'large' ); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php 
                // Affiche le contenu COMPLET de l'article
                the_content(); 
                ?>
            </div>

            <footer class="entry-footer">
                <?php
                // Catégories
                the_category( ', ' );
                
                // Tags
                the_tags( '<p>Tags : ', ', ', '</p>' );
                ?>
            </footer>
            
        </article>

        <?php
        // Navigation entre articles
        the_post_navigation( array(
            'prev_text' => '← %title',
            'next_text' => '%title →',
        ));
        
        // Commentaires
        if ( comments_open() ) :
            comments_template();
        endif;
        
    endwhile;
    ?>
    
</main>

<?php
get_footer();