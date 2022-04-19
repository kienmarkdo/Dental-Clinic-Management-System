-- Patient Info
INSERT INTO Patient_info VALUES (
    164645466, 
    '123 Sesame Street', 
    'Elmo Lee', 
    'M', 
    'elmo@elmail.com', 
    '6664206969', 
    TO_DATE('2000 01 01', 'YYYY MM DD'), -- constrained checked with '2008-01-01' https://onecompiler.com/postgresql/3xyjc6nst
    NULL,
    NULL
),
(111111111,'529 Random Road','Stephie McRandom','M','random@gmail.com','1231231234',TO_DATE('2012 01 01','YYYY MM DD'),'Random Insurance Company Inc.',ROW('Random McRandom Sr.','1231231234','randomsenior@gmail.com','Dad')),
(111111112,'529 Random Road','Paul McRandomee','F','randomee@gmail.com','5551231234',
TO_DATE('2008 01 01','YYYY MM DD'),'Random Insurance Company Inc.',ROW('Random McRandom Sr.','1231231234','randomsenior@gmail.com','Dad')),
--(111111112,'529 Random Road','Random McRandom','F','random@gmail.com','1231231234',
--TO_DATE('2020 01 01','YYYY MM DD'),'Random Insurance Company Inc.',
--NULL), -- this will not work because rep is NULL, yet the age < 15
(515151547, '525 Elgin Street', 'Brooke Lay', 'F', 'brooke@gamil.com','3436589636',TO_DATE('2002 06 08', 'YYYY MM DD'),NULL,NULL),
(388498874, '1225 Imaginary Street, Toronto, ON, Canada', 'John Li', 'F', 'john@gamil.com','3437826548',TO_DATE('2000 09 03', 'YYYY MM DD'),NULL,NULL);

-- Patient
INSERT INTO Patient VALUES 
(DEFAULT,164645466), -- should be 1,2,3,4,5
(DEFAULT,111111111),
(DEFAULT,111111112),
(DEFAULT,515151547),
(DEFAULT,388498874);


-- Patient records
INSERT INTO Patient_records VALUES
(DEFAULT, 'Patient is going lose their teeth in 2 years if they do not book another appointment with us.', 1),
(DEFAULT,'Stephie current has healthy teeth. Only requires annual cleaning.', 2),
(DEFAULT,'Paul Sr. had an annual teeth cleaning last year', 3),
(DEFAULT,'Brooke needs invisalign because her teeth is crooked.',4),
(DEFAULT,'John needs extractions on her teeth.',5);


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

(388498874,'d','John Li', '1225 Imaginary Street, Toronto, ON, Canada', 70000.50), -- dentist at branch id 2, ALSO A PATIENT
(432364646,'d','Samy Touabi', '5346 Postgres Avenue, Toronto, ON, Canada', 70000.50), -- dentist at branch id 2
(665946369,'r','Oliva Mars', '355 MySQL Road, Toronto, ON, Canada', 55000.50), -- receptionist at branch id 2
(135941655,'r','Christopher Castillo', '885 NoSQL Drive, Toronto, ON, Canada', 55000.50), -- receptionist at branch id 2
(256356565,'h','Nakul Lover', '5243 MariaDB Crossing, Toronto, ON, Canada', 60000.50), -- hygienist at branch id 2
(956233565,'b','Kien Do', '420 Oracle Street, Toronto, ON, Canada', 83000.50); -- manager at branch id 2

-- Branch
INSERT INTO Branch VALUES 
(DEFAULT,'Ottawa', NULL, NULL, NULL),
(DEFAULT,'Toronto', NULL, NULL, NULL);

-- Employee
INSERT INTO Employee VALUES 
 -- Employees in branch id 1; 
(DEFAULT,123456789, 1), -- this is a receptionist at branch 1
(DEFAULT,141286236, 1),
(DEFAULT,158453648, 1),
(DEFAULT,198523644, 1),
(DEFAULT,165984846, 1), -- this is a manager at branch 1
 -- Employees in branch id 2
(DEFAULT,388498874, 2),
(DEFAULT,432364646, 2),
(DEFAULT,665946369, 2), -- receptionist 1 at branch 2
(DEFAULT,135941655, 2), -- receptionist 2 at branch 2
(DEFAULT,256356565, 2),
(DEFAULT,956233565, 2); -- this is a manager at branch 2
-- don't modify the insertions above to DEFAULT, just make the next insertions DEFAULT

