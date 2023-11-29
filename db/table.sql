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


CREATE TABLE USERS (
  SOLDIERID NUMBER(7) PRIMARY KEY,
  PASSWORD VARCHAR2(255) DEFAULT '123456',
  ROLE VARCHAR2(50) DEFAULT 'soldier',
  FAILED_LOGIN_ATTEMPTS NUMBER(3) DEFAULT 0,
  LAST_LOGIN_TIME TIMESTAMP,
  STATUS NUMBER(1) DEFAULT 0,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);

INSERT INTO USERS (SOLDIERID, ROLE)
SELECT SOLDIERID, 'soldier' AS ROLE
FROM SOLDIER;


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


-- Create MEDICALINFO table
CREATE TABLE MEDICALINFO (
  MEDICALID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  SOLDIERID NUMBER,
  DISPOSALID NUMBER,
  STARTDATE DATE CHECK (STARTDATE IS NOT NULL),
  ENDDATE DATE CHECK (ENDDATE IS NULL OR ENDDATE >= STARTDATE),
  REASON VARCHAR2(255),
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (DISPOSALID) REFERENCES DISPOSALTYPE(DISPOSALID)
);

CREATE TABLE DISPOSALTYPE (
  DISPOSALID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  DISPOSALTYPE VARCHAR2(255),
  SHOW_DISPOSAL NUMBER(1) DEFAULT 0
);

CREATE TABLE APPOINTMENTS (
  APPOINTMENTID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  APPOINTMENTNAME VARCHAR2(255)
);

CREATE TABLE LEAVEMODULE (
  LEAVEID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  SOLDIERID NUMBER,
  LEAVETYPEID NUMBER, -- Reference to the LEAVETYPE table
  LEAVESTARTDATE DATE CHECK (LEAVESTARTDATE IS NOT NULL),
  LEAVEENDDATE DATE CHECK (LEAVEENDDATE IS NOT NULL),
  OUTTIME TIMESTAMP,
  INTIME TIMESTAMP,
  STATUS VARCHAR2(50),
  REQUESTDATE DATE,
  AUTHBY NUMBER,
  ONLEAVE NUMBER(1) DEFAULT 0,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (AUTHBY) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (LEAVETYPEID) REFERENCES LEAVETYPE(LEAVETYPEID), -- Added foreign key reference
  CONSTRAINT chk_leave_dates CHECK (LEAVEENDDATE IS NULL OR LEAVEENDDATE >= LEAVESTARTDATE)
);

CREATE TABLE LEAVETYPE (
  LEAVETYPEID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  LEAVETYPE VARCHAR2(255),
  SHOW_LEAVE NUMBER(1) DEFAULT 0
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



CREATE TABLE UPLOADED_IMAGES (
  SOLDIER_ID NUMBER PRIMARY KEY,
  PASSPORT_PICTURE_PATH VARCHAR2(255),
  NID_PICTURE_PATH VARCHAR2(255),
  COMBO_ID_PICTURE_PATH VARCHAR2(255),
  FOREIGN KEY (SOLDIER_ID) REFERENCES SOLDIER(SOLDIERID),
  SIGNATURE_PATH VARCHAR2(255)
);


CREATE TABLE SOLDIERTEAM (
  SOLDIERID NUMBER,
  TEAMID NUMBER,
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID),
  FOREIGN KEY (TEAMID) REFERENCES TEAM(TEAMID)
);



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



--for password reset

CREATE TABLE pwd_reset_req (
    req_id NUMBER GENERATED ALWAYS AS IDENTITY,
    username VARCHAR2(255) NOT NULL,
    req_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reset_code VARCHAR2(6),
    is_approved NUMBER(1) DEFAULT 0, -- 0 for not approved, 1 for approved
    PRIMARY KEY (req_id)
);


