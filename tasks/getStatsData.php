<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$hostDao = new GrcPool_Member_Host_Credit_DAO();

$sql = '
	insert into grcpool.member_host_stat_mag (memberId,hostId,projectUrl,thetime,mag,avgCredit) 
	select id,hostDbid,projectUrl,UNIX_TIMESTAMP(NOW()),mag,avgCredit from grcpool.view_member_host_project_credit 
';
$hostDao->executeQuery($sql);
