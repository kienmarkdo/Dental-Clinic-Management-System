-- Set a new appointment for patient id 2 
-- (still needs to be worked on, needs Treatment and Appointment_procedure)

-- not sure if we just need to add a new value in the table 
-- OR update the values of the existing row of the patient 

-- FIRST OPTION
INSERT INTO Appointment VALUES
(3,2,2,TO_DATE('2022-04-28', 'YYYYMMDD'),'14:00:00','15:00:00',1,'Booked',8)

-- SECOND OPTION
UPDATE Appointment
SET date_of_appointment = '2022-04-28',
    start_time = '14:00:00', end_time = '15:00:00',
    appointment_type = 1, appointment_status = 'Booked', room = 8
WHERE patient_id = 2;