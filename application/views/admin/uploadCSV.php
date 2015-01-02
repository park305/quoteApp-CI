    <?php    
    $errors = array();

    print "<h1>Quotes : MySQL Admin : Upload CSV Quotes</h1>";



if (isset($_POST['submit'])) {    //form submitted updating quote


  $target_dir = "uploads/";
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]) ;
  $uploadOk = 1;
  $uploadFileType = pathinfo($target_file,PATHINFO_EXTENSION);


  switch($_FILES['fileToUpload']['error'])
  {
  case 0:
    // file found
    if($_FILES['fileToUpload']['name'] != NULL)  //&& okFileType($file['type']) != false
      {
        //print "<p>" . $file['name'] . "</p>";

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($uploadFileType != "csv" ) {
            echo "Sorry, only CSV files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0 ) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file ". basename($_FILES["fileToUpload"]["name"] ) . " has been uploaded.";
                //do the uploading shit here

                $STH = $DBH->prepare("INSERT INTO $dbtable (quote, author) VALUES (?, ?)");
                $quotes = file( $target_file );

                for ($i = 0; $i < count($quotes ); $i++) {
                  $pieces = explode(";", $quotes[$i]);
                  $quote = $pieces[0];
                  $author = $pieces[1];

                  //check for dupes!

                  if (validateField($quote))
                    $quote = trim($quote);    
                  else 
                    array_push($errors, "Quote field is empty");
                  if (validateField($author))
                    $author = trim($author);    
                  else 
                    array_push($errors, "Author field is empty");

                  
                    //check to see if quote already exists?
                  $VALSTH = $DBH->prepare('SELECT count(id) FROM ' . $dbtable . ' WHERE quote=?');
                  $VALSTH->execute(array($quote));
                  $rows = $VALSTH->fetch(PDO::FETCH_NUM);
                  if($rows[0] > 0) {
                    array_push($errors, "Quote already exists in database.");
                  }                  

                  if(! count($errors ) > 0)  {
                    $STH->bindParam(1, $quote);
                    $STH->bindParam(2, $author);  
                    $STH->execute();  
                  }
                }

            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
     

      }
    break;

  case (1|2):
    // upload too large
    $error = "file upload is too large";
    break;

  case 4:
    // no file uploaded
    //print "<p>no file</p>";
    break;

  case (6|7):
    // no temp folder or failed write – server config errors
    $error = "internal error – flog the webmaster";
    break;
  }
}



print "<hr />";
  ?>
  <form action="mySQL-admin-uploadCSV.php" method="post" enctype="multipart/form-data">
    Select CSV file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload"><br />
    <input type="submit" value="Upload CSV" name="submit">
  </form>
  <?php   

    foreach ($errors as $error) 
      print "<h3>" . $error . "</h3>";    

     

 
    ?>
