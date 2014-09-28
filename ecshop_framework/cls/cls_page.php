<?php
class cls_page{
	public $page = 1;
	public $pagesize = 15;
	public $page_pre = 1;//上一页
	public $page_next = 1;//下一页
	public $curr_page;//总页数
	public $curr_count;
	public $http_url;
	public $page_url = '{page}';
	public $page_num = 5;//分页中间的数组显示数量
	public $show_total_num = false;
	public function __construct($curr_count,$page,$pagesize = 15,$url = ''){
		$page = $page > 0 ? $page : 0;
		$page_pre = $page > 1 ? $page-1 : 1;
		$curr_page = ceil($curr_count/$pagesize);
		$page_next = $page < $curr_page ? $page + 1 : $curr_page;
		
		$this->page = $page;
		$this->pagesize = $pagesize;
		$this->page_pre = $page_pre;
		$this->page_next = $page_next;
		$this->curr_page = $curr_page;
		$this->curr_count = $curr_count;
		$this->http_url = $this->getUrl($page,$url);
	}
	
	public function run(){
		if($this->curr_count == 0){
			return '';
		}
		$str = $this->setHeader();
		if($this->curr_page > 1){
			$str .= $this->setPre();
			$str .= $this->setNum();
			$str .= $this->setNext();
		}
		$str .= $this->setFooter();
		return $str;
	}
	public function setShowTotalNum($boo){
		$this->show_total_num = $boo;
	}
	public function getUrl($page,$url){
		if(empty($url)){
			$param = $_REQUEST;
			$url = '?';
			$urlParam = array();
			foreach($param as $key => $value){
				if($key != 'page'){
					$urlParam[$key] = $value;
					$url .= $key."=".$value.'&';
				}
			}
			$url .= 'page={page}';
		}else{
			$url = $url.'&page={page}';
		}
		return $url;
	}
	
	public function setHeader(){
		$str = '<div class="ui-pagination">';
		if($this->show_total_num){
			$str .= '<div class="total left">总<b>'.$this->curr_count.'</b>个记录</div>';
		}
		//$str .= '<div class="page"><ul>';
		return $str;
	}
	
	public function setPre(){
		$one_url = str_replace($this->page_url,1,$this->http_url);
		$pre_url = str_replace($this->page_url,$this->page_pre,$this->http_url);
		$str = '<a href="'.$one_url.'" class="enable">首页</a>
        <a href="'.$pre_url.'">上一页</a>';
		return $str;
	}
	
	public function setNum(){
		if($this->curr_page <= 5){
			$start_page = 1;
			$end_page = $this->curr_page;
		}else{
			$start_page = ($this->page - 2 > 0) ? $this->page - 2 : 1;
			$end_page = ($start_page + $this->page_num -1 < $this->curr_page) ? $start_page + $this->page_num - 1 : $this->curr_page;
		}
		$str = '';
		for($i=$start_page;$i<=$end_page;$i++){
			$http_url = str_replace($this->page_url,$i,$this->http_url);
			$str .= '<a href="'.$http_url.'"';
			if($this->page == $i){
				$str .= ' class="page-cur"';
			}
			else 
			{
				//$str .= ' class="enable"';
			}
			$str .= '>'.$i.'</a>';
		}
		if($this->curr_page > 5){
			$str .= '<a>...</a>';
		}
		return $str;
	}
	
	public function setNext(){
		$next_url = str_replace($this->page_url,$this->page_next,$this->http_url);
		$wei_url = str_replace($this->page_url,$this->curr_page,$this->http_url);
		$str = '<a href="'.$next_url.'">下一页</a></li>
          <a href="'.$wei_url.'">尾页</a></li>';
		return $str;
	}
	
	public function setFooter(){
		$str = '</div></div>';
		return $str;
	}
}