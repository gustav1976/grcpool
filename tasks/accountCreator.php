<?php

require_once(dirname(__FILE__).'/../bootstrap.php');

# Details for the account to register
$username='grcpool.com-3';
$email= 'admin3@grcpool.com';
$host = 'localhost:31423';
$passwd = '474fdc822287d80cb61db25e55d6d672';

# Urls for projects
$dao = new GrcPool_Boinc_Account_DAO();
$projects = $dao->fetchAll();

$FORCE =isset($argv[1]) && $argv[1] == 'FORCE';
$idArg = 1;
if ($FORCE) {$idArg++;}
$id = 0;
if (isset($argv[$idArg])) {
	$id = $argv[$idArg];
}

foreach($projects as $project) {
	
		if ($id && $project->getId() != $id) {
			//echo 'SKIPPING: '.$project->getBaseUrl()."\n";
			continue;
		}
	
		$domain = $project->getBaseUrl();
		if ($project->getSecure() && !strstr($domain,'https:')) {
			$domain = str_replace('http:','https:',$domain);
		}
	
        echo $project->getName()." : ";
        $password = substr(md5($project->getName().time()),0,20);
		echo $password .' : ';
		
        $curl=curl_init();
        $request = $domain.'create_account.php?email_addr='.$email.'&passwd_hash='.md5(strtolower($email).$password).'&user_name='.$username;
        echo $request."\n";
        curl_setopt_array($curl,array(
            CURLOPT_RETURNTRANSFER=>1,
        	CURLOPT_URL => $request,
        	CURLOPT_SSL_VERIFYHOST => 0,
        	CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        print_r($response);
        $temp_array=json_decode(json_encode(simplexml_load_string($response)),TRUE);
        $authenticator = $temp_array['authenticator'];
        //$authenticator = '9ea77e48adf9c734ae9d0d887626dd9a';
        echo  $authenticator. ' : ';
        curl_close($curl);
        
        $curl=curl_init();
        $request = $domain.'am_set_info.php?account_key='.$authenticator.'&teamid='.$project->getTeamId();
        echo $request."\n";
        curl_setopt_array($curl,array(
        	CURLOPT_RETURNTRANSFER=>1,
        	CURLOPT_URL => $request,
        	CURLOPT_SSL_VERIFYHOST => 0,
        	CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        //print_r($response);
        $request = $domain.'am_get_info.php?account_key='.$authenticator;
        echo $request."\n";
        $curl=curl_init();
        curl_setopt_array($curl,array(
        	CURLOPT_RETURNTRANSFER=>1,
        	CURLOPT_URL => $request,
        	CURLOPT_SSL_VERIFYHOST => 0,
        	CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        //print_r($response);
        
        $urlDao = new GrcPool_Boinc_Account_Url_DAO();
        $urlObj = $urlDao->initWithKey($project->getUrlId());
        $url = $urlObj->getUrl();
        
        $temp_array=json_decode(json_encode(simplexml_load_string($response)),TRUE);
        echo $temp_array['weak_auth'] . ' : '.$temp_array['teamid']."\n";
        echo 'boinccmd --host '.$host.' --passwd '.$passwd.' --project_attach '.$url.' '.$temp_array['weak_auth']."\n-------------------------------------------------";
        echo "\n";
         
}