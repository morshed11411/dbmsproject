CREATE TABLE TRAININGEVENT (
  EVENTID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  TRGID NUMBER NOT NULL,
  EVENTDATE DATE NOT NULL,
  CREATIONTIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  BOARDPRESIDENTID NUMBER NOT NULL,
  AUTHORITYLETTERNO VARCHAR2(255) NOT NULL,
  BOARDNO NUMBER NOT NULL,
  EVENTNAME VARCHAR2(255),
  STATUS VARCHAR2(50) CHECK (STATUS IN ('Ongoing', 'Terminated')),
  
  CONSTRAINT FK_TRAININGTYPE FOREIGN KEY (TRGID) REFERENCES BASICTRAINING(TRGID),
  CONSTRAINT FK_BOARDPRESIDENT FOREIGN KEY (BOARDPRESIDENTID) REFERENCES SOLDIER(SOLDIERID)
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