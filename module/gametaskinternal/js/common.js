$(function()
{
    $(document).on('click', '.task-toggle', function(e)
    {
        var $toggleIcon = $(this).find('i');
        var id  = $(this).data('id');

        if($toggleIcon.hasClass('icon-double-angle-down'))
        {
            $('tr.parent-'+id).show();
            $toggleIcon.removeClass('icon-double-angle-down').addClass('icon-double-angle-up');
        }
        else if($toggleIcon.hasClass('icon-double-angle-up'))
        {
            $('tr.parent-'+id).hide();
            $toggleIcon.removeClass('icon-double-angle-up').addClass('icon-double-angle-down');
        }

        e.stopPropagation();
        e.preventDefault();
    });
})
