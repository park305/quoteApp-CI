
    <?php
            foreach ($quotes->result_array() as $row) {

          if(is_string($row['quote']) AND is_string($row['author']))
             print $row['quote'] . " - " . $row['author'] . "<br />";      
}          

    ?>
