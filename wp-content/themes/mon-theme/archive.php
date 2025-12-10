<?php
/**
 * ARCHIVE.PHP - Template pour les archives
 * 
 * Utilisé pour :
 * - Pages de catégories
 * - Pages de tags (étiquettes)
 * - Pages par auteur
 * - Pages par date
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<main id="main" class="site-main">
    
    <header class="archive-header">
        <h1 class="archive-title">
            <?php 

            the_archive_title(); 
            ?>
        </h1>
        <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
    </header>
    
    <?php
    if ( have_posts() ) :
        
        while ( have_posts() ) :
            the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header">
                    <h2 class="entry-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <div class="entry-meta">
                        <?php echo get_the_date(); ?>
                    </div>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'medium' ); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_excerpt(); ?>
                </div>
                
                <a href="<?php the_permalink(); ?>" class="btn">Lire la suite</a>
                
            </article>

            <?php
        endwhile;

        the_posts_pagination();

    else :
        ?>
        <p>Aucun article trouvé.</p>
        <?php
    endif;
    ?>
    
</main>

<?php
get_footer();