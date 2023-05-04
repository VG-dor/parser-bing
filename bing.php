function getBing($query, $st){
	include_once('simple_html_dom.php');
	global $settings, $bg, $no_tag;
	$bg_txt = file_get_contents($bg);
	$url = "https://www.bing.com/search?q=".urlencode($query)."&sp=-1&lq=0&pq=".urlencode($query)."&sc=9-20&qs=n&sk=&cvid=&ghsh=0&ghacc=0&ghpl=&first=$st&FORM=PERE";

	$cookieFile = "cookie.txt";
	$curl_arr = curl_init();
	curl_setopt($curl_arr, CURLOPT_URL, $url);
    curl_setopt($curl_arr, CURLOPT_HEADER, 0);
    curl_setopt($curl_arr, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_arr, CURLOPT_TIMEOUT, 15);
    curl_setopt($curl_arr, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl_arr, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl_arr, CURLOPT_MAXREDIRS, 3);
	curl_setopt($curl_arr, CURLOPT_COOKIEJAR, $cookieFile);
	curl_setopt($curl_arr, CURLOPT_COOKIEFILE, $cookieFile);
	curl_setopt($curl_arr, CURLOPT_HTTPHEADER, array('Host: www.bing.com', 
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 YaBrowser/23.1.5.708 Yowser/2.5 Safari/537.36", 
                'Accept: * /*', 
                "Accept-Language: ru,uk;q=0.9,en;q=0.8,uz;q=0.7,fr;q=0.6,de;q=0.5,sr;q=0.4,eu;q=0.3,ky;q=0.2,ar;q=0.1,tr;q=0.1,zh;q=0.1", 
                'Origin: https://www.bing.com', 
                'DNT: 1', 
                'Connection: keep-alive', 
                'If-Modified-Since: *'));
	$res = curl_exec($curl_arr);
    curl_close($curl_arr);
	
	$f = str_get_html($res);
	
	$g = $f->find('.b_rs',0)->find('a');
	foreach($g as $a){
		$lnk[] = trim($a->plaintext);
	}
	$lnk = implode(",",$lnk);

	$c = 0;
	foreach($f->find('.b_algo') as $t){
		$title = $t->find('h2', 0)->innertext;
		$title = strip_tags($title);
		$dsc = $t->find('.b_algoSlug', 0)->innertext;
		$dsc = trim(strip_tags($dsc));
		$dsc = str_replace($no_tag, "", $dsc);
		
		
		if(strlen($dsc) > 250){
			$title = str_replace($no_tag, "", $title);
			$bing[$c]['title'] = $title;
			$bing[$c]['desc'] = $dsc;
			$c++;
			
			if(strpos($bg_txt, $title) === false){
				$sv = $query ."|". $title . "|" . $dsc . "|" . $lnk;
				file_put_contents($bg, $sv . "\n", FILE_APPEND);
			}
		}
		
	}
	
	sleep(1);
	return $bing;
}

$bg = "base.txt";

$key = "Зайти в Dragon Money";

$str = 1;
for($i=0;$i<=4;$i++){
	$bing[] = getBing($key, $str);

	$str = $str+10;
}
	


echo "<pre>";
print_r($bing);
echo "</pre>";
