-- Patient Info
CREATE TABLE Patient_info (
    patient_sin INTEGER PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    gender CHAR(1) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone TEXT NOT NULL,
    date_of_birth DATE NOT NULL,
    insurance VARCHAR(255) NULL
);

-- Patient
CREATE TABLE Patient(
  patient_id INTEGER PRIMARY KEY,
  sin_info INTEGER,
  CONSTRAINT FK_patient_sin 
    FOREIGN KEY(sin_info) 
    REFERENCES Patient_info(patient_sin)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

-- Patient Records
CREATE TABLE Patient_records (
    record_id INTEGER PRIMARY KEY,
    treatment_details TEXT, --likely that treatment_details exceeds 255 characters
    patient_id INTEGER,
    CONSTRAINT FK_patient_id 
        FOREIGN KEY(patient_id) 
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Invoice
CREATE TABLE Invoice (
    invoice_id INTEGER PRIMARY KEY,
    date_of_issue DATE,
    contact_info VARCHAR(255),
    patient_charge NUMERIC(10,2),
    Insurance_charge NUMERIC(10,2),
    discount NUMERIC(10,2),
    penalty NUMERIC(10,2),
    patient_id INTEGER,
    
    CONSTRAINT FK_patient_id 
        FOREIGN KEY(patient_id) 
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Insurance Claim
CREATE TABLE Insurance_claim (
    claim_id INTEGER PRIMARY KEY,
    patient_sin INTEGER,
    employer_name VARCHAR(255),
    Insurance_company VARCHAR(255),
    plan_number INTEGER,
    coverage NUMERIC(10,2),
    invoice_id INTEGER,
    
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

-- Review
CREATE TABLE Review (
    review_id INTEGER PRIMARY KEY,
    dentist_name VARCHAR(30), --not sure why this is VAR in the diagram
    professionalism INTEGER,
    communication INTEGER, 
    cleanliness INTEGER,
    date_of_review DATE,
    procedure_id INTEGER
    -- don't forget to add a comma before here when uncommenting the next portion
    
    -- CONSTRAINT FK_procedure_id 
    --     FOREIGN KEY(procedure_id) 
    --     REFERENCES Appointment_procedure(procedure_id)
    --     ON UPDATE CASCADE
    --     ON DELETE CASCADE
);

-- Representative
CREATE TABLE Representative (
    name VARCHAR(255) PRIMARY KEY,
    patient_sin INTEGER,
    phone INTEGER,
    relationship VARCHAR(255),
    
    CONSTRAINT FK_patient_sin 
        FOREIGN KEY(patient_sin) 
        REFERENCES Patient_info(patient_sin)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- Patient Billing
CREATE TABLE Patient_billing (
    bill_id INTEGER PRIMARY KEY,
    patient_id INTEGER,
    patient_amount NUMERIC(10, 2),
    insurance_amount NUMERIC(10, 2),
    total_amount NUMERIC(10, 2),
    payment_type VARCHAR(255), -- constrain this?
    
    CONSTRAINT FK_patient_id 
        FOREIGN KEY(patient_id) 
        REFERENCES Patient(patient_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- User Account
CREATE TABLE User_account ( -- user is keyword, changed to User_account 
    username VARCHAR(255) PRIMARY KEY,
    password VARCHAR(255), -- encrypt this
    type_id SMALLINT -- constrain this
);

-- Employee Info
CREATE TABLE Employee_info (
    employee_sin INTEGER PRIMARY KEY,
    employee_type CHAR(1), -- constrain this
    name VARCHAR(255),
    address VARCHAR(255),
    annual_salary NUMERIC(10, 2)
);

-- Employee
CREATE TABLE Employee (
    employee_id INTEGER PRIMARY KEY,
    employee_sin INTEGER,
    branch_id INTEGER
);

-- Branch
CREATE TABLE Branch (
    branch_id INTEGER PRIMARY KEY, -- in diagram is VARCHAR(255)
    city VARCHAR(255),
    manager_id INTEGER,
    receptionist1_id INTEGER,
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

-- Add Branch FK to Employee Table 
ALTER TABLE Employee ADD CONSTRAINT FK_branch_id 
FOREIGN KEY (branch_id) REFERENCES Branch(branch_id) 
ON UPDATE CASCADE ON DELETE CASCADE;
