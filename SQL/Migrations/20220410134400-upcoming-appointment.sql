-- Displays upcoming appointments with the dentist of all patients
SELECT I.name, A.date_of_appointment, A.start_time, 
      A.end_time, A.appointment_status, C.procedure_name, EE.name AS dentist_name
FROM Patient_info I, Patient P, Appointment A, Procedure_codes C,
     Employee E, Employee_info EE
WHERE I.patient_sin = P.sin_info AND P.patient_id = A.patient_id
     AND A.dentist_id=E.employee_id AND E.employee_sin=EE.employee_sin
     AND CAST(A.appointment_type AS INT)=C.procedure_code;

-- Gives the upcoming appointment of patient with id 2
SELECT I.name, A.date_of_appointment, A.start_time, 
      A.end_time, A.appointment_status, C.procedure_name, EE.name AS dentist_name
FROM Patient_info I, Patient P, Appointment A, Procedure_codes C,
     Employee E, Employee_info EE
WHERE I.patient_sin = P.sin_info AND P.patient_id = A.patient_id
     AND A.dentist_id=E.employee_id AND E.employee_sin=EE.employee_sin
     AND CAST(A.appointment_type AS INT)=C.procedure_code AND P.patient_id = 2;