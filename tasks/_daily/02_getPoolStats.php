<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../../bootstrap.php');
$hostDao = new GrcPool_Member_Host_Credit_DAO();
 $sql = '
 	insert into grcpool.member_host_stat_mag (memberId,hostId,accountId,thetime,mag,avgCredit,poolId) 
 	select memberId,hostDbid,accountId,UNIX_TIMESTAMP(NOW()),mag,avgCredit,projectPoolId
 	from grcpool.view_member_host_project_credit 
 	where avgCredit > 0 or mag > 0
 ';
$hostDao->executeQuery($sql);