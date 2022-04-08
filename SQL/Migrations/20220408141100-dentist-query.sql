--Show the list of dentists in each branch

SELECT name 
FROM Branch B, Employee E, Employee_info I
WHERE E.branch_id = B.branch_id
AND E.employee_sin = I.employee_sin
AND I.employee_type LIKE 'd';