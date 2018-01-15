$(
    function () {
        //alert("reprot project view init create");
    }
)

function on_createUserAbsent(userid, day) {
    //alert("setUserAbsent");

    url = createLink('blog', 'createUserAbsent');
    alert("on_createUserAbsent userid:" + userid + " day:" + day + " url:" + url);

    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {"userid": userid, "day":day}
            /*,
            success:  function()
            {
                alert("createUserAbsent:success");
            },
            error: function(error){
                alert("createUserAbsent error:" + JSON.stringify(error));
            },
            complete:function()
            {
                alert("createUserAbsent:complete");
            }
            //*/
        });

    document.location.reload();
}

function on_setUserAbsent(userid, day) {
    //alert("setUserAbsent");

    url = createLink('blog', 'setUserAbsent');
    alert("on_setUserAbsent userid:" + userid + " day:" + day + " url:" + url);

    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {"userid": userid, "day":day}
        });

    document.location.reload();
}

function on_removeUserAbsent(userid, day) {
    //alert("removeUserAbsent");
    //alert("removeUserAbsent userid:" + userid + " day:" + day);
    url = createLink('blog', 'removeUserAbsent');
    alert("on_removeUserAbsent userid:" + userid + " day:" + day + " url:" + url);

    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {"userid": userid, "day":day}
        });

    document.location.reload();
}