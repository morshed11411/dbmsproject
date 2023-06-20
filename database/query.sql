--connection
Database connection parameters
error_reporting(0);
ini_set('display_errors', 0);
$db_user = 'UMS';
$db_password = '12345';
$db_host = 'localhost/XE';

--dashboard
// Count total number of soldiers
    SELECT COUNT(*) AS total_soldiers FROM Soldier;
 // Count number of soldiers in each company
    SELECT c.CompanyName, COUNT(s.SoldierID) AS soldiers_count FROM Company c LEFT JOIN Soldier s ON c.CompanyID = s.CompanyID GROUP BY c.CompanyName;
// Count total number of teams
        SELECT COUNT(*) AS total_teams FROM Team;
 // Fetch the leave count from the TODAYS_LEAVE_VIEW
         SELECT COUNT(*) AS LeaveCount FROM TODAYS_LEAVE_VIEW;
 // Query to count the number of present soldiers
        SELECT COUNT(*) AS numPresentSoldiers FROM Soldier WHERE ISPRESENT = 1;
// Fetch soldier information for the dashboard
                   SELECT SOLDIERID,  RANK||' '||NAME AS RNAME, SERVINGSTATUS FROM SOLDIER_VIEW;
// Fetch soldiers with the specified appointments
                    SELECT r.RANK, s.NAME, a.APPOINTMENTID, a.APPOINTMENTNAME FROM Soldier s JOIN SoldierAppointment sa ON s.SOLDIERID = sa.SOLDIERID JOIN Appointments a ON sa.APPOINTMENTID = a.APPOINTMENTID  JOIN Ranks r ON s.RANKID = r.RANKID  WHERE a.APPOINTMENTNAME IN ('Duty Officer', 'Duty JCO', 'Duty NCO');
// Fetch the overweight soldier information from the view
                     SELECT SOLDIERID, RANK, NAME, COMPANYNAME, POUNDSOVERWEIGHT FROM OVERWEIGHTSOLDIERSVIEW;
// Fetch the company names and medical disposal counts from the "TODAYS_DISPOSAL_HOLDER" view
                      SELECT COMPANYNAME, COUNT(*) AS DISPOSAL_COUNT FROM TODAYS_DISPOSAL_HOLDER GROUP BY COMPANYNAME;

--EDIT_PASSWORD
 // Check if the current password matches the stored password for the soldier
            SELECT Password FROM Soldier WHERE SoldierID = :soldier_id;
 // New password and confirm password match
            UPDATE Soldier SET Password = :new_password WHERE SoldierID = :soldier_id;
                                                


--add_soldier
// Fetch data for the rank table
    SELECT RANKID, RANK FROM Ranks;
// Fetch data for the trade table
    SELECT TRADEID, TRADE FROM TRADE;

    SELECT COMPANYID, COMPANYNAME FROM Company;

// Prepare the INSERT statement for Soldier table
INSERT INTO Soldier (SoldierID, Name, RankID, TradeID, CompanyID, Gender, Religion, DateOfBirth, DateOfEnroll, BloodGroup, MaritalStatus, Village, Thana, District, Height, Weight, LivingStatus) 
VALUES (:soldier_id, :name, :rank, :trade, :company, :gender, :religion, TO_DATE(:date_of_birth, 'YYYY-MM-DD'), TO_DATE(:date_of_joining, 'YYYY-MM-DD'), :blood_group, :marital_status, :village, :thana, :district, :height, :weight, :living_status)";
INSERT INTO ContactNumber (SoldierID, ContactNumber) VALUES (:soldier_id, :contact_number)";
// Prepare the INSERT statement for ContactNumber table
INSERT INTO ContactNumber (SoldierID, ContactNumber) VALUES (:soldier_id, :contact_number);


