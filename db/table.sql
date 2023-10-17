CREATE TABLE TRADE (
  TRADEID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  TRADE VARCHAR2(255)
);

CREATE TABLE RANKS (
  RANKID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  RANK VARCHAR2(255)
);

CREATE TABLE COMPANY (
  COMPANYID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  COMPANYNAME VARCHAR2(255)
);

CREATE TABLE TEMPORARYCOMMAND (
  COMDID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  COMDNAME VARCHAR2(255)
);

CREATE TABLE ERE (
  EREID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  ERENAME VARCHAR2(255)
);

CREATE TABLE SERVINGSTATUS (
  STATUSID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  SERVINGTYPE VARCHAR2(255)
);


CREATE TABLE SOLDIER (
  SOLDIERID NUMBER(7) PRIMARY KEY,
  NAME VARCHAR2(255),
  MARITALSTATUS VARCHAR2(255),
  BLOODGROUP VARCHAR2(255),
  WEIGHT NUMBER,
  HEIGHT NUMBER,
  RELIGION VARCHAR2(255),
  DATEOFBIRTH DATE,
  GENDER VARCHAR2(255),
  LIVINGSTATUS VARCHAR2(255),
  VILLAGE VARCHAR2(255),
  THANA VARCHAR2(255),
  DISTRICT VARCHAR2(255),
  DATEOFENROLL DATE,
  PARENTUNIT VARCHAR2(255),
  MISSION VARCHAR2(255),
  MEDCATEGORY VARCHAR2(1),
  NOOFCHILDREN NUMBER(2),
  DATERETIREMENT DATE,
  TRADEID NUMBER,
  RANKID NUMBER,
  COMPANYID NUMBER,
  PERSONALCONTACT NUMBER(11),
  EMERGENCYCONTACT NUMBER(11),
  FOREIGN KEY (TRADEID) REFERENCES TRADE(TRADEID),
  FOREIGN KEY (RANKID) REFERENCES RANKS(RANKID),
  FOREIGN KEY (COMPANYID) REFERENCES COMPANY(COMPANYID)
);




