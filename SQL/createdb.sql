-- Representative type
CREATE TYPE Representative AS (
  name VARCHAR(255),
  phone VARCHAR(20),
  email VARCHAR(255),
  relationship VARCHAR(255) -- e.g.: 'Mom', 'Dad'...
);

-- Patient Info
CREATE TABLE Patient_info (
    patient_sin INTEGER PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    gender VARCHAR(1) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    date_of_birth DATE NOT NULL,
    insurance VARCHAR(255) NULL,
    rep REPRESENTATIVE NULL, -- Representative type (name, phone, email, relationship); patient can have 0, 1 or 2 Representatives
    
    -- constraints
    CONSTRAINT Gender_check
        CHECK(gender IN ('M','F','X')), -- gender must be M, F or X
    CONSTRAINT Age_and_representative_check
        CHECK(
        date_of_birth <= (CURRENT_DATE - '15 years'::interval)::date  -- age must be 15 or higher
        OR (date_of_birth > (CURRENT_DATE - '15 years'::interval)::date AND (rep IS NOT NULL)) -- age lower than 15 must have Representative)
    )

    -- Composite type PostgreSQL documentation: https://www.postgresql.org/docs/current/rowtypes.html
    -- StackOverflow code for age check: https://stackoverflow.com/questions/59975034 select-all-participants-under-the-age-of-18-using-the-current-date-in-postgresql
    -- Link to test: https://onecompiler.com/postgresql/3xyfnb8ju
);

