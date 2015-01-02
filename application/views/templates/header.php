<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link href='http://fonts.googleapis.com/css?family=Lilita+One' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url("/includes/quoteApp.css"); ?>">
	<script type="text/javascript" src="<?php echo base_url("/includes/ajax.js"); ?>"></script>
	<?php
		if(!isset($title))
    		echo '<title>Inspiring Quotes CI</title>';
    	else 
    		echo '<title>Inspiring Quotes CI : ' . $title . '</title>';
    ?>
  </head>
  <body>
  	<h1 class="siteTitle"><a href="<?php echo site_url(); ?>">Motivational Quotes</a></h1>
<div class="body-wrapper">
  <div class="body-content">