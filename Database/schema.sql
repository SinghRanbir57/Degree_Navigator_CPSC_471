-- ========================
-- USERS & ROLES
-- ========================
CREATE TABLE Users (
    UserID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(255) NOT NULL,
    LastName VARCHAR(255) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    PhoneNumber VARCHAR(255),
    Address VARCHAR(255),
    BirthDate DATE,
    SIN VARCHAR(255) UNIQUE,
    Username VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('student', 'advisor') NOT NULL
);

CREATE TABLE Students (
    StudentID INT NOT NULL PRIMARY KEY,
    MajorMinor VARCHAR(255) NOT NULL,
    GPA DECIMAL(3, 2) DEFAULT 0.00,
    Course_year INT NOT NULL,
    FOREIGN KEY (StudentID) REFERENCES Users(UserID) ON DELETE CASCADE
);

CREATE TABLE Advisors (
    AdvisorID INT NOT NULL PRIMARY KEY,
    Department VARCHAR(255),
    Notes TEXT,
    FOREIGN KEY (AdvisorID) REFERENCES Users(UserID) ON DELETE CASCADE
);

CREATE TABLE Advisees (
    AdvisorID INT NOT NULL,
    StudentID INT NOT NULL,
    PRIMARY KEY (AdvisorID, StudentID),
    FOREIGN KEY (AdvisorID) REFERENCES Advisors(AdvisorID) ON DELETE CASCADE,
    FOREIGN KEY (StudentID) REFERENCES Students(StudentID) ON DELETE CASCADE
);


-- ========================
-- DEGREE, COURSES, ENROLLMENT
-- ========================
CREATE TABLE Degrees (
    DegreeID INT AUTO_INCREMENT,
    DegreeName VARCHAR(255) NOT NULL,
    Faculty VARCHAR(255),
    CreditsRequired INT NOT NULL,
    PRIMARY KEY (DegreeID)
);

CREATE TABLE Courses (
    CourseID INT AUTO_INCREMENT PRIMARY KEY,
    CourseName VARCHAR(255) NOT NULL,
    CourseCode VARCHAR(255) UNIQUE NOT NULL,
    Credits INT NOT NULL,
    CourseLevel INT NOT NULL,
    CourseDesc TEXT,
    Instructor VARCHAR(255),
    PrerequisiteID INT NULL,
    FOREIGN KEY (PrerequisiteID) REFERENCES Courses(CourseID) ON DELETE SET NULL
);

CREATE TABLE Enrollment (
    EnrollmentID INT AUTO_INCREMENT,
    StudentID INT NOT NULL,
    CourseID INT NOT NULL,
    Status ENUM('Completed', 'In Progress', 'Not Taken') DEFAULT 'Not Taken',
    Grade VARCHAR(255) NULL,
    PRIMARY KEY (EnrollmentID),
    FOREIGN KEY (StudentID) REFERENCES Students(StudentID) ON DELETE CASCADE,
    FOREIGN KEY (CourseID) REFERENCES Courses(CourseID) ON DELETE CASCADE
);

CREATE TABLE Requirements (
    RequirementID INT AUTO_INCREMENT,
    DegreeID INT NOT NULL,
    CourseID INT NOT NULL,
    RequirementType ENUM('Core', 'Elective'),
    RequirementDesc TEXT,
    PRIMARY KEY (RequirementID),
    FOREIGN KEY (DegreeID) REFERENCES Degrees(DegreeID) ON DELETE CASCADE,
    FOREIGN KEY (CourseID) REFERENCES Courses(CourseID) ON DELETE CASCADE
);

CREATE TABLE Progress (
    ProgressID INT AUTO_INCREMENT,
    StudentID INT NOT NULL,
    CompletedCourses INT DEFAULT 0,
    InProgressCourses INT DEFAULT 0,
    RemainingCourses INT DEFAULT 0,
    PRIMARY KEY (ProgressID),
    FOREIGN KEY (StudentID) REFERENCES Students(StudentID) ON DELETE CASCADE
);

CREATE TABLE Semesters (
    SemesterID INT AUTO_INCREMENT,
    Year INT NOT NULL,
    Term ENUM('Fall', 'Winter', 'Spring', 'Summer') NOT NULL,
    PRIMARY KEY (SemesterID)
);

