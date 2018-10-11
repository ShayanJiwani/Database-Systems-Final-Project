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
     <h3>
        Hello Student! Please enter the information for the class you want to know about.
     </h5>
        <form action = "studentClassStatistics.php" method = "POST">
          <br><br>
          <div class="form-row">
            <div class="col">
              <label for="course_name">Course Name (Ex: CS377)</label>
              <input type="text" name = "course_name" class="form-control" placeholder="Course Name" required>
            </div>
          </div>
          <br><br><br>
          <div class="form-row">
            <div class="col">
              <label for="course_name">Instructor First Name (Example: Joyce)</label>
              <input type="text" name = "fname" class="form-control" placeholder="First Name" required>
            </div>
          </div>
          <br><br><br>
          <div class="form-row">
            <div class="col">
              <label for="course_name">Instructor Last Name (Example: Ho)</label>
              <input type="text" name = "lname" class="form-control" placeholder="Last Name" required>
            </div>
          </div>
          <br><br><br>
          <button type = "submit" class = "btn btn-primary"> Submit </button>
        </form>
     </h5>
   </div>
   </body>
</html>
