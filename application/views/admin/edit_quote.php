    <?php

    print "<h1>Quotes : Edit Quote</h1>";

    echo validation_errors();
    if(isset($success))
      echo $success;

    
    print $quote_form;
    //printQuoteForm("mySQL-admin-addQuote.php", $quote, $author, $DBHc);            


    ?>
