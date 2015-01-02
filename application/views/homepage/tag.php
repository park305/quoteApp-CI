<?php
 print $quotes['count'] . " quotes under tag " . $tag . "<br />";

if($quotes['count'] > 0) {
  foreach($quotes['rows'] as $row)
      print $row['quote'] . " by " . $row['author'] . "<br />";                    
    }
?>
