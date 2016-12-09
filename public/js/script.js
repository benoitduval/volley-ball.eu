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
    pills();
    table();
    comment();
    share();
    badge();
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
    $(".date-only-picker").each(function () {
        $(this).bootstrapMaterialDatePicker({ 
            format : 'DD/MM/YYYY',
            lang : 'fr',
            weekStart : 1,
            cancelText : 'ANNULER',
            minDate : new Date(),
            time : false
        });
    });

    if (($("#datepicker").length > 0)) {
        $('#datepicker').bootstrapMaterialDatePicker({ 
            format : 'DD/MM/YYYY HH:mm',
            lang : 'fr',
            weekStart : 1,
            cancelText : 'Annuler',
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
        if (window_width < 992){
            $('.fixed-action-btn').addClass('click-to-toggle');
        }
    }
}

function switcher()
{
    $('input[name="users"]').each(function () {
        $(this).on('click', function(event) {
            var userId  = $(this).attr('data-user');
            var groupId = $(this).attr('data-group');
            var value = $(this).attr("value");
            if (value == 0) {
                $(this).attr("value", 1);
            } else {
                $(this).attr("value", 0);
            }
            var url = '/api/user/grant/' + groupId + '/' + userId + '/' + value;
            var request = $.ajax({
                type: "GET",
                url: url
            }).done(function() {
                notify('Enregistré', true);
            });
        });
    });

    $('input[name="notification"]').each(function() {
        $(this).on('click', function(event) {
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
            }).done(function() {
                notify('Enregistré', true);
            });
        });
    });

    $('input[name="recurent"]').each(function () {
        $(this).on('click', function(event) {
            var id    = $(this).attr('data-id');
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
            }).done(function() {
                notify('Enregistré', true);
            });
        });
    });
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

            if (response == 1) {
                var text = 'Présent';
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

            if ($("#presence-" + eventId).length) {
                $("#presence-" + eventId).html(text);
            }

            var url = '/api/guest/response/' + eventId + '/' + response;
            var request = $.ajax({
                type: "GET",
                url: url
            }).done(function(resp) {

                notify('Enregistré', true);

                var result = jQuery.parseJSON(resp);
                $.each( result.counters, function( key, value ) {
                    if ($("#resp-" + eventId + '-' + key).length) {
                        $("#resp-" + eventId + '-' + key).html(value);
                    }
                });

                $.each( result.users, function( key, values ) {
                    if ($("#list-" + key).length) {
                        $("#list-" + key).html('');
                        $.each(values, function (index, value) {
                            $("#list-" + key).prepend(
                            '<p class="classic">' + value + '</p>');
                        });
                    }
                });

                var $owl = $('.owl-item.active');
                $owl.trigger('next.owl.carousel');
                $owl.trigger('prev.owl.carousel');

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

function pills() {
    if (($(".nav-pills").length > 0)) {
        window_width = $(window).width();
        if (window_width < 992) {
            $(".nav-pills").each(function () {
                $(this).removeClass('nav-pills');
                $(this).children('li').addClass('col-xs-4')
            });
        }
    }
}

function deleteUser() {

    $(document).on("click", ".open-delete-modal", function () {
         var url = $(this).data('url');
         $("#delete-url").attr('href', url);
    });
}

function table() {
    $().ready(function(){
        $('.ftable').each(function() {
            $(this).bootstrapTable({
                toolbar: ".toolbar",

                showRefresh: false,
                search: true,
                showToggle: false,
                showColumns: true,
                pagination: true,
                striped: true,
                sortable: true,
                pageSize: 5,
                pageList: [5,10,25,50,100],

                formatShowingRows: function(pageFrom, pageTo, totalRows) {
                    //do nothing here, we don't want to show the text "showing x of y from..." 
                },
                formatRecordsPerPage: function(pageNumber){
                    return pageNumber + " lignes";
                }
            });
        })
    });
}

function comment() {
    if ($("#submit-comment").length) {
        $('#submit-comment').on('click', function(event) {
            $('#modale-comment').toggle();
            $('.modal-backdrop').remove();
            $('.index-page').removeClass('modal-open');
            event.preventDefault();
            var notify = $.notify('Envoi des notifications...', {
                type: 'info',
                placement: {
                    from: "top",
                    align: "center"
                },
                allow_dismiss: false,
                delay: 2000,
                animate: {
                    enter: 'animated bounceInDown',
                    exit: 'animated bounceOutUp'
                },
                icon_type: 'class',
                template: '<div data-notify="container" class="text-center col-xs-6 col-sm-3 alert alert-{0}" style="border-radius:3px;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<span data-notify="icon"></span> ' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span data-notify="message">{2}</span>' +
                    '<div class="progress" data-notify="progressbar">' +
                        '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                    '</div>' +
                    '<a href="{3}" target="{4}" data-notify="url"></a>' +
                '</div>' 
            });
            var groupId = $(this).attr('data-groupId');
            var eventId = $(this).attr('data-eventId');
            var comment = $('textarea[name=comment]').val();
            var url = '/api/comment/' + eventId + '/' + groupId;
            var request = $.ajax({
                type: "POST",
                url: url,
                data: {
                    comment: comment
                }
            }).done(function(resp) {
                if ($('.no-comment').length > 0) {
                    $('.no-comment').remove();
                }
                var result = jQuery.parseJSON(resp);
                if (result.success) {
                    $('.comment-title').after('<div class="card card-nav-tabs">'
                        + '<div class="content">'
                            + '<div class="tab-content">'
                                + '<div class="tab-pane active" id="profile">'
                                    + '<blockquote class="comment">'
                                        + '<p>'
                                            + nl2br(comment) + '</p>'
                                        + '<small>'
                                            + result.user + ' - <i>' + result.date + '</i>'
                                        + '</small>'
                                    + '</blockquote>'
                                + '</div>'
                            + '</div>'
                        + '</div>'
                    + '</div>');
                    notify.update({'type': 'success', 'message': 'Envoyé', 'progress': 25, delay: 5000});
                    notify.close();
                } else {
                    notify.update({'type': 'error', 'message': '<strong>Erreur</strong> pendant l\'envoi', 'progress': 25});
                }
            });
        });
    }
}

function share() {
    if ($("#submit-share").length) {
        $('#submit-share').on('click', function(event) {
            var notify = $.notify('Envoi des notifications...', {
                type: 'info',
                placement: {
                    from: "top",
                    align: "center"
                },
                allow_dismiss: false,
                delay: 5000,
                animate: {
                    enter: 'animated bounceInDown',
                    exit: 'animated bounceOutUp'
                },
                icon_type: 'class',
                template: '<div data-notify="container" class="text-center col-xs-6 col-sm-3 alert alert-{0}" style="border-radius:3px;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<span data-notify="icon"></span> ' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span data-notify="message">{2}</span>' +
                    '<div class="progress" data-notify="progressbar">' +
                        '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                    '</div>' +
                    '<a href="{3}" target="{4}" data-notify="url"></a>' +
                '</div>' 
            });

            $('#modale-share').toggle();
            $('.modal-backdrop').remove();
            $('.index-page').removeClass('modal-open');
             event.preventDefault();
            var groupId = $(this).attr('data-groupId');
            var emails = $('textarea[name=emails]').val();
            var url = '/api/group/share/' + groupId;
            var request = $.ajax({
                type: "POST",
                url: url,
                data: {
                    emails: emails
                }
            }).done(function(resp) {
                var result = jQuery.parseJSON(resp);
                if (result.success) {
                    notify.update({'type': 'success', 'message': 'Envoyé', 'progress': 25, delay: 5000});
                    notify.close();
                } else {
                    notify.update({'type': 'error', 'message': '<strong>Erreur</strong> pendant l\'envoi', 'progress': 25});
                }
            });

         });
    }
}

function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

function notify(txt, success) {
    if (success == true) {
        var type = 'success';
    } else {
        var type = 'danger';
    }
    $.notify({  // options
        message: txt,
    },{
        // settings
        element: 'body',
        type: type,
        allow_dismiss: true,
        placement: {
            from: "top",
            align: "center"
        },
        offset: {
            x: 0,
            y: 0,
        },
        spacing: 10,
        z_index: 1031,
        delay: 1500,
        timer: 100,
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated bounceOutUp'
        },
        icon_type: 'class',
        template: '<div data-notify="container" class="text-center col-xs-6 col-sm-3 alert alert-{0}" style="border-radius:3px;" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            '<span data-notify="icon"></span> ' +
            '<span data-notify="title">{1}</span> ' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
                '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
            '<a href="{3}" target="{4}" data-notify="url"></a>' +
        '</div>' 
    });
}

function badge() {
    $('.badge-comment').on('click', function(event) {
        event.preventDefault();
        var eventId = $(this).attr('data-eventId');
        var url = '/api/comment/cache/' + eventId;
        var request = $.ajax({
            type: "GET",
            url: url,
        }).done(function(resp) {
            window.location.href = '/event/detail/' + eventId;
        });
    });
}

$( document ).ready(function() {
    var urlHash = window.location.href.split("#")[1];
    if ($('#'+urlHash).length) {
        $('html,body').animate({scrollTop:$('#'+urlHash).offset().top}, 700);
    }
});