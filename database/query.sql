$sql = "INSERT INTO Soldier (SoldierID, Password, Name, MaritalStatus, BloodGroup, Weight, Height, Religion, Age, DateOfBirth, Gender, LivingStatus, Village, Thana, District, DateOfEnroll, TemporaryCommand, ERE, ServingStatus, TradeID, RankID, CompanyID, CarrierPlanID)
        VALUES (:soldierID, :password, :name, :maritalStatus, :bloodGroup, :weight, :height, :religion, :age, :dateOfBirth, :gender, :livingStatus, :village, :thana, :district, :dateOfEnroll, :temporaryCommand, :ere, :servingStatus, :tradeID, :rankID, :companyID, :carrierPlanID)";


$sql = "UPDATE Soldier SET
            Password = :password,
            Name = :name,
            MaritalStatus = :maritalStatus,
            BloodGroup = :bloodGroup,
            Weight = :weight,
            Height = :height,
            Religion = :religion,
            Age = :age,
            DateOfBirth = :dateOfBirth,
            Gender = :gender,
            LivingStatus = :livingStatus,
            Village = :village,
            Thana = :thana,
            District = :district,
            DateOfEnroll = :dateOfEnroll,
            TemporaryCommand = :temporaryCommand,
            ERE = :ere,
            ServingStatus = :servingStatus,
            TradeID = :tradeID,
            RankID = :rankID,
            CompanyID = :companyID,
            CarrierPlanID = :carrierPlanID
        WHERE SoldierID = :soldierID";


$sql = "DELETE FROM Soldier WHERE SoldierID = :soldierID";


// Insert into Company
$sqlCompany = "INSERT INTO Company (CompanyID, CompanyName) VALUES (:companyID, :companyName)";

// Insert into Trade
$sqlTrade = "INSERT INTO Trade (TradeID, Trade) VALUES (:tradeID, :trade)";

// Insert into Ranks
$sqlRanks = "INSERT INTO Ranks (RankID, Rank) VALUES (:rankID, :rank)";

// Insert into Appointments
$sqlAppointments = "INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (:appointmentID, :appointmentName)";


// Update Company
$sqlUpdateCompany = "UPDATE Company SET CompanyName = :companyName WHERE CompanyID = :companyID";

// Update Trade
$sqlUpdateTrade = "UPDATE Trade SET Trade = :trade WHERE TradeID = :tradeID";

// Update Ranks
$sqlUpdateRanks = "UPDATE Ranks SET Rank = :rank WHERE RankID = :rankID";

// Update Appointments
$sqlUpdateAppointments = "UPDATE Appointments SET AppointmentName = :appointmentName WHERE AppointmentID = :appointmentID";

// Delete from Company
$sqlDeleteCompany = "DELETE FROM Company WHERE CompanyID = :companyID";

// Delete from Trade
$sqlDeleteTrade = "DELETE FROM Trade WHERE TradeID = :tradeID";

// Delete from Ranks
$sqlDeleteRanks = "DELETE FROM Ranks WHERE RankID = :rankID";

// Delete from Appointments
$sqlDeleteAppointments = "DELETE FROM Appointments WHERE AppointmentID = :appointmentID";

