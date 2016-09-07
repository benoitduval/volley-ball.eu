$(function () {
    fab();
    map();
    datepicker();
    carousel();
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
            minDate : new Date() 
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
}

function fab() {
    if (($(".fixed-action-btn").length > 0)) {
        window_width = $(window).width();
        if (window_width <= 1024){
            $('.fixed-action-btn').addClass('click-to-toggle');
        }
    }
}