-- Add managers and receptionists to the existing branches
UPDATE Branch
SET manager_id = 5,
receptionist1_id = 1
WHERE (city = 'Ottawa');

UPDATE Branch
SET manager_id = 11,
receptionist1_id = 8,
receptionist2_id = 9
WHERE (city = 'Toronto');

-- Procedure codes
INSERT INTO Procedure_codes VALUES
  (1, 'Teeth Cleanings'),
  (2, 'Teeth Whitening'),
  (3, 'Extractions'),
  (4, 'Veneers'),
  (5, 'Fillings'),
  (6, 'Crowns'),
  (7, 'Root Canal'),
  (8, 'Braces/invisalign'),
  (9, 'Bonding'),
  (10,'Dentures')
;

-- First the patient books an appointment (into a future date)
-- The patient is diagnosed in Treatment
-- Once the Treatment is prescribed, we then create a Appointment_procedure

-- Appointment
-- We could make a list/drop menu of dentists where the person who's doing 
-- the appt booking can choose a dentist
INSERT INTO Appointment VALUES
(1,3,3,TO_DATE('2022 04 14', 'YYYY MM DD'),'10:00:00','11:00:00',2,'Booked',5), -- Make sure the 'Extractions' and 'Teeth Cleanings' match up with the procedure code in the Appointment_procedure table
 -- Stephie McRandom's appointments START
(2,2,3,TO_DATE('2022 04 02', 'YYYY MM DD'),'08:30:00','09:00:00',2,'Cancelled',1),
(3,2,3,TO_DATE('2022 04 03', 'YYYY MM DD'),'08:30:00','09:00:00',3,'No Show',2),
(4,2,2,TO_DATE('2022 04 04', 'YYYY MM DD'),'10:00:00','11:00:00',3,'Completed',23),
(5,2,2,TO_DATE('2022 04 05', 'YYYY MM DD'),'11:00:00','12:00:00',4,'Booked',21),
(6,2,3,TO_DATE('2022 04 06', 'YYYY MM DD'),'09:00:00','10:00:00',1,'Booked',13),
(7,2,6,TO_DATE('2022 04 07', 'YYYY MM DD'),'14:00:00','14:30:00',2,'Unscheduled',11);
 -- Stephie McRandom's appointments END

-- Treatment
INSERT INTO Treatment VALUES
(DEFAULT, 'Tooth removal', 'Midazolam', 'Tooth ache', 23, 'Do not eat food 24 hours before the procedure', 1, 1),
-- Stephie McRandom's treatments START
(DEFAULT, 'Tooth cleaning', 'No medications administered', 'No symptoms', 999, 'No comments', 2, 2),
(DEFAULT, 'Root Canal', 'Anesthesia', 'Dysarthria (Temporary speech impairment)', 33, 'Do not eat food 24 hours before the procedure. Cannot drive after the treatment.', 2, 3),
(DEFAULT, 'Bonding', 'No medications administered', 'No symptoms', 21, 'No comments', 2, 5),
(DEFAULT, 'Invisalign', 'No medications administered', 'No symptoms', 999, 'Await further instructions from the orthodonist', 2, 7)
-- Stephie McRandom's treatments START
;

