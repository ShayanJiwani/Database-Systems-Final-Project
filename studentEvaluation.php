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
        Course Evaluation
     </h1>
     <P>
      <?php
        $conn = mysqli_connect("localhost","cs377",
        "cs377_s18", "courseEvaluations");
         if (mysqli_connect_errno()){
           printf("Connect failed: %s\n", mysqli_connect_error());
           exit(1);
         }
         $class_no = $_POST['class_no'];
         $studentID = $_POST['studentID'];
         $query = "SELECT question, typeID, options, question.questionID from classQuestions, question
         where class_no = '$class_no' and classQuestions.questionID = question.questionID";
         if ( ! ( $result = mysqli_query($conn, $query)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         $query2 = "SELECT course_name, instructor.fname, instructor.lname from class, instructor
         where class_no = '$class_no' and class.instructorID = instructor.instructorID";
         if ( ! ( $result2 = mysqli_query($conn, $query2)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }
         $courseRow = mysqli_fetch_assoc($result2);
         # create a new paragraph
         print("<h4> Student: " . $studentID . "</h4>");
         print("<p>\n");
         print("<h4>Course Evaluation For:</h4>");
         print("<h6> Class: " . $courseRow["course_name"] . "<br>");
         print("<h6> Professor: " . $courseRow["fname"] . " " . $courseRow["lname"] . "<p><br><br>");
         print("<form action = \"postEvaluation.php\" method = \"POST\">\n");
         while ( $row = mysqli_fetch_assoc( $result ) ) {
           print("<u><b><h5>" . $row["question"] . "</h5></b></u><br>\n");
           if ($row["typeID"] != "ftr") {
             $options = explode(";", $row["options"]);
             for ($i = 0; $i < count($options); $i++) {
               if ($row["typeID"] == "mc" or $row["typeID"] == "ad") {
                 print("<div class=\"radio\">\n<label>
                        <input type=\"radio\" name=\"responses[$row[questionID]]\" value = \"" .
                        $options[$i] . "\" required> " . $options[$i] . " \n</label>\n
                       </div>\n");
               }
               else if ($row["typeID"] == "ott") {
                 if ($i == 0) {
                   print("<div class=\"col\">\n
                          <select name=\"responses[$row[questionID]]\" class=\"form-control\" required>\n
                          <option value = \"\" selected disabled>Select a Rating</option>\n");
                 }
                 print("<option value = \"" . $options[$i] . "\"> " . $options[$i] . " </option>\n");
                 if ($i == count($options) - 1) {
                   print("</select>\n</div>\n");
                 }
               }
             }
           }
           else {
             print("<div class=\"form-row\">\n <div class=\"col]\">
                  <input type = \"text\" maxLength = \"1000\" name = \"responses[$row[questionID]]\"
                  class=\"form-control\" size=\"100\">\n</div>\n</div>");
           }
           print("<br><br>");
         }
         print("<input type=\"hidden\" name = \"studentID\" value = " . $studentID . ">");
         print("<input type=\"hidden\" name = \"class_no\" value = " . $class_no . ">");
         print("<button type = \"submit\" class = \"btn btn-primary\"> Submit </button>\n");
         print("<p>\n");
         print("</form>\n");
         mysqli_free_result($result);
         mysqli_free_result($result2);
         mysqli_close($conn);
      ?>
      <P>
      </div>
   </body>
</html>