// Fetch soldier data for the given soldier ID
    SELECT s.SOLDIERID, s.NAME, s.RANKID, s.TRADEID, s.COMPANYID, s.GENDER, s.RELIGION, 
    TO_CHAR(s.DATEOFBIRTH, 'YYYY-MM-DD') AS DATEOFBIRTH, TO_CHAR(s.DATEOFENROLL, 'YYYY-MM-DD') AS DATEOFENROLL, 
    s.BLOODGROUP, s.MARITALSTATUS, s.VILLAGE, s.THANA, s.DISTRICT, s.HEIGHT, s.WEIGHT, s.LIVINGSTATUS,
    r.RANK, t.TRADE, c.COMPANYNAME
    FROM Soldier s
    JOIN Ranks r ON s.RANKID = r.RANKID
    JOIN Trade t ON s.TRADEID = t.TRADEID
    JOIN Company c ON s.COMPANYID = c.COMPANYID
    WHERE s.SOLDIERID = :soldier_id;

 // Prepare the UPDATE statement
        UPDATE Soldier SET  NAME = :name, RANKID = :rank, TRADEID = :trade, COMPANYID = :company,  GENDER = :gender, RELIGION = :religion,  DATEOFBIRTH = TO_DATE(:date_of_birth, 'YYYY-MM-DD'),  DATEOFENROLL = TO_DATE(:date_of_joining, 'YYYY-MM-DD'), BLOODGROUP = :blood_group, MARITALSTATUS = :marital_status,  VILLAGE = :village,  THANA = :thana, DISTRICT = :district,  HEIGHT = :height,  WEIGHT = :weight, LIVINGSTATUS = :living_status WHERE SOLDIERID = :soldier_id;
        SELECT SOLDIERID, NAME FROM Soldier WHERE COMPANYID = :company_id

        INSERT INTO LeaveModule (LeaveID, SoldierID, LeaveType, LeaveStartDate, LeaveEndDate)
        VALUES (:leave_id, :soldier_id, :leave_type, TO_DATE(:leave_start_date, 'YYYY-MM-DD'), TO_DATE(:leave_end_date, 'YYYY-MM-DD'));
              
        SELECT * FROM LeaveModule ORDER BY LEAVEID;
--INSERT_SOLDIER
// Prepare the INSERT statement
            INSERT INTO Soldier (SoldierID, Password, Name, MaritalStatus, BloodGroup, Weight, Height, Religion, Age, DateOfBirth, Gender, LivingStatus, Village, Thana, District, DateOfEnroll, TemporaryCommand, ERE, ServingStatus, TradeID, RankID, CompanyID, CarrierPlanID) 
                      VALUES (:soldier_id, :password, :name, :marital_status, :blood_group, :weight, :height, :religion, :age, TO_DATE(:date_of_birth, 'YYYY-MM-DD'), :gender, :living_status, :village, :thana, :district, TO_DATE(:date_of_enroll, 'YYYY-MM-DD'), :temporary_command, :ere, :serving_status, :trade_id, :rank_id, :company_id, :carrier_plan_id);
--EDIT_SOLDIER
 // Fetch data for the trade table
        SELECT TRADEID, TRADE FROM TRADE;
