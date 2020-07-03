<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-md-12">
            <h1>Data Report</h1>
            <hr>
            <form action="<?= baseUrl('report'); ?>" class="mb-3" method="POST">
                <div class="form-row align-items-center">
                    <div class="col-sm-6 my-1">
                        <label for="inputDate">Date Range</label>
                        <div class="row">
                            <div class="col">
                                <input type="date" class="form-control" id="fromDate" name="from_date"
                                       placeholder="select from date" value="<?= getFlashData('from_date') ?>">
                            </div>
                            <div class="col-sm-1 pt-2">to</div>
                            <div class="col pl-0">
                                <input type="date" class="form-control" id="toDate" name="to_date"
                                       placeholder="select to date" value="<?= getFlashData('to_date') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 my-1">
                        <label for="inputUser">Entry User ID</label>
                        <input type="number" class="form-control" id="inputUser" name="entry_by" placeholder="enter ID" value="<?= getFlashData('entry_by') ?>">
                    </div>
                    <div class="col-auto my-1 pt-2">
                        <input type="submit" name="filter" value="Filter Now" class="btn btn-primary mt-4">
                    </div>
                </div>
            </form>
            <?php if (!empty($reportData)): ?>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <?php
                                $heads = array_keys($reportData[0]);
                                foreach ($heads as $val) {
                                    $class = $sign = '';
                                    if ($val === 'entry_at') {
                                        $class = (($order === 'entry_at') ? 'class="' . $direction : 'class="desc"') . '" data-order="entry_at"';
                                        $sign = "↑↓";
                                    } elseif ($val === 'entry_by') {
                                        $class = (($order === 'entry_by') ? 'class="' . $direction : 'class="desc"') . '" data-order="entry_by"';
                                        $sign = "↑↓";
                                    }
                                    $val = ucwords(str_replace('_', ' ', $val));
                                    echo "<th {$class}>{$val} {$sign}</th>";
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($reportData as $row) {
                                echo '<tr>';
                                foreach ($row as $key => $val) {
                                    if ($key === 'note') {
                                        $val = nl2br($val);
                                    } elseif ($key === 'items') {
                                        $val = str_replace(',', '<br>', $val);
                                    }
                                    echo "<td>{$val}</td>";
                                }
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    No reports found
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
