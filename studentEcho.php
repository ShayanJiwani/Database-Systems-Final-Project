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
        Student Classes Page
     </h1>
     <P>
      <?php
        $conn = mysqli_connect("localhost","cs377",
        "cs377_s18", "courseEvaluations");
         if (mysqli_connect_errno()){
           printf("Connect failed: %s\n", mysqli_connect_error());
           exit(1);
         }
         $studentID = $_POST['studentID'];
         $query = "SELECT studentID, class.class_no, evaluates, course_name FROM studentClass,class
          where studentID = '$studentID' and class.class_no = studentClass.class_no";
         if ( ! ( $result = mysqli_query($conn, $query)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         # number of returned rows
         if ((mysqli_num_rows($result)) == 0) {
           exit("Student ID not found. Please return to login page and try again.");
         }
         # create a new paragraph
         print("<h5> Student: " . $studentID . "</h5>");
         print("<p>\n");
         print("<form action = \"studentEvaluation.php\" method = \"POST\">\n");
         //print("What class do you want to take an evaluation for? <br>\n");
         print("<div class=\"form-row\">\n <div class=\"col\">\n
             <label for=\"class_no\">Class #</label>\n
             <select name=\"class_no\" class=\"form-control\" required>\n
              <option value = \"\" selected disabled>Select a class</option>\n");
         //print("<select name = \"class_no\" required>\n");
         //print("<option value =\"\"selected disabled> Select a class </option>");
         while ( $row = mysqli_fetch_assoc( $result ) ) {
             if ($row["evaluates"] == 0) {
               print("<option value= \"" . $row["class_no"] . "\">" . $row["course_name"] . "</option>\n");
             }
         }
         print("</select>\n</div>\n</div>");
         print("<br>\n<br>\n<br>\n");
         print("<input type=\"hidden\" name = \"studentID\" value = " . $studentID . ">");
         print("<button type = \"submit\" class = \"btn btn-primary\"> Submit </button>\n");
         //print("</table>\n");
         print("<p>\n");
         print("</form>\n");
         mysqli_free_result($result);
         mysqli_close($conn);
      ?>
      <P>
      </div>
   </body>
</html>
