<?php
class KalturaHelpers
{
	static $platfromConfig = null;

	function importCE($url, $email, $password, &$secret, &$adminSecret, &$partner)
	{
		$kConfig = new KalturaConfiguration(0);
		$kConfig->serviceUrl = $url;
		$kClient = new KalturaClient($kConfig);
//		$kPartner = $kClient -> partner -> getSecrets(1, $email, $password);
//		$partner = 1;
		$ks = $kClient -> adminUser -> login($email, $password);
		$kClient -> setKs($ks);
		$kPartner = $kClient -> partner -> getInfo();
		$secret = $kPartner -> secret;
		$adminSecret = $kPartner -> adminSecret;	
		$partner = $kPartner -> id;
	}
 
	function register($name, $email, &$secret, &$adminSecret, &$partner, $phone="", 
			 $description="", $ver="", $describeYourself="", $webSiteUrl="", $contentCategory="",$adultContent=false)
	{
		$kConfig = new KalturaConfiguration(0);
		$kConfig->serviceUrl = KalturaSettings_SERVER_URL;
		$kClient = new KalturaClient($kConfig);
		$kPartner = new KalturaPartner();
		$kPartner -> name = $name;
		$kPartner -> adminName = $name;
		$kPartner -> adminEmail =  $email;
		$kPartner -> phone = $phone;
		$kPartner -> describeYourself = $describeYourself;
		$kPartner -> website = $webSiteUrl;
		$kPartner -> contentCategories = $contentCategory;
		$kPartner -> adultContent = $adultContent;
		$kPartner -> description = $description . "\n|" . "Moodle|" . $ver;
		$kPartner -> commercialUse = "non-commercial_use";
		$kPartner -> type = 104;
		$kPartner = $kClient -> partner -> register ($kPartner);

		$partner  = $kPartner -> id;
		$secret = $kPartner -> secret;
    $adminSecret = $kPartner -> adminSecret;
	}

	function getContributionWizardFlashVars($ks, $type="", $kshowId=-2, $partner_data="",  $comment=false)
	{
		$sessionUser = KalturaHelpers::getSessionUser();
		$config = KalturaHelpers::getServiceConfiguration();
		
		$flashVars = array();

		$flashVars["userId"] = $sessionUser->userId;
		$flashVars["sessionId"] = $ks;

		if ($sessionUserId == KalturaSettings_ANONYMOUS_USER_ID) {
			 $flashVars["isAnonymous"] = true;
		}
			
//		$flashVars["partnerId"] 	= 1;
//		$flashVars["subPartnerId"] 	= 100;
		$flashVars["partnerId"] 	= $config->partnerId;
//		$flashVars["subPartnerId"] 	= $config->subPartnerId;
/*		if ($kshowId)
			// TODO: change the following line for roughcut
			$flashVars["kshow_id"] 	= ($type == 'entry')? $type.'-'.$kshowId: $kshowId;
		else*/
			$flashVars["kshow_id"] 	= -2;
		
		$flashVars["afterAddentry"] 	= "onContributionWizardAfterAddEntry";
		$flashVars["close"] 		= "onContributionWizardClose";
		$flashVars["partnerData"] 	= $partner_data;
		
    
		if (!$comment)
    {
      if ($type == KalturaEntryType::MEDIA_CLIP)
      {
			  $flashVars["uiConfId"] 		= KalturaHelpers::getPlatformKey("uploader_regular",KalturaSettings_CW_REGULAR_UICONF_ID);
      }
      else
      {
			  $flashVars["uiConfId"] 		= KalturaHelpers::getPlatformKey("uploader_mix",KalturaSettings_CW_MIX_UICONF_ID);
      }
    }
		else
			$flashVars["uiConfId"] 		= KalturaSettings_CW_COMMENTS_UICONF_ID;
			
		$flashVars["terms_of_use"] 	= "http://corp.kaltura.com/tandc" ;
		
		return $flashVars;
	}
	
