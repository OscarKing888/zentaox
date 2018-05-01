
const C_TaskColor_Pending = "#00AAFF";
const C_TaskColor_Completed = "#00AA00";
const C_TaskColor_Warning = "#FFAA00";
const C_TaskColor_Delay = "#AA0000";

const C_TaskNameColor = "#000000";
const C_TaskNameFont = "13px 新宋体";

const C_Taskbar_Height = 10;
const C_Taskbar_DayUnit = 10;

$(function()
{
    var canvas = document.getElementById("projectCanvas");
    //canvas.setWidth(document.width);
    //alert("canvas:" + canvas.id + " docW:" + document.width);
    var ctx = canvas.getContext("2d");
    ctx.rect(0, 0, 100, 200);
    drawTask(ctx, "CRASH自动收集分析",  "2018-04-14", 8);
    //ctx.fill();
});

function drawTask(ctx, name, startDate, hours)
{
    //ctx.fillColor = "rgba(1,0,0,0.5)";
    ctx.fillStyle = C_TaskColor_Completed;
    ctx.fillRect(0, 0, hours / 8 * C_Taskbar_DayUnit, 10);

    ctx.fillStyle = C_TaskColor_Delay;
    ctx.fillRect(0, 40, 100, 10);

    ctx.fillStyle = C_TaskColor_Pending;
    ctx.fillRect(0, 80, 100, 10);

    ctx.fillStyle = C_TaskColor_Warning;
    ctx.fillRect(0, 120, 100, 10);

    ctx.fillStyle = C_TaskNameColor;
    ctx.font = C_TaskNameFont;
    ctx.fillText(name, 10, 30);
}