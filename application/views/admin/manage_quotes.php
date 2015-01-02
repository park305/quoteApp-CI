    <?php

    print "<h1>Quotes : Manage Quotes</h1>";

    if(isset($success))
      echo $success;

?>
    <table border="1">
      <thead>
        <th>ID</th>
        <th>Quote</th>
        <th>Author</th>
        <th>Edit</th>
        <th>Delete</th>
      </thead>
      <tbody>        
    <?php
    foreach ($quotes->result() as $row) {
      if( is_string($row->quote) AND is_string( $row->author ) ) {
         print "<tr><td>";
       print "<a href=\"" . site_url("single/" . $row->id) . "\">";
       print $row->id . "</a></td><td>" . $row->quote . " </td><td><a href=\"" . site_url("author/" . $row->author) . "\">";
       print $row->author . "</a></td><td><a href=\"" . site_url("admin/editQuote/" . $row->id) . "\">Edit</a></td><td><a href=\"" . site_url("admin/deleteQuote/" . $row->id) . "\">Delete</a></td></tr>";      
     }
     }

    ?>
      </tbody>
    </table>


