
/* Create the courseEvaluations schema */
CREATE DATABASE courseEvaluations;

/* Specify the database to use */
USE courseEvaluations;

/* Create the student table */
CREATE TABLE student
(
  studentID VARCHAR(16),
  CONSTRAINT studentPK PRIMARY KEY(studentID)
);

/* Create the instructor table */
CREATE TABLE instructor
(
  instructorID CHAR(5),
  fname VARCHAR(20) NOT NULL,
  lname VARCHAR(20) NOT NULL,
  CONSTRAINT instructorIDPK PRIMARY KEY (instructorID)
);

/* Create the class table */
CREATE TABLE class
(
  class_no CHAR(4),
  course_name VARCHAR(20) NOT NULL,
  section_no VARCHAR(2) NOT NULL,
  /* Fall or Spring */
  semester VARCHAR(6) NOT NULL,
  year CHAR(4) NOT NULL,
  instructorID CHAR(5),
  CONSTRAINT classPK PRIMARY KEY(class_no),
  CONSTRAINT classInstructorFK FOREIGN KEY(instructorID) REFERENCES instructor(instructorID)
);

/* Create the studentClass table */
CREATE TABLE studentClass
(
  studentID VARCHAR(16),
  class_no CHAR(4),
  evaluates BOOLEAN NOT NULL,
  CONSTRAINT studentClassPK PRIMARY KEY (studentID, class_no),
  CONSTRAINT studentsFK FOREIGN KEY(studentID) REFERENCES student(studentID),
  CONSTRAINT classNumberFK FOREIGN KEY(class_no) REFERENCES class(class_no)
);

/*Create the question table*/
CREATE TABLE question
(
  questionID CHAR(3),
  question VARCHAR(1000) NOT NULL,
  typeID VARCHAR(3) NOT NULL,
  options VARCHAR(1000) NOT NULL,
  CONSTRAINT questionPK PRIMARY KEY (questionID)
);

/* Create the classQuestions table */
CREATE TABLE classQuestions
(
  class_no CHAR(4),
  questionID CHAR(3),
  CONSTRAINT classQuestionsPK PRIMARY KEY (class_no, questionID),
  CONSTRAINT classFK FOREIGN KEY(class_no) REFERENCES class(class_no),
  CONSTRAINT questionFK FOREIGN KEY(questionID) REFERENCES question(questionID)
);

/* Create the studentResponses table */
CREATE TABLE studentResponses
(
  studentID CHAR(16),
  class_no CHAR(4),
  questionID CHAR(3),
  response VARCHAR(1000),
  CONSTRAINT studentResponsesPK PRIMARY KEY (studentID, class_no, questionID),
  CONSTRAINT studentListFK FOREIGN KEY(studentID) REFERENCES student(studentID),
  CONSTRAINT classResponseFK FOREIGN KEY(class_no) REFERENCES class(class_no),
  CONSTRAINT questionResponseFK FOREIGN KEY(questionID) REFERENCES question(questionID)
);
