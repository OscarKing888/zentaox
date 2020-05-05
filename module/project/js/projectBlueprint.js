/*
var myDate = new Date();

myDate.getYear(); //获取当前年份(2位)
myDate.getFullYear(); //获取完整的年份(4位,1970)
myDate.getMonth(); //获取当前月份(0-11,0代表1月)
myDate.getDate(); //获取当前日(1-31)
myDate.getDay(); //获取当前星期X(0-6,0代表星期天)
myDate.getTime(); //获取当前时间(从1970.1.1开始的毫秒数)
myDate.getHours(); //获取当前小时数(0-23)
myDate.getMinutes(); //获取当前分钟数(0-59)
myDate.getSeconds(); //获取当前秒数(0-59)
myDate.getMilliseconds(); //获取当前毫秒数(0-999)
myDate.toLocaleDateString(); //获取当前日期
var mytime=myDate.toLocaleTimeString(); //获取当前时间
myDate.toLocaleString( ); //获取日期与时间

 */

const C_TaskColor_StoryTitle = "#0077ba";
const C_TaskColor_Story = "#0077ba";
const C_StoryNameColor = "#d000ff";

const C_TaskColor_Pending = "#0080c8";
const C_TaskColor_Completed = "#64c800";

const C_TaskColor_Warning = "#ff8000";
const C_TaskColor_Delay = "#AA0000";
const C_TaskColor_ErrorStart = "#FFEE00";
const C_TaskColor_Closed = "#888888";
const C_TaskColor_Pause = "#880088";
//const C_TaskColor_Cancel = "#440000";
const C_TaskColor_Cancel = "#AAAAAA";

const C_TaskColor_StartNotSet = "#ff0000";

const C_TaskNameColor = "#000000";
//const C_TaskNameFont = "16px 新宋体";
const C_TaskUserNameColor = "#d10054";
const C_TaskUserDeptColor = "#006ea5";

var EDrawUnit =
{
    "day" : 0,
    "week" : 1,
    "month" : 2,
    "season" : 3
};

var C_Taskbar_DayUnit = 32;
var C_Taskbar_Height = 10;
var C_Taskbar_VSpace = 20;

var C_TaskNameFont = "20px 微软雅黑";
var C_RulerFont = "14pt Calibri";
var C_FontHeight = 20;
var C_RulerHeight = 24;


var origX = 200;
var origY = C_RulerHeight * 3;

var lastX = -1;
var lastY = 0;

var g_tasks = null;
var canvas = null;
var context = null;
var mouseIsDown = false;

var drawType = EDrawUnit.day;

var g_showDelayOnly = false;
var g_minTaskDate = Date.now();
var g_maxTaskDate = Date.now();
var g_maxTaskDays = 1;

var g_drawYIdx = 0;
var g_boundYMax = 0;
var g_boundXMin = 0;
var g_boundXMax = 0;

var selectedMilestone = 0;
var selectedDept = 0;


function onOrigi()
{
    origX = canvas.width / 3 * 1;
    origY = C_Taskbar_Height * 4 + C_RulerHeight * 3;
    redraw();
}

function onShowDelayOnly()
{
    g_showDelayOnly = !g_showDelayOnly;
    //console.log("show delay:", g_showDelayOnly);
    refreshDelayCheck();
    redraw();
}

function refreshDelayCheck()
{
    $('#delayLabel').text(g_showDelayOnly  ? '✔已延期' : ' 已延期');
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
    C_Taskbar_DayUnit = 32;
    C_Taskbar_Height = 10;
    C_Taskbar_VSpace = 20;

    C_TaskNameFont = "20px 微软雅黑";
    C_RulerFont = "14pt Calibri";
    C_FontHeight = 20;
    C_RulerHeight = 24;

    redraw();
}

function onZoomWeek()
{
    drawType = EDrawUnit.week;
    C_Taskbar_DayUnit = 24;
    C_Taskbar_Height = 8;
    C_Taskbar_VSpace = 15;

    C_TaskNameFont = "14px 微软雅黑";
    C_RulerFont = "8pt Calibri";
    C_FontHeight = 12;
    C_RulerHeight = 15;

    redraw();
}

