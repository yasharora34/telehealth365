<?php
/*
Plugin Name: wpsc Support Tickets PRO
Plugin URI: http://wpstorecart.com/wpsc-support-tickets/
Description: A professional add-on for the wpsc-Support-Tickets system for Wordpress. Adds many professional features.
Version: 2.0.0
Author: wpStoreCart, LLC
Author URI: URI: http://wpstorecart.com/
Text Domain: wpsc-support-tickets-pro
*/

if (file_exists(ABSPATH . 'wp-includes/pluggable.php')) {
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}

function wpscSupportTicketsPRO() {
    
}

/**
 * Adds options to the admin panel
 */
function wstPROSettingsForm() {
    
        //error_reporting(E_ALL);
    
        if (file_exists(ABSPATH . 'wp-includes/capabilities.php')) {
            require_once(ABSPATH . 'wp-includes/capabilities.php');
        }    
    
        $devOptions = get_option('wpscSupportTicketsAdminOptions');  
    
        echo '
            <p><center><u><h3>wpsc-Support-Tickets PRO Settings:</h3></u></center></p>
        <p><strong>'.__('Allow ticket creators to upload file attachments', 'wpsc-support-tickets-pro').':</strong> '.__('Set this to true if you want ticket creators to be able to upload files.', 'wpsc-support-tickets-pro').'  <br />
        <select name="allow_uploads">
            ';

            $pagesY = array();
            $pagesY[0] = 'true';
            $pagesY[1] = 'false';
            foreach ($pagesY as $pagg) {
                $option = '<option value="'.$pagg.'"';
                if($pagg==$devOptions['allow_uploads']) {
                        $option .= ' selected="selected"';
                }
                $option .='>';
                $option .= $pagg;
                $option .= '</option>';
                echo $option;
            }

        echo '
        </select>
        </p>
        
        <p><strong>'.__('Who can view &amp; administrate all tickets', 'wpsc-support-tickets-pro').':</strong> '.__('Users with the following roles will have full access to edit, reply to, close, re-open, and delete all tickets.', 'wpsc-support-tickets-pro').'  <br />
            <ul>
            <li><input type="checkbox" name="wstpro_admin[]" value="administrator" checked disabled /> '.__('Administrator', 'wpsc-support-tickets-pro').'</li>';
            
            $check_editor_role = (array)get_role('editor');
            $checked_editor_role = '';
            if($check_editor_role != NULL && $check_editor_role['capabilities']['manage_wpsc_support_tickets'] == 1 ) {
                $checked_editor_role = 'checked';
            }
            echo '<li><input type="checkbox" name="wstpro_admin[]" value="editor" '.$checked_editor_role.' /> '.__('Editor', 'wpsc-support-tickets-pro').'</li>';

            $check_author_role = (array)get_role('author');
            $checked_author_role = '';
            if($check_author_role != NULL && $check_author_role['capabilities']['manage_wpsc_support_tickets'] == 1 ) {
                $checked_author_role = 'checked';
            }
            echo '<li><input type="checkbox" name="wstpro_admin[]" '.$checked_author_role.' value="author" /> '.__('Author', 'wpsc-support-tickets-pro').'</li>';
            
            $check_contributor_role = (array)get_role('contributor');
            $checked_contributor_role = '';
            if($check_contributor_role != NULL && $check_contributor_role['capabilities']['manage_wpsc_support_tickets'] == 1 ) {
                $checked_contributor_role = 'checked';
            }            
            echo '<li><input type="checkbox" name="wstpro_admin[]" '.$checked_contributor_role.' value="contributor" /> '.__('Contributor', 'wpsc-support-tickets-pro').'</li>';
            echo '</ul>
        </p>

        <p><strong>'.__('Allow users to close and reopen tickets?', 'wpsc-support-tickets-pro').':</strong> '.__('Setting this to true, allows users (and/or guests, if the setting is turned on) to reopen or close tickets that they have permission to view.  ', 'wpsc-support-tickets-pro').'  <br />
        <select name="allow_closing_ticket">
            ';

            $pagesY = array();
            $pagesY[0] = 'false';            
            $pagesY[1] = 'true';
            foreach ($pagesY as $pagg) {
                $option = '<option value="'.$pagg.'"';
                if($pagg==$devOptions['allow_closing_ticket']) {
                        $option .= ' selected="selected"';
                }
                $option .='>';
                $option .= $pagg;
                $option .= '</option>';
                echo $option;
            }

        echo '
        </select>
        </p>

        <p><strong>'.__('Send HTML Emails?', 'wpsc-support-tickets-pro').':</strong> '.__('Set this to true if you want emails to be sent in HTML format.  Note that you will need to add HTML markup to the emails in the Settings tab to take advantage of this feature.', 'wpsc-support-tickets-pro').'  <br />
        <select name="allow_html">
            ';

            $pagesY = array();
            $pagesY[0] = 'false';            
            $pagesY[1] = 'true';
            foreach ($pagesY as $pagg) {
                $option = '<option value="'.$pagg.'"';
                if($pagg==$devOptions['allow_html']) {
                        $option .= ' selected="selected"';
                }
                $option .='>';
                $option .= $pagg;
                $option .= '</option>';
                echo $option;
            }

        echo '
        </select>
        </p>


        <p><strong>'.__('Allow everyone to see all tickets?', 'wpsc-support-tickets-pro').':</strong> '.__('Setting this to true, allows all guests and users to view all tickets created by anyone. Do not use this setting if tickets will contain ANY confidential information, and be sure to inform your users that their information is being posted publically.', 'wpsc-support-tickets-pro').'  <br />
        <select name="allow_all_tickets_to_be_viewed">
            ';

            $pagesY = array();
            $pagesY[0] = 'false';            
            $pagesY[1] = 'true';
            foreach ($pagesY as $pagg) {
                $option = '<option value="'.$pagg.'"';
                if($pagg==$devOptions['allow_all_tickets_to_be_viewed']) {
                        $option .= ' selected="selected"';
                }
                $option .='>';
                $option .= $pagg;
                $option .= '</option>';
                echo $option;
            }

        echo '
        </select>
        </p>

        <p id="wstpro_reply"><strong>'.__('Allow everyone to reply to all open tickets?', 'wpsc-support-tickets-pro').':</strong> '.__('Setting this to true, allows users (and/or guests, if the setting is turned on) to reply to all open tickets created by anyone.  Requires the *Allow everyone to see all tickets* setting to be set to True.  Do not use this setting if tickets will contain ANY confidential information, and be sure to inform your users that their information is being posted publically.', 'wpsc-support-tickets-pro').'  <br />
        <select name="allow_all_tickets_to_be_replied">
            ';

            $pagesY = array();
            $pagesY[0] = 'false';            
            $pagesY[1] = 'true';
            foreach ($pagesY as $pagg) {
                $option = '<option value="'.$pagg.'"';
                if($pagg==$devOptions['allow_all_tickets_to_be_replied']) {
                        $option .= ' selected="selected"';
                }
                $option .='>';
                $option .= $pagg;
                $option .= '</option>';
                echo $option;
            }

        echo '
        </select>
        </p>

        ';    
}
add_action( 'wpscSupportTickets_settings', 'wstPROSettingsForm' );



