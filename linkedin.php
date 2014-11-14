<?php
	/*
	* Author name :  Saran Pal
	* saran.pal1911@gmail.com
	* This is quick start with linkedin API using the OAUTH_USER_TOKEN
	* You must pre-register your redirect_uri at https://www.linkedin.com/secure/developer
	* We are just sending the OAUTH_USER_TOKEN as header and calling the linkedin company look up api.
	* @ http://api.linkedin.com/v1/companies/universal-name=google:(id,name,ticker,description,employee-count-range)
	*/
	require_once "oauth/OAuth.php";
	
	define('API_KEY','YOUR_API_KEY');
	define('SECRET_KEY','YOUR_SECRET_KEY');	
	define('OAUTH_USER_TOKEN','YOUR_OAUTH_USER_TOKEN');	
		
	$lnd = new Linkedin(API_KEY, SECRET_KEY);
	$company_info =$lnd->getCompanyInfo();
	var_dump($company_info);
	
	class Linkedin {
		
		function __construct($oaConsumerKey, $oaConsumerSecret) {			
			$this->oaConsumerKey = $oaConsumerKey;
			$this->oaConsumerSecret = $oaConsumerSecret;
			$this->signature = new OAuthSignatureMethod_HMAC_SHA1();
		}
		
		function __call($method, $arguments) {
		
			$linkedin_url = "http://api.linkedin.com/v1/companies/universal-name=google:(id,name,ticker,description,employee-count-range)";
			
			$ch = curl_init($linkedin_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json', 
				'Accept: application/json',
				$this->__build_oauth_header($linkedin_url)
			));
			$rawresponse = curl_exec($ch);
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$responseContentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);			
			curl_close($ch);			
			return $rawresponse;
				
		}
		
		private function __build_oauth_header($linkedin_url) {
			$request = new OAuthRequest('GET', $linkedin_url, array(
				'oauth_nonce' => OAuthRequest::generate_nonce(),
				'oauth_timestamp' => OAuthRequest::generate_timestamp(),
				'oauth_version' => '1.0',
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_consumer_key' => $this->oaConsumerKey,
				'oauth_token' => OAUTH_USER_TOKEN
			));
			
			$request->sign_request($this->signature, new OAuthConsumer('', $this->oaConsumerSecret), new OAuthToken('', '95b27494-0a99-47c0-a66c-533cef4b8a28'));			
			return $request->to_header();
		}
		
	}