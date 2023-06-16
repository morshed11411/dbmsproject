INSERT INTO Soldier (SoldierID, Password, Name, TradeID, RankID, CompanyID)
VALUES ('12345', '12345', 'Admin', 1, 1, 1);

INSERT INTO Soldier (SoldierID, Password, Name, MaritalStatus, BloodGroup, Weight, Height, Religion, Age, DateOfBirth, Gender, LivingStatus, Village, Thana, District, DateOfEnroll, TemporaryCommand, ERE, ServingStatus, TradeID, RankID, CompanyID)
VALUES (11111, '12345', 'MORSHED', 'UNMARRIED', 'O+', 70, 5.7, 'ISLAM', 25, to_date('04-02-2000', 'DD-MM-YYYY'), 'MALE', 'OUTLIVING', 'DHAKA', 'MATIKATA', 'DHAKA', to_date('04-02-2021', 'DD-MM-YYYY'), 0, 0, 0, 1, 1, 1);

INSERT INTO Soldier (SoldierID, Password, Name, MaritalStatus, BloodGroup, Weight, Height, Religion, Age, DateOfBirth, Gender, LivingStatus, Village, Thana, District, DateOfEnroll, TemporaryCommand, ERE, ServingStatus, TradeID, RankID, CompanyID)
VALUES (22222, '12345', 'FERDOUSI', 'MARRIED', 'O+', 58, 5.3, 'ISLAM', 29, to_date('20-02-1995', 'DD-MM-YYYY'), 'FEMALE', 'OUTLIVING', 'DHAKA', 'MIRPUR', 'DHAKA', to_date('01-01-2015', 'DD-MM-YYYY'), 0, 0, 0, 2, 2, 2);

INSERT INTO Soldier (SoldierID, Password, Name, MaritalStatus, BloodGroup, Weight, Height, Religion, Age, DateOfBirth, Gender, LivingStatus, Village, Thana, District, DateOfEnroll, TemporaryCommand, ERE, ServingStatus, TradeID, RankID, CompanyID)
VALUES (33333, '12345', 'NOFAYER', 'MARRIED', 'A+', 68, 5.3, 'ISLAM', 27, to_date('20-07-1997', 'DD-MM-YYYY'), 'MALE', 'INTLIVING', 'GOPALGANJ', 'KOTWALI', 'GOPALGANJ', to_date('01-01-2017', 'DD-MM-YYYY'), 0, 0, 0, 3, 3, 3);

INSERT INTO Soldier (SoldierID, Password, Name, MaritalStatus, BloodGroup, Weight, Height, Religion, Age, DateOfBirth, Gender, LivingStatus, Village, Thana, District, DateOfEnroll, TemporaryCommand, ERE, ServingStatus, TradeID, RankID, CompanyID)
VALUES (44444, '12345', 'SABRINA', 'UNMARRIED', 'B+', 60, 5.4, 'ISLAM', 28, to_date('08-08-1994', 'DD-MM-YYYY'), 'FEMALE', 'INTLIVING', 'JAMALPUR', 'JAMALPUR', 'FENI', to_date('01-06-2016', 'DD-MM-YYYY'), 0, 0, 0, 4, 4, 4);

INSERT INTO Soldier (SoldierID, Password, Name, MaritalStatus, BloodGroup, Weight, Height, Religion, Age, DateOfBirth, Gender, LivingStatus, Village, Thana, District, DateOfEnroll, TemporaryCommand, ERE, ServingStatus, TradeID, RankID, CompanyID)
VALUES (55555, '12345', 'TANVEER', 'MARRIED', 'AB+', 78, 5.9, 'HINDU', 24, to_date('26-09-2001', 'DD-MM-YYYY'), 'FEMALE', 'OUTLIVING', 'JASHORE', 'KATDI', 'JASHORE', to_date('01-07-2021', 'DD-MM-YYYY'), 0, 1, 1, 5, 5, 5);


