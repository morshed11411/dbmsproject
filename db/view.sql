CREATE OR REPLACE VIEW OFFICER AS
SELECT
  S.SOLDIERID,
  CONCAT(R.RANK, ' ', S.NAME) AS NAME,
  T.TRADE,
  C.COMPANYNAME,
  E.ERENAME AS ERE_NAME,
  TC.COMDNAME AS TEMP_COMMAND_NAME,
  SS.SERVINGTYPE AS SERVING_STATUS,
  S.MARITALSTATUS,
  S.BLOODGROUP,
  S.WEIGHT,
  S.HEIGHT,
  S.RELIGION,
  S.AGE,
  S.DATEOFBIRTH,
  S.GENDER,
  S.LIVINGSTATUS,
  S.VILLAGE,
  S.THANA,
  S.DISTRICT,
  S.DATEOFENROLL,
  S.ISPRESENT,
FROM SOLDIER S
LEFT JOIN TRADE T ON S.TRADEID = T.TRADEID
LEFT JOIN RANKS R ON S.RANKID = R.RANKID
LEFT JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
LEFT JOIN ERE E ON S.EREID = E.EREID
LEFT JOIN TEMPORARYCOMMAND TC ON S.COMDID = TC.COMDID
LEFT JOIN SERVINGSTATUS SS ON S.STATUSID = SS.STATUSID
WHERE R.RANK IN ('Lt Col', 'Maj', 'Capt', 'Lt', '2Lt');

CREATE OR REPLACE VIEW JCO AS
SELECT
  S.SOLDIERID,
  CONCAT(R.RANK, ' ', S.NAME) AS NAME,
  T.TRADE,
  C.COMPANYNAME,
  E.ERENAME AS ERE_NAME,
  TC.COMDNAME AS TEMP_COMMAND_NAME,
  SS.SERVINGTYPE AS SERVING_STATUS,
  S.MARITALSTATUS,
  S.BLOODGROUP,
  S.WEIGHT,
  S.HEIGHT,
  S.RELIGION,
  S.AGE,
  S.DATEOFBIRTH,
  S.GENDER,
  S.LIVINGSTATUS,
  S.VILLAGE,
  S.THANA,
  S.DISTRICT,
  S.DATEOFENROLL,
  S.ISPRESENT,
FROM SOLDIER S
LEFT JOIN TRADE T ON S.TRADEID = T.TRADEID
LEFT JOIN RANKS R ON S.RANKID = R.RANKID
LEFT JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
LEFT JOIN ERE E ON S.EREID = E.EREID
LEFT JOIN TEMPORARYCOMMAND TC ON S.COMDID = TC.COMDID
LEFT JOIN SERVINGSTATUS SS ON S.STATUSID = SS.STATUSID
WHERE R.RANK IN ('H Lt', 'SWO', 'WO');


CREATE OR REPLACE VIEW otherRanks AS
SELECT
  S.SOLDIERID,
  CONCAT(R.RANK, ' ', S.NAME) AS NAME,
  T.TRADE,
  C.COMPANYNAME,
  E.ERENAME AS ERE_NAME,
  TC.COMDNAME AS TEMP_COMMAND_NAME,
  SS.SERVINGTYPE AS SERVING_STATUS,
  S.MARITALSTATUS,
  S.BLOODGROUP,
  S.WEIGHT,
  S.HEIGHT,
  S.RELIGION,
  S.AGE,
  S.DATEOFBIRTH,
  S.GENDER,
  S.LIVINGSTATUS,
  S.VILLAGE,
  S.THANA,
  S.DISTRICT,
  S.DATEOFENROLL,
  S.ISPRESENT,
FROM SOLDIER S
LEFT JOIN TRADE T ON S.TRADEID = T.TRADEID
LEFT JOIN RANKS R ON S.RANKID = R.RANKID
LEFT JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
LEFT JOIN ERE E ON S.EREID = E.EREID
LEFT JOIN TEMPORARYCOMMAND TC ON S.COMDID = TC.COMDID
LEFT JOIN SERVINGSTATUS SS ON S.STATUSID = SS.STATUSID
WHERE R.RANK IN ('Sgt', 'Cpl', 'Lcpl', 'Snk', 'NCE', 'NCU');

