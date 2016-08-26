$(function () {

    map();
    datepicker();
    carousel();
    // animations();
    // fullScreenContainer();
    // utils();
    // sliding();
    // counters();
    // parallax();
    // sliders();
    // form();
    // adminDate();
    // modal();
    // addPage();
    // switcher();
    // meteo();
    // signin();
    // response();
});

// $(window).load(function () {
//     windowWidth = $(window).width();
//     $(this).alignElementsSameHeight();

//     masonry();

// });
// $(window).resize(function () {

//     newWindowWidth = $(window).width();

//     if (windowWidth !== newWindowWidth) {
//     setTimeout(function () {
//         $(this).alignElementsSameHeight();
//         fullScreenContainer();
//         waypointsRefresh();
//     }, 205);
//     windowWidth = newWindowWidth;
//     }

// });

// /* =========================================
//  *  animations
//  *  =======================================*/

// function animations() {
//     if (Modernizr.csstransitions) {
//         delayTime = 0;
//         $('[data-animate]').css({opacity: '0'});
//         $('[data-animate]').waypoint(function (direction) {
//             delayTime += 150;
//             $(this).delay(delayTime).queue(function (next) {
//             $(this).toggleClass('animated');
//             $(this).toggleClass($(this).data('animate'));
//             delayTime = 0;
//             next();
//             //$(this).removeClass('animated');
//             //$(this).toggleClass($(this).data('animate'));
//             });
//         },
//             {
//                 offset: '95%',
//                 triggerOnce: true
//             });
//         $('[data-animate-hover]').hover(function () {
//             $(this).css({opacity: 1});
//             $(this).addClass('animated');
//             $(this).removeClass($(this).data('animate'));
//             $(this).addClass($(this).data('animate-hover'));
//         }, function () {
//             $(this).removeClass('animated');
//             $(this).removeClass($(this).data('animate-hover'));
//         });
//     }
// }

// /* =========================================
//  * sliding 
//  *  =======================================*/

//  function sliding() {
//      $('body').on('click', '.scrollTo, #navigation a', function (event) {
//         event.preventDefault();
//         var full_url = this.href;
//         var parts = full_url.split("#");
//         var trgt = parts[1];

//         $('body').scrollTo($('#' + trgt), 800, {offset: -80});

//      });
//  }

// /* =========================================
//  * counters 
//  *  =======================================*/

// function counters() {

//     $('.counter').counterUp({
//     delay: 10,
//     time: 1000
//     });

// }

// /* =========================================
//  * parallax 
//  *  =======================================*/

// function parallax() {

//     $('.text-parallax').parallax("50%", 0.1);
    
// }

// /* =========================================
//  *  masonry 
//  *  =======================================*/

// function masonry() {

//     $('#references-masonry').css({visibility: 'visible'});

//     $('#references-masonry').masonry({
//     itemSelector: '.reference-item:not(.hidden)',
//     isFitWidth: true,
//     isResizable: true,
//     isAnimated: true,
//     animationOptions: {
//         duration: 200,
//         easing: 'linear',
//         queue: true
//     },
//     gutter: 30
//     });
//     scrollSpyRefresh();
//     waypointsRefresh();
// }

// /* =========================================
//  * filter 
//  *  =======================================*/

// $('#filter a').click(function (e) {
//     e.preventDefault();

//     $('#filter li').removeClass('active');
//     $(this).parent('li').addClass('active');

//     var categoryToFilter = $(this).attr('data-filter');

//     $('.reference-item').each(function () {
//         if ($(this).data('category') === categoryToFilter || categoryToFilter === 'all') {
//             $(this).removeClass('hidden');
//         }
//         else {
//             $(this).addClass('hidden');
//         }
//     });

//     if ($('#detail').hasClass('open')) {
//         closeReference();
//     }
//     else {
//         $('#references-masonry').masonry('reloadItems').masonry('layout');

//     }

//     scrollSpyRefresh();
//     waypointsRefresh();
// });

// /* =========================================
//  *  open reference 
//  *  =======================================*/

// $('.reference-item').click(function (e) {
//     e.preventDefault();

//     var element = $(this);
//     var title = element.find('.reference-title').text();
//     var description = element.find('.reference-description').html();

//     images = element.find('.reference-description').data('images').split(',');

//     if (images.length > 0) {
//     slider = '';
//     for (var i = 0; i < images.length; ++i) {
//         slider = slider + '<div class="item"><img src=' + images[i] + ' alt="" class="img-responsive"></div>';
//     }
//     }
//     else {
//     slider = '';
//     }

//     $('#detail-title').text(title);
//     $('#detail-content').html(description);

//     openReference();

// });

// function openReference() {

//     $('#detail').addClass('open');
//     $('#references-masonry').animate({opacity: 0}, 300);
//     $('#detail').animate({opacity: 1}, 300);

//     setTimeout(function () {
//     $('#detail').slideDown();
//     $('#references-masonry').slideUp();

//     }, 300);

//     setTimeout(function () {
//     $('body').scrollTo($('#detail'), 1000, {offset: -80});
//     }, 500);

// }

// function closeReference() {

