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

const C_EventColor_Pending = "#0080c8";
const C_EventColor_Completed = "#64c800";
const C_EventColor_Warning = "#ff8000";
const C_EventColor_Delay = "#AA0000";
const C_EventColor_ErrorStart = "#FFEE00";
const C_EventColor_Closed = "#888888";
const C_EventColor_Pause = "#880088";
//const C_EventColor_Cancel = "#440000";
const C_EventColor_Cancel = "#AAAAAA";

const C_EventColor_StartNotSet = "#ff0000";

const C_EventNameColor = "#000000";
//const C_EventNameFont = "16px 新宋体";
const C_EventUserNameColor = "#d10054";
const C_EventUserDeptColor = "#006ea5";

var EDrawUnit =
    {
        "day" : 0,
        "week" : 1,
        "month" : 2,
        "season" : 3
    };


var EDrawMode =
    {
        "day" : 0,
        "month" : 1,
        "year" : 2,
        "years10" : 3,
        "years100" : 4,
        "years1000" : 5
    };

var EDrawColor =
    {
        0 : "#0080c8",
        1 : "#ff0000",
        2 : "#64c800",
        3 : "#880088",
        4 : "#ff8000"
    };

var g_drawMode = EDrawMode.years100;

var C_Eventbar_DayUnit = 0.005;
var C_Eventbar_Height = 6;
var C_Eventbar_VSpace = 20;

var C_EventNameFont = "12px 微软雅黑";
var C_RulerFont = "12pt Calibri";
var C_FontHeight = 8;
var C_RulerHeight = 15;
var C_FontOffset = 3;

var origX = 200;
var C_RulerRows = 2;
var origY = C_RulerHeight * C_RulerRows;

var lastX = -1;
var lastY = 0;

var g_events = [];
var canvas = null;
var context = null;
var mouseIsDown = false;

var drawType = EDrawUnit.day;


var g_drawYIdx = 0;
var g_boundYMax = 0;
var g_boundXMin = 0;
var g_boundXMax = 0;

var g_eventFirst = null;
var g_eventLast = null;

Date.prototype.addDays = function(days) {
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
}

function onOrigi()
{
    origX = canvas.width / 3 * 1;
    origY = C_Eventbar_Height * 4 + C_RulerHeight * C_RulerRows;
    redraw();
}


function onZoomIn()
{
    //alert("onZoomIn");
    C_Eventbar_DayUnit = Math.min(C_Eventbar_DayUnit + 5, 30);
    redraw();
}

function onZoomOut()
{
    //alert("onZoomOut");
    C_Eventbar_DayUnit = Math.max(C_Eventbar_DayUnit - 5, 2);
    redraw();
}

function onZoom1000Years()
{
    g_drawMode = EDrawMode.years1000;
    C_Eventbar_DayUnit = 0.001;
    C_Eventbar_Height = 6;
    C_Eventbar_VSpace = 20;

    C_EventNameFont = "12px 微软雅黑";
    C_RulerFont = "12pt Calibri";
    C_FontHeight = 8;
    C_RulerHeight = 15;
    C_FontOffset = 4;

    redraw();
}

function onZoom100Years()
{
    g_drawMode = EDrawMode.years100;
    C_Eventbar_DayUnit = 0.005;
    C_Eventbar_Height = 6;
    C_Eventbar_VSpace = 20;

    C_EventNameFont = "12px 微软雅黑";
    C_RulerFont = "12pt Calibri";
    C_FontHeight = 8;
    C_RulerHeight = 15;
    C_FontOffset = 3;

    redraw();
}

function onZoom10Years()
{
    g_drawMode = EDrawMode.years10;
    C_Eventbar_DayUnit = 0.01;
    C_Eventbar_Height = 6;
    C_Eventbar_VSpace = 20;

    C_EventNameFont = "12px 微软雅黑";
    C_RulerFont = "8pt Calibri";
    C_FontHeight = 8;
    C_RulerHeight = 15;
    C_FontOffset = 3;

    redraw();
}

