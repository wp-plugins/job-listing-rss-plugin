<?php
/*
Plugin Name: Job Listing Rss Feed
Plugin URI: http://www.guyro.com/job-listing-rss-plugin
Description: Adds a customizeable widget which displays the latest available Job Listings from an NYC Listing Board. 
Version: 1.0
Author: Guy Roman
Author URI: http://www.guyro.com
License: GPL3
*/

function jobnews()
{
  $options = get_option("widget_jobnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Job Listings',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Feed 
  $rss = simplexml_load_file( 
  'http://hotjobs.yahoo.com/job-rss-l-New_York-NY-d-FT-d-PT-j-PERM-j-CONT'); 
  ?> 
  
  <ul> 
  
  <?php 
  // max number of news slots, with 0 (zero) all display
  $max_news = $options['news'];
  // maximum length to which a title may be reduced if necessary,
  $max_length = $options['chars'];
  
  // RSS Elements 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Title
    $title = $i->title;
    // Length of title
    $length = strlen($title);
    // if the title is longer than the previously defined maximum length,
    // it'll he shortened and "..." added, or it'll output normaly
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_jobnews($args)
{
  extract($args);
  
  $options = get_option("widget_jobnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Job Listings',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  jobnews();
  echo $after_widget;
}

function jobnews_control()
{
  $options = get_option("widget_jobnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Job Listings',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['jobnews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['jobnews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['jobnews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['jobnews-CharCount']);
    update_option("widget_jobnews", $options);
  }
?> 
  <p>
    <label for="jobnews-WidgetTitle">Widget Title: </label>
    <input type="text" id="jobnews-WidgetTitle" name="jobnews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="jobnews-NewsCount">Max. News: </label>
    <input type="text" id="jobnews-NewsCount" name="jobnews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="jobnews-CharCount">Max. Characters: </label>
    <input type="text" id="jobnews-CharCount" name="jobnews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="jobnews-Submit"  name="jobnews-Submit" value="1" />
  </p>
  
<?php
}

function jobnews_init()
{
  register_sidebar_widget(__('Job Listings'), 'widget_jobnews');    
  register_widget_control('Job Listings', 'jobnews_control', 300, 200);
}
add_action("plugins_loaded", "jobnews_init");
?>