//     $('#detail').removeClass('open');
//     $('#detail').animate({'opacity': 0}, 300);

//     setTimeout(function () {
//     $('#detail').slideUp();
//     $('#references-masonry').slideDown().animate({'opacity': 1}, 300).masonry('reloadItems').masonry();

//     }, 300);

//     setTimeout(function () {
//     $('body').scrollTo($('#filter'), 1000, {offset: -110});
//     }, 500);


//     setTimeout(function () {
//     $('#references-masonry').masonry('reloadItems').masonry();
//     }, 800);

// }

// $('#detail .close').click(function () {
//     closeReference(true);
// })

// /* =========================================
//  * full screen intro 
//  *  =======================================*/

// function fullScreenContainer() {

//     var screenWidth = $(window).width() + "px";
//     var screenHeight = '';
//     if ($(window).height() > 500) {
//     screenHeight = $(window).height() + "px";
//     }
//     else {
//     screenHeight = "500px";
//     }


//     $("#intro, #intro .item").css({
//     width: screenWidth,
//     height: screenHeight
//     });
// }

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

// /* =========================================
//  *  UTILS
//  *  =======================================*/

// function utils() {

//     /* tooltips */
//     if ($('[data-toggle="tooltip"]').length) {
//         $('[data-toggle="tooltip"]').tooltip();
//     }

//     /* external links in new window*/

//     $('.external').on('click', function (e) {

//     e.preventDefault();
//     window.open($(this).attr("href"));
//     });
//     /* animated scrolling */

// }

// $.fn.alignElementsSameHeight = function () {
//     $('.same-height-row').each(function () {

//     var maxHeight = 0;
//     var children = $(this).find('.same-height');
//     children.height('auto');
//     if ($(window).width() > 768) {
//         children.each(function () {
//         if ($(this).innerHeight() > maxHeight) {
//             maxHeight = $(this).innerHeight();
//         }
//         });
//         children.innerHeight(maxHeight);
//     }

//     maxHeight = 0;
//     children = $(this).find('.same-height-always');
//     children.height('auto');
//     children.each(function () {
//         if ($(this).height() > maxHeight) {
//         maxHeight = $(this).innerHeight();
//         }
//     });
//     children.innerHeight(maxHeight);
//     });
// }

// /* refresh scrollspy */
// function scrollSpyRefresh() {
//     if ($('body').length) {
//         setTimeout(function () {
//         $('body').scrollspy('refresh');
//         }, 1000);
//     }
// }

// /* refresh waypoints */
// function waypointsRefresh() {
//     setTimeout(function () {
//         $.waypoints('refresh');
//     }, 1000);
// }

// function form() {
//     $('body').on('change', '#weekDay', function (event) {
//         $( "#weekDay option:selected" ).each(function() {
//             if ($( this ).text() == 'Lundi') {
//                 var day = 'Lun';
//             } else {
//                 var day = 'Mar';
//             }

//             $.each($('#date option'), function( index, element ) {
//                 if (!element.text.match("^(Date|" + day + ").*")) {
//                     $(this).hide();
//                 } else {
//                     $(this).show();
//                 }
//             });
//         });
//         $('#date').removeAttr('disabled');
//     });

//     $('body').on('change', '#date', function (event) {
//         $('#time').attr('disabled', 'disabled');
//         $("#date option:selected").each(function() {
//             var url = '/calendar/' + $(this).val();
//             var el = $('#time');
//             $('#icon-time').html('<i class="fa fa-refresh fa-spin"></i>');
//             var request = $.ajax({
//               type: "GET",
//               url: url
//             }).done(function(times) {
//                 var times = jQuery.parseJSON(times);
//                 el.empty();
//                 $.each(times, function(value, key) {
//                     var option = $('<option value="' + value + '">' + key + '</option>');
//                     el.append(option);
//                 });
//                 $('#icon-time').html('<i class="fa fa-angle-double-down">');
//                 $('#time').removeAttr('disabled');
//             });
//         });
//     });
// }

// function adminDate() {
//     $('#full-date').datetimepicker({
//         format: "dd/mm/yyyy hh:ii",
//         language: "fr",
//         autoclose: true,
//         orientation: "top left",
//         minuteStep: 60,
//         daysOfWeekDisabled: [0, 3, 4, 5, 6]
//     });
// }

// function modal() {
//     $('#notify').modal('toggle');
// }

// function addPage() {
//     $( "#creation" ).click(function() {
//         $("#select-form").fadeOut(200).css('display','none');
//         $("#create-form").fadeIn(200).css('display','block');
//     });

//     $('#date').datetimepicker({
//         format: "dd/mm/yyyy hh:ii",
//         language: "FR",
//         autoclose: true,
//         orientation: "top left",
//         minuteStep: 15
//     });

//     $("#eventSubmit").click(function() {
//         $("#eventSubmit").addClass('disabled');
//         $("#icon").removeClass("fa-share");
//         $("#icon").addClass("fa-spinner fa-spin");
//     });

//     jQuery(document).ready(function(){
//         $('input:radio[name="groups"]').change(function(){
//             var value = $(this).val();
//             var url = '/api/place/' + value;

