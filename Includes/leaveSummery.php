

    <div class='card'>
        <div class='card-header'>
            <h5 class='card-title'>Leave Summary</h5>
        </div>
        <div class='card-body'>
            <table class='table table-bordered'>
                <thead>
                    <tr>
                        <?php foreach ($leaveTypes as $leaveType) : ?>
                            <th class='text-center'><?php echo $leaveType; ?></th>
                        <?php endforeach; ?>
                        <th class='text-center'>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php foreach ($leaveTypes as $leaveType) : ?>
                            <td class='text-center'><?php echo $totalDays[$leaveType]; ?> Days</td>
                        <?php endforeach; ?>
                        <td class='text-center'><?php echo array_sum($totalDays); ?> Days</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