CREATE OR REPLACE VIEW soldier_view AS
SELECT
  s.SOLDIERID,
  s.NAME,
  s.MARITALSTATUS,
  s.BLOODGROUP,
  s.WEIGHT,
  s.PARENTUNIT,
  s.noofchildren,
  s.mission,
  s.personalcontact,
  s.emergencycontact,
  s.HEIGHT,
  s.RELIGION,
  s.DATEOFBIRTH,
  s.GENDER,
  s.LIVINGSTATUS,
  s.VILLAGE,
  s.THANA,
  s.DISTRICT,
  s.DATEOFENROLL,
  r.RANK,
  t.TRADE,
  c.COMPANYNAME,
  i.PASSPORT_PICTURE_PATH AS PROFILEPICTURE,
  TRUNC(MONTHS_BETWEEN(CURRENT_DATE, s.DATEOFBIRTH) / 12) AS AGE,
  TRUNC(MONTHS_BETWEEN(CURRENT_DATE, s.DATEOFENROLL) / 12) AS SERVICEAGE,
  TRUNC(CURRENT_DATE - l.LEAVEENDDATE) AS LASTLEAVE
FROM SOLDIER s
LEFT JOIN RANKS r ON s.RANKID = r.RANKID
LEFT JOIN TRADE t ON s.TRADEID = t.TRADEID
LEFT JOIN COMPANY c ON s.COMPANYID = c.COMPANYID
LEFT JOIN (
  SELECT SOLDIERID, MAX(LEAVEENDDATE) AS LEAVEENDDATE
  FROM LEAVEMODULE
  WHERE LEAVEENDDATE <= CURRENT_DATE
  GROUP BY SOLDIERID
) l ON s.SOLDIERID = l.SOLDIERID
LEFT JOIN UPLOADED_IMAGES i ON s.SOLDIERID = i.SOLDIER_ID;




CREATE OR REPLACE VIEW soldier_view AS
SELECT
  s.SOLDIERID,
  s.NAME,
  s.MARITALSTATUS,
  s.BLOODGROUP,
  s.WEIGHT,
  s.PARENTUNIT,
  s.noofchildren,
  s.mission,
  s.personalcontact,
  s.emergencycontact,
  s.HEIGHT,
  s.RELIGION,
  s.MEDCATEGORY,
  s.DATEOFBIRTH,
  s.GENDER,
  s.LIVINGSTATUS,
  s.VILLAGE,
  s.THANA,
  s.DISTRICT,
  s.DATEOFENROLL,
  r.RANK,
  t.TRADE,
  c.COMPANYNAME,
  i.PASSPORT_PICTURE_PATH AS PROFILEPICTURE,
  TRUNC(MONTHS_BETWEEN(CURRENT_DATE, s.DATEOFBIRTH) / 12) AS AGE,
  TRUNC(MONTHS_BETWEEN(CURRENT_DATE, s.DATEOFENROLL) / 12) AS SERVICEAGE,
  TRUNC(CURRENT_DATE - l.LEAVEENDDATE) AS LASTLEAVE
FROM SOLDIER s
LEFT JOIN RANKS r ON s.RANKID = r.RANKID
LEFT JOIN TRADE t ON s.TRADEID = t.TRADEID
LEFT JOIN COMPANY c ON s.COMPANYID = c.COMPANYID
LEFT JOIN (
  SELECT SOLDIERID, MAX(LEAVEENDDATE) AS LEAVEENDDATE
  FROM LEAVEMODULE
  WHERE LEAVEENDDATE <= CURRENT_DATE
  GROUP BY SOLDIERID
) l ON s.SOLDIERID = l.SOLDIERID
LEFT JOIN UPLOADED_IMAGES i ON s.SOLDIERID = i.SOLDIER_ID;
