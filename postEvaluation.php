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
        Post Evaluation
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
         $responses = $_POST['responses'];

         $query = "UPDATE studentClass SET evaluates = '1' WHERE studentID = '$studentID'
                   AND class_no = '$class_no'";
         if ( ! ( $result = mysqli_query($conn, $query)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }

         foreach($responses as $key => $val) {
           $query2 = "INSERT INTO studentResponses VALUES('$studentID', '$class_no', '$key', '$val')";
           if ( ! ( $result2 = mysqli_query($conn, $query2)) ) {
             print("<h4> Error: Course Evaluation already completed. Cannot take the same evaluation again. </h4>\n");
             exit(1);
           }
         }
         print("<h4>Evaluation Successfully Completed!</h4>\n");
         mysqli_free_result($result);
         mysqli_free_result($result2);
         mysqli_close($conn);
      ?>
      <P>
      </div>
   </body>
</html>
