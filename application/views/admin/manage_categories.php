    <?php

    print "<h1>Quotes : Manage Categories</h1>";

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
    foreach ($categories as $row) {
          print "<tr><td>" . $row['id'] . "</td><td>";
         print "<a href=\"" . site_url("category/" . $row['name']) . "\">" . $row['name'] . " </a></td><td>";
         print $row['status'] . "</td><td><a href=\"" . site_url("admin/editCategory/" . $row['id']) . "\">Edit</a></td><td><a href=\"" . site_url("admin/deleteCategory/" . $row['id']) . "\">Delete</a></td></tr>";      
     }

    ?>
      </tbody>
    </table>

<a href="<?php echo site_url("admin/addCategory"); ?>">Add New Category</a>