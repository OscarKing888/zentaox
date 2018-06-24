<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog w-800px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><i
                            class="icon-file-text"></i> <?php echo $lang->task->importTaskFromMSProject ?></h4>
            </div>
            <div class="modal-body">
                <?php echo html::textarea('pasteText', '', "class='form-control mgb-10' rows='10' placeholder='$lang->pasteTextInfo'") ?>
                <?php echo html::submitButton() ?>
            </div>
        </div>
    </div>
</div>
<script>

    $('#myModal').modal('show');

    $("button[data-toggle='myModal']").click(function () {
        $('#myModal').modal('show')
    })

    function getSelectOptionsValue(selectObj, strValue)
    {
        var opts = selectObj.find("option");
        var count = $(opts).length;
        var outVal = 0;
        //console.log("%s want:%s cnt:%d", selectObj.attr("name"), strValue, count);
        for(var i = 0; i < count; ++i)
        {
            var k = $(selectObj).get(0).options[i].value;
            var v = $(selectObj).get(0).options[i].text;
            //console.log("   %s => %s", k, v);
            if(v == strValue)
            {
                outVal = k;
                //console.log("   outVal = ", outVal);
                break;
            }
        }

        return outVal;
    }

    $("#myModal button[type='submit']").click(function () {
        var pasteText = $('#myModal #pasteText').val();

        $('#myModal').modal('hide')
        $('#myModal #pasteText').val('');

        var dataList = pasteText.split("\n");
        var index = 0;
        for (i in dataList) {

            if(dataList[i] == "")
            {
                continue;
            }

            var data = dataList[i].replace(/(^\s*)|(\s*$)/g, "");
            data = data.replace(/ 工时/g, "");
            data = data.replace(/年/g, "-");
            data = data.replace(/月/g, "-");
            data = data.replace(/日/g, "");
            data = data.replace("[子]", "");
            //data = data.replace(/\//g, "-");

            var fields = data.split("\t");
            //console.log("oscar: paste data count:" + fields.length + " dat: " + data);

            // 最后一个空行
            //if (fields.length == 1){ //continue;}

            if (fields.length != 11)
            {
                console.error("从Microsoft Project粘贴的列数只能是11列， 列数：" + fields.length);
                continue;
            }

            const idx_id = 0;
            const idx_project = 1;
            const idx_module = 2;
            const idx_story = 3;
            const idx_dept = 4;
            const idx_name = 5;
            const idx_pri = 6;
            const idx_estimate = 7;
            const idx_estStarted = 8;
            const idx_deadline = 9;
            const idx_assignedTo = 10;


            var v = 0;

            //if (typeof(mainField) == 'undefined') mainField = 'id';

            cloneTr = $('#trTemp tbody').html();
            cloneTr = cloneTr.replace(/%s/g, index);
            $('form tbody tr').eq(-1).before(cloneTr);

            idField = $('form tbody tr').eq(index).find("input[id*='id']");
            $(idField).val(fields[idx_id]);
            //console.log("idx:" + index + " idField:" + idField.attr('name') + " val:" + fields[idx_id] + " story:" + fields[idx_story]);

            idField = $('form tbody tr').eq(index).find("select[id*='project']");
            v = getSelectOptionsValue(idField, fields[idx_project]);
            $(idField).val(v);

            idField = $('form tbody tr').eq(index).find("select[id*='module']");
            v = getSelectOptionsValue(idField, fields[idx_module]);
            $(idField).val(v);

            idField = $('form tbody tr').eq(index).find("select[id*='story']");
            v = getSelectOptionsValue(idField, fields[idx_story]);
            $(idField).val(v);

            idField = $('form tbody tr').eq(index).find("select[id*='dept']");
            v = getSelectOptionsValue(idField, fields[idx_dept]);
            $(idField).val(v);

            idField = $('form tbody tr').eq(index).find("input[id*='name']");
            $(idField).val(fields[idx_name]);

            idField = $('form tbody tr').eq(index).find("select[id*='pri']");
            v = getSelectOptionsValue(idField, fields[idx_pri]);
            $(idField).val(v);

            idField = $('form tbody tr').eq(index).find("input[id*='estimate']");
            $(idField).val(fields[idx_estimate]);

            idField = $('form tbody tr').eq(index).find("input[id*='estStarted']");
            var datStr = fields[idx_estStarted];
            datStr = datStr.replace(/\//g, "-");
            var datStrLst = datStr.split('-');
            var newDatStr = datStrLst[0] + "-" + ("0" + datStrLst[1]).slice(-2) + "-" + ("0" + datStrLst[2]).slice(-2);
            //console.warn("date -> local", datStr, newDatStr);
            $(idField).val(newDatStr);

            idField = $('form tbody tr').eq(index).find("input[id*='deadline']");
            var datStr = fields[idx_deadline];
            datStr = datStr.replace(/\//g, "-");
            $(idField).val(datStr);

            idField = $('form tbody tr').eq(index).find("select[id*='assignedTo']");
            v = getSelectOptionsValue(idField, fields[idx_assignedTo]);
            $(idField).val(v);

            index++;

            /*
            while (true) {
                var title = $('form tbody tr').eq(index).find("input[id*='" + mainField + "']");
                if ($(title).size() == 0) {
                    if (index == 0) break;
                    cloneTr = $('#trTemp tbody').html();
                    cloneTr = cloneTr.replace(/%s/g, index);
                    $('form tbody tr').eq(index - 1).after(cloneTr);
                    $('form tbody tr').eq(index).find('td:first').html(index + 1);
                    $('form tbody tr').eq(index - 1).find('td').each(function () {
                        if ($(this).find('div.chosen-container').size() != 0) {
                            //$('form tbody tr').eq(index).find("td").eq($(this).index()).find('select').chosen(defaultChosenOptions);
                        }
                    });

                    if(idx == 0)
                    {
                        console.log("[" + index +"]================================================");
                        console.log("cloneTr:" + cloneTr);

                        idx++
                    }

                    title = $('form tbody tr').eq(index).find("input[id*='" + mainField + "']");
                    //$('#color\\[' + index + '\\]').colorPicker();//Update color picker.
                }

                index++;

                if ($(title).val() != '') continue;
                if ($(title).val() == '') $(title).val(fields[idx_id]);
                break;
            }
            //*/
        }
    });
</script>
