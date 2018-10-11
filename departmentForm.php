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
        Hello Department Chair! Please select the question and department.
      </h1>
      <h5>
        <?php
            $conn = mysqli_connect("localhost","cs377",
            "cs377_s18", "courseEvaluations");
             if (mysqli_connect_errno()){
               printf("Connect failed: %s\n", mysqli_connect_error());
               exit(1);
             }
            $query = "SELECT question FROM question";
            if ( ! ( $result = mysqli_query($conn, $query)) ) {
              printf("Error: %s\n", mysqli_error($conn));
              exit(1);
            }
            print("<p>\n");
            print("<form action = \"departmentView.php\" method = \"POST\">\n");
            print("<div class=\"form-row\">\n<div class=\"col\">\n
                <label for=\"question\">Select Question</label>\n
                <select name=\"question\" class=\"form-control\" required>\n
                 <option value = \"\" selected disabled>Select a class</option>\n");
            while ( $row = mysqli_fetch_assoc( $result ) ) {
              print("<option value= \"" . $row["question"] . "\">" . $row["question"] . "</option>\n");
            }
            print("</select>\n</div>\n</div>");
            print("<br>\n<br>\n<br>\n");
            mysqli_free_result($result);
            mysqli_close($conn);
         ?>
          <div class="form-row">
            <div class="col">
              <label for="course_name">Department(Ex: CS)</label>
              <input type="text" name = "department" class="form-control" placeholder="Department" required>
            </div>
          </div>
          <br><br><br>
          <button type = "submit" class = "btn btn-primary"> Submit </button>
        </form>
     </h5>
   </div>
   </body>
</html>
