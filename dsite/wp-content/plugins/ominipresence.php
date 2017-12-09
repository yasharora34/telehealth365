<?php 

global $wpdb; 

if(isset($_REQUEST['update'])){
	$wpdb->update('wp_term_taxonomy', array("description" => $_POST['apiurl']), array("term_taxonomy_id" => 41) );
}

	
if(isset($_REQUEST['updatem'])){
	$wpdb->update('wp_term_taxonomy', array("description" => $_POST['mediaurl']), array("term_taxonomy_id" => 56) );
}
if(isset($_REQUEST['updatepass'])){
	$wpdb->update('wp_term_taxonomy', array("description" => $_POST['rtc_password']), array("term_taxonomy_id" => 67) );
}

$query = $wpdb->get_results('SELECT description FROM wp_term_taxonomy WHERE term_taxonomy_id = 41');
$querym = $wpdb->get_results('SELECT description FROM wp_term_taxonomy WHERE term_taxonomy_id = 56');
$queryn = $wpdb->get_results('SELECT description FROM wp_term_taxonomy WHERE term_taxonomy_id = 67');
?>
<div class="wrap">
<div id="icon-plugins" class="icon32">
	<br>
</div>
<h2>OmniReach</h2>
<form method="post">

	<table class="wp-list-table widefat fixed pages" cellspacing="0" width="400px">

    	<tr><td>API Url</td><td> <input size="50" type="text" name="apiurl" id="apiurl" value="<?php echo $query[0]->description; ?>" /></td></tr>

        <tr>

        	<td></td>

            <td><input type="submit" name="update" value="Update"  /></td>

        </tr>

    </table>

</form> 

<form method="post">

	<table class="wp-list-table widefat fixed pages" cellspacing="0" width="400px">

        <tr>

          <td>Media Server</td>

          <td><input size="50" type="text" name="mediaurl" id="mediaurl" value="<?php echo $querym[0]->description; ?>" /></td>

        </tr>

        <tr>

        	<td></td>

            <td><input type="submit" name="updatem" value="Update"  /></td>

        </tr>

    </table>

</form> 

<form method="post">

	<table class="wp-list-table widefat fixed pages" cellspacing="0" width="400px">

        <tr>

          <td>RTC Server Authorization</td>

          <td><input size="50" type="text" name="rtc_password" id="rtc_password" value="<?php echo $queryn[0]->description; ?>" /></td>

        </tr>

        <tr>

        	<td></td>

            <td><input type="submit" name="updatepass" value="Update"  /></td>

        </tr>

    </table>

</form> 

</div>