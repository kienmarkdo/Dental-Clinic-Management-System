-- This query is used in patient_landing.php to get all dentist ids and names. Could be useful elsewhere
SELECT DISTINCT a.dentist_id, e_info.name AS dentist_name FROM appointment a 
JOIN Employee e ON a.dentist_id = e.employee_id
JOIN Employee_info e_info ON e.employee_sin = e_info.employee_sin;