<?php
error_reporting(0);
add_action('widgets_init', create_function('', 'return register_widget("SocialSlider_SocialWidget");'));
class SocialSlider_SocialWidget extends WP_Widget
{

    function SocialSlider_SocialWidget() {
        $widget_ops = array('classname' => 'socialslider', 'description' => __('A widget to display social feeds ', 'socialslider-widget'));
        $control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'socialslider-widget');
        parent::__construct(false, 'Social Slider Widget');
    }

    function widget($args, $instance) {
        extract($args);
        $shortcode = $instance['shortcode'];
        $shortcode = explode('-',$shortcode);
        if($instance['slider_speed']){
            echo '<script>var slider_speed = '.$instance['slider_speed'].';
            </script>';
        }else{
            echo '<script>var slider_speed = 3;
            </script>';
        }


		$feeds = array();
        if($instance['display_info_publicly'] == '1'){
            if(in_array('twitter', $shortcode)){
                $feeds[] = $this->getTwitterFeed($instance['no_twitter_feed']);
            }
           
            if(in_array('customtext', $shortcode)){
                $feeds[] = $this->getCustomTextFeed($instance['no_custom_text']);
            }
           
        }
        shuffle($feeds);
        echo '<div class="widget socialSlider"><div class="widget-wrap">';
        echo '<h4 class="widget-title widgettitle">'.$instance['title'].'</h4>';
        echo '<div>';
        echo '<ul class="bxslider" style="margin:0 !important;">';
        foreach($feeds as $feed){
            echo $feed;
        }
        echo '</ul>';
        echo '</div>';
        echo '</div></div>';
    }

    private function getTwitterFeed($no_of_tweets) {

        //get the twitter oauth controls
        $twitterOauth = get_option(SocialSlider::OPTION_KEY.'_twitter', array());

        $token = $twitterOauth['twitter_access_token'];
        $token_secret = $twitterOauth['twitter_access_token_secret'];
        $consumer_key = $twitterOauth['twitter_consumer_key'];
        $consumer_secret = $twitterOauth['twitter_consumer_secret'];
        $twitter_profile = $twitterOauth['twitter_profile'];

        $host = 'api.twitter.com';
        $method = 'GET';
        $path = '/1.1/statuses/user_timeline.json'; // api call path

        $query = array( // query parameters
            'count' => $no_of_tweets,
            'screen_name'=>$twitter_profile
        );

        $oauth = array(
            'oauth_consumer_key' => $consumer_key,
            'oauth_token' => $token,
            'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
            'oauth_timestamp' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0'
        );

        $oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
        $query = array_map("rawurlencode", $query);

        $arr = array_merge($oauth, $query); // combine the values THEN sort

        asort($arr); // secondary sort (value)
        ksort($arr); // primary sort (key)


        $querystring = urldecode(http_build_query($arr, '', '&'));

        $url = "https://$host$path";

        // mash everything together for the text to hash
        $base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);

        // same with the key
        $key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);

        // generate the hash
        $signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));

        $url .= "?".http_build_query($query);
        $url=str_replace("&amp;","&",$url); //Patch by @Frewuill

        $oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
        ksort($oauth); // probably not necessary, but twitter's demo does it

        // also not necessary, but twitter's demo does this too
        function add_quotes($str) { return '"'.$str.'"'; }
        $oauth = array_map("add_quotes", $oauth);

        // this is the full value of the Authorization line
        $auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

        $options = array( CURLOPT_HTTPHEADER => array("Authorization: $auth"),
                          //CURLOPT_POSTFIELDS => $postfields,
                          CURLOPT_HEADER => false,
                          CURLOPT_URL => $url,
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_SSL_VERIFYPEER => false);

        // do our business
        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);

        $twitter_data = json_decode($json);

          $twitter_data = json_decode($json);
          $return = '';
          foreach($twitter_data as $data){
            if($twitter_profile == ''){
                $twitter_profile = $data->user->screen_name;
            }
            $return.='<li class="twitterBox">';
            $return.='<div class="tweetContent">'.$data->text.'</div>';
            $return.= '<a href="https://twitter.com/'.$twitter_profile.'/statuses/'.$data->id_str.'" target="_blank" class="tweetDate"> '.$data->created_at.'</a>';
            $return.='<div class="tweetOption"><a href="https://twitter.com/intent/tweet?in_reply_to='.$data->id_str.'" target="_blank">Reply</a> <a href="http://twitter.com/intent/retweet?tweet_id='.$data->id_str.'" target="_blank">Retweet</a> <a href="http://twitter.com/intent/favorite?tweet_id='.$data->id_str.'" target="_blank">Favourite</a></div>';
            $return.='<iframe allowtransparency="true" frameborder="0" scrolling="no"
  src="//platform.twitter.com/widgets/follow_button.html?screen_name='.$twitter_profile.'"
  style="width:300px; height:20px;"></iframe>';
            $return.='</li>';
          }

       return $return;
    }

  
    private function getCustomTextFeed($no_custom_text) {
		$customtexts = get_option(SocialSlider::OPTION_KEY.'_customtext', array());
        $customtexts = array_slice($customtexts,0, $no_custom_text);
		if($customtexts){
			$returnMarkup = '';
			foreach($customtexts as $customtext){
				$returnMarkup.= '<li class="customText">';
				$returnMarkup.= $customtext['description'];
				$returnMarkup.= '</li>';
			}
		}
		return $returnMarkup;

    }

   

    //Update the widget

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        //Strip tags from title and name to remove HTML
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['shortcode'] = strip_tags($new_instance['shortcode']);
        $instance['no_twitter_feed'] = strip_tags($new_instance['no_twitter_feed']);
        $instance['no_custom_text'] = strip_tags($new_instance['no_custom_text']);
        $instance['slider_speed'] = strip_tags($new_instance['slider_speed']);
        $instance['display_info_publicly'] = $new_instance['display_info_publicly'];
        return $instance;
    }

    function form($instance) {

        //Set up some default widget settings.
        $defaults = array('title' => __('', 'socialslider-widget'), 'shortcode' => __('', 'socialslider-widget'), 'display_info_publicly' => true,'slider_speed'=>__('3','socialslider-widget'),'no_testimonial'=>__('5','socialslider-widget'),'no_facebook_post'=>__('5','socialslider-widget'),'no_twitter_feed'=>__('5','socialslider-widget'),'no_custom_text'=>__('5','socialslider-widget'),'no_image'=>__('5','socialslider_widget'));

        $instance = wp_parse_args((array)$instance, $defaults); ?>

		<p>
			<label for="<?php
        echo $this->get_field_id('title'); ?>"><?php
        _e('Title:', 'socialslider-widget'); ?></label>
			<input id="<?php
        echo $this->get_field_id('title'); ?>" name="<?php
        echo $this->get_field_name('title'); ?>" value="<?php
        echo $instance['title']; ?>" style="width:100%;" />
		</p>

    
        <p>
            <label for="<?php
        echo $this->get_field_id('no_twitter_feed'); ?>"><?php
        _e('No of tweets:', 'socialslider-widget'); ?></label>
            <input id="<?php
        echo $this->get_field_id('no_twitter_feed'); ?>" name="<?php
        echo $this->get_field_name('no_twitter_feed'); ?>" value="<?php
        echo $instance['no_twitter_feed']; ?>" style="width:100%;" />
        </p>
     
        <p>
            <label for="<?php
        echo $this->get_field_id('no_custom_text'); ?>"><?php
        _e('No of custom texts:', 'socialslider-widget'); ?></label>
            <input id="<?php
        echo $this->get_field_id('no_custom_text'); ?>" name="<?php
        echo $this->get_field_name('no_custom_text'); ?>" value="<?php
        echo $instance['no_custom_text']; ?>" style="width:100%;" />
        </p>

    

        <p>
            <label for="<?php
        echo $this->get_field_id('slider_speed'); ?>"><?php
        _e('Slider Speed in seconds:', 'socialslider-widget'); ?></label>
            <input id="<?php
        echo $this->get_field_id('slider_speed'); ?>" name="<?php
        echo $this->get_field_name('slider_speed'); ?>" value="<?php
        echo $instance['slider_speed']; ?>" style="width:100%;" />
        </p>

		<p>
			<label for="<?php
        echo $this->get_field_id('shortcode'); ?>"><?php
        _e('Social shortcode', 'socialslider-widget'); ?></label>
                        <input id="<?php
                    echo $this->get_field_id('shortcode'); ?>" name="<?php
                    echo $this->get_field_name('shortcode'); ?>" value="<?php
                    echo $instance['shortcode']; ?>" style="width:100%;" />
              eg: twitter-customtext or twitter or customtext etc
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php
        checked($instance['display_info_publicly'], true); ?> id="<?php
        echo $this->get_field_id('display_info_publicly'); ?>" name="<?php
        echo $this->get_field_name('display_info_publicly'); ?>" value="1"/>
			<label for="<?php
        echo $this->get_field_id('display_info_publicly'); ?>"><?php
        _e('Display info publicly', 'socialslider-facebook'); ?></label>
		</p>

	<?php
    }

}
?>