CREATE OR REPLACE FUNCTION CalculateAge(DateOfBirth DATE) RETURN NUMBER DETERMINISTIC IS
BEGIN
  RETURN TRUNC(MONTHS_BETWEEN(SYSDATE, DateOfBirth) / 12);
END;
/

ALTER TABLE Soldier ADD Age NUMBER GENERATED ALWAYS AS (CalculateAge(DateOfBirth));



CREATE OR REPLACE FUNCTION CalculateIsPresent(p_soldier_id IN NUMBER)
  RETURN NUMBER
DETERMINISTIC
IS
  l_leave_count NUMBER;
  l_temp_command VARCHAR2(3);
  l_ere VARCHAR2(3);
  l_serving_status VARCHAR2(10);
  l_disposal_count NUMBER;
BEGIN
  -- Check if the soldier is on leave
  SELECT COUNT(*) INTO l_leave_count
  FROM TODAYS_LEAVE_VIEW
  WHERE SoldierID = p_soldier_id;

  -- Check the soldier's temporary command, ere, and serving status
  SELECT TemporaryCommand, ERE, ServingStatus
  INTO l_temp_command, l_ere, l_serving_status
  FROM Soldier
  WHERE SoldierID = p_soldier_id;
  
  -- Check if the soldier has a disposal today
  SELECT COUNT(*) INTO l_disposal_count
  FROM MedicalInfo
  WHERE SOLDIERID = p_soldier_id
    AND TRUNC(STARTDATE) <= TRUNC(SYSDATE)
    AND TRUNC(ENDDATE) >= TRUNC(SYSDATE);

  -- Return 0 if soldier is on leave, has a disposal today, or meets additional conditions, 1 otherwise
  RETURN CASE
    WHEN l_leave_count > 0 THEN 0
    WHEN l_temp_command = 'Yes' OR l_ere = 'Yes' OR l_serving_status <> 'Serving' OR l_disposal_count > 0 THEN 0
    ELSE 1
  END;
END;
/

ALTER TABLE Soldier ADD ISPRESENT GENERATED ALWAYS AS (CalculateIsPresent(SoldierID)) VIRTUAL;




CREATE OR REPLACE TRIGGER CHECK_TEMP_ERE_TRIGGER
BEFORE UPDATE ON SOLDIER
FOR EACH ROW
BEGIN
    IF :NEW.TEMPORARYCOMMAND = 'YES' AND :NEW.ERE = 'YES' THEN
        RAISE_APPLICATION_ERROR(-20001, 'A SOLDIER CANNOT BE IN TEMPORARY COMMAND AND ERE SIMULTANEOUSLY.');
    END IF; 
END;