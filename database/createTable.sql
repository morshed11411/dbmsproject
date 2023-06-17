-- Drop dependent tables
DROP TABLE SoldierTeam;
DROP TABLE SoldierAppointment;
DROP TABLE SoldierBasicTraining;
DROP TABLE AdvanceTraining;
DROP TABLE LeaveModule;
DROP TABLE Appointments;
DROP TABLE Ranks;
DROP TABLE MedicalInfo;
DROP TABLE Company;
DROP TABLE Authorization;
DROP TABLE Trade;
DROP TABLE ContactNumber;
DROP TABLE Team;
DROP TABLE Punishment;
DROP TABLE Soldier;

-- Create updated tables
CREATE TABLE Soldier (
  SOLDIERID NUMBER(7) PRIMARY KEY,
  PASSWORD NUMBER(8),
  NAME VARCHAR2(255),
  MARITALSTATUS VARCHAR2(255),
  BLOODGROUP VARCHAR2(255),
  WEIGHT NUMBER,
  HEIGHT NUMBER,
  RELIGION VARCHAR2(255),
  AGE NUMBER,
  DATEOFBIRTH DATE,
  GENDER VARCHAR2(255),
  LIVINGSTATUS VARCHAR2(255),
  VILLAGE VARCHAR2(255),
  THANA VARCHAR2(255),
  DISTRICT VARCHAR2(255),
  DATEOFENROLL DATE,
  TEMPORARYCOMMAND VARCHAR2(255),
  ERE VARCHAR2(255),
  SERVINGSTATUS VARCHAR2(255),
  TRADEID NUMBER,
  RANKID NUMBER,
  COMPANYID NUMBER,
  ISPRESENT VARCHAR2(10),
  AccessRole VARCHAR2(255),
  FOREIGN KEY (TRADEID) REFERENCES Trade(TRADEID),
  FOREIGN KEY (RANKID) REFERENCES Ranks(RANKID),
  FOREIGN KEY (COMPANYID) REFERENCES Company(COMPANYID)
);


CREATE TABLE Punishment (
  PUNISHMENTID NUMBER PRIMARY KEY,
  SOLDIERID NUMBER,
  PUNISHMENT VARCHAR2(255),
  REASON VARCHAR2(255),
  PUNISHMENTDATE DATE,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE Team (
  TEAMID NUMBER PRIMARY KEY,
  TEAMNAME VARCHAR2(255),
  STARTDATE DATE,
  ENDDATE DATE,
  TEAMOIC VARCHAR2(255)
);

CREATE TABLE ContactNumber (
  SOLDIERID NUMBER,
  CONTACTNUMBER NUMBER(11),
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE Trade (
  TRADEID NUMBER PRIMARY KEY,
  TRADE VARCHAR2(255)
);

CREATE TABLE Authorization (
  AUTHORIZATIONID NUMBER PRIMARY KEY,
  AUTHORIZATIONTYPE VARCHAR2(255),
  MANPOWER NUMBER,
  COMPANYID NUMBER,
  FOREIGN KEY (COMPANYID) REFERENCES COMPANY(COMPANYID)
);

CREATE TABLE Company (
  COMPANYID NUMBER PRIMARY KEY,
  COMPANYNAME VARCHAR2(255)
);

CREATE TABLE MedicalInfo (
  MEDICALID NUMBER PRIMARY KEY,
  SOLDIERID NUMBER,
  DISPOSALTYPE VARCHAR2(255),
  STARTDATE DATE,
  ENDDATE DATE,
  REASON VARCHAR2(255),
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE Ranks (
  RANKID NUMBER PRIMARY KEY,
  RANK VARCHAR2(255)
);

CREATE TABLE Appointments (
  APPOINTMENTID NUMBER PRIMARY KEY,
  APPOINTMENTNAME VARCHAR2(255)
);

CREATE TABLE LeaveModule (
  LEAVEID NUMBER PRIMARY KEY,
  SOLDIERID NUMBER,
  LEAVETYPE VARCHAR2(255),
  LEAVESTARTDATE DATE,
  LEAVEENDDATE DATE,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE CarrierPlan (
  SOLDIERID NUMBER(7) PRIMARY KEY,
  FIRSTCYCLE VARCHAR2(255),
  SECONDCYCLE VARCHAR2(255),
  THIRDCYCLE VARCHAR2(255),
  FOURTHCYCLE VARCHAR2(255),
  FOREIGN KEY (SOLDIERID) REFERENCES Soldier(SOLDIERID)
);

CREATE TABLE AdvanceTraining (
  CADREID NUMBER PRIMARY KEY,
  NAME VARCHAR2(255),
  TRAININGSTARTDATE DATE,
  TRAININGENDDATE DATE,
  TRAININGOIC VARCHAR2(255),
  INSTRUCTOR VARCHAR2(255),
  RESULT VARCHAR2(255)
);

CREATE TABLE SoldierBasicTraining (
  TRAININGID NUMBER,
  TRAININGDATE DATE,
  SOLDIERID NUMBER,
  REMARK VARCHAR2(255),
  FOREIGN KEY (TRAININGID) REFERENCES BASICTRAINING(TRAININGID),
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE BasicTraining (
  TRAININGID NUMBER PRIMARY KEY,
  TRAININGCODE VARCHAR2(255),
  TRAININGNAME VARCHAR2(255)
);

CREATE TABLE SoldierAppointment (
  SOLDIERID NUMBER,
  APPOINTMENTID NUMBER,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (APPOINTMENTID) REFERENCES APPOINTMENTS(APPOINTMENTID)
);

CREATE TABLE SoldierTeam (
  SOLDIERID NUMBER,
  TEAMID NUMBER,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (TEAMID) REFERENCES APPOINTMENTS(APPOINTMENTID)
);



