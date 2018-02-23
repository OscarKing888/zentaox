
function on_like(userid, imageid) {

    url = createLink('artstation', 'like');
    //alert("on_like userid:" + userid + " imageid:" + imageid + " url:" + url);

    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {"userid": userid, "imageid":imageid}
        });

    document.location.reload();
}

function downloadFileToDisk(fileID)
{
    if(!fileID) return;
    var sessionString = '<?php echo $sessionString;?>';
    var url = createLink('file', 'downloadToDisk', 'fileID=' + fileID);// + sessionString;
    window.open(url, '_blank');
    return false;
}



function setStoriesByProduct(productID)
{
    link = createLink('story', 'ajaxGetProductStories', 'productID=' + productID);
    $.get(link, function(stories)
    {
        var storyID = $('#story').val();
        if(!stories) stories = '<select name="story" class="form-control"></select>';
        $('#story').replaceWith(stories);
        $('#story').val(storyID);
        setPreview();
        $('#story_chosen').remove();
        $("#story").chosen(defaultChosenOptions);
    });
}

function reloadStoriesByProduct()
{
    setStoriesByProduct($('#product').val());
}