$(document).ready(function () {
    $('.reserve-btn').on('click', function () {
        var bouton = $(this);
        $.ajax({
            url: '/omnesevent/api/reserve.php',
            method: 'POST',
            dataType: 'json',
            data: {
                evenement_id: bouton.data('event'),
                csrf_token: bouton.data('token')
            },
            success: function (reponse) {
                $('#reserve-message').text(reponse.message);
                if (reponse.ok) {
                    $('#places-restantes').text(reponse.places);
                    if (reponse.action === 'annule') {
                        bouton.text(reponse.places > 0 ? 'Reserver ma place' : 'Liste d attente');
                    } else {
                        bouton.text(reponse.action === 'liste_attente' ? 'Annuler ma liste d attente' : 'Annuler ma reservation');
                    }
                }
            }
        });
    });
});
