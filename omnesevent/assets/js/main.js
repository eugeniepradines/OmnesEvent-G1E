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

    function updatePriceField() {
        $('[data-price-toggle]').each(function () {
            var wrapper = $(this);
            var mode = wrapper.find('input[name="mode_prix"]:checked').val();
            var field = wrapper.siblings('[data-price-field]');
            field.toggle(mode === 'payant');
            field.find('input').prop('required', mode === 'payant');
        });
    }
    $('[data-price-toggle] input').on('change', updatePriceField);
    updatePriceField();

    $(document).on('click', '.qr-open', function () {
        var source = $(this).data('qr-src') || $(this).find('img').attr('src').replace('size=140x140', 'size=700x700');
        var modal = $('.qr-modal');
        modal.find('img').attr('src', source);
        modal.addClass('open').attr('aria-hidden', 'false');
        $('body').addClass('modal-open');
    });

    $('.qr-modal, .qr-modal-close').on('click', function (event) {
        if ($(event.target).is('.qr-modal, .qr-modal-close')) {
            $('.qr-modal').removeClass('open').attr('aria-hidden', 'true');
            $('body').removeClass('modal-open');
        }
    });

    $(document).on('keydown', function (event) {
        if (event.key === 'Escape') {
            $('.qr-modal').removeClass('open').attr('aria-hidden', 'true');
            $('body').removeClass('modal-open');
        }
    });

    $('.confirm-form').on('submit', function (event) {
        if (!confirm('Confirmer cette action ?')) {
            event.preventDefault();
        }
    });

    var statsStarted = false;
    var statsSection = document.querySelector('.stats-section');
    if (statsSection && 'IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            if (entries[0].isIntersecting && !statsStarted) {
                statsStarted = true;
                $('.stat-number').each(function () {
                    var element = $(this);
                    var target = parseInt(element.data('target'), 10) || 0;
                    var current = 0;
                    var step = Math.max(1, Math.ceil(target / 45));
                    var timer = setInterval(function () {
                        current += step;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        element.text(current);
                    }, 24);
                });
                observer.disconnect();
            }
        }, { threshold: 0.35 });
        observer.observe(statsSection);
    } else {
        $('.stat-number').each(function () {
            $(this).text($(this).data('target'));
        });
    }
});
