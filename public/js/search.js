$( document ).ready(function() {
    var url = '/api/search/data';
    var request = $.ajax({
        type: "GET",
        url: url,
    }).done(function(resp) {
        var data = jQuery.parseJSON(resp);
        var options = {
            data: data,

            categories: [{
                listLocation: "groups",
                maxNumberOfElements: 4,
                header: "Groupes"
            }, {
                listLocation: "events",
                maxNumberOfElements: 4,
                header: "Évènements"
            }],

            getValue: function(element) {
                return element.label;
            },

            template: {
                type: "description",
                fields: {
                    description: "description",
                }
            },

            list: {
                maxNumberOfElements: 8,
                match: {
                    enabled: true
                },
                sort: {
                    enabled: true
                },
                onChooseEvent: function() {
                    var url = $("#main-search").getSelectedItemData().link;
                    document.location.href = url;
                },
                onClickEvent: function() {
                    var url = $("#main-search").getSelectedItemData().link;
                    document.location.href = url;
                },
                showAnimation: {
                    type: "fade", //normal|slide|fade
                    time: 200,
                    callback: function() {}
                },

                hideAnimation: {
                    type: "fade", //normal|slide|fade
                    time: 200,
                    callback: function() {}
                }
            },

            theme: "square"

        };
        $("#main-search").easyAutocomplete(options);
    });
});