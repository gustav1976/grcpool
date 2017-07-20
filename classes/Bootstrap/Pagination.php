<?php 
class Bootstrap_Pagination {
	private $grpVar = 'grp';
	private $howMany;
	private $group = 20;
	private $href = '';
	private $maxButtons = 5;
	private $start = '';
	private $adjacents = 2;
	private $showArrows = true;
	private $appendPagination = '';
	private $queryString = false;
	public function setQueryString($b) {$this->queryString = $b;}
	public function setAppendPagination($str) {$this->appendPagination = $str;}
	public function setFloat($s) {}
	public function setArrows($b) {$this->showArrows = $b;}
	public function setAdjacents($i) {$this->adjacents = $i;}
	public function setGrpVar($s) {$this->grpVar = $s;}
	public function setHref($s) {$this->href = $s;}
	public function setHowMany($s) {$this->howMany = $s;}
	public function setGroup($s) {$this->group = $s;}
	public function setStart($i) {$this->start = $i;}
	
	private function queryString() {
		return $this->queryString?'?'.$_SERVER['QUERY_STRING']:'';
	}
	
	private function getPaginationString($page, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/") {		
		//other vars
		$numberOfButtons = 7;
		$prev = $page - 1;									//previous page is page - 1
		$next = $page + 1;									//next page is page + 1
		$lastpage = ceil($totalitems / $limit);				//lastpage is = total items / items per page, rounded up.
		$lpm1 = $lastpage - 1;								//last page minus 1
		$pagination = "";
		if($lastpage > 1) {	
			$pagination .= '<ul style="margin-top:0px;padding-top:0px;" class="pagination pagination-sm">';
			//previous button
			if ($this->showArrows) {
				if ($page > 1) 
					$pagination .= "<li><a href=\"$targetpage".($prev-1).$this->queryString()."\">&larr;</a></li>";
				else
					$pagination .= "<li class=\"disabled\"><a href=\"#\">&larr;</a></li>";
			}	
			//pages	
			if ($lastpage < $numberOfButtons + ($adjacents * 2))	//not enough pages to bother breaking it up
			{	
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($page !== '' && $counter == $page)
						$pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
					else
						$pagination .= "<li><a href=\"" . str_replace('?',($counter-1),$targetpage) .$this->queryString()."\">$counter</a></li>";					
				}
			}
			elseif($lastpage >= $numberOfButtons + ($adjacents * 2))	//enough pages to hide some
			{
				//close to beginning; only hide later pages
				if($page < 1 + ($adjacents * 3))		
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($page !== '' && $counter == $page)
							$pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
						else
							$pagination .= "<li><a href=\"" . str_replace('?',($counter-1),$targetpage) .$this->queryString()."\">$counter</a></li>";					
					}
					$pagination .= "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					$pagination .= "<li><a href=\"" . str_replace('?',(($lpm1-1) ),$targetpage) .$this->queryString()."\">$lpm1</a></li>";
					$pagination .= "<li><a href=\"" . str_replace('?',($lastpage-1),$targetpage) .$this->queryString()."\">$lastpage</a></li>";		
				}
				//in middle; hide some front and some back
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination .= "<li><a href=\"" . str_replace('?',0,$targetpage) .$this->queryString()."\">1</a></li>";
					$pagination .= "<li><a href=\"" . str_replace('?',1,$targetpage) .$this->queryString()."\">2</a></li>";
					$pagination .= "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($page !== '' && $counter == $page)
							$pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
						else
							$pagination .= "<li><a href=\"" . str_replace('?',($counter-1),$targetpage)  .$this->queryString()."\">$counter</a></li>";					
					}
					$pagination .= "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					$pagination .= "<li><a href=\"" . str_replace('?',($lpm1-1),$targetpage)  .$this->queryString()."\">$lpm1</a></li>";
					$pagination .= "<li><a href=\"" . str_replace('?',($lastpage-1),$targetpage)    .$this->queryString()."\">$lastpage</a></li>";		
				}
				//close to end; only hide early pages
				else
				{
					$pagination .= "<li><a href=\"" . str_replace('?',0,$targetpage)  .$this->queryString()."\">1</a></li>";
					$pagination .= "<li><a href=\"" . str_replace('?',1,$targetpage)   .$this->queryString()."\">2</a></li>";
					$pagination .= "<li class=\"disabled\"><a href=\"#\">...</a></li>";
					for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
						else
							$pagination .= "<li><a href=\"" . str_replace('?',($counter-1),$targetpage)  .$this->queryString()."\">$counter</a></li>";					
					}
				}
			}
			
			//next button
			if ($this->showArrows) {
				if ($page < $counter - 1) 
					$pagination .= "<li><a href=\"" . str_replace('?',($next-1),$targetpage)  .$this->queryString()."\">&rarr;</a></li>";
				else
					$pagination .= "<li class=\"disabled\"><a href=\"#\">&rarr;</a></li>";
			}
			if ($this->appendPagination != '') {
				$pagination .= '<li>'.$this->appendPagination.'</li>';
			}
			$pagination .= "</ul>";
		}
		
		return $pagination;
	
	} 
	public function render() {
		return $this->getPaginationString($this->start!==''?$this->start+1:'', $this->howMany, $this->group, $this->adjacents, $this->href);
	}

}
?>