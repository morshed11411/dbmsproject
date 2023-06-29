Project Name: Unit Management System (UMS)
Description: The Unit Management System is a comprehensive web application specifically designed for use in Bangladesh Army units. It serves as a centralized platform for managing various aspects of soldier administration and unit operations. The UMS offers a user-friendly interface and a range of features to facilitate efficient soldier management, enhance communication, and streamline administrative tasks within army units.

Key Features:
1. Soldier Management: The system allows for the creation, modification, and deletion of soldier profiles. It captures essential details such as personal information, rank, trade, appointment, team assignment, and serving status.

2. Appointment Management: Soldiers can be assigned to different appointments, such as Duty Officer, Duty JCO, Duty NCO, and more. The system enables the easy assignment and change of appointments, ensuring proper organization and coordination within the unit.

3. Team Management: Unit teams can be created and managed, providing a structured approach to team assignments, coordination, and communication. The system allows for the creation, modification, and deletion of teams, and facilitates team member assignments.

4. Medical Disposal Management: The UMS includes a feature to record and track medical disposals for soldiers. It ensures that disposal dates do not overlap, maintaining accurate records and accountability.

5. Training Management: Soldier training records, including basic and advanced training, can be tracked and updated. The system captures training details such as training start and end dates, training officers in charge, and training results.

6. Contact Number Management: Contact numbers of soldiers can be stored and managed within the system. This feature provides quick access to important contact information, facilitating effective communication within the unit.

7. Punishment Record Management: The system allows for the recording and maintenance of punishment records for soldiers. This feature helps track disciplinary actions and associated details, ensuring proper documentation and accountability.

8. Authorization Management: The UMS includes features to manage authorizations for companies. It enables the storage and management of company-related information, ensuring appropriate access and permissions within the system.

9. User Authentication and Session Management: The application incorporates secure user authentication and session management features. Only authorized users can log in and access the system, ensuring data confidentiality and system integrity. The session management feature stores soldier ID and name for convenient access and reference throughout the application.

The Unit Management System (UMS) provides a powerful tool for efficient soldier management, streamlined unit operations, and enhanced communication within Bangladesh Army units. It aims to improve administrative processes, facilitate decision-making, and promote effective coordination and organization within the military units.


Based on our conversation, here are the features of your application:

1. Soldier Management:
   - Create, update, and delete soldier records.
   - View detailed information about a specific soldier, including appointments, teams, contact numbers, punishment records, etc.
   - Search for soldiers based on various criteria, such as trade, rank, company, overweight status, training status, etc.

2. Dashboard:
   - Display summary information about the application, such as total soldiers, present soldiers, teams, medical disposal holders, etc.
   - Show notifications for special cases, such as soldiers on AWOL status.

3. Soldier Appointments:
   - Assign and manage appointments for soldiers, such as Duty Officer, Duty JCO, Duty NCO, etc.
   - Allow for changing soldier appointments through the application.

4. Team Management:
   - Create, update, and delete teams.
   - Assign soldiers to teams and manage team details, including team name, start/end date, and OIC.

5. Medical Disposal Management:
   - Add and manage medical disposal records for soldiers.
   - Ensure that medical disposal dates do not overlap for a soldier.

6. Soldier Training:
   - Track and manage soldier training records, including basic training and advanced training.
   - Maintain training details, such as training start/end dates, training OIC, and training results.

7. Contact Number Management:
   - Store and manage contact numbers for soldiers.

8. Punishment Records:
   - Record and manage punishment records for soldiers.

9. Authorization and Company Management:
   - Manage authorization details for companies.
   - Store company information.

10. User Authentication and Session Management:
    - Authenticate users and manage user sessions using PHP session handling.
    - Store soldier ID and name in the session for easy access across the application.



1. Database Tables:
   - Soldier: Contains information about soldiers, including their ID, name, rank, trade, company, etc.
   - Punishment: Stores punishment records for soldiers.
   - Team: Holds information about teams, including team ID, name, start/end date, and OIC.
   - ContactNumber: Stores contact numbers of soldiers.
   - Trade: Stores trade information.
   - Authorization: Stores authorization details for companies.
   - Company: Contains company information.
   - MedicalInfo: Stores medical disposal information for soldiers.
   - Ranks: Stores rank information.
   - Appointments: Stores appointment details.
   - LeaveModule: Stores leave information for soldiers.
   - CarrierPlan: Contains career plan details for soldiers.
   - AdvanceTraining: Stores advance training information.
   - SoldierBasicTraining: Holds basic training information for soldiers.
   - BasicTraining: Stores basic training details.
   - SoldierAppointment: Maps soldiers to their appointments.
   - SoldierTeam: Maps soldiers to their teams.

2. Views:
   - OverweightSoldiersView: Retrieves overweight soldier information, including soldier ID, rank, name, company, and pounds overweight.

3. Triggers:
   - check_medical_disposal_trigger: Prevents overlapping medical disposal dates for a soldier.
   - check_temp_ere_trigger: Ensures that a soldier cannot be in temporary command and ERE at the same time.

4. Frontend Pages:
   - Dashboard: Displays various summary cards and notifications for soldier-related information, such as total soldiers, present soldiers, AWOL soldiers, teams, medical disposal holders, etc.
   - Soldier Details Page: Shows detailed information about a specific soldier, including their appointments, team, contact number, punishment records, etc.
   - Soldier Search Page: Allows searching for soldiers based on multiple filters such as trade, rank, company, overweight status, training status, etc.

5. Frontend Components:
   - Cards: Used to display summary information, such as total soldiers, present soldiers, teams, medical disposal holders, etc. Each card is clickable and links to the respective management page.
   - Tables: Display soldier information, such as overweight soldiers, punishment records, etc. The tables are scrollable and responsive.

6. Session Handling:
   - The soldier's ID and name are stored in the session after authentication.