function onZoomMonth()
{
    drawType = EDrawUnit.month;
    C_Taskbar_DayUnit = 16;
    C_Taskbar_Height = 8;
    C_Taskbar_VSpace = 8;

    C_TaskNameFont = "12px 微软雅黑";
    C_RulerFont = "8pt Calibri";
    C_FontHeight = 12;
    C_RulerHeight = 15;
    redraw();
}


function onZoomSeason()
{
    drawType = EDrawUnit.season;
    C_Taskbar_DayUnit = 2;
    redraw();
}

var g_weekDay =
    {
        0 : "日",
        1 : "一",
        2 : "二",
        3 : "三",
        4 : "四",
        5 : "五",
        6 : "六",
    };

function drawRuler()
{
    ctx = context;
    ctx.save();

    ctx.font = ctx.font = C_RulerFont;
    ctx.strokeStyle = "#888888";
    ctx.lineWidth = 2;

    ctx.fillStyle = "#dddddd";
    var ruleHeight = C_RulerHeight;
    ctx.fillRect(0, 0, window.innerWidth, ruleHeight * 3);

    ctx.translate(origX, 0);

    context.font = C_RulerFont;
    context.fillStyle = 'black';

    var cur = Date.now();
    var curMonth = new Date(cur).getMonth();
    var curYear = new Date(cur).getFullYear();

    // draw months
    for(var i = -5; i < 7; ++i)
    {
        var nextMonth = curMonth + i;

        var nextYear = curYear + Math.floor(nextMonth / 12);
        //console.log("idx:%d curMonth:%d nextMonth:%d nextYear:%d", i, curMonth, nextMonth, nextYear);

        var nextDate = new Date(nextYear, nextMonth, 1);
        var nextDate2 = new Date(nextYear, nextMonth + 1, 1);
        var xy = dateToCoord(nextDate, 0, 0);
        var xy2 = dateToCoord(nextDate2, 0, 0);

        ctx.rect(xy[0], xy[1], xy2[0] - xy[0], ruleHeight);
        var ymStr = nextYear.toString() + "年" + (nextMonth + 1).toString() + "月";// + "   cur:" + curMonth;
        ctx.fillText(ymStr, xy[0] + 8, xy[1] + C_FontHeight);
    }

    var weekendClr = "#aaaaaa";
    var weekdayClr = "#eeeeee";

    for(var i = -31 * 5; i < 31 * 7; ++i)
    {
        var nextDate = new Date(cur).addDays(i);
        var xy = dateToCoord(nextDate, 0, 0);

        var weekDay = nextDate.getDay();

        if(weekDay == 0 || weekDay == 6)
        {
            ctx.fillStyle = weekendClr;
        }
        else
        {
            ctx.fillStyle = weekdayClr;
        }

        // draw days
        ctx.fillRect(xy[0], xy[1] + ruleHeight * 1, C_Taskbar_DayUnit, ruleHeight);
        ctx.rect(xy[0], xy[1] + ruleHeight * 1, C_Taskbar_DayUnit, ruleHeight);
        var dayStr = nextDate.getDate().toString();
        var dayStrWidth = ctx.measureText(dayStr).width;


        // draw weekdays
        ctx.fillRect(xy[0], xy[1] + ruleHeight * 2, C_Taskbar_DayUnit, ruleHeight);
        ctx.rect(xy[0], xy[1] + ruleHeight * 2, C_Taskbar_DayUnit, ruleHeight);
        var weekdayStr = g_weekDay[weekDay];
        var weekdayStrWidth = ctx.measureText(weekdayStr).width;

        ctx.fillStyle = "black";
        ctx.fillText(dayStr, xy[0] + (C_Taskbar_DayUnit - dayStrWidth) / 2, xy[1] + C_FontHeight + ruleHeight * 1);
        ctx.fillText(weekdayStr, xy[0] + (C_Taskbar_DayUnit - weekdayStrWidth) / 2, xy[1] + C_FontHeight + ruleHeight * 2);
        //console.log("draw ruler day:", xy, nextDate.getDaysInMonth());
    }

    //ctx.fillText("ssssssssssssssssssssssssssss", 100, 10);

    ctx.stroke();

    ctx.translate(-origX, 0);
    ctx.fillStyle = weekdayClr;
    ctx.fillRect(canvas.width - ruleHeight/2, ruleHeight * 3, ruleHeight/2, g_boundYMax);

    ctx.fillStyle = weekendClr;
    var scrollPos = Math.abs(origY) / Math.abs(g_boundYMax) * canvas.height;
    ctx.fillRect(canvas.width - ruleHeight/2, ruleHeight * 3 + scrollPos, ruleHeight / 2, ruleHeight / 2);

    //*
    //if(mouseIsDown)
    {
        ctx.fillStyle = weekdayClr;
        ctx.fillRect(0, canvas.height - ruleHeight/2, canvas.width, ruleHeight / 2);

        ctx.fillStyle = weekendClr;
        var scrollPosX =((origX) / (g_boundXMax - g_boundXMin)) * canvas.width;
        ctx.fillRect(scrollPosX, canvas.height - ruleHeight / 2, C_Taskbar_DayUnit, ruleHeight / 2);

        //console.warn("ox:%d oy:%d pos:%f minX:%d maxX:%d maxY:%d", origX, origY, scrollPosX, g_boundXMin, g_boundXMax, g_boundYMax);
    }
    //*/

    ctx.restore();
}

