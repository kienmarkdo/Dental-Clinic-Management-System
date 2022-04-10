-- adds a new employee

INSERT INTO Employee_info (employee_sin, employee_type, name, address, annual_salary) 
    VALUES (123123123, 'd', 'Johnny Smith', '123 Middle of Nowhere Avenue', 100);
INSERT INTO Employee (employee_sin, branch_id) 
    VALUES (123123123, 1);