//             var request = $.ajax({
//               type: "GET",
//               url: url
//             }).done(function(places) {
//                 var $el = $('#addresses');
//                 $el.empty();
//                 var places = jQuery.parseJSON(places);

//                 $.each(places, function(value, key) {
//                     var radioBtn = $('<label class="radio col-xs-12"><input type="radio" name="places" value="' + value + '" /> ' + key + '</label>');
//                     radioBtn.appendTo($el);
//                 });

//                 $("#address-form").fadeOut(200).css('display','none');
//                 $("#select-form").fadeIn(200).css('display','block');
//             });
//         });
//     });
// }

// function switcher() {
//     if ($("[name='my-checkbox']").length) {
//         $("[name='my-checkbox']").bootstrapSwitch();
//         $('input[name="my-checkbox"]').on('switchChange.bootstrapSwitch', function(event, state) {
//             var value = $(this).val();
//             if (state == true) {
//                 var status = 1;
//             } else {
//                 var status = 2;
//             }
//             var url = '/api/recurent/' + value + '/' + status;
//             var request = $.ajax({
//                 type: "GET",
//                 url: url
//             });
//         });
//     }

//     if ($("[name='device']").length) {
//         $("[name='device']").bootstrapSwitch();
//         $('input[name="device"]').on('switchChange.bootstrapSwitch', function(event, state) {
//             var value = $(this).val();
//             if (state == true) {
//                 var status = 1;
//             } else {
//                 var status = 2;
//             }
//             var url = '/api/pushbullet/' + value + '/' + status;
//             var request = $.ajax({
//                 type: "GET",
//                 url: url
//             });
//         });
//     }


//     if ($("[name='weather']").length) {
//         $("[name='weather']").bootstrapSwitch();
//     }
// }

// function meteo() {
//     var result = new RegExp('event/detail/([0-9]*)').exec(window.location.href);
//     if (result !== null) {
//         var eventId = result[1];
//         jQuery(document).ready(function(){

//             var url = '/api/weather/' + eventId;
//             var request = $.ajax({
//               type: "GET",
//               url: url
//             }).done(function(weather) {
//                 var weather = jQuery.parseJSON(weather);
//                 var qpf  = [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null];
//                 var temp = [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null];
//                 $.each( weather.qpf, function( index, value ) {
//                     qpf[index] = parseInt(value);
//                 });
//                 $.each( weather.temp, function( index, value ) {
//                     temp[index] = parseInt(value);
//                 });
//                 $('#global').highcharts({
//                     chart: {
//                         zoomType: 'xy'
//                     },
//                     title: {
//                         text: 'Météo pour cette adresse'
//                     },
//                     xAxis: [{
//                         categories: ['00h', '01h', '02h', '03h', '04h', '04h', '06h', '07h', '08h', '09h', '10h', '11h', '12h',
//                                      '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h'],
//                         crosshair: true
//                     }],
//                     yAxis: [{ // Primary yAxis
//                         labels: {
//                             format: '{value}°C',
//                             style: {
//                                 color: Highcharts.getOptions().colors[1]
//                             }
//                         },
//                         title: {
//                             text: 'Température',
//                             style: {
//                                 color: Highcharts.getOptions().colors[1]
//                             }
//                         }
//                     }, { // Secondary yAxis
//                         title: {
//                             text: 'Précipitation',
//                             style: {
//                                 color: Highcharts.getOptions().colors[0]
//                             }
//                         },
//                         labels: {
//                             format: '{value} mm',
//                             style: {
//                                 color: Highcharts.getOptions().colors[0]
//                             }
//                         },
//                         opposite: true
//                     }],
//                     tooltip: {
//                         shared: true
//                     },
//                     series: [{
//                         name: 'Précipitation',
//                         type: 'column',
//                         yAxis: 1,
//                         data: qpf,
//                         tooltip: {
//                             valueSuffix: ' mm'
//                         }

//                     }, {
//                         name: 'Température',
//                         type: 'spline',
//                         color: '#D75E56',
//                         data: temp,
//                         tooltip: {
//                             valueSuffix: '°C'
//                         }
//                     }]
//                 });
//             });
//         });
//     }
// }

// function response(){
//     $(".response-link").click(function () {
//         event.preventDefault();
//         var element = $(this);
//         var eventId = $(this).attr('data-event');
//         var responseId = $(this).attr('data-response');
//         var url = '/api/response/' + eventId + '/' + responseId;
//         element.parent('div').find('.given-response').remove();
//         $('<i class="fa fa-thumbs-o-up given-response"></i>').appendTo(element.children('h4'));
//         var request = $.ajax({
//             type: "GET",
//             url: url
//         });
//     });
// }

// function signin() {
//     $('#signModal').on('show.bs.modal', function (event) {
//       var button = $(event.relatedTarget) // Button that triggered the modal
//       console.log(button);
//       var recipient = button.data('whatever') // Extract info from data-* attributes
//       // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
//       // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
//       var modal = $(this)
//       modal.find('.modal-title').text('New message to ' + recipient)
//       modal.find('.modal-body input').val(recipient)
//     })
// }