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
        Department Statistics
     </h1>
     <P>
      <?php
        function adCalc($response) {
          switch($response) {
            case 'Strongly Agree': return 5;
            case 'Agree': return 4;
            case 'Neutral': return 3;
            case 'Disagree': return 2;
            case 'Strongly Disagree': return 1;
          }
        }
        $conn = mysqli_connect("localhost","cs377",
        "cs377_s18", "courseEvaluations");
         if (mysqli_connect_errno()){
           printf("Connect failed: %s\n", mysqli_connect_error());
           exit(1);
         }
         $department = $_POST['department'];
         $question = $_POST['question'];

         $initialQuery = "SELECT typeID FROM question WHERE question = '$question'";
         $initialResult = mysqli_query($conn, $initialQuery);
         $row = mysqli_fetch_assoc($initialResult);
         $type = $row['typeID'];
         if ($type != 'ftr') {
           //gets breakdown of all non-text questions for each response (count)
           $query = "SELECT c.semester, c.year, s.response, COUNT(s.response) AS count
           FROM question q, studentResponses s, class c WHERE q.question = '$question'
           AND c.course_name LIKE '$department%' AND q.questionID = s.questionID
           AND c.class_no = s.class_no GROUP BY c.semester, c.year, s.questionID, s.response";

           //finds all responses to find the department mean and standard deviation
           if ($type == 'ad' || $type == 'ott') {
             if ($type == 'ott') {
               //gets mean for each course
               $query2 = "SELECT c.course_name, AVG(s.response) AS average FROM
               question q, studentResponses s, class c WHERE q.question = '$question'
               AND c.course_name LIKE '$department%' AND q.questionID = s.questionID
               AND s.class_no = c.class_no GROUP BY c.course_name";
               //query to get average for each instructor across all classes in department
               $query3 = "SELECT i.fname, i.lname, AVG(s.response) AS average
               FROM instructor i, question q, class c, studentResponses s
               WHERE q.question = '$question' AND c.course_name LIKE '$department%'
               AND q.questionID = s.questionID AND s.class_no = c.class_no
               AND i.instructorID = c.instructorID GROUP BY i.fname, i.lname";
             }
             else {
               //query to get the A/D responses grouped by course name
               $query2 = "SELECT c.course_name, s.response, COUNT(s.response) AS count
               FROM question q, studentResponses s, class c WHERE q.question = '$question'
               AND c.course_name LIKE '$department%' AND q.questionID = s.questionID
               AND s.class_no = c.class_no GROUP BY c.course_name, s.response";
               //query to get responses for A/D questions grouped by the instructor
               $query3 = "SELECT i.fname, i.lname, s.response, COUNT(s.response) AS count
               FROM instructor i, question q, class c, studentResponses s
               WHERE q.question = '$question' AND c.course_name LIKE '$department%'
               AND q.questionID = s.questionID AND s.class_no = c.class_no
               AND i.instructorID = c.instructorID GROUP BY i.fname, i.lname, s.response";
             }
             if ( ! ( $result2 = mysqli_query($conn, $query2)) ) {
               printf("Error: %s\n", mysqli_error($conn));
               exit(1);
             }
             if ( ! ( $result3 = mysqli_query($conn, $query3)) ) {
               printf("Error: %s\n", mysqli_error($conn));
               exit(1);
             }
           }
         }
         else {
           //Gets information for all text based questions
           $query = "SELECT c.semester, c.year, s.response
           FROM question q, studentResponses s, class c WHERE q.question = '$question'
           AND c.course_name LIKE '$department%' AND s.response != ''
           AND q.questionID = s.questionID AND c.class_no = s.class_no";
         }

         if ( ! ( $result = mysqli_query($conn, $query)) ) {
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
         }

        if ((mysqli_num_rows($result)) == 0) {
           exit("<h4>This question has yet to be evaluated for the department
                or the department name was incorrect. Please return to the page and try again.</h4>");
         }

         # create a new paragraph
         print("<h5> Department: " . $department . "</h5><br><br>");
         print("<h5> Question: " . $question . "</h5><br>");
         print("<p><br>\n");

         # write the contents of the table
         $header = false;
         if ($type == 'ott') {
           //Creating the table for the breakdown for rate 1-10 questions
           print("<p>\n");
           print("<table class=\"table table-striped\">\n");
           $countElements = 0;
           $medianArray ='';
           $arrayOfClass ='';
           $semesterTrack ='';
           $yearTrack ='';
           $meanArray ='';
           $countRows = mysqli_num_rows($result);
           while ($row = mysqli_fetch_assoc($result)){
             $countRows--;
             if (!$header) {
                $header = true;
                # specify the header to be dark class
                print("<thead class=\"thead-dark\"><tr>\n");
                print "<th>Semester</th>";
                print "<th>Year</th>";
                print "<th>Rating</th>";
                print "<th>Number of Responses</th>";
                print("</tr></thead>\n");
             }
             //set the first question
             if ($countElements == 0) {
               $semesterTrack = $row["semester"];
               $yearTrack = $row["year"];
             }
             //the semester or year has switched so we're done with the responses
             if ($row["semester"] != $semesterTrack || $row["year"] != $yearTrack) {
               //sets the question as the key and the median as the value
               $keySet = $semesterTrack . "," . $yearTrack;
               sort($arrayOfClass);
               $countArray = count($arrayOfClass);
               //sets the question as the key and the median as the value
               if ($countArray % 2 == 0) {
                 $medianArray[$keySet] = ($arrayOfClass[$countArray/2] + $arrayOfClass[$countArray/2 - 1]) / 2.0;
               }
               else {
                 $medianArray[$keySet] = $arrayOfClass[floor($countArray/2)];
               }
               //calculates the mean and sets it as the value
               $meanArray[$keySet] = array_sum($arrayOfClass) / count($arrayOfClass);

               $countElements = 0;
               $semesterTrack = $row["semester"];
               $yearTrack = $row["year"];
               $arrayOfClass = "";
             }
             print("<tr>\n");     # Start row of HTML table
             foreach ($row as $key => $value) {
                print ("<td>" . $value . "</td>"); # One item in row
             }
             for($i = 0; $i < $row["count"]; $i++) {
               $arrayOfClass[$countElements] = $row["response"];
               $countElements++;
             }
             print ("</tr>\n");
             if ($countRows == 0) {
               sort($arrayOfClass);
               $keySet = $semesterTrack . "," . $yearTrack;
               $countArray = count($arrayOfClass);
               //sets the question as the key and the median as the value
               if ($countArray % 2 == 0) {
                 $medianArray[$keySet] = ($arrayOfClass[$countArray/2] + $arrayOfClass[$countArray/2 - 1]) / 2.0;
               }
               else {
                 $medianArray[$keySet] = $arrayOfClass[floor($countArray/2)];
               }
               //sets the question as the key and the median as the value
               //calculates the mean and sets it as the value
               $meanArray[$keySet] = array_sum($arrayOfClass) / count($arrayOfClass);
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
              print "<th>Semester</th>";
              print "<th>Year</th>";
              print "<th>Median</th>";
              print "<th>Mean</th>";
              print("</tr></thead>\n");
           }
           foreach ($medianArray as $key => $value) {
              print("<tr>\n");     # Start row of HTML table
              $keyArray = explode(",", $key);
              print ("<td>" . $keyArray[0] . "</td>"); # One item in row
              print ("<td>" . $keyArray[1] . "</td>"); # One item in row
              print ("<td>" . $value . "</td>"); # One item in row
              print ("<td>" . round($meanArray[$key], 2) . "</td>");
              print ("</tr>\n");   # End row of HTML table
           }

           print("</table>\n");
           print("<p><br><br><br>\n");

           //put means of all classes into an array
           $counter = 0;
           $allResponses ='';
           $dataArray ='';
           $countData = 0;
           while($row = mysqli_fetch_assoc($result2)) {
             $dataArray[$countData] = $row;
             $countData++;
             foreach($row AS $key => $value) {
               if ($key == "average") {
                 $allResponses[$counter] = $value;
                 $counter++;
               }
             }
           }
           //find the mean
           $meanTotal = array_sum($allResponses) / count($allResponses);
           $sum = 0;
           //finds the sum of the means-meanTotal squared
           for($i = 0; $i < count($allResponses); $i++) {
             $sum += pow($allResponses[$i] - $meanTotal, 2);
           }
           //square root of sum of means squared gives standard deviation
           $meanSquared = $sum / count($allResponses);
           $standardDeviation = sqrt($meanSquared);
           //Creating the table for poor classes
           print("<p>\n");
           print("<table class=\"table table-striped\">\n");
           # write the contents of the table
           $header = false;
           //only prints out all the courses doing poorly
           $i = 0;
           while (($row = $dataArray[$i]) != NULL){
             if (!$header) {
                $header = true;
                # specify the header to be dark class
                print("<thead class=\"thead-dark\"><tr>\n");
                print "<th>Courses Performing Poorly</th>";
                print "<th>Course Mean Rating</th>";
                print("</tr></thead>\n");
             }
             if ($row["average"] <= $meanTotal- 1.5 * $standardDeviation) {
               print("<tr>\n");     # Start row of HTML table
               foreach ($row as $key => $value) {
                  if ($key == 'average') {
                    $value = round($value, 2);
                  }
                  print ("<td>" . $value . "</td>"); # One item in row
               }
               print ("</tr>\n");   # End row of HTML table
             }
             $i++;
           }

           print("</table>\n");
           print("<p><br><br><br>\n");

           //put means of all classes into an array
           $counter = 0;
           $allResponses ='';
           $dataArray = '';
           $countData = 0;
           while($row = mysqli_fetch_assoc($result3)) {
             $dataArray[$countData] = $row;
             $countData++;
             foreach($row AS $key => $value) {
               if ($key == "average") {
                 $allResponses[$counter] = $value;
                 $counter++;
               }
             }
           }
           //find the mean
           $meanTotal = array_sum($allResponses) / count($allResponses);
           $sum = 0;
           //finds the sum of the means-meanTotal squared
           for($i = 0; $i < count($allResponses); $i++) {
             $sum += pow($allResponses[$i] - $meanTotal, 2);
           }
           //square root of sum of means squared gives standard deviation
           $meanSquared = $sum / count($allResponses);
           $standardDeviation = sqrt($meanSquared);
           //Creating the table for poor classes
           print("<p>\n");
           print("<table class=\"table table-striped\">\n");
           # write the contents of the table
           $header = false;
           //only prints out all the courses doing poorly
           $i = 0;
           while (($row = $dataArray[$i]) != NULL){
             if (!$header) {
                $header = true;
                # specify the header to be dark class
                print("<thead class=\"thead-dark\"><tr>\n");
                print "<th>Instructor Performing Poorly (First Name)</th>";
                print "<th>Instructor Performing Poorly (Last Name)</th>";
                print "<th>Average Rating</th>";
                print("</tr></thead>\n");
             }
             if ($row["average"] <= $meanTotal- 1.5 * $standardDeviation) {
               print("<tr>\n");     # Start row of HTML table
               foreach ($row as $key => $value) {
                 if ($key == 'average') {
                   $value = round($value, 2);
                 }
                  print ("<td>" . $value . "</td>"); # One item in row
               }
               print ("</tr>\n");   # End row of HTML table
             }
             $i++;
           }

           print("</table>\n");
           print("<p><br><br><br>\n");
         }
         //creating table if type is multiple choice or agree/disagree
        else if ($type == 'ad'|| $type == 'mc') {
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
               print "<th>Semester</th>";
               print "<th>Year</th>";
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

          if ($type == 'ad') {
            //finding the mean of each class where Strongly Agree = 5, Strongly Disagree = 1, etc.
            $countElements = 0;
            $arrayOfClass ='';
            $courseTrack ='';
            $meanArray ='';
            $countRows = mysqli_num_rows($result2);
            $header = false;
            while ($row = mysqli_fetch_assoc($result2)){
              $countRows--;
              //set the first question
              if ($countElements == 0) {
                $courseTrack = $row["course_name"];
              }
              //the semester or year has switched so we're done with the responses
              if ($row["course_name"] != $courseTrack) {
                //calculates the mean and sets it as the value
                $meanArray[$courseTrack] = array_sum($arrayOfClass) / count($arrayOfClass);
                $countElements = 0;
                $courseTrack = $row["course_name"];
                $arrayOfClass = "";
              }
              for($i = 0; $i < $row["count"]; $i++) {
                $arrayOfClass[$countElements] = adCalc($row["response"]);
                $countElements++;
              }
              if ($countRows == 0) {
                //calculates the mean and sets it as the value
                $meanArray[$courseTrack] = array_sum($arrayOfClass) / count($arrayOfClass);
              }
            }

            //meanArray now has the means for the A/D question for each course
            $meanTotal = array_sum($meanArray) / count($meanArray);
            $sum = 0;
            foreach ($meanArray AS $key => $value) {
              $sum += pow($value - $meanTotal, 2);
            }
            //square root of sum of means squared gives standard deviation
            $meanSquared = $sum / count($meanArray);
            $standardDeviation = sqrt($meanSquared);
            //Creating the table for poor classes
            print("<p>\n");
            print("<table class=\"table table-striped\">\n");
            # write the contents of the table
            $header = false;
            if (!$header) {
               $header = true;
               # specify the header to be dark class
               print("<thead class=\"thead-dark\"><tr>\n");
               print "<th>Courses Performing Poorly</th>";
               print "<th>Course Mean for Agree/Disagree Responses (Scale of 1-5)</th>";
               print("</tr></thead>\n");
            }
            //only prints out all the courses doing poorly
            foreach ($meanArray AS $key => $value){
              if ($value <= $meanTotal- 1.5 * $standardDeviation) {
                print("<tr>\n");
                print ("<td>" . $key . "</td>");
                print ("<td>" . round($value, 2) . "</td>");
                print ("</tr>\n");
              }
            }

            print("</table>\n");
            print("<p><br><br><br>\n");

            //finding the mean for each teacher where Strongly Agree = 5, Strongly Disagree = 1, etc.
            $countElements = 0;
            $arrayOfClass = "";
            $fnameTrack;
            $lnameTrack;
            $meanArray ='';
            $countRows = mysqli_num_rows($result3);
            $header = false;
            while ($row = mysqli_fetch_assoc($result3)){
              $countRows--;
              //set the first question
              if ($countElements == 0) {
                $fnameTrack = $row["fname"];
                $lnameTrack = $row["lname"];
              }
              //the semester or year has switched so we're done with the responses
              if ($row["fname"] != $fnameTrack || $row["lname"] != $lnameTrack) {
                //calculates the mean and sets it as the value
                $keySet = $fnameTrack . ',' . $lnameTrack;
                $meanArray[$keySet] = array_sum($arrayOfClass) / count($arrayOfClass);
                $countElements = 0;
                $fnameTrack = $row["fname"];
                $lnameTrack = $row["lname"];
                $arrayOfClass = "";
              }
              for($i = 0; $i < $row["count"]; $i++) {
                $arrayOfClass[$countElements] = adCalc($row["response"]);
                $countElements++;
              }
              if ($countRows == 0) {
                //calculates the mean and sets it as the value
                $keySet = $fnameTrack . ',' . $lnameTrack;
                $meanArray[$keySet] = array_sum($arrayOfClass) / count($arrayOfClass);
              }
            }

            //meanArray now has the means for the A/D question for each course

            $meanTotal = array_sum($meanArray) / count($meanArray);
            $sum = 0;
            foreach ($meanArray AS $key => $value) {
              $sum += pow($value - $meanTotal, 2);
            }
            //square root of sum of means squared gives standard deviation
            $meanSquared = $sum / count($meanArray);
            $standardDeviation = sqrt($meanSquared);
            //Creating the table for poor classes
            print("<p>\n");
            print("<table class=\"table table-striped\">\n");
            # write the contents of the table
            $header = false;
            if (!$header) {
               $header = true;
               # specify the header to be dark class
               print("<thead class=\"thead-dark\"><tr>\n");
               print("<th>Instructor Performing Poorly (First Name)</th>");
               print("<th>Instructor Performing Poorly (Last Name)</th>");
               print("<th>Course Mean for Agree/Disagree Responses (Scale of 1-5)</th>");
               print("</tr></thead>\n");
            }
            //only prints out all the courses doing poorly
            foreach ($meanArray AS $key => $value){
              if ($value <= $meanTotal- 1.5 * $standardDeviation) {
                $keyArray = explode(",", $key);
                print("<tr>\n");
                print("<td>" . $keyArray[0] . "</td>");
                print("<td>" . $keyArray[1] . "</td>");
                print("<td>" . round($value, 2) . "</td>");
                print("</tr>\n");
              }
            }

            print("</table>\n");
            print("<p><br><br><br>\n");
          }

        }
         else {
           //Creating the table for free text questions
           print("<p>\n");
           print("<table class=\"table table-striped\">\n");
           # write the contents of the table
           $header = false;
           while ($row = mysqli_fetch_assoc($result)){
             if (!$header) {
                $header = true;
                # specify the header to be dark class
                print("<thead class=\"thead-dark\"><tr>\n");
                print "<th>Semester</th>";
                print "<th>Year</th>";
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
         }

         mysqli_free_result($result1);
         mysqli_free_result($result2);
         mysqli_free_result($result3);
         mysqli_close($conn);
      ?>
      <P>
   </body>
</html>