-- Appointment Procedure
INSERT INTO Appointment_procedure VALUES (
  DEFAULT,
  1,
  3,
  TO_DATE('2022 04 14', 'YYYY MM DD'),
  NULL,
  3,
  'We need to remove the wisdom tooth of the patient - Booked',
  8, -- this means quadrant 2, tooth #3 https://www.summerleadental.com/all-about-the-tooth-numbers/
  1, -- this means, remove 1 tooth
  NULL,
  NULL,
  500.00,
  NULL
),
-- Stephie McRandom's procedures START
(
  DEFAULT,2,2,TO_DATE('2022 04 02', 'YYYY MM DD'),NULL,2,'Annual patient dental cleaning - Cancelled',
  999, -- code for operation that involves every tooth
  0, -- it's a cleaning, so it's 0
  NULL,NULL,00.00,NULL -- cancelled so price is 0
),
(
  DEFAULT,3,2,TO_DATE('2022 04 03', 'YYYY MM DD'),NULL,2,'Annual patient dental cleaning - No show',
  999, -- code for operation that involves every tooth
  0, -- it's a cleaning, so it's 0
  NULL,NULL,14.00,NULL -- no show - a charge of $14 is added to the patient's account
),
(
  DEFAULT,4,2,TO_DATE('2022 04 04', 'YYYY MM DD'),NULL,2,'Annual patient dental cleaning - Completed',
  999, -- code for operation that involves every tooth
  0, -- it's a cleaning, so it's 0
  NULL,NULL,100,NULL -- teeth cleaning completed
),
(
  DEFAULT,5,2,TO_DATE('2022 04 05', 'YYYY MM DD'),NULL,7,'Root canal appointment - Booked',
  33,
  7, -- 7 Root Canal
  NULL,NULL,1000,NULL -- root canal - cost is $1000
),
(
  DEFAULT,6,2,TO_DATE('2022 04 06', 'YYYY MM DD'),NULL,9,'Dental Cleaning - Booked',
  21,
  9, -- 9 Bonding
  NULL,NULL,500,NULL -- bonding booked - cost is $500
),
(
  DEFAULT,7,2,TO_DATE('2022 04 07', 'YYYY MM DD'),NULL,8,'Invisalign appointment - Unscheduled',
  999, -- code for operation that involves every tooth
  8, -- 8 Invisalign
  NULL,NULL,7200,NULL -- invisalign unscheduled
)
-- Stephie McRandom's procedures START
;


-- Fee charge
INSERT INTO Fee_charge VALUES
(DEFAULT, 1, 123,400), -- 123 is a random fee code for extractions
(DEFAULT, 1, 124,100), -- 124 is a random fee code for medications
-- Stephie McRandom's appointment procedure fee charges START
(DEFAULT, 2, -100,0), -- -100 is a random fee code for cancelled appointments
(DEFAULT, 3, 94303,14), -- 94303 is a code for no shows; automatic $14 charge
(DEFAULT, 4, 100,100), -- dental cleaning code 100 charge 100
(DEFAULT, 5, 107,800), -- root canal cost
(DEFAULT, 5, 125,200), -- root canal anesthesia cost
(DEFAULT, 6, 109,500), -- bonding cost
(DEFAULT, 7, 108,7200) -- invisalign cost
-- Stephie McRandom's appointment procedure fee charges END
;


-- Invoice (update Appointment_procedure depending on the values of Invoice)
INSERT INTO Invoice VALUES
(
  -- Elmo's invoice
  DEFAULT,
  TO_DATE('2022 04 05', 'YYYY MM DD'),
  'The Downtown Dental Clinic
  Ottawa ON K1P 6L7
  (613) 234-0792
  ',
  500,0,0,0,1 -- Elmo does not have insurance
),
  -- Random's invoices START
(
  DEFAULT,TO_DATE('2022 04 02', 'YYYY MM DD'), -- cancelled
  'The Downtown Dental Clinic
  Ottawa ON K1P 6L7
  (613) 234-0792
  ',
  0,0,0,0,2
),
(
  DEFAULT,TO_DATE('2022 04 03', 'YYYY MM DD'), -- no show
  'The Downtown Dental Clinic
  Ottawa ON K1P 6L7
  (613) 234-0792
  ',
  0,0,0,14,2
),
(
  DEFAULT,TO_DATE('2022 04 04', 'YYYY MM DD'), -- dental cleaning completed
  'The Downtown Dental Clinic
  Ottawa ON K1P 6L7
  (613) 234-0792
  ',
  20,80,0,0,2
),
(
  DEFAULT,TO_DATE('2022 04 05', 'YYYY MM DD'), -- root canal booked
  'The Downtown Dental Clinic
  Ottawa ON K1P 6L7
  (613) 234-0792
  ',
  100,900,0,0,2
),
(
  DEFAULT,TO_DATE('2022 04 06', 'YYYY MM DD'), -- bonding booked
  'The Downtown Dental Clinic
  Ottawa ON K1P 6L7
  (613) 234-0792
  ',
  300,200,0,0,2
),
(
  DEFAULT,TO_DATE('2022 04 07', 'YYYY MM DD'), -- invisalign unscheduled
  'The Downtown Dental Clinic
  Ottawa ON K1P 6L7
  (613) 234-0792
  ',
  7200,0,0,0,2
)
  -- Random's invoices END
;

