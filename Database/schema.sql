
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

-- Insert Student
INSERT INTO Users (
    FirstName, LastName, Email, PhoneNumber, Address,
    BirthDate, SIN, Username, Password, Role
) VALUES (
    'Jane', 'Smith', 'janesmith@email.com',
    '987-654-3210', '456 Elm St, Calgary', '1985-08-22',
    '987654321', 'student', 'student123', 'student'
);
SET @student_id = LAST_INSERT_ID();

INSERT INTO Students (StudentID, MajorMinor, GPA, Course_year)
VALUES (@student_id, 'Computer Science', 3.8, 3);

-- Insert Advisor (✅ with unique SIN to avoid conflict)
INSERT INTO Users (
    FirstName, LastName, Email, PhoneNumber, Address,
    BirthDate, SIN, Username, Password, Role
) VALUES (
    'Alex', 'Johnson', 'advisor@email.com',
    '403-555-1212', '789 Maple St', '1979-06-10',
    '123456789', 'advisor', 'advisor123', 'advisor'
);
SET @advisor_id = LAST_INSERT_ID();

INSERT INTO Advisors (AdvisorID, Department, Notes)
VALUES (@advisor_id, 'Computer Science', 'Specializes in AI');

-- Insert Degree
INSERT INTO Degrees (DegreeName, Faculty, CreditsRequired)
VALUES ('BSc Computer Science', 'Science', 120);

-- Insert Courses
INSERT INTO Courses (
    CourseName, CourseCode, Credits, CourseLevel, CourseDesc, Instructor
) VALUES
    ('Introduction to Programming', 'CPSC 231', 3, 200, 'Basic programming concepts', 'Dr. Adams'),
    ('Data Structures', 'CPSC 331', 3, 300, 'Advanced data structures', 'Dr. Brown'),
    ('Operating Systems', 'CPSC 457', 3, 400, 'OS fundamentals', 'Dr. Green');

-- Enroll student
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

-- Update GPA
UPDATE Students SET GPA = 3.9 WHERE StudentID = 1;

-- Update enrollment
UPDATE Enrollment
SET Status = 'Completed', Grade = 'B+'
WHERE EnrollmentID = 2;

-- Clean-up test data
DELETE FROM Enrollment WHERE StudentID = 1 AND CourseID = 1;
DELETE FROM Users WHERE UserID = 10;