    <?php

    print "<h1>Quotes : Manage Tags</h1>";

    if(isset($success))
      echo $success;

?>
    <table border="1">
      <thead>
        <th>ID</th>
        <th>Tag</th>
        <th>Status</th>
        <th>Edit</th>
        <th>Delete</th>
      </thead>
      <tbody>        
    <?php
    foreach ($tags as $row) {
          print "<tr><td>" . $row['id'] . "</td><td>";
         print "<a href=\"" . site_url("tag/" . $row['name']) . "\">" . $row['name'] . " </a></td><td>";
         print $row['status'] . "</td><td><a href=\"" . site_url("admin/editTag/" . $row['id']) . "\">Edit</a></td><td><a href=\"" . site_url("admin/deleteTag/" . $row['id']) . "\">Delete</a></td></tr>";      
     }

    ?>
      </tbody>
    </table>

<a href="<?php echo site_url("admin/addTag"); ?>">Add New Tag</a>