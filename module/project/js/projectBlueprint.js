
const C_TaskColor_Pending = "#0080c8";
const C_TaskColor_Completed = "#64c800";
const C_TaskColor_Warning = "#ff8000";
const C_TaskColor_Delay = "#AA0000";
const C_TaskColor_ErrorStart = "#FFEE00";
const C_TaskColor_Closed = "#888888";
const C_TaskColor_Pause = "#880088";
//const C_TaskColor_Cancel = "#440000";
const C_TaskColor_Cancel = "#AAAAAA";

const C_TaskNameColor = "#000000";
//const C_TaskNameFont = "16px 新宋体";
const C_TaskUserNameColor = "#d10054";
const C_TaskUserDeptColor = "#006ea5";
const C_TaskNameFont = "24px 微软雅黑";
const C_RulerFont = "16px 微软雅黑";
const C_FontHeight = 24;

var EDrawUnit =
{
    "day" : 0,
    "week" : 1,
    "month" : 2,
    "season" : 3
};

var C_Taskbar_Height = 10;
var C_Taskbar_VSpace = 30;
var C_Taskbar_DayUnit = 10;


var origX = 200;
var origY = C_Taskbar_Height * 4;

var lastX = -1;
var lastY = 0;

var g_tasks = [];
var canvas = null;
var context = null;
var mouseIsDown = false;

var drawType = EDrawUnit.day;

var g_showDelayOnly = false;

Date.prototype.addDays = function(days) {
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
}

function onShowDelayOnly()
{
    g_showDelayOnly = !g_showDelayOnly;
    console.log("show delay:", g_showDelayOnly);
    redraw();
}

function onZoomIn()
{
    //alert("onZoomIn");
    C_Taskbar_DayUnit = Math.min(C_Taskbar_DayUnit + 5, 30);
    redraw();
}

function onZoomOut()
{
    //alert("onZoomOut");
    C_Taskbar_DayUnit = Math.max(C_Taskbar_DayUnit - 5, 2);
    redraw();
}

function onZoomDay()
{
    drawType = EDrawUnit.day;
    C_Taskbar_DayUnit = 40;
    redraw();
}

function onZoomWeek()
{
    drawType = EDrawUnit.week;
    C_Taskbar_DayUnit = 20;
    redraw();
}

function onZoomMonth()
{
    drawType = EDrawUnit.month;
    C_Taskbar_DayUnit = 5;
    redraw();
}


function onZoomSeason()
{
    drawType = EDrawUnit.season;
    C_Taskbar_DayUnit = 2;
    redraw();
}

function drawRuler()
{
    ctx = context;
    ctx.save();
    ctx.translate(origX, 0);

    ctx.font = ctx.font = C_RulerFont;
    ctx.strokeStyle = "#888888";
    ctx.lineWidth = 2;

    ctx.fillStyle = "#dddddd";
    ctx.fillRect(-100000, 0, 1000000, 30);



    ctx.restore();
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
    canvas = document.getElementById("projectCanvas");
    context = canvas.getContext('2d');

    canvas.onmousemove = onCanvasMouseMove;
    canvas.onmousedown = onCanvasMouseDown;
    canvas.onmouseup = onCanvasMouseUp;
    canvas.onmouseout = onCanvasMouseOut;
});
//*/

function onCanvasMouseOut(evt)
{
    mouseIsDown = false;
    //console.log("!!!! canvas mouse out !!!!");
}

function onCanvasMouseMove(evt)
{
    var mousePos = getMousePos(canvas, evt);
    var message = 'Mouse position: ' + mousePos.x + ',' + mousePos.y;
    writeMessage(canvas, message, mousePos);

    if(lastX == -1)
    {
        lastX = mousePos.x;
        lastY = mousePos.y;
    }

    if(mouseIsDown)
    {
        origX += mousePos.x - lastX;
        //origY += mousePos.y - lastY;
    }

    lastX = mousePos.x;
    lastY = mousePos.y;
}

function onCanvasMouseDown(evt)
{
    mouseIsDown = true;
    var mousePos = getMousePos(canvas, evt);
    var message = 'Mouse Down: ' + mousePos.x + ',' + mousePos.y;
    writeMessage(canvas, message, mousePos);
}

function onCanvasMouseUp(evt)
{
    mouseIsDown = false;
    var mousePos = getMousePos(canvas, evt);
    var message = 'Mouse Up: ' + mousePos.x + ',' + mousePos.y;
    writeMessage(canvas, message, mousePos);
}

