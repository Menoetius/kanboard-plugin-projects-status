KB.on('dom.ready', function() {

    function saveIssuePosition(issueId, position) {
        var url = $(".issues-table").data("save-position-url");

        $.ajax({
            cache: false,
            url: url,
            contentType: "application/json",
            type: "POST",
            processData: false,
            data: JSON.stringify({
                "issue_id": issueId,
                "position": position
            })
        });
    }

    $(".draggable-row-handle").mouseenter(function() {
        $(this).parent().parent().addClass("draggable-item-hover");
    }).mouseleave(function() {
        $(this).parent().parent().removeClass("draggable-item-hover");
    });

    $(".issues-table tbody").sortable({
        forcePlaceholderSize: true,
        handle: "td:first i",
        helper: function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });

            return ui;
        },
        stop: function(event, ui) {
            var issue = ui.item;
            issue.removeClass("draggable-item-selected");
            saveIssuePosition(issue.data("issue-id"), issue.index() + 1);
        },
        start: function(event, ui) {
            ui.item.addClass("draggable-item-selected");
        }
    }).disableSelection();
});
