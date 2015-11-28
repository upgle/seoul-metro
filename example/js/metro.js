

$( document ).ready(function() {

    var states = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.name);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        identify: function (obj) {
            return obj.id;
        },
        // `states` is an array of state names defined in "The Basics"
        local: statesData
    });

    $('.minTime').click(function () {
        $("input[name=target]").val("minTime");
        $("#form-search").submit();
    });

    $('.minTransfer').click(function () {
        $("input[name=target]").val("minTransfer");
        $("#form-search").submit();
    });

    $('.start_typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'states',
            displayKey: "name",
            source: states,
            templates: {
                suggestion: function (data) {
                    return '<div><strong>' + data.name + '</strong> - ' + data.line + '호선</div>';
                }
            }
        }).on('typeahead:selected', function (event, data) {
            $('.start').val(data.id);
        });

    $('.goal_typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'states',
            displayKey: "name",
            source: states,
            templates: {
                suggestion: function (data) {
                    return '<div><strong>' + data.name + '</strong> - ' + data.line + '호선</div>';
                }
            }
        }).on('typeahead:selected', function (event, data) {
            $('.goal').val(data.id);
        });
});