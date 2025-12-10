<?php
/**
 * FRONT-PAGE.PHP - Page d'accueil personnalisée
 * 
 * @package MonECommerceTheme
 */

get_header(); ?>

<!-- HERO SECTION -->
<section class="relative bg-brand-dark text-white py-32 overflow-hidden">
    <!-- Background Glows -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-brand-primary opacity-20 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-[800px] h-[600px] bg-brand-secondary opacity-10 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="container mx-auto px-4 relative z-10 text-center">
        <div class="inline-block mb-6 px-4 py-1.5 rounded-full border border-brand-border bg-brand-surface/50 backdrop-blur-sm text-sm font-medium text-brand-accent">
            ✨ Nouvelle Collection 2024
        </div>
        <h1 class="text-5xl md:text-8xl font-display font-bold mb-8 tracking-tight leading-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-200 to-gray-400">
            Elevate Your <br/>
            <span class="text-brand-primary">Digital Workflow</span>
        </h1>
        <p class="text-xl md:text-2xl text-gray-400 mb-12 max-w-2xl mx-auto font-light leading-relaxed">
            Des claviers conçus pour les créateurs, les développeurs et les visionnaires. L'alliance parfaite entre esthétique et performance.
        </p>
        <div class="flex flex-col md:flex-row gap-6 justify-center">
            <a href="#finder" class="bg-brand-primary hover:bg-indigo-500 text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 shadow-[0_0_20px_rgba(99,102,241,0.3)] hover:shadow-[0_0_30px_rgba(99,102,241,0.5)] transform hover:-translate-y-1">
                Trouver mon setup
            </a>
            <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="bg-brand-surface border border-brand-border hover:border-brand-primary text-white font-bold py-4 px-10 rounded-xl transition-all duration-300 hover:bg-brand-border">
                Explorer la boutique
            </a>
        </div>
    </div>
</section>