function onZoomYear()
{
    g_drawMode = EDrawMode.year;
    C_Eventbar_DayUnit = 0.1;
    C_Eventbar_Height = 8;
    C_Eventbar_VSpace = 20;

    C_EventNameFont = "12px 微软雅黑";
    C_RulerFont = "8pt Calibri";
    C_FontHeight = 8;
    C_RulerHeight = 15;

    redraw();
}

function onZoomMonth()
{
    g_drawMode = EDrawMode.month;
    C_Eventbar_DayUnit = 1;
    C_Eventbar_Height = 8;
    C_Eventbar_VSpace = 20;

    C_EventNameFont = "12px 微软雅黑";
    C_RulerFont = "8pt Calibri";
    C_FontHeight = 6;
    C_RulerHeight = 15;
    redraw();
}

function onZoomDay()
{
    g_drawMode = EDrawMode.day;
    C_Eventbar_DayUnit = 8;
    C_Eventbar_Height = 8;
    C_Eventbar_VSpace = 20;

    C_EventNameFont = "12px 微软雅黑";
    C_RulerFont = "8pt Calibri";
    C_FontHeight = 8;
    C_RulerHeight = 15;
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
    //ctx.translate(0, 0);
    ctx.fillRect(-canvas.width, 0, window.innerWidth * 2, ruleHeight * C_RulerRows);

    ctx.translate(origX, 0);

    context.font = C_RulerFont;
    context.fillStyle = 'black';

    if(g_eventFirst == null)
    {
        console.log("g_eventFirst is null");
        return;
    }

    if(g_eventLast == null)
    {
        console.log("g_eventLast is null");
        return;
    }

    var beginDate = convertStringToDate(g_eventFirst.datebegin);
    var endDate =  convertStringToDate(g_eventFirst.dateend);



    var beginYear = new Date(beginDate).getFullYear();
    var endYear = new Date(endDate).getFullYear();

    // draw year
    if(g_drawMode <= EDrawMode.year) {
        for (var y = beginYear; y < endYear + 10000; ++y) {
            var curDrawYear = new Date(y, 0, 0);
            var nextDrawYear = new Date(y + 1, 0, 0);

            var xy = dateToCoord(curDrawYear, 0, 0);
            var xy2 = dateToCoord(nextDrawYear, 0, 0);

            ctx.rect(xy[0], xy[1], xy2[0] - xy[0], ruleHeight);
            var ymStr = y.toString();// + "-" + (nextMonth + 1).toString();// + "月";// + "   cur:" + curMonth;
            ctx.fillText(ymStr, xy[0] + 8, xy[1] + C_FontHeight + 4);
        }
    }

    if(g_drawMode == EDrawMode.years10) {
        for (var y = beginYear; y < endYear + 10000; y += 10) {
            var curDrawYear = new Date(y, 0, 0);
            var nextDrawYear = new Date(y + 10, 0, 0);

            var xy = dateToCoord(curDrawYear, 0, 0);
            var xy2 = dateToCoord(nextDrawYear, 0, 0);

            ctx.rect(xy[0], xy[1], xy2[0] - xy[0], ruleHeight);
            var ymStr = y.toString();// + "-" + (nextMonth + 1).toString();// + "月";// + "   cur:" + curMonth;
            ctx.fillText(ymStr, xy[0] + 8, xy[1] + C_FontHeight + 4);
        }
    }

    if(g_drawMode == EDrawMode.years100) {
        for (var y = beginYear; y < endYear + 10000; y += 100) {
            var curDrawYear = new Date(y, 0, 0);
            var nextDrawYear = new Date(y + 100, 0, 0);

            var xy = dateToCoord(curDrawYear, 0, 0);
            var xy2 = dateToCoord(nextDrawYear, 0, 0);

            ctx.rect(xy[0], xy[1], xy2[0] - xy[0], ruleHeight);
            var ymStr = y.toString();// + "-" + (nextMonth + 1).toString();// + "月";// + "   cur:" + curMonth;
            ctx.fillText(ymStr, xy[0] + 8, xy[1] + C_FontHeight + 4);
        }
    }

    if(g_drawMode == EDrawMode.years1000) {
        for (var y = beginYear; y < endYear + 10000; y += 1000) {
            var curDrawYear = new Date(y, 0, 0);
            var nextDrawYear = new Date(y + 1000, 0, 0);

            var xy = dateToCoord(curDrawYear, 0, 0);
            var xy2 = dateToCoord(nextDrawYear, 0, 0);

            ctx.rect(xy[0], xy[1], xy2[0] - xy[0], ruleHeight);
            var ymStr = y.toString();// + "-" + (nextMonth + 1).toString();// + "月";// + "   cur:" + curMonth;
            ctx.fillText(ymStr, xy[0] + 8, xy[1] + C_FontHeight + 4);
        }
    }

    /*
    var cur = beginDate;
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
        var xy = dateToCoord(nextDate, 0);
        var xy2 = dateToCoord(nextDate2, 0);

        ctx.rect(xy[0], xy[1], xy2[0] - xy[0], ruleHeight);
        var ymStr = nextYear.toString() + "-" + (nextMonth + 1).toString();// + "月";// + "   cur:" + curMonth;
        ctx.fillText(ymStr, xy[0] + 8, xy[1] + C_FontHeight);
    }

    var weekendClr = "#aaaaaa";
    var weekdayClr = "#eeeeee";

    for(var i = -31 * 5; i < 31 * 7; ++i)
    {
        var nextDate = new Date(cur).addDays(i);
        var xy = dateToCoord(nextDate, 0);

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
        ctx.fillRect(xy[0], xy[1] + ruleHeight * 1, C_Eventbar_DayUnit, ruleHeight);
        ctx.rect(xy[0], xy[1] + ruleHeight * 1, C_Eventbar_DayUnit, ruleHeight);
        var dayStr = nextDate.getDate().toString();
        var dayStrWidth = ctx.measureText(dayStr).width;

        ctx.fillStyle = "black";
        ctx.fillText(dayStr, xy[0] + (C_Eventbar_DayUnit - dayStrWidth) / 2, xy[1] + C_FontHeight + ruleHeight * 1);
    }

    //ctx.fillText("ssssssssssssssssssssssssssss", 100, 10);
*/

    var weekendClr = "#aaaaaa";
    var weekdayClr = "#eeeeee";
    ctx.stroke();

    ctx.translate(-origX, 0);
    ctx.fillStyle = weekdayClr;
    ctx.fillRect(canvas.width - ruleHeight/2, ruleHeight * C_RulerRows, ruleHeight/2, g_boundYMax);

    ctx.fillStyle = weekendClr;
    var scrollPos = Math.abs(origY) / Math.abs(g_boundYMax) * canvas.height;
    ctx.fillRect(canvas.width - ruleHeight/2, ruleHeight * C_RulerRows + scrollPos, ruleHeight / 2, ruleHeight / 2);

    //*
    //if(mouseIsDown)
    {
        ctx.fillStyle = weekdayClr;
        ctx.fillRect(0, canvas.height - ruleHeight/2, canvas.width, ruleHeight / 2);

        ctx.fillStyle = weekendClr;
        var scrollPosX =((origX) / (g_boundXMax - g_boundXMin)) * canvas.width;
        ctx.fillRect(scrollPosX, canvas.height - ruleHeight / 2, C_Eventbar_DayUnit, ruleHeight / 2);

        console.warn("ox:%d oy:%d pos:%f minX:%d maxX:%d maxY:%d", origX, origY, scrollPosX, g_boundXMin, g_boundXMax, g_boundYMax);
    }
    //*/

    ctx.restore();
}