CREATE TABLE LOGIN (
  SOLDIERID NUMBER(7),
  PASSWORD NUMBER(8),
  ROLE VARCHAR2(255) DEFAULT 'soldier',
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

-- Change the data type of the PASSWORD column to VARCHAR2 to store hashed passwords
ALTER TABLE LOGIN
MODIFY PASSWORD VARCHAR2(255);

-- Add new columns
ALTER TABLE LOGIN
ADD (
  FAILED_LOGIN_ATTEMPTS NUMBER(3) DEFAULT 0,
  LAST_LOGIN_TIME TIMESTAMP,
  STATUS NUMBER(1) DEFAULT 0 -- 0 for active, 1 for disabled
);


CREATE TABLE SOLDIERERE (
  ID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  SOLDIER_ID NUMBER,
  ERE_ID NUMBER,
  START_DATE DATE,
  END_DATE DATE,
  FOREIGN KEY (SOLDIER_ID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (ERE_ID) REFERENCES ERE(EREID)
);

CREATE TABLE SOLDIERSTATUS (
  SOLDIER_ID NUMBER,
  STATUSID NUMBER,
  FOREIGN KEY (SOLDIER_ID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (STATUSID) REFERENCES SERVINGSTATUS(STATUSID)
);

CREATE TABLE SOLDIERTEMPCOMD (
  ID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  SOLDIER_ID NUMBER,
  TEMPCOMD_ID NUMBER,
  START_DATE DATE,
  END_DATE DATE,
  FOREIGN KEY (SOLDIER_ID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (TEMPCOMD_ID) REFERENCES TEMPORARYCOMMAND(COMDID)
);


CREATE TABLE PUNISHMENT (
  PUNISHMENTID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  SOLDIERID NUMBER,
  PUNISHMENT VARCHAR2(255),
  REASON VARCHAR2(255),
  PUNISHMENTDATE DATE,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE TEAM (
  TEAMID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  TEAMNAME VARCHAR2(255),
  STARTDATE DATE,
  ENDDATE DATE,
  TEAMOIC VARCHAR2(255)
);

CREATE TABLE AUTHORIZATION (
  MANPOWER NUMBER,
  COMPANYID NUMBER,
  FOREIGN KEY (COMPANYID) REFERENCES COMPANY(COMPANYID)
);

CREATE TABLE MEDICALINFO (
  MEDICALID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  SOLDIERID NUMBER,
  DISPOSALTYPE VARCHAR2(255),
  STARTDATE DATE,
  ENDDATE DATE,
  REASON VARCHAR2(255),
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE APPOINTMENTS (
  APPOINTMENTID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  APPOINTMENTNAME VARCHAR2(255)
);

CREATE TABLE LEAVEMODULE (
  LEAVEID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  SOLDIERID NUMBER,
  LEAVEYPE VARCHAR2(255),
  LEAVESTARTDATE DATE,
  LEAVEENDDATE DATE,
  OUTTIME TIMESTAMP,
  INTIME TIMESTAMP,
  STATUS VARCHAR2(50),
  REQUESTDATE DATE;
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);


CREATE TABLE CARRIERPLAN (
  SOLDIERID NUMBER PRIMARY KEY,
  FIRSTCYCLE VARCHAR2(255),
  SECONDCYCLE VARCHAR2(255),
  THIRDCYCLE VARCHAR2(255),
  FOURTHCYCLE VARCHAR2(255),
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE ADVANCETRAINING (
  CADREID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  NAME VARCHAR2(255),
  TRAININGSTARTDATE DATE,
  TRAININGENDDATE DATE,
  TRAININGOIC VARCHAR2(255),
  INSTRUCTOR VARCHAR2(255)
);

CREATE TABLE BASICTRAINING (
  TRAININGID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  TRAININGCODE VARCHAR2(255),
  TRAININGNAME VARCHAR2(255)
);

CREATE TABLE SOLDIERBASICTRAINING (
  TRAININGID NUMBER,
  TRAININGDATE DATE,
  SOLDIERID NUMBER,
  REMARK VARCHAR2(255),
  FOREIGN KEY (TRAININGID) REFERENCES BASICTRAINING(TRAININGID),
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE SOLDIERADVANCEDTRAINING (
  CADREID NUMBER,
  SOLDIERID NUMBER,
  REMARK VARCHAR2(255),
  FOREIGN KEY (CADREID) REFERENCES ADVANCETRAINING(CADREID),
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE SOLDIERAPPOINTMENT (
  ID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  SOLDIER_ID NUMBER,
  APPOINTMENT_ID NUMBER,
  START_DATE DATE,
  END_DATE DATE,
  FOREIGN KEY (SOLDIER_ID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (APPOINTMENT_ID) REFERENCES APPOINTMENTS(APPOINTMENTID)
);

CREATE TABLE SOLDIERTEAM (
  SOLDIERID NUMBER,
  TEAMID NUMBER,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (TEAMID) REFERENCES TEAM(TEAMID)
);


CREATE TABLE UPLOADED_IMAGES (
  SOLDIER_ID NUMBER PRIMARY KEY,
  PASSPORT_PICTURE_PATH VARCHAR2(255),
  NID_PICTURE_PATH VARCHAR2(255),
  COMBO_ID_PICTURE_PATH VARCHAR2(255),
  FOREIGN KEY (SOLDIER_ID) REFERENCES SOLDIER(SOLDIERID)
);


CREATE TABLE SOLDIERTEAM (
  SOLDIERID NUMBER,
  TEAMID NUMBER,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (TEAMID) REFERENCES TEAM(TEAMID)
);


ALTER TABLE UPLOADED_IMAGES ADD (SIGNATURE_PATH VARCHAR2(255));

CREATE TABLE events (
  event_id NUMBER GENERATED ALWAYS AS IDENTITY,
  event_date DATE,
  event_name VARCHAR2(100),
  event_time VARCHAR2(20),
  event_location VARCHAR2(100),
  attendees_responsibility VARCHAR2(100),
  remarks VARCHAR2(200),
  CONSTRAINT events_pk PRIMARY KEY (event_id)
);