	function getSimpleEditorFlashVars($ks, $kshowId, $type, $partner_data)
	{
		$sessionUser = KalturaHelpers::getSessionUser();
		$config = KalturaHelpers::getServiceConfiguration();
		
		$flashVars = array();
		
		if($type == 'entry')
		{
			$flashVars["entry_id"] 		= $kshowId;
			$flashVars["kshow_id"] 		= 'entry-'.$kshowId;
		} else {
			$flashVars["entry_id"] 		= -1;
			$flashVars["kshow_id"] 		= $kshowId;
		}

		$flashVars["partner_id"] 	= $config->partnerId;;
		$flashVars["partnerData"] 	= $partner_data;
		$flashVars["subp_id"] 		= $config->subPartnerId;
		$flashVars["uid"] 			= $sessionUser->userId;
		$flashVars["ks"] 			= $ks;
		$flashVars["backF"] 		= "onSimpleEditorBackClick";
		$flashVars["saveF"] 		= "onSimpleEditorSaveClick";
		$flashVars["uiConfId"] 		= KalturaHelpers::getPlatformKey("editor",null);
		
		return $flashVars;
	}
	
	function getKalturaPlayerFlashVars($ks, $kshowId = -1, $entryId = -1)
	{
		$sessionUser = KalturaHelpers::getSessionUser();
//		$config = KalturaHelpers::getServiceConfiguration();
		
		$flashVars = array();
		
//		$flashVars["kshowId"] 		= $kshowId;
//		$flashVars["entryId"] 		= $entryId;
//		$flashVars["partner_id"] 	= $config->partnerId;
//		$flashVars["subp_id"] 		= $config->subPartnerId;
		$flashVars["externalInterfaceDisabled"] = 0;		
		$flashVars["uid"] 			= $sessionUser->userId;
//		$flashVars["ks"] 			= $ks;
		
		return $flashVars;
	}
	
	function flashVarsToString($flashVars)
	{
		$flashVarsStr = "";
		foreach($flashVars as $key => $value)
		{
			$flashVarsStr .= ($key . "=" . urlencode($value) . "&"); 
		}
		return substr($flashVarsStr, 0, strlen($flashVarsStr) - 1);
	}
	
	function getSwfUrlForBaseWidget() 
	{
		return KalturaHelpers::getSwfUrlForWidget(KalturaSettings_BASE_WIDGET_ID);
	}
	
	function getSwfUrlForWidget($widgetId)
	{
		return KalturaHelpers::getKalturaServerUrl() . "/kwidget/wid/_" . $widgetId;
	}
	
	function getContributionWizardUrl($type)
	{
      if ($type == KalturaEntryType::MEDIA_CLIP)
      {
			  return KalturaHelpers::getKalturaServerUrl() . "/kcw/ui_conf_id/" . KalturaHelpers::getPlatformKey("uploader_regular",KalturaSettings_CW_REGULAR_UICONF_ID);
      }
      else
      {
			  return KalturaHelpers::getKalturaServerUrl() . "/kcw/ui_conf_id/" . KalturaHelpers::getPlatformKey("uploader_mix",KalturaSettings_CW_MIX_UICONF_ID);
      }
  }
	
  function getPlayer($type, $design)
  {
    $full_name = 'player_' . ($type == KalturaEntryType::MEDIA_CLIP ? 'regular_' : 'mix_') . $design;
    $cnfg = get_record('config_plugins', 'plugin','kaltura','name', $full_name);
    
    return $cnfg->value;
  }
  
  function getDesigns($type)
  {
    global $CFG;
    $arr = array();
    if ($type == KalturaEntryType::MEDIA_CLIP)
    {
      $temp_arr = get_records_sql('select name from ' . $CFG->prefix . 'config_plugins where plugin="kaltura" and name like "player_regular%"');
    }
    else
    {
       $temp_arr = get_records_sql('select name from ' . $CFG->prefix . 'config_plugins where plugin="kaltura" and name like "player_mix%"');
    }
    
    foreach($temp_arr as $k=>$v)
    {
      $parts =  explode ("_", $v->name); // the convention is player_mix_THENAME or player_regular_THENAME
      $arr[$parts[count($parts)-1]] = $parts[count($parts)-1];
    }
    
    return $arr;
  }
  
