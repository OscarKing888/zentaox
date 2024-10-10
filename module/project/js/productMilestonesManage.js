function on_activeMilestone(id) {

    //alert("on_activeVersion:" + id);

    url = createLink('project', 'activeMilestone');

    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {"id": id}
        });

    document.location.reload();
}

function on_closeMilestone(id) {

    url = createLink('project', 'closeMilestone');
    //alert("on_closeVersion:" + id + " url:" + url);

    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {"id": id}
        });

    document.location.reload();
}

function on_updateMilestoneDeadline(id)
{
    url = createLink('project', 'updateMilestoneDeadline');
    //alert("on_closeVersion:" + id + " url:" + url);
    //$(deadline).hide();

    //var x = document.getElementById("deadline");
    //x.setAttribute("value", "2014-06-01");
    //var v = x.valueAsDate;

    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {"id": id, "deadline": $("#deadline" + id).val()}
        });
    //$("#deadline").val(new Date().toLocaleDateString());

    //document.body.appendChild(x);

    alert("更新成功！" + $("#deadline" + id).val());

    document.location.reload();
}