function writeMessage(canvas, message, mousePos) {
    //var context = canvas.getContext('2d');
    //console.log("====msg " + message);

    redraw();

    context.font = '18pt Calibri';
    context.fillStyle = 'black';
    //context.translate(0, 0);
    context.fillText(message, mousePos.x, mousePos.y);
}

function redraw()
{
    context.clearRect(0, 0, canvas.width, canvas.height);

    //context.translate(0, 0);
    drawBg();

    //context.translate(0, 0);
    drawProjectBlueprintGlobal();

    //context.translate(0, 0);
    drawDeadline();

    drawRuler();
}

function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
    };
}


$(document).ready(function()
{
    //alert("doc.ready");


    var tasks = ajaxGetTasks();
    //alert("init:" + tasks);

});


function ajaxGetTasks()
{
    url = createLink('task', 'ajaxGetBlueprintTasks');
    //alert("on_createUserAbsent userid:" + userid + " day:" + day + " url:" + url);

    var tasks = null;

    //*
    $.getJSON(link, function(r)
    {
        //alert("ajaxGetTasks:" + r.toString());
        g_tasks = r;
        canvas.height = ((g_tasks.length + 6) * (C_Taskbar_Height + C_Taskbar_VSpace));
        canvas.width = 2048;
        console.log("==== w:" + document.width + " H:" + canvas.height);

       redraw();


        //console.warn("blueprint tasks:", g_tasks, g_tasks.length);
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

function drawBg()
{
    //var canvas = document.getElementById("projectCanvas");
    ctx = context;
    ctx.save();
    ctx.translate(origX, origY);
    //canvas.setWidth(document.width);
    //alert("canvas:" + canvas.id + " docW:" + document.width);
    var ctx = canvas.getContext("2d");
    ctx.fillStyle = "#ffFFff";
    ctx.fillRect(0, 0, 4096, 4096);
    ctx.restore();
}

function drawDeadline()
{
    //var canvas = document.getElementById("projectCanvas");
    var ctx = context;//canvas.getContext("2d");
    ctx.save();
    ctx.translate(origX, origY);

    ctx.strokeStyle = "#7e0000";
    ctx.beginPath();
    ctx.moveTo(0, -1000000);
    ctx.lineTo(0, 1000000);
    ctx.lineWidth = 4;
    ctx.stroke();
    ctx.restore();
}


function drawProjectBlueprintGlobal()
{
    drawProjectBlueprint(g_tasks);
}

function drawProjectBlueprint(tasks)
{
    //alert("drawProjectBlueprint:" + tasks.length);

    //console.error("task len:", tasks.length);

    //var canvas = document.getElementById("projectCanvas");

    //canvas.setWidth(document.width);
    //alert("canvas:" + canvas.id + " docW:" + document.width);
    var ctx = context;//canvas.getContext("2d");

    ctx.save();
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
        var drawYIdx = 0;
        for(var i = 0; i < tasks.length; ++i)
        {
            if(!g_showDelayOnly || (g_showDelayOnly && isTaskDelay(tasks[i])))
            {
                drawTask(ctx, tasks[i].id, tasks[i].name, tasks[i].assignedToRealName, tasks[i].deptName,  tasks[i].estStarted, tasks[i].estimate, tasks[i].status, drawYIdx);
                ++drawYIdx;
            }
            //alert("task:" + tasks[i]);
            //console.log("task[" + tasks[i].id + "]: " + tasks[i].name + " start:" + tasks[i].realStarted + " status:" + tasks[i].status);
        }
    }
    ctx.restore();
    //alert("canvas:" + canvas.id + " docW:" + document.width);

}

function isTaskDelay(task)
{
    var start = convertStringToDate(task.estStarted);
    var cur = Date.now();
    var d = days_between(start, cur);
    var taskDays =  Math.ceil(task.estimate / 8);

   // console.log("isTaskDelay: staus:", task.status);

    if(task.status == 'doing')
    {
        if(d + taskDays <= 0)
        {
            //console.log("task is delay:", task.id, task.name);
            return true;
        }
    }

    return false;
}

//wait,doing,done,pause,cancel,closed
function drawTask(ctx, id, name, user, dept, startDate, hours, status, idx)
{
    ctx.fillStyle = C_TaskNameColor;
    ctx.font = C_TaskNameFont;
    //ctx.fillText("auto:" + name, 10, 30);
    var cur = Date.now();

    var start = convertStringToDate(startDate);
    //alert(start);

    var d = days_between(start, cur);
    //alert("days:" + d);

    var taskDays =  Math.ceil(hours / 8);
    //alert("taskDays:" + taskDays);



    ctx.fillStyle = C_TaskColor_Pending;
    if(status == 'done')
    {
        ctx.fillStyle = C_TaskColor_Completed;
    }

    //alert(ctx.fillStyle);

    if(status == 'doing')
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

    if(status == 'wait')
    {
        if(d + 1 < 0)
        {
            ctx.fillStyle = C_TaskColor_ErrorStart;
        }
    }

    if(status == 'closed')
    {
        ctx.fillStyle = C_TaskColor_Closed;
    }

    if(status == 'pause')
    {
        ctx.fillStyle = C_TaskColor_Pause;
    }

    if(status == 'cancel')
    {
        ctx.fillStyle = C_TaskColor_Cancel;
    }

    var xy = dateToCoord(start, idx);
    var xyend =  dateToCoord(new Date(start).addDays(taskDays), idx);

    //ctx.fillRect(xy[0], xy[1], xyend[0] - xy[0], xyend[1] - xy[1] + C_Taskbar_Height);

    roundRect(ctx, xy[0], xy[1], xyend[0] - xy[0], xyend[1] - xy[1] + C_Taskbar_Height, 5, true, true);
    if(status == 'cancel')
    {
        drawLine(xy[0], xy[1] + C_Taskbar_Height / 2, xyend[0] - xy[0]);
    }
    ctx.strokeStyle = "#111111";
    ctx.fillStyle = C_TaskNameColor;
    ctx.lineWidth = 2;
    ctx.font = C_TaskNameFont;
    ctx.fillText("[" + id + "]  " + name, xyend[0] + 8, xyend[1] + C_FontHeight);

    ctx.fillStyle = C_TaskUserNameColor;
    var userNameStr = "        [" + user + "]";
    ctx.fillText(userNameStr, xyend[0] + 8 + ctx.measureText(name).width + 50, xyend[1] + C_FontHeight);

    ctx.fillStyle = C_TaskUserDeptColor;
    var depStr = " - " + dept;
    ctx.fillText(depStr, xyend[0] + 8 + ctx.measureText(name).width + ctx.measureText(userNameStr).width + 50, xyend[1] + C_FontHeight);
}

function drawLine(x, y, w)
{
    ctx = context;
    ctx.save();
    //ctx.translate(origX, origY);

    ctx.strokeStyle = "#000000";
    ctx.beginPath();
    ctx.moveTo(x - 8, y);
    ctx.lineTo(x + w + 8, y);
    ctx.lineWidth = 2;
    ctx.stroke();
    ctx.restore();
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

/**
 * Draws a rounded rectangle using the current state of the canvas.
 * If you omit the last three params, it will draw a rectangle
 * outline with a 5 pixel border radius
 * @param {CanvasRenderingContext2D} ctx
 * @param {Number} x The top left x coordinate
 * @param {Number} y The top left y coordinate
 * @param {Number} width The width of the rectangle
 * @param {Number} height The height of the rectangle
 * @param {Number} [radius = 5] The corner radius; It can also be an object
 *                 to specify different radii for corners
 * @param {Number} [radius.tl = 0] Top left
 * @param {Number} [radius.tr = 0] Top right
 * @param {Number} [radius.br = 0] Bottom right
 * @param {Number} [radius.bl = 0] Bottom left
 * @param {Boolean} [fill = false] Whether to fill the rectangle.
 * @param {Boolean} [stroke = true] Whether to stroke the rectangle.
 */
function roundRect(ctx, x, y, width, height, radius, fill, stroke) {
    ctx.save();
    if (typeof stroke == 'undefined') {
        stroke = true;
    }
    if (typeof radius === 'undefined') {
        radius = 5;
    }
    if (typeof radius === 'number') {
        radius = {tl: radius, tr: radius, br: radius, bl: radius};
    } else {
        var defaultRadius = {tl: 0, tr: 0, br: 0, bl: 0};
        for (var side in defaultRadius) {
            radius[side] = radius[side] || defaultRadius[side];
        }
    }
    ctx.beginPath();
    ctx.moveTo(x + radius.tl, y);
    ctx.lineTo(x + width - radius.tr, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
    ctx.lineTo(x + width, y + height - radius.br);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
    ctx.lineTo(x + radius.bl, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
    ctx.lineTo(x, y + radius.tl);
    ctx.quadraticCurveTo(x, y, x + radius.tl, y);
    ctx.closePath();
    if (fill) {
        ctx.fill();
    }
    if (stroke) {
        ctx.stroke();
    }

    ctx.restore();
}