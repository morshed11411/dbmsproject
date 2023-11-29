<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-info">
                <h5 class="card-title text-white"><i class="fas fa-users"></i> সৈনিক তথ্য</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">পদবি</th>
                                <th scope="col">মোট</th>
                                <th scope="col">উপস্থিত</th>
                                <th scope="col">অনুপস্থিত</th>
                                <th scope="col">ছুটিতে</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="fas fa-users"></i> সৈনিক</td>
                                <td><?php echo count($postedTotal); ?></td>
                                <td><?php echo count($allPresent); ?></td>
                                <td><?php echo count($allAbsent); ?></td>
                                <td><?php echo count($allOnLeave); ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-user-tie"></i> অফিসার</td>
                                <td><?php echo count($allOfficer); ?></td>
                                <td><?php echo count($officerPresent); ?></td>
                                <td><?php echo count($officerAbsent); ?></td>
                                <td><?php echo count($officerOnLeave); ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-user-secret"></i> জেসিও</td>
                                <td><?php echo count($allJCO); ?></td>
                                <td><?php echo count($jcoPresent); ?></td>
                                <td><?php echo count($jcoAbsent); ?></td>
                                <td><?php echo count($jcoOnLeave); ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-user"></i> অন্যান্য পদবী</td>
                                <td><?php echo count($allORS); ?></td>
                                <td><?php echo count($orsPresent); ?></td>
                                <td><?php echo count($orsAbsent); ?></td>
                                <td><?php echo count($orsOnLeave); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
