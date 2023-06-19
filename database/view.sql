CREATE OR REPLACE VIEW manage_company_view AS
SELECT
  C.COMPANYID,
  C.COMPANYNAME,
  S.NAME AS "Coy Comd",
  (SELECT COUNT(*) FROM SOLDIER WHERE COMPANYID = C.COMPANYID) AS TotalManpower
FROM
  COMPANY C
  JOIN SOLDIER S ON C.COMPANYID = S.COMPANYID
  JOIN SOLDIERAPPOINTMENT SA ON S.SOLDIERID = SA.SOLDIERID
  JOIN APPOINTMENTS A ON SA.APPOINTMENTID = A.APPOINTMENTID
WHERE
  A.APPOINTMENTNAME = 'COY COMD';



CREATE OR REPLACE VIEW todays_leave_view AS
SELECT s.SoldierID, s.Name, r.Rank, t.Trade, c.CompanyName, l.LeaveType, l.LeaveStartDate, l.LeaveEndDate,
       (l.LeaveEndDate - TRUNC(SYSDATE)) AS RemainingLeave
FROM Soldier s
JOIN Ranks r ON s.RankID = r.RankID
JOIN Trade t ON s.TradeID = t.TradeID
JOIN Company c ON s.CompanyID = c.CompanyID
JOIN LeaveModule l ON s.SoldierID = l.SoldierID
WHERE l.LeaveStartDate <= TRUNC(SYSDATE)
AND l.LeaveEndDate >= TRUNC(SYSDATE);



CREATE OR REPLACE VIEW AbsentSoldiersView AS
SELECT s.SoldierID, s.Name, r.Rank, c.CompanyName, 'ERE' AS Reason 
FROM Soldier s
JOIN Ranks r ON s.RankID = r.RankID
JOIN Company c ON s.CompanyID = c.CompanyID
WHERE s.ERE = 'Yes'
UNION
SELECT s.SoldierID, s.Name, r.Rank, c.CompanyName, 'Temporary Command' AS Reason 
FROM Soldier s
JOIN Ranks r ON s.RankID = r.RankID
JOIN Company c ON s.CompanyID = c.CompanyID
WHERE s.TemporaryCommand = 'Yes'
UNION
SELECT s.SoldierID, s.Name, r.Rank, c.CompanyName, 'AWOL' AS Reason 
FROM Soldier s
JOIN Ranks r ON s.RankID = r.RankID
JOIN Company c ON s.CompanyID = c.CompanyID
WHERE s.ServingStatus = 'AWOL'
UNION
SELECT tl.SoldierID, s.Name, r.Rank, c.CompanyName, tl.LeaveType AS Reason
FROM Todays_Leave_View tl
JOIN Soldier s ON tl.SoldierID = s.SoldierID
JOIN Ranks r ON s.RankID = r.RankID
JOIN Company c ON s.CompanyID = c.CompanyID
UNION
SELECT m.SoldierID, s.Name, r.Rank, c.CompanyName, m.DisposalType AS Reason
FROM TODAYS_DISPOSAL_HOLDER m
JOIN Soldier s ON m.SoldierID = s.SoldierID
JOIN Ranks r ON s.RankID = r.RankID
JOIN Company c ON s.CompanyID = c.CompanyID;




CREATE OR REPLACE VIEW parade_state_view AS
SELECT
  c.COMPANYNAME,
  a.MANPOWER AS "Auth",
  COUNT(s.SOLDIERID) AS "Granted",
  COUNT(l.SOLDIERID) AS "Leave",
  COUNT(CASE WHEN s.ISPRESENT = 0 THEN 1 END) AS "Absent",
  COUNT(s.SOLDIERID) - COUNT(CASE WHEN s.ISPRESENT = 0 THEN 1 END) AS "Present",
  COUNT(d.SOLDIERID) AS "MedicalDisposal"
FROM
  COMPANY c
LEFT JOIN
  AUTHORIZATION a ON c.COMPANYID = a.COMPANYID
LEFT JOIN
  SOLDIER s ON c.COMPANYID = s.COMPANYID
LEFT JOIN
  TODAYS_LEAVE_VIEW l ON s.SOLDIERID = l.SOLDIERID
LEFT JOIN
  TODAYS_DISPOSAL_HOLDER d ON s.SOLDIERID = d.SOLDIERID
GROUP BY
  c.COMPANYNAME, a.MANPOWER;



CREATE OR REPLACE VIEW SOLDIER_VIEW AS 
SELECT
  s.SOLDIERID,  s.PASSWORD,  s.NAME,  s.MARITALSTATUS,  s.BLOODGROUP,  s.WEIGHT,  s.HEIGHT,  s.RELIGION,  s.AGE,  s.DATEOFBIRTH,  s.GENDER,  s.LIVINGSTATUS,  s.VILLAGE,  s.THANA,  s.DISTRICT,  s.DATEOFENROLL,  s.TEMPORARYCOMMAND,  s.ERE,  s.SERVINGSTATUS,  s.TRADEID,  t.TRADE,  s.RANKID,  r.RANK,  s.COMPANYID,  c.COMPANYNAME,  s.ISPRESENT,  s.AccessRoleFROM Soldier s
LEFT JOIN Trade t ON s.TRADEID = t.TRADEID
LEFT JOIN Ranks r ON s.RANKID = r.RANKID
LEFT JOIN Company c ON s.COMPANYID = c.COMPANYID;


  CREATE OR REPLACE FORCE VIEW "UMS"."OVERWEIGHTSOLDIERSVIEW" ("SOLDIERID", "RANK", "TRADE", "NAME", "COMPANYNAME", "POUNDSOVERWEIGHT") AS 
  SELECT s.SoldierID, r.Rank, t.Trade, s.Name, c.CompanyName,
    CASE
        WHEN ((s.Weight * 0.453592) / ((s.Height / 100) * (s.Height / 100))) > 25 THEN ROUND(((s.Weight * 0.453592) / ((s.Height / 100) * (s.Height / 100))) - 25)
        ELSE 0
    END AS PoundsOverweight
FROM Soldier s
JOIN Ranks r ON s.RankID = r.RankID
JOIN Trade t ON s.TradeID = t.TradeID
JOIN Company c ON s.CompanyID = c.CompanyID
WHERE ((s.Weight * 0.453592) / ((s.Height / 100) * (s.Height / 100))) > 25;




  CREATE OR REPLACE FORCE VIEW "UMS"."TODAYS_DISPOSAL_HOLDER" ("SOLDIERID", "NAME", "RANK", "TRADE", "COMPANYNAME", "DISPOSALTYPE", "STARTDATE", "ENDDATE") AS 
  SELECT s.SOLDIERID, s.NAME, r.RANK, t.TRADE, c.COMPANYNAME, m.DISPOSALTYPE, m.STARTDATE, m.ENDDATE
FROM Soldier s
JOIN Ranks r ON s.RankID = r.RankID
JOIN Trade t ON s.TradeID = t.TradeID
JOIN Company c ON s.CompanyID = c.CompanyID
JOIN MedicalInfo m ON s.SoldierID = m.SoldierID
WHERE TRUNC(m.STARTDATE) <= TRUNC(SYSDATE)
  AND TRUNC(m.ENDDATE) >= TRUNC(SYSDATE);

