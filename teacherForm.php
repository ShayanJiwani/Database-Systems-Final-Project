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
        Hello Professor! Please enter your instructor ID and course information.
      </h1>
      <h5>
        <form action = "teacherClassStatistics.php" method = "POST">
          <br><br>
          <div class="form-row">
            <div class="col">
              <label for="instructorID">Instructor ID</label>
              <input type="text" maxlength= "5" name = "instructorID" class="form-control" placeholder="Instructor ID" required>
            </div>
          </div>
          <br><br><br>
          <div class="form-row">
            <div class="col">
              <label for="course_name">Course Name</label>
              <input type="text" name = "course_name" class="form-control" placeholder="Course Name" required>
            </div>
            <div class="col">
              <label for="semester">Semester</label>
              <input type="text" name = "semester" class="form-control" placeholder="Semester" required>
            </div>
            <div class="col">
              <label for="year">Year</label>
              <input type="text" name = "year" class="form-control" placeholder="Year" required>
            </div>
          </div>
          <br><br><br>
          <button type = "submit" class = "btn btn-primary"> Submit </button>
        </form>
     </h5>
   </div>
   </body>
</html>
