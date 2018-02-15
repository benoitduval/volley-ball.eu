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
        });6

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

    showSwal: function(type, redirectUrl){
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

    initStatsBtns: function() {
        $('.stats-btn').on('click', function(e, state) {
            var value = $(this).attr('data-stats-value');
            var request = $.ajax({
                type: "GET",
                url: '/api/stats/save'
            }).done(function(resp) {
                
            });
        });
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
        $('.btn-wait').on('click', function() {
            var $this = $(this);
            $this.button('loading');
            setTimeout(function() {
                $this.button('reset');
            }, 500);
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
                            ' <i class="far fa-hand-paper text-success"></i></span> P<span class="hidden-xs">oints</span> Service',
                            ' <i class="fa fa-crosshairs text-success"></i> P<span class="hidden-xs">oints</span> Attaque',
                            ' <i class="fas fa-ban text-success"></i> P<span class="hidden-xs">oints</span> Block',
                            ' <i class="far fa-hand-paper text-danger"></i> F<span class="hidden-xs">autes</span> Service',
                            ' <i class="fa fa-crosshairs text-danger"></i> F<span class="hidden-xs">autes</span> Attaque',
                            ' <i class="fas fa-shield-alt text-danger"></i> F<span class="hidden-xs">autes</span> Défensives',
                            'Total F<span class="hidden-xs">autes</span>',
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
                        color: '#66E2A4'
                    }, {
                        name: 'Eux',
                        data: JSON.parse(dataThem),
                        color: '#FD8F63'
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
                            ' <i class="far fa-hand-paper text-success"></i></span> P<span class="hidden-xs">oints</span> Service',
                            ' <i class="fa fa-crosshairs text-success"></i> P<span class="hidden-xs">oints</span> Attaque',
                            ' <i class="fas fa-ban text-success"></i> P<span class="hidden-xs">oints</span> Block',
                            ' <i class="far fa-hand-paper text-danger"></i> F<span class="hidden-xs">autes</span> Service',
                            ' <i class="fa fa-crosshairs text-danger"></i> F<span class="hidden-xs">autes</span> Attaque',
                            ' <i class="fas fa-shield-alt text-danger"></i> F<span class="hidden-xs">autes</span> Défensives',
                            'Total F<span class="hidden-xs">autes</span>',
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
                        color: '#66E2A4',
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
                        color: '#FD8F63',
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

        $('.post-attack-chart').each(function () {
            Highcharts.chart(this, {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Répartition des Points Par Poste'
                },
                subtitle: {
                    text: 'Cliquez sur une colone pour voir les détails'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Nombre de points'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
                },

                series: [{
                    name: 'Points',
                    colorByPoint: true,
                    data: [{
                        name: 'Recep 4',
                        y: 33,
                        drilldown: 'Recep 4'
                    }, {
                        name: 'Pointe',
                        y: 24,
                        drilldown: 'Pointe'
                    }, {
                        name: 'Centre',
                        y: 10,
                        drilldown: 'Centre'
                    }, {
                        name: '3 mètres',
                        y: 7,
                        drilldown: '3 mètres'
                    }, {
                        name: 'Passeur',
                        y: 9,
                        drilldown: 'Passeur'
                    }]
                }],
                credits: {
                    enabled: false
                },
                drilldown: {
                    series: [{
                        name: 'Recep 4',
                        id: 'Recep 4',
                        data: [
                            [
                                'Ligne',
                                13
                            ],
                            [
                                'Grande Diag.',
                                17
                            ],
                            [
                                'Petite Diag.',
                                8
                            ],
                            [
                                'Block Out',
                                5
                            ],
                            [
                                'Bidouille',
                                1
                            ]
                        ]
                    }, {
                        name: 'Pointe',
                        id: 'Pointe',
                        data: [
                            [
                                'Ligne',
                                13
                            ],
                            [
                                'Grande Diag.',
                                17
                            ],
                            [
                                'Petite Diag.',
                                8
                            ],
                            [
                                'Block Out',
                                5
                            ],
                            [
                                'Bidouille',
                                1
                            ]
                        ]
                    }, {
                        name: 'Centre',
                        id: 'Centre',
                        data: [
                            [
                                'Fixe',
                                2.76
                            ],
                            [
                                'Arrière',
                                2.32
                            ],
                            [
                                'Décalée',
                                2.31
                            ]
                        ]
                    }, {
                        name: '3 mètres',
                        id: '3 mètres',
                        data: [
                            [
                                'Ligne',
                                13
                            ],
                            [
                                'Grande Diag.',
                                17
                            ],
                            [
                                'Petite Diag.',
                                8
                            ],
                            [
                                'Block Out',
                                5
                            ],
                            [
                                'Bidouille',
                                1
                            ]
                        ]
                    }, {
                        name: 'Passeur',
                        id: 'Passeur',
                        data: [
                            [
                                'Attaque',
                                3
                            ],
                            [
                                'Bidouille',
                                2
                            ]
                        ]
                    }]
                }
            });
        });

        $('.zone-attack-chart').each(function () {
            Highcharts.chart(this, {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Répartition des Points Par Zone'
                },
                subtitle: {
                    text: 'Cliquez sur une colone pour voir les détails'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Nombre de points'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
                },

                series: [{
                    name: 'Points',
                    colorByPoint: true,
                    data: [{
                        name: 'Ligne',
                        y: 9,
                        drilldown: 'Ligne'
                    }, {
                        name: 'Grande Diag.',
                        y: 3,
                        drilldown: 'Grande Diag.'
                    }, {
                        name: 'Petite Diag.',
                        y: 8,
                        drilldown: 'Petite Diag.'
                    }, {
                        name: 'Bidouille',
                        y: 7,
                        drilldown: 'Bidouille'
                    }, {
                        name: 'Block Out',
                        y: 1,
                        drilldown: 'Block Out'
                    }]
                }],
                credits: {
                    enabled: false
                },
                drilldown: {
                    series: [{
                        name: 'Ligne',
                        id: 'Ligne',
                        data: [
                            [
                                'Recep 4',
                                13
                            ],
                            [
                                'Pointe',
                                17
                            ],
                            [
                                '3 mètres',
                                8
                            ]
                        ]
                    }, {
                        name: 'Grande Diag.',
                        id: 'Grande Diag.',
                        data: [
                            [
                                'Recep 4',
                                13
                            ],
                            [
                                'Pointe',
                                17
                            ],
                            [
                                '3 mètres',
                                8
                            ]
                        ]
                    }, {
                        name: 'Petite Diag.',
                        id: 'Petite Diag.',
                        data: [
                            [
                                'Recep 4',
                                13
                            ],
                            [
                                'Pointe',
                                17
                            ],
                            [
                                '3 mètres',
                                8
                            ]
                        ]
                    }, {
                        name: 'Block Out',
                        id: 'Block Out',
                        data: [
                            [
                                'Recep 4',
                                24
                            ],
                            [
                                'Pointe',
                                17
                            ],
                            [
                                '3 mètres',
                                8
                            ]
                        ]
                    }, {
                        name: 'Bidouille',
                        id: 'Bidouille',
                        data: [
                            [
                                'Recep 4',
                                24
                            ],
                            [
                                'Pointe',
                                17
                            ],
                            [
                                '3 mètres',
                                8
                            ],
                            [
                                'Passeur',
                                0
                            ]
                        ]
                    }]
                }
            });
        });

        $('.defensive-chart').each(function () {
            var blockUs     = $(this).attr('data-stats-block-us');
            var blockThem   = $(this).attr('data-stats-block-them');
            var defenceUs   = $(this).attr('data-stats-defence-us');
            var defenceThem = $(this).attr('data-stats-defence-them');

            Highcharts.chart(this, {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },
                title: {
                    text: 'Jeu Défensif'
                },
                subtitle: {
                    text: 'Ne concluant pas le point'
                },
                tooltip: {
                    pointFormat: '<b>{point.y}</b>'
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}',
                            distance: -50
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    type: 'pie',
                    // innerSize: '50%',
                    data: [{
                        name: 'Blocks (nous)',
                        y: parseInt(blockUs),
                        color: '#54B172'
                    },
                    {
                        name: 'Défenses (nous)',
                        y: parseInt(defenceUs),
                        color: '#7BB320'
                    },
                    {
                        name: 'Blocks (eux)',
                        y: parseInt(blockThem),
                        color: '#FD8F63'
                    },
                    {
                        name: 'Défenses (eux)',
                        y: parseInt(defenceThem),
                        color: '#FDBB7A'
                    }]
                }]
            });
        });

        $('.efficiency-chart').each(function () {
            var fault   = $(this).attr('data-stats-fault');
            var point   = $(this).attr('data-stats-point');
            var defence = $(this).attr('data-stats-defence');
            var block   = $(this).attr('data-stats-block');
            Highcharts.chart(this, {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Efficacité à L\'attaque'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br/> Nombre: {point.y}'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.percentage:.1f}%',
                            distance: -50
                        },
                        showInLegend: true
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: 'Efficacité',
                    colorByPoint: true,
                    data: [{
                            name:'Fautes Attaque (nous)',
                            y: parseInt(fault)
                        },
                        {
                            name:'Défense (eux)',
                            y: parseInt(defence)
                        },{
                            name:'Points Attaque (nous)',
                            y: parseInt(point),
                            sliced: true
                        },
                        {
                            name:'Block défensifs (eux)',
                            y: parseInt(block)
                        }
                    ]
                }]
            });
        });

        $('.fault-repartition-chart').each(function () {
            var post4      = $(this).attr('data-stats-fault-4');
            var post2      = $(this).attr('data-stats-fault-2');
            var post3m     = $(this).attr('data-stats-fault-3m');
            var postSetter = $(this).attr('data-stats-fault-setter');
            var postCenter = $(this).attr('data-stats-fault-center');
            Highcharts.chart(this, {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Répartition Des Fautes'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b><br/>Nombre: {point.y}'
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.percentage:.1f}%',
                            distance: -50
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Fault Repartition',
                    colorByPoint: true,
                    data: [{
                        name: 'Recep 4',
                        y: parseInt(post4)
                    }, {
                        name: 'Pointe',
                        y: parseInt(post2)
                    }, {
                        name: 'Centre',
                        y: parseInt(postCenter),
                    }, {
                        name: '3 mètres',
                        y: parseInt(post3m)
                    }, {
                        name: 'Passe',
                        y: parseInt(postSetter)
                    }]
                }]
            });
        });
    },

    initWizard: function() {
        $(document).ready(function() {

            $('#point-them').on('click', function() {
                $('#attack-us').addClass('hidden');
                $('#attack-them').removeClass('hidden');
                $('#attack-fault-them').addClass('hidden');
                $('#attack-fault-us').removeClass('hidden');
            });

            $('#point-us').on('click', function() {
                $('#attack-us').removeClass('hidden');
                $('#attack-them').addClass('hidden');
                $('#attack-fault-us').addClass('hidden');
                $('#attack-fault-them').removeClass('hidden');
            });

            $('#attack-fault-us').on('click', function() {
                $('#attack-point-detail').addClass('hidden');
                $('#attack-fault-detail').removeClass('hidden');
            });

            $('#attack-us').on('click', function() {
                $('#attack-point-detail').removeClass('hidden');
                $('#attack-fault-detail').addClass('hidden');
            });

            var $validator = $("#wizardForm").validate();

            // you can also use the nav-pills-[blue | azure | green | orange | red] for a different color of wizard
            $('#wizardCard').bootstrapWizard({
                tabClass: 'nav nav-pills',
                nextSelector: '.btn-next',
                previousSelector: '.btn-back',
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
    },

    initCounterButton: function() {
        $('.btn-counter').each(function () {
            $(this).on('click', function() {
                var input = $(this).find('input');
                var value = input.val();
                value = parseInt(value) + 1;
                input.val(value);
                var counter = $(this).find('.counter');
                counter.html(value);
            });
        });
    }
}