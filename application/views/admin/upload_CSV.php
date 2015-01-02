    <?php    
    foreach($results as $result)
      print "<h3>" . $result . "</h3>";
    ?>
  <h1>Quotes : Upload CSV Quotes</h1>
  <hr />
   <?php echo form_open_multipart("admin/uploadCSV"); ?>
    Select CSV file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload"><br />
    <input type="submit" value="Upload CSV" name="submit">
  </form>
