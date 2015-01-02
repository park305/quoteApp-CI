
    <?php

          if(is_string($quoteRow['quote']) AND is_string($quoteRow['author']))
             print $quoteRow['quote'] . " - " . $quoteRow['author'] . "<br />";      
if(isset($category))
  print "<hr />Category: "  . $category;
if(!empty($tags)) {
  print "Tags: ";
  foreach($tags as $tag) 
  {
    print $tag . " ";
  }
}
    ?>
