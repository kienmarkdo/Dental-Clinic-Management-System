-- Patient Info
INSERT INTO Patient_info VALUES (
    1, 
    '123 Sesame Street', 
    'Elmo', 
    'M', 
    'elmo@elmail.com', 
    '6664206969', 
    TO_DATE('2000-01-01', 'YYYYMMDD'), 
    NULL
);

-- Patient
INSERT INTO Patient VALUES (1,1);

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