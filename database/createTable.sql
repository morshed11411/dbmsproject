CREATE TABLE Soldier (
  SoldierID NUMBER(7) PRIMARY KEY,
  Password NUMBER(8),
  Name VARCHAR2(255),
  MaritalStatus VARCHAR2(255),
  BloodGroup VARCHAR2(255),
  Weight NUMBER,
  Height NUMBER,
  Religion VARCHAR2(255),
  Age NUMBER,
  DateOfBirth DATE,
  Gender VARCHAR2(255),
  LivingStatus VARCHAR2(255),
  Village VARCHAR2(255),
  Thana VARCHAR2(255),
  District VARCHAR2(255),
  DateOfEnroll DATE,
  TemporaryCommand VARCHAR2(255),
  ERE VARCHAR2(255),
  ServingStatus VARCHAR2(255),
  TradeID NUMBER,
  RankID NUMBER,
  CompanyID NUMBER,
  CarrierPlanID NUMBER,
  FOREIGN KEY (TradeID) REFERENCES Trade(TradeID),
  FOREIGN KEY (RankID) REFERENCES Ranks(RankID),
  FOREIGN KEY (CompanyID) REFERENCES Company(CompanyID),
  FOREIGN KEY (CarrierPlanID) REFERENCES CarrierPlan(PlanID)
);


CREATE TABLE Punishment (
  PunishmentID NUMBER PRIMARY KEY,
  SoldierID NUMBER,
  Punishment VARCHAR2(255),
  Reason VARCHAR2(255),
  PunishmentDate DATE,
  FOREIGN KEY (SoldierID) REFERENCES Soldier(SoldierID)
);


CREATE TABLE UserAccess (
  SoldierID NUMBER(7) PRIMARY KEY,
  AccessLevel VARCHAR2(20),
  FOREIGN KEY (SoldierID) REFERENCES Soldier(SoldierID)
);


CREATE TABLE Team (
  TeamID NUMBER PRIMARY KEY,
  TeamName VARCHAR2(255),
  StartDate DATE,
  EndDate DATE,
  TeamOIC VARCHAR2(255)
);

CREATE TABLE ContactNumber (
  SoldierID NUMBER,
  ContactNumber NUMBER(11),
  FOREIGN KEY (SoldierID) REFERENCES Soldier(SoldierID)
);

CREATE TABLE Trade (
  TradeID NUMBER PRIMARY KEY,
  Trade VARCHAR2(255)
);

CREATE TABLE Authorization (
  AuthorizationID NUMBER PRIMARY KEY,
  AuthorizationType VARCHAR2(255),
  Manpower NUMBER,
  CompanyID NUMBER,
  FOREIGN KEY (CompanyID) REFERENCES Company(CompanyID)
);

CREATE TABLE Company (
  CompanyID NUMBER PRIMARY KEY,
  CompanyName VARCHAR2(255)
);

CREATE TABLE MedicalInfo (
  MedicalID NUMBER PRIMARY KEY,
  SoldierID NUMBER,
  DisposalType VARCHAR2(255),
  StartDate DATE,
  EndDate DATE,
  Reason VARCHAR2(255),
  FOREIGN KEY (SoldierID) REFERENCES Soldier(SoldierID)
);

CREATE TABLE Ranks (
  RankID NUMBER PRIMARY KEY,
  Rank VARCHAR2(255)
);


CREATE TABLE Appointments (
  AppointmentID NUMBER PRIMARY KEY,
  AppointmentName VARCHAR2(255)
);


CREATE TABLE LeaveModule (
  LeaveID NUMBER PRIMARY KEY,
  SoldierID NUMBER,
  LeaveType VARCHAR2(255),
  LeaveStartDate DATE,
  LeaveEndDate DATE,
  FOREIGN KEY (SoldierID) REFERENCES Soldier(SoldierID)
);

CREATE TABLE CarrierPlan (
  PlanID NUMBER PRIMARY KEY,
  FirstCycle VARCHAR2(255),
  SecondCycle VARCHAR2(255),
  ThirdCycle VARCHAR2(255),
  FourthCycle VARCHAR2(255)
);

CREATE TABLE AdvanceTraining (
  CadreID NUMBER PRIMARY KEY,
  Name VARCHAR2(255),
  TrainingStartDate DATE,
  TrainingEndDate DATE,
  TrainingOIC VARCHAR2(255),
  Instructor VARCHAR2(255),
  Result VARCHAR2(255)
);

CREATE TABLE SoldierBasicTraining (
  TrainingID NUMBER,
  TrainingDate DATE,
  SoldierID NUMBER,
  Remark VARCHAR2(255),
  FOREIGN KEY (TrainingID) REFERENCES BasicTraining(TrainingID),
  FOREIGN KEY (SoldierID) REFERENCES Soldier(SoldierID)
);

CREATE TABLE BasicTraining (
  TrainingID NUMBER PRIMARY KEY,
  TrainingCode VARCHAR2(255),
  TrainingName VARCHAR2(255)
);


CREATE TABLE SoldierAppointment (
  SoldierID NUMBER,
  AppointmentID NUMBER,
  FOREIGN KEY (SoldierID) REFERENCES Soldier(SoldierID),
  FOREIGN KEY (AppointmentID) REFERENCES Appointments(AppointmentID)
);



CREATE TABLE SoldierTeam (
  SoldierID NUMBER,
  TeamID NUMBER,
  FOREIGN KEY (SoldierID) REFERENCES Soldier(SoldierID),
  FOREIGN KEY (TeamID) REFERENCES Appointments(AppointmentID)
);
