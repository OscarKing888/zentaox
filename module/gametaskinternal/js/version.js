function on_activeVersion(id) {

    //alert("on_activeVersion:" + id);

    url = createLink('gametaskinternal', 'activeVersion');

    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {"id": id}
        });

    document.location.reload();
}

function on_closeVersion(id) {

    url = createLink('gametaskinternal', 'closeVersion');
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

function on_updateVersionDeadline(id)
{
    url = createLink('gametaskinternal', 'updateVersionDeadline');
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