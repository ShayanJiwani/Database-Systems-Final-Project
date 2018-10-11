<!DOCTYPE html>
<html lang="en">
   <head>
     <!-- Required meta tags -->
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
     </head>
     <body>
     <!-- use the div container -->
     <div class="container">
     <h1>
        Class Statistics
     </h1>
     <P>
      <?php
        $conn = mysqli_connect("localhost","cs377",
        "cs377_s18", "courseEvaluations");
         if (mysqli_connect_errno()){
           printf("Connect failed: %s\n", mysqli_connect_error());
           exit(1);
         }
         $course_name = $_POST['course_name'];
         $instructorFname = $_POST['fname'];
         $instructorLname = $_POST['lname'];

         //query to get the count of strongly agree or agree for each question for a teacher's class
         $query = "SELECT q.questionID, q.question, COUNT(s.response) AS count
         FROM question q, studentResponses s, instructor i, class c WHERE i.fname = '$instructorFname'
         AND i.lname = '$instructorLname' AND c.course_name = '$course_name' AND
         (s.response = 'Strongly Agree' OR s.response = 'Agree')
         AND q.typeID = 'ad' AND i.instructorID = c.instructorID AND c.class_no = s.class_no
         AND q.questionID = s.questionID GROUP BY s.questionID";

         if ( ! ( $result = mysqli_query($conn, $query)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         //query gets the total number of each response for each question
         $query2 = "SELECT q2.question, q2.questionID, COUNT(q2.questionID) AS total
         FROM question q2, studentResponses s2, instructor i2, class c2
         WHERE i2.fname = '$instructorFname' AND i2.lname = '$instructorLname' AND c2.course_name = '$course_name'
         AND i2.instructorID = c2.instructorID AND q2.typeID = 'ad' AND c2.class_no = s2.class_no
         AND q2.questionID = s2.questionID GROUP BY s2.questionID";
         if ( ! ( $result2 = mysqli_query($conn, $query2)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }

         $query3 = "SELECT q.question AS Question, AVG(response) AS Average FROM question q, studentResponses s,
         (SELECT class_no FROM instructor, class WHERE fname = '$instructorFname' AND lname = '$instructorLname'
         AND course_name = '$course_name' AND instructor.instructorID = class.instructorID) cn
         WHERE q.typeID = 'ott' AND cn.class_no = s.class_no AND q.questionID = s.questionID GROUP BY q.questionID";

         if ( ! ( $result3 = mysqli_query($conn, $query3)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         //Finds count for each response for each multiple choice question
         $query4 = "SELECT q.questionID, q.question, s.response, COUNT(s.response) AS count
         FROM question q, studentResponses s, instructor i, class c WHERE i.fname = '$instructorFname'
         AND i.lname = '$instructorLname' AND c.course_name = '$course_name' AND q.typeID = 'mc'
         And i.instructorID = c.instructorID AND c.class_no = s.class_no AND q.questionID = s.questionID
         GROUP BY s.questionID, s.response";

         if ( ! ( $result4 = mysqli_query($conn, $query4)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         //Finds total responses for each multiple choice question
         $query5 = "SELECT q.questionID, q.question, COUNT(s.response) AS total
         FROM question q, studentResponses s, instructor i, class c
         WHERE i.fname = '$instructorFname' AND i.lname = '$instructorLname' AND c.course_name = '$course_name'
         AND q.typeID = 'mc' AND i.instructorID = c.instructorID AND c.class_no = s.class_no
         AND q.questionID = s.questionID GROUP BY s.questionID";

         if ( ! ( $result5 = mysqli_query($conn, $query5)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         if ((mysqli_num_rows($result)) == 0 && (mysqli_num_rows($result2)) == 0 &&
             (mysqli_num_rows($result3)) == 0 && (mysqli_num_rows($result4)) == 0 &&
             (mysqli_num_rows($result5)) == 0) {
           exit("This class has yet to be evaluated or some information was incorrect. Please try again.");
         }
         # create a new paragraph
         print("<h5> Professor: " . $instructorFname . " " . $instructorLname . "</h5>");
         print("<h5> Course: " . $course_name. "</h5>");
         print("<p><br>\n");

         //Creating the table for the breakdown for rate 1-10 questions
         print("<p>\n");
         print("<table class=\"table table-striped\">\n");
         # write the contents of the table
         $header = false;
         while ($row = mysqli_fetch_assoc($result3)){
           if (!$header) {
              $header = true;
              # specify the header to be dark class
              print("<thead class=\"thead-dark\"><tr>\n");
              foreach ($row as $key => $value) {
                 print "<th>" . $key . "</th>";             // Print attr. name
              }
              print("</tr></thead>\n");
           }
           print("<tr>\n");     # Start row of HTML table
           foreach ($row as $key => $value) {
              if ($key == "Average")
                $value = round($value, 2);
              print ("<td>" . $value . "</td>"); # One item in row
           }
           print ("</tr>\n");   # End row of HTML table
         }
         print("</table>\n");
         print("<p><br><br><br>\n");

         //Creating the table for the breakdown for agree/disagree questions
         print("<p>\n");
         print("<table class=\"table table-striped\">\n");
         # write the contents of the table
         $row2 = mysqli_fetch_assoc($result);
         $header = false;
         while ($row = mysqli_fetch_assoc($result2)){
           if (!$header) {
              $header = true;
              # specify the header to be dark class
              print("<thead class=\"thead-dark\"><tr>\n");
              print "<th>Question</th>";
              print "<th>Number of students who agree</th>";
              print "<th>Total Responses</th>";
              print "<th>Percentage of students who agreed</th>";
              print("</tr></thead>\n");
           }
           print("<tr>\n");     # Start row of HTML table
           if (!is_null($row2["questionID"]) && $row["questionID"] == $row2["questionID"]) {
             print ("<td>" . $row["question"] . "</td>");
             print ("<td>" . $row2["count"] . "</td>");
             print ("<td>" . $row["total"] . "</td>");
             print ("<td>" . round((100*$row2["count"]/$row["total"]),2) . "%</td>");
             $row2 = mysqli_fetch_assoc($result);
           }
           else {
             print ("<td>" . $row["question"] . "</td>");
             print ("<td>0</td>");
             print ("<td>" . $row["total"] . "</td>");
             print ("<td>0%</td>");
           }
           print ("</tr>\n");   # End row of HTML table
         }

         print("</table>\n");
         print("<p><br><br><br>\n");

         //Creating the table for the breakdown for multiple choice questions
         print("<p>\n");
         print("<table class=\"table table-striped\">\n");
         # write the contents of the table
         $row2 = mysqli_fetch_assoc($result5);
         $header = false;
         while ($row = mysqli_fetch_assoc($result4)){
           if (!$header) {
              $header = true;
              # specify the header to be dark class
              print("<thead class=\"thead-dark\"><tr>\n");
              print "<th>Question</th>";
              print "<th>Response</th>";
              print "<th>Count of Responses</th>";
              print "<th>Total Responses for Question</th>";
              print "<th>Percentage</th>";
              print("</tr></thead>\n");
           }
           print("<tr>\n");     # Start row of HTML table
           if (!is_null($row2["questionID"]) && $row["questionID"] == $row2["questionID"]) {
             print ("<td>" . $row["question"] . "</td>");
             print ("<td>" . $row["response"] . "</td>");
             print ("<td>" . $row["count"] . "</td>");
             print ("<td>" . $row2["total"] . "</td>");
             print ("<td>" . round((100*$row["count"]/$row2["total"]),2) . "%</td>");
           }
           else {
             $row2 = mysqli_fetch_assoc($result5);
             print ("<td>" . $row["question"] . "</td>");
             print ("<td>" . $row["response"] . "</td>");
             print ("<td>" . $row["count"] . "</td>");
             print ("<td>" . $row2["total"] . "</td>");
             print ("<td>" . round((100*$row["count"]/$row2["total"]),2) . "%</td>");
           }
           print ("</tr>\n");   # End row of HTML table
         }

         print("</table>\n");
         print("<p><br><br><br>\n");


         mysqli_free_result($result);
         mysqli_free_result($result2);
         mysqli_close($conn);
      ?>
      <P>
   </body>
</html>
