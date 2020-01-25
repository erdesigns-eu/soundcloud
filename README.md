# PHP Soundcloud Downloader Class
Simple class for downloading music (mp3) from SoundCloud. I made this class, because i could. I found some older variants on github and elsewhere but those are old, and non working.

Private Functions:
- is_json
- curl_http_get
- curl_http_get_json
- resolve_stream_url

Public Functions:
- is_track_downloadable (Used to filter out "downloadable" tracks from search query)
- search (Search by wildcard on souncloud, returns array of tracks)
- search_next (Get the next 200 results from previous search query, if available)
- convert_url (Convert soundcloud permalink url to track for downloading; https://soundcloud.com/djpaulelstak/i-love-beatz-mixtape-3)
- filter_mp3_downloads (Filter out downloadable tracks from the array of tracks)
- download_mp3 (Download filtered mp3 - first use filter_mp3_downloads)

Shouldnt be to hard to use, also see the "demo.php" for a simple search example, and a single convert and download example. If you use it in your website, please just like our facebook page and credits would be nice :)

Please visit our website: https://erdesigns.eu
and facebook page: https://fb.me/erdesigns.eu
