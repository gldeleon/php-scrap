<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController as RC;
use Goutte\Client;

class ScrapController extends RC {

    public function scrap(Client $client) {
//        $crawler = $client->request('GET', 'https://editalfil.com/producto/serendipia-destinos-convergentes-2-vols/');//
        $crawler = $client->request('GET', 'https://editalfil.com/producto/actualidades-en-dolor-clinicas-mexicanas-de-anestesiologia-14/');
        $images = $crawler->filter("[class=woocommerce-product-gallery__image]")->first();
        $bookImage = $images
                ->filterXpath('//img')
                ->extract(array('src'));
        $selectorTitle = '[class="product_title entry-title elementor-heading-title elementor-size-default"]';
        $bookTitle = $crawler->filter($selectorTitle)->first();

        $selectorPrice = '[class="woocommerce-Price-amount amount"]';
        $bookPrice = $crawler->filter($selectorPrice)->first();

        $selectorDetails = '[class="woocommerce-product-details__short-description"]';
        $bookDetails = $crawler->filter($selectorDetails)->first();

//        dd($bookDetails->text());
        $bookResponse = [
            "img" => $bookImage,
            "bookTitle" => $bookTitle->text(),
            "bookPrice" => $bookPrice->text(),
            "bookDetails" => $bookDetails->text()
        ];
        return $this->sendResponse($bookResponse, 'ok');
    }

}
