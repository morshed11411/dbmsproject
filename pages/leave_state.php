<?php
$pageTitle = "Leave State of " . date('M,Y');
;
define('BASE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/upcs/');

require_once(BASE_DIR . 'includes/header.php');
require_once(BASE_DIR . 'includes/leave_controller.php');
require_once(BASE_DIR . 'includes/disposal_controller.php');

$company = getAllCompanyData($conn);
$companies = $company;

$result = getLeaveCountsByDateRange($conn, $companies, $startDate, $endDate);

?>
<div class="card-body">
    <!-- Welcome message and date -->
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Leave State-
                <?php echo date('M,Y'); ?>
            </h3>
        </div>
        <div class="text-right">
            <form method="post" action="">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label class="sr-only" for="startDate">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="startDate"
                            value="<?= $startDate; ?>">
                    </div>
                    <div class="col-auto">
                        <label class="sr-only" for="endDate">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="endDate" value="<?= $endDate; ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary" name="filterBtn">Filter</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<?php include '../includes/alert.php'; ?>

<section class="content">

    <!-- Leave Status Table -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title text-white">Monthly Leave State</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center text-small table-hover" id="leavetable">
                        <thead>
                            <tr>
                                <th style="width: 10%">Date</th> <!-- Fix the th size and make it center -->
                                <?php foreach ($company as $coy): ?>
                                    <th style="width: 10%">
                                        <?php echo $coy['NAME']; ?>
                                    </th>
                                <?php endforeach; ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $date => $leaveCounts): ?>
                                <tr>
                                    <td style="width: 10%"> <!-- Fix the td size and make it center text -->
                                        <?php echo $date; ?>
                                    </td>
                                    <?php
                                    foreach ($leaveCounts as $count): ?>
                                        <td style="width: 10%">
                                            <?php echo $count; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td>
                                        <?= array_sum($leaveCounts); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- /.row -->
</section>


<?php include '../includes/footer.php'; ?>


<script>
$(document).ready(function () {
      $('#leavetable').DataTable({
        dom: 'Bfrtip',
        buttons: [
          {
            extend: 'excel',
            text: 'Excel'
          },
          {
            extend: 'pdfHtml5',
            text: 'PDF',
            orientation: 'portrait',
            pageSize: 'A4',
            customize: function (doc) {
              doc.defaultStyle.fontSize = 12;
              doc.defaultStyle.font = 'Times New Roman';
              var pageSize = doc.internal.pageSize;
              var pageWidth = pageSize.width - doc.internal.margins.left - doc.internal.margins.right;
              var table = doc.content[1].table;
              table.widths = Array(table.body[0].length + 1).join('*').split('').map(function () {
                return pageWidth / table.body[0].length;
              });
            }
          },
          {
            extend: 'print',
            text: 'Print'
          }
        ],
        searching: false,
        paging: false
      });
    });
    
</script>