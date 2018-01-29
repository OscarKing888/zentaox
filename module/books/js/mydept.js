$(function()
{
    //alert("init checked summary");
    setOuterBox();
    if(browseType == 'bysearch') ajaxGetSearchForm();
    setTimeout(function(){fixedTfootAction('#gametaskinternalMydept')}, 100);
    setTimeout(function(){fixedTheadOfList('#taskList')}, 100);

    $('.dropdown-menu .with-search .menu-search').click(function(e)
    {
        e.stopPropagation();
        return false;
    }).on('keyup change paste', 'input', function()
    {
        var val = $(this).val().toLowerCase();
        var $options = $(this).closest('.dropdown-menu.with-search').find('.option');
        if(val == '') return $options.removeClass('hide');
        $options.each(function()
        {
            var $option = $(this);
            $option.toggleClass('hide', $option.text().toString().toLowerCase().indexOf(val) < 0 && $option.data('key').toString().toLowerCase().indexOf(val) < 0);
        });
    });

    if($('#taskList thead th.w-name').width() < 150) $('#taskList thead th.w-name').width(150);
    
    $(document).on('click', "#taskList tbody tr", function(){showCheckedSummary();});
    $(document).on('change', "#taskList :checkbox", function(){showCheckedSummary();});
    $(document).on('click', "#datatable-taskList table tr", function(){showCheckedSummary();});
});

function setQueryBar(queryID, title)
{
    var $tagTab = $('#featurebar #calendarTab').size() > 0 ? $('#featurebar #calendarTab') : $('#featurebar #kanbanTab');
    $tagTab.before("<li id='QUERY" + queryID + "Tab' class='active'><a href='" + createLink('project', 'task', "projectID=" + projectID + "&browseType=bysearch&param=" + queryID) + "'>" + title + "</a></li>");
}

function showCheckedSummary()
{
    alert("show checked summary");

    var $summary = $('tfoot .table-actions .text:last');
    if(!$summary.hasClass('readed'))
    {
        taskSummary = $summary.html();
        $summary.addClass('readed');
    }

    var checkedTotal    = 0;

    $('[name^="taskIDList"]').each(function()
    {
        if($(this).prop('checked'))
        {
            checkedTotal += 1;
            var taskID = $(this).val();
            $tr = $("#taskList tbody tr[data-id='" + taskID + "']");
            //if($tr.data('status') == 'wait')  checkedWait += 1;
            //if($tr.data('status') == 'doing') checkedDoing += 1;
            //checkedEstimate += Number($tr.data('estimate'));
            //checkedConsumed += Number($tr.data('consumed'));
            //checkedLeft     += Number($tr.data('left'));
        }
    });
    if(checkedTotal > 0)
    {
        summary = checkedSummary
            .replace('%total%', checkedTotal)
            //.replace('%wait%', checkedWait)
          ;
        $('tfoot .table-actions .text:last').html(summary);
    }
    else
    {
        $('tfoot .table-actions .text:last').html(taskSummary);
    }
}

$('#module' + moduleID).addClass('active');
$('#product' + productID).addClass('active');