// Fetch data for the rank table
        SELECT RANKID, RANK FROM Ranks;
        SELECT COMPANYID, COMPANYNAME FROM Company;
 // Fetch soldier data for the given soldier ID
        SELECT s.SOLDIERID, s.NAME, s.RANKID, s.TRADEID, s.COMPANYID, s.GENDER, s.RELIGION,  TO_CHAR(s.DATEOFBIRTH, 'YYYY-MM-DD') AS DATEOFBIRTH, TO_CHAR(s.DATEOFENROLL, 'YYYY-MM-DD') AS DATEOFENROLL, s.BLOODGROUP, s.MARITALSTATUS, s.VILLAGE, s.THANA, s.DISTRICT, s.HEIGHT, s.WEIGHT, s.LIVINGSTATUS, r.RANK, t.TRADE, c.COMPANYNAME FROM Soldier s JOIN Ranks r ON s.RANKID = r.RANKID JOIN Trade t ON s.TRADEID = t.TRADEID  JOIN Company c ON s.COMPANYID = c.COMPANYID  WHERE s.SOLDIERID = :soldier_id
 // Prepare the UPDATE statement
        UPDATE Soldier SET  NAME = :name,  RANKID = :rank,  TRADEID = :trade,  COMPANYID = :company,  GENDER = :gender,   RELIGION = :religion,  DATEOFBIRTH = TO_DATE(:date_of_birth, 'YYYY-MM-DD'), DATEOFENROLL = TO_DATE(:date_of_joining, 'YYYY-MM-DD'),  BLOODGROUP = :blood_group, MARITALSTATUS = :marital_status,  VILLAGE = :village,  THANA = :thana, DISTRICT = :district,  HEIGHT = :height, WEIGHT = :weight,   LIVINGSTATUS = :living_status WHERE SOLDIERID = :soldier_id
 // Fetch data for the trade table
        SELECT TRADEID, TRADE FROM TRADE;  
  
  
  --delete_soldier              
// Delete Leave Information
            DELETE FROM LeaveModule WHERE LeaveID = :leave_id;
--ABSENT_SOLDIER
SELECT * FROM AbsentSoldiersView;
--index.php
 // Prepare the SQL statement
            SELECT * FROM soldier WHERE soldierid = :username AND password = :password;
                    

--MANAGE_RANK
// Retrieve the list of ranks and the count of soldiers in each rank
            SELECT r.Rank, COUNT(*) AS SoldierCount FROM Ranks r JOIN Soldier s ON r.RankID = s.RankID GROUP BY r.Rank


--duty
 // Prepare the INSERT statement
            INSERT into duty (dt, doffr, djco, dnco, dclk, drnr) values (TO_DATE(:dt, 'YYYY-MM-DD'), :doffr, :djco, :dnco, :dclk, :drnr);


--ADD_COY
// Include the conn.php file for database connection
                                            
                SELECT * FROM Company;
// Include the conn.php file for database connection
                                    
                INSERT INTO Company (CompanyID, CompanyName) VALUES (:company_id, :company_name);
--edit_coy
            SELECT * FROM Company WHERE CompanyID = :company_id;
--update_coy
    // Perform the update operation
    UPDATE Company SET COMPANYNAME = :company_name WHERE COMPANYID = :company_id;
--delete coy
    DELETE FROM COMPANY WHERE COMPANYID = :company_id;
 --coy_details
    SELECT Soldier.SOLDIERID, Ranks.RANK, Trade.TRADE, Soldier.NAME, LISTAGG(Appointments.APPOINTMENTNAME, ', ') WITHIN GROUP (ORDER BY Appointments.APPOINTMENTNAME) AS APPOINTMENTS FROM Soldier LEFT JOIN Ranks ON Soldier.RANKID = Ranks.RANKID  LEFT JOIN Trade ON Soldier.TRADEID = Trade.TRADEID  LEFT JOIN SoldierAppointment ON Soldier.SOLDIERID = SoldierAppointment.SOLDIERID LEFT JOIN Appointments ON SoldierAppointment.APPOINTMENTID = Appointments.APPOINTMENTID WHERE Soldier.COMPANYID = :company_id GROUP BY Soldier.SOLDIERID, Ranks.RANK, Trade.TRADE, Soldier.NAME ORDER BY Soldier.SOLDIERID;
--approve_lve
            SELECT * FROM LeaveModule ORDER BY LEAVEID;
            DELETE FROM LeaveModule WHERE LeaveID = :leave_id;
            INSERT INTO LeaveModule (LeaveID, SoldierID, LeaveType, LeaveStartDate, LeaveEndDate)
            VALUES (:leave_id, :soldier_id, :leave_type, TO_DATE(:leave_start_date, 'YYYY-MM-DD'), TO_DATE(:leave_end_date, 'YYYY-MM-DD'));