CREATE TABLE DegreePlan (
    PlanID INT AUTO_INCREMENT,
    StudentID INT NOT NULL,
    SemesterID INT NOT NULL,
    PRIMARY KEY (PlanID),
    FOREIGN KEY (StudentID) REFERENCES Students(StudentID) ON DELETE CASCADE,
    FOREIGN KEY (SemesterID) REFERENCES Semesters(SemesterID) ON DELETE CASCADE
);

CREATE TABLE DegreePlanCourses (
    PlanCourseID INT AUTO_INCREMENT PRIMARY KEY,
    PlanID       INT NOT NULL,
    CourseID     INT NOT NULL,
    FOREIGN KEY (PlanID)   REFERENCES DegreePlan(PlanID)   ON DELETE CASCADE,
    FOREIGN KEY (CourseID) REFERENCES Courses(CourseID)     ON DELETE RESTRICT
);

CREATE TABLE Meetings (
  id           VARCHAR(16)    PRIMARY KEY,
  advisorId    INT            NOT NULL,
  studentId    INT            NOT NULL,
  studentName  VARCHAR(255),
  date         DATE           NOT NULL,
  time         TIME           NOT NULL,
  status       ENUM('pending','accepted','declined')
                   NOT NULL   DEFAULT 'pending',
  FOREIGN KEY (advisorId)  REFERENCES Users(UserID),
  FOREIGN KEY (studentId)  REFERENCES Users(UserID)
);

-- ========================
-- SEED DATA
-- ========================
-- Insert Advisors
INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES 
('Alex', 'Johnson', 'advisor@email.com', '403-555-1212', '789 Maple St', '1979-06-10', '123456789', 'advisor', 'advisor123', 'advisor');
SET @advisor_id = LAST_INSERT_ID();

INSERT INTO Advisors (AdvisorID, Department, Notes)
VALUES (@advisor_id, 'Computer Science', 'Specializes in AI');

INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES 
('Emily', 'Wong', 'ewong@email.com', '403-555-3333', '891 Mountain Ave, Calgary', '1982-01-20', '741852963', 'ewong', 'advisorpass', 'advisor');
SET @advisor_id_2 = LAST_INSERT_ID();

INSERT INTO Advisors (AdvisorID, Department, Notes)
VALUES (@advisor_id_2, 'Electrical & Software Engineering', 'Cybersecurity specialist');

-- Insert Students
INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES 
('Jane', 'Smith', 'janesmith@email.com', '987-654-3210', '456 Elm St, Calgary', '1985-08-22', '987654321', 'jane2024', 'janepass', 'student');
SET @student_id = LAST_INSERT_ID();

INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@student_id, 'Computer Science', 3.8, 3);

INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES 
('David', 'Nguyen', 'davidnguyen@email.com', '403-555-2222', '123 Foothills Blvd, Calgary', '2003-02-11', '456789123', 'david2025', 'studentpass', 'student');
SET @student_id_2 = LAST_INSERT_ID();

INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@student_id_2, 'Software Engineering', 3.6, 2);


-- === New Students for Alex Johnson (AdvisorID = 3) ===
INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES 
('Liam', 'Turner', 'liamturner@email.com', '403-555-4444', '321 Oak St', '2001-11-05', '159357258', 'liamt', 'liam123', 'student');
SET @s1 = LAST_INSERT_ID();

INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@s1, 'Mathematics / Statistics', 3.7, 4);

INSERT INTO Advisees (AdvisorID, StudentID)
VALUES (3, @s1);

INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES 
('Sophia', 'Lee', 'sophialee@email.com', '403-555-5555', '456 River Dr', '2002-03-19', '456123789', 'sophial', 'sophia456', 'student');
SET @s2 = LAST_INSERT_ID();

INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@s2, 'Data Science / Philosophy', 3.9, 3);

INSERT INTO Advisees (AdvisorID, StudentID)
VALUES (3, @s2);

-- === New Students for Emily Wong (AdvisorID = 4) ===
INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES 
('Noah', 'Patel', 'noahpatel@email.com', '403-555-6666', '678 Sunridge Blvd', '2001-08-30', '852963741', 'noahp', 'noahpass', 'student');
SET @s3 = LAST_INSERT_ID();

INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@s3, 'Electrical Engineering / Robotics', 3.5, 2);

INSERT INTO Advisees (AdvisorID, StudentID)
VALUES (4, @s3);

INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES 
('Ava', 'Chen', 'avachen@email.com', '403-555-7777', '123 Lakeview Cres', '2000-12-25', '321654987', 'avac', 'avapass', 'student');
SET @s4 = LAST_INSERT_ID();

INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@s4, 'Software Engineering / Business', 3.85, 4);

INSERT INTO Advisees (AdvisorID, StudentID)
VALUES (4, @s4);

-- Student 1
INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES ('Ethan', 'Zhao', 'ethanzhao@email.com', '403-555-8888', '234 Birch St, Calgary', '2002-04-12', '789123456', 'ethanz', 'ethanpass', 'student');
SET @s5 = LAST_INSERT_ID();
INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@s5, 'Electrical Engineering / AI', 3.75, 3);
INSERT INTO Advisees (AdvisorID, StudentID) VALUES (4, @s5);

-- Student 2
INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES ('Maya', 'Singh', 'mayasingh@email.com', '403-555-9999', '678 Willow Way, Calgary', '2003-05-18', '963852741', 'mayas', 'mayapass', 'student');
SET @s6 = LAST_INSERT_ID();
INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@s6, 'Software Engineering / Psychology', 3.65, 2);
INSERT INTO Advisees (AdvisorID, StudentID) VALUES (4, @s6);

-- Student 3
INSERT INTO Users (FirstName, LastName, Email, PhoneNumber, Address, BirthDate, SIN, Username, Password, Role)
VALUES ('Leo', 'Martinez', 'leom@email.com', '403-555-1010', '101 Pine Ct, Calgary', '2001-09-03', '951753852', 'leom', 'leopass', 'student');
SET @s7 = LAST_INSERT_ID();
INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@s7, 'Computer Engineering / Economics', 3.92, 4);
INSERT INTO Advisees (AdvisorID, StudentID) VALUES (4, @s7);


-- Insert Degree and Courses
INSERT INTO Degrees (DegreeName, Faculty, CreditsRequired)
VALUES ('BSc Computer Science', 'Science', 120);

INSERT INTO Courses (CourseName, CourseCode, Credits, CourseLevel, CourseDesc, Instructor)
VALUES
('Introduction to Programming', 'CPSC 231', 3, 200, 'Basic programming concepts', 'Dr. Adams'),
('Data Structures', 'CPSC 331', 3, 300, 'Advanced data structures', 'Dr. Brown'),
('Operating Systems', 'CPSC 457', 3, 400, 'OS fundamentals', 'Dr. Green');

-- Enrollments
INSERT IGNORE INTO Enrollment (StudentID, CourseID, Status)
VALUES (1, 1, 'In Progress');

-- ========================
-- SAMPLE QUERIES & UPDATES
-- ========================
SELECT u.FirstName, u.LastName, p.CompletedCourses, p.InProgressCourses, p.RemainingCourses
FROM Users u
JOIN Progress p ON u.UserID = p.StudentID
WHERE u.UserID = 1;

SELECT c.CourseName, c.CourseCode, r.RequirementType
FROM Requirements r
JOIN Courses c ON r.CourseID = c.CourseID
WHERE r.DegreeID = 1;


-- Associate Students with Advisors (using IDs retrieved earlier)
INSERT INTO Advisees (AdvisorID, StudentID)
VALUES
(@advisor_id, 1),       -- Alex Johnson advises Jane Smith
(@advisor_id_2, 2);     -- Emily Wong advises David Nguyen

-- Update GPA
UPDATE Students SET GPA = 3.9 WHERE StudentID = 1;

-- Update enrollment
UPDATE Enrollment
SET Status = 'Completed', Grade = 'B+'
WHERE EnrollmentID = 2;

-- Clean-up test data
DELETE FROM Enrollment WHERE StudentID = 1 AND CourseID = 1;
DELETE FROM Users WHERE UserID = 10;
