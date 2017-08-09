<?php
namespace app\index\controller;

class Index extends Base
{
    public function index()
    {
        return view('index');
    }

    public function news()
    {
    	$id = input('id');
    	$imgs = $this->get_imgs($id);
    	$this->assign('imgs', $imgs);
        return view('news');
    }

    public function get_imgs($id){
    	switch ($id) {
    		//新闻
    		case 1:
    			$imgs[] = 'zixun1-1.jpg';
    			$imgs[] = 'zixun1-2.jpg';
    			$imgs[] = 'zixun1-3.jpg';
    			break;
    		case 2:
    			$imgs[] = 'zixun2-1.jpg';
    			break;
		    case 3:
				$imgs[] = 'zixun3-1.jpg';
				$imgs[] = 'zixun3-2.jpg';
				$imgs[] = 'zixun3-3.jpg';
				$imgs[] = 'zixun3-4.jpg';
				$imgs[] = 'zixun3-5.jpg';
				$imgs[] = 'zixun3-6.jpg';
				break;
			case 4:
				$imgs[] = 'zixun4-1.jpg';
				$imgs[] = 'zixun4-2.jpg';
				break;
			case 5:
				$imgs[] = 'zixun5-1.jpg';
				$imgs[] = 'zixun5-2.jpg';
				break;
			case 6:
				$imgs[] = 'zixun6-1.jpg';
				$imgs[] = 'zixun6-2.jpg';
				$imgs[] = 'zixun6-3.jpg';
				$imgs[] = 'zixun6-4.jpg';
				break;
			//专利
		    case 7:
    			$imgs[] = 'zhuanli1-1.jpg';
    			break;
    		case 8:
    			$imgs[] = 'zhuanli2-1.jpg';
    			$imgs[] = 'zhuanli2-2.jpg';
    			break;
		    case 9:
				$imgs[] = 'zhuanli3-1.jpg';
				break;
			case 10:
				$imgs[] = 'zhuanli4-1.jpg';
				break;
			case 11:
				$imgs[] = 'zhuanli5-1.jpg';
				break;
			case 12:
				$imgs[] = 'zhuanli6-1.jpg';
				break;

			//法律
		    case 13:
		    	for($i=1;$i<39;$i++){
		    		$imgs[] = "falv1-$i.jpg";
		    	}    			
    			break;
    		case 14:
    			for($i=1;$i<7;$i++){
		    		$imgs[] = "falv2-$i.jpg";
		    	}   
    			break;
		    case 15:
				$imgs[] = 'falv3-1.jpg';
				break;
			case 16:
    			for($i=1;$i<12;$i++){
		    		$imgs[] = "falv4-$i.jpg";
		    	}  
				break;
			case 17:
    			for($i=1;$i<24;$i++){
		    		$imgs[] = "falv5-$i.jpg";
		    	}  
				break;

		    //行业报告
		    case 18:
		    	for($i=1;$i<16;$i++){
		    		$imgs[] = "shuju1-$i.jpg";
		    	}    			
    			break;
    		case 19:
    			for($i=1;$i<25;$i++){
		    		$imgs[] = "shuju2-$i.jpg";
		    	}   
    			break;
		    case 20:
    			for($i=1;$i<17;$i++){
		    		$imgs[] = "shuju3-$i.jpg";
		    	}   
    			break;
			case 21:
    			for($i=1;$i<8;$i++){
		    		$imgs[] = "shuju4-$i.jpg";
		    	} 
				break;
			case 22:
    			for($i=1;$i<22;$i++){
		    		$imgs[] = "shuju5-$i.jpg";
		    	}  
				break;
			case 23:
    			for($i=1;$i<34;$i++){
		    		$imgs[] = "shuju6-$i.jpg";
		    	}  
				break;
			case 24:
    			for($i=1;$i<20;$i++){
		    		$imgs[] = "shuju7-$i.jpg";
		    	}   
				break;
			case 25:
    			for($i=1;$i<4;$i++){
		    		$imgs[] = "shuju8-$i.jpg";
		    	}  
				break;
    	}

    	return $imgs;
    }
}
