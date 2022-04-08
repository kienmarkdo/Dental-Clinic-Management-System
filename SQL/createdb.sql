-- Patient Info
CREATE TABLE Patient_info (
    patient_sin INTEGER PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    gender VARCHAR(1) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL, -- type needs to be review with Representative
    date_of_birth DATE NOT NULL,
    insurance VARCHAR(255) NULL,
    
    -- constraints
    CHECK (gender IN ('M','F','X')), -- gender must be M, F or X
    CHECK (date_of_birth <= (CURRENT_DATE - '15 years'::interval)::date) -- age must be 15 or higher
    -- https://stackoverflow.com/questions/59975034 select-all-participants-under-the-age-of-18-using-the-current-date-in-postgresql
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
    treatment_details TEXT NOT NULL, --likely that treatment_details exceeds 255 characters
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
    employer_name VARCHAR(255) NOT NULL,
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
    appointment_status VARCHAR(255) NOT NULL, 
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

-- Appointment Procedure
CREATE TABLE Appointment_procedure (
    procedure_id SERIAL PRIMARY KEY,
    appointment_id INTEGER NOT NULL,
    patient_id INTEGER NOT NULL,
    date_of_procedure DATE NOT NULL, 
    invoice_id INTEGER NOT NULL,
    procedure_code INTEGER NOT NULL,
    procedure_type VARCHAR(255) NOT NULL,
    appointment_description VARCHAR(255) NOT NULL,
    tooth INTEGER NOT NULL,
    amount_of_procedure INTEGER NOT NULL,
    patient_charge NUMERIC(10, 2) NOT NULL,
    insurance_charge NUMERIC(10, 2) NOT NULL,
    total_charge NUMERIC(10, 2) NOT NULL,
    insurance_claim_id INTEGER NOT NULL,

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

-- Representative
CREATE TABLE Representative (
    name VARCHAR(255) PRIMARY KEY,
    patient_sin INTEGER NOT NULL,
    phone VARCHAR(20) NOT NULL,  -- type needs to be review with Patient_info
    relationship VARCHAR(255) NOT NULL, -- i.e.: mother, father, etc. Can be a textbox or selection menu
    
    CONSTRAINT FK_patient_sin 
        FOREIGN KEY(patient_sin) 
        REFERENCES Patient_info(patient_sin)
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
    payment_type VARCHAR(255) NOT NULL, -- constrain this? 
                        -- nah, I wouldn't. We can just make a selection menu (VISA, Mastercard etc.) and check the input
                        -- in the backend before inserting it into the database - Kien

                        -- we added them as one of our constraints tho in deliverable 1
                        -- it was CHECK(payment_type IN ('cash', 'debit card', 'Amex', 'Visa', 'Mastercard'))
                        -- but it's true we can just do a dropdown menu - Céline

    
    CONSTRAINT FK_patient_id 
        FOREIGN KEY(patient_id) 
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- User Account
CREATE TABLE User_account ( -- user is keyword, changed to User_account 
    username VARCHAR(255) PRIMARY KEY,
    password VARCHAR(255) NOT NULL, -- encrypt this  
                                    -- i dont think it's necessary to encryp this if it's too complicated -Céline
    type_id SMALLINT CHECK(type_id >= 0 AND type_id <= 2)
                    -- type_id 0 -> patient, 1 -> employee, 2 -> employee and patient
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
    manager_id INTEGER NOT NULL,
    receptionist1_id INTEGER NOT NULL,
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
    tooth VARCHAR(255) NOT NULL,
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


-- SAMY PROPOSITION FOR REPRESENTATIVE *******************************************************************************************

-- Patient Info
CREATE TABLE Patient_info_ALT (
    patient_sin INTEGER PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    gender VARCHAR(1) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL, -- type needs to be the same as in Representative
    date_of_birth DATE NOT NULL,
    insurance VARCHAR(255) NULL,
    representative_sin INTEGER NOT NULL, --sin will be 0 of there is no representative
    -- constraints
    CHECK (gender IN ('M','F','X')), -- gender must be M, F or X
    CHECK (date_of_birth <= (CURRENT_DATE - '15 years'::interval)::date OR representative_sin <> 0), -- age must be 15 or higher
    -- https://stackoverflow.com/questions/59975034 select-all-participants-under-the-age-of-18-using-the-current-date-in-postgresql
    -- Link to test: https://onecompiler.com/postgresql/3xyfnb8ju
    FOREIGN KEY(representative_sin) REFERENCES Representative(representative_sin) ON UPDATE CASCASE ON DELETE CASCADE
);

-- Representative
CREATE TABLE Representative_ALT (
    representative_sin INTEGER PRIMARY KEY,
    name VARCHAR(255) PRIMARY KEY,
    patient_sin INTEGER NOT NULL,
    phone VARCHAR(20) NOT NULL,  -- type needs to be the same as in Patient_info
    relationship VARCHAR(255) NOT NULL, -- i.e.: mother, father, etc. Can be a textbox or selection menu
    
    CONSTRAINT FK_patient_sin 
        FOREIGN KEY(patient_sin) 
        REFERENCES Patient_info(patient_sin)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);