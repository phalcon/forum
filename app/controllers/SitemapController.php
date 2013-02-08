<?php

namespace Phosphorum\Controllers;

use Phosphorum\Models\Posts,
	Phosphorum\Models\PostsReplies,
	Phalcon\Http\Response;

class SitemapController extends \Phalcon\Mvc\Controller
{

	public function initialize()
	{
		$this->view->disable();
	}

	/**
	 * Generate the website sitemap
	 *
	 */
	public function indexAction()
	{

		$response = new Response();

		$expireDate = new \DateTime();
		$expireDate->modify('+1 day');

		$response->setExpires($expireDate);

		$response->setHeader('Content-Type', "application/xml; charset=UTF-8");

		$sitemap = new \DOMDocument("1.0", "UTF-8");

		$urlset = $sitemap->createElement('urlset');
		$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

		$url = $sitemap->createElement('url');
		$url->appendChild($sitemap->createElement('loc', 'http://forum.phalconphp.com/'));
		$url->appendChild($sitemap->createElement('changefreq', 'daily'));
		$url->appendChild($sitemap->createElement('priority', '1.0'));
		$urlset->appendChild($url);

		foreach (Posts::find(array('order' => 'number_replies DESC')) as $post) {
			$url = $sitemap->createElement('url');
			$url->appendChild($sitemap->createElement('loc', 'http://forum.phalconphp.com/discussion/' . $post->id . '/' . $post->slug));
			$url->appendChild($sitemap->createElement('priority', '0.8'));
			$url->appendChild($sitemap->createElement('lastmod', $post->getUTCModifiedAt()));
			$urlset->appendChild($url);
		}

		$sitemap->appendChild($urlset);

		$response->setContent($sitemap->saveXML());
		return $response;
	}

}