CREATE TABLE TRAININGEVENT (
  EVENTID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  TRGID NUMBER NOT NULL,
  EVENTDATE DATE NOT NULL,
  CREATIONTIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  BOARDPRESIDENTID NUMBER NOT NULL,
  AUTHORITYLETTERNO VARCHAR2(255) NOT NULL,
  BOARDNO NUMBER NOT NULL,
  EVENTNAME VARCHAR2(255),
  STATUS VARCHAR2(50) CHECK (STATUS IN ('Ongoing', 'Terminated', 'Locked','Unlocked','Forwarded')),
  
  CONSTRAINT FK_TRAININGTYPE FOREIGN KEY (TRGID) REFERENCES BASICTRAINING(TRGID),
  CONSTRAINT FK_BOARDPRESIDENT FOREIGN KEY (BOARDPRESIDENTID) REFERENCES SOLDIER(SOLDIERID)
);

CREATE TABLE BASICTRAINING (
  TRGID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  TRGNAME VARCHAR2(255) NOT NULL
  -- Add other relevant columns for basic training information
);



CREATE TABLE BoardMembers (
  MemberID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  EventID NUMBER NOT NULL,
  MemberName VARCHAR2(255) NOT NULL,
  -- Add other relevant columns as needed
  FOREIGN KEY (EventID) REFERENCES TrainingEvent(EventID)
);


CREATE TABLE SOLDIERTRAINING (
  SOLDIEREVENTID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  EVENTID NUMBER,
  SOLDIERID NUMBER,
  STATUS VARCHAR2(50), -- Pass or Fail
  -- Add other relevant columns as needed
  FOREIGN KEY (EVENTID) REFERENCES TRAININGEVENT(EVENTID),
  FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);


CREATE TABLE NOTIFICATIONS (
    ID NUMBER GENERATED BY DEFAULT ON NULL AS IDENTITY,
    NOTIFIED_SOLDIERID NUMBER,
    NOTIFIER_SOLDIERID NUMBER,
    NOTIFIED_GROUP VARCHAR2(50), -- New column for group information
    MESSAGE VARCHAR2(255),
    STATUS VARCHAR2(20) DEFAULT 'Unread',
    CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID),
    FOREIGN KEY (NOTIFIED_SOLDIERID) REFERENCES SOLDIER(SOLDIERID),
    FOREIGN KEY (NOTIFIER_SOLDIERID) REFERENCES SOLDIER(SOLDIERID)
);


-- Create DutyTypes Table
CREATE TABLE DUTYTYPES (
    DUTYTYPEID NUMBER GENERATED BY DEFAULT ON NULL AS IDENTITY,
    TYPENAME VARCHAR2(255) NOT NULL,
    SHOW_DUTY NUMBER(1) DEFAULT 0,
    PRIMARY KEY (DUTYTYPEID)

);

-- Create Posts Table
CREATE TABLE POSTS (
    POSTID NUMBER PRIMARY KEY,
    POSTNAME VARCHAR2(255) NOT NULL
);

-- Create Duties Table
CREATE TABLE DUTIES (
    DUTYID NUMBER GENERATED BY DEFAULT ON NULL AS IDENTITY,
    DUTYTYPEID NUMBER,
    POSTNAME VARCHAR2,
    NUMPERSONS NUMBER,
    PRIMARY KEY(DUTYID),
    FOREIGN KEY (DUTYTYPEID) REFERENCES DUTYTYPES(DUTYTYPEID)
);

-- Create SoldierDuties Table
CREATE TABLE SOLDIERDUTIES (
    SOLDIERDUTYID NUMBER PRIMARY KEY,
    SOLDIERID NUMBER,
    DUTYID NUMBER,
    DUTYDATE DATE,
    STARTTIME TIMESTAMP,
    ENDTIME TIMESTAMP,
    CONSTRAINT FK_SOLDIER FOREIGN KEY (SOLDIERID) REFERENCES SOLDIER(SOLDIERID),
    CONSTRAINT FK_DUTY FOREIGN KEY (DUTYID) REFERENCES DUTIES(DUTYID)
);
