-- Patient Info
INSERT INTO Patient_info VALUES (
    164645466, 
    '123 Sesame Street', 
    'Elmo', 
    'M', 
    'elmo@elmail.com', 
    '6664206969', 
    TO_DATE('2000-01-01', 'YYYYMMDD'), -- constrained checked with '2008-01-01' https://onecompiler.com/postgresql/3xyjc6nst
    NULL,
    NULL
),
(111111111,'529 Random Road','Random McRandom','M','random@gmail.com','1231231234',TO_DATE('2020-01-01','YYYYMMDD'),'Random Insurance Company Inc.',ROW('Random McRandom Sr.','1231231234','randomsenior@gmail.com','Dad')),
(111111112,'529 Random Road','Randomee McRandomee','F','randomee@gmail.com','5551231234',
TO_DATE('2020-01-01','YYYYMMDD'),'Random Insurance Company Inc.',ROW('Random McRandom Sr.','1231231234','randomsenior@gmail.com','Dad')),
--(111111112,'529 Random Road','Random McRandom','F','random@gmail.com','1231231234',
--TO_DATE('2020-01-01','YYYYMMDD'),'Random Insurance Company Inc.',
--NULL), -- this will not work because rep is NULL, yet the age < 15
(515151547, '525 Elgin Street', 'Brooke Lay', 'F', 'brooke@gamil.com','3436589636',TO_DATE('2002-06-08', 'YYYYMMDD'),NULL,NULL),
(388498874, '1225 Imaginary Street', 'John Li', 'F', 'john@gamil.com','3437826548',TO_DATE('2000-09-03', 'YYYYMMDD'),NULL,NULL);

-- Patient
INSERT INTO Patient VALUES 
(1,164645466),
(2,515151547),
(3,388498874);

-- Representative for patient under 15yo
INSERT INTO Representative VALUES 
('Jennie', 388498874, 3437826548, 'mother');

-- Patient Records
INSERT INTO Patient_records VALUES (1, '100 of Samy''s hugs', 1),
(2,'Removal of tooth 23 on John',3);

-- Invoice
INSERT INTO Invoice VALUES
(   1, 
    TO_DATE('2022-04-06', 'YYYYMMDD'),
    'Jeenie Lie, mother',
    250.00,
    250.75,
    0,
    0,
    3
),
(
    2,
    TO_DATE('2000-01-01', 'YYYYMMDD'),
    'Selin',
    820000,
    960000,
    40000,
    -30000,
    1
);

-- Appointment
INSERT INTO Appointment VALUES
(1,3,2,TO_DATE('2022-04-06', 'YYYYMMDD'),'10:00:00','11:00:00','Extraction','completed',23);

-- Appointment procedure
INSERT INTO Appointment_procedure VALUES
(
  1,
  1,
  3,
  TO_DATE('2022-04-06', 'YYYYMMDD'),
  1,
  3,
  'Extraction',
  'We need to remove a teeth of the patient',
  23,
  1,
  250.00,
  250.75,
  500.75,
  1
  );

-- Insurance Claim
INSERT INTO Insurance_claim VALUES
(2,388498874,'Tisham','SunLife',12938484,200.75,1);

-- Review
INSERT INTO Review VALUES (
  123,
  'John Doe',
  5, -- constraint is checked with values -1 and 6 (https://onecompiler.com/postgresql/3xxy4xntj)
  2,
  4,
  '2022-04-02',
  1
);

-- Patient Billing
INSERT INTO Patient_billing VALUES
(1,3,250.00,250.75,500.75,'Visa');

-- Employee Info 
INSERT INTO Employee_info VALUES (
  123456789,
  'r', -- constraint checked with 'f' (https://onecompiler.com/postgresql/3xxy77n5g)
  'Bob Marley',
  '123 Postgres Street, Ottawa, ON, Canada',
  60000.25123 -- tested - only shows 60000.25
), -- branch id has 1 receptionist
(141286236,'d','Tisham Islam', '123 Postgres Street, Ottawa, ON, Canada', 75000.50), -- dentist at branch id 1
(158453648,'d','Céline Wan', '123 Postgres Street, Ottawa, ON, Canada', 75000.50), -- dentist at branch id 1
(198523644,'h','Amy Kkiti', '123 Postgres Street, Ottawa, ON, Canada', 65000.50), -- hygenist at branch id 1
(165984846,'b','Bruno Bale', '523 Sesame Street, Ottawa, ON, Canada', 83000.50), -- manager at branch id 1

(175256987,'d','Sarah Lee', '523 Sesame Street, Ottawa, ON, Canada', 70000.50), -- dentist at branch id 2
(432364646,'d','Samy Touabi', '523 Sesame Street, Ottawa, ON, Canada', 70000.50), -- dentist at branch id 2
(665946369,'r','Oliva Mars', '523 Sesame Street, Ottawa, ON, Canada', 55000.50), -- receptionist at branch id 2
(135941655,'r','Christopher Castillo', '523 Sesame Street, Ottawa, ON, Canada', 55000.50), -- receptionist at branch id 2
(256356565,'h','Nakul Lover', '523 Sesame Street, Ottawa, ON, Canada', 60000.50), -- hygienist at branch id 2
(956233565,'b','Kien Do', '523 Sesame Street, Ottawa, ON, Canada', 83000.50); -- manager at branch id 2


-- Employee
INSERT INTO Employee VALUES 
(1,123456789, 1), -- Employees in branch id 1
(2,141286236, 1),
(3,158453648, 1),
(4,198523644, 1),
(5,165984846, 1),

(6,175256987, 2), -- Employees in branch id 2
(7,432364646, 2),
(8,665946369, 2),
(9,135941655, 2),
(10,256356565, 2),
(11,956233565, 2);


-- Branch (Not sure how we're supposed to initialise the manager and receptionist id
-- i dont think hardcoding is the proper way) 
INSERT INTO Branch VALUES 
(1,'Ottawa', 5, 1, DEFAULT),
(2,'Ottawa', 11, 8, 9);