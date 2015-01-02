    <?php

    print "<h1>Quotes : Add Category</h1>";

    echo validation_errors();
    if(isset($success))
      echo $success;
   
    print $tag_form;


    ?>
