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
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            dark: '#0B0F19', // Deep background
                            primary: '#6366f1', // Indigo 500
                            secondary: '#8b5cf6', // Violet 500
                            accent: '#10b981', // Emerald 500
                            surface: '#111827', // Gray 900
                            border: '#1f2937', // Gray 800
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                        'hero-glow': 'conic-gradient(from 90deg at 50% 50%, #0B0F19 0%, #111827 50%, #0B0F19 100%)',
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <?php 
    // OBLIGATOIRE : Permet à WordPress d'injecter les styles/scripts
    wp_head(); 
    ?>
</head>

<body <?php body_class('bg-brand-dark text-gray-300 font-sans antialiased selection:bg-brand-primary selection:text-white'); ?>>

<?php wp_body_open(); ?>

<div id="page" class="site min-h-screen flex flex-col">

    <!-- HEADER / NAVIGATION -->
    <header id="masthead" class="site-header w-full bg-brand-surface border-b border-brand-border z-50">
        
        <div class="container mx-auto px-4 h-20 flex items-center justify-between">

            <!-- LOGO -->
            <div class="site-branding flex-shrink-0">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="text-2xl font-display font-bold text-white tracking-tight hover:text-brand-primary transition-colors flex items-center gap-2">
                    <span class="w-8 h-8 bg-brand-primary rounded-lg flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </span>
                    <?php bloginfo( 'name' ); ?>
                </a>
            </div>

            <!-- DESKTOP MENU -->
            <nav id="site-navigation" class="hidden md:flex items-center">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'menu_class'     => 'flex gap-8 font-medium text-gray-300',
                    'fallback_cb'    => false,
                    'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                ) );
                ?>
            </nav>

            <!-- ACTIONS -->
            <div class="flex items-center gap-6">
                
                <!-- Account -->
                <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="text-gray-400 hover:text-white transition-colors flex items-center gap-2 group" title="Mon Compte">
                    <div class="w-8 h-8 rounded-full bg-brand-dark border border-brand-border flex items-center justify-center group-hover:border-brand-primary transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <span class="hidden lg:block text-sm font-medium">Compte</span>
                </a>

                <!-- Cart -->
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <a href="<?php echo wc_get_cart_url(); ?>" class="relative group text-gray-400 hover:text-brand-primary transition-colors flex items-center gap-2" title="Panier">
                        <div class="w-8 h-8 rounded-full bg-brand-dark border border-brand-border flex items-center justify-center group-hover:border-brand-primary transition-colors relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <?php 
                            $count = WC()->cart->get_cart_contents_count();
                            if ( $count > 0 ) {
                                ?>
                                <span class="absolute -top-1 -right-1 bg-brand-primary text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-brand-dark">
                                    <?php echo esc_html( $count ); ?>
                                </span>
                                <?php
                            }
                            ?>
                        </div>
                        <span class="hidden lg:block text-sm font-medium">Panier</span>
                    </a>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-toggle" class="md:hidden text-gray-300 hover:text-white focus:outline-none p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

            </div>
        </div>
        
        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu" class="hidden absolute top-full left-0 w-full bg-brand-surface border-b border-brand-border p-6 md:hidden shadow-2xl z-50">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'flex flex-col gap-4 text-center font-medium text-lg',
            ) );
            ?>
        </div>

    </header>

    <!-- SPACER REMOVED -->

    <div id="content" class="site-content flex-grow w-full">
        <!-- Main Content -->