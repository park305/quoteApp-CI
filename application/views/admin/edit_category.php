    <?php

    print "<h1>Quotes : Edit Category</h1>";

    echo validation_errors();
    if(isset($success))
      echo $success;

    
    print $category_form;


    ?>
