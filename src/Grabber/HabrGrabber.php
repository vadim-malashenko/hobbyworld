<?php


namespace Hobbyworld\Grabber;


class HabrGrabber implements IGrabber {


    public function grab (int $timestamp, int $limit) : array {

        //throw new \Exception ('Grabber error: Something went wrong', 500);

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

        function trim_tail ($s, $t) {

            $b = str_split ($t);
            $a = '';

            while ($c = array_shift ($b))

                if (preg_match ('#' . preg_quote ($a .= $c) . '$#', $s))

                    break;

            return str_replace ($a, '', $s);
        }

        function content (string $url) : string {

            $document = new \DOMDocument ();
            @$document->loadHTMLFile($url);
            $xpath = new \DOMXPath ($document);
            $node  = $xpath->query("//*[@id='post-content-body']") [0];
            $html  = implode(array_map([$node->ownerDocument, 'saveHTML'], iterator_to_array($node->childNodes)));

            return ($node !== null) ? mb_convert_encoding($html, 'utf-8', 'auto') : '';
        }

        function reducer ($timestamp) {

            return function ($items, $item) use ($timestamp) {

                if ($timestamp < ($pub_date = strtotime($item->xpath('pubDate') [0]))) {

                    try {

                        $url = $url = (string)$item->xpath('guid') [0];
                        $id = preg_replace('#\D+#', '', $url);
                        $title = $item->xpath('title') [0];
                        $brief = trim_tail (mb_substr(trim(strip_tags((string)$item->xpath('description') [0])), 0, 200, 'utf-8'), ' Читать дальше →');
                        $content = content ($url);

                        $items [] = [
                            'id'        => $id,
                            'url'       => $url,
                            'timestamp' => $pub_date,
                            'title'     => $title,
                            'brief'     => $brief,
                            'content'   => $content
                        ];
                    }
                    catch (\Exception $ex) {}
                }

                return $items;
            };
        }

        return array_reduce (array_slice ($xml->xpath( '//item'), 0, $limit), reducer ($timestamp), []);
    }
}