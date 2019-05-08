$(function () {
    $('.jtoggler').jtoggler();

    $('.jtoggler-btn-wrapper').removeClass('is-active');
    $('input.jtoggler').each(function () {
        if ($(this).data('current-value') === 2) {
            $(this).next().addClass('is-fully-active');
        }
        $(this).next().find('.jtoggler-btn-wrapper:eq(' + $(this).data('current-value') + ')').addClass('is-active');
    });
});


$(document).on('jt:toggled:multi', function (event, target) {
    let url = $(".switch-wrapper").data("save-position-url");
    let project_id = $(target).parent().parent().parent().children().first().data('project-id');
    let status = $(target).parent().index();

    $('.progress' + project_id).removeClass('color0').removeClass('color1').removeClass('color2').addClass('color' + status);

    $.ajax({
        cache: false,
        url: url,
        contentType: "application/json",
        type: "POST",
        processData: false,
        data: JSON.stringify({
            "project_id": project_id,
            "status": status
        })
    });
});


$.each($(".user_chart"), function () {
    c3.generate({
        bindto: '#user_chart'+$(this).data('chartid'),
        data: {
            columns: [
                ['active tasks'].concat($(this).data('params').tasks),
                ['closed tasks'].concat($(this).data('params').closed_tasks)
            ],
            type: 'bar'
        },
        axis: {
            x: {
                type: 'category',
                categories: $(this).data('params').users
            },
            y: {
                min: 0,
                tick: {
                    format: d3.format('d')
                },
                padding: {top: 0, bottom: 0}
            }
        },
        bar: {
            width: {
                ratio: 0.7
            }
        }
    });
});

$.each($(".columns_chart"), function () {

    c3.generate({
        bindto: '#columns_chart'+$(this).data('chartid'),
        data: {
            columns: [
                ['tasks'].concat($(this).data('params').tasks),
            ],
            type: 'bar'
        },
        axis: {
            x: {
                type: 'category',
                categories: $(this).data('params').column_title
            },
            y: {
                min: 0,
                tick: {
                    format: d3.format('d')
                },
                padding: {top: 0, bottom: 0}
            }
        },
        bar: {
            width: {
                ratio: 0.5
            }
        }
    });

});


$("select#form-status").change(function () {
    let issue_id = $(this).data('id');
    let status = $(this).val();
    let url = $('.issues-table').data("save-status-url");

    $.ajax({
        cache: false,
        url: url,
        contentType: "application/json",
        type: "POST",
        processData: false,
        data: JSON.stringify({
            "issue_id": issue_id,
            "status": status
        })
    });
});