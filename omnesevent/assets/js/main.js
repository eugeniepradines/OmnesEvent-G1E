$(document).ready(function () {
    $('.menu-toggle').on('click', function () {
        $('.nav-links, .nav-actions').toggleClass('open');
    });

    $('.validate-form input[required], .validate-form textarea[required]').on('input', function () {
        if ($(this).val().trim() === '') {
            $(this).addClass('invalid');
        } else {
            $(this).removeClass('invalid');
        }
    });

    $('.ajax-filters').on('submit change keyup', function (event) {
        if (event.type === 'keyup' && event.key !== undefined && event.key.length > 1) {
            return;
        }
        event.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: 'GET',
            data: form.serialize() + '&ajax=1',
            success: function (html) {
                $('#event-grid').html(html);
            }
        });
    });

    $('.tab-btn').on('click', function () {
        var tab = $(this).data('tab');
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active').hide();
        $('.' + tab).addClass('active').show();
    });
    $('.tab-content').hide();
    $('.tab-content.active, .tab-content.futur').show();

    $('.preview-input').on('change', function () {
        var fichier = this.files[0];
        var image = $('.preview-img');
        if (fichier) {
            var lecteur = new FileReader();
            lecteur.onload = function (e) {
                image.attr('src', e.target.result).show();
            };
            lecteur.readAsDataURL(fichier);
        }
    });

    $('.confirm-form').on('submit', function (event) {
        if (!confirm('Confirmer cette action ?')) {
            event.preventDefault();
        }
    });
});