/**
 * Save the options
 */
function wstPROSettingsSave() {

    if (@isset($_POST['update_wpscSupportTicketsSettings'])) {    
        $wstpro_check_admin = false;
        $wstpro_check_editor = false;
        $wstpro_check_author = false;
        $wstpro_check_cont = false;
        foreach($_POST['wstpro_admin'] as $check) {
            if($check == 'administrator') {$role = get_role('administrator');$role->add_cap('manage_wpsc_support_tickets');$wstpro_check_admin= true;}
            if($check == 'editor') {$role = get_role('editor');$role->add_cap('manage_wpsc_support_tickets');$wstpro_check_editor= true; }
            if($check == 'author') {$role = get_role('author');$role->add_cap('manage_wpsc_support_tickets');$wstpro_check_author= true;}
            if($check == 'contributor') {$role = get_role('contributor');$role->add_cap('manage_wpsc_support_tickets');$wstpro_check_cont= true;}
        }
        if($wstpro_check_editor===false) { $role = get_role('editor');$role->remove_cap('manage_wpsc_support_tickets');  }
        if($wstpro_check_author===false) { $role = get_role('author');$role->remove_cap('manage_wpsc_support_tickets'); }
        if($wstpro_check_cont===false) { $role = get_role('contributor');$role->remove_cap('manage_wpsc_support_tickets'); }
    }
    
    $devOptions = get_option('wpscSupportTicketsAdminOptions');
    
    if (@isset($_POST['allow_uploads'])) {
            $devOptions['allow_uploads'] = esc_sql($_POST['allow_uploads']);
    }    
    
    if (@isset($_POST['allow_html'])) {
            $devOptions['allow_html'] = esc_sql($_POST['allow_html']);
    }     
    
    if (@isset($_POST['allow_all_tickets_to_be_viewed'])) {
            $devOptions['allow_all_tickets_to_be_viewed'] = esc_sql($_POST['allow_all_tickets_to_be_viewed']);
    }    
    
    if (@isset($_POST['allow_all_tickets_to_be_replied'])) {
            $devOptions['allow_all_tickets_to_be_replied'] = esc_sql($_POST['allow_all_tickets_to_be_replied']);
    }    
    
    if (@isset($_POST['allow_closing_ticket'])) {
            $devOptions['allow_closing_ticket'] = esc_sql($_POST['allow_closing_ticket']);
    }     
    
    update_option('wpscSupportTicketsAdminOptions', $devOptions);
}
add_action( 'admin_init', 'wstPROSettingsSave' );




