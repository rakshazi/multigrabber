<?php
namespace Rakshazi;

use PicoFeed\Client\Url;
use PicoFeed\Encoding\Encoding;
use PicoFeed\Filter\Filter;
use PicoFeed\Parser\XmlParser;
use PicoFeed\Scraper\CandidateParser;
use PicoFeed\Scraper\RuleLoader;
use PicoFeed\Scraper\RuleParser;

class Multigrabber
{
    /** @var \PicoFeed\Config\Config */
    protected $config;
    /** @var \MCurl\Client */
    protected $client;

    public function __construct(\PicoFeed\Config\Config $config)
    {
        $this->config = $config;
        $this->client = new \MCurl\Client;
        $this->client->setMaxRequest(20); //Max parallel request
    }

    /**
     * Grab and parse urls
     * @param array $urls Array of urls, eg: ['http://example.site', 'http://example2.com/article']
     *
     * @return array
     */
    public function run($urls = [])
    {
        $results = $this->client->get($urls);
        $data = [];
        foreach ($results as $result) {
            $data[$result->info['url']] = $this->grab($result->info['url'], $result->body);
        }

        return $data;
    }

    /**
     * Grab parse and filter downloaded content
     *
     * @param string $url
     * @param string $html
     *
     * @return string
     */
    protected function grab($url, $html)
    {
        $html = $this->getParser($url, $html)->execute();
        $filter = Filter::html($html, $url);
        $filter->setConfig($this->config);

        return $filter->execute();
    }

    /**
     * Get Parser for content
     *
     * @param string $url
     * @param string $html
     *
     * @return \PicoFeed\Scraper\ParserInterface
     */
    protected function getParser($url, $html)
    {
        $html_encoding = XmlParser::getEncodingFromMetaTag($html);
        $html = Encoding::convert($html, $html_encoding ?: 'utf-8');
        $html = Filter::stripHeadTags($html);
        $ruleLoader = new RuleLoader($this->config);
        $rules = $ruleLoader->getRules($url);
        if (!empty($rules['grabber'])) {
            foreach ($rules['grabber'] as $pattern => $rule) {
                $url = new Url($url);
                $sub_url = $url->getFullPath();
                if (preg_match($pattern, $sub_url)) {
                    return new RuleParser($html, $rule);
                }
            }
        }
        return new CandidateParser($html);
    }
}
