# WEBSYS-FINALS

## Resident masterlist and applications

For a fresh installation, import `financialaidsystem-new.sql`. For an existing installation, back up the database and run `migrations/add_resident_application_relationship.sql`, `migrations/allow_applications_without_email.sql`, and `migrations/expand_resident_profile.sql` once each. Resident self-registration, worker-created resident records, and online aid applications use the same full resident profile. Worker accounts can use **Resident Masterlist** to search, encode, and update resident profiles. While adding a resident, a worker can optionally create a pending financial assistance application and upload its supporting documents. This does not create a login account. New online applications are linked to the resident profile associated with the applicant's account. Workers can link legacy applications to existing resident records from **Manage Applications**; applications with no reliable resident match remain unlinked rather than being assigned by name alone.

## Project structure

- `admin/`, `resident/`, and `worker/` contain role-specific pages.
- `application/` contains the resident application page and shared application-form styles.
- `classes/` contains database-backed application, resident, program, and user logic.
- `migrations/` contains one-time schema updates for existing databases.
- `uploads/applications/` contains legacy application-document files referenced by older database records. Current online application uploads are stored as BLOBs in the `application_document` database table. Do not remove the legacy files unless their database references have been migrated or intentionally discarded.