--leave_details
SELECT SoldierID, Name, Rank, Trade, CompanyName, LeaveType, LeaveStartDate, LeaveEndDate, RemainingLeave FROM todays_leave_view;
--leave percentage
 // Retrieve all company names
SELECT CompanyName FROM Company;
 // Get total manpower for the company
SELECT COUNT(*) AS TotalManpower FROM Soldier WHERE CompanyID = (SELECT CompanyID FROM Company WHERE CompanyName = :company_name);
  // Get total on leave count for the company
    SELECT COUNT(*) AS OnLeaveCount FROM Soldier s JOIN Company c ON s.CompanyID = c.CompanyID JOIN LeaveModule l ON s.SoldierID = l.SoldierID WHERE l.LeaveStartDate <= TRUNC(SYSDATE) AND l.LeaveEndDate >= TRUNC(SYSDATE) AND c.CompanyName = :company_name;
--archive_med_info
SELECT SOLDIERID, NAME, RANK, TRADE, COMPANYNAME, DISPOSALTYPE, STARTDATE, ENDDATE FROM todays_disposal_holder;

--assign_appt
// Delete existing appointments for the selected soldiers
    DELETE FROM SoldierAppointment WHERE AppointmentID = :appointment_id;
    // Assign the appointment to the selected soldiers
            INSERT INTO SoldierAppointment (SoldierID, AppointmentID) VALUES (:soldier_id, :appointment_id);

--edit_appt
    SELECT * FROM APPOINTMENTS WHERE APPOINTMENTID = :appointment_id;
--remove_appt
     // Delete the appointment from the SoldierAppointment table
        DELETE FROM SoldierAppointment WHERE SoldierID = :soldier_id AND AppointmentID = :appointment_id;
--update_appt
          // Update the appointment details in the database
        UPDATE APPOINTMENTS SET APPOINTMENTNAME = :appointment_name WHERE APPOINTMENTID = :appointment_id;
--MANAGE_APPT
INSERT INTO APPOINTMENTS (APPOINTMENTID, APPOINTMENTNAME) VALUES (:appointment_id, :appointment_name);
--update_auth
 // Check if an authorization record already exists for the company, trade, and rank
        SELECT COUNT(*) AS count FROM Authorization WHERE CompanyID = :companyID AND TradeID = :tradeID AND RankID = :rankID;
 // Update the existing authorization record
        UPDATE Authorization SET Manpower = :manpower WHERE CompanyID = :companyID AND TradeID = :tradeID AND RankID = :rankID;
// Insert a new authorization record
        INSERT INTO Authorization (CompanyID, TradeID, RankID, Manpower) VALUES (:companyID, :tradeID, :rankID, :manpower);
--manage_auth
 // Fetch the list of companies
        SELECT * FROM COMPANY;
// Fetch the list of companies and their authorized manpower
         SELECT c.COMPANYNAME, a.MANPOWER FROM COMPANY c LEFT JOIN AUTHORIZATION a ON c.COMPANYID = a.COMPANYID;
 // Check if the authorization already exists for the company
        SELECT * FROM AUTHORIZATION WHERE COMPANYID = :COMPANYID;
 // Authorization already exists, update the existing record
    UPDATE AUTHORIZATION SET MANPOWER = :MANPOWER WHERE COMPANYID = :COMPANYID;
 // Authorization doesn't exist, insert a new record'
        INSERT INTO AUTHORIZATION (MANPOWER, COMPANYID) VALUES (:MANPOWER, :COMPANYID);
--MEDICAL_INFO
// Generate the Medical ID automatically as an integer
            INSERT INTO MedicalInfo (SoldierID, DisposalType, StartDate, EndDate, Reason) VALUES (:soldier_id, :disposal_type, TO_DATE(:start_date, 'YYYY-MM-DD'), TO_DATE(:end_date, 'YYYY-MM-DD'), :reason);
