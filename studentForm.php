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
        Hello Student! Please enter your student ID.
      </h3>
        <form action = "studentEcho.php" method = "POST">
          <br><br><br>
          <div class="form-row">
            <div class="col">
              <label for="studentID">Student ID</label>
              <input type="text" maxlength="16" name = "studentID" class="form-control" placeholder="Student ID" required>
            </div>
          </div>
          <br><br><br>
          <button type = "submit" class = "btn btn-primary"> Submit </button>
        </form>
   </div>
   </body>
</html>
