<?php
/**
 * Repair Requests administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
?>

<?php 
global $header_authorization;
$header_authorization=getrtcPass();
global $massurl;
$massurl=getURL();

$url= $massurl."/users";

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:'.$header_authorization, 'Content-type:text/xml' ));
$data = curl_exec($ch); 


if(curl_errno($ch))
    print curl_error($ch);
else
	$resp = explode("\n<?", $data);
    $response = "<?{$resp[1]}";
    $xml = simplexml_load_string($response);
	if(!empty($xml))
	{
		?>
        
     <form method="post" id="userform">  
       <input type="hidden"  name="delall" id="delall"  />
       <input type="submit" style="margin-top:15px;" name="deleteall" value="Delete All" id="deleteall"  />
    <table class="wp-list-table widefat fixed posts" style="margin-top:30px;">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"  ></th>
        <th scope="col" id="title" class="manage-column column-title sortable desc" style=""><span>Username</span></th>
        <th scope="col" id="author" class="manage-column column-author" style="">Name</th>
        <th scope="col" id="title" class="manage-column column-title sortable desc" style="">Email</th>
        <th scope="col" id="categories" class="manage-column column-categories" style="">User Role</th>
        <th scope="col" id="categories" class="manage-column column-categories" style="">Action</th>
    </tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column column-cb check-column" style=""><input id="cb-select-all-2" type="checkbox"></th>
        <th scope="col" class="manage-column column-title sortable desc" style="">Username</th>
        <th scope="col" class="manage-column column-author" style="">Name</th>
        <th scope="col" class="manage-column column-title sortable desc" style="">Email</th>
        <th scope="col" class="manage-column column-categories" style="">User Role</th>
        <th scope="col" class="manage-column column-categories" style="">Action</th>
     </tr>
	</tfoot>

	<tbody id="the-list">    
    <?php
	$i=1;
    foreach($xml as $newxml)
    {
		$pos=strpos($newxml->username,"\\40");
		if($pos == true)
		{
			$username=str_replace("\\40","@",$newxml->username);
		}
		else
		{
			$username=$newxml->username;
		}
		
		?>
        <tr id="post_<?php echo $i; ?>">
			<th scope="row" class="check-column"><input type="checkbox" name="rtcusers" value="<?php echo $username; ?>" class="rtcusers" id="allrtcusers" /></th>
			<td class="post-title page-title column-title"><strong><?php echo $username; ?></strong></td>			
            <td class="author column-author"><?php echo $newxml->name; ?></td>
            <td class="post-title page-title column-title"><?php echo $newxml->email; ?></td>
			<td class="categories column-categories">
				<?php 
				if(!empty($newxml->properties))
				{
					echo $newxml->properties->property[1]->attributes()->{'value'};
				}
					
				?> </td>
            <td class="categories column-categories"><a href="/dsite/wp-admin/admin.php?page=rtcusers.php&userdelete=<?php echo $username; ?>" >Delete</a></td>
		</tr>
        <?php
		$i++;
    }
    ?>
    </tbody>
    </table>
    </form>
    <?php
	
    
	}
    curl_close($ch);
	?>
<script>

    jQuery('#cb-select-all-1').click(function(event) {  //on click 
        if(this.checked) { // check select status
		var value='';
            jQuery('.rtcusers').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1" 
				value += jQuery(this).val()+",";
            });
			jQuery("#delall").val(value);
			jQuery("#deleteall").removeAtt("disabled");
        }else{
            jQuery('.rtcusers').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                       
            }); 
			jQuery("#delall").val('');        
        }
    });
    
jQuery('.rtcusers').click(function(){
	z=document.getElementById('userform');
	zall=z.getElementsByTagName('input');
 	document.getElementById('delall').value="";

		for(var i=0; i<zall.length; ++i){
			if(zall.item(i).checked==true){
				if(document.getElementById('delall').value==""){
					document.getElementById('delall').value=zall.item(i).value;
				}else{
					document.getElementById('delall').value+=","+zall.item(i).value;
				}
			
			}
		}	
});

</script>