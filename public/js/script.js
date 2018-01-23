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

    initSwitchers: function () {

        $('input[name="training"]').each(function () {
            $(this).on('switchChange.bootstrapSwitch', function(event, state) {
                var id    = $(this).attr('data-id');
                var value = $(this).attr("value");
                if (value == 1) {
                    $(this).attr("value", 2);
                } else {
                    $(this).attr("value", 1);
                }
                var url = '/api/recurrent/enable/' + id + '/' + value;
                var request = $.ajax({
                    type: "GET",
                    url: url
                });
            });
        });

        $('input[name="notification"]').each(function () {
            $(this).on('switchChange.bootstrapSwitch', function(event, state) {
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
                });
            });
        });

        $('input[name="admin"]').each(function () {
            $(this).on('switchChange.bootstrapSwitch', function(event, state) {
                var id    = $(this).attr('data-id');
                var value = $(this).attr("value");
                var groupId = $(this).attr("data-groupId");
                if (value === 0) {
                    $(this).attr("value", 1);
                } else {
                    $(this).attr("value", 0);
                }
                var url = '/api/user/grant/' + groupId + '/' + id + '/' + value;
                var request = $.ajax({
                    type: "GET",
                    url: url
                });
            });
        });
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
            new Chartist.Bar('#chart-matches', {
              labels: ['2016 2017', '2017 2018'],
                  series: JSON.parse(match.attr('data-scores')),
            }, {
              // stackBars: true,
              horizontalBars: true,
              seriesBarDistance: 18,
              height: 300,
              axisX: {
                    onlyInteger: true
                }
            }).on('draw', function(data) {
              if(data.type === 'bar') {
                data.element.attr({
                  style: 'stroke-width: 15px'
                });
              }
            });
        }

        $('.chart-stats').each(function() {
            var sets = $(this).attr('data-sets');
            if (sets == 3) var labels = ['1er', '2nd', '3e'];
            if (sets == 4) var labels = ['1er', '2nd', '3e', '4e'];
            if (sets == 5) var labels = ['1er', '2nd', '3e', '4e', '5e'];
            new Chartist.Line(this, {
                  labels: labels,
                  series: JSON.parse($(this).attr('data-stats')),
            }, {
              fullWidth: true,
              showPoint: true,
              height: 300,
              axisX: {
                onlyInteger: true
              }
            });
        });
    },

    showSwal: function(type, redirectUrl = null){
        if(type == 'warning-message-and-confirmation'){
            swal({
                title: 'Êtes vous sûr?',
                text: "Le point précédent sera supprimé",
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn btn-success btn-fill',
                cancelButtonClass: 'btn btn-danger btn-fill',
                confirmButtonText: 'Oui !',
                cancelButtonText: 'Annuler',
                buttonsStyling: false
            }).then(function() {
                window.location.replace(redirectUrl);
            });
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

    initFormExtendedDatetimepickers: function(){
        $('#event-date').datetimepicker({
            inline: true,
            sideBySide: true,
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
        $("#event-date").on("dp.change", function (e) {
            $('.datetimepicker').attr("value", e.date.format('DD/MM/YYYY HH:mm'));
        });

        $('#holiday-from').datetimepicker({
            inline: true,
            format: 'DD/MM/YYYY',    // use this format if you want the 24hours timepicker
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
            },
        });

        $('#holiday-to').datetimepicker({
            inline: true,
            format: 'DD/MM/YYYY',    // use this format if you want the 24hours timepicker
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
            },
        });
        $("#holiday-from").on("dp.change", function (e) {
            $('#holiday-to').data("DateTimePicker").minDate(e.date);
            $('.holiday-from-input').attr("value", e.date.format('DD/MM/YYYY'));
        });
        $("#holiday-to").on("dp.change", function (e) {
            $('#holiday-from').data("DateTimePicker").maxDate(e.date);
            $('.holiday-to-input').attr("value", e.date.format('DD/MM/YYYY'));
        });

        $('.timepicker').datetimepicker({
            format: 'H:mm',    // use this format if you want the 24hours timepicker
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
            height = Math.max(height - 235, 570);
        }

        var calendar = $('#fullCalendar');
        var groupId = calendar.attr('data-groupId');

        $('#fullCalendar').fullCalendar({
            lazyFetching: true,
            events: {
                url: '/api/event/get/all',
                // cache: true,
                type: 'GET',
                data: function() { // a function that returns an object
                    return {
                        dynamic_value: Math.random(),
                        groupId: groupId
                    };
                }
            },
            header: {
                right: 'title',
                left: 'prev,next,today', //listMonth,month,
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
            eventLimit: false, // allow "more" link when too many events
            timeFormat: 'H:mm',
            eventClick:  function(event, jsEvent, view) {
                var calendarModal = $('#fullCalModal');
                var myEvent = event;
                jsEvent.preventDefault();
                if (typeof event.url !== "undefined") {
                    $('#modal-title').html(myEvent.title);
                    $('#modal-date').html(myEvent.date);
                    $('.event-url').attr('href', myEvent.url);
                    $('#modal-count').html(myEvent.count);
                    $('#modal-place').html(myEvent.place);
                    $('#modal-city').html(myEvent.city);
                    $('#modal-zipcode').html(myEvent.zipcode);
                    $('#modal-address').html(myEvent.address);
                    $('#modal-month').html(myEvent.month);
                    $('#modal-day').html(myEvent.day);
                    $('#modal-date').html(myEvent.date);
                    $('#event-place-url').attr('href', 'https://maps.google.com/?q=' + myEvent.address + '+' + myEvent.city + '+' + myEvent.zipcode);
                    calendarModal.modal();

                    var url = '/api/guest/response/' + myEvent.id;
                    $('#event-url-ok').off('click').on('click', function(e, state) {
                        url = url + '/1';
                        var request = $.ajax({
                            type: "GET",
                            url: url
                        }).done(function(resp) {
                            if (myEvent.className != 'event-green') {
                                myEvent.count = myEvent.count + 1;
                            }
                            myEvent.className = ['event-green'];
                            $('#fullCalendar').fullCalendar('updateEvent', myEvent);
                            calendarModal.modal('hide');
                        });
                        swal({
                          title: 'Enregistré',
                          type: 'success',
                          showConfirmButton: false
                        });
                    });

                    $('#event-url-no').off('click').on('click', function(e, state) {
                        url = url + '/2';
                        var request = $.ajax({
                            type: "GET",
                            url: url
                        }).done(function(resp) {
                            if (myEvent.className == 'event-green') {
                                myEvent.count = myEvent.count - 1;
                            }
                            myEvent.className = ['event-red'];
                            $('#fullCalendar').fullCalendar('updateEvent', myEvent);
                            calendarModal.modal('hide');
                        });
                        swal({
                          title: 'Enregistré',
                          type: 'success',
                          showConfirmButton: false
                        });
                    });

                    $('#event-url-incertain').off('click').on('click', function(e, state) {
                        url = url + '/3';
                        var request = $.ajax({
                            type: "GET",
                            url: url
                        }).done(function(resp) {
                            if (myEvent.className == 'event-green') {
                                myEvent.count = myEvent.count - 1;
                            }
                            myEvent.className = ['event-orange'];
                            $('#fullCalendar').fullCalendar('updateEvent', myEvent);
                            calendarModal.modal('hide');
                        });
                        swal({
                          title: 'Enregistré',
                          type: 'success',
                          showConfirmButton: false
                        });
                    });
                }
            }

        });
    },

    showNotification: function() {
        if ($('#notification').length > 0) {
            var message = $('#notification').attr('data-message');
            var type = $('#notification').attr('data-type');
            var icon = (type == 'success') ? 'ti-check' : 'ti-na';
            $.notify({
                icon: icon,
                message: message

            },{
                type: type,
                timer: 10,
                placement: {
                    from: 'top',
                    align: 'right'
                }
            });
        }
    },

    initClipboard: function() {
        var clipboard = new Clipboard('.copy-to-clipboard');

        clipboard.on('success', function(e) {
            $(e.trigger).removeClass('btn-info');
            $(e.trigger).addClass('btn-success');
            $(e.trigger).html('copié !');
            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            $(e.trigger).removeClass('btn-info');
            $(e.trigger).addClass('btn-error');
            $(e.trigger).html('erreur');
        });
    },

    initTooltips: function() {
        $('[data-toggle="tooltip"]').tooltip();
    },

    initSubmitButton: function () {
        $('input[type="submit"]').each(function () {
            $(this).on('click', function() {
                $(this).attr('value', '<i class="fa fa-spinner fa-spin"></i>');
            });
        });
    },

    initHighcharts: function() {

        $(document).ready(function () {
            $('.stats-chart').each(function () {
                var dataUs = $(this).attr('data-stats-us');
                var dataThem = $(this).attr('data-stats-them');

                Highcharts.chart(this, {
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: ''
                    },
                    tooltip: { enabled: false },
                    xAxis: {
                        categories: [
                            ' <i class="fa fa-hand-paper-o text-success"></i></span> Points <br> Service',
                            ' <i class="fa fa-crosshairs text-success"></i> Points <br> Attaque',
                            ' <i class="fa fa-ban text-success"></i> Points <br> Block',
                            ' <i class="fa fa-hand-paper-o text-danger"></i> Fautes <br> Service',
                            ' <i class="fa fa-crosshairs text-danger"></i> Fautes <br> Attaque',
                            ' <i class="fa fa-shield text-danger"></i> Fautes <br> Défensives',
                            'Total <br> Fautes',
                        ],
                        title: {
                            text: null,
                        },
                        labels: {
                            useHTML: true,
                            align: 'right'
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '',
                            align: 'left'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: 'Nous',
                        data: JSON.parse(dataUs),
                    }, {
                        name: 'Eux',
                        data: JSON.parse(dataThem),
                    }],
                    dataLabels: {
                        useHTML: true
                    }
                });
            });

            $('.overall-stats-chart').each(function () {
                var dataUs = $(this).attr('data-stats-us');
                var dataThem = $(this).attr('data-stats-them');

                Highcharts.chart(this, {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    tooltip: { enabled: false },
                    xAxis: {
                        categories: [
                            ' <i class="fa fa-hand-paper-o text-success"></i></span> Points <br> Service',
                            ' <i class="fa fa-crosshairs text-success"></i> Points <br> Attaque',
                            ' <i class="fa fa-ban text-success"></i> Points <br> Block',
                            ' <i class="fa fa-hand-paper-o text-danger"></i> Fautes <br> Service',
                            ' <i class="fa fa-crosshairs text-danger"></i> Fautes <br> Attaque',
                            ' <i class="fa fa-shield text-danger"></i> Fautes <br> Défensives',
                            'Total <br> Fautes',
                        ],
                        title: {
                            text: null,
                        },
                        labels: {
                            useHTML: true,
                            align: 'right'
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '',
                            align: 'left'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: 'Nous',
                        data: JSON.parse(dataUs),
                        dataLabels: {
                            enabled: true,
                            // rotation: -90,
                            color: '#FFFFFF',
                            align: 'center',
                            y: 25, // 10 pixels down from the top
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    }, {
                        name: 'Eux',
                        data: JSON.parse(dataThem),
                        dataLabels: {
                            enabled: true,
                            // rotation: -90,
                            color: '#FFFFFF',
                            align: 'center',
                            y: 25, // 10 pixels down from the top
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    }],
                    dataLabels: {
                        useHTML: true
                    }
                });
            });
        });
    }
}