events-booking-web based system with php
Summary of the Event Management System
This Event Management System is designed for admins to manage users, events, and bookings efficiently. It provides a dashboard with various widgets to display key statistics and details in real-time. Below is a breakdown of the main features and components:

1. Admin Dashboard
The admin dashboard serves as the central interface for managing and monitoring the system. Key features include:

User Management: Displays the total number of users in the system.
Event Statistics: Shows:
Total Events: The total number of events in the system.
Upcoming Events: Events scheduled for the future.
Available Spots: The remaining spots for events based on capacity and bookings.
Booking Statistics: Includes:
Total Bookings: The total number of bookings made.
Full Capacity Events: Number of events that have reached their full capacity.
Pending Bookings: Number of bookings that are yet to be confirmed or processed.
Recent Bookings: A list of the most recent bookings made on the platform.
Recent Events: Displays the latest events with their descriptions and images.
2. Database Design
The system uses a MySQL database to store the following data:

Users Table: Stores information about users, including roles (admin or regular users).
Events Table: Stores information about events, including event name, description, date, capacity, and associated images.
Bookings Table: Tracks bookings made by users for events, including the number of seats reserved, booking status (pending, confirmed), and the event ID.
3. Widgets and User Interface
The dashboard is built using Bootstrap for a responsive, modern design. Widgets are used to display:

User Count: A circular widget that shows the total number of users.
Event Statistics: Displays the number of total events, upcoming events, and available spots.
Booking Statistics: Shows the total number of bookings, events with full capacity, and pending bookings.
Recent Activities: Displays recent bookings and events to keep the admin informed of the latest actions.
4. Backend Logic
The backend logic in PHP handles data retrieval from the MySQL database and populates the widgets on the dashboard. The system includes SQL queries to:

Count total users with the role of "user."
Count total events, upcoming events, and available spots by joining the events and bookings tables.
Count total bookings, full capacity events, and pending bookings from the bookings table.
Retrieve recent bookings and recent events for display in the widgets.
5. Flash Messages
The system includes functionality to display flash messages at the top of the page (e.g., success or error messages), which disappear after a few seconds.

6. Responsive Design
The system is fully responsive, with the sidebar and content area adjusting based on the screen size. For smaller screens, the sidebar collapses, and the content is displayed in a single-column layout.

7. Security and Authentication
Admin login is required to access the dashboard.
Session management is handled to ensure that only authorized users can view the admin panel.
Authentication is done through either GitHub or direct login credentials.
Purpose and Use
This system is built to simplify the management of events and bookings, offering admins an overview of user activity, event details, and booking trends. It is useful for event organizers or platforms that need to track event performance and user engagement in real time.
