$(function()
{
    ajaxGetSearchForm();

    $('#storyList').on('sort.sortable', function(e, data)
    {
        var list = '';
        for(i = 0; i < data.list.length; i++) list += $(data.list[i]).attr('data-id') + ',';
        $.post(createLink('project', 'storySort', 'projectID=' + projectID), {'storys' : list, 'orderBy' : orderBy}, function()
        {
            var $target = $(data.element[0]);
            $target.hide();
            $target.fadeIn(1000);
            order = 'order_asc'
            history.pushState({}, 0, createLink('project', 'story', "projectID=" + projectID + '&orderBy=' + order));
        });
    });

    fixedTfootAction('#projectStoryForm');
    fixedTheadOfList('#storyList');

    $('#module' + moduleID).addClass('active');
    $('#product' + productID).addClass('active');
    $('#branch' + branchID).addClass('active');
    $(document).on('click', "#storyList tbody tr", function(){showCheckedSummary();});
    $(document).on('change', "#storyList :checkbox", function(){showCheckedSummary();});
});

function showCheckedSummary()
{
    var $summary = $('tfoot .table-actions .text:last');
    if(!$summary.hasClass('readed'))
    {
        taskSummary = $summary.html();
        $summary.addClass('readed');
    }

    var checkedTotal    = 0;
    var checkedEstimate = 0;
    var checkedCase     = 0;
    $('[name^="storyIDList"]').each(function()
    {
        if($(this).prop('checked'))
        {
            checkedTotal += 1;
            var taskID = $(this).val();
            $tr = $("#storyList tbody tr[data-id='" + taskID + "']");
            checkedEstimate += Number($tr.data('estimate'));
            if(Number($tr.data('cases')) > 0) checkedCase += 1;
        }
    });
    if(checkedTotal > 0)
    {
        rate    = Math.round(checkedCase / checkedTotal * 10000) / 100 + '' + '%';
        summary = checkedSummary.replace('%total%', checkedTotal)
          .replace('%estimate%', checkedEstimate)
          .replace('%rate%', rate)
        $('tfoot .table-actions .text:last').html(summary);
    }
    else
    {
        $('tfoot .table-actions .text:last').html(taskSummary);
    }
}



function on_createRootTask(projectID, storyID, storyTitle, productID) {

    //var pipelineID = document.getElementById('batchCreateRootTask[' + storyID + ']').val();
    var pipelineID = $("#batchCreateRootTask_" + storyID).val();
    var pipelineText = $("#batchCreateRootTask_" + storyID + " option:selected").text();
    if(pipelineID == -1)
    {
        //alert("不创建None");
        return;
    }

     if(!confirm("确认要对需求：[" + storyID + "] [" + storyTitle + "]  根据Pipeline自动创建任务?\n批量任务类型:" + pipelineText))
     {
         $("#batchCreateRootTask_" + storyID + ' option:last').attr('selected','selected');
         //$("#batchCreateRootTask_" + storyID).selectpicker('refresh');
         //$("#batchCreateRootTask_" + storyID).remove();
         //$("#batchCreateRootTask_" + storyID + 'option:first').text("ssss");

         return;
     }

    url = createLink('pipeline', 'batchCreateRootTask');
    alert("on_createRootTask productID:" + productID + " projectID:" + projectID + " pipelineID:" + pipelineID + " storyID:" + storyID + " url:" + url);

    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {"projectID": projectID, "pipelineID":pipelineID, "storyID":storyID, "productID":productID}
            //*
            ,success:  function()
            {
                alert("批量创建主任务成功！！");
            },
            error: function(error){
                alert("批量创建主任务出错：" + JSON.stringify(error));
            },
            complete:function()
            {
                //alert("批量创建主任务 完成！");
            }
            //*/
        });

    document.location.reload();
}