if (!function_exists('wstPROBulkTabIndex')) {
    function wstPROBulkTabIndex() {
        echo '<li><a href="#wst_tabs-all">'.__('Advanced (PRO Only)','wpsc-support-tickets-pro').'</a></li>';
    }
}

if (!function_exists('wstPROBulkTabContents')) {
    function wstPROBulkTabContents() {
        global $wpdb;
        if ( current_user_can('manage_wpsc_support_tickets')) { 
            

            $devOptions = get_option('wpscSupportTicketsAdminOptions');

            $output .= '
            <script type="text/javascript">
                jQuery(function() {                
                    jQuery( "[href=\'#wst_tabs-all\']").trigger( "click" );
                });

                function wstPRORevealOptions() {
                    jQuery(\'#wstpro_depart\').hide();
                    jQuery(\'#wstpro_severity\').hide();
                    jQuery(\'#wstpro_staff\').hide();
                    if( jQuery(\'#wstpro_bulk_select\').val() == \'department\' ) {
                        jQuery(\'#wstpro_depart\').show();
                    }
                    if( jQuery(\'#wstpro_bulk_select\').val() == \'severity\' ) {
                        jQuery(\'#wstpro_severity\').show();
                    }     
                    if( jQuery(\'#wstpro_bulk_select\').val() == \'staff\' ) {
                        jQuery(\'#wstpro_staff\').show();
                    }                 
                }

            </script>
            ';        

            // Bulk edits
            if(@!empty($_POST['wstpro_bulk'])) {
                foreach($_POST['wstpro_bulk'] as $ticket_id) {
                    switch ($_POST['wstpro_bulk_select']) {
                        case 'close':
                            $wpdb->query("UPDATE `{$wpdb->prefix}wpscst_tickets` SET `resolution`='Closed' WHERE `primkey`='{$ticket_id}';");
                        break;
                        case 'reopen':
                            $wpdb->query("UPDATE `{$wpdb->prefix}wpscst_tickets` SET `resolution`='Open' WHERE `primkey`='{$ticket_id}';");
                        break;
                        case 'delete':
                            $wpdb->query("DELETE FROM `{$wpdb->prefix}wpscst_tickets` WHERE `primkey`='{$ticket_id}';");
                        break;
                        case 'department':
                            $wpscst_department = base64_encode(strip_tags($_POST['wstpro_depart']));
                            $wpdb->query("UPDATE `{$wpdb->prefix}wpscst_tickets` SET `type`='{$wpscst_department}' WHERE `primkey`='{$ticket_id}';");
                        break;            
                        case 'severity':
                            $wpdb->query("UPDATE `{$wpdb->prefix}wpscst_tickets` SET `severity`='{$_POST['wstpro_severity']}' WHERE `primkey`='{$ticket_id}';");
                        break; 
                        case 'staff':
                            $wpdb->query("UPDATE `{$wpdb->prefix}wpscst_tickets` SET `assigned_to`='{$_POST['wstpro_staff']}' WHERE `primkey`='{$ticket_id}';");
                        break;                    
                    }                   
                }

            }



            $output .= '<form action="#" method="post"><div id="wst_tabs-all">';
            $table_name = $wpdb->prefix . "wpscst_tickets";
            $sql = "SELECT * FROM `{$table_name}` ORDER BY `last_updated` DESC;";
            $results = $wpdb->get_results( $sql , ARRAY_A );
            if(isset($results) && isset($results[0]['primkey'])) {

                $output .= '<h3>'.__('View All Tickets:', 'wpsc-support-tickets-pro').'</h3>
                <p>
                '.__('Apply the following action: ', 'wpsc-support-tickets-pro').' <select id="wstpro_bulk_select" name="wstpro_bulk_select" onchange="wstPRORevealOptions();">
                    <option value="close">'.__('Close tickets', 'wpsc-support-tickets-pro').'</option>
                    <option value="reopen">'.__('Re-open tickets', 'wpsc-support-tickets-pro').'</option>
                    <option value="delete">'.__('Delete tickets', 'wpsc-support-tickets-pro').'</option>
                    <option value="department">'.__('Change department to', 'wpsc-support-tickets-pro').'</option>
                    <option value="severity">'.__('Change severity to', 'wpsc-support-tickets-pro').'</option>
                    <option value="staff">'.__('Change staff member assigned to', 'wpsc-support-tickets-pro').'</option>
                </select>

                <select id="wstpro_depart" name="wstpro_depart" style="display:none;">';
                $exploder = explode('||', $devOptions['departments']);
                if(@isset($exploder[0])) {
                    foreach($exploder as $exploded) {
                        $output .= '<option value="'.$exploded.'">'.$exploded.'</option>';
                    }
                }
                $output .='
                </select>

                <select id="wstpro_severity" name="wstpro_severity" style="display:none;">
                    <option value="Low">'.__('Low','wpsc-support-tickets-pro').'</option>
                    <option value="Normal">'.__('Normal','wpsc-support-tickets-pro').'</option>
                    <option value="High">'.__('High','wpsc-support-tickets-pro').'</option>
                    <option value="Critical">'.__('Critical/Urgent','wpsc-support-tickets-pro').'</option>
                </select>

                <select id="wstpro_staff" name="wstpro_staff" style="display:none;">
                <option value="0">'.__('All Staff','wpsc-support-tickets-pro').'</option>';

                $membersQuery = $wpdb->get_results("SELECT `ID`, `user_nicename` FROM `{$wpdb->users}` ORDER BY `ID`;", ARRAY_A);

                foreach ($membersQuery as $thecheckeduser) {
                    if (user_can($thecheckeduser['ID'],  'manage_wpsc_support_tickets')) {
                        $output .='<option value="'.$thecheckeduser['ID'].'">'.$thecheckeduser['user_nicename'].'</option>';
                    }
                }
                
                $output .='
                </select>            

                '.__('on all selected tickets.', 'wpsc-support-tickets-pro').' <input type="submit" class="button-secondary" value="'.__('Apply', 'wpsc-support-tickets-pro').'" />
                </p>
                ';

                $output .= '<table class="widefat" style="width:100%"><thead><tr><th>'.__('Select', 'wpsc-support-tickets-pro').'</th><th>'.__('Ticket', 'wpsc-support-tickets-pro').'</th><th>'.__('Status', 'wpsc-support-tickets-pro').'</th><th>'.__('Severity', 'wpsc-support-tickets-pro').'</th><th>'.__('Creator', 'wpsc-support-tickets-pro').'</th><th>'.__('Staff', 'wpsc-support-tickets-pro').'</th><th>'.__('Department', 'wpsc-support-tickets-pro').'</th><th>'.__('Last Reply', 'wpsc-support-tickets-pro').'</th></tr></thead><tbody>';
                foreach($results as $result) {
                        $ticket_red = false;
                        if($result['user_id']!=0) {
                            @$user=get_userdata($result['user_id']);
                            $theusersname = $user->user_nicename;
                        } else {
                            $user = false; // Guest
                            $theusersname = __('Guest', 'wpsc-support-tickets');
                        }									
                                                            if(trim($result['last_staff_reply'])=='') {
                                                                    $last_staff_reply = __('ticket creator', 'wpsc-support-tickets-pro');
                                                                    $ticket_red = true;
                                                            } else {
                                                                    if($result['last_updated'] > $result['last_staff_reply']) {
                                                                            $last_staff_reply = __('ticket creator', 'wpsc-support-tickets-pro');
                                                                            $ticket_red = true;
                                                                    } else {
                                                                            $last_staff_reply = '<strong>'.__('Staff Member', 'wpsc-support-tickets-pro').'</strong>';
                                                                            $ticket_red = false;
                                                                    }
                                                            }
                        if($result['resolution']=='Open') {
                            if($ticket_red) {
                                $output .= '<tr style="background-color:#f3c9c9;">';
                            } else {
                                $output .= '<tr style="background-color:#FFF;">';
                            }
                        } else {
                        $output .= '<tr  style="background-color:#DDD;">'; 
                        }
                        $output .= '<td><input type="checkbox" name="wstpro_bulk[]" value="'.$result['primkey'].'" /></td><td><a href="admin.php?page=wpscSupportTickets-edit&primkey='.$result['primkey'].'" style="border:none;text-decoration:none;"><img style="float:left;border:none;margin-right:5px;" src="'.plugins_url().'/wpsc-support-tickets/images/page_edit.png" alt="'.__('View', 'wpsc-support-tickets-pro').'"  /> '.base64_decode($result['title']).'</a></td><td>';
                        if($result['resolution']=='Open') {
                            $output .= '<font color="red"><strong>'.$result['resolution'].'</strong></font>';
                        } else {
                            $output .= $result['resolution'];
                        }
                        $output .= '</td>';

                        $output .= '<td>'.$result['severity'].'</td>';

                        $output .= '<td><a href="'.get_admin_url().'user-edit.php?user_id='.$result['user_id'].'&wp_http_referer='.urlencode(get_admin_url().'admin.php?page=wpscSupportTickets-admin').'">'.$theusersname.'</a></td>';

                        if($result['assigned_to']==0) {
                            $output .= '<td>'.__('All Staff', 'wpsc-support-tickets-pro').'</td>';
                        } else {
                            @$userstaff=get_userdata($result['assigned_to']);
                            $theusersnamestaff = $userstaff->user_nicename;                        
                            $output .= '<td><a href="'.get_admin_url().'user-edit.php?user_id='.$result['assigned_to'].'&wp_http_referer='.urlencode(get_admin_url().'admin.php?page=wpscSupportTickets-admin').'">'.$theusersnamestaff.'</a></td>';
                        }
                        $output .= '<td>'.base64_decode($result['type']).'</td>';
                        $output .= '<td>'.date('Y-m-d g:i A',$result['last_updated']).' '.__('by', 'wpsc-support-tickets-pro').' '.$last_staff_reply.'</td>';

                        $output .= '</tr>';
                }
                $output .= '</tbody></table>';
            }
            $output .= '</div></form>';
            echo $output;         
        }
    }
}

add_action( 'wpscSupportTickets_extraTabsIndex', 'wstPROBulkTabIndex' );
add_action( 'wpscSupportTickets_extraTabsContents', 'wstPROBulkTabContents' );



?>