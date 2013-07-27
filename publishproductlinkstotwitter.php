<?php

if (!defined('_PS_VERSION_'))  {
    exit;
}

class PublishProductLinksToTwitter extends Module 
{
    public function __construct()
    {
        $this->name = 'publishproductlinkstotwitter';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0';
        $this->author = 'Kamba Abudu';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->is_configurable = 1;
		$this->module_key = 'd8b57cddf079b455b96d1f5554be90c1';
        
        parent::__construct();
        
        $this->displayName = $this->l('Publish product links to Twitter');
        $this->description = $this->l('This module enables your Prestashop store to publish product titles, prices and links for your products to Twitter periodically, automatically via a cron job (which you will need to set up). The current version selects products at random but a future version may allow you to specify certain rules for how products should be selected. You need to create a Twitter Application for your prestashop store as you will need the provided consumer key/secret and access token/secret to configure the module.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        
        if (!Configuration::get('PLTOTWITTER_CONSUMER_KEY') 
                || !Configuration::get('PLTOTWITTER_CONSUMER_SECRET')
                || !Configuration::get('PLTOTWITTER_ACCESS_TOKEN')
                || !Configuration::get('PLTOTWITTER_ACCESS_TOKEN_SECRET')) 
        {
            $this->warning = $this->l('No Twitter Consumer Key/Secret, Access token/secret pair provided'); 
        }        
    }
    
    public function install()
    {
      if (parent::install() == false) 
      {
          return false;
      }
      Configuration::updateValue('PLTOTWITTER_LIMIT', 1);
      return true;
    }
    
    public function uninstall()
    {
      return parent::uninstall() 
              && Configuration::deleteByName('PLTOTWITTER_LIMIT')
              && Configuration::deleteByName('PLTOTWITTER_CONSUMER_KEY')
              && Configuration::deleteByName('PLTOTWITTER_CONSUMER_SECRET')
              && Configuration::deleteByName('PLTOTWITTER_ACCESS_TOKEN')
              && Configuration::deleteByName('PLTOTWITTER_ACCESS_TOKEN_SECRET');
    }
    
    public function getContent()
    {
        $output = null;
 
        if (Tools::isSubmit('submit'.$this->name))
        {
            $error = false;
            $limit = strval(Tools::getValue('PLTOTWITTER_LIMIT'));
            $consumer_key = strval(Tools::getValue('PLTOTWITTER_CONSUMER_KEY'));
            $consumer_secret = strval(Tools::getValue('PLTOTWITTER_CONSUMER_SECRET'));
            $access_token = strval(Tools::getValue('PLTOTWITTER_ACCESS_TOKEN'));
            $access_token_secret = strval(Tools::getValue('PLTOTWITTER_ACCESS_TOKEN_SECRET'));
            if (!$consumer_key  || empty($consumer_key)) 
            {
                $output .= $this->displayError( $this->l('The Twitter Consumer Key cannot be empty') );
                $error = true;
            }
            if(!$consumer_secret  || empty($consumer_secret)) 
            {
                $output .= $this->displayError( $this->l('The Twitter Consumer Secret cannot be empty') );
                $error = true;
            }
            if(!$limit || !is_int((int)$limit)) 
            {
                $output .= $this->displayError( $this->l('The Number of Products to Publish cannot be zero or empty') );
                $error = true;
            }
            if(!$access_token  || empty($access_token)) 
            {
                $output .= $this->displayError( $this->l('The Twitter Access Token cannot be empty') );
                $error = true;
            }
            if(!$access_token_secret  || empty($access_token_secret)) 
            {
                $output .= $this->displayError( $this->l('The Twitter Access Token Secret cannot be empty') );
                $error = true;
            }
            if($error == false)
            {
                Configuration::updateValue('PLTOTWITTER_LIMIT', $limit);
                Configuration::updateValue('PLTOTWITTER_CONSUMER_KEY', $consumer_key);
                Configuration::updateValue('PLTOTWITTER_CONSUMER_SECRET', $consumer_secret);
                Configuration::updateValue('PLTOTWITTER_ACCESS_TOKEN', $access_token);
                Configuration::updateValue('PLTOTWITTER_ACCESS_TOKEN_SECRET', $access_token_secret);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        
        return $output.$this->displayForm();
    }
    
    public function displayForm()
    {
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form = array();
        
        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Twitter Consumer Key'),
                    'name' => 'PLTOTWITTER_CONSUMER_KEY',
                    'size' => 50,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Twitter Consumer Secret'),
                    'name' => 'PLTOTWITTER_CONSUMER_SECRET',
                    'size' => 100,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Twitter Access Token'),
                    'name' => 'PLTOTWITTER_ACCESS_TOKEN',
                    'size' => 100,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Twitter Access Token Secret'),
                    'name' => 'PLTOTWITTER_ACCESS_TOKEN_SECRET',
                    'size' => 100,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Number of Products to Publish'),
                    'name' => 'PLTOTWITTER_LIMIT',
                    'size' => 10,
                    'required' => true
                )
            ),            
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
        
        $helper = new HelperForm();
     
        // Module, t    oken and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['PLTOTWITTER_CONSUMER_KEY'] = Configuration::get('PLTOTWITTER_CONSUMER_KEY');
        $helper->fields_value['PLTOTWITTER_CONSUMER_SECRET'] = Configuration::get('PLTOTWITTER_CONSUMER_SECRET');
        $helper->fields_value['PLTOTWITTER_ACCESS_TOKEN'] = Configuration::get('PLTOTWITTER_ACCESS_TOKEN');
        $helper->fields_value['PLTOTWITTER_ACCESS_TOKEN_SECRET'] = Configuration::get('PLTOTWITTER_ACCESS_TOKEN_SECRET');
        $helper->fields_value['PLTOTWITTER_LIMIT'] = Configuration::get('PLTOTWITTER_LIMIT');

        return $helper->generateForm($fields_form);
    }
    
    
}
  
