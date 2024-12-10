<?php

namespace Oembed\tests;

use FunctionalTester;

class ExampleFunctionalCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testPageLoads(FunctionalTester $I): void
    {
        $I->amOnPage('?p=/');
        $I->seeResponseCodeIs(200);
    }
}
