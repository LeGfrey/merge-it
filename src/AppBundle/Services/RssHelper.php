<?php

namespace AppBundle\Services;

use \SimplePie as SimplePie;

class RssHelper
{
    public function __construct() {
        $this->feed = new SimplePie();
        $this->feed->set_cache_location(__dir__.'/../../../var/cache');
    }

    /**
     * Merge several RSS feeds into one
     *
     * @param array $feedsUrl
     * @param $title
     * @param $link
     * @param $description
     * @return string
     */
    public function mergeFeeds(array $feedsUrl, $title, $link, $description) {
        $feedString = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $feedString .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";
        $feedString .= '<channel>'."\n";
        $feedString .= '<title>'.htmlspecialchars($title).'</title>'."\n";
        $feedString .= '<description>'.htmlspecialchars($description).'</description>'."\n";
        $feedString .= '<copyright>Copyright 2016 - '.date('Y').'</copyright>'."\n";

        $feed = $this->feed;
        $feed->set_feed_url($feedsUrl);
        $feed->set_cache_duration(600);
        $success = $feed->init();
        $feed->handle_content_type();
        
        if(!$success) return false;

        foreach($feed->get_items() as $item) {
            $feedString .= '<item>'."\n";
            $feedString .= '<link>'.htmlspecialchars($item->get_permalink()).'</link>'."\n";
            $feedString .= '<guid>'.htmlspecialchars($item->get_permalink()).'</guid>'."\n";
            $feedString .= '<title>'.htmlspecialchars($item->get_title()).'</title>'."\n";
            $feedString .= '<description>'.htmlspecialchars(strip_tags($item->get_description())).'</description>'."\n";
            $feedString .= '<pubdate>'.$item->get_date('D, d M Y H:i:s T').'</pubdate>'."\n";
            if($enclosures = $item->get_enclosures()) {
                foreach($enclosures as $enclosure) {
                    $feedString .= '<enclosure url="'.htmlspecialchars($enclosure->get_link()).'" length="'.$enclosure->get_length().'" type="'.htmlspecialchars($enclosure->get_type()).'" />'."\n";
                }
            }
            $feedString .= '</item>'."\n";
        }

        $feedString .= '</channel></rss>';

        return $feedString;
    }
}
