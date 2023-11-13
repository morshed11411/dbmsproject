<?php
session_start();
include '../includes/connection.php';
include '../includes/header.php';

// Placeholder event ID for testing
$event_id = 1;

?>

    <!-- Custom Card Tabs -->
    <div class="card card-primary card-tabs mt-5">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="addSoldiersTab" data-toggle="pill" href="#addSoldiers" role="tab"
                        aria-controls="addSoldiers" aria-selected="true">Add Soldiers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="trainingResultTab" data-toggle="pill" href="#trainingResult" role="tab"
                        aria-controls="trainingResult" aria-selected="false">Training Result</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="publishResultTab" data-toggle="pill" href="#publishResult" role="tab"
                        aria-controls="publishResult" aria-selected="false">Publish Result</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-one-tabContent">
                <!-- Add Soldiers Tab -->
                <div class="tab-pane fade show active" id="addSoldiers" role="tabpanel"
                    aria-labelledby="addSoldiersTab">
                    <h4>Add Soldiers</h4>
                    <?php
// Add Soldiers Tab
echo '<div class="tab-pane fade show active" id="addSoldiers" role="tabpanel" aria-labelledby="addSoldiersTab">';
echo '<h4>Add Soldiers</h4>';

// Example form with Bootstrap Dual Listbox
echo '<form method="POST" action="">';

// Dual Listbox
echo '<div class="form-group">';
echo '<label for="soldiersList">Select Soldiers:</label>';
echo '<select id="soldiersList" name="soldiersList[]" multiple="multiple">';
// Populate with soldiers data from your database
// Example data - replace it with your actual data
$soldiers = [
    1 => 'Soldier 1',
    2 => 'Soldier 2',
    3 => 'Soldier 3',
    // Add more soldiers as needed
];
foreach ($soldiers as $soldierId => $soldierName) {
    echo '<option value="' . $soldierId . '">' . $soldierName . '</option>';
}
echo '</select>';
echo '</div>';

// Submit button
echo '<button type="submit" name="add_soldiers_submit" class="btn btn-primary">Add Soldiers</button>';

echo '</form>';
echo '</div>';
?>

                    <form method="POST" action="">
                        <!-- Form fields go here -->
                        <button type="submit" name="add_soldiers_submit" class="btn btn-primary">Add Soldiers</button>
                    </form>
                </div>

                <!-- Training Result Tab -->
                <div class="tab-pane fade" id="trainingResult" role="tabpanel"
                    aria-labelledby="trainingResultTab">
                    <h4>Training Result</h4>
                    <?php
                    // Your "Training Result" content goes here
                    // Example table:
                    ?>
                    <table class="table table-bordered">
                        <!-- Table content goes here -->
                    </table>
                </div>

                <!-- Publish Result Tab -->
                <div class="tab-pane fade" id="publishResult" role="tabpanel"
                    aria-labelledby="publishResultTab">
                    <h4>Publish Result</h4>
                    <?php
                    // Your "Publish Result" content goes here
                    // Example publish button:
                    ?>
                    <button type="button" class="btn btn-success">Publish Result</button>
                </div>
            </div>
        </div>
    </div>


<?php include '../includes/footer.php'; ?>

<script>
    // JavaScript to handle tab switching
    $(document).ready(function () {
        $('#custom-tabs-one-tab a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
