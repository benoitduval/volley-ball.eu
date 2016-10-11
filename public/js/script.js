$(function () {
    fab();
    map();
    datepicker();
    carousel();
    switcher();
    rotating();
    display();
    response();
    deleteUser();
    grow();
    filtering();
});


/* =========================================
 *  map 
 *  =======================================*/

function map() {
    if (($("#map").length > 0)) {
        var long = $("#map").attr('data-long');
        var lat = $("#map").attr('data-lat');
        var styles = [{"featureType": "landscape", "stylers": [{"saturation": -100}, {"lightness": 65}, {"visibility": "on"}]}, {"featureType": "poi", "stylers": [{"saturation": -100}, {"lightness": 51}, {"visibility": "simplified"}]}, {"featureType": "road.highway", "stylers": [{"saturation": -100}, {"visibility": "simplified"}]}, {"featureType": "road.arterial", "stylers": [{"saturation": -100}, {"lightness": 30}, {"visibility": "on"}]}, {"featureType": "road.local", "stylers": [{"saturation": -100}, {"lightness": 40}, {"visibility": "on"}]}, {"featureType": "transit", "stylers": [{"saturation": -100}, {"visibility": "simplified"}]}, {"featureType": "administrative.province", "stylers": [{"visibility": "off"}]}, {"featureType": "water", "elementType": "labels", "stylers": [{"visibility": "on"}, {"lightness": -25}, {"saturation": -100}]}, {"featureType": "water", "elementType": "geometry", "stylers": [{"hue": "#ffff00"}, {"lightness": -25}, {"saturation": -97}]}];
        map = new GMaps({
        el: '#map',
        lat: lat,
        lng: long,
        zoom:17,
        zoomControl: false,
        // zoomControlOpt: {
        //     style: 'SMALL',
        //     position: 'BOTTOM_LEFT'
        // },
        panControl: false,
        streetViewControl: false,
        mapTypeControl: false,
        overviewMapControl: false,
        scrollwheel: false,
        draggable: true,
        // styles: styles
        });

        var image = '/img/marker.png';

        map.addMarker({
        lat: lat,
        lng: long,
        icon: image/* ,
         title: '',
         infoWindow: {
         content: '<p>HTML Content</p>'
         }*/
        });
    }
}

function datepicker() {
    if (($("#datepicker").length > 0)) {
        $('#datepicker').bootstrapMaterialDatePicker({ 
            format : 'DD/MM/YYYY HH:mm',
            lang : 'fr',
            weekStart : 1,
            cancelText : 'ANNULER',
            minDate : new Date(),
        });
    }

    if (($("#datetimepicker").length > 0)) {
        $('#datetimepicker').bootstrapMaterialDatePicker({ 
            format : 'HH:mm',
            lang : 'fr',
            cancelText : 'ANNULER',
            date : false
        });
    }
}

/* =========================================
 * Carousel
 *  =======================================*/

function carousel() {
    $(".owl-carousel").each(function () {
        $(this).owlCarousel({
            responsiveClass:true,
            navText: ['<i class="fa fa-backward" aria-hidden="true"></i>',
                      '<i class="fa fa-forward" aria-hidden="true"></i>'
            ],
            responsive:{
                0:{
                    autoHeight:true,
                    items:1,
                    nav:true,
                    dots: true
                },
                600:{
                    autoHeight:true,
                    items:2,
                    nav:true,
                    dots: true
                },
                1000:{
                    autoHeight:false,
                    items:4,
                    nav:false,
                    dots: false
                }
            }
        });
    });

    $(".owl-carousel-3").each(function () {
        $(this).owlCarousel({
            responsiveClass:true,
            navText: ['<i class="fa fa-backward" aria-hidden="true"></i>',
                      '<i class="fa fa-forward" aria-hidden="true"></i>'
            ],
            responsive:{
                0:{
                    autoHeight:true,
                    items:1,
                    nav:false,
                    dots: true
                },
                600:{
                    autoHeight:true,
                    items:3,
                    nav:false,
                    dots: false
                },
                1000:{
                    autoHeight:false,
                    items:3,
                    nav:false,
                    dots: false
                }
            }
        });
    });
}

function fab() {
    if (($(".fixed-action-btn").length > 0)) {
        window_width = $(window).width();
        if (window_width < 992){
            $('.fixed-action-btn').addClass('click-to-toggle');
        }
    }
}