--get_teams
SELECT SOLDIERID, NAME FROM Soldier WHERE COMPANYID = :company_id;
UPDATE Team SET EndDate = SYSDATE WHERE TeamID = :team_id;
--team_details
// Get Team Details
    SELECT * FROM Team WHERE TeamID = :team_id;
// Get All Soldiers
        SELECT * FROM Soldier;
// Get Soldiers Assigned to the Team
        SELECT s.*, c.CompanyName, t.TeamName FROM Soldier s JOIN Company c ON s.CompanyID = c.CompanyID LEFT JOIN SoldierTeam st ON s.SoldierID = st.SoldierID LEFT JOIN Team t ON st.TeamID = t.TeamID  WHERE st.TeamID = :team_id
 // Clear existing assigned soldiers for the team 
        DeLETE FROM SoldierTeam WHERE TeamID = :team_id;

    // Assign selected soldiers to the team
        INSERT INTO SoldierTeam (SoldierID, TeamID) VALUES (:soldier_id, :team_id);
--END _TEAM
UPDATE Team SET EndDate = SYSDATE WHERE TeamID = :team_id;
--edit_trade
SELECT * FROM TRADE WHERE TRADEID = :trade_id;
--update_trade
    UPDATE TRADE SET TRADE = :trade_name WHERE TRADEID = :trade_id ;

 --MANAGE TRADE
    INSERT INTO TRADE (TRADEID, TRADE) VALUES (:trade_id, :trade_name);
--DELETE_TRADE
DELETE FROM TRADE WHERE TRADEID = :trade_id;
--generate_parade_state
 // Fetch the parade state data from the view
        SELECT * FROM parade_state_view;

--MANAGE_ACCESS
// Perform the necessary database operations
        UPDATE Soldier SET AccessRole = :access_role WHERE SoldierID = :soldier_id;
// Update the password in the Soldier table
        UPDATE Soldier SET Password = :password WHERE SoldierID = :soldier_id;
// Include the conn.php file for database connection
                                            
        SELECT SoldierID, Name, CompanyID FROM Soldier WHERE AccessRole='admin';
// For example, you can execute an UPDATE query on the Soldier table to remove the access role
                                        
        UPDATE Soldier SET AccessRole = NULL, PASSWORD = NULL   WHERE SoldierID = :soldier_id;

 //  Update the password in the Soldier table
        UPDATE Soldier SET Password = :password WHERE SoldierID = :soldier_id;
--BASIC_TRAINING
SELECT * FROM BasicTraining;
// Check if the soldier ID exists
        SELECT COUNT(*) AS COUNT FROM Soldier WHERE SoldierID = :soldierID;
// Get the training ID based on the training type
            SELECT TrainingID FROM BasicTraining WHERE TrainingName = :trainingType;
// Check if the soldier basic training record already exists
        SELECT COUNT(*) AS COUNT FROM SoldierBasicTraining WHERE SoldierID = :soldierID AND TrainingID = :trainingID;
 // Update the soldier basic training record
            UPDATE SoldierBasicTraining SET Remark = :remark, TrainingDate = TO_DATE(:trainingDate, 'YYYY-MM-DD') WHERE SoldierID = :soldierID AND TrainingID = :trainingID;
// Insert a new soldier basic training record
            INSERT INTO SoldierBasicTraining (TrainingID, SoldierID, Remark, TrainingDate) VALUES (:trainingID, :soldierID, :remark, TO_DATE(:trainingDate, 'YYYY-MM-DD'));
            
