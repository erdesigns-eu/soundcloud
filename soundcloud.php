<?php

/**********************************************************************************/
/*										  */
/*				soundcloud.php					  */
/*										  */
/*				Author	: Ernst Reidinga 			  */
/*				Date 	: 25/01/2020 20:00			  */
/*				Version	: 1.0					  */
/*										  */
/**********************************************************************************/

class soundCloud {
	private $api_url = 'https://api-v2.soundcloud.com';
	private $client_id;
	private $next_search;
	var $search_total;

	// Soundcloud Class Constructor
	function __construct ($client_id = '') {
		$this->client_id = $client_id;
	}

	private function is_json ($string) {
		if (is_string($string)) {
			json_decode($string);
			return (json_last_error() == JSON_ERROR_NONE);
		} else {
			return false;
		}
	}

	private function curl_http_get ($url, $useragent = 'Mozilla/5.0 like Gecko', $headers = []) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	private function curl_http_get_json ($url, $useragent = 'Mozilla/5.0 like Gecko', $headers = [], $array = true) {
		$res = $this->curl_http_get($url, $useragent, $headers);
		if ($this->is_json($res)) {
		return json_decode($res, $array);
		} else {
		return json_decode('[]', $array);
		}
	}

	// Resolve download / stream url from url
	private function resolve_stream_url ($url) {
		return $this->curl_http_get_json(sprintf('%s?client_id=%s', $url, $this->client_id))['url'];
	}

	// Return Find 'progressive' in the stream url
	public function is_track_downloadable ($track) {
		if (is_array($track) && array_key_exists('media', $track) && array_key_exists('transcodings', $track['media'])) {
			foreach ($track['media']['transcodings'] as $stream) {
				$parts = explode('/', $stream['url']);
				if (end($parts) == 'progressive') {
					return true;
				}
			}
			return false;
		} else {
			return false;
		}
	}

	// Search for tracks
	public function search ($query, $offset = 0, $limit = 200) {
		$res = $this->curl_http_get_json(sprintf('%s/search/tracks?q=%s&offset=%s&limit=%s&client_id=%s', $this->api_url, urlencode($query), $offset, $limit, $this->client_id));
		$this->next_search  = array_key_exists('next_href', $res) ? $res['next_href'] : null;
		$this->search_total = $res['total_results'];
		return $res['collection'];
	}

	// Get next tracks (from previous search query)
	public function search_next () {
		if (!empty($this->next_search)) {
			$res = $this->curl_http_get_json(sprintf('%s&client_id=%s', $this->next_search, $this->client_id));
			$this->next_search = array_key_exists('next_href', $res) ? $res['next_href'] : null;
			return $res['collection'];
		} else {
			return [];
		}
	}

	// Convert soundcloud url to track
	public function convert_url ($url) {
		$temp = $this->curl_http_get($url);
		if (stripos($temp, '{"id":17,') > 0) {
			$temp = substr($temp, stripos($temp, '{"id":17,'), strlen($temp));
			$temp = substr($temp, 0, stripos($temp, ']);'));
			if ($this->is_json($temp)) {
				return json_decode($temp, true);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	// Filter all tracks, return downloadable tracks including resolved url
	public function filter_mp3_downloads ($collection) {
		$res = [];
		foreach(array_filter($collection, [new soundCloud(), 'is_track_downloadable']) as $track) {
			foreach ($track['media']['transcodings'] as $stream) {
				$parts = explode('/', $stream['url']);
				if (end($parts) == 'progressive') {
					array_push($res, [
						'filename'	=> sprintf('%s.mp3', $track['title']),
						'download'	=> $this->resolve_stream_url($stream['url'])
					]);
				}
			}
		}
		return $res;
	}

	// Download filtered mp3
	public function download_mp3 ($mp3) {
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"{$mp3['filename']}\"");
		readfile($mp3['download']);
	}

}
