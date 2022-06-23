use addis_complaint;
CREATE USER 'user'@'localhost';
GRANT SELECT, INSERT,UPDATE,DELETE on users to user@localhost;
GRANT SELECT, INSERT,UPDATE,DELETE on feedbacks to user@localhost;


use addis_complaint;
CREATE USER 'admin'@'localhost';
GRANT SELECT, INSERT,UPDATE,DELETE on users to admin@localhost;
GRANT SELECT, INSERT,UPDATE,DELETE on feedbacks to admin@localhost;
GRANT SELECT, INSERT,UPDATE,DELETE on admin to admin@localhost;

use addis_complaint;
CREATE USER 'superadmin'@'localhost';
GRANT SELECT, INSERT,UPDATE,DELETE on admin to superadmin@localhost;
GRANT SELECT on super_admin to superadmin@localhost;