INSERT INTO Trade (TradeID, Trade) VALUES (1, 'OPERATOR');
INSERT INTO Trade (TradeID, Trade) VALUES (2, 'TECHNICIAN');
INSERT INTO Trade (TradeID, Trade) VALUES (3, 'DRIVER');
INSERT INTO Trade (TradeID, Trade) VALUES (4, 'WIREMAN SIGNAL');
INSERT INTO Trade (TradeID, Trade) VALUES (5, 'E&BR');
INSERT INTO Trade (TradeID, Trade) VALUES (6, 'COOK');
INSERT INTO Trade (TradeID, Trade) VALUES (7, 'CRYPTOGRAPHER');
INSERT INTO Trade (TradeID, Trade) VALUES (8, 'NCE');
INSERT INTO Trade (TradeID, Trade) VALUES (9, 'OPERATOR');

INSERT INTO Company (CompanyID, CompanyName) VALUES (1, 'RADIO');
INSERT INTO Company (CompanyID, CompanyName) VALUES (2, 'RR');
INSERT INTO Company (CompanyID, CompanyName) VALUES (3, 'OP');
INSERT INTO Company (CompanyID, CompanyName) VALUES (4, 'BN HQ');
INSERT INTO Company (CompanyID, CompanyName) VALUES (5, 'ARTY ABSC');
INSERT INTO Company (CompanyID, CompanyName) VALUES (6, '101 ABSC');
INSERT INTO Company (CompanyID, CompanyName) VALUES (7, '102 ABSC');
INSERT INTO Company (CompanyID, CompanyName) VALUES (8, '109 ABSC');

INSERT INTO Ranks (RankID, Rank) VALUES (1, 'LT COL');
INSERT INTO Ranks (RankID, Rank) VALUES (2, 'MAJOR');
INSERT INTO Ranks (RankID, Rank) VALUES (3, 'CAPT');
INSERT INTO Ranks (RankID, Rank) VALUES (4, 'LT');
INSERT INTO Ranks (RankID, Rank) VALUES (5, '2LT');
INSERT INTO Ranks (RankID, Rank) VALUES (6, 'MWO');
INSERT INTO Ranks (RankID, Rank) VALUES (7, 'SWO');
INSERT INTO Ranks (RankID, Rank) VALUES (8, 'WO');
INSERT INTO Ranks (RankID, Rank) VALUES (9, 'SGT');
INSERT INTO Ranks (RankID, Rank) VALUES (10, 'CPL');
INSERT INTO Ranks (RankID, Rank) VALUES (11, 'LCPL');
INSERT INTO Ranks (RankID, Rank) VALUES (12, 'SNK');


INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (1, 'CO');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (2, '2IC');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (3, 'ADJT');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (4, 'QM');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (5, 'COY COMMANDER');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (6, 'SM');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (7, 'BSM');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (8, 'RP NCO');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (9, 'CSM');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (10, 'CQMS');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (11, 'MT NCO');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (12, 'STOREMAN');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (13, 'HD CLK');
INSERT INTO Appointments (AppointmentID, AppointmentName) VALUES (14, 'ORS');


INSERT INTO SoldierAppointment (SoldierID, AppointmentID) VALUES (11111, 1);
INSERT INTO SoldierAppointment (SoldierID, AppointmentID) VALUES (22222, 2);
INSERT INTO SoldierAppointment (SoldierID, AppointmentID) VALUES (33333, 4);
INSERT INTO SoldierAppointment (SoldierID, AppointmentID) VALUES (44444, 3);


INSERT INTO BasicTraining (TrainingID, TrainingCode, TrainingName) VALUES (1, 'TRG-01', 'IPFT');
INSERT INTO BasicTraining (TrainingID, TrainingCode, TrainingName) VALUES (2, 'TRG-02', 'RET');
INSERT INTO BasicTraining (TrainingID, TrainingCode, TrainingName) VALUES (3, 'TRG-03', 'GRENADE FIRING');
INSERT INTO BasicTraining (TrainingID, TrainingCode, TrainingName) VALUES (4, 'TRG-04', 'SPEED MARCH');
INSERT INTO BasicTraining (TrainingID, TrainingCode, TrainingName) VALUES (5, 'TRG-05', 'GRENADE FIRING');
