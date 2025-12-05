/**
 * JavaScript pour la page d'administration
 * 
 * @package Product_Reviews
 */

jQuery(document).ready(function($) {
    
    /**
     * APPROUVER UN AVIS
     * Quand on clique sur le bouton "Approuver"
     */
    $('.pr-approve-btn').on('click', function(e) {
        e.preventDefault(); // Empêcher le comportement par défaut
        
        // Récupérer l'ID de l'avis
        var reviewId = $(this).data('review-id');
        var button = $(this);
        var row = button.closest('tr');
        
        // Désactiver le bouton pendant le traitement
        button.prop('disabled', true).text('Approbation...');
        
        // Requête AJAX vers WordPress
        $.ajax({
            url: prAdmin.ajaxUrl,          // URL d'AJAX WordPress
            type: 'POST',
            data: {
                action: 'pr_approve_review', // Nom de l'action PHP
                review_id: reviewId,
                nonce: prAdmin.nonce         // Token de sécurité
            },
            success: function(response) {
                if (response.success) {
                    // Succès : afficher un message
                    showAdminMessage('Avis approuvé avec succès !', 'success');
                    
                    // Remplacer le bouton par un indicateur "Approuvé"
                    button.parent().html(
                        '<span class="dashicons dashicons-yes-alt" style="color: green;"></span> ' +
                        '<em>Approuvé</em>'
                    );
                    
                    // Animation de la ligne
                    row.css('background-color', '#d4edda')
                       .animate({ backgroundColor: '#fff' }, 2000);
                    
                } else {
                    // Erreur
                    showAdminMessage('Erreur : ' + response.data.message, 'error');
                    button.prop('disabled', false).text('Approuver');
                }
            },
            error: function() {
                showAdminMessage('Erreur de connexion au serveur', 'error');
                button.prop('disabled', false).text('Approuver');
            }
        });
    });
    
    /**
     * SUPPRIMER UN AVIS
     * Quand on clique sur le bouton "Supprimer"
     */
    $('.pr-delete-btn').on('click', function(e) {
        e.preventDefault();
        
        // Demander confirmation
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet avis ? Cette action est irréversible.')) {
            return;
        }
        
        var reviewId = $(this).data('review-id');
        var button = $(this);
        var row = button.closest('tr');
        
        // Animation de suppression
        row.addClass('deleting');
        button.prop('disabled', true).text('Suppression...');
        
        // Requête AJAX
        $.ajax({
            url: prAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'pr_delete_review',
                review_id: reviewId,
                nonce: prAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Succès : supprimer la ligne avec animation
                    row.fadeOut(400, function() {
                        $(this).remove();
                        showAdminMessage('Avis supprimé avec succès', 'success');
                    });
                } else {
                    // Erreur
                    showAdminMessage('Erreur : ' + response.data.message, 'error');
                    row.removeClass('deleting');
                    button.prop('disabled', false).text('Supprimer');
                }
            },
            error: function() {
                showAdminMessage('Erreur de connexion au serveur', 'error');
                row.removeClass('deleting');
                button.prop('disabled', false).text('Supprimer');
            }
        });
    });
    
    /**
     * AFFICHER UN MESSAGE D'ADMINISTRATION
     * 
     * @param {string} message - Le texte du message
     * @param {string} type - 'success' ou 'error'
     */
    function showAdminMessage(message, type) {
        // Créer le div de message
        var $message = $('<div class="pr-admin-message ' + type + '">' + message + '</div>');
        
        // L'insérer en haut de la page
        $('.pr-tab-content').prepend($message);
        
        // Le faire disparaître après 5 secondes
        setTimeout(function() {
            $message.fadeOut(400, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * ANIMATION DES CARTES DE STATISTIQUES
     * Quand on arrive sur l'onglet Statistiques
     */
    if ($('.pr-stats-cards').length) {
        // Animer l'apparition des cartes
        $('.pr-stat-card').each(function(index) {
            var $card = $(this);
            setTimeout(function() {
                $card.css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                }).animate({
                    'opacity': '1'
                }, 300, function() {
                    $(this).css('transform', 'translateY(0)');
                });
            }, index * 100); // Décalage de 100ms entre chaque carte
        });
    }
    
    /**
     * ANIMATION DES BARRES DE PROGRESSION
     */
    if ($('.pr-progress-fill').length) {
        $('.pr-progress-fill').each(function() {
            var $bar = $(this);
            var width = $bar.css('width');
            $bar.css('width', '0');
            
            setTimeout(function() {
                $bar.css('width', width);
            }, 500);
        });
    }
    
});