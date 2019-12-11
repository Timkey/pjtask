# pjtask
Netpay PHP and Js Tasks

![Layout](netpay.png)

## File Management
This task contains three sections. The database schema, the backend logic and the web interface for performing the search.

### Database
This first section included the design of a database to store the directory structure. The directory sructure in question is acquired from a text file. dirStructure.txt is the name of the file in this case. dbase.sql is the sql file containing the DDL of the table structure.

### PHP Business logic
To ensure the business logic works, the conf.php is initialized with variables pointing to a mysql database. It could either be a local or public database. Ensure the user defined for the connection can create tables and insert data on them.

There are two classes built in the conn.php file. Bridge is the class that interfaces with the database directly while the FileDirectoryMap manages the logging and reading of the directory structure. In test.php the Bridge class is instantiated to generate the table structure if it doesn't already exist. The FileDirectoryMap instance is an interface that can recieve a list of paths through the logContents() method for insertion into the database should the path not already exist. Another method is the searchContent() method that will recurceively generate a list of paths containing the search parameter word.

The test.php can be executed from the terminal using "php test.php". Uncomment the $cnt->logContent('db') instance to generate a list of your choosing in the database of your choosing.

### web interface
To run and see the results of this project, deploy this repository inside the web root of the Apache server. It could be htdocs for Windows and /var/www/html folder on Linux.
The search interface is based on html and JavaScript. Interaction with the database is performed over an api as defined on api.php.
The web interface in question is defined in index.html. The same interface also includes the second task which is discussed in the next section, Form Validation.
  

## Form Validation
As long as the Apache server is running, this app can be reached over the IP address of the host server or local host address. The results of this task are saved in a text file called store.json. Ensure this file is writable by the Apache server user. On Linux "chmod 777 store.json" does it.
The layout of the app uses bootstrap without any custom css. Index.html contains all the markup code for both tasks. The main JavaScript libraries, especially those shared by both tasks are stored under js/xlock.js . t1.js contains the js code for task one and t2.js for task two.

The backend of the second task is defined in form.php. This file contains two classes. The Store class and validation class.
