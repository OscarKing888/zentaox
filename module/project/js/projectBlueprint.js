
const C_TaskColor_Pending = "#00AAFF";
const C_TaskColor_Completed = "#00AA00";
const C_TaskColor_Warning = "#FFAA00";
const C_TaskColor_Delay = "#AA0000";

const C_TaskNameColor = "#000000";
const C_TaskNameFont = "13px 新宋体";
const C_FontHeight = 10;

const C_Taskbar_Height = 10;
const C_Taskbar_VSpace = 10;
const C_Taskbar_DayUnit = 10;


var origX = 200;
var origY = 50;

Date.prototype.addDays = function(days) {
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
}

function test()
{
    alert("test");
}

function days_between(date1, date2) {

    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24;

    // Convert both dates to milliseconds
    var date1_ms = new Date(date1).getTime();
    var date2_ms = new Date(date2).getTime();

    // Calculate the difference in milliseconds
    var difference_ms = (date1_ms - date2_ms);

    // Convert back to days and return
    return Math.round(difference_ms/ONE_DAY);
}



function convertStringToDate(dateString)
{
    //dateString = dateString.split('-');
    //dateString = dateString[1] + '/' + dateString[2] + '/' + dateString[0];
    var outDate = Date.parse(dateString);
    //alert("convertStringToDate: " + dateString + " o:" + outDate);
    return outDate;
}


function print_call_stack() {
    var stack = new Error().stack;
    console.log("PRINTING CALL STACK");
    console.log( stack );
}

//*
$(function()
{
    //alert("init()");
    //console.log($("#tasks"));
    //drawProjectBlueprint($("#tasks"));

});
//*/

$(document).ready(function()
{
    //alert("doc.ready");


    var tasks = ajaxGetTasks();
    //alert("init:" + tasks);

});

function drawBg()
{
    var canvas = document.getElementById("projectCanvas");

    //canvas.setWidth(document.width);
    //alert("canvas:" + canvas.id + " docW:" + document.width);
    var ctx = canvas.getContext("2d");
    ctx.fillStyle = "#ffFFff";
    ctx.fillRect(0, 0, 4096, 4096);
}

function drawDeadline()
{
    var canvas = document.getElementById("projectCanvas");
    var ctx = canvas.getContext("2d");
    //ctx.translate(origX, -100000);
    ctx.beginPath();
    ctx.strokeStyle = "#FF6600";
    ctx.moveTo(0, -1000000);
    ctx.lineTo(0, 1000000);
    ctx.stroke();
}

function ajaxGetTasks()
{
    url = createLink('task', 'ajaxGetBlueprintTasks');
    //alert("on_createUserAbsent userid:" + userid + " day:" + day + " url:" + url);

    var tasks = null;

    //*
    $.getJSON(link, function(r)
    {
        //alert("ajaxGetTasks:" + r.toString());
        tasks = r;
        drawBg();
        drawProjectBlueprint(tasks);
        drawDeadline();
    });
    //*/

    /*
    $.ajax(
        {
            type:     'POST',
            url:      url,
            dataType: 'json',
            data: {},

            success:  function(r)
            {
                alert("ajaxGetTasks success:" + (r));
                tasks = r;
            },
            error: function(error){
                alert("ajaxGetTasks error:" + JSON.stringify(error));
            },
            complete:function()
            {
                //alert("ajaxGetTasks:complete");
            }

        });

    //*/

    return tasks;
}

function drawProjectBlueprint(tasks)
{
    //alert("drawProjectBlueprint:" + tasks.length);

    var canvas = document.getElementById("projectCanvas");

    //canvas.setWidth(document.width);
    //alert("canvas:" + canvas.id + " docW:" + document.width);
    var ctx = canvas.getContext("2d");

    ctx.translate(origX, origY);

    /*
    var idx = 0;
    drawTask(ctx, "1 CRASH自动收集分析",  "2018-5-1", 24, 'done', idx++);
    drawTask(ctx, "2 CRASH自动收集分析",  "2018-5-1", 32, 'doing', idx++);
    drawTask(ctx, "3 CRASH自动收集分析",  "2018-5-2", 40, 'wait', idx++);
    drawTask(ctx, "4 CRASH自动收集分析",  "2018-5-6", 48, 'wait', idx++);
    //*/

    if(tasks != null)
    {
        for(var i = 0; i < tasks.length; ++i)
        {
            drawTask(ctx, tasks[i].id, tasks[i].name,  tasks[i].realStarted, tasks[i].estimate, tasks[i].status, i);
            //alert("task:" + tasks[i]);
            console.log("task[" + tasks[i].id + "]: " + tasks[i].name + " start:" + tasks[i].realStarted + " status:" + tasks[i].status);
        }
    }
    //alert("canvas:" + canvas.id + " docW:" + document.width);

}

//wait,doing,done,pause,cancel,closed
function drawTask(ctx, id, name, startDate, hours, status, idx)
{
    ctx.fillStyle = C_TaskNameColor;
    ctx.font = C_TaskNameFont;
    //ctx.fillText("auto:" + name, 10, 30);


    if(status == 'done')
    {
        ctx.fillStyle = C_TaskColor_Completed;
    }
    else if(status == 'wait')
    {
        ctx.fillStyle = C_TaskColor_Pending;
    }

    //alert(ctx.fillStyle);

    var cur = Date.now();

    var start = convertStringToDate(startDate);
    //alert(start);

    var d = days_between(start, cur);
    //alert("days:" + d);

    var taskDays =  Math.ceil(hours / 8);
    //alert("taskDays:" + taskDays);

    if(status != 'done')
    {
        if(d + taskDays < 0)
        {
            ctx.fillStyle = C_TaskColor_Delay;
        }
        else if(d + taskDays < 2)
        {
            ctx.fillStyle = C_TaskColor_Warning;
        }
    }

    var xy = dateToCoord(start, idx);
    var xyend =  dateToCoord(new Date(start).addDays(taskDays), idx);

    ctx.fillRect(xy[0], xy[1], xyend[0] - xy[0], xyend[1] - xy[1] + C_Taskbar_Height);

    ctx.fillStyle = C_TaskNameColor;
    ctx.font = C_TaskNameFont;
    ctx.fillText("[" + id + "]" + name, xyend[0] + 8, xyend[1] + C_FontHeight);
}

function dateToCoord(startDate, idx)
{
    var now = Date.now();
    //new Date(now).getTime();

    var d = days_between(startDate, now);

    var x = d * C_Taskbar_DayUnit;
    var y = (C_Taskbar_Height + C_Taskbar_VSpace) * idx;

    //print_call_stack();
    //console.log("dateToCoord idx:" + idx + " date:" + startDate + " xy:" + x + "," + y + " d:" + d);
    return [x, y];
}