-- Set a new appointment for patient id 2 

-- not sure if we just need to add a new value in the table 
-- OR update the values of the existing row of the patient 

-- FIRST OPTION
WITH App AS (
    INSERT INTO Appointment 
    VALUES (DEFAULT, 2, 2, TO_DATE('2022 04 28', 'YYYY MM DD'), '14:00:00', '15:00:00', 1, 'Booked', 8)
    RETURNING appointment_id
    ), 
    Treat AS (
        INSERT INTO Treatment
        VALUES (DEFAULT, 'Tooth cleaning', 'No medications administered', 'No symptoms', 999, 'The teeth are very dirty', 2, (SELECT appointment_id FROM App))
    )
INSERT INTO Appointment_procedure (appointment_id, patient_id, date_of_procedure, procedure_code, appointment_description, tooth, amount_of_procedure, total_charge) 
    VALUES ((SELECT appointment_id FROM App), 2, TO_DATE('2022 04 28', 'YYYY MM DD'), 1, 'Regular cleaning', 999, 1, 1000);