function drawMiniMap()
{
    //roundRect(context, lastX, lastY, 100, 10, 5, true, false);
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
    return Math.ceil(difference_ms/ONE_DAY);
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
$(document).ready(function()
{
    //alert("init()");
    //console.log($("#tasks"));
    //drawProjectBlueprint($("#tasks"));

    refreshDelayCheck();

    $('#dept').change(function()
    {
        onDeptChange();
        ajaxGetTasks();
    });

    $('#milestone').change(function()
    {
        onMilestoneChange();
        ajaxGetTasks();
    });

    canvas = document.getElementById("projectCanvas");
    context = canvas.getContext('2d');

    canvas.onmousemove = onCanvasMouseMove;
    canvas.onmousedown = onCanvasMouseDown;
    canvas.onmouseup = onCanvasMouseUp;
    canvas.onmouseout = onCanvasMouseOut;
    canvas.onmousewheel = onCanvasMouseWheel;

    window.addEventListener("resize", resizeCanvas, false);
    resizeCanvas();
    onOrigi();

    //console.log("canSeeTest:", canSee(-1, -1), canSee(canvas.width + 1, canvas.height + 1));

});
//*/

function setupCanvas()
{
    if(canvas == null)
    {
        canvas = document.getElementById("projectCanvas");
    }

    if(context == null)
    {
        context = canvas.getContext('2d');
    }
}

function onDeptChange()
{
    selectedDept = $('#dept option:selected').val();
    //alert("onDeptChange:" + selectedDept + " " + $('#dept option:selected').text());
}

function onMilestoneChange()
{
    selectedMilestone = $('#milestone option:selected').val();
    //  alert("onMilestoneChange    " + selectedMilestone + ":" + $('#milestone option:selected').text());
}

function resizeCanvas() {
    var captionBarHeight = 165 + 32;

    //canvas.width = screen.availWidth;
    //canvas.height = screen.availHeight;

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight - captionBarHeight;
    redraw();
    //console.warn("screen w:%d h:%d inner w:%d h:%d", screen.availWidth, screen.availHeight, window.innerWidth, window.innerHeight - captionBarHeight)
}

function onCanvasMouseWheel(evt)
{
    origY -= evt.deltaY;

    clampMinMaxOrigXY();

    redraw();
    //console.log("!!!! canvas mouse onCanvasMouseWheel !!!!", origY, evt.deltaY);
}


function onCanvasMouseOut(evt)
{
    mouseIsDown = false;
    //console.log("!!!! canvas mouse out !!!!");
}

function clampMinMaxOrigXY()
{

    origY = Math.min(C_Taskbar_Height * 4 + 3 * C_RulerHeight, origY);
    return;
    //origY = Math.max(-(C_Taskbar_Height + C_Taskbar_VSpace) * (g_drawYIdx), origY);
    origY = Math.max(-g_boundYMax, origY);

    return;
    origX = Math.max(g_boundXMin, origX);
    origX = Math.min(g_boundXMax - C_Taskbar_Height, origX);

    //console.warn("ox:%d oy:%d minX:%d maxX:%d maxY:%d", origX, origY, g_boundXMin, g_boundXMax, g_boundYMax);
}

function canSee(x, y)
{
    if(x >= origX && x <= origX + canvas.width)
    {
        return true;
    }

    if(y >= origY && y <= origY + canvas.height)
    {
        return true;
    }

    return false;
}

function onCanvasMouseMove(evt)
{
    var mousePos = getMousePos(canvas, evt);
    var message = 'Mouse move position: ' + mousePos.x + ',' + mousePos.y;
    //console.log(message);


    //writeMessage(canvas, message, mousePos);

    if(lastX == -1)
    {
        lastX = mousePos.x;
        lastY = mousePos.y;
    }

    if(mouseIsDown)
    {
        origX += (mousePos.x - lastX) * 5;
        origY += (mousePos.y - lastY) * 5;
        clampMinMaxOrigXY();

        redraw();
    }

    lastX = mousePos.x;
    lastY = mousePos.y;
}

function onCanvasMouseDown(evt)
{
    mouseIsDown = true;
    var mousePos = getMousePos(canvas, evt);
    var message = 'Mouse Down: ' + mousePos.x + ',' + mousePos.y;
    //writeMessage(canvas, message, mousePos);
}

function onCanvasMouseUp(evt)
{
    mouseIsDown = false;
    var mousePos = getMousePos(canvas, evt);
    var message = 'Mouse Up: ' + mousePos.x + ',' + mousePos.y;
    //writeMessage(canvas, message, mousePos);
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
    //  alert("redraw");

    setupCanvas();

    context.clearRect(0, 0, canvas.width, canvas.height);

    //alert("drawBg");
    //context.translate(0, 0);
    drawBg();

    //alert("drawProjectBlueprintGlobal");
    //context.translate(0, 0);
    drawProjectBlueprintGlobal();

    //alert("drawRuler");
    drawRuler();

    //alert("drawDeadline");
    //context.translate(0, 0);
    drawDeadline();

    //alert("drawMiniMap");
    drawMiniMap();

    context.fillStyle = C_TaskColor_StoryTitle;
    roundRect(context, origX, origY, 5, 5, 2, true);
    //context.rect(origX, origX, 10, 10);
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
    selectedDept = $('#dept option:selected').val();
    selectedMilestone = $('#milestone option:selected').val();

    //alert("ajaxGetTasks selectedDept:" + selectedDept + " selectedMilestone:" + selectedMilestone);

    url = createLink('task', 'ajaxGetBlueprintTasks', 'dept=' + selectedDept + "&milestone=" + selectedMilestone);
    //alert("ajaxGetTasks userid:" + " url:" + url);

    var tasks = null;

    //*
    $.getJSON(link, function(r)
    {
        //console.log("ajaxGetTasks:", JSON.stringify(r));
        g_tasks = r;
        //canvas.height = ((g_tasks.length + 6) * (C_Taskbar_Height + C_Taskbar_VSpace));
        //canvas.width = 4096;
        //canvas.height = 2080;
        //console.log("==== w:" + document.width + " H:" + canvas.height + " tasks:" + g_tasks.length);

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

    return g_tasks;
}

function drawBg()
{
    setupCanvas();
    //var canvas = document.getElementById("projectCanvas");
    ctx = context;
    ctx.save();
    ctx.translate(origX, origY);
    //canvas.setWidth(document.width);
    //alert("canvas:" + canvas.id + " docW:" + document.width);
    var ctx = canvas.getContext("2d");
    ctx.fillStyle = "#ffFFff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
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


function drawProjectBlueprintGlobal() {
    if (selectedMilestone == 0) {
        drawProjectBlueprint(g_tasks);
    }
    else
    {
        drawProjectBlueprintWithMilestone(g_tasks);
    }
}

function drawProjectBlueprintWithMilestone(tasks)
{
    var ctx = context;//canvas.getContext("2d");

    ctx.save();
    ctx.translate(origX, origY);

    /*
    var idx = 0;
    drawBar(ctx, "1 CRASH自动收集分析",  "2018-5-1", 24, 'done', idx++);
    drawBar(ctx, "2 CRASH自动收集分析",  "2018-5-1", 32, 'doing', idx++);
    drawBar(ctx, "3 CRASH自动收集分析",  "2018-5-2", 40, 'wait', idx++);
    drawBar(ctx, "4 CRASH自动收集分析",  "2018-5-6", 48, 'wait', idx++);
    //*/

    ctx.fillStyle = C_TaskColor_Completed;

    g_drawYIdx = 0;
    g_boundYMax = 0;
    g_boundXMin = 0;
    g_boundXMax = 0;

    //alert("drawProjectBlueprintWithMilestone:" + tasks);

    if(tasks != null)
    {
        for(var i = 0; i < tasks.length; ++i) {
            //console.log("   drawTasks:", JSON.stringify(tasks[i]));
            var taskPair = tasks[i];

            var stroy = taskPair.story;
            var taskCount = taskPair.tasks.length;

            //drawBar(ctx, stroy.taskEndDate, stroy.id, stroy.title, stroy.assignedToRealName, stroy.deptName + "Cnt:" + taskCount,  stroy.taskBeginDate, 'story', g_drawYIdx);

            var storyIdx = g_drawYIdx;
            var storyBeginDate = null;
            var storyEndDate = null;
            var taskDrawCount = 0;
            //drawBar(ctx, stroy.taskEndDate, stroy.id, stroy.title, stroy.assignedToRealName, stroy.deptName,  stroy.taskBeginDate, 'story', g_drawYIdx);
            //drawBar(ctx, storyBeginDate, stroy.id, stroy.title, stroy.assignedToRealName, stroy.deptName,  storyBeginDate, 'story', storyIdx);

            ++g_drawYIdx;

            ctx.fillStyle = C_TaskColor_Completed;
            ctx.strokeStyle = "#000000";


            for (var j = 0; j < taskPair.tasks.length;++j){
                var task = taskPair.tasks[j];
                //console.log("   drawTasks:", i, tasks.name);
                if (!g_showDelayOnly || (g_showDelayOnly && isTaskDelay(task))) {

                    ++taskDrawCount;
                    var beginDate = convertStringToDate(task.estStarted);
                    var realBeginDate = convertStringToDate(task.realStarted);
                    var endDate = convertStringToDate(task.deadline);

                    //console.log("task delay:", task.id, task.name);

                    if(isNaN(beginDate))
                    {
                        //console.log("StartErr ", task.id, task.name, "s:", task.estStarted, beginDate, "rs:", task.realStarted, "e:", new Date(endDate).toDateString());
                    }

                    if(isNaN(endDate))
                    {
                        //console.log("EndErr ", task.id, task.name, "end:", task.deadline, endDate);
                    }

                    if(isNaN(beginDate))
                    {
                        beginDate = convertStringToDate(task.realStarted);
                    }

                    if(isNaN(beginDate))
                    {
                        beginDate = new Date();
                    }

                    if(storyBeginDate == null || (!isNaN(beginDate) && storyBeginDate > beginDate))
                    {
                        storyBeginDate = beginDate;
                    }

                    if(storyEndDate == null || (!isNaN(endDate) && storyEndDate < endDate))
                    {
                        storyEndDate = endDate;
                    }

                    ctx.fillStyle = C_TaskColor_Completed;
                    ctx.strokeStyle = "black";

                    drawBar(ctx, endDate, task.id, task.name, task.assignedToRealName, task.deptName, beginDate, task.status, g_drawYIdx);
                    ++g_drawYIdx;
                    //ctx.stroke();

                    //if(g_drawYIdx > 100)
                    {
                        //console.error("break for 100 tasks");
                        // break;
                    }
                }
            }

            // draw story
            ctx.strokeStyle = C_TaskColor_StoryTitle;
            var xy = dateToCoord(storyBeginDate, storyIdx, 0);
            var xyend = dateToCoord(storyEndDate, storyIdx + taskDrawCount, 1, 0);
            xyend[1] = (C_Taskbar_Height + C_Taskbar_VSpace) * (taskDrawCount + 1);

            ctx.rect(xy[0], xy[1], xyend[0] - xy[0], xyend[1]);
            ctx.stroke();

            ctx.strokeStyle = C_TaskColor_Story;
            drawBar(ctx, storyEndDate, stroy.id, stroy.title, stroy.assignedToRealName, stroy.deptName,  storyBeginDate, 'story', storyIdx);
            //ctx.stroke();
            g_drawYIdx += 5;
        }
            //alert("task:" + tasks[i]);
            //console.log("task[" + tasks[i].id + "]: " + tasks[i].name + " start:" + tasks[i].realStarted + " status:" + tasks[i].status);
    }
    ctx.stroke();
    ctx.restore();
    //alert("canvas:" + canvas.id + " docW:" + document.width);
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
    drawBar(ctx, "1 CRASH自动收集分析",  "2018-5-1", 24, 'done', idx++);
    drawBar(ctx, "2 CRASH自动收集分析",  "2018-5-1", 32, 'doing', idx++);
    drawBar(ctx, "3 CRASH自动收集分析",  "2018-5-2", 40, 'wait', idx++);
    drawBar(ctx, "4 CRASH自动收集分析",  "2018-5-6", 48, 'wait', idx++);
    //*/

    ctx.fillStyle = C_TaskColor_Completed;
    //ctx.fillText("SSSSSSSSSSSSSSSSSSS", 0, C_FontHeight);
    //ctx.moveTo(0, 1000);
    //ctx.lineTo(4000, 4000);
    //ctx.stroke();
    g_drawYIdx = 0;
    g_boundYMax = 0;
    g_boundXMin = 0;
    g_boundXMax = 0;

    //console.log("   drawTasks:================================>>");

    if(tasks != null)
    {
        for(var i = 0; i < tasks.length; ++i)
        {
            // if(tasks[i].id == 865)
            // {
            //     console.log("   drawTasks:", i, " ", JSON.stringify(tasks[i]));
            // }

            //console.log("   drawTasks:", i, tasks[i].name);

            if(!g_showDelayOnly || (g_showDelayOnly && isTaskDelay(tasks[i])))
            {
                var beginDate = convertStringToDate(tasks[i].estStarted);
                var endDate = convertStringToDate(tasks[i].deadline);
                drawBar(ctx, endDate, tasks[i].id, tasks[i].name, tasks[i].assignedToRealName, tasks[i].deptName,  beginDate, tasks[i].status, g_drawYIdx);
                ++g_drawYIdx;

                //if(g_drawYIdx > 100)
                {
                    //console.error("break for 100 tasks");
                   // break;
                }
            }
            //alert("task:" + tasks[i]);
            //console.log("task[" + tasks[i].id + "]: " + tasks[i].name + " start:" + tasks[i].realStarted + " status:" + tasks[i].status);
        }
    }
    ctx.stroke();
    ctx.restore();
    //alert("canvas:" + canvas.id + " docW:" + document.width);

}

function isTaskDelay(task)
{
    var start = convertStringToDate(task.estStarted);
    var deadline = convertStringToDate(task.deadline);
    var cur = Date.now();
    var d = days_between(start, cur);
    //var d = days_between(deadline, start);

    var taskDays =  (1 + days_between(deadline, start));//Math.ceil(task.estimate / 8);

   // console.log("isTaskDelay: staus:", task.status);

    if(task.status == 'doing'
    || task.status == 'wait')
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
function drawBar(ctx, deadline, id, name, user, dept, startDate, status, idx) {
    ctx.fillStyle = C_TaskNameColor;
    ctx.font = C_TaskNameFont;
    //ctx.fillText("auto:" + name, 10, 30);
    var cur = Date.now();

    var start = startDate;//convertStringToDate(startDate);
    var deadline = deadline;//convertStringToDate(deadline);

    if(isNaN(start))
    {
        start = Date.now();
        deadline = new Date(start).addDays(5);
    }

    if(isNaN(deadline))
    {
        deadline = new Date(start).addDays(5);
    }

    // if(start > deadline)
    // {
    //     var tmp = start;
    //     start = deadline;
    //     deadline = tmp;
    // }

    //alert(start);

    var d = days_between(start, cur);
    //alert("days:" + d);


    var taskDays =  (1 + days_between(deadline, start));//Math.ceil(task.estimate / 8);

    //alert("taskDays:" + taskDays);

    if(start > g_maxTaskDate)
    {
        g_maxTaskDate = start;
        g_maxTaskDays = taskDays;
    }
    else if(start < g_minTaskDate)
    {
        g_minTaskDate = start;
    }

    //console.warn("==== drawBar start");

    var xy = dateToCoord(start, idx, 0);
    var xyend = dateToCoord(new Date(start).addDays(taskDays), idx, 0);

    /*
    if(Math.abs(xy[0] - origX) > 4000)
    {
        //alert("discard task x:", xy[0]);
        return;
    }
    */
    var cntTasksMin =  Math.round(Math.abs(origY ) / (C_Taskbar_Height + C_Taskbar_VSpace) - 10);
    var cntTasksMax =  Math.round((Math.abs(origY) + canvas.height) / (C_Taskbar_Height + C_Taskbar_VSpace));
    var needDraw = idx >= cntTasksMin && idx <= cntTasksMax; // xy[1] > origY && xyend[1] < origY + canvas.height;// canSee(xy[0], xy[1]) || canSee(xyend[0], xyend[1]);



    if(!needDraw)
    {
        //console.log("skip draw idx:", idx, "oY:", origY, "cntTask:", cntTasksMin, " - ", cntTasksMax);
        //console.log("skip draw: [", id, "]", name);
    }

    ctx.fillStyle = C_TaskColor_Pending;
    if (status == 'done') {
        ctx.fillStyle = C_TaskColor_Completed;
    }

    if (status == 'story') {
        ctx.fillStyle = C_TaskColor_Story;
    }

    //alert(ctx.fillStyle);

    if (status == 'doing') {
        if (d + taskDays < 0) {
            ctx.fillStyle = C_TaskColor_Delay;
        }
        else if (d + taskDays < 2) {
            ctx.fillStyle = C_TaskColor_Warning;
        }
    }

    if (status == 'wait') {
        if (d + 1 < 0) {
            ctx.fillStyle = C_TaskColor_ErrorStart;
        }
    }

    if (status == 'pause') {
        ctx.fillStyle = C_TaskColor_Pause;
    }

    if (status == 'cancel') {
        ctx.fillStyle = C_TaskColor_Cancel;
    }

    if (startDate == "0000-00-00") {
        ctx.fillStyle = C_TaskColor_StartNotSet;
        ctx.setLineDash([2]);
        xy[0] = 0;
        xyend[0] = taskDays * C_Taskbar_DayUnit;
        name = "【" + startDate + "】 " + name;
    }
    else
    {
        ctx.setLineDash([0]);
    }



    if (status == 'closed') {
        ctx.fillStyle = C_TaskColor_Closed;
    }

    //var xy = dateToCoord(start, idx);
    //var xyend =  dateToCoord(new Date(start).addDays(taskDays), idx);
    //xy = dateToCoord(start, idx);
    //xyend =  dateToCoord(new Date(start).addDays(taskDays), idx);


    //ctx.fillRect(xy[0], xy[1], xyend[0] - xy[0], xyend[1] - xy[1] + C_Taskbar_Height);

    if(needDraw)
    {
        roundRect(ctx, xy[0], xy[1], xyend[0] - xy[0], xyend[1] - xy[1] + C_Taskbar_Height, 5, true, false);
    }

    if(needDraw && status == 'cancel')
    {
        drawLine(xy[0], xy[1] + C_Taskbar_Height / 2, xyend[0] - xy[0]);
    }

    if(needDraw)
    {
        ctx.stroke();
    }

    //console.warn("==== stroke");

    if(start > deadline)
    {
        //var tmp = start;
        //start = deadline;
        //deadline = tmp;

        xy = dateToCoord(deadline, idx, 0);
        xyend = dateToCoord(new Date(deadline).addDays(-taskDays + 1), idx, 0);
    }

    var allStr = "";

    if (status == 'story') {
        ctx.fillStyle = C_StoryNameColor;
    }
    else{
        ctx.fillStyle = C_TaskNameColor;
    }

    ctx.strokeStyle = "#111111";

    ctx.lineWidth = 2;
    ctx.font = C_TaskNameFont;
    var idStr = "[" + id + "]  " + name;
    if(needDraw)
    {
        ctx.fillText(idStr, xyend[0] + 8, xyend[1] + C_FontHeight);
    }

    allStr += idStr;

    ctx.fillStyle = C_TaskUserNameColor;
    var userNameStr = "        [" + user + "]";
    if(needDraw)
    {
        ctx.fillText(userNameStr, xyend[0] + 8 + ctx.measureText(name).width + 50, xyend[1] + C_FontHeight);
    }

    allStr += userNameStr;


    ctx.fillStyle = C_TaskUserDeptColor;
    var depStr = " - " + dept;

    //depStr += " [" + idx + "]";

    if(needDraw)
    {
        ctx.fillText(depStr, xyend[0] + 8 + ctx.measureText(name).width + ctx.measureText(userNameStr).width + 50, xyend[1] + C_FontHeight);
    }

    allStr += depStr;


    g_boundYMax = Math.max((idx + 2) * (C_Taskbar_Height + C_Taskbar_VSpace) - canvas.height, g_boundXMax);

    var strW = ctx.measureText(allStr).width + 8 + 50 + 50;
    g_boundXMin = Math.min(xy[0] - canvas.width, g_boundXMin);
    g_boundXMin = Math.min(xyend[0] - strW, g_boundXMin);

    g_boundXMax = Math.max(xy[0] + strW, g_boundXMax);
    g_boundXMax = Math.max(xyend[0] + strW, g_boundXMax);
}

function drawLine(x, y, w)
{
    ctx = context;
    //ctx.save();
    //ctx.translate(origX, origY);

    ctx.strokeStyle = "#000000";
    ctx.beginPath();
    ctx.moveTo(x - 8, y);
    ctx.lineTo(x + w + 8, y);
    ctx.lineWidth = 2;
    //ctx.stroke();
    //ctx.restore();
}

function dateToCoord(startDate, idx, offsetDays)
{
    var dateNaN = isNaN(startDate);
    if(dateNaN)
    {
        //console.log("task:", idx, "date:", startDate);
        //startDate = Date.now();
    }

    var now = Date.now();
    //new Date(now).getTime();

    var d = days_between(startDate, now) + offsetDays;

    if(dateNaN)
    {
        //d = Math.max(1, d);
    }

    var x = d * C_Taskbar_DayUnit;
    var y = (C_Taskbar_Height + C_Taskbar_VSpace) * idx;

    /*
    if(isNaN(x))
    {
        console.warn("x is err days:%d date:%s idx:%d", d, startDate, idx);
    }
    */

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
    //ctx.save();
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

    //ctx.restore();
}

onZoomMonth();