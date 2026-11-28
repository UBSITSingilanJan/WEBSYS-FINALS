ALTER TABLE application_document
ADD COLUMN requirement_label VARCHAR(150) NOT NULL DEFAULT 'Supporting Document' AFTER applicant_id;
