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
$blogusers = get_users();
?>
<script>
function opendetail(val){
	jQuery('.user_info').css('display','none');
	jQuery('#row_'+val).css('display','block');
	}
function closepopup(){
	jQuery('.user_info').css('display','none');
	}
</script>
        <style>
        .usertable{width:100%;border:1px;}
		.usertable td{padding:0;}
		.usertable .user_row{font-weight:bold; background:#0CF!important; padding:2px;}
		.usertable .patient_row{background:#efefef!important;padding:2px;}
		.user_info{ width:700px; height:400px; position:fixed; background:#fff;border-radius:10px; left:50%; top:50%; margin:-200px 0 0 -350px; padding:10px; box-shadow:0px 0px 5px #CCCCCC;}
		.popcontent{ height:300px; overflow-y:scroll;}
		.close{ float:right; background:#006; color:#fff; padding:2px 8px;border-radius:50%; margin:-20px -20px 0 0;}
        .wp-list-table tr:nth-child(2n+1) {background:#efefef;}
        .wp-list-table tr td table tr{background:none!important;}
        </style>
    <table class="wp-list-table widefat fixed posts" style="margin-top:30px;">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"  ></th>
        <th scope="col" id="title" class="manage-column column-title sortable desc" style=""><span>Username</span></th>
        <th scope="col" id="author" class="manage-column column-author" style="">Name</th>
        <th scope="col" id="title" class="manage-column column-title sortable desc" style="">Email</th>
        <th scope="col" class="manage-column column-categories" style="">Password</th>
        <th scope="col" id="categories" class="manage-column column-categories" style="">User Role</th>
      </tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column column-cb check-column" style=""><input id="cb-select-all-2" type="checkbox"></th>
        <th scope="col" class="manage-column column-title sortable desc" style="">Username</th>
        <th scope="col" class="manage-column column-author" style="">Name</th>
        <th scope="col" class="manage-column column-title sortable desc" style="">Email</th>
        <th scope="col" class="manage-column column-categories" style="">Password</th>
        <th scope="col" class="manage-column column-categories" style="">User Role</th>
        </tr>
	</tfoot>

	<tbody id="the-list">    
    <?php
	//echo "<pre>";
	//print_r($blogusers[0]);
    foreach ( $blogusers as $user ) {
	
	?>
        <tr>
			<th scope="row" class="check-column"><a href="javascript:;" onclick="opendetail('<?php echo $user->ID; ?>')">#</a></th>
			<td class="post-title page-title column-title"><?php echo $user->user_login;?></td>			
            <td class="author column-author"><?php echo $user->user_nicename;?></td>
            <td class="post-title page-title column-title"><?php echo $user->user_email;?></td>
			<td class="categories column-categories"><?php echo get_user_meta($user->ID, 'account_password', true); ?></td>
            <td class="categories column-categories"><?php if($user->roles[0]=='customer'){echo "Free User";}else{echo $user->roles[0];}?>
        <div id="row_<?php echo $user->ID; ?>" class="user_info">
        <a href="javascript:;" class="close" onclick="closepopup();">x</a>
        <table class="usertable">
        <tr><td>Username: <?php echo $user->user_login;?></td><td>Name: <?php echo $user->user_nicename;?></tr>
        <tr><td>Password: <?php echo get_user_meta($user->ID, 'account_password', true); ?></td>
        <td>Email: <?php echo $user->user_email;?></td></tr>
		<tr><td>User Type: <?php if($user->roles[0]=='customer'){echo "Free User";}else{echo $user->roles[0];}?></td><td></td></tr>
</table>
		<?php
        $groups =  mysql_query('SELECT * FROM `wp_groups` WHERE companyId= "'.$user->ID.'"');
	if(mysql_num_rows($groups)>0){
		echo "<strong>Groups: </strong>";
	while($all_groups = mysql_fetch_array($groups)){
		echo $all_groups['groupName'].", ";
		}
		echo "<br />";
	}
		?>
		<strong>Users</strong><br />
        <div class="popcontent">
        <table class="usertable">
        <tr><td>Name</td><td>Username</td><td>Email</td><td>Password</td><td>Member of</td></tr>
		<?php
        	$get_user = mysql_query('SELECT * FROM wp_adduser WHERE companyid = "'.$user->ID.'"'); 
			while($all_user = mysql_fetch_array($get_user)){
		?>
        <tr class="user_row"><td><?php echo $all_user['title']." ".$all_user['firstname']." ".$all_user['lastname'];?></td><td><?php echo $all_user['username'];?></td><td><?php echo $all_user['email'];?></td><td><?php echo $all_user['pwd'];?></td>
        <td>
        <?php $get_group = 	$wpdb->get_results('SELECT * FROM wp_groups WHERE id in  ("'.$all_user['grp'].'")');
		foreach($get_group as $grp){echo $grp->groupName;}
		?>
        </td>
        </tr>
        <?php $getpatients =  $wpdb->get_results('SELECT * FROM wp_patients WHERE userId='.$all_user['id'] );
		foreach($getpatients as $patient){
			//print_r($patient);
			?>
            <tr class="patient_row"><td><?php echo $patient->ptitle." ".$patient->pfirstName." ".$patient->plastName;?></td><td><?php echo $patient->pusername;?></td><td><?php echo $patient->pEmail;?></td><td><?php echo $patient->pPassword;?></td>
        <td></td></tr>
            <?php
			}
		?>
        
        <?php 
		}
		?>
        </table>
        </div>
</div>
</td>
		</tr>
        <?php } ?>
    </tbody>
    </table>
<script>jQuery('.user_info').css('display','none');</script>