  function getPlayers($type)
  {
    global $CFG;
    $arr = array();
    if ($type == KalturaEntryType::MEDIA_CLIP)
    {
      $temp_arr = get_records_sql('select name,value from ' . $CFG->prefix . 'config_plugins where plugin="kaltura" and name like "player_regular%"');
    }
    else
    {
       $temp_arr = get_records_sql('select name,value from ' . $CFG->prefix . 'config_plugins where plugin="kaltura" and name like "player_mix%"');
    }
    
    foreach($temp_arr as $k=>$v)
    {
      $parts =  explode ("_", $v->name); // the convention is player_mix_THENAME or player_regular_THENAME
      $arr[$parts[count($parts)-1]] = $v->value;
    }
    
    return $arr;
  }
  
	function getSimpleEditorUrl($uiConfId = null)
	{
		if ($uiConfId)
			return KalturaHelpers::getKalturaServerUrl() . "/kse/ui_conf_id/" . $uiConfId;
		else
			return KalturaHelpers::getKalturaServerUrl() . "/kse/ui_conf_id/" . KalturaSettings_SE_UICONF_ID;
	}
	
	function getThumbnailUrl($widgetId = null, $entryId = null, $width = 240, $height= 180)
	{
		$config = KalturaHelpers::getServiceConfiguration();
		$url = KalturaHelpers::getKalturaServerUrl();
		$url .= "/p/" . $config->partnerId;
		$url .= "/sp/" . $config->partnerId * 100;
		$url .= "/thumbnail";
		if ($widgetId)
			$url .= "/widget_id/" . $widgetId;
		else if ($entryId)
			$url .= "/entry_id/" . $entryId;
		$url .= "/width/" . $width;
		$url .= "/height/" . $height;
		$url .= "/type/2";
		$url .= "/bgcolor/000000"; 
		return $url;
	}
	
	function getPlatformConfig() {
		if (self::$platfromConfig != null)
		{
			return self::$platfromConfig;
		}

		$activeServices = DekiService::getSiteList(DekiService::TYPE_EXTENSION, true);

		foreach ($activeServices as $aService)
		{	
			if ($aService->getName() == "Kaltura")
			{
				self::$platfromConfig = $aService;
				return $aService;
			}
		}
		return null;

	}

	function getPlatformKey($key = "", $default = "")
	{
//		$val = get_field('config_plugins','value','plugin','kaltura','name',$key);
    $val = get_config('kaltura', $key);
		if ($val == null ||  strlen($val) == 0)
		{
			return $default;
		}
		return $val;
	}

	function getServiceConfiguration() {

		$partnerId = KalturaHelpers::getPlatformKey("partner_id","0");

		$config = new KalturaConfiguration($partnerId);
		$config->serviceUrl = KalturaHelpers::getKalturaServerUrl();
		$config->setLogger(new KalturaLogger());
		return $config;
	}
	
	function getKalturaServerUrl() {
		$url = KalturaHelpers::getPlatformKey("server_uri",KalturaSettings_SERVER_URL);
		if($url == '') $url = KalturaSettings_SERVER_URL;
		
		// remove the last slash from the url
		if (substr($url, strlen($url) - 1, 1) == '/')
			$url = substr($url, 0, strlen($url) - 1);
		return $url;
	}
	
	function getSessionUser() {
		global $USER;
	
		$kalturaUser = new KalturaUser();

		if ($USER->id) {
			$kalturaUser->userId= $USER->id;
			$kalturaUser->screenName = $USER->username;			
		}
		else
		{
			$kalturaUser->userId = KalturaSettings_ANONYMOUS_USER_ID; 
		}

		return $kalturaUser;
	}
	
