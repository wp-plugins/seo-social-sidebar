<?php
class SocialSlider_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'menu'));
    }

    /**
     * Register the Menu.
     */
    public function menu() {
        add_menu_page(
                'SocialSlider', 'SocialSlider', 'administrator', plugin_basename(SocialSlider::FILE), array($this, 'addTwitter')
        );
         add_submenu_page(
                plugin_basename(SocialSlider::FILE), 'Twitter Settings', 'Twitter Settings', 'administrator', plugin_basename(SocialSlider::FILE).'-addTwitter', array($this, 'addTwitter')
        );
      
        add_submenu_page(
                plugin_basename(SocialSlider::FILE), 'Custom Text', 'Custom Text', 'administrator', plugin_basename(SocialSlider::FILE).'-customtext', array($this, 'customtext')
        );

        add_submenu_page(
                plugin_basename(SocialSlider::FILE), 'Add Custom Text', 'Add Custom Text', 'administrator', plugin_basename(SocialSlider::FILE).'-customtext&action=add', array($this, 'addcustomtext')
        );

        add_submenu_page(
                plugin_basename(SocialSlider::FILE), 'Upload premium file', 'Upload premium file', 'administrator', plugin_basename(SocialSlider::FILE).'-addPremium', array($this, 'addPremium')
        );
      
    }
   
    public function addPremium()
    {
        if(isset($_POST) && $_POST['action'] == 'addfile'){
          if($_FILES['premium_file']['name']){
              move_uploaded_file($_FILES['premium_file']['tmp_name'], plugin_dir_path(__FILE__).'/../../'.$_FILES['premium_file']['name']);
              $zip = new ZipArchive;
              if ($zip->open(plugin_dir_path(__FILE__).'/../../'.$_FILES['premium_file']['name']) === TRUE) {
                  $zip->extractTo(plugin_dir_path(__FILE__).'/../../');
                  $zip->close();
                  @unlink(plugin_dir_path(__FILE__).'/../../'.$_FILES['premium_file']['name']);
                  $this->addMessage('Premium files has been added successfully','success');    
              } else {
                  echo 'file could not be uploaded';
              }
          }  
        }
        echo SocialSlider_View::render('admin_uploadfile',array()); 
    }


    public function customtext()
    {
        if(isset($_GET['action']) && $_GET['action'] == 'add'){
             $this->addCustomText();
             die;      
        }

        if(isset($_GET['action']) && $_GET['action'] == 'edit'){
             $this->editCustomText();
             die;      
        }

        if(isset($_GET['action']) && $_GET['action'] == 'delete'){
             $this->deleteCustomText($_GET['id']);
             die;      
        }
          $queryArgs = array(
              'page' => plugin_basename(SocialSlider::FILE.'-customtext'),
          );
          $customtext = get_option(SocialSlider::OPTION_KEY.'_customtext', array());
          $data = array(
              'queryArgs' => $queryArgs,
              'baseUrl' => admin_url('/admin.php'),
              'customtexts' => $customtext,
          );
          echo SocialSlider_View::render('admin_customtext', $data); 

    }

    function addCustomText()
    {
        if(isset($_POST['action']) && $_POST['action'] == 'addcustomtext')
        {
           $options = get_option(SocialSlider::OPTION_KEY.'_customtext', array());
           $data['description'] = $_POST['description'];
           $options[] = $data;
           update_option(SocialSlider::OPTION_KEY.'_customtext', $options);
           $this->addMessage("Custom text is added successfully");
           $this->redirectUrl(get_bloginfo('wpurl')."/wp-admin/admin.php?page=seo-social-sidebar/socialslider.php-customtext");

        }
        $queryArgs = array(
            'page' => plugin_basename(SocialSlider::FILE.'-customtext&action=add'),
        );
        $data = array(
            'queryArgs' => $queryArgs,
            'baseUrl' => admin_url('/admin.php'),
        );
        echo SocialSlider_View::render('admin_addcustomtext', $data); 
    }

    function editCustomText()
    {
        if(isset($_POST['action']) && $_POST['action'] == 'editcustomtext'){
            $options = get_option(SocialSlider::OPTION_KEY.'_customtext', array());
            $data['description'] = $_POST['description'];
            $options[$_POST['id']] = $data;
            update_option(SocialSlider::OPTION_KEY.'_customtext', $options);
            $this->addMessage("Custom text is updated successfully");
            $this->redirectUrl(get_bloginfo('wpurl')."/wp-admin/admin.php?page=seo-social-sidebar/socialslider.php-customtext");
        }
        $queryArgs = array(
            'page' => plugin_basename(SocialSlider::FILE.'-customtext&action=edit'),
        );

        $data = array(
            'queryArgs' => $queryArgs,
            'baseUrl' => admin_url('/admin.php'),
            'customtext'=>get_option(SocialSlider::OPTION_KEY.'_customtext', array()),
            'editCustomtext'=>$_GET['id']
        );
        echo SocialSlider_View::render('admin_addcustomtext', $data);
    }

    function deleteCustomText($id)
    {
        $a = get_option(SocialSlider::OPTION_KEY.'_customtext', array());
        unset($a[$id]);
        update_option(SocialSlider::OPTION_KEY.'_customtext', $a);
        $this->addMessage('Custom text is deleted successfully');
        $this->redirectUrl(get_bloginfo('wpurl')."/wp-admin/admin.php?page=seo-social-sidebar/socialslider.php-customtext");
    }



    public function addTwitter()
    {   
        if(isset($_POST['action'])){
            $options = get_option(SocialSlider::OPTION_KEY.'_twitter', array());
            $data['twitter_profile'] = $_POST['twitter_profile'];
            $data['twitter_consumer_key'] = $_POST['twitter_consumer_key'];
            $data['twitter_consumer_secret'] = $_POST['twitter_consumer_secret'];
            $data['twitter_access_token'] = $_POST['twitter_access_token'];
            $data['twitter_access_token_secret'] = $_POST['twitter_access_token_secret'];
            $options = $data;
            update_option(SocialSlider::OPTION_KEY.'_twitter', $options);
            $this->addMessage('Twitter widget settings is saved successfully','success');        
        }

        $templates = get_option(SocialSlider::OPTION_KEY, array());

        $queryArgs = array(
            'page' => plugin_basename(SocialSlider::FILE).'-addtwitter',
        );

         $data = array(
             'queryArgs' => $queryArgs,
             'baseUrl' => admin_url('/admin.php'),
             'templates'=>$templates,
             'result'=>get_option(SocialSlider::OPTION_KEY.'_twitter', array())
         );
         echo SocialSlider_View::render('admin_addtwitter', $data);

    }


    private function addMessage($msg, $type = 'success') {
        if ($type == 'success') {
            printf(
                    "<div class='updated'><p><strong>%s</strong></p></div>", $msg
            );
        } else {
            printf(
                    "<div class='error'><p><strong>%s</strong></p></div>", $msg
            );
        }
    }

    private function redirectUrl($url) {
        echo '<script>';
        echo 'window.location.href="' . $url . '"';
        echo '</script>';
    }

}
?>
