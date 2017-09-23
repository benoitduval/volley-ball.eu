type = ['','info','success','warning','danger'];


demo = {

    initCirclePercentage: function(){
        $('#chartDashboard, #chartOrders, #chartNewVisitors, #chartSubscriptions, #chartDashboardDoc, #chartOrdersDoc').easyPieChart({
            lineWidth: 6,
            size: 160,
            scaleColor: false,
            trackColor: 'rgba(255,255,255,.25)',
            barColor: '#FFFFFF',
            animate: ({duration: 1000, enabled: true})
        });
    },

    initGoogleMaps: function(){

        // Satellite Map
        var myLatlng = new google.maps.LatLng(40.748817, -73.985428);
        var mapOptions = {
            zoom: 3,
            scrollwheel: false, //we disable de scroll over the map, it is a really annoing when you scroll through page
            center: myLatlng,
             mapTypeId: google.maps.MapTypeId.SATELLITE
        }

        var map = new google.maps.Map(document.getElementById("satelliteMap"), mapOptions);

        var marker = new google.maps.Marker({
            position: myLatlng,
            title:"Satellite Map!"
        });

        marker.setMap(map);
    },

    initSmallGoogleMaps: function(){
        if (document.getElementById("regularMap")) {
            // Regular Map
            var myLatlng = new google.maps.LatLng(40.748817, -73.985428);
            var mapOptions = {
                zoom: 15,
                center: myLatlng,
                scrollwheel: false, //we disable de scroll over the map, it is a really annoing when you scroll through page
            }

            var map = new google.maps.Map(document.getElementById("regularMap"), mapOptions);

            var marker = new google.maps.Marker({
                position: myLatlng,
                title:"Regular Map!"
            });

            marker.setMap(map);

            // Custom Skin & Settings Map
            var myLatlng = new google.maps.LatLng(40.748817, -73.985428);
            var mapOptions = {
                zoom: 13,
                center: myLatlng,
                scrollwheel: false, //we disable de scroll over the map, it is a really annoing when you scroll through page
                disableDefaultUI: true, // a way to quickly hide all controls
                zoomControl: true,
                styles: [{"featureType":"water","stylers":[{"saturation":43},{"lightness":-11},{"hue":"#0088ff"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"hue":"#ff0000"},{"saturation":-100},{"lightness":99}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"color":"#808080"},{"lightness":54}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#ece2d9"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#ccdca1"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#767676"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]},{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#b8cb93"}]},{"featureType":"poi.park","stylers":[{"visibility":"on"}]},{"featureType":"poi.sports_complex","stylers":[{"visibility":"on"}]},{"featureType":"poi.medical","stylers":[{"visibility":"on"}]},{"featureType":"poi.business","stylers":[{"visibility":"simplified"}]}]

            }

            var map = new google.maps.Map(document.getElementById("customSkinMap"), mapOptions);

            var marker = new google.maps.Marker({
                position: myLatlng,
                title:"Custom Skin & Settings Map!"
            });

            marker.setMap(map);
        }

    },

    initStatsDashboard: function(){

        var disponibility = $("#chart-disponibility");
        if (disponibility.length > 0) {

            var data = {
              labels: ['Sep', 'Oct', 'Nov', 'Dec','Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jui', 'aou'],
              series: [
                  JSON.parse(disponibility.attr('data-last-season')),
                  JSON.parse(disponibility.attr('data-current-season'))
              ]
            };

            var options = {
                seriesBarDistance: 10,
                axisX: {
                    showGrid: false
                },
                height: "245px",
                axisY: {
                    onlyInteger: true,
                }
            };

            var responsiveOptions = [
              ['screen and (max-width: 640px)', {
                seriesBarDistance: 5,
                axisX: {
                  labelInterpolationFnc: function (value) {
                    return value[0];
                  }
                }
              }]
            ];

            Chartist.Line('#chart-disponibility', data, options, responsiveOptions);
        }

        var match = $("#chart-matches");
        if (match.length > 0) {
            var data = {
              labels: ['3/0', '3/1', '3/2', '2/3', '1/3', '0/3'],
              series: [
                JSON.parse(match.attr('data-last-season')),
                JSON.parse(match.attr('data-current-season'))
              ]
            };

            var options = {
                seriesBarDistance: 10,
                axisX: {
                    showGrid: false
                },
                axisY: {
                    onlyInteger: true,
                },
                height: "245px"
            };

            var responsiveOptions = [
              ['screen and (max-width: 640px)', {
                seriesBarDistance: 6,
                axisX: {
                  labelInterpolationFnc: function (value) {
                    return value[0];
                  }
                }
              }]
            ];

            Chartist.Bar('#chart-matches', data, options, responsiveOptions);
        }
    },

    showSwal: function(type){
        if(type == 'basic'){
            swal({
                title: "Here's a message!",
                buttonsStyling: false,
                confirmButtonClass: "btn btn-success btn-fill"
            });

        }else if(type == 'title-and-text'){
            swal({
                title: "Here's a message!",
                text: "It's pretty, isn't it?",
                buttonsStyling: false,
                confirmButtonClass: "btn btn-info btn-fill"
            });

        }else if(type == 'success-message'){
            swal({
                title: "Good job!",
                text: "You clicked the button!",
                buttonsStyling: false,
                confirmButtonClass: "btn btn-success btn-fill",
                type: "success"
            });

        }else if(type == 'warning-message-and-confirmation'){
            swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success btn-fill',
                    cancelButtonClass: 'btn btn-danger btn-fill',
                    confirmButtonText: 'Yes, delete it!',
                    buttonsStyling: false
                }).then(function() {
                  swal({
                    title: 'Deleted!',
                    text: 'Your file has been deleted.',
                    type: 'success',
                    confirmButtonClass: "btn btn-success btn-fill",
                    buttonsStyling: false
                    })
                });
        }else if(type == 'warning-message-and-cancel'){
            swal({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this imaginary file!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it',
                    confirmButtonClass: "btn btn-success btn-fill",
                    cancelButtonClass: "btn btn-danger btn-fill",
                    buttonsStyling: false
                }).then(function() {
                  swal({
                    title: 'Deleted!',
                    text: 'Your imaginary file has been deleted.',
                    type: 'success',
                    confirmButtonClass: "btn btn-success btn-fill",
                    buttonsStyling: false
                    })
                }, function(dismiss) {
                  // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                  if (dismiss === 'cancel') {
                    swal({
                      title: 'Cancelled',
                      text: 'Your imaginary file is safe :)',
                      type: 'error',
                      confirmButtonClass: "btn btn-info btn-fill",
                      buttonsStyling: false
                    })
                  }
                })

        }else if(type == 'custom-html'){
            swal({
                title: 'HTML example',
                buttonsStyling: false,
                confirmButtonClass: "btn btn-success btn-fill",
                html:
                        'You can use <b>bold text</b>, ' +
                        '<a href="http://github.com">links</a> ' +
                        'and other HTML tags'
                });

        }else if(type == 'auto-close'){
            swal({ title: "Auto close alert!",
                   text: "I will close in 2 seconds.",
                   timer: 2000,
                   showConfirmButton: false
                });
        } else if(type == 'input-field'){
            swal({
                    title: 'Input something',
                    html: '<div class="form-group">' +
                              '<input id="input-field" type="text" class="form-control" />' +
                          '</div>',
                    showCancelButton: true,
                    confirmButtonClass: 'btn btn-success btn-fill',
                    cancelButtonClass: 'btn btn-danger btn-fill',
                    buttonsStyling: false
                }).then(function(result) {
                    swal({
                        type: 'success',
                        html: 'You entered: <strong>' +
                                $('#input-field').val() +
                              '</strong>',
                        confirmButtonClass: 'btn btn-success btn-fill',
                        buttonsStyling: false

                    })
                }).catch(swal.noop)
            }
        },

    checkFullPageBackgroundImage: function(){
        $page = $('.full-page');
        image_src = $page.data('image');

        if(image_src !== undefined){
            image_container = '<div class="full-page-background" style="background-image: url(' + image_src + ') "/>'
            $page.append(image_container);
        }
    },

    initWizard: function(){
        $(document).ready(function(){

            var $validator = $("#wizardForm").validate({
              rules: {
                name: {
                    required: true,
                    minlength: 5
                },
                date: {
                    required: true,
                },
                address: {
                    required: true,
                },
                city: {
                    required: true,
                },
                place: {
                    required: true,
                },
                zipCode: {
                    required: true,
                }
              }
            });

            // you can also use the nav-pills-[blue | azure | green | orange | red] for a different color of wizard
            $('#wizardCard').bootstrapWizard({
                tabClass: 'nav nav-pills',
                nextSelector: '.btn-next',
                previousSelector: '.btn-back',
                onNext: function(tab, navigation, index) {
                    var $valid = $('#wizardForm').valid();

                    if(!$valid) {
                        $validator.focusInvalid();
                        return false;
                    }
                },
                onInit : function(tab, navigation, index){

                    //check number of tabs and fill the entire row
                    var $total = navigation.find('li').length;
                    $width = 100/$total;

                    $display_width = $(document).width();

                    if($display_width < 600 && $total > 3){
                       $width = 50;
                    }

                    navigation.find('li').css('width',$width + '%');
                },
                onTabClick : function(tab, navigation, index){
                    // Disable the posibility to click on tabs
                    return false;
                },
                onTabShow: function(tab, navigation, index) {
                    var $total = navigation.find('li').length;
                    var $current = index+1;

                    var wizard = navigation.closest('.card-wizard');

                    // If it's the last tab then hide the last button and show the finish instead
                    if($current >= $total) {
                        $(wizard).find('.btn-next').hide();
                        $(wizard).find('.btn-finish').show();
                    } else if($current == 1){
                        $(wizard).find('.btn-back').hide();
                    } else {
                        $(wizard).find('.btn-back').show();
                        $(wizard).find('.btn-next').show();
                        $(wizard).find('.btn-finish').hide();
                    }
                }
            });
        });

        function onFinishWizard(){
            //here you can do something, sent the form to server via ajax and show a success message with swal

            swal("Good job!", "You clicked the finish button!", "success");
        }
    },

    initFormExtendedSliders: function(){
        // Sliders for demo purpose in refine cards section
        var slider = document.getElementById('sliderRegular');

        noUiSlider.create(slider, {
            start: 40,
            connect: [true,false],
            range: {
                min: 0,
                max: 100
            }
        });

        var slider2 = document.getElementById('sliderDouble');

        noUiSlider.create(slider2, {
            start: [ 20, 60 ],
            connect: true,
            range: {
                min:  0,
                max:  100
            }
        });
    },



    initFormExtendedDatetimepickers: function(){
        $('.datetimepicker').datetimepicker({
            format: 'DD/MM/YYYY H:mm',    // use this format if you want the 24hours timepicker
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove'
            }
         });
    },

    initCirclePercentage: function(){
        $('#chartDashboard, #chartOrders, #chartNewVisitors, #chartSubscriptions, #chartDashboardDoc, #chartOrdersDoc').easyPieChart({
            lineWidth: 6,
            size: 160,
            scaleColor: false,
            trackColor: 'rgba(255,255,255,.25)',
            barColor: '#FFFFFF',
            animate: ({duration: 1000, enabled: true})
        });
    },

    initChartDisponibilities: function () {
        var disponibility = $("#chart-disponibilities");
        if (disponibility.length > 0) {
            var data = {
              series: JSON.parse(disponibility.attr('data-disponibilities'))
            };

            new Chartist.Pie('#chart-disponibilities', data, {
              donut: true,
              height: 260,
              donutWidth: 50,
              donutSolid: true,
              startAngle: 270,
              showLabel: true
            });
        }
    },

    initFullCalendar: function() {
        var body = document.body,
            html = document.documentElement;

        var height = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);

        if (height > 600) {
            height = height - 235;
        }

        if ($('#fullCalendar').length > 0) {
            var url = '/api/event/get/all';
            var request = $.ajax({
                type: "GET",
                url: url,
            }).done(function(resp) {

                var data  = JSON.parse(resp);
                $('#fullCalendar').fullCalendar({
                    eventAfterAllRender: function(view) {
                        $('#full-calendar-loading').hide();
                    },
                    header: {
                        right: '',
                        left: 'listMonth,month,prev,next',
                        center: 'title'
                    },
                    height: height,
                    aspectRatio: false,
                    locale: 'fr',
                    defaultDate: new Date(),
                    views: {
                        month: { // name of view
                            titleFormat: 'MMM YYYY'
                        }
                    },
                    firstDay: 1,
                    eventLimit: true, // allow "more" link when too many events
                    events: data,
                    timeFormat: 'H:mm',
                    eventClick:  function(event, jsEvent, view) {
                        jsEvent.preventDefault();
                        $('#modal-title').html(event.title);
                        $('#modal-date').html(event.date);
                        $('#modal-descrition').html(event.description);
                        $('#event-url').attr('href',event.url);
                        $('#modal-count').html(event.count);
                        $('#modal-place').html(event.place);
                        $('#modal-city').html(event.city);
                        $('#modal-zipcode').html(event.zipcode);
                        $('#modal-address').html(event.address);
                        $('#event-url-ok').attr('href',event.urlOk);
                        $('#event-url-no').attr('href',event.urlNo);
                        $('#event-url-incertain').attr('href',event.urlIncertain);
                        $('#fullCalModal').modal();
                    }
                });
            });
        }
    },

    showNotification: function(from, align){
        color = Math.floor((Math.random() * 4) + 1);

        $.notify({
            icon: "ti-gift",
            message: "Welcome to <b>Paper Dashboard</b> - a beautiful dashboard for every web developer."

        },{
            type: type[color],
            timer: 4000,
            placement: {
                from: from,
                align: align
            }
        });
    },
}
