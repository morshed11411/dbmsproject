-- Connect as the SYSTEM user to a specific PDB
CONNECT system/12345@<xepdb1>

-- Create the user in the PDB
CREATE USER ums IDENTIFIED BY 12345;

-- Grant necessary privileges to the user in the PDB
GRANT CONNECT, RESOURCE, DBA TO ums;