	function getKalturaClient($isAdmin = false, $privileges = null)
	{
		// get the configuration to use the kaltura client
		$kalturaConfig = KalturaHelpers::getServiceConfiguration();
		$sessionUser = KalturaHelpers::getSessionUser();
		
		if(!$privileges) $privileges = 'edit:*';
		// inititialize the kaltura client using the above configurations
		$kalturaClient = new KalturaClient($kalturaConfig);
	
		// get the current logged in user
//		$user = KalturaHelpers::getPlatformKey("user", "");
		$user = $sessionUser->userId;

		if ($isAdmin)
		{
			$adminSecret = KalturaHelpers::getPlatformKey("admin_secret", "");
			$ksId = $kalturaClient-> session -> start($adminSecret, $user, KalturaSessionType::ADMIN, -1, 86400, $privileges);
		}
		else
		{
			$secret = KalturaHelpers::getPlatformKey("secret", "");
			$ksId = $kalturaClient-> session -> start($secret, $user, KalturaSessionType::USER, -1, 86400, $privileges);
		}
			
		$kalturaClient->setKs($ksId);
		
		return $kalturaClient;
	}
	
	function doHttpRequest($url, $params = array(), $files = array())
	{
		if (function_exists('curl_init'))
			return KalturaHelpers::doCurl($url, $params, $files);
		else
			return KalturaHelpers::doPostRequest($url, $params, $files);
	}

	/**
	 * Curl HTTP POST Request
	 *
	 * @param string $url
	 * @param array $params
	 * @return array of result and error
	 */
	function doCurl($url, $params = array(), $files = array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		if (count($files) > 0)
		{
			foreach($files as &$file)
				$file = "@".$file; // let curl know its a file
			curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($params, $files));
		}
		else
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, null, "&"));
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, '');
		if (count($files) > 0)
			curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		else
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$result = curl_exec($ch);
		$curlError = curl_error($ch);
		curl_close($ch);
		return array($result, $curlError);
	}

	/**
	 * HTTP stream context request 
	 *
	 * @param string $url
	 * @param array $params
	 * @return array of result and error
	 */
	function doPostRequest($url, $params = array(), $files = array())
	{
		if (count($files) > 0)
			throw new Exception("Uploading files is not supported with stream context http request, please use curl");
			
		$formattedData = http_build_query($params , "", "&");
		$params = array('http' => array(
					"method" => "POST",
					"Accept-language: en\r\n".
					"Content-type: application/x-www-form-urlencoded\r\n",
					"content" => $formattedData
		          ));

		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			$phpErrorMsg = "";
			throw new Exception("Problem with $url, $phpErrorMsg");
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
		   throw new Exception("Problem reading data from $url, $phpErrorMsg");
		}
		return array($response, '');
	}

  function kaltura_check_version()
  {
      //prepare the field values being posted to the service
      $data = array(
        'method' => '"node.get"',
        'nid' => '"65"',
      );

        try
        {
           $result = KalturaHelpers::doHttpRequest('http://exchange.kaltura.com/services/json', $data);
        }
        catch (Exception $e)
        {
            return array('', '');
        }
        
    //moodle 65
      //make the request
      if (empty($result[1]))
      {
          $result = json_decode($result[0]);
      } 
      else
      {
          return array('', '');
      }
           
      $downloadUrl = "";
      if (substr($result->{"#data"}->field_download_bundle[0]->filepath, 0, 4) != "http")
      {
        $downloadUrl = 'http://exchange.kaltura.com/' . $result->{"#data"}->field_download_bundle[0]->filepath;
      }
      else
      {
        $downloadUrl = $result->{"#data"}->field_download_bundle[0]->filepath;
      }
      return array($result->{"#data"}->field_application_version[0]->value, $downloadUrl);
    }
	
}
?>