-- Patient
CREATE TABLE Patient (
  patient_id SERIAL PRIMARY KEY,
  sin_info INTEGER NOT NULL,
  CONSTRAINT FK_patient_sin 
    FOREIGN KEY(sin_info) 
    REFERENCES Patient_info(patient_sin)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

-- Patient Records
CREATE TABLE Patient_records (
    record_id SERIAL PRIMARY KEY,
    patient_details TEXT NOT NULL, -- details on the state of the patient
    patient_id INTEGER NOT NULL,
    CONSTRAINT FK_patient_id 
        FOREIGN KEY(patient_id) 
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Invoice
CREATE TABLE Invoice (
    invoice_id SERIAL PRIMARY KEY,
    date_of_issue DATE NOT NULL,
    contact_info VARCHAR(255) NOT NULL,
    patient_charge NUMERIC(10,2) NOT NULL,
    insurance_charge NUMERIC(10,2) NOT NULL,
    discount NUMERIC(10,2) NOT NULL,
    penalty NUMERIC(10,2) NOT NULL,
    patient_id INTEGER NOT NULL,
    
    CONSTRAINT FK_patient_id 
        FOREIGN KEY(patient_id) 
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Insurance Claim
CREATE TABLE Insurance_claim (
    claim_id SERIAL PRIMARY KEY,
    patient_sin INTEGER NOT NULL,
    insurance_company VARCHAR(255) NOT NULL,
    plan_number INTEGER NOT NULL,
    coverage NUMERIC(10,2) NOT NULL,
    invoice_id INTEGER NOT NULL,
    
    CONSTRAINT FK_patient_sin
        FOREIGN KEY(patient_sin) 
        REFERENCES Patient_info(patient_sin)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    
    CONSTRAINT FK_invoice_id 
        FOREIGN KEY(invoice_id) 
        REFERENCES Invoice(invoice_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Appointment
CREATE TABLE Appointment (
    appointment_id SERIAL PRIMARY KEY,
    patient_id INTEGER NOT NULL,
    dentist_id INTEGER NOT NULL,
    date_of_appointment DATE NOT NULL, 
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    appointment_type VARCHAR(255) NOT NULL, 
    appointment_status VARCHAR(255) NOT NULL, -- no show, cancelled, completed, unscheduled, booked
    room INTEGER NOT NULL,

    CONSTRAINT FK_patient_id
        FOREIGN KEY(patient_id)
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
    
    -- NOTE: CONSTRAINT FOREIGN KEY(dentist_id) REFERENCES Employee(employee_id) 
    --      is added at the end of the file as ALTER TABLE
    --      Must add constraint like this due to circular referencing problems in Postgres
);

-- Procedure codes
-- (1: Teeth Cleanings, 2: Teeth Whitening, 3: Extractions, 4: Veneers, 5: Fillings, 6: Crowns, 7: Root Canal, 8: Braces/Invisalign, 9: Bonding, 10: Dentures) 
CREATE TABLE Procedure_codes (
    procedure_code INTEGER PRIMARY KEY,
    procedure_name VARCHAR(255),

    CONSTRAINT procedure_code_check
        CHECK(procedure_code >= 1 AND procedure_code <= 10)
);

-- Appointment Procedure
CREATE TABLE Appointment_procedure (
    procedure_id SERIAL PRIMARY KEY,
    appointment_id INTEGER NOT NULL,
    patient_id INTEGER NOT NULL,
    date_of_procedure DATE NOT NULL, -- should be the same date as Appointment; can be auto-populated in the backend
    invoice_id INTEGER NULL,
    procedure_code INTEGER NOT NULL, -- 1: Teeth Cleanings, 2: Teeth Whitening, 3: Extractions, 4: Veneers, 5: Fillings, 6: Crowns, 7: Root Canal, 8: Braces/Invisalign, 9: Bonding, 10: Dentures
    appointment_description VARCHAR(255) NOT NULL,
    tooth INTEGER NOT NULL, -- Canadian tooth numbering system: https://www.summerleadental.com/all-about-the-tooth-numbers/
    amount_of_procedure INTEGER NOT NULL,
    patient_charge NUMERIC(10, 2) NULL,
    insurance_charge NUMERIC(10, 2) NULL,
    total_charge NUMERIC(10, 2) NOT NULL, -- total_charge should be known
    insurance_claim_id INTEGER NULL,
    -- NOTE: We update the invoice_id, insurance_charge, patient_charge, insurance_claim_id AFTER the invoice is made

    CONSTRAINT FK_appointment_id
        FOREIGN KEY(appointment_id)
        REFERENCES Appointment(appointment_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT FK_patient_id
        FOREIGN KEY(patient_id)
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT FK_invoice_id
        FOREIGN KEY(invoice_id)
        REFERENCES Invoice(invoice_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    
    CONSTRAINT FK_procedure_code
        FOREIGN KEY(procedure_code)
        REFERENCES Procedure_codes(procedure_code)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT FK_insurance_claim_id
        FOREIGN KEY(insurance_claim_id)
        REFERENCES Insurance_claim(claim_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Review
CREATE TABLE Review (
    review_id SERIAL PRIMARY KEY,
    dentist_name VARCHAR(30) NOT NULL,
    --review_description VARCHAR(255) NULL,
    professionalism INTEGER CHECK(professionalism >= 0 AND professionalism <= 5) NOT NULL,
    communication INTEGER CHECK(communication >= 0 AND communication <= 5) NOT NULL, 
    cleanliness INTEGER CHECK(cleanliness >= 0 AND cleanliness <= 5) NOT NULL,
    date_of_review DATE NOT NULL,
    procedure_id INTEGER NOT NULL, -- example IDs https://www.crescentdental.ca/10-most-common-dental-procedures-and-how-they-work/
    
    CONSTRAINT FK_procedure_id 
        FOREIGN KEY(procedure_id) 
        REFERENCES Appointment_procedure(procedure_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Patient Billing
CREATE TABLE Patient_billing (
    bill_id SERIAL PRIMARY KEY,
    patient_id INTEGER NOT NULL,
    patient_amount NUMERIC(10, 2) NOT NULL,
    insurance_amount NUMERIC(10, 2) NOT NULL,
    total_amount NUMERIC(10, 2) NOT NULL,
    payment_type VARCHAR(255) NOT NULL, -- added payment_type contraint in ALTER TABLE (line 332)
    
    CONSTRAINT FK_patient_id 
        FOREIGN KEY(patient_id) 
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Employee Info
CREATE TABLE Employee_info (
    employee_sin INTEGER PRIMARY KEY,
    employee_type VARCHAR(1) NOT NULL,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    annual_salary NUMERIC(10, 2) NOT NULL,

    CONSTRAINT employee_type
    CHECK(employee_type IN ('r', 'd', 'h', 'b')) 
    -- 'r'eceptionist, 'd'entist, 'h'ygienist, 'b'ranch manager
);

-- Employee
CREATE TABLE Employee (
    employee_id SERIAL PRIMARY KEY,
    employee_sin INTEGER NOT NULL, -- FOREIGN KEY - constraint added at the end as ALTER TABLE
    branch_id INTEGER NOT NULL -- FOREIGN KEY - constraint added at the end as ALTER TABLE

    -- NOTE: employee_sin and branch_id are FOREIGN KEYS
    -- the constraints are added at the bottom of the file using ALTER TABLE because circular referencing 
    -- is not allowed in Postgres, so if we add the constraints in CREATE TABLE Employee, it would not work
    -- because the relation Branch would not have been created
    -- code tested here https://onecompiler.com/postgresql/3xxy82f4f
);

-- Branch
CREATE TABLE Branch (
    branch_id SERIAL PRIMARY KEY,
    city VARCHAR(255) NOT NULL,
    manager_id INTEGER NULL,
    receptionist1_id INTEGER NULL,
    receptionist2_id INTEGER NULL,
    
    CONSTRAINT FK_manager_id 
        FOREIGN KEY(manager_id) 
        REFERENCES Employee(employee_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    
    CONSTRAINT FK_receptionist1_id 
        FOREIGN KEY(receptionist1_id) 
        REFERENCES Employee(employee_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
        
    CONSTRAINT FK_receptionist2_id 
        FOREIGN KEY(receptionist2_id) 
        REFERENCES Employee(employee_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Treatment
CREATE TABLE Treatment (
    treatment_id SERIAL PRIMARY KEY,
    treatment_type VARCHAR(255) NOT NULL,
    medication VARCHAR(255) NOT NULL,
    symptoms VARCHAR(255) NOT NULL,
    tooth INTEGER NOT NULL,
    comments VARCHAR(255) NOT NULL,
    patient_id INTEGER NOT NULL,
    appointment_id INTEGER NOT NULL,
    
    CONSTRAINT FK_patient_id 
        FOREIGN KEY(patient_id) 
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
        
    CONSTRAINT FK_appointment_id 
        FOREIGN KEY(appointment_id) 
        REFERENCES Appointment(appointment_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE

);

-- Fee Charge
CREATE TABLE Fee_charge (
    fee_id SERIAL PRIMARY KEY,
    procedure_id INTEGER NOT NULL,
    fee_code INTEGER NOT NULL,
    charge NUMERIC(10,2) NOT NULL,
    
    CONSTRAINT FK_procedure_id 
        FOREIGN KEY(procedure_id) 
        REFERENCES Appointment_procedure(procedure_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
); 


-- User Account
CREATE TABLE User_account ( -- user is keyword, changed to User_account 
    username VARCHAR(255) PRIMARY KEY,
    password VARCHAR(255) NOT NULL, -- encrypt this  
                                    -- i dont think it's necessary to encryp this if it's too complicated -Céline
    type_id SMALLINT CHECK(type_id >= 0 AND type_id <= 2),
                    -- type_id 0 -> patient, 1 -> employee, 2 -> employee and patient
    patient_id INTEGER NULL,
    employee_id INTEGER NULL,

    CONSTRAINT FK_patient_id 
        FOREIGN KEY(patient_id) 
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT FK_employee_id 
        FOREIGN KEY(employee_id) 
        REFERENCES Employee(employee_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- ================================  CONSTRAINTS ADDED USING ALTER TABLE  ================================ --
-- NOTE: some constraints need to be added using ALTER TABLE due to circular referencing errors in Postgres

-- Add Branch and Employee_info FK to Employee Table 
ALTER TABLE Employee 
ADD CONSTRAINT FK_branch_id
    FOREIGN KEY (branch_id) REFERENCES Branch(branch_id) 
    ON UPDATE CASCADE ON DELETE CASCADE,
ADD CONSTRAINT FK_employee_sin
    FOREIGN KEY (employee_sin) REFERENCES Employee_info(employee_sin)
    ON UPDATE CASCADE ON DELETE CASCADE;

-- Add Employee FK to Appointment Table
ALTER TABLE Appointment
ADD CONSTRAINT FK_dentist_id
    FOREIGN KEY(dentist_id) REFERENCES Employee(employee_id)
    ON UPDATE CASCADE ON DELETE CASCADE;

-- Add payment type constraint to Patient_billing table
ALTER TABLE Patient_billing
ADD CONSTRAINT Payment_type_check
    CHECK(payment_type IN ('Cash', 'Debit Card', 'Amex', 'Visa', 'Mastercard', 'American Express'));
