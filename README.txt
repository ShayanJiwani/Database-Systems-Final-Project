/* THIS CODE IS MY OWN WORK.
IT WAS WRITTEN WITHOUT CONSULTING CODE WRITTEN BY OTHER STUDENTS.
Shayan Jiwani*/

Server IP: 35.196.82.243

Assumptions:
1. From the evaluations csv: If a student only had part of an evaluation filled
out (i.e. a couple questions were filled in the columns in the csv), then I
assumed that they had not filled out an evaluation and did not include their
partially filled data.
2. For classes that had no evaluations, I assumed that they would have all of
the questions on their evaluation because each specific class/ instructor
may want to ask different questions, even within the same department.
3. I assumed that all classes will have all free-text questions on there, since
the students do not need to respond to a free-response question.
4. For any tuples that were filled out mostly, with only one missing question,
I assumed I could fill it out with whatever satisfied the question.
5. I used Freshman, Sophomore, etc. as my year scale rather than the first,
second, third, etc.
-------
For the php webpages:
1. I assumed that students would know their student IDs.
2. I assumed that teachers would know their instructor IDs.

For opening the PHP webpages:

1. Use "studentForm.php" for inputting a student ID so a student can take a
course evaluation.
2. Use "studentClassForm.php" to allow a student to look up a specific class
with a specific teacher.
3. Use "teacherForm.php" to allow an instructor to see data about a specific
class that they have taught.
4. Use "departmentForm.php" to allow a department chair to select a question
and department (which I assumed was just: CS, MATH, CHEM, etc.) and find
statistics for that question across the entire department.
