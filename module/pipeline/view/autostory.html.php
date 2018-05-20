<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<form method='post'>
    <table class='table table-borderless table-form mw-500px' align='center'>
        <thead>
        <tr class="colhead tablesorter-headerRow" role="row">
            <th class='w-30px'><?php echo $lang->idAB; ?></th>
            <th class='w-100px'><?php echo $lang->project->name; ?></th>
            <th class='w-100px'><?php echo $lang->product->name; ?></th>
        </tr>
        </thead>
        <tbody aria-live="polite" aria-relevant="all">

        <?php
        /*
        foreach($productprojectpairs as $k => $v)
        {
            echo "pp: $k -> $v<br>";
        }

        foreach($projects as $k => $v)
        {
            echo "proj: $k -> $v<br>";
        }

        foreach($products as $k => $v)
        {
            echo "product: $k -> $v<br>";
        }
        //*/
        ?>

        <?php $i = 0; foreach($projects as $k => $v): ?>
            <tr class="text-center">
                <td><?php echo $i + 1; ?></td>
                <td><?php echo $v; ?></td>
                <td><?php

                    echo html::select("project[$i]", $projects, $k, "class='form-control hidden'");

                    //error_log("oscar: project:$k product:$productprojectpairs[$k]");

                    if(array_key_exists($k, $projProdPairs))
                    {
                        $product = $projProdPairs[$k];
                        echo html::select("product[$i]", $products, $product, "class='form-control'");
                    } else {
                        echo html::select("product[$i]", $products, 0, "class='form-control'");
                    }


                    ++$i;

                    ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="3" align="center">
                <?php echo html::submitButton('更新'); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</form>

<?php include '../../common/view/footer.html.php'; ?>
