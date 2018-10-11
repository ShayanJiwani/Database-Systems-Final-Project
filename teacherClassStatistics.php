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
        Class Statistics for Instructor
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
         $semester = $_POST['semester'];
         $year = $_POST['year'];
         $instructorID = $_POST['instructorID'];

         //gets breakdown of a/d questions for each response (count)
         $query = "SELECT q.question, s.response, COUNT(s.response) AS count
         FROM question q, studentResponses s, class c WHERE
         q.typeID = 'ad' AND c.semester = '$semester' AND c.year = '$year'
         AND c.course_name = '$course_name' AND q.questionID = s.questionID
         AND c.class_no = s.class_no GROUP BY s.questionID, s.response";

         if ( ! ( $result = mysqli_query($conn, $query)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         //find count for each 1-10 rating
         $query2 = "SELECT q.question, s.response, COUNT(s.response) AS count
         FROM question q, studentResponses s, class c WHERE
         q.typeID = 'ott'AND c.semester = '$semester' AND c.year = '$year'
         AND c.course_name = '$course_name' AND q.questionID = s.questionID
         AND c.class_no = s.class_no GROUP BY s.questionID, s.response";

         if ( ! ( $result2 = mysqli_query($conn, $query2)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         //Finds count for each response for each multiple choice question
         $query3 = "SELECT q.question, s.response, COUNT(s.response) AS count
         FROM question q, studentResponses s, class c WHERE
         q.typeID = 'mc' AND c.semester = '$semester' AND c.year = '$year'
         AND c.course_name = '$course_name' AND q.questionID = s.questionID
         AND c.class_no = s.class_no GROUP BY s.questionID, s.response";

         if ( ! ( $result3 = mysqli_query($conn, $query3)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         //Gets information for all text based questions
         $query4 = "SELECT q.question, s.response
         FROM question q, studentResponses s, class c WHERE
         q.typeID = 'ftr' AND c.semester = '$semester'
         AND c.year = '$year' AND c.course_name = '$course_name'
         AND s.response != '' AND q.questionID = s.questionID AND c.class_no = s.class_no ";

         if ( ! ( $result4 = mysqli_query($conn, $query4)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         if ((mysqli_num_rows($result) == 0) && (mysqli_num_rows($result2) == 0) &&
             (mysqli_num_rows($result2) == 0) && (mysqli_num_rows($result3) == 0)) {
           exit("This class has yet to be evaluated or some information was incorrect. Please search for another class.");
         }
         # create a new paragraph
         print("<h5> Instructor ID: " . $instructorID . "</h5>");
         print("<h5> Course: " . $course_name . "</h5>");
         print("<h5> Year: " . $year . "</h5>");
         print("<h5> Semester: " . $semester . "</h5>");
         print("<p><br>\n");

         //Creating the table for the breakdown for rate 1-10 questions
         print("<p>\n");
         print("<table class=\"table table-striped\">\n");
         # write the contents of the table
         $header = false;
         $countElements = 0;
         $questionTrack;
         $arrayOfQuestions;
         $medianArray;
         $countRows = mysqli_num_rows($result2);
         while ($row = mysqli_fetch_assoc($result2)){
           $countRows--;
           if (!$header) {
              $header = true;
              # specify the header to be dark class
              print("<thead class=\"thead-dark\"><tr>\n");
              print "<th>Question</th>";
              print "<th>Response</th>";
              print "<th>Number of Responses</th>";
              print("</tr></thead>\n");
           }
           //set the first question
           if ($countElements == 0) {
             $questionTrack = $row["question"];
           }
           //the question has switched so we're done with the responses for a question
           if ($row["question"] != $questionTrack) {
             sort($arrayOfQuestions);
             $count = count($arrayOfQuestions);
             //sets the question as the key and the median as the value
             if ($count % 2 == 0) {
               $medianArray[$questionTrack] = ($arrayOfQuestions[$count/2] + $arrayOfQuestions[$count/2 - 1]) / 2.0;
             }
             else {
               $medianArray[$questionTrack] = $arrayOfQuestions[floor($count/2)];
             }
             $countElements = 0;
             $questionTrack = $row["question"];
             $arrayOfQuestions = "";
           }
           print("<tr>\n");     # Start row of HTML table
           foreach ($row as $key => $value) {
              print ("<td>" . $value . "</td>"); # One item in row
           }
           for($i = 0; $i < $row["count"]; $i++) {
             $arrayOfQuestions[$countElements] = $row["response"];
             $countElements++;
           }
           print ("</tr>\n");
           if ($countRows == 0) {
             sort($arrayOfQuestions);
             $count = count($arrayOfQuestions);
             //sets the question as the key and the median as the value
             if ($count % 2 == 0) {
               $medianArray[$questionTrack] = ($arrayOfQuestions[$count/2] + $arrayOfQuestions[$count/2 - 1]) / 2.0;
             }
             else {
               $medianArray[$questionTrack] = $arrayOfQuestions[floor($count/2)];
             }

           }
         }

         print("</table>\n");
         print("<p><br><br><br>\n");
         //creates a table for the median for the 1-10 questions
         print("<p>\n");
         print("<table class=\"table table-striped\">\n");
         # write the contents of the table
         $header = false;
         if (!$header) {
            $header = true;
            # specify the header to be dark class
            print("<thead class=\"thead-dark\"><tr>\n");
            print "<th>Question</th>";
            print "<th>Median</th>";
            print("</tr></thead>\n");
         }
         foreach ($medianArray as $key => $value) {
            print("<tr>\n");     # Start row of HTML table
            print ("<td>" . $key . "</td>"); # question
            print ("<td>" . $value . "</td>"); # median
            print ("</tr>\n");   # End row of HTML table
         }

         print("</table>\n");
         print("<p><br><br><br>\n");

         //Creating the table for the breakdown for agree/disagree questions
         print("<p>\n");
         print("<table class=\"table table-striped\">\n");
         # write the contents of the table
         $header = false;
         while ($row = mysqli_fetch_assoc($result)){
           if (!$header) {
              $header = true;
              # specify the header to be dark class
              print("<thead class=\"thead-dark\"><tr>\n");
              print "<th>Question</th>";
              print "<th>Response</th>";
              print "<th>Number of Responses</th>";
              print("</tr></thead>\n");
           }
           print("<tr>\n");     # Start row of HTML table
           foreach ($row as $key => $value) {
              print ("<td>" . $value . "</td>"); # One item in row
           }
           print ("</tr>\n");   # End row of HTML table
         }

         print("</table>\n");
         print("<p><br><br><br>\n");

         //Creating the table for the breakdown for multiple choice questions
         print("<p>\n");
         print("<table class=\"table table-striped\">\n");
         # write the contents of the table
         $header = false;
         while ($row = mysqli_fetch_assoc($result3)){
           if (!$header) {
              $header = true;
              # specify the header to be dark class
              print("<thead class=\"thead-dark\"><tr>\n");
              print "<th>Question</th>";
              print "<th>Response</th>";
              print "<th>Number of Responses</th>";
              print("</tr></thead>\n");
           }
           print("<tr>\n");     # Start row of HTML table
           foreach ($row as $key => $value) {
              print ("<td>" . $value . "</td>"); # One item in row
           }
           print ("</tr>\n");   # End row of HTML table
         }

         print("</table>\n");
         print("<p><br><br><br>\n");

         //Creating the table for free text questions
         print("<p>\n");
         print("<table class=\"table table-striped\">\n");
         # write the contents of the table
         $header = false;
         while ($row = mysqli_fetch_assoc($result4)){
           if (!$header) {
              $header = true;
              # specify the header to be dark class
              print("<thead class=\"thead-dark\"><tr>\n");
              print "<th>Question</th>";
              print "<th>Response</th>";
              print("</tr></thead>\n");
           }
           print("<tr>\n");     # Start row of HTML table
           foreach ($row as $key => $value) {
              print ("<td>" . $value . "</td>"); # One item in row
           }
           print ("</tr>\n");   # End row of HTML table
         }

         print("</table>\n");
         print("<p><br><br><br>\n");


         mysqli_free_result($result);
         mysqli_free_result($result2);
         mysqli_free_result($result3);
         mysqli_free_result($result4);
         mysqli_close($conn);
      ?>
      <P>
   </body>
</html>
