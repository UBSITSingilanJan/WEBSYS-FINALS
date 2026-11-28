# WEBSYS-FINALS

## Resident management and applications

For a fresh installation, import `financialaidsystem-v4.sql`. For an existing installation, back up the database and run `migrations/add_resident_application_relationship.sql`, `migrations/allow_applications_without_email.sql`, `migrations/expand_resident_profile.sql`, and `migrations/add_resident_move_in_date.sql`, and `migrations/add_application_document_requirement_label.sql` once each. Existing resident records need their verified move-in date entered through **Manage Residents**. Resident self-registration and worker-created resident records capture the date they moved into the barangay. Workers enter program document requirements one per line; residents can view active programs and upload one file for each listed requirement. Residents become eligible to apply for financial aid after six months; online and worker-created applications are blocked before that date.

## Project structure

- `admin/`, `resident/`, and `worker/` contain role-specific pages.
- `application/` contains the resident application page and shared application-form styles.
- `classes/` contains database-backed application, resident, program, and user logic.
- `migrations/` contains one-time schema updates for existing databases.
- `uploads/applications/` contains legacy application-document files referenced by older database records. Current online application uploads are stored as BLOBs in the `application_document` database table. Do not remove the legacy files unless their database references have been migrated or intentionally discarded.