-- Insurance_claim
INSERT INTO Insurance_claim VALUES
(DEFAULT,164645466,'SunLife Insurance','91833',200,1),
-- Random's Insurance claims START
(DEFAULT,111111111,'Random Insurance Company Inc.','11111',80,4),
(DEFAULT,111111111,'Random Insurance Company Inc.','11111',900,5),
(DEFAULT,111111111,'Random Insurance Company Inc.','11111',200,6)
-- Random's Insurance claims END
;


-- Appointment procedure
UPDATE Appointment_procedure -- Elmo does not have insurance
SET 
invoice_id = 1,
patient_charge = 500
WHERE (procedure_id = 1);

UPDATE Appointment_procedure -- Random has insurance; Cancelled
SET 
invoice_id = 2,
patient_charge = 0
WHERE (procedure_id = 2);

UPDATE Appointment_procedure -- Random has insurance; No show
SET 
invoice_id = 3,
patient_charge = 14
WHERE (procedure_id = 3);

UPDATE Appointment_procedure -- Random has insurance; Completed
SET 
invoice_id = 4,
insurance_charge = 80,
patient_charge = 20,
insurance_claim_id = 2
WHERE (procedure_id = 4);

UPDATE Appointment_procedure -- Random has insurance; Booked Root Canal
SET 
invoice_id = 5,
insurance_charge = 900,
patient_charge = 100,
insurance_claim_id = 3
WHERE (procedure_id = 5);

UPDATE Appointment_procedure -- Random has insurance; Booked Bonding
SET 
invoice_id = 6,
insurance_charge = 200,
patient_charge = 300,
insurance_claim_id = 4
WHERE (procedure_id = 6);

UPDATE Appointment_procedure -- Random has insurance; Unscheduled invisalign
SET 
invoice_id = 7,
patient_charge = 7200
WHERE (procedure_id = 7);



-- Patient Billing
INSERT INTO Patient_billing VALUES
(DEFAULT,1,300,200,500,'Visa'),
(DEFAULT,2,14,0,14,'Cash'),
(DEFAULT,2,20,80,100,'Mastercard'),
(DEFAULT,2,100,900,1000,'Mastercard'),
(DEFAULT,2,300,200,500,'Mastercard'),
(DEFAULT,2,7200,0,7200,'Visa');


-- User Accounts
-- password is 'ASDFGHJKL:123456', entered value are hashed values
INSERT INTO user_account VALUES ('elmurder666', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 0, 1, NULL);
INSERT INTO user_account VALUES ('randommd5', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 0, 2, NULL);
INSERT INTO user_account VALUES ('randomeemd6', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 0, 3, NULL);
INSERT INTO user_account VALUES ('xXx_blayde_xXx', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 0, 4, NULL);
INSERT INTO user_account VALUES ('tisla2714', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 1, NULL, 2);
INSERT INTO user_account VALUES ('cwmk3565', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 1, NULL, 3);
INSERT INTO user_account VALUES ('akiti7935', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 1, NULL, 4);
INSERT INTO user_account VALUES ('stoua0809', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 1, NULL, 7);
INSERT INTO user_account VALUES ('kdo2342', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 1, NULL, 11);
INSERT INTO user_account VALUES ('johnli255', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 2, 5, 6);
INSERT INTO user_account VALUES ('bobmley1', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 1, NULL, 1); --receptionist
INSERT INTO user_account VALUES ('olvMar8', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 1, NULL, 8); --receptionist
INSERT INTO user_account VALUES ('CrsClo9', '$2y$10$1q472H5E5tmeYWmKnSeuWOic2ooLuTLbU6gjSBrrwJEsp/Tq8uvpu', 1, NULL, 9); --receptionist

-- Review
INSERT INTO Review VALUES (
  DEFAULT,
  'Tisham Islam',
  "The dentist was very professional and clean, but he did not communicate with me very well",
  5, -- constraint is checked with values -1 and 6 (https://onecompiler.com/postgresql/3xxy4xntj)
  2,
  4,
  TO_DATE('2022 04 09', 'YYYY MM DD'),
  1
),
(
  DEFAULT,
  'Céline Wan',
  "I could not believe the service I received for this appointment. Absolutely horrible. You'll be hearing about me on Yelp!!!",
  1,
  1,
  1,
  TO_DATE('2022 04 10', 'YYYY MM DD'),
  2
);