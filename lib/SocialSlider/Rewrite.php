<?php

class SocialSlider_Rewrite
{

    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this,'theme_name_scripts' ));
    }

    public function theme_name_scripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('bxsliderjs', plugins_url().'/'.plugin_basename(__DIR__).'/../../js/jquery.bxslider.min.js',array(),'',5);
        wp_enqueue_script('sliderjs', plugins_url().'/'.plugin_basename(__DIR__).'/../../js/slider.js',array(),'',5);
        wp_enqueue_style( 'bxslider', plugins_url().'/'.plugin_basename(__DIR__).'/../../css/jquery.bxslider.css');
        wp_enqueue_style( 'widgetcss', plugins_url().'/'.plugin_basename(__DIR__).'/../../css/widget.css');
    }


}