SELECT s.SoldierID, s.Name, bt.TrainingName, sbt.Remark FROM Soldier s JOIN SoldierBasicTraining sbt ON s.SoldierID = sbt.SoldierID JOIN BasicTraining bt ON sbt.TrainingID = bt.TrainingID
--MANAGE_BASIC_TRG
SELECT * FROM BasicTraining; 
--MANAGE_CARRIER_PLAN
// Retrieve the selected company name
        SELECT COMPANYNAME FROM COMPANY WHERE COMPANYID = :companyID;
        SELECT s.SOLDIERID AS ID, s.NAME, r.RANK, cp.* FROM SOLDIER s JOIN RANKS r ON s.RANKID = r.RANKID LEFT JOIN CarrierPlan cp ON s.SOLDIERID = cp.SOLDIERID WHERE s.COMPANYID = :companyID AND r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO')
// Check if career plan already exists for the soldier
        SELECT COUNT(*) FROM CarrierPlan WHERE SOLDIERID = :soldierID;
 // Perform update operation
            UPDATE CarrierPlan SET FIRSTCYCLE = :firstCycle, SECONDCYCLE = :secondCycle, THIRDCYCLE = :thirdCycle, FOURTHCYCLE = :fourthCycle  WHERE SOLDIERID = :soldierID;
--MANAGE_PLAN
// Fetch the company information for each cycle
        SELECT c.COMPANYID, c.COMPANYNAME,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') THEN s.SOLDIERID END) AS TOTAL_SOLDIERS,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FIRSTCYCLE = 'Admin' THEN s.SOLDIERID END) AS FIRST_CYCLE_ADMIN,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FIRSTCYCLE = 'Leave' THEN s.SOLDIERID END) AS FIRST_CYCLE_PLEAVE,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FIRSTCYCLE = 'Training' THEN s.SOLDIERID END) AS FIRST_CYCLE_TRAINING,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.SECONDCYCLE = 'Admin' THEN s.SOLDIERID END) AS SECOND_CYCLE_ADMIN,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.SECONDCYCLE = 'Leave' THEN s.SOLDIERID END) AS SECOND_CYCLE_PLEAVE,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.SECONDCYCLE = 'Training' THEN s.SOLDIERID END) AS SECOND_CYCLE_TRAINING,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.THIRDCYCLE = 'Admin' THEN s.SOLDIERID END) AS THIRD_CYCLE_ADMIN,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.THIRDCYCLE = 'Leave' THEN s.SOLDIERID END) AS THIRD_CYCLE_PLEAVE,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.THIRDCYCLE = 'Training' THEN s.SOLDIERID END) AS THIRD_CYCLE_TRAINING,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FOURTHCYCLE = 'Admin' THEN s.SOLDIERID END) AS FOURTH_CYCLE_ADMIN,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FOURTHCYCLE = 'Leave' THEN s.SOLDIERID END) AS FOURTH_CYCLE_PLEAVE,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FOURTHCYCLE = 'Training' THEN s.SOLDIERID END) AS FOURTH_CYCLE_TRAINING
        FROM Company c
        LEFT JOIN Soldier s ON c.COMPANYID = s.COMPANYID
        LEFT JOIN Ranks r ON s.RANKID = r.RANKID
        LEFT JOIN CarrierPlan cp ON s.SOLDIERID = cp.SOLDIERID
    WHERE r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO')
    GROUP BY c.COMPANYID, c.COMPANYNAME;

--PROFILE
    SELECT s.SoldierID, s.Name, s.MaritalStatus, s.BloodGroup, s.Weight, s.Height, s.Religion,
              s.Age, s.DateOfBirth, s.Gender, s.LivingStatus, s.Village, s.Thana, s.District,
              s.DateOfEnroll, s.TemporaryCommand, s.ERE, s.ServingStatus, t.Trade, r.Rank, c.CompanyName
              FROM Soldier s
              INNER JOIN Trade t ON s.TradeID = t.TradeID
              INNER JOIN Ranks r ON s.RankID = r.RankID
              INNER JOIN Company c ON s.CompanyID = c.CompanyID
              WHERE s.SoldierID = :soldier_id;