<!-- LIFESTYLE FINDER SECTION -->
<section id="finder" class="py-32 bg-brand-dark relative">
    <div class="container mx-auto px-4">
        <div class="text-center mb-20">
            <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-6">Quel est votre profil ?</h2>
            <p class="text-gray-400 text-lg">Laissez la technique de côté. Parlons de votre quotidien.</p>
        </div>

        <div class="max-w-5xl mx-auto bg-brand-surface/50 backdrop-blur-xl border border-brand-border rounded-3xl p-8 md:p-16 relative overflow-hidden shadow-2xl">
            
            <form id="keyboard-finder-form" class="relative z-10">
                <!-- Step 1: Mission -->
                <div class="finder-step active" data-step="1">
                    <h3 class="text-3xl font-bold text-white mb-10 text-center">Quelle est votre mission principale ?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <label class="cursor-pointer group">
                            <input type="radio" name="mission" value="gaming" class="hidden peer">
                            <div class="p-8 bg-brand-dark border border-brand-border rounded-2xl peer-checked:border-brand-primary peer-checked:bg-brand-primary/10 transition-all hover:border-brand-secondary/50 group-hover:shadow-lg h-full flex flex-col items-center justify-center text-center">
                                <span class="text-5xl mb-6">🎮</span>
                                <span class="font-bold text-white text-xl mb-2">Gaming & Streaming</span>
                                <span class="text-sm text-gray-400">Performance pure, zéro latence.</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="mission" value="coding" class="hidden peer">
                            <div class="p-8 bg-brand-dark border border-brand-border rounded-2xl peer-checked:border-brand-primary peer-checked:bg-brand-primary/10 transition-all hover:border-brand-secondary/50 group-hover:shadow-lg h-full flex flex-col items-center justify-center text-center">
                                <span class="text-5xl mb-6">💻</span>
                                <span class="font-bold text-white text-xl mb-2">Code & Dev</span>
                                <span class="text-sm text-gray-400">Flow ininterrompu, focus total.</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="mission" value="writing" class="hidden peer">
                            <div class="p-8 bg-brand-dark border border-brand-border rounded-2xl peer-checked:border-brand-primary peer-checked:bg-brand-primary/10 transition-all hover:border-brand-secondary/50 group-hover:shadow-lg h-full flex flex-col items-center justify-center text-center">
                                <span class="text-5xl mb-6">✍️</span>
                                <span class="font-bold text-white text-xl mb-2">Création & Rédaction</span>
                                <span class="text-sm text-gray-400">Confort et inspiration.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Environment -->
                <div class="finder-step hidden" data-step="2">
                    <h3 class="text-3xl font-bold text-white mb-10 text-center">Où travaillez-vous ?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                        <label class="cursor-pointer group">
                            <input type="radio" name="environment" value="private" class="hidden peer">
                            <div class="p-8 bg-brand-dark border border-brand-border rounded-2xl peer-checked:border-brand-primary peer-checked:bg-brand-primary/10 transition-all hover:border-brand-secondary/50 group-hover:shadow-lg h-full flex flex-col items-center justify-center text-center">
                                <span class="text-5xl mb-6">🏠</span>
                                <span class="font-bold text-white text-xl mb-2">Sanctuaire Privé</span>
                                <span class="text-sm text-gray-400">Le bruit n'est pas un problème (j'aime le clic !).</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="environment" value="shared" class="hidden peer">
                            <div class="p-8 bg-brand-dark border border-brand-border rounded-2xl peer-checked:border-brand-primary peer-checked:bg-brand-primary/10 transition-all hover:border-brand-secondary/50 group-hover:shadow-lg h-full flex flex-col items-center justify-center text-center">
                                <span class="text-5xl mb-6">🏢</span>
                                <span class="font-bold text-white text-xl mb-2">Espace Partagé / Open Space</span>
                                <span class="text-sm text-gray-400">Silence requis, discrétion absolue.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Step 3: Space -->
                <div class="finder-step hidden" data-step="3">
                    <h3 class="text-3xl font-bold text-white mb-10 text-center">Quelle place avez-vous sur votre bureau ?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <label class="cursor-pointer group">
                            <input type="radio" name="space" value="tiny" class="hidden peer">
                            <div class="p-8 bg-brand-dark border border-brand-border rounded-2xl peer-checked:border-brand-primary peer-checked:bg-brand-primary/10 transition-all hover:border-brand-secondary/50 group-hover:shadow-lg h-full flex flex-col items-center justify-center text-center">
                                <span class="text-5xl mb-6">🤏</span>
                                <span class="font-bold text-white text-xl mb-2">Minimaliste</span>
                                <span class="text-sm text-gray-400">Je veux optimiser chaque cm².</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="space" value="average" class="hidden peer">
                            <div class="p-8 bg-brand-dark border border-brand-border rounded-2xl peer-checked:border-brand-primary peer-checked:bg-brand-primary/10 transition-all hover:border-brand-secondary/50 group-hover:shadow-lg h-full flex flex-col items-center justify-center text-center">
                                <span class="text-5xl mb-6">📏</span>
                                <span class="font-bold text-white text-xl mb-2">Standard</span>
                                <span class="text-sm text-gray-400">J'ai de la place, mais j'aime l'ordre.</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="space" value="massive" class="hidden peer">
                            <div class="p-8 bg-brand-dark border border-brand-border rounded-2xl peer-checked:border-brand-primary peer-checked:bg-brand-primary/10 transition-all hover:border-brand-secondary/50 group-hover:shadow-lg h-full flex flex-col items-center justify-center text-center">
                                <span class="text-5xl mb-6">🚢</span>
                                <span class="font-bold text-white text-xl mb-2">Grand Luxe</span>
                                <span class="text-sm text-gray-400">J'ai besoin de toutes les touches (pavé num inclus).</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Result -->
                <div id="finder-result" class="hidden text-center py-8">
                    <div class="inline-block p-4 rounded-full bg-brand-accent/20 text-brand-accent mb-6">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-4xl font-bold text-white mb-6">Configuration Identifiée</h3>
                    <p class="text-xl text-gray-400 mb-10">Voici le setup parfait pour votre profil :</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto mb-12">
                        <div class="p-6 bg-brand-dark rounded-xl border border-brand-border">
                            <span class="block text-gray-500 text-sm uppercase tracking-wider mb-2">Format</span>
                            <span id="result-format" class="text-2xl font-bold text-white">TKL (80%)</span>
                        </div>
                        <div class="p-6 bg-brand-dark rounded-xl border border-brand-border">
                            <span class="block text-gray-500 text-sm uppercase tracking-wider mb-2">Switch</span>
                            <span id="result-switch" class="text-2xl font-bold text-white">Brown (Tactile)</span>
                        </div>
                        <div class="p-6 bg-brand-dark rounded-xl border border-brand-border">
                            <span class="block text-gray-500 text-sm uppercase tracking-wider mb-2">Série</span>
                            <span id="result-series" class="text-2xl font-bold text-white">Pro Creator</span>
                        </div>
                    </div>

                    <div>
                        <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="bg-brand-primary text-white font-bold py-4 px-12 rounded-xl hover:bg-indigo-500 transition-all shadow-lg hover:shadow-indigo-500/50 text-lg">
                            Voir ma sélection
                        </a>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-12 flex justify-between items-center" id="finder-nav">
                    <button type="button" id="prev-btn" class="hidden px-6 py-3 rounded-lg text-gray-400 hover:text-white hover:bg-brand-surface transition-colors">
                        ← Retour
                    </button>
                    <div class="flex gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand-primary step-dot"></span>
                        <span class="w-2 h-2 rounded-full bg-brand-border step-dot"></span>
                        <span class="w-2 h-2 rounded-full bg-brand-border step-dot"></span>
                    </div>
                    <button type="button" id="next-btn" class="bg-white text-brand-dark px-8 py-3 rounded-lg font-bold hover:bg-gray-200 transition-colors">
                        Continuer
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="py-32 bg-brand-surface text-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <span class="text-brand-primary font-bold tracking-wider uppercase text-sm mb-2 block">Collection Exclusive</span>
                <h2 class="text-4xl md:text-5xl font-display font-bold">Nos Best-Sellers</h2>
            </div>
            <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="text-gray-400 hover:text-white transition-colors flex items-center gap-2 group">
                Voir tout le catalogue <span class="group-hover:translate-x-1 transition-transform">→</span>
            </a>
        </div>
        
        <div class="woocommerce">
            <?php echo do_shortcode('[featured_products limit="4" columns="4"]'); ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('.finder-step');
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const resultSection = document.getElementById('finder-result');
    const navSection = document.getElementById('finder-nav');
    const dots = document.querySelectorAll('.step-dot');
    let currentStep = 0;

    function updateDots(index) {
        dots.forEach((dot, i) => {
            if (i <= index) {
                dot.classList.remove('bg-brand-border');
                dot.classList.add('bg-brand-primary');
            } else {
                dot.classList.add('bg-brand-border');
                dot.classList.remove('bg-brand-primary');
            }
        });
    }

    function showStep(index) {
        steps.forEach((step, i) => {
            if (i === index) {
                step.classList.remove('hidden');
                setTimeout(() => step.classList.add('active'), 50);
            } else {
                step.classList.add('hidden');
                step.classList.remove('active');
            }
        });

        updateDots(index);


        if (index === 0) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }

        if (index === steps.length - 1) {
            nextBtn.textContent = 'Découvrir mon setup';
        } else {
            nextBtn.textContent = 'Continuer';
        }
    }

    nextBtn.addEventListener('click', function() {
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        } else {

            const mission = document.querySelector('input[name="mission"]:checked')?.value || 'gaming';
            const env = document.querySelector('input[name="environment"]:checked')?.value || 'private';
            const space = document.querySelector('input[name="space"]:checked')?.value || 'average';

            let format = 'TKL (80%)';
            let switchType = 'Brown (Tactile)';
            let series = 'Pro Creator';


            if (space === 'tiny') format = 'Compact (60%)';
            if (space === 'massive') format = 'Full Size (100%)';

            if (env === 'shared') switchType = 'Red (Silent Linear)';
            if (env === 'private' && mission === 'writing') switchType = 'Blue (Clicky)';

            if (mission === 'gaming') series = 'Esports Elite';
            if (mission === 'coding') series = 'Dev Master';


            document.getElementById('result-format').textContent = format;
            document.getElementById('result-switch').textContent = switchType;
            document.getElementById('result-series').textContent = series;


            steps[currentStep].classList.add('hidden');
            navSection.classList.add('hidden');
            resultSection.classList.remove('hidden');
        }
    });

    prevBtn.addEventListener('click', function() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });
});
</script>

<?php get_footer(); ?>