function switcher()
{
    if ($("[name='users']").length) {
        $('input[name="users"]').on('click', function(event) {
            var userId  = $(this).attr('data-user');
            var groupId = $(this).attr('data-group');
            var value = $(this).attr("value");

            var url = '/api/user/grant/' + groupId + '/' + userId + '/' + value;
            var request = $.ajax({
                type: "GET",
                url: url
            });
        });
    }

    if ($('input[name="notification"]').length) {
        $('input[name="notification"]').on('click', function(event) {
            var id    = $(this).attr('data-notif');
            var value = $(this).attr("value");
            if (value == 1) {
                $(this).attr("value", 2);
            } else {
                $(this).attr("value", 1);
            }
            var url = '/api/user/params/' + id + '/' + value;
            var request = $.ajax({
                type: "GET",
                url: url
            });
        });
    }
}

function rotating()
{
    if (($(".card-container").length > 0)) {
        window_width = $(window).width();
        if (window_width < 992) {
            $(".card-container").each(function () {
                $(this).addClass('manual-flip');
            });
        }
    }
}

function rotateCard(btn){
    var $card = $(btn).closest('.card-container');
    if($card.hasClass('hover')){
        $card.removeClass('hover');
    } else {
        $card.addClass('hover');
    }
}

function display()
{
    if ($(".display").length) {
        $('.display').on('click', function(event) {
            event.preventDefault();
            var display = $(this).attr('data-display');

            var url = '/api/user/display/' + display;
            var request = $.ajax({
                type: "GET",
                url: url
            }).done(function() {
                location.reload();
            });
        });
    }
}

function response()
{
    if ($(".response").length) {
        $('.response').on('click', function(event) {
            event.preventDefault();
            el = $(this);
            var response = $(this).attr('data-response');
            var eventId  = $(this).attr('data-event');

            // var header = $(this).find('div.header');

            if (response == 1) {
                var txt = 'Présent';
                var headerColor = 'header-success';
                var titleColor = 'text-success';
            }

            if (response == 2) {
                var text = 'Absent';
                var headerColor = 'header-danger';
                var titleColor = 'text-danger';
            }

            if (response == 3) {
                var text = 'À confirmer';
                var headerColor = 'header-warning';
                var titleColor = 'text-warning';
            }

            var header = $('#header-' + eventId);
            header.removeClass('header-success');
            header.removeClass('header-warning');
            header.removeClass('header-danger');
            header.removeClass('header-primary');
            header.addClass(headerColor);

            if ($("#title-" + eventId).length) {
                var title = $("#title-" + eventId);
                title.removeClass('text-success');
                title.removeClass('text-warning');
                title.removeClass('text-danger');
                title.removeClass('text-primary');
                title.addClass(titleColor);
            }

            var url = '/api/guest/response/' + eventId + '/' + response;
            var request = $.ajax({
                type: "GET",
                url: url
            }).done(function(resp) {

                var result = jQuery.parseJSON(resp);
                $.each( result.counts, function( key, value ) {
                    if ($("#resp-" + eventId + '-' + key).length) {
                        $("#resp-" + eventId + '-' + key).html(value);
                    }
                });

            });
        });
    }
}

function grow()
{
    $('input[type=text], textarea').each(function () {
        $(this).autogrow({vertical: true, horizontal: false});
    });
}

function filtering()
{
    $('.group-filter').on('click', function(event) {
        var el = $(this);
        $('.event-card').each(function() {
            if ($(this).attr('data-brand') == el.attr('data-brand')) {
                if (el.prop('checked')) {
                    el.prop( "checked", true);
                    $(this).animate({
                        // width: [ "toggle", "swing" ],
                        // height: [ "toggle", "swing" ],
                        opacity: "toggle"
                      }, 500, function() {
                        // Animation complete.
                      });
                    $(this).css('display', 'block');
                } else {
                    el.prop( "checked", false);
                    $(this).animate({
                        // width: [ "toggle", "swing" ],
                        // height: [ "toggle", "swing" ],
                        opacity: "toggle"
                      }, 500, function() {
                        // Animation complete.
                      });
                    // $(this).css('display', 'none');
                }
            }
        });
    });
}

function deleteUser() {

    $(document).on("click", ".open-delete-modal", function () {
         var url = $(this).data('url');
         $("#delete-url").attr('href', url);
    });
}