function drawMiniMap()
{

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
$(function()
{
    //alert("init()");
    //console.log($("#timelineEvents"));
    //drawEvents($("#timelineEvents"));
    canvas = document.getElementById("projectCanvas");
    context = canvas.getContext('2d');

    canvas.onmousemove = onCanvasMouseMove;
    canvas.onmousedown = onCanvasMouseDown;
    canvas.onmouseup = onCanvasMouseUp;
    canvas.onmouseout = onCanvasMouseOut;
    canvas.onmousewheel = onCanvasMouseWheel;

    window.addEventListener("resize", resizeCanvas, false);
    resizeCanvas();
    //onZoom100Years();
    onOrigi();
});
//*/

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
    return;

    origX = Math.max(g_boundXMin, origX);
    origX = Math.min(g_boundXMax - C_Eventbar_Height, origX);

    origY = Math.min(C_Eventbar_Height * 4 + 3 * C_RulerHeight, origY);
    //origY = Math.max(-(C_Eventbar_Height + C_Eventbar_VSpace) * (g_drawYIdx), origY);
    origY = Math.max(-g_boundYMax, origY);

    //console.warn("ox:%d oy:%d minX:%d maxX:%d maxY:%d", origX, origY, g_boundXMin, g_boundXMax, g_boundYMax);
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
    context.clearRect(0, 0, canvas.width, canvas.height);

    //context.translate(0, 0);
    drawBg();

    //context.translate(0, 0);
    drawEventsGlobal();

    drawRuler();

    //context.translate(0, 0);
    drawCurrentDayLine();

    drawMiniMap();
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


    var timelineEvents = ajaxGetEvents();
    //alert("init:" + timelineEvents);

});


