<?php
/**
 * PAGE.PHP - Template pour une page statique
 * 
 * Utilisé pour : À propos, Contact, Mentions légales, etc.
 * 
 * TEMPLATE HIERARCHY :
 * 1. {template personnalisé}.php
 * 2. page-{slug}.php
 * 3. page-{id}.php
 * 4. page.php  ← CE FICHIER
 * 5. singular.php
 * 6. index.php
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

        <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <div class="entry-thumbnail">
                    <?php the_post_thumbnail( 'large' ); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>
            
        </article>

        <?php
    endwhile;
    ?>
    
</main>

<?php
get_footer();