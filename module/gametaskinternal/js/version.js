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