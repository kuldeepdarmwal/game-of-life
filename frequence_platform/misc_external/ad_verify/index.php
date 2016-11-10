<?php

//$derp = shell_exec("python /home/adverify/bin/ad_verify.py &;

?>

<html> 
<head><title>Screenshot Bot v.2</title><head>
<script>

var rand_id = 0;

function run_script()
{

    var blank_fields = new Array();
  var search_string = document.getElementById("search_string").value;
  var cookies_string = document.getElementById("cookie_list").value;
  var site_list_string = document.getElementById("sites_list").value;
  var dest_dir = document.getElementById("dest_dir").value;
  var max_loops = document.getElementById("max_loops").value;
  var max_depth = document.getElementById("max_depth").value;
  
  if (!/[^\s]+/.test(search_string))
  {
      blank_fields.push("Search String");     
  }
  if (!/[^\s]+/.test(site_list_string))
  {
      blank_fields.push("Sites List");
  }
  if (!/[^\s]+/.test(dest_dir))
  {
      blank_fields.push("Destination Directory");
  }
  if (!/[^\s]+/.test(max_loops))
  {
      blank_fields.push("Max # of Loops");
  }
  if (!/[^\s]+/.test(max_depth))
  {
      blank_fields.push("Max Frame Depth");
  }

  if(blank_fields.length > 0){
      var alert_string = "The following fields shouldn't be left blank:\n";
      for(i = 0; i < blank_fields.length; i++)
      {
	  alert_string += blank_fields[i] + "\n";
      }
      alert(alert_string);
      return;
  }

  //alert(cookies_string);

  var request = new XMLHttpRequest;
  request.open("POST", "execute_script.php", true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  rand_id = Math.floor((Math.random()*200000)+1);
  var params = "search_string="+search_string+"&cookie_string="+cookies_string+"&site_list_string="+site_list_string+"&dest_dir="+dest_dir+"&max_loops="+max_loops+"&max_depth="+max_depth+"&kill_id="+rand_id;
//  alert(params);
  request.send(params);
  document.getElementById("start_button").style.visibility = "hidden";
  document.getElementById("submitted").style.visibility = "visible";
  document.getElementById("stop_button").style.visibility = "visible";
  
}

function stop_script()
{
    var kill_request = new XMLHttpRequest;
    
    kill_request.open("POST", "kill_script.php", true);
    kill_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    var params = "kill_id="+rand_id;
//   alert(params);
    kill_request.send(params);

    
    document.getElementById("submitted").innerHTML = "<strong>Killing process, please wait...</strong>";
    document.getElementById("stop_button").style.visibility = "hidden";
    window.setTimeout(reinit_butans, 8000);
    
}

function reinit_butans()
{

    document.getElementById("start_button").style.visibility = "visible";
    document.getElementById("submitted").style.visibility = "hidden";
    document.getElementById("stop_button").style.visibility = "hidden";
    document.getElementById("submitted").innerHTML = "<strong>REQUEST SUBMITTED!</strong> - You can find logs detailing bot jobs <a href='/screensho\
ts/logs/'>here</a> - <a href='/screenshots/'>Gallery</a>";


}

function fill_in(campaign)
{
    var json = campaign.options[campaign.selectedIndex].value;
    var hold = JSON.parse(json);
    var b_name = hold[0];
    //alert(hold[2]);
    // alert(campaign.options[campaign.selectedIndex].value);
    document.getElementById("cookie_list").value = "http://adverify.vantagelocalstage.com/cookie_monster.php?id="+b_name;
    //document.getElementById("cookie_list").value = "http://google.com/";
    document.getElementById("sites_list").value = hold[2];
    document.getElementById("search_string").value = hold[1];
    
}
</script>


<body>
<h2>Screenshot Bot</h2>
   <table>
   <tr>
   <td>Campaign: </td>
   <td><select name="campaigns" onchange="fill_in(this);">
    <option>----SELECT A CAMPAIGN----</option>
    <?php 
    $db = mysql_connect('db.vantagelocaldev.com', 'vldevuser', 'L0cal1s1n!');
    mysql_select_db('vantagelocal_dev');
$one_month_ago = date("Y-m-d", strtotime('-1 month'));
    $active_campaign_query = "
       SELECT c.Name as Name, a.Name as Business, c.LandingPage as Page, c.id as id
      FROM Campaigns c LEFT JOIN Advertisers a ON (c.business_id = a.id) JOIN AdGroups ad ON (c.id = ad.campaign_id)
      JOIN CityRecords cy ON (ad.ID = cy.AdGroupId) 
      WHERE c.ignore_for_healthcheck = 0 
      GROUP BY c.id ORDER BY a.Name ASC";
echo "\n".$active_campaign_query;
    $response = mysql_query($active_campaign_query);
    $campaigns = array();

    while($row = mysql_fetch_assoc($response))
    {

	array_push($campaigns, $row); 
    }

    foreach($campaigns as $a_campaign)
    {
	$search_string = "engvlx4ie".str_pad($a_campaign['id'], 7, '0', STR_PAD_LEFT).'rlp';
	$top_site_query = "
SELECT b.Base_site AS Site,
SUM(b.Impressions) AS Imps
FROM
AdGroups a LEFT JOIN SiteRecords b ON (a.ID = b.AdGroupID)

WHERE
b.Base_site != 'All other sites' AND
a.BusinessName = '".$a_campaign['Business']."' AND
a.CampaignName RLIKE '".$a_campaign['Name']."' AND
b.Date > '2012-11-10'
GROUP BY Site ORDER BY Imps DESC  LIMIT 32";

	$second_result = mysql_query($top_site_query);

	$site_list = "";

	while($row = mysql_fetch_assoc($second_result))
	{
	    $site_list .="http://".$row['Site']."\n";
	}


	$to_json = array(urlencode($a_campaign['id']), $search_string, $site_list);
	
	
	

	
	$json = json_encode($to_json);
	echo "<option value='".$json."'>".$a_campaign['Business']." - ".$a_campaign['Name']."</option>";
    }
    ?>
    </select>
   </tr>
   <tr>
   <td>Search: </td>
   <td><input type="text" size="30" id="search_string"></td>
   </tr>
   <tr>
   <td>Cookies: </td>
   <td><textarea rows="5" cols="50" id="cookie_list"></textarea></td>
   <td><p style="font-size:70%;">URLs to fetch<br> cookies</p></td>

   </tr>
   <tr>
   <td>Sites: </td>
   <td><textarea rows="5" cols="50" id="sites_list"></textarea></td>
   <td><p style="font-size:70%;">URLs of sites to<br> search for ad on</p></td>

   </tr>
</table>
<table>
   <tr>Screenshot Destination:</tr>
   <tr><input type="text" size="50" id="dest_dir" value="/home/adverify/public/adverify.vantagelocalstage.com/screenshots/"></td>
</table>
<table>
   <tr>
   <td>Max # of Checks</td>
   <td><input type="text" size="5" id="max_loops" value="200"></td>
   <td>Max Frame Depth</td>
   <td><input type="text" size="3" id="max_depth" value="15"></td>
   </tr>
</table>
<button name="go" value="Run!" onClick="run_script();" id="start_button">Run!</button>
<div id="submitted" style="visibility: hidden;"><strong>REQUEST SUBMITTED!</strong> - You can find logs detailing bot jobs <a href="/screenshots/logs/">here</a> - <a href="/screenshots/">Gallery</a></div>
    <button name="stop" value="STOP!" onClick="stop_script();" id="stop_button" style="visibility: hidden;">No, wait! Stop!</stop>
</body>

</html>