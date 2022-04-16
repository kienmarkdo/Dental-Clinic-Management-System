-- adds a new patient

INSERT INTO Patient_info (patient_sin, address, name, gender, email, phone, date_of_birth, insurance) 
    VALUES (123321123, 'some address', 'First Last', 'X', 'fl@mail.com', '1472583690', TO_DATE('2000 01 01','YYYY MM DD'), 'Insured Company');
WITH P AS (
        INSERT INTO Patient (sin_info) 
        VALUES (123321123) RETURNING patient_id
    ),
    PR AS (
        INSERT INTO Patient_records (patient_details, patient_id) 
        VALUES ('No information available', (SELECT patient_id FROM P))
    )
INSERT INTO User_account (username, password, type_id, patient_id) 
    VALUES ('flast123', 'password', 0, (SELECT patient_id FROM P));