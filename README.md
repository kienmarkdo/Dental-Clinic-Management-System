# Overview

This Dental Clinic Management System (DCMS) provides an efficient and fast way to manage appointments and related activities for a dental center with clinics in major cities across Canada. In addition, this DCMS will enable the dental center practitioners to track records, minimize data loss, provide privacy & security of records, enable quick report generation, implement updates, eliminate redundant paper work, and save time.

- Dental Clinic website: https://dental-clinic-mgmt-service.herokuapp.com/index.php
- Original repository: https://github.com/CSI2532-Databases-I-Group-8/Dental-Clinic-Management-System

# Implementation

All components of this application are implemented from scratch which includes the ER model, the relational schema, populated relations, database queries, as well as the user interface. The DCMS is hosted on Heroku.



# Database Structure

## User Types
- Administrator
  - The administrator can create, view, edit, or delete all information within the system.
- Employee
  - Details of the treatment rendered must be recorded in the patient’s chart progress notes by an employee.
  - Every employee needs to be identified by some basic information (e.g. given names, address, role, employee type, SSN, and salary).
  - Receptionists
    - Receptionists are able to add patient information, edit patient information, set patient appointments.
  - Dentists/Hygienists
    - Dentists and hygienists can retrieve the records of appointed patients and track the patients' data (e.g. check medical history before administering new procedures).
  - Branch manager
    - Manages employees within a branch of the clinic
- Patients
  - Patients can access their records (e.g. medical history, upcoming appointments or schedule with the dentists).
  - The patient's address, name, gender, insurance, SSN, email address, date of birth, phone numbers are stored.
  - Can also be an employee (e.g., dentist, hygienist or receptionist).
  - Should be able to book more than one procedure. 
  - Must be 15 years old or above to be registered. Otherwise, a parent or a responsible party should be responsible for the patient. 
  - Each responsible party should be a registered user. A responsible party or parent is not required to be a patient.

## Clinic structure
- Branches
  - The dental clinic enterprise is organized into branches, represented by the city in which the clinics are located.
  - Employees within a branch are managed by a branch manager, who is also an employee.
  - Each branch of the clinic can have many dentists and hygienists, but cannot have more than two receptionists.
- Appointment
  - Appointment bookings are captured by the system after a patient is registered.
  - Displays the patient, dentist identifier, date, start time, end time, appointment type, status (no show, canceled, completed, unschedule), and room assigned.
  - Each patient and dentist may have zero or more appointments.
- Appointment Procedure
  - Captured information includes: appointment id, patient id, invoice id, procedure code, procedure type, description, tooth involved, amount of procedure, patient charge, insurance charge, total charge, insurance claim id, etc.
  - Patients who cancel an appointment within 24 hours notice or do not show up are penalized.
- Treatment
  - Appropriate treatment is provided after a diagnosis by the dentist, and is based on the patient's condition.
  - Captured information include: appointment type, treatment type, medication, symptoms, tooth, comments, etc.
- Fee Charge
  - Information displayed are the fee id, procedure id, fee code, charge.
  - Fees are charged for all procedures provided at the clinics.
- Invoices
  - Information include patient id, date of issue, contact information, patient charge, insurance charge, total fee charge, discount, penalty, insurance claim id, etc.
  - An invoice can contain many appointments.
  - Fee charged for employee services are 50% of the professional fee.
  - The invoice may be partly billed to the patient, and the remaining value sent to the insurance company.
- Patient Billing/Payment 
  - Contains information related to a patient’s visit or an appointment.
  - Captures the bill id, patient id, appointment id, procedure id, patient amount, insurance amount, total amount, insurance claim id, payment type, etc.
  - Patients are expected to pay for services on the day the service is completed.
  - Payments can be made through cash, debit card, Amex, Visa or Mastercard payment types.
  - Multiple payment types can be used to pay for the invoice.
  - An employee can pay for the procedure of many patients. 
- Insurance Claim
  - Patients can submit electronic insurance claims, which should be applied to the cost of the treatment.
- Reviews 
  - The dental clinic enterprise also needs to keep track of the reviews from the patients. 
  - Information that needs to be stored includes professionalism of employees, communication, cleanliness and value.


