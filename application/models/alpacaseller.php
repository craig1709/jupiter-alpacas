<?php

class Alpacaseller_Model extends Model
{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_alpacas()
	{
		//cache currently disabled to see if it helps the PHPSESSID problem
		if (file_exists('cache/scrape.html') && 1==2) {
			if ((date('U')-filemtime('cache/scrape.html')) < 86400) {
				//if cached in the last 24hours
				$html = file_get_contents('cache/scrape.html');
				$cached = true;
			}
		} else {
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_HEADER, TRUE);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, TRUE);
			curl_setopt($curl, CURLOPT_URL, 'http://www.alpacaseller.com/main.php?Country=United Kingdom&FromDotCom=1');
			$results = curl_exec($curl);
			$cookie = substr($results, strpos($results, "PHPSESSID")+strlen("PHPSESSID")+1, strlen($results)-strpos($results, "PHPSESSID"));
			$cookie = substr($cookie, 0, strpos($cookie, ";"));
			curl_close($curl);
			
			$url = 'http://www.alpacaseller.com/BreederAnimalList.php?BreederID=222&Direct=1&PHPSESSID=' . $cookie;
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			$results = curl_exec($curl);
			curl_close($curl);
			
			$html = substr($results, strrpos($results, "</script>"), strlen($results)-strrpos($results, "</script>"));
			$html = substr($html, strpos($html, "<tr"), strlen($html)-strpos($html, "<tr"));
			$html = str_replace("</td>", "</td>\n\t", $html);
			$html = str_replace("</tr>", "</tr>\n", $html);
			file_put_contents('cache/scrape.html', $html);
		}
		
		$regex_pattern = "/<td>(.*)<\/td>/siU";
		$matches = array();
		preg_match_all($regex_pattern,$html,$matches);
		$matches = preg_replace($regex_pattern, "$1", $matches[1]);
		//print_r($matches); die();
		//for ($i=0; $i<7; $i++) { array_shift($matches); }
		for ($i=0; $i<3; $i++) { array_pop($matches); }
		//find animal ID
		$regex_pattern = "/<a href=\"ViewBreederAnimalDetails.php\\?AnimalID\=([^\>]*)>(.*)<\/a>/siU";
		preg_match_all($regex_pattern,$html,$id_initial);
		$count = 0;
		$details = array();
		foreach ($id_initial[0] as $id) {
			if ($count % 2) { //array contains two of every id, so skip one
				$link = substr($id, strpos($id, 'href="')+6);
				$link = substr($link, 0, strpos($link, '"'));
				$details[] = $link;
			}
			$count++;
		}
		
		$count = 0;
		$ids = array();
		foreach ($id_initial[1] as $id) {
			if ($count % 2) { //array contains two of every id, so skip one
				$ids[] = substr($id,0,strpos($id, '&amp;'));
			}
			$count++;
		}
		
		//remove all hyperlinks
		$regex_patten = "/<a([^\>]*)>(.*)<\/a>/siU";
		$matches = preg_replace($regex_patten, "$2", $matches);
		
		//remove all SOLD alpacas
		$regex_patten = "/<font([^\>]*)>(.*)<\/font([^\>]*)>/siU";
		$temp = preg_replace($regex_patten, '$2', $matches);
		$removals = array();
		foreach ($temp as $key => $tmp) {
			if (strpos($tmp, '(SOLD)') != FALSE) {
				$removals[] = $key / 6;
				unset($temp[$key]);
				unset($temp[$key+1]);
				unset($temp[$key+2]);
				unset($temp[$key+3]);
				unset($temp[$key+4]);
				unset($temp[$key+5]);
			}
		}

		foreach ($removals as $removal) {
			unset($details[$removal]);
			unset($ids[$removal]);
		}
		
		$idtemp = $ids;
		unset($ids);
		foreach ($idtemp as $id) {
			$ids[] = $id;
		}
		
		$detailtemp = $details;
		unset($details);
		$details = array();
		foreach ($detailtemp as $tmp) {
			$details[] = $tmp;
		}
		
		//reindex
		unset($matches);
		$matches = array();
		foreach ($temp as $tmp) {
			//remove all the (NEW) crap from names
			$tmp = str_replace('(NEW)', '', $tmp);
			$matches[] = $tmp;
		}
		
		$count = 0;
		$alpacas = array();
		for ($i=0; $i<(count($matches)-4); $i++) {
			
			if ($i % 6 == 0) {
				$alpacas[$count]['name'] = $matches[$i];
				$alpacas[$count]['breed'] = $matches[$i+1];
				$alpacas[$count]['gender'] = $matches[$i+2];
				$alpacas[$count]['colour'] = $matches[$i+3];
				$alpacas[$count]['price'] = substr($matches[$i+4], 1);
				$alpacas[$count]['details'] = $details[$i/6];
				$count++;
			}
		
		}
		
		for ($i=0; $i<count($alpacas); $i++) {
			
			$img_url = 'http://www.alpacaseller.com/photos/Animal_' . $ids[$i] . '_T.jpg';
				
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $img_url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
			curl_close($ch);
			
			preg_match("/HTTP\/1\.[1|0]\s(\d{3})/",$data,$matches); //can't remember what this does
			
			$alpacas[$i]['image_thumb'] = $img_url;
		}
		
		return $alpacas;
	}

}

?>