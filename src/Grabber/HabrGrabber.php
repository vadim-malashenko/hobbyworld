<?php


namespace Hobbyworld\Grabber;


class HabrGrabber implements IGrabber {


    public function grab (int $timestamp, int $limit) : array {


        $timestamp = ($timestamp <= ($now = time ()))
            ? ($timestamp > 0)
                ? $timestamp
                : 0
            : $now;

        $limit = ($limit > 0)
            ? ($limit <= 20)
                ? $limit
                : 20
            : 5;


        $feed  = implode (file ('https://habr.com/ru/rss/all/'));
        $xml   = new \SimpleXMLElement ($feed);

        function content (string $url) : string {

            $document = new \DOMDocument ();
            @$document->loadHTMLFile ($url);
            $xpath = new \DOMXPath ($document);
            $node = $xpath->query ("//*[@id='post-content-body']") [0];
            $html = implode (array_map ([$node->ownerDocument, 'saveHTML'], iterator_to_array ($node->childNodes)));

            return ($node !== NULL) ? mb_convert_encoding ($html, 'utf-8', 'auto') : '';
        }

        function reducer ($timestamp) {

            return function ($items, $item) use ($timestamp) {

                if ($timestamp < ($pub_date = strtotime($item->xpath('pubDate') [0]))) {

                    $items [] = [
                        'url'       => $url = (string)$item->xpath('guid') [0],
                        'timestamp' => $pub_date,
                        'id'        => preg_replace('#\D+#', '', $url),
                        'title'     => $item->xpath('title') [0],
                        'brief' => mb_substr(trim(strip_tags((string)$item->xpath('description') [0])), 0, 200, 'utf-8'),
                        'content'   => content ($url)
                    ];
                }

                return $items;
            };
        }

        return array_reduce (array_slice ($xml->xpath( '//item'), 0, $limit), reducer ($timestamp), []);
    }
}