function ajaxGetEvents()
{
    url = createLink('timeline', 'ajaxGetTimelineEvents');
    //alert("ajaxGetEvents url:" + url);

    var timelineEvents = null;

    //*
    $.getJSON(link, function(r)
    {
        //console.log("ajaxGetEvents:", JSON.stringify(r));
        g_events = r;


        if(r.length > 0)
        {
            g_eventFirst = r[0];
            g_eventLast = r[0];
        }

        if(r.length > 1)
        {
            g_eventLast = r[r.length - 1];
        }

        console.log("begin:%s end:%s", g_eventFirst.datebegin, g_eventFirst.dateend);

        //canvas.height = ((g_events.length + 6) * (C_Eventbar_Height + C_Eventbar_VSpace));
        //canvas.width = 4096;
        //canvas.height = 2080;
        //console.log("==== w:" + document.width + " H:" + canvas.height + " timelineEvents:" + g_events.length);

        redraw();


        //console.warn("blueprint timelineEvents:", g_events, g_events.length);
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
                alert("ajaxGetEvents success:" + (r));
                timelineEvents = r;
            },
            error: function(error){
                alert("ajaxGetEvents error:" + JSON.stringify(error));
            },
            complete:function()
            {
                //alert("ajaxGetEvents:complete");
            }

        });

    //*/

    return timelineEvents;
}

function drawBg()
{
    //var canvas = document.getElementById("projectCanvas");
    ctx = context;
    ctx.save();
    ctx.translate(-canvas.width, 0);
    //canvas.setWidth(document.width);
    //alert("canvas:" + canvas.id + " docW:" + document.width);
    var ctx = canvas.getContext("2d");
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.restore();
}

function drawCurrentDayLine()
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


function drawEventsGlobal()
{
    drawEvents(g_events);
}

