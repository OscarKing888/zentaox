function changeDate(date)
{
    date = date.replace(/\-/g, '');
    link = createLink('meeting', 'index', 'type=' + date);
    location.href=link;
}