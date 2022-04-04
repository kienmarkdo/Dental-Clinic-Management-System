-- Patient Info
INSERT INTO Patient_info VALUES (
    164645466, 
    '123 Sesame Street', 
    'Elmo', 
    'M', 
    'elmo@elmail.com', 
    '6664206969', 
    TO_DATE('2000-01-01', 'YYYYMMDD'), 
    NULL
), 
(515151547, '525 Elgin Street', 'Brooke Lay', 'F', 'brooke@gamil.com',3436589636,TO_DATE('2002-06-08', 'YYYYMMDD'),NULL);

-- Patient
INSERT INTO Patient VALUES 
(DEFAULT,164645466),
(DEFAULT,515151547);

-- Patient Records
INSERT INTO Patient_records VALUES (1, '100 of Samy''s hugs', 1);

-- Invoice
INSERT INTO Invoice VALUES (
    1,
    TO_DATE('2000-01-01', 'YYYYMMDD'),
    'Selin',
    820000,
    960000,
    40000,
    -30000,
    1
);

-- Insurance Claim
INSERT INTO Insurance_claim VALUES (
    1,
    1,
    'Dorra',
    'Insurance R'' Us',
    1,
    666420,
    1
);

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
(DEFAULT,123456789, 1), -- Employees in branch id 1
(DEFAULT,141286236, 1),
(DEFAULT,158453648, 1),
(DEFAULT,198523644, 1),
(DEFAULT,165984846, 1),

(DEFAULT,175256987, 2), -- Employees in branch id 2
(DEFAULT,432364646, 2),
(DEFAULT,665946369, 2),
(DEFAULT,135941655, 2),
(DEFAULT,256356565, 2),
(DEFAULT,956233565, 2);


-- Branch (Not sure how we're supposed to initialise the manager and receptionist id
-- i dont think hardcoding is the proper way) 
INSERT INTO Branch VALUES 
(DEFAULT,'Ottawa', 5, 1, DEFAULT),
(DEFAULT,'Ottawa', 11, 8, 9);