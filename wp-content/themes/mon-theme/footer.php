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

    <footer id="colophon" class="site-footer bg-brand-surface border-t border-brand-border pt-16 pb-8 mt-auto">
        <div class="w-full px-4 md:px-12">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                
                <!-- BRAND / ABOUT -->
                <div class="col-span-1 md:col-span-1">
                    <h2 class="text-2xl font-display font-bold text-white mb-6">
                        <?php bloginfo( 'name' ); ?><span class="text-brand-primary">.</span>
                    </h2>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        Des périphériques conçus pour l'excellence. Élevez votre setup avec notre collection premium.
                    </p>
                    <div class="flex gap-4">
                        <!-- Social Icons (Mockup) -->
                        <a href="#" class="w-10 h-10 rounded-full bg-brand-dark border border-brand-border flex items-center justify-center text-gray-400 hover:text-white hover:border-brand-primary transition-all">
                            <span class="sr-only">Twitter</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-brand-dark border border-brand-border flex items-center justify-center text-gray-400 hover:text-white hover:border-brand-primary transition-all">
                            <span class="sr-only">Instagram</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.416 2.065c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/></svg>
                        </a>
                    </div>
                </div>

                <!-- SHOP -->
                <div class="col-span-1">
                    <h3 class="text-white font-bold mb-6">Boutique</h3>
                    <ul class="space-y-4 text-gray-400">
                        <li><a href="#" class="hover:text-brand-primary transition-colors">Nouveautés</a></li>
                        <li><a href="#" class="hover:text-brand-primary transition-colors">Claviers Mécaniques</a></li>
                        <li><a href="#" class="hover:text-brand-primary transition-colors">Keycaps</a></li>
                        <li><a href="#" class="hover:text-brand-primary transition-colors">Accessoires</a></li>
                    </ul>
                </div>

                <!-- SUPPORT -->
                <div class="col-span-1">
                    <h3 class="text-white font-bold mb-6">Support</h3>
                    <ul class="space-y-4 text-gray-400">
                        <li><a href="#" class="hover:text-brand-primary transition-colors">Centre d'aide</a></li>
                        <li><a href="#" class="hover:text-brand-primary transition-colors">Livraison & Retours</a></li>
                        <li><a href="#" class="hover:text-brand-primary transition-colors">Garantie</a></li>
                        <li><a href="#" class="hover:text-brand-primary transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- NEWSLETTER -->
                <div class="col-span-1">
                    <h3 class="text-white font-bold mb-6">Restez connecté</h3>
                    <p class="text-gray-400 mb-4 text-sm">Recevez nos dernières offres et actus.</p>
                    <form class="flex flex-col gap-3">
                        <input type="email" placeholder="Votre email" class="bg-brand-dark border border-brand-border rounded-lg px-4 py-3 text-white focus:border-brand-primary focus:outline-none w-full">
                        <button type="submit" class="bg-brand-primary hover:bg-indigo-500 text-white font-bold py-3 px-6 rounded-lg transition-all">
                            S'inscrire
                        </button>
                    </form>
                </div>

            </div>
            
            <!-- BOTTOM BAR -->
            <div class="border-t border-brand-border pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <p>
                    &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. Tous droits réservés.
                </p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition-colors">Confidentialité</a>
                    <a href="#" class="hover:text-white transition-colors">CGV</a>
                    <a href="#" class="hover:text-white transition-colors">Mentions Légales</a>
                </div>
            </div>

        </div>
    </footer>

</div><!-- #page -->

<?php 
// OBLIGATOIRE : Permet à WordPress d'injecter les scripts avant </body>
wp_footer(); 
?>

</body>
</html>