function drawEvents(timelineEvents)
{
    //alert("drawEvents:" + timelineEvents.length);

    //console.error("task len:", timelineEvents.length);

    //var canvas = document.getElementById("projectCanvas");

    //canvas.setWidth(document.width);
    //alert("canvas:" + canvas.id + " docW:" + document.width);
    var ctx = context;//canvas.getContext("2d");

    ctx.save();
    ctx.translate(origX, origY);

    /*
    var idx = 0;
    drawEvent(ctx, "1 CRASH自动收集分析",  "2018-5-1", 24, 'done', idx++);
    drawEvent(ctx, "2 CRASH自动收集分析",  "2018-5-1", 32, 'doing', idx++);
    drawEvent(ctx, "3 CRASH自动收集分析",  "2018-5-2", 40, 'wait', idx++);
    drawEvent(ctx, "4 CRASH自动收集分析",  "2018-5-6", 48, 'wait', idx++);
    //*/

    ctx.fillStyle = C_EventColor_Completed;
    //ctx.fillText("SSSSSSSSSSSSSSSSSSS", 0, C_FontHeight);
    //ctx.moveTo(0, 1000);
    //ctx.lineTo(4000, 4000);
    //ctx.stroke();
    g_drawYIdx = 0;
    g_boundYMax = 0;
    g_boundXMin = 0;
    g_boundXMax = 0;

    if(timelineEvents != null)
    {
        for(var i = 0; i < timelineEvents.length; ++i)
        {
            //console.log("   drawtimelineEvents:", JSON.stringify(timelineEvents[i]));
            //console.log("   drawtimelineEvents:", i, timelineEvents[i].name);


            drawEvent(ctx, timelineEvents[i], timelineEvents[i].title, timelineEvents[i].datebegin, timelineEvents[i].dateend, g_drawYIdx);
            ++g_drawYIdx;

            //if(g_drawYIdx > 100)
            {
                //console.error("break for 100 timelineEvents");
                // break;
            }
            //alert("task:" + timelineEvents[i]);
            //console.log("task[" + timelineEvents[i].id + "]: " + timelineEvents[i].name + " start:" + timelineEvents[i].realStarted + " status:" + timelineEvents[i].status);
        }
    }
    ctx.stroke();
    ctx.restore();
    //alert("canvas:" + canvas.id + " docW:" + document.width);

}


//wait,doing,done,pause,cancel,closed
function drawEvent(ctx, evt, title, begindate, enddate, idx) {
    ctx.fillStyle = C_EventNameColor;
    ctx.font = C_EventNameFont;
    //ctx.fillText("auto:" + name, 10, 30);
    var cur = Date.now();

    var start = convertStringToDate(begindate);
    var end = convertStringToDate(enddate);

    //alert(start);

    var d = days_between(start, end);
    //alert("days:" + d);

    //var taskDays = Math.ceil(hours / 8);

    var xy = dateToCoord(start, idx, 0, 0);
    var xyend = dateToCoord(end, idx, 0, 0);

    /*
    if(Math.abs(xy[0] - origX) > 4000)
    {
        //alert("discard task x:", xy[0]);
        return;
    }
    */

    ctx.fillStyle = EDrawColor[evt.type];
    ctx.strokeStyle = "#000000";

    //roundRect(ctx, xy[0], xy[1], xyend[0] - xy[0], xyend[1] - xy[1] + C_Eventbar_Height, 5, true, false);
    ctx.fillRect(xy[0], xy[1], xyend[0] - xy[0], xyend[1] - xy[1] + C_Eventbar_Height);
    //ctx.rect(xy[0], xy[1], xyend[0] - xy[0], xyend[1] - xy[1] + C_Eventbar_Height);

    ctx.stroke();
    //console.warn("==== stroke");

    var allStr = "";

    ctx.strokeStyle = "#111111";
    ctx.fillStyle = C_EventNameColor;
    ctx.lineWidth = 2;
    ctx.font = C_EventNameFont;
    //var idStr = "[" + title + "]  " + name;
    var idStr = title;
    ctx.fillText(idStr, xy[0], xy[1] - C_FontHeight + C_FontOffset);

    g_boundYMax = Math.max((idx + 2) * (C_Eventbar_Height + C_Eventbar_VSpace) - canvas.height, g_boundXMax);

    //var strW = ctx.measureText(allStr).width + 8 + 50 + 50;
    g_boundXMin = Math.min(xy[0] - canvas.width, g_boundXMin);
    g_boundXMin = Math.min(xyend[0], g_boundXMin);

    g_boundXMax = Math.max(xy[0], g_boundXMax);
    g_boundXMax = Math.max(xyend[0], g_boundXMax);
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

function dateToCoord(startDate, idx)
{
    var now = Date.now();
    //new Date(now).getTime();

    var refNow = now;

    if(g_eventFirst != null)
    {
        refNow = convertStringToDate(g_eventFirst.datebegin);
    }

    var d = days_between(startDate, refNow);

    var x = d * C_Eventbar_DayUnit;
    var y = (C_Eventbar_Height + C_Eventbar_VSpace) * idx;

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