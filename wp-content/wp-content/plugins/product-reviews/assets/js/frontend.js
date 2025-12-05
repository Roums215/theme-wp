/**
 * JavaScript pour le site public (frontend)
 * 
 * @package Product_Reviews
 */

jQuery(document).ready(function($) {
    
    /**
     * SOUMETTRE UN AVIS
     * Quand l'utilisateur envoie le formulaire
     */
    $('#pr-review-form').on('submit', function(e) {
        e.preventDefault(); // Empêcher le rechargement de la page
        
        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var $message = $('.pr-form-message');
        
        // Récupérer les données du formulaire
        var formData = {
            action: 'pr_submit_review',        // Action PHP à appeler
            nonce: prFrontend.nonce,            // Token de sécurité
            product_id: $form.find('[name="product_id"]').val(),
            author_name: $form.find('[name="author_name"]').val(),
            author_email: $form.find('[name="author_email"]').val(),
            rating: $form.find('[name="rating"]:checked').val(),
            review_text: $form.find('[name="review_text"]').val()
        };
        
        // Vérifier que tous les champs obligatoires sont remplis
        if (!formData.author_name || !formData.author_email || !formData.rating) {
            showMessage('Veuillez remplir tous les champs obligatoires.', 'error');
            return;
        }
        
        // Désactiver le bouton pendant l'envoi
        $button.prop('disabled', true).text('Envoi en cours...');
        
        // Cacher les anciens messages
        $message.removeClass('show success error');
        
        // Envoyer la requête AJAX
        $.ajax({
            url: prFrontend.ajaxUrl,  // URL d'AJAX WordPress
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Succès !
                    showMessage(response.data.message, 'success');
                    
                    // Réinitialiser le formulaire
                    $form[0].reset();
                    
                    // Réinitialiser les étoiles
                    $('.pr-star-rating input').prop('checked', false);
                    
                } else {
                    // Erreur
                    showMessage(response.data.message, 'error');
                }
                
                // Réactiver le bouton
                $button.prop('disabled', false).text('Envoyer mon avis');
            },
            error: function() {
                showMessage('Erreur de connexion. Veuillez réessayer.', 'error');
                $button.prop('disabled', false).text('Envoyer mon avis');
            }
        });
    });
    
    /**
     * AFFICHER UN MESSAGE
     * 
     * @param {string} message - Le texte du message
     * @param {string} type - 'success' ou 'error'
     */
    function showMessage(message, type) {
        var $message = $('.pr-form-message');
        
        // Définir le message et le type
        $message
            .removeClass('success error')
            .addClass(type)
            .html(message)
            .addClass('show');
        
        // Faire défiler jusqu'au message
        $('html, body').animate({
            scrollTop: $message.offset().top - 100
        }, 500);
        
        // Cacher automatiquement après 8 secondes (seulement si succès)
        if (type === 'success') {
            setTimeout(function() {
                $message.removeClass('show');
            }, 8000);
        }
    }
    
    /**
     * VALIDATION EN TEMPS RÉEL
     * Afficher les erreurs pendant que l'utilisateur tape
     */
    
    // Validation de l'email
    $('[name="author_email"]').on('blur', function() {
        var email = $(this).val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            $(this).css('border-color', '#d63638');
            if (!$(this).next('.field-error').length) {
                $(this).after('<span class="field-error" style="color: #d63638; font-size: 13px;">Email invalide</span>');
            }
        } else {
            $(this).css('border-color', '#ddd');
            $(this).next('.field-error').remove();
        }
    });
    
    // Validation du nom
    $('[name="author_name"]').on('blur', function() {
        var name = $(this).val();
        
        if (!name || name.length < 2) {
            $(this).css('border-color', '#d63638');
        } else {
            $(this).css('border-color', '#ddd');
        }
    });
    
    /**
     * EFFET VISUEL DES ÉTOILES
     * Améliorer l'expérience utilisateur
     */
    $('.pr-star-rating label').on('mouseenter', function() {
        $(this).css('transform', 'scale(1.2)');
    }).on('mouseleave', function() {
        $(this).css('transform', 'scale(1)');
    });
    
    /**
     * COMPTEUR DE CARACTÈRES POUR LE TEXTAREA
     * Optionnel : afficher le nombre de caractères restants
     */
    var maxLength = 500;
    var $textarea = $('[name="review_text"]');
    
    if ($textarea.length) {
        // Ajouter un compteur sous le textarea
        $textarea.after('<div class="char-counter" style="text-align: right; font-size: 13px; color: #666; margin-top: 5px;"><span class="current">0</span> / ' + maxLength + ' caractères</div>');
        
        var $counter = $('.char-counter .current');
        
        // Mettre à jour le compteur
        $textarea.on('input', function() {
            var length = $(this).val().length;
            $counter.text(length);
            
            // Changer la couleur si on approche de la limite
            if (length > maxLength * 0.9) {
                $counter.css('color', '#d63638');
            } else {
                $counter.css('color', '#666');
            }
            
            // Limiter à la longueur maximale
            if (length > maxLength) {
                $(this).val($(this).val().substring(0, maxLength));
                $counter.text(maxLength);
            }
        });
    }
    
    /**
     * ANIMATION DES CARTES D'AVIS
     * Faire apparaître les avis avec une animation
     */
    if ($('.review-card').length) {
        $('.review-card').each(function(index) {
            var $card = $(this);
            setTimeout(function() {
                $card.css({
                    'opacity': '0',
                    'transform': 'translateY(30px)'
                }).animate({
                    'opacity': '1'
                }, 400, function() {
                    $(this).css('transform', 'translateY(0)');
                });
            }, index * 150); // Décalage de 150ms entre chaque carte
        });
    }
    
});