
CREATE OR REPLACE TRIGGER check_medical_disposal_trigger
BEFORE INSERT ON MedicalInfo
FOR EACH ROW
DECLARE
    disposal_count NUMBER;
BEGIN
    -- Check if there are any overlapping disposal dates for the soldier
    SELECT COUNT(*) INTO disposal_count
    FROM MedicalInfo
    WHERE SoldierID = :NEW.SoldierID
        AND ((:NEW.StartDate >= StartDate AND :NEW.StartDate <= EndDate)
            OR (:NEW.EndDate >= StartDate AND :NEW.EndDate <= EndDate));
            
    -- If there are overlapping disposal dates, raise an exception
    IF disposal_count > 0 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Medical disposal dates overlap for the soldier');
    END IF;
END;
/



CREATE OR REPLACE TRIGGER MedicalIDTrigger
BEFORE INSERT ON MedicalInfo
FOR EACH ROW
BEGIN
  SELECT MedicalIDSeq.NEXTVAL INTO :new.MedicalID FROM dual;
END;
/


CREATE OR REPLACE TRIGGER check_leave_trigger
BEFORE INSERT OR UPDATE ON LeaveModule
FOR EACH ROW
DECLARE
    leave_count NUMBER;
BEGIN
    -- Check if the soldier's previous leave overlaps with the system date
    SELECT COUNT(*) INTO leave_count
    FROM LeaveModule
    WHERE SoldierID = :NEW.SoldierID
        AND (LeaveStartDate <= SYSDATE AND SYSDATE <= LeaveEndDate);

    -- If the soldier's previous leave overlaps with the system date, raise an exception
    IF leave_count > 0 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Soldier is already on leave');
    END IF;
END;


/

create or replace TRIGGER check_temp_ere_trigger
BEFORE INSERT OR UPDATE ON Soldier
FOR EACH ROW
BEGIN
    IF :NEW.TemporaryCommand = 'Yes' AND :NEW.ERE = 'Yes' THEN
        RAISE_APPLICATION_ERROR(-20001, 'A soldier cannot be in Temporary Command and ERE simultaneously.');
    END IF;
END;


CREATE OR REPLACE TRIGGER appointmentAbsent
BEFORE INSERT OR UPDATE ON SOLDIERAPPOINTMENT
FOR EACH ROW
DECLARE
    isPresent NUMBER;
BEGIN
    -- Check if the soldier's is present
    SELECT ISPRESENT INTO isPresent
    FROM SOLDIER
    WHERE SoldierID = :NEW.SoldierID;

    -- If the soldier is absent, raise an exception
    IF isPresent = 0 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Soldier is absent');
    END IF;
END;
/


/
