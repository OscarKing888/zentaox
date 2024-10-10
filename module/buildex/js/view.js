function showLink(buildID, type, param)
{
    var method = type == 'story' ? 'linkStory' : 'linkBug';
    $.get(createLink('buildex', method, 'buildID=' + buildID + (typeof(param) == 'undefined' ? '' : param)), function(data)
    {
        //console.debug("dat:", data);
        //alert(data);

        var obj = type == 'story' ? '.tab-pane#stories .linkBox' : '.tab-pane#bugs .linkBox';
        $(obj).html(data);
        //$(obj).html("<div>text</div>");

        $('#' + type + 'List').hide();

        var formID = type == 'story' ? '#unlinkedStoriesForm' : '#unlinkedBugsForm';
        setTimeout(function(){fixedTfootAction(formID)}, 100);
        checkTable($(formID).find('table'));